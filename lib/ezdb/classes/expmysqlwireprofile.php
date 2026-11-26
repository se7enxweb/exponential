<?php
/**
 * File containing the expMySQLWireProfile class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * What a database that speaks the MySQL protocol does differently from MySQL, and the statements the MySQL driver
 * writes rewritten for it: TiDB, Vitess (PlanetScale), SingleStore, MariaDB Xpand and Amazon Aurora MySQL.
 *
 * The drivers of these databases (expTiDBDB, expVitessDB, expSingleStoreDB, expXpandDB, expAuroraMySQLDB) are the
 * MySQL driver eZMySQLiDB with one of these profiles: databaseName() stays 'mysql', so the kernel's MySQL SQL, the
 * MySQL schema handler and the MySQL cluster backend run unchanged, and every difference is handled here. Everything
 * in this class is a pure function of its arguments (no connection, no settings), tested without a server in
 * tests/tests/lib/ezdb/mysqlwire.
 *
 * The facts of each profile were measured against TiDB 8.5.1, Vitess 25 (vttestserver, MySQL 8.4 underneath) and
 * SingleStore 10.10 in containers; Xpand and Aurora MySQL could not run locally, and their profiles follow the vendors'
 * documentation (doc/eng-us/features/6.0/mysql-wire-databases.md says which line is measured and which is not).
 */
class expMySQLWireProfile
{
    const MYSQL = 'mysql';
    const MARIADB = 'mariadb';
    const TIDB = 'tidb';
    const VITESS = 'vitess';
    const SINGLESTORE = 'singlestore';
    const XPAND = 'xpand';
    const AURORA = 'aurora';

    /** The collation a table gets when its statement names none, on a server whose default is binary (utf8mb4_bin) */
    const CASE_INSENSITIVE_COLLATION = 'utf8mb4_general_ci';

