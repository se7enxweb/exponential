<?php
/**
 * shop/myorders without the database: the view is defined with its own policy function (shop/myorders, not
 * shop/administrate and not omitted from the policy check), takes its offset as a view parameter, the page size
 * and offset are bounded, the order fetchers filter on the user and leave temporary orders out, and the template
 * exists, has no inline CSS and links each order where the shop links orders.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 * @group shop
 */

#[\PHPUnit\Framework\Attributes\Group('kernel')]
#[\PHPUnit\Framework\Attributes\Group('shop')]
class eZShopMyOrdersViewTest extends PHPUnit\Framework\TestCase
{
    private $root;
    private $saved = null;

    protected function setUp(): void
    {
        $this->root = dirname( __DIR__, 5 );
        chdir( $this->root );
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance( 'shop.ini' );
        if ( $this->saved !== null )
        {
            if ( $this->saved === false )
                $ini->removeSetting( 'MyOrdersSettings', 'Limit' );
            else
                $ini->setVariable( 'MyOrdersSettings', 'Limit', $this->saved );
            $this->saved = null;
        }
    }

    private function module()
    {
        $Module = $ViewList = $FunctionList = null;
        include $this->root . '/kernel/shop/module.php';
        return array( $ViewList, $FunctionList );
    }

    public function testViewHasItsOwnPolicyFunction()
    {
        list( $views, $functions ) = $this->module();
        $this->assertArrayHasKey( 'myorders', $views );
        $this->assertSame( array( 'myorders' ), $views['myorders']['functions'] );
        $this->assertSame( 'myorders.php', $views['myorders']['script'] );
        $this->assertSame( array( 'offset' => 'Offset' ), $views['myorders']['unordered_params'] );
        $this->assertArrayHasKey( 'myorders', $functions );
        $this->assertFileExists( $this->root . '/kernel/shop/myorders.php' );
    }

    public function testViewIsNotOmittedFromThePolicyCheck()
    {
        $site = (string)file_get_contents( $this->root . '/settings/site.ini' );
        $this->assertStringNotContainsString( 'PolicyOmitList[]=shop/myorders', $site );
    }

    public function testPageSizeIsBounded()
    {
        $ini = eZINI::instance( 'shop.ini' );
        $this->saved = $ini->hasVariable( 'MyOrdersSettings', 'Limit' ) ? $ini->variable( 'MyOrdersSettings', 'Limit' ) : false;
        $ini->setVariable( 'MyOrdersSettings', 'Limit', '0' );
        $this->assertSame( \Exponential\View\Kernel\Shop\Myorders::LIMIT, \Exponential\View\Kernel\Shop\Myorders::limit() );
        $ini->setVariable( 'MyOrdersSettings', 'Limit', '500' );
        $this->assertSame( 100, \Exponential\View\Kernel\Shop\Myorders::limit() );
        $ini->setVariable( 'MyOrdersSettings', 'Limit', '15' );
        $this->assertSame( 15, \Exponential\View\Kernel\Shop\Myorders::limit() );
    }

    public function testOffsetIsANonNegativeWholeNumber()
    {
        $this->assertSame( 0, \Exponential\View\Kernel\Shop\Myorders::offset( null ) );
        $this->assertSame( 0, \Exponential\View\Kernel\Shop\Myorders::offset( '-20' ) );
        $this->assertSame( 0, \Exponential\View\Kernel\Shop\Myorders::offset( 'abc' ) );
        $this->assertSame( 40, \Exponential\View\Kernel\Shop\Myorders::offset( '40' ) );
    }

    public function testFetchersFilterOnTheUserAndLeaveTemporaryOrdersOut()
    {
        $source = (string)file_get_contents( $this->root . '/kernel/classes/ezorder.php' );
        foreach ( array( 'fetchListByUserID', 'countByUserID' ) as $method )
        {
            $this->assertTrue( method_exists( 'eZOrder', $method ), $method );
            $this->assertMatchesRegularExpression( '/function ' . $method . '\b.*?array\( \'user_id\' => \(int\)\$userID, \'is_temporary\' => 0 \)/s', $source, $method );
        }
    }

    public function testTemplateLinksOrdersWithoutInlineCSS()
    {
        $file = $this->root . '/design/standard/templates/shop/myorders.tpl';
        $this->assertFileExists( $file );
        $template = preg_replace( '/\{\*.*?\*\}/s', '', (string)file_get_contents( $file ) );
        $this->assertStringNotContainsString( '<style', $template );
        $this->assertDoesNotMatchRegularExpression( '/\sstyle\s*=/i', $template );
        $this->assertStringContainsString( '$order.link_url|ezurl', $template );
        $this->assertStringContainsString( "page_uri='/shop/myorders'", $template );
    }
}
