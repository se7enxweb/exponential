<?php
/**
 * File containing the expPostgreSQLWireProfile class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * What a database that speaks the PostgreSQL wire protocol does differently from PostgreSQL itself: one profile per
 * database, read by expPostgreSQLWireDB (the driver), eZPgsqlSchema (the DDL it writes) and the installer. Pure data
 * and pure functions: nothing here opens a connection, so every rule is tested without a server.
 *
 * Profiles:
 *
 *  - cockroachdb: CockroachDB 23.2 or later (tested live: 25.2). Serializable by default, so a statement can fail with
 *    40001 at any time; no LOCK TABLE; pg_try_advisory_lock() exists but locks nothing; temporary tables only with a
 *    session setting; an INTEGER column is 64 bits.
 *  - yugabytedb: YugabyteDB 2.20 or later (tested live: 2.25). No LOCK TABLE ... ACCESS EXCLUSIVE; advisory locks only
 *    as a preview flag; adding a primary key after CREATE TABLE rewrites the table; sequences hand out numbers in
 *    blocks per node (CACHE), so ids are unique but not in order across nodes; no pgcrypto by default.
 *  - spanner: Google Cloud Spanner, PostgreSQL dialect, through PGAdapter (tested live: the emulator). Only
 *    bit-reversed sequences (64-bit ids, no currval/lastval), no temporary tables, no LOCK, no advisory locks, no
 *    smallint, no timestamp without time zone, no md5(), no SELECT ... FOR UPDATE outside a read-write transaction,
 *    no DDL inside a transaction. Not a site database: data-table connections and imports.
 *  - neon: Neon serverless PostgreSQL. A suspended compute wakes on the first connection (a cold start of a second
 *    or more); the "-pooler" host is PgBouncer in transaction mode; TLS is required. Not tested live.
 *  - supabase: Supabase PostgreSQL. The direct host (db.<ref>.supabase.co, IPv6) is plain PostgreSQL; the
 *    Supavisor pooler is transaction mode on port 6543 and session mode on 5432; TLS is required. Not tested live.
 *  - aurorapostgresql: Amazon Aurora PostgreSQL. The cluster endpoint is the writer, the reader endpoint the replicas; a
 *    failover drops every connection (57P01, 08006); a reader is in recovery but has no WAL receiver. Not tested live.
 *
 * @since 6.0.15
 */
class expPostgreSQLWireProfile
{
    /** The pooler modes: none (a direct connection), session (a server connection per client), transaction */
    const POOLER_NONE = 'none';
    const POOLER_SESSION = 'session';
    const POOLER_TRANSACTION = 'transaction';

    /** How the driver locks a table (eZDBInterface::lock()): LOCK TABLE, or a row of the lock table, FOR UPDATE */
    const LOCK_TABLE = 'lock_table';
    const LOCK_ROW = 'lock_row';
    const LOCK_NONE = 'none';

    /** The table of the row locks (LOCK_ROW) */
    const LOCK_TABLE_NAME = 'exp_wire_lock';

    /** SQLSTATEs after which a statement outside a transaction is sent again: serialization failure, deadlock */
    const RETRY_SQLSTATES = array( '40001', '40P01' );

    /**
     * SQLSTATEs of a lost connection: admin shutdown (a failover, a Neon compute suspended), crash shutdown, cannot
     * connect now, connection failure / exception / does not exist; after which a statement outside a transaction is
     * sent again on a new connection.
     */
    const RECONNECT_SQLSTATES = array( '57P01', '57P02', '57P03', '08000', '08003', '08006', '08001', '08004' );

