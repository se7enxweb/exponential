<?php
/**
 * The Couchbase driver (expCouchbaseDB) and its schema handler over a stand-in for the Query service
 * (expCouchbaseFakeService), without a server:
 *
 *  CD-01 - Connecting: the version asked, bucket.scope and the query context; no database named: not connected
 *  CD-02 - A SELECT: the request (statement, query context, scan consistency), the rows in the select list's order,
 *          a missing field as NULL, true as 1, an object as JSON; column and limit parameters; the translation cached
 *  CD-03 - INSERT: documents with the defaults, keys from the primary key, ids from the counter (made from the
 *          column's highest value when missing), an id given moves the counter, lastSerialID()
 *  CD-04 - INSERT IGNORE and ON DUPLICATE KEY UPDATE: one document at a time, a duplicate key turned into an UPDATE
 *          by key (VALUES() as the row's value) or ignored; a duplicate of a plain INSERT is error 1062
 *  CD-05 - UPDATE of a key column: RETURNING the documents, the moved ones written under their new key
 *  CD-06 - Transactions: BEGIN WORK with the timeout, the txid on each statement, counters and DDL outside it, COMMIT
 *          and ROLLBACK; a ROLLBACK of an ended transaction is no error
 *  CD-07 - CREATE TABLE from the schema handler: collection, primary index and indexes deferred, BUILD INDEX, the
 *          wait for them, the definition kept; the schema read back
 *  CD-08 - ALTER TABLE ADD / DROP COLUMN, DROP TABLE, relationList() without the driver's own collections
 *  CD-09 - An unsupported statement: false with its name (error 1235), an exception with exceptions on
 *  CD-10 - bucketAndScope(), rows(), selectNames(), documentLiteral(), the helpers of the kernel
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expCouchTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('couch')]
class expCouchbaseDBTest extends PHPUnit\Framework\TestCase
{
    private $saved;

    /** @var expCouchbaseFakeService */
    private $service;

    /** @var array table => .dba definition the stand-in reports */
    private $definitions = array();

    protected function setUp(): void
    {
        $this->saved = expCouchTestKit::quietDebug();
        $this->service = new expCouchbaseFakeService();
        $this->definitions = array(
            'ezpreferences' => array( 'fields' => array( 'id' => array( 'type' => 'auto_increment' ), 'user_id' => array( 'type' => 'int', 'default' => 0 ),
                                                         'name' => array( 'type' => 'varchar', 'length' => 100, 'default' => '' ),
                                                         'value' => array( 'type' => 'longtext', 'default' => false ) ),
                                      'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ),
                                                          'ezpreferences_name' => array( 'type' => 'non-unique', 'fields' => array( 'name' ) ) ) ),
        );
        $definitions =& $this->definitions;
        $service = $this->service;
        $this->service->answer = function ( $request ) use ( &$definitions, $service ) {
            // the scope exists, unless a test queued another answer
            if ( strpos( $request['statement'], 'FROM system:scopes' ) !== false && !$service->queue )
                return expCouchbaseFakeService::ok( array( 'site' ) );
            if ( strpos( $request['statement'], 'FROM `exp_couchbase_schema` AS d' ) !== false )
            {
                $rows = array();
                foreach ( $definitions as $name => $definition )
                    $rows[] = array( 'name' => $name, 'definition' => $definition );
                return expCouchbaseFakeService::ok( $rows );
            }
            return null;
        };
    }

    protected function tearDown(): void
    {
        expCouchTestKit::restoreDebug( $this->saved );
    }

    private function db( array $settings = array() )
    {
        return new expCouchbaseDB( expCouchTestKit::parameters( array( 'database' => 'exponential.site', 'couchbase_service' => $this->service,
                                                                       'couchbase' => $settings + array( 'IndexWait' => 1 ) ) ) );
    }

    /** The last statement sent */
    private function last()
    {
        $statements = $this->service->statements();
        return end( $statements );
    }

    /** CD-01 */
    public function testConnect()
    {
        $this->service->queue = array( expCouchbaseFakeService::ok( array( '7.6.2-3721-community' ) ), expCouchbaseFakeService::ok( array( 'site' ) ) );
        $db = $this->db();
        $this->assertTrue( $db->isConnected() );
        $this->assertSame( array( 'exponential', 'site' ), array( $db->bucket(), $db->scope() ) );
        $this->assertSame( 'default:`exponential`.`site`', $db->queryContext() );
        $this->assertSame( 'SELECT RAW ds_version()', $this->service->requests[0]['statement'] );
        $this->assertArrayNotHasKey( 'query_context', $this->service->requests[0] );
        $this->assertStringContainsString( 'FROM system:scopes', $this->service->requests[1]['statement'] );
        $this->assertSame( array( 'exponential', 'site' ), $this->service->requests[1]['args'] );
        $version = $db->databaseServerVersion();
        $this->assertSame( array( '7.6.2', 'Couchbase Server Community' ), array( $version['string'], $version['name'] ) );
        $this->assertSame( array( 'couchbase', 'couchbase' ), array( $db->databaseName(), $db->sqlHelperDialect() ) );
        $none = new expCouchbaseDB( expCouchTestKit::parameters( array( 'couchbase_service' => $this->service ) ) );
        $this->assertFalse( $none->isConnected() );
        // a scope that is missing
        $this->service->queue = array( expCouchbaseFakeService::ok( array( '7.6.2' ) ), expCouchbaseFakeService::ok( array() ) );
        try
        {
            $this->db();
            $this->fail( 'a missing scope is a connection error' );
        }
        catch ( eZDBNoConnectionException $e )
        {
            $this->assertStringContainsString( 'has no scope site', $e->getMessage() );
        }
    }

    /** CD-02 */
    public function testSelect()
    {
        $db = $this->db();
        $this->service->requests = array();
        $this->service->queue = array( expCouchbaseFakeService::ok( array( array( 'value' => 'v', 'id' => 1, 'user_id' => 14, 'name' => true ),
                                                                         array( 'id' => 2, 'name' => array( 'a' => 1 ) ) ) ) );
        $rows = $db->arrayQuery( "SELECT * FROM ezpreferences WHERE user_id = '14'" );
        $request = $this->service->requests[count( $this->service->requests ) - 1];
        $this->assertSame( 'SELECT `ezpreferences`.* FROM `ezpreferences` AS `ezpreferences` WHERE `user_id` = 14', $request['statement'] );
        $this->assertSame( 'default:`exponential`.`site`', $request['query_context'] );
        $this->assertSame( 'request_plus', $request['scan_consistency'] );
        $this->assertSame( array( array( 'id' => 1, 'user_id' => 14, 'name' => 1, 'value' => 'v' ),
                                  array( 'id' => 2, 'user_id' => null, 'name' => '{"a":1}', 'value' => null ) ), $rows );
        $this->service->queue = array( expCouchbaseFakeService::ok( array( array( 'name' => 'a' ), array( 'name' => 'b' ) ) ) );
        $this->assertSame( array( 3 => 'a', 4 => 'b' ), $db->arrayQuery( 'SELECT name FROM ezpreferences', array( 'column' => 'name', 'limit' => 2, 'offset' => 3 ) ) );
        $this->assertStringEndsWith( 'LIMIT 2 OFFSET 3', $this->service->requests[count( $this->service->requests ) - 1]['statement'] );
        // not_bounded when asked
        $db2 = $this->db( array( 'ScanConsistency' => 'not_bounded' ) );
        $db2->arrayQuery( 'SELECT name FROM ezpreferences' );
        $this->assertSame( 'not_bounded', $this->service->requests[count( $this->service->requests ) - 1]['scan_consistency'] );
    }

    /** CD-03 */
    public function testInsert()
    {
        $db = $this->db();
        $this->service->requests = array();
        $calls = 0;
        $previous = $this->service->answer;
        $this->service->answer = function ( $request ) use ( &$calls, $previous ) {
            $s = $request['statement'];
            if ( strpos( $s, 'UPDATE `exp_couchbase_sequence` USE KEYS "ezpreferences" SET v = v + 2' ) === 0 )
                return expCouchbaseFakeService::ok( ++$calls === 1 ? array() : array( 12 ) );
            if ( strpos( $s, 'SELECT RAW MAX(' ) === 0 )
                return expCouchbaseFakeService::ok( array( 10 ) );
            return $previous( $request );
        };
        $this->assertTrue( $db->query( "INSERT INTO ezpreferences (user_id, name) VALUES ('14', 'a'), (15, 'b')" ) );
        $statements = $this->service->statements();
        $this->assertContains( 'CREATE COLLECTION `exp_couchbase_sequence` IF NOT EXISTS', $statements );
        $this->assertContains( 'INSERT INTO `exp_couchbase_sequence` (KEY, VALUE) VALUES ("ezpreferences", {"v": 10})', $statements );
        $this->assertSame( 'INSERT INTO `ezpreferences` (KEY, VALUE) VALUES ("11", {"id":11,"user_id":14,"name":"a","value":null}), '
                           . '("12", {"id":12,"user_id":15,"name":"b","value":null})', end( $statements ) );
        $this->assertSame( 12, $db->lastSerialID( 'ezpreferences', 'id' ) );
        // counters are moved outside a transaction
        foreach ( $this->service->requests as $r )
            if ( strpos( $r['statement'], 'exp_couchbase_sequence' ) !== false )
                $this->assertArrayNotHasKey( 'txid', $r );
        // an id given moves the counter past it
        $this->service->requests = array();
        $this->assertTrue( $db->query( "INSERT INTO ezpreferences (id, name) VALUES (40, 'c')" ) );
        $this->assertSame( 'UPDATE `exp_couchbase_sequence` USE KEYS "ezpreferences" SET v = GREATEST(v, 40) RETURNING RAW v', $this->service->statements()[0] );
        $this->assertSame( 40, $db->lastSerialID( 'ezpreferences' ) );
        // an expression is kept as SQL++ in the document
        $this->service->requests = array();
        $this->assertTrue( $db->query( "INSERT INTO ezpreferences (id, name, value) VALUES (41, CONCAT('a', 'b'), 'x')" ) );
        $this->assertSame( 'INSERT INTO `ezpreferences` (KEY, VALUE) VALUES ("41", {"id": 41, "user_id": 0, "name": CONCAT("a", "b"), "value": "x"})', $this->last() );
    }

    /** CD-04 */
    public function testIgnoreAndOnDuplicate()
    {
        $db = $this->db();
        $previous = $this->service->answer;
        $this->service->answer = function ( $request ) use ( $previous ) {
            if ( strpos( $request['statement'], 'INSERT INTO `ezpreferences` (KEY, VALUE) VALUES ("5"' ) === 0 )
                return expCouchbaseFakeService::error( 12009, 'DML Error, possible causes include concurrent modification. Failed to perform INSERT on key 5 - cause: Duplicate Key: 5' );
            if ( strpos( $request['statement'], 'GREATEST' ) !== false )
                return expCouchbaseFakeService::ok( array( 5 ) );
            return $previous( $request );
        };
        $this->service->requests = array();
        $this->assertTrue( $db->query( "INSERT INTO ezpreferences (id, name) VALUES (5, 'n') ON DUPLICATE KEY UPDATE name = VALUES(name), user_id = user_id + 1" ) );
        $this->assertSame( 'UPDATE `ezpreferences` USE KEYS "5" SET `ezpreferences`.`name` = "n", `ezpreferences`.`user_id` = `user_id` + 1', $this->last() );
        $this->service->requests = array();
        $this->assertTrue( $db->query( "INSERT IGNORE INTO ezpreferences (id, name) VALUES (5, 'n')" ) );
        $this->assertStringStartsWith( 'INSERT INTO', $this->last() );
        $this->assertFalse( $db->query( "INSERT INTO ezpreferences (id, name) VALUES (5, 'n')" ) );
        $this->assertSame( 1062, $db->errorNumber() );
        // the portable upsert of the helpers says the same
        $this->assertTrue( $db->query( $db->sqlUpsert( 'ezpreferences', array( 'id' => 5, 'name' => 'm' ), array( 'id' ) ) ) );
        $this->assertSame( 'UPDATE `ezpreferences` USE KEYS "5" SET `ezpreferences`.`name` = "m"', $this->last() );
    }

    /** CD-05 */
    public function testKeyChange()
    {
        $db = $this->db();
        $previous = $this->service->answer;
        $this->service->answer = function ( $request ) use ( $previous ) {
            if ( strpos( $request['statement'], 'RETURNING META(' ) !== false )
                return expCouchbaseFakeService::ok( array( array( '_k' => '3', 'id' => 30, 'user_id' => 1, 'name' => 'x', 'value' => null ) ) );
            return $previous( $request );
        };
        $this->service->requests = array();
        $this->assertTrue( $db->query( 'UPDATE ezpreferences SET id = 30 WHERE id = 3' ) );
        $this->assertSame( array( 'UPDATE `ezpreferences` AS `ezpreferences` USE KEYS ["3"] SET `ezpreferences`.`id` = 30 WHERE `id` = 3 RETURNING META(`ezpreferences`).id AS _k, `ezpreferences`.* ',
                                  'INSERT INTO `ezpreferences` (KEY, VALUE) VALUES ("30", {"id":30,"user_id":1,"name":"x","value":null})',
                                  'DELETE FROM `ezpreferences` USE KEYS "3"' ), array_slice( $this->service->statements(), -3 ) );
    }

    /** CD-06 */
    public function testTransactions()
    {
        $db = $this->db( array( 'TransactionTimeout' => '30s', 'DurabilityLevel' => 'none' ) );
        $this->service->queue = array( expCouchbaseFakeService::ok( array( array( 'txid' => 'tx-1' ) ) ) );
        $this->service->requests = array();
        $db->begin();
        $this->assertSame( 'tx-1', $db->transactionId() );
        $this->assertSame( array( 'BEGIN WORK', '30s', 'none' ), array( $this->service->requests[0]['statement'], $this->service->requests[0]['txtimeout'],
                                                                        $this->service->requests[0]['durability_level'] ) );
        $db->query( "UPDATE ezpreferences SET name = 'x' WHERE id = 1" );
        $this->assertSame( 'tx-1', end( $this->service->requests )['txid'] );
        $this->assertArrayNotHasKey( 'scan_consistency', end( $this->service->requests ) );
        $db->commit();
        $this->assertSame( 'COMMIT WORK', end( $this->service->requests )['statement'] );
        $this->assertNull( $db->transactionId() );
        $this->service->queue = array( expCouchbaseFakeService::ok( array( array( 'txid' => 'tx-2' ) ) ) );
        $db->begin();
        $this->service->queue = array( expCouchbaseFakeService::error( 17004, 'Transaction context error' ) );
        $db->rollback();
        $this->assertSame( array( 'ROLLBACK WORK', 'tx-2' ), array( end( $this->service->requests )['statement'], end( $this->service->requests )['txid'] ) );
        $this->assertSame( 0, $db->errorNumber() );
    }

    /** CD-07 */
    public function testCreateTable()
    {
        $db = $this->db();
        $previous = $this->service->answer;
        $this->service->answer = function ( $request ) use ( $previous ) {
            if ( strpos( $request['statement'], 'SELECT RAW name FROM system:keyspaces' ) === 0 )
                return expCouchbaseFakeService::ok( array( $request['args'][2] ) );
            if ( strpos( $request['statement'], 'SELECT RAW state FROM system:indexes' ) === 0 )
                return expCouchbaseFakeService::ok( array( 'online', 'online', 'online' ) );
            return $previous( $request );
        };
        $handler = new expCouchbaseSchema( array( 'instance' => $db ) );
        $definition = array( 'fields' => array( 'id' => array( 'type' => 'auto_increment', 'default' => false ), 'title' => array( 'type' => 'varchar', 'length' => 50, 'not_null' => '1', 'default' => "it's" ),
                                                'n' => array( 'type' => 'int', 'default' => 0 ) ),
                             'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ), 't_title' => array( 'type' => 'non-unique', 'fields' => array( 'title', 'n' ) ) ) );
        $list = $handler->generateTableSQLList( 't', $definition, array(), false );
        $this->assertSame( array( "CREATE TABLE t (\n  id int AUTO_INCREMENT,\n  title varchar(50) NOT NULL DEFAULT 'it\\'s',\n  n int DEFAULT 0,\n  PRIMARY KEY (id),\n  KEY t_title (title, n)\n)" ), $list );
        $this->service->requests = array();
        $this->assertTrue( $db->query( $list[0] ), $db->errorMessage() );
        $s = $this->service->statements();
        foreach ( array( 'CREATE COLLECTION `t` IF NOT EXISTS', 'CREATE PRIMARY INDEX IF NOT EXISTS ON `t` WITH {"defer_build": true}',
                         'CREATE INDEX `pk_t` IF NOT EXISTS ON `t`(`id`) WITH {"defer_build": true}',
                         'CREATE INDEX `t_title` IF NOT EXISTS ON `t`(`title`, `n`) WITH {"defer_build": true}', 'BUILD INDEX ON `t`(`#primary`, `pk_t`, `t_title`)' ) as $statement )
            $this->assertContains( $statement, $s );
        $kept = end( $s );
        $this->assertStringStartsWith( 'UPSERT INTO `exp_couchbase_schema` (KEY, VALUE) VALUES ("t", {"definition":', $kept );
        $this->assertStringContainsString( '"default":"it\'s"', $kept );
        $this->assertStringContainsString( '"type":"auto_increment"', $kept );
        $this->assertStringEndsWith( "KEY t_title (title, n)\n);\n", $handler->generateTableSchema( 't', $definition, array() ) );
        // the schema read back
        $this->service->answer = function ( $request ) use ( $previous ) {
            if ( strpos( $request['statement'], 'SELECT RAW name FROM system:keyspaces' ) === 0 )
                return expCouchbaseFakeService::ok( array( 'ezpreferences', 'exp_couchbase_schema' ) );
            return $previous( $request );
        };
        $schema = ( new expCouchbaseSchema( array( 'instance' => $db ) ) )->schema();
        unset( $schema['_info'] );
        $this->assertSame( array( 'ezpreferences' ), array_keys( $schema ) );
        $this->assertSame( array( 'id', 'name', 'user_id', 'value' ), array_keys( $schema['ezpreferences']['fields'] ) );
    }

    /** CD-08 */
    public function testAlterDropAndRelations()
    {
        $db = $this->db();
        $this->service->requests = array();
        $this->assertTrue( $db->query( "ALTER TABLE ezpreferences ADD COLUMN note varchar(10) DEFAULT 'n'" ) );
        $s = $this->service->statements();
        $this->assertStringContainsString( '"note":{"type":"varchar","not_null":"0","default":"n","length":10}', $s[count( $s ) - 2] );
        $this->assertSame( 'UPDATE `ezpreferences` AS t SET t.`note` = "n" WHERE t.`note` IS MISSING', end( $s ) );
        $this->assertTrue( $db->query( 'ALTER TABLE ezpreferences DROP COLUMN note' ) );
        $this->assertContains( 'UPDATE `ezpreferences` AS t UNSET t.`note` WHERE t.`note` IS NOT MISSING', $this->service->statements() );
        $this->service->requests = array();
        $this->assertTrue( $db->query( 'DROP TABLE IF EXISTS ezpreferences' ) );
        $this->assertSame( array( 'DROP COLLECTION `ezpreferences` IF EXISTS', 'DELETE FROM `exp_couchbase_schema` USE KEYS "ezpreferences"',
                                  'DELETE FROM `exp_couchbase_sequence` USE KEYS "ezpreferences"' ), $this->service->statements() );
        $this->service->queue = array( expCouchbaseFakeService::ok( array( 'a', 'exp_couchbase_schema', 'exp_couchbase_sequence', '_default', 'ezb' ) ) );
        $this->assertSame( array( 'a', 'ezb' ), $db->relationList() );
        $this->service->queue = array( expCouchbaseFakeService::ok( array( 'a', 'ezb', 'expx' ) ) );
        $this->assertSame( array( 'ezb' => eZDBInterface::RELATION_TABLE, 'expx' => eZDBInterface::RELATION_TABLE ), $db->eZTableList() );
    }

    /** CD-09 */
    public function testUnsupported()
    {
        $db = $this->db();
        $this->assertFalse( $db->arrayQuery( 'SELECT MD5(name) FROM ezpreferences' ) );
        $this->assertSame( 1235, $db->errorNumber() );
        $this->assertStringContainsString( 'MD5 of a column', $db->errorMessage() );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        $this->expectException( eZDBException::class );
        $db->arrayQuery( 'SELECT a.id FROM ezpreferences a RIGHT JOIN b ON a.id = b.id' );
    }

    /** CD-10 */
    public function testPureHelpers()
    {
        $this->assertSame( array( 'b', 's' ), expCouchbaseDB::bucketAndScope( 'b.s' ) );
        $this->assertSame( array( 'b', 's' ), expCouchbaseDB::bucketAndScope( 'b/s' ) );
        $this->assertSame( array( 'b', '_default' ), expCouchbaseDB::bucketAndScope( 'b' ) );
        $this->assertSame( array( 'b', 'x' ), expCouchbaseDB::bucketAndScope( 'b', 'x' ) );
        $this->assertSame( array( array( 0 => 5 ) ), expCouchbaseDB::rows( array( 5 ), null ) );
        $this->assertSame( array( 'a', 'b c' ), expCouchbaseDB::selectNames( 'SELECT 1 AS `a`, `x`.`y` AS `b c` FROM `t` AS `x`' ) );
        $this->assertSame( '{"a":1,"b":1.0,"c":null}', expCouchbaseDB::documentLiteral( array( 'a' => 1, 'b' => 1.0, 'c' => null ) ) );
        $this->assertMatchesRegularExpression( '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', expCouchbaseDB::uuid() );
        $db = $this->db();
        $this->assertSame( "it\\'s \\\\", $db->escapeString( "it's \\" ) );
        $this->assertSame( ' BITAND( a, 1 ) ', $db->bitAnd( 'a', 1 ) );
        $this->assertSame( ' CONCAT( a, b ) ', $db->concatString( array( 'a', 'b' ) ) );
        $this->assertSame( 'expCouchbaseDB', get_class( $db ) );
    }
}
?>
