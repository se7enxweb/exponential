<?php
/**
 * File containing the expDataConnections class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The named database connections of the installation (doc/eng-us/features/6.0/data-tables.md, "Connections"):
 *
 *   site          the installation's own database (always there)
 *   <identifier>  a connection made in the administration (a row of exp_data_connection), or one an extension
 *                 declares in datatable.ini [Connection_<identifier>] (read-only in the administration)
 *
 * Engines: SQLite (a file in one of [ConnectionSettings] SQLiteDirectories), MySQL/MariaDB, PostgreSQL, Oracle (the
 * ezoracle handler when active, else PDO oci), Microsoft SQL Server (PDO sqlsrv, dblib or odbc), MongoDB; a server may
 * be remote. The password is the name of a secret of the kernel's secret store (expSecrets).
 *
 *   $db = expDataConnections::db( 'archive' );          // an eZDBInterface of its own, one per connection and request
 *   $result = expDataConnections::test( 'archive' );    // ok, engine, version, tables, ms, error (secrets masked)
 *   $db = expDataConnections::dbFor( MyRow::definition() ); // the connection a persistent class declares
 *
 * Every connection is its own eZDB instance (eZDB::instance( ..., true ) for the engines the kernel has a driver for,
 * expPDODB for the others), opened on first use in a request and closed when the next request begins: a Velocity
 * worker never keeps a socket from one request to the next, nor one opened by its parent before the fork.
 *
 * A persistent class names its connection in its definition() ('connection' => 'archive'), or the settings do
 * (datatable.ini [ClassSettings] Connection[<class>]=archive); eZPersistentObject then reads and writes it there.
 *
 * @since 6.0.15
 * @package kernel
 */
class expDataConnections
{
    /** The installation's own database */
    const SITE = 'site';

    /** An identifier: lower case letters, digits and _, starting with a letter */
    const IDENTIFIER_PATTERN = '/^[a-z][a-z0-9_]{0,63}$/';

    /** @var array identifier => eZDBInterface opened in this request */
    protected static $open = array();

    /** @var string the request the open connections belong to */
    protected static $requestKey = '';

    /** @var array|null class name => identifier, from the settings */
    protected static $classMap = null;

    /** @var array|null tests: identifier => descriptor instead of the settings and the table */
    protected static $override = null;

    /**
     * Every engine a connection can use, and what it takes.
     *
     * @return array key => array( label, kind (relational, document), ez (the site.ini alias), pdo (drivers), port, file )
     */
    public static function engineTypes()
    {
        return array(
            'sqlite' => array( 'label' => 'SQLite', 'kind' => 'relational', 'ez' => 'sqlite3', 'pdo' => array( 'sqlite' ), 'port' => 0, 'file' => true ),
            'mysql' => array( 'label' => 'MySQL / MariaDB', 'kind' => 'relational', 'ez' => 'ezmysqli', 'pdo' => array( 'mysql' ), 'port' => 3306, 'file' => false ),
            'postgresql' => array( 'label' => 'PostgreSQL', 'kind' => 'relational', 'ez' => 'ezpostgresql', 'pdo' => array( 'pgsql' ), 'port' => 5432, 'file' => false ),
            'oracle' => array( 'label' => 'Oracle', 'kind' => 'relational', 'ez' => 'ezoracle', 'pdo' => array( 'oci' ), 'port' => 1521, 'file' => false ),
            'sqlserver' => array( 'label' => 'Microsoft SQL Server', 'kind' => 'relational', 'ez' => '', 'pdo' => array( 'sqlsrv', 'dblib', 'odbc' ), 'port' => 1433, 'file' => false ),
            'mongodb' => array( 'label' => 'MongoDB', 'kind' => 'document', 'ez' => 'mongodb', 'pdo' => array(), 'port' => 27017, 'file' => false ),
        );
    }