    /**
     * Every profile, by name.
     *
     * Keys: label; kernel_name (databaseName() of the driver; postgresql: the kernel treats the database as
     * PostgreSQL); alias (site.ini ImplementationAlias); aliases; port; user; database; live_tested; level (site,
     * datatable, import: the most this database is used for); sslmode (the default of a connection); lock (LOCK_*);
     * advisory_locks (whether pg_advisory_lock() really locks); temp_tables (none, native, session_setting);
     * inline_primary_key (the primary key goes into CREATE TABLE); named_primary_key (CONSTRAINT <name> before it);
     * catalog (pg_catalog, or information_schema where pg_class is only emulated); alter_table_only (ALTER TABLE ONLY
     * is accepted); index_method (USING ... of CREATE INDEX, or '' for none); sequences (sql: CREATE SEQUENCE with START/CACHE and
     * nextval('...'), bit_reversed: Spanner's); sequence_cache (the CACHE of a new sequence); currval (whether the
     * session's currval() gives a new id); returning (INSERT ... RETURNING); pgcrypto (digest() is there by
     * default); md5 (md5() is there); transactional_ddl; for_update (outside an explicit transaction);
     * limit_all (LIMIT ALL); serializable_default; session (statements sent after every connect); retries (single
     * statements sent again after 40001); connect_attempts; connect_wait_ms (doubled per attempt); connect_timeout
     * (seconds); pooler (none, or the mode the host or port says); replica_check (wal_receiver: the replica must be
     * streaming; in_recovery: a replica in recovery is enough); version_pattern (finds the version in version());
     * types (standard type => this database's type, where it differs); notes.
     *
     * @return array
     */
    public static function all()
    {
        $base = array(
            'kernel_name' => 'postgresql',
            'database' => 'exponential',
            'live_tested' => false,
            'level' => 'site',
            'sslmode' => '',
            'lock' => self::LOCK_TABLE,
            'advisory_locks' => true,
            'temp_tables' => 'native',
            'inline_primary_key' => false,
            'named_primary_key' => true,
            'catalog' => 'pg_catalog',
            'alter_table_only' => true,
            'index_method' => 'btree',
            'sequences' => 'sql',
            'sequence_cache' => 1,
            'currval' => true,
            'returning' => true,
            'pgcrypto' => false,
            'md5' => true,
            'transactional_ddl' => true,
            'for_update' => true,
            'limit_all' => true,
            'serializable_default' => false,
            'session' => array(),
            'retries' => 0,
            'connect_attempts' => 1,
            'connect_wait_ms' => 0,
            'connect_timeout' => 0,
            'pooler' => self::POOLER_NONE,
            'replica_check' => 'wal_receiver',
            'version_pattern' => '/^PostgreSQL ([0-9][0-9.]*)/',
            'types' => array(),
            'notes' => array(),
        );
        $profiles = array(
            'postgresql' => array(
                'label' => 'PostgreSQL', 'alias' => 'postgresql', 'aliases' => array(), 'port' => 5432, 'user' => 'postgres',
                'live_tested' => true,
            ),
            'cockroachdb' => array(
                'label' => 'CockroachDB', 'alias' => 'cockroachdb', 'aliases' => array( 'cockroach', 'crdb' ),
                'port' => 26257, 'user' => 'root', 'live_tested' => true,
                'lock' => self::LOCK_ROW, 'advisory_locks' => false, 'temp_tables' => 'session_setting',
                'inline_primary_key' => true, 'transactional_ddl' => false, 'serializable_default' => true,
                // READ COMMITTED (CockroachDB 23.2 and later, on by default from 24.1): far fewer 40001 errors than
                // SERIALIZABLE, and the isolation PostgreSQL itself gives the kernel; temporary tables for the
                // statements that use them (the search, the subtree removal)
                'session' => array( "SET default_transaction_isolation = 'read committed'",
                                    "SET experimental_enable_temp_tables = 'on'" ),
                'retries' => 5, 'connect_attempts' => 3, 'connect_wait_ms' => 200, 'connect_timeout' => 10,
                'version_pattern' => '/^CockroachDB \w+ v([0-9][0-9.]*)/',
                // INTEGER is INT8 on CockroachDB: int4 keeps the kernel's columns 32-bit, as on PostgreSQL, and the
                // schema read back says integer again
                'types' => array( 'integer' => 'int4' ),
                'notes' => array( 'INTEGER means INT8: the schema writes int4, so the columns are 32-bit as on PostgreSQL',
                                  'pg_try_advisory_lock() always answers true without locking: the driver never uses it',
                                  'ALTER TABLE ... ADD PRIMARY KEY replaces the hidden rowid key: the schema writes the key into CREATE TABLE' ),
            ),
            'yugabytedb' => array(
                'label' => 'YugabyteDB', 'alias' => 'yugabytedb', 'aliases' => array( 'yugabyte', 'ysql' ),
                'port' => 5433, 'user' => 'yugabyte', 'live_tested' => true,
                'lock' => self::LOCK_ROW, 'advisory_locks' => false, 'inline_primary_key' => true,
                'index_method' => '', 'transactional_ddl' => false,
                'retries' => 5, 'connect_attempts' => 3, 'connect_wait_ms' => 200, 'connect_timeout' => 10,
                'version_pattern' => '/^PostgreSQL ([0-9][0-9.]*-YB-[0-9][0-9.]*)/',
                'notes' => array( 'USING btree becomes YugabyteDB\'s lsm: the schema writes no access method',
                                  'Sequences cache 100 numbers per node: ids are unique, not in insertion order',
                                  'READ COMMITTED needs the yb_enable_read_committed_isolation flag, else it runs as REPEATABLE READ' ),
            ),
            'spanner' => array(
                'label' => 'Google Cloud Spanner (PostgreSQL dialect)', 'alias' => 'spanner', 'aliases' => array( 'spannerpg', 'pgadapter' ),
                'port' => 5432, 'user' => '', 'live_tested' => true, 'level' => 'datatable',
                'lock' => self::LOCK_NONE, 'advisory_locks' => false, 'temp_tables' => 'none',
                'inline_primary_key' => true, 'named_primary_key' => false, 'alter_table_only' => false, 'index_method' => '',
                'catalog' => 'information_schema',
                'sequences' => 'bit_reversed', 'currval' => false, 'md5' => false, 'transactional_ddl' => false,
                'for_update' => false, 'limit_all' => false, 'serializable_default' => true,
                // PGAdapter: DROP TABLE takes the table's indexes with it, as on PostgreSQL
                'session' => array( 'SET spanner.support_drop_cascade = true' ),
                'retries' => 5, 'connect_attempts' => 3, 'connect_wait_ms' => 500, 'connect_timeout' => 30,
                'replica_check' => 'none',
                'version_pattern' => '/^PostgreSQL ([0-9][0-9.]*)/',
                'types' => array( 'int' => 'bigint', 'integer' => 'bigint', 'smallint' => 'bigint', 'int2' => 'bigint', 'int4' => 'bigint',
                                  'real' => 'double precision', 'float4' => 'double precision',
                                  'timestamp' => 'timestamptz', 'timestamp without time zone' => 'timestamptz',
                                  'char' => 'varchar', 'character' => 'varchar' ),
                'notes' => array( 'Only bit-reversed sequences: new ids are 64-bit numbers, past the kernel\'s 32-bit ids',
                                  'No temporary tables, no LOCK, no advisory locks: the kernel\'s search and tree code cannot run',
                                  'DDL runs outside transactions, one statement at a time (PGAdapter batches it)',
                                  'Every table needs a primary key' ),
            ),
            'neon' => array(
                'label' => 'Neon', 'alias' => 'neon', 'aliases' => array( 'neondb' ), 'port' => 5432, 'user' => '',
                'database' => 'neondb', 'sslmode' => 'require',
                // a suspended compute starts on the first connection; the pooler answers at once and waits for it
                'connect_attempts' => 5, 'connect_wait_ms' => 500, 'connect_timeout' => 15,
                'notes' => array( 'A compute that scaled to zero starts on the first connection: the driver waits and tries again',
                                  'The host with -pooler is PgBouncer in transaction mode',
                                  'Old libpq without SNI needs the endpoint id as options=endpoint=<id> ([PostgreSQLWireSettings] Endpoint)' ),
            ),
            'supabase' => array(
                'label' => 'Supabase', 'alias' => 'supabase', 'aliases' => array(), 'port' => 5432, 'user' => 'postgres',
                'database' => 'postgres', 'sslmode' => 'require', 'pgcrypto' => true,
                'connect_attempts' => 3, 'connect_wait_ms' => 500, 'connect_timeout' => 10,
                'notes' => array( 'Port 6543 of the pooler host is Supavisor in transaction mode, 5432 session mode',
                                  'The pooler takes the user as postgres.<project ref>',
                                  'The direct host db.<project ref>.supabase.co answers on IPv6 only, unless the IPv4 add-on is bought' ),
            ),
            // aurora alone is Aurora MySQL (expMySQLWireDB)
            'aurorapostgresql' => array(
                'label' => 'Amazon Aurora PostgreSQL', 'alias' => 'aurorapostgresql', 'aliases' => array( 'aurora-postgresql' ),
                'port' => 5432, 'user' => 'postgres', 'database' => 'postgres', 'sslmode' => 'require',
                'connect_attempts' => 5, 'connect_wait_ms' => 1000, 'connect_timeout' => 10,
                'replica_check' => 'in_recovery', 'pgcrypto' => false,
                'notes' => array( 'A failover drops every connection: outside a transaction the driver connects again and repeats the statement',
                                  'A reader has no WAL receiver (storage-level replication): pg_is_in_recovery() is what tells a reader',
                                  'IAM authentication: the token is the password, and it expires after 15 minutes' ),
            ),
        );
        foreach ( $profiles as $name => $profile )
            $profiles[$name] = array_merge( $base, $profile, array( 'name' => $name ) );
        return $profiles;
    }

