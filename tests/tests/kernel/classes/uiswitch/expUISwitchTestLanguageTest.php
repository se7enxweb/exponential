<?php
/**
 * The interface language choice and test languages (#523): a locale marked [LanguageSettings] TestLanguage=true (Lorem
 * Ipsum, qlo-ZZ) is never in a ranked group nor in the A-Z list of AllLanguages; only AllowedValues[] offers it, and
 * then A-Z. Without a database: a fixture installation in var/tmp, and the real one for the shipped settings.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/uiswitch/expUISwitchTestLanguageTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

$expUISwitchRoot = dirname( __DIR__, 5 );
require_once $expUISwitchRoot . '/kernel/classes/uiswitch/expuiswitchproviderinterface.php';
require_once $expUISwitchRoot . '/kernel/classes/uiswitch/expuiswitchprovider.php';
require_once $expUISwitchRoot . '/kernel/classes/uiswitch/expuiswitchlanguagecatalogue.php';
require_once $expUISwitchRoot . '/kernel/classes/uiswitch/expuiswitchlanguageprovider.php';
unset( $expUISwitchRoot );

class expUISwitchTestLanguageTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    protected $root;

    /** @var string */
    protected $fixture;

    protected function setUp(): void
    {
        $this->root = dirname( __DIR__, 5 );
        $this->fixture = $this->root . '/var/tmp/uiswitch-test-language-' . getmypid() . '-' . mt_rand();
        mkdir( $this->fixture . '/share/locale', 0777, true );
        expUISwitchLanguageCatalogue::resetMemory();
        $this->locale( 'eng-GB', 'English (United Kingdom)', 'en-GB' );
        $this->locale( 'ger-DE', 'Deutsch (Deutschland)', 'de-DE' );
        $this->locale( 'qlo-ZZ', 'Lorem Ipsum', 'qlo-ZZ', "\n[LanguageSettings]\nTestLanguage=true\n" );
    }

    protected function tearDown(): void
    {
        expUISwitchLanguageCatalogue::resetMemory();
        foreach ( glob( $this->fixture . '/share/locale/*.ini' ) as $file )
            unlink( $file );
        foreach ( array( '/share/locale', '/share', '' ) as $dir )
            @rmdir( $this->fixture . $dir );
    }

    protected function locale( $code, $name, $http, $extra = '' )
    {
        file_put_contents( $this->fixture . '/share/locale/' . $code . '.ini',
                           "#?ini charset=\"utf-8\"?\n[RegionalSettings]\nLanguageName=$name\nInternationalLanguageName=$name\n$extra\n[HTTP]\nContentLanguage=$http\n" );
    }

    protected function provider( array $settings )
    {
        $settings += array( 'ListUntranslated' => 'enabled' );
        return new expUISwitchLanguageProvider( 'language', $settings, 'eng-GB', $this->fixture,
                                                new expUISwitchLanguageCatalogue( $this->fixture, '' ) );
    }

    protected function groupValues( expUISwitchLanguageProvider $provider )
    {
        $values = array();
        foreach ( $provider->groups() as $group )
            $values[$group['identifier']] = $group['values'];
        return $values;
    }

    public function testTheCatalogueKnowsTheTestLanguage()
    {
        $catalogue = new expUISwitchLanguageCatalogue( $this->fixture, '' );
        $this->assertTrue( $catalogue->isTestLocale( 'qlo-ZZ' ) );
        $this->assertFalse( $catalogue->isTestLocale( 'ger-DE' ) );
        $this->assertFalse( $catalogue->isTestLocale( 'xxx-XX' ) );
    }

    public function testNotInARankedGroupNorInAllLanguages()
    {
        $provider = $this->provider( array( 'GroupList' => array( 'major' ), 'GroupLabel' => array( 'major' => 'Major' ),
                                            'GroupValues_major' => array( 'qlo-ZZ', 'ger-DE' ), 'AllLanguages' => 'enabled' ) );
        $this->assertNotContains( 'qlo-ZZ', $provider->allowedValues() );
        $this->assertFalse( $provider->isValid( 'qlo-ZZ' ) );
        $groups = $this->groupValues( $provider );
        $this->assertSame( array( 'ger-DE' ), $groups['major'] );
        $this->assertNotContains( 'qlo-ZZ', $groups['all'] );
        $this->assertContains( 'ger-DE', $groups['all'] );
    }

    public function testAllowedValuesOffersItAToZButNeverRanked()
    {
        $provider = $this->provider( array( 'GroupList' => array( 'major' ), 'GroupLabel' => array( 'major' => 'Major' ),
                                            'GroupValues_major' => array( 'qlo-ZZ', 'ger-DE' ), 'AllLanguages' => 'enabled',
                                            'AllowedValues' => array( 'qlo-ZZ' ) ) );
        $this->assertTrue( $provider->isValid( 'qlo-ZZ' ) );
        $groups = $this->groupValues( $provider );
        $this->assertSame( array( 'ger-DE' ), $groups['major'] );
        $this->assertContains( 'qlo-ZZ', $groups['all'] );
    }

    public function testTheShippedSettingsDoNotOfferTheShippedTestLanguage()
    {
        $cwd = getcwd();
        chdir( $this->root );
        $ini = eZINI::fetchFromFile( 'settings/uiswitch.ini' );
        chdir( $cwd );
        $provider = new expUISwitchLanguageProvider( 'language', $ini->group( 'UISwitch_language' ), 'eng-GB', $this->root,
                                                     new expUISwitchLanguageCatalogue( $this->root, '' ) );
        $this->assertFileExists( $this->root . '/share/locale/qlo-ZZ.ini' );
        $this->assertNotContains( 'qlo-ZZ', $provider->allowedValues() );
        foreach ( $provider->configuredGroups() as $group => $values )
            $this->assertNotContains( 'qlo-ZZ', $values, $group );
    }
}
