<?php
/**
 * expI18nTranslationWaves (kernel/classes/i18n, ./console exp:i18n waves): the measured plan of the translation waves
 * at the top of doc/eng-us/translation-waves.md and doc/ger-de/uebersetzungswellen.md, without a database.
 *
 *  TW-01  the committed pages are current (fails when a percentage changed or a size is off: run
 *         ./console exp:i18n waves), and both link to each other through the path map
 *  TW-02  on a private installation: the administration catalogue in parts, each translation's share of it counted as
 *         the translator counts (a source with a Qt byte element included), the shipped extensions from the manifest
 *         only, the help by section and tier, the pages a documentation language has (its path map or the same path),
 *         the generated example pages counted as their tables, words without front matter and code
 *  TW-03  the languages: difficulty and score, the languages without a translation (in progress, new locales,
 *         derived catalogues), and the queue: the priority group's catalogues first, German's help, the group's help
 *         part by part one language after the other, then the new translations; the ones in progress wait
 *  TW-04  the pages: the generated part replaces its markers, keeps its date while nothing changes, takes the new date
 *         when something does; check() lets a small size change pass, fails on a percentage, a new language or a
 *         size beyond the tolerance; a page without the plan is reported
 *  TW-05  the generated part renders in the help viewer's renderer: tables, the collapsible lists, no marker or data
 *         comment shown
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 * @group ezi18n
 */

// a checkout whose class map was built before the class existed
if ( !class_exists( 'expI18nTranslationWaves' ) )
    require_once __DIR__ . '/../../../../../kernel/classes/i18n/expi18ntranslationwaves.php';

