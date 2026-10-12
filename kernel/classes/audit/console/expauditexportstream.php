<?php
/**
 * File containing the expAuditExportStream class.
 *
 * The audit records of a filter as CSV, JSON lines or JSON, newest first, written piece by piece to a callback as
 * they are read: the download of audit/export (sent to the browser while it is read, doc/bc/6.0/audit.md,
 * "Export") and the background export (the content job 'auditexport', expContentJobAuditExport, which writes a
 * file in batches and keeps state() in its checkpoint). Nothing is built in memory: at most one page of rows and
 * one segment of ids at a time.
 *
 *   $stream = new expAuditExportStream( $raw, expAuditConsole::allowedChannels(), 'csv', $limit );
 *   $stream->run( function ( $s ) { echo $s; } );          // everything; or run( $emit, 5000 ) a batch at a time
 *   $stream->count(); $stream->state(); expAuditExportStream::fromState( $state );
 *
 * The output is that of the export before #393, byte for byte: the first page comes from expAuditConsole::page()
 * (with the records of the live files not indexed yet), and the rest in the same order. Only the way the rest is
 * read changed: the ordered ids of the index in segments (expAuditQuery::orderedIds(), after the last row read)
 * and the full rows by id (expAuditQuery::byIds()), instead of OFFSET pages that sorted every whole record of the
 * filter again for each page of 500. On an engine without that SQL (MongoDB) or when the database refuses it, the
 * old pages are used. The SQL query cache is not used while the stream reads: an export is read once, and its
 * pages would fill the cache with rows nobody asks for again.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditExportStream
{
    /** format => media type */
    const FORMATS = array( 'csv' => 'text/csv', 'jsonl' => 'application/x-ndjson', 'json' => 'application/json' );

    /** The generic CSV columns; with a channel filter, the columns its source declares follow */
    const CSV_COLUMNS = array( 'time_utc', 'id', 'name', 'channel', 'seq', 'severity', 'result', 'reason', 'login', 'user_id', 'ip',
                               'object_type', 'object_id', 'object', 'target_type', 'target_id', 'request', 'siteaccess', 'engine',
                               'module_view', 'job', 'run', 'parent', 'file' );

    /** Rows read at a time (one page) */
    const CHUNK = 500;

    /** Ids read at a time */
    const ID_SEGMENT = 10000;

    /** @var array raw filters (expAuditConsole::rawFilters()) */
    protected $raw;

    /** @var array normalised filters */
    protected $filters;

    /** @var string[]|null the channels the user may read; null: all */
    protected $channels;

    /** @var string csv, jsonl or json */
    protected $format;

    /** @var int records at most */
    protected $limit;

    /** @var string[] column => path: the source's CSV columns */
    protected $extra;

    /**
     * @var array what is done: started (the header written), position (index rows of the pages read: the first
     *            page counts CHUNK), after (time_ms, id of the last index row read past the first page), count
     *            (records written), finished (the end written), paged (the old OFFSET pages are used)
     */
    protected $state = array( 'started' => false, 'position' => 0, 'after' => null, 'count' => 0, 'finished' => false, 'paged' => false );

    /** @var array[] ids read and not written yet: array( id, time_ms ) */
    protected $ids = array();

    /**
     * @param array $raw raw filters
     * @param string[]|null $channels the channels the user may read (expAuditConsole::allowedChannels())
     * @param string $format csv, jsonl or json
     * @param int $limit records at most
     */
    public function __construct( array $raw, $channels, $format, $limit )
    {
        if ( !isset( self::FORMATS[$format] ) )
            throw new InvalidArgumentException( "Unknown audit export format '$format'" );
        $this->raw = $raw;
        $this->filters = expAuditQuery::normalise( $raw );
        $this->channels = $channels === null ? null : array_values( (array)$channels );
        $this->format = $format;
        $this->limit = max( 0, (int)$limit );
        // an export of one channel: the columns its source declares follow the generic ones
        $this->extra = $format === 'csv' && isset( $raw['channel'] ) ? expAuditSourceRegistry::exportColumns( $raw['channel'], self::CSV_COLUMNS ) : array();
        $this->state['paged'] = !self::seekable();
    }

    /**
     * How many records the filter has, as the export page and the download count them (with the records of the
     * live files not indexed yet).
     *
     * @param array $raw
     * @param string[]|null $channels
     * @return int
     */
    public static function total( array $raw, $channels )
    {
        $first = expAuditConsole::page( expAuditQuery::normalise( $raw ), $channels, 0, 1 );
        return (int)$first['total'];
    }

    /** @return array the state to go on from (fromState()) */
    public function state()
    {
        return array( 'raw' => $this->raw, 'channels' => $this->channels, 'format' => $this->format, 'limit' => $this->limit ) + $this->state;
    }

    /**
     * A stream that goes on where state() was taken.
     *
     * @param array $state
     * @return expAuditExportStream
     */
    public static function fromState( array $state )
    {
        $stream = new self( (array)$state['raw'], $state['channels'], (string)$state['format'], (int)$state['limit'] );
        foreach ( array_keys( $stream->state ) as $k )
            if ( array_key_exists( $k, $state ) )
                $stream->state[$k] = $state[$k];
        return $stream;
    }

    /** @return int records written so far */
    public function count()
    {
        return (int)$this->state['count'];
    }

    /** @return bool everything is written */
    public function finished()
    {
        return !empty( $this->state['finished'] );
    }

    /** @return string the file name a download gets: audit-20261012-063000.csv */
    public static function fileName( $format, $time = null )
    {
        return 'audit-' . date( 'Ymd-His', $time === null ? time() : (int)$time ) . '.' . $format;
    }

    /**
     * Writes the export to $emit, a string at a time: the header, every record, the end.
     *
     * @param callable $emit function ( string $s )
     * @param int $maxRecords 0: everything; else stops after the page that reaches it (a batch), so the next run()
     *                        (or fromState( state() )->run()) goes on
     * @return bool everything is written
     */
    public function run( callable $emit, $maxRecords = 0 )
    {
        $budget = $maxRecords > 0 ? (int)$maxRecords : PHP_INT_MAX;
        $cache = class_exists( 'eZDBQueryCache' ) ? eZDBQueryCache::settings() : null;
        if ( $cache !== null && $cache['mode'] !== 'off' )
            eZDBQueryCache::setSettings( array( 'mode' => 'off' ) + $cache );
        try
        {
            if ( !$this->state['started'] )
            {
                if ( $this->format === 'csv' )
                    $emit( "\xEF\xBB\xBF" . self::csvLine( array_merge( self::CSV_COLUMNS, array_keys( $this->extra ) ) ) );
                elseif ( $this->format === 'json' )
                    $emit( "[\n" );
                $this->state['started'] = true;
            }
            while ( !$this->state['finished'] && $budget > 0 )
            {
                $rows = $this->nextPage();
                if ( !$rows )
                {
                    if ( $this->format === 'json' )
                        $emit( "\n]\n" );
                    $this->state['finished'] = true;
                    break;
                }
                foreach ( $rows as $row )
                {
                    $emit( $this->line( $row ) );
                    $this->state['count']++;
                    $budget--;
                }
            }
        }
        finally
        {
            if ( $cache !== null && $cache['mode'] !== 'off' )
                eZDBQueryCache::setSettings( $cache );
        }
        return $this->finished();
    }

    /**
     * The next page of index rows, in the order of the export: the first page (expAuditConsole::page(), with the
     * records not indexed yet), then the rest by position.
     *
     * @return array[] rows; none at the end
     */
    protected function nextPage()
    {
        $position = (int)$this->state['position'];
        if ( $position >= $this->limit )
            return array();
        $size = min( self::CHUNK, $this->limit - $position );
        if ( $position === 0 || $this->state['paged'] )
        {
            $page = expAuditConsole::page( $this->filters, $this->channels, $position, $size );
            $this->state['position'] = $position + self::CHUNK;
            return $page['rows'];
        }
        if ( count( $this->ids ) < $size && !$this->readIds() )
        {
            // the database refused the ordered ids: the old pages from here on
            $this->state['paged'] = true;
            return $this->nextPage();
        }
        $take = array_splice( $this->ids, 0, $size );
        $rows = $take ? ( new expAuditQuery() )->byIds( array_column( $take, 0 ) ) : array();
        if ( $rows === null )
        {
            array_splice( $this->ids, 0, 0, $take );
            $this->state['paged'] = true;
            return $this->nextPage();
        }
        $this->state['position'] = $position + self::CHUNK;
        if ( $take )
        {
            $last = end( $take );
            $this->state['after'] = array( (int)$last[1], (string)$last[0] );
        }
        return $rows;
    }

    /**
     * Reads the next segment of ordered ids after the last one held (or, the first time, from the position).
     *
     * @return bool false when the database refused the query
     */
    protected function readIds()
    {
        $last = $this->ids ? end( $this->ids ) : null;
        $after = $last ? array( (int)$last[1], (string)$last[0] ) : $this->state['after'];
        $need = $this->limit - (int)$this->state['position'] - count( $this->ids );
        if ( $need <= 0 )
            return true;
        $rows = ( new expAuditQuery() )->orderedIds( $this->filters, $this->channels, $after ? 0 : (int)$this->state['position'],
                                                      min( self::ID_SEGMENT, $need ), $after );
        if ( $rows === null )
            return false;
        foreach ( $rows as $r )
            $this->ids[] = array( (string)$r['id'], (int)$r['time_ms'] );
        return true;
    }

    /**
     * One record in the format.
     *
     * @param array $row an index row
     * @return string
     */
    protected function line( array $row )
    {
        if ( $this->format === 'jsonl' )
            return rtrim( (string)$row['record'], "\n" ) . "\n";
        if ( $this->format === 'json' )
            return ( $this->state['count'] ? ",\n" : '' ) . rtrim( (string)$row['record'], "\n" );
        $v = expAuditConsole::view( $row );
        $line = array();
        foreach ( self::CSV_COLUMNS as $c )
            $line[] = isset( $v[$c] ) ? $v[$c] : '';
        if ( $this->extra )
        {
            $record = json_decode( (string)$row['record'], true );
            $line = array_merge( $line, array_values( expAuditSourceRegistry::exportValues( $this->extra, is_array( $record ) ? $record : array() ) ) );
        }
        return self::csvLine( $line );
    }

    /**
     * Whether the index can be read by ordered ids: the index is in use and its engine speaks SQL (not MongoDB's
     * emulation, which keeps the old pages).
     *
     * @return bool
     */
    public static function seekable()
    {
        if ( !expAuditConsole::indexUsable() )
            return false;
        return in_array( expAuditIndexSchema::type(), array( 'mysql', 'postgresql', 'sqlite', 'oracle' ), true );
    }

    /**
     * One CSV line; a value starting with = + - @ gets a leading apostrophe, so a spreadsheet does not run it.
     *
     * @param array $values
     * @return string
     */
    public static function csvLine( array $values )
    {
        $cells = array();
        foreach ( $values as $v )
        {
            $v = (string)$v;
            if ( $v !== '' && strpos( '=+-@', $v[0] ) !== false && !is_numeric( $v ) )
                $v = "'" . $v;
            $cells[] = '"' . str_replace( '"', '""', $v ) . '"';
        }
        return implode( ',', $cells ) . "\r\n";
    }
}
