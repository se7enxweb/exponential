<?php
/**
 * eZDBException::getCode() is the database driver's error number of the failed statement, on every driver.
 *
 * Each driver runs statements that fail in a known way, a table that does not exist and a duplicate key, with the
 * exception error handling (eZDB::ERROR_HANDLING_EXCEPTIONS), and the exception's code must be the driver's own
 * number for it, the same as errorNumber():
 *   - SQLite (always, a database file of this test under var/tmp): result code 1 (SQLITE_ERROR) for a missing
 *     table, 19 (SQLITE_CONSTRAINT) for a duplicate key. The SQLite driver throws inside a transaction, where it
 *     rolls back; outside one it answers false, with errorNumber() set.
 *   - MySQL/MariaDB: 1146 and 1062. EXP_TEST_MYSQL_USER, EXP_TEST_MYSQL_PASSWORD, EXP_TEST_MYSQL_DATABASE,
 *     EXP_TEST_MYSQL_HOST (default localhost), EXP_TEST_MYSQL_PORT (default 3306).
 *   - PostgreSQL: the SQLSTATE, '42P01' and '23505', and the message in its usual form (no code, no LOCATION line).
 *     EXP_TEST_PGSQL_USER, EXP_TEST_PGSQL_PASSWORD, EXP_TEST_PGSQL_DATABASE, EXP_TEST_PGSQL_HOST (default
 *     localhost), EXP_TEST_PGSQL_PORT (default 5432).
 *   - Oracle: ORA-00942, code 942. EXP_TEST_ORACLE_USER, EXP_TEST_ORACLE_PASSWORD, EXP_TEST_ORACLE_CONNECT (an
 *     Easy Connect string or TNS name); needs the ezoracle extension and oci8.
 *   - MongoDB: the server's error code of a refused statement is kept in errorNumber() (the driver answers false
 *     and throws nothing), checked without a server.
 * A server whose variables are not set is skipped. Only temporary tables are written on the servers (they go with
 * the connection); nothing of an installation is read or changed.
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 3d18f47551), Copyright (C) Mugo Web, GPL v2
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezdb
 */

class eZDBExceptionCodeTest extends PHPUnit\Framework\TestCase
{
    const MISSING_TABLE = 'expw6_no_such_table';

    private $savedGlobals = array();

    private $files = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        foreach ( array( 'eZDBGlobalInstance', 'eZDebugGlobalInstance', 'eZDebugEnabled', 'eZDebugLogOnly' ) as $name )
            $this->savedGlobals[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
        // the driver's error messages are not written to the site's error.log
        $GLOBALS['eZDebugGlobalInstance'] = new eZDebug();
        foreach ( $GLOBALS['eZDebugGlobalInstance']->LogFiles as $level => $file )
            $GLOBALS['eZDebugGlobalInstance']->LogFiles[$level] = array( $this->logDir(), $file[1] );
        $GLOBALS['eZDebugEnabled'] = false;
    }

