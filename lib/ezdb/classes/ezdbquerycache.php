<?php
/**
 * File containing the eZDBQueryCache class.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package lib
 */

/**
 * A result cache for the SQL drivers (MySQL/MariaDB, PostgreSQL, SQLite):
 * the rows a SELECT returned, answered again until a write to one of the
 * tables it read makes them stale. The MongoDB driver does not use it.
 *
 * settings/querycache.ini [QueryCacheSettings] Mode:
 *   off      nothing is kept (the default)
 *   request  a memo for the length of one request
 *   shared   the memo, and APCu across requests (where APCu is usable)
 *
 * Validity. Every table has the time of its last write in a state file that
 * every process reads and writes -- the web servers, Velocity and scripts each
 * have an APCu of their own, so the state cannot live there. An entry is kept
 * with the time its query *started* and the tables it read, and is answered
 * only while every one of them was last written before that time: a read that
 * raced a write is always stale, never trusted.
 *
 * Never cached: reads inside a transaction, statements whose tables cannot be
 * read from the text, and anything that is not the same every time (NOW(),
 * RAND(), locks, FOUND_ROWS(), ...). A write whose tables cannot be read
 * makes everything stale. Writes inside a transaction count at COMMIT.
 *
 * Storage, under var/<site>/cache/querycache/: state.ser
 *   { generation, genTime, tables: { table => last write } }
 *
 * See doc/bc/6.0/sql-query-cache.md.
 */
class eZDBQueryCache
{
    /** @var array|null settings: mode, maxAge, maxRows, exclude; null until read */
    protected static $settings = null;

    /** @var string|null another state directory (tests); null for var/<site>/cache/querycache */
    protected static $stateDir = null;

    /** @var array|null the state as last read, and when */
    protected static $state = null;
    protected static $stateRead = 0.0;

    /** @var array this request's memo: key => entry */
    protected static $memo = array();

    /**
     * @var array temporary tables this process created (name => true). Not
     * reset per request: a persistent connection keeps them until dropped.
     */
    protected static $temporary = array();

    /** @var array tables written inside the open transaction */
    protected static $pending = array();

    /** @var array counters for this request (the setup/info box sums them per server) */
    public static $stats = array( 'hits' => 0, 'misses' => 0, 'stores' => 0, 'uncacheable' => 0, 'writes' => 0 );

    /**
     * @var WeakMap|null per driver object: the session parameters its ALTER
     * SESSION statements set (see noteSession()), which key() adds to every
     * statement of that connection. Not reset per request: the session lives
     * as long as the connection.
     */
    protected static $session = null;

    /**
     * Oracle session parameters that become part of the key instead of making
     * every result stale: the NLS settings (number and date format, length
     * semantics, linguistic comparison and sort, language, territory ...),
     * CURSOR_SHARING and TIME_ZONE. Each changes a result, if at all, only by
     * its value, and every connection's statements are keyed with the values
     * it set, so a result is answered only to a session with the same ones.
     * Anything else (CURRENT_SCHEMA, EDITION, CONTAINER, EVENTS ...) stays a
     * write that makes every result stale.
     */
    const SESSION_KEY_PARAMETERS = '/^(?:NLS_[A-Z_]+|CURSOR_SHARING|TIME_ZONE)$/';

    /**
     * @var array|null this request's debug report, null until the first
     * statement with the debug output on, started again after eZDebug::reset()
     * (see debugBegin()): its own counters (hit, miss, store, skip, write: the
     * request's $stats are zeroed by flushStats() whenever Setup > System
     * information asks), the database time the hits saved (ms, from the
     * duration recorded with each entry), hits on entries without it, why
     * statements were not cached, the writes that made everything stale, and
     * whether the state timing point was set. resetRequest() drops it.
     */
    protected static $debug = null;

    /** At most this many statements that made everything stale are listed in the debug report. */
    const DEBUG_MAX_STALE_STATEMENTS = 20;

    /** Words that make a statement's result depend on more than its tables. */
    /**
     * Oracle's forms of the same, without parentheses: a sequence's NEXTVAL and
     * CURRVAL (the Oracle driver reads every new row's id with
     * "SELECT <sequence>.currval FROM DUAL"), the clock, the SCN, generated
     * ids and the session context.
     */
    const ORACLE_VOLATILE = '/\.\s*(?:NEXTVAL|CURRVAL)\b|\b(?:SYSDATE|SYSTIMESTAMP|CURRENT_SCN|ORA_ROWSCN|SYS_GUID|SYS_CONTEXT|USERENV|DBMS_RANDOM|DBMS_LOCK)\b/i';

    const VOLATILE = '/\bUNIX_TIMESTAMP\s*\(\s*\)|\b(?:CURRENT_DATE|CURRENT_TIME|CURRENT_TIMESTAMP|LOCALTIME|LOCALTIMESTAMP|CURRENT_USER)\b|\b(NOW|SYSDATE|CURDATE|CURTIME|CURRENT_DATE|CURRENT_TIME|CURRENT_TIMESTAMP|LOCALTIME|LOCALTIMESTAMP|UTC_DATE|UTC_TIME|UTC_TIMESTAMP|RAND|RANDOM|UUID|UUID_SHORT|LAST_INSERT_ID|LASTVAL|CURRVAL|NEXTVAL|FOUND_ROWS|ROW_COUNT|CONNECTION_ID|GET_LOCK|RELEASE_LOCK|IS_FREE_LOCK|SLEEP|BENCHMARK|DATABASE|USER|CURRENT_USER|SESSION_USER|SYSTEM_USER|VERSION)\s*\(|\bFOR\s+UPDATE\b|\bLOCK\s+IN\s+SHARE\s+MODE\b|\bFOR\s+SHARE\b|\bSQL_CALC_FOUND_ROWS\b|\bSQL_NO_CACHE\b|@@?\w|\bINTO\s+(OUTFILE|DUMPFILE|@)/i';

