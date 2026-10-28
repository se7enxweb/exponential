<?php
/**
 * The inference of bin/php/expbackfillordersiteaccess.php (expOrderSiteaccessBackfill::infer()): an order whose
 * products all lie in the tree of exactly one siteaccess gets that siteaccess; none or several leave it unknown, and
 * so does a product no longer in the tree. With "prefer the deepest root" a shop's own siteaccess beats an
 * administration that serves the whole tree, but two siteaccesses with the same root stay ambiguous. Fed with path
 * strings and siteaccess roots; no database.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/shop/expOrderSiteaccessBackfillTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 * @group shop
 */

use PHPUnit\Framework\Attributes\DataProvider;

class expOrderSiteaccessBackfillTest extends PHPUnit\Framework\TestCase
{
    /** two shops below the content root, an administration and an editor on the whole tree, a site on a shop's tree */
    const ROOTS = array( 'shop_de' => '/1/2/100/', 'shop_fr' => '/1/2/200/', 'admin' => '/1/2/', 'editor' => '/1/2/',
                         'shop_de_mobile' => '/1/2/100/' );

    public static function cases()
    {
        return array(
            'one shop only' => array( array( '/1/2/200/210/' ), array( 'shop_fr' => '/1/2/200/', 'shop_de' => '/1/2/100/' ), false, 'shop_fr', 'the only siteaccess' ),
            'all items in one shop' => array( array( '/1/2/200/210/', '/1/2/200/220/221/' ), array( 'shop_fr' => '/1/2/200/', 'shop_de' => '/1/2/100/' ), false, 'shop_fr', 'the only siteaccess' ),
            'items in two shops' => array( array( '/1/2/100/110/', '/1/2/200/210/' ), array( 'shop_fr' => '/1/2/200/', 'shop_de' => '/1/2/100/' ), false, '', 'no siteaccess serves every product' ),
            'outside every tree' => array( array( '/1/43/50/' ), array( 'shop_fr' => '/1/2/200/' ), false, '', 'lies in no siteaccess' ),
            'removed product' => array( array( '/1/2/200/210/', null ), array( 'shop_fr' => '/1/2/200/' ), false, '', 'no longer in the content tree' ),
            'no items' => array( array(), array( 'shop_fr' => '/1/2/200/' ), false, '', 'no product items' ),
            'admin serves everything' => array( array( '/1/2/200/210/' ), self::ROOTS, false, '', 'several siteaccesses serve its products: admin, editor, shop_fr' ),
            'deepest root wins' => array( array( '/1/2/200/210/' ), self::ROOTS, true, 'shop_fr', 'the deepest root' ),
            'same deepest root twice' => array( array( '/1/2/100/110/' ), self::ROOTS, true, '', 'several siteaccesses with the same root serve its products: shop_de, shop_de_mobile' ),
            'only the whole-tree ones' => array( array( '/1/2/300/' ), self::ROOTS, true, '', 'same root serve its products: admin, editor' ),
            'root node itself' => array( array( '/1/2/200/' ), array( 'shop_fr' => '/1/2/200/' ), false, 'shop_fr', 'the only siteaccess' ),
            'prefix is not a sibling' => array( array( '/1/2/2000/' ), array( 'shop_fr' => '/1/2/200/' ), false, '', 'lies in no siteaccess' ),
        );
    }

    #[DataProvider( 'cases' )]
    public function testInfer( array $paths, array $roots, $preferDeepest, $siteaccess, $reason )
    {
        $result = expOrderSiteaccessBackfill::infer( $paths, $roots, $preferDeepest );
        $this->assertSame( $siteaccess, $result['siteaccess'] );
        $this->assertStringContainsString( $reason, $result['reason'] );
    }

    public function testPathsAreNormalized()
    {
        $this->assertSame( '/1/2/61/', expOrderSiteaccessBackfill::normalizePath( '/1/2/61' ) );
        $this->assertSame( '/1/2/61/', expOrderSiteaccessBackfill::normalizePath( '1/2/61/' ) );
        $this->assertSame( '', expOrderSiteaccessBackfill::normalizePath( '' ) );
        $this->assertSame( '', expOrderSiteaccessBackfill::normalizePath( null ) );
        $this->assertSame( '', expOrderSiteaccessBackfill::normalizePath( '/1/x/' ) );
        // a root given without the trailing slash still serves only its own subtree
        $this->assertSame( array( 'shop_fr' => 3 ), expOrderSiteaccessBackfill::siteaccessesServing( '/1/2/200/5/', array( 'shop_fr' => '/1/2/200', 'other' => '/1/2/20' ) ) );
    }

    public function testTheScriptNeverOverwritesAndDefaultsToADryRun()
    {
        $source = file_get_contents( dirname( __DIR__, 5 ) . '/kernel/private/classes/commands/expbackfillordersiteaccess.php' );
        $this->assertStringContainsString( '"[apply][prefer-deepest-root]"', $source );
        $this->assertMatchesRegularExpression( "/if \( \\\$order->siteaccessName\(\) !== '' \)\s*\{\s*\\\$counts\['kept'\]\+\+;\s*continue;/", $source );
        $this->assertMatchesRegularExpression( '/if \( \$apply \)\s*\{\s*\$db->begin\(\);/', $source );
    }
}
