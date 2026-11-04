<?php
/**
 * The siteaccess part of a cache-block key (eZTemplateCacheBlock::accessSuffix(), accessKey()):
 *   - without an access path and a UI switch it is the siteaccess name alone, as before
 *   - the same siteaccess below /site (URI matching) gets another key than at the root of its host (host
 *     matching), so the links of the one (/site/about) never reach the other (/about)
 *   - the UI switch variant still joins the key, after the access path
 *   - without a siteaccess and without a suffix the key stays false
 *   - the compiled {cache-block} code reads the suffix at run time
 *
 * No kernel bootstrap, no database.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group lib
 * @group eztemplate
 */
#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('eztemplate')]
class eZTemplateCacheBlockAccessPathTest extends PHPUnit\Framework\TestCase
{
    private $savedAccess;
    private $savedVariant;
    private $savedPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->savedAccess = isset( $GLOBALS['eZCurrentAccess'] ) ? $GLOBALS['eZCurrentAccess'] : null;
        $this->savedVariant = isset( $GLOBALS['expUISwitchVariantKey'] ) ? $GLOBALS['expUISwitchVariantKey'] : null;
        $this->savedPath = eZSys::instance()->AccessPath;
        eZSys::clearAccessPath();
        eZSys::clearAccessPath( false );
        unset( $GLOBALS['expUISwitchVariantKey'] );
        $GLOBALS['eZCurrentAccess'] = array( 'name' => 'site' );
    }

    protected function tearDown(): void
    {
        eZSys::setAccessPath( $this->savedPath['siteaccess']['url'], $this->savedPath['siteaccess']['name'] );
        eZSys::setAccessPath( $this->savedPath['path']['url'], $this->savedPath['path']['name'], false );
        if ( $this->savedAccess === null )
            unset( $GLOBALS['eZCurrentAccess'] );
        else
            $GLOBALS['eZCurrentAccess'] = $this->savedAccess;
        if ( $this->savedVariant === null )
            unset( $GLOBALS['expUISwitchVariantKey'] );
        else
            $GLOBALS['expUISwitchVariantKey'] = $this->savedVariant;
        parent::tearDown();
    }

    public function testWithoutAccessPathTheKeyIsTheSiteAccessName()
    {
        $this->assertSame( '', eZTemplateCacheBlock::accessSuffix() );
        $this->assertSame( 'site', eZTemplateCacheBlock::accessKey() );
    }

    public function testUriMatchedSiteAccessGetsItsOwnKey()
    {
        $root = eZTemplateCacheBlock::cachePath( eZTemplateCacheBlock::keyString( array( 'keys', 'placement', eZTemplateCacheBlock::accessKey() ) ), 89 );
        eZSys::setAccessPath( array( 'site' ), 'site' );
        $this->assertSame( '@/site', eZTemplateCacheBlock::accessSuffix() );
        $this->assertSame( 'site@/site', eZTemplateCacheBlock::accessKey() );
        $below = eZTemplateCacheBlock::cachePath( eZTemplateCacheBlock::keyString( array( 'keys', 'placement', eZTemplateCacheBlock::accessKey() ) ), 89 );
        $this->assertNotSame( $root, $below, 'the root and /site never share a cache-block' );
    }

    public function testUiSwitchVariantFollowsTheAccessPath()
    {
        $GLOBALS['expUISwitchVariantKey'] = 'fake-c';
        $this->assertSame( 'site-fake-c', eZTemplateCacheBlock::accessKey(), 'the key of a switch alone is as before' );
        eZSys::setAccessPath( array( 'site' ), 'site' );
        $this->assertSame( '@/site-fake-c', eZTemplateCacheBlock::accessSuffix() );
        $this->assertSame( 'site@/site-fake-c', eZTemplateCacheBlock::accessKey() );
    }

    public function testWithoutSiteAccessTheKeyStaysFalse()
    {
        unset( $GLOBALS['eZCurrentAccess'] );
        $this->assertFalse( eZTemplateCacheBlock::accessKey() );
    }

    public function testCompiledCacheBlockReadsTheSuffixAtRunTime()
    {
        $source = (string)file_get_contents( dirname( __DIR__, 4 ) . '/lib/eztemplate/classes/eztemplatecachefunction.php' );
        $this->assertStringContainsString( '$accessNameText . eZTemplateCacheBlock::accessSuffix() );', $source, 'the compiled form with keys' );
        $this->assertStringContainsString( '\$cacheBlockAccessSuffix = eZTemplateCacheBlock::accessSuffix();', $source, 'the compiled form without keys' );
        $this->assertStringNotContainsString( "\$GLOBALS['expUISwitchVariantKey']", $source );
    }
}
