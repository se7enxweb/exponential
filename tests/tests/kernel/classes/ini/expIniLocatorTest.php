<?php
/**
 * expIniLocator::where() on the real installation, read only: every file that sets a variable, in eZINI's load
 * order for a siteaccess, and the effective value from eZINI.
 *
 *  LOC-01 — settings/<file>.ini comes first, settings/override last, siteaccess files before extension files
 *  LOC-02 — the effective value of a plain variable is the value of the last file that sets it
 *  LOC-03 — an array's effective value is eZINI's merge (a reset in a later file wins)
 *  LOC-04 — every file found is mapped to its scope; the placement is eZINI's findSettingPlacement()
 *  LOC-05 — an unknown siteaccess is refused; a variable set nowhere is reported as not found
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group ini
 */

require_once __DIR__ . '/fixtures/expinienginetestfixtures.php';

class expIniLocatorTest extends PHPUnit\Framework\TestCase
{
    private $handler;

    private static function currentExceptionHandler()
    {
        $h = set_exception_handler( null );
        restore_exception_handler();
        return $h;
    }

    public function tearDown(): void
    {
        // the kernel's first eZINI/eZSiteAccess use installs an exception handler: take it off again
        for ( $i = 0; $i < 5 && self::currentExceptionHandler() !== $this->handler; ++$i )
            restore_exception_handler();
        parent::tearDown();
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->handler = self::currentExceptionHandler();
        expIniEditor::setRoot( null );
        if ( !is_dir( expIniEngineTestFixtures::realRoot() . 'settings/siteaccess/admin' ) )
            $this->markTestSkipped( 'no admin siteaccess here' );
    }

    /** LOC-01, LOC-02, LOC-04 */
    public function testLoadOrderAndEffective()
    {
        $w = expIniLocator::where( 'site.ini', 'DesignSettings', 'SiteDesign', 'admin' );
        $this->assertSame( 'admin', $w['siteaccess'] );
        $this->assertTrue( $w['found'] );
        $this->assertSame( 'settings/site.ini', $w['inputFiles'][0] );
        $this->assertSame( 'settings/site.ini', $w['files'][0]['path'] );
        $this->assertSame( 'default', $w['files'][0]['placement'] );
        $this->assertSame( 'default', $w['files'][0]['scope'] );
        $last = end( $w['files'] );
        $this->assertSame( $last['value'], $w['effective'] );

        $order = array_flip( $w['inputFiles'] );
        $sa = 'settings/siteaccess/admin/site.ini.append.php';
        $ov = 'settings/override/site.ini.append.php';
        if ( isset( $order[$sa], $order[$ov] ) )
            $this->assertLessThan( $order[$ov], $order[$sa] );
        if ( isset( $order[$ov] ) )
            $this->assertSame( count( $w['inputFiles'] ) - 1, $order[$ov], 'settings/override is loaded last' );
        foreach ( $w['inputFiles'] as $i => $f )
        {
            if ( preg_match( '#^extension/[^/]+/settings/[^/]+$#', $f ) && isset( $order[$sa] ) )
                $this->assertGreaterThan( $order[$sa], $i, "$f after the siteaccess file" );
        }
        foreach ( $w['files'] as $f )
            $this->assertNotNull( $f['scope'], $f['path'] . ' maps to a scope' );
    }

    /** LOC-03 */
    public function testArrayEffectiveIsEZINIs()
    {
        $w = expIniLocator::where( 'site', 'ExtensionSettings', 'ActiveExtensions', 'admin' );
        $this->assertTrue( $w['found'] );
        $this->assertIsArray( $w['effective'] );
        $ini = expIniLocator::iniFor( 'site.ini', 'admin' );
        $this->assertSame( $ini->variable( 'ExtensionSettings', 'ActiveExtensions' ), $w['effective'] );
        $this->assertNotEmpty( $w['files'] );
    }

    /** LOC-05 */
    public function testErrors()
    {
        try
        {
            expIniLocator::where( 'site', 'SiteSettings', 'SiteName', 'no_such_siteaccess_xyz' );
            $this->fail( 'unknown siteaccess accepted' );
        }
        catch ( expIniException $e )
        {
            $this->assertSame( expIniException::REFUSED, $e->getCode() );
        }
        $w = expIniLocator::where( 'site', 'ExpIniNoSuchBlock', 'Nothing', 'admin' );
        $this->assertFalse( $w['found'] );
        $this->assertNull( $w['effective'] );
        $this->assertSame( array(), $w['files'] );
    }
}
