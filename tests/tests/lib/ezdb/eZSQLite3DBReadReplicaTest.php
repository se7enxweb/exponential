<?php
/**
 * SQLite has no read replica: the database is one file on one server. [DatabaseSettings] UseSlaveServer and the
 * SlaveServerArray[] keys are ignored, so settings copied from a MySQL or PostgreSQL site work unchanged.
 *
 *  RR-S1 - The driver turns UseSlaveServer off and never pins or decides anything
 *  RR-S2 - Reads and writes of an anonymous GET web request all run on the one database, a write read back at once
 *  RR-S3 - The shared reset hook leaves an SQLite driver as it was
 *
 * No installation database: an SQLite database in memory.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezdb
 */

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
class eZSQLite3DBReadReplicaTest extends PHPUnit\Framework\TestCase
{
    private $globalInstance;
    private $server;
    private $cookies;

    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        $this->globalInstance = isset( $GLOBALS['eZDBGlobalInstance'] ) ? $GLOBALS['eZDBGlobalInstance'] : null;
        $this->server = $_SERVER;
        $this->cookies = $_COOKIE;
    }

    protected function tearDown(): void
    {
        if ( $this->globalInstance === null )
            unset( $GLOBALS['eZDBGlobalInstance'] );
        else
            $GLOBALS['eZDBGlobalInstance'] = $this->globalInstance;
        $_SERVER = $this->server;
        $_COOKIE = $this->cookies;
    }

    /** An SQLite database in memory, created with the settings of a site that reads from a replica. */
    private function db()
    {
        $db = new eZSQLite3DB( array( 'server' => '', 'port' => false, 'user' => '', 'password' => '', 'database' => ':memory:',
                                      'use_slave_server' => true, 'slave_server' => '127.0.0.1', 'slave_port' => 3408,
                                      'slave_user' => 'reader', 'slave_password' => 'x', 'slave_database' => 'replica', 'socket' => false,
                                      'charset' => 'utf-8', 'is_internal_charset' => false, 'builtin_encoding' => false,
                                      'connect_retries' => 1, 'use_persistent_connection' => false, 'show_errors' => false ) );
        $this->assertTrue( $db->isConnected(), 'an SQLite database in memory' );
        return $db;
    }

    /** RR-S1 */
    public function testUseSlaveServerIsTurnedOff()
    {
        $db = $this->db();
        $this->assertFalse( $db->UseSlaveServer );
        $this->assertFalse( eZSQLite3DB::isReplicaReadable( 'SELECT 1' ), 'no statement is replica-readable' );
        $db->query( 'CREATE TABLE t ( id INTEGER PRIMARY KEY, name TEXT )' );
        $this->assertFalse( $db->ReplicaPinned, 'nothing to pin' );
        $this->assertNull( $db->ReplicaState, 'nothing decided' );
        $this->assertFalse( $db->ReplicaConnection );
    }

    /** RR-S2 */
    public function testAnAnonymousWebRequestReadsItsOwnWriteAtOnce()
    {
        $_SERVER['QBIX_WORKER'] = '1';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_COOKIE = array();
        $db = $this->db();
        $db->query( 'CREATE TABLE t ( id INTEGER PRIMARY KEY, name TEXT )' );
        $this->assertSame( array(), $db->arrayQuery( 'SELECT name FROM t' ) );
        $db->query( "INSERT INTO t ( name ) VALUES ( 'edit' )" );
        $this->assertSame( array( array( 'name' => 'edit' ) ), $db->arrayQuery( 'SELECT name FROM t' ) );
        $db->begin();
        $db->query( "UPDATE t SET name = 'edited'" );
        $this->assertSame( array( array( 'name' => 'edited' ) ), $db->arrayQuery( 'SELECT name FROM t' ), 'in a transaction' );
        $db->commit();
        $this->assertSame( array( array( 'name' => 'edited' ) ), $db->arrayQuery( 'SELECT name FROM t', array(), eZDBInterface::SERVER_SLAVE ) );
        $this->assertFalse( $db->ReplicaConnection );
    }

    /** RR-S3 */
    public function testTheResetHookLeavesSQLiteAsItWas()
    {
        $db = $this->db();
        $GLOBALS['eZDBGlobalInstance'] = $db;
        eZDBInterface::resetReplicaRequest();
        $this->assertFalse( $db->UseSlaveServer );
        $this->assertNull( $db->ReplicaState );
        $this->assertSame( array( array( 'one' => 1 ) ), $db->arrayQuery( 'SELECT 1 AS one' ) );
    }
}
