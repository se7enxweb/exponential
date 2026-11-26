<?php
/**
 * expSQLServerDialect::applyLimit() (no server needed): arrayQuery()'s limit and offset as OFFSET ... FETCH.
 *
 *  PG-01 - The statement's own outer ORDER BY is kept; ORDER BY inside a subquery, a CTE, OVER(), a literal or a
 *          comment does not count; without an outer one ORDER BY (SELECT NULL) is added
 *  PG-02 - ORDER BY with parentheses (LOWER(name), CASE ...) is still found: expPDODB::limitClause()'s
 *          '[^)]*$' test misses it and appends a second ORDER BY
 *  PG-03 - TOP, an OFFSET of its own, DISTINCT or UNION without ORDER BY: the statement is wrapped
 *  PG-04 - OPTION (...) and FOR XML/JSON stay last
 *  PG-05 - limit 0 gives null (FETCH NEXT 0 ROWS is error 10744); no limit and no offset leaves the statement as
 *          it is; offset alone pages without FETCH; negative, empty and non-numeric limits are no limit
 *  PG-06 - Trailing semicolons go; statements other than SELECT are left alone
 *  PG-07 - The old extension's paging (bug 1/2): offset rows were never skipped server side; here the server skips
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expSQLServerTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('sqlserver')]
class expSQLServerDialectPagingTest extends PHPUnit\Framework\TestCase
{
    public static function cases()
    {
        $null = "\nORDER BY (SELECT NULL)";
        return array(
            'no order by' => array( 'SELECT id FROM t', 10, 20, "SELECT id FROM t$null\nOFFSET 20 ROWS FETCH NEXT 10 ROWS ONLY" ),
            'own order by' => array( 'SELECT id FROM t ORDER BY id', 10, 0, "SELECT id FROM t ORDER BY id\nOFFSET 0 ROWS FETCH NEXT 10 ROWS ONLY" ),
            'order by desc two columns' => array( 'SELECT a, b FROM t ORDER BY a DESC, b', 5, 5, "SELECT a, b FROM t ORDER BY a DESC, b\nOFFSET 5 ROWS FETCH NEXT 5 ROWS ONLY" ),
            'order by with function (PG-02)' => array( 'SELECT name FROM t ORDER BY LOWER(name)', 3, 0, "SELECT name FROM t ORDER BY LOWER(name)\nOFFSET 0 ROWS FETCH NEXT 3 ROWS ONLY" ),
            'order by case (PG-02)' => array( "SELECT a FROM t ORDER BY CASE WHEN a = 1 THEN 0 ELSE 1 END, a", 2, 0,
                                              "SELECT a FROM t ORDER BY CASE WHEN a = 1 THEN 0 ELSE 1 END, a\nOFFSET 0 ROWS FETCH NEXT 2 ROWS ONLY" ),
            'order by only in subquery' => array( 'SELECT * FROM (SELECT TOP 5 id FROM t ORDER BY id) x', 2, 0,
                                                  "SELECT * FROM (SELECT TOP 5 id FROM t ORDER BY id) x$null\nOFFSET 0 ROWS FETCH NEXT 2 ROWS ONLY" ),
            'order by only in over()' => array( 'SELECT id, ROW_NUMBER() OVER (ORDER BY id) AS n FROM t', 2, 0,
                                                "SELECT id, ROW_NUMBER() OVER (ORDER BY id) AS n FROM t$null\nOFFSET 0 ROWS FETCH NEXT 2 ROWS ONLY" ),
            'order by in literal' => array( "SELECT 'ORDER BY x' AS s FROM t", 1, 0, "SELECT 'ORDER BY x' AS s FROM t$null\nOFFSET 0 ROWS FETCH NEXT 1 ROWS ONLY" ),
            'order by in comment' => array( "SELECT a FROM t -- ORDER BY a\n", 1, 0, "SELECT a FROM t -- ORDER BY a$null\nOFFSET 0 ROWS FETCH NEXT 1 ROWS ONLY" ),
            'order by in cte' => array( 'WITH c AS (SELECT TOP 9 a FROM t ORDER BY a) SELECT a FROM c', 1, 1,
                                        "WITH c AS (SELECT TOP 9 a FROM t ORDER BY a) SELECT a FROM c$null\nOFFSET 1 ROWS FETCH NEXT 1 ROWS ONLY" ),
            'cte with outer order' => array( 'WITH c AS (SELECT a FROM t) SELECT a FROM c ORDER BY a', 1, 1,
                                             "WITH c AS (SELECT a FROM t) SELECT a FROM c ORDER BY a\nOFFSET 1 ROWS FETCH NEXT 1 ROWS ONLY" ),
            'bracketed order column' => array( 'SELECT [order] FROM t ORDER BY [order]', 1, 0, "SELECT [order] FROM t ORDER BY [order]\nOFFSET 0 ROWS FETCH NEXT 1 ROWS ONLY" ),
            'column named order by in brackets only' => array( 'SELECT [order by] FROM t', 1, 0, "SELECT [order by] FROM t$null\nOFFSET 0 ROWS FETCH NEXT 1 ROWS ONLY" ),
            'offset only' => array( 'SELECT id FROM t ORDER BY id', false, 7, "SELECT id FROM t ORDER BY id\nOFFSET 7 ROWS" ),
            'offset only no order' => array( 'SELECT id FROM t', null, 7, "SELECT id FROM t$null\nOFFSET 7 ROWS" ),
            'string numbers' => array( 'SELECT id FROM t ORDER BY id', '10', '20', "SELECT id FROM t ORDER BY id\nOFFSET 20 ROWS FETCH NEXT 10 ROWS ONLY" ),
            'negative offset is zero' => array( 'SELECT id FROM t ORDER BY id', 1, -5, "SELECT id FROM t ORDER BY id\nOFFSET 0 ROWS FETCH NEXT 1 ROWS ONLY" ),
            'trailing semicolons' => array( 'SELECT id FROM t ORDER BY id;;  ', 1, 0, "SELECT id FROM t ORDER BY id\nOFFSET 0 ROWS FETCH NEXT 1 ROWS ONLY" ),
            'top wrapped' => array( 'SELECT TOP 100 id FROM t ORDER BY id', 10, 10,
                                    "SELECT * FROM (\nSELECT TOP 100 id FROM t ORDER BY id\n) AS exp_page$null\nOFFSET 10 ROWS FETCH NEXT 10 ROWS ONLY" ),
            'distinct top wrapped' => array( 'SELECT DISTINCT TOP (5) id FROM t', 1, 0,
                                             "SELECT * FROM (\nSELECT DISTINCT TOP (5) id FROM t\n) AS exp_page$null\nOFFSET 0 ROWS FETCH NEXT 1 ROWS ONLY" ),
            'own offset wrapped' => array( 'SELECT id FROM t ORDER BY id OFFSET 5 ROWS', 2, 0,
                                           "SELECT * FROM (\nSELECT id FROM t ORDER BY id OFFSET 5 ROWS\n) AS exp_page$null\nOFFSET 0 ROWS FETCH NEXT 2 ROWS ONLY" ),
            'distinct without order wrapped' => array( 'SELECT DISTINCT a FROM t', 3, 0,
                                                       "SELECT * FROM (\nSELECT DISTINCT a FROM t\n) AS exp_page$null\nOFFSET 0 ROWS FETCH NEXT 3 ROWS ONLY" ),
            'distinct with order kept' => array( 'SELECT DISTINCT a FROM t ORDER BY a', 3, 0, "SELECT DISTINCT a FROM t ORDER BY a\nOFFSET 0 ROWS FETCH NEXT 3 ROWS ONLY" ),
            'union without order wrapped' => array( 'SELECT a FROM t UNION SELECT a FROM u', 3, 0,
                                                    "SELECT * FROM (\nSELECT a FROM t UNION SELECT a FROM u\n) AS exp_page$null\nOFFSET 0 ROWS FETCH NEXT 3 ROWS ONLY" ),
            'union all with outer order kept' => array( 'SELECT a FROM t UNION ALL SELECT a FROM u ORDER BY a', 3, 0,
                                                        "SELECT a FROM t UNION ALL SELECT a FROM u ORDER BY a\nOFFSET 0 ROWS FETCH NEXT 3 ROWS ONLY" ),
            'except wrapped' => array( 'SELECT a FROM t EXCEPT SELECT a FROM u', 1, 0,
                                       "SELECT * FROM (\nSELECT a FROM t EXCEPT SELECT a FROM u\n) AS exp_page$null\nOFFSET 0 ROWS FETCH NEXT 1 ROWS ONLY" ),
            'option stays last' => array( 'SELECT a FROM t ORDER BY a OPTION (RECOMPILE)', 1, 0, "SELECT a FROM t ORDER BY a\nOFFSET 0 ROWS FETCH NEXT 1 ROWS ONLY OPTION (RECOMPILE)" ),
            'option without order' => array( 'SELECT a FROM t OPTION (MAXDOP 1)', 1, 0, "SELECT a FROM t$null\nOFFSET 0 ROWS FETCH NEXT 1 ROWS ONLY OPTION (MAXDOP 1)" ),
            'for json stays last' => array( 'SELECT a FROM t ORDER BY a FOR JSON PATH', 1, 0, "SELECT a FROM t ORDER BY a\nOFFSET 0 ROWS FETCH NEXT 1 ROWS ONLY FOR JSON PATH" ),
            'lower case keywords' => array( 'select id from t order by id', 1, 0, "select id from t order by id\nOFFSET 0 ROWS FETCH NEXT 1 ROWS ONLY" ),
            'parenthesized select' => array( '(SELECT id FROM t)', 1, 0, "(SELECT id FROM t)$null\nOFFSET 0 ROWS FETCH NEXT 1 ROWS ONLY" ),
            'big numbers' => array( 'SELECT id FROM t ORDER BY id', 2147483647, 2147483647, "SELECT id FROM t ORDER BY id\nOFFSET 2147483647 ROWS FETCH NEXT 2147483647 ROWS ONLY" ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('cases')]
    public function testApplyLimit( $sql, $limit, $offset, $expected )
    {
        $this->assertSame( $expected, expSQLServerDialect::applyLimit( $sql, $limit, $offset ) );
    }

    public static function unchanged()
    {
        return array(
            'no limit no offset' => array( 'SELECT id FROM t', false, 0 ),
            'null limit' => array( 'SELECT id FROM t', null, 0 ),
            'empty limit' => array( 'SELECT id FROM t', '', 0 ),
            'negative limit' => array( 'SELECT id FROM t', -1, 0 ),
            'non numeric limit' => array( 'SELECT id FROM t', 'abc', 0 ),
            'non numeric offset' => array( 'SELECT id FROM t', false, 'x' ),
            'update' => array( 'UPDATE t SET a = 1', 10, 5 ),
            'delete' => array( 'DELETE FROM t', 10, 5 ),
            'exec' => array( 'EXEC sp_who', 10, 5 ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('unchanged')]
    public function testStatementsLeftAsTheyAre( $sql, $limit, $offset )
    {
        $this->assertSame( $sql, expSQLServerDialect::applyLimit( $sql, $limit, $offset ) );
    }

    public static function zeroLimits()
    {
        return array( array( 0 ), array( '0' ), array( 0.0 ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('zeroLimits')]
    public function testLimitZeroIsNoRows( $limit )
    {
        $this->assertNull( expSQLServerDialect::applyLimit( 'SELECT id FROM t ORDER BY id', $limit, 5 ) );
    }

    public function testExactlyOneOuterOffset()
    {
        foreach ( self::cases() as $name => $case )
        {
            $sql = expSQLServerDialect::applyLimit( $case[0], $case[1], $case[2] );
            $shape = expSQLServerDialect::outerShape( $sql );
            $this->assertTrue( $shape['offset'], $name );
            $tokens = expSQLServerLexer::tokenize( $sql );
            $outerOffsets = 0;
            foreach ( expSQLServerLexer::significant( $tokens ) as $entry )
                if ( $entry[1] === 0 && expSQLServerLexer::keyword( $tokens[$entry[0]] ) === 'OFFSET' )
                    $outerOffsets++;
            $this->assertSame( 1, $outerOffsets, $name );
        }
    }

    public function testPdoLimitClauseMissesOrderByWithParenthesesWhileThisDoesNot()
    {
        // PG-02: the generic PDO driver's sqlserver dialect looks for 'order by' with no ')' after it
        require_once dirname( __DIR__, 5 ) . '/lib/ezdb/classes/exppdodb.php';
        $sql = 'SELECT name FROM t ORDER BY LOWER(name)';
        $pdo = expPDODB::limitClause( 'sqlserver', $sql, 3, 0 );
        $this->assertSame( 2, substr_count( strtoupper( $pdo ), 'ORDER BY' ), 'expPDODB appends a second ORDER BY' );
        $this->assertSame( 1, substr_count( strtoupper( expSQLServerDialect::applyLimit( $sql, 3, 0 ) ), 'ORDER BY' ) );
    }

    public function testOuterShapeFacts()
    {
        $shape = expSQLServerDialect::outerShape( 'SELECT DISTINCT TOP 3 a FROM t UNION SELECT b FROM u ORDER BY 1 OPTION (RECOMPILE)' );
        $this->assertTrue( $shape['distinct'] );
        $this->assertTrue( $shape['top'] );
        $this->assertTrue( $shape['setop'] );
        $this->assertNotNull( $shape['order'] );
        $this->assertNotNull( $shape['tail'] );
    }

    public function testOuterOrderByOffset()
    {
        $tokens = expSQLServerLexer::tokenize( 'SELECT a FROM (SELECT a FROM t ORDER BY a OFFSET 0 ROWS) x ORDER BY a' );
        $index = expSQLServerDialect::outerOrderBy( $tokens );
        $this->assertSame( 'ORDER', $tokens[$index]['text'] );
        $this->assertSame( 'SELECT a FROM (SELECT a FROM t ORDER BY a OFFSET 0 ROWS) x ', expSQLServerLexer::join( array_slice( $tokens, 0, $index ) ) );
        $this->assertNull( expSQLServerDialect::outerOrderBy( expSQLServerLexer::tokenize( 'SELECT a FROM t' ) ) );
    }
}