    // ── Settings ─────────────────────────────────────────────────────────

    public static function settings()
    {
        if ( self::$settings !== null )
            return self::$settings;
        $s = array( 'mode' => 'off', 'maxAge' => 300, 'maxRows' => 5000, 'exclude' => array(), 'report' => true );
        if ( class_exists( 'eZINI', false ) && eZINI::exists( 'querycache.ini' ) )
        {
            $ini = eZINI::instance( 'querycache.ini' );
            $mode = strtolower( (string)$ini->variable( 'QueryCacheSettings', 'Mode' ) );
            $s['mode'] = in_array( $mode, array( 'off', 'request', 'shared' ), true ) ? $mode : 'off';
            $s['maxAge'] = max( 1, (int)$ini->variable( 'QueryCacheSettings', 'MaxAge' ) );
            $s['maxRows'] = max( 1, (int)$ini->variable( 'QueryCacheSettings', 'MaxRows' ) );
            foreach ( (array)$ini->variable( 'QueryCacheSettings', 'ExcludeTables' ) as $t )
            {
                if ( $t !== '' )
                    $s['exclude'][strtolower( $t )] = true;
            }
            if ( $ini->hasVariable( 'DebugSettings', 'Report' ) )
                $s['report'] = $ini->variable( 'DebugSettings', 'Report' ) !== 'disabled';
        }
        return self::$settings = $s;
    }

    /** For tests and setup/info: set the settings (null reads them again). */
    public static function setSettings( $settings )
    {
        self::$settings = $settings;
    }

    public static function enabled()
    {
        return self::settings()['mode'] !== 'off';
    }

    protected static function apcuUsable()
    {
        return self::settings()['mode'] === 'shared' && function_exists( 'apcu_enabled' ) && apcu_enabled();
    }

    // ── Reads ────────────────────────────────────────────────────────────

    /**
     * Before a driver runs a SELECT through arrayQuery(): the rows if they are
     * cached and current (as $hit), and the key to store() them under, or null
     * when this statement is not cached.
     *
     * @param eZDBInterface $db
     * @param string $sql
     * @param array $params arrayQuery()'s offset, limit, column
     * @param mixed $hit set to the rows on a hit, null otherwise
     * @return array|null [key, tables, started] for store()
     */
    public static function lookup( $db, $sql, $params, &$hit )
    {
        $hit = null;
        if ( !self::enabled() )
            return null;
        self::registerFlush();
        // eZDebug::isDebugEnabled() inlined: with the debug output off, this test is all the
        // debug report costs a statement (no call; measured ~150 ns a lookup with two calls)
        $debugStart = empty( $GLOBALS['eZDebugEnabled'] ) ? null : self::debugStart();
        if ( ( $db->TransactionCounter ?? 0 ) > 0 )
        {
            self::debugSkip( 'inside a transaction', $debugStart );
            return null;
        }
        // The stored entry first: it carries the tables its statement read, so
        // a hit needs no parsing. Reading the tables from the text was the
        // largest single cost of a rendered page (a third of it, ~0.12 ms a
        // statement for ~545 statements); only a miss parses now.
        $key = self::key( $db, $sql, $params );
        $entry = self::$memo[$key] ?? null;
        if ( $entry === null && self::apcuUsable() )
        {
            $got = @apcu_fetch( 'ezqc:' . $key );
            if ( is_array( $got ) )
                $entry = $got;
        }
        // Only statements that were cacheable when stored have entries; one of
        // their tables excluded since, or temporary, is not answered from it.
        if ( $entry !== null && self::current( $entry ) && self::answerable( $entry['tables'] ) )
        {
            self::$memo[$key] = $entry;
            self::$stats['hits']++;
            $hit = $entry['rows'];
            if ( $debugStart !== null )
            {
                if ( isset( $entry['ms'] ) )
                    self::$debug['saved_ms'] += $entry['ms'];
                else
                    self::$debug['hits_unmeasured']++;
                self::debugTime( 'hit', $debugStart );
            }
            return null;
        }
        $tables = self::readTables( $sql );
        if ( $tables === null )
        {
            self::$stats['uncacheable']++;
            self::debugSkip( 'statement (volatile or tables not readable)', $debugStart );
            return null;
        }
        $s = self::settings();
        foreach ( $tables as $t )
        {
            if ( isset( $s['exclude'][$t] ) )
            {
                self::debugSkip( 'excluded table', $debugStart );
                return null;
            }
            // A temporary table belongs to one connection and its name is
            // reused: another request's rows must never answer for it. A
            // database's own catalogue changes without any write this cache
            // sees (ANALYZE, a schema change): never answered either.
            if ( self::isTemporary( $t ) || self::isSystemTable( $t ) )
            {
                self::$stats['uncacheable']++;
                self::debugSkip( self::isTemporary( $t ) ? 'temporary table' : 'catalogue table', $debugStart );
                return null;
            }
        }
        self::$stats['misses']++;
        if ( $debugStart !== null )
            self::debugTime( 'miss', $debugStart );
        return array( $key, $tables, microtime( true ) );
    }

