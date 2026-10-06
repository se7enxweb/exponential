<?php
/**
 * The installation name of site.ini [SiteSettings] EzInstallationName: ExpInstallationDetailsOutputFilter (the
 * hidden comment after <head>, the name before the title and in placeholders on systems that are not production,
 * nothing visible on production, HTML escaped) and the template operators installation_name and
 * is_production_system.
 *
 * No database. The settings are set on the loaded site.ini and put back; the filter's cache is reset around each
 * test.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class ExpInstallationDetailsTest extends PHPUnit\Framework\TestCase
{
    private $saved = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        ExpInstallationDetailsOutputFilter::resetCache();
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance();
        foreach ( array_reverse( $this->saved ) as $entry )
        {
            if ( $entry[1] === null )
                $ini->removeSetting( 'SiteSettings', $entry[0] );
            else
                $ini->setVariable( 'SiteSettings', $entry[0], $entry[1][0] );
        }
        $this->saved = array();
        ExpInstallationDetailsOutputFilter::resetCache();
    }

    private function installation( $name, $productionList )
    {
        $ini = eZINI::instance();
        foreach ( array( 'EzInstallationName' => $name, 'ProductionInstallationList' => $productionList ) as $setting => $value )
        {
            $this->saved[] = array( $setting, $ini->hasVariable( 'SiteSettings', $setting ) ? array( $ini->variable( 'SiteSettings', $setting ) ) : null );
            if ( $value === null )
                $ini->removeSetting( 'SiteSettings', $setting );
            else
                $ini->setVariable( 'SiteSettings', $setting, $value );
        }
        ExpInstallationDetailsOutputFilter::resetCache();
    }

    private const PAGE = '<html><HEAD><title>Home</title></head><body><p><!--INSTALLATION_NAME--></p><title>second</title><head></body></html>';

    public function testNoNameLeavesThePageAlone()
    {
        $this->installation( null, null );
        $this->assertSame( '', ExpInstallationDetailsOutputFilter::installationName() );
        $this->assertFalse( ExpInstallationDetailsOutputFilter::isProductionSystem() );
        $this->assertSame( self::PAGE, ExpInstallationDetailsOutputFilter::filter( self::PAGE ) );
    }

    public function testStagingShowsTheNameEscaped()
    {
        $this->installation( ' Stage <K1> & "B" ', array( 'live' ) );
        $this->assertSame( 'Stage <K1> & "B"', ExpInstallationDetailsOutputFilter::installationName() );
        $this->assertFalse( ExpInstallationDetailsOutputFilter::isProductionSystem() );
        $escaped = 'Stage &lt;K1&gt; &amp; &quot;B&quot;';
        $this->assertSame( '<html><head><!-- [I] ' . $escaped . ' --><title>[' . $escaped . '] Home</title></head><body><p>' . $escaped
                         . '</p><title>second</title><head></body></html>', ExpInstallationDetailsOutputFilter::filter( self::PAGE ) );
    }

    public function testProductionHidesTheName()
    {
        $this->installation( 'live', array( 'other', 'live' ) );
        $this->assertTrue( ExpInstallationDetailsOutputFilter::isProductionSystem() );
        $this->assertSame( '<html><head><!-- [I] live --><title>Home</title></head><body><p></p><title>second</title><head></body></html>',
                           ExpInstallationDetailsOutputFilter::filter( self::PAGE ) );
    }

    public function testProductionListThatIsNotAListMeansNotProduction()
    {
        $this->installation( 'live', 'live' );
        $this->assertFalse( ExpInstallationDetailsOutputFilter::isProductionSystem() );
    }

    public function testAnswersAreKeptUntilReset()
    {
        $this->installation( 'first', array( 'first' ) );
        $this->assertSame( 'first', ExpInstallationDetailsOutputFilter::installationName() );
        $this->assertTrue( ExpInstallationDetailsOutputFilter::isProductionSystem() );
        eZINI::instance()->setVariable( 'SiteSettings', 'EzInstallationName', 'second' );
        $this->assertSame( 'first', ExpInstallationDetailsOutputFilter::installationName() );
        ExpInstallationDetailsOutputFilter::resetCache();
        $this->assertSame( 'second', ExpInstallationDetailsOutputFilter::installationName() );
        $this->assertFalse( ExpInstallationDetailsOutputFilter::isProductionSystem() );
    }

    public function testPageWithoutHeadOrTitle()
    {
        $this->installation( 'stage', array() );
        $this->assertSame( 'plain text', ExpInstallationDetailsOutputFilter::filter( 'plain text' ) );
    }

    // ---------------------------------------------------------------- operators

    private static function apply( $operator, array $named )
    {
        $operators = new ExpInstallationOperator();
        $value = null;
        $operators->modify( null, $operator, array(), '', '', $value, $named );
        return $value;
    }

    public function testOperatorDefinitions()
    {
        $operators = new ExpInstallationOperator();
        $this->assertSame( array( 'installation_name', 'is_production_system' ), $operators->operatorList() );
        $this->assertTrue( $operators->namedParameterPerOperator() );
        $this->assertSame( array( 'installation_name', 'is_production_system' ), array_keys( $operators->namedParameterList() ) );
        $this->assertFalse( $operators->namedParameterList()['installation_name']['hide_on_prod']['default'] );
    }

    public function testOperatorsOnStaging()
    {
        $this->installation( 'stage', array( 'live' ) );
        $this->assertSame( 'stage', self::apply( 'installation_name', array() ) );
        $this->assertSame( 'stage', self::apply( 'installation_name', array( 'hide_on_prod' => true ) ) );
        $this->assertFalse( self::apply( 'is_production_system', array() ) );
    }

    public function testOperatorsOnProduction()
    {
        $this->installation( 'live', array( 'live' ) );
        $this->assertSame( 'live', self::apply( 'installation_name', array( 'hide_on_prod' => false ) ) );
        $this->assertSame( '', self::apply( 'installation_name', array( 'hide_on_prod' => 1 ) ) );
        $this->assertTrue( self::apply( 'is_production_system', array() ) );
        $this->assertNull( self::apply( 'k1_unknown', array() ) );
    }
}
