<?php
/**
 * expI18nFallbackReport (kernel/classes/i18n), what ./console exp:i18n and Setup > Interface languages show, against a
 * private installation under var/tmp (locale files, kernel translations, two extension translation directories), no
 * cache file:
 *   - rows: own translation, chain with the rule and whether each step is a translation, kernel coverage through
 *     the chain, the kind (own, fallback, untranslated), the counts, filtering by kind
 *   - extension coverage: the first file of the chain each extension has, against its untranslated catalogue or
 *     every message of its files; FallbackMode=merge counts the chain together; English is the source
 *   - problems of every locale, each once
 *   - exp:i18n's output: the list, one locale in detail, the problems with PASS or FAIL, the JSON, the exit codes
 *   - Setup > Interface languages' rows (Exponential\View\Kernel\Setup\Languages::shown()) and settings
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 * @group ezi18n
 */

use Exponential\Command\Kernel\I18n;
use Exponential\View\Kernel\Setup\Languages;

class expI18nFallbackReportTest extends PHPUnit\Framework\TestCase
{
    private $dir;

    protected function setUp(): void
    {
        expUISwitchLanguageCatalogue::resetMemory();
        $this->dir = dirname( __DIR__, 5 ) . '/var/tmp/phpunit-i18n-report-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        foreach ( array( 'esl-ES' => 'Español (España)|Spanish (Spain)|es-ES', 'esl-MX' => 'Español (México)|Spanish (Mexico)|es-MX',
                         'esl-AR' => 'Español (Argentina)|Spanish (Argentina)|es-AR', 'ger-DE' => 'Deutsch|German|de-DE',
                         'ger-AT' => 'Deutsch (Österreich)|German (Austria)|de-AT', 'eng-GB' => 'English|English (United Kingdom)|en-GB',
                         'swa-KE' => 'Kiswahili|Swahili|sw-KE' ) as $code => $names )
        {
            list( $native, $english, $http ) = explode( '|', $names );
            $this->write( "share/locale/$code.ini", "[RegionalSettings]\n[HTTP]\nContentLanguage=$http\n[LanguageSettings]\nLanguageName=$native\nInternationalLanguageName=$english\n" );
        }
        $this->ts( 'share/translations/untranslated', array( 'A' => '', 'B' => '', 'C' => '', 'D' => '' ) );
        $this->ts( 'share/translations/esl-ES', array( 'A' => 'a', 'B' => 'b', 'C' => 'c', 'D' => 'd' ) );
        $this->ts( 'share/translations/esl-MX', array( 'A' => 'a', 'B' => '' ) );
        $this->ts( 'share/translations/ger-DE', array( 'A' => 'a', 'B' => 'b' ) );
        $this->ts( 'ext/one/translations/untranslated', array( 'X' => '', 'Y' => '' ) );
        $this->ts( 'ext/one/translations/esl-ES', array( 'X' => 'x', 'Y' => 'y' ) );
        $this->ts( 'ext/one/translations/esl-MX', array( 'X' => 'x' ) );
        $this->ts( 'ext/two/translations/ger-DE', array( 'P' => 'p', 'Q' => '' ) );
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
        if ( is_dir( $path ) )
        {
            foreach ( scandir( $path ) as $name )
            {
                if ( $name !== '.' && $name !== '..' )
                    $this->remove( $path . '/' . $name );
            }
            rmdir( $path );
        }
        elseif ( is_file( $path ) )
            unlink( $path );
    }

    private function report( array $configured, array $options = array() )
    {
        $resolver = new expI18nFallbackResolver( $configured, array(), array_merge( array(
            'repository' => $this->dir . '/share/translations', 'locale_directory' => $this->dir . '/share/locale' ), $options ) );
        $catalogue = new expUISwitchLanguageCatalogue( $this->dir, '', null, $resolver );
        return new expI18nFallbackReport( $resolver, $catalogue,
                                          array( 'one' => $this->dir . '/ext/one/translations', 'two' => $this->dir . '/ext/two/translations' ), '' );
    }

    private function byLocale( array $rows )
    {
        $map = array();
        foreach ( $rows as $row )
            $map[$row['locale']] = $row;
        return $map;
    }

    public function testRowsShowTheChainAndTheCoverageThroughIt()
    {
        $report = $this->report( array( 'esl-AR' => 'esl-MX', 'esl-MX' => 'esl-ES', 'ger-AT' => 'ger-DE', 'swa-KE' => 'untranslated' ) );
        $rows = $this->byLocale( $report->rows( null, true ) );
        $this->assertSame( array( 'eng-GB', 'esl-AR', 'esl-ES', 'esl-MX', 'ger-AT', 'ger-DE', 'swa-KE' ), array_keys( $rows ) );

        $ar = $rows['esl-AR'];
        $this->assertFalse( $ar['own'] );
        $this->assertSame( 'Spanish (Argentina)', $ar['english'] );
        $this->assertSame( 'es-AR', $ar['http'] );
        $this->assertSame( array( array( 'target' => 'esl-MX', 'rule' => 'configured', 'translated' => true ),
                                  array( 'target' => 'esl-ES', 'rule' => 'configured', 'translated' => true ) ), $ar['chain'] );
        $this->assertSame( 'esl-MX', $ar['from'], 'first: the first translation of the chain' );
        $this->assertSame( 25, $ar['coverage'], 'esl-MX translates A of A to D' );
        $this->assertSame( array( 'one' => 50, 'two' => null ), $ar['extensions'], 'one has esl-MX (X of X, Y); two has no Spanish' );
        $this->assertSame( array( 'total' => 2, 'translated' => 1, 'complete' => 0, 'average' => 50 ), $ar['extension_summary'] );
        $this->assertSame( 'fallback', expI18nFallbackReport::kind( $ar ) );

        $this->assertSame( array( 'one' => null, 'two' => 50 ), $rows['ger-AT']['extensions'] );
        $this->assertSame( 50, $rows['ger-AT']['coverage'] );
        $this->assertSame( 'own', expI18nFallbackReport::kind( $rows['esl-ES'] ) );
        $this->assertSame( 'own', expI18nFallbackReport::kind( $rows['eng-GB'] ), 'English is the source' );
        $this->assertSame( array( 'one' => 100, 'two' => 100 ), $rows['eng-GB']['extensions'] );
        $this->assertSame( 'untranslated', expI18nFallbackReport::kind( $rows['swa-KE'] ) );
        $this->assertSame( array( array( 'target' => 'untranslated', 'rule' => 'configured', 'translated' => false ) ), $rows['swa-KE']['chain'] );

        $this->assertSame( array( 'own' => 4, 'fallback' => 2, 'untranslated' => 1 ), expI18nFallbackReport::counts( $report->rows() ) );
        $this->assertSame( array( 'esl-AR', 'ger-AT' ), array_column( expI18nFallbackReport::filterRows( $report->rows(), 'fallback' ), 'locale' ) );
        $this->assertCount( 7, expI18nFallbackReport::filterRows( $report->rows(), '' ) );
        $this->assertArrayNotHasKey( 'extensions', $report->rows( array( 'esl-AR' ) )[0], 'only when asked' );
        $this->assertSame( array( 'one', 'two' ), array_keys( $report->extensionRoots() ) );
    }

    public function testMergeCountsTheWholeChain()
    {
        $report = $this->report( array( 'esl-AR' => 'esl-MX', 'esl-MX' => 'esl-ES' ), array( 'mode' => 'merge' ) );
        $rows = $this->byLocale( $report->rows( array( 'esl-AR', 'esl-MX' ), true ) );
        $this->assertSame( 100, $rows['esl-AR']['coverage'], 'esl-MX and esl-ES together' );
        $this->assertSame( 100, $rows['esl-MX']['coverage'], 'its own file and esl-ES' );
        $this->assertSame( array( 'one' => 100, 'two' => null ), $rows['esl-AR']['extensions'] );
    }

    public function testTheAutomaticFallbackShowsAsSuch()
    {
        $rows = $this->byLocale( $this->report( array() )->rows( array( 'esl-AR', 'ger-AT' ) ) );
        $this->assertSame( array( array( 'target' => 'esl-ES', 'rule' => 'automatic', 'translated' => true ) ), $rows['esl-AR']['chain'],
                           'esl-ES translates more than esl-MX' );
        $this->assertSame( 100, $rows['esl-AR']['coverage'] );
        $this->assertSame( 'ger-DE', $rows['ger-AT']['from'] );
    }

    public function testProblemsAreListedOnce()
    {
        $problems = $this->report( array( 'esl-AR' => 'esl-MX', 'esl-MX' => 'esl-AR', 'ger-AT' => 'ger-XX' ), array( 'automatic' => false ) )->problems();
        $lines = array();
        foreach ( $problems as $problem )
            $lines[] = $problem['locale'] . ' ' . $problem['code'] . ' ' . $problem['detail'];
        sort( $lines );
        $this->assertSame( array( 'esl-AR cycle esl-AR', 'esl-MX cycle esl-MX', 'ger-AT missing-target ger-XX' ), $lines );
    }

    public function testTheCommandPrintsTheListTheDetailsAndTheProblems()
    {
        $report = $this->report( array( 'esl-AR' => 'esl-MX', 'esl-MX' => 'esl-ES', 'ger-AT' => 'ger-DE' ) );
        $table = I18n::renderTable( $report->resolver(), $report->rows( null, true ) );
        $this->assertStringContainsString( 'Mode first, rules FallbackLanguages[], automatic, max depth 4', $table );
        $this->assertMatchesRegularExpression( '/^esl-AR\s+Spanish \(Argentina\)\s+25%\s+esl-MX\s+1\/2 50%\s+esl-MX > esl-ES$/m', $table );
        $this->assertMatchesRegularExpression( '/^ger-DE\s+German\s+50%\s+own\s+1\/2 50%\s+-$/m', $table );
        $this->assertStringContainsString( '7 locales: 4 with a translation of their own, 2 through a fallback, 1 in the source texts (English)', $table );

        $detail = I18n::renderLocales( $report->rows( array( 'esl-AR' ), true ) );
        $this->assertStringContainsString( "esl-AR Spanish (Argentina)\n  own translation: no\n  chain: esl-MX (FallbackLanguages[]) > esl-ES (FallbackLanguages[])\n  kernel: 25% through esl-MX", $detail );
        $this->assertMatchesRegularExpression( '/^    one\s+50%$/m', $detail );
        $this->assertMatchesRegularExpression( '/^    two\s+-$/m', $detail );

        $this->assertSame( "PASS: 0 errors, 0 warnings\n", I18n::renderProblems( array(), false ) );
        $problems = $this->report( array( 'esl-AR' => 'esl-MX', 'esl-MX' => 'esl-AR' ), array( 'automatic' => false ) )->problems();
        $text = I18n::renderProblems( $problems, false );
        $this->assertStringContainsString( 'ERROR: esl-AR its chain comes back to esl-AR', $text );
        $this->assertStringContainsString( 'FAIL: 2 errors, 0 warnings', $text );
        $this->assertSame( 1, I18n::exitCode( $problems, false ) );
        $this->assertSame( 0, I18n::exitCode( array(), true ) );
        $this->assertSame( array( 'esl-AR', 'ger-AT' ), array_column( I18n::filter( $report->rows(), 'fallback' ), 'locale' ) );

        $json = I18n::toArray( $report->resolver(), $report->rows( array( 'ger-AT' ) ), $problems, true );
        $this->assertSame( array( 'mode', 'rules', 'max_depth', 'automatic', 'locales', 'problems' ), array_keys( $json ) );
        $this->assertSame( 'ger-DE', $json['locales'][0]['chain'][0]['target'] );
        $this->assertNotFalse( json_encode( $json ) );
        $this->assertArrayNotHasKey( 'problems', I18n::toArray( $report->resolver(), array(), array(), false ) );
        $this->assertSame( 'FallbackLanguages[]', I18n::ruleLabel( 'configured' ) );
        $this->assertSame( 'myRule', I18n::ruleLabel( 'myRule' ) );
    }

    public function testThePageRowsAndSettings()
    {
        $report = $this->report( array( 'esl-AR' => 'esl-MX', 'esl-MX' => 'esl-ES' ) );
        $rows = $this->byLocale( $report->rows( null, true ) );
        $shown = Languages::shown( $rows['esl-AR'] );
        $this->assertSame( 'fallback', $shown['kind'] );
        $this->assertTrue( $shown['has_coverage'] );
        $this->assertSame( 25, $shown['pct'] );
        $this->assertSame( array( 'configured', 'configured' ), array_column( $shown['chain'], 'rule_kind' ) );
        $this->assertSame( 2, $shown['extensions']['total'] );
        $automatic = Languages::shown( $rows['ger-AT'] );
        $this->assertSame( 'automatic', $automatic['chain'][0]['rule_kind'] );
        $untranslated = Languages::shown( $rows['swa-KE'] );
        $this->assertFalse( $untranslated['has_coverage'] );
        $this->assertSame( 0, $untranslated['pct'] );
        $settings = Languages::fallbackSettings( $report->resolver() );
        $this->assertSame( array( 'mode' => 'first', 'automatic' => true, 'exclude' => array( 'eng' ), 'max_depth' => 4, 'rules' => array(), 'configured' => 2 ), $settings );
    }
}