    /**
     * One profile by its name or one of its aliases; null when there is none.
     *
     * @param string $name
     * @return array|null
     */
    public static function get( $name )
    {
        $name = strtolower( trim( (string)$name ) );
        $all = self::all();
        if ( isset( $all[$name] ) )
            return $all[$name];
        foreach ( $all as $profile )
            if ( $profile['alias'] === $name || in_array( $name, $profile['aliases'], true ) )
                return $profile;
        return null;
    }

    /**
     * The profile names, with the aliases of each, for messages and the installer.
     *
     * @return array name => array of aliases (the name first)
     */
    public static function names()
    {
        $names = array();
        foreach ( self::all() as $name => $profile )
            $names[$name] = array_values( array_unique( array_merge( array( $name, $profile['alias'] ), $profile['aliases'] ) ) );
        return $names;
    }

    /**
     * The profile with the settings of an installation applied ([PostgreSQLWireSettings], or the 'postgresql_wire'
     * parameters of eZDB::instance()): a setting that is not empty replaces the profile's default.
     *
     * Settings: SSLMode, SSLRootCert, ConnectTimeout, ConnectAttempts, ConnectWait (ms), SerializationRetries,
     * RetryWait (ms), ReconnectOnLoss (enabled/disabled), Pooler (auto, none, session, transaction), Isolation
     * (default, read committed, repeatable read, serializable), ApplicationName, Endpoint (Neon), Options (libpq
     * options, -c name=value ...), SessionStatements[] (more statements after connecting).
     *
     * @param array $profile
     * @param array $settings
     * @return array the profile, plus 'settings' (the effective values)
     */
    public static function apply( array $profile, array $settings )
    {
        $value = function ( $key, $default ) use ( $settings ) {
            return ( isset( $settings[$key] ) && !is_array( $settings[$key] ) && trim( (string)$settings[$key] ) !== '' ) ? trim( (string)$settings[$key] ) : $default;
        };
        $effective = array(
            'SSLMode' => $value( 'SSLMode', $profile['sslmode'] ),
            'SSLRootCert' => $value( 'SSLRootCert', '' ),
            'ConnectTimeout' => max( 0, (int)$value( 'ConnectTimeout', $profile['connect_timeout'] ) ),
            'ConnectAttempts' => max( 1, (int)$value( 'ConnectAttempts', $profile['connect_attempts'] ) ),
            'ConnectWait' => max( 0, (int)$value( 'ConnectWait', $profile['connect_wait_ms'] ) ),
            'SerializationRetries' => max( 0, (int)$value( 'SerializationRetries', $profile['retries'] ) ),
            'RetryWait' => max( 0, (int)$value( 'RetryWait', 20 ) ),
            'ReconnectOnLoss' => $value( 'ReconnectOnLoss', 'enabled' ) === 'enabled',
            'Pooler' => strtolower( $value( 'Pooler', 'auto' ) ),
            'Isolation' => strtolower( $value( 'Isolation', 'default' ) ),
            'ApplicationName' => $value( 'ApplicationName', 'Exponential' ),
            'Endpoint' => $value( 'Endpoint', '' ),
            'Options' => $value( 'Options', '' ),
            'SessionStatements' => array(),
        );
        if ( isset( $settings['SessionStatements'] ) && is_array( $settings['SessionStatements'] ) )
            $effective['SessionStatements'] = array_values( array_filter( array_map( 'trim', array_map( 'strval', $settings['SessionStatements'] ) ), 'strlen' ) );
        if ( !in_array( $effective['Pooler'], array( 'auto', self::POOLER_NONE, self::POOLER_SESSION, self::POOLER_TRANSACTION ), true ) )
            $effective['Pooler'] = 'auto';
        $profile['settings'] = $effective;
        return $profile;
    }