    protected function tearDown(): void
    {
        foreach ( $this->savedGlobals as $name => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$name] );
            else
                $GLOBALS[$name] = $saved[0];
        }
        foreach ( $this->files as $file )
        {
            foreach ( array( '', '-journal', '-wal', '-shm', '.writer-lock' ) as $suffix )
            {
                if ( file_exists( $file . $suffix ) )
                    unlink( $file . $suffix );
            }
        }
        $dir = $this->logDir( false );
        if ( is_dir( $dir ) )
        {
            foreach ( glob( $dir . '*' ) as $file )
                unlink( $file );
            rmdir( $dir );
        }
    }

    private function logDir( $create = true )
    {
        $dir = getcwd() . '/var/tmp/phpunit-dbexceptioncode-' . getmypid() . '/';
        if ( $create && !is_dir( $dir ) )
            mkdir( $dir, 0777, true );
        return $dir;
    }

    private static function env( $name, $default = '' )
    {
        $value = getenv( $name );
        return $value === false || trim( $value ) === '' ? $default : trim( $value );
    }

    /** A new connection of $implementation, without replacing the site's (eZDB::instance() keeps the last one). */
    private function connect( $implementation, array $parameters )
    {
        $parameters += array( 'use_defaults' => false, 'socket' => false, 'port' => false, 'charset' => 'utf-8',
                              'use_slave_server' => false, 'use_persistent_connection' => false, 'show_errors' => false,
                              'connect_retries' => 0 );
        $saved = isset( $GLOBALS['eZDBGlobalInstance'] ) ? $GLOBALS['eZDBGlobalInstance'] : null;
        try
        {
            $db = eZDB::instance( $implementation, $parameters, true );
        }
        catch ( Exception $e )
        {
            $db = null;
        }
        $GLOBALS['eZDBGlobalInstance'] = $saved;
        if ( !$db instanceof eZDBInterface || $db instanceof eZNullDB || !$db->isConnected() )
            return null;
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        return $db;
    }

    /** Runs $callback and returns the eZDBException it throws (fails when it throws none). */
    private function exceptionOf( callable $callback )
    {
        try
        {
            $callback();
        }
        catch ( eZDBException $e )
        {
            return $e;
        }
        $this->fail( 'no eZDBException was thrown' );
    }

    private function assertCode( $expected, eZDBException $e, eZDBInterface $db, $label )
    {
        $this->assertSame( $expected, $e->getCode(), "$label: getCode()" );
        $this->assertSame( $expected, $db->errorNumber(), "$label: errorNumber()" );
        $this->assertNotSame( '', (string)$e->getMessage(), "$label: message" );
    }

    /**
     * @testdox SQLite: result code 1 for a missing table and 19 for a duplicate key, in a transaction
     */
    public function testSQLite(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'the sqlite3 PHP extension is not loaded' );
        $file = $this->logDir() . 'codes.db';
        $this->files[] = $file;
        $db = $this->connect( 'sqlite3', array( 'server' => 'localhost', 'user' => '', 'password' => '', 'database' => $file ) );
        $this->assertNotNull( $db, 'a new SQLite database file' );

        // outside a transaction the driver answers false and keeps the number
        $this->assertFalse( $db->query( 'SELECT * FROM ' . self::MISSING_TABLE ) );
        $this->assertSame( 1, $db->errorNumber() );

        $e = $this->exceptionOf( function () use ( $db ) {
            $db->begin();
            $db->query( 'INSERT INTO ' . self::MISSING_TABLE . ' VALUES ( 1 )' );
        } );
        $this->assertCode( 1, $e, $db, 'missing table' );
        $this->assertStringContainsString( self::MISSING_TABLE, $e->getMessage() );

        $db->query( 'CREATE TABLE expw6_codes ( id INTEGER PRIMARY KEY )' );
        $db->query( 'INSERT INTO expw6_codes VALUES ( 1 )' );
        $e = $this->exceptionOf( function () use ( $db ) {
            $db->begin();
            $db->query( 'INSERT INTO expw6_codes VALUES ( 1 )' );
        } );
        $this->assertCode( 19, $e, $db, 'duplicate key' );
        $this->assertSame( 0, $db->transactionCounter() > 0 ? 1 : 0, 'the failed transaction was rolled back' );
    }

    /**
     * @testdox MySQL: 1146 for a missing table, 1062 for a duplicate key
     */
    public function testMySQL(): void
    {
        if ( !extension_loaded( 'mysqli' ) )
            $this->markTestSkipped( 'the mysqli PHP extension is not loaded' );
        if ( self::env( 'EXP_TEST_MYSQL_USER' ) === '' || self::env( 'EXP_TEST_MYSQL_DATABASE' ) === '' )
            $this->markTestSkipped( 'EXP_TEST_MYSQL_USER and EXP_TEST_MYSQL_DATABASE are not set' );
        $db = $this->connect( 'ezmysqli', array(
            'server' => self::env( 'EXP_TEST_MYSQL_HOST', 'localhost' ), 'port' => (int)self::env( 'EXP_TEST_MYSQL_PORT', '3306' ),
            'user' => self::env( 'EXP_TEST_MYSQL_USER' ), 'password' => (string)getenv( 'EXP_TEST_MYSQL_PASSWORD' ),
            'database' => self::env( 'EXP_TEST_MYSQL_DATABASE' ) ) );
        if ( !$db )
            $this->markTestSkipped( 'no connection to the MySQL server of EXP_TEST_MYSQL_*' );

        $e = $this->exceptionOf( function () use ( $db ) { $db->arrayQuery( 'SELECT * FROM ' . self::MISSING_TABLE ); } );
        $this->assertCode( 1146, $e, $db, 'missing table' );

        $db->query( 'CREATE TEMPORARY TABLE expw6_codes ( id INT PRIMARY KEY )' );
        $db->query( 'INSERT INTO expw6_codes VALUES ( 1 )' );
        $e = $this->exceptionOf( function () use ( $db ) { $db->query( 'INSERT INTO expw6_codes VALUES ( 1 )' ); } );
        $this->assertCode( 1062, $e, $db, 'duplicate key' );

        $e = $this->exceptionOf( function () use ( $db ) {
            $db->begin();
            $db->query( 'INSERT INTO expw6_codes VALUES ( 1 )' );
        } );
        $this->assertCode( 1062, $e, $db, 'duplicate key in a transaction (after its rollback)' );
        $db->query( 'DROP TEMPORARY TABLE expw6_codes' );
    }

    /**
     * @testdox PostgreSQL: the SQLSTATE, 42P01 for a missing table, 23505 for a duplicate key, the message as usual
     */
    public function testPostgreSQL(): void
    {
        if ( !extension_loaded( 'pgsql' ) )
            $this->markTestSkipped( 'the pgsql PHP extension is not loaded' );
        if ( self::env( 'EXP_TEST_PGSQL_USER' ) === '' || self::env( 'EXP_TEST_PGSQL_DATABASE' ) === '' )
            $this->markTestSkipped( 'EXP_TEST_PGSQL_USER and EXP_TEST_PGSQL_DATABASE are not set' );
        $db = $this->connect( 'ezpostgresql', array(
            'server' => self::env( 'EXP_TEST_PGSQL_HOST', 'localhost' ), 'port' => (int)self::env( 'EXP_TEST_PGSQL_PORT', '5432' ),
            'user' => self::env( 'EXP_TEST_PGSQL_USER' ), 'password' => (string)getenv( 'EXP_TEST_PGSQL_PASSWORD' ),
            'database' => self::env( 'EXP_TEST_PGSQL_DATABASE' ) ) );
        if ( !$db )
            $this->markTestSkipped( 'no connection to the PostgreSQL server of EXP_TEST_PGSQL_*' );

        $e = $this->exceptionOf( function () use ( $db ) { $db->query( 'SELECT * FROM ' . self::MISSING_TABLE ); } );
        $this->assertCode( '42P01', $e, $db, 'missing table' );
        $this->assertStringStartsWith( 'ERROR:  relation "' . self::MISSING_TABLE . '" does not exist', $db->errorMessage() );
        $this->assertStringNotContainsString( 'LOCATION:', $db->errorMessage() );
        $this->assertStringNotContainsString( '42P01', $db->errorMessage() );

        $db->query( 'CREATE TEMPORARY TABLE expw6_codes ( id INT PRIMARY KEY )' );
        $db->query( 'INSERT INTO expw6_codes VALUES ( 1 )' );
        $e = $this->exceptionOf( function () use ( $db ) { $db->query( 'INSERT INTO expw6_codes VALUES ( 1 )' ); } );
        $this->assertCode( '23505', $e, $db, 'duplicate key' );
        $this->assertStringContainsString( 'DETAIL:  Key (id)=(1) already exists.', $db->errorMessage() );
        $this->assertStringNotContainsString( 'CONSTRAINT NAME:', $db->errorMessage() );
        $db->query( 'DROP TABLE expw6_codes' );
    }

    /**
     * @testdox PostgreSQL messages: the SQLSTATE and the extra lines of the verbose form are taken out
     */
    public function testPostgreSQLMessageSplit(): void
    {
        $verbose = "ERROR:  23505: duplicate key value violates unique constraint \"t_pkey\"\n" .
                   "DETAIL:  Key (id)=(1) already exists.\n" .
                   "SCHEMA NAME:  pg_temp_3\nTABLE NAME:  t\nCONSTRAINT NAME:  t_pkey\n" .
                   "LOCATION:  _bt_check_unique, nbtinsert.c:666\n";
        $this->assertSame( array( '23505', "ERROR:  duplicate key value violates unique constraint \"t_pkey\"\n" .
                                           "DETAIL:  Key (id)=(1) already exists.\n" ),
                           eZPostgreSQLDB::splitErrorMessage( $verbose ) );
        $this->assertSame( array( 1, 'no connection to the server' ), eZPostgreSQLDB::splitErrorMessage( 'no connection to the server' ) );
        $this->assertSame( array( 0, '' ), eZPostgreSQLDB::splitErrorMessage( '' ) );
    }

    /**
     * @testdox Oracle: 942 for a missing table (ORA-00942)
     */
    public function testOracle(): void
    {
        if ( !extension_loaded( 'oci8' ) )
            $this->markTestSkipped( 'the oci8 PHP extension is not loaded' );
        if ( self::env( 'EXP_TEST_ORACLE_USER' ) === '' || self::env( 'EXP_TEST_ORACLE_CONNECT' ) === '' )
            $this->markTestSkipped( 'EXP_TEST_ORACLE_USER and EXP_TEST_ORACLE_CONNECT are not set' );
        $db = $this->connect( 'ezoracle', array( 'server' => '', 'user' => self::env( 'EXP_TEST_ORACLE_USER' ),
            'password' => (string)getenv( 'EXP_TEST_ORACLE_PASSWORD' ), 'database' => self::env( 'EXP_TEST_ORACLE_CONNECT' ) ) );
        if ( !$db )
            $this->markTestSkipped( 'no connection to the Oracle database of EXP_TEST_ORACLE_* (or the ezoracle extension is not active)' );

        $e = $this->exceptionOf( function () use ( $db ) { $db->arrayQuery( 'SELECT * FROM ' . self::MISSING_TABLE ); } );
        $this->assertCode( 942, $e, $db, 'missing table, arrayQuery()' );
        $e = $this->exceptionOf( function () use ( $db ) { $db->query( 'DELETE FROM ' . self::MISSING_TABLE ); } );
        $this->assertCode( 942, $e, $db, 'missing table, query()' );
        $e = $this->exceptionOf( function () use ( $db ) {
            $db->begin();
            $db->query( 'DELETE FROM ' . self::MISSING_TABLE );
        } );
        $this->assertCode( 942, $e, $db, 'missing table in a transaction (after its rollback)' );
    }

    /**
     * @testdox MongoDB: the server's error code of a refused statement is errorNumber(), the next statement clears it
     */
    public function testMongoDBRecordsTheServerErrorCode(): void
    {
        if ( !class_exists( 'expMongoDB' ) )
            $this->markTestSkipped( 'expMongoDB is not available' );
        $db = ( new ReflectionClass( 'expMongoDB' ) )->newInstanceWithoutConstructor();
        $db->recordDriverError( new RuntimeException( 'E11000 duplicate key error collection: exp.t index: _id_', 11000 ) );
        $this->assertSame( 11000, $db->errorNumber() );
        $this->assertStringContainsString( 'E11000', $db->errorMessage() );

        $db->RecordError = false;
        $db->recordDriverError( new RuntimeException( 'ignored', 26 ) );
        $this->assertSame( 11000, $db->errorNumber(), 'not recorded while RecordError is off' );

        $db->setError();
        $this->assertSame( 0, $db->errorNumber() );
        $this->assertSame( '', $db->errorMessage() );
    }
}
