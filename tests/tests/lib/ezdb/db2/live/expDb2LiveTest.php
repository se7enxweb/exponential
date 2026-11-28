<?php
/**
 * expDb2DB and expInformixDB against real servers (skipped without them, see expDb2TestKit::live()): set
 * EXP_TEST_DB2_HOST ... for Db2 and EXP_TEST_INFORMIX_HOST ... for Informix. The test databases are emptied: give
 * each a database of its own.
 *
 *  DL-01 - New ids: a row without id, with NULL and with 0 gets the next number; lastSerialID() per table and per
 *          connection (another connection's insert does not change it)
 *  DL-02 - Paging and the kernel's words on the server: LIMIT, arrayQuery()'s offset, LENGTH and SUBSTRING in
 *          characters, CONCAT, MD5, the bit operators, LIKE with a backslash, a SELECT without FROM
 *  DL-03 - Transactions: rollback undoes, nested commit keeps; FOR UPDATE locks the row for another connection
 *  DL-04 - Text: Unicode kept, a text of 40,000 bytes (a bound parameter), a CLOB compared with =
 *  DL-05 - Temporary tables: rows belong to the connection; the kernel's create, insert, read, drop twice
 *  DL-06 - The kernel schema and data (share/db_schema.dba, share/db_data.dba), the next id after the data, the
 *          schema read back (equal on Db2), upgrades, the portable helpers
 *  DL-07 - Memory: 2,000 reads of 10 columns keep less than 256 KB (pdo_ibm's closeCursor() kept every result's
 *          column descriptors, ibm_db2's DB2_ATTR_CASE every column name)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__ ) . '/expDb2TestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('db2')]
#[\PHPUnit\Framework\Attributes\Group('db2-live')]
class expDb2LiveTest extends PHPUnit\Framework\TestCase
{
    /** @var expDb2DB */
    private $db;

    private $saved;

    public static function engines()
    {
        return array( 'Db2' => array( 'expDb2DB' ), 'Informix' => array( 'expInformixDB' ) );
    }

    protected function setUp(): void
    {
        $this->saved = expDb2TestKit::quietDebug();
    }

    protected function tearDown(): void
    {
        if ( $this->db )
        {
            expDb2TestKit::emptyDatabase( $this->db );
            $this->db->close();
        }
        expDb2TestKit::restoreDebug( $this->saved );
    }

    /**
     * @param string $class
     * @return expDb2DB
     */
    private function connect( $class )
    {
        $db = expDb2TestKit::liveDriver( array(), array(), $class );
        if ( !$db instanceof expDb2DB )
            $this->markTestSkipped( $db );
        $this->db = $db;
        expDb2TestKit::emptyDatabase( $db );
        return $db;
    }

    private function handler( expDb2DB $db )
    {
        return $db instanceof expInformixDB ? new expInformixSchema( array( 'instance' => $db ) ) : new expDb2Schema( array( 'instance' => $db ) );
    }

    private function makeTable( expDb2DB $db )
    {
        $def = array( 'fields' => array( 'id' => array( 'type' => 'auto_increment', 'default' => false ),
                                         'name' => array( 'type' => 'varchar', 'length' => 100, 'not_null' => '1', 'default' => '' ),
                                         'mask' => array( 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
                                         'value' => array( 'type' => 'longtext' ) ),
                      'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ) ) );
        foreach ( $this->handler( $db )->generateTableSQLList( 'exp_live', $def, array(), false ) as $sql )
            $this->assertTrue( $db->query( $sql ), (string)$db->errorMessage() . "\n" . $sql );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('engines')]
    public function testIdsAndPaging( $class )
    {
        $db = $this->connect( $class );
        $this->makeTable( $db );
        $this->assertTrue( $db->query( "INSERT INTO exp_live (name, mask) VALUES ('a', 6)" ), (string)$db->errorMessage() );
        $this->assertSame( 1, $db->lastSerialID( 'exp_live', 'id' ) );
        $this->assertTrue( $db->query( "INSERT INTO exp_live (id, name, mask) VALUES (0, 'b', 1)" ), (string)$db->errorMessage() );
        $this->assertSame( 2, $db->lastSerialID( 'exp_live', 'id' ) );
        $this->assertSame( 2, $db->lastSerialID() );
        if ( !$db instanceof expInformixDB )
        {
            $this->assertTrue( $db->query( "INSERT INTO exp_live (id, name) VALUES (NULL, 'c')" ), (string)$db->errorMessage() );
            $this->assertSame( 3, $db->lastSerialID( 'exp_live' ) );
        }
        else
            $this->assertTrue( $db->query( "INSERT INTO exp_live (name) VALUES ('c')" ) );

        // another connection's insert leaves this connection's id alone
        $other = expDb2TestKit::liveDriver( array(), array(), $class );
        $this->assertTrue( $other->query( "INSERT INTO exp_live (name) VALUES ('d')" ), (string)$other->errorMessage() );
        $this->assertSame( 4, $other->lastSerialID( 'exp_live' ) );
        $this->assertSame( 3, $db->lastSerialID( 'exp_live' ) );
        $other->close();

        $this->assertSame( array( 'b', 'c' ), array_values( $db->arrayQuery( 'SELECT name FROM exp_live ORDER BY id LIMIT 2 OFFSET 1', array( 'column' => 'name' ) ) ) );
        $this->assertSame( array( 2 => 'c', 3 => 'd' ), $db->arrayQuery( 'SELECT name FROM exp_live ORDER BY id', array( 'offset' => 2, 'limit' => 5, 'column' => 'name' ) ) );
        $this->assertSame( '1', $db->arrayQuery( "SELECT COUNT(*) AS c FROM exp_live WHERE exp_live.mask & 4 > 0" )[0]['c'] );
        $this->assertSame( '3', $db->arrayQuery( "SELECT COUNT(*) AS c FROM exp_live WHERE " . $db->bitAnd( 'mask', 3 ) . ' > 0 OR name = \'d\'' )[0]['c'] );
        $row = $db->arrayQuery( "SELECT LENGTH('äöü') AS l, SUBSTRING('äöüx', 2, 2) AS s, CONCAT('a', 'b', 'c') AS c, MD5('abc') AS m" )[0];
        $this->assertSame( array( 'l' => '3', 's' => 'öü', 'c' => 'abc', 'm' => md5( 'abc' ) ), $row );
        $this->assertTrue( $db->query( "UPDATE exp_live SET name = 'a_b' WHERE id = 1" ) );
        $this->assertSame( array( 'a_b' ), array_values( $db->arrayQuery( "SELECT name FROM exp_live WHERE name LIKE 'a\\_%'", array( 'column' => 'name' ) ) ) );
        $this->assertSame( array( '1' ), array_values( $db->arrayQuery( "SELECT id FROM exp_live WHERE name = 'a_b' FOR UPDATE", array( 'column' => 'id' ) ) ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('engines')]
    public function testTransactionsTextAndTemporaryTables( $class )
    {
        $db = $this->connect( $class );
        $this->makeTable( $db );
        $db->begin();
        $this->assertTrue( $db->query( "INSERT INTO exp_live (name) VALUES ('gone')" ) );
        $db->rollback();
        $this->assertSame( '0', $db->arrayQuery( 'SELECT COUNT(*) AS c FROM exp_live' )[0]['c'] );
        $db->begin();
        $db->begin();
        $this->assertTrue( $db->query( "INSERT INTO exp_live (name) VALUES ('kept')" ) );
        $db->commit();
        $db->commit();
        $this->assertSame( '1', $db->arrayQuery( 'SELECT COUNT(*) AS c FROM exp_live' )[0]['c'] );

        $text = str_repeat( "Grüße 漢字 it's \\ ", 2000 );
        $text = substr( $text, 0, $db instanceof expInformixDB ? 15000 : 30000 );
        $text = mb_substr( $text, 0, mb_strlen( $text ) - 1 );
        $this->assertTrue( $db->query( "UPDATE exp_live SET value = '" . $db->escapeString( $text ) . "'" ), (string)$db->errorMessage() );
        $this->assertSame( $text, $db->arrayQuery( 'SELECT value FROM exp_live' )[0]['value'] );
        $this->assertSame( '1', $db->arrayQuery( "SELECT COUNT(*) AS c FROM exp_live WHERE value LIKE 'Grüße%'" )[0]['c'] );

        foreach ( array( 1, 2 ) as $round )
        {
            $name = $db->generateUniqueTempTableName( 'exp_tmp_%' );
            $this->assertTrue( $db->createTempTable( "CREATE TEMPORARY TABLE $name ( id int primary key not null, t varchar(20) )" ), (string)$db->errorMessage() );
            $this->assertTrue( $db->query( "INSERT INTO $name (id, t) VALUES ($round, 'x')" ), (string)$db->errorMessage() );
            $this->assertSame( array( (string)$round ), array_values( $db->arrayQuery( "SELECT id FROM $name", array( 'column' => 'id' ) ) ) );
            $db->dropTempTableList( array( $name ) );
        }
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('engines')]
    public function testKernelSchemaDataAndHelpers( $class )
    {
        $db = $this->connect( $class );
        $root = dirname( __DIR__, 6 );
        $schema = eZDbSchema::read( "$root/share/db_schema.dba" );
        $handler = $this->handler( $db );
        foreach ( $schema as $table => $def )
        {
            if ( $table === '_info' )
                continue;
            foreach ( $handler->generateTableSQLList( $table, $def, array(), false ) as $statement )
                $this->assertTrue( $db->query( $statement ), $table . ': ' . (string)$db->errorMessage() . "\n" . $statement );
        }
        $data = eZDbSchema::read( "$root/share/db_data.dba", true );
        $rows = 0;
        $db->begin();
        foreach ( $data['data'] as $table => $entries )
        {
            if ( !isset( $entries['rows'], $schema[$table] ) )
                continue;
            foreach ( $handler->generateTableInsertSQLList( $table, $schema[$table], $entries, array(), false ) as $statement )
                $this->assertTrue( $db->query( $statement ), $table . ': ' . (string)$db->errorMessage() );
            $rows += count( $entries['rows'] );
        }
        $db->commit();
        $this->assertTrue( $db->correctSequenceValues() );
        $this->assertGreaterThan( 500, $rows );

        $max = (int)$db->arrayQuery( 'SELECT MAX(id) AS m FROM ezcontentobject' )[0]['m'];
        $this->assertTrue( $db->query( "INSERT INTO ezcontentobject (name, remote_id) VALUES ('x', 'dl06')" ), (string)$db->errorMessage() );
        $this->assertSame( $max + 1, $db->lastSerialID( 'ezcontentobject', 'id' ) );

        $read = $handler->schema( array( 'format' => 'generic' ) );
        if ( !$db instanceof expInformixDB )
            $this->assertSame( array(), eZDbSchemaChecker::diff( $schema, $read ) );
        $expected = array_keys( array_diff_key( $schema, array( '_info' => 1 ) ) );
        $tables = array_keys( array_diff_key( $read, array( '_info' => 1 ) ) );
        sort( $expected );
        sort( $tables );
        $this->assertSame( array(), array_merge( array_diff( $expected, $tables ), array_diff( $tables, $expected ) ) );

        // upgrades on a table with rows
        $this->assertTrue( $db->query( "INSERT INTO ezsection (name, identifier) VALUES ('s', 's')" ) );
        foreach ( array_filter( explode( ";\n", $handler->generateAddFieldSql( 'ezsection', 'exp_n', array( 'type' => 'int', 'not_null' => '1', 'default' => 7 ), array() ) ) ) as $sql )
            $this->assertTrue( $db->query( $sql ), (string)$db->errorMessage() . "\n" . $sql );
        $this->assertSame( array( '7' ), array_values( array_unique( $db->arrayQuery( 'SELECT exp_n FROM ezsection', array( 'column' => 'exp_n' ) ) ) ) );
        foreach ( array( $handler->generateAlterFieldSql( 'ezsection', 'exp_n', array( 'type' => 'bigint', 'not_null' => '1', 'default' => 8 ), array() ),
                         $handler->generateAddIndexSql( 'ezsection', 'ezsection_exp_n', array( 'type' => 'non-unique', 'fields' => array( 'exp_n', 'name' ) ), array() ),
                         $handler->generateDropIndexSql( 'ezsection', 'ezsection_exp_n', array( 'type' => 'non-unique' ) ),
                         $handler->generateDropFieldSql( 'ezsection', 'exp_n', array() ) ) as $script )
            foreach ( array_filter( array_map( 'trim', explode( ";\n", $script ) ) ) as $sql )
                $this->assertTrue( $db->query( $sql ), (string)$db->errorMessage() . "\n" . $sql );
        $this->assertArrayNotHasKey( 'exp_n', $handler->fetchTableFields( 'ezsection', array() ) );

        // the portable helpers
        $this->assertTrue( $db->query( $db->sqlUpsert( 'ezsite_data', array( 'name' => 'exp-live', 'value' => 'a' ), array( 'name' ) ) ), (string)$db->errorMessage() );
        $this->assertTrue( $db->query( $db->sqlUpsert( 'ezsite_data', array( 'name' => 'exp-live', 'value' => "it's" ), array( 'name' ) ) ), (string)$db->errorMessage() );
        $this->assertSame( "it's", $db->arrayQuery( "SELECT value FROM ezsite_data WHERE name = 'exp-live'" )[0]['value'] );
        $this->assertSame( 'ezsite_data', $db->arrayQuery( $db->sqlLimit( "SELECT 'ezsite_data' AS t, name FROM ezsite_data ORDER BY name", 1, 0 ) )[0]['t'] );
        $now = (int)$db->arrayQuery( 'SELECT ' . $db->sqlUnixTimestamp() . ' AS t' . ( $db instanceof expInformixDB ? ' FROM systables WHERE tabid = 1' : '' ) )[0]['t'];
        $this->assertLessThan( 120, abs( $now - time() ) );
        if ( !$db instanceof expInformixDB )
        {
            $this->assertSame( '4102444800', $db->arrayQuery( 'SELECT ' . $db->sqlUnixTimestamp( $db->sqlFromUnixTimestamp( '4102444800' ) ) . ' AS t' )[0]['t'] );
            $joined = $db->arrayQuery( 'SELECT ' . $db->sqlGroupConcat( 'name', '|', 'name' ) . " AS j FROM ezsection WHERE name IN ('s', 'Standard')" )[0]['j'];
            $this->assertSame( 'Standard|s', $joined );
            $this->assertSame( md5( 'x' ), $db->arrayQuery( "SELECT " . $db->md5( 'name' ) . " AS m FROM ezcontentobject WHERE remote_id = 'dl06'" )[0]['m'] );
        }
    }

    /**
     * DL-07: a result keeps no memory once read. pdo_ibm's closeCursor() held the column descriptors of every
     * result for the rest of the process (68 MB for these 2,000 reads of 10 columns with a CLOB), which took a site
     * install past a memory_limit of 1G; ibm_db2's DB2_ATTR_CASE held 16 bytes a column of every row (320 KB).
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('engines')]
    public function testReadsKeepNoMemory( $class )
    {
        $db = $this->connect( $class );
        $this->makeTable( $db );
        $this->assertTrue( $db->query( "INSERT INTO exp_live (name, mask, value) VALUES ('a', 1, 'text')" ), (string)$db->errorMessage() );
        // the long text once, on Db2: on Informix 14.10 its column takes nearly a whole row, and the other nine pass
        // the row's limit of 32,767 bytes (SQLCODE -528)
        $text = $db instanceof expInformixDB ? 'id AS i4' : 'value';
        $sql = "SELECT id, name, mask, $text, id AS i2, name AS n2, mask AS m2, id AS i3, name AS n3, mask AS m3 FROM exp_live";
        $rows = $db->arrayQuery( $sql );
        $this->assertIsArray( $rows, (string)$db->errorMessage() );
        $this->assertCount( 10, $rows[0] );
        gc_collect_cycles();
        $before = memory_get_usage();
        for ( $i = 0; $i < 2000; $i++ )
            $db->arrayQuery( $sql );
        gc_collect_cycles();
        $this->assertLessThan( 262144, memory_get_usage() - $before, 'memory kept by 2,000 reads of 10 columns' );
    }
}