    /**
     * The pooler a connection goes through: the setting when it names one, else what the host and port say (Neon's
     * "-pooler" host, Supabase's pooler on 6543 or its pooler.supabase.com host, an Aurora RDS Proxy host).
     *
     * @param array $profile an applied profile (apply())
     * @param string $host
     * @param int|string $port
     * @return string POOLER_*
     */
    public static function pooler( array $profile, $host, $port )
    {
        $setting = isset( $profile['settings']['Pooler'] ) ? $profile['settings']['Pooler'] : 'auto';
        if ( $setting !== 'auto' )
            return $setting;
        $host = strtolower( (string)$host );
        switch ( $profile['name'] )
        {
            case 'neon':
                return strpos( $host, '-pooler.' ) !== false ? self::POOLER_TRANSACTION : self::POOLER_NONE;
            case 'supabase':
                if ( (int)$port === 6543 )
                    return self::POOLER_TRANSACTION;
                return strpos( $host, 'pooler.supabase.com' ) !== false ? self::POOLER_SESSION : self::POOLER_NONE;
            case 'aurorapostgresql':
                // RDS Proxy pins a client to one server connection as soon as it sets session state: session mode
                return strpos( $host, '.proxy-' ) !== false ? self::POOLER_SESSION : self::POOLER_NONE;
        }
        return $profile['pooler'];
    }

