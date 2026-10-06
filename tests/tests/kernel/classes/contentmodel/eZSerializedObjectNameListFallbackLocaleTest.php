<?php
/**
 * The locale eZSerializedObjectNameList stores a name in when no content language is prioritized yet:
 * ContentObjectLocale, else site.ini [RegionalSettings] ContentObjectFallbackLocale, else eng-US. It was a
 * hard-coded eng-GB, which left class names and descriptions in a language the site did not have.
 * The settings are given as a stand-in for eZINI, so no database and no settings directory is needed; the
 * shipped settings/site.ini is read as a file.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class eZSerializedObjectNameListFallbackLocaleTest extends PHPUnit\Framework\TestCase
{
    /** A stand-in for eZINI with the two methods the name list calls, holding [RegionalSettings] only. */
    private static function ini( array $regional )
    {
        return new class( $regional ) {
            private $values;
            public function __construct( array $values ) { $this->values = $values; }
            public function hasVariable( $group, $name ) { return $group === 'RegionalSettings' && array_key_exists( $name, $this->values ); }
            public function variable( $group, $name ) { return $this->hasVariable( $group, $name ) ? $this->values[$name] : false; }
        };
    }

    public function testDefaultIsEngUS()
    {
        $this->assertSame( 'eng-US', eZSerializedObjectNameList::DEFAULT_FALLBACK_LOCALE );
    }

    public function testFallbackReadsTheSetting()
    {
        $this->assertSame( 'ger-DE', eZSerializedObjectNameList::fallbackLanguageLocale( self::ini( array( 'ContentObjectFallbackLocale' => 'ger-DE' ) ) ) );
        $this->assertSame( 'nor-NO', eZSerializedObjectNameList::fallbackLanguageLocale( self::ini( array( 'ContentObjectFallbackLocale' => ' nor-NO ' ) ) ), 'trimmed' );
    }

    public static function missingSettingProvider()
    {
        return array( 'not set' => array( array() ), 'empty' => array( array( 'ContentObjectFallbackLocale' => '' ) ),
                      'blank' => array( array( 'ContentObjectFallbackLocale' => '  ' ) ), 'false' => array( array( 'ContentObjectFallbackLocale' => false ) ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('missingSettingProvider')]
    public function testFallbackWithoutTheSettingIsEngUS( array $regional )
    {
        $this->assertSame( 'eng-US', eZSerializedObjectNameList::fallbackLanguageLocale( self::ini( $regional ) ) );
    }

    public function testContentObjectLocaleComesFirst()
    {
        $ini = self::ini( array( 'ContentObjectLocale' => 'ger-DE', 'ContentObjectFallbackLocale' => 'fre-FR' ) );
        $this->assertSame( 'ger-DE', eZSerializedObjectNameList::configuredLanguageLocale( $ini ) );
    }

    public function testWithoutContentObjectLocaleTheFallbackIsUsed()
    {
        $this->assertSame( 'fre-FR', eZSerializedObjectNameList::configuredLanguageLocale( self::ini( array( 'ContentObjectLocale' => '', 'ContentObjectFallbackLocale' => 'fre-FR' ) ) ) );
        $this->assertSame( 'eng-US', eZSerializedObjectNameList::configuredLanguageLocale( self::ini( array() ) ), 'never eng-GB' );
    }

    /** The shipped defaults: content in eng-US, the fallback setting present and eng-US. */
    public function testShippedSiteIniDefaultsAreEngUS()
    {
        $siteIni = file_get_contents( dirname( __DIR__, 5 ) . '/settings/site.ini' );
        $this->assertSame( 1, preg_match( '/^\[RegionalSettings\]\R(.*?)^\[/ms', $siteIni, $m ), '[RegionalSettings] in settings/site.ini' );
        $this->assertMatchesRegularExpression( '/^ContentObjectLocale=eng-US$/m', $m[1] );
        $this->assertMatchesRegularExpression( '/^ContentObjectFallbackLocale=eng-US$/m', $m[1] );
    }

    /** No hard-coded eng-GB is left as the name list's fallback. */
    public function testNoHardCodedEngGBFallback()
    {
        $source = file_get_contents( dirname( __DIR__, 5 ) . '/kernel/classes/ezserializedobjectnamelist.php' );
        $this->assertStringNotContainsString( "= 'eng-GB'", $source );
    }
}
