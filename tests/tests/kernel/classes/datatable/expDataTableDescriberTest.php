<?php
/**
 * A data table as its database has it (expDataTableDescriber; doc/eng-us/features/6.0/data-tables.md, "Describe").
 * No installation: drift() is pure; describe(), columns() and rowCount() run on a SQLite file under var/datatable/,
 * removed afterwards.
 *
 *  DD-01 - drift(): a field without a column is missing_column, a column without a field extra_column
 *  DD-02 - drift(): a field stored under its short_name is found by it
 *  DD-03 - drift(): an integer field over a text column and a text field over a number column are type_mismatch;
 *          SQLite (any type in any column) does not report text over number
 *  DD-04 - drift(): MongoDB reports no missing column (a sample holds only the fields its documents have), and _id is
 *          never an extra column
 *  DD-05 - describe() on SQLite: the table exists, its columns and types, its row count, the engine and the drift
 *  DD-06 - describe() of a table that is not there: exists false and missing_table; a class without a table: error
 *  DD-07 - columns() and rowCount() refuse a name that is not a table name
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expDataTableTestLoader.php';

class expDataTableDescriberTest extends PHPUnit\Framework\TestCase
{
    /** @var string|null */
    private $file = null;

    protected function tearDown(): void
    {
        expDataConnections::setConnections( null );
        foreach ( array( '', '-wal', '-shm', '-journal' ) as $suffix )
            if ( $this->file && is_file( $this->file . $suffix ) )
                unlink( $this->file . $suffix );
    }

    private static function entry( array $fields, $table = 't691_d', $connection = 't691d' )
    {
        $definition = array( 'fields' => $fields, 'keys' => array( 'id' ), 'increment_key' => 'id', 'class_name' => 'X', 'name' => $table );
        $entry = expDataTableCatalogue::entryFromDefinition( 'X', $definition, '', 'kernel', false );
        $entry['connection'] = $connection;
        return $entry;
    }

    private static function kinds( array $drift )
    {
        $kinds = array();
        foreach ( $drift as $d )
            $kinds[] = $d['kind'] . ':' . $d['column'];
        sort( $kinds );
        return $kinds;
    }

    /** @return eZDBInterface a SQLite connection t691d */
    private function connection()
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        $relative = 'var/datatable/t691-dd-' . getmypid() . '.sqlite';
        $this->file = eZSys::rootDir() . '/' . $relative;
        expDataConnections::setConnections( array( 't691d' => array( 'engine' => 'sqlite', 'database' => $relative ) ) );
        return expDataConnections::db( 't691d' );
    }

    /** DD-01 */
    public function testMissingAndExtraColumns()
    {
        $entry = self::entry( array( 'id' => array( 'datatype' => 'integer' ), 'name' => array( 'datatype' => 'string' ) ) );
        $columns = array( 'id' => array( 'type' => 'int' ), 'other' => array( 'type' => 'varchar' ) );
        $this->assertSame( array( 'extra_column:other', 'missing_column:name' ), self::kinds( expDataTableDescriber::drift( $entry, $columns, 'mysql' ) ) );
        $this->assertSame( array(), expDataTableDescriber::drift( $entry, array( 'ID' => array( 'type' => 'int' ), 'NAME' => array( 'type' => 'varchar' ) ), 'oracle' ), 'names compared without case' );
    }

    /** DD-02 */
    public function testShortName()
    {
        $entry = self::entry( array( 'id' => array( 'datatype' => 'integer' ), 'contentobject_attribute_version' => array( 'datatype' => 'integer', 'short_name' => 'coa_version' ) ) );
        $this->assertSame( array(), expDataTableDescriber::drift( $entry, array( 'id' => array( 'type' => 'int' ), 'coa_version' => array( 'type' => 'int' ) ), 'oracle' ) );
        $this->assertSame( array(), expDataTableDescriber::drift( $entry, array( 'id' => array( 'type' => 'int' ), 'contentobject_attribute_version' => array( 'type' => 'int' ) ), 'mysql' ) );
    }

    /** DD-03 */
    public function testTypeMismatch()
    {
        $entry = self::entry( array( 'id' => array( 'datatype' => 'integer' ), 'name' => array( 'datatype' => 'string' ) ) );
        $columns = array( 'id' => array( 'type' => 'varchar' ), 'name' => array( 'type' => 'int' ) );
        $this->assertSame( array( 'type_mismatch:id', 'type_mismatch:name' ), self::kinds( expDataTableDescriber::drift( $entry, $columns, 'mysql' ) ) );
        $this->assertSame( array( 'type_mismatch:id' ), self::kinds( expDataTableDescriber::drift( $entry, $columns, 'sqlite' ) ) );
        $this->assertSame( array(), expDataTableDescriber::drift( $entry, array( 'id' => array( 'type' => 'bigint' ), 'name' => array( 'type' => 'longtext' ) ), 'mysql' ) );
    }

    /** DD-04 */
    public function testMongo()
    {
        $entry = self::entry( array( 'id' => array( 'datatype' => 'integer' ), 'name' => array( 'datatype' => 'string' ) ) );
        $this->assertSame( array(), expDataTableDescriber::drift( $entry, array( '_id' => array( 'type' => 'object' ), 'id' => array( 'type' => 'int' ) ), 'mongodb' ) );
        $this->assertSame( array( 'extra_column:x' ), self::kinds( expDataTableDescriber::drift( $entry, array( '_id' => array( 'type' => 'object' ), 'x' => array( 'type' => 'string' ) ), 'mongodb' ) ) );
    }

    /** DD-05 */
    public function testDescribeOnSqlite()
    {
        $db = $this->connection();
        $db->query( "CREATE TABLE t691_d ( id INTEGER PRIMARY KEY AUTOINCREMENT, name varchar(64) NOT NULL DEFAULT '', extra TEXT )" );
        $db->query( "INSERT INTO t691_d ( name ) VALUES ( 'a' )" );
        $db->query( "INSERT INTO t691_d ( name ) VALUES ( 'b' )" );
        $entry = self::entry( array( 'id' => array( 'datatype' => 'integer' ), 'name' => array( 'datatype' => 'string' ), 'missing' => array( 'datatype' => 'string' ) ) );
        $d = expDataTableDescriber::describe( $entry );
        $this->assertSame( '', $d['error'] );
        $this->assertTrue( $d['exists'] );
        $this->assertSame( 'sqlite', $d['engine'] );
        $this->assertSame( 't691d', $d['connection'] );
        $this->assertSame( array( 'id', 'name', 'extra' ), array_keys( $d['columns'] ) );
        $this->assertSame( 'integer', $d['columns']['id']['type'] );
        $this->assertFalse( $d['columns']['name']['nullable'] );
        $this->assertSame( 2, $d['rows'] );
        $this->assertSame( array( 'extra_column:extra', 'missing_column:missing' ), self::kinds( $d['drift'] ) );
    }

    /** DD-06 */
    public function testMissingTable()
    {
        $this->connection();
        $d = expDataTableDescriber::describe( self::entry( array( 'id' => array( 'datatype' => 'integer' ) ), 't691_nosuch' ) );
        $this->assertFalse( $d['exists'] );
        $this->assertSame( array( 'missing_table:' ), self::kinds( $d['drift'] ) );
        $this->assertNull( $d['rows'] );

        $entry = self::entry( array( 'id' => array( 'datatype' => 'integer' ) ), '' );
        $d = expDataTableDescriber::describe( $entry );
        $this->assertNotSame( '', $d['error'] );
        $this->assertNull( $d['exists'] );

        $d = expDataTableDescriber::describe( self::entry( array( 'id' => array( 'datatype' => 'integer' ) ), 't691_d', 'nosuchconnection' ) );
        $this->assertStringContainsString( 'nosuchconnection', $d['error'] );
    }

    /** DD-07 */
    public function testBadNames()
    {
        $db = $this->connection();
        $this->assertSame( array(), expDataTableDescriber::columns( $db, 'x; DROP TABLE y' ) );
        $this->assertNull( expDataTableDescriber::rowCount( $db, 'x y' ) );
        $this->assertFalse( expKernelTableSchema::exists( "x'y", $db ) );
    }
}
