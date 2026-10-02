<?php
/**
 * Fills the audit index from the channel files (doc/bc/6.0/audit.md, "Incremental indexing", "Rebuild",
 * "Pseudonymisation after 90 days").
 *
 * Incremental: each live file is read from its cursor's byte offset; whole lines only (a line without its "\n"
 * waits for the next run); each record's prev is checked against the cursor's last hash (a mismatch marks the
 * file broken in expaudit_file and records system.audit.chain.broken once); rows are inserted in batches of
 * BatchSize and the cursor moves in the same transaction, so a crash never indexes a line twice or skips one.
 * Records written by the 4.x import (<LogDir>/imported/) are indexed too, without a chain check.
 *
 * Nothing here runs at request time: the cronjob part (cronjobs/auditindex.php), the console when it opens,
 * exp:audit reindex and the upgrade script call it. One run at a time, under <LogDir>/.index.lock.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditIndexer
{
    /** The columns of expaudit_event in insert order */
    const COLUMNS = array( 'id', 'channel', 'seq', 'file_name', 'name', 'domain_name', 'severity', 'time_ms', 'request_id',
                           'siteaccess', 'engine', 'module_view', 'user_id', 'login', 'ip', 'session_h', 'ua', 'verb',
                           'object_type', 'object_id', 'object_name', 'target_type', 'target_id', 'result', 'reason',
                           'parent_id', 'depth', 'job_id', 'run_id', 'imported', 'pseudonymised', 'record', 'search_text' );

    /** Bytes read from a file at a time */
    const CHUNK = 1048576;

    /** @var eZDBInterface */
    protected $db;

    /** @var array expAuditConfig::get() */
    protected $config;

    /** @var array expAuditIndexSettings::get() */
    protected $settings;

    /** @var string */
    protected $type;

    /** @var bool SQLite with its FTS5 table */
    protected $fts5;

    /** @var resource|null */
    protected $lock = null;

    /** @var callable|null progress callback( string $message ) */
    protected $progress = null;

    /**
     * @param eZDBInterface|null $db
     * @param array|null $config expAuditConfig::get()
     * @param array|null $settings expAuditIndexSettings::get()
     */
    public function __construct( $db = null, ?array $config = null, ?array $settings = null )
    {
        $this->db = $db ?: eZDB::instance();
        $this->config = $config ?: expAuditConfig::get();
        $this->settings = $settings ?: expAuditIndexSettings::get();
        $this->type = expAuditIndexSchema::type( $this->db );
        $this->fts5 = expAuditIndexSchema::fullTextKind( $this->db ) === 'fts5';
    }

    /** @param callable|null $callback receives one line of progress */
    public function setProgress( $callback )
    {
        $this->progress = $callback;
    }

    /** @return string the live directory */
    public function logDir()
    {
        return $this->config['logDir'];
    }

    /**
     * Indexes what is new in the files.
     *
     * @param array $options channel (one channel only), maxRows (stop after this many rows; default: no limit),
     *                       maxSeconds (stop after this long), wait (wait for another run's lock; default false)
     * @return array ok, locked, installed, files, rows, batches, broken (file => line), skipped (unparseable
     *               lines), ms, error
     */
    public function run( array $options = array() )
    {
        $start = microtime( true );
        $stats = array( 'ok' => true, 'locked' => false, 'installed' => true, 'files' => 0, 'rows' => 0, 'batches' => 0,
                        'broken' => array(), 'skipped' => 0, 'ms' => 0, 'error' => '' );
        if ( !expAuditIndexSchema::isInstalled( $this->db ) )
        {
            $stats['ok'] = false;
            $stats['installed'] = false;
            $stats['error'] = 'The audit index tables do not exist (update/common/scripts/6.0/createaudittables.php creates them).';
            return $stats;
        }
        if ( !$this->acquireLock( !empty( $options['wait'] ) ) )
        {
            $stats['locked'] = true;
            return $stats;
        }
        try
        {
            $this->indexSources( $options, $stats, $start );
        }
        catch ( Throwable $e )
        {
            $stats['ok'] = false;
            $stats['error'] = $e->getMessage();
            eZDebug::writeError( 'The audit index run stopped: ' . $e->getMessage(), __METHOD__ );
        }
        $this->releaseLock();
        $stats['ms'] = (int)round( ( microtime( true ) - $start ) * 1000 );
        return $stats;
    }

    /**
     * The files to index, in chain order per channel: array( channel, file key (as stored), path, chained ).
     *
     * @param string|null $only one channel
     * @return array[]
     */
    public function sources( $only = null )
    {
        $dir = $this->logDir();
        $out = array();
        $reader = new expAuditReader( $dir );
        foreach ( array_keys( $reader->channels() ) as $channel )
        {
            if ( $only !== null && $only !== '' && $channel !== $only )
                continue;
            if ( $channel === 'read' && !$this->settings['indexReads'] )
                continue;
            foreach ( expAuditWriter::channelFiles( $dir, $channel ) as $f )
                $out[] = array( 'channel' => $channel, 'file' => $f, 'path' => $dir . '/' . $f, 'chained' => true );
        }
        // records imported from the 4.x logs (stage 5): outside the chain
        if ( is_dir( $dir . '/imported' ) )
        {
            $files = array();
            foreach ( (array)@scandir( $dir . '/imported' ) as $f )
            {
                $p = is_string( $f ) ? expAuditWriter::parseFileName( $f ) : null;
                if ( $p && ( $only === null || $only === '' || $p['channel'] === $only ) )
                    $files[] = $f;
            }
            sort( $files );
            foreach ( $files as $f )
            {
                $p = expAuditWriter::parseFileName( $f );
                $out[] = array( 'channel' => $p['channel'], 'file' => 'imported/' . $f, 'path' => $dir . '/imported/' . $f, 'chained' => false );
            }
        }
        return $out;
    }

    /**
     * @param array $options
     * @param array $stats
     * @param float $start
     */
    protected function indexSources( array $options, array &$stats, $start )
    {
        $maxRows = isset( $options['maxRows'] ) ? max( 1, (int)$options['maxRows'] ) : PHP_INT_MAX;
        $maxSeconds = isset( $options['maxSeconds'] ) ? (float)$options['maxSeconds'] : 0.0;
        $cursors = $this->cursors();
        $files = $this->fileStates();
        $lastHashOfChannel = array();
        $read = 0; // records read (rows already in the index are read but not inserted)

        foreach ( $this->sources( isset( $options['channel'] ) ? $options['channel'] : null ) as $src )
        {
            $key = $src['channel'] . "\n" . $src['file'];
            $cursor = isset( $cursors[$key] ) ? $cursors[$key] : null;
            $offset = $cursor ? (int)$cursor['byte_offset'] : 0;
            $lastSeq = $cursor ? (int)$cursor['last_seq'] : 0;
            $lastHash = $cursor ? (string)$cursor['last_hash'] : '';
            clearstatcache( true, $src['path'] );
            $size = (int)@filesize( $src['path'] );
            if ( $src['chained'] && $offset === 0 && isset( $lastHashOfChannel[$src['channel']] ) )
                $lastHash = $lastHashOfChannel[$src['channel']];
            if ( $size <= $offset )
            {
                if ( $src['chained'] && $lastHash !== '' )
                    $lastHashOfChannel[$src['channel']] = $lastHash;
                continue;
            }
            $stats['files']++;
            $state = isset( $files[$key] ) ? $files[$key] : null;
            $h = @fopen( $src['path'], 'rb' );
            if ( !$h )
            {
                $stats['ok'] = false;
                $stats['error'] = 'cannot read ' . $src['file'];
                continue;
            }
            fseek( $h, $offset );
            $rest = '';
            $pos = $offset;
            $batch = array();
            $broken = $state && $state['verified'] === 'broken';
            $breakLine = $state ? (int)$state['break_line'] : 0;
            $newBreak = null;
            while ( !feof( $h ) )
            {
                $chunk = fread( $h, self::CHUNK );
                if ( $chunk === false || $chunk === '' )
                    break;
                $buf = $rest . $chunk;
                $lines = explode( "\n", $buf );
                $rest = array_pop( $lines );
                foreach ( $lines as $line )
                {
                    $pos += strlen( $line ) + 1;
                    if ( $line === '' )
                        continue;
                    $rec = json_decode( $line, true );
                    $row = is_array( $rec ) ? expAuditIndexRow::fromRecord( $rec, $line, $src['file'] ) : null;
                    if ( $row === null )
                    {
                        // a torn line (the writer repairs after it) or a line that is not a record: the chain
                        // check of the next record says whether anything is missing
                        $stats['skipped']++;
                        continue;
                    }
                    if ( $src['chained'] )
                    {
                        $prev = isset( $rec['prev'] ) ? (string)$rec['prev'] : '';
                        if ( $lastHash !== '' && $prev !== $lastHash && !$broken )
                        {
                            $broken = true;
                            $breakLine = $row['seq'];
                            $newBreak = array( 'line' => $row['seq'], 'id' => $row['id'], 'expected' => $lastHash, 'prev' => $prev );
                        }
                        $lastHash = isset( $rec['hash'] ) ? (string)$rec['hash'] : $lastHash;
                    }
                    $lastSeq = max( $lastSeq, $row['seq'] );
                    $batch[] = $row;
                    if ( count( $batch ) >= $this->settings['batchSize'] || $read + count( $batch ) >= $maxRows )
                    {
                        $stats['rows'] += $this->commitBatch( $src, $batch, $pos, $lastSeq, $lastHash, $broken, $breakLine, $cursor !== null, $state !== null );
                        $cursor = $cursor ?: array();
                        $state = $state ?: array();
                        $read += count( $batch );
                        $stats['batches']++;
                        $batch = array();
                        $this->say( sprintf( '%s: %d rows indexed (offset %d)', $src['file'], $stats['rows'], $pos ) );
                        if ( $read >= $maxRows || ( $maxSeconds > 0 && microtime( true ) - $start > $maxSeconds ) )
                            break 2;
                    }
                }
            }
            fclose( $h );
            if ( $batch || $pos > $offset )
            {
                $stats['rows'] += $this->commitBatch( $src, $batch, $pos, $lastSeq, $lastHash, $broken, $breakLine, $cursor !== null, $state !== null );
                $read += count( $batch );
                if ( $batch )
                    $stats['batches']++;
            }
            if ( $src['chained'] )
                $lastHashOfChannel[$src['channel']] = $lastHash;
            if ( $broken )
                $stats['broken'][$src['file']] = $breakLine;
            if ( $newBreak !== null )
                $this->recordBreak( $src, $newBreak );
            if ( $read >= $maxRows || ( $maxSeconds > 0 && microtime( true ) - $start > $maxSeconds ) )
                break;
        }
    }

    /**
     * Inserts a batch, moves the cursor and the file state, in one transaction. Returns the rows inserted (ids
     * already in the index are left out).
     */
    protected function commitBatch( array $src, array $rows, $offset, $lastSeq, $lastHash, $broken, $breakLine, $hasCursor, $hasState )
    {
        $db = $this->db;
        $before = expAuditIndexSchema::errorHandling( $db );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        try
        {
            // ids already in the index (a rebuild that reads archives and live files, a copied file) are skipped
            if ( $rows )
            {
                $ids = array_map( function ( $r ) use ( $db ) { return "'" . $db->escapeString( $r['id'] ) . "'"; }, $rows );
                $have = array();
                foreach ( array_chunk( $ids, 500 ) as $chunk )
                    foreach ( (array)$db->arrayQuery( 'SELECT id FROM ' . expAuditIndexSchema::EVENT . ' WHERE id IN (' . implode( ',', $chunk ) . ')' ) as $r )
                        $have[$r['id']] = true;
                $seen = array();
                $rows = array_values( array_filter( $rows, function ( $r ) use ( &$have, &$seen ) {
                    if ( isset( $have[$r['id']] ) || isset( $seen[$r['id']] ) )
                        return false;
                    return $seen[$r['id']] = true;
                } ) );
            }
            $db->begin();
            foreach ( $rows as $row )
                $db->query( $this->insertSQL( $row ) );
            if ( $rows && $this->fts5 )
            {
                $ids = array_map( function ( $r ) use ( $db ) { return "'" . $db->escapeString( $r['id'] ) . "'"; }, $rows );
                foreach ( array_chunk( $ids, 500 ) as $chunk )
                    $db->query( 'INSERT INTO ' . expAuditIndexSchema::FTS . ' (rowid, search_text) SELECT rowid, search_text FROM '
                                . expAuditIndexSchema::EVENT . ' WHERE id IN (' . implode( ',', $chunk ) . ')' );
            }
            $now = (int)round( microtime( true ) * 1000 );
            $ch = "'" . $db->escapeString( $src['channel'] ) . "'";
            $fn = "'" . $db->escapeString( $src['file'] ) . "'";
            $hash = $lastHash === '' ? 'NULL' : "'" . $db->escapeString( $lastHash ) . "'";
            if ( $hasCursor )
                $db->query( 'UPDATE ' . expAuditIndexSchema::CURSOR . ' SET byte_offset = ' . (int)$offset . ', last_seq = ' . (int)$lastSeq
                            . ", last_hash = $hash, updated_ms = $now WHERE channel = $ch AND file_name = $fn" );
            else
                $db->query( 'INSERT INTO ' . expAuditIndexSchema::CURSOR . ' (channel, file_name, byte_offset, last_seq, last_hash, updated_ms) VALUES ('
                            . "$ch, $fn, " . (int)$offset . ', ' . (int)$lastSeq . ", $hash, $now)" );
            $verified = $broken ? "'broken'" : null;
            if ( $hasState )
                $db->query( 'UPDATE ' . expAuditIndexSchema::FILE . ' SET records = ' . (int)$lastSeq . ", state = 'live'"
                            . ( $verified ? ", verified = $verified, verified_ms = $now, break_line = " . (int)$breakLine : '' )
                            . " WHERE channel = $ch AND file_name = $fn" );
            else
                $db->query( 'INSERT INTO ' . expAuditIndexSchema::FILE . ' (channel, file_name, state, records, verified, verified_ms, break_line) VALUES ('
                            . "$ch, $fn, 'live', " . (int)$lastSeq . ', ' . ( $verified ?: "'unchecked'" ) . ', ' . ( $verified ? $now : 0 )
                            . ', ' . ( $verified ? (int)$breakLine : 0 ) . ')' );
            $db->commit();
        }
        catch ( Throwable $e )
        {
            $db->setErrorHandling( $before );
            throw new RuntimeException( 'Indexing ' . $src['file'] . ' failed: ' . $e->getMessage(), 0, $e );
        }
        $db->setErrorHandling( $before );
        return count( $rows );
    }

    /**
     * The INSERT of one row; Oracle gets long strings as concatenated CLOB pieces (a literal holds 4000 bytes).
     *
     * @param array $row
     * @return string
     */
    public function insertSQL( array $row )
    {
        $values = array();
        foreach ( self::COLUMNS as $col )
            $values[] = $this->literal( array_key_exists( $col, $row ) ? $row[$col] : null );
        return 'INSERT INTO ' . expAuditIndexSchema::EVENT . ' (' . implode( ', ', self::COLUMNS ) . ') VALUES (' . implode( ', ', $values ) . ')';
    }

    /**
     * @param mixed $v
     * @return string
     */
    protected function literal( $v )
    {
        if ( $v === null )
            return 'NULL';
        if ( is_int( $v ) )
            return (string)$v;
        $s = (string)$v;
        if ( $this->type === 'oracle' && strlen( $s ) > 3000 )
        {
            $pieces = array();
            for ( $i = 0; $i < strlen( $s ); )
            {
                $piece = function_exists( 'mb_strcut' ) ? mb_strcut( $s, $i, 3000, 'UTF-8' ) : substr( $s, $i, 3000 );
                if ( $piece === '' )
                    break;
                $pieces[] = "TO_CLOB('" . $this->db->escapeString( $piece ) . "')";
                $i += strlen( $piece );
            }
            return implode( ' || ', $pieces );
        }
        return "'" . $this->db->escapeString( $s ) . "'";
    }

    /** @return array "channel\nfile" => cursor row */
    public function cursors()
    {
        $out = array();
        foreach ( (array)expAuditIndexSchema::tryArrayQuery( $this->db, 'SELECT * FROM ' . expAuditIndexSchema::CURSOR ) as $r )
            $out[$r['channel'] . "\n" . $r['file_name']] = $r;
        return $out;
    }

    /** @return array "channel\nfile" => expaudit_file row */
    public function fileStates()
    {
        $out = array();
        foreach ( (array)expAuditIndexSchema::tryArrayQuery( $this->db, 'SELECT * FROM ' . expAuditIndexSchema::FILE ) as $r )
            $out[$r['channel'] . "\n" . $r['file_name']] = $r;
        return $out;
    }

    /**
     * How far the index is behind the files.
     *
     * @return array bytes, files (with unindexed bytes), per channel => bytes
     */
    public function lag()
    {
        $cursors = expAuditIndexSchema::isInstalled( $this->db ) ? $this->cursors() : array();
        $out = array( 'bytes' => 0, 'files' => 0, 'channels' => array() );
        foreach ( $this->sources() as $src )
        {
            clearstatcache( true, $src['path'] );
            $size = (int)@filesize( $src['path'] );
            $key = $src['channel'] . "\n" . $src['file'];
            $behind = $size - ( isset( $cursors[$key] ) ? (int)$cursors[$key]['byte_offset'] : 0 );
            if ( $behind > 0 )
            {
                $out['bytes'] += $behind;
                $out['files']++;
                $out['channels'][$src['channel']] = ( isset( $out['channels'][$src['channel']] ) ? $out['channels'][$src['channel']] : 0 ) + $behind;
            }
        }
        return $out;
    }

    /**
     * The records of the live files after the cursors (not indexed yet), as rows, newest first: the console
     * merges them into its first page.
     *
     * @param array $filters expAuditQuery::normalise()
     * @param string[]|null $channels the channels the user may read
     * @param int $limit
     * @param int $maxBytes read at most this much per file (from the end)
     * @return array[]
     */
    public function unindexedRows( array $filters, ?array $channels, $limit = 50, $maxBytes = 1048576 )
    {
        $cursors = expAuditIndexSchema::isInstalled( $this->db ) ? $this->cursors() : array();
        $f = $filters;
        if ( $channels !== null )
            $f['channels'] = isset( $f['channel'] ) ? array_values( array_intersect( $channels, array( $f['channel'] ) ) ) : $channels;
        elseif ( isset( $f['channel'] ) )
            $f['channels'] = array( $f['channel'] );
        $rows = array();
        foreach ( $this->sources() as $src )
        {
            if ( !$src['chained'] )
                continue;
            if ( isset( $f['channels'] ) && !in_array( $src['channel'], $f['channels'], true ) )
                continue;
            clearstatcache( true, $src['path'] );
            $size = (int)@filesize( $src['path'] );
            $key = $src['channel'] . "\n" . $src['file'];
            $offset = isset( $cursors[$key] ) ? (int)$cursors[$key]['byte_offset'] : 0;
            if ( $size <= $offset )
                continue;
            $from = max( $offset, $size - $maxBytes );
            $h = @fopen( $src['path'], 'rb' );
            if ( !$h )
                continue;
            fseek( $h, $from );
            $buf = stream_get_contents( $h );
            fclose( $h );
            $lines = explode( "\n", (string)$buf );
            array_pop( $lines );
            if ( $from > $offset )
                array_shift( $lines );
            foreach ( $lines as $line )
            {
                $rec = $line === '' ? null : json_decode( $line, true );
                $row = is_array( $rec ) ? expAuditIndexRow::fromRecord( $rec, $line, $src['file'] ) : null;
                if ( $row !== null && expAuditIndexRow::matches( $row, $f ) )
                    $rows[] = $row;
            }
        }
        usort( $rows, function ( $a, $b ) { return array( $b['time_ms'], $b['id'] ) <=> array( $a['time_ms'], $a['id'] ); } );
        return array_slice( $rows, 0, max( 1, (int)$limit ) );
    }

    /**
     * Empties the index and indexes every live file again (and the imported records).
     *
     * @param array $options channel (only that channel's rows are removed and indexed again)
     * @return array the stats of run(), plus removed
     */
    public function rebuild( array $options = array() )
    {
        $start = microtime( true );
        if ( !expAuditIndexSchema::isInstalled( $this->db ) )
            return array( 'ok' => false, 'installed' => false, 'error' => 'The audit index tables do not exist.', 'rows' => 0 );
        if ( !$this->acquireLock( true ) )
            return array( 'ok' => false, 'locked' => true, 'rows' => 0 );
        $channel = isset( $options['channel'] ) && $options['channel'] !== '' ? (string)$options['channel'] : null;
        $where = $channel === null ? '' : " WHERE channel = '" . $this->db->escapeString( $channel ) . "'";
        $removed = 0;
        $n = expAuditIndexSchema::tryArrayQuery( $this->db, 'SELECT COUNT(*) AS n FROM ' . expAuditIndexSchema::EVENT . $where );
        $removed = $n ? (int)$n[0]['n'] : 0;
        $db = $this->db;
        $before = expAuditIndexSchema::errorHandling( $db );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        try
        {
            $db->begin();
            if ( $this->fts5 )
            {
                if ( $channel === null )
                    $db->query( 'INSERT INTO ' . expAuditIndexSchema::FTS . ' (' . expAuditIndexSchema::FTS . ") VALUES ('delete-all')" );
                else
                    $db->query( 'DELETE FROM ' . expAuditIndexSchema::FTS . ' WHERE rowid IN (SELECT rowid FROM ' . expAuditIndexSchema::EVENT . $where . ')' );
            }
            foreach ( array( expAuditIndexSchema::EVENT, expAuditIndexSchema::CURSOR, expAuditIndexSchema::FILE ) as $t )
                $db->query( 'DELETE FROM ' . $t . $where );
            $db->commit();
        }
        catch ( Throwable $e )
        {
            $db->setErrorHandling( $before );
            $this->releaseLock();
            return array( 'ok' => false, 'error' => $e->getMessage(), 'rows' => 0 );
        }
        $db->setErrorHandling( $before );
        $this->releaseLock();
        $stats = $this->run( array( 'channel' => $channel, 'wait' => true ) );
        $stats['removed'] = $removed;
        $stats['ms'] = (int)round( ( microtime( true ) - $start ) * 1000 );
        if ( class_exists( 'expAudit' ) )
            expAudit::event( 'system.audit.reindex', array( 'object' => array( 'type' => 'index', 'id' => $channel === null ? 'all' : $channel ),
                                                           'after' => array( 'rows' => $stats['rows'], 'removed' => $removed, 'ms' => $stats['ms'] ),
                                                           'result' => !empty( $stats['ok'] ) ? 'success' : 'failed' ) );
        return $stats;
    }

    /**
     * Replaces the personal fields of rows older than PseudonymiseAfterDays by their hashed form.
     *
     * @param array $options dryRun, now (epoch seconds, tests), maxRows, channels (only these channels)
     * @return array ok, rows, error
     */
    public function pseudonymise( array $options = array() )
    {
        $now = isset( $options['now'] ) ? (int)$options['now'] : time();
        $cutoff = ( $now - $this->settings['pseudonymiseAfterDays'] * 86400 ) * 1000;
        $out = array( 'ok' => true, 'rows' => 0, 'error' => '', 'cutoff' => gmdate( 'Y-m-d\TH:i:s\Z', (int)( $cutoff / 1000 ) ) );
        if ( !expAuditIndexSchema::isInstalled( $this->db ) )
            return array( 'ok' => false, 'rows' => 0, 'error' => 'The audit index tables do not exist.' );
        $privacy = new expAuditPrivacy( $this->config, new expAuditKeys( $this->config ) );
        if ( $privacy->hash( 'probe' ) === null )
            return array( 'ok' => false, 'rows' => 0, 'error' => 'No pseudonym key yet ([AuditKeySettings] PseudonymKey).' );
        $where = 'pseudonymised = 0 AND time_ms < ' . (int)$cutoff . $this->channelCondition( $options );
        if ( !empty( $options['dryRun'] ) )
        {
            $n = expAuditIndexSchema::tryArrayQuery( $this->db, 'SELECT COUNT(*) AS n FROM ' . expAuditIndexSchema::EVENT . ' WHERE ' . $where );
            $out['rows'] = $n ? (int)$n[0]['n'] : 0;
            return $out;
        }
        $maxRows = isset( $options['maxRows'] ) ? (int)$options['maxRows'] : PHP_INT_MAX;
        $db = $this->db;
        $before = expAuditIndexSchema::errorHandling( $db );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        try
        {
            while ( $out['rows'] < $maxRows )
            {
                $rows = $db->arrayQuery( 'SELECT id, name, login, ip, ua, session_h, record FROM ' . expAuditIndexSchema::EVENT
                                         . ' WHERE ' . $where . ' ORDER BY time_ms', array( 'limit' => $this->settings['batchSize'] ) );
                if ( !$rows )
                    break;
                $db->begin();
                foreach ( $rows as $r )
                {
                    $id = "'" . $db->escapeString( $r['id'] ) . "'";
                    $set = array( 'pseudonymised = 1' );
                    foreach ( array( 'login', 'ip', 'ua', 'session_h' ) as $col )
                        if ( $r[$col] !== null && $r[$col] !== '' && strpos( $r[$col], 'h:' ) !== 0 )
                            $set[] = "$col = '" . $db->escapeString( (string)$privacy->hash( $r[$col] ) ) . "'";
                    $rec = json_decode( (string)$r['record'], true );
                    if ( is_array( $rec ) )
                    {
                        $rec = $this->pseudonymiseRecord( $rec, $privacy );
                        $set[] = 'record = ' . $this->literal( json_encode( $rec, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
                        $set[] = 'search_text = ' . $this->literal( expAuditIndexRow::searchText( $rec ) );
                        $set[] = 'object_name = ' . $this->literal( isset( $rec['object']['name'] ) && is_scalar( $rec['object']['name'] )
                                                                    ? expAuditIndexRow::cut( (string)$rec['object']['name'], 255 ) : null );
                    }
                    if ( $this->fts5 )
                        $db->query( 'DELETE FROM ' . expAuditIndexSchema::FTS . ' WHERE rowid = (SELECT rowid FROM ' . expAuditIndexSchema::EVENT . " WHERE id = $id)" );
                    $db->query( 'UPDATE ' . expAuditIndexSchema::EVENT . ' SET ' . implode( ', ', $set ) . " WHERE id = $id" );
                    if ( $this->fts5 )
                        $db->query( 'INSERT INTO ' . expAuditIndexSchema::FTS . ' (rowid, search_text) SELECT rowid, search_text FROM '
                                    . expAuditIndexSchema::EVENT . " WHERE id = $id" );
                    $out['rows']++;
                }
                $db->commit();
            }
        }
        catch ( Throwable $e )
        {
            $out['ok'] = false;
            $out['error'] = $e->getMessage();
        }
        $db->setErrorHandling( $before );
        if ( $out['rows'] > 0 && class_exists( 'expAudit' ) )
            expAudit::event( 'system.audit.pseudonymise', array( 'object' => array( 'type' => 'index', 'id' => expAuditIndexSchema::EVENT ),
                                                                'after' => array( 'rows' => $out['rows'], 'before' => $out['cutoff'] ) ) );
        return $out;
    }

    /**
     * The record with its personal fields hashed: the actor's login, address, user agent and OS user, and for
     * access.user.* events the person fields of object, target, before and after.
     *
     * @param array $rec
     * @param expAuditPrivacy $privacy
     * @return array
     */
    public function pseudonymiseRecord( array $rec, expAuditPrivacy $privacy )
    {
        $hash = function ( $v ) use ( $privacy ) {
            if ( !is_scalar( $v ) || $v === '' || ( is_string( $v ) && strpos( $v, 'h:' ) === 0 ) )
                return $v;
            $h = $privacy->hash( (string)$v );
            return $h === null ? $v : $h;
        };
        foreach ( array( 'login', 'ip', 'ua', 'session' ) as $k )
            if ( isset( $rec['actor'][$k] ) )
                $rec['actor'][$k] = $hash( $rec['actor'][$k] );
        if ( isset( $rec['actor']['cli']['os_user'] ) )
            $rec['actor']['cli']['os_user'] = $hash( $rec['actor']['cli']['os_user'] );
        foreach ( array( 'login', 'os_user' ) as $k )
            if ( isset( $rec['actor']['impersonator'][$k] ) )
                $rec['actor']['impersonator'][$k] = $hash( $rec['actor']['impersonator'][$k] );
        $isPerson = strpos( (string)$rec['name'], 'access.user.' ) === 0 || strpos( (string)$rec['name'], 'access.session.' ) === 0;
        if ( $isPerson )
        {
            $personKeys = array( 'login', 'email', 'attempted_login', 'first_name', 'last_name', 'name' );
            $walk = function ( array $data ) use ( &$walk, $hash, $personKeys ) {
                foreach ( $data as $k => $v )
                {
                    if ( is_array( $v ) )
                        $data[$k] = $walk( $v );
                    elseif ( is_string( $k ) && ( in_array( $k, $personKeys, true ) || substr( $k, -6 ) === '_email' ) )
                        $data[$k] = $hash( $v );
                }
                return $data;
            };
            foreach ( array( 'object', 'target', 'before', 'after' ) as $k )
                if ( isset( $rec[$k] ) && is_array( $rec[$k] ) )
                    $rec[$k] = $walk( $rec[$k] );
        }
        $rec['pseudonymised'] = true;
        return $rec;
    }

    /**
     * Removes rows older than KeepDays (they stay in the files and archives).
     *
     * @param array $options now (epoch seconds), dryRun, channels (only these channels)
     * @return array ok, rows, error
     */
    public function purgeOld( array $options = array() )
    {
        $now = isset( $options['now'] ) ? (int)$options['now'] : time();
        $cutoff = ( $now - $this->settings['keepDays'] * 86400 ) * 1000;
        if ( !expAuditIndexSchema::isInstalled( $this->db ) )
            return array( 'ok' => false, 'rows' => 0, 'error' => 'The audit index tables do not exist.' );
        $where = ' WHERE time_ms < ' . (int)$cutoff . $this->channelCondition( $options );
        $n = expAuditIndexSchema::tryArrayQuery( $this->db, 'SELECT COUNT(*) AS n FROM ' . expAuditIndexSchema::EVENT . $where );
        $rows = $n ? (int)$n[0]['n'] : 0;
        if ( $rows === 0 || !empty( $options['dryRun'] ) )
            return array( 'ok' => true, 'rows' => $rows, 'error' => '' );
        $db = $this->db;
        $before = expAuditIndexSchema::errorHandling( $db );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        $out = array( 'ok' => true, 'rows' => $rows, 'error' => '' );
        try
        {
            $db->begin();
            if ( $this->fts5 )
                $db->query( 'DELETE FROM ' . expAuditIndexSchema::FTS . ' WHERE rowid IN (SELECT rowid FROM ' . expAuditIndexSchema::EVENT . $where . ')' );
            $db->query( 'DELETE FROM ' . expAuditIndexSchema::EVENT . $where );
            $db->commit();
        }
        catch ( Throwable $e )
        {
            $out = array( 'ok' => false, 'rows' => 0, 'error' => $e->getMessage() );
        }
        $db->setErrorHandling( $before );
        if ( $out['rows'] > 0 && class_exists( 'expAudit' ) )
            expAudit::event( 'system.audit.purge', array( 'object' => array( 'type' => 'index', 'id' => expAuditIndexSchema::EVENT ),
                                                         'after' => array( 'rows' => $out['rows'], 'oldest_kept' => gmdate( 'Y-m-d', (int)( $cutoff / 1000 ) ) ) ) );
        return $out;
    }

    /**
     * Records the first break found in a file (once: the file is marked broken from then on).
     */
    protected function recordBreak( array $src, array $break )
    {
        eZDebug::writeError( sprintf( 'Audit chain broken in %s at seq %d (record %s): prev %s, expected %s',
                                      $src['file'], $break['line'], $break['id'], $break['prev'], $break['expected'] ), __METHOD__ );
        if ( class_exists( 'expAudit' ) )
            expAudit::event( 'system.audit.chain.broken', array(
                'object' => array( 'type' => 'file', 'id' => $src['file'] ),
                'target' => array( 'type' => 'line', 'id' => (int)$break['line'] ),
                'after' => array( 'file' => $src['file'], 'line' => (int)$break['line'], 'kind' => 'link', 'record' => $break['id'],
                                  'found_by' => 'index' ),
                'result' => 'failed', 'reason' => 'link' ) );
    }

    /**
     * Stores the verifier's result for a channel's files (the console and the cronjob part verify).
     *
     * @param string $channel
     * @param array $result expAuditVerifier::verifyChannel()
     * @param string[] $files the files that were verified (all of the channel's when empty)
     */
    public function storeVerification( $channel, array $result, array $files = array() )
    {
        if ( !expAuditIndexSchema::isInstalled( $this->db ) || empty( $result['files'] ) )
            return;
        $db = $this->db;
        $states = $this->fileStates();
        $breakAt = array();
        foreach ( (array)$result['breaks'] as $b )
            if ( !isset( $breakAt[$b['file']] ) )
                $breakAt[$b['file']] = (int)$b['line'];
        $repaired = array();
        foreach ( (array)( isset( $result['repairs'] ) ? $result['repairs'] : array() ) as $r )
            if ( isset( $r['file'] ) )
                $repaired[$r['file']] = true;
        $now = (int)round( microtime( true ) * 1000 );
        if ( !$files )
            $files = expAuditWriter::channelFiles( $this->logDir(), $channel );
        foreach ( $files as $file )
        {
            if ( !is_string( $file ) )
                continue;
            $verified = isset( $breakAt[$file] ) ? 'broken' : ( isset( $repaired[$file] ) ? 'repaired' : 'intact' );
            $ch = "'" . $db->escapeString( $channel ) . "'";
            $fn = "'" . $db->escapeString( $file ) . "'";
            $line = isset( $breakAt[$file] ) ? $breakAt[$file] : 0;
            if ( isset( $states[$channel . "\n" . $file] ) )
                expAuditIndexSchema::tryQuery( $db, 'UPDATE ' . expAuditIndexSchema::FILE . " SET verified = '$verified', verified_ms = $now, break_line = $line"
                                                    . " WHERE channel = $ch AND file_name = $fn" );
            else
                expAuditIndexSchema::tryQuery( $db, 'INSERT INTO ' . expAuditIndexSchema::FILE . ' (channel, file_name, state, records, verified, verified_ms, break_line)'
                                                    . " VALUES ($ch, $fn, 'live', 0, '$verified', $now, $line)" );
        }
    }

    /**
     * @param array $options channels => string[]
     * @return string " AND channel IN (...)" or ''
     */
    protected function channelCondition( array $options )
    {
        if ( empty( $options['channels'] ) || !is_array( $options['channels'] ) )
            return '';
        $db = $this->db;
        return ' AND channel IN (' . implode( ', ', array_map( function ( $c ) use ( $db ) { return "'" . $db->escapeString( (string)$c ) . "'"; }, $options['channels'] ) ) . ')';
    }

    /** @return bool the lock is held */
    protected function acquireLock( $wait )
    {
        if ( $this->lock )
            return true;
        $dir = $this->logDir();
        if ( !is_dir( $dir ) && !expAuditWriter::ensureDirectory( $dir ) )
            return false;
        $path = $dir . '/.index.lock';
        $new = !file_exists( $path );
        $h = @fopen( $path, 'c' );
        if ( !$h )
            return false;
        if ( $new )
            expAuditWriter::ownLikeParent( $path, 0660 );
        if ( !flock( $h, $wait ? LOCK_EX : LOCK_EX | LOCK_NB ) )
        {
            fclose( $h );
            return false;
        }
        $this->lock = $h;
        return true;
    }

    protected function releaseLock()
    {
        if ( $this->lock )
        {
            flock( $this->lock, LOCK_UN );
            fclose( $this->lock );
            $this->lock = null;
        }
    }

    protected function say( $message )
    {
        if ( $this->progress )
            call_user_func( $this->progress, $message );
    }
}
