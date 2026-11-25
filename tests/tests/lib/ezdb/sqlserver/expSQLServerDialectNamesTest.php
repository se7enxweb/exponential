<?php
/**
 * expSQLServerDialect names (no server needed):
 *
 *  NM-01 - quoteIdentifier(): [name], ] doubled, at most 128 characters (116 for a #temp table), counted in
 *          characters not bytes; empty names and NUL bytes refused
 *  NM-02 - quoteName(): every part of schema.table / db.schema.table in brackets, parts already quoted kept
 *  NM-03 - Every reserved keyword of T-SQL is known and is bracketed by quoteIdentifierIfNeeded(); a kernel column
 *          name that is no keyword stays bare
 *  NM-04 - backticksToBrackets(): MySQL `names` become [names], nothing else changes
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expSQLServerTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('sqlserver')]
class expSQLServerDialectNamesTest extends PHPUnit\Framework\TestCase
{
    public static function reservedWords()
    {
        $list = array();
        foreach ( expSQLServerDialect::RESERVED_WORDS as $word )
            $list[$word] = array( $word );
        return $list;
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('reservedWords')]
    public function testReservedWordIsBracketed( $word )
    {
        $this->assertTrue( expSQLServerDialect::isReservedWord( strtolower( $word ) ) );
        $this->assertSame( '[' . strtolower( $word ) . ']', expSQLServerDialect::quoteIdentifierIfNeeded( strtolower( $word ) ) );
    }

    public static function plainKernelNames()
    {
        return array(
            array( 'ezcontentobject' ), array( 'contentobject_id' ), array( 'data_text' ), array( 'path_string' ), array( 'modified' ),
            array( 'name' ), array( 'value' ), array( 'version' ), array( 'status' ), array( 'language_mask' ), array( 'identifier' ),
            array( 'login' ), array( 'type' ), array( 'date' ), array( 'time' ), array( 'day' ), array( 'level' ), array( 'action' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('plainKernelNames')]
    public function testKernelNameThatIsNoKeywordStaysBare( $name )
    {
        $this->assertFalse( expSQLServerDialect::isReservedWord( $name ) );
        $this->assertSame( $name, expSQLServerDialect::quoteIdentifierIfNeeded( $name ) );
    }

    public static function kernelNamesThatAreKeywords()
    {
        // columns and tables of the kernel schema (or of MySQL-era extensions) that collide with T-SQL keywords
        return array( array( 'key' ), array( 'file' ), array( 'user' ), array( 'order' ), array( 'current' ), array( 'public' ),
                      array( 'rule' ), array( 'percent' ), array( 'plan' ), array( 'option' ), array( 'open' ), array( 'identity' ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('kernelNamesThatAreKeywords')]
    public function testKernelNameThatIsAKeywordIsBracketed( $name )
    {
        $this->assertSame( "[$name]", expSQLServerDialect::quoteIdentifierIfNeeded( $name ) );
    }

    public function testWordListHasNoDuplicates()
    {
        $this->assertSame( count( expSQLServerDialect::RESERVED_WORDS ), count( array_unique( expSQLServerDialect::RESERVED_WORDS ) ) );
        $this->assertGreaterThan( 170, count( expSQLServerDialect::RESERVED_WORDS ) );
    }

    public static function quotedNames()
    {
        return array(
            'plain' => array( 'ezuser', '[ezuser]' ),
            'close bracket doubled' => array( 'a]b', '[a]]b]' ),
            'open bracket kept' => array( 'a[b', '[a[b]' ),
            'space' => array( 'my table', '[my table]' ),
            'quote' => array( "o'neil", "[o'neil]" ),
            'unicode' => array( 'größe', '[größe]' ),
            'temp' => array( '#tmp', '[#tmp]' ),
            'dot is part of the name' => array( 'a.b', '[a.b]' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('quotedNames')]
    public function testQuoteIdentifier( $name, $expected )
    {
        $this->assertSame( $expected, expSQLServerDialect::quoteIdentifier( $name ) );
    }

    public static function multiPartNames()
    {
        return array(
            array( 'ezuser', '[ezuser]' ),
            array( 'dbo.ezuser', '[dbo].[ezuser]' ),
            array( 'site.dbo.ezuser', '[site].[dbo].[ezuser]' ),
            array( '[dbo].[ez user]', '[dbo].[ez user]' ),
            array( 'dbo.[a]]b]', '[dbo].[a]]b]' ),
            array( '"dbo"."t"', '[dbo].[t]' ),
            array( 'dbo . t', '[dbo].[t]' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('multiPartNames')]
    public function testQuoteName( $name, $expected )
    {
        $this->assertSame( $expected, expSQLServerDialect::quoteName( $name ) );
    }

    public function test128CharactersAreAccepted()
    {
        $name = str_repeat( 'a', 128 );
        $this->assertSame( "[$name]", expSQLServerDialect::quoteIdentifier( $name ) );
    }

    public function test129CharactersAreRefused()
    {
        $this->expectException( InvalidArgumentException::class );
        expSQLServerDialect::quoteIdentifier( str_repeat( 'a', 129 ) );
    }

    public function testLengthIsCountedInCharactersNotBytes()
    {
        // 128 characters of two bytes each: 256 bytes, still a valid sysname
        $name = str_repeat( 'ä', 128 );
        $this->assertSame( "[$name]", expSQLServerDialect::quoteIdentifier( $name ) );
    }

    public function testTempTableNameIsLimitedTo116Characters()
    {
        expSQLServerDialect::checkIdentifier( '#' . str_repeat( 't', 115 ) );
        $this->expectException( InvalidArgumentException::class );
        expSQLServerDialect::checkIdentifier( '#' . str_repeat( 't', 116 ) );
    }

    public function testGlobalTempTableNameMayHave128Characters()
    {
        expSQLServerDialect::checkIdentifier( '##' . str_repeat( 't', 126 ) );
        $this->assertTrue( true );
    }

    public function testEmptyNameIsRefused()
    {
        $this->expectException( InvalidArgumentException::class );
        expSQLServerDialect::quoteIdentifier( '' );
    }

    public function testNulByteIsRefused()
    {
        $this->expectException( InvalidArgumentException::class );
        expSQLServerDialect::quoteIdentifier( "a\0b" );
    }

    public function testNameNeedingBracketsBecauseOfASpace()
    {
        $this->assertSame( '[a b]', expSQLServerDialect::quoteIdentifierIfNeeded( 'a b' ) );
    }

    public function testNameStartingWithDigitNeedsBrackets()
    {
        $this->assertSame( '[1abc]', expSQLServerDialect::quoteIdentifierIfNeeded( '1abc' ) );
    }

    public static function backtickStatements()
    {
        return array(
            array( 'SELECT `a` FROM `t`', 'SELECT [a] FROM [t]' ),
            array( 'SELECT `a``b`', 'SELECT [a`b]' ),
            array( 'SELECT `a]b`', 'SELECT [a]]b]' ),
            array( "SELECT '`x`' FROM `t`", "SELECT '`x`' FROM [t]" ),
            array( 'SELECT 1 -- `x`', 'SELECT 1 -- `x`' ),
            array( 'SELECT [x]', 'SELECT [x]' ),
            array( 'SELECT `open', 'SELECT `open' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('backtickStatements')]
    public function testBackticksToBrackets( $sql, $expected )
    {
        $this->assertSame( $expected, expSQLServerDialect::backticksToBrackets( $sql ) );
    }
}