    /**
     * The facts of each profile.
     *
     * - label, driver, implementation (DatabaseImplementation), port: names and the usual port
     * - level: site (a whole site installs and runs), datatable (data-table connections and imports), import
     * - measured: true when the facts were measured against a running server
     * - foreign_keys: enforced, refused (the server rejects a FOREIGN KEY: it is left out), ignored (accepted, not kept)
     * - savepoints: true when ROLLBACK TO SAVEPOINT undoes the work after the savepoint
     * - isolation_levels: the levels SET TRANSACTION ISOLATION LEVEL accepts and keeps
     * - default_isolation: the server's own default
     * - table_locks: native (LOCK TABLES locks), named (LOCK TABLES is a no-op, GET_LOCK() is used), none
     * - named_locks: GET_LOCK()/RELEASE_LOCK() work
     * - share_lock: native, for_update (LOCK IN SHARE MODE / FOR SHARE are refused: FOR UPDATE is sent), lock_in_share_mode
     * - fulltext: native, ignored (accepted, no index), refused (left out; the kernel then searches with LIKE)
     * - auto_increment: consecutive (as MySQL), cached (unique, increasing per server, gaps across servers),
     *   unique (unique only, not in insert order)
     * - binary_default_collation: the server's default collation is case sensitive (utf8mb4_bin)
     * - reference_tables: tables are created as replicated rowstore reference tables (SingleStore)
     * - rename_table, change_column_type, delete_order_limit, xa, optimize_table, checksum_table, unix_timestamp_2038:
     *   statements and functions that work (true) or not (false)
     * - create_database: true when CREATE/DROP DATABASE are allowed (a keyspace or a managed database: false)
     * - failover_read_only: a connection can land on a node that became read-only after a failover (Aurora)
     *
     * @return array name => facts
     */
    public static function definitions()
    {
        $mysql = array(
            'label' => 'MySQL', 'driver' => 'eZMySQLiDB', 'implementation' => 'mysqli', 'port' => 3306,
            'level' => 'site', 'measured' => true,
            'foreign_keys' => 'enforced', 'savepoints' => true,
            'isolation_levels' => array( 'READ UNCOMMITTED', 'READ COMMITTED', 'REPEATABLE READ', 'SERIALIZABLE' ),
            'default_isolation' => 'REPEATABLE READ',
            'table_locks' => 'native', 'named_locks' => true, 'share_lock' => 'native', 'fulltext' => 'native',
            'auto_increment' => 'consecutive', 'binary_default_collation' => false, 'reference_tables' => false,
            'rename_table' => true, 'change_column_type' => true, 'delete_order_limit' => true, 'xa' => true,
            'optimize_table' => true, 'checksum_table' => true, 'unix_timestamp_2038' => true, 'create_database' => true,
            'failover_read_only' => false,
        );

        return array(
            self::MYSQL => $mysql,
            self::MARIADB => array( 'label' => 'MariaDB' ) + $mysql,
            // TiDB 8.5 (measured): BEGIN WORK, LOCK IN SHARE MODE, XA, OPTIMIZE TABLE and the isolation levels READ
            // UNCOMMITTED and SERIALIZABLE are refused; LOCK TABLES is a no-op unless the server runs with
            // enable-table-lock; the default collation is utf8mb4_bin
            self::TIDB => array(
                'label' => 'TiDB', 'driver' => 'expTiDBDB', 'implementation' => 'tidb', 'port' => 4000,
                'isolation_levels' => array( 'READ COMMITTED', 'REPEATABLE READ' ),
                'table_locks' => 'named', 'share_lock' => 'for_update', 'fulltext' => 'ignored',
                'auto_increment' => 'cached', 'binary_default_collation' => true,
                'xa' => false, 'optimize_table' => false, 'checksum_table' => false,
            ) + $mysql,
            // Vitess / PlanetScale, one unsharded keyspace (measured on vttestserver): foreign keys are refused with
            // foreign_key_mode=disallow (PlanetScale's default), BEGIN WORK, XA and CHECKSUM TABLE are refused; a
            // keyspace is not created by CREATE DATABASE; a transaction longer than vttablet's transaction timeout
            // (30 seconds by default, 20 on PlanetScale) is killed
            self::VITESS => array(
                'label' => 'Vitess / PlanetScale', 'driver' => 'expVitessDB', 'implementation' => 'vitess', 'port' => 3306,
                'foreign_keys' => 'refused', 'xa' => false, 'checksum_table' => false, 'create_database' => false,
                'default_isolation' => 'REPEATABLE READ',
            ) + $mysql,
            // SingleStore 10.10 (measured, the dev container): a unique key must hold the shard key, so the kernel's
            // tables become reference tables; foreign keys, FULLTEXT on rowstore, GET_LOCK(), RENAME TABLE, CHANGE
            // of a column type, DELETE ... ORDER BY ... LIMIT and XA are refused; SAVEPOINT is accepted and does
            // nothing; isolation is READ COMMITTED whatever is set; UNIX_TIMESTAMP() ends in 2038
            self::SINGLESTORE => array(
                'label' => 'SingleStore', 'driver' => 'expSingleStoreDB', 'implementation' => 'singlestore', 'port' => 3306,
                'foreign_keys' => 'refused', 'savepoints' => false,
                'isolation_levels' => array( 'READ COMMITTED' ), 'default_isolation' => 'READ COMMITTED',
                'table_locks' => 'native', 'named_locks' => false, 'share_lock' => 'lock_in_share_mode',
                'fulltext' => 'refused', 'auto_increment' => 'consecutive', 'binary_default_collation' => true,
                'reference_tables' => true, 'rename_table' => false, 'change_column_type' => false,
                'delete_order_limit' => false, 'xa' => false, 'checksum_table' => false, 'unix_timestamp_2038' => false,
            ) + $mysql,
            // MariaDB Xpand (documentation; no container image is published): distributed, FULLTEXT is not
            // supported, AUTO_INCREMENT is unique but not in insert order, LOCK TABLES is accepted and ignored
            self::XPAND => array(
                'label' => 'MariaDB Xpand', 'driver' => 'expXpandDB', 'implementation' => 'xpand', 'port' => 3306,
                'level' => 'site', 'measured' => false,
                'isolation_levels' => array( 'READ COMMITTED', 'REPEATABLE READ', 'SERIALIZABLE' ),
                'table_locks' => 'named', 'fulltext' => 'refused', 'auto_increment' => 'unique', 'xa' => false,
            ) + $mysql,
            // Amazon Aurora MySQL 3 (documentation; a managed service only): MySQL 8.0 itself, but after a failover
            // a connection can reach the old writer, which is read-only now (1290, 1836)
            self::AURORA => array(
                'label' => 'Amazon Aurora MySQL', 'driver' => 'expAuroraMySQLDB', 'implementation' => 'aurora', 'port' => 3306,
                'measured' => false, 'failover_read_only' => true,
            ) + $mysql,
        );
    }

