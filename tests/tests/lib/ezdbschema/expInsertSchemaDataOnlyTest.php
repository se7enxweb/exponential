<?php
/**
 * eZDBSchemaInterface::insertSchema() with the data alone ('schema' => false): the rows go into tables that
 * exist. A package that adds rows after the install (the default admin layouts of Exponential Layouts) asked for
 * exactly that, and the call returned true and inserted nothing, because the tables were skipped as "already
 * there" - on Oracle, MySQL and PostgreSQL alike. A run with the tables too ('schema' => true, a setup run again
 * on a database it filled before) still leaves the data of an existing table alone.
 *
 *  IS-01 - data only: every row of an existing table is inserted
 *  IS-02 - tables and data: an existing table is neither created nor filled again; a new one is both
 *  IS-03 - a column the engine renames is inserted under its local name, also from data marked "local" elsewhere
 *
 * No database: a recording connection and a handler with a fixed schema.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__, 4 ) . '/lib/ezdbschema/classes/ezdbschemainterface.php';

/** The calls insertSchema() makes on a connection, recorded */
class expInsertSchemaDataOnlyTestDB
{
    public $OutputSQL = false;
    public $queries = array();
    public $tables = array();

    public function relationList()
    {
        return $this->tables;
    }

    public function query( $sql )
    {
        $this->queries[] = $sql;
        return true;
    }

    public function begin() {}
    public function commit() {}
    public function rollback() {}
}

class expInsertSchemaDataOnlyTestSchema extends eZDBSchemaInterface
{
    /** @var array ColumnNameTranslation of the engine under test */
    public $translations = array();

    protected function columnNameTranslations()
    {
        return $this->translations;
    }

    function schema( $params = array() )
    {
        return $this->Schema;
    }

    function data( $schema = false, $tableNameList = false, $params = array() )
    {
        return $this->Data;
    }

    function generateTableSQLList( $tableName, $table, $params, $separateTypes )
    {
        return array( "CREATE TABLE $tableName ( id int )" );
    }

    function generateTableInsertSQLList( $tableName, $tableDef, $dataEntries, $params, $withClosure = true )
    {
        $list = array();
        foreach ( $dataEntries['rows'] as $row )
            $list[] = "INSERT INTO $tableName ( " . implode( ", ", array_values( array_filter( $dataEntries["fields"], function ( $f ) use ( $tableDef ) { return isset( $tableDef["fields"][$f] ); } ) ) ) . " ) VALUES ( " . (int)$row[0] . " )";
        return $list;
    }
}

class expInsertSchemaDataOnlyTest extends PHPUnit\Framework\TestCase
{
    private function handler( $db )
    {
        $table = array( 'fields' => array( 'id' => array( 'type' => 'int' ) ), 'indexes' => array() );
        return new expInsertSchemaDataOnlyTestSchema( array(
            'instance' => $db,
            'schema' => array( '_info' => array( 'format' => 'local' ), 'explayouts_layout' => $table, 'explayouts_new' => $table ),
            'data' => array( 'explayouts_layout' => array( 'fields' => array( 'id' ), 'rows' => array( array( 100001 ), array( 100002 ) ) ),
                             'explayouts_new' => array( 'fields' => array( 'id' ), 'rows' => array( array( 7 ) ) ) ) ) );
    }

    public function testDataOnlyFillsExistingTables()
    {
        $db = new expInsertSchemaDataOnlyTestDB();
        $db->tables = array( 'explayouts_layout', 'explayouts_new' );
        $this->assertTrue( $this->handler( $db )->insertSchema( array( 'schema' => false, 'data' => true ) ) );
        $this->assertSame( array(
            'INSERT INTO explayouts_layout ( id ) VALUES ( 100001 )',
            'INSERT INTO explayouts_layout ( id ) VALUES ( 100002 )',
            'INSERT INTO explayouts_new ( id ) VALUES ( 7 )',
        ), $db->queries );
    }

    public function testTablesAndDataLeaveAnExistingTableAlone()
    {
        $db = new expInsertSchemaDataOnlyTestDB();
        $db->tables = array( 'explayouts_layout' );
        $this->assertTrue( $this->handler( $db )->insertSchema( array( 'schema' => true, 'data' => true ) ) );
        $this->assertSame( array(
            'CREATE TABLE explayouts_new ( id int )',
            'INSERT INTO explayouts_new ( id ) VALUES ( 7 )',
        ), $db->queries );
    }

    /**
     * IS-03 - data marked "local" by the engine it was dumped from (MySQL) keeps the generic column names; a column
     * the engine renames (Oracle: contentobject_attribute_version is contentobject_attr_version) is inserted under
     * its local name instead of being left out
     */
    public function testRenamedColumnOfDataDumpedElsewhereIsInserted()
    {
        $db = new expInsertSchemaDataOnlyTestDB();
        $db->tables = array( 'ezurl_object_link' );
        $handler = new expInsertSchemaDataOnlyTestSchema( array(
            'instance' => $db,
            'schema' => array( '_info' => array( 'format' => 'local' ), 'ezurl_object_link' => array(
                'fields' => array( 'url_id' => array( 'type' => 'int' ), 'contentobject_attr_version' => array( 'type' => 'int' ) ),
                'indexes' => array() ) ),
            'data' => array( '_info' => array( 'format' => 'local' ), 'ezurl_object_link' => array(
                'fields' => array( 'url_id', 'contentobject_attribute_version' ), 'rows' => array( array( 337, 1 ) ) ) ) ) );
        $handler->translations = array( 'ezurl_object_link.contentobject_attribute_version' => 'contentobject_attr_version' );
        $this->assertTrue( $handler->insertSchema( array( 'schema' => false, 'data' => true ) ) );
        $this->assertSame( array( 'INSERT INTO ezurl_object_link ( url_id, contentobject_attr_version ) VALUES ( 337 )' ), $db->queries );
    }
}
