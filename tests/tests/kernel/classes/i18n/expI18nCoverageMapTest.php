<?php
/**
 * expI18nCoverageMap (kernel/classes/i18n, ./console exp:i18n coverage): the language totals and the world map at the
 * top of doc/eng-us/languages.md and doc/ger-de/sprachen.md, without a database.
 *
 *  CM-01  the committed totals, lists and maps are those the shipped files give (fails when they are out of date:
 *         run ./console exp:i18n coverage); the WebP copies exist and, where Imagick can render SVG, show the maps
 *  CM-02  on a private installation: translations and complete ones, locales, variants, the test language, language
 *         codes, right to left; a country is covered by its own translation, the first translation of its chain
 *         (FallbackMode=first) or English, not by a locale without a translation; private-use regions and locales
 *         without a region name no country; only the shipped settings/i18n.ini counts, not an override; the known
 *         languages (language codes of the real locales), the supported ones and those not translated
 *  CM-03  the map: covered countries, the uninhabited ones without a locale by design and their small-country circles
 *         in the green group with darker green borders, the others grey with grey borders, title,
 *         description and legend in the page's language, no script; the outline is required
 *  CM-04  the page: the generated part goes below the first heading, is replaced in place, keeps its date while
 *         nothing else changes and takes the new date when something does; write() then check() is clean, a
 *         changed file is reported
 *  CM-05  the generated part renders in the help viewer's renderer: the summary table, the image and the two
 *         collapsible lists, nothing of the markers shown
 *  CM-08  the raster check: a WebP copy differs from its map when one island circle or one country is drawn in the
 *         other colour, or when the size differs; edges, borders and the legend's fonts, which differ from one SVG
 *         renderer to the next, do not count
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 * @group ezi18n
 */

// a checkout whose class map was built before the class existed
if ( !class_exists( 'expI18nCoverageMap' ) )
    require_once __DIR__ . '/../../../../../kernel/classes/i18n/expi18ncoveragemap.php';

