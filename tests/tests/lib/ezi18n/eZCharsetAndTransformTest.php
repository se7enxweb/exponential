<?php
/**
 * Tests of lib/ezi18n without the kernel: eZCharTransform (the urlalias, urlalias_iri, urlalias_compat,
 * identifier, search, lowercase and uppercase groups, the URL cleanup commands and the word separator), eZUTF8Codec,
 * eZTextCodec and eZCodePage conversions between UTF-8, ISO-8859-1, ISO-8859-15 and windows-1252, eZCharsetInfo
 * aliases and the pseudo translators eZBorkTranslator and eZ1337Translator.
 *
 * Transformations run with useCache=false and code pages are loaded without their cache, so nothing is written.
 * The word separator is set explicitly, so the result does not depend on the installation's site.ini.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezi18n
 */

class eZCharsetAndTransformTest extends PHPUnit\Framework\TestCase
{
    private $separator;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->separator = array_key_exists( 'eZCharTransform_wordSeparator', $GLOBALS ) ? array( $GLOBALS['eZCharTransform_wordSeparator'] ) : null;
        $GLOBALS['eZCharTransform_wordSeparator'] = '-';
    }

    protected function tearDown(): void
    {
        if ( $this->separator === null )
            unset( $GLOBALS['eZCharTransform_wordSeparator'] );
        else
            $GLOBALS['eZCharTransform_wordSeparator'] = $this->separator[0];
    }

    private static function transform( $text, $group )
    {
        return eZCharTransform::instance()->transformByGroup( $text, $group, 'utf-8', false );
    }

    // ---------------------------------------------------------------- transformation groups

    public static function groupProvider()
    {
        return array(
            'urlalias spaces'          => array( 'urlalias', 'Hello World', 'hello-world' ),
            'urlalias punctuation'     => array( 'urlalias', 'What? Now, really!', 'what-now-really' ),
            'urlalias latin accents'   => array( 'urlalias', 'Crème brûlée', 'creme-brulee' ),
            'urlalias german'          => array( 'urlalias', 'Grüße aus Köln', 'gruesse-aus-koeln' ),
            'urlalias norwegian'       => array( 'urlalias', 'Blåbær og øl', 'blaabaer-og-oel' ),
            'urlalias trims separator' => array( 'urlalias', '  --Edge case--  ', 'edge-case' ),
            'urlalias double dots'     => array( 'urlalias', 'a..b', 'a-b' ),
            'urlalias keeps dot'       => array( 'urlalias', 'version 1.2', 'version-1.2' ),
            'urlalias underscore'      => array( 'urlalias', 'snake_case', 'snake_case' ),
            'urlalias_compat'          => array( 'urlalias_compat', 'Hello World Again', 'hello_world_again' ),
            'urlalias_compat accents'  => array( 'urlalias_compat', 'Crème', 'creme' ),
            'urlalias_iri keeps utf8'  => array( 'urlalias_iri', 'Crème brûlée', 'Crème-brûlée' ),
            'urlalias_iri reserved'    => array( 'urlalias_iri', 'a/b?c=d&e', 'a-b-c-d-e' ),
            'identifier'               => array( 'identifier', 'My Class Name', 'my_class_name' ),
            'lowercase'                => array( 'lowercase', 'ÆØÅ ABC', 'æøå abc' ),
            'uppercase'                => array( 'uppercase', 'æøå abc', 'ÆØÅ ABC' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('groupProvider')]
    public function testTransformByGroup( $group, $input, $expected )
    {
        $this->assertSame( $expected, self::transform( $input, $group ) );
    }

    public function testSearchGroupNormalisesCaseAndAccents()
    {
        $this->assertSame( self::transform( 'creme brulee', 'search' ), self::transform( 'Crème Brûlée', 'search' ) );
    }

    public function testUrlAliasWithUnderscoreSeparator()
    {
        $GLOBALS['eZCharTransform_wordSeparator'] = '_';
        $this->assertSame( 'hello_world', self::transform( 'Hello  World', 'urlalias' ) );
    }

    public function testGroupCommandsOfAKnownAndAnUnknownGroup()
    {
        $trans = eZCharTransform::instance();
        $this->assertNotEmpty( $trans->groupCommands( 'urlalias' ) );
        $this->assertFalse( $trans->groupCommands( 'no_such_group_t1' ) );
    }

    public function testTransformWithRules()
    {
        $this->assertSame( 'abc', eZCharTransform::instance()->transform( 'ABC', 'ascii_lowercase', 'utf-8', false ) );
    }

    public static function urlCleanupProvider()
    {
        return array(
            'cleanup'        => array( 'commandUrlCleanup', 'a b!c', 'a-b!c' ),
            // a trailing "!" is stripped, a leading one is kept
            'cleanup strip'  => array( 'commandUrlCleanup', '!a b!', '!a-b' ),
            'cleanup multi'  => array( 'commandUrlCleanup', 'a---b', 'a-b' ),
            'compat'         => array( 'commandUrlCleanupCompat', 'A B-C', 'a_b_c' ),
            'iri'            => array( 'commandUrlCleanupIRI', 'å (b) + c', 'å-b-c' ),
            'iri strip dots' => array( 'commandUrlCleanupIRI', '.a.', 'a' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('urlCleanupProvider')]
    public function testUrlCleanupCommands( $command, $input, $expected )
    {
        $this->assertSame( $expected, eZCharTransform::$command( $input, 'utf-8' ) );
    }

    public function testWordSeparatorFromSettings()
    {
        unset( $GLOBALS['eZCharTransform_wordSeparator'] );
        $ini = eZINI::instance();
        $old = $ini->variable( 'URLTranslator', 'WordSeparator' );
        try
        {
            foreach ( array( 'dash' => '-', 'underscore' => '_', 'space' => ' ', 'something' => '-' ) as $setting => $char )
            {
                unset( $GLOBALS['eZCharTransform_wordSeparator'] );
                $ini->setVariable( 'URLTranslator', 'WordSeparator', $setting );
                $this->assertSame( $char, eZCharTransform::wordSeparator(), $setting );
            }
        }
        finally
        {
            $ini->setVariable( 'URLTranslator', 'WordSeparator', $old );
            unset( $GLOBALS['eZCharTransform_wordSeparator'] );
        }
    }

    // ---------------------------------------------------------------- UTF-8 codec

    public static function utf8Provider()
    {
        return array(
            'ascii'     => array( 'A', array( 65 ) ),
            'two bytes' => array( 'é', array( 0xE9 ) ),
            'three'     => array( '€', array( 0x20AC ) ),
            'four'      => array( "\u{1F600}", array( 0x1F600 ) ),
            'mixed'     => array( 'aé€', array( 97, 0xE9, 0x20AC ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('utf8Provider')]
    public function testUtf8ToUnicodeAndBack( $string, $codes )
    {
        $this->assertSame( $codes, eZUTF8Codec::convertStringToUnicode( $string ) );
        $this->assertSame( $string, eZUTF8Codec::convertUnicodeToString( $codes ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('utf8Provider')]
    public function testUtf8CharacterEncoding( $string, $codes )
    {
        $encoded = '';
        foreach ( $codes as $code )
            $encoded .= eZUTF8Codec::toUTF8( $code );
        $this->assertSame( $string, $encoded );
        $this->assertSame( count( $codes ), eZUTF8Codec::strlen( $string ) );
    }

    public function testUtf8FromUtf8ReadsOneCharacterAndItsLength()
    {
        $len = 0;
        $this->assertSame( 0x20AC, eZUTF8Codec::fromUtf8( "x€", 1, $len ) );
        $this->assertSame( 3, $len );
        $this->assertSame( 2, eZUTF8Codec::characterByteLength( 'é', 0 ) );
    }

    // ---------------------------------------------------------------- text codecs and code pages

    public static function codecProvider()
    {
        return array(
            'latin1 to utf8'   => array( 'iso-8859-1', 'utf-8', "\xE6\xF8\xE5", 'æøå' ),
            'utf8 to latin1'   => array( 'utf-8', 'iso-8859-1', 'æøå', "\xE6\xF8\xE5" ),
            'latin9 euro'      => array( 'iso-8859-15', 'utf-8', "\xA4", '€' ),
            'cp1252 euro'      => array( 'windows-1252', 'utf-8', "\x80", '€' ),
            'same charset'     => array( 'utf-8', 'utf-8', 'æøå', 'æøå' ),
            'ascii unchanged'  => array( 'iso-8859-1', 'utf-8', 'plain', 'plain' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('codecProvider')]
    public function testTextCodecConverts( $from, $to, $input, $expected )
    {
        $codec = eZTextCodec::instance( $from, $to );
        $this->assertSame( $expected, $codec->convertString( $input ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('codecProvider')]
    public function testTextCodecWithoutMBString( $from, $to, $input, $expected )
    {
        $codec = new eZTextCodec( $from, $to, eZCharsetInfo::realCharsetCode( $from ), eZCharsetInfo::realCharsetCode( $to ),
                                  eZCharsetInfo::characterEncodingScheme( $from ), eZCharsetInfo::characterEncodingScheme( $to ) );
        $codec->setUseMBString( false );
        $this->assertSame( $expected, $codec->convertString( $input ) );
    }

    public function testTextCodecStrlenCountsCharacters()
    {
        $this->assertSame( 3, eZTextCodec::instance( 'utf-8', 'utf-8' )->strlen( 'æøå' ) );
        $this->assertSame( 3, eZTextCodec::instance( 'iso-8859-1', 'iso-8859-1' )->strlen( "\xE6\xF8\xE5" ) );
    }

    public function testCodePageLoadsAndConverts()
    {
        $cp = new eZCodePage( 'iso-8859-1', false );
        $this->assertTrue( $cp->isValid() );
        $this->assertSame( 'iso-8859-1', $cp->charsetCode() );
        $this->assertSame( array( 0xE6, 65 ), $cp->convertStringToUnicode( "\xE6A" ) );
        $this->assertSame( "\xE6A", $cp->convertUnicodeToString( array( 0xE6, 65 ) ) );
        $this->assertSame( "\xE6", $cp->convertStringFromUTF8( 'æ' ) );
        $this->assertSame( 'æ', $cp->convertString( "\xE6" ) );
        $this->assertSame( 2, $cp->strlen( "\xE6A" ) );
    }

    public function testCodePageSubstitutesCharactersItCannotEncode()
    {
        $cp = new eZCodePage( 'iso-8859-1', false );
        $this->assertSame( 'a' . chr( $cp->substituteChar() ) . 'b', $cp->convertStringFromUTF8( 'a€b' ) );
        $cp->setSubstituteChar( ord( '#' ) );
        $this->assertSame( 'a#b', $cp->convertStringFromUTF8( 'a€b' ) );
    }

    public function testCodePageExistence()
    {
        $this->assertTrue( eZCodePage::exists( 'iso-8859-1' ) );
        $this->assertFalse( eZCodePage::exists( 'no-such-charset-t1' ) );
    }

    public static function aliasProvider()
    {
        return array(
            'latin1'      => array( 'latin1', 'iso-8859-1' ),
            'upper case'  => array( 'ISO-8859-1', 'iso-8859-1' ),
            'utf8'        => array( 'utf8', 'utf-8' ),
            'unknown'     => array( 'x-t1-charset', 'x-t1-charset' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('aliasProvider')]
    public function testCharsetAliases( $alias, $real )
    {
        $this->assertSame( $real, eZCharsetInfo::realCharsetCode( $alias ) );
    }

    public function testCharacterEncodingSchemes()
    {
        $this->assertSame( 'utf-8', eZCharsetInfo::characterEncodingScheme( 'utf-8' ) );
        $this->assertSame( 'singlebyte', eZCharsetInfo::characterEncodingScheme( 'iso-8859-1' ) );
    }

    // ---------------------------------------------------------------- pseudo translators

    public function testBorkTranslatorKeepsPlaceholders()
    {
        $bork = new eZBorkTranslator();
        $message = $bork->translate( 'test', 'Delete %count items' );
        $this->assertStringContainsString( '%count', $message );
        $this->assertNotSame( 'Delete %count items', $message );
    }

    public function testBorkTranslatorOnTextStartingWithAPlaceholder()
    {
        $bork = new eZBorkTranslator();
        $this->assertSame( '[%1 epples]', $bork->translate( 'test', '%1 apples' ) );
    }

    public function test1337TranslatorReplacesLetters()
    {
        $leet = new eZ1337Translator();
        $message = $leet->translate( 'test', 'leet' );
        $this->assertNotSame( 'leet', $message );
        $this->assertSame( strlen( 'leet' ), strlen( $message ) );
    }
}