    /**
     * The names of the profiles, MySQL and MariaDB first.
     *
     * @return string[]
     */
    public static function names()
    {
        return array_keys( self::definitions() );
    }

    /**
     * The facts of one profile.
     *
     * @param string $name
     * @return array
     * @throws InvalidArgumentException for an unknown name
     */
    public static function get( $name )
    {
        $definitions = self::definitions();
        $name = self::normalizeName( $name );
        if ( !isset( $definitions[$name] ) )
            throw new InvalidArgumentException( "Unknown MySQL-wire profile '$name' (" . implode( ', ', array_keys( $definitions ) ) . ')' );
        return array( 'name' => $name ) + $definitions[$name];
    }

    /**
     * A profile name from what a person may write: planetscale, memsql, aurora-mysql ...
     *
     * @param string $name
     * @return string
     */
    public static function normalizeName( $name )
    {
        $name = strtolower( trim( (string)$name ) );
        $aliases = array( 'planetscale' => self::VITESS, 'vttestserver' => self::VITESS, 'memsql' => self::SINGLESTORE,
                          'singlestoredb' => self::SINGLESTORE, 'clustrix' => self::XPAND, 'mariadb-xpand' => self::XPAND,
                          'aurora-mysql' => self::AURORA, 'auroramysql' => self::AURORA, 'aurora_mysql' => self::AURORA,
                          'mysqli' => self::MYSQL, 'ezmysqli' => self::MYSQL, 'ezmysql' => self::MYSQL );
        return isset( $aliases[$name] ) ? $aliases[$name] : $name;
    }

    /**
     * The profile of a running server, from what it says about itself.
     *
     * @param string $version SELECT VERSION()
     * @param string $versionComment SELECT @@version_comment
     * @param array $variables more answers, when asked: 'aurora_version', 'memsql_version' (null: the server does not know it)
     * @return string a profile name
     */
    public static function detect( $version, $versionComment = '', array $variables = array() )
    {
        $version = (string)$version;
        $comment = (string)$versionComment;
        if ( stripos( $version, '-TiDB-' ) !== false || stripos( $comment, 'TiDB' ) !== false )
            return self::TIDB;
        if ( stripos( $version, 'Vitess' ) !== false || stripos( $comment, 'Vitess' ) !== false || stripos( $comment, 'PlanetScale' ) !== false )
            return self::VITESS;
        if ( !empty( $variables['memsql_version'] ) || stripos( $comment, 'SingleStore' ) !== false || stripos( $comment, 'MemSQL' ) !== false )
            return self::SINGLESTORE;
        if ( stripos( $version, 'Xpand' ) !== false || stripos( $version, 'clustrix' ) !== false || stripos( $comment, 'Xpand' ) !== false )
            return self::XPAND;
        if ( !empty( $variables['aurora_version'] ) )
            return self::AURORA;
        if ( stripos( $version, 'MariaDB' ) !== false || stripos( $comment, 'MariaDB' ) !== false )
            return self::MARIADB;
        return self::MYSQL;
    }

