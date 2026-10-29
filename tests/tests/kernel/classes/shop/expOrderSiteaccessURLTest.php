<?php
/**
 * The order view links the siteaccess an order was placed in: its front page address and, per product, the
 * product's node as that siteaccess serves it (expOrderSiteaccess::frontURL(), servedURL(), nodeURL()).
 *
 *  - The front page address is the one the siteaccess menu of the administration links (ezpSiteAccessURL::root()):
 *    a host map gives a host, URI matching a path on this host; an unknown siteaccess or none has no address.
 *  - A node's address is that address and its URL alias without the siteaccess's PathPrefix; a node outside the
 *    prefix is not served there and gets none; PathPrefixExclude keeps a path whole.
 *  - Both order view templates show the links with messages the catalogues already translate.
 *
 * No database. The matching rules are injected into site.ini (eZINI::injectSettings(), put back in tearDown()) for
 * siteaccesses that do not exist (k2-*), the request is $_SERVER over HTTPS, and a siteaccess's own site.ini is a
 * stand-in holding only its [SiteAccessSettings].
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/shop/expOrderSiteaccessURLTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 * @group shop
 */

/** The site.ini of a siteaccess, with nothing but the values given. */
class expOrderSiteaccessURLTestINI extends eZINI
{
    private $values;

    public function __construct( array $values = array() )
    {
        $this->values = $values;
    }

    function hasVariable( $blockName, $varName )
    {
        return isset( $this->values[$blockName][$varName] );
    }

    function variable( $blockName, $varName )
    {
        return isset( $this->values[$blockName][$varName] ) ? $this->values[$blockName][$varName] : false;
    }
}

