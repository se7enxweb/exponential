<?php
/**
 * What the MonetDB driver changes in a statement (expMonetDBDialect), how it escapes text and names errors.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expColumnarTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('columnar')]
class expMonetDBDialectTest extends PHPUnit\Framework\TestCase
{
    public static function rewrites()
    {
        return array(
            'backticks' => array( 'SELECT `name` FROM `ezsection`', 'SELECT "name" FROM "ezsection"' ),
            'backtick in a literal stays' => array( "SELECT 'a`b' FROM t", "SELECT 'a`b' FROM t" ),
            'MySQL paging' => array( 'SELECT id FROM t ORDER BY id LIMIT 20, 10', 'SELECT id FROM t ORDER BY id LIMIT 10 OFFSET 20' ),
            'paging in a literal stays' => array( "SELECT 'LIMIT 1, 2' AS x", "SELECT 'LIMIT 1, 2' AS x" ),
            'FOR UPDATE' => array( 'SELECT id FROM t WHERE id = 1 FOR UPDATE', 'SELECT id FROM t WHERE id = 1' ),
            'LOCK IN SHARE MODE' => array( 'SELECT id FROM t LOCK IN SHARE MODE', 'SELECT id FROM t' ),
            'FOR UPDATE NOWAIT' => array( 'SELECT id FROM t FOR UPDATE NOWAIT', 'SELECT id FROM t' ),
            'LOCK TABLE' => array( 'LOCK TABLE ezcontentobject WRITE', null ),
            'UNLOCK TABLES' => array( 'UNLOCK TABLES', null ),
            'day on its table' => array( 'SELECT user_id, day, time FROM ezgeneral_digest_user_settings WHERE day = \'monday\'',
                                         'SELECT user_id, "day", time FROM ezgeneral_digest_user_settings WHERE "day" = \'monday\'' ),
            'day quoted already' => array( 'SELECT "day" FROM ezgeneral_digest_user_settings', 'SELECT "day" FROM ezgeneral_digest_user_settings' ),
            'day elsewhere stays' => array( "SELECT EXTRACT(DAY FROM d) AS day FROM t", "SELECT EXTRACT(DAY FROM d) AS day FROM t" ),
            'records on its table' => array( 'UPDATE expaudit_file SET records = records + 1', 'UPDATE expaudit_file SET "records" = "records" + 1' ),
            'records in a literal stays' => array( "INSERT INTO expaudit_file (records, channel) VALUES (1, 'records')",
                                                   "INSERT INTO expaudit_file (\"records\", channel) VALUES (1, 'records')" ),
            'backslash before a quote in a literal' => array( "SELECT 'a\\'`b' FROM `t`", "SELECT 'a\\'`b' FROM \"t\"" ),
            'trailing space' => array( "SELECT 1 \n", 'SELECT 1' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('rewrites')]
    public function testRewrite( $sql, $expected )
    {
        $this->assertSame( $expected, expMonetDBDialect::rewrite( $sql ) );
    }

    public function testWrites()
    {
        foreach ( array( 'INSERT INTO t VALUES (1)', 'UPDATE t SET a = 1', 'DELETE FROM t', 'CREATE TABLE t (a INT)', 'DROP TABLE t',
                         'ALTER TABLE t ADD COLUMN b INT', 'MERGE INTO t USING s ON (1=1) WHEN MATCHED THEN DELETE',
                         'WITH x AS (SELECT 1) INSERT INTO t SELECT * FROM x', '/* c */ INSERT INTO t VALUES (1)' ) as $sql )
            $this->assertTrue( expMonetDBDialect::isWrite( $sql ), $sql );
        foreach ( array( 'SELECT 1', '  ( SELECT 1 )', 'START TRANSACTION', 'COMMIT', 'ROLLBACK', 'SET SCHEMA x',
                         "WITH x AS (SELECT 'INSERT') SELECT * FROM x", 'EXPLAIN SELECT 1', '' ) as $sql )
            $this->assertFalse( expMonetDBDialect::isWrite( $sql ), $sql );
        $this->assertSame( 'INSERT', expMonetDBDialect::firstWord( "-- note\n/* x */ INSERT INTO t VALUES (1)" ) );
    }

    public function testEscape()
    {
        $this->assertSame( "it''s", expMonetDBDialect::escape( "it's" ) );
        $this->assertSame( 'a\\\\b', expMonetDBDialect::escape( 'a\\b' ), 'MonetDB reads C escapes: a backslash is doubled' );
        $this->assertSame( 'ab', expMonetDBDialect::escape( "a\0b" ) );
        $this->assertSame( '', expMonetDBDialect::escape( null ) );
    }

    public function testBlankLiterals()
    {
        $this->assertSame( "SELECT '   ' FROM t", expMonetDBDialect::blankLiterals( "SELECT 'a''' FROM t" ) );
        $this->assertSame( "SELECT '    ' FROM t", expMonetDBDialect::blankLiterals( "SELECT 'a\\'b' FROM t" ) );
        $this->assertSame( "SELECT 1         ", expMonetDBDialect::blankLiterals( "SELECT 1 -- 'note" ) );
        $this->assertNull( expMonetDBDialect::blankLiterals( "SELECT 'open" ) );
    }

    public static function errors()
    {
        return array(
            array( '40002', "INSERT INTO: PRIMARY KEY constraint 't.t_pkey' violated", '23505' ),
            array( '40002', "INSERT INTO: UNIQUE constraint 't.t_code' violated", '23505' ),
            array( '40002', 'INSERT INTO: NOT NULL constraint violated for column t.id', '23502' ),
            array( '40002', "INSERT INTO: FOREIGN KEY constraint 't.fk' violated", '23503' ),
            array( '42S02', "SELECT: no such table 'missing'", '42P01' ),
            array( '42000', "SELECT: identifier 'nope' unknown", '42703' ),
            array( '42000', 'syntax error, unexpected IDENT in: "selec"', '42601' ),
            array( '42S11', "CREATE INDEX: name 'x' already in use", '42P07' ),
            array( '42000', 'Update failed due to conflict with another transaction', '40001' ),
            array( '40001', 'COMMIT: transaction is aborted because of concurrency conflicts, will ROLLBACK instead', '40001' ),
            array( '25005', 'Current transaction is aborted (please ROLLBACK)', '40001' ),
            array( '22001', "INSERT INTO: value too long for type (var)char(2)", '22001' ),
            array( '08006', 'The connection to MonetDB was lost', '08006' ),
            array( '', 'something else', 'XX000' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('errors')]
    public function testErrorCode( $state, $message, $expected )
    {
        $this->assertSame( $expected, expMonetDBDialect::errorCode( $state, $message ) );
    }
}
