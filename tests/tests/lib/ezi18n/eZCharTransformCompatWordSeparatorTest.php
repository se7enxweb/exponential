<?php
/**
 * The separator of the old-style lowercase url aliases (url_cleanup_compat, TransformationGroup=urlalias_compat):
 * site.ini [URLTranslator] CompatWordSeparator.
 *
 *  CWS-01 - Underscore, the default and the shipped value: the aliases as before
 *  CWS-02 - Dash: lowercase aliases with dashes, separators at both ends trimmed -- what sites got before from a
 *           kernel override of eZCharTransform (bcurlaliaswithdash)
 *  CWS-03 - The setting missing from site.ini counts as underscore; an unknown value too
 *
 * Run: php vendor/bin/phpunit tests/tests/lib/ezi18n/eZCharTransformCompatWordSeparatorTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group lib
 * @group ezi18n
 */
#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezi18n')]
class eZCharTransformCompatWordSeparatorTest extends PHPUnit\Framework\TestCase
{
    /** @var array|null the setting before the test, array( value ) or null when it was missing */
    private $setting;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $ini = eZINI::instance();
        $this->setting = $ini->hasVariable( 'URLTranslator', 'CompatWordSeparator' ) ? array( $ini->variable( 'URLTranslator', 'CompatWordSeparator' ) ) : null;
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance();
        if ( $this->setting === null )
            $ini->removeSetting( 'URLTranslator', 'CompatWordSeparator' );
        else
            $ini->setVariable( 'URLTranslator', 'CompatWordSeparator', $this->setting[0] );
    }

    private function compat( $text )
    {
        return eZCharTransform::commandUrlCleanupCompat( $text, 'utf-8' );
    }

    /** CWS-01 */
    public function testUnderscoreIsTheDefault()
    {
        eZINI::instance()->setVariable( 'URLTranslator', 'CompatWordSeparator', 'underscore' );
        $this->assertSame( 'bhad_bhabie_gucci_flip_flops_feat_lil_yachty', $this->compat( 'Bhad Bhabie - Gucci Flip Flops (feat. Lil Yachty)' ) );
        $this->assertSame( 'iggy_azalea_pu_y', $this->compat( 'Iggy Azalea - Pu$$y' ) );
        $this->assertSame( 'underscore', eZINI::instance( 'site.ini', 'settings', null, null, false, true )->variable( 'URLTranslator', 'CompatWordSeparator' ),
                           'site.ini ships underscore' );
    }

    /** CWS-02 */
    public function testDashGivesLowercaseAliasesWithDashes()
    {
        eZINI::instance()->setVariable( 'URLTranslator', 'CompatWordSeparator', 'dash' );
        $this->assertSame( 'bhad-bhabie-gucci-flip-flops-feat-lil-yachty', $this->compat( 'Bhad Bhabie - Gucci Flip Flops (feat. Lil Yachty)' ) );
        $this->assertSame( 'iggy-azalea-pu-y', $this->compat( 'Iggy Azalea - Pu$$y' ) );
        $this->assertSame( 'k-flay-run-for-your-life-tomb-raider', $this->compat( '...K.Flay - Run For Your Life [Tomb Raider]!' ), 'separators at both ends trimmed' );
    }

    /** CWS-03 */
    public function testMissingOrUnknownMeansUnderscore()
    {
        $ini = eZINI::instance();
        $ini->removeSetting( 'URLTranslator', 'CompatWordSeparator' );
        $this->assertSame( 'a_b', $this->compat( 'A B' ) );
        $ini->setVariable( 'URLTranslator', 'CompatWordSeparator', 'tilde' );
        $this->assertSame( 'a_b', $this->compat( 'A B' ) );
    }
}