    /**
     * The libpq connection string of a profile: host, port, dbname, user, password, and sslmode, sslrootcert,
     * connect_timeout, application_name, options as the profile and the settings want them. Values are quoted for
     * libpq (a quote and a backslash escaped). Options are left out behind a pooler in transaction mode, which
     * refuses startup parameters it does not know.
     *
     * @param array $profile an applied profile (apply())
     * @param string|false|null $server
     * @param int|string|false|null $port
     * @param string|false|null $db
     * @param string|false|null $user
     * @param string|false|null $password
     * @return string
     */
    public static function connectString( array $profile, $server, $port, $db, $user, $password )
    {
        $s = isset( $profile['settings'] ) ? $profile['settings'] : array();
        $quote = function ( $value ) {
            return "'" . str_replace( array( '\\', "'" ), array( '\\\\', "\\'" ), (string)$value ) . "'";
        };
        $parts = array();
        foreach ( array( 'host' => $server, 'port' => $port, 'dbname' => $db, 'user' => $user, 'password' => $password ) as $key => $v )
            if ( $v !== false && $v !== null && $v !== '' )
                $parts[] = $key . '=' . $quote( $v );
        if ( !empty( $s['SSLMode'] ) )
            $parts[] = 'sslmode=' . $quote( $s['SSLMode'] );
        if ( !empty( $s['SSLRootCert'] ) )
            $parts[] = 'sslrootcert=' . $quote( $s['SSLRootCert'] );
        if ( !empty( $s['ConnectTimeout'] ) )
            $parts[] = 'connect_timeout=' . $quote( (int)$s['ConnectTimeout'] );
        if ( !empty( $s['ApplicationName'] ) )
            $parts[] = 'application_name=' . $quote( substr( (string)$s['ApplicationName'], 0, 63 ) );
        $options = array();
        if ( !empty( $s['Endpoint'] ) && $profile['name'] === 'neon' )
            $options[] = 'endpoint=' . $s['Endpoint'];
        if ( !empty( $s['Options'] ) )
            $options[] = $s['Options'];
        if ( $options && self::pooler( $profile, $server, $port ) !== self::POOLER_TRANSACTION )
            $parts[] = 'options=' . $quote( implode( ' ', $options ) );
        else if ( $options && !empty( $s['Endpoint'] ) && $profile['name'] === 'neon' )
            // Neon's pooler reads the endpoint from options too: it is not a startup parameter of PgBouncer
            $parts[] = 'options=' . $quote( 'endpoint=' . $s['Endpoint'] );
        return implode( ' ', $parts );
    }

