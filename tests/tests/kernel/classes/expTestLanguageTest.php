<?php
/**
 * The test language Lorem Ipsum (qlo-ZZ, #523): its shipped locale file is valid and marked TestLanguage=true, its
 * code lies in the ISO 639-2 range reserved for local use, no default installation adds it, and the lorem ipsum text
 * exp:loremipsum writes per datatype (expTestLanguage). No database, no kernel bootstrap beyond eZINI and eZLocale.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/expTestLanguageTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__, 4 ) . '/kernel/classes/exptestlanguage.php';

class expTestLanguageTest extends PHPUnit\Framework\TestCase
{
    const STAMP = 1700000000;

    /** @var string */
    private $root;

    /** @var string */
    private $cwd;

    /** @var string */
    private $timezone;

    protected function setUp(): void
    {
        $this->root = dirname( __DIR__, 4 );
        $this->cwd = getcwd();
        chdir( $this->root );
        $this->timezone = date_default_timezone_get();
        date_default_timezone_set( 'UTC' );
    }

    protected function tearDown(): void
    {
        date_default_timezone_set( $this->timezone );
        chdir( $this->cwd );
    }

    // ---- the locale file -------------------------------------------------------------------------------------------

    public function testLoremIpsumIsShippedAndMarkedAsATestLanguage()
    {
        $file = 'share/locale/' . expTestLanguage::LOREM_IPSUM . '.ini';
        $this->assertFileExists( $file );
        $text = (string)file_get_contents( $file );
        $this->assertTrue( mb_check_encoding( $text, 'UTF-8' ) );
        $this->assertStringStartsWith( '#?ini charset="utf-8"?', $text );
        $this->assertTrue( expTestLanguage::isTestLocale( 'qlo-ZZ' ) );
        $this->assertContains( 'qlo-ZZ', expTestLanguage::testLocaleCodes() );

        $ini = new eZINI( 'qlo-ZZ.ini', 'share/locale', null, false, false );
        $this->assertSame( 'Lorem Ipsum', $ini->variable( 'RegionalSettings', 'LanguageName' ) );
        $this->assertSame( 'Lorem Ipsum', $ini->variable( 'RegionalSettings', 'InternationalLanguageName' ), 'the plain name, no "(test)"' );
        $this->assertSame( 'true', $ini->variable( 'LanguageSettings', 'TestLanguage' ) );
        $this->assertSame( 'utf-8', $ini->variable( 'Charset', 'Preferred' ) );
        $this->assertSame( 'qlo-ZZ', $ini->variable( 'HTTP', 'ContentLanguage' ) );
    }

    public function testTheCodeIsReservedForLocalUseSoNoRealLanguageHasIt()
    {
        list( $language, $country ) = explode( '-', expTestLanguage::LOREM_IPSUM );
        // ISO 639-2 qaa-qtz: reserved for local use
        $this->assertMatchesRegularExpression( '/^q[a-t][a-z]$/', $language );
        // ISO 3166 / CLDR ZZ: unknown region
        $this->assertSame( 'ZZ', $country );
        $this->assertMatchesRegularExpression( '/^' . eZLocale::localeRegexp( false, false ) . '$/', expTestLanguage::LOREM_IPSUM, 'the add form accepts the code' );
        // every locale in the reserved range is a test language, and only those are
        foreach ( glob( 'share/locale/*.ini' ) as $file )
        {
            $code = basename( $file, '.ini' );
            if ( preg_match( '/^q[a-t][a-z]-/', $code ) )
                $this->assertTrue( expTestLanguage::isTestLocaleFile( $file ), "$code is in the local-use range and must be marked TestLanguage=true" );
            elseif ( strpos( $code, '@' ) === false )
                $this->assertFalse( expTestLanguage::isTestLocaleFile( $file ), "$code is a real language" );
        }
    }

    public function testEzLocaleLoadsAndFormatsIt()
    {
        $locale = eZLocale::create( 'qlo-ZZ' );
        $this->assertTrue( $locale->isValid() );
        $this->assertSame( 'Lorem Ipsum', $locale->languageName() );
        $this->assertSame( 'Ipsumdies 14 Temporember 2023', $locale->formatDate( self::STAMP ) );
        $this->assertSame( '2023-11-14', $locale->formatShortDate( self::STAMP ) );
        $this->assertSame( 12, count( array_unique( array_map( array( $locale, 'longMonthName' ), range( 1, 12 ) ) ) ) );
        $this->assertSame( 12, count( array_unique( array_map( array( $locale, 'shortMonthName' ), range( 1, 12 ) ) ) ) );
        $this->assertSame( 7, count( array_unique( array_map( array( $locale, 'longDayName' ), range( 0, 6 ) ) ) ) );
        $this->assertSame( '-1,234,567.89', $locale->formatNumber( -1234567.891 ) );
        $this->assertStringContainsString( '¤', $locale->formatCurrency( 1234.5 ) );
    }

    public function testTheMarkIsReadFromItsGroupOnly()
    {
        $dir = $this->root . '/var/tmp/exptestlanguage-' . getmypid() . '-' . mt_rand();
        mkdir( $dir, 0777, true );
        file_put_contents( "$dir/a.ini", "[LanguageSettings]\nTestLanguage=true\n" );
        file_put_contents( "$dir/b.ini", "[RegionalSettings]\nTestLanguage=true\n" );
        file_put_contents( "$dir/c.ini", "[LanguageSettings]\n# TestLanguage=true\nTestLanguage=false\n" );
        try
        {
            $this->assertTrue( expTestLanguage::isTestLocaleFile( "$dir/a.ini" ) );
            $this->assertFalse( expTestLanguage::isTestLocaleFile( "$dir/b.ini" ) );
            $this->assertFalse( expTestLanguage::isTestLocaleFile( "$dir/c.ini" ) );
            $this->assertFalse( expTestLanguage::isTestLocaleFile( "$dir/missing.ini" ) );
            $this->assertFalse( expTestLanguage::isTestLocale( '../x' ) );
            $this->assertFalse( expTestLanguage::isTestLocale( 'eng-GB' ) );
        }
        finally
        {
            foreach ( array( 'a', 'b', 'c' ) as $name )
                unlink( "$dir/$name.ini" );
            rmdir( $dir );
        }
    }

    // ---- installed nowhere by default --------------------------------------------------------------------------

    public function testNoDefaultInstallationAddsIt()
    {
        $files = array_merge( glob( 'kernel/sql/*/*.sql' ), glob( 'kickstart.ini*' ), glob( 'settings/*.ini' ),
                              glob( 'settings/siteaccess/*/*.ini*' ), glob( 'extension/*/share/*.dba' ),
                              glob( 'extension/*/settings/*.ini*' ), glob( 'extension/*/settings/siteaccess/*/*.ini*' ),
                              glob( 'kernel/setup/*.php' ), glob( 'kernel/setup/*/*.php' ) );
        $this->assertNotEmpty( $files );
        foreach ( $files as $file )
        {
            if ( basename( $file ) === 'uiswitch.ini' )
                continue; // names it in a comment, to say it is never offered
            $this->assertStringNotContainsString( 'qlo-ZZ', (string)file_get_contents( $file ), "$file must not add the test language" );
        }
    }

    // ---- lorem ipsum per datatype ------------------------------------------------------------------------------

    public function testTextLineIsShortTitleCaseAndRespectsTheMaximumLength()
    {
        $line = expTestLanguage::valueFor( 'ezstring', '57:title' );
        $this->assertStringStartsWith( 'Lorem Ipsum ', $line );
        $this->assertStringNotContainsString( "\n", $line );
        $this->assertSame( $line, expTestLanguage::valueFor( 'ezstring', '57:title' ), 'the same seed gives the same text' );
        $this->assertNotSame( $line, expTestLanguage::valueFor( 'ezstring', '57:short_title' ) );
        $this->assertLessThanOrEqual( 10, strlen( expTestLanguage::valueFor( 'ezstring', '57:title', 10 ) ) );
    }

    public function testTextBlockIsParagraphsOfSentences()
    {
        $block = expTestLanguage::valueFor( 'eztext', '57:intro' );
        $paragraphs = explode( "\n\n", $block );
        $this->assertCount( 2, $paragraphs );
        foreach ( $paragraphs as $paragraph )
            $this->assertMatchesRegularExpression( '/^[A-Z][a-z ]+\.( [A-Z][a-z ]+\.){2}$/', $paragraph );
    }

    public function testRichTextIsWellFormedStoredXml()
    {
        $xml = expTestLanguage::valueFor( 'ezxmltext', '57:body' );
        $doc = new DOMDocument();
        $this->assertTrue( $doc->loadXML( $xml ), 'well-formed' );
        $this->assertSame( 'section', $doc->documentElement->tagName );
        $this->assertSame( 1, $doc->getElementsByTagName( 'header' )->length );
        $this->assertSame( 2, $doc->getElementsByTagName( 'paragraph' )->length );
        $this->assertSame( 1, $doc->getElementsByTagName( 'emphasize' )->length );
        $this->assertStringStartsWith( 'Lorem Ipsum', $doc->getElementsByTagName( 'header' )->item( 0 )->textContent );
    }

    public function testKeywordsAreDistinctAndCommaSeparated()
    {
        $keywords = explode( ', ', expTestLanguage::valueFor( 'ezkeyword', '57:tags' ) );
        $this->assertCount( 4, $keywords );
        $this->assertSame( 'lorem ipsum', $keywords[0] );
        $this->assertSame( $keywords, array_values( array_unique( $keywords ) ) );
    }

    public function testOtherDatatypesKeepTheirCopiedValue()
    {
        foreach ( array( 'ezinteger', 'ezimage', 'ezobjectrelationlist', 'ezboolean', 'ezdatetime', 'ezurl', '' ) as $type )
            $this->assertNull( expTestLanguage::valueFor( $type, '57:x' ), $type );
        foreach ( expTestLanguage::TEXT_DATATYPES as $type )
            $this->assertNotNull( expTestLanguage::valueFor( $type, '57:x' ), $type );
    }

    public function testWordsAreLoremIpsumAndDistinctWithinARun()
    {
        $words = expTestLanguage::words( 20, 'seed' );
        $this->assertCount( 20, $words );
        $this->assertSame( $words, array_values( array_unique( $words ) ) );
        $this->assertSame( array(), array_diff( $words, expTestLanguage::WORDS ) );
        $this->assertSame( array(), expTestLanguage::words( 0, 'seed' ) );
    }
}
