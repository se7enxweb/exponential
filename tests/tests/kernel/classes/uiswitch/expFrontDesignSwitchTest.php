<?php
/**
 * The front design switch (expFrontDesignSwitch), without a database or a session: off unless SwitchableDesigns[]
 * lists designs, never on an administration siteaccess, the view parameter winning over the cookie, invalid and
 * unknown names ignored, the cookie cleared for the default, the in-memory apply and its reset for persistent workers,
 * the fallback chain, the cache variant key and the data the templates get.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/uiswitch/expFrontDesignSwitchTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__, 5 ) . '/kernel/classes/uiswitch/expfrontdesignswitch.php';

class expFrontDesignSwitchTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    protected $cwd;

    /** @var array site.ini [DesignSettings] before the test */
    protected $designSettings;

    protected function setUp(): void
    {
        $this->cwd = getcwd();
        chdir( dirname( __DIR__, 5 ) );
        $ini = eZINI::instance();
        $this->designSettings = array();
        foreach ( array( 'SiteDesign', 'AdditionalSiteDesignList', 'SwitchableDesigns', 'SwitchableDesignLabel', 'SwitchableDesignChain' ) as $name )
            $this->designSettings[$name] = $ini->hasVariable( 'DesignSettings', $name ) ? $ini->variable( 'DesignSettings', $name ) : array();
        expFrontDesignSwitch::setExistingDesigns( array( 'doc', 'doc2025' ) );
        expFrontDesignSwitch::beginRequest();
        unset( $GLOBALS['expUISwitchVariantKey'] );
    }

    protected function tearDown(): void
    {
        expFrontDesignSwitch::beginRequest();
        expFrontDesignSwitch::setExistingDesigns( null );
        $ini = eZINI::instance();
        foreach ( $this->designSettings as $name => $value )
            $ini->setVariable( 'DesignSettings', $name, $value );
        eZTemplateDesignResource::clearInMemoryCache();
        unset( $GLOBALS['eZTemplateDesignResourceBases'], $GLOBALS['expUISwitchVariantKey'] );
        chdir( $this->cwd );
    }

    /** a front siteaccess with the doc designs */
    protected function docSiteaccess( array $switchable = array( 'doc2025' ) )
    {
        $ini = eZINI::instance();
        $ini->setVariable( 'DesignSettings', 'SiteDesign', 'doc' );
        $ini->setVariable( 'DesignSettings', 'AdditionalSiteDesignList', array( 'base' ) );
        $ini->setVariable( 'DesignSettings', 'SwitchableDesigns', $switchable );
        $ini->setVariable( 'DesignSettings', 'SwitchableDesignLabel', array( 'doc' => 'Modern', 'doc2025' => 'Classic' ) );
        $ini->setVariable( 'DesignSettings', 'SwitchableDesignChain', array() );
        return $ini;
    }

    public function testOffWithoutDesigns()
    {
        $ini = $this->docSiteaccess( array() );
        $this->assertSame( array(), expFrontDesignSwitch::designs( $ini ) );
        $this->assertNull( expFrontDesignSwitch::applyRequest() );
        $this->assertFalse( expFrontDesignSwitch::isActive() );
        $this->assertSame( array(), expFrontDesignSwitch::switchData() );
    }

    public function testNeverOnAnAdministration()
    {
        $ini = $this->docSiteaccess();
        $ini->setVariable( 'DesignSettings', 'SiteDesign', 'admin4' );
        $this->assertSame( array(), expFrontDesignSwitch::designs( $ini ) );
        $ini->setVariable( 'DesignSettings', 'SiteDesign', 'doc' );
        $ini->setVariable( 'DesignSettings', 'AdditionalSiteDesignList', array( 'adminui' ) );
        $this->assertSame( array(), expFrontDesignSwitch::designs( $ini ) );
    }

    public function testDesignsAreDesignNamesWithoutDoubles()
    {
        $ini = $this->docSiteaccess( array( 'doc2025', 'doc2025', '../etc', 'Bad Name', '' ) );
        $this->assertSame( array( 'doc2025' ), expFrontDesignSwitch::designs( $ini ) );
    }

    public function testDesignFromUri()
    {
        $this->assertSame( 'doc2025', expFrontDesignSwitch::designFromUri( '/doc/manual/(design)/doc2025' ) );
        $this->assertSame( 'doc2025', expFrontDesignSwitch::designFromUri( '/(design)/doc2025/(offset)/10?x=1' ) );
        $this->assertSame( '', expFrontDesignSwitch::designFromUri( '/manual/page' ) );
        $this->assertSame( '', expFrontDesignSwitch::designFromUri( '/(design)/..%2Fetc' ) );
        $this->assertSame( '', expFrontDesignSwitch::designFromUri( '/page?(design)/doc2025' ) );
    }

    public function testViewParameterWinsAndSetsTheCookie()
    {
        $r = expFrontDesignSwitch::resolve( array( 'doc2025' ), 'doc', '/page/(design)/doc2025', 'doc' );
        $this->assertSame( array( 'design' => 'doc2025', 'cookie' => 'doc2025' ), $r );
    }

    public function testCookieIsUsedWithoutViewParameter()
    {
        $r = expFrontDesignSwitch::resolve( array( 'doc2025' ), 'doc', '/page', 'doc2025' );
        $this->assertSame( array( 'design' => 'doc2025', 'cookie' => null ), $r );
    }

    public function testChoosingTheDefaultClearsTheCookie()
    {
        $r = expFrontDesignSwitch::resolve( array( 'doc2025' ), 'doc', '/page/(design)/doc', 'doc2025' );
        $this->assertSame( array( 'design' => 'doc', 'cookie' => '' ), $r );
    }

    public function testUnknownOrMissingDesignsAreIgnored()
    {
        // not offered: the view parameter is ignored, the cookie naming it is cleared
        $r = expFrontDesignSwitch::resolve( array( 'doc2025' ), 'doc', '/page/(design)/admin4', 'admin4' );
        $this->assertSame( array( 'design' => 'doc', 'cookie' => '' ), $r );
        // offered but not installed
        expFrontDesignSwitch::setExistingDesigns( array( 'doc' ) );
        $r = expFrontDesignSwitch::resolve( array( 'doc2025' ), 'doc', '/page/(design)/doc2025', null );
        $this->assertSame( array( 'design' => 'doc', 'cookie' => null ), $r );
    }

    public function testApplyAndResetForTheNextRequest()
    {
        $ini = $this->docSiteaccess();
        $ini->setVariable( 'DesignSettings', 'SwitchableDesignChain', array( 'doc2025' => 'base;standard' ) );
        $GLOBALS['expUISwitchVariantKey'] = 'lang-ger';
        expFrontDesignSwitch::apply( 'doc2025', $ini );
        $this->assertSame( 'doc2025', $ini->variable( 'DesignSettings', 'SiteDesign' ) );
        $this->assertSame( array( 'base', 'standard' ), $ini->variable( 'DesignSettings', 'AdditionalSiteDesignList' ) );
        $this->assertSame( 'doc2025', expFrontDesignSwitch::appliedDesign() );
        $this->assertSame( 'lang-ger.fd-doc2025', $GLOBALS['expUISwitchVariantKey'] );

        expFrontDesignSwitch::beginRequest();
        $this->assertSame( 'doc', $ini->variable( 'DesignSettings', 'SiteDesign' ) );
        $this->assertSame( array( 'base' ), $ini->variable( 'DesignSettings', 'AdditionalSiteDesignList' ) );
        $this->assertNull( expFrontDesignSwitch::appliedDesign() );
    }

    public function testWithoutChainTheSiteaccessListStays()
    {
        $ini = $this->docSiteaccess();
        expFrontDesignSwitch::apply( 'doc2025', $ini );
        $this->assertSame( array( 'base' ), $ini->variable( 'DesignSettings', 'AdditionalSiteDesignList' ) );
        $this->assertSame( 'fd-doc2025', $GLOBALS['expUISwitchVariantKey'] );
    }

    public function testSwitchDataForTemplates()
    {
        $ini = $this->docSiteaccess();
        $_SERVER['REQUEST_URI'] = '/manual/(design)/doc2025';
        expFrontDesignSwitch::applyRequest();
        unset( $_SERVER['REQUEST_URI'] );
        $data = expFrontDesignSwitch::switchData();
        $this->assertTrue( expFrontDesignSwitch::isActive() );
        $this->assertSame( 'doc2025', $data['current'] );
        $this->assertSame( 'doc', $data['default'] );
        $this->assertSame( 'design', $data['parameter'] );
        $this->assertSame( array( 'doc', 'doc2025' ), array_column( $data['designs'], 'name' ) );
        $this->assertSame( array( 'Modern', 'Classic' ), array_column( $data['designs'], 'label' ) );
        $this->assertSame( array( false, true ), array_column( $data['designs'], 'current' ) );
    }

    public function testCookieNameFallsBackToTheDefault()
    {
        $ini = $this->docSiteaccess();
        $before = $ini->hasVariable( 'DesignSettings', 'DesignSwitchCookie' ) ? $ini->variable( 'DesignSettings', 'DesignSwitchCookie' ) : null;
        $ini->setVariable( 'DesignSettings', 'DesignSwitchCookie', 'bad name;' );
        $this->assertSame( 'exp_design', expFrontDesignSwitch::cookieName( $ini ) );
        $ini->setVariable( 'DesignSettings', 'DesignSwitchCookie', 'docdesign' );
        $this->assertSame( 'docdesign', expFrontDesignSwitch::cookieName( $ini ) );
        $ini->setVariable( 'DesignSettings', 'DesignSwitchCookie', $before === null ? 'exp_design' : $before );
    }
}
