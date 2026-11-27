<?php
/**
 * What the ClickHouse driver changes in a statement (expClickHouseDialect), how it escapes text and names errors.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expColumnarTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('columnar')]
class expClickHouseDialectTest extends PHPUnit\Framework\TestCase
{
    public static function rewrites()
    {
        return array(
            'a select stays' => array( 'SELECT `id` FROM t LIMIT 10, 5', 'SELECT `id` FROM t LIMIT 10, 5' ),
            'FOR UPDATE' => array( 'SELECT id FROM t WHERE id = 1 FOR UPDATE;', 'SELECT id FROM t WHERE id = 1' ),
            'FOR UPDATE in a literal stays' => array( "SELECT 'x FOR UPDATE'", "SELECT 'x FOR UPDATE'" ),
            'BEGIN' => array( 'BEGIN', null ),
            'START TRANSACTION' => array( 'START TRANSACTION', null ),
            'COMMIT' => array( 'COMMIT', null ),
            'ROLLBACK' => array( 'ROLLBACK', null ),
            'LOCK TABLE' => array( 'LOCK TABLE t WRITE', null ),
            'DELETE without WHERE' => array( 'DELETE FROM `events`', 'TRUNCATE TABLE `events`' ),
            'DELETE with WHERE stays' => array( 'DELETE FROM events WHERE id = 1', 'DELETE FROM events WHERE id = 1' ),
            'UPDATE without WHERE' => array( 'UPDATE t SET n = 0', 'UPDATE t SET n = 0 WHERE 1' ),
            'UPDATE with WHERE in a literal' => array( "UPDATE t SET n = 'WHERE'", "UPDATE t SET n = 'WHERE' WHERE 1" ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('rewrites')]
    public function testRewrite( $sql, $expected )
    {
        $this->assertSame( $expected, expClickHouseDialect::rewrite( $sql ) );
    }

    public function testUpdateAsMutation()
    {
        $this->assertSame( "ALTER TABLE t UPDATE n = 1, s = 'a WHERE b' WHERE id = 2",
                           expClickHouseDialect::updateAsMutation( "UPDATE t SET n = 1, s = 'a WHERE b' WHERE id = 2" ) );
        $this->assertSame( 'ALTER TABLE `db`.`t` UPDATE n = 1 WHERE 1', expClickHouseDialect::updateAsMutation( 'UPDATE `db`.`t` SET n = 1' ) );
        $this->assertNull( expClickHouseDialect::updateAsMutation( 'SELECT 1' ) );
    }

    public function testWrites()
    {
        foreach ( array( 'INSERT INTO t VALUES (1)', 'ALTER TABLE t UPDATE n = 1 WHERE 1', 'CREATE TABLE t (a Int8) ENGINE = Memory', 'DROP TABLE t',
                         'TRUNCATE TABLE t', 'OPTIMIZE TABLE t FINAL', 'DELETE FROM t WHERE 1' ) as $sql )
            $this->assertTrue( expClickHouseDialect::isWrite( $sql ), $sql );
        foreach ( array( 'SELECT 1', 'WITH 1 AS x SELECT x', 'SHOW TABLES', 'DESCRIBE t', 'EXISTS t', 'BEGIN', 'SET max_threads = 1', '' ) as $sql )
            $this->assertFalse( expClickHouseDialect::isWrite( $sql ), $sql );
        $this->assertTrue( expClickHouseDialect::isTransaction( ' commit' ) );
    }

    public function testEscape()
    {
        $this->assertSame( "it\\'s", expClickHouseDialect::escape( "it's" ) );
        $this->assertSame( 'a\\\\b', expClickHouseDialect::escape( 'a\\b' ) );
        $this->assertSame( "\\\\\\'", expClickHouseDialect::escape( "\\'" ), 'a backslash before a quote cannot end the literal' );
        $this->assertSame( '', expClickHouseDialect::escape( null ) );
    }

    public function testErrorCodes()
    {
        $this->assertSame( '42P01', expClickHouseDialect::errorCode( 60 ) );
        $this->assertSame( '42703', expClickHouseDialect::errorCode( 47 ) );
        $this->assertSame( '42601', expClickHouseDialect::errorCode( 62 ) );
        $this->assertSame( '28000', expClickHouseDialect::errorCode( 516 ) );
        $this->assertSame( '08006', expClickHouseDialect::errorCode( -1 ) );
        $this->assertSame( 'CH999', expClickHouseDialect::errorCode( 999 ) );
        $this->assertSame( 'XX000', expClickHouseDialect::errorCode( 0 ) );
        $this->assertTrue( expClickHouseDialect::isLightweightUpdateRefused( 48, 'Lightweight updates are not supported. Lightweight updates are supported only for tables with materialized _block_number column' ) );
        $this->assertFalse( expClickHouseDialect::isLightweightUpdateRefused( 60, 'Unknown table' ) );
    }
}
