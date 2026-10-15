<?php
/**
 * The administration design provider of the UI switch (expUISwitchDesignProvider), without a database or a session:
 * its allow-list (only designs with a fallback chain that exist, the siteaccess's own design first), strict
 * validation, the in-memory apply and reset of SiteDesign and AdditionalSiteDesignList[], the statics it clears for
 * persistent Velocity workers, its cache variant key, and the design base cache file name, which changes only with a
 * switched design.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/uiswitch/expUISwitchDesignProviderTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

$expUISwitchDesignRoot = dirname( __DIR__, 5 );
require_once $expUISwitchDesignRoot . '/kernel/classes/uiswitch/expuiswitchproviderinterface.php';
require_once $expUISwitchDesignRoot . '/kernel/classes/uiswitch/expuiswitchprovider.php';
require_once $expUISwitchDesignRoot . '/kernel/classes/uiswitch/expuiswitchdesignprovider.php';
require_once $expUISwitchDesignRoot . '/kernel/classes/uiswitch/expuiswitch.php';
unset( $expUISwitchDesignRoot );

class expUISwitchDesignProviderTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    protected $cwd;

    /** @var string */
    protected $root;

    /** @var array site.ini [DesignSettings] before the test */
    protected $designSettings;

    protected function setUp(): void
    {
        $this->cwd = getcwd();
        $this->root = dirname( __DIR__, 5 );
        chdir( $this->root );
        $ini = eZINI::instance();
        $this->designSettings = array(
            'SiteDesign' => $ini->variable( 'DesignSettings', 'SiteDesign' ),
            'AdditionalSiteDesignList' => $ini->variable( 'DesignSettings', 'AdditionalSiteDesignList' ),
        );
        unset( $GLOBALS['expUISwitchVariantKey'] );
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance();
        foreach ( $this->designSettings as $name => $value )
            $ini->setVariable( 'DesignSettings', $name, $value );
        eZTemplateDesignResource::clearInMemoryCache();
        unset( $GLOBALS['eZTemplateDesignResourceBases'], $GLOBALS['expUISwitchVariantKey'] );
        chdir( $this->cwd );
    }

    /** the settings of the design provider as the kernel ships them, with changes */
    protected function shipped( array $changes = array() )
    {
        $ini = eZINI::fetchFromFile( 'settings/uiswitch.ini' );
        return $changes + $ini->group( 'UISwitch_design' );
    }

    /** a provider on the siteaccess design admin4l, no design extensions */
    protected function provider( ?array $settings = null, $siteDefault = 'admin4l', array $extensions = array() )
    {
        return new expUISwitchDesignProvider( 'design', $settings === null ? $this->shipped() : $settings, $siteDefault, $this->root, $extensions );
    }

    public function testShippedSettingsListTheDesignProviderAfterTheLanguage()
    {
        $ini = eZINI::fetchFromFile( 'settings/uiswitch.ini' );
        $list = expUISwitch::providerIdentifiers( $ini );
        $this->assertContains( 'design', $list );
        $this->assertGreaterThan( array_search( 'language', $list, true ), array_search( 'design', $list, true ), 'the design follows the language in the toolbar' );
        $this->assertSame( 'expUISwitchDesignProvider', $ini->variable( 'UISwitch_design', 'Class' ) );
        $this->assertSame( 'disabled', $ini->variable( 'UISwitch_design', 'RememberPreference' ) );
        $providers = expUISwitch::buildProviders( $ini );
        $this->assertInstanceOf( 'expUISwitchDesignProvider', $providers['design'] );
        $this->assertContains( 'design', expUISwitch::policyFunctions( $ini ) );
    }

    public function testAllowListOffersExistingDesignsWithAChainAndTheDefaultFirst()
    {
        $provider = $this->provider();
        // adminui lives in the exp_adminui extension, which is not a design extension here
        $this->assertSame( array( 'admin4l', 'admin4', 'admin' ), $provider->allowedValues() );
        $this->assertSame( 'admin4l', $provider->defaultValue() );

        $editor = $this->provider( null, 'editor' );
        $this->assertSame( array( 'editor', 'admin4l', 'admin4', 'admin' ), $editor->allowedValues(), 'the siteaccess design is offered even without a chain' );
    }

    public function testDesignsOfDesignExtensionsAreOffered()
    {
        if ( !is_dir( $this->root . '/extension/exp_adminui/design/adminui' ) )
            $this->markTestSkipped( 'exp_adminui is not installed' );
        $provider = $this->provider( null, 'admin4l', array( 'exp_adminui' ) );
        $this->assertSame( array( 'admin4l', 'admin4', 'adminui', 'admin' ), $provider->allowedValues() );
    }

    public function testDesignsWithoutAChainOrADirectoryOrAWellFormedNameAreNotOffered()
    {
        $provider = $this->provider( array(
            'AllowedValues' => array( 'admin4', 'admin3', 'nosuchdesign', '../design/admin', 'Admin', 'admin/../x', 'admin' ),
            'DesignChain' => array( 'admin4' => 'admin3;admin2;admin', 'nosuchdesign' => 'admin', '../design/admin' => '', 'admin' => '' ),
        ) );
        $this->assertSame( array( 'admin4l', 'admin4', 'admin' ), $provider->allowedValues(), 'admin3 has no chain, nosuchdesign no directory' );
        foreach ( array( 'admin3', 'ADMIN4', ' admin4', 'admin4 ', '', null, array( 'admin4' ), 1, '../design/admin', 'nosuchdesign', 'standard' ) as $bad )
            $this->assertFalse( $provider->isValid( $bad ), var_export( $bad, true ) );
        $this->assertTrue( $provider->isValid( 'admin' ) );
    }

    public function testChainsAreCleanAndNeverHoldTheDesignItself()
    {
        $provider = $this->provider( array(
            'AllowedValues' => array( 'admin4' ),
            'DesignChain' => array( 'admin4' => ' admin3 ;admin4;;../x;admin3; admin ', 'admin' => '' ),
        ) );
        $this->assertSame( array( 'admin3', 'admin' ), $provider->chain( 'admin4' ) );
        $this->assertSame( array(), $provider->chain( 'admin' ), 'an empty chain is a chain' );
        $this->assertNull( $provider->chain( 'admin2' ) );
    }

    public function testLabelsComeFromTheSettings()
    {
        $provider = $this->provider();
        $this->assertSame( 'Classic', $provider->label( 'admin' ) );
        $this->assertSame( 'Admin 4 (layouts)', $provider->label( 'admin4l' ) );
        $this->assertSame( 'editor', $this->provider( null, 'editor' )->label( 'editor' ), 'a design without a label shows its name' );
    }

    public function testApplySwitchesTheDesignInMemoryAndResetForgetsIt()
    {
        $ini = eZINI::instance();
        $ini->setVariable( 'DesignSettings', 'SiteDesign', 'admin4l' );
        $ini->setVariable( 'DesignSettings', 'AdditionalSiteDesignList', array( 'admin4', 'admin3', 'admin2', 'admin' ) );
        eZTemplateDesignResource::clearInMemoryCache();
        $this->assertSame( 'admin4l', eZTemplateDesignResource::designSetting( 'site' ) );
        $GLOBALS['eZTemplateDesignResourceBases'] = array( 'design/admin4l' );

        $provider = $this->provider();
        $this->assertFalse( $provider->apply( 'nosuchdesign' ) );
        $this->assertFalse( $provider->apply( 'admin4l' ), 'the default is not applied, it is the absence of a switch' );
        $this->assertSame( array( 'design/admin4l' ), $GLOBALS['eZTemplateDesignResourceBases'], 'a refused value touches nothing' );

        $this->assertTrue( $provider->apply( 'admin' ) );
        $this->assertSame( 'admin', $ini->variable( 'DesignSettings', 'SiteDesign' ) );
        $this->assertSame( array(), $ini->variable( 'DesignSettings', 'AdditionalSiteDesignList' ) );
        $this->assertSame( 'admin', eZTemplateDesignResource::designSetting( 'site' ), 'the design name is read again' );
        $this->assertArrayNotHasKey( 'eZTemplateDesignResourceBases', $GLOBALS, 'the design bases are found again' );
        $this->assertSame( 'admin', $provider->currentValue() );
        $this->assertSame( 'design-admin', $provider->cacheVariantKey( 'admin' ) );
        $this->assertSame( '', $provider->cacheVariantKey( 'admin4l' ) );

        $this->assertTrue( $provider->apply( 'admin4' ) );
        $this->assertSame( array( 'admin3', 'admin2', 'admin' ), $ini->variable( 'DesignSettings', 'AdditionalSiteDesignList' ) );

        $GLOBALS['eZTemplateDesignResourceBases'] = array( 'design/admin4' );
        $provider->reset();
        $this->assertArrayNotHasKey( 'eZTemplateDesignResourceBases', $GLOBALS, 'reset() forgets the switched design bases' );
        $this->assertSame( 'admin4l', $provider->currentValue() );
    }

    public function testDesignBaseCacheFileChangesOnlyWithAChoice()
    {
        unset( $GLOBALS['expUISwitchVariantKey'] );
        $plain = eZTemplateDesignResource::designBaseCachePath();
        $siteAccessName = $GLOBALS['eZCurrentAccess']['name'] ?? '';
        $this->assertSame( eZSys::cacheDirectory() . '/' . eZTemplateDesignResource::DESIGN_BASE_CACHE_NAME . md5( $siteAccessName ) . '.php', $plain, 'the name of before the switch existed' );
        $GLOBALS['expUISwitchVariantKey'] = '';
        $this->assertSame( $plain, eZTemplateDesignResource::designBaseCachePath() );

        $GLOBALS['expUISwitchVariantKey'] = 'design-admin';
        $classic = eZTemplateDesignResource::designBaseCachePath();
        $this->assertNotSame( $plain, $classic );
        $GLOBALS['expUISwitchVariantKey'] = 'design-admin4';
        $this->assertNotSame( $classic, eZTemplateDesignResource::designBaseCachePath(), 'each design its own file' );

        $named = eZTemplateDesignResource::designBaseCachePath( 'admin' );
        unset( $GLOBALS['expUISwitchVariantKey'] );
        $this->assertSame( $named, eZTemplateDesignResource::designBaseCachePath( 'admin' ), 'a siteaccess asked for by name is never switched' );
    }

    public function testRegistryAppliesTheDesignAndSetsTheVariantKey()
    {
        $ini = eZINI::instance();
        $ini->setVariable( 'DesignSettings', 'SiteDesign', 'admin4l' );
        $settings = eZINI::fetchFromFile( 'settings/uiswitch.ini' );
        $settings->setReadOnlySettingsCheck( false );
        $settings->setVariables( array( 'UISwitchSettings' => array( 'ProviderList' => array( 'design' ) ) ) );
        expUISwitch::setSettings( $settings );
        expUISwitch::beginRequest();
        try
        {
            $user = $this->createStub( 'eZUser' );
            $user->method( 'isRegistered' )->willReturn( true );
            $user->method( 'id' )->willReturn( 42 );
            $user->method( 'hasAccessTo' )->willReturn( array( 'accessWord' => 'no' ) );
            $this->assertSame( array(), expUISwitch::applyValues( array( 'design' => 'admin' ), $user ), 'no policy, no design' );
            $this->assertSame( 'admin4l', $ini->variable( 'DesignSettings', 'SiteDesign' ) );

            $user = $this->createStub( 'eZUser' );
            $user->method( 'isRegistered' )->willReturn( true );
            $user->method( 'id' )->willReturn( 42 );
            $user->method( 'hasAccessTo' )->willReturnCallback( function ( $module, $function = false ) {
                return array( 'accessWord' => $module === 'uiswitch' && $function === 'design' ? 'yes' : 'no' );
            } );
            $this->assertSame( array(), expUISwitch::applyValues( array( 'design' => '../admin' ), $user ), 'a value off the list is refused' );
            $this->assertSame( array( 'design' => 'admin' ), expUISwitch::applyValues( array( 'design' => 'admin' ), $user ) );
            $this->assertSame( 'admin', $ini->variable( 'DesignSettings', 'SiteDesign' ) );
            $this->assertSame( 'design-admin', $GLOBALS['expUISwitchVariantKey'] );

            expUISwitch::beginRequest();
            $this->assertSame( '', $GLOBALS['expUISwitchVariantKey'], 'the next request starts without the choice' );
        }
        finally
        {
            expUISwitch::beginRequest();
            expUISwitch::setSettings( null );
        }
    }
}
