<?php
/**
 * expSQLServerLexer: the tokens every SQL rewrite of the SQL Server driver stands on (no server needed).
 *
 *  LX-01 - Lossless: the tokens joined are the statement again, for any input (also random bytes)
 *  LX-02 - Token types: literals (with '' and N''), [names] with ]], "names", `names`, comments (nested), numbers,
 *          binary, @variables, #temp tables, ?, punctuation
 *  LX-03 - A literal, bracket or comment left open runs to the end and is flagged
 *  LX-04 - literalValue()/identifierValue() undo the quoting
 *  LX-05 - significant(): parenthesis depth, noise left out
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expSQLServerTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('sqlserver')]
class expSQLServerLexerTest extends PHPUnit\Framework\TestCase
{
    private static function types( $sql, $comments = false )
    {
        $out = array();
        foreach ( expSQLServerLexer::tokenize( $sql ) as $token )
            if ( $token['type'] !== 'ws' && ( $comments || $token['type'] !== 'comment' ) )
                $out[] = $token['type'] . ':' . $token['text'];
        return $out;
    }

    public static function statements()
    {
        return array(
            'plain select' => array( 'SELECT a, b FROM t WHERE c = 1' ),
            'literal with doubled quote' => array( "SELECT 'it''s' AS x" ),
            'N literal' => array( "SELECT N'ä😀' AS x" ),
            'brackets with ]]' => array( 'SELECT [a]]b] FROM [dbo].[t]' ),
            'double quoted name' => array( 'SELECT "a""b" FROM t' ),
            'backticks' => array( 'SELECT `name` FROM `ezcontentobject`' ),
            'line comment' => array( "SELECT 1 -- it's a comment\nFROM t" ),
            'block comment' => array( "SELECT /* it's */ 1" ),
            'nested block comment' => array( "SELECT /* a /* b */ c */ 1" ),
            'open literal' => array( "SELECT 'abc" ),
            'open bracket' => array( 'SELECT [abc' ),
            'open comment' => array( 'SELECT /* abc' ),
            'binary' => array( 'SELECT 0x0aFF, 0x' ),
            'numbers' => array( 'SELECT 1, 1.5, .5, 1e10, 2.5E-3' ),
            'variables' => array( 'DECLARE @x INT; SELECT @@IDENTITY, @x' ),
            'temp tables' => array( 'SELECT * FROM #t JOIN ##g ON 1=1' ),
            'param' => array( 'SELECT ? , ?' ),
            'unicode name' => array( 'SELECT größe FROM t' ),
            'empty' => array( '' ),
            'only spaces' => array( "  \t\n " ),
            'invalid utf8' => array( "SELECT '\xff\xfe' AS b" ),
            'question in literal' => array( "SELECT '?' AS q" ),
            'semicolons' => array( 'SELECT 1; SELECT 2;' ),
            'operators' => array( 'SELECT a<>b, a>=b, a!=b, ~a, a^b, a%b' ),
            'N alone word' => array( 'SELECT N FROM t' ),
            'n lower literal' => array( "SELECT n'x'" ),
            'quote after word' => array( "SELECT x FROM t WHERE y IN('a','b')" ),
            'dash minus not comment' => array( 'SELECT 1 - 1, a-b' ),
            'slash not comment' => array( 'SELECT 4 / 2' ),
            'dollar action' => array( 'MERGE t USING s ON 1=1 WHEN MATCHED THEN DELETE OUTPUT $action;' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('statements')]
    public function testTokensJoinToTheStatement( $sql )
    {
        $this->assertSame( $sql, expSQLServerLexer::join( expSQLServerLexer::tokenize( $sql ) ) );
    }

    public static function seeds()
    {
        $list = array();
        for ( $i = 1; $i <= 40; $i++ )
            $list["seed $i"] = array( $i );
        return $list;
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('seeds')]
    public function testRandomBytesJoinToThemselves( $seed )
    {
        mt_srand( $seed );
        $alphabet = "ab N'\"[]`-/*?;()\n\t,.#@0x1\\\xc3\xa4\xff";
        $sql = '';
        $length = mt_rand( 0, 120 );
        for ( $i = 0; $i < $length; $i++ )
            $sql .= $alphabet[mt_rand( 0, strlen( $alphabet ) - 1 )];
        $this->assertSame( $sql, expSQLServerLexer::join( expSQLServerLexer::tokenize( $sql ) ) );
    }

    public function testLiteralWithDoubledQuoteIsOneToken()
    {
        $this->assertSame( array( 'word:SELECT', "string:'it''s'", 'word:AS', 'word:x' ), self::types( "SELECT 'it''s' AS x" ) );
    }

    public function testNLiteralIsOneNstringToken()
    {
        $this->assertSame( array( 'word:SELECT', "nstring:N'a'", 'punct:,', "nstring:n'b'" ), self::types( "SELECT N'a', n'b'" ) );
    }

    public function testWordEndingInNBeforeQuoteIsNotAnNLiteral()
    {
        $this->assertSame( array( 'word:IN', 'punct:(', "string:'a'", 'punct:)' ), self::types( "IN('a')" ) );
    }

    public function testBracketWithDoubledCloseIsOneIdent()
    {
        $tokens = expSQLServerLexer::tokenize( 'SELECT [a]]b]' );
        $this->assertSame( 'ident', $tokens[2]['type'] );
        $this->assertSame( 'a]b', expSQLServerLexer::identifierValue( $tokens[2] ) );
    }

    public function testDoubleQuotedIdentifierValue()
    {
        $tokens = expSQLServerLexer::tokenize( '"a""b"' );
        $this->assertSame( 'a"b', expSQLServerLexer::identifierValue( $tokens[0] ) );
    }

    public function testBacktickIdentifierValue()
    {
        $tokens = expSQLServerLexer::tokenize( '`a``b`' );
        $this->assertSame( 'a`b', expSQLServerLexer::identifierValue( $tokens[0] ) );
    }

    public function testLineCommentHidesQuote()
    {
        $this->assertSame( array( 'word:SELECT', 'number:1', "comment:-- it's", 'word:FROM', 'word:t' ), self::types( "SELECT 1 -- it's\nFROM t", true ) );
    }

    public function testNestedBlockCommentIsOneToken()
    {
        $this->assertSame( array( 'word:SELECT', 'comment:/* a /* b */ c */', 'number:1' ), self::types( 'SELECT /* a /* b */ c */ 1', true ) );
    }

    public function testOpenLiteralRunsToTheEndAndIsFlagged()
    {
        $tokens = expSQLServerLexer::tokenize( "SELECT 'ab" );
        $last = end( $tokens );
        $this->assertSame( 'string', $last['type'] );
        $this->assertTrue( $last['open'] );
        $this->assertSame( 'ab', expSQLServerLexer::literalValue( $last ) );
    }

    public function testLiteralEndingInEscapedQuoteIsOpen()
    {
        $tokens = expSQLServerLexer::tokenize( "'ab''" );
        $this->assertTrue( $tokens[0]['open'] );
    }

    public function testLiteralOfOneQuoteIsClosed()
    {
        $tokens = expSQLServerLexer::tokenize( "''''" );
        $this->assertCount( 1, $tokens );
        $this->assertFalse( $tokens[0]['open'] );
        $this->assertSame( "'", expSQLServerLexer::literalValue( $tokens[0] ) );
    }

    public function testEmptyLiteral()
    {
        $tokens = expSQLServerLexer::tokenize( "''" );
        $this->assertFalse( $tokens[0]['open'] );
        $this->assertSame( '', expSQLServerLexer::literalValue( $tokens[0] ) );
    }

    public function testOpenCommentIsFlagged()
    {
        $tokens = expSQLServerLexer::tokenize( 'SELECT /* x' );
        $this->assertTrue( end( $tokens )['open'] );
    }

    public function testOpenBracketIsFlagged()
    {
        $tokens = expSQLServerLexer::tokenize( 'SELECT [x' );
        $this->assertTrue( end( $tokens )['open'] );
    }

    public function testBinaryLiteral()
    {
        $this->assertSame( array( 'word:SELECT', 'binary:0x0aFF' ), self::types( 'SELECT 0x0aFF' ) );
    }

    public function testNumbers()
    {
        $this->assertSame( array( 'number:1', 'punct:,', 'number:1.5', 'punct:,', 'number:.5', 'punct:,', 'number:1e10' ), self::types( '1, 1.5, .5, 1e10' ) );
    }

    public function testVariablesAndTempTablesAreWords()
    {
        $this->assertSame( array( 'word:@@IDENTITY', 'word:@x', 'word:#t', 'word:##g' ), self::types( '@@IDENTITY @x #t ##g' ) );
    }

    public function testQuestionMarkIsAParamOnlyOutsideLiterals()
    {
        $this->assertSame( array( 'param:?', "string:'?'" ), self::types( "? '?'" ) );
    }

    public function testMinusAndSlashAreNotComments()
    {
        $this->assertSame( array( 'word:a', 'punct:-', 'word:b', 'punct:/', 'word:c' ), self::types( 'a-b/c' ) );
    }

    public function testNonAsciiNameIsOneWord()
    {
        $this->assertSame( array( 'word:größe' ), self::types( 'größe' ) );
    }

    public function testInvalidUtf8InsideLiteralDoesNotStopTheLexer()
    {
        $tokens = expSQLServerLexer::tokenize( "SELECT '\xff' AS x" );
        $this->assertSame( "\xff", expSQLServerLexer::literalValue( $tokens[2] ) );
        $this->assertSame( 'x', $tokens[6]['text'] );
    }

    public function testKeywordIsUpperCaseOnlyForWords()
    {
        $tokens = expSQLServerLexer::tokenize( "select 'x'" );
        $this->assertSame( 'SELECT', expSQLServerLexer::keyword( $tokens[0] ) );
        $this->assertSame( '', expSQLServerLexer::keyword( $tokens[2] ) );
    }

    public function testSignificantTracksDepth()
    {
        $tokens = expSQLServerLexer::tokenize( 'a ( b ( c ) ) d' );
        $depths = array();
        foreach ( expSQLServerLexer::significant( $tokens ) as $entry )
            $depths[] = $tokens[$entry[0]]['text'] . $entry[1];
        $this->assertSame( array( 'a0', '(0', 'b1', '(1', 'c2', ')1', ')0', 'd0' ), $depths );
    }

    public function testSignificantNeverGoesBelowZero()
    {
        $tokens = expSQLServerLexer::tokenize( ') ) a' );
        $significant = expSQLServerLexer::significant( $tokens );
        $this->assertSame( 0, end( $significant )[1] );
    }

    public function testSameStatementTwiceGivesTheSameTokens()
    {
        $a = expSQLServerLexer::tokenize( "SELECT 'a'" );
        $b = expSQLServerLexer::tokenize( "SELECT 'a'" );
        $this->assertSame( $a, $b );
    }

    public function testLongLiteralIsOneToken()
    {
        $value = str_repeat( "x'y", 50000 );
        $sql = "SELECT '" . str_replace( "'", "''", $value ) . "'";
        $tokens = expSQLServerLexer::tokenize( $sql );
        $this->assertCount( 3, $tokens );
        $this->assertSame( $value, expSQLServerLexer::literalValue( $tokens[2] ) );
    }
}