    /**
     * The key of a statement: the connection, its session settings (Oracle,
     * see noteSession()), the text and arrayQuery()'s parameters. xxh128
     * where PHP has it (8.1+), several times faster than
     * md5 on a long statement; the parameters (offset, limit, column) joined
     * instead of serialized.
     */
    public static function key( $db, $sql, $params )
    {
        $p = '';
        if ( is_array( $params ) )
        {
            foreach ( $params as $name => $value )
                $p .= $name . '=' . ( is_scalar( $value ) || $value === null ? (string)$value : serialize( $value ) ) . ';';
        }
        $raw = get_class( $db ) . "\0" . ( $db->DB ?? '' ) . "\0" . ( $db->Server ?? '' ) . "\0" . self::sessionKey( $db ) . "\0" . $sql . "\0" . $p;
        static $xxh = null;
        if ( $xxh === null )
            $xxh = in_array( 'xxh128', hash_algos(), true );
        return $xxh ? hash( 'xxh128', $raw ) : md5( $raw );
    }

    /** A table of the database's own catalogue: sqlite_master, sqlite_stat1, pg_class, information_schema.tables ... */
    public static function isSystemTable( $table )
    {
        // ... and Oracle's: USER_TABLES, ALL_TAB_COLUMNS, DBA_SEQUENCES, V$SESSION ...
        return preg_match( '/^(sqlite_|pg_|information_schema)/', (string)$table ) === 1
            || preg_match( '/^(?:(?:user|all|dba|cdb)_[a-z0-9_$#]+|g?v\$[a-z0-9_$#]*)$/i', (string)$table ) === 1;
    }

    /** Whether a stored entry's tables may still be answered: none excluded, none temporary, none of the catalogue. */
    protected static function answerable( array $tables )
    {
        $exclude = self::settings()['exclude'];
        foreach ( $tables as $t )
        {
            if ( isset( $exclude[$t] ) || self::isTemporary( $t ) || self::isSystemTable( $t ) )
                return false;
        }
        return true;
    }

    /** After a driver ran the SELECT lookup() handed a key for: keep its rows. */
    public static function store( $ticket, $rows )
    {
        if ( $ticket === null || !is_array( $rows ) )
            return;
        list( $key, $tables, $started ) = $ticket;
        $debugStart = empty( $GLOBALS['eZDebugEnabled'] ) ? null : self::debugStart();
        if ( count( $rows ) > self::settings()['maxRows'] )
        {
            self::debugSkip( 'more rows than MaxRows', $debugStart );
            return;
        }
        // Values, not references (arrayQuery() with a column hands out
        // references): the caller may change what it was given.
        $copy = array();
        foreach ( $rows as $k => $v )
            $copy[$k] = $v;
        // 'ms': how long the database took for the statement and its rows, which
        // every later hit saves; the debug report adds them up.
        $entry = array( 'created' => $started, 'tables' => $tables, 'rows' => $copy,
                        'ms' => round( ( microtime( true ) - $started ) * 1000, 3 ) );
        self::$memo[$key] = $entry;
        if ( self::apcuUsable() )
            @apcu_store( 'ezqc:' . $key, $entry, self::settings()['maxAge'] );
        self::$stats['stores']++;
        if ( $debugStart !== null )
            self::debugTime( 'store', $debugStart );
    }

    /** Whether an entry is current: nothing it read was written since its query started. */
    protected static function current( array $entry )
    {
        if ( microtime( true ) - $entry['created'] > self::settings()['maxAge'] )
            return false;
        $state = self::state();
        if ( $entry['created'] <= $state['genTime'] )
            return false;
        foreach ( $entry['tables'] as $t )
        {
            if ( isset( $state['tables'][$t] ) && $entry['created'] <= $state['tables'][$t] )
                return false;
        }
        return true;
    }

    // ── Debug output ─────────────────────────────────────────────────────

    /**
     * The start time for a debug accumulator, or null when the debug output is
     * off or querycache.ini [DebugSettings] Report=disabled. With the debug
     * output off the callers do not call it: they test the global eZDebug::isDebugEnabled() reads first.
     *
     * The first call of a request with the debug output on, and the first one
     * after eZDebug::reset() (a worker between two jobs: the reset dropped the
     * report and the accumulators), starts the report of that request or job.
     *
     * @return float|null
     */
    protected static function debugStart()
    {
        if ( self::$debug === null || !isset( eZDebug::instance()->bottomReportsList['Query cache'] ) )
        {
            if ( !class_exists( 'eZDebug', false ) || !eZDebug::isDebugEnabled() || !( self::settings()['report'] ?? true ) )
                return null;
            self::debugBegin();
        }
        return microtime( true );
    }

    /**
     * Starts the debug report of a request or job: empty counters, the
     * accumulator group and the bottom report registered with eZDebug, and the
     * timing point: at once when this process already holds the state (a job
     * after eZDebug::reset(), whose statements may never read it again), else
     * with the first look at it.
     */
    protected static function debugBegin()
    {
        self::$debug = array( 'count' => array( 'hit' => 0, 'miss' => 0, 'store' => 0, 'skip' => 0, 'write' => 0 ),
                              'saved_ms' => 0.0, 'hits_unmeasured' => 0, 'skipped' => array(), 'stale' => array(),
                              'stale_more' => 0, 'state_point' => false );
        eZDebug::createAccumulatorGroup( 'querycache_total', 'Query cache' );
        eZDebug::appendBottomReport( 'Query cache', array( __CLASS__, 'debugReport' ) );
        if ( self::$state !== null )
            self::debugStatePoint();
    }

    /** The timing point of the state, once per debug report. */
    protected static function debugStatePoint()
    {
        self::$debug['state_point'] = true;
        eZDebug::addTimingPoint( 'Query cache: state read (generation ' . (int)self::$state['generation'] . ')' );
    }

