<?php
/**
 * expSQLServerDialect literals (no server needed):
 *
 *  LI-01 - escapeString(): quotes doubled, NUL dropped, backslashes, newlines, emoji and invalid UTF-8 kept
 *  LI-02 - literal(): NULL, booleans, integers, floats without exponent (NaN/INF as NULL), text as N'...'
 *  LI-03 - unicodeLiterals(): every '...' becomes N'...', found by the lexer (never inside comments, names or
 *          other literals; never twice); a backslash before a line break (T-SQL's line continuation) is kept
 *  LI-04 - parameterize(): literals of a query or data change become ? parameters with the exact values; DDL,
 *          GROUP BY, open literals, existing ? and more than 2,000 literals fall back to unicodeLiterals()
 *  LI-05 - The old extension's regular expression for N'' (bug 5) failed on an odd number of quotes, on a
 *          backslash before a quote and on numeric text; the lexer handles each of them
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expSQLServerTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('sqlserver')]
class expSQLServerDialectLiteralsTest extends PHPUnit\Framework\TestCase
{
    public static function escapes()
    {
        return array(
            'plain' => array( 'abc', 'abc' ),
            'quote' => array( "o'neil", "o''neil" ),
            'two quotes' => array( "''", "''''" ),
            'nul dropped' => array( "a\0b", 'ab' ),
            'backslash kept' => array( 'a\\b', 'a\\b' ),
            'backslash quote' => array( "a\\'b", "a\\''b" ),
            'newline kept' => array( "a\nb", "a\nb" ),
            'emoji' => array( "😀'", "😀''" ),
            'percent and underscore kept' => array( '50%_x', '50%_x' ),
            'brackets kept' => array( '[a]', '[a]' ),
            'comment markers kept' => array( "-- /* */", "-- /* */" ),
            'question mark kept' => array( '?', '?' ),
            'invalid utf8 kept' => array( "\xff'", "\xff''" ),
            'empty' => array( '', '' ),
            'N prefix text' => array( "N'x'", "N''x''" ),
            'trailing spaces kept' => array( 'a  ', 'a  ' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('escapes')]
    public function testEscapeString( $value, $expected )
    {
        $this->assertSame( $expected, expSQLServerDialect::escapeString( $value ) );
    }

    public static function literals()
    {
        return array(
            'null' => array( null, 'NULL' ),
            'true' => array( true, '1' ),
            'false' => array( false, '0' ),
            'int' => array( 42, '42' ),
            'negative int' => array( -7, '-7' ),
            'zero' => array( 0, '0' ),
            'float' => array( 1.5, '1.5' ),
            'float integral' => array( 3.0, '3' ),
            'tiny float without exponent' => array( 0.000001, '0.000001' ),
            'big float without exponent' => array( 1.0e15, '1000000000000000' ),
            'negative zero float' => array( -0.0, '0' ),
            'nan is NULL' => array( NAN, 'NULL' ),
            'inf is NULL' => array( INF, 'NULL' ),
            'string' => array( 'x', "N'x'" ),
            'numeric string stays text' => array( '42', "N'42'" ),
            'empty string is not NULL' => array( '', "N''" ),
            'quote' => array( "'", "N''''" ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('literals')]
    public function testLiteral( $value, $expected )
    {
        $this->assertSame( $expected, expSQLServerDialect::literal( $value ) );
    }

    public function testBinaryLiteral()
    {
        $this->assertSame( '0x00FF41', expSQLServerDialect::binaryLiteral( "\x00\xffA" ) );
        $this->assertSame( '0x', expSQLServerDialect::binaryLiteral( '' ) );
    }

    public static function unicodeCases()
    {
        return array(
            'one literal' => array( "SELECT 'a'", "SELECT N'a'" ),
            'already N' => array( "SELECT N'a'", "SELECT N'a'" ),
            'lower n' => array( "SELECT n'a'", "SELECT N'a'" ),
            'doubled quote' => array( "SELECT 'it''s'", "SELECT N'it''s'" ),
            'empty literal' => array( "SELECT ''", "SELECT N''" ),
            'quote in comment untouched' => array( "SELECT 1 -- it's\n", "SELECT 1 -- it's\n" ),
            'quote in block comment untouched' => array( "SELECT /* 'x' */ 1", "SELECT /* 'x' */ 1" ),
            'quote in bracket name untouched' => array( "SELECT [it's] FROM t", "SELECT [it's] FROM t" ),
            'numeric text' => array( "WHERE a = '42'", "WHERE a = N'42'" ),
            'odd number of quotes (bug 5)' => array( "SELECT 'a''', 'b'", "SELECT N'a''', N'b'" ),
            'backslash before quote (bug 5)' => array( "SELECT 'a\\''b'", "SELECT N'a\\''b'" ),
            'text with N inside' => array( "SELECT 'NN'", "SELECT N'NN'" ),
            'word ending in N' => array( "WHERE x IN('a')", "WHERE x IN(N'a')" ),
            'emoji' => array( "SELECT '😀'", "SELECT N'😀'" ),
            'no literal' => array( 'SELECT 1', 'SELECT 1' ),
            'open literal untouched' => array( "SELECT 'a", "SELECT 'a" ),
            'nul dropped' => array( "SELECT 'a\0b'", "SELECT N'ab'" ),
            'line continuation kept' => array( "SELECT 'a\\\nb'", "SELECT (N'a\\' + NCHAR(10) + N'b')" ),
            'line continuation crlf kept' => array( "SELECT 'a\\\r\nb'", "SELECT (N'a\\' + NCHAR(13) + NCHAR(10) + N'b')" ),
            'line continuation at end' => array( "SELECT 'a\\\n'", "SELECT (N'a\\' + NCHAR(10))" ),
            'plain newline is no continuation' => array( "SELECT 'a\nb'", "SELECT N'a\nb'" ),
            'binary untouched' => array( 'SELECT 0x41', 'SELECT 0x41' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('unicodeCases')]
    public function testUnicodeLiterals( $sql, $expected )
    {
        $this->assertSame( $expected, expSQLServerDialect::unicodeLiterals( $sql ) );
    }

    public function testUnicodeLiteralsIsIdempotent()
    {
        $once = expSQLServerDialect::unicodeLiterals( "SELECT 'a', N'b', 'c''d' FROM t WHERE x = 'y'" );
        $this->assertSame( $once, expSQLServerDialect::unicodeLiterals( $once ) );
    }

    public static function parameterizeCases()
    {
        return array(
            'select' => array( "SELECT * FROM t WHERE a = 'x'", 'SELECT * FROM t WHERE a = ?', array( 'x' ) ),
            'insert' => array( "INSERT INTO t (a, b) VALUES ('x', 'y')", 'INSERT INTO t (a, b) VALUES (?, ?)', array( 'x', 'y' ) ),
            'update' => array( "UPDATE t SET a = 'x' WHERE b = 'y'", 'UPDATE t SET a = ? WHERE b = ?', array( 'x', 'y' ) ),
            'delete' => array( "DELETE FROM t WHERE a = 'x'", 'DELETE FROM t WHERE a = ?', array( 'x' ) ),
            'merge' => array( "MERGE t USING (SELECT 'x' AS a) s ON 1=0 WHEN NOT MATCHED THEN INSERT (a) VALUES (s.a);",
                              'MERGE t USING (SELECT ? AS a) s ON 1=0 WHEN NOT MATCHED THEN INSERT (a) VALUES (s.a);', array( 'x' ) ),
            'with cte' => array( "WITH c AS (SELECT 'x' AS a) SELECT a FROM c", 'WITH c AS (SELECT ? AS a) SELECT a FROM c', array( 'x' ) ),
            'doubled quote unescaped' => array( "SELECT 'it''s'", 'SELECT ?', array( "it's" ) ),
            'N literal' => array( "SELECT N'ä'", 'SELECT ?', array( 'ä' ) ),
            'empty' => array( "SELECT ''", 'SELECT ?', array( '' ) ),
            'question mark inside literal' => array( "SELECT '?'", 'SELECT ?', array( '?' ) ),
            'comment kept' => array( "SELECT 'a' -- 'b'\n", "SELECT ? -- 'b'\n", array( 'a' ) ),
            'like with escape' => array( "SELECT 1 WHERE a LIKE 'x!%' ESCAPE '!'", 'SELECT 1 WHERE a LIKE ? ESCAPE ?', array( 'x!%', '!' ) ),
            'line continuation is data' => array( "SELECT 'a\\\nb'", 'SELECT ?', array( "a\\\nb" ) ),
            'no literal' => array( 'SELECT 1', 'SELECT 1', array() ),
            'string_agg separator stays a literal' => array( "SELECT STRING_AGG(name, '|') WITHIN GROUP (ORDER BY name) FROM t WHERE a = 'x'",
                                                             "SELECT STRING_AGG(name, N'|') WITHIN GROUP (ORDER BY name) FROM t WHERE a = ?", array( 'x' ) ),
            'string_agg first argument is bound' => array( "SELECT STRING_AGG(CONCAT(name, '!'), ',') FROM t", "SELECT STRING_AGG(CONCAT(name, ?), N',') FROM t", array( '!' ) ),
            'numeric text stays text' => array( "SELECT * FROM t WHERE id = '5'", 'SELECT * FROM t WHERE id = ?', array( '5' ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('parameterizeCases')]
    public function testParameterize( $sql, $expectedSql, array $expectedValues )
    {
        $this->assertSame( array( $expectedSql, $expectedValues ), expSQLServerDialect::parameterize( $sql ) );
    }

    public static function notParameterized()
    {
        return array(
            'create table default must be a literal' => array( "CREATE TABLE t (a NVARCHAR(10) DEFAULT 'x')" ),
            'alter table' => array( "ALTER TABLE t ADD CONSTRAINT d DEFAULT 'x' FOR a" ),
            'exec' => array( "EXEC sp_rename 't', 'u'" ),
            'declare' => array( "DECLARE @a NVARCHAR(10) = 'x'" ),
            'group by' => array( "SELECT CASE WHEN a = 'x' THEN 1 END FROM t GROUP BY CASE WHEN a = 'x' THEN 1 END" ),
            'existing parameter' => array( "SELECT * FROM t WHERE a = ? AND b = 'x'" ),
            'open literal' => array( "SELECT 'x" ),
            'set' => array( "SET LANGUAGE 'us_english'" ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('notParameterized')]
    public function testStatementsThatKeepLiterals( $sql )
    {
        $this->assertNull( expSQLServerDialect::parameterize( $sql ) );
    }

    public function testGroupByInsideLiteralDoesNotStopBinding()
    {
        $this->assertSame( array( 'SELECT ?', array( 'GROUP BY' ) ), expSQLServerDialect::parameterize( "SELECT 'GROUP BY'" ) );
    }

    public function testTwoThousandLiteralsAreBound()
    {
        $sql = 'SELECT 1 WHERE a IN (' . implode( ', ', array_fill( 0, 2000, "'x'" ) ) . ')';
        $result = expSQLServerDialect::parameterize( $sql );
        $this->assertCount( 2000, $result[1] );
    }

    public function testMoreThanTheParameterBudgetFallsBack()
    {
        // the server takes at most 2,100 parameters per call (error 8003)
        $sql = 'SELECT 1 WHERE a IN (' . implode( ', ', array_fill( 0, 2001, "'x'" ) ) . ')';
        $this->assertNull( expSQLServerDialect::parameterize( $sql ) );
        $this->assertLessThan( expSQLServerDialect::MAX_PARAMETERS, expSQLServerDialect::PARAMETER_BUDGET );
    }

    public function testNulIsDroppedFromBoundValue()
    {
        $this->assertSame( array( 'SELECT ?', array( 'ab' ) ), expSQLServerDialect::parameterize( "SELECT 'a\0b'" ) );
    }

    public function testInvalidUtf8IsBoundAsItIs()
    {
        $this->assertSame( array( 'SELECT ?', array( "\xff" ) ), expSQLServerDialect::parameterize( "SELECT '\xff'" ) );
    }

    public function testEscapeThenParameterizeGivesTheValueBack()
    {
        foreach ( array( "o'neil", "''", "a\\'b", "😀", "-- x", "/* y */", "?", "N'z'", "a\nb" ) as $value )
        {
            $result = expSQLServerDialect::parameterize( "SELECT '" . expSQLServerDialect::escapeString( $value ) . "'" );
            $this->assertSame( array( $value ), $result[1], $value );
        }
    }
}