    /**
     * The engines with what this installation can do about each: available, via (ez, pdo), the driver, why not.
     *
     * @return array key => the entry of engineTypes() plus enabled, available, via, handler, pdo_driver, why
     */
    public static function engines()
    {
        $aliases = array();
        $enabled = array_keys( self::engineTypes() );
        try
        {
            $aliases = (array)eZINI::instance( 'site.ini' )->variable( 'DatabaseSettings', 'ImplementationAlias' );
            $ini = eZINI::instance( 'datatable.ini' );
            if ( $ini->hasVariable( 'ConnectionSettings', 'Engines' ) )
                $enabled = array_values( array_filter( array_map( 'strval', (array)$ini->variable( 'ConnectionSettings', 'Engines' ) ), 'strlen' ) );
        }
        catch ( Throwable $e )
        {
        }
        $pdo = class_exists( 'PDO' ) ? PDO::getAvailableDrivers() : array();
        $engines = array();
        foreach ( self::engineTypes() as $key => $type )
        {
            $handler = $type['ez'] !== '' && isset( $aliases[$type['ez']] ) ? (string)$aliases[$type['ez']] : '';
            $viaEZ = $handler !== '' && class_exists( $handler ) && self::extensionLoaded( $key );
            $pdoDriver = '';
            foreach ( $type['pdo'] as $driver )
            {
                if ( in_array( $driver, $pdo, true ) )
                {
                    $pdoDriver = $driver;
                    break;
                }
            }
            $why = '';
            if ( !in_array( $key, $enabled, true ) )
                $why = 'switched off in datatable.ini [ConnectionSettings] Engines[]';
            elseif ( !$viaEZ && $pdoDriver === '' )
                $why = $type['pdo'] ? 'no driver on this server (' . implode( ', ', array_merge( $type['ez'] !== '' ? array( $type['ez'] . ' handler' ) : array(), array_map( function ( $d ) { return 'pdo_' . $d; }, $type['pdo'] ) ) ) . ')'
                                    : 'no driver on this server (' . $type['ez'] . ')';
            $engines[$key] = $type + array(
                'enabled' => in_array( $key, $enabled, true ),
                'available' => $why === '',
                'via' => $viaEZ ? 'ez' : ( $pdoDriver !== '' ? 'pdo' : '' ),
                'handler' => $viaEZ ? $handler : ( $pdoDriver !== '' ? 'expPDODB' : '' ),
                'pdo_driver' => $pdoDriver,
                'why' => $why,
                'untested' => $key === 'sqlserver',
            );
        }
        return $engines;
    }

    /**
     * Every connection, identifier => descriptor: the site's own database first, then the declared ones, then the
     * ones made in the administration (a row cannot take a declared identifier).
     *
     * @return array
     */
    public static function all()
    {
        if ( self::$override !== null )
            return array( self::SITE => self::siteDescriptor() ) + self::$override;
        $all = array( self::SITE => self::siteDescriptor() );
        foreach ( self::declared() as $id => $connection )
            $all[$id] = $connection;
        if ( self::installed() )
        {
            foreach ( expDataConnection::fetchList() as $row )
            {
                $connection = $row->toDescriptor();
                if ( !isset( $all[$connection['identifier']] ) )
                    $all[$connection['identifier']] = $connection;
            }
        }
        return $all;
    }

    /**
     * One connection, or null.
     *
     * @param string $identifier
     * @return array|null
     */
    public static function get( $identifier )
    {
        $identifier = (string)$identifier;
        if ( $identifier === '' || $identifier === self::SITE )
            return self::siteDescriptor();
        $all = self::all();
        return isset( $all[$identifier] ) ? $all[$identifier] : null;
    }

    /**
     * The connections declared in datatable.ini [Connection_<identifier>].
     *
     * @param eZINI|null $ini
     * @return array identifier => descriptor
     */
    public static function declared( $ini = null )
    {
        $declared = array();
        try
        {
            $ini = $ini instanceof eZINI ? $ini : eZINI::instance( 'datatable.ini' );
            foreach ( $ini->groups() as $group => $values )
            {
                if ( strncmp( $group, 'Connection_', 11 ) !== 0 || !is_array( $values ) )
                    continue;
                $id = substr( $group, 11 );
                if ( !preg_match( self::IDENTIFIER_PATTERN, $id ) || $id === self::SITE )
                    continue;
                $get = function ( $name, $default = '' ) use ( $values ) { return isset( $values[$name] ) ? $values[$name] : $default; };
                $options = array();
                foreach ( array( 'Charset' => 'charset', 'Socket' => 'socket', 'DSN' => 'dsn', 'Dialect' => 'dialect', 'Driver' => 'driver' ) as $key => $option )
                    if ( isset( $values[$key] ) && trim( (string)$values[$key] ) !== '' )
                        $options[$option] = trim( (string)$values[$key] );
                $declared[$id] = self::normalize( array(
                    'identifier' => $id, 'name' => $get( 'Name', $id ), 'engine' => $get( 'Engine' ), 'server' => $get( 'Server' ),
                    'port' => $get( 'Port', 0 ), 'database' => $get( 'Database' ), 'user' => $get( 'User' ), 'secret' => $get( 'Secret' ),
                    'options' => $options, 'description' => $get( 'Description' ),
                    'read_only' => in_array( strtolower( trim( (string)$get( 'ReadOnly', 'false' ) ) ), array( 'true', 'enabled', '1', 'yes' ), true ),
                    'origin' => 'settings' ) );
            }
        }
        catch ( Throwable $e )
        {
        }
        return $declared;
    }

