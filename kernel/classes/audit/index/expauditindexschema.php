<?php
/**
 * The audit index tables (doc/bc/6.0/audit.md, "The index"): their definition in the generic schema format of
 * share/db_schema.dba, creating them on an existing installation (every engine, through its eZDbSchema handler:
 * MySQL/MariaDB, PostgreSQL, SQLite, Oracle with the ezoracle extension, MongoDB through expMongoSchema), the
 * full-text part each engine has on top, and the questions the indexer and the console ask: are the tables there,
 * and which full-text search does this database have.
 *
 * The same definition is in share/db_schema.dba and in kernel/sql/<engine>/; this class is what the upgrade script
 * (update/common/scripts/6.0/createaudittables.php) and the tests use, so an installation made before the tables
 * existed gets exactly the tables a new installation has.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditIndexSchema
{
    const EVENT = 'expaudit_event';
    const CURSOR = 'expaudit_cursor';
    const FILE = 'expaudit_file';
    /** SQLite: the FTS5 table over expaudit_event.search_text (external content) */
    const FTS = 'expaudit_event_fts';

    /** @var array database key => bool, per request */
    protected static $installed = array();

    /** @var array database key => full-text kind, per process */
    protected static $fullText = array();

    /**
     * The three tables in the generic schema format (share/db_schema.dba).
     *
     * @return array table => definition
     */
    public static function definition()
    {
        $str = function ( $length, $notNull = false ) {
            return $notNull ? array( 'length' => $length, 'type' => 'varchar', 'not_null' => '1', 'default' => '' )
                            : array( 'length' => $length, 'type' => 'varchar', 'default' => null );
        };
        $int = function ( $length, $notNull = true ) {
            return $notNull ? array( 'length' => $length, 'type' => 'int', 'not_null' => '1', 'default' => 0 )
                            : array( 'length' => $length, 'type' => 'int', 'default' => null );
        };
        $big = function ( $notNull = true ) {
            return $notNull ? array( 'length' => 20, 'type' => 'bigint', 'not_null' => '1', 'default' => 0 )
                            : array( 'length' => 20, 'type' => 'bigint', 'default' => null );
        };
        $idx = function ( $type, array $fields ) {
            return array( 'type' => $type, 'fields' => $fields );
        };

        $event = array(
            'name' => self::EVENT,
            'fields' => array(
                'channel' => $str( 32, true ),
                'depth' => $int( 4 ),
                'domain_name' => $str( 16, true ),
                'engine' => $str( 16 ),
                'file_name' => $str( 64, true ),
                'id' => array( 'length' => 26, 'type' => 'char', 'not_null' => '1', 'default' => '' ),
                'imported' => $int( 4 ),
                'ip' => $str( 64 ),
                'job_id' => $str( 32 ),
                'login' => $str( 150 ),
                'module_view' => $str( 128 ),
                'name' => $str( 128, true ),
                'object_id' => $str( 64 ),
                'object_name' => $str( 255 ),
                'object_type' => $str( 32 ),
                'parent_id' => array( 'length' => 26, 'type' => 'char', 'default' => null ),
                'pseudonymised' => $int( 4 ),
                'reason' => $str( 32 ),
                'record' => array( 'type' => 'longtext', 'default' => null ),
                'request_id' => $str( 40 ),
                'result' => $str( 8 ),
                'run_id' => $str( 40 ),
                'search_text' => array( 'type' => 'longtext', 'default' => null ),
                'seq' => $int( 11 ),
                'session_h' => $str( 24 ),
                'severity' => $int( 4 ),
                'siteaccess' => $str( 64 ),
                'target_id' => $str( 64 ),
                'target_type' => $str( 32 ),
                'time_ms' => $big(),
                'ua' => $str( 128 ),
                'user_id' => $int( 11, false ),
                'verb' => $str( 32 ),
            ),
            'indexes' => array(
                'PRIMARY' => $idx( 'primary', array( 'id' ) ),
                'expaudit_event_domain' => $idx( 'non-unique', array( 'domain_name', 'severity', 'time_ms' ) ),
                'expaudit_event_file_seq' => $idx( 'unique', array( 'channel', 'file_name', 'seq' ) ),
                'expaudit_event_ip' => $idx( 'non-unique', array( 'ip', 'time_ms' ) ),
                'expaudit_event_job' => $idx( 'non-unique', array( 'job_id' ) ),
                'expaudit_event_name' => $idx( 'non-unique', array( 'name', 'time_ms' ) ),
                'expaudit_event_object' => $idx( 'non-unique', array( 'object_type', 'object_id', 'time_ms' ) ),
                'expaudit_event_parent' => $idx( 'non-unique', array( 'parent_id' ) ),
                'expaudit_event_request' => $idx( 'non-unique', array( 'request_id' ) ),
                'expaudit_event_result' => $idx( 'non-unique', array( 'result', 'time_ms' ) ),
                'expaudit_event_time' => $idx( 'non-unique', array( 'time_ms' ) ),
                'expaudit_event_user' => $idx( 'non-unique', array( 'user_id', 'time_ms' ) ),
            ),
        );

        $cursor = array(
            'name' => self::CURSOR,
            'fields' => array(
                'byte_offset' => $big(),
                'channel' => $str( 32, true ),
                'file_name' => $str( 64, true ),
                'last_hash' => $str( 80 ),
                'last_seq' => $int( 11 ),
                'updated_ms' => $big(),
            ),
            'indexes' => array(
                'PRIMARY' => $idx( 'primary', array( 'channel', 'file_name' ) ),
            ),
        );

        $file = array(
            'name' => self::FILE,
            'fields' => array(
                'archive_path' => $str( 255 ),
                'break_line' => $int( 11 ),
                'channel' => $str( 32, true ),
                'file_name' => $str( 64, true ),
                'records' => $int( 11 ),
                'state' => array( 'length' => 16, 'type' => 'varchar', 'not_null' => '1', 'default' => 'live' ),
                'verified' => array( 'length' => 16, 'type' => 'varchar', 'not_null' => '1', 'default' => 'unchecked' ),
                'verified_ms' => $big(),
            ),
            'indexes' => array(
                'PRIMARY' => $idx( 'primary', array( 'channel', 'file_name' ) ),
            ),
        );

        return array( self::CURSOR => $cursor, self::EVENT => $event, self::FILE => $file );
    }

    /**
     * The statements each engine adds for full-text search (doc/bc/6.0/audit.md, "Full-text search per engine").
     * Oracle's CONTEXT index needs Oracle Text (the CTXAPP role); install() tries it and falls back to LIKE.
     *
     * @param string $type eZDB databaseName(): mysql, postgresql, sqlite, oracle, mongodb
     * @return string[]
     */
    public static function fullTextSQL( $type )
    {
        switch ( $type )
        {
            case 'mysql':
                return array( 'ALTER TABLE expaudit_event ADD FULLTEXT INDEX expaudit_event_fts (search_text)' );
            case 'postgresql':
                return array( "ALTER TABLE expaudit_event ADD COLUMN search_tsv tsvector GENERATED ALWAYS AS (to_tsvector('simple', coalesce(search_text, ''))) STORED",
                              'CREATE INDEX expaudit_event_tsv ON expaudit_event USING GIN (search_tsv)' );
            case 'sqlite':
                return array( "CREATE VIRTUAL TABLE expaudit_event_fts USING fts5(search_text, content='expaudit_event', content_rowid='rowid', tokenize='trigram')" );
            case 'oracle':
                return array( "CREATE INDEX expaudit_event_ctx ON expaudit_event(search_text) INDEXTYPE IS CTXSYS.CONTEXT PARAMETERS ('SYNC (ON COMMIT)')" );
        }
        return array();
    }

    /**
     * @param eZDBInterface|null $db
     * @return string mysql, postgresql, sqlite, oracle, mongodb, ...
     */
    public static function type( $db = null )
    {
        $db = $db ?: eZDB::instance();
        $type = strtolower( (string)$db->databaseName() );
        if ( strpos( $type, 'mongo' ) === 0 )
            return 'mongodb';
        if ( $type === 'mysqli' )
            return 'mysql';
        return $type;
    }

    /** @return string a key for the database this process talks to */
    protected static function key( $db )
    {
        return self::type( $db ) . ':' . spl_object_id( $db );
    }

    /**
     * The three tables exist (asked once per request and database).
     *
     * @param eZDBInterface|null $db
     * @return bool
     */
    public static function isInstalled( $db = null )
    {
        $db = $db ?: eZDB::instance();
        $key = self::key( $db ) . '@' . ( isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? $_SERVER['REQUEST_TIME_FLOAT'] : '' );
        if ( isset( self::$installed[$key] ) )
            return self::$installed[$key];
        $missing = self::missingTables( $db );
        return self::$installed[$key] = ( $missing !== null && !$missing );
    }

    /**
     * @param eZDBInterface $db
     * @return string[]|null the tables not there; null when the database cannot be asked
     */
    public static function missingTables( $db )
    {
        // asked directly: the drivers' relationList() keeps the tables whose name starts with "ez" only
        $names = array( self::EVENT, self::CURSOR, self::FILE );
        $in = "'" . implode( "', '", $names ) . "'";
        $list = null;
        switch ( self::type( $db ) )
        {
            case 'sqlite':
                $rows = self::tryArrayQuery( $db, "SELECT name FROM sqlite_master WHERE type = 'table' AND name IN ($in)" );
                break;
            case 'mysql':
                $rows = self::tryArrayQuery( $db, "SELECT table_name AS name FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name IN ($in)" );
                break;
            case 'postgresql':
                $rows = self::tryArrayQuery( $db, "SELECT relname AS name FROM pg_class WHERE relkind = 'r' AND relname IN ($in)" );
                break;
            case 'oracle':
                $rows = self::tryArrayQuery( $db, "SELECT LOWER(table_name) AS name FROM user_tables WHERE table_name IN (" . strtoupper( $in ) . ")" );
                break;
            case 'mongodb':
                $rows = null;
                if ( method_exists( $db, 'listCollectionNames' ) )
                {
                    try
                    {
                        $rows = array();
                        foreach ( (array)$db->listCollectionNames() as $n )
                            $rows[] = array( 'name' => $n );
                    }
                    catch ( Throwable $e )
                    {
                        $rows = null;
                    }
                }
                break;
            default:
                try
                {
                    $rows = array();
                    foreach ( (array)$db->relationList() as $n )
                        $rows[] = array( 'name' => $n );
                }
                catch ( Throwable $e )
                {
                    $rows = null;
                }
        }
        if ( !is_array( $rows ) )
            return null;
        $list = array();
        foreach ( $rows as $r )
            $list[] = isset( $r['name'] ) ? $r['name'] : reset( $r );
        $have = array_flip( array_map( 'strtolower', $list ) );
        $missing = array();
        foreach ( array( self::EVENT, self::CURSOR, self::FILE ) as $t )
            if ( !isset( $have[$t] ) )
                $missing[] = $t;
        return $missing;
    }

    /**
     * Creates the tables that are missing, then the full-text part. Idempotent: existing tables are left alone.
     *
     * @param eZDBInterface|null $db
     * @param string[] $messages what was done, one line each
     * @return bool all three tables exist afterwards
     */
    public static function install( $db = null, &$messages = array() )
    {
        $db = $db ?: eZDB::instance();
        $type = self::type( $db );
        $missing = self::missingTables( $db );
        if ( $missing === null )
        {
            $messages[] = 'cannot list the tables of this database';
            return false;
        }
        if ( $missing )
        {
            $schema = array_intersect_key( self::definition(), array_flip( $missing ) );
            $handler = eZDbSchema::instance( array( 'type' => $type === 'mongodb' ? $db->databaseName() : $type,
                                                    'instance' => $db, 'schema' => $schema ) );
            if ( !$handler )
            {
                $messages[] = "no schema handler for $type";
                return false;
            }
            $ok = $handler->insertSchema( array( 'schema' => true, 'data' => false, 'table_type' => $type === 'mysql' ? 'innodb' : '' ) );
            $messages[] = ( $ok ? 'created ' : 'could not create ' ) . implode( ', ', $missing );
            if ( !$ok )
                return false;
            if ( in_array( self::EVENT, $missing, true ) )
                $messages = array_merge( $messages, self::installFullText( $db ) );
        }
        else
            $messages[] = 'the tables exist already';
        self::$installed = array();
        self::$fullText = array();
        $missing = self::missingTables( $db );
        return $missing !== null && !$missing;
    }

    /**
     * The full-text part of this engine; a statement the database refuses (no FTS5 in this SQLite build, no
     * Oracle Text, MySQL before 5.6) is reported and search uses LIKE.
     *
     * @param eZDBInterface $db
     * @return string[] messages
     */
    public static function installFullText( $db )
    {
        $messages = array();
        foreach ( self::fullTextSQL( self::type( $db ) ) as $sql )
        {
            $ok = self::tryQuery( $db, $sql );
            $messages[] = ( $ok ? 'full text: ' : 'full text not available (search uses LIKE): ' ) . strtok( $sql, '(' );
            if ( !$ok )
                break;
        }
        self::$fullText = array();
        return $messages;
    }

    /**
     * The full-text search this database has for the index.
     *
     * @param eZDBInterface|null $db
     * @return string fts5, mysql, postgresql, oracle or like
     */
    public static function fullTextKind( $db = null )
    {
        $db = $db ?: eZDB::instance();
        $key = self::key( $db );
        if ( isset( self::$fullText[$key] ) )
            return self::$fullText[$key];
        $kind = 'like';
        $type = self::type( $db );
        $rows = null;
        switch ( $type )
        {
            case 'sqlite':
                $rows = self::tryArrayQuery( $db, "SELECT name FROM sqlite_master WHERE name = '" . self::FTS . "'" );
                if ( $rows )
                    $kind = 'fts5';
                break;
            case 'mysql':
                $rows = self::tryArrayQuery( $db, "SHOW INDEX FROM expaudit_event WHERE Key_name = 'expaudit_event_fts'" );
                if ( $rows )
                    $kind = 'mysql';
                break;
            case 'postgresql':
                $rows = self::tryArrayQuery( $db, "SELECT column_name FROM information_schema.columns WHERE table_name = 'expaudit_event' AND column_name = 'search_tsv'" );
                if ( $rows )
                    $kind = 'postgresql';
                break;
            case 'oracle':
                $rows = self::tryArrayQuery( $db, "SELECT index_name FROM user_indexes WHERE index_name = 'EXPAUDIT_EVENT_CTX'" );
                if ( $rows )
                    $kind = 'oracle';
                break;
        }
        return self::$fullText[$key] = $kind;
    }

    /**
     * Runs a statement with exceptions as the error handling, so a refused statement never ends the request
     * (the standard handling stops a request whose transaction fails).
     *
     * @param eZDBInterface $db
     * @param string $sql
     * @return bool
     */
    public static function tryQuery( $db, $sql )
    {
        $before = self::errorHandling( $db );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        try
        {
            $ok = (bool)$db->query( $sql );
        }
        catch ( Throwable $e )
        {
            $ok = false;
        }
        $db->setErrorHandling( $before );
        return $ok;
    }

    /**
     * @param eZDBInterface $db
     * @param string $sql
     * @param array $params offset, limit
     * @return array|null rows, null when the database refused
     */
    public static function tryArrayQuery( $db, $sql, array $params = array() )
    {
        $before = self::errorHandling( $db );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        try
        {
            $rows = $db->arrayQuery( $sql, $params );
        }
        catch ( Throwable $e )
        {
            $rows = null;
        }
        $db->setErrorHandling( $before );
        return is_array( $rows ) ? $rows : null;
    }

    /**
     * @param eZDBInterface $db
     * @return int the error handling mode the connection has now
     */
    public static function errorHandling( $db )
    {
        try
        {
            $mode = ( function () { return isset( $this->errorHandling ) ? $this->errorHandling : null; } )->call( $db );
        }
        catch ( Throwable $e )
        {
            $mode = null;
        }
        return $mode === null ? eZDB::ERROR_HANDLING_STANDARD : $mode;
    }
}
