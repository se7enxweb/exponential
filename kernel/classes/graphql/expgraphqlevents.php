<?php
/**
 * The event journal of the GraphQL API (doc/eng-us/features/6.0/graphql.md, "Real time: webhooks and subscriptions"):
 * what happened to content (publish, update, trash, delete, move, hide, reveal, and the GraphQL mutations), one JSON
 * line per event, appended by expGraphQLEventListener at the end of the request that changed the content, and read by
 * the two consumers that tell others about it:
 *
 *   the webhooks      expGraphQLWebhooks reads from its cursor (the cronjob part graphqlwebhooks) and sends each
 *                     event to the end points that want it, signed, with retries
 *   the subscriptions an open subscription (/api/graphql/subscribe, SSE, or WebSocket through Velocity) follows the
 *                     journal and sends each event it matches, as the subscriber may read it
 *
 * Files, not a table: a reader follows the journal without a database connection (a subscription waits minutes for
 * the next event and must not hold one), every PHP-FPM and Velocity worker sees the same events, and a writer appends
 * under an exclusive lock. The journal is var/<site>/graphql/events/events-<generation>.jsonl; when the current file
 * passes JournalMaxBytes a new generation starts, and only the newest JournalKeep files stay.
 *
 * A position is "<generation>.<byte offset>": where the next read starts. It is the id of an event in a subscription
 * (SSE id:, the Last-Event-ID a client sends back to resume) and the cursor of the webhooks.
 *
 * An event (record()) is a flat array of scalars and lists of scalars: event, id, time, objectId, remoteId, version,
 * classIdentifier, classId, sectionId, ownerId, name, language, languages, nodeIds, mainNodeId, parentNodeIds,
 * pathStrings, stateIds, userId, siteaccess, mutation, oldParentNodeId, newParentNodeId.
 *
 * Plain PHP and files only: no kernel class is needed to read the journal (tests run it without a database).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLEvents
{
    /** The events of the journal, as webhooks and subscriptions name them. */
    const PUBLISH = 'publish';
    const UPDATE = 'update';
    const TRASH = 'trash';
    const DELETE = 'delete';
    const MOVE = 'move';
    const HIDE = 'hide';
    const REVEAL = 'reveal';
    const MUTATION = 'mutation';
    /** A webhook's test event (exp:graphql:webhooks --test, the Test button); never in the journal. */
    const PING = 'ping';

    /** Every event a webhook or a subscription may ask for. */
    const ALL = array( self::PUBLISH, self::UPDATE, self::TRASH, self::DELETE, self::MOVE, self::HIDE, self::REVEAL, self::MUTATION );

    /** The JSON of a journal line: version 1. */
    const VERSION = 1;

    /** @var string the directory of the journal */
    protected $dir;

    /** @var int bytes a journal file grows to before a new one starts */
    protected $maxBytes;

    /** @var int journal files kept */
    protected $keep;

    /**
     * @param string $dir
     * @param int $maxBytes
     * @param int $keep
     */
    public function __construct( $dir, $maxBytes = 5242880, $keep = 3 )
    {
        $this->dir = rtrim( (string)$dir, '/' );
        $this->maxBytes = max( 1024, (int)$maxBytes );
        $this->keep = max( 1, (int)$keep );
    }

    /**
     * The journal of the installation: var/<site>/graphql/events, graphql.ini [GraphQLEvents].
     *
     * @return self
     */
    public static function fromSettings()
    {
        $ini = eZINI::instance( 'graphql.ini' );
        $max = $ini->hasVariable( 'GraphQLEvents', 'JournalMaxBytes' ) ? self::bytes( $ini->variable( 'GraphQLEvents', 'JournalMaxBytes' ) ) : 0;
        $keep = $ini->hasVariable( 'GraphQLEvents', 'JournalKeep' ) ? (int)$ini->variable( 'GraphQLEvents', 'JournalKeep' ) : 0;
        return new self( eZSys::varDirectory() . '/graphql/events', $max > 0 ? $max : 5242880, $keep > 0 ? $keep : 3 );
    }

    /**
     * @param string $value 5242880, 512k, 5M, 1G
     * @return int bytes (0 for a value that is not one)
     */
    public static function bytes( $value )
    {
        if ( !preg_match( '/^\s*(\d+)\s*([kmg]?)b?\s*$/i', (string)$value, $m ) )
            return 0;
        $factor = array( '' => 1, 'k' => 1024, 'm' => 1048576, 'g' => 1073741824 );
        return (int)$m[1] * $factor[strtolower( $m[2] )];
    }

    /** @return string */
    public function directory()
    {
        return $this->dir;
    }

    /**
     * Whether a text names an event.
     *
     * @param string $event
     * @return bool
     */
    public static function isEvent( $event )
    {
        return in_array( (string)$event, self::ALL, true );
    }

    /**
     * A new event id: sortable by time (microseconds) and unique across processes.
     *
     * @param float|null $time
     * @return string
     */
    public static function newId( $time = null )
    {
        $time = $time === null ? microtime( true ) : (float)$time;
        return sprintf( '%d%06d-%s', (int)$time, (int)round( ( $time - (int)$time ) * 1000000 ) % 1000000, bin2hex( random_bytes( 4 ) ) );
    }

    /**
     * An event as it goes into the journal: the fields it has, of the right types, an id and a time when it has none.
     *
     * @param array $data
     * @return array
     */
    public static function record( array $data )
    {
        $ints = array( 'objectId', 'version', 'classId', 'sectionId', 'ownerId', 'mainNodeId', 'userId', 'oldParentNodeId', 'newParentNodeId' );
        $intLists = array( 'nodeIds', 'parentNodeIds', 'stateIds' );
        $strings = array( 'remoteId', 'classIdentifier', 'name', 'language', 'siteaccess', 'mutation' );
        $stringLists = array( 'languages', 'pathStrings' );
        $record = array( 'v' => self::VERSION,
                         'id' => isset( $data['id'] ) && is_string( $data['id'] ) && $data['id'] !== '' ? $data['id'] : self::newId(),
                         'event' => (string)( isset( $data['event'] ) ? $data['event'] : '' ),
                         'time' => isset( $data['time'] ) ? round( (float)$data['time'], 3 ) : round( microtime( true ), 3 ) );
        foreach ( $ints as $key )
            if ( isset( $data[$key] ) && $data[$key] !== '' )
                $record[$key] = (int)$data[$key];
        foreach ( $intLists as $key )
            if ( isset( $data[$key] ) )
                $record[$key] = array_values( array_unique( array_map( 'intval', (array)$data[$key] ) ) );
        foreach ( $strings as $key )
            if ( isset( $data[$key] ) && $data[$key] !== '' )
                $record[$key] = (string)$data[$key];
        foreach ( $stringLists as $key )
            if ( isset( $data[$key] ) )
                $record[$key] = array_values( array_unique( array_map( 'strval', (array)$data[$key] ) ) );
        return $record;
    }

    /**
     * Appends events to the journal (one exclusive lock for all of them), and starts a new generation when the
     * current file has grown past its size.
     *
     * @param array[] $events records of record()
     * @return string|null the position after the last event (null: nothing written)
     */
    public function append( array $events )
    {
        if ( !$events || !self::ensureDirectory( $this->dir ) )
            return null;
        $bytes = '';
        foreach ( $events as $event )
        {
            $json = json_encode( self::record( $event ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE );
            if ( $json !== false )
                $bytes .= $json . "\n";
        }
        if ( $bytes === '' )
            return null;
        for ( $attempt = 0; $attempt < 3; $attempt++ )
        {
            $generation = $this->currentGeneration();
            $path = $this->file( $generation );
            $created = !is_file( $path );
            $h = @fopen( $path, 'ab' );
            if ( !$h )
                return null;
            flock( $h, LOCK_EX );
            // a writer that waited for the lock while another started the next generation writes there instead
            if ( $this->currentGeneration() !== $generation )
            {
                flock( $h, LOCK_UN );
                fclose( $h );
                continue;
            }
            fseek( $h, 0, SEEK_END );
            $written = fwrite( $h, $bytes );
            fflush( $h );
            $end = ftell( $h );
            flock( $h, LOCK_UN );
            fclose( $h );
            if ( $created )
                self::ownLikeParent( $path, 0640 );
            if ( $written !== strlen( $bytes ) )
                return null;
            if ( $end >= $this->maxBytes )
                $this->rotate( $generation );
            return $generation . '.' . $end;
        }
        return null;
    }

    /**
     * Starts the generation after $generation (when nobody did yet) and removes the files older than the newest
     * JournalKeep.
     *
     * @param int $generation
     */
    protected function rotate( $generation )
    {
        $lock = @fopen( $this->dir . '/journal.lock', 'cb' );
        if ( $lock )
            flock( $lock, LOCK_EX );
        if ( $this->currentGeneration() === $generation )
        {
            $next = $this->file( $generation + 1 );
            if ( @touch( $next ) )
                self::ownLikeParent( $next, 0640 );
        }
        foreach ( $this->generations() as $old )
        {
            if ( $old <= $this->currentGeneration() - $this->keep )
                @unlink( $this->file( $old ) );
        }
        if ( $lock )
        {
            flock( $lock, LOCK_UN );
            fclose( $lock );
        }
    }

    /**
     * @param int $generation
     * @return string
     */
    public function file( $generation )
    {
        return $this->dir . '/events-' . (int)$generation . '.jsonl';
    }

    /**
     * @return int[] the generations on disk, oldest first
     */
    public function generations()
    {
        $list = array();
        foreach ( (array)@scandir( $this->dir ) as $name )
        {
            if ( preg_match( '/^events-(\d+)\.jsonl$/', (string)$name, $m ) )
                $list[] = (int)$m[1];
        }
        sort( $list );
        return $list;
    }

    /**
     * @return int the generation written now (1 for an empty journal)
     */
    public function currentGeneration()
    {
        clearstatcache();
        $list = $this->generations();
        return $list ? (int)end( $list ) : 1;
    }

    /**
     * @return string the position at the end of the journal: a reader starting here reads only what comes next
     */
    public function end()
    {
        $generation = $this->currentGeneration();
        $path = $this->file( $generation );
        clearstatcache( true, $path );
        return $generation . '.' . ( is_file( $path ) ? (int)filesize( $path ) : 0 );
    }

    /**
     * @param string $position
     * @return bool it has the form of a position ("3.1045")
     */
    public static function isPosition( $position )
    {
        return is_string( $position ) && preg_match( '/^\d{1,9}\.\d{1,12}$/', $position ) === 1;
    }

    /**
     * Reads the events after a position.
     *
     * @param string|null $position null or not a position: the end (nothing is read)
     * @param int $limit at most this many events
     * @return array events (the records, each with its position: _position, where the next read after it starts),
     *               position (where the next read starts), lost (true when the position's file was removed already
     *               and the reading went on from the oldest file kept)
     */
    public function read( $position, $limit = 100 )
    {
        if ( !self::isPosition( $position ) )
            return array( 'events' => array(), 'position' => $this->end(), 'lost' => false );
        list( $generation, $offset ) = array_map( 'intval', explode( '.', $position ) );
        $events = array();
        $lost = false;
        $generations = $this->generations();
        if ( $generations && !in_array( $generation, $generations, true ) && $generation < end( $generations ) )
        {
            // the file of this position is gone: on from the oldest one still there
            $older = array_values( array_filter( $generations, function ( $g ) use ( $generation ) { return $g > $generation; } ) );
            $generation = $older ? $older[0] : end( $generations );
            $offset = 0;
            $lost = true;
        }
        for ( $guard = 0; $guard < 16 && count( $events ) < $limit; $guard++ )
        {
            $path = $this->file( $generation );
            clearstatcache( true, $path );
            if ( is_file( $path ) && filesize( $path ) > $offset )
            {
                $h = @fopen( $path, 'rb' );
                if ( $h )
                {
                    fseek( $h, $offset );
                    while ( count( $events ) < $limit && ( $line = fgets( $h ) ) !== false )
                    {
                        if ( substr( $line, -1 ) !== "\n" )
                            break; // half written: read again next time
                        $offset += strlen( $line );
                        $record = json_decode( $line, true );
                        if ( is_array( $record ) && isset( $record['event'] ) )
                        {
                            $record['_position'] = $generation . '.' . $offset;
                            $events[] = $record;
                        }
                    }
                    fclose( $h );
                }
            }
            if ( count( $events ) >= $limit || !is_file( $this->file( $generation + 1 ) ) )
                break;
            // this file is finished once a newer one exists: on to the next
            clearstatcache( true, $path );
            if ( is_file( $path ) && filesize( $path ) > $offset )
                continue;
            $generation++;
            $offset = 0;
        }
        return array( 'events' => $events, 'position' => $generation . '.' . $offset, 'lost' => $lost );
    }

    /**
     * Whether an event passes a filter.
     *
     * @param array $event a record
     * @param array $filter events (names; empty: any), classes (class identifiers; empty: any), parentId (a node id
     *                      the content is directly below), subtreeId (a node id the content is anywhere below, or at),
     *                      subtrees (several such node ids; any of them), rootNodeIds (the content is below one of
     *                      them: the content tree of a siteaccess), languages (any of them)
     * @return bool
     */
    public static function matches( array $event, array $filter )
    {
        $name = isset( $event['event'] ) ? (string)$event['event'] : '';
        if ( !empty( $filter['events'] ) && !in_array( $name, (array)$filter['events'], true ) )
            return false;
        if ( !empty( $filter['classes'] )
             && !( isset( $event['classIdentifier'] ) && in_array( (string)$event['classIdentifier'], (array)$filter['classes'], true ) ) )
            return false;
        $parents = isset( $event['parentNodeIds'] ) ? array_map( 'intval', (array)$event['parentNodeIds'] ) : array();
        if ( isset( $event['newParentNodeId'] ) )
            $parents[] = (int)$event['newParentNodeId'];
        if ( isset( $filter['parentId'] ) && $filter['parentId'] !== null && !in_array( (int)$filter['parentId'], $parents, true ) )
            return false;
        $subtrees = isset( $filter['subtrees'] ) ? array_map( 'intval', (array)$filter['subtrees'] ) : array();
        if ( isset( $filter['subtreeId'] ) && $filter['subtreeId'] !== null )
            $subtrees[] = (int)$filter['subtreeId'];
        if ( $subtrees && !self::below( $event, $subtrees ) )
            return false;
        if ( !empty( $filter['rootNodeIds'] ) && !self::below( $event, array_map( 'intval', (array)$filter['rootNodeIds'] ) ) )
            return false;
        if ( !empty( $filter['languages'] ) )
        {
            $languages = isset( $event['languages'] ) ? (array)$event['languages'] : array();
            if ( isset( $event['language'] ) )
                $languages[] = (string)$event['language'];
            // an event without languages (a move, a removal) concerns every language
            if ( $languages && !array_intersect( $languages, (array)$filter['languages'] ) )
                return false;
        }
        return true;
    }

    /**
     * @param array $event
     * @param int[] $nodeIds
     * @return bool the content of the event is at or below one of the nodes (by its path strings, its node ids, or
     *              its parents)
     */
    public static function below( array $event, array $nodeIds )
    {
        $nodes = isset( $event['nodeIds'] ) ? array_map( 'intval', (array)$event['nodeIds'] ) : array();
        $parents = isset( $event['parentNodeIds'] ) ? array_map( 'intval', (array)$event['parentNodeIds'] ) : array();
        foreach ( $nodeIds as $nodeId )
        {
            $nodeId = (int)$nodeId;
            if ( in_array( $nodeId, $nodes, true ) || in_array( $nodeId, $parents, true ) )
                return true;
            foreach ( isset( $event['pathStrings'] ) ? (array)$event['pathStrings'] : array() as $path )
            {
                if ( strpos( (string)$path, '/' . $nodeId . '/' ) !== false )
                    return true;
            }
        }
        return false;
    }

    /**
     * The event as a client sees it (webhooks, subscriptions): the fields of the GraphQL type ContentEvent, with the
     * time in ISO 8601. The internal fields (classId, sectionId, ownerId, stateIds, pathStrings, userId) stay out.
     *
     * @param array $event
     * @return array
     */
    public static function publicFields( array $event )
    {
        $time = isset( $event['time'] ) ? (float)$event['time'] : microtime( true );
        $public = array( 'id' => isset( $event['id'] ) ? (string)$event['id'] : '',
                         'event' => isset( $event['event'] ) ? (string)$event['event'] : '',
                         'time' => gmdate( 'Y-m-d\TH:i:s', (int)$time ) . sprintf( '.%03dZ', (int)round( ( $time - (int)$time ) * 1000 ) % 1000 ) );
        foreach ( array( 'objectId', 'remoteId', 'version', 'classIdentifier', 'name', 'language', 'languages', 'nodeIds', 'mainNodeId',
                         'parentNodeIds', 'oldParentNodeId', 'newParentNodeId', 'mutation' ) as $key )
            $public[$key] = isset( $event[$key] ) ? $event[$key] : ( in_array( $key, array( 'languages', 'nodeIds', 'parentNodeIds' ), true ) ? array() : null );
        return $public;
    }

    /**
     * @param string $dir
     * @return bool
     */
    public static function ensureDirectory( $dir )
    {
        if ( is_dir( $dir ) )
            return true;
        if ( class_exists( 'expAuditWriter' ) )
            return expAuditWriter::ensureDirectory( $dir );
        return @mkdir( $dir, eZDir::dirMode( 0770 ), true ) || is_dir( $dir );
    }

    /**
     * A file written by one user (root running Velocity, the site user running PHP-FPM) stays readable and writable
     * by the other: the owner and group of its directory.
     *
     * @param string $path
     * @param int $mode
     */
    public static function ownLikeParent( $path, $mode )
    {
        if ( class_exists( 'expAuditWriter' ) )
            expAuditWriter::ownLikeParent( $path, $mode );
        else
            @chmod( $path, eZFile::fileMode( $mode ) );
    }
}

?>
