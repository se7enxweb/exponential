<?php
/**
 * Tests of eZURLAliasML::getPathPrefix() and removePathPrefixFromURI(), which turn the url alias path of a node
 * into the URL of a siteaccess with a PathPrefix: the prefix is removed only as a whole first path element, and
 * the elements PathPrefixExclude names keep their full path.
 *
 * No database and no siteaccess. INI values are set in memory and put back in tearDown().
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class eZURLAliasMLPathPrefixTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
    }

    protected function tearDown(): void
    {
        ezpINIHelper::restoreINISettings();
    }

    public function testPathPrefixIsRemovedAsAWholeFirstElement()
    {
        ezpINIHelper::setINISetting( 'site.ini', 'SiteAccessSettings', 'PathPrefix', '' );
        $this->assertFalse( eZURLAliasML::getPathPrefix() );
        $this->assertSame( 'news/today', eZURLAliasML::removePathPrefixFromURI( 'news/today' ) );

        ezpINIHelper::setINISetting( 'site.ini', 'SiteAccessSettings', 'PathPrefix', '/Shop/' );
        ezpINIHelper::setINISetting( 'site.ini', 'SiteAccessSettings', 'PathPrefixExclude', array( 'media', 'user' ) );
        $this->assertSame( 'Shop', eZURLAliasML::getPathPrefix() );
        $this->assertSame( 'products/one', eZURLAliasML::removePathPrefixFromURI( 'Shop/products/one' ) );
        $this->assertSame( 'products', eZURLAliasML::removePathPrefixFromURI( 'shop/products' ) );
        $this->assertSame( '', eZURLAliasML::removePathPrefixFromURI( 'Shop' ) );
        $this->assertSame( 'Shopping/x', eZURLAliasML::removePathPrefixFromURI( 'Shopping/x' ) );
        $this->assertSame( 'media/images', eZURLAliasML::removePathPrefixFromURI( 'media/images' ) );
        $this->assertSame( 'user', eZURLAliasML::removePathPrefixFromURI( 'user' ) );
        $this->assertSame( 'other/x', eZURLAliasML::removePathPrefixFromURI( 'other/x' ) );
    }

    public function testPathPrefixWithoutAnExcludeList()
    {
        ezpINIHelper::setINISetting( 'site.ini', 'SiteAccessSettings', 'PathPrefix', 'Shop' );
        $ini = eZINI::instance();
        $had = $ini->hasVariable( 'SiteAccessSettings', 'PathPrefixExclude' );
        $old = $had ? $ini->variable( 'SiteAccessSettings', 'PathPrefixExclude' ) : null;
        $ini->removeSetting( 'SiteAccessSettings', 'PathPrefixExclude' );
        try
        {
            $this->assertSame( 'a', eZURLAliasML::removePathPrefixFromURI( 'Shop/a' ) );
        }
        finally
        {
            if ( $had )
                $ini->setVariable( 'SiteAccessSettings', 'PathPrefixExclude', $old );
        }
    }

}
