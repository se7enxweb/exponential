<?php
/**
 * A persistent worker keeps its SQLite connection for its next request.
 *
 * Opening the database cost some 5 ms per request (SQLite reads the whole schema for every new connection). In a
 * Velocity pool worker close() gives the connection back to the worker store and the next connect() takes it, with
 * its PRAGMAs and schema; a transaction still open, another process or a connection that no longer answers closes it
 * instead, and temporary tables a request left are dropped before it is used again. Outside a worker nothing is kept.
 * The worker is played by a subclass whose keptConnectionKey() answers as a pool worker's does.
 *
 * Database: a SQLite file below var/tmp, removed afterwards.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class eZSQLite3KeptConnectionTestDB extends eZSQLite3DB
{
    protected static function keptConnectionKey( $fullPath )
    {
        return $fullPath === ':memory:' ? null : getmypid() . '|' . $fullPath;
    }

    public function wasKept()
    {
        return $this->KeptConnection;
    }

    public function connection()
    {
        return $this->DBConnection;
    }
}

class eZSQLite3KeptConnectionTest extends PHPUnit\Framework\TestCase
{
    /** @var array the debug globals before the test */
    private $savedDebug = array();

    /** @var string */
    private $file;

    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        if ( !method_exists( 'eZDebug', 'createAccumulatorGroup' ) )
            $this->markTestSkipped( 'eZDebug is a stand-in declared by another test of this run' );
        foreach ( array( 'eZDebugGlobalInstance', 'eZDebugEnabled' ) as $key )
            $this->savedDebug[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        $GLOBALS['eZDebugGlobalInstance'] = new eZDebug();
        $GLOBALS['eZDebugEnabled'] = false;
        @mkdir( 'var/tmp', 0775, true );
        $this->file = getcwd() . '/var/tmp/kept-connection-' . getmypid() . '-' . mt_rand() . '.db';
        eZSQLite3DB::closeKeptConnections();
    }

    protected function tearDown(): void
    {
        eZSQLite3DB::closeKeptConnections();
        foreach ( array( '', '-wal', '-shm' ) as $suffix )
        {
            if ( is_file( $this->file . $suffix ) )
                unlink( $this->file . $suffix );
        }
        foreach ( $this->savedDebug as $key => $value )
        {
            if ( $value === null )
                unset( $GLOBALS[$key] );
            else
                $GLOBALS[$key] = $value[0];
        }
    }

    private function parameters()
    {
        return array( 'server' => '', 'port' => false, 'user' => '', 'password' => '', 'database' => $this->file,
                      'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false,
                      'slave_user' => '', 'slave_password' => '', 'slave_database' => '', 'socket' => false,
                      'charset' => 'utf-8', 'is_internal_charset' => false, 'builtin_encoding' => false,
                      'connect_retries' => 1, 'use_persistent_connection' => false, 'show_errors' => false );
    }

    public function testTheNextConnectionIsTheKeptOne()
    {
        $first = new eZSQLite3KeptConnectionTestDB( $this->parameters() );
        $this->assertFalse( $first->wasKept() );
        $handle = $first->connection();
        $first->query( 'CREATE TABLE t ( v INTEGER )' );
        $first->query( 'INSERT INTO t VALUES ( 7 )' );
        $first->close();
        $this->assertFalse( $first->isConnected() );

        $second = new eZSQLite3KeptConnectionTestDB( $this->parameters() );
        $this->assertTrue( $second->wasKept() );
        $this->assertSame( $handle, $second->connection() );
        $this->assertSame( array( array( 'v' => 7 ) ), $second->arrayQuery( 'SELECT v FROM t' ) );
        // the settings of the connection are still there
        $this->assertSame( 'wal', $second->connection()->querySingle( 'PRAGMA journal_mode' ) );
        $this->assertSame( 2, $second->connection()->querySingle( 'PRAGMA temp_store' ) );
        // and the functions the driver adds
        $this->assertSame( md5( 'x' ), $second->connection()->querySingle( "SELECT md5( 'x' )" ) );
        $this->assertSame( 1, $second->connection()->querySingle( 'SELECT MOD( 7, 2 )' ) );
        $second->close();
    }

    public function testTemporaryTablesOfARequestAreGone()
    {
        $first = new eZSQLite3KeptConnectionTestDB( $this->parameters() );
        $first->query( 'CREATE TEMPORARY TABLE leftover ( v INTEGER )' );
        $first->close();
        $second = new eZSQLite3KeptConnectionTestDB( $this->parameters() );
        $this->assertTrue( $second->wasKept() );
        $this->assertSame( 0, $second->connection()->querySingle( "SELECT count(*) FROM sqlite_temp_master WHERE name = 'leftover'" ) );
        $second->close();
    }

    public function testOpenTransactionClosesTheConnection()
    {
        $first = new eZSQLite3KeptConnectionTestDB( $this->parameters() );
        $first->query( 'CREATE TABLE t ( v INTEGER )' );
        $first->begin();
        $first->query( 'INSERT INTO t VALUES ( 1 )' );
        $first->close();
        $second = new eZSQLite3KeptConnectionTestDB( $this->parameters() );
        $this->assertFalse( $second->wasKept() );
        // closed, so SQLite rolled the insert back
        $this->assertSame( array(), $second->arrayQuery( 'SELECT v FROM t' ) );
        $second->close();
    }

    public function testClosedKeptConnectionIsNotUsed()
    {
        $first = new eZSQLite3KeptConnectionTestDB( $this->parameters() );
        $first->close();
        eZSQLite3DB::closeKeptConnections();
        $second = new eZSQLite3KeptConnectionTestDB( $this->parameters() );
        $this->assertFalse( $second->wasKept() );
        $this->assertTrue( $second->isConnected() );
        $second->close();
    }

    public function testReplacedDatabaseFileIsOpenedAgain()
    {
        $first = new eZSQLite3KeptConnectionTestDB( $this->parameters() );
        $first->query( 'CREATE TABLE t ( v INTEGER )' );
        $first->query( 'INSERT INTO t VALUES ( 1 )' );
        $first->close();
        // a restore puts another file at the path: written beside it and renamed over it
        $other = new SQLite3( $this->file . '.new' );
        $other->exec( 'CREATE TABLE t ( v INTEGER )' );
        $other->exec( 'INSERT INTO t VALUES ( 2 )' );
        $other->close();
        foreach ( array( '-wal', '-shm' ) as $suffix )
        {
            if ( is_file( $this->file . $suffix ) )
                unlink( $this->file . $suffix );
        }
        rename( $this->file . '.new', $this->file );
        $second = new eZSQLite3KeptConnectionTestDB( $this->parameters() );
        $this->assertFalse( $second->wasKept() );
        $this->assertSame( array( array( 'v' => 2 ) ), $second->arrayQuery( 'SELECT v FROM t' ) );
        $second->close();
    }

    public function testConnectionThatMetAnErrorIsNotKept()
    {
        $first = new eZSQLite3KeptConnectionTestDB( $this->parameters() );
        $first->query( 'SELECT * FROM no_such_table' );
        $first->close();
        $second = new eZSQLite3KeptConnectionTestDB( $this->parameters() );
        $this->assertFalse( $second->wasKept() );
        $second->close();
    }

    public function testNothingIsKeptOutsideAWorker()
    {
        $first = new eZSQLite3DB( $this->parameters() );
        $first->close();
        $worker = expKernelServiceScope::worker();
        $this->assertEmpty( isset( $worker->connections['sqlite3'] ) ? $worker->connections['sqlite3'] : array() );
    }
}