class expI18nTranslationWavesTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $dir;

    protected function setUp(): void
    {
        expUISwitchLanguageCatalogue::resetMemory();
        $this->dir = dirname( __DIR__, 5 ) . '/var/tmp/phpunit-i18n-waves-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        foreach ( array( 'ger-DE' => 'de-DE', 'ara-SA' => 'ar-SA', 'tur-TR' => 'tr-TR', 'fre-FR' => 'fr-FR', 'eng-US' => 'en-US',
                         'kor-KR' => 'ko-KR', 'swa-KE' => 'sw-KE', 'ind-ID' => 'id-ID', 'fas-AF' => 'fa-AF', 'fas-IR' => 'fa-IR' ) as $code => $http )
            $this->write( "share/locale/$code.ini", "[RegionalSettings]\n[LanguageSettings]\nLanguageName=$code\nInternationalLanguageName=$code\n[HTTP]\nContentLanguage=$http\n" );
        // four messages; the last source holds a Qt byte element, as one source of the real catalogue does
        $this->ts( 'share/translations/untranslated', array( 'a' => array( 'One' => '', 'Two' => '' ), 'b' => array( 'Three' => '', 'We<byte value="x19"/>re' => '' ) ) );
        $this->ts( 'share/translations/ger-DE', array( 'a' => array( 'One' => 'Eins', 'Two' => 'Zwei' ), 'b' => array( 'Three' => 'Drei', 'We<byte value="x19"/>re' => 'Wir' ) ) );
        $this->ts( 'share/translations/ara-SA', array( 'a' => array( 'One' => 'x', 'Two' => 'x' ), 'b' => array( 'Three' => 'x', 'We<byte value="x19"/>re' => '' ) ) );
        $this->ts( 'share/translations/tur-TR', array( 'a' => array( 'One' => 'x', 'Two' => 'x' ), 'b' => array( 'Three' => 'x', 'We<byte value="x19"/>re' => 'x' ) ) );
        $this->ts( 'share/translations/fre-FR', array( 'a' => array( 'One' => 'x', 'Two' => 'x' ), 'b' => array( 'Three' => 'x', 'We<byte value="x19"/>re' => 'x' ) ) );
        $this->ts( 'share/translations/fas-IR', array( 'a' => array( 'One' => 'x', 'Two' => 'x' ), 'b' => array( 'Three' => 'x', 'We<byte value="x19"/>re' => 'x' ) ) );
        $this->ts( 'share/translations/kor-KR', array( 'a' => array( 'One' => 'x', 'Two' => 'x' ), 'b' => array( 'Three' => 'x', 'We<byte value="x19"/>re' => 'x' ) ) );
        $this->ts( 'share/translations/eng-US', array( 'a' => array( 'One' => 'One' ) ) );
        // a shipped extension and one the manifest does not list
        $this->ts( 'extension/shipped/translations/untranslated', array( 'e' => array( 'Save' => '', 'Load' => '' ) ) );
        $this->ts( 'extension/shipped/translations/ger-DE', array( 'e' => array( 'Save' => 'Speichern', 'Load' => '' ) ) );
        $this->ts( 'extension/shipped/translations/tur-TR', array( 'e' => array( 'Save' => 'x', 'Load' => 'x' ) ) );
        $this->ts( 'extension/shipped/translations/fre-FR', array( 'e' => array( 'Save' => 'x', 'Load' => 'x' ) ) );
        $this->ts( 'extension/shipped/translations/fas-IR', array( 'e' => array( 'Save' => 'x', 'Load' => 'x' ) ) );
        $this->ts( 'extension/shipped/translations/kor-KR', array( 'e' => array( 'Save' => 'x', 'Load' => 'x' ) ) );
        $this->ts( 'extension/other/translations/untranslated', array( 'o' => array( 'Many' => '', 'More' => '' ) ) );
        $this->write( 'share/filelist.md5', str_repeat( '0', 32 ) . "  extension/shipped/translations/untranslated/translation.ts\n" );
        $this->write( 'settings/i18n.ini', "[TranslationSettings]\nFallbackRules[]\nAutomaticLanguageFallback=enabled\nAutomaticFallbackExclude[]\nAutomaticFallbackExclude[]=eng\nFallbackMaxDepth=4\nFallbackMode=first\nFallbackLanguages[fas-AF]=fas-IR\n" );
        $this->write( 'settings/country.ini', "[DE]\nName=Germany\n" );
        // the help: daily use, a guide, the reference, a generated example page
        $this->write( 'doc/eng-us/help/getting-started.md', "---\nsummary: How to start.\n---\n# Getting started\n\nOne two three four.\n\n```bash\nnot counted here\n```\n" );
        $this->write( 'doc/eng-us/help/manual/editing.md', "# Editing\n\nFive six seven eight nine ten.\n" );
        $this->write( 'doc/eng-us/help/government/a.md', "# Government\n\n" . str_repeat( 'word ', 999 ) . "\n" );
        $this->write( 'doc/eng-us/bc/x.md', "# BC\n\nThirteen fourteen fifteen.\n" );
        $this->write( 'doc/eng-us/examples/commands/foo.md', "---\ngenerated: exp:help:examples\n---\n# foo\n\nMany many words that are not counted as a page.\n" );
        $this->write( 'doc/ger-de/.paths.json', "{\n    \"hilfe/erste-schritte.md\": \"help/getting-started.md\",\n    \"hilfe/fehlt.md\": \"help/manual/editing.md\"\n}\n" );
        $this->write( 'doc/ger-de/hilfe/erste-schritte.md', "# Erste Schritte\n\nEins.\n" );
        $this->write( 'doc/ger-de/bc/x.md', "# BC\n\nDrei.\n" );
        $this->write( 'doc/ger-de/.examples/strings.json', "{\"keywords\": {\"command\": \"Befehl\", \"event\": \"\"}}\n" );
        $this->write( 'doc/ger-de/.examples/descriptions/a.json', "{\"command:foo\": {\"en\": \"Shows the foo\", \"de\": \"Zeigt das Foo\"}, \"command:bar\": {\"en\": \"Old text\", \"de\": \"Alt\"}}\n" );
        $this->write( 'doc/eng-us/translation-waves.md', "---\nsummary: S.\n---\n# Translation waves\n\nThe intro.\n\n## What the waves are\n\nText.\n" );
        $this->write( 'doc/ger-de/uebersetzungswellen.md', "# Übersetzungswellen\n\nDie Einleitung.\n" );
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

    private function ts( $dir, array $contexts )
    {
        $xml = '';
        foreach ( $contexts as $name => $messages )
        {
            $xml .= "<context><name>$name</name>\n";
            foreach ( $messages as $source => $translation )
                $xml .= '<message><source>' . $source . '</source><translation' . ( $translation === '' ? ' type="unfinished"' : '' ) . '>' . $translation . "</translation></message>\n";
            $xml .= "</context>\n";
        }
        $this->write( $dir . '/translation.ts', "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<!DOCTYPE TS>\n<TS version=\"2.0\">\n$xml</TS>\n" );
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

    /** TW-01 */
    public function testTheCommittedPagesAreCurrent()
    {
        $root = dirname( __DIR__, 5 );
        $waves = new expI18nTranslationWaves( $root );
        $problems = $waves->check( date( 'Y-m-d' ) );
        $this->assertSame( array(), $problems, "The translation waves are out of date: run ./console exp:i18n waves\n" . implode( "\n", $problems ) );
        $map = json_decode( (string)file_get_contents( $root . '/doc/ger-de/.paths.json' ), true );
        $this->assertSame( 'translation-waves.md', $map['uebersetzungswellen.md'], 'the German page translates the English one' );
        $this->assertStringContainsString( '](translation-waves.md)', (string)file_get_contents( $root . '/doc/eng-us/languages.md' ) );
        $this->assertStringContainsString( '](uebersetzungswellen.md)', (string)file_get_contents( $root . '/doc/ger-de/sprachen.md' ) );
        $data = $waves->data();
        $this->assertSame( count( glob( $root . '/share/translations/*/translation.ts' ) ) - 1, count( $data['status'] ), 'every translation but the source texts' );
        $this->assertArrayHasKey( 'kor-KP', $data['untranslated'], 'North Korean is planned' );
    }

    /** TW-02 */
    public function testTheMeasurement()
    {
        $waves = new expI18nTranslationWaves( $this->dir );
        $data = $waves->data();
        $this->assertSame( 4, $data['admin']['messages'] );
        $this->assertCount( 1, $data['admin']['parts'] );
        $this->assertSame( array( 'a', 'b' ), $data['admin']['parts'][0]['contexts'] );
        $this->assertSame( array( 'shipped' => 2 ), $data['extensions'], 'only what the manifest lists' );
        $status = $data['status'];
        $this->assertSame( array( 'ara-SA', 'eng-US', 'fas-IR', 'fre-FR', 'ger-DE', 'kor-KR', 'tur-TR' ), array_keys( $status ) );
        $this->assertSame( 100, $status['ger-DE']['admin'], 'the source with the byte element is matched' );
        $this->assertSame( 75, $status['ara-SA']['admin'] );
        $this->assertSame( 50, $status['ger-DE']['extensions'] );
        $this->assertSame( 0, $status['ara-SA']['extensions'], 'no catalogue' );
        $this->assertSame( 100, $status['eng-US']['admin'], 'English is the source' );

        $pages = $waves->pages();
        $this->assertSame( array( 'bc/x.md', 'help/getting-started.md', 'help/government/a.md', 'help/manual/editing.md' ), array_keys( $pages ), 'the generated page is a table, this plan is not measured' );
        $this->assertSame( array( 'section' => 'help', 'tier' => 'daily', 'words' => 6 ), $pages['help/getting-started.md'], 'no front matter, no code' );
        $this->assertSame( 'guides', $pages['help/government/a.md']['tier'] );
        $this->assertSame( 'reference', $pages['bc/x.md']['tier'] );
        $translated = $waves->translatedPages( 'ger-de', $pages );
        ksort( $translated );
        $this->assertSame( array( 'bc/x.md' => true, 'help/getting-started.md' => true ), $translated,
                           'a path map entry without its file does not count; the same path does' );
        $tiers = $data['help']['tiers'];
        $this->assertSame( 2, $tiers['daily']['pages'] );
        $this->assertSame( 4, $tiers['tables']['entries'], 'two keywords, two descriptions' );
        // German: getting-started (6 of the 13 daily words) translated, the manual page not
        $this->assertSame( 46, $status['ger-DE']['daily'] );
        $this->assertSame( 0, $status['ara-SA']['daily'] );
        $this->assertSame( '', $status['ara-SA']['directory'] );
        $this->assertSame( 'ger-de', $status['ger-DE']['directory'] );
        $ids = array_column( $data['help']['parts'], 'id' );
        $this->assertSame( array( 'H-help', 'H-help-manual', 'H-help-government', 'H-bc', 'X01' ), $ids, 'daily, guides, reference, tables' );
        $this->assertSame( 2, expI18nTranslationWaves::countWords( "don't stop" ) + 0 * expI18nTranslationWaves::countWords( '' ) );
        $this->assertSame( 3, expI18nTranslationWaves::pageWords( "---\na: b c d\n---\nOne two three\n~~~\nx y\n~~~\n" ) );
    }

    /** TW-03 */
    public function testLanguagesAndTheQueue()
    {
        $ara = expI18nTranslationWaves::language( 'ara-SA' );
        $this->assertSame( array( 'Arabic', 'Arabisch', 400, 'Arab', true, 4 ), array( $ara['en'], $ara['de'], $ara['speakers'], $ara['script'], $ara['rtl'], $ara['difficulty'] ) );
        $this->assertSame( 1, expI18nTranslationWaves::language( 'esl-ES' )['difficulty'] );
        $this->assertSame( 5, expI18nTranslationWaves::language( 'mal-IN' )['difficulty'], 'capped at 5' );
        $this->assertSame( 'Chinese (Traditional)', expI18nTranslationWaves::language( 'chi-TW' )['en'] );
        $this->assertSame( 'Hant', expI18nTranslationWaves::language( 'chi-TW' )['script'] );

        $data = ( new expI18nTranslationWaves( $this->dir ) )->data();
        $untranslated = $data['untranslated'];
        $this->assertTrue( $untranslated['ind-ID']['in_progress'], 'a locale being translated on a branch' );
        $this->assertFalse( $untranslated['swa-KE']['in_progress'] );
        $this->assertTrue( $untranslated['tsn-ZA']['new'], 'no locale yet' );
        $this->assertSame( 'kor-KR', $untranslated['kor-KP']['from'] );
        $this->assertSame( 'fas-IR', $untranslated['fas-AF']['from'] );
        $this->assertFalse( $untranslated['fas-AF']['new'], 'the Dari locale exists' );

        $ids = array_column( $data['queue'], 'id' );
        $phase = array_column( $data['queue'], 'phase', 'id' );
        $this->assertSame( array( 'ger-DE/E', 'ara-SA/A01', 'ara-SA/E', 'fas-AF/L' ), array_slice( $ids, 0, 4 ), 'the group\'s catalogues, then Dari' );
        $this->assertSame( 2, $phase['ger-DE/H-help-manual'], 'German finishes its daily-use help' );
        $this->assertSame( array( 'ara-SA/S', 'tur-TR/S', 'fas-IR/S', 'ara-SA/H-help', 'tur-TR/H-help', 'fas-IR/H-help', 'ara-SA/H-help-manual' ),
                           array_slice( array_values( array_filter( $ids, function ( $id ) use ( $phase ) { return $phase[$id] === 3; } ) ), 0, 7 ),
                           'part by part, one language after the other, in the order of the group' );
        $this->assertSame( 4, $phase['fas-AF/A01'], 'the review of Dari waits for phase 4' );
        $this->assertSame( 4, $phase['kor-KP/L'] );
        $this->assertSame( 'review', $data['queue'][array_search( 'kor-KP/A01', $ids, true )]['kind'] );
        $this->assertSame( 4, $phase['swa-KE/A01'] );
        $this->assertSame( 4, $phase['tsn-ZA/L'] );
        $this->assertNotContains( 'ind-ID/A01', $ids, 'in progress: it waits' );
        $this->assertNotContains( 'tur-TR/E', $ids, 'complete' );
        $this->assertNotContains( 'eng-US/H-help', $ids, 'English is the source' );
        $this->assertSame( 6, $phase['fre-FR/H-help'] );
        $this->assertSame( 7, $phase['ger-DE/H-help-government'] );
        $this->assertSame( 8, $phase['fre-FR/X01'] );
        $this->assertLessThan( array_search( 'kor-KR/H-help', $ids, true ), array_search( 'fre-FR/H-help', $ids, true ),
                               'French (310 million, difficulty 1) before Korean (82 million, difficulty 3)' );
        $this->assertSame( range( 1, count( $data['queue'] ) ), array_column( $data['queue'], 'number' ) );
    }

    /** TW-04 */
    public function testThePagesAreWrittenAndChecked()
    {
        $waves = new expI18nTranslationWaves( $this->dir );
        $files = $waves->build( '2026-10-26' );
        $page = $files['doc/eng-us/translation-waves.md'];
        $this->assertStringStartsWith( "---\nsummary: S.\n---\n# Translation waves\n\n<!-- generated: exp:i18n waves 2026-10-26 (start) -->\n**Measured on 26 October 2026**", $page );
        $this->assertStringContainsString( expI18nTranslationWaves::END . "\n\nThe intro.", $page );
        $this->assertStringContainsString( '| Administration: strings of `share/translations/untranslated/translation.ts`, in 1 parts | **4**', $page );
        $this->assertStringContainsString( '| Arabic | `ara-SA` | 400 | 4 | **75** | **0** | **0** | **0** |', $page );
        $this->assertStringContainsString( '| Korean (North Korea, Munhwaŏ) | `kor-KP` |', $page );
        $this->assertStringContainsString( '| 1 | `ger-DE/E` | German | Shipped extensions | 1 of 2 strings | 2 | `GUIDE.md` first |', $page );
        $german = $files['doc/ger-de/uebersetzungswellen.md'];
        $this->assertStringContainsString( '**Gemessen am 26. Oktober 2026**', $german );
        $this->assertStringContainsString( '| Arabisch | `ara-SA` | 400 | 4 | **75** |', $german );
        $this->assertStringContainsString( '### Sprachen ohne Übersetzung', $german );

        $this->assertSame( array( 'doc/eng-us/translation-waves.md', 'doc/ger-de/uebersetzungswellen.md' ), $waves->write( '2026-10-26' ) );
        $this->assertSame( array(), $waves->check( '2026-11-30' ), 'a later day keeps the date while nothing changed' );
        $this->assertSame( array(), $waves->write( '2026-11-30' ) );
        $this->assertSame( $page, expI18nTranslationWaves::replaceBlock( $page, $waves->block( 'eng-us', '2026-10-26' ) ), 'replaced in place' );
        $this->assertSame( 1, substr_count( $page, '(start) -->' ) );

        // a few more words in a page: the sizes stay within the tolerance, the check passes without a rewrite
        $this->write( 'doc/eng-us/help/government/a.md', "# Government\n\n" . str_repeat( 'word ', 999 ) . "and three more\n" );
        expUISwitchLanguageCatalogue::resetMemory();
        $this->assertSame( array(), ( new expI18nTranslationWaves( $this->dir ) )->check( '2026-11-30' ) );

        // a percentage that changes is reported
        $this->ts( 'share/translations/ara-SA', array( 'a' => array( 'One' => 'x', 'Two' => 'x' ), 'b' => array( 'Three' => 'x', 'We<byte value="x19"/>re' => 'x' ) ) );
        expUISwitchLanguageCatalogue::resetMemory();
        $changed = new expI18nTranslationWaves( $this->dir );
        $problems = $changed->check( '2026-11-30' );
        $this->assertContains( 'doc/eng-us/translation-waves.md is out of date (ara-SA: 75/0/0/0 is now 100/0/0/0): run ./console exp:i18n waves', $problems );
        $changed->write( '2026-11-30' );
        $this->assertStringContainsString( '<!-- generated: exp:i18n waves 2026-11-30 (start) -->', (string)file_get_contents( $this->dir . '/doc/eng-us/translation-waves.md' ) );
        $this->assertSame( array(), $changed->check( '2026-12-01' ) );

        // sizes beyond the tolerance, a new language, a page without its plan
        $figures = $changed->figures();
        $moved = $figures;
        $moved['sizes']['daily_words'] = (int)( $figures['sizes']['daily_words'] * 2 ) + 10;
        $this->assertNotSame( array(), expI18nTranslationWaves::compareFigures( $moved, $figures ) );
        $moved = $figures;
        $moved['sizes']['daily_words'] += 1;
        $this->assertSame( array(), expI18nTranslationWaves::compareFigures( $moved, $figures ), 'within max( 2, 3 % )' );
        $fewer = $figures;
        unset( $fewer['status']['tur-TR'] );
        $this->assertContains( 'tur-TR is new', expI18nTranslationWaves::compareFigures( $fewer, $figures ) );
        $this->write( 'doc/ger-de/uebersetzungswellen.md', "# Übersetzungswellen\n" );
        $this->assertContains( 'doc/ger-de/uebersetzungswellen.md has no generated plan: run ./console exp:i18n waves', $changed->check( '2026-12-01' ) );

        // the command's view of one wave
        $wave = $changed->wave( 'ger-DE/H-help-manual' );
        $this->assertSame( array( array( 'item' => 'help/manual/editing.md', 'done' => false ) ), $wave['items'] );
        $this->assertSame( array( 'a', 'b' ), array_column( $changed->wave( 'A01' )['items'], 'item' ) );
        $this->assertNull( $changed->wave( 'H-nothing' ) );
    }

    /** TW-05 */
    public function testTheGeneratedPartRendersInTheHelpViewer()
    {
        if ( !class_exists( 'expHelpMarkdownRenderer' ) )
            $this->markTestSkipped( 'expHelpMarkdownRenderer is not available' );
        $page = ( new expI18nTranslationWaves( $this->dir ) )->build( '2026-10-26' )['doc/eng-us/translation-waves.md'];
        $renderer = new expHelpMarkdownRenderer();
        $html = $renderer->render( $page, 'translation-waves.md' )['html'];
        $this->assertGreaterThanOrEqual( 5, substr_count( $html, '<table' ), 'summary, languages, tiers, parts, untranslated, phases, queue' );
        $this->assertSame( 2, substr_count( $html, '<details class="a4-help-details"' ), 'the parts of the help and of the administration' );
        $this->assertStringContainsString( 'id="where-each-language-stands"', $html );
        $this->assertStringContainsString( 'id="languages-without-a-translation"', $html );
        $this->assertStringContainsString( '<code>ara-SA/H-help</code>', $html );
        $this->assertStringNotContainsString( 'generated: exp:i18n', $html );
        $this->assertStringNotContainsString( 'waves data', $html );
    }
}
