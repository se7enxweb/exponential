<?php
/**
 * The product services against the live database: reads on a product created in a test folder under the Media
 * root, which tearDown removes.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expCommerceTestCase.php';

class expProductServicesTest extends expCommerceTestCase
{
    public function testClassesListsTheProductClass()
    {
        $list = $this->ok( 'expProductServices', 'classes' );
        $ids = array_column( $list, 'identifier' );
        $this->assertContains( 'product', $ids );
        $this->assertSame( 'ezprice', $list[array_search( 'product', $ids )]['type'] );
    }

    public function testListOfTheTestFolderHoldsTheProduct()
    {
        $p = $this->createProduct( '12.5', 'Zeta widget' );
        $folder = $this->testFolder();
        $r = $this->call( 'expProductServices', 'list', array( $folder ) );
        $this->assertTrue( $r['ok'] );
        $this->assertCount( 1, $r['data'] );
        $this->assertSame( $p, $r['data'][0]['node_id'] );
        $this->assertSame( 1, $r['meta']['total'] );
        $this->assertEqualsWithDelta( 12.5, $r['data'][0]['price']['inc_vat'], 0.001 );
    }

    public function testListPagesAndSorts()
    {
        $this->createProduct( '30', 'B product' );
        $this->createProduct( '10', 'A product' );
        $this->createProduct( '20', 'C product' );
        $f = $this->testFolder();
        $byName = $this->ok( 'expProductServices', 'list', array( $f, 2, 0, '', 'name' ) );
        $this->assertSame( array( 'A product', 'B product' ), array_column( $byName, 'name' ) );
        $page2 = $this->call( 'expProductServices', 'list', array( $f, 2, 2 ) );
        $this->assertCount( 1, $page2['data'] );
        $this->assertFalse( $page2['meta']['has_more'] );
        $byPrice = $this->ok( 'expProductServices', 'list', array( $f, 10, 0, '', 'price' ) );
        $this->assertSame( array( 10.0, 20.0, 30.0 ), array_map( function ( $i ) { return $i['price']['inc_vat']; }, $byPrice ) );
    }

    public function testCountBelowANode()
    {
        $this->createProduct();
        $this->createProduct();
        $this->assertSame( 2, $this->ok( 'expProductServices', 'count', array( $this->testFolder() ) )['count'] );
    }

    public function testSearchFindsByNameAndNumber()
    {
        $this->createProduct( '5', 'Quuxinator 3000' );
        $hits = $this->ok( 'expProductServices', 'search', array( 'quuxinator' ) );
        $this->assertCount( 1, $hits );
        $this->assertSame( 'Quuxinator 3000', $hits[0]['name'] );
        $this->assertSame( array(), $this->ok( 'expProductServices', 'search', array( 'no-such-product-xyz' ) ) );
        $this->fails( 400, 'expProductServices', 'search', array( '  ' ) );
    }

    public function testViewReturnsFieldsPriceAndOptions()
    {
        $p = $this->createProduct( '19.9', 'Viewed' );
        $v = $this->ok( 'expProductServices', 'view', array( $p ) );
        $this->assertSame( 'Viewed', $v['name'] );
        $this->assertSame( 'Viewed', $v['fields']['name'] );
        $this->assertStringStartsWith( 'T', $v['number'] );
        $this->assertArrayHasKey( 'options', $v );
    }

    public function testViewByObjectMatchesViewByNode()
    {
        $p = $this->createProduct();
        $v = $this->ok( 'expProductServices', 'view', array( $p ) );
        $o = $this->ok( 'expProductServices', 'viewByObject', array( $v['object_id'] ) );
        $this->assertSame( $v['node_id'], $o['node_id'] );
    }

    public function testViewOfANonProductIs404()
    {
        $this->fails( 404, 'expProductServices', 'view', array( 43 ) );
        $this->fails( 404, 'expProductServices', 'view', array( 99999999 ) );
        $this->fails( 400, 'expProductServices', 'view', array( 'abc' ) );
    }

    public function testIsProduct()
    {
        $p = $this->createProduct();
        $this->assertTrue( $this->ok( 'expProductServices', 'isProduct', array( $p ) )['is_product'] );
        $this->assertFalse( $this->ok( 'expProductServices', 'isProduct', array( 43 ) )['is_product'] );
    }

    public function testPriceAndPricesAndVat()
    {
        $p = $this->createProduct( '40' );
        $price = $this->ok( 'expProductServices', 'price', array( $p ) );
        $this->assertEqualsWithDelta( 40.0, $price['price'], 0.001 );
        $this->assertNotEmpty( $price['currency'] );
        $prices = $this->ok( 'expProductServices', 'prices', array( $p ) );
        $this->assertTrue( $prices[0]['base'] );
        $vat = $this->ok( 'expProductServices', 'vat', array( $p ) );
        $this->assertArrayHasKey( 'percent', $vat );
    }

    public function testCurrencyArgumentMustBeAThreeLetterCode()
    {
        $p = $this->createProduct();
        $this->fails( 400, 'expProductServices', 'price', array( $p, 'EURO' ) );
        $this->fails( 400, 'expProductServices', 'list', array( $this->testFolder(), 5, 0, '12' ) );
    }

    public function testOptionsVariationsAndOptionPriceOfAProductWithoutOptions()
    {
        $p = $this->createProduct( '8' );
        $this->assertIsArray( $this->ok( 'expProductServices', 'options', array( $p ) ) );
        $v = $this->call( 'expProductServices', 'variations', array( $p ) );
        $this->assertTrue( $v['ok'] );
        $this->assertEqualsWithDelta( 8.0, $v['data'][0]['price'], 0.001 );
        $op = $this->ok( 'expProductServices', 'optionPrice', array( $p, '{}' ) );
        $this->assertEqualsWithDelta( 8.0, $op['total'], 0.001 );
        $this->fails( 400, 'expProductServices', 'optionPrice', array( $p, '{not json' ) );
    }

    public function testLatestCategoriesAndPriceRange()
    {
        $this->createProduct( '10' );
        $this->createProduct( '30' );
        $latest = $this->ok( 'expProductServices', 'latest', array( 500 ) );
        $this->assertGreaterThanOrEqual( 2, count( $latest ) );
        $cats = $this->ok( 'expProductServices', 'categories', array( 43 ) );
        $this->assertContains( $this->testFolder(), array_column( $cats, 'node_id' ) );
        $range = $this->ok( 'expProductServices', 'priceRange', array( $this->testFolder() ) );
        $this->assertSame( 2, $range['count'] );
        $this->assertEqualsWithDelta( 10.0, $range['min'], 0.001 );
        $this->assertEqualsWithDelta( 30.0, $range['max'], 0.001 );
        $this->assertEqualsWithDelta( 20.0, $range['avg'], 0.001 );
    }

    public function testByNumberAndRelated()
    {
        $a = $this->createProduct();
        $b = $this->createProduct();
        $v = $this->ok( 'expProductServices', 'view', array( $a ) );
        $found = $this->ok( 'expProductServices', 'byNumber', array( $v['number'] ) );
        $this->assertSame( $a, $found['node_id'] );
        $rel = $this->ok( 'expProductServices', 'related', array( $a ) );
        $this->assertContains( $b, array_column( $rel, 'node_id' ) );
        $this->assertNotContains( $a, array_column( $rel, 'node_id' ) );
        $this->fails( 404, 'expProductServices', 'byNumber', array( 'NO-SUCH-NUMBER' ) );
    }

    public function testSetPriceChangesThePriceAndNeedsAValidNumber()
    {
        $p = $this->createProduct( '10' );
        $r = $this->ok( 'expProductServices', 'setPrice', array( $p ), array( 'price' => '24.5' ) );
        $this->assertEqualsWithDelta( 24.5, $r['price']['price'], 0.001 );
        $this->fails( 422, 'expProductServices', 'setPrice', array( $p ), array( 'price' => 'abc' ) );
        $this->fails( 422, 'expProductServices', 'setPrice', array( $p ), array( 'price' => '-1' ) );
        $this->fails( 400, 'expProductServices', 'setPrice', array( $p ), array() );
    }

    public function testAnonymousMayReadProductsButNotSetPrices()
    {
        $p = $this->createProduct();
        $this->loginAnonymous();
        $r = $this->call( 'expProductServices', 'classes' );
        $this->assertTrue( $r['ok'] );
        $this->fails( 401, 'expProductServices', 'setPrice', array( $p ), array( 'price' => '1' ) );
    }
}
