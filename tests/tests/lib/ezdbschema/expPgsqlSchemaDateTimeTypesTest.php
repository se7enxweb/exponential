<?php
/**
 * eZPgsqlSchema and the date and time column types. A MySQL database converted to PostgreSQL with Exponential's
 * schema tools (ezsqldumpschema from MySQL, ezsqlinsertschema into PostgreSQL) stopped with "Unhandled column type:
 * timestamp": third-party and extension tables in a site's database (a cron_schedules table, stores' order tables)
 * carry timestamp, datetime and date columns, which the PostgreSQL handler had no mapping for.
 *
 *  DT-01 - timestamp and datetime become PostgreSQL timestamp, date stays date, time stays time
 *  DT-02 - they take no length: a MySQL length such as datetime(6) is not written into the PostgreSQL type
 *  DT-03 - reading back: PostgreSQL's "timestamp without time zone", date and "time without time zone" read as
 *          timestamp, date and time; an unknown type still fails
 *
 * No database.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

$root = dirname( __DIR__, 4 );
foreach ( array( 'lib/ezdbschema/classes/ezdbschemainterface.php', 'lib/ezdbschema/classes/ezpgsqlschema.php' ) as $file )
    require_once $root . '/' . $file;

class expPgsqlSchemaDateTimeTypesTest extends PHPUnit\Framework\TestCase
{
    private function schema()
    {
        return new eZPgsqlSchema( array( 'instance' => null ) );
    }

    public function testStandardToPostgresql()
    {
        $s = $this->schema();
        $length = null;
        $this->assertSame( 'timestamp', $s->convertFromStandardType( 'timestamp', $length ), 'DT-01 timestamp' );
        $this->assertSame( 'timestamp', $s->convertFromStandardType( 'datetime', $length ), 'DT-01 datetime' );
        $this->assertSame( 'date', $s->convertFromStandardType( 'date', $length ), 'DT-01 date' );
        $this->assertSame( 'time', $s->convertFromStandardType( 'time', $length ), 'DT-01 time' );
    }

    public function testNoLength()
    {
        $s = $this->schema();
        foreach ( array( 'timestamp', 'date', 'time' ) as $t )
            $this->assertFalse( $s->isTypeLengthSupported( $t ), "DT-02 $t" );
        $this->assertTrue( $s->isTypeLengthSupported( 'character varying' ), 'DT-02 varchar keeps its length' );
    }

    /** DT-04: MySQL's implicit '' default of a NOT NULL number or date column is no DEFAULT in PostgreSQL */
    public function testEmptyDefaultOfNumberOrDateIsLeftOut()
    {
        $s = $this->schema();
        foreach ( array( 'decimal', 'int', 'bigint', 'tinyint', 'float', 'double', 'timestamp', 'datetime', 'date' ) as $t )
            $this->assertSame( '', $s->generateDefaultDef( false, false, array( 'type' => $t, 'default' => '' ), array() ), "DT-04 $t" );
        $this->assertSame( "DEFAULT ''::character varying ",
                           $s->generateDefaultDef( false, false, array( 'type' => 'varchar', 'default' => '' ), array() ), 'DT-04 varchar keeps it' );
        $this->assertSame( "DEFAULT '0.00' ",
                           $s->generateDefaultDef( false, false, array( 'type' => 'decimal', 'default' => '0.00' ), array() ), 'DT-04 a real default stays' );
    }

    /** DT-05: reserved words are quoted in INSERT column lists and data SELECTs; other names stay plain */
    public function testReservedColumnNamesQuoted()
    {
        $s = $this->schema();
        $this->assertSame( '"group"', $s->insertColumnName( 'group' ), 'DT-05 group' );
        $this->assertSame( '"group"', $s->quoteSchemaIdentifier( 'group' ), 'DT-05 select' );
        $this->assertSame( 'name', $s->insertColumnName( 'name' ), 'DT-05 plain name' );
    }

    public function testPostgresqlToStandard()
    {
        $s = $this->schema();
        $length = null;
        $this->assertSame( 'timestamp', $s->parseType( 'timestamp without time zone', $length ), 'DT-03 timestamp' );
        $this->assertSame( 'date', $s->parseType( 'date', $length ), 'DT-03 date' );
        $this->assertSame( 'time', $s->parseType( 'time without time zone', $length ), 'DT-03 time' );
        $this->expectException( RuntimeException::class );
        $s->parseType( 'point', $length );
    }
}
