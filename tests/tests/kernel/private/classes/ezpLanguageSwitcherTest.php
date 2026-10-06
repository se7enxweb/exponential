<?php
/**
 * ezpLanguageSwitcher without the database: the URL, user parameters and query string taken from the module
 * parameters, the PathPrefix added to and removed from a URL, the module check, and the language switcher links
 * of site.ini [RegionalSettings] TranslationSA.
 *
 * No database. The settings are set on the loaded instances and put back in tearDown(); the destination
 * siteaccess's site.ini is an eZINI that loads no file.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class ezpLanguageSwitcherTestDouble extends ezpLanguageSwitcher
{
    public function exposed( $name )
    {
        return $this->$name;
    }

    public function setIni( $ini )
    {
        $this->destinationSiteAccessIni = $ini;
    }

    public function pointsToModule( $url )
    {
        return $this->isUrlPointingToModule( $url );
    }

    public function fallbackAvailable()
    {
        return $this->isLocaleAvailableAsFallback();
    }

    public static function add( $url )
    {
        return self::addPathPrefixIfNeeded( $url );
    }

    public static function remove( eZINI $ini, &$url )
    {
        return self::removePathPrefixIfNeeded( $ini, $url );
    }
}

class ezpLanguageSwitcherTest extends PHPUnit\Framework\TestCase
{
    private $saved = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
    }

    protected function tearDown(): void
    {
        foreach ( array_reverse( $this->saved ) as $entry )
        {
            list( $file, $group, $name, $value ) = $entry;
            $ini = eZINI::instance( $file );
            if ( $value === null )
                $ini->removeSetting( $group, $name );
            else
                $ini->setVariable( $group, $name, $value[0] );
        }
        $this->saved = array();
    }

    private function setting( $file, $group, $name, $value )
    {
        $ini = eZINI::instance( $file );
        $this->saved[] = array( $file, $group, $name, $ini->hasVariable( $group, $name ) ? array( $ini->variable( $group, $name ) ) : null );
        if ( $value === null )
            $ini->removeSetting( $group, $name );
        else
            $ini->setVariable( $group, $name, $value );
    }

    private function siteAccessIni( array $settings )
    {
        $ini = new eZINI( 'site.ini', 'settings', null, false, false, false, false, false );
        foreach ( $settings as $group => $variables )
            foreach ( $variables as $name => $value )
                $ini->setVariable( $group, $name, $value );
        return $ini;
    }

    public function testConstructorWithoutParametersKeepsEverythingEmpty()
    {
        $switcher = new ezpLanguageSwitcherTestDouble();
        $this->assertNull( $switcher->exposed( 'origUrl' ) );
        $this->assertNull( $switcher->exposed( 'userParamString' ) );
        $this->assertNull( $switcher->exposed( 'queryString' ) );
    }

    public function testConstructorDropsTheSiteAccessAndJoinsTheRest()
    {
        $switcher = new ezpLanguageSwitcherTestDouble( array(
            'Parameters' => array( 'eng', 'news', 'article-1' ),
            'UserParameters' => array( 'offset' => 10, 'view' => 'list' ),
            'QueryString' => 'a=1&b=2' ) );
        $this->assertSame( 'news/article-1', $switcher->exposed( 'origUrl' ) );
        $this->assertSame( '/(offset)/10/(view)/list', $switcher->exposed( 'userParamString' ) );
        $this->assertSame( 'a=1&b=2', $switcher->exposed( 'queryString' ) );
    }

    public function testConstructorWithoutQueryStringUsesEmptyString()
    {
        $switcher = new ezpLanguageSwitcherTestDouble( array( 'Parameters' => array( 'ger' ), 'UserParameters' => array() ) );
        $this->assertSame( '', $switcher->exposed( 'origUrl' ) );
        $this->assertSame( '', $switcher->exposed( 'userParamString' ) );
        $this->assertSame( '', $switcher->exposed( 'queryString' ) );
    }

    public function testSetDestinationSiteAccess()
    {
        $switcher = new ezpLanguageSwitcherTestDouble();
        $switcher->setDestinationSiteAccess( 'ger' );
        $this->assertSame( 'ger', $switcher->exposed( 'destinationSiteAccess' ) );
    }

    public function testPathPrefixIsAddedWhenTheCurrentSiteAccessHasOne()
    {
        $this->setting( 'site.ini', 'SiteAccessSettings', 'PathPrefix', 'shop' );
        $this->assertSame( 'shop/products/a', ezpLanguageSwitcherTestDouble::add( 'products/a' ) );
    }

    public function testNoPathPrefixWhenEmptyOrMissing()
    {
        $this->setting( 'site.ini', 'SiteAccessSettings', 'PathPrefix', '' );
        $this->assertSame( 'products/a', ezpLanguageSwitcherTestDouble::add( 'products/a' ) );
        $this->setting( 'site.ini', 'SiteAccessSettings', 'PathPrefix', null );
        $this->assertSame( 'products/a', ezpLanguageSwitcherTestDouble::add( 'products/a' ) );
    }

    public static function removePrefixProvider()
    {
        return array(
            'prefix and path' => array( 'shop', 'shop/products/a', true, 'products/a' ),
            'prefix only' => array( 'shop', 'shop', true, '' ),
            'prefix not matched' => array( 'shop', 'news/a', false, 'news/a' ),
            'longer name is no match' => array( 'shop', 'shopping/a', false, 'shopping/a' ),
            'empty prefix' => array( '', 'news/a', true, 'news/a' ),
            'no prefix setting' => array( null, 'news/a', true, 'news/a' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('removePrefixProvider')]
    public function testPathPrefixIsRemovedOnlyWhenItMatches( $prefix, $url, $expectedResult, $expectedUrl )
    {
        $ini = $this->siteAccessIni( $prefix === null ? array() : array( 'SiteAccessSettings' => array( 'PathPrefix' => $prefix ) ) );
        $this->assertSame( $expectedResult, ezpLanguageSwitcherTestDouble::remove( $ini, $url ) );
        $this->assertSame( $expectedUrl, $url );
    }

    public function testUrlPointingToModule()
    {
        $this->setting( 'module.ini', 'ModuleSettings', 'ModuleList', array( 'content', 'user', 'shop' ) );
        $switcher = new ezpLanguageSwitcherTestDouble();
        $this->assertTrue( $switcher->pointsToModule( 'content/view/full/2' ) );
        $this->assertTrue( $switcher->pointsToModule( 'user' ) );
        $this->assertFalse( $switcher->pointsToModule( 'news/content' ) );
        $this->assertFalse( $switcher->pointsToModule( 'Content/view' ) );
        $this->assertFalse( $switcher->pointsToModule( '' ) );
    }

    public function testLocaleAvailableAsFallback()
    {
        $this->setting( 'site.ini', 'RegionalSettings', 'ContentObjectLocale', 'eng-GB' );
        $switcher = new ezpLanguageSwitcherTestDouble();
        $switcher->setIni( $this->siteAccessIni( array( 'RegionalSettings' => array( 'SiteLanguageList' => array( 'ger-DE', 'eng-GB' ) ) ) ) );
        $this->assertTrue( $switcher->fallbackAvailable() );
        $switcher->setIni( $this->siteAccessIni( array( 'RegionalSettings' => array( 'SiteLanguageList' => array( 'ger-DE' ) ) ) ) );
        $this->assertFalse( $switcher->fallbackAvailable() );
    }

    public function testNoTranslationSiteAccessesGiveNoLinks()
    {
        $this->setting( 'site.ini', 'RegionalSettings', 'TranslationSA', null );
        $this->assertSame( array(), ezpLanguageSwitcher::setupTranslationSAList( 'news' ) );
    }

    public function testEmptyTranslationSiteAccessListGivesNoLinks()
    {
        $this->setting( 'site.ini', 'RegionalSettings', 'TranslationSA', array() );
        $this->assertSame( array(), ezpLanguageSwitcher::setupTranslationSAList() );
    }
}
