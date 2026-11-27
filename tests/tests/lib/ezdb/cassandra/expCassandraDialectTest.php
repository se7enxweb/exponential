<?php
/**
 * expCassandraDialect without a server: the SQL the driver answers on CQL.
 *
 *  CD-01 - Tokens: quotes doubled, quoted names, numbers, comments, the operators
 *  CD-02 - SELECT parsed: the select list with aliases and aggregates, a table alias, WHERE, ORDER BY, LIMIT and
 *          OFFSET in MySQL's and PostgreSQL's forms, FOR UPDATE
 *  CD-03 - INSERT (several rows, IGNORE, REPLACE, INSERT OR REPLACE, ON DUPLICATE KEY UPDATE), UPDATE with
 *          arithmetic, DELETE
 *  CD-04 - What is refused, with the name of it: joins, sub-selects, GROUP BY, HAVING, UNION, CASE, unknown
 *          functions, INSERT ... SELECT, bound parameters
 *  CD-05 - Conditions in PHP: SQL's three values with NULL, numbers against numeric text, LIKE with % _ and an
 *          escape, IN, BETWEEN, the bit operators, LOWER/UPPER, SUBSTRING, CONCAT, MD5
 *  CD-06 - project(): ORDER BY with nulls first, DISTINCT, aggregates, offset and limit of the statement and of the
 *          call together
 *  CD-07 - Types: .dba to CQL and back, values bound per CQL type (an empty text in a number column is null)
 *  CD-08 - sqlUpsert() in the three forms, the helpers of expDBSQLHelpers for the dialect cassandra
 *  CD-09 - errorCategory() of the protocol's error codes
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expCassandraTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('cassandra')]
class expCassandraDialectTest extends PHPUnit\Framework\TestCase
{
    /** CD-01 */
    public function testTokens()
    {
        $tokens = expCassandraDialect::tokenize( "SELECT `a`, \"B\" FROM t -- note\n WHERE x = 'it''s' AND y >= 1.5e3 /* c */ AND z <> -2;" );
        $this->assertContains( array( 'quoted', 'a' ), $tokens );
        $this->assertContains( array( 'quoted', 'B' ), $tokens );
        $this->assertContains( array( 'string', "it's" ), $tokens );
        $this->assertContains( array( 'number', '1.5e3' ), $tokens );
        $this->assertContains( array( 'op', '>=' ), $tokens );
        $this->assertContains( array( 'op', '<>' ), $tokens );
        $this->assertNotContains( array( 'op', ';' ), $tokens );
        $this->assertNotContains( array( 'word', 'note' ), $tokens );
        $this->expectException( expDBUnsupportedException::class );
        expCassandraDialect::tokenize( "SELECT 'open" );
    }

    /** CD-02 */
    public function testSelectParsed()
    {
        $s = expCassandraDialect::parse( 'SELECT DISTINCT a.id, a.name AS title, COUNT(*) n, MAX(a.value) FROM ks.items a WHERE a.id = 5 AND NOT ( a.x IS NULL ) ORDER BY title DESC, 2 LIMIT 10 OFFSET 20 FOR UPDATE' );
        $this->assertSame( 'select', $s['type'] );
        $this->assertSame( 'items', $s['table'] );
        $this->assertSame( 'a', $s['alias'] );
        $this->assertTrue( $s['distinct'] );
        $this->assertSame( array( 'id', 'title', 'n', 'max(a.value)' ), array_column( $s['columns'], 'alias' ) );
        $this->assertSame( array( 't' => 'col', 'name' => 'id' ), $s['columns'][0]['expr'] );
        $this->assertSame( 'agg', $s['columns'][2]['expr']['t'] );
        $this->assertNull( $s['columns'][2]['expr']['arg'] );
        $this->assertSame( 'and', $s['where']['t'] );
        $this->assertTrue( $s['order'][0]['desc'] );
        $this->assertSame( 10, $s['limit'] );
        $this->assertSame( 20, $s['offset'] );
        $mysql = expCassandraDialect::parse( 'SELECT * FROM t LIMIT 5, 10' );
        $this->assertSame( array( 10, 5 ), array( $mysql['limit'], $mysql['offset'] ) );
        $all = expCassandraDialect::parse( 'SELECT * FROM t LIMIT ALL OFFSET 3' );
        $this->assertSame( array( null, 3 ), array( $all['limit'], $all['offset'] ) );
        $noFrom = expCassandraDialect::parse( 'SELECT 1 AS one' );
        $this->assertNull( $noFrom['table'] );
        $this->assertSame( 'select', expCassandraDialect::kind( '  ( SELECT 1 )' ) );
        $this->assertSame( 'cql', expCassandraDialect::kind( 'CREATE TABLE x (id int PRIMARY KEY)' ) );
        $this->assertSame( 'insert', expCassandraDialect::kind( 'REPLACE INTO t (a) VALUES (1)' ) );
    }

    /** CD-03 */
    public function testWritesParsed()
    {
        $i = expCassandraDialect::parse( "INSERT INTO t (id, name) VALUES (1, 'a'), (2, NULL), (-3, 'c')" );
        $this->assertSame( array( 'id', 'name' ), $i['columns'] );
        $this->assertCount( 3, $i['rows'] );
        $this->assertNull( $i['rows'][1]['name']['v'] );
        $this->assertSame( -3, $i['rows'][2]['id']['v'] );
        $this->assertFalse( $i['ignore'] || $i['replace'] );
        $this->assertTrue( expCassandraDialect::parse( 'INSERT IGNORE INTO t (a) VALUES (1)' )['ignore'] );
        $this->assertTrue( expCassandraDialect::parse( 'REPLACE INTO t (a) VALUES (1)' )['replace'] );
        $this->assertTrue( expCassandraDialect::parse( 'INSERT OR REPLACE INTO t (a) VALUES (1)' )['replace'] );
        $this->assertTrue( expCassandraDialect::parse( 'INSERT OR IGNORE INTO t (a) VALUES (1)' )['ignore'] );
        $u = expCassandraDialect::parse( 'INSERT INTO t (id, a, b) VALUES (1, 2, 3) ON DUPLICATE KEY UPDATE a = VALUES(a), `b` = VALUES(b)' );
        $this->assertSame( array( 'a', 'b' ), $u['update_columns'] );
        $up = expCassandraDialect::parse( "UPDATE t SET count = count + 1, name = 'x' WHERE id IN (1, 2)" );
        $this->assertSame( 'arith', $up['set']['count']['t'] );
        $this->assertSame( 'in', $up['where']['t'] );
        $d = expCassandraDialect::parse( 'DELETE FROM t WHERE id = 3' );
        $this->assertSame( array( 'delete', 't' ), array( $d['type'], $d['table'] ) );
        $this->assertNull( expCassandraDialect::parse( 'DELETE FROM t' )['where'] );
    }

    public static function refusedProvider()
    {
        return array(
            'join' => array( 'SELECT a.id FROM a JOIN b ON b.id = a.id', 'join' ),
            'comma join' => array( 'SELECT a.id FROM a, b WHERE b.id = a.id', 'join' ),
            'left join' => array( 'SELECT * FROM a LEFT JOIN b ON 1 = 1', 'join' ),
            'sub-select in' => array( 'SELECT * FROM a WHERE id IN (SELECT id FROM b)', 'sub-select' ),
            'sub-select from' => array( 'SELECT * FROM (SELECT 1) x', 'sub-select' ),
            'exists' => array( 'SELECT * FROM a WHERE EXISTS (SELECT 1 FROM b)', 'EXISTS' ),
            'group by' => array( 'SELECT a, COUNT(*) FROM t GROUP BY a', 'GROUP BY' ),
            'union' => array( 'SELECT a FROM t UNION SELECT a FROM u', 'UNION' ),
            'case' => array( 'SELECT CASE WHEN a = 1 THEN 2 END FROM t', 'CASE' ),
            'function' => array( 'SELECT NOW() FROM t', 'NOW' ),
            'insert select' => array( 'INSERT INTO t (a) SELECT a FROM u', 'INSERT ... SELECT' ),
            'parameter' => array( 'SELECT * FROM t WHERE a = ?', 'bound parameter' ),
            'on conflict' => array( 'INSERT INTO t (a) VALUES (1) ON CONFLICT (a) DO NOTHING', 'ON CONFLICT' ),
            'update limit' => array( 'UPDATE t SET a = 1 LIMIT 1', 'LIMIT' ),
        );
    }

    /** CD-04 */
    #[\PHPUnit\Framework\Attributes\DataProvider('refusedProvider')]
    public function testRefused( $sql, $named )
    {
        try
        {
            expCassandraDialect::parse( $sql );
            $this->fail( "$sql was parsed" );
        }
        catch ( expDBUnsupportedException $e )
        {
            $this->assertStringContainsString( $named, $e->getMessage() );
            $this->assertSame( 'cassandra', $e->dialect() );
        }
    }

    private static function where( $condition )
    {
        return expCassandraDialect::parse( "SELECT * FROM t WHERE $condition" )['where'];
    }

    public static function conditionProvider()
    {
        $row = array( 'id' => 5, 'name' => 'Apple pie', 'flags' => 6, 'price' => '1.50', 'note' => null, 'code' => '007', 'pct' => '50%' );
        return array(
            array( 'id = 5', $row, true ),
            array( "id = '5'", $row, true ),
            array( "code = 7", $row, true ),
            // two numeric texts compare as numbers too (a decimal column's values are text)
            array( "code = '7'", $row, true ),
            array( 'price > 1.2', $row, true ),
            array( 'note = 1', $row, false ),
            array( 'NOT ( note = 1 )', $row, false ),
            array( 'note IS NULL', $row, true ),
            array( 'note IS NOT NULL OR id = 5', $row, true ),
            array( 'note <> 1 OR id = 4', $row, false ),
            array( "name LIKE 'Apple%'", $row, true ),
            array( "name LIKE 'apple%'", $row, false ),
            array( "LOWER(name) LIKE 'apple%'", $row, true ),
            array( "name LIKE 'Apple_pie'", $row, true ),
            array( "name NOT LIKE '%pie'", $row, false ),
            array( "pct LIKE '50\\%' ESCAPE '\\'", $row, true ),
            array( "pct LIKE '5!_%' ESCAPE '!'", $row, false ),
            array( 'id IN (1, 5, 9)', $row, true ),
            array( 'id NOT IN (1, 9)', $row, true ),
            array( 'id IN (1, NULL)', $row, false ),
            array( 'id NOT IN (1, NULL)', $row, false ),
            array( 'id BETWEEN 5 AND 6', $row, true ),
            array( 'id NOT BETWEEN 1 AND 9', $row, false ),
            array( '( flags & 2 ) > 0', $row, true ),
            array( '( flags & 1 ) = 0 AND ( flags | 1 ) = 7', $row, true ),
            array( 'id + 1 = 6 AND id * 2 = 10 AND id % 2 = 1 AND -id = -5', $row, true ),
            array( "SUBSTRING(name, 1, 5) = 'Apple' AND SUBSTR(name, 7) = 'pie'", $row, true ),
            array( "CONCAT(name, '!') = 'Apple pie!' AND UPPER(code) = '007'", $row, true ),
            array( "MD5(name) = '" . md5( 'Apple pie' ) . "'", $row, true ),
            array( 'COALESCE(note, id) = 5', $row, true ),
            array( 'LENGTH(name) = 9', $row, true ),
            array( 'missing = 1', $row, false ),
        );
    }

    /** CD-05 */
    #[\PHPUnit\Framework\Attributes\DataProvider('conditionProvider')]
    public function testConditions( $condition, array $row, $expected )
    {
        $this->assertSame( $expected, expCassandraDialect::matches( self::where( $condition ), $row ), $condition );
    }

    public function testComparisonOfLargeIntegers()
    {
        $this->assertSame( -1, expCassandraDialect::compare( '9007199254740992', '9007199254740993' ) );
        $this->assertSame( 1, expCassandraDialect::compare( '-1', '-12345678901234567890' ) );
        $this->assertSame( 0, expCassandraDialect::compare( 7, '07' ) );
        $this->assertSame( 1, expCassandraDialect::compare( 'b', 'a' ) );
        $this->assertSame( 1, expCassandraDialect::compare( 'é', 'z' ), 'text by code points, as Cassandra orders it' );
    }

    /** CD-06 */
    public function testProject()
    {
        $rows = array( array( 'id' => 1, 'name' => 'b', 'v' => 10 ), array( 'id' => 2, 'name' => null, 'v' => 20 ),
                       array( 'id' => 3, 'name' => 'a', 'v' => 10 ), array( 'id' => 4, 'name' => 'c', 'v' => true ) );
        $columns = array( 'id', 'name', 'v' );
        $s = expCassandraDialect::parse( 'SELECT id, name FROM t ORDER BY name' );
        $this->assertSame( array( '2', '3', '1', '4' ), array_column( expCassandraDialect::project( $s, $rows, $columns ), 'id' ) );
        $s = expCassandraDialect::parse( 'SELECT id FROM t ORDER BY name DESC' );
        $this->assertSame( array( '4', '1', '3', '2' ), array_column( expCassandraDialect::project( $s, $rows, $columns ), 'id' ) );
        $s = expCassandraDialect::parse( 'SELECT id AS i FROM t ORDER BY i DESC LIMIT 2 OFFSET 1' );
        $this->assertSame( array( '3', '2' ), array_column( expCassandraDialect::project( $s, $rows, $columns ), 'i' ) );
        $this->assertSame( array( '2' ), array_column( expCassandraDialect::project( $s, $rows, $columns, 1, 1 ), 'i' ), 'the call\'s offset after the statement\'s, the smaller limit' );
        $s = expCassandraDialect::parse( 'SELECT DISTINCT v FROM t ORDER BY v' );
        $this->assertSame( array( '1', '10', '20' ), array_column( expCassandraDialect::project( $s, $rows, $columns ), 'v' ) );
        $s = expCassandraDialect::parse( 'SELECT COUNT(*) AS n, COUNT(name) AS named, COUNT(DISTINCT v) AS d, SUM(v) AS s, AVG(id) AS a, MIN(name) AS lo FROM t' );
        $this->assertSame( array( array( 'n' => '4', 'named' => '3', 'd' => '3', 's' => '41', 'a' => '2.5', 'lo' => 'a' ) ), expCassandraDialect::project( $s, $rows, $columns ) );
        $s = expCassandraDialect::parse( 'SELECT MAX(id) AS m FROM t' );
        $this->assertSame( array( array( 'm' => null ) ), expCassandraDialect::project( $s, array(), $columns ) );
        $s = expCassandraDialect::parse( 'SELECT * FROM t' );
        $this->assertSame( array( 'id' => '4', 'name' => 'c', 'v' => '1' ), expCassandraDialect::project( $s, $rows, $columns )[3] );
    }

    /** CD-07 */
    public function testTypes()
    {
        $this->assertSame( 'int', expCassandraDialect::cqlType( array( 'type' => 'auto_increment' ) ) );
        $this->assertSame( 'bigint', expCassandraDialect::cqlType( array( 'type' => 'bigint' ) ) );
        $this->assertSame( 'double', expCassandraDialect::cqlType( array( 'type' => 'float' ) ) );
        $this->assertSame( 'decimal', expCassandraDialect::cqlType( array( 'type' => 'decimal', 'length' => '10,2' ) ) );
        $this->assertSame( 'text', expCassandraDialect::cqlType( array( 'type' => 'varchar', 'length' => 255 ) ) );
        $this->assertSame( 'text', expCassandraDialect::cqlType( array( 'type' => 'longtext' ) ) );
        $this->assertSame( 'blob', expCassandraDialect::cqlType( array( 'type' => 'blob' ) ) );
        $this->assertSame( array( 'length' => 255, 'type' => 'varchar', 'not_null' => '1', 'default' => '' ), expCassandraDialect::dbaField( 'text', true ) );
        $this->assertSame( 'longtext', expCassandraDialect::dbaField( 'text' )['type'] );
        $this->assertSame( 'longtext', expCassandraDialect::dbaField( 'frozen<map<text, int>>' )['type'] );
        $this->assertSame( 'char', expCassandraDialect::dbaField( 'timeuuid' )['type'] );
        $this->assertSame( 'bigint', expCassandraDialect::dbaField( 'counter' )['type'] );
        $this->assertSame( 5, expCassandraDialect::bindValue( '5', 'int' ) );
        $this->assertSame( 0, expCassandraDialect::bindValue( 'abc', 'int' ) );
        $this->assertNull( expCassandraDialect::bindValue( '', 'int' ) );
        $this->assertSame( 2, expCassandraDialect::bindValue( 1.6, 'bigint' ) );
        $this->assertSame( 1.5, expCassandraDialect::bindValue( '1.5', 'double' ) );
        $this->assertSame( '12.30', expCassandraDialect::bindValue( ' 12.30 ', 'decimal' ) );
        $this->assertSame( '12345678901234567890', expCassandraDialect::bindValue( '12345678901234567890', 'varint' ) );
        $this->assertTrue( expCassandraDialect::bindValue( '1', 'boolean' ) );
        $this->assertFalse( expCassandraDialect::bindValue( 'false', 'boolean' ) );
        $this->assertSame( '7', expCassandraDialect::bindValue( 7, 'text' ) );
        $this->assertSame( '0.25', expCassandraDialect::bindValue( 0.25, 'text' ) );
        $this->assertSame( '"a""b"', expCassandraDialect::quoteName( 'A"B' ) === '"a""b"' ? '"a""b"' : expCassandraDialect::quoteName( 'A"B' ) );
        $this->assertSame( '"user"', expCassandraDialect::quoteName( '`User`' ) );
        $this->assertSame( 'CREATE TABLE "t" ("id" int)', expCassandraDialect::passThrough( 'CREATE TABLE `t` (`id` int);' ) );
        $this->assertSame( "INSERT INTO x VALUES ('a`b')", expCassandraDialect::passThrough( "INSERT INTO x VALUES ('a`b')" ) );
    }

    /** CD-08 */
    public function testUpsertAndHelpers()
    {
        $row = array( 'id' => 1, 'name' => "it's", 'value' => 2 );
        $this->assertSame( 'REPLACE INTO "t" ("id", "name", "value") VALUES (1, \'it\'\'s\', 2)', expCassandraDialect::upsert( 't', $row, array( 'id' ) ) );
        $this->assertSame( 'REPLACE INTO "t" ("id", "name", "value") VALUES (1, \'it\'\'s\', 2)', expCassandraDialect::upsert( 't', $row, array( 'id' ), array( 'name', 'value' ) ) );
        $this->assertSame( 'INSERT IGNORE INTO "t" ("id", "name", "value") VALUES (1, \'it\'\'s\', 2)', expCassandraDialect::upsert( 't', $row, array( 'id' ), array() ) );
        $sql = expCassandraDialect::upsert( 't', $row, array( 'id' ), array( 'name' ) );
        $this->assertSame( 'INSERT INTO "t" ("id", "name", "value") VALUES (1, \'it\'\'s\', 2) ON DUPLICATE KEY UPDATE "name" = VALUES("name")', $sql );
        $this->assertSame( array( 'name' ), expCassandraDialect::parse( $sql )['update_columns'] );
        $this->assertSame( $sql, expDBSQLHelpers::upsert( 'cassandra', array( 'expCassandraDialect', 'literal' ), 't', $row, array( 'id' ), array( 'name' ) ) );
        $this->assertContains( 'cassandra', expDBSQLHelpers::DIALECTS );
        $this->assertSame( "SELECT a FROM t\nLIMIT 5 OFFSET 10", expDBSQLHelpers::limit( 'cassandra', 'SELECT a FROM t', 5, 10 ) );
        $parsed = expCassandraDialect::parse( expDBSQLHelpers::limit( 'cassandra', 'SELECT a FROM t', false, 10 ) );
        $this->assertSame( array( null, 10 ), array( $parsed['limit'], $parsed['offset'] ) );
        $this->assertSame( 'UNIX_TIMESTAMP()', expDBSQLHelpers::unixTimestamp( 'cassandra' ) );
        $this->assertSame( 'FROM_UNIXTIME(x)', expDBSQLHelpers::fromUnixTimestamp( 'cassandra', 'x' ) );
        $this->assertTrue( expCassandraDialect::matches( self::where( "FROM_UNIXTIME(0) = '1970-01-01 00:00:00' AND UNIX_TIMESTAMP('1970-01-02 00:00:00') = 86400" ), array() ) );
        $this->assertSame( '"a"."b"', expDBSQLHelpers::quoteIdentifier( 'cassandra', 'a.b' ) );
        $this->expectException( expDBUnsupportedException::class );
        expDBSQLHelpers::groupConcat( 'cassandra', array( 'expCassandraDialect', 'literal' ), 'name' );
    }

    /** CD-09 */
    public function testErrorCategory()
    {
        $this->assertSame( 'syntax', expCassandraDialect::errorCategory( 0x2000, 'line 1:0 no viable alternative' ) );
        $this->assertSame( 'invalid', expCassandraDialect::errorCategory( 0x2200, 'Cannot execute this query as it might involve data filtering' ) );
        $this->assertSame( 'unavailable', expCassandraDialect::errorCategory( 0x1000, 'Cannot achieve consistency level QUORUM' ) );
        $this->assertSame( 'unavailable', expCassandraDialect::errorCategory( 0x1200, 'Operation timed out' ) );
        $this->assertSame( 'auth', expCassandraDialect::errorCategory( 0x0100, 'Provided username and/or password are incorrect' ) );
        $this->assertSame( 'connection', expCassandraDialect::errorCategory( null, 'Stream connection reset by peer' ) );
        $this->assertSame( 'duplicate', expCassandraDialect::errorCategory( 1062, 'Duplicate entry' ) );
    }
}
?>
