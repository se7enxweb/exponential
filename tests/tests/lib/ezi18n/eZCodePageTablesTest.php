<?php
/**
 * Every codepage table in share/codepages read by eZCodePage, and eZCodePageMapper between two of them:
 *   - each table loads (isValid) and has a character range, and for every character it defines: the character
 *     written back from its Unicode value
 *     reads as the same Unicode value (unicodeToChar / charToUnicode, codeToUtf8 / utf8ToCode); double byte tables
 *     (cp932) included
 *   - convertString() to UTF-8 and convertStringFromUTF8() back for the text of each table's own characters,
 *     convertStringToUnicode() / convertUnicodeToString(), strlen() of single and double byte text, the substitute
 *     character for what a table cannot represent
 *   - eZCodePageMapper: iso-8859-1 to iso-8859-15 and back, characters without a counterpart replaced by '?',
 *     mapInputCode / mapOutputCode, the string helpers, a missing table
 *
 * Plain PHP and share/codepages; no cache is read or written (the codepage permission setting is taken away for
 * the test, and the mapper is created with its cache off). Codepage objects shared through $GLOBALS are restored in tearDown().
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezi18n
 */

class eZCodePageTablesTest extends PHPUnit\Framework\TestCase
{
    private $savedGlobals;
    private $savedPermissions;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->savedGlobals = array();
        foreach ( $GLOBALS as $key => $value )
        {
            if ( strpos( $key, 'eZCodePage' ) === 0 )
                $this->savedGlobals[$key] = $value;
        }
        // a kernel boot earlier in the process sets the cache permissions, which turns the codepage cache on:
        // taken away for the test, so nothing is read from or written to var/cache
        $this->savedPermissions = array_key_exists( 'EZCODEPAGEPERMISSIONS', $GLOBALS ) ? array( $GLOBALS['EZCODEPAGEPERMISSIONS'] ) : null;
        unset( $GLOBALS['EZCODEPAGEPERMISSIONS'] );
        foreach ( array_keys( $GLOBALS ) as $key )
        {
            if ( strpos( $key, 'eZCodePage' ) === 0 )
                unset( $GLOBALS[$key] );
        }
    }

    protected function tearDown(): void
    {
        foreach ( array_keys( $GLOBALS ) as $key )
        {
            if ( strpos( $key, 'eZCodePage' ) === 0 && !array_key_exists( $key, $this->savedGlobals ) )
                unset( $GLOBALS[$key] );
        }
        foreach ( $this->savedGlobals as $key => $value )
            $GLOBALS[$key] = $value;
        if ( $this->savedPermissions !== null )
            $GLOBALS['EZCODEPAGEPERMISSIONS'] = $this->savedPermissions[0];
    }

    public static function tableProvider()
    {
        $rows = array();
        foreach ( scandir( dirname( __DIR__, 4 ) . '/share/codepages' ) as $name )
        {
            if ( $name[0] !== '.' && is_file( dirname( __DIR__, 4 ) . '/share/codepages/' . $name ) )
                $rows[$name] = array( $name );
        }
        return $rows;
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('tableProvider')]
    public function testTableLoadsAndRoundTrips( $name )
    {
        $cp = new eZCodePage( $name, false );
        $this->assertTrue( $cp->isValid(), "$name loads" );
        $this->assertTrue( eZCodePage::exists( $name ) );
        $this->assertNotEmpty( $cp->UnicodeMap );
        $this->assertLessThanOrEqual( $cp->maxCharValue(), $cp->minCharValue() );

        $utf8 = eZUTF8Codec::instance();
        $checked = 0;
        foreach ( $cp->UnicodeMap as $code => $unicode )
        {
            $char = $cp->unicodeToChar( $unicode );
            $length = 0;
            $this->assertSame( $unicode, $cp->charToUnicode( $char, 0, $length ), sprintf( '%s: U+%04X through code 0x%X', $name, $unicode, $code ) );
            $this->assertSame( strlen( $char ), $length );
            $this->assertSame( $utf8->toUtf8( $unicode ), $cp->codeToUtf8( $code ), sprintf( '%s: UTF-8 of 0x%X', $name, $code ) );
            $this->assertSame( $unicode, $cp->codeToUnicode( $code ) );
            $this->assertSame( $unicode, $cp->codeToUnicode( $cp->unicodeToCode( $unicode ) ) );
            $this->assertSame( $unicode, $cp->codeToUnicode( $cp->utf8ToCode( $cp->codeToUtf8( $code ) ) ) );
            ++$checked;
        }
        $this->assertGreaterThan( 0, $checked );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('tableProvider')]
    public function testStringConversionRoundTrips( $name )
    {
        $cp = new eZCodePage( $name, false );
        // the table's own characters, as a string in the codepage
        $text = '';
        foreach ( array_slice( $cp->UnicodeMap, 0, 200, true ) as $unicode )
            $text .= $cp->unicodeToChar( $unicode );

        $asUtf8 = $cp->convertString( $text );
        $this->assertTrue( mb_check_encoding( $asUtf8, 'UTF-8' ), "$name gives valid UTF-8" );
        $this->assertSame( $text, $cp->convertStringFromUTF8( $asUtf8 ), "$name back from UTF-8" );

        $unicodeValues = $cp->convertStringToUnicode( $text );
        $this->assertSame( $text, $cp->convertUnicodeToString( $unicodeValues ) );
        $this->assertSame( count( $unicodeValues ), $cp->strlen( $text ) );
        $this->assertSame( count( $unicodeValues ), $cp->strlenFromUTF8( $asUtf8 ) );
    }

    public function testLatin1()
    {
        $cp = new eZCodePage( 'iso-8859-1', false );
        $this->assertSame( 'iso-8859-1', $cp->charsetCode() );
        $this->assertSame( 'iso-8859-1', $cp->requestedCharsetCode() );
        $this->assertSame( "gr\xC3\xBC\xC3\x9Fe", $cp->convertString( "gr\xFC\xDFe" ) );
        $this->assertSame( "gr\xFC\xDFe", $cp->convertStringFromUTF8( 'grüße' ) );
        $this->assertSame( 'a?b', $cp->convertStringFromUTF8( 'a€b' ), 'no euro sign in Latin-1' );
        $this->assertSame( '?', $cp->unicodeToChar( 0x20AC ) );
        $this->assertNull( $cp->unicodeToCode( 0x20AC ) );
        $this->assertSame( 63, $cp->substituteChar() );
        $cp->setSubstituteChar( 42 );
        $this->assertSame( 'a*b', $cp->convertStringFromUTF8( 'a€b' ) );
        $this->assertFalse( $cp->convertUnicodeToString( 'not an array' ) );
    }

    public function testDoubleByteTable()
    {
        $cp = new eZCodePage( 'cp932', false );
        $this->assertTrue( $cp->isValid() );
        $text = $cp->convertStringFromUTF8( 'aあ日b' );
        $this->assertSame( 6, strlen( $text ), 'two single and two double byte characters' );
        $this->assertSame( 4, $cp->strlen( $text ) );
        $this->assertSame( 'aあ日b', $cp->convertString( $text ) );
    }

    public function testMissingTable()
    {
        $this->assertFalse( eZCodePage::exists( 'no-such-codepage' ) );
        $cp = @new eZCodePage( 'no-such-codepage', false );
        $this->assertFalse( $cp->isValid() );
    }

    private function mapper( $from, $to )
    {
        // the codepages the mapper asks for, loaded without their cache
        foreach ( array( $from, $to ) as $code )
            eZCodePage::instance( eZCharsetInfo::realCharsetCode( $code ), false );
        return new eZCodePageMapper( $from, $to, false );
    }

    public function testMapperBetweenLatin1AndLatin9()
    {
        $map = $this->mapper( 'iso-8859-1', 'iso-8859-15' );
        $this->assertTrue( $map->isValid() );
        $this->assertSame( "abc\xE9", $map->convertString( "abc\xE9" ), 'é is the same code in both' );
        $this->assertSame( '?', $map->mapInputChar( "\xA4" ), 'the currency sign is not in Latin-9' );
        $this->assertNull( $map->mapInputCode( 0xA4 ) );
        $this->assertSame( 0xE9, $map->mapInputCode( 0xE9 ) );
        $this->assertSame( 0x41, $map->mapOutputCode( 0x41 ) );
        $this->assertSame( "\xE9", $map->mapOutputChar( "\xE9" ) );
        $this->assertSame( 63, $map->substituteCharacter() );

        $this->assertSame( 3, $map->strlen( 'abc' ) );
        $this->assertSame( 1, $map->strpos( 'abc', 'b' ) );
        $this->assertSame( 2, $map->strrpos( 'abcb', 'c' ) );
        $this->assertSame( 'bc', $map->substr( 'abcd', 1, 2 ) );

        $back = $this->mapper( 'iso-8859-15', 'iso-8859-1' );
        $this->assertSame( '?', $back->mapInputChar( "\xA4" ), 'the euro sign is not in Latin-1' );
    }

    public function testMapperToAscii()
    {
        $map = $this->mapper( 'iso-8859-1', 'us-ascii' );
        $this->assertSame( 'gr??e', $map->convertString( "gr\xFC\xDFe" ) );
    }

    public function testMapperWithAMissingTable()
    {
        $map = @new eZCodePageMapper( 'iso-8859-1', 'no-such-codepage', false );
        $this->assertFalse( $map->isValid() );
    }
}