    /**
     * The statements sent after every connect: the isolation the settings ask for (or the profile's), the profile's
     * own, and the SessionStatements of the settings. None behind a pooler in transaction mode: the next statement
     * may run on another server connection, which never saw them.
     *
     * @param array $profile an applied profile (apply())
     * @param string $pooler POOLER_*
     * @return string[]
     */
    public static function sessionStatements( array $profile, $pooler )
    {
        if ( $pooler === self::POOLER_TRANSACTION )
            return array();
        $s = isset( $profile['settings'] ) ? $profile['settings'] : array();
        $statements = array();
        $isolation = isset( $s['Isolation'] ) ? $s['Isolation'] : 'default';
        foreach ( $profile['session'] as $statement )
        {
            if ( $isolation !== 'default' && stripos( $statement, 'default_transaction_isolation' ) !== false )
                continue;
            $statements[] = $statement;
        }
        if ( in_array( $isolation, array( 'read committed', 'repeatable read', 'serializable' ), true ) )
            array_unshift( $statements, "SET default_transaction_isolation = '$isolation'" );
        if ( !empty( $s['SessionStatements'] ) )
            $statements = array_merge( $statements, $s['SessionStatements'] );
        return $statements;
    }

    /**
     * The SQLSTATE of a libpq message (verbose or not): the five characters after the severity, or ''.
     *
     * @param string $message
     * @return string
     */
    public static function sqlState( $message )
    {
        if ( preg_match( '/^[A-Z]+:  ([0-9A-Z]{5}): /', (string)$message, $m ) )
            return $m[1];
        if ( preg_match( '/\bSQLSTATE[ =:]+([0-9A-Z]{5})\b/', (string)$message, $m ) )
            return $m[1];
        return '';
    }

    /**
     * Whether a statement that failed with $sqlState may be sent again as it is: a serialization failure or a
     * deadlock (CockroachDB's and YugabyteDB's restart errors are 40001), when the profile retries.
     *
     * @param array $profile
     * @param string $sqlState
     * @return bool
     */
    public static function isRetryable( array $profile, $sqlState )
    {
        $retries = isset( $profile['settings']['SerializationRetries'] ) ? $profile['settings']['SerializationRetries'] : $profile['retries'];
        return $retries > 0 && in_array( (string)$sqlState, self::RETRY_SQLSTATES, true );
    }