class expOrderSiteaccessURLTest extends PHPUnit\Framework\TestCase
{
    private $server;
    private $injected;
    private $index;
    private $handler;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->server = $_SERVER;
        $property = new ReflectionProperty( 'eZINI', 'injectedSettings' );
        $this->injected = $property->getValue();
        foreach ( array( 'HTTP_X_FORWARDED_HOST', 'HTTP_X_FORWARDED_PROTO', 'HTTP_X_FORWARDED_PORT', 'HTTP_X_FORWARDED_SERVER', 'SERVER_PORT' ) as $key )
            unset( $_SERVER[$key] );
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['HTTP_HOST'] = 'edit.k2.example.invalid';
        $index = trim( (string)eZSys::indexFile( false ), '/' );
        $this->index = $index !== '' ? $index . '/' : '';
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->server;
        eZINI::injectSettings( $this->injected );
        // Loading a siteaccess's settings registers eZExecution's shutdown and exception handlers once per process
        $current = set_exception_handler( null );
        restore_exception_handler();
        if ( $current === array( 'eZExecution', 'defaultExceptionHandler' ) && $this->handler !== $current )
            restore_exception_handler();
    }

    protected function assertPreConditions(): void
    {
        $this->handler = set_exception_handler( null );
        restore_exception_handler();
    }

    /**
     * k2-shop matched by its host, k2-ger by the URI element on any host, k2-admin on the host of the administration.
     */
    private function rules()
    {
        $settings = $this->injected;
        $settings['site.ini']['SiteSettings']['DefaultAccess'] = 'k2-shop';
        $settings['site.ini']['SiteAccessSettings'] = array(
            'AvailableSiteAccessList' => array( 'k2-shop', 'k2-ger', 'k2-admin' ),
            'MatchOrder' => 'host;uri',
            'HostMatchType' => 'map',
            'HostMatchMethod' => 'strict',
            'HostMatchMapItems' => array( 'shop.k2.example.invalid;k2-shop', 'edit.k2.example.invalid;k2-admin' ),
            'URIMatchType' => 'element',
            'URIMatchElement' => '1',
            'RemoveSiteAccessIfDefaultAccess' => 'disabled',
        );
        eZINI::injectSettings( $settings );
    }

    private function prefix( $prefix, array $exclude = array() )
    {
        return new expOrderSiteaccessURLTestINI( array( 'SiteAccessSettings' => array( 'PathPrefix' => $prefix, 'PathPrefixExclude' => $exclude ) ) );
    }

    public function testHostMatchedSiteAccessIsItsHost()
    {
        $this->rules();
        $this->assertSame( 'https://shop.k2.example.invalid/' . $this->index, expOrderSiteaccess::frontURL( 'k2-shop' ) );
        $this->assertSame( ezpSiteAccessURL::root( 'k2-shop' ), expOrderSiteaccess::frontURL( 'k2-shop' ) );
    }

    public function testUriMatchedSiteAccessIsItsUriOnThisHost()
    {
        $this->rules();
        $_SERVER['HTTP_HOST'] = 'www.k2.example.invalid';
        $this->assertSame( 'https://www.k2.example.invalid/' . $this->index . 'k2-ger/', expOrderSiteaccess::frontURL( 'k2-ger' ) );
        $this->assertSame( 'https://www.k2.example.invalid/' . $this->index . 'k2-ger/products/tea',
                           expOrderSiteaccess::servedURL( expOrderSiteaccess::frontURL( 'k2-ger' ), 'products/tea', new expOrderSiteaccessURLTestINI() ) );
    }

    public function testUnknownSiteAccessHasNoAddress()
    {
        $this->rules();
        $this->assertFalse( expOrderSiteaccess::frontURL( 'k2-gone' ) );
        $this->assertFalse( expOrderSiteaccess::frontURL( '' ) );
        $this->assertFalse( expOrderSiteaccess::frontURL( '   ' ) );
        $this->assertFalse( expOrderSiteaccess::servedURL( false, 'products/tea', null ) );
        $this->assertFalse( expOrderSiteaccess::servedURL( '', 'products/tea', null ) );
        $this->assertFalse( expOrderSiteaccess::nodeURL( null, 'k2-shop' ) );
        $this->assertSame( array(), expOrderSiteaccess::productURLs( 'k2-gone', array( array( 'item_object' => null ) ) ) );
        $this->assertSame( array(), expOrderSiteaccess::productURLs( '', array() ) );
    }

    public function testNodeUnderThePathPrefixLosesIt()
    {
        $this->rules();
        $front = expOrderSiteaccess::frontURL( 'k2-shop' );
        $ini = $this->prefix( 'fit-healthy' );
        $this->assertSame( $front . 'recipes/x', expOrderSiteaccess::servedURL( $front, 'fit-healthy/recipes/x', $ini ) );
        $this->assertSame( $front . 'recipes/x', expOrderSiteaccess::servedURL( $front, '/Fit-Healthy/recipes/x/', $ini ) );
        // the prefix itself is the front page
        $this->assertSame( $front, expOrderSiteaccess::servedURL( $front, 'fit-healthy', $ini ) );
    }

    public function testNodeOutsideThePathPrefixHasNoAddress()
    {
        $this->rules();
        $front = expOrderSiteaccess::frontURL( 'k2-shop' );
        $ini = $this->prefix( 'fit-healthy' );
        $this->assertFalse( expOrderSiteaccess::servedURL( $front, 'other-site/recipes/x', $ini ) );
        $this->assertFalse( expOrderSiteaccess::servedURL( $front, 'fit-healthy-old/x', $ini ) );
        $this->assertNull( expSubitemsURLColumn::servedPath( 'other-site/recipes/x', $ini ) );
        // the subitems column still shows such a path whole
        $this->assertSame( 'other-site/recipes/x', expSubitemsURLColumn::publicPath( new eZContentObjectTreeNode( array() ), $ini, 'other-site/recipes/x' ) );
    }

    public function testPathPrefixExcludeKeepsThePathWhole()
    {
        $this->rules();
        $front = expOrderSiteaccess::frontURL( 'k2-shop' );
        $ini = $this->prefix( 'fit-healthy', array( 'media' ) );
        $this->assertSame( $front . 'media/images/tea', expOrderSiteaccess::servedURL( $front, 'media/images/tea', $ini ) );
    }

    public function testWithoutPathPrefixTheWholeAliasIsServed()
    {
        $this->rules();
        $front = expOrderSiteaccess::frontURL( 'k2-shop' );
        $this->assertSame( $front . 'shop/tea', expOrderSiteaccess::servedURL( $front, 'shop/tea', $this->prefix( '' ) ) );
        $this->assertSame( $front . 'shop/tea', expOrderSiteaccess::servedURL( $front, 'shop/tea', null ) );
        $this->assertSame( $front, expOrderSiteaccess::servedURL( $front, '', null ) );
    }

    public function testPathIsEncodedSlashesKept()
    {
        $this->rules();
        $front = expOrderSiteaccess::frontURL( 'k2-shop' );
        $this->assertSame( $front . 'shop/gr%C3%BCner-tee', expOrderSiteaccess::servedURL( $front, 'shop/grüner-tee', null ) );
    }

    public function testOrderHasTheAttributes()
    {
        $definition = eZOrder::definitionWithAllColumns();
        $this->assertSame( 'siteaccessURL', $definition['function_attributes']['siteaccess_url'] );
        $this->assertSame( 'siteaccessProductURLs', $definition['function_attributes']['siteaccess_product_urls'] );
        $order = new eZOrder( array( 'siteaccess' => '' ) );
        $this->assertSame( '', $order->siteaccessURL() );
    }

    public static function designs()
    {
        return array( 'admin' => array( 'admin' ), 'admin4' => array( 'admin4' ) );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'designs' )]
    public function testOrderViewLinksTheSiteaccess( $design )
    {
        $root = dirname( __DIR__, 5 );
        $template = file_get_contents( "$root/design/$design/templates/shop/orderview.tpl" );
        $this->assertStringContainsString( '{if $order.siteaccess_url}<p class="exp-order-siteaccess-url">', $template );
        $this->assertStringContainsString( '$order.siteaccess_product_urls', $template );
        $this->assertStringContainsString( 'target="_blank" rel="noopener"', $template );
        // the admin link stays
        $this->assertStringContainsString( '<a href={$:node_url|ezurl}>', $template );

        // messages the catalogues already have, in the contexts the template asks for them
        $catalogue = file_get_contents( "$root/share/translations/untranslated/translation.ts" );
        $messages = array( array( 'design/admin/rss/edit_export', 'Site URL' ), array( 'design/admin/pagelayout', 'Open the site' ),
                           array( 'design/admin/node/view/full', 'View on site' ), array( 'design/admin/node/view/full', 'View on site (new window)' ) );
        foreach ( $messages as list( $context, $source ) )
        {
            $this->assertStringContainsString( "'$source'|i18n( '$context' )", $template );
            // the context's block cut out by position: a regex across the catalogue's long contexts runs into the PCRE limits
            $start = strpos( $catalogue, '<name>' . $context . '</name>' );
            $this->assertNotFalse( $start, "context $context" );
            $block = substr( $catalogue, $start, strpos( $catalogue, '</context>', $start ) - $start );
            $this->assertStringContainsString( '<source>' . htmlspecialchars( $source, ENT_NOQUOTES ) . '</source>', $block, "$context: $source" );
        }

        // the customer's view and the lists are left as they are
        foreach ( array( 'customerorderview.tpl', 'orderlist.tpl', 'archivelist.tpl' ) as $other )
        {
            if ( is_file( "$root/design/$design/templates/shop/$other" ) )
                $this->assertStringNotContainsString( 'siteaccess_url', file_get_contents( "$root/design/$design/templates/shop/$other" ) );
        }
    }
}