    /** The accumulators of the group "Query cache": debug counter => accumulator key and name. */
    const DEBUG_ACCUMULATORS = array( 'hit' => array( 'querycache_hit', 'Hits' ),
                                      'miss' => array( 'querycache_miss', 'Misses (lookup only)' ),
                                      'store' => array( 'querycache_store', 'Stores' ),
                                      'skip' => array( 'querycache_skip', 'Not cached' ) );

    /** Counts one statement as $what (hit, miss, store, skip) and adds the time since $started to its accumulator. */
    protected static function debugTime( $what, $started )
    {
        self::$debug['count'][$what]++;
        list( $key, $name ) = self::DEBUG_ACCUMULATORS[$what];
        $debug = eZDebug::instance();
        if ( !isset( $debug->TimeAccumulatorList[$key] ) )
            eZDebug::createAccumulator( $key, 'querycache_total', $name );
        $debug->TimeAccumulatorList[$key]['time'] += microtime( true ) - $started;
        $debug->TimeAccumulatorList[$key]['count']++;
    }

    /** Counts a statement that was not cached, by reason, and its lookup time. */
    protected static function debugSkip( $reason, $started )
    {
        if ( $started === null )
            return;
        self::$debug['skipped'][$reason] = ( self::$debug['skipped'][$reason] ?? 0 ) + 1;
        self::debugTime( 'skip', $started );
    }

    /** Counts a write, and remembers one that makes every result stale ($everything), for the debug report. */
    protected static function debugWrite( $sql, $everything )
    {
        if ( empty( $GLOBALS['eZDebugEnabled'] ) || self::debugStart() === null )
            return;
        self::$debug['count']['write']++;
        if ( !$everything )
            return;
        if ( count( self::$debug['stale'] ) >= self::DEBUG_MAX_STALE_STATEMENTS )
        {
            self::$debug['stale_more']++;
            return;
        }
        // Literals masked: values from requests and content stay out of the
        // report. Only the start is masked: an unterminated literal is masked
        // to the end, so cutting the text first hides nothing it would show.
        $text = preg_replace( '/\s+/', ' ', trim( self::maskLiterals( substr( (string)$sql, 0, 4096 ) ) ) );
        self::$debug['stale'][] = strlen( $text ) > 200 ? substr( $text, 0, 200 ) . ' ...' : $text;
    }

    /**
     * $sql with every string literal replaced by '?'. Where a literal ends
     * depends on the database: SQL-standard quoting doubles a quote inside it
     * ('it''s', Oracle, PostgreSQL, SQLite), MySQL also escapes it with a
     * backslash ('it\'s', what eZDBInterface::escapeString() writes there), and
     * a backslash before the closing quote ('C:\') ends a standard literal but
     * not a MySQL one. Both readings are masked, and so is everything from an
     * unterminated quote to the end: no part of a literal shows under either.
     * Double-quoted text (an identifier, except in MySQL without ANSI_QUOTES)
     * is left as it is.
     *
     * @param string $sql
     * @return string
     */
    public static function maskLiterals( $sql )
    {
        $sql = (string)$sql;
        if ( strpos( $sql, "'" ) === false )
            return $sql;
        $ranges = array();
        foreach ( array( "/'(?:[^']++|'')*+(?:'|$)/", "/'(?:[^'\\\\]++|''|\\\\.?)*+(?:'|$)/s" ) as $pattern )
        {
            if ( preg_match_all( $pattern, $sql, $m, PREG_OFFSET_CAPTURE ) )
            {
                foreach ( $m[0] as $hit )
                    $ranges[] = array( $hit[1], $hit[1] + strlen( $hit[0] ) );
            }
        }
        sort( $ranges );
        $out = '';
        $pos = 0;
        $end = -1;
        foreach ( $ranges as $range )
        {
            if ( $range[0] < $end )
            {
                $end = max( $end, $range[1] );
                continue;
            }
            if ( $end >= 0 )
            {
                $out .= "'?'";
                $pos = $end;
            }
            $out .= substr( $sql, $pos, $range[0] - $pos );
            $end = $range[1];
        }
        if ( $end >= 0 )
        {
            $out .= "'?'";
            $pos = $end;
        }
        return $out . substr( $sql, $pos );
    }

