<?php
/**
 * expHANASchema against a real SAP HANA (skipped without one, see expHANATestKit::live()). The test schema is emptied
 * first: point EXP_TEST_HANA_SCHEMA at a schema of its own.
 *
 *  HS-01 - The kernel schema (share/db_schema.dba): every table, index, sequence and trigger made in an empty schema,
 *          the kernel's share/db_data.dba inserted (rows with explicit ids), the schema read back equal to the .dba
 *  HS-02 - Ids after the data: a row without id gets the next number past the highest id of the data (the trigger
 *          moved the sequence), lastSerialID() is that id
 *  HS-03 - Upgrades: a column added NOT NULL with a default to a table with rows, altered, dropped; an index added
 *          and dropped
 *  HS-04 - The helpers of every driver on HANA: upsert, limit, group concat, timestamps, md5
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__ ) . '/expHANATestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('hana')]
#[\PHPUnit\Framework\Attributes\Group('hana-live')]
class expHANASchemaLiveTest extends PHPUnit\Framework\TestCase
{
    /** @var expHANADB */
    private $db;

    private $saved;

    protected function setUp(): void
    {
        $this->saved = expHANATestKit::quietDebug();
        $db = expHANATestKit::liveDriver();
        if ( !$db instanceof expHANADB )
            $this->markTestSkipped( $db );
        $this->db = $db;
        expHANATestKit::emptySchema( $this->db );
    }

    protected function tearDown(): void
    {
        if ( $this->db )
        {
            expHANATestKit::emptySchema( $this->db );
            $this->db->close();
        }
        expHANATestKit::restoreDebug( $this->saved );
    }

    /**
     * Runs the statements of an upgrade (";\n" separated; a trigger body ends with a line "END;").
     */
    private function runScript( $sql )
    {
        $statements = array();
        $current = '';
        foreach ( explode( "\n", trim( $sql ) ) as $line )
        {
            $current .= ( $current === '' ? '' : "\n" ) . $line;
            if ( substr( rtrim( $line ), -1 ) === ';' && stripos( $current, 'CREATE TRIGGER' ) === false || rtrim( $line ) === 'END;' )
            {
                $statements[] = rtrim( rtrim( $current ), ';' );
                $current = '';
            }
        }
        if ( trim( $current ) !== '' )
            $statements[] = $current;
        foreach ( $statements as $statement )
            $this->assertTrue( $this->db->query( $statement ), (string)$this->db->errorMessage() . "\n" . $statement );
    }

    public function testKernelSchemaAndDataRoundTrip()
    {
        $root = dirname( __DIR__, 6 );
        $schema = eZDbSchema::read( "$root/share/db_schema.dba" );
        $handler = new expHANASchema( array( 'instance' => $this->db ) );
        foreach ( $schema as $table => $def )
        {
            if ( $table === '_info' )
                continue;
            foreach ( $handler->generateTableSQLList( $table, $def, array(), false ) as $statement )
                $this->assertTrue( $this->db->query( $statement ), $table . ': ' . (string)$this->db->errorMessage() . "\n" . $statement );
        }
        $data = eZDbSchema::read( "$root/share/db_data.dba", true );
        $rows = 0;
        $this->db->begin();
        foreach ( $data['data'] as $table => $entries )
        {
            if ( !isset( $entries['rows'], $schema[$table] ) )
                continue;
            foreach ( $handler->generateTableInsertSQLList( $table, $schema[$table], $entries, array(), false ) as $statement )
                $this->assertTrue( $this->db->query( $statement ), $table . ': ' . (string)$this->db->errorMessage() );
            $rows += count( $entries['rows'] );
            $this->assertSame( (string)count( $entries['rows'] ), (string)$this->db->arrayQuery( "SELECT COUNT(*) AS c FROM $table" )[0]['c'], $table );
        }
        $this->db->commit();
        $this->assertGreaterThan( 500, $rows );

        // HS-02: the next id comes after the data's (an insert with its own id below the sequence takes a number of
        // it, so there may be a gap)
        $max = (int)$this->db->arrayQuery( 'SELECT MAX(id) AS m FROM ezcontentobject' )[0]['m'];
        $this->assertTrue( $this->db->query( "INSERT INTO ezcontentobject (name, remote_id) VALUES ('x', 'hs02')" ), (string)$this->db->errorMessage() );
        $id = (int)$this->db->arrayQuery( "SELECT id FROM ezcontentobject WHERE remote_id = 'hs02'" )[0]['id'];
        $this->assertGreaterThan( $max, $id );
        $this->assertSame( $id, $this->db->lastSerialID( 'ezcontentobject', 'id' ) );
        $this->assertSame( $id, $this->db->lastSerialID() );
        $this->db->query( "DELETE FROM ezcontentobject WHERE remote_id = 'hs02'" );

        $read = ( new expHANASchema( array( 'instance' => $this->db ) ) )->schema( array( 'format' => 'generic' ) );
        $this->assertSame( array(), eZDbSchemaChecker::diff( $schema, $read ) );
        $read['ezuser']['fields']['login']['length'] = 1;
        $this->assertArrayHasKey( 'table_changes', eZDbSchemaChecker::diff( $schema, $read ) );
    }

    public function testUpgradeStatements()
    {
        $h = new expHANASchema( array( 'instance' => $this->db ) );
        $def = array( 'fields' => array( 'id' => array( 'type' => 'auto_increment', 'default' => false ), 'name' => array( 'type' => 'varchar', 'length' => 20, 'default' => '' ) ),
                      'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ) ) );
        foreach ( $h->generateTableSQLList( 'up', $def, array(), false ) as $statement )
            $this->assertTrue( $this->db->query( $statement ), (string)$this->db->errorMessage() );
        $this->assertTrue( $this->db->query( "INSERT INTO up (name) VALUES ('a')" ) );
        $this->assertTrue( $this->db->query( "INSERT INTO up (name) VALUES ('b')" ) );
        $this->runScript( $h->generateAddFieldSql( 'up', 'n', array( 'type' => 'int', 'not_null' => '1', 'default' => 7 ), array() ) );
        $this->assertSame( array( '7', '7' ), array_values( $this->db->arrayQuery( 'SELECT n FROM up ORDER BY id', array( 'column' => 'n' ) ) ) );
        $this->runScript( $h->generateAlterFieldSql( 'up', 'name', array( 'type' => 'varchar', 'length' => 50, 'not_null' => '1', 'default' => 'z' ), array() ) );
        $this->runScript( $h->generateAddIndexSql( 'up', 'up_name', array( 'type' => 'non-unique', 'fields' => array( 'name', 'n' ) ), array() ) );
        $this->assertContains( 'up$up_name', $this->db->relationList( eZDBInterface::RELATION_INDEX ) );
        $this->assertArrayHasKey( 'up_name', $h->fetchTableIndexes( 'up', array() ) );
        $this->runScript( $h->generateDropIndexSql( 'up', 'up_name', array( 'type' => 'non-unique' ) ) );
        $this->runScript( $h->generateDropFieldSql( 'up', 'n', array() ) );
        $fields = $h->fetchTableFields( 'up', array( 'sort_columns' => true ) );
        $this->assertSame( array( 'id', 'name' ), array_keys( $fields ) );
        $this->assertSame( array( 'type' => 'varchar', 'length' => 50, 'not_null' => '1', 'default' => 'z' ), $fields['name'] );
        $this->assertSame( 'auto_increment', $fields['id']['type'] );
    }

    public function testHelpersOnHANA()
    {
        $db = $this->db;
        $this->assertTrue( $db->query( 'CREATE COLUMN TABLE kv (name NVARCHAR(20) NOT NULL PRIMARY KEY, value NCLOB, n INTEGER)' ), (string)$db->errorMessage() );
        $this->assertTrue( $db->query( $db->sqlUpsert( 'kv', array( 'name' => 'a', 'value' => 'ä', 'n' => 1 ), array( 'name' ) ) ), (string)$db->errorMessage() );
        $this->assertTrue( $db->query( $db->sqlUpsert( 'kv', array( 'name' => 'a', 'value' => "it's", 'n' => 2 ), array( 'name' ) ) ), (string)$db->errorMessage() );
        $this->assertTrue( $db->query( $db->sqlUpsert( 'kv', array( 'name' => 'b', 'value' => 'x', 'n' => 3 ), array( 'name' ), array() ) ), (string)$db->errorMessage() );
        $this->assertTrue( $db->query( $db->sqlUpsert( 'kv', array( 'name' => 'b', 'value' => 'y', 'n' => 4 ), array( 'name' ), array() ) ), (string)$db->errorMessage() );
        $this->assertSame( array( "it's", 'x' ), array_values( $db->arrayQuery( 'SELECT value FROM kv ORDER BY name', array( 'column' => 'value' ) ) ) );
        $this->assertSame( 'b', $db->arrayQuery( $db->sqlLimit( 'SELECT name FROM kv ORDER BY name', 1, 1 ) )[0]['name'] );
        $this->assertSame( 'a|b', $db->arrayQuery( 'SELECT ' . $db->sqlGroupConcat( 'name', '|', 'name' ) . ' AS j FROM kv' )[0]['j'] );
        $this->assertSame( '4102444800', $db->arrayQuery( 'SELECT ' . $db->sqlUnixTimestamp( $db->sqlFromUnixTimestamp( '4102444800' ) ) . ' AS t' )[0]['t'] );
        $now = (int)$db->arrayQuery( 'SELECT ' . $db->sqlUnixTimestamp() . ' AS t' )[0]['t'];
        $this->assertLessThan( 120, abs( $now - time() ) );
        $this->assertSame( md5( 'ä€' ), $db->arrayQuery( 'SELECT ' . $db->md5( "'ä€'" ) . ' AS m' )[0]['m'] );
        $this->assertSame( '2', $db->arrayQuery( $db->sqlForUpdate( "SELECT n FROM kv WHERE name = 'a'" ) )[0]['n'] );
    }
}
