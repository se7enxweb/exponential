<?php
/**
 * The PostgreSQL schema handler (eZPgsqlSchema) adding a NOT NULL column with a default to a table that already has
 * rows. ALTER ... SET DEFAULT does not fill the existing rows, so the handler must give them the default before
 * SET NOT NULL, or PostgreSQL refuses with "column ... contains null values". No database is opened: the statements
 * are checked as text.
 *
 *  PA-01 - An integer NOT NULL column with a default: ADD COLUMN, SET DEFAULT, UPDATE ... WHERE ... IS NULL,
 *          SET NOT NULL, in that order
 *  PA-02 - A varchar default is filled as a quoted literal
 *  PA-03 - A nullable column, and a NOT NULL column without a default, get no UPDATE
 *  PA-04 - A column named by a reserved word is quoted in the UPDATE
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

$root = dirname( __DIR__, 4 );
foreach ( array( 'lib/ezdbschema/classes/ezdbschemainterface.php', 'lib/ezdbschema/classes/ezpgsqlschema.php' ) as $file )
    require_once $root . '/' . $file;

class expPgsqlSchemaAddFieldTest extends PHPUnit\Framework\TestCase
{
    private function handler()
    {
        return ( new ReflectionClass( 'eZPgsqlSchema' ) )->newInstanceWithoutConstructor();
    }

    /** the statements of one generateAddFieldSql() call, without the empty ones */
    private function statements( $field, array $def )
    {
        $sql = $this->handler()->generateAddFieldSql( 'ticketsystem_ticket', $field, $def, array() );
        return array_values( array_filter( array_map( 'trim', explode( ";\n", $sql ) ), 'strlen' ) );
    }

    public function testIntegerNotNullWithDefaultFillsExistingRowsBeforeNotNull()
    {
        $sql = $this->statements( 'priority_rank', array( 'type' => 'int', 'length' => 11, 'default' => 0, 'not_null' => '1' ) );
        $this->assertCount( 4, $sql, implode( "\n", $sql ) );
        $this->assertSame( 'ALTER TABLE ticketsystem_ticket ADD COLUMN priority_rank integer', $sql[0] );
        $this->assertSame( 'ALTER TABLE ticketsystem_ticket ALTER priority_rank SET DEFAULT 0', $sql[1] );
        $this->assertSame( 'UPDATE ticketsystem_ticket SET priority_rank = 0 WHERE priority_rank IS NULL', $sql[2] );
        $this->assertSame( 'ALTER TABLE ticketsystem_ticket ALTER priority_rank SET NOT NULL', $sql[3] );
    }

    public function testVarcharDefaultIsFilledAsQuotedLiteral()
    {
        $sql = $this->statements( 'source', array( 'type' => 'varchar', 'length' => 16, 'default' => 'web', 'not_null' => '1' ) );
        $this->assertContains( "UPDATE ticketsystem_ticket SET source = 'web'::character varying WHERE source IS NULL", $sql );
        $this->assertSame( 'ALTER TABLE ticketsystem_ticket ALTER source SET NOT NULL', end( $sql ) );
    }

    public function testNoUpdateForNullableColumnOrNotNullWithoutDefault()
    {
        foreach ( array(
            array( 'type' => 'int', 'length' => 11, 'default' => 0 ),
            array( 'type' => 'int', 'length' => 11, 'not_null' => '1' ),
            array( 'type' => 'int', 'length' => 11, 'default' => null, 'not_null' => '1' ),
        ) as $def )
        {
            foreach ( $this->statements( 'assignee_id', $def ) as $statement )
                $this->assertStringStartsNotWith( 'UPDATE', $statement, var_export( $def, true ) );
        }
    }

    public function testReservedWordColumnIsQuotedInUpdate()
    {
        $handler = $this->handler();
        $reserved = $handler->reservedKeywordList();
        $this->assertContains( 'user', $reserved );
        $fill = $handler->generateFillExistingRowsSql( 't', 'user', array( 'type' => 'int', 'default' => 1, 'not_null' => '1' ), array() );
        $this->assertSame( 'UPDATE t SET "user" = 1 WHERE "user" IS NULL', $fill );
    }
}
