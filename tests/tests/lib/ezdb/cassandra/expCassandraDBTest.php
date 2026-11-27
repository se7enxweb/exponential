<?php
/**
 * expCassandraDB without a server: a recording transport answers (expCassandraFakeTransport).
 *
 *  CB-01 - Opening: the keyspace changed to with USE; a missing keyspace refused, or made with the replication of the
 *          settings (ScyllaDB without tablets); the password never in a message
 *  CB-02 - plan(): the whole partition key with = or IN is a key lookup (and = on the clustering columns), = on an
 *          indexed column an index lookup, comparisons of other columns a filter, the rest a scan; what is left for PHP
 *  CB-03 - SELECT as sent: ALLOW FILTERING, LIMIT given to the server only when nothing is left for PHP, an IN list in
 *          chunks of InListChunkSize, COUNT(*) counted by the server, values bound by the column's type
 *  CB-04 - INSERT: the next id by compare and set, an id given moves the counter, the defaults of the definition, the
 *          duplicate check (read, lwt), REPLACE without a check
 *  CB-05 - UPDATE and DELETE: the rows read, written by their key; DELETE by the whole key without a read; DELETE
 *          without WHERE is TRUNCATE
 *  CB-06 - rollback(): the compensating writes, newest first
 *  CB-07 - Errors: an unknown table (1146) and column (1054), what CQL cannot say (unsupported), eZDBException with the
 *          exceptions error handling
 *  CB-08 - The helpers: escapeString(), the SQL expressions, sqlUpsert(), replication(), hosts(), the data table
 *          connection's options as [CassandraSettings]
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expCassandraTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('cassandra')]
class expCassandraDBTest extends PHPUnit\Framework\TestCase
{
    private $saved;

    protected function setUp(): void
    {
        $this->saved = expCassandraTestKit::quietDebug();
    }

    protected function tearDown(): void
    {
        expCassandraTestKit::restoreDebug( $this->saved );
    }

    /** The table item (id auto_increment, name indexed, value, section with a default) */
    private static function itemTable( expCassandraFakeTransport $t )
    {
        $definition = array( 'fields' => array( 'id' => array( 'type' => 'auto_increment', 'default' => false ),
                                                'name' => array( 'type' => 'varchar', 'length' => 50, 'default' => '' ),
                                                'value' => array( 'type' => 'int', 'default' => 0 ),
                                                'section' => array( 'type' => 'varchar', 'length' => 20, 'default' => 'standard' ) ),
                             'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ) ) );
        $t->describe( 'item', array( 'id' => array( 'int', 'partition_key', 0 ), 'name' => array( 'text', 'regular' ),
                                     'value' => array( 'int', 'regular' ), 'section' => array( 'text', 'regular' ) ),
                      array( 'name' => 'item_name' ), $definition );
        $t->describe( 'exp_cassandra_sequence', array( 'name' => array( 'text', 'partition_key', 0 ), 'value' => array( 'bigint', 'regular' ) ) );
        $t->describe( 'exp_cassandra_schema', array( 'table_name' => array( 'text', 'partition_key', 0 ), 'definition' => array( 'text', 'regular' ),
                                                     'created' => array( 'timestamp', 'regular' ) ) );
        // the counter: a stateful compare and set
        $counter = 0;
        $t->on( '/^SELECT value FROM "exp_cassandra_sequence"/', function () use ( &$counter ) {
            return array( array( 'value' => $counter ) );
        } );
        $t->on( '/^UPDATE "exp_cassandra_sequence" SET value = \? WHERE name = \? IF value = \?/', function ( $cql, $values ) use ( &$counter ) {
            if ( $values[2] !== $counter )
                return array( array( '[applied]' => false, 'value' => $counter ) );
            $counter = $values[0];
            return array( array( '[applied]' => true ) );
        } );
        return $t;
    }

    /** The table pair (object_id partition key, version clustering) */
    private static function pairTable( expCassandraFakeTransport $t )
    {
        return $t->describe( 'pair', array( 'object_id' => array( 'int', 'partition_key', 0 ), 'version' => array( 'int', 'clustering', 0, 'asc' ),
                                            'title' => array( 'text', 'regular' ) ) );
    }

    private static function keyspace( expCassandraFakeTransport $t, $exists = true )
    {
        return $t->on( '/FROM system_schema\.keyspaces WHERE keyspace_name = \?/', $exists ? array( array( 'keyspace_name' => 'ks' ) ) : array() );
    }

    /** CB-01 */
    public function testOpening()
    {
        $t = self::keyspace( new expCassandraFakeTransport() );
        $db = expCassandraTestKit::driver( $t, array(), array( 'database' => 'ks', 'server' => 'a.example, b.example:9043' ) );
        $this->assertTrue( $db->isConnected() );
        $this->assertSame( array( 'a.example', 'b.example:9043' ), $t->config['hosts'] );
        $this->assertSame( '', $t->config['keyspace'] );
        $this->assertCount( 1, $t->sent( '/^USE "ks"$/' ) );
        $this->assertSame( 'cassandra', $db->databaseName() );
        $this->assertSame( 'php-cassandra', ( new expCassandraPHPTransport() )->name() );

        $t = self::keyspace( new expCassandraFakeTransport(), false );
        try
        {
            expCassandraTestKit::driver( $t, array(), array( 'database' => 'ks' ) );
            $this->fail( 'a missing keyspace opened' );
        }
        catch ( eZDBNoConnectionException $e )
        {
            $this->assertStringContainsString( "keyspace 'ks' does not exist", $e->getMessage() );
        }

        // made with the settings' replication; on ScyllaDB without tablets
        $t = self::keyspace( new expCassandraFakeTransport(), false );
        $t->on( '/FROM system\.versions/', array( array( 'version' => '2025.1.17-0.20261111' ) ) );
        $db = new expScyllaDB( expCassandraTestKit::parameters( array( 'database' => 'ks', 'cassandra_transport' => $t,
                                                                        'cassandra' => array( 'AutoCreateKeyspace' => 'enabled', 'DataCenters' => 'dc1:3,dc2' ) ) ) );
        $this->assertSame( 'scylladb', $db->product() );
        $this->assertSame( 'scylladb', $db->databaseName() );
        $this->assertSame( '2025.1.17', $db->databaseServerVersion()['string'] );
        $created = $t->sent( '/^CREATE KEYSPACE/' );
        $this->assertSame( "CREATE KEYSPACE IF NOT EXISTS \"ks\" WITH replication = {'class': 'NetworkTopologyStrategy', 'dc1': 3, 'dc2': 1} AND tablets = {'enabled': false}", $created[0][0] );

        // a refused login: the password is not in the message
        $t = new expCassandraFakeTransport();
        $t->refuseOpen = true;
        try
        {
            expCassandraTestKit::driver( $t );
            $this->fail( 'a refused login opened' );
        }
        catch ( eZDBNoConnectionException $e )
        {
            $this->assertStringNotContainsString( 'Secret-Pass-123', $e->getMessage() );
        }
    }

    private static function table( array $columns, array $partition, array $clustering = array(), array $indexes = array() )
    {
        return array( 'name' => 't', 'columns' => $columns, 'partition' => $partition, 'clustering' => $clustering, 'indexes' => $indexes,
                      'definition' => null, 'auto' => null );
    }

    /** CB-02 */
    public function testPlan()
    {
        $table = self::table( array( 'a' => 'int', 'b' => 'int', 'c' => 'text', 'd' => 'int', 'e' => 'text' ), array( 'a' ), array( 'b' => 'asc' ), array( 'c' => 't_c' ) );
        $plan = expCassandraDB::plan( expCassandraDialect::parse( "SELECT * FROM t WHERE a = '5' AND b = 2 AND e LIKE 'x%'" ), $table );
        $this->assertSame( 'key', $plan['kind'] );
        $this->assertSame( array( '"a" = ?', '"b" = ?' ), $plan['where'] );
        $this->assertSame( array( 5, 2 ), $plan['values'] );
        $this->assertSame( 'like', $plan['rest']['t'] );
        $plan = expCassandraDB::plan( expCassandraDialect::parse( 'SELECT * FROM t WHERE a IN (1, 2, 2) AND b > 3' ), $table );
        $this->assertSame( 'key', $plan['kind'] );
        $this->assertSame( array( 'column' => 'a', 'values' => array( 1, 2 ) ), $plan['in'] );
        $this->assertSame( 'cmp', $plan['rest']['t'], 'a range of a clustering column stays for PHP' );
        $plan = expCassandraDB::plan( expCassandraDialect::parse( "SELECT * FROM t WHERE c = 'x' AND d > 4" ), $table );
        $this->assertSame( 'filter', $plan['kind'] );
        $this->assertSame( array( '"c" = ?', '"d" > ?' ), $plan['where'] );
        $this->assertNull( $plan['rest'] );
        $plan = expCassandraDB::plan( expCassandraDialect::parse( "SELECT * FROM t WHERE c = 'x' AND d IN (1, 2)" ), $table );
        $this->assertSame( 'index', $plan['kind'] );
        $this->assertSame( 'in', $plan['rest']['t'] );
        $plan = expCassandraDB::plan( expCassandraDialect::parse( 'SELECT * FROM t WHERE d = 1 OR a = 2' ), $table );
        $this->assertSame( 'scan', $plan['kind'] );
        $this->assertSame( 'or', $plan['rest']['t'] );
        $plan = expCassandraDB::plan( expCassandraDialect::parse( 'SELECT * FROM t WHERE d = 1' ), $table, false );
        $this->assertSame( 'scan', $plan['kind'], 'AllowFiltering disabled' );
        $plan = expCassandraDB::plan( expCassandraDialect::parse( "SELECT * FROM t WHERE a = ''" ), $table );
        $this->assertSame( 'scan', $plan['kind'], 'an empty text is no number of the key' );
        $plan = expCassandraDB::plan( expCassandraDialect::parse( 'SELECT * FROM t WHERE 5 = a' ), $table );
        $this->assertSame( 'key', $plan['kind'] );
    }

    /** CB-03 */
    public function testSelectAsSent()
    {
        $t = self::itemTable( self::keyspace( new expCassandraFakeTransport() ) );
        $t->on( '/^SELECT .* FROM "item"/', function ( $cql ) {
            return array( array( 'id' => 1, 'name' => 'a', 'value' => 5, 'section' => 'x' ), array( 'id' => 2, 'name' => 'b', 'value' => 7, 'section' => 'y' ) );
        } );
        $db = expCassandraTestKit::driver( $t, array( 'InListChunkSize' => 2 ), array( 'database' => 'ks' ) );
        $db->arrayQuery( "SELECT name FROM item WHERE value > '4'", array( 'limit' => 1 ) );
        $sent = $t->sent( '/FROM "item"/' );
        $this->assertSame( array( 'SELECT "name" FROM "item" WHERE "value" > ? LIMIT 1 ALLOW FILTERING', array( 4 ) ), end( $sent ) );
        $rows = $db->arrayQuery( "SELECT name FROM item WHERE name LIKE '%b%' ORDER BY name DESC", array( 'limit' => 1 ) );
        $this->assertSame( array( array( 'name' => 'b' ) ), $rows );
        $sent = $t->sent( '/FROM "item"/' );
        $this->assertSame( 'SELECT "name" FROM "item"', end( $sent )[0], 'nothing for the server to filter or limit' );
        $db->arrayQuery( 'SELECT * FROM item WHERE id IN (1, 2, 3)' );
        $chunks = array_slice( $t->sent( '/FROM "item" WHERE "id" IN/' ), -2 );
        $this->assertSame( array( array( 'SELECT * FROM "item" WHERE "id" IN (?, ?)', array( 1, 2 ) ), array( 'SELECT * FROM "item" WHERE "id" IN (?)', array( 3 ) ) ), $chunks );
        $t->on( '/^SELECT COUNT\(\*\) AS n FROM "item"/', array( array( 'n' => 42 ) ) );
        $this->assertSame( array( array( 'n' => '42' ) ), $db->arrayQuery( "SELECT COUNT(*) AS n FROM item WHERE name = 'a'" ) );
        $sent = $t->sent( '/COUNT/' );
        $this->assertSame( array( 'SELECT COUNT(*) AS n FROM "item" WHERE "name" = ?', array( 'a' ) ), end( $sent ) );
        $this->assertSame( 1, $db->Plans['index'] );
        $this->assertSame( array( 'b' ), $db->arrayQuery( 'SELECT name FROM item ORDER BY id', array( 'offset' => 1, 'column' => 'name' ) ) === array( 1 => 'b' ) ? array( 'b' ) : array() );
    }

    /** CB-04 */
    public function testInsert()
    {
        $t = self::itemTable( self::keyspace( new expCassandraFakeTransport() ) );
        $existing = array();
        $t->on( '/^SELECT \* FROM "item" WHERE "id" = \?$/', function ( $cql, $values ) use ( &$existing ) {
            return isset( $existing[$values[0]] ) ? array( $existing[$values[0]] ) : array();
        } );
        $t->on( '/^INSERT INTO "item"/', function ( $cql, $values ) use ( &$existing ) {
            preg_match( '/\(([^)]*)\) VALUES/', $cql, $m );
            $columns = array_map( function ( $c ) { return trim( $c, ' "' ); }, explode( ',', $m[1] ) );
            $row = array_combine( $columns, $values );
            $existing[$row['id']] = $row;
            return array();
        } );
        $db = expCassandraTestKit::driver( $t, array(), array( 'database' => 'ks' ) );
        $this->assertTrue( $db->query( "INSERT INTO item (name) VALUES ('a')" ), $db->errorMessage() );
        $this->assertTrue( $db->query( "INSERT INTO item (id, name, value) VALUES (0, 'b', '3')" ) );
        $this->assertSame( 2, $db->lastSerialID( 'item', 'id' ) );
        $inserts = $t->sent( '/^INSERT INTO "item"/' );
        $this->assertSame( array( 'INSERT INTO "item" ("name", "value", "section", "id") VALUES (?, ?, ?, ?)', array( 'a', 0, 'standard', 1 ) ), $inserts[0] );
        $this->assertSame( array( 2, 'b', 3, 'standard' ), $inserts[1][1] );
        $this->assertCount( 2, $t->sent( '/IF value = \?/' ) );
        // an id given moves the counter past it, at the end of the statement
        $this->assertTrue( $db->query( "INSERT INTO item (id, name) VALUES (50, 'c')" ) );
        $bump = $t->sent( '/IF value = \?/' );
        $this->assertSame( array( 50, 'item.id', 2 ), end( $bump )[1] );
        $this->assertTrue( $db->query( "INSERT INTO item (name) VALUES ('d')" ) );
        $this->assertSame( 51, $db->lastSerialID( 'item' ) );
        // a duplicate
        $this->assertFalse( $db->query( "INSERT INTO item (id, name) VALUES (1, 'again')" ) );
        $this->assertSame( 1062, $db->errorNumber() );
        $this->assertSame( 'duplicate', $db->ErrorCategory );
        $before = count( $t->sent( '/^SELECT \* FROM "item" WHERE/' ) );
        $this->assertTrue( $db->query( "REPLACE INTO item (id, name) VALUES (1, 'over')" ) );
        $this->assertSame( $before, count( $t->sent( '/^SELECT \* FROM "item" WHERE/' ) ), 'REPLACE reads nothing outside a transaction' );
        // lwt
        $t2 = self::itemTable( self::keyspace( new expCassandraFakeTransport() ) );
        $t2->on( '/^INSERT INTO "item" .* IF NOT EXISTS$/', array( array( '[applied]' => false, 'id' => 1 ) ) );
        $lwt = expCassandraTestKit::driver( $t2, array( 'InsertCheck' => 'lwt' ), array( 'database' => 'ks' ) );
        $this->assertFalse( $lwt->query( "INSERT INTO item (id, name) VALUES (1, 'x')" ) );
        $this->assertSame( 1062, $lwt->errorNumber() );
        $this->assertTrue( $lwt->query( "INSERT IGNORE INTO item (id, name) VALUES (1, 'x')" ) );
        $this->assertCount( 0, $t2->sent( '/^SELECT \* FROM "item"/' ), 'lwt asks no lookup' );
    }

    /** CB-05 */
    public function testUpdateAndDelete()
    {
        $t = self::pairTable( self::keyspace( new expCassandraFakeTransport() ) );
        $t->on( '/^SELECT .* FROM "pair"/', array( array( 'object_id' => 7, 'version' => 1, 'title' => 'a' ), array( 'object_id' => 7, 'version' => 2, 'title' => 'b' ) ) );
        $db = expCassandraTestKit::driver( $t, array(), array( 'database' => 'ks' ) );
        $this->assertTrue( $db->query( "UPDATE pair SET title = CONCAT(title, '!') WHERE object_id = 7" ) );
        $updates = $t->sent( '/^UPDATE "pair"/' );
        $this->assertSame( array( array( 'UPDATE "pair" SET "title" = ? WHERE "object_id" = ? AND "version" = ?', array( 'a!', 7, 1 ) ),
                                  array( 'UPDATE "pair" SET "title" = ? WHERE "object_id" = ? AND "version" = ?', array( 'b!', 7, 2 ) ) ), $updates );
        $this->assertSame( 2, $db->AffectedRows );
        $this->assertTrue( $db->query( 'UPDATE pair SET version = 3 WHERE object_id = 7 AND version = 1' ) );
        $this->assertSame( array( 'DELETE FROM "pair" WHERE "object_id" = ? AND "version" = ?', array( 7, 1 ) ), $t->sent( '/^DELETE/' )[0] );
        $this->assertSame( array( 'INSERT INTO "pair" ("object_id", "version", "title") VALUES (?, ?, ?)', array( 7, 3, 'a' ) ), $t->sent( '/^INSERT/' )[0] );
        $reads = count( $t->sent( '/^SELECT/' ) );
        $this->assertTrue( $db->query( 'DELETE FROM pair WHERE object_id = 8 AND version = 1' ) );
        $this->assertSame( $reads, count( $t->sent( '/^SELECT/' ) ), 'the whole key: no read' );
        $this->assertTrue( $db->query( 'DELETE FROM pair WHERE version = 2' ) );
        $deletes = $t->sent( '/^DELETE/' );
        $this->assertSame( array( 7, 2 ), end( $deletes )[1] );
        $this->assertTrue( $db->query( 'DELETE FROM pair' ) );
        $this->assertCount( 1, $t->sent( '/^TRUNCATE "pair"$/' ) );
    }

    /** CB-06 */
    public function testRollback()
    {
        $t = self::pairTable( self::keyspace( new expCassandraFakeTransport() ) );
        $t->on( '/^SELECT \* FROM "pair" WHERE "object_id" = \? AND "version" = \?$/', function ( $cql, $values ) {
            return $values[0] === 7 ? array( array( 'object_id' => 7, 'version' => $values[1], 'title' => 'old' ) ) : array();
        } );
        $db = expCassandraTestKit::driver( $t, array(), array( 'database' => 'ks' ) );
        $db->begin();
        $this->assertTrue( $db->query( "INSERT INTO pair (object_id, version, title) VALUES (9, 1, 'new')" ) );
        $this->assertTrue( $db->query( "UPDATE pair SET title = 'changed' WHERE object_id = 7 AND version = 1" ) );
        $this->assertTrue( $db->query( 'DELETE FROM pair WHERE object_id = 7 AND version = 2' ) );
        $count = count( $t->log );
        $this->assertTrue( $db->rollback() );
        $undo = array_map( function ( $e ) { return array( $e[0], $e[1] ); }, array_slice( $t->log, $count ) );
        $this->assertSame( array(
            array( 'INSERT INTO "pair" ("object_id", "version", "title") VALUES (?, ?, ?)', array( 7, 2, 'old' ) ),
            array( 'INSERT INTO "pair" ("object_id", "version", "title") VALUES (?, ?, ?)', array( 7, 1, 'old' ) ),
            array( 'DELETE FROM "pair" WHERE "object_id" = ? AND "version" = ?', array( 9, 1 ) ),
        ), $undo );
        $db->begin();
        $this->assertTrue( $db->query( "INSERT INTO pair (object_id, version, title) VALUES (10, 1, 'kept')" ) );
        $this->assertTrue( $db->commit() );
        $this->assertSame( array(), $db->Journal );
    }

    /** CB-07 */
    public function testErrors()
    {
        $t = self::itemTable( self::keyspace( new expCassandraFakeTransport() ) );
        $db = expCassandraTestKit::driver( $t, array(), array( 'database' => 'ks' ) );
        $this->assertFalse( $db->arrayQuery( 'SELECT * FROM missing' ) );
        $this->assertSame( 1146, $db->errorNumber() );
        $this->assertFalse( $db->arrayQuery( 'SELECT nope FROM item' ) );
        $this->assertSame( 1054, $db->errorNumber() );
        $this->assertFalse( $db->query( 'UPDATE item SET nope = 1' ) );
        $this->assertFalse( $db->arrayQuery( 'SELECT * FROM item i JOIN pair p ON p.object_id = i.id' ) );
        $this->assertSame( 'unsupported', $db->ErrorCategory );
        $t->on( '/^CREATE TABLE "broken"/', array( 'error' => array( 0x2000, 'line 1:0 no viable alternative' ) ) );
        $this->assertFalse( $db->query( 'CREATE TABLE "broken" (x)' ) );
        $this->assertSame( 'syntax', $db->ErrorCategory );
        $this->assertSame( 0x2000, $db->errorNumber() );
        $this->assertFalse( $db->createTempTable( 'CREATE TEMPORARY TABLE x (id int)' ) );
        $this->assertTrue( $db->query( 'SET NAMES utf8' ), 'session statements of other databases do nothing' );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        $this->expectException( eZDBException::class );
        $db->arrayQuery( 'SELECT * FROM missing' );
    }

    /** CB-08 */
    public function testHelpers()
    {
        $t = self::keyspace( new expCassandraFakeTransport() );
        $db = expCassandraTestKit::driver( $t, array(), array( 'database' => 'ks' ) );
        $this->assertSame( "it''s", $db->escapeString( "it's\0" ) );
        $this->assertSame( 'cassandra', $db->sqlHelperDialect() );
        $this->assertSame( "'a''b'", $db->sqlLiteral( "a'b" ) );
        $this->assertSame( '( a & 4 )', $db->bitAnd( 'a', 4 ) );
        $this->assertSame( 'CONCAT( a, b )', $db->concatString( array( 'a', 'b' ) ) );
        foreach ( array( $db->bitAnd( 'a', 4 ), $db->bitOr( 'a', 4 ), $db->concatString( array( 'a', "'x'" ) ), $db->md5( 'a' ), $db->subString( 'a', 1, 2 ) ) as $expression )
            $this->assertSame( 'select', expCassandraDialect::parse( "SELECT $expression AS e FROM t" )['type'], $expression );
        $this->assertStringStartsWith( 'REPLACE INTO "t"', $db->sqlUpsert( 't', array( 'id' => 1, 'v' => 2 ), array( 'id' ) ) );
        $this->assertSame( "{'class': 'SimpleStrategy', 'replication_factor': 3}", expCassandraDB::replication( array( 'ReplicationStrategy' => 'SimpleStrategy', 'ReplicationFactor' => 3, 'DataCenters' => '' ) ) );
        $this->assertSame( "{'class': 'NetworkTopologyStrategy', 'replication_factor': 2}", expCassandraDB::replication( array( 'ReplicationStrategy' => 'NetworkTopologyStrategy', 'ReplicationFactor' => 2, 'DataCenters' => '' ) ) );
        $this->assertSame( array( '127.0.0.1' ), expCassandraDB::hosts( '' ) );
        $this->assertSame( array( 'tls://n1', '[::1]:9142' ), expCassandraDB::hosts( 'tls://n1 , [::1]:9142' ) );
        $this->assertSame( array( 'tls://n1', 9142 ), expCassandraPHPTransport::splitHost( 'tls://n1:9142', 9042 ) );
        $this->assertSame( array( '[::1]', 9042 ), expCassandraPHPTransport::splitHost( '[::1]', 9042 ) );
        $this->assertMatchesRegularExpression( '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', expCassandraDB::uuid4() );
        $this->assertFalse( $db->lastSerialID() );
        $this->assertSame( array( 'TLS' => 'disabled', 'Compression' => 'disabled' ), array_intersect_key( expCassandraDB::settings(), array( 'TLS' => 1, 'Compression' => 1 ) ) );
        // a data table connection's options
        if ( class_exists( 'expDataConnections' ) )
        {
            $c = array( 'identifier' => 'wide', 'engine' => 'scylla', 'server' => 'n1', 'database' => 'ks', 'options' => array( 'ssl' => 'true', 'consistency' => 'local_one' ) );
            $this->assertSame( 'scylladb', expDataConnections::normalize( $c )['engine'] );
            $plan = expDataConnections::plan( $c, 'pw' );
            if ( expCassandraTransport::availableLayer() === null )
                $this->assertStringContainsString( 'ScyllaDB', $plan['error'] );
            else
            {
                $this->assertSame( array( 'ez', 'scylladb', 9042 ), array( $plan['via'], $plan['implementation'], $plan['parameters']['port'] ) );
                $this->assertSame( array( 'TLS' => 'enabled', 'Consistency' => 'LOCAL_ONE' ), $plan['parameters']['cassandra'] );
            }
        }
    }
}
?>
