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
 *  CM-03  the map: covered countries and their small-country circles in the green group, the others grey, title,
 *         description and legend in the page's language, no script; the outline is required
 *  CM-04  the page: the generated part goes below the first heading, is replaced in place, keeps its date while
 *         nothing else changes and takes the new date when something does; write() then check() is clean, a
 *         changed file is reported
 *  CM-05  the generated part renders in the help viewer's renderer: the summary table, the image and the two
 *         collapsible lists, nothing of the markers shown
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
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
                                              . "[EG]\nName=Egypt\n[GB]\nName=United Kingdom\n[KE]\nName=Kenya\n[RS]\nName=Serbia\n" );
        $this->write( 'share/locale/ger-DE.ini', "[RegionalSettings]\n[LanguageSettings]\nLanguageName=Deutsch\nInternationalLanguageName=German\n[HTTP]\nContentLanguage=de-DE\n"
                                                . "[CountryNames]\nCountries[DE]=Deutschland\nCountries[AT]=Österreich\nCountries[KE]=Kenia\n" );
        $paths = '';
        foreach ( array( 'AT', 'CA', 'DE', 'EG', 'FR', 'GB', 'KE', 'RS', 'SA', 'US', '' ) as $i => $iso )
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
        $this->assertSame( array( 'path:AT', 'path:CA', 'path:DE', 'path:EG', 'path:FR', 'path:GB', 'path:RS', 'path:SA', 'circle:SA' ), $isos( 'covered' ) );
        $this->assertSame( array( 'path:KE', 'path:US', 'path:', 'circle:MC' ), $isos( 'other' ) );
        $this->assertSame( expI18nCoverageMap::COVERED, $xpath->query( "//s:g[@id='covered']" )->item( 0 )->getAttribute( 'fill' ) );
        $this->assertSame( expI18nCoverageMap::OTHER, $xpath->query( "//s:g[@id='other']" )->item( 0 )->getAttribute( 'fill' ) );
        $this->assertStringContainsString( '<title id="map-title">Exponential: the countries', $svg );
        $this->assertStringContainsString( 'Green: 8 countries and territories', $svg );
        $this->assertStringContainsString( '>Administration in the language of a shipped locale (8)</text>', $svg );
        $this->assertStringContainsString( 'Natural Earth', $svg );
        $this->assertDoesNotMatchRegularExpression( '/<script|\son[a-z]+=|javascript:|<foreignObject|xlink:href|href=/i', $svg );
        $german = $map->svg( 'ger-de' );
        $this->assertStringContainsString( '>Administration in der Sprache einer mitgelieferten Locale (8)</text>', $german );
        $this->assertStringContainsString( 'Grün: 8 Länder und Gebiete', $german );

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
        $this->assertSame( 3, substr_count( $html, '<details class="a4-help-details"' ), 'countries in green, the others, the languages not translated' );
        $this->assertStringContainsString( 'id="the-languages-not-translated-yet"', $html );
        $this->assertStringContainsString( 'The 8 countries and territories in green', $html );
        $this->assertStringContainsString( '<table', $html );
        $this->assertStringNotContainsString( 'generated: exp:i18n', $html );
    }
}