    /**
     * Whether $sqlState (or a message without one, such as "server closed the connection unexpectedly") says the
     * connection is gone.
     *
     * @param string $sqlState
     * @param string $message
     * @return bool
     */
    public static function isConnectionLost( $sqlState, $message = '' )
    {
        if ( in_array( (string)$sqlState, self::RECONNECT_SQLSTATES, true ) )
            return true;
        return (bool)preg_match( '/server closed the connection unexpectedly|no connection to the server|connection (?:to server )?.*(?:lost|reset|refused)|SSL SYSCALL error|could not receive data from server|terminating connection due to administrator command/i', (string)$message );
    }

    /**
     * The server version from SELECT version(): array( 'string', 'values' ), as eZDBInterface::databaseServerVersion()
     * answers; false when the text has no version of this profile.
     *
     * @param array $profile
     * @param string $versionText
     * @return array|false
     */
    public static function parseVersion( array $profile, $versionText )
    {
        if ( !preg_match( $profile['version_pattern'], (string)$versionText, $m ) )
        {
            if ( !preg_match( '/([0-9]+\.[0-9]+(?:\.[0-9]+)?)/', (string)$versionText, $m ) )
                return false;
        }
        $string = $m[1];
        // YugabyteDB: 15.12-YB-2.25.2.0 is PostgreSQL 15.12 and YugabyteDB 2.25.2.0; the PostgreSQL part is the version
        $numbers = preg_replace( '/-.*$/', '', $string );
        return array( 'string' => $string, 'values' => explode( '.', rtrim( $numbers, '.' ) ) );
    }

    /**
     * The profile that a version() text belongs to: cockroachdb, yugabytedb, spanner (PGAdapter answers
     * "PostgreSQL 14.1" and nothing else, so it is never guessed), or postgresql.
     *
     * @param string $versionText
     * @return string
     */
    public static function detect( $versionText )
    {
        $versionText = (string)$versionText;
        if ( stripos( $versionText, 'CockroachDB' ) === 0 )
            return 'cockroachdb';
        if ( preg_match( '/-YB-[0-9]/', $versionText ) )
            return 'yugabytedb';
        return 'postgresql';
    }

    /**
     * Whether eZDBInterface::lock() can lock tables here, and how (LOCK_*).
     *
     * @param array $profile
     * @return string
     */
    public static function lockMethod( array $profile )
    {
        return $profile['lock'];
    }

    /**
     * The statement that creates the table of the row locks (LOCK_ROW), once per database.
     *
     * @return string
     */
    public static function lockTableDDL()
    {
        return 'CREATE TABLE IF NOT EXISTS ' . self::LOCK_TABLE_NAME . ' ( name VARCHAR(255) NOT NULL PRIMARY KEY )';
    }

    /**
     * The statements that lock one table's row in the lock table until the end of the transaction: the row is made
     * when it is missing, then read FOR UPDATE, so a second locker of the same table waits for the first.
     *
     * @param string $table
     * @param callable $literal function( string ): string
     * @return string[]
     */
    public static function rowLockStatements( $table, callable $literal )
    {
        $name = $literal( strtolower( (string)$table ) );
        return array( 'INSERT INTO ' . self::LOCK_TABLE_NAME . " ( name ) VALUES ( $name ) ON CONFLICT ( name ) DO NOTHING",
                      'SELECT name FROM ' . self::LOCK_TABLE_NAME . " WHERE name = $name FOR UPDATE" );
    }

    /**
     * The table an INSERT writes to, as it is written (unquoted, lower case); '' when the statement is no INSERT.
     *
     * @param string $sql
     * @return string
     */
    public static function insertTable( $sql )
    {
        if ( preg_match( '/^\s*INSERT\s+INTO\s+"?([A-Za-z_][A-Za-z0-9_]*)"?/i', (string)$sql, $m ) )
            return strtolower( $m[1] );
        return '';
    }

    /**
     * A type of the PostgreSQL schema as this database takes it (Spanner has no smallint, real or timestamp without
     * time zone); the type itself when nothing changes.
     *
     * @param array $profile
     * @param string $type
     * @return string
     */
    public static function mapType( array $profile, $type )
    {
        $key = strtolower( trim( (string)$type ) );
        return isset( $profile['types'][$key] ) ? $profile['types'][$key] : $type;
    }
}