    /**
     * The statement as the server of the profile takes it: an array of statements to run in their order (one in most
     * cases, none when the statement has nothing to do there, such as a FULLTEXT index on SingleStore).
     *
     * @param string $profile
     * @param string $sql one statement
     * @param array $options 'auto_id_cache' (TiDB: AUTO_ID_CACHE of a new table with an auto_increment column, 0: off)
     * @return string[]
     */
    public static function rewriteStatement( $profile, $sql, array $options = array() )
    {
        $sql = (string)$sql;
        // most statements are data: they go as they are, without a look past their first word
        if ( !preg_match( '/^\s*(CREATE|ALTER|RENAME|OPTIMIZE|BEGIN|SELECT|\()/i', $sql, $first ) )
            return array( $sql );
        if ( ( strtoupper( $first[1] ) === 'SELECT' || $first[1] === '(' ) && stripos( $sql, 'SHARE' ) === false )
            return array( $sql );
        $facts = self::get( $profile );
        $trimmed = rtrim( trim( $sql ), ';' );
        if ( $trimmed === '' )
            return array( $sql );

        // CREATE TABLE (not TEMPORARY, not ... LIKE / AS SELECT)
        if ( preg_match( '/^CREATE\s+TABLE\s/i', $trimmed ) )
            return array( self::rewriteCreateTable( $facts, $trimmed, $options ) );

        // ALTER TABLE t ADD [CONSTRAINT c] FOREIGN KEY ... / ADD FULLTEXT ...
        if ( preg_match( '/^ALTER\s+TABLE\s/i', $trimmed ) )
        {
            if ( $facts['foreign_keys'] !== 'enforced' && preg_match( '/^ALTER\s+TABLE\s+\S+\s+ADD\s+(CONSTRAINT\s+\S+\s+)?FOREIGN\s+KEY\b/i', $trimmed ) )
                return array();
            if ( $facts['fulltext'] === 'refused' && preg_match( '/^ALTER\s+TABLE\s+\S+\s+ADD\s+FULLTEXT\b/i', $trimmed ) )
                return array();
            return array( $sql );
        }
        if ( $facts['fulltext'] === 'refused' && preg_match( '/^CREATE\s+FULLTEXT\s+INDEX\b/i', $trimmed ) )
            return array();

        // RENAME TABLE a TO b [, c TO d]
        if ( !$facts['rename_table'] && preg_match( '/^RENAME\s+TABLE\s+(.+)$/is', $trimmed, $m ) )
        {
            $statements = array();
            foreach ( self::splitTopLevel( $m[1], ',' ) as $pair )
            {
                if ( !preg_match( '/^\s*(\S+)\s+TO\s+(\S+)\s*$/i', $pair, $p ) )
                    return array( $sql );
                $statements[] = "ALTER TABLE {$p[1]} RENAME TO {$p[2]}";
            }
            return $statements;
        }

        if ( !$facts['optimize_table'] && preg_match( '/^OPTIMIZE\s+(?:NO_WRITE_TO_BINLOG\s+|LOCAL\s+)?TABLE\s+(.+)$/is', $trimmed, $m ) )
            return array( 'ANALYZE TABLE ' . $m[1] );

        if ( preg_match( '/^BEGIN\s+WORK$/i', $trimmed ) )
            return array( 'BEGIN' );

        // a SELECT that takes a shared lock
        if ( preg_match( '/^(SELECT|\()/i', $trimmed ) && preg_match( '/\s(LOCK\s+IN\s+SHARE\s+MODE|FOR\s+SHARE)$/i', $trimmed, $m ) )
        {
            $head = substr( $trimmed, 0, -strlen( $m[1] ) );
            if ( $facts['share_lock'] === 'for_update' )
                return array( $head . 'FOR UPDATE' );
            if ( $facts['share_lock'] === 'lock_in_share_mode' )
                return array( $head . 'LOCK IN SHARE MODE' );
        }

        return array( $sql );
    }