class expI18nCoverageMapTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $dir;

    protected function setUp(): void
    {
        expUISwitchLanguageCatalogue::resetMemory();
        $this->dir = dirname( __DIR__, 5 ) . '/var/tmp/phpunit-i18n-coverage-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        $locales = array(
            'ger-DE' => array( 'de-DE', '' ), 'ger-AT' => array( 'de-AT', '' ), 'ger-DE@euro' => array( 'de-DE', '' ),
            'fre-FR' => array( 'fr-FR', '' ), 'fre-CA' => array( 'fr-CA', '' ), 'bre-FR' => array( 'br-FR', '' ),
            'ara-SA' => array( 'ar-SA', 'rtl' ), 'ara-EG' => array( 'ar-EG', 'rtl' ), 'eng-GB' => array( 'en-GB', '' ),
            'eng-CA' => array( 'en-CA', '' ), 'swa-KE' => array( 'sw-KE', '' ), 'ser-SR' => array( 'sr-RS', '' ),
            'tlh-QO' => array( 'tlh', '' ), 'epo-EO' => array( 'eo', '' ), 'qlo-ZZ' => array( 'qlo-ZZ', '' ),
        );
        foreach ( $locales as $code => $info )
        {
            $test = $code === 'qlo-ZZ' ? "TestLanguage=true\n" : '';
            $direction = $info[1] === 'rtl' ? "TextDirection=rtl\n" : '';
            $this->write( "share/locale/$code.ini", "[RegionalSettings]\n[LanguageSettings]\n{$direction}{$test}LanguageName=$code\nInternationalLanguageName=$code\n[HTTP]\nContentLanguage={$info[0]}\n" );
        }
        $this->ts( 'share/translations/untranslated', array( 'A' => '', 'B' => '' ) );
        $this->ts( 'share/translations/ger-DE', array( 'A' => 'a', 'B' => 'b' ) );
        $this->ts( 'share/translations/fre-FR', array( 'A' => 'a', 'B' => 'b' ) );
        $this->ts( 'share/translations/ara-SA', array( 'A' => 'a', 'B' => '' ) );
        $this->ts( 'share/translations/ser-SR', array( 'A' => 'a', 'B' => 'b' ) );
        $this->ts( 'share/translations/tlh-QO', array( 'A' => 'a', 'B' => 'b' ) );
        // ger-AT and fre-CA have a line; ara-EG falls back by the automatic same-language rule; swa-KE is untranslated
        $this->write( 'settings/i18n.ini', "#?ini charset=\"utf-8\"?\n[TranslationSettings]\n# a comment\nFallbackRules[]\nAutomaticLanguageFallback=enabled\n"
                                           . "AutomaticFallbackExclude[]\nAutomaticFallbackExclude[]=eng\nFallbackMaxDepth=4\nFallbackMode=first\n"
                                           . "FallbackLanguages[ger-AT]=ger-DE\nFallbackLanguages[fre-CA]=fre-FR\nFallbackLanguages[eng-CA]=eng-GB\n"
                                           . "FallbackLanguages[swa-KE]=untranslated\n[Other]\nFallbackMode=merge\n" );
        // an override must not count: the result is that of a default installation
        $this->write( 'settings/override/i18n.ini.append.php', "<?php /*\n[TranslationSettings]\nFallbackLanguages[swa-KE]=ger-DE\n*/ ?>\n" );
        $this->write( 'settings/country.ini', "[DE]\nName=Germany\n[AT]\nName=Austria\n[FR]\nName=France\n[CA]\nName=Canada\n[SA]\nName=Saudi Arabia\n"
                                              . "[EG]\nName=Egypt\n[GB]\nName=United Kingdom\n[KE]\nName=Kenya\n[RS]\nName=Serbia\n"
                                              . "[AQ]\nName=Antarctica\n[AN]\n# Obsolete\nName=Netherlands Antilles\n[MS]\nName=Montserrat\n[QO]\nName=Outlying Oceania\n" );
        $this->write( 'share/locale/ger-DE.ini', "[RegionalSettings]\n[LanguageSettings]\nLanguageName=Deutsch\nInternationalLanguageName=German\n[HTTP]\nContentLanguage=de-DE\n"
                                                . "[CountryNames]\nCountries[DE]=Deutschland\nCountries[AT]=Österreich\nCountries[KE]=Kenia\n" );
        $paths = '';
        foreach ( array( 'AT', 'CA', 'DE', 'EG', 'FR', 'GB', 'KE', 'RS', 'SA', 'US', 'AQ', '' ) as $i => $iso )
            $paths .= '<path data-iso="' . $iso . '" d="M' . ( 10 * $i ) . ' 10l5 0 0 5z"/>' . "\n";
        $this->write( expI18nCoverageMap::OUTLINE, "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 2000 800\" width=\"2000\" height=\"800\">\n<g id=\"countries\">\n$paths</g>\n"
                                                    . "<g id=\"tiny\">\n<circle data-iso=\"SA\" cx=\"50\" cy=\"50\" r=\"6\"/>\n<circle data-iso=\"MC\" cx=\"60\" cy=\"60\" r=\"6\"/>\n</g>\n</svg>\n" );
        $this->write( 'doc/eng-us/languages.md', "# Languages in Exponential\n\nThe intro.\n\n## 1. Locales\n\nA.\n\n## 13. Translation fallbacks\n\nText.\n" );
        $this->write( 'doc/ger-de/sprachen.md', "# Sprachen in Exponential\n\nDie Einleitung.\n" );
    }

    protected function tearDown(): void
    {
        $this->remove( $this->dir );
        expUISwitchLanguageCatalogue::resetMemory();
    }

    private function write( $relative, $text )
    {
        $path = $this->dir . '/' . $relative;
        if ( !is_dir( dirname( $path ) ) )
            mkdir( dirname( $path ), 0777, true );
        file_put_contents( $path, $text );
    }

    private function ts( $dir, array $messages )
    {
        $xml = '';
        foreach ( $messages as $source => $translation )
            $xml .= '<message><source>' . $source . '</source><translation' . ( $translation === '' ? ' type="unfinished"' : '' ) . '>' . $translation . "</translation></message>\n";
        $this->write( $dir . '/translation.ts', "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<!DOCTYPE TS>\n<TS version=\"2.0\">\n<context><name>test</name>\n$xml</context>\n</TS>\n" );
    }

    private function remove( $path )
    {
        if ( is_dir( $path ) && !is_link( $path ) )
        {
            foreach ( scandir( $path ) as $name )
            {
                if ( $name !== '.' && $name !== '..' )
                    $this->remove( $path . '/' . $name );
            }
            rmdir( $path );
        }
        elseif ( is_file( $path ) || is_link( $path ) )
            unlink( $path );
    }

    /** CM-01 */
    public function testTheCommittedTotalsAndMapsAreCurrent()
    {
        $root = dirname( __DIR__, 5 );
        $map = new expI18nCoverageMap( $root );
        $problems = $map->check( date( 'Y-m-d' ) );
        $this->assertSame( array(), $problems, "The language totals or maps are out of date: run ./console exp:i18n coverage\n" . implode( "\n", $problems ) );
        if ( !expI18nCoverageMap::canRasterize() )
            $this->addToAssertionCount( 1 ); // the WebP copies were only checked to exist
        $data = $map->data();
        $this->assertSame( $data['translations']['total'], count( glob( $root . '/share/translations/*/translation.ts' ) ) - 1, 'every translation but the source texts' );
        $this->assertSame( count( glob( $root . '/share/locale/*.ini' ) ), $data['locales']['files'] );
        // every code of settings/country.ini has a locale, but those without one by design
        $this->assertSame( array(), $data['without']['missing'], 'codes of settings/country.ini without a locale: add one, or list the code in expI18nCoverageMap::NO_LOCALE_BY_DESIGN with the reason' );
        $this->assertSame( array( 'AN', 'AQ', 'BV', 'HM' ), array_keys( $data['without']['design'] ) );
        $this->assertSame( array( 'AQ', 'BV', 'HM' ), $map->greenByDesign() );
        // the shipped map: every territory with a covered locale or without a locale by design is drawn and green,
        // Antarctica, the overseas parts of France and Svalbard as shapes of their own, small islands as circles
        $drawn = array( 'covered' => array(), 'other' => array() );
        $doc = new DOMDocument();
        $this->assertTrue( $doc->loadXML( $map->svg( 'eng-us' ) ) );
        $xpath = new DOMXPath( $doc );
        $xpath->registerNamespace( 's', 'http://www.w3.org/2000/svg' );
        foreach ( array_keys( $drawn ) as $group )
        {
            foreach ( $xpath->query( "//s:g[@id='$group']/*" ) as $node )
                $drawn[$group][$node->getAttribute( 'data-iso' )] = true;
        }
        $this->assertSame( array(), array_keys( $drawn['other'] ), 'no grey land in a default installation' );
        $green = array();
        foreach ( $data['countries'] as $iso => $country )
        {
            if ( $country['covered'] )
                $green[] = $iso;
        }
        $green = array_merge( $green, $map->greenByDesign() );
        sort( $green );
        $codes = array_keys( $drawn['covered'] );
        sort( $codes );
        $this->assertSame( $green, $codes, 'every covered or by-design territory is drawn, and nothing else' );
        $this->assertCount( 250, $codes, '247 countries and territories with a locale and 3 uninhabited ones' );
        foreach ( array( 'AQ', 'GF', 'RE', 'SJ', 'FR', 'NO' ) as $iso )
            $this->assertCount( 1, $xpath->query( "//s:g[@id='covered']/s:path[@data-iso='$iso']" ), "$iso is a shape of its own" );
        foreach ( array( 'BV', 'HM', 'CC', 'CX', 'NF', 'MS', 'UM', 'YT', 'RE' ) as $iso )
            $this->assertGreaterThan( 0, $xpath->query( "//s:g[@id='covered']/s:circle[@data-iso='$iso']" )->length, "$iso has a circle" );
        foreach ( expI18nCoverageMap::NO_LOCALE_BY_DESIGN as $iso => $entry )
        {
            $this->assertArrayNotHasKey( $iso, $data['countries'], "$iso has a locale: take it out of NO_LOCALE_BY_DESIGN" );
            foreach ( isset( $entry['successors'] ) ? $entry['successors'] : array() as $successor )
                $this->assertArrayHasKey( $successor, $data['countries'], "$successor, successor of $iso, has a locale" );
        }
    }

    /** CM-08 */
    public function testTheRasterCheckSeesARecolouredTerritoryButNotTheRenderer()
    {
        if ( !expI18nCoverageMap::canRasterize() )
            $this->markTestSkipped( 'Imagick cannot render SVG to WebP here' );
        $root = dirname( __DIR__, 5 );
        $webp = $root . '/' . expI18nCoverageMap::PAGES['eng-us']['raster'];
        $svg = ( new expI18nCoverageMap( $root ) )->svg( 'eng-us' );
        // one island circle (radius 6) and one country drawn grey instead of green: the copy no longer shows the map
        foreach ( array( '<circle data-iso="AD" ', '<path data-iso="DE" ' ) as $element )
        {
            $this->assertSame( 1, substr_count( $svg, $element ), "$element is drawn once" );
            $difference = expI18nCoverageMap::rasterDifference( str_replace( $element, $element . 'fill="' . expI18nCoverageMap::OTHER . '" ', $svg ), $webp );
            $this->assertIsInt( $difference );
            $this->assertGreaterThan( 0, $difference, "$element recoloured" );
        }
        // a map of another size is never the same picture
        $this->assertNull( expI18nCoverageMap::rasterDifference( preg_replace( '/ width="\d+" height="\d+"/', ' width="1000" height="571"', $svg, 1 ), $webp ) );
    }

    /** CM-07 */
    public function testTheCodesWithoutALocaleAreListedByDesignOrAsNotYet()
    {
        $map = new expI18nCoverageMap( $this->dir );
        $data = $map->data();
        $this->assertSame( array( 'AN' => expI18nCoverageMap::NO_LOCALE_BY_DESIGN['AN'], 'AQ' => expI18nCoverageMap::NO_LOCALE_BY_DESIGN['AQ'] ), $data['without']['design'] );
        $this->assertSame( array( 'MS' ), $data['without']['missing'], 'a code with a locale and a user-assigned code (QO) are left out' );
        $english = $map->withoutLocale( 'eng-us' );
        $this->assertSame( array( 'AN', 'AQ', 'MS' ), array_column( $english, 'code' ) );
        $this->assertSame( array( true, true, false ), array_column( $english, 'design' ) );
        $this->assertSame( 'No locale by design: code withdrawn in 2010; its successors are BQ (BQ), CW (CW) and SX (SX), still without a locale: BQ, CW, SX', $english[0]['why'] );
        $this->assertSame( 'No locale by design: uninhabited, CLDR defines no locale for it (shown green: Exponential serves these territories in every language; no local conventions exist)', $english[1]['why'] );
        $this->assertSame( 'No locale yet', $english[2]['why'] );
        $german = $map->withoutLocale( 'ger-de' );
        $this->assertSame( 'Niederländische Antillen', $german[0]['name'], 'a withdrawn code ger-DE.ini does not name' );
        $this->assertSame( 'Keine Locale, mit Absicht: unbewohnt, CLDR definiert keine Locale dafür (grün dargestellt: Exponential bedient diese Gebiete in jeder Sprache; lokale Konventionen gibt es keine)', $german[1]['why'] );

        $files = $map->build( '2026-10-26' );
        $page = $files['doc/eng-us/languages.md'];
        $this->assertStringContainsString( '| Codes of ISO 3166-1 (`settings/country.ini`) without a locale | **3**: 2 by design, 1 not yet (list below the map) |', $page );
        $this->assertStringContainsString( "<details><summary>The 3 codes of ISO 3166-1 without a locale</summary>\n\n| Country or territory | Code | Why |\n|---|---|---|\n| Netherlands Antilles | AN | No locale by design: code withdrawn in 2010;", $page );
        $this->assertStringContainsString( "| Antarctica | AQ | No locale by design: uninhabited, CLDR defines no locale for it (shown green: Exponential serves these territories in every language; no local conventions exist) |\n| Montserrat | MS | No locale yet |\n\n</details>", $page );
        $this->assertStringContainsString( '| Codes aus ISO 3166-1 (`settings/country.ini`) ohne Locale | **3**: 2 mit Absicht, 1 noch nicht (Liste unter der Karte) |', $files['doc/ger-de/sprachen.md'] );
        $this->assertStringContainsString( '| Niederländische Antillen | AN | Keine Locale, mit Absicht: Code 2010 zurückgezogen; seine Nachfolger sind', $files['doc/ger-de/sprachen.md'] );

        // with locales for the successors and Montserrat, only the codes by design are left, named as such
        foreach ( array( 'dut-BQ' => 'nl-BQ', 'dut-CW' => 'nl-CW', 'dut-SX' => 'nl-SX', 'eng-MS' => 'en-MS' ) as $code => $http )
            $this->write( "share/locale/$code.ini", "[RegionalSettings]\nLanguageName=$code\nInternationalLanguageName=$code\n[HTTP]\nContentLanguage=$http\n" );
        $this->write( 'settings/country.ini', file_get_contents( $this->dir . '/settings/country.ini' ) . "[BQ]\nName=Caribbean Netherlands\n[CW]\nName=Curaçao\n[SX]\nName=Sint Maarten\n" );
        expUISwitchLanguageCatalogue::resetMemory();
        $map = new expI18nCoverageMap( $this->dir );
        $this->assertSame( array(), $map->data()['without']['missing'] );
        $this->assertSame( 'No locale by design: code withdrawn in 2010; its successors Caribbean Netherlands (BQ), Curaçao (CW) and Sint Maarten (SX) each have a locale of their own',
                           $map->withoutLocale( 'eng-us' )[0]['why'] );
        $files = $map->build( '2026-10-26' );
        $this->assertStringContainsString( '| Codes of ISO 3166-1 (`settings/country.ini`) without a locale | **2**, all of them by design: uninhabited or withdrawn (list below the map) |', $files['doc/eng-us/languages.md'] );
        $this->assertStringContainsString( '| **2**, alle mit Absicht: unbewohnt oder zurückgezogen (Liste unter der Karte) |', $files['doc/ger-de/sprachen.md'] );

        // without any code left, no section and a plain 0
        $this->write( 'settings/country.ini', "[DE]\nName=Germany\n" );
        $map = new expI18nCoverageMap( $this->dir );
        $this->assertSame( array(), $map->withoutLocale( 'eng-us' ) );
        $page = $map->build( '2026-10-26' )['doc/eng-us/languages.md'];
        $this->assertStringContainsString( '| Codes of ISO 3166-1 (`settings/country.ini`) without a locale | **0** |', $page );
        $this->assertStringNotContainsString( 'codes of ISO 3166-1 without a locale</summary>', $page );
    }

    /** CM-02 */
    public function testTotalsAndCoverage()
    {
        $data = ( new expI18nCoverageMap( $this->dir ) )->data();
        $this->assertSame( array( 'total' => 5, 'complete' => 4, 'codes' => array( 'ara-SA', 'fre-FR', 'ger-DE', 'ser-SR', 'tlh-QO' ) ), $data['translations'] );
        $this->assertSame( array( 'files' => 15, 'variants' => 1, 'test' => 1, 'base' => 14, 'languages' => 9, 'rtl' => 2 ), $data['locales'],
                           'languages: ara bre eng epo fre ger ser swa tlh (not the test language)' );
        $this->assertSame( array( 'AT', 'CA', 'DE', 'EG', 'FR', 'GB', 'KE', 'RS', 'SA' ), array_keys( $data['countries'] ), 'QO, ZZ and a locale without a region name no country' );
        $shows = array();
        foreach ( $data['countries'] as $iso => $country )
        {
            foreach ( $country['locales'] as $locale )
                $shows[$locale['locale']] = $locale['shows'];
        }
        $this->assertSame( array( 'ger-AT' => 'ger-DE', 'eng-CA' => 'eng', 'fre-CA' => 'fre-FR', 'ger-DE' => 'ger-DE', 'ara-EG' => 'ara-SA',
                                  'bre-FR' => '', 'fre-FR' => 'fre-FR', 'eng-GB' => 'eng', 'swa-KE' => '', 'ser-SR' => 'ser-SR', 'ara-SA' => 'ara-SA' ), $shows );
        $this->assertTrue( $data['countries']['FR']['covered'], 'one translated locale is enough' );
        $this->assertFalse( $data['countries']['KE']['covered'], 'the override is not read' );
        $this->assertSame( 8, $data['covered'] );
        $this->assertSame( array( 'ara', 'bre', 'eng', 'epo', 'fre', 'ger', 'ser', 'swa', 'tlh' ), array_keys( $data['languages'] ), 'known: every language code of the real locales, Klingon without a country too' );
        $this->assertSame( 6, $data['supported'], 'ara (own and fallback), eng (source texts), fre, ger, ser, tlh' );
        $map = new expI18nCoverageMap( $this->dir );
        $this->assertSame( array( 'bre', 'epo', 'swa' ), array_column( $map->untranslatedLanguages(), 'code' ) );
        $this->assertSame( 'German', expI18nCoverageMap::baseName( 'German (Austria)', 'x' ) );
        $this->assertSame( 'x', expI18nCoverageMap::baseName( '', 'x' ) );
        $this->assertSame( array( 'List' => array( 'x' ), 'Mode' => 'first', 'FallbackLanguages' => array( 'a' => 'b' ) ),
                           expI18nCoverageMap::parseBlock( "[B]\nList[]=old\nList[]\nList[]=x\n#Mode=no\nMode=first\nFallbackLanguages[a]=b\n[C]\nMode=merge\n", 'B' ) );
    }

    /** CM-03 */
    public function testTheMap()
    {
        $map = new expI18nCoverageMap( $this->dir );
        $svg = $map->svg( 'eng-us' );
        $this->assertStringStartsWith( '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2000 ' . ( 800 + expI18nCoverageMap::LEGEND_HEIGHT ) . '"', $svg );
        $doc = new DOMDocument();
        $this->assertTrue( $doc->loadXML( $svg ), 'well-formed' );
        $xpath = new DOMXPath( $doc );
        $xpath->registerNamespace( 's', 'http://www.w3.org/2000/svg' );
        $isos = function ( $group ) use ( $xpath )
        {
            $list = array();
            foreach ( $xpath->query( "//s:g[@id='$group']/*" ) as $node )
                $list[] = $node->localName . ':' . $node->getAttribute( 'data-iso' );
            return $list;
        };
        // Antarctica has no locale by design (uninhabited) and is green all the same; the withdrawn AN has no shape
        $this->assertSame( array( 'AQ' ), $map->greenByDesign() );
        $this->assertSame( array( 'path:AT', 'path:CA', 'path:DE', 'path:EG', 'path:FR', 'path:GB', 'path:RS', 'path:SA', 'path:AQ', 'circle:SA' ), $isos( 'covered' ) );
        $this->assertSame( array( 'path:KE', 'path:US', 'path:', 'circle:MC' ), $isos( 'other' ) );
        $covered = $xpath->query( "//s:g[@id='covered']" )->item( 0 );
        $other = $xpath->query( "//s:g[@id='other']" )->item( 0 );
        $this->assertSame( expI18nCoverageMap::COVERED, $covered->getAttribute( 'fill' ) );
        $this->assertSame( expI18nCoverageMap::OTHER, $other->getAttribute( 'fill' ) );
        // the borders of covered land are a darker green, never the grey of the other land, so that crowded small
        // islands read green in a thumbnail; small shapes get a thinner border
        $this->assertSame( expI18nCoverageMap::COVERED_STROKE, $covered->getAttribute( 'stroke' ) );
        $this->assertSame( expI18nCoverageMap::STROKE, $other->getAttribute( 'stroke' ) );
        $this->assertNotSame( expI18nCoverageMap::STROKE, expI18nCoverageMap::COVERED_STROKE );
        foreach ( $xpath->query( "//s:g[@id='covered']//*[@stroke] | //s:g[@id='other']//*[@stroke]" ) as $node )
            $this->fail( 'a shape with a stroke of its own: ' . $node->getAttribute( 'data-iso' ) );
        $this->assertSame( (string)expI18nCoverageMap::SMALL_STROKE_WIDTH, $xpath->query( "//s:g[@id='covered']/s:path[@data-iso='AT']" )->item( 0 )->getAttribute( 'stroke-width' ) );
        $this->assertSame( 20, expI18nCoverageMap::pathSize( 'M0 0l20 0 0 5zM3 3l1 1 -1 0z' ) );
        $this->assertSame( 30, expI18nCoverageMap::pathSize( 'M0 0l5 0 0 5zM10 10l1 0 0 20z' ) );
        $this->assertStringContainsString( '>Also green, without a locale by design (uninhabited): Antarctica.</text>', $svg );
        $this->assertStringContainsString( '>Exponential serves these territories in every language; no local conventions exist.</text>', $svg );
        $this->assertStringContainsString( '<title id="map-title">Exponential: the countries', $svg );
        $this->assertStringContainsString( 'Green: 8 countries and territories', $svg );
        $this->assertStringContainsString( '>Administration in the language of a shipped locale (8)</text>', $svg );
        $this->assertStringContainsString( 'Natural Earth', $svg );
        $this->assertDoesNotMatchRegularExpression( '/<script|\son[a-z]+=|javascript:|<foreignObject|xlink:href|href=/i', $svg );
        $german = $map->svg( 'ger-de' );
        $this->assertStringContainsString( '>Administration in der Sprache einer mitgelieferten Locale (8)</text>', $german );
        $this->assertStringContainsString( 'Grün: 8 Länder und Gebiete', $german );
        $this->assertStringContainsString( '>Ebenfalls grün, ohne Locale mit Absicht (unbewohnt): Antarctica.</text>', $german, 'ger-DE.ini of the fixture names no AQ' );

        $this->remove( $this->dir . '/' . expI18nCoverageMap::OUTLINE );
        $this->expectException( RuntimeException::class );
        ( new expI18nCoverageMap( $this->dir ) )->svg( 'eng-us' );
    }

    /** CM-04 */
    public function testThePagesAreWrittenAndChecked()
    {
        $map = new expI18nCoverageMap( $this->dir );
        $files = $map->build( '2026-10-26' );
        $page = $files['doc/eng-us/languages.md'];
        $this->assertStringStartsWith( "# Languages in Exponential\n\n<!-- generated: exp:i18n coverage 2026-10-26 (start) -->\n**At a glance, on 26 October 2026.**", $page );
        $this->assertStringContainsString( "<!-- generated: exp:i18n coverage (end) -->\n\nThe intro.", $page );
        $this->assertStringContainsString( '| Countries and territories of the locales | **9** |', $page );
        $this->assertStringContainsString( '| of them with the administration in their language (green on the map) | **8** |', $page );
        $this->assertStringContainsString( '| Languages of the administration: translations in `share/translations` | **5**, 4 of them complete |', $page );
        $this->assertStringContainsString( '| Locales written right to left | **2** |', $page );
        $this->assertStringContainsString( '| Known languages: the language codes of the locales (variants and the test language left out) | **9** |', $page );
        $this->assertStringContainsString( '| of them shown translated by the administration (own translation, fallback or English) | **6** |', $page );
        $this->assertStringContainsString( '| of them not translated ([list](#the-languages-not-translated-yet)) | **3** |', $page );
        $this->assertStringContainsString( "## The languages not translated yet\n", $page );
        $this->assertStringContainsString( "| swa-KE | swa-KE | `swa` | `swa-KE` (Kenya) |", $page );
        $this->assertStringContainsString( "<!-- generated: exp:i18n coverage languages (end) -->\n\n## 1. Locales", $page, 'before section 1' );
        $this->assertStringEndsWith( "</details>\n" . expI18nCoverageMap::LANGUAGES_END . "\n", $files['doc/ger-de/sprachen.md'], 'without a section 1: at the end' );
        $this->assertStringContainsString( "## Die noch nicht übersetzten Sprachen\n", $files['doc/ger-de/sprachen.md'] );
        $this->assertStringContainsString( '![World map: 8 countries and territories in green', $page );
        $this->assertStringContainsString( '](images/languages/coverage-map.webp)', $page );
        $this->assertStringContainsString( '| France | FR | `bre-FR` (untranslated), `fre-FR` |', $page );
        $this->assertStringContainsString( '| Austria | AT | `ger-AT` → `ger-DE` |', $page );
        $this->assertStringContainsString( '| Canada | CA | `eng-CA` (source texts), `fre-CA` → `fre-FR` |', $page );
        $this->assertStringContainsString( "| Kenya | KE | `swa-KE` |", $page );
        $german = $files['doc/ger-de/sprachen.md'];
        $this->assertStringContainsString( '**Auf einen Blick, Stand 26. Oktober 2026.**', $german );
        $this->assertStringContainsString( '| Österreich | AT | `ger-AT` → `ger-DE` |', $german );
        $this->assertStringContainsString( '| Kenia | KE | `swa-KE` |', $german );
        $this->assertStringContainsString( '](bilder/sprachen/abdeckungskarte.webp)', $german );

        $result = $map->write( '2026-10-26' );
        $this->assertSame( array(), array_values( array_diff( array( 'doc/eng-us/images/languages/coverage-map.svg', 'doc/eng-us/languages.md',
                                                                    'doc/ger-de/bilder/sprachen/abdeckungskarte.svg', 'doc/ger-de/sprachen.md' ), $result['written'] ) ) );
        if ( !expI18nCoverageMap::canRasterize() )
        {
            $this->assertNotSame( array(), $result['errors'] );
            $this->write( expI18nCoverageMap::PAGES['eng-us']['raster'], 'x' );
            $this->write( expI18nCoverageMap::PAGES['ger-de']['raster'], 'x' );
        }
        else
            $this->assertSame( array(), $result['errors'] );
        $this->assertSame( array(), $map->check( '2026-11-30' ), 'a later day keeps the date while nothing changed' );
        $this->assertSame( $page, file_get_contents( $this->dir . '/doc/eng-us/languages.md' ) );
        $this->assertSame( array( 'written' => array(), 'errors' => array() ), $map->write( '2026-11-30' ), 'nothing to write' );

        // the page is replaced in place, not added again
        $again = expI18nCoverageMap::replaceBlock( $page, $map->block( 'eng-us', '2026-10-26' ) );
        $this->assertSame( $page, $again );
        $this->assertSame( 2, substr_count( $again, '(start) -->' ), 'the summary and the languages section, each once' );
        $this->assertSame( $page, expI18nCoverageMap::replaceLanguagesBlock( $page, $map->languagesBlock( 'eng-us' ) ) );
        $this->assertSame( "---\nsummary: S\n---\n# T\n\nBLOCK\n\nIntro\n", expI18nCoverageMap::replaceBlock( "---\nsummary: S\n---\n# T\n\nIntro\n", 'BLOCK' ),
                           'below the heading, after the front matter' );

        // a new translation changes the figures and the date
        $this->ts( 'share/translations/swa-KE', array( 'A' => 'a', 'B' => 'b' ) );
        expUISwitchLanguageCatalogue::resetMemory();
        $map = new expI18nCoverageMap( $this->dir );
        $problems = $map->check( '2026-11-30' );
        $this->assertContains( 'doc/eng-us/languages.md is out of date: run ./console exp:i18n coverage', $problems );
        $this->assertContains( 'doc/eng-us/images/languages/coverage-map.svg is out of date: run ./console exp:i18n coverage', $problems );
        $map->write( '2026-11-30' );
        $this->assertStringContainsString( '<!-- generated: exp:i18n coverage 2026-11-30 (start) -->', file_get_contents( $this->dir . '/doc/eng-us/languages.md' ) );
        $this->assertStringContainsString( '| Kenya | KE | `swa-KE` |', file_get_contents( $this->dir . '/doc/eng-us/languages.md' ) );
        $this->assertStringContainsString( '**6**, 5 of them complete', file_get_contents( $this->dir . '/doc/eng-us/languages.md' ) );

        // a WebP copy that is gone is reported
        $this->remove( $this->dir . '/' . expI18nCoverageMap::PAGES['ger-de']['raster'] );
        $this->assertContains( 'doc/ger-de/bilder/sprachen/abdeckungskarte.webp is missing: run ./console exp:i18n coverage', $map->check( '2026-11-30' ) );
    }

    /** CM-06 */
    public function testTheCoverageColumnAndTheCompleteTranslationsOfAPageAreMadeFromTheFiles()
    {
        $map = new expI18nCoverageMap( $this->dir );
        $table = "| Code | English name | Source | Translation coverage |\n|---|---|---|---:|\n"
               . "| `ara-EG` | Arabic (Egypt) | CLDR 44 | 7% |\n| `bre-FR` | Breton | CLDR 44 | 100% |\n| `eng-CA` | English (Canada) | CLDR 44 | — |\n"
               . "| `fre-CA` | French (Canada) | CLDR 44 | — |\n| `ger-DE` | German | CLDR 44 | 99% |\n| `ger-DE@euro` | German (euro) | CLDR 44 | — |\n"
               . "| `xxx-XX` | No locale | CLDR 44 | 12% |\n\n| `ger-DE` | not in the locale table |\n";
        $english = "# T\n\n" . expI18nCoverageMap::TRANSLATIONS_START . "\nold text\n" . expI18nCoverageMap::TRANSLATIONS_END . "\n\n## 3. Every locale\n\n$table";
        $this->write( 'doc/eng-us/languages.md', $english );
        $this->write( 'doc/ger-de/sprachen.md', str_replace( array( '| Code | English name', 'Translation coverage' ), array( '| Code | Englischer Name', 'Übersetzungsabdeckung' ), $english ) );
        $files = $map->build( '2026-10-26' );
        $page = $files['doc/eng-us/languages.md'];
        $flat = str_replace( "\n", ' ', $page );
        // ara-SA is 1 of 2 messages finished: its own row and its fallbacks show 50%
        $this->assertStringContainsString( '| `ara-EG` | Arabic (Egypt) | CLDR 44 | 50% (falls back to `ara-SA`) |', $page );
        $this->assertStringContainsString( '| `bre-FR` | Breton | CLDR 44 | — |', $page, 'no translation' );
        $this->assertStringContainsString( '| `eng-CA` | English (Canada) | CLDR 44 | 100% |', $page, 'English is the source language' );
        $this->assertStringContainsString( '| `fre-CA` | French (Canada) | CLDR 44 | 100% (falls back to `fre-FR`) |', $page );
        $this->assertStringContainsString( '| `ger-DE` | German | CLDR 44 | 100% |', $page );
        $this->assertStringContainsString( '| `ger-DE@euro` | German (euro) | CLDR 44 | 100% (falls back to `ger-DE`) |', $page, 'a variant takes its locale' );
        $this->assertStringContainsString( '| `xxx-XX` | No locale | CLDR 44 | 12% |', $page, 'a code without a locale file is left alone' );
        $this->assertStringContainsString( "| `ger-DE` | not in the locale table |\n", $page, 'only the rows of the table' );
        $this->assertStringContainsString( 'In short: **15 locale files** (13 languages and countries, the test language Lorem Ipsum and 1 variation, 2 of them written right to left)', $flat );
        $this->assertStringContainsString( '**5 administration translations** exist, 4 of them complete: fre-FR (`fre-FR`), German', $flat );
        $this->assertStringContainsString( 'German (`ger-DE`), ser-SR (`ser-SR`), tlh-QO (`tlh-QO`). The names are', $flat );
        $this->assertStringNotContainsString( 'ara-SA (`ara-SA`)', $flat, 'a translation that is not complete is not listed' );
        $this->assertStringNotContainsString( 'old text', $page );
        $german = str_replace( "\n", ' ', $files['doc/ger-de/sprachen.md'] );
        $this->assertStringContainsString( '| `ara-EG` | Arabic (Egypt) | CLDR 44 | 50 % (fällt auf `ara-SA` zurück) |', $files['doc/ger-de/sprachen.md'] );
        $this->assertStringContainsString( 'es gibt **5 Übersetzungen der Administrationsoberfläche**, 4 davon vollständig: ', $german );

        // written, then current; a new translation changes the figures
        $map->write( '2026-10-26' );
        $this->assertSame( array(), array_values( array_filter( $map->check( '2026-10-26', false ), function ( $p ) { return strpos( $p, '.webp' ) === false; } ) ) );
        $this->ts( 'share/translations/swa-KE', array( 'A' => 'a', 'B' => 'b' ) );
        expUISwitchLanguageCatalogue::resetMemory();
        $page = ( new expI18nCoverageMap( $this->dir ) )->build( '2026-10-27' )['doc/eng-us/languages.md'];
        $this->assertStringContainsString( '**6 administration translations** exist, 5 of them complete: ', str_replace( "\n", ' ', $page ) );
        $this->assertStringContainsString( 'swa-KE (`swa-KE`)', str_replace( "\n", ' ', $page ) );
        $this->assertStringContainsString( '| `bre-FR` | Breton | CLDR 44 | — |', $page );

        // a page without the markers and the table is kept
        $this->assertSame( 'Text', expI18nCoverageMap::replaceTranslationsBlock( 'Text', 'BLOCK' ) );
        $this->assertSame( "| `ger-DE` | x |", $map->replaceLocaleCoverage( '| `ger-DE` | x |', false ) );
    }

    /** CM-05 */
    public function testTheGeneratedPartRendersInTheHelpViewer()
    {
        if ( !class_exists( 'expHelpMarkdownRenderer' ) )
            $this->markTestSkipped( 'expHelpMarkdownRenderer is not available' );
        $map = new expI18nCoverageMap( $this->dir );
        $page = $map->build( '2026-10-26' )['doc/eng-us/languages.md'];
        $renderer = new expHelpMarkdownRenderer( array(
            'image_resolver' => function ( $src ) { return 'https://example.invalid/exphelp/asset/' . $src; },
        ) );
        $html = $renderer->render( $page, 'languages.md' )['html'];
        $this->assertStringContainsString( '<img', $html );
        $this->assertStringContainsString( 'https://example.invalid/exphelp/asset/images/languages/coverage-map.webp', $html );
        $this->assertSame( 4, substr_count( $html, '<details class="a4-help-details"' ), 'countries in green, the others, the codes without a locale, the languages not translated' );
        $this->assertStringContainsString( 'id="the-languages-not-translated-yet"', $html );
        $this->assertStringContainsString( 'The 8 countries and territories in green', $html );
        $this->assertStringContainsString( '<table', $html );
        $this->assertStringNotContainsString( 'generated: exp:i18n', $html );
    }
}
