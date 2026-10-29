<?php
/**
 * expI18nFallbackResolver (lib/ezi18n), the translation fallbacks, against a private repository of translations and
 * locale files under var/tmp:
 *   - chains through FallbackLanguages[] (esl-AR -> esl-MX -> esl-ES), eng-GB and untranslated as the source texts
 *     that end a chain, an empty value saying nothing
 *   - a cycle and a chain longer than max_depth: cut, and reported by explain() and problems()
 *   - the automatic same-language fallback: the most complete translation of the language, never for a locale with
 *     a translation of its own, a variant or an excluded language (eng), and never over a configured fallback
 *   - rules of FallbackRules[]: asked after the configured ones and before the automatic one, a name that is no rule
 *     reported, a rule that throws skipped
 *   - problems(): missing and invalid targets, a configured locale without a locale file, a chain to a locale
 *     without a translation
 *   - the signature changes with the settings
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group lib
 * @group ezi18n
 */

class expI18nFallbackResolverTest extends PHPUnit\Framework\TestCase
{
    private $dir;

    protected function setUp(): void
    {
        $this->dir = dirname( __DIR__, 4 ) . '/var/tmp/phpunit-i18n-fallback-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( $this->dir . '/translations', 0777, true );
        mkdir( $this->dir . '/locale', 0777, true );
        foreach ( array( 'esl-ES', 'esl-MX', 'esl-AR', 'esl-CL', 'ger-DE', 'ger-CH', 'ger-AT', 'ger-IT', 'eng-GB', 'eng-IE',
                         'eng-US', 'fre-FR', 'fre-NC', 'ara-SA', 'ara-AE', 'ita-IT' ) as $code )
            file_put_contents( $this->dir . '/locale/' . $code . '.ini', "[RegionalSettings]\n" );
        $this->translation( 'esl-ES', 10 );
        $this->translation( 'esl-MX', 4 );
        $this->translation( 'ger-DE', 10 );
        $this->translation( 'ger-CH', 3 );
        $this->translation( 'eng-US', 2 );
        $this->translation( 'fre-FR', 5 );
    }