    /**
     * The bottom report of the debug output: this request's (or, after
     * eZDebug::reset(), this job's) counters, the database time the hits
     * saved, why statements were not cached and which writes made everything
     * stale.
     *
     * @param bool $as_html
     * @return string
     */
    public static function debugReport( $as_html = true )
    {
        if ( self::$debug === null )
            return '';
        $d = self::$debug;
        $looked = $d['count']['hit'] + $d['count']['miss'];
        $mode = self::settings()['mode'];
        $rows = array(
            'Mode' => $mode . ( $mode === 'shared' && !self::apcuUsable() ? ' (no APCu: works as request)' : '' ),
            // as the request last read it; the report itself reads nothing and sets no timing point
            'Generation' => self::$state !== null ? (int)self::$state['generation'] : 'not read',
            'Hits' => $d['count']['hit'],
            'Misses' => $d['count']['miss'],
            'Hit rate' => $looked > 0 ? round( 100 * $d['count']['hit'] / $looked, 1 ) . ' %' : '-',
            'Stored' => $d['count']['store'],
            'Not cached' => $d['count']['skip'],
            'Writes' => $d['count']['write'],
            'Database time saved (estimated)' => round( $d['saved_ms'], 1 ) . ' ms'
                . ( $d['hits_unmeasured'] ? ' (+ ' . $d['hits_unmeasured'] . ' hits on entries without a duration)' : '' ),
        );
        foreach ( $d['skipped'] as $reason => $n )
            $rows['Not cached: ' . $reason] = $n;
        $stale = $d['stale'];
        if ( $d['stale_more'] )
            $stale[] = '... and ' . $d['stale_more'] . ' more';

        if ( !$as_html )
        {
            $out = "Query cache\n";
            foreach ( $rows as $label => $value )
                $out .= sprintf( "%-40s %s\n", $label, $value );
            foreach ( $stale as $sql )
                $out .= 'Made everything stale: ' . $sql . "\n";
            return $out;
        }
        $out = '<div id="querycachereport"><h3>Query cache:</h3>'
             . '<table id="querycache" class="debug_resource_usage" title="SQL query cache of this request">';
        foreach ( $rows as $label => $value )
            $out .= '<tr class="data"><td>' . self::html( $label ) . '</td><td>' . self::html( $value ) . '</td></tr>';
        foreach ( $stale as $sql )
            $out .= '<tr class="data"><td>Made everything stale</td><td>' . self::html( $sql ) . '</td></tr>';
        return $out . '</table></div>';
    }

