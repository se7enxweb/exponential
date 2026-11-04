<?php
/**
 * The PostgreSQL schema reader (eZPgsqlSchema) on a table that carries a column of a type only PostgreSQL has: the
 * audit log's full-text column (expaudit_event.search_tsv, a generated tsvector with a GIN index). No database is
 * opened: a stub answers the reader's catalogue queries with the rows PostgreSQL returns for that table.
 *
 *  PT-01 - Reading the table's fields leaves the tsvector column out instead of failing with
 *          "Unhandled column type: tsvector"; the other columns read as before
 *  PT-02 - Reading the table's indexes leaves the GIN index over that column out; the primary key and the
 *          ordinary index stay
 *  PT-03 - Only tsvector and tsquery count as PostgreSQL's own types; a standard type still converts, and an
 *          unknown type still fails
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

$root = dirname( __DIR__, 4 );
foreach ( array( 'lib/ezdbschema/classes/ezdbschemainterface.php', 'lib/ezdbschema/classes/ezpgsqlschema.php' ) as $file )
    require_once $root . '/' . $file;

/** Enough of eZDBInterface for the reader: arrayQuery() answering the pg_catalog queries for expaudit_event */
class expPgsqlSchemaEngineOnlyTypesTestDB
{
    public $columns = array(
        array( 'attname' => 'id', 'format_type' => 'integer', 'default' => "nextval(('expaudit_event_id_seq'::text)::regclass)", 'attnotnull' => 't', 'attnum' => 1 ),
        array( 'attname' => 'branch', 'format_type' => 'character varying(64)', 'default' => "''::character varying", 'attnotnull' => 't', 'attnum' => 2 ),
        array( 'attname' => 'search_text', 'format_type' => 'text', 'default' => null, 'attnotnull' => 'f', 'attnum' => 3 ),
        array( 'attname' => 'search_tsv', 'format_type' => 'tsvector', 'default' => "to_tsvector('simple'::regconfig, COALESCE(search_text, ''::text))", 'attnotnull' => 'f', 'attnum' => 4 ),
    );

    public function arrayQuery( $sql )
    {
        if ( strpos( $sql, 'pg_catalog.pg_class c' ) !== false && strpos( $sql, 'relname ~' ) !== false )
            return array( array( 'oid' => 4242, 'nspname' => 'public', 'relname' => 'expaudit_event' ) );
        if ( strpos( $sql, 'a.attnum > 0' ) !== false )
            return $this->columns;
        if ( strpos( $sql, 'pg_catalog.pg_index i' ) !== false )
            return array(
                array( 'relname' => 'expaudit_event_pkey', 'indkey' => '1', 'indisprimary' => 't', 'indisunique' => 't', 'indrelid' => 4242 ),
                array( 'relname' => 'expaudit_event_branch', 'indkey' => '2', 'indisprimary' => 'f', 'indisunique' => 'f', 'indrelid' => 4242 ),
                array( 'relname' => 'expaudit_event_tsv', 'indkey' => '4', 'indisprimary' => 'f', 'indisunique' => 'f', 'indrelid' => 4242 ),
            );
        if ( preg_match( '/a\.attnum IN \(([0-9, ]+)\)/', $sql, $m ) )
        {
            $ids = array_map( 'intval', explode( ',', $m[1] ) );
            $rows = array();
            foreach ( $this->columns as $column )
            {
                if ( in_array( $column['attnum'], $ids, true ) )
                    $rows[] = array( 'attnum' => $column['attnum'], 'attname' => $column['attname'], 'format_type' => $column['format_type'] );
            }
            return $rows;
        }
        return array();
    }
}

class expPgsqlSchemaEngineOnlyTypesTest extends PHPUnit\Framework\TestCase
{
    private function schema()
    {
        return new eZPgsqlSchema( array( 'instance' => new expPgsqlSchemaEngineOnlyTypesTestDB() ) );
    }

    public function testTsvectorColumnIsLeftOut()
    {
        $fields = $this->schema()->fetchTableFields( 'expaudit_event', array( 'sort_columns' => true ) );
        $this->assertSame( array( 'branch', 'id', 'search_text' ), array_keys( $fields ) );
        $this->assertSame( 'auto_increment', $fields['id']['type'] );
        $this->assertSame( 'varchar', $fields['branch']['type'] );
        $this->assertSame( 64, $fields['branch']['length'] );
        $this->assertSame( 'longtext', $fields['search_text']['type'] );
    }

    public function testIndexOverTsvectorIsLeftOut()
    {
        $indexes = $this->schema()->fetchTableIndexes( 'expaudit_event', array( 'sort_indexes' => true ) );
        $this->assertSame( array( 'PRIMARY', 'expaudit_event_branch' ), array_keys( $indexes ) );
        $this->assertSame( array( 'id' ), $indexes['PRIMARY']['fields'] );
        $this->assertSame( array( 'branch' ), $indexes['expaudit_event_branch']['fields'] );
    }

    public function testOnlyPostgresqlOwnTypesAreSkipped()
    {
        $schema = $this->schema();
        $this->assertTrue( $schema->isEngineOnlyType( 'tsvector' ) );
        $this->assertTrue( $schema->isEngineOnlyType( 'tsquery' ) );
        $this->assertFalse( $schema->isEngineOnlyType( 'text' ) );
        $this->assertFalse( $schema->isEngineOnlyType( 'character varying(64)' ) );

        $length = null;
        $this->assertSame( 'varchar', $schema->parseType( 'character varying(64)', $length ) );
        $this->expectException( RuntimeException::class );
        $schema->parseType( 'point', $length );
    }
}
