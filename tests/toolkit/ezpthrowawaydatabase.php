<?php
/**
 * File containing the ezpThrowawayDatabase class.
 *
 * throwaway MySQL/MariaDB databases for the tests of the group "database". A database is made for the run
 * or for one test class, filled with the kernel schema (kernel/sql/mysql/kernel_schema.sql) and the clean data
 * (kernel/sql/common/cleandata.sql), made the eZDB instance of the test and removed again when it is no longer
 * needed: by the next class, at the end of the run, and on an abort (fatal error, Ctrl-C, SIGTERM).
 *
 * Safety: this class only ever writes to, empties or drops a database whose name is a test name (isTestDatabaseName():
 * "test" as a word of the name) and that it made itself in this process. Every name it makes is
 * exp_test_<pid>_<random>. A database of an installation is never touched: its name is not one of those this class made.
 *
 * Environment:
 *   EXP_TEST_SITEACCESS      the siteaccess whose settings the tests run with (ezpLiveInstallation::siteAccess())
 *   EXP_TEST_DB_HOST         server of the throwaway databases       default: [DatabaseSettings] Server of the siteaccess
 *   EXP_TEST_DB_PORT         port                                    default: [DatabaseSettings] Port
 *   EXP_TEST_DB_SOCKET       socket                                  default: [DatabaseSettings] Socket (*)
 *   EXP_TEST_DB_USER         user with CREATE and DROP for exp_test_% default: [DatabaseSettings] User
 *   EXP_TEST_DB_PASSWORD     its password                            default: [DatabaseSettings] Password (*)
 *   EXP_TEST_KEEP_DATABASES  1: nothing is dropped, every database made is listed in var/log/test-databases.log
 *   (*) only while neither EXP_TEST_DB_USER nor EXP_TEST_DB_HOST is set: the site's password and socket are never
 *       sent to another account or server.
 *
 * Lifetime: one seed database per run gets the schema and the clean data (about 135 tables and 3,800 rows, the slow
 * part); every test class gets a copy of the seed, made on the server table by table (CREATE TABLE ... LIKE,
 * INSERT ... SELECT). A class finds the data as the clean installation has it, whatever the classes before it wrote.
 * The live tests (ezpLiveInstallation) share one copy for the run: each of them removes what it creates. Before a
 * database is dropped, the connections of this process to it are rolled back and closed (an idle connection holding
 * a metadata lock made DROP DATABASE wait forever); DROP waits at most lock_wait_timeout seconds and then fails.
 *
 * The admin connection (CREATE, DROP, loading) is opened for each operation and closed after it, so that a forked
 * child (the content job tests fork) never inherits it: a child that ends closes the connections it inherited,
 * and the server would end the parent's session with them.
 *
 * Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class ezpThrowawayDatabase
{
    /** Every database this class makes starts with this; the "test" in it is what isTestDatabaseName() asks for. */
    const NAME_PREFIX = 'exp_test_';

    /** The file under var/log that lists the databases kept with EXP_TEST_KEEP_DATABASES=1, or left behind. */
    const KEEP_LOG = 'var/log/test-databases.log';

    /** Character set and collation of the throwaway databases: those of the kernel's MySQL tables. */
    const CHARSET = 'utf8mb4';
    const COLLATION = 'utf8mb4_general_ci';

    /** Seconds a DROP DATABASE waits for a metadata lock before it fails. */
    const LOCK_WAIT_TIMEOUT = 30;

    /** @var array|null connection settings of the server, see server() */
    private static $server = null;

    /** @var ezpThrowawayDatabaseConnection|null the admin connection of the running operation */
    private static $admin = null;

    /** @var int nesting depth of withAdmin() */
    private static $adminDepth = 0;

    /** @var string|null|false false: not checked yet, null: databases can be made, string: why not */
    private static $unavailable = false;

    /** @var string|null why the seed could not be made; the classes after the first are skipped with it */
    private static $seedFailure = null;

    /** @var array name => true, the databases this process made and has not dropped yet */
    private static $created = array();

    /** @var array name => eZDBInterface[], the connections this class opened to each database */
    private static $connections = array();

    /** @var string|null the seed database of the run (schema and clean data) */
    private static $seed = null;

    /** @var string|null the database of the run, shared by the live tests */
    private static $runDatabase = null;

    /** @var array the class whose database is current: array( class, name ) */
    private static $classDatabase = array( null, null );

    /** @var int the process that made the databases (0: none yet); a forked child never drops them */
    private static $pid = 0;

    /** @var bool|null pcntl_async_signals() before registerCleanup() changed it; null: not changed */
    private static $previousAsyncSignals = null;

    /** @var array signal => true, the handlers registerCleanup() installed */
    private static $signalHandlers = array();

    /** @var string|null the database activate() pointed the kernel to last; the caches belong to it */
    private static $activeName = null;

    /**
     * Whether $name may be written to, emptied or dropped by a test, see ezpLiveInstallation::isTestDatabaseName().
     *
     * @param mixed $name
     * @return bool
     */
    public static function isTestDatabaseName( $name )
    {
        return ezpLiveInstallation::isTestDatabaseName( $name );
    }

    /**
     * A new, random database name of this process: exp_test_<pid>_<8 hex digits>.
     *
     * @return string
     */
    public static function newName()
    {
        return self::NAME_PREFIX . getmypid() . '_' . bin2hex( random_bytes( 4 ) );
    }

    /**
     * Whether the databases are kept (EXP_TEST_KEEP_DATABASES=1).
     *
     * @return bool
     */
    public static function keepDatabases()
    {
        return in_array( strtolower( trim( (string)getenv( 'EXP_TEST_KEEP_DATABASES' ) ) ), array( '1', 'true', 'yes', 'on' ), true );
    }

    /**
     * The server the throwaway databases are made on: the EXP_TEST_DB_* variables, else the [DatabaseSettings] of
     * the siteaccess the kernel was started with. Never the database of the siteaccess: only server and account.
     *
     * @return array( 'implementation', 'host', 'port', 'socket', 'user', 'password' )
     */
    public static function server()
    {
        if ( self::$server !== null )
            return self::$server;

        ezpLiveInstallation::startKernel();
        $ini = eZINI::instance();
        $setting = function ( $name ) use ( $ini )
        {
            return $ini->hasVariable( 'DatabaseSettings', $name ) ? (string)$ini->variable( 'DatabaseSettings', $name ) : '';
        };
        $env = function ( $name )
        {
            $value = getenv( $name );
            return $value === false ? null : $value;
        };
        // the site's password and socket belong to the site's account on the site's server
        $siteAccount = $env( 'EXP_TEST_DB_USER' ) === null && $env( 'EXP_TEST_DB_HOST' ) === null;
        $socket = $env( 'EXP_TEST_DB_SOCKET' ) ?? ( $siteAccount ? trim( $setting( 'Socket' ) ) : '' );
        self::$server = array(
            'implementation' => trim( $setting( 'DatabaseImplementation' ) ),
            'host' => trim( $env( 'EXP_TEST_DB_HOST' ) ?? $setting( 'Server' ) ),
            'port' => trim( $env( 'EXP_TEST_DB_PORT' ) ?? $setting( 'Port' ) ),
            'socket' => $socket === 'disabled' ? '' : trim( $socket ),
            'user' => trim( $env( 'EXP_TEST_DB_USER' ) ?? $setting( 'User' ) ),
            'password' => $env( 'EXP_TEST_DB_PASSWORD' ) ?? ( $siteAccount ? $setting( 'Password' ) : '' ),
        );
        return self::$server;
    }

    /**
     * user@host of the server, for messages.
     *
     * @return string
     */
    public static function account()
    {
        try
        {
            $server = self::server();
        }
        catch ( Throwable $e )
        {
            return '(unknown account)';
        }
        $host = $server['host'] !== '' ? $server['host'] : 'localhost';
        return $server['user'] . '@' . $host . ( $server['port'] !== '' ? ':' . $server['port'] : '' );
    }

    /**
     * Null when throwaway databases can be made, else why not: no MySQL/MariaDB, no connection, no CREATE or DROP
     * right. Checked once per process, by making and dropping a database.
     *
     * @return string|null
     */
    public static function unavailableReason()
    {
        if ( self::$unavailable !== false )
            return self::$unavailable;

        self::$unavailable = null;
        try
        {
            $server = self::server();
            if ( !in_array( $server['implementation'], array( 'ezmysqli', 'ezmysql' ), true ) )
                throw new RuntimeException( "throwaway databases are made on MySQL/MariaDB only, the siteaccess '" . ezpLiveInstallation::siteAccess()
                                            . "' uses '" . $server['implementation'] . "'" );
            $probe = self::newName();
            self::create( $probe, false );
            try
            {
                self::drop( $probe, true );
            }
            catch ( Throwable $e )
            {
                // CREATE without DROP would leave a database behind for every class: say so, note it, use none
                fwrite( STDERR, "could not drop the probe database $probe: " . $e->getMessage() . PHP_EOL );
                self::logName( $probe, 'left behind (probe, DROP failed)' );
                unset( self::$created[$probe] );
                throw new RuntimeException( "CREATE works but DROP does not ($probe is left behind): " . $e->getMessage(), 0, $e );
            }
        }
        catch ( Throwable $e )
        {
            self::$unavailable = 'no throwaway database (siteaccess \'' . ezpLiveInstallation::siteAccess() . '\', account ' . self::account() . '): '
                . trim( strtok( $e->getMessage(), "\n" ) )
                . '. Set EXP_TEST_DB_USER/EXP_TEST_DB_PASSWORD to an account with CREATE and DROP on `' . self::NAME_PREFIX . '%`.*';
        }
        return self::$unavailable;
    }

    /**
     * The database of a test class (ezpDatabaseTestCase): made on its first test, a copy of the seed (or an empty
     * database), plus the SQL files of the class; the database of the class before is dropped then, after the new
     * one is the eZDB instance. Made the eZDB instance every time. Skips the test when no throwaway database can be
     * made.
     *
     * @param string $class
     * @param bool $withCleanData false: no schema and no data, only $sqlFiles
     * @param array $sqlFiles as ezpDatabaseTestCase::$sqlFiles
     * @return eZDBInterface
     */
    public static function forClass( $class, $withCleanData = true, array $sqlFiles = array() )
    {
        self::requireOrSkip();
        if ( $withCleanData && self::$seedFailure !== null )
            PHPUnit\Framework\TestCase::markTestSkipped( self::$seedFailure );
        list( $currentClass, $previous ) = self::$classDatabase;
        if ( $currentClass === $class && $previous !== null && isset( self::$created[$previous] ) )
            return self::activate( $previous );

        $name = self::newName();
        if ( $withCleanData )
            self::copySeed( $name );
        else
            self::create( $name );
        self::$classDatabase = array( $class, $name );
        $db = self::activate( $name );
        if ( $previous !== null && isset( self::$created[$previous] ) )
            self::drop( $previous );
        if ( count( $sqlFiles ) > 0 )
            ezpTestDatabaseHelper::insertSqlData( $db, self::normalizeSqlFiles( $sqlFiles ) );
        return $db;
    }

    /**
     * The database of the run for the live tests: a copy of the seed, made once, made the eZDB instance every time.
     * Throws when no throwaway database can be made; ezpLiveInstallation turns that into a skip.
     *
     * @return eZDBInterface
     */
    public static function forRun()
    {
        $reason = self::unavailableReason();
        if ( $reason === null )
            $reason = self::$seedFailure;
        if ( $reason !== null )
            throw new RuntimeException( $reason );
        if ( self::$runDatabase === null || !isset( self::$created[self::$runDatabase] ) )
        {
            $name = self::newName();
            self::copySeed( $name );
            self::$runDatabase = $name;
        }
        return self::activate( self::$runDatabase );
    }

    /**
     * Skips the running test unless throwaway databases can be made.
     */
    public static function requireOrSkip()
    {
        $reason = self::unavailableReason();
        if ( $reason !== null )
            PHPUnit\Framework\TestCase::markTestSkipped( $reason );
    }

    /**
     * The DSN of the current throwaway database (ezpTestRunner::dsn()); without one, the server
     * without a database.
     *
     * @return string
     */
    public static function dsn()
    {
        $server = self::server();
        $name = self::currentName();
        if ( $server['socket'] !== '' )
            $host = 'unix(' . $server['socket'] . ')';
        else
            $host = ( $server['host'] !== '' ? $server['host'] : 'localhost' ) . ( $server['port'] !== '' ? ':' . $server['port'] : '' );
        return 'mysqli://' . rawurlencode( $server['user'] ) . ':' . rawurlencode( $server['password'] ) . '@' . $host . '/' . ( $name !== null ? $name : '' );
    }

    /**
     * The throwaway database the eZDB instance was last pointed to, null when none.
     *
     * @return string|null
     */
    public static function currentName()
    {
        $db = eZDB::hasInstance() ? eZDB::instance() : null;
        $name = $db instanceof eZDBInterface ? $db->DB : null;
        return ( $name !== null && isset( self::$created[$name] ) ) ? $name : null;
    }

    /**
     * Points the kernel to the database $name: [DatabaseSettings] and [FileSettings] VarDir of the loaded site.ini
     * (so that a new eZDB instance connects there too) and a new eZDB instance.
     *
     * @param string $name a database this process made
     * @return eZDBInterface
     */
    private static function activate( $name )
    {
        self::assertOwn( $name );
        $server = self::server();
        $settings = array(
            'DatabaseImplementation' => 'ezmysqli',
            'Server' => $server['host'],
            'Port' => $server['port'],
            'Socket' => $server['socket'] !== '' ? $server['socket'] : 'disabled',
            'User' => $server['user'],
            'Password' => $server['password'],
            'Database' => $name,
            'UseSlaveServer' => 'disabled',
        );
        // the caches, logs and storage of the run go to a directory of its own: the site's VarDir holds caches made
        // from the site's database (class identifiers, languages, ...), which a test must neither read nor overwrite
        $files = array( 'VarDir' => self::varDir(), 'CacheVarDir' => '', 'LogVarDir' => '' );
        // set in the loaded site.ini, not injected: injected settings would override what a test sets on purpose
        // (its own DatabaseSettings, CacheVarDir, ...). A test that reloads the settings gets these back on the next
        // setUp() of a database test or requireOrSkip() of a live test; the eZDB instance stays this database.
        $ini = eZINI::instance();
        foreach ( $settings as $key => $value )
            $ini->setVariable( 'DatabaseSettings', $key, $value );
        foreach ( $files as $key => $value )
            $ini->setVariable( 'FileSettings', $key, $value );

        $current = eZDB::hasInstance() ? eZDB::instance() : null;
        if ( $current instanceof eZDBInterface && $current->DB === $name && $current->isConnected() && self::isAlive( $current ) )
            return $current;

        $db = eZDB::instance( 'ezmysqli', array( 'use_defaults' => false, 'server' => $server['host'], 'port' => $server['port'] !== '' ? (int)$server['port'] : false,
                                                 'socket' => $server['socket'] !== '' ? $server['socket'] : false, 'user' => $server['user'],
                                                 'password' => $server['password'], 'database' => $name, 'show_errors' => false ), true );
        if ( !$db->isConnected() )
            throw new RuntimeException( "cannot connect to the throwaway database $name as " . self::account() . ': ' . $db->errorMessage() );
        self::$connections[$name][] = $db;
        eZDB::setInstance( $db );
        if ( self::$activeName !== $name )
        {
            self::$activeName = $name;
            self::resetCaches();
        }
        return $db;
    }

    /**
     * Whether the session of $db still answers. A forked child that ends closes the connections it inherited, and
     * the server ends the parent's session with them (expAuditForkTest): the parent's eZDB still says it is
     * connected, and every query after it fails with "MySQL server has gone away".
     *
     * @param eZDBInterface $db
     * @return bool
     */
    private static function isAlive( eZDBInterface $db )
    {
        $connection = $db->DBWriteConnection;
        if ( !$connection instanceof mysqli )
            return false;
        try
        {
            $result = @mysqli_query( $connection, 'SELECT 1' );
        }
        catch ( Throwable $e )
        {
            return false;
        }
        if ( $result instanceof mysqli_result )
        {
            $result->free();
            return true;
        }
        return false;
    }

    /**
     * The VarDir of the run, relative to the root: var/tests/run-<pid>. Removed again by dropAll().
     *
     * @return string
     */
    public static function varDir()
    {
        return 'var/tests/run-' . ( self::$pid !== 0 ? self::$pid : getmypid() );
    }

    /**
     * Forgets what the kernel cached from the database before (in memory and in the VarDir of the run): the next
     * database has other classes, objects and users under the same IDs.
     */
    private static function resetCaches()
    {
        eZCache::clearByID( array( 'classid', 'sortkey', 'urlalias', 'user_info_cache', 'content', 'content_language',
                                   'state_limitations', 'content_tree_menu', 'rss_cache' ) );
        eZContentLanguage::expireCache();
        eZContentObject::clearCache();
        eZContentClass::expireCache();
    }

    /**
     * Makes the database $name (empty).
     *
     * @param string $name
     * @param bool $logKept false for the probe of unavailableReason(), which is always dropped
     */
    private static function create( $name, $logKept = true )
    {
        if ( !self::isTestDatabaseName( $name ) || strpos( $name, self::NAME_PREFIX ) !== 0 )
            throw new RuntimeException( "refusing to make the database '$name': not a throwaway name (" . self::NAME_PREFIX . '...)' );
        self::registerCleanup();
        self::withAdmin( function ( $admin ) use ( $name )
        {
            $admin->query( 'CREATE DATABASE `' . $name . '` CHARACTER SET ' . self::CHARSET . ' COLLATE ' . self::COLLATION );
        } );
        self::$created[$name] = true;
        if ( $logKept && self::keepDatabases() )
            self::logName( $name, 'kept' );
    }

    /**
     * Makes the database $name as a copy of the seed of the run (made on first use).
     *
     * @param string $name
     */
    private static function copySeed( $name )
    {
        $seed = self::seed();
        self::create( $name );
        self::withAdmin( function ( $admin ) use ( $name, $seed )
        {
            $result = $admin->query( "SHOW FULL TABLES FROM `$seed` WHERE Table_type = 'BASE TABLE'" );
            $tables = array();
            while ( $row = $result->fetch_row() )
                $tables[] = $row[0];
            $result->free();
            $admin->query( 'SET SESSION foreign_key_checks = 0' );
            foreach ( $tables as $table )
            {
                $admin->query( "CREATE TABLE `$name`.`$table` LIKE `$seed`.`$table`" );
                $admin->query( "INSERT INTO `$name`.`$table` SELECT * FROM `$seed`.`$table`" );
            }
        } );
    }

    /**
     * The seed database of the run: kernel schema and clean data, loaded once. When that fails, the half-made seed is
     * dropped at once and the failure kept: the classes after the first are skipped with it instead of trying again.
     *
     * @return string
     */
    private static function seed()
    {
        if ( self::$seed !== null && isset( self::$created[self::$seed] ) )
            return self::$seed;
        if ( self::$seedFailure !== null )
            throw new RuntimeException( self::$seedFailure );
        $name = self::newName();
        self::create( $name );
        try
        {
            $root = dirname( __DIR__, 2 );
            self::loadFile( $name, $root . '/kernel/sql/mysql/kernel_schema.sql', false );
            self::loadFile( $name, $root . '/kernel/sql/common/cleandata.sql', true );
            self::loadExtensionSchemas( $name );
        }
        catch ( Throwable $e )
        {
            self::$seedFailure = 'the seed database (kernel schema and clean data) could not be made: ' . trim( strtok( $e->getMessage(), "\n" ) );
            try
            {
                self::drop( $name, true );
            }
            catch ( Throwable $dropFailure )
            {
                fwrite( STDERR, "could not drop the failed seed $name: " . $dropFailure->getMessage() . PHP_EOL );
            }
            throw new RuntimeException( self::$seedFailure, 0, $e );
        }
        self::$seed = $name;
        return $name;
    }

    /**
     * Adds the tables of the active extensions to the database $name: each one's share/db_schema.dba (the tables
     * an installation of the extension has, as the setup check reads them), tables the database has already left
     * out. Without them every test that publishes or removes content fails on an extension's table (ezfind's
     * ezfind_elevate_configuration, for one).
     *
     * @param string $name
     */
    private static function loadExtensionSchemas( $name )
    {
        self::assertOwn( $name );
        $server = self::server();
        $db = eZDB::instance( 'ezmysqli', array( 'use_defaults' => false, 'server' => $server['host'], 'port' => $server['port'] !== '' ? (int)$server['port'] : false,
                                                 'socket' => $server['socket'] !== '' ? $server['socket'] : false, 'user' => $server['user'],
                                                 'password' => $server['password'], 'database' => $name, 'show_errors' => false ), true );
        if ( !$db->isConnected() )
            throw new RuntimeException( "cannot connect to $name: " . $db->errorMessage() );
        self::$connections[$name][] = $db;
        foreach ( eZExtension::activeExtensions() as $extension )
        {
            $path = eZExtension::extensionPath( $extension );
            $file = $path ? "$path/share/db_schema.dba" : '';
            if ( $file === '' || !file_exists( $file ) )
                continue;
            $definition = eZDbSchema::read( $file, true );
            $schema = ( is_array( $definition ) && isset( $definition['schema'] ) ) ? $definition['schema'] : $definition;
            if ( !is_array( $schema ) )
                throw new RuntimeException( "cannot read $file" );
            $dbSchema = eZDbSchema::instance( array( 'instance' => $db, 'type' => 'mysql', 'schema' => $schema ) );
            if ( !$dbSchema || !$dbSchema->insertSchema( array( 'schema' => true, 'data' => false, 'table_type' => 'innodb' ) ) )
                throw new RuntimeException( "the tables of the extension $extension ($file) could not be made: " . $db->errorMessage() );
        }
        self::releaseConnections( $name );
    }

    /**
     * Runs the statements of an SQL file in the database $name.
     *
     * @param string $name
     * @param string $file
     * @param bool $inTransaction true for data: one commit instead of one per row. Not for the schema: every
     *        CREATE TABLE commits on its own (implicit commit), a transaction around it would not hold.
     */
    private static function loadFile( $name, $file, $inTransaction )
    {
        self::assertOwn( $name );
        $sql = @file_get_contents( $file );
        if ( $sql === false )
            throw new RuntimeException( "cannot read $file" );
        self::withAdmin( function ( $admin ) use ( $name, $file, $sql, $inTransaction )
        {
            $admin->select_db( $name );
            $admin->query( 'SET SESSION foreign_key_checks = 0' );
            if ( $inTransaction )
                $admin->query( 'START TRANSACTION' );
            $statement = 0;
            try
            {
                $admin->multi_query( $sql );
                do
                {
                    ++$statement;
                    if ( $result = $admin->store_result() )
                        $result->free();
                }
                while ( $admin->more_results() && $admin->next_result() );
                if ( $admin->errno )
                    throw new RuntimeException( $admin->error );
            }
            catch ( Throwable $e )
            {
                throw new RuntimeException( 'loading ' . basename( $file ) . " into $name failed at statement $statement: " . $e->getMessage(), 0, $e );
            }
            if ( $inTransaction )
                $admin->query( 'COMMIT' );
        } );
    }

    /**
     * Drops the database $name, which this process made, unless the databases are kept. First the connections of
     * this process to it are rolled back and closed, and any other session of the account still on it is ended:
     * an idle session with an open transaction holds a metadata lock DROP DATABASE waits for.
     *
     * @param string $name
     * @param bool $evenWhenKept true for the probe and a failed seed
     */
    private static function drop( $name, $evenWhenKept = false )
    {
        self::assertOwn( $name );
        self::releaseConnections( $name );
        if ( $evenWhenKept || !self::keepDatabases() )
        {
            self::withAdmin( function ( $admin ) use ( $name )
            {
                // sessions still on this database (one this process created, see assertOwn()) are ours or a kept
                // test's; the user filter keeps an account with PROCESS rights from seeing anyone else's
                $result = $admin->query( 'SELECT ID FROM information_schema.PROCESSLIST WHERE DB = \'' . $admin->real_escape_string( $name ) . '\''
                                         . ' AND USER = SUBSTRING_INDEX( CURRENT_USER(), \'@\', 1 ) AND ID <> CONNECTION_ID()' );
                $ids = array();
                while ( $row = $result->fetch_row() )
                    $ids[] = (int)$row[0];
                $result->free();
                foreach ( $ids as $id )
                {
                    try
                    {
                        $admin->query( "KILL CONNECTION $id" );
                    }
                    catch ( Throwable $e )
                    {
                        // gone in between
                    }
                }
                $admin->query( 'DROP DATABASE IF EXISTS `' . $name . '`' );
            } );
        }
        unset( self::$created[$name] );
    }

    /**
     * Rolls back and closes the connections this class opened to $name.
     *
     * @param string $name
     */
    private static function releaseConnections( $name )
    {
        foreach ( isset( self::$connections[$name] ) ? self::$connections[$name] : array() as $db )
        {
            foreach ( array( $db->DBWriteConnection, $db->DBConnection ) as $connection )
            {
                if ( $connection instanceof mysqli )
                {
                    try
                    {
                        @mysqli_query( $connection, 'ROLLBACK' );
                        @mysqli_query( $connection, 'UNLOCK TABLES' );
                    }
                    catch ( Throwable $e )
                    {
                        // a connection that is gone holds no lock
                    }
                }
            }
            $db->TransactionCounter = 0;
            try
            {
                if ( $db->IsConnected )
                    $db->close();
            }
            catch ( Throwable $e )
            {
            }
            // stays the eZDB instance when it is one, closed: a query after it fails instead of connecting anew to
            // whatever the settings name by then
            $db->IsConnected = false;
        }
        unset( self::$connections[$name] );

        // the DFS cluster tests point the cluster backend to the class database; backend and its settings are cached
        // statically: the session would stay open (killed below, "MySQL server has gone away" in the next class), and
        // the next class would connect to this database again
        if ( class_exists( 'eZDFSFileHandler', false ) )
        {
            $backend = new ReflectionProperty( 'eZDFSFileHandler', 'dbbackend' );
            $instance = $backend->getValue();
            if ( is_object( $instance ) )
            {
                $instance->_disconnect();
                $backend->setValue( null, null );
            }
            if ( class_exists( 'eZDFSFileHandlerMySQLiBackend', false ) && property_exists( 'eZDFSFileHandlerMySQLiBackend', 'dbparams' ) )
                ( new ReflectionProperty( 'eZDFSFileHandlerMySQLiBackend', 'dbparams' ) )->setValue( null, null );
            eZClusterFileHandler::resetHandler();
        }
    }

    /**
     * Drops every database this process made and has not dropped yet (shutdown handler), and puts the signal
     * handling back as it was. In a forked child it does nothing: the databases are the parent's.
     */
    public static function dropAll()
    {
        if ( self::$pid === 0 || self::$pid !== getmypid() )
            return;
        foreach ( array_keys( self::$created ) as $name )
        {
            try
            {
                self::drop( $name );
            }
            catch ( Throwable $e )
            {
                fwrite( STDERR, "could not drop the throwaway database $name: " . $e->getMessage() . PHP_EOL );
                self::logName( $name, 'left behind (DROP failed)' );
            }
        }
        self::$seed = self::$runDatabase = self::$activeName = null;
        self::$classDatabase = array( null, null );
        self::restoreSignals();
        self::removeVarDir();
    }

    /**
     * Refuses any database this process did not make, or whose name is no test name.
     *
     * @param string $name
     */
    private static function assertOwn( $name )
    {
        if ( !self::isTestDatabaseName( $name ) || !isset( self::$created[$name] ) || self::$pid !== getmypid() )
            throw new RuntimeException( "refusing to touch the database '$name': it is not a throwaway database of this process" );
    }

    /**
     * Runs $operation with an admin connection (no database selected), opened for it and closed after it. Errors
     * are exceptions; DROP waits at most LOCK_WAIT_TIMEOUT seconds for a lock.
     *
     * @param callable $operation function ( ezpThrowawayDatabaseConnection $admin )
     * @return mixed what $operation returns
     */
    private static function withAdmin( callable $operation )
    {
        if ( self::$adminDepth === 0 )
        {
            $server = self::server();
            $previous = ( new mysqli_driver() )->report_mode;
            mysqli_report( MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT );
            try
            {
                $host = $server['host'] !== '' ? $server['host'] : 'localhost';
                $mysqli = new mysqli( $host, $server['user'], $server['password'], '', $server['port'] !== '' ? (int)$server['port'] : 3306,
                                      $server['socket'] !== '' ? $server['socket'] : null );
                $mysqli->set_charset( self::CHARSET );
                $mysqli->query( 'SET SESSION lock_wait_timeout = ' . self::LOCK_WAIT_TIMEOUT );
            }
            finally
            {
                mysqli_report( $previous );
            }
            self::$admin = new ezpThrowawayDatabaseConnection( $mysqli );
        }
        ++self::$adminDepth;
        try
        {
            return $operation( self::$admin );
        }
        finally
        {
            if ( --self::$adminDepth === 0 )
            {
                self::$admin->close();
                self::$admin = null;
            }
        }
    }

    /**
     * Registers the cleanup once, in the process that makes the first database: at shutdown (also after a fatal
     * error) and on SIGINT and SIGTERM, which end PHP without running shutdown functions otherwise (Ctrl-C would
     * leave every database behind). The handlers turn the signal into exit(), so the shutdown function runs; they are
     * only installed where no handler is set yet, asynchronous signals only switched on where they are off, and both
     * are put back by dropAll().
     */
    private static function registerCleanup()
    {
        if ( self::$pid !== 0 )
            return;
        self::$pid = getmypid();
        register_shutdown_function( array( __CLASS__, 'dropAll' ) );
        if ( !function_exists( 'pcntl_signal' ) || !function_exists( 'pcntl_async_signals' ) )
            return;
        $pid = self::$pid;
        foreach ( array( SIGINT => 130, SIGTERM => 143 ) as $signal => $code )
        {
            if ( pcntl_signal_get_handler( $signal ) !== SIG_DFL )
                continue;
            $handler = function ( $signo ) use ( $code, $pid )
            {
                if ( getmypid() !== $pid )
                {
                    // a forked child: die of the signal as without a handler (exit without posix)
                    pcntl_signal( $signo, SIG_DFL );
                    if ( function_exists( 'posix_kill' ) )
                    {
                        posix_kill( getmypid(), $signo );
                        return;
                    }
                    exit( $code );
                }
                exit( $code );
            };
            pcntl_signal( $signal, $handler );
            self::$signalHandlers[$signal] = $handler;
        }
        if ( self::$signalHandlers && !pcntl_async_signals() )
        {
            self::$previousAsyncSignals = false;
            pcntl_async_signals( true );
        }
    }

    /**
     * Removes the VarDir of the run (var/tests/run-<pid>), unless the databases are kept.
     */
    private static function removeVarDir()
    {
        $dir = dirname( __DIR__, 2 ) . '/' . self::varDir();
        if ( self::keepDatabases() || !preg_match( '#/var/tests/run-\d+$#', $dir ) || !is_dir( $dir ) || is_link( $dir ) )
            return;
        $items = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ),
                                                RecursiveIteratorIterator::CHILD_FIRST );
        foreach ( $items as $item )
            $item->isDir() && !$item->isLink() ? @rmdir( $item->getPathname() ) : @unlink( $item->getPathname() );
        @rmdir( $dir );
    }

    /**
     * Puts the signal handling back as registerCleanup() found it.
     */
    private static function restoreSignals()
    {
        // only where our handler is still installed: a handler set later by someone else stays
        foreach ( self::$signalHandlers as $signal => $handler )
        {
            if ( pcntl_signal_get_handler( $signal ) === $handler )
                pcntl_signal( $signal, SIG_DFL );
        }
        self::$signalHandlers = array();
        if ( self::$previousAsyncSignals !== null )
            pcntl_async_signals( self::$previousAsyncSignals );
        self::$previousAsyncSignals = null;
    }

    /**
     * Notes a database in var/log/test-databases.log: kept (EXP_TEST_KEEP_DATABASES=1) or left behind.
     *
     * @param string $name
     * @param string $why
     */
    private static function logName( $name, $why )
    {
        $file = dirname( __DIR__, 2 ) . '/' . self::KEEP_LOG;
        if ( !is_dir( dirname( $file ) ) )
            @mkdir( dirname( $file ), 0775, true );
        @file_put_contents( $file, date( 'c' ) . ' ' . $name . ' ' . self::account() . ' ' . $why . PHP_EOL, FILE_APPEND | LOCK_EX );
    }

    /**
     * ezpDatabaseTestCase::$sqlFiles in the form ezpTestDatabaseHelper::insertSqlData() reads: a pair
     * array( 'dir/', 'file.sql' ) given flat, as some classes do, is one file of the database type's folder.
     *
     * @param array $sqlFiles
     * @return array
     */
    private static function normalizeSqlFiles( array $sqlFiles )
    {
        if ( count( $sqlFiles ) === 2 && is_string( $sqlFiles[0] ) && is_string( $sqlFiles[1] )
             && substr( $sqlFiles[0], -1 ) === '/' && strpos( $sqlFiles[1], '/' ) === false )
            return array( $sqlFiles );
        return $sqlFiles;
    }
}

/**
 * the admin connection of ezpThrowawayDatabase, with errors as exceptions whatever mysqli_report() the
 * kernel set: a failed CREATE, DROP or load must stop the setup, not go on silently.
 */
class ezpThrowawayDatabaseConnection
{
    /** @var mysqli */
    private $mysqli;

    public function __construct( mysqli $mysqli )
    {
        $this->mysqli = $mysqli;
    }

    public function __get( $name )
    {
        return $this->mysqli->$name;
    }

    public function __call( $method, $arguments )
    {
        $previous = ( new mysqli_driver() )->report_mode;
        mysqli_report( MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT );
        try
        {
            $result = $this->mysqli->$method( ...$arguments );
        }
        finally
        {
            mysqli_report( $previous );
        }
        if ( $result === false && $this->mysqli->errno )
            throw new RuntimeException( $this->mysqli->error );
        return $result;
    }
}