    /**
     * A connection with every key, typed and trimmed (pure).
     *
     * @param array $c
     * @return array identifier, name, engine, server, port, database, user, secret, options, description, read_only,
     *               origin (site, settings, admin), id
     */
    public static function normalize( array $c )
    {
        $text = function ( $key, $max = 255 ) use ( $c ) {
            return isset( $c[$key] ) && is_scalar( $c[$key] ) ? mb_substr( trim( (string)$c[$key] ), 0, $max ) : '';
        };
        $options = isset( $c['options'] ) && is_array( $c['options'] ) ? $c['options'] : array();
        $clean = array();
        foreach ( array( 'charset', 'socket', 'dsn', 'dialect', 'driver' ) as $key )
            if ( isset( $options[$key] ) && is_scalar( $options[$key] ) && trim( (string)$options[$key] ) !== '' )
                $clean[$key] = mb_substr( trim( (string)$options[$key] ), 0, 1000 );
        $engine = strtolower( $text( 'engine', 32 ) );
        if ( $engine === 'mariadb' || $engine === 'mysqli' )
            $engine = 'mysql';
        if ( $engine === 'pgsql' )
            $engine = 'postgresql';
        if ( $engine === 'mongo' )
            $engine = 'mongodb';
        if ( $engine === 'mssql' )
            $engine = 'sqlserver';
        return array(
            'identifier' => strtolower( $text( 'identifier', 64 ) ),
            'name' => $text( 'name' ),
            'engine' => $engine,
            'server' => $text( 'server' ),
            'port' => isset( $c['port'] ) && is_numeric( $c['port'] ) ? max( 0, min( 65535, (int)$c['port'] ) ) : 0,
            'database' => $text( 'database' ),
            'user' => $text( 'user' ),
            'secret' => $text( 'secret', 128 ),
            'options' => $clean,
            'description' => $text( 'description' ),
            'read_only' => !empty( $c['read_only'] ),
            'origin' => isset( $c['origin'] ) && in_array( $c['origin'], array( 'site', 'settings', 'admin' ), true ) ? $c['origin'] : 'admin',
            'id' => isset( $c['id'] ) ? (int)$c['id'] : 0,
            'modified' => isset( $c['modified'] ) ? (int)$c['modified'] : 0,
        );
    }

    /**
     * What is wrong with a connection, as messages (empty when it can be saved).
     *
     * @param array $c a descriptor (normalize())
     * @param bool $new a connection that does not exist yet (its identifier must be free)
     * @return string[]
     */
    public static function problems( array $c, $new = true )
    {
        $c = self::normalize( $c );
        $problems = array();
        if ( !preg_match( self::IDENTIFIER_PATTERN, $c['identifier'] ) )
            $problems[] = 'The identifier must start with a letter and hold only lower case letters, digits and _ (at most 64).';
        elseif ( $c['identifier'] === self::SITE )
            $problems[] = 'The identifier "site" is the installation\'s own database.';
        elseif ( $new && self::$override === null && self::get( $c['identifier'] ) !== null )
            $problems[] = 'A connection with this identifier exists already.';
        $types = self::engineTypes();
        if ( !isset( $types[$c['engine']] ) )
            $problems[] = 'Choose an engine.';
        elseif ( $types[$c['engine']]['file'] )
        {
            if ( $c['database'] === '' )
                $problems[] = 'Give the database file.';
            elseif ( self::sqlitePath( $c['database'] ) === null )
                $problems[] = 'The database file must end in .db, .sqlite or .sqlite3 and be in one of the directories of datatable.ini [ConnectionSettings] SQLiteDirectories[] (' . implode( ', ', self::sqliteDirectories() ) . ').';
        }
        else
        {
            if ( $c['server'] === '' && empty( $c['options']['dsn'] ) && empty( $c['options']['socket'] ) )
                $problems[] = 'Give the server.';
            elseif ( $c['server'] !== '' && !self::hostAllowed( $c['server'] ) )
                $problems[] = 'The server is not in datatable.ini [ConnectionSettings] AllowedHosts[].';
            if ( $c['server'] !== '' && !preg_match( '/^[A-Za-z0-9.:_\[\]-]{1,253}$/', $c['server'] ) )
                $problems[] = 'The server must be a host name or an address.';
            if ( $c['database'] === '' && $c['engine'] !== 'oracle' )
                $problems[] = 'Give the database.';
        }
        if ( $c['secret'] !== '' && !expSecrets::validName( $c['secret'] ) )
            $problems[] = 'The password must be the name of a secret (Setup > Secrets) or env:VARIABLE.';
        if ( isset( $c['options']['dsn'] ) && preg_match( '/(password|pwd)\s*=/i', $c['options']['dsn'] ) )
            $problems[] = 'The DSN must not hold a password: name a secret instead.';
        return $problems;
    }