    protected function tearDown(): void
    {
        $this->remove( $this->dir );
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

    /** A translation with $translated translated messages and two that are not (empty, unfinished). */
    private function translation( $code, $translated )
    {
        $messages = '';
        for ( $i = 0; $i < $translated; $i++ )
            $messages .= "<message><source>Text $i</source><translation>T $i</translation></message>\n";
        $messages .= "<message><source>Empty</source><translation></translation></message>\n";
        $messages .= "<message><source>Open</source><translation type=\"unfinished\">x</translation></message>\n";
        mkdir( $this->dir . '/translations/' . $code, 0777, true );
        file_put_contents( $this->dir . '/translations/' . $code . '/translation.ts',
                           "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<!DOCTYPE TS>\n<TS version=\"2.0\">\n<context><name>test</name>\n$messages</context>\n</TS>\n" );
    }

    private function resolver( array $configured, array $rules = array(), array $options = array() )
    {
        return new expI18nFallbackResolver( $configured, $rules,
            array_merge( array( 'repository' => $this->dir . '/translations', 'locale_directory' => $this->dir . '/locale' ), $options ) );
    }

    public function testAChainFollowsTheConfiguredFallbacks()
    {
        $resolver = $this->resolver( array( 'esl-AR' => 'esl-MX', 'esl-MX' => 'esl-ES', 'ger-AT' => 'ger-DE', 'eng-IE' => 'eng-GB',
                                            'ara-AE' => 'ara-SA', 'ara-SA' => 'untranslated', 'ita-IT' => '' ), array(), array( 'automatic' => false ) );
        $this->assertSame( array( 'esl-MX', 'esl-ES' ), $resolver->chain( 'esl-AR' ) );
        $this->assertSame( array( 'esl-ES' ), $resolver->chain( 'esl-MX' ) );
        $this->assertSame( array(), $resolver->chain( 'esl-ES' ) );
        $this->assertSame( array( 'untranslated' ), $resolver->chain( 'eng-IE' ), 'eng-GB means the source texts' );
        $this->assertSame( array( 'ara-SA', 'untranslated' ), $resolver->chain( 'ara-AE' ) );
        $this->assertSame( array(), $resolver->chain( 'ita-IT' ), 'an empty value says nothing' );
        $explained = $resolver->explain( 'esl-AR' );
        $this->assertSame( array( array( 'target' => 'esl-MX', 'rule' => 'configured' ), array( 'target' => 'esl-ES', 'rule' => 'configured' ) ),
                           $explained['steps'] );
        $this->assertSame( '', $explained['problem'] );
        $this->assertSame( array(), $resolver->problems() );
    }

    public function testACycleIsCutAndReported()
    {
        $resolver = $this->resolver( array( 'esl-AR' => 'esl-CL', 'esl-CL' => 'esl-AR', 'ger-AT' => 'ger-AT' ), array(), array( 'automatic' => false ) );
        $this->assertSame( array( 'esl-CL' ), $resolver->chain( 'esl-AR' ) );
        $explained = $resolver->explain( 'esl-AR' );
        $this->assertSame( 'cycle', $explained['problem'] );
        $this->assertSame( 'esl-AR', $explained['problem_target'] );
        $this->assertSame( array(), $resolver->chain( 'ger-AT' ), 'a locale falling back to itself has no fallback' );
        $codes = array_column( $resolver->problems(), 'code', 'locale' );
        $this->assertSame( 'cycle', $codes['esl-AR'] );
        $this->assertSame( 'cycle', $codes['esl-CL'] );
    }

    public function testAChainLongerThanTheMaximumIsCut()
    {
        $map = array( 'esl-AR' => 'esl-CL', 'esl-CL' => 'esl-MX', 'esl-MX' => 'esl-ES', 'esl-ES' => 'untranslated' );
        $resolver = $this->resolver( $map, array(), array( 'automatic' => false, 'max_depth' => 2 ) );
        $this->assertSame( array( 'esl-CL', 'esl-MX' ), $resolver->chain( 'esl-AR' ) );
        $this->assertSame( 'depth', $resolver->explain( 'esl-AR' )['problem'] );
        $this->assertSame( array( 'esl-MX', 'esl-ES' ), $resolver->chain( 'esl-CL' ) );
        $this->assertSame( 4, $this->resolver( $map, array(), array( 'automatic' => false ) )->option( 'max_depth' ), 'the default' );
        $this->assertSame( 16, $this->resolver( $map, array(), array( 'max_depth' => 100 ) )->option( 'max_depth' ), 'at most 16' );
        $this->assertSame( 1, $this->resolver( $map, array(), array( 'max_depth' => 0 ) )->option( 'max_depth' ), 'at least 1' );
    }

    public function testTheAutomaticFallbackTakesTheMostCompleteTranslationOfTheLanguage()
    {
        $resolver = $this->resolver( array( 'ger-AT' => 'ger-CH' ) );
        $this->assertSame( array( 'ger-DE' ), $resolver->chain( 'ger-IT' ), 'ger-DE translates more than ger-CH' );
        $this->assertSame( 'automatic', $resolver->explain( 'ger-IT' )['steps'][0]['rule'] );
        $this->assertSame( array( 'fre-FR' ), $resolver->chain( 'fre-NC' ) );
        $this->assertSame( array( 'esl-ES' ), $resolver->chain( 'esl-AR' ) );
        $this->assertSame( array( 'ger-CH' ), $resolver->chain( 'ger-AT' ), 'the configured fallback wins, and ger-CH has a translation of its own' );
        $this->assertSame( array(), $resolver->chain( 'ger-CH' ), 'never for a locale with a translation of its own' );
        $this->assertSame( array(), $resolver->chain( 'esl-MX' ) );
        $this->assertSame( array(), $resolver->chain( 'eng-IE' ), 'English is excluded by default' );
        $this->assertSame( array(), $resolver->chain( 'ara-AE' ), 'no translation of the language' );
        $this->assertSame( array(), $resolver->chain( 'ger-IT@euro' ), 'a variant loads its locale anyway' );
        $this->assertSame( array( 'eng-US' ), $this->resolver( array(), array(), array( 'automatic_exclude' => array() ) )->chain( 'eng-IE' ) );
        $excluded = $this->resolver( array(), array(), array( 'automatic_exclude' => array( 'eng', 'fre-NC', 'ger-DE' ) ) );
        $this->assertSame( array(), $excluded->chain( 'fre-NC' ), 'a locale can be excluded' );
        $this->assertSame( array( 'ger-CH' ), $excluded->chain( 'ger-IT' ), 'an excluded locale is no target' );
        $this->assertSame( array(), $this->resolver( array(), array(), array( 'automatic' => false ) )->chain( 'ger-IT' ) );
        $this->assertNotContains( 'automatic', $this->resolver( array(), array(), array( 'automatic' => false ) )->ruleNames() );
    }

    public function testTranslatedCountLeavesOutEmptyAndUnfinishedMessages()
    {
        $this->assertSame( 10, expI18nFallbackSameLanguageRule::translatedCount( $this->dir . '/translations/ger-DE/translation.ts' ) );
        $this->assertSame( 3, expI18nFallbackSameLanguageRule::translatedCount( $this->dir . '/translations/ger-CH/translation.ts' ) );
        $this->assertSame( 0, expI18nFallbackSameLanguageRule::translatedCount( $this->dir . '/translations/xxx-XX/translation.ts' ) );
    }

    public function testRulesOfExtensionsComeAfterTheConfiguredFallbacksAndBeforeTheAutomaticOne()
    {
        $resolver = $this->resolver( array( 'esl-AR' => 'esl-MX' ), array( 'expI18nFallbackResolverTestRule', new expI18nFallbackResolverTestThrowingRule() ) );
        $this->assertSame( array( 'configured', 'expI18nFallbackResolverTestRule', 'expI18nFallbackResolverTestThrowingRule', 'automatic' ), $resolver->ruleNames() );
        $this->assertSame( array( 'esl-MX' ), $resolver->chain( 'esl-AR' ), 'configured first' );
        $this->assertSame( array( 'fre-FR' ), $resolver->chain( 'ger-IT' ), 'the extension rule before the automatic one' );
        $this->assertSame( 'expI18nFallbackResolverTestRule', $resolver->explain( 'ger-IT' )['steps'][0]['rule'] );
        $this->assertSame( array( 'esl-ES' ), $resolver->chain( 'esl-CL' ), 'a rule that throws has no say' );
        $this->assertSame( array(), $resolver->problems() );
    }

    public function testANameThatIsNoRuleIsReported()
    {
        $resolver = $this->resolver( array(), array( 'NoSuchFallbackRuleClass', 'stdClass', '' ) );
        $this->assertSame( array( 'configured', 'automatic' ), $resolver->ruleNames() );
        $problems = $resolver->problems();
        $this->assertCount( 2, $problems );
        $this->assertSame( array( 'error', 'invalid-rule', 'NoSuchFallbackRuleClass' ), array( $problems[0]['level'], $problems[0]['code'], $problems[0]['detail'] ) );
        $this->assertSame( 'stdClass', $problems[1]['detail'] );
    }

    public function testProblemsOfTargetsAndLocales()
    {
        $resolver = $this->resolver( array( 'esl-AR' => 'esl-XX', 'esl-CL' => 'spanish', 'xyz-QQ' => 'esl-ES', 'ara-AE' => 'ara-SA' ),
                                     array(), array( 'automatic' => false ) );
        $found = array();
        foreach ( $resolver->problems() as $problem )
            $found[] = $problem['level'] . ' ' . $problem['locale'] . ' ' . $problem['code'] . ' ' . $problem['detail'];
        sort( $found );
        $this->assertSame( array( 'error esl-AR missing-target esl-XX', 'error esl-CL invalid-target spanish',
                                  'warning ara-AE no-translation ara-SA', 'warning xyz-QQ unknown-locale xyz-QQ' ), $found );
        $this->assertSame( 1, \Exponential\Command\Kernel\I18n::exitCode( $resolver->problems(), false ) );
        $warnings = $this->resolver( array( 'ara-AE' => 'ara-SA' ), array(), array( 'automatic' => false ) )->problems();
        $this->assertSame( 0, \Exponential\Command\Kernel\I18n::exitCode( $warnings, false ), 'warnings pass' );
        $this->assertSame( 1, \Exponential\Command\Kernel\I18n::exitCode( $warnings, true ), 'unless strict' );
    }

    public function testTheSignatureChangesWithTheSettings()
    {
        $a = $this->resolver( array( 'esl-AR' => 'esl-ES', 'ger-AT' => 'ger-DE' ) );
        $b = $this->resolver( array( 'ger-AT' => 'ger-DE', 'esl-AR' => 'esl-ES' ) );
        $this->assertSame( $a->signature(), $b->signature(), 'the order of the lines does not matter' );
        $this->assertNotSame( $a->signature(), $this->resolver( array( 'esl-AR' => 'esl-MX', 'ger-AT' => 'ger-DE' ) )->signature() );
        $this->assertNotSame( $a->signature(), $this->resolver( array( 'esl-AR' => 'esl-ES', 'ger-AT' => 'ger-DE' ), array(), array( 'mode' => 'merge' ) )->signature() );
        $this->assertNotSame( $a->signature(), $this->resolver( array( 'esl-AR' => 'esl-ES', 'ger-AT' => 'ger-DE' ), array( 'expI18nFallbackResolverTestRule' ) )->signature() );
        $this->assertSame( 'first', $a->mode() );
        $this->assertSame( 'first', $this->resolver( array(), array(), array( 'mode' => 'other' ) )->mode() );
    }

    public function testTheShippedSettingsHaveNoProblem()
    {
        $root = dirname( __DIR__, 4 );
        preg_match_all( '/^FallbackLanguages\[([^\]]+)\]=(.*)$/m', file_get_contents( $root . '/settings/i18n.ini' ), $matches );
        $resolver = new expI18nFallbackResolver( array_combine( $matches[1], array_map( 'trim', $matches[2] ) ), array(),
                                                 array( 'repository' => $root . '/share/translations', 'locale_directory' => $root . '/share/locale' ) );
        $locales = array();
        foreach ( glob( $root . '/share/locale/*.ini' ) as $file )
            $locales[] = basename( $file, '.ini' );
        $errors = array_filter( $resolver->problems( array_merge( $locales, array_keys( $resolver->configured() ) ) ),
                                function ( $problem ) { return $problem['level'] === 'error'; } );
        $this->assertSame( array(), array_values( $errors ) );
        // the automatic fallback changes nothing for the shipped locales: every country of a translated language
        // has its FallbackLanguages[] line (expUISwitchLanguageProviderTest)
        foreach ( $locales as $locale )
        {
            foreach ( $resolver->explain( $locale )['steps'] as $step )
                $this->assertNotSame( 'automatic', $step['rule'], $locale );
        }
    }

    public function testSettingsAreReadFromI18nIni()
    {
        $settings = expI18nFallbackResolver::settings();
        $this->assertArrayHasKey( 'ger-AT', $settings['configured'] );
        $this->assertSame( array(), $settings['rules'] );
        $this->assertTrue( $settings['options']['automatic'] );
        $this->assertSame( array( 'eng' ), $settings['options']['automatic_exclude'] );
        $this->assertSame( 4, $settings['options']['max_depth'] );
        $this->assertSame( 'first', $settings['options']['mode'] );
        expI18nFallbackResolver::resetInstances();
        $this->assertSame( expI18nFallbackResolver::instance(), expI18nFallbackResolver::instance(), 'shared while the settings stay' );
        $this->assertSame( array( 'ger-DE' ), expI18nFallbackResolver::instance()->chain( 'ger-AT' ) );
    }
}

/** A rule of an extension: German locales without a rule fall back to French (a test, not advice). */
class expI18nFallbackResolverTestRule implements expI18nFallbackRuleInterface
{
    public function fallbackFor( $locale, expI18nFallbackResolver $resolver )
    {
        return $locale === 'ger-IT' ? 'fre-FR' : null;
    }
}

/** A rule that breaks. */
class expI18nFallbackResolverTestThrowingRule implements expI18nFallbackRuleInterface
{
    public function fallbackFor( $locale, expI18nFallbackResolver $resolver )
    {
        throw new RuntimeException( 'broken' );
    }
}