    protected static function html( $value )
    {
        return htmlspecialchars( (string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
    }

    // ── Writes ───────────────────────────────────────────────────────────

    /**
     * After a driver ran a statement that is not a SELECT: the tables it wrote
     * are stale from now on -- at once outside a transaction, at COMMIT inside
     * one. A write whose tables cannot be read makes everything stale.
     */
    public static function noteWrite( $db, $sql )
    {
        if ( !self::enabled() )
            return;
        // An anonymous PL/SQL block (Oracle) can write anything: DECLARE ..., or
        // BEGIN ... END; -- unlike the bare BEGIN that starts a transaction in
        // MySQL. Its tables cannot be read, so it makes everything stale.
        if ( preg_match( '/^\s*DECLARE\b/i', $sql ) || preg_match( '/^\s*BEGIN\b.*\bEND\b\s*;?\s*$/is', $sql ) )
        {
            self::$stats['writes']++;
            self::debugWrite( $sql, true );
            if ( ( $db->TransactionCounter ?? 0 ) > 0 )
                self::$pending['*'] = true;
            else
                self::bump( null );
            return;
        }
        $verb = strtoupper( (string)strtok( ltrim( $sql ), " \t\r\n(" ) );
        // ezoracle runs one ALTER SESSION on every connect. Counted as a write of
        // unknown tables it made every result stale on every request. Its parameters
        // (SESSION_KEY_PARAMETERS) go into the connection's key instead; any other
        // ALTER SESSION (CURRENT_SCHEMA, EDITION, CONTAINER ...) stays an unreadable write.
        if ( $verb === 'ALTER' && ( $set = self::sessionSettings( $sql ) ) !== null )
        {
            self::noteSession( $db, $set );
            return;
        }
        if ( in_array( $verb, array( 'SELECT', 'SHOW', 'DESCRIBE', 'DESC', 'EXPLAIN', 'SET', 'BEGIN', 'START', 'COMMIT', 'ROLLBACK', 'SAVEPOINT', 'RELEASE', 'PRAGMA', 'USE', 'LOCK', 'UNLOCK', 'ANALYZE', 'CHECK', 'CHECKSUM', 'FLUSH', 'KILL', 'OPTIMIZE', 'VACUUM' ), true ) )
            return;
        // Temporary tables are never cached, so their writes invalidate
        // nothing and stay out of the shared state.
        if ( preg_match( '/^\s*CREATE\s+TEMPORARY\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?([`"\w.]+)/i', $sql, $m ) )
        {
            $name = self::tableName( $m[1] );
            if ( $name !== null )
                self::$temporary[$name] = true;
            return;
        }
        if ( preg_match( '/^\s*DROP\s+TEMPORARY\s+TABLE\b/i', $sql ) )
            return;
        self::$stats['writes']++;
        $tables = self::writtenTables( $sql );
        self::debugWrite( $sql, $tables === null );
        if ( $tables !== null )
        {
            $tables = array_values( array_filter( $tables, function ( $t ) { return !self::isTemporary( $t ); } ) );
            if ( !$tables )
                return;
        }
        if ( ( $db->TransactionCounter ?? 0 ) > 0 )
        {
            if ( $tables === null )
                self::$pending['*'] = true;
            else
                foreach ( $tables as $t ) self::$pending[$t] = true;
            return;
        }
        self::bump( $tables );
    }

    /**
     * Whether $sql is an ALTER SESSION SET that changes nothing a stored result
     * could be wrong about: it only sets parameters of SESSION_KEY_PARAMETERS
     * (the statement ezoracle runs on every connect, with or without
     * OracleCaseInsensitive). noteWrite() makes such a statement part of the
     * connection's key instead of a write.
     *
     * @param string $sql
     * @return bool
     */
    public static function isNeutralAlterSession( $sql )
    {
        return self::sessionSettings( $sql ) !== null;
    }

    /**
     * The parameters an ALTER SESSION SET sets (NAME => value, names and
     * unquoted values in upper case, a quoted value as written), or null when
     * $sql is anything else or sets a parameter outside SESSION_KEY_PARAMETERS.
     *
     * @param string $sql
     * @return array|null
     */
    public static function sessionSettings( $sql )
    {
        if ( !preg_match( '/^\s*ALTER\s+SESSION\s+SET\s+(.+?)\s*;?\s*$/is', $sql, $m ) )
            return null;
        $rest = $m[1];
        $set = array();
        while ( $rest !== '' )
        {
            if ( !preg_match( "/^([A-Za-z_][A-Za-z0-9_\$#]*)\s*=\s*('(?:[^']++|'')*+'|[^\s']++)\s*/", $rest, $p ) )
                return null;
            $name = strtoupper( $p[1] );
            if ( !preg_match( self::SESSION_KEY_PARAMETERS, $name ) )
                return null;
            $set[$name] = $p[2][0] === "'" ? $p[2] : strtoupper( $p[2] );
            $rest = (string)substr( $rest, strlen( $p[0] ) );
        }
        return $set ?: null;
    }

    /**
     * Adds the session parameters $set to what the key of $db's statements
     * carries from now on. A connection's session lives as long as the driver
     * object (a persistent worker's too), so this is not reset per request.
     */
    protected static function noteSession( $db, array $set )
    {
        if ( self::$session === null )
            self::$session = new WeakMap();
        $all = ( isset( self::$session[$db] ) ? self::$session[$db]['set'] : array() );
        foreach ( $set as $name => $value )
            $all[$name] = $value;
        ksort( $all );
        $text = '';
        foreach ( $all as $name => $value )
            $text .= $name . '=' . $value . ' ';
        self::$session[$db] = array( 'set' => $all, 'key' => $text );
    }

    /**
     * What key() adds for $db: the session parameters noteSession() saw on it,
     * '' for a connection that set none.
     *
     * @param object $db
     * @return string
     */
    public static function sessionKey( $db )
    {
        return self::$session !== null && isset( self::$session[$db] ) ? self::$session[$db]['key'] : '';
    }

    /**
     * Whether a table is temporary: created as one by this process, or named
     * the way eZDBInterface::generateUniqueTempTableName() names them
     * (ezproductcoll_tmp_123, ezsearch_tmp_4, eznode_count_56, ...).
     */
    public static function isTemporary( $table )
    {
        return isset( self::$temporary[$table] ) || preg_match( '/(?:_tmp_|^eznode_count_)\d+$/', $table ) === 1;
    }

    /** From eZDBInterface::commit(), once the outermost transaction is committed. */
    public static function afterCommit()
    {
        if ( !self::$pending )
            return;
        $pending = self::$pending;
        self::$pending = array();
        self::bump( isset( $pending['*'] ) ? null : array_keys( $pending ) );
    }

    /** From eZDBInterface::rollback(): nothing written was kept. */
    public static function afterRollback()
    {
        self::$pending = array();
    }

    /** Everything stale at once (Setup > Caches, ezcache.php --clear-id=querycache). */
    public static function clearAll()
    {
        self::bump( null );
    }

    /** Marks tables (null: all) written now, in the shared state and this request. */
    protected static function bump( $tables )
    {
        $now = microtime( true );
        // The memo forgets what the write touched: it would otherwise answer
        // this request's next read of the table from before the write.
        foreach ( self::$memo as $key => $entry )
        {
            if ( $tables === null || array_intersect( $entry['tables'], $tables ) )
                unset( self::$memo[$key] );
        }
        self::updateState( function ( $s ) use ( $tables, $now ) {
            if ( $tables === null )
            {
                $s['generation'] = (int)$s['generation'] + 1;
                $s['genTime'] = $now;
                $s['tables'] = array();
            }
            else
            {
                // A write older than the longest entry life can no longer matter.
                $horizon = $now - self::settings()['maxAge'] - 1;
                foreach ( $s['tables'] as $t => $time )
                {
                    if ( $time < $horizon )
                        unset( $s['tables'][$t] );
                }
                foreach ( $tables as $t )
                    $s['tables'][$t] = $now;
            }
            return $s;
        } );
    }

    // ── State ────────────────────────────────────────────────────────────

    public static function stateDir()
    {
        if ( self::$stateDir !== null )
            return self::$stateDir;
        return eZSys::rootDir() . '/' . eZSys::cacheDirectory() . '/querycache';
    }

    /**
     * The state as this request knows it, read again when it is older than a
     * second, so a long script sees other processes' writes.
     */
    public static function state()
    {
        $now = microtime( true );
        if ( self::$state === null || $now - self::$stateRead > 1.0 )
        {
            $raw = @file_get_contents( self::stateDir() . '/state.ser' );
            $s = $raw === false ? false : @unserialize( $raw, array( 'allowed_classes' => false ) );
            self::$state = is_array( $s ) ? $s : array( 'generation' => 1, 'genTime' => 0, 'tables' => array() );
            self::$stateRead = $now;
        }
        // Once per debug report, where the request first looks at the state.
        if ( self::$debug !== null && !self::$debug['state_point'] )
            self::debugStatePoint();
        return self::$state;
    }

    /** Uses another state directory (tests), or the site's again with null. */
    public static function setStateDir( $dir )
    {
        self::$stateDir = $dir;
        self::$state = null;
    }

    /** The state as on disk now (setup/info, tests), without touching this request's memo. */
    public static function freshState()
    {
        self::$state = null;
        return self::state();
    }

    protected static function updateState( $change )
    {
        $dir = self::stateDir();
        $asRoot = function_exists( 'posix_geteuid' ) && posix_geteuid() === 0;
        if ( !is_dir( $dir ) )
        {
            @mkdir( $dir, eZDir::dirMode( 0770 ), true );
            // A root process (Velocity, a CLI script) must not leave a root-owned
            // directory behind: the site user's cron and FPM could no longer write in it.
            if ( $asRoot && ( $parent = @stat( dirname( $dir ) ) ) && $parent['uid'] !== 0 )
            {
                @chown( $dir, $parent['uid'] );
                @chgrp( $dir, $parent['gid'] );
            }
        }
        $lock = @fopen( $dir . '/state.lock', 'c' );
        if ( $lock )
            flock( $lock, LOCK_EX );
        self::$state = null;
        $state = call_user_func( $change, self::state() );
        $tmp = $dir . '/state.ser.' . getmypid() . '.' . mt_rand() . '.tmp';
        $ok = @file_put_contents( $tmp, serialize( $state ) ) !== false && @chmod( $tmp, eZFile::fileMode( 0660 ) ) !== null && @rename( $tmp, $dir . '/state.ser' );
        if ( !$ok )
            error_log( 'eZDBQueryCache: could not write ' . $dir . '/state.ser; cached results may be stale until MaxAge' );
        // A root process (a script, a server) leaves the files to the directory's owner.
        if ( $asRoot && ( $owner = @stat( $dir ) ) && $owner['uid'] !== 0 )
        {
            @chown( $dir . '/state.ser', $owner['uid'] );
            @chgrp( $dir . '/state.ser', $owner['gid'] );
            @chown( $dir . '/state.lock', $owner['uid'] );
            @chgrp( $dir . '/state.lock', $owner['gid'] );
        }
        self::$state = $state;
        self::$stateRead = microtime( true );
        if ( $lock )
        {
            flock( $lock, LOCK_UN );
            fclose( $lock );
        }
    }

    /** Resets what belongs to one request (a persistent worker calls this per request). */
    public static function resetRequest()
    {
        self::flushStats();
        self::$memo = array();
        self::$pending = array();
        self::$state = null;
        self::$settings = null;
        self::$stats = array( 'hits' => 0, 'misses' => 0, 'stores' => 0, 'uncacheable' => 0, 'writes' => 0 );
        self::$debug = null;
    }

    /** @var string|false eZExecution::requestKey() of the request flushStats() is registered to run at the end of */
    protected static $flushRegistered = false;

    /**
     * Adds this request's counters to the totals of the server process group
     * (APCu ezqcstat:*), for Setup > System information, and zeroes them. Runs
     * at shutdown and again at the next resetRequest(), whichever comes first,
     * so a persistent worker counts every request once.
     */
    public static function flushStats()
    {
        $any = array_sum( self::$stats ) > 0;
        if ( $any && function_exists( 'apcu_enabled' ) && apcu_enabled() )
        {
            if ( !@apcu_exists( 'ezqcstat:since' ) )
                @apcu_add( 'ezqcstat:since', time() );
            foreach ( self::$stats as $name => $n )
            {
                if ( $n > 0 && @apcu_inc( 'ezqcstat:' . $name, $n ) === false )
                    @apcu_store( 'ezqcstat:' . $name, $n );
            }
            if ( @apcu_inc( 'ezqcstat:requests', 1 ) === false )
                @apcu_store( 'ezqcstat:requests', 1 );
        }
        self::$stats = array( 'hits' => 0, 'misses' => 0, 'stores' => 0, 'uncacheable' => 0, 'writes' => 0 );
    }

    /** Makes sure the counters of a plain (non-persistent) request are flushed. */
    protected static function registerFlush()
    {
        if ( self::$flushRegistered !== eZExecution::requestKey() )
        {
            self::$flushRegistered = eZExecution::requestKey();
            register_shutdown_function( array( __CLASS__, 'flushStats' ) );
        }
    }

    /**
     * What Setup > System information shows: settings, state, what this server
     * holds in APCu and its counters since they were last reset.
     */
    public static function status()
    {
        $s = self::settings();
        $state = self::freshState();
        $tables = $state['tables'] ?? array();
        arsort( $tables );
        $out = array(
            'mode' => $s['mode'],
            'enabled' => $s['mode'] !== 'off',
            'max_age' => $s['maxAge'],
            'max_rows' => $s['maxRows'],
            'exclude' => array_keys( $s['exclude'] ),
            'state_file' => self::stateDir() . '/state.ser',
            'state_exists' => is_file( self::stateDir() . '/state.ser' ),
            'generation' => (int)( $state['generation'] ?? 1 ),
            'cleared' => (int)( $state['genTime'] ?? 0 ),
            'tables_tracked' => count( $tables ),
            'recent_writes' => array_slice( array_map( 'intval', $tables ), 0, 8, true ),
            'apcu' => function_exists( 'apcu_enabled' ) && apcu_enabled(),
            'entries' => 0,
            'memory' => 0,
            'counters' => null,
        );
        if ( $out['apcu'] && class_exists( 'APCUIterator' ) )
        {
            $it = new APCUIterator( '/^ezqc:/', APC_ITER_MEM_SIZE );
            foreach ( $it as $item )
            {
                $out['entries']++;
                $out['memory'] += (int)$item['mem_size'];
            }
            $c = array();
            foreach ( array( 'requests', 'hits', 'misses', 'stores', 'uncacheable', 'writes', 'since' ) as $name )
                $c[$name] = (int)@apcu_fetch( 'ezqcstat:' . $name );
            $looked = $c['hits'] + $c['misses'];
            $c['hit_rate'] = $looked > 0 ? round( 100 * $c['hits'] / $looked, 1 ) : null;
            $out['counters'] = $c;
        }
        return $out;
    }

    /** Zeroes this server's counters (Setup > System information). */
    public static function resetStats()
    {
        if ( function_exists( 'apcu_enabled' ) && apcu_enabled() && class_exists( 'APCUIterator' ) )
            @apcu_delete( new APCUIterator( '/^ezqcstat:/' ) );
    }

    // ── Reading the tables from SQL ──────────────────────────────────────

    /**
     * The tables a SELECT reads (lower case), or null when it must not be
     * cached: not a plain SELECT, volatile, or no table found.
     */
    public static function readTables( $sql )
    {
        $sql = (string)$sql;
        if ( strncasecmp( ltrim( $sql ), 'select', 6 ) !== 0 )
            return null;
        if ( preg_match( self::VOLATILE, $sql ) || preg_match( self::ORACLE_VOLATILE, $sql ) )
            return null;
        $text = self::withoutStrings( $sql );
        if ( $text === null )
            return null;
        $tables = array();
        // FROM a, b alias, c ... up to the next clause; subqueries have their own FROM.
        if ( preg_match_all( '/\bFROM\s+(.+?)(?=\bWHERE\b|\bGROUP\s+BY\b|\bORDER\s+BY\b|\bLIMIT\b|\bHAVING\b|\bUNION\b|\b(?:INNER|LEFT|RIGHT|CROSS|FULL|STRAIGHT_JOIN|NATURAL|OUTER)\b|\bJOIN\b|\bON\b|\)|$)/is', $text, $m ) )
        {
            foreach ( $m[1] as $list )
            {
                foreach ( explode( ',', $list ) as $item )
                {
                    $name = self::tableName( $item );
                    if ( $name !== null )
                        $tables[$name] = true;
                }
            }
        }
        if ( preg_match_all( '/\bJOIN\s+([`"\w.]+)/i', $text, $m ) )
        {
            foreach ( $m[1] as $item )
            {
                $name = self::tableName( $item );
                if ( $name !== null )
                    $tables[$name] = true;
            }
        }
        return $tables ? array_keys( $tables ) : null;
    }

    /** The tables a write statement changes (lower case), or null when not readable. */
    public static function writtenTables( $sql )
    {
        $text = self::withoutStrings( (string)$sql );
        if ( $text === null )
            return null;
        $patterns = array(
            '/^\s*(?:INSERT|REPLACE)\s+(?:LOW_PRIORITY\s+|DELAYED\s+|HIGH_PRIORITY\s+|IGNORE\s+|OR\s+\w+\s+)*INTO\s+([`"\w.]+)/i',
            '/^\s*UPDATE\s+(?:LOW_PRIORITY\s+|IGNORE\s+|OR\s+\w+\s+)*(.+?)\s+SET\b/is',
            '/^\s*DELETE\s+(?:LOW_PRIORITY\s+|QUICK\s+|IGNORE\s+)*(?:[`"\w.,\s]*?\s)?FROM\s+(.+?)(?=\bWHERE\b|\bUSING\b|\bORDER\b|\bLIMIT\b|$)/is',
            '/^\s*TRUNCATE\s+(?:TABLE\s+)?([`"\w.]+)/i',
            '/^\s*(?:ALTER|DROP|CREATE)\s+(?:TEMPORARY\s+)?TABLE\s+(?:IF\s+(?:NOT\s+)?EXISTS\s+)?([`"\w.]+)/i',
            '/^\s*(?:CREATE|DROP)\s+(?:UNIQUE\s+)?INDEX\s+[`"\w.]+\s+ON\s+([`"\w.]+)/i',
        );
        foreach ( $patterns as $p )
        {
            if ( !preg_match( $p, $text, $m ) )
                continue;
            $tables = array();
            // A multi-table UPDATE or DELETE names several, and may JOIN more.
            foreach ( preg_split( '/,|\bJOIN\b/i', $m[1] ) as $item )
            {
                $name = self::tableName( $item );
                if ( $name !== null )
                    $tables[$name] = true;
            }
            if ( preg_match_all( '/\bJOIN\s+([`"\w.]+)/i', $text, $j ) )
            {
                foreach ( $j[1] as $item )
                {
                    $name = self::tableName( $item );
                    if ( $name !== null )
                        $tables[$name] = true;
                }
            }
            return $tables ? array_keys( $tables ) : null;
        }
        // RENAME TABLE a TO b, and anything else: not readable.
        if ( preg_match_all( '/\bRENAME\s+TABLE\s+([`"\w.]+)\s+TO\s+([`"\w.]+)/i', $text, $m ) )
            return array_values( array_unique( array_map( array( __CLASS__, 'tableName' ), array_merge( $m[1], $m[2] ) ) ) );
        return null;
    }

    /** The table of "name", "`name`", "db.name", "name alias" or "name AS alias"; null for a subquery. */
    protected static function tableName( $item )
    {
        $item = trim( $item );
        if ( $item === '' || $item[0] === '(' )
            return null;
        $first = preg_split( '/\s+/', $item )[0];
        $first = str_replace( array( '`', '"' ), '', $first );
        if ( strpos( $first, '.' ) !== false )
            $first = substr( $first, strrpos( $first, '.' ) + 1 );
        return preg_match( '/^\w+$/', $first ) ? strtolower( $first ) : null;
    }

    /** The SQL with string literals blanked, so a word in a value is not read as SQL. */
    protected static function withoutStrings( $sql )
    {
        // Unrolled: one step per escape or doubled quote rather than one per character, so a
        // long literal (an attribute's XML) stays within PCRE's limits. Null when it fails.
        return preg_replace( "/'[^'\\\\]*+(?:(?:\\\\.|'')[^'\\\\]*+)*+'/s", "''", $sql );
    }
}
