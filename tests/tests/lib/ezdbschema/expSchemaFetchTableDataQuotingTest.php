<?php
/**
 * eZDBSchemaInterface::fetchTableData() quotes the table and column names per engine. A MySQL/MariaDB table with a
 * column named like a reserved word ("interval" in a cron_schedules table) made ezsqldumpschema --output-types=data
 * stop with a syntax error, so a whole database could not be dumped (found converting a site from MySQL to
 * PostgreSQL).
 *
 *  FQ-01 - MySQL: every column and the table name in backticks; a backtick inside a name is doubled
 *  FQ-02 - the default (other engines): names unchanged, as before
 *  FQ-03 - the rows come back keyed by the plain column names (char columns still padded)
 *
 * No database: a recording connection.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__, 4 ) . '/lib/ezdbschema/classes/ezdbschemainterface.php';
require_once dirname( __DIR__, 4 ) . '/lib/ezdbschema/classes/ezmysqlschema.php';

/** Records the SELECT and answers with fixed rows */
class expSchemaFetchTableDataQuotingTestDB
{
    public $queries = array();
    public $rows = array();

    public function arrayQuery( $sql, $params = array() )
    {
        $this->queries[] = $sql;
        return $this->rows;
    }
}

/** The default quoting of the base class */
class expSchemaFetchTableDataQuotingTestPlain extends eZDBSchemaInterface
{
    function schema( $params = array() ) { return array(); }
    function data( $schema = false, $tableNameList = false, $params = array() ) { return array(); }
    function escapeSQLString( $value ) { return $value; }
}

class expSchemaFetchTableDataQuotingTest extends PHPUnit\Framework\TestCase
{
    private function table()
    {
        return array( 'name' => 'cron_schedules',
                      'fields' => array( 'id' => array( 'type' => 'int' ),
                                         'interval' => array( 'type' => 'varchar' ),
                                         'code' => array( 'type' => 'char', 'length' => 4 ) ) );
    }

    public function testMysqlQuotesTableAndColumns()
    {
        $db = new expSchemaFetchTableDataQuotingTestDB();
        $schema = new eZMysqlSchema( array( 'instance' => $db ) );
        $schema->fetchTableData( $this->table() );
        $this->assertSame( 'SELECT `id`, `interval`, `code` FROM `cron_schedules`', $db->queries[0], 'FQ-01' );
        $this->assertSame( '`a``b`', $schema->quoteIdentifier( 'a`b' ), 'FQ-01 backtick doubled' );
    }

    public function testDefaultLeavesNamesAsTheyAre()
    {
        $db = new expSchemaFetchTableDataQuotingTestDB();
        $schema = new expSchemaFetchTableDataQuotingTestPlain( array( 'instance' => $db ) );
        $schema->fetchTableData( $this->table() );
        $this->assertSame( 'SELECT id, interval, code FROM cron_schedules', $db->queries[0], 'FQ-02' );
    }

    public function testRowsKeyedByPlainNames()
    {
        $db = new expSchemaFetchTableDataQuotingTestDB();
        $db->rows = array( array( 'id' => 1, 'interval' => 'hourly', 'code' => 'ab' ) );
        $schema = new eZMysqlSchema( array( 'instance' => $db ) );
        $out = $schema->fetchTableData( $this->table() );
        $this->assertSame( array( 'id', 'interval', 'code' ), $out['fields'], 'FQ-03 fields' );
        $this->assertSame( array( array( 1, 'hourly', 'ab  ' ) ), $out['rows'], 'FQ-03 rows, char padded' );
    }
}
