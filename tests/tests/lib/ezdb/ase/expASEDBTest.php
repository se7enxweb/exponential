<?php
/**
 * expASEDB over a recording transport (no server needed; no ASE server was available for live tests).
 *
 *  AS-01 - Connecting: the session setup, @@version read, databaseName() ase, the helper dialect, no password in errors
 *  AS-02 - Paging with TOP and the offset dropped here; the kernel's trailing LIMIT; a one-space value read as ''
 *  AS-03 - New ids: NULL/0 leaves the identity column out, SELECT @@identity in the batch; an own id with
 *          identity_insert on and off; an INSERT of several rows as one batch of single-row INSERTs
 *  AS-04 - Locks with LOCK TABLE, no application locks; temporary tables as #tables with ASE's types
 *  AS-05 - Relations from sysobjects, no sequences; the helpers that ASE lacks are refused
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expASETestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('ase')]
class expASEDBTest extends PHPUnit\Framework\TestCase
{
    private $saved;

    protected function setUp(): void
    {
        $this->saved = expASETestKit::quietDebug();
    }

    protected function tearDown(): void
    {
        expASETestKit::restoreDebug( $this->saved );
    }

    public function testConnect()
    {
        $t = new expASEFakeTransport();
        $db = expASETestKit::driver( $t, array(), array( 'server' => 'ase.example', 'port' => 5001, 'database' => 'site' ) );
        $this->assertTrue( $db->isConnected() );
        $this->assertSame( 'ase', $db->databaseName() );
        $this->assertSame( 'ase', $db->sqlHelperDialect() );
        $this->assertSame( array( 'ase.example', 5001, 'site', 'sa' ), array( $t->config['host'], $t->config['port'], $t->config['database'], $t->config['user'] ) );
        $this->assertStringStartsWith( 'set quoted_identifier on', $t->log[0][0] );
        $this->assertSame( '16.0 SP04', $db->databaseServerVersion()['string'] );
        $this->assertFalse( $db->supportsDefaultValuesInsertion() );

        $refused = new expASEFakeTransport();
        $refused->refuseOpen = true;
        try
        {
            expASETestKit::driver( $refused );
            $this->fail( 'no exception' );
        }
        catch ( eZDBNoConnectionException $e )
        {
            $this->assertStringNotContainsString( 'Secret-Pass-123', $e->getMessage() );
        }
    }

    public function testPaging()
    {
        $t = new expASEFakeTransport();
        $rows = array();
        foreach ( range( 1, 15 ) as $n )
            $rows[] = array( 'id' => (string)$n, 'name' => $n === 12 ? ' ' : "n$n" );
        $t->on( '/FROM ezx/', $rows );
        $db = expASETestKit::driver( $t );
        $page = $db->arrayQuery( 'SELECT id, name FROM ezx ORDER BY id', array( 'limit' => 5, 'offset' => 10 ) );
        $this->assertSame( 'SELECT TOP 15 id, name FROM ezx ORDER BY id', $db->LastStatement );
        $this->assertSame( array( 10, 11, 12, 13, 14 ), array_keys( $page ) );
        $this->assertSame( '11', $page[10]['id'] );
        $this->assertSame( '', $page[11]['name'] );
        $db->arrayQuery( 'SELECT id FROM ezx ORDER BY id LIMIT 2, 3', array( 'column' => 'id' ) );
        $this->assertSame( 'SELECT TOP 5 id FROM ezx ORDER BY id', $db->LastStatement );
        $this->assertSame( array(), $db->arrayQuery( 'SELECT id FROM ezx', array( 'limit' => 0 ) ) );
    }

    public function testNewIds()
    {
        $t = new expASEFakeTransport();
        $db = expASETestKit::driver( $t );
        $this->assertTrue( $db->query( "INSERT INTO ezx (id, name) VALUES (NULL, 'a')" ) );
        $this->assertSame( "INSERT INTO ezx (name) VALUES ('a')\nSELECT @@identity AS exp_identity", $db->LastStatement );
        $this->assertSame( 41, $db->lastSerialID( 'ezx', 'id' ) );
        $this->assertFalse( $db->lastSerialID( 'ezy' ) );
        $this->assertTrue( $db->query( "INSERT INTO ezx (id, name) VALUES (7, 'b')" ) );
        $this->assertSame( "SET IDENTITY_INSERT ezx ON\nINSERT INTO ezx (id, name) VALUES (7, 'b')\nSELECT @@identity AS exp_identity\nSET IDENTITY_INSERT ezx OFF", $db->LastStatement );
        $this->assertTrue( $db->query( "INSERT INTO ezy (a, b) VALUES (1, 'x'), (2, 'y')" ) );
        $this->assertSame( "INSERT INTO ezy (a, b) VALUES (1, 'x')\nINSERT INTO ezy (a, b) VALUES (2, 'y')", $db->LastStatement );
    }

    public function testLocksAndTemporaryTables()
    {
        $t = new expASEFakeTransport();
        $db = expASETestKit::driver( $t );
        $db->lock( array( array( 'table' => 'ezx' ), 'ezy WRITE' ) );
        $this->assertSame( array( 'LOCK TABLE [ezx] IN EXCLUSIVE MODE', 'LOCK TABLE [ezy] IN EXCLUSIVE MODE' ), $t->sent( '/^LOCK TABLE/' ) );
        $db->unlock();
        $this->assertFalse( $db->getAppLock( 'x' ) );
        $name = $db->generateUniqueTempTableName( 'ezsearch_tmp_%', 0 );
        $this->assertSame( '#ezsearch_tmp_0', $name );
        $this->assertTrue( $db->createTempTable( "CREATE TEMPORARY TABLE $name ( contentobject_id int primary key not null )" ) );
        $this->assertSame( array( 'CREATE TABLE #ezsearch_tmp_0 ( contentobject_id INT NOT NULL PRIMARY KEY )' ), $t->sent( '/^CREATE TABLE/' ) );
        $db->dropTempTableList( array( $name ) );
        $this->assertSame( array( "IF OBJECT_ID('tempdb..#ezsearch_tmp_0') IS NOT NULL DROP TABLE #ezsearch_tmp_0" ), $t->sent( '/DROP TABLE/' ) );
    }

    public function testRelationsAndHelpers()
    {
        $t = new expASEFakeTransport();
        $t->on( "/FROM sysobjects WHERE type = 'U'/", array( array( 'name' => 'ezx' ), array( 'name' => 'ezy' ) ) );
        $db = expASETestKit::driver( $t );
        $this->assertSame( array( 'ezx', 'ezy' ), $db->relationList() );
        $this->assertSame( array(), $db->relationList( eZDBInterface::RELATION_SEQUENCE ) );
        $this->assertSame( array( 'ezx' => eZDBInterface::RELATION_TABLE, 'ezy' => eZDBInterface::RELATION_TABLE ), $db->eZTableList() );
        $this->assertTrue( $db->removeRelation( 'ezy', eZDBInterface::RELATION_TABLE ) );
        $this->assertSame( array( 'DROP TABLE [ezy]' ), $t->sent( '/^DROP TABLE \[ezy\]/' ) );
        $this->assertSame( "lower(hash(x, 'md5'))", $db->md5( 'x' ) );
        $this->assertSame( "(a + b)", $db->concatString( array( 'a', 'b' ) ) );
        $this->assertSame( 'SELECT TOP 2 a FROM t', $db->sqlLimit( 'SELECT a FROM t', 2 ) );
        $this->assertSame( "datediff(ss, '19700101', getutcdate())", $db->sqlUnixTimestamp() );
        $this->assertSame( '[a].[b]', $db->sqlQuoteIdentifier( 'a.b' ) );
        $this->expectException( expDBUnsupportedException::class );
        $db->sqlGroupConcat( 'name' );
    }
}