    /**
     * CREATE TABLE for the profile: foreign keys and FULLTEXT keys the server refuses left out, a case-insensitive
     * default collation where the server's default is binary, TiDB's AUTO_ID_CACHE, SingleStore's reference tables.
     *
     * @param array $facts
     * @param string $sql without the closing semicolon
     * @param array $options
     * @return string
     */
    protected static function rewriteCreateTable( array $facts, $sql, array $options )
    {
        $open = strpos( $sql, '(' );
        $close = $open === false ? false : self::matchingParenthesis( $sql, $open );
        // CREATE TABLE a LIKE b copies the kind of b as it is; CREATE TABLE a AS SELECT ...: nothing to change but
        // the table kind (no primary key: a sharded rowstore table on SingleStore)
        if ( preg_match( '/^CREATE\s+TABLE\s+(IF\s+NOT\s+EXISTS\s+)?\S+\s+\(?\s*LIKE\b/i', $sql ) )
            return $sql;
        if ( $open === false || $close === false || preg_match( '/^CREATE\s+TABLE\s+(IF\s+NOT\s+EXISTS\s+)?\S+\s+(AS|SELECT)\b/i', $sql ) )
            return $facts['reference_tables'] ? self::singleStoreKind( $sql, false ) : $sql;

        $head = substr( $sql, 0, $open );
        $body = substr( $sql, $open + 1, $close - $open - 1 );
        $tail = substr( $sql, $close + 1 );

        $elements = self::splitTopLevel( $body, ',' );
        $kept = array();
        foreach ( $elements as $element )
        {
            $e = trim( $element );
            if ( $facts['foreign_keys'] !== 'enforced' && preg_match( '/^(CONSTRAINT\s+\S+\s+)?FOREIGN\s+KEY\b/i', $e ) )
                continue;
            if ( $facts['fulltext'] === 'refused' && preg_match( '/^FULLTEXT\b/i', $e ) )
                continue;
            $kept[] = $element;
        }
        if ( count( $kept ) !== count( $elements ) )
        {
            // every element keeps the space before it; the body keeps the space it ended with
            preg_match( '/\s*$/', $body, $trailing );
            $body = rtrim( implode( ',', $kept ) ) . $trailing[0];
        }

        if ( $tail !== '' && preg_match( '/\bSELECT\b/i', $tail ) )
            return ( $facts['reference_tables'] ? self::singleStoreKind( $head, false ) : $head ) . '(' . $body . ')' . $tail;

        $options_ = array();
        if ( $facts['binary_default_collation'] && !preg_match( '/\bCOLLATE\b/i', $tail ) )
        {
            $charset = null;
            if ( preg_match( '/\b(?:CHARACTER\s+SET|CHARSET)\s*=?\s*`?(\w+)`?/i', $tail, $m ) )
                $charset = strtolower( $m[1] );
            if ( $charset === null || $charset === 'utf8mb4' )
                $options_[] = 'COLLATE=' . self::CASE_INSENSITIVE_COLLATION;
            else if ( $charset === 'utf8' || $charset === 'utf8mb3' )
                $options_[] = 'COLLATE=utf8_general_ci';
        }
        if ( $facts['name'] === self::TIDB && !empty( $options['auto_id_cache'] ) && (int)$options['auto_id_cache'] > 0
             && preg_match( '/\bauto_increment\b/i', $body ) && !preg_match( '/\bAUTO_ID_CACHE\b/i', $tail ) )
        {
            $options_[] = 'AUTO_ID_CACHE=' . (int)$options['auto_id_cache'];
        }
        if ( $options_ )
            $tail = rtrim( $tail ) . ' ' . implode( ' ', $options_ );

        if ( $facts['reference_tables'] )
            $head = self::singleStoreKind( $head, (bool)preg_match( '/\bPRIMARY\s+KEY\b/i', $body ) );

        return $head . '(' . $body . ')' . $tail;
    }

    /**
     * CREATE TABLE as SingleStore's replicated rowstore reference table, or as a sharded rowstore table when there is
     * no primary key (a reference table needs one).
     *
     * @param string $sql a statement or its head, starting with CREATE TABLE
     * @param bool $hasPrimaryKey
     * @return string
     */
    protected static function singleStoreKind( $sql, $hasPrimaryKey )
    {
        return preg_replace( '/^CREATE\s+TABLE\s/i', $hasPrimaryKey ? 'CREATE ROWSTORE REFERENCE TABLE ' : 'CREATE ROWSTORE TABLE ', $sql, 1 );
    }

    /**
     * The position of the parenthesis that closes the one at $open, outside quotes and comments; false when none.
     *
     * @param string $sql
     * @param int $open
     * @return int|false
     */
    public static function matchingParenthesis( $sql, $open )
    {
        $depth = 0;
        $length = strlen( $sql );
        for ( $i = $open; $i < $length; ++$i )
        {
            $c = $sql[$i];
            if ( $c === "'" || $c === '"' || $c === '`' )
            {
                $i = self::skipQuoted( $sql, $i );
                continue;
            }
            if ( $c === '(' )
                ++$depth;
            else if ( $c === ')' )
            {
                --$depth;
                if ( $depth === 0 )
                    return $i;
            }
        }
        return false;
    }

