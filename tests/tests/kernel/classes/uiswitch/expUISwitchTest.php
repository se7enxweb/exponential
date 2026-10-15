<?php
/**
 * The UI switch framework (expUISwitch, expUISwitchProvider, expUISwitchLanguageProvider, module uiswitch), without a
 * database or a session: the registry from settings, the language provider's allow-list and validation, the policy
 * checks (a user double), the request lifecycle for persistent Velocity workers (applied values, the cache variant
 * key and the providers' reset() at the next request), safe redirects, the set view's decisions, the module's policy
 * functions and the cache keys of compiled templates.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/uiswitch/expUISwitchTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

$expUISwitchRoot = dirname( __DIR__, 5 );
require_once $expUISwitchRoot . '/kernel/classes/uiswitch/expuiswitchproviderinterface.php';
require_once $expUISwitchRoot . '/kernel/classes/uiswitch/expuiswitchprovider.php';
require_once $expUISwitchRoot . '/kernel/classes/uiswitch/expuiswitchlanguageprovider.php';
require_once $expUISwitchRoot . '/kernel/classes/uiswitch/expuiswitch.php';
unset( $expUISwitchRoot );

/** A provider that records what the registry asks of it */
class expUISwitchTestProvider extends expUISwitchProvider
{
    public static $applied = array();
    public static $resets = 0;

    public function defaultValue()
    {
        return 'a';
    }

    public function apply( $value )
    {
        if ( !$this->isValid( $value ) || $value === $this->defaultValue() )
            return false;
        self::$applied[] = $value;
        $this->current = $value;
        return true;
    }

    public function reset()
    {
        self::$resets++;
        parent::reset();
    }
}

/** A class that is not a provider */
class expUISwitchTestNotAProvider
{
}

class expUISwitchTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    protected $cwd;

    /** @var string */
    protected $root;

    protected function setUp(): void
    {
        $this->cwd = getcwd();
        $this->root = dirname( __DIR__, 5 );
        chdir( $this->root );
        expUISwitchTestProvider::$applied = array();
        expUISwitchTestProvider::$resets = 0;
        expUISwitch::setSettings( null );
        expUISwitch::beginRequest();
    }

    protected function tearDown(): void
    {
        expUISwitch::beginRequest();
        expUISwitch::setSettings( null );
        unset( $GLOBALS['expUISwitchVariantKey'] );
        chdir( $this->cwd );
    }

    /** the shipped uiswitch.ini, with extra groups in memory */
    protected function settings( array $groups = array() )
    {
        $ini = eZINI::fetchFromFile( 'settings/uiswitch.ini' );
        $ini->setReadOnlySettingsCheck( false );
        if ( $groups )
            $ini->setVariables( $groups );
        return $ini;
    }

    /** settings with the test provider "fake" (values a, b, c) after language */
    protected function fakeSettings( array $fake = array() )
    {
        return $this->settings( array(
            'UISwitchSettings' => array( 'ProviderList' => array( 'language', 'fake' ) ),
            'UISwitch_fake' => $fake + array( 'Class' => 'expUISwitchTestProvider', 'AllowedValues' => array( 'a', 'b', 'c' ) ),
        ) );
    }

    /** a signed-in user (or not) holding uiswitch/<function> for the functions given */
    protected function user( array $functions, $registered = true, $id = 42 )
    {
        $user = $this->createStub( 'eZUser' );
        $user->method( 'isRegistered' )->willReturn( $registered );
        $user->method( 'id' )->willReturn( $id );
        $user->method( 'hasAccessTo' )->willReturnCallback( function ( $module, $function = false ) use ( $functions ) {
            return array( 'accessWord' => $module === 'uiswitch' && in_array( $function, $functions, true ) ? 'yes' : 'no' );
        } );
        return $user;
    }

    // ------------------------------------------------------------------ settings and registry

    public function testShippedSettingsOfferTheLanguageProvider()
    {
        $ini = $this->settings();
        $this->assertTrue( expUISwitch::isEnabled( $ini ) );
        $this->assertSame( array( 'language', 'design' ), expUISwitch::providerIdentifiers( $ini ), 'the language first, then the design' );
        $providers = expUISwitch::buildProviders( $ini );
        $this->assertSame( array( 'language', 'design' ), array_keys( $providers ) );
        $this->assertInstanceOf( 'expUISwitchLanguageProvider', $providers['language'] );
        $this->assertSame( 'disabled', $ini->variable( 'UISwitch_language', 'RememberPreference' ), 'the choice ends with the session by default' );
        $this->assertFalse( $providers['language']->remembersPreference() );
    }

    public function testRegistrySkipsMissingClassesNonProvidersDisabledAndIllFormedNames()
    {
        $ini = $this->settings( array(
            'UISwitchSettings' => array( 'ProviderList' => array( 'language', 'missing', 'notone', 'off', 'Bad-Name', 'fake', 'fake' ) ),
            'UISwitch_missing' => array( 'Class' => 'expUISwitchNoSuchClass' ),
            'UISwitch_notone' => array( 'Class' => 'expUISwitchTestNotAProvider' ),
            'UISwitch_off' => array( 'Class' => 'expUISwitchTestProvider', 'Enabled' => 'false' ),
            'UISwitch_fake' => array( 'Class' => 'expUISwitchTestProvider', 'AllowedValues' => array( 'a', 'b' ) ),
        ) );
        $this->assertSame( array( 'language', 'missing', 'notone', 'off', 'fake' ), expUISwitch::providerIdentifiers( $ini ) );
        $this->assertSame( array( 'language', 'fake' ), array_keys( expUISwitch::buildProviders( $ini ) ) );
    }

    public function testDisabledFrameworkHasNoProviders()
    {
        $ini = $this->settings( array( 'UISwitchSettings' => array( 'Enabled' => 'false' ) ) );
        $this->assertFalse( expUISwitch::isEnabled( $ini ) );
        expUISwitch::setSettings( $ini );
        $this->assertSame( array(), expUISwitch::providers() );
    }

    public function testPolicyFunctionsComeFromTheProviderList()
    {
        $this->assertSame( array( 'language', 'design' ), expUISwitch::policyFunctions( $this->settings() ) );
        $this->assertSame( array( 'language', 'fake' ), expUISwitch::policyFunctions( $this->fakeSettings() ) );
        $this->assertSame( array( 'language', 'design' ),
            expUISwitch::policyFunctions( $this->settings( array( 'UISwitchSettings' => array( 'ProviderList' => array( 'design' ) ) ) ) ),
            'language is always a function, so roles that hold it keep working' );
    }

    public function testModuleDeclaresOneFunctionPerProvider()
    {
        expUISwitch::setSettings( $this->fakeSettings() );
        $Module = $ViewList = $FunctionList = null;
        include $this->root . '/kernel/uiswitch/module.php';
        $this->assertSame( array( 'language', 'fake' ), array_keys( $FunctionList ) );
        $this->assertSame( 'language or fake', $ViewList['set']['functions'] );
        $this->assertSame( 'language or fake', $ViewList['state']['functions'] );
        $this->assertSame( 'set.php', $ViewList['set']['script'] );
    }

    // ------------------------------------------------------------------ the provider base and validation

    public function testAllowListIsStrictAndStartsWithTheDefault()
    {
        $provider = new expUISwitchTestProvider( 'fake', array( 'AllowedValues' => array( 'b', 'a', 'b', '../etc', ' c', 'c' ) ) );
        $this->assertSame( array( 'a', 'b', 'c' ), $provider->allowedValues() );
        $this->assertTrue( $provider->isValid( 'b' ) );
        foreach ( array( 'B', ' b', 'b ', '', null, array( 'b' ), 1, '../etc', 'd' ) as $bad )
            $this->assertFalse( $provider->isValid( $bad ), var_export( $bad, true ) );
        $this->assertSame( '', $provider->cacheVariantKey( 'a' ), 'the default value keeps every cache name' );
        $this->assertSame( 'fake-b', $provider->cacheVariantKey( 'b' ) );
        $this->assertSame( '', $provider->cacheVariantKey( 'zzz' ) );
    }

    public function testLanguageProviderOffersOnlyInstalledLocalesWithATranslation()
    {
        $provider = new expUISwitchLanguageProvider( 'language',
            array( 'AllowedValues' => array( 'eng-US', 'ger-DE', 'fre-FR', 'xxx-XX', 'ger-de', '../../etc/passwd', 'eng-GB', 'ger-DE@x/y' ) ),
            'eng-GB', $this->root );
        $this->assertSame( array( 'eng-GB', 'eng-US', 'ger-DE', 'fre-FR' ), $provider->allowedValues() );
        $this->assertSame( 'eng-GB', $provider->defaultValue() );
        $this->assertTrue( $provider->isValid( 'ger-DE' ) );
        foreach ( array( 'ger-de', 'xxx-XX', '../../etc/passwd', 'ger-DE ', 'ita-IT' ) as $bad )
            $this->assertFalse( $provider->isValid( $bad ), $bad );
        $this->assertSame( 'language-ger-DE', $provider->cacheVariantKey( 'ger-DE' ) );
        $this->assertSame( '', $provider->cacheVariantKey( 'eng-GB' ) );
        $this->assertSame( 'de-DE', $provider->labelLanguage( 'ger-DE' ) );
        $this->assertSame( 'Deutsch (Deutschland)', $provider->label( 'ger-DE' ) );
        $this->assertSame( '', $provider->labelLanguage( 'xxx-XX' ) );
    }

    public function testLanguageProviderOffersTheSiteaccessLocaleEvenWhenNotListed()
    {
        $provider = new expUISwitchLanguageProvider( 'language', array( 'AllowedValues' => array( 'ger-DE' ) ), 'eng-US', $this->root );
        $this->assertSame( array( 'eng-US', 'ger-DE' ), $provider->allowedValues() );
    }

    public function testLanguageProviderAppliesTheLocaleInMemoryOnly()
    {
        $ini = eZINI::instance();
        $before = $ini->variable( 'RegionalSettings', 'Locale' );
        $contentBefore = $ini->variable( 'RegionalSettings', 'ContentObjectLocale' );
        $siteLanguages = $ini->variable( 'RegionalSettings', 'SiteLanguageList' );
        $target = $before === 'ger-DE' ? 'eng-US' : 'ger-DE';
        $provider = new expUISwitchLanguageProvider( 'language', array( 'AllowedValues' => array( 'eng-US', 'ger-DE' ) ), $before, $this->root );
        try
        {
            $this->assertFalse( $provider->apply( 'xxx-XX' ) );
            $this->assertFalse( $provider->apply( $before ), 'the default is not applied, it is the absence of a switch' );
            $this->assertTrue( $provider->apply( $target ) );
            $this->assertSame( $target, $ini->variable( 'RegionalSettings', 'Locale' ) );
            $this->assertSame( $target, $provider->currentValue() );
            $this->assertSame( $target, eZLocale::instance()->localeFullCode(), 'eZLocale is built again from the new Locale' );
            $this->assertSame( $contentBefore, $ini->variable( 'RegionalSettings', 'ContentObjectLocale' ), 'content languages are untouched' );
            $this->assertSame( $siteLanguages, $ini->variable( 'RegionalSettings', 'SiteLanguageList' ) );
        }
        finally
        {
            $ini->setVariable( 'RegionalSettings', 'Locale', $before );
            $provider->reset();
        }
        $this->assertSame( $before, eZLocale::instance()->localeFullCode(), 'reset() forgets the switched locale' );
    }

    // ------------------------------------------------------------------ policy and siteaccess

    public function testPolicyIsCheckedPerProvider()
    {
        $this->assertTrue( expUISwitch::canUse( 'language', $this->user( array( 'language' ) ) ) );
        $this->assertFalse( expUISwitch::canUse( 'fake', $this->user( array( 'language' ) ) ) );
        $this->assertFalse( expUISwitch::canUse( 'language', $this->user( array() ) ) );
        $this->assertFalse( expUISwitch::canUse( 'language', $this->user( array( 'language' ), false ) ), 'never the anonymous user' );
        $this->assertFalse( expUISwitch::canUse( 'Language', $this->user( array( 'Language' ) ) ), 'not a provider identifier' );
    }

    public function testSwitchSiteAccessesAreTheAdministrations()
    {
        $ini = $this->settings();
        $this->assertTrue( expUISwitch::isSwitchSiteAccess( 'admin', $ini, array( 'admin4l', 'admin4', 'admin' ) ) );
        $this->assertTrue( expUISwitch::isSwitchSiteAccess( 'editor', $ini, array( 'editor', 'admin4' ) ) );
        $this->assertTrue( expUISwitch::isSwitchSiteAccess( 'adminui', $ini, array( 'adminui', 'admin4l' ) ) );
        $this->assertFalse( expUISwitch::isSwitchSiteAccess( 'site', $ini, array( 'media', 'standard' ) ), 'never a public site' );
        $this->assertFalse( expUISwitch::isSwitchSiteAccess( '', $ini, array( 'admin' ) ) );
        $listed = $this->settings( array( 'UISwitchSettings' => array( 'SiteAccessList' => array( 'editor' ) ) ) );
        $this->assertTrue( expUISwitch::isSwitchSiteAccess( 'editor', $listed, array( 'media' ) ) );
        $this->assertFalse( expUISwitch::isSwitchSiteAccess( 'admin', $listed, array( 'admin4' ) ) );
    }

    // ------------------------------------------------------------------ storage

    public function testSessionStateIsPerUserAndSiteaccessAndWellFormed()
    {
        $state = expUISwitch::stateWithValues( null, 42, 'admin', array( 'language' => 'ger-DE' ) );
        $state = expUISwitch::stateWithValues( $state, 42, 'editor', array( 'language' => 'eng-US' ) );
        $this->assertSame( array( 'language' => 'ger-DE' ), expUISwitch::valuesFromState( $state, 42, 'admin' ) );
        $this->assertSame( array( 'language' => 'eng-US' ), expUISwitch::valuesFromState( $state, 42, 'editor' ) );
        $this->assertSame( array(), expUISwitch::valuesFromState( $state, 43, 'admin' ), 'another user never gets them' );
        $this->assertSame( array(), expUISwitch::valuesFromState( $state, 42, 'site' ) );
        $this->assertSame( array(), expUISwitch::valuesFromState( 'garbage', 42, 'admin' ) );
        $this->assertSame( array( 'language' => 'x' ),
            expUISwitch::valuesFromState( array( 'user' => 42, 'values' => array( 'admin' => array( 'language' => 'x', 'Bad' => 'y', 'design' => array( 'z' ), 'fake' => '../a' ) ) ), 42, 'admin' ) );
        // another user's state is replaced, not merged
        $this->assertSame( array( 'user' => 7, 'values' => array( 'admin' => array( 'language' => 'ger-DE' ) ) ),
            expUISwitch::stateWithValues( $state, 7, 'admin', array( 'language' => 'ger-DE' ) ) );
        $this->assertSame( array( 'user' => 42, 'values' => array( 'editor' => array( 'language' => 'eng-US' ) ) ),
            expUISwitch::stateWithValues( $state, 42, 'admin', array() ) );
    }

    // ------------------------------------------------------------------ request lifecycle (Velocity)

    public function testApplyRechecksPolicyAndAllowListAndSetsTheVariantKey()
    {
        expUISwitch::setSettings( $this->fakeSettings() );
        expUISwitch::beginRequest();
        $this->assertSame( array(), expUISwitch::applyValues( array( 'fake' => 'b' ), $this->user( array( 'language' ) ) ), 'no policy, nothing applied' );
        $this->assertSame( array(), expUISwitch::applyValues( array( 'fake' => 'zzz' ), $this->user( array( 'fake' ) ) ), 'outside the allow-list' );
        $this->assertSame( array(), expUISwitch::applyValues( array( 'fake' => 'a' ), $this->user( array( 'fake' ) ) ), 'the default is no switch' );
        $this->assertSame( '', $GLOBALS['expUISwitchVariantKey'] );
        $this->assertSame( array(), expUISwitchTestProvider::$applied );

        $this->assertSame( array( 'fake' => 'c' ), expUISwitch::applyValues( array( 'fake' => 'c' ), $this->user( array( 'fake' ) ) ) );
        $this->assertSame( array( 'c' ), expUISwitchTestProvider::$applied );
        $this->assertSame( array( 'fake' => 'c' ), expUISwitch::appliedValues() );
        $this->assertSame( 'fake-c', expUISwitch::cacheVariantKey() );
        $this->assertSame( 'fake-c', $GLOBALS['expUISwitchVariantKey'] );
    }

    public function testNextRequestStartsCleanAndResetsWhatWasApplied()
    {
        expUISwitch::setSettings( $this->fakeSettings() );
        expUISwitch::beginRequest();
        expUISwitch::applyValues( array( 'fake' => 'b' ), $this->user( array( 'fake' ), true, 42 ) );
        $this->assertSame( 'fake-b', $GLOBALS['expUISwitchVariantKey'] );

        // the next request in the same worker, by another user without a choice
        expUISwitch::beginRequest();
        $this->assertSame( 1, expUISwitchTestProvider::$resets, 'the provider resets what it touched' );
        $this->assertSame( array(), expUISwitch::appliedValues() );
        $this->assertSame( '', $GLOBALS['expUISwitchVariantKey'] );
        $this->assertSame( '', expUISwitch::cacheVariantKey() );
        expUISwitch::applyValues( array(), $this->user( array( 'fake' ), true, 43 ) );
        $this->assertSame( '', $GLOBALS['expUISwitchVariantKey'], 'nothing leaks to the next user' );
        $this->assertSame( 'a', expUISwitch::provider( 'fake' )->currentValue() );

        // a request without a switch resets nothing
        expUISwitch::beginRequest();
        $this->assertSame( 1, expUISwitchTestProvider::$resets );
    }

    public function testVariantKeysAreSortedAndClean()
    {
        $this->assertSame( '', expUISwitch::composeVariantKey( array() ) );
        $this->assertSame( '', expUISwitch::composeVariantKey( array( 'language' => '' ) ) );
        $this->assertSame( 'design-classic.language-ger-DE',
            expUISwitch::composeVariantKey( array( 'language' => 'language-ger-DE', 'design' => 'design-classic' ) ) );
        $this->assertSame( 'x_y', expUISwitch::composeVariantKey( array( 'x' => 'x/y' ) ) );
    }

    public function testCompiledTemplateNamesChangeOnlyWithAVariant()
    {
        $resource = array( 'template-filename' => 'design/admin4/templates/pagelayout.tpl' );
        $GLOBALS['expUISwitchVariantKey'] = '';
        $plain = eZTemplateCompiler::compilationFilename( 'key', $resource );
        unset( $GLOBALS['expUISwitchVariantKey'] );
        $this->assertSame( $plain, eZTemplateCompiler::compilationFilename( 'key', $resource ) );
        $GLOBALS['expUISwitchVariantKey'] = 'design-classic';
        $this->assertNotSame( $plain, eZTemplateCompiler::compilationFilename( 'key', $resource ) );
    }

    // ------------------------------------------------------------------ the set view

    public function testRedirectsGoOnlyToLocalPaths()
    {
        $this->assertSame( '/content/view/full/2', expUISwitch::safeRedirect( '/content/view/full/2', '/f' ) );
        $this->assertSame( '/content/dashboard', expUISwitch::safeRedirect( 'content/dashboard', '/f' ) );
        foreach ( array( '//evil.example/x', 'https://evil.example/', '/https://evil.example', '/\\evil.example', "/a\nb",
                         '', null, array( '/x' ), '/a/../../b', 'javascript:alert(1)', '/' . str_repeat( 'a', 3000 ) ) as $bad )
            $this->assertSame( '/f', expUISwitch::safeRedirect( $bad, '/f' ), var_export( $bad, true ) );
    }

    public function testSetViewRefusesWhatItMust()
    {
        $post = array( 'Provider' => 'language', 'Value' => 'ger-DE', 'RedirectURI' => '/content/view/full/2' );
        $ok = Exponential\View\Kernel\Uiswitch\Set::decide( 'POST', 'same-origin', $post );
        $this->assertSame( 'set', $ok['step'] );
        $this->assertSame( '/content/view/full/2', $ok['redirect'] );
        $this->assertSame( 'set', Exponential\View\Kernel\Uiswitch\Set::decide( 'POST', null, $post )['step'], 'browsers without Sec-Fetch-Site' );

        $get = Exponential\View\Kernel\Uiswitch\Set::decide( 'GET', 'same-origin', $post );
        $this->assertSame( array( 'refused', 'method', 405 ), array( $get['step'], $get['reason'], Exponential\View\Kernel\Uiswitch\Set::status( $get['reason'] ) ) );
        $cross = Exponential\View\Kernel\Uiswitch\Set::decide( 'POST', 'cross-site', $post );
        $this->assertSame( array( 'refused', 'cross-site', 403 ), array( $cross['step'], $cross['reason'], Exponential\View\Kernel\Uiswitch\Set::status( $cross['reason'] ) ) );
        $bad = Exponential\View\Kernel\Uiswitch\Set::decide( 'POST', 'same-origin', array( 'Provider' => '../x', 'Value' => 'ger-DE' ) );
        $this->assertSame( array( 'refused', 'invalid', 400 ), array( $bad['step'], $bad['reason'], Exponential\View\Kernel\Uiswitch\Set::status( $bad['reason'] ) ) );
        $this->assertSame( 'refused', Exponential\View\Kernel\Uiswitch\Set::decide( 'POST', 'same-origin', array( 'Provider' => 'language', 'Value' => array( 'x' ) ) )['step'] );
        $this->assertSame( '/content/dashboard',
            Exponential\View\Kernel\Uiswitch\Set::decide( 'POST', 'same-origin', array( 'RedirectURI' => '//evil.example' ) + $post )['redirect'] );
        $this->assertSame( 403, Exponential\View\Kernel\Uiswitch\Set::status( 'denied' ) );
        $this->assertSame( 404, Exponential\View\Kernel\Uiswitch\Set::status( 'unavailable' ) );
    }
}
