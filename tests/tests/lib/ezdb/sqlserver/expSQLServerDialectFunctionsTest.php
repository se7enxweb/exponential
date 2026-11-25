<?php
/**
 * expSQLServerDialect SQL expressions and the helpers every driver offers (no server needed):
 *
 *  FN-01 - upsert(): MERGE ... WITH (HOLDLOCK) ... ; (plain MERGE races; MERGE must end with ;), keys checked,
 *          update list default / explicit / empty (insert only)
 *  FN-02 - groupConcat(): STRING_AGG over NVARCHAR(MAX) (no 8,000 byte limit, error 9829), WITHIN GROUP order
 *  FN-03 - unixTimestamp()/fromUnixTimestamp(): DATEDIFF_BIG (no 2038 overflow), DATEADD split in days + seconds
 *  FN-04 - subString(): no 8,000 cut without a length (the old extension cut there, bug 11)
 *  FN-05 - concatString(): CONCAT (numbers are not added, NULL reads as '')
 *  FN-06 - md5(): PHP's md5 of the UTF-8 bytes (UTF-8 collation, 2019+), not master.dbo.fn_varbintohexstr (bug 11)
 *  FN-07 - inList(): chunks joined by OR / AND, empty lists, types, unique
 *  FN-08 - charLength(): trailing spaces counted (LEN() drops them)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expSQLServerTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('sqlserver')]
class expSQLServerDialectFunctionsTest extends PHPUnit\Framework\TestCase
{
    public function testUpsertDefaultUpdatesEveryNonKeyColumn()
    {
        $this->assertSame(
            "MERGE INTO [ezsite_data] WITH (HOLDLOCK) AS t USING (SELECT N'k' AS [name], N'v' AS [value]) AS s ON (t.[name] = s.[name])"
            . " WHEN MATCHED THEN UPDATE SET t.[value] = s.[value] WHEN NOT MATCHED THEN INSERT ([name], [value]) VALUES (s.[name], s.[value]);",
            expSQLServerDialect::upsert( 'ezsite_data', array( 'name' => 'k', 'value' => 'v' ), array( 'name' ) ) );
    }

    public function testUpsertWithoutUpdateIsInsertOnly()
    {
        $sql = expSQLServerDialect::upsert( 't', array( 'a' => 1, 'b' => 2 ), array( 'a' ), array() );
        $this->assertStringNotContainsString( 'WHEN MATCHED', $sql );
        $this->assertStringContainsString( 'WHEN NOT MATCHED THEN INSERT', $sql );
    }

    public function testUpsertExplicitUpdateColumns()
    {
        $sql = expSQLServerDialect::upsert( 't', array( 'a' => 1, 'b' => 2, 'c' => 3 ), array( 'a' ), array( 'c' ) );
        $this->assertStringContainsString( 'UPDATE SET t.[c] = s.[c] WHEN', $sql );
        $this->assertStringNotContainsString( 't.[b] = s.[b]', $sql );
    }

    public function testUpsertCompositeKey()
    {
        $sql = expSQLServerDialect::upsert( 't', array( 'a' => 1, 'b' => 2, 'c' => 3 ), array( 'a', 'b' ) );
        $this->assertStringContainsString( 'ON (t.[a] = s.[a] AND t.[b] = s.[b])', $sql );
    }

    public function testUpsertEndsWithSemicolonAndHoldlock()
    {
        $sql = expSQLServerDialect::upsert( 'dbo.t', array( 'a' => null ), array( 'a' ) );
        $this->assertStringEndsWith( ';', $sql );
        $this->assertStringContainsString( 'MERGE INTO [dbo].[t] WITH (HOLDLOCK)', $sql );
        $this->assertStringContainsString( 'NULL AS [a]', $sql );
    }

    public function testUpsertValuesAreEscaped()
    {
        $sql = expSQLServerDialect::upsert( 't', array( 'a' => "x'; DROP TABLE t; --" ), array( 'a' ) );
        $this->assertStringContainsString( "N'x''; DROP TABLE t; --' AS [a]", $sql );
        $this->assertTrue( expSQLServerDialect::isSingleStatement( $sql ) );
    }

    public function testUpsertNeedsAKeyWithAValue()
    {
        $this->expectException( InvalidArgumentException::class );
        expSQLServerDialect::upsert( 't', array( 'a' => 1 ), array( 'b' ) );
    }

    public function testUpsertNeedsColumns()
    {
        $this->expectException( InvalidArgumentException::class );
        expSQLServerDialect::upsert( 't', array(), array( 'a' ) );
    }

    public function testGroupConcat()
    {
        $this->assertSame( "STRING_AGG(CAST(name AS NVARCHAR(MAX)), N',')", expSQLServerDialect::groupConcat( 'name' ) );
        $this->assertSame( "STRING_AGG(CAST(id AS NVARCHAR(MAX)), N'; ') WITHIN GROUP (ORDER BY id DESC)", expSQLServerDialect::groupConcat( 'id', '; ', 'id DESC' ) );
        $this->assertSame( "STRING_AGG(CAST(a AS NVARCHAR(MAX)), N'''')", expSQLServerDialect::groupConcat( 'a', "'" ) );
        $this->assertStringNotContainsString( 'WITHIN', expSQLServerDialect::groupConcat( 'a', ',', '  ' ) );
    }

    public function testUnixTimestamp()
    {
        $this->assertSame( "DATEDIFF_BIG(SECOND, DATETIME2FROMPARTS(1970, 1, 1, 0, 0, 0, 0, 0), SYSUTCDATETIME())", expSQLServerDialect::unixTimestamp() );
        $this->assertSame( "DATEDIFF_BIG(SECOND, DATETIME2FROMPARTS(1970, 1, 1, 0, 0, 0, 0, 0), created)", expSQLServerDialect::unixTimestamp( 'created' ) );
    }

    public function testFromUnixTimestampSplitsDaysAndSeconds()
    {
        $sql = expSQLServerDialect::fromUnixTimestamp( 'modified' );
        $this->assertStringContainsString( 'DATEADD(DAY, CAST((modified) / 86400 AS INT)', $sql );
        $this->assertStringContainsString( 'DATEADD(SECOND, CAST((modified) % 86400 AS INT)', $sql );
        $this->assertStringNotContainsString( "'", $sql );
    }

    public function testCharLengthCountsTrailingSpaces()
    {
        $this->assertSame( "(LEN(CONCAT(a, N'x')) - 1)", expSQLServerDialect::charLength( 'a' ) );
    }

    public static function substrings()
    {
        return array(
            array( 'a', 1, null, 'SUBSTRING(a, 1, 2147483647)' ),
            array( 'a', 1, '', 'SUBSTRING(a, 1, 2147483647)' ),
            array( 'a', 1, false, 'SUBSTRING(a, 1, 2147483647)' ),
            array( 'a', 2, 3, 'SUBSTRING(a, 2, 3)' ),
            array( 'a', 'x', 'y', 'SUBSTRING(a, x, y)' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('substrings')]
    public function testSubString( $string, $from, $len, $expected )
    {
        $this->assertSame( $expected, expSQLServerDialect::subString( $string, $from, $len ) );
        $this->assertStringNotContainsString( '8000', $expected );
    }

    public static function concats()
    {
        return array(
            array( array(), "N''" ),
            array( array( 'a' ), "CONCAT(a, N'')" ),
            array( array( 'a', 'b' ), 'CONCAT(a, b)' ),
            array( array( 'path_string', "'/'", 'id' ), "CONCAT(path_string, '/', id)" ),
            array( array( 3 => 'a', 7 => 'b' ), 'CONCAT(a, b)' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('concats')]
    public function testConcatString( array $strings, $expected )
    {
        $this->assertSame( $expected, expSQLServerDialect::concatString( $strings ) );
    }

    public function testMd5On2019AndLaterHashesUtf8()
    {
        foreach ( array( 15, 16, 17 ) as $major )
        {
            $sql = expSQLServerDialect::md5( 'name', $major );
            $this->assertStringContainsString( '_UTF8', $sql );
            $this->assertStringContainsString( "HASHBYTES('MD5'", $sql );
            $this->assertStringStartsWith( 'LOWER(CONVERT(VARCHAR(32)', $sql );
            $this->assertStringNotContainsString( 'fn_varbintohexstr', $sql );
        }
    }

    public function testMd5On2017HasNoUtf8Collation()
    {
        $this->assertStringNotContainsString( 'UTF8', expSQLServerDialect::md5( 'name', 14 ) );
    }

    public function testBitOperators()
    {
        $this->assertSame( '(a & 3)', expSQLServerDialect::bitAnd( 'a', 3 ) );
        $this->assertSame( '(a | 3)', expSQLServerDialect::bitOr( 'a', 3 ) );
    }

    public static function inLists()
    {
        return array(
            'ints' => array( array( 1, 2, 3 ), 'id', false, 'int', 1000, 'id IN ( 1, 2, 3 )' ),
            'not' => array( array( 1, 2 ), 'id', true, 'int', 1000, 'id NOT IN ( 1, 2 )' ),
            'empty in is never true' => array( array(), 'id', false, 'int', 1000, '(1=0)' ),
            'empty not in is always true' => array( array(), 'id', true, 'int', 1000, '(1=1)' ),
            'chunks joined with or' => array( array( 1, 2, 3 ), 'id', false, 'int', 2, '( id IN ( 1, 2 ) OR id IN ( 3 ) )' ),
            'not chunks joined with and' => array( array( 1, 2, 3 ), 'id', true, 'int', 2, '( id NOT IN ( 1, 2 ) AND id NOT IN ( 3 ) )' ),
            'int cast' => array( array( '7abc', '2' ), 'id', false, 'int', 10, 'id IN ( 7, 2 )' ),
            'strings' => array( array( "a'b", 'c' ), 'name', false, 'string', 10, "name IN ( N'a''b', N'c' )" ),
            'floats' => array( array( 1.5, '2' ), 'f', false, 'float', 10, 'f IN ( 1.5, 2 )' ),
            'raw' => array( array( "'x'", 'y' ), 'c', false, false, 10, "c IN ( 'x', y )" ),
            'chunk below one is one' => array( array( 1, 2 ), 'id', false, 'int', 0, '( id IN ( 1 ) OR id IN ( 2 ) )' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('inLists')]
    public function testInList( array $elements, $column, $not, $type, $chunk, $expected )
    {
        $this->assertSame( $expected, expSQLServerDialect::inList( $elements, $column, $not, $type, $chunk ) );
    }

    public function testInListOfFiveThousandIdsHasFiveChunks()
    {
        $sql = expSQLServerDialect::inList( range( 1, 5000 ), 'id', false, 'int', 1000 );
        $this->assertSame( 5, substr_count( $sql, ' IN ( ' ) );
        $this->assertSame( 4, substr_count( $sql, ' OR ' ) );
    }
}
