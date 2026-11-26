<?php
/**
 * expSQLServerSchema against a real SQL Server (skipped without one, see expSQLServerLiveTest):
 *
 *  SL-01 - The kernel schema (share/db_schema.dba): every table and index made in a fresh database, the kernel's
 *          share/db_data.dba inserted (rows with explicit identity values), the schema read back equal to the .dba
 *  SL-02 - Upgrades: a column added NOT NULL with a default to a table with rows, altered, dropped (with its default
 *          constraint); an index added and dropped
 *  SL-03 - The helpers of every driver on SQL Server: upsert, limit, group concat, timestamps
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__ ) . '/expSQLServerTestKit.php';
require_once dirname( __DIR__, 6 ) . '/lib/ezdbschema/classes/expsqlserverschema.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('sqlserver')]
#[\PHPUnit\Framework\Attributes\Group('sqlserver-live')]
class expSQLServerSchemaLiveTest extends PHPUnit\Framework\TestCase
{
    const DATABASE = 'exp_schema_roundtrip';

    /** @var expSQLServerDB */
    private $db;
    private $saved;

    protected function setUp(): void
    {
        $this->saved = expSQLServerTestKit::quietDebug();
        $master = expSQLServerTestKit::liveDriver( array(), array( 'database' => 'master' ) );
        if ( !$master instanceof expSQLServerDB )
            $this->markTestSkipped( $master );
        $name = self::DATABASE;
        $master->query( "IF DB_ID(N'$name') IS NOT NULL BEGIN ALTER DATABASE [$name] SET SINGLE_USER WITH ROLLBACK IMMEDIATE; DROP DATABASE [$name] END" );
        $this->assertTrue( $master->query( "CREATE DATABASE [$name]" ), (string)$master->errorMessage() );
        $master->close();
        $this->db = expSQLServerTestKit::liveDriver( array(), array( 'database' => $name ) );
        $this->assertInstanceOf( 'expSQLServerDB', $this->db );
    }

    protected function tearDown(): void
    {
        if ( $this->db )
            $this->db->close();
        $master = expSQLServerTestKit::liveDriver( array(), array( 'database' => 'master' ) );
        if ( $master instanceof expSQLServerDB )
        {
            $name = self::DATABASE;
            $master->query( "IF DB_ID(N'$name') IS NOT NULL BEGIN ALTER DATABASE [$name] SET SINGLE_USER WITH ROLLBACK IMMEDIATE; DROP DATABASE [$name] END" );
            $master->close();
        }
        expSQLServerTestKit::restoreDebug( $this->saved );
    }

    public function testKernelSchemaAndDataRoundTrip()
    {
        $root = dirname( __DIR__, 6 );
        $schema = eZDbSchema::read( "$root/share/db_schema.dba" );
        $handler = new expSQLServerSchema( array( 'instance' => $this->db ) );
        foreach ( $schema as $table => $def )
        {
            if ( $table === '_info' )
                continue;
            foreach ( $handler->generateTableSQLList( $table, $def, array(), false ) as $statement )
                $this->assertTrue( $this->db->query( $statement ), $table . ': ' . $this->db->errorMessage() . "\n" . $statement );
        }
        $data = eZDbSchema::read( "$root/share/db_data.dba", true );
        $rows = 0;
        foreach ( $data['data'] as $table => $entries )
        {
            if ( !isset( $entries['rows'], $schema[$table] ) )
                continue;
            foreach ( $handler->generateTableInsertSQLList( $table, $schema[$table], $entries, array(), false ) as $statement )
                $this->assertTrue( $this->db->query( $statement ), $table . ': ' . $this->db->errorMessage() );
            $rows += count( $entries['rows'] );
            $this->assertSame( (string)count( $entries['rows'] ), (string)$this->db->arrayQuery( "SELECT COUNT(*) AS c FROM $table" )[0]['c'], $table );
        }
        $this->assertGreaterThan( 1000, $rows );

        $read = ( new expSQLServerSchema( array( 'instance' => $this->db ) ) )->schema( array( 'format' => 'generic' ) );
        $this->assertSame( array(), eZDbSchemaChecker::diff( $schema, $read ) );
        // and the comparison sees a difference when there is one
        $read['ezuser']['fields']['login']['length'] = 1;
        $this->assertArrayHasKey( 'table_changes', eZDbSchemaChecker::diff( $schema, $read ) );
    }

    public function testUpgradeStatements()
    {
        $h = new expSQLServerSchema( array( 'instance' => $this->db ) );
        $def = array( 'fields' => array( 'id' => array( 'type' => 'auto_increment', 'default' => false ), 'name' => array( 'type' => 'varchar', 'length' => 20, 'default' => '' ) ),
                      'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ) ) );
        foreach ( $h->generateTableSQLList( 'up', $def, array(), false ) as $statement )
            $this->assertTrue( $this->db->query( $statement ) );
        $this->db->query( "INSERT INTO up (name) VALUES ('a'), ('b')" );
        $run = function ( $sql )
        {
            foreach ( preg_split( "/;\n/", trim( $sql ) ) as $statement )
                if ( trim( $statement, " ;\n" ) !== '' )
                    $this->assertTrue( $this->db->query( trim( $statement, " ;\n" ) ), $this->db->errorMessage() . "\n" . $statement );
        };
        $run( $h->generateAddFieldSql( 'up', 'n', array( 'type' => 'int', 'not_null' => '1', 'default' => 7 ), array() ) );
        $this->assertSame( array( '7', '7' ), array_values( $this->db->arrayQuery( 'SELECT n FROM up ORDER BY id', array( 'column' => 'n' ) ) ) );
        $run( $h->generateAlterFieldSql( 'up', 'name', array( 'type' => 'varchar', 'length' => 50, 'not_null' => '1', 'default' => 'z' ), array() ) );
        $run( $h->generateAddIndexSql( 'up', 'up_name', array( 'type' => 'non-unique', 'fields' => array( 'name', 'n' ) ), array() ) );
        $this->assertContains( 'up_name', $this->db->relationList( eZDBInterface::RELATION_INDEX ) );
        $run( $h->generateDropIndexSql( 'up', 'up_name', array( 'type' => 'non-unique' ) ) );
        $run( $h->generateDropFieldSql( 'up', 'n', array() ) );
        $fields = $h->fetchTableFields( 'up', array( 'sort_columns' => true ) );
        $this->assertSame( array( 'id', 'name' ), array_keys( $fields ) );
        $this->assertSame( array( 'type' => 'varchar', 'length' => 50, 'not_null' => '1', 'default' => 'z' ), $fields['name'] );
    }

    public function testHelpersOnSqlServer()
    {
        $db = $this->db;
        $this->assertTrue( $db->query( 'CREATE TABLE kv (name NVARCHAR(20) PRIMARY KEY, value NVARCHAR(MAX), n INT)' ) );
        $this->assertTrue( $db->query( $db->sqlUpsert( 'kv', array( 'name' => 'a', 'value' => 'ä', 'n' => 1 ), array( 'name' ) ) ) );
        $this->assertTrue( $db->query( $db->sqlUpsert( 'kv', array( 'name' => 'a', 'value' => "it's", 'n' => 2 ), array( 'name' ) ) ) );
        $this->assertTrue( $db->query( $db->sqlUpsert( 'kv', array( 'name' => 'b', 'value' => 'x', 'n' => 3 ), array( 'name' ), array() ) ) );
        $this->assertSame( array( "it's", 'x' ), array_values( $db->arrayQuery( 'SELECT value FROM kv ORDER BY name', array( 'column' => 'value' ) ) ) );
        $this->assertSame( 'b', $db->arrayQuery( $db->sqlLimit( 'SELECT name FROM kv ORDER BY name', 1, 1 ) )[0]['name'] );
        $this->assertSame( 'a|b', $db->arrayQuery( 'SELECT ' . $db->sqlGroupConcat( 'name', '|', 'name' ) . ' AS j FROM kv' )[0]['j'] );
        $this->assertSame( '4102444800', $db->arrayQuery( 'SELECT ' . $db->sqlUnixTimestamp( $db->sqlFromUnixTimestamp( '4102444800' ) ) . ' AS t' )[0]['t'] );
    }
}