    /**
     * Saves a connection made in the administration (a new one, or a change of an existing row).
     *
     * @param array $input a descriptor
     * @param int $id the row to change (0 for a new one)
     * @param string[] $problems
     * @return expDataConnection|null
     */
    public static function save( array $input, $id = 0, &$problems = array() )
    {
        $c = self::normalize( $input + array( 'origin' => 'admin' ) );
        $row = $id ? expDataConnection::fetchObject( expDataConnection::definition(), null, array( 'id' => (int)$id ) ) : null;
        if ( $row instanceof expDataConnection )
            $c['identifier'] = (string)$row->attribute( 'identifier' );
        $problems = self::problems( $c, !$row instanceof expDataConnection );
        if ( isset( self::declared()[$c['identifier']] ) )
            $problems[] = 'This connection is declared in datatable.ini and cannot be changed here.';
        if ( $problems )
            return null;
        $now = time();
        $userID = (int)eZUser::currentUserID();
        if ( !$row instanceof expDataConnection )
            $row = new expDataConnection( array( 'identifier' => $c['identifier'], 'created' => $now, 'creator_id' => $userID ) );
        foreach ( array( 'name' => 'name', 'engine' => 'engine', 'server' => 'server', 'port' => 'port', 'database_name' => 'database',
                         'user_name' => 'user', 'secret' => 'secret', 'description' => 'description' ) as $column => $key )
            $row->setAttribute( $column, $c[$key] );
        $row->setAttribute( 'name', $c['name'] !== '' ? $c['name'] : $c['identifier'] );
        $row->setAttribute( 'options', json_encode( $c['options'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
        $row->setAttribute( 'read_only', $c['read_only'] ? 1 : 0 );
        $row->setAttribute( 'modified', $now );
        $row->setAttribute( 'modifier_id', $userID );
        $row->store();
        self::forget( $c['identifier'] );
        return $row;
    }

    /**
     * Removes a connection made in the administration.
     *
     * @param string $identifier
     * @return bool
     */
    public static function remove( $identifier )
    {
        if ( !self::installed() )
            return false;
        $row = expDataConnection::fetchByIdentifier( (string)$identifier );
        if ( !$row instanceof expDataConnection )
            return false;
        $row->remove();
        self::forget( (string)$identifier );
        return true;
    }

    /**
     * The database of a connection, opened once per request.
     *
     * @param string $identifier '' or site for the installation's own database
     * @return eZDBInterface
     * @throws RuntimeException when there is no such connection or it cannot be opened (secrets masked)
     */
    public static function db( $identifier )
    {
        $identifier = (string)$identifier;
        if ( $identifier === '' || $identifier === self::SITE )
            return eZDB::instance();
        self::checkRequest();
        if ( isset( self::$open[$identifier] ) )
            return self::$open[$identifier];
        $connection = self::get( $identifier );
        if ( $connection === null )
            throw new RuntimeException( "There is no data table connection '$identifier'." );
        return self::$open[$identifier] = self::open( $connection );
    }

    /**
     * The connection a persistent class's definition names ('' for the site's database).
     *
     * @param array $definition
     * @return string
     */
    public static function connectionOf( array $definition )
    {
        if ( isset( $definition['connection'] ) && is_string( $definition['connection'] ) && $definition['connection'] !== '' )
            return $definition['connection'] === self::SITE ? '' : $definition['connection'];
        if ( !isset( $definition['class_name'] ) )
            return '';
        $map = self::classMap();
        $class = (string)$definition['class_name'];
        return isset( $map[$class] ) ? $map[$class] : '';
    }

    /**
     * Whether the settings give a class a connection ([ClassSettings] Connection[<class>]).
     *
     * @param string $class
     * @return bool
     */
    public static function classHasConnection( $class )
    {
        $map = self::$classMap !== null ? self::$classMap : self::classMap();
        return isset( $map[$class] ) && $map[$class] !== '';
    }

    /**
     * The database a persistent class reads and writes: its connection, or the site's.
     *
     * @param array $definition
     * @return eZDBInterface
     */
    public static function dbFor( array $definition )
    {
        $identifier = self::connectionOf( $definition );
        return $identifier === '' ? eZDB::instance() : self::db( $identifier );
    }

    /**
     * Opens a connection (a new instance each time; db() keeps one per request).
     *
     * @param array $connection a descriptor
     * @param string|null $password the password itself (a test before saving), instead of the secret
     * @return eZDBInterface
     * @throws RuntimeException
     */
    public static function open( array $connection, $password = null )
    {
        $c = self::normalize( $connection );
        $plan = self::plan( $c, $password );
        $masked = function ( $text ) use ( $c, $password ) {
            return expSecrets::mask( (string)$text, $c['secret'] !== '' ? array( $c['secret'] ) : array(), $password !== null ? array( $password ) : array() );
        };
        if ( $plan['error'] !== '' )
            throw new RuntimeException( $masked( $plan['error'] ) );
        $timeout = self::connectTimeout();
        $socketTimeout = ini_get( 'default_socket_timeout' );
        @ini_set( 'default_socket_timeout', (string)$timeout );
        try
        {
            if ( $plan['via'] === 'pdo' )
                $db = new expPDODB( $plan['parameters'] );
            else
                $db = eZDB::instance( $plan['implementation'], $plan['parameters'], true );
        }
        catch ( Throwable $e )
        {
            @ini_set( 'default_socket_timeout', (string)$socketTimeout );
            throw new RuntimeException( $masked( $c['identifier'] . ': ' . $e->getMessage() ) );
        }
        @ini_set( 'default_socket_timeout', (string)$socketTimeout );
        if ( !$db instanceof eZDBInterface || !$db->isConnected() )
        {
            $error = $db instanceof eZDBInterface ? (string)$db->errorMessage() : '';
            throw new RuntimeException( $masked( $c['identifier'] . ': the connection could not be opened' . ( $error !== '' ? ': ' . $error : '' ) ) );
        }
        return $db;
    }

    /**
     * How a connection is opened (pure apart from the secret): the driver, the eZDB parameters.
     *
     * @param array $c a descriptor
     * @param string|null $password
     * @return array via (ez, pdo), implementation, parameters, error
     */
    public static function plan( array $c, $password = null )
    {
        $c = self::normalize( $c );
        $engines = self::engines();
        $plan = array( 'via' => '', 'implementation' => '', 'parameters' => array(), 'error' => '' );
        if ( !isset( $engines[$c['engine']] ) )
        {
            $plan['error'] = "unknown engine '{$c['engine']}'";
            return $plan;
        }
        $engine = $engines[$c['engine']];
        if ( !$engine['available'] )
        {
            $plan['error'] = $engine['label'] . ': ' . $engine['why'];
            return $plan;
        }
        if ( $password === null && $c['secret'] !== '' )
        {
            $password = expSecrets::get( $c['secret'] );
            if ( $password === null )
            {
                $plan['error'] = "the secret '{$c['secret']}' is not set (Setup > Secrets)";
                return $plan;
            }
        }
        $password = (string)$password;
        $port = $c['port'] ?: $engine['port'];
        $database = $c['database'];
        if ( $engine['file'] )
        {
            $database = self::sqlitePath( $c['database'] );
            if ( $database === null )
            {
                $plan['error'] = 'the database file is not in an allowed directory';
                return $plan;
            }
        }
        $parameters = array( 'use_defaults' => false, 'server' => $c['server'], 'port' => $port ?: false, 'user' => $c['user'],
                             'password' => $password, 'database' => $database, 'show_errors' => false, 'connect_retries' => 0,
                             'use_persistent_connection' => false, 'use_slave_server' => false,
                             'charset' => isset( $c['options']['charset'] ) ? $c['options']['charset'] : 'utf-8' );
        if ( isset( $c['options']['socket'] ) )
            $parameters['socket'] = $c['options']['socket'];
        if ( $engine['via'] === 'ez' )
        {
            $plan['via'] = 'ez';
            $plan['implementation'] = $engine['ez'];
            if ( $c['engine'] === 'mongodb' )
                $parameters['charset'] = 'utf-8';
        }
        else
        {
            $driver = isset( $c['options']['driver'] ) && in_array( $c['options']['driver'], $engine['pdo'], true ) && in_array( $c['options']['driver'], PDO::getAvailableDrivers(), true )
                    ? $c['options']['driver'] : $engine['pdo_driver'];
            $plan['via'] = 'pdo';
            $parameters['pdo_driver'] = $driver;
            $parameters['pdo_dsn'] = isset( $c['options']['dsn'] ) ? $c['options']['dsn'] : self::dsn( $c['engine'], $driver, $c['server'], $port, $database, $c['options'] );
            $parameters['pdo_dialect'] = isset( $c['options']['dialect'] ) ? $c['options']['dialect'] : ( $c['engine'] === 'sqlserver' ? 'sqlserver' : ( $c['engine'] === 'oracle' ? 'oracle' : '' ) );
            $parameters['pdo_options'] = array( PDO::ATTR_TIMEOUT => self::connectTimeout() );
        }
        $plan['parameters'] = $parameters;
        return $plan;
    }

    /**
     * The PDO data source name of a connection (pure).
     *
     * @param string $engine
     * @param string $driver
     * @param string $server
     * @param int $port
     * @param string $database
     * @param array $options
     * @return string
     */
    public static function dsn( $engine, $driver, $server, $port, $database, array $options = array() )
    {
        $server = (string)$server;
        $port = (int)$port;
        switch ( $driver )
        {
            case 'sqlite':
                return 'sqlite:' . $database;
            case 'mysql':
                return 'mysql:host=' . $server . ( $port ? ';port=' . $port : '' ) . ';dbname=' . $database . ';charset=utf8mb4';
            case 'pgsql':
                return 'pgsql:host=' . $server . ( $port ? ';port=' . $port : '' ) . ';dbname=' . $database;
            case 'oci':
                return 'oci:dbname=//' . $server . ( $port ? ':' . $port : '' ) . '/' . $database . ';charset=AL32UTF8';
            case 'sqlsrv':
                return 'sqlsrv:Server=' . $server . ( $port ? ',' . $port : '' ) . ';Database=' . $database;
            case 'dblib':
                return 'dblib:host=' . $server . ( $port ? ':' . $port : '' ) . ';dbname=' . $database . ';charset=UTF-8';
            case 'odbc':
                $odbcDriver = isset( $options['odbc_driver'] ) ? $options['odbc_driver'] : 'ODBC Driver 18 for SQL Server';
                return 'odbc:Driver={' . $odbcDriver . '};Server=' . $server . ( $port ? ',' . $port : '' ) . ';Database=' . $database;
        }
        return '';
    }

    /**
     * Tries a connection: opens it, asks the server's version and its tables, and closes it.
     *
     * @param string|array $connection an identifier or a descriptor
     * @param string|null $password the password itself, for a test before saving
     * @return array ok, engine, via, version, tables (count or null), ms, error
     */
    public static function test( $connection, $password = null )
    {
        $c = is_array( $connection ) ? self::normalize( $connection ) : self::get( (string)$connection );
        $result = array( 'ok' => false, 'engine' => '', 'via' => '', 'version' => '', 'tables' => null, 'ms' => 0, 'error' => '' );
        if ( $c === null )
        {
            $result['error'] = 'There is no such connection.';
            return $result;
        }
        $result['engine'] = $c['engine'];
        $started = microtime( true );
        if ( $c['origin'] === 'site' )
        {
            $db = eZDB::instance();
            $result['via'] = 'ez';
        }
        else
        {
            $plan = self::plan( $c, $password );
            $result['via'] = $plan['via'];
            // a server that does not answer fails within the timeout, not the driver's minute
            if ( $plan['error'] === '' && $c['server'] !== '' && empty( $c['options']['dsn'] ) && !self::reachable( $c['server'], $plan['parameters']['port'] ?: self::engineTypes()[$c['engine']]['port'] ) )
            {
                $result['error'] = 'The server ' . $c['server'] . ' does not answer on port ' . ( $plan['parameters']['port'] ?: self::engineTypes()[$c['engine']]['port'] ) . ' within ' . self::connectTimeout() . ' s.';
                $result['ms'] = (int)round( ( microtime( true ) - $started ) * 1000 );
                return $result;
            }
            try
            {
                $db = self::open( $c, $password );
            }
            catch ( Throwable $e )
            {
                $result['error'] = $e->getMessage();
                $result['ms'] = (int)round( ( microtime( true ) - $started ) * 1000 );
                return $result;
            }
        }
        try
        {
            $result['version'] = self::serverVersion( $db );
            $tables = expKernelTableSchema::tables( $db );
            $result['tables'] = $tables === null ? null : count( $tables );
            // a server that answers but refuses to list its tables (no rights, no authentication) is not usable
            $result['ok'] = $tables !== null;
            if ( $tables === null )
                $result['error'] = 'Connected, but the server does not list its tables to this user (rights or authentication).';
        }
        catch ( Throwable $e )
        {
            $result['error'] = expSecrets::mask( $e->getMessage(), $c['secret'] !== '' ? array( $c['secret'] ) : array(), $password !== null ? array( $password ) : array() );
        }
        if ( $c['origin'] !== 'site' )
        {
            try
            {
                $db->close();
            }
            catch ( Throwable $e )
            {
            }
        }
        $result['ms'] = (int)round( ( microtime( true ) - $started ) * 1000 );
        return $result;
    }

    /**
     * The absolute path of a SQLite connection's file, or null when it is not allowed.
     *
     * @param string $file relative to the installation, or absolute
     * @return string|null
     */
    public static function sqlitePath( $file )
    {
        $file = trim( (string)$file );
        if ( $file === '' || strpos( $file, "\0" ) !== false || !preg_match( '/\.(db|sqlite|sqlite3)$/i', $file ) )
            return null;
        $root = rtrim( eZSys::rootDir(), '/' );
        $path = $file[0] === '/' ? $file : $root . '/' . $file;
        $allowedDirs = array();
        foreach ( self::sqliteDirectories() as $allowed )
        {
            $allowedReal = self::realDirectory( $allowed[0] === '/' ? $allowed : $root . '/' . $allowed, true );
            if ( $allowedReal !== null )
                $allowedDirs[] = $allowedReal;
        }
        $dir = self::realDirectory( dirname( $path ) );
        if ( $dir === null )
            return null;
        foreach ( $allowedDirs as $allowedReal )
        {
            if ( $dir === $allowedReal || strpos( $dir . '/', $allowedReal . '/' ) === 0 )
                return $dir . '/' . basename( $path );
        }
        return null;
    }

    /** @return string[] the directories a SQLite file may be in, as set */
    public static function sqliteDirectories()
    {
        try
        {
            $ini = eZINI::instance( 'datatable.ini' );
            if ( $ini->hasVariable( 'ConnectionSettings', 'SQLiteDirectories' ) )
                return array_values( array_filter( array_map( function ( $d ) { return rtrim( trim( (string)$d ), '/' ); },
                                                              (array)$ini->variable( 'ConnectionSettings', 'SQLiteDirectories' ) ), 'strlen' ) );
        }
        catch ( Throwable $e )
        {
        }
        return array( 'var/datatable' );
    }

    /**
     * Whether a server may be reached ([ConnectionSettings] AllowedHosts[]; any when the list is empty).
     *
     * @param string $host
     * @return bool
     */
    public static function hostAllowed( $host )
    {
        $allowed = array();
        try
        {
            $ini = eZINI::instance( 'datatable.ini' );
            if ( $ini->hasVariable( 'ConnectionSettings', 'AllowedHosts' ) )
                $allowed = array_values( array_filter( array_map( 'trim', (array)$ini->variable( 'ConnectionSettings', 'AllowedHosts' ) ), 'strlen' ) );
        }
        catch ( Throwable $e )
        {
        }
        if ( !$allowed || in_array( '*', $allowed, true ) )
            return true;
        return in_array( strtolower( (string)$host ), array_map( 'strtolower', $allowed ), true );
    }

    /**
     * Whether the table exp_data_connection exists on the site's database.
     *
     * @param bool $fresh
     * @return bool
     */
    public static function installed( $fresh = false )
    {
        try
        {
            return expKernelTableSchema::exists( expDataConnection::TABLE, null, $fresh ) === true;
        }
        catch ( Throwable $e )
        {
            return false;
        }
    }

    /**
     * Creates the kernel tables of the data tables (exp_data_connection) and of the secret store when missing.
     *
     * @param string[] $messages
     * @return bool
     */
    public static function install( &$messages = array() )
    {
        $ok = expKernelTableSchema::install( array( expDataConnection::TABLE, 'exp_secret' ), null, null, $messages );
        expKernelTableSchema::reset();
        return $ok;
    }

    /**
     * Closes the connections opened in this request (the next request opens its own).
     */
    public static function closeAll()
    {
        foreach ( self::$open as $db )
        {
            try
            {
                $db->close();
            }
            catch ( Throwable $e )
            {
            }
        }
        self::$open = array();
    }

    /**
     * Tests: connections instead of the settings and the table (null for the real ones), and forgets the class map.
     *
     * @param array|null $connections identifier => descriptor
     */
    public static function setConnections( $connections )
    {
        self::closeAll();
        self::$classMap = null;
        if ( $connections === null )
        {
            self::$override = null;
            return;
        }
        self::$override = array();
        foreach ( $connections as $id => $connection )
            self::$override[(string)$id] = self::normalize( array( 'identifier' => $id ) + $connection );
    }

    /**
     * Tests: the class map ([ClassSettings] Connection[]) instead of the settings.
     *
     * @param array|null $map class => identifier
     */
    public static function setClassMap( $map )
    {
        self::$classMap = $map;
    }

    /** @return array the site's own database as a descriptor */
    public static function siteDescriptor()
    {
        $engine = 'mysql';
        $database = '';
        try
        {
            $ini = eZINI::instance( 'site.ini' );
            $impl = strtolower( (string)$ini->variable( 'DatabaseSettings', 'DatabaseImplementation' ) );
            $engine = strpos( $impl, 'sqlite' ) !== false ? 'sqlite' : ( strpos( $impl, 'postgres' ) !== false ? 'postgresql'
                    : ( strpos( $impl, 'oracle' ) !== false ? 'oracle' : ( strpos( $impl, 'mongo' ) !== false ? 'mongodb' : 'mysql' ) ) );
            $database = (string)$ini->variable( 'DatabaseSettings', 'Database' );
        }
        catch ( Throwable $e )
        {
        }
        return array( 'identifier' => self::SITE, 'name' => 'This installation\'s database', 'engine' => $engine, 'server' => '',
                      'port' => 0, 'database' => $database, 'user' => '', 'secret' => '', 'options' => array(), 'description' => '',
                      'read_only' => false, 'origin' => 'site', 'id' => 0, 'modified' => 0 );
    }

    /** @return int seconds */
    public static function connectTimeout()
    {
        try
        {
            $ini = eZINI::instance( 'datatable.ini' );
            if ( $ini->hasVariable( 'ConnectionSettings', 'ConnectTimeout' ) && is_numeric( $ini->variable( 'ConnectionSettings', 'ConnectTimeout' ) ) )
                return max( 1, min( 60, (int)$ini->variable( 'ConnectionSettings', 'ConnectTimeout' ) ) );
        }
        catch ( Throwable $e )
        {
        }
        return 5;
    }

    /**
     * @param eZDBInterface $db
     * @return string
     */
    public static function serverVersion( $db )
    {
        try
        {
            $version = $db->databaseServerVersion();
            if ( is_array( $version ) )
                return (string)( isset( $version['string'] ) ? $version['string'] : implode( '.', array_filter( $version, 'is_scalar' ) ) );
            return (string)$version;
        }
        catch ( Throwable $e )
        {
            return '';
        }
    }

    /**
     * Forgets an open connection (after a change of it).
     *
     * @param string $identifier
     */
    protected static function forget( $identifier )
    {
        if ( isset( self::$open[$identifier] ) )
        {
            try
            {
                self::$open[$identifier]->close();
            }
            catch ( Throwable $e )
            {
            }
            unset( self::$open[$identifier] );
        }
    }

    /** A new request (Velocity keeps the process): the connections of the last one are closed. */
    protected static function checkRequest()
    {
        $key = getmypid() . ':' . ( isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? (string)$_SERVER['REQUEST_TIME_FLOAT'] : '' )
             . ':' . ( isset( $GLOBALS['eZDBGlobalInstance'] ) && is_object( $GLOBALS['eZDBGlobalInstance'] ) ? spl_object_id( $GLOBALS['eZDBGlobalInstance'] ) : 0 );
        if ( $key !== self::$requestKey )
        {
            if ( self::$requestKey !== '' )
                self::closeAll();
            self::$requestKey = $key;
        }
    }

    /** @return array class => identifier */
    protected static function classMap()
    {
        if ( self::$classMap !== null )
            return self::$classMap;
        $map = array();
        try
        {
            $ini = eZINI::instance( 'datatable.ini' );
            if ( $ini->hasVariable( 'ClassSettings', 'Connection' ) )
                foreach ( (array)$ini->variable( 'ClassSettings', 'Connection' ) as $class => $identifier )
                    if ( is_string( $class ) && $class !== '' && is_string( $identifier ) && trim( $identifier ) !== '' )
                        $map[$class] = trim( $identifier ) === self::SITE ? '' : trim( $identifier );
        }
        catch ( Throwable $e )
        {
        }
        return self::$classMap = $map;
    }

    /**
     * @param string $host
     * @param int $port
     * @return bool
     */
    protected static function reachable( $host, $port )
    {
        $host = trim( (string)$host, '[]' );
        if ( $host === 'localhost' || $host === '' || !(int)$port )
            return true;
        $socket = @fsockopen( strpos( $host, ':' ) !== false ? '[' . $host . ']' : $host, (int)$port, $errno, $errstr, self::connectTimeout() );
        if ( !$socket )
            return false;
        fclose( $socket );
        return true;
    }

    /**
     * @param string $engine
     * @return bool whether the PHP extension of the kernel's handler is loaded
     */
    protected static function extensionLoaded( $engine )
    {
        switch ( $engine )
        {
            case 'sqlite':
                return extension_loaded( 'sqlite3' );
            case 'mysql':
                return extension_loaded( 'mysqli' );
            case 'postgresql':
                return extension_loaded( 'pgsql' );
            case 'oracle':
                return extension_loaded( 'oci8' );
            case 'mongodb':
                return extension_loaded( 'mongodb' ) && class_exists( 'MongoDB\\Client' );
        }
        return false;
    }

    /**
     * The real path of a directory, made when $create and it is missing.
     *
     * @param string $dir
     * @param bool $create
     * @return string|null
     */
    protected static function realDirectory( $dir, $create = false )
    {
        if ( !is_dir( $dir ) && $create )
            @mkdir( $dir, 0775, true );
        $real = realpath( $dir );
        return $real === false ? null : rtrim( $real, '/' );
    }
}
?>