    /**
     * $text split at $separator where it is outside parentheses and quotes.
     *
     * @param string $text
     * @param string $separator one character
     * @return string[]
     */
    public static function splitTopLevel( $text, $separator )
    {
        $parts = array();
        $depth = 0;
        $start = 0;
        $length = strlen( $text );
        for ( $i = 0; $i < $length; ++$i )
        {
            $c = $text[$i];
            if ( $c === "'" || $c === '"' || $c === '`' )
            {
                $i = self::skipQuoted( $text, $i );
                continue;
            }
            if ( $c === '(' )
                ++$depth;
            else if ( $c === ')' )
                --$depth;
            else if ( $c === $separator && $depth === 0 )
            {
                $parts[] = substr( $text, $start, $i - $start );
                $start = $i + 1;
            }
        }
        $parts[] = substr( $text, $start );
        return $parts;
    }

    /**
     * The position of the quote that ends the quoted text starting at $i (MySQL: a backslash escapes in ' and ",
     * a doubled quote is one quote).
     *
     * @param string $text
     * @param int $i
     * @return int
     */
    protected static function skipQuoted( $text, $i )
    {
        $quote = $text[$i];
        $length = strlen( $text );
        for ( $j = $i + 1; $j < $length; ++$j )
        {
            if ( $text[$j] === '\\' && $quote !== '`' )
            {
                ++$j;
                continue;
            }
            if ( $text[$j] === $quote )
            {
                if ( $j + 1 < $length && $text[$j + 1] === $quote )
                {
                    ++$j;
                    continue;
                }
                return $j;
            }
        }
        return $length - 1;
    }

    /**
     * The isolation level as SET TRANSACTION takes it, when the profile keeps it; null for an empty or unknown level
     * or one the server refuses or does not keep.
     *
     * @param string $profile
     * @param string $level READ COMMITTED, read-committed, REPEATABLE READ ...
     * @return string|null
     */
    public static function isolationLevel( $profile, $level )
    {
        $level = strtoupper( trim( str_replace( array( '-', '_' ), ' ', (string)$level ) ) );
        $level = preg_replace( '/\s+/', ' ', $level );
        if ( $level === '' )
            return null;
        $facts = self::get( $profile );
        return in_array( $level, $facts['isolation_levels'], true ) ? $level : null;
    }

    /**
     * Seconds since 1970 of now or of a date/time expression: SingleStore's UNIX_TIMESTAMP() ends in 2038, so there
     * it is counted with TIMESTAMPDIFF.
     *
     * @param string $profile
     * @param string|null $expression
     * @return string|null null: the MySQL helper is right
     */
    public static function unixTimestamp( $profile, $expression = null )
    {
        $facts = self::get( $profile );
        if ( $facts['unix_timestamp_2038'] )
            return null;
        return "TIMESTAMPDIFF(SECOND, '1970-01-01 00:00:00', " . ( $expression === null ? 'UTC_TIMESTAMP()' : $expression ) . ')';
    }

    /**
     * A date/time from seconds since 1970, past 2038 on SingleStore too.
     *
     * @param string $profile
     * @param string $expression
     * @return string|null null: the MySQL helper is right
     */
    public static function fromUnixTimestamp( $profile, $expression )
    {
        $facts = self::get( $profile );
        if ( $facts['unix_timestamp_2038'] )
            return null;
        return "TIMESTAMPADD(SECOND, $expression, '1970-01-01 00:00:00')";
    }

    /**
     * The name of the GET_LOCK() lock that stands for a table lock.
     *
     * @param string $database
     * @param string $table
     * @return string at most 64 characters (MySQL's limit)
     */
    public static function namedLockName( $database, $table )
    {
        $name = 'exp:' . $database . '.' . $table;
        return strlen( $name ) <= 64 ? $name : 'exp:' . md5( $name );
    }

    /**
     * True for errors after which a statement outside a transaction is sent again on a new connection: the server
     * went away (2006, 2013), or, on a database with failover (Aurora), the connection reached a node that is
     * read-only now (1290 --read-only, 1836 read-only mode).
     *
     * @param string $profile
     * @param int $errno
     * @return bool
     */
    public static function isReconnectError( $profile, $errno )
    {
        $facts = self::get( $profile );
        $errno = (int)$errno;
        if ( in_array( $errno, array( 2006, 2013 ), true ) )
            return $facts['failover_read_only'];
        return $facts['failover_read_only'] && in_array( $errno, array( 1290, 1836 ), true );
    }
}
