<?php
/**
 * The VAT and currency services. Test VAT types, rules, categories and a test currency (code XTS) are created and
 * removed again in each test; the kernel audits the changes.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expCommerceTestCase.php';

class expVatCurrencyServicesTest extends expCommerceTestCase
{
    protected $typeIds = array();
    protected $ruleIds = array();
    protected $categoryIds = array();
    protected $currencyCodes = array();

    public function tearDown(): void
    {
        if ( self::$bootError === null )
        {
            $this->loginAdmin();
            foreach ( $this->ruleIds as $id )
                if ( $rule = eZVatRule::fetch( $id ) )
                {
                    $rule->removeProductCategories( $id );
                    eZPersistentObject::removeObject( eZVatRule::definition(), array( 'id' => $id ) );
                }
            foreach ( $this->typeIds as $id )
                if ( $t = eZVatType::fetch( $id ) )
                    $t->removeThis();
            foreach ( $this->categoryIds as $id )
                if ( eZProductCategory::fetch( $id ) )
                    eZProductCategory::removeByID( $id );
            foreach ( $this->currencyCodes as $code )
                if ( eZCurrencyData::fetch( $code ) )
                    eZShopFunctions::removeCurrency( array( $code ) );
        }
        parent::tearDown();
    }

    protected function newType( $name = 'Test VAT', $pct = '19' )
    {
        $t = $this->ok( 'expVatServices', 'createType', array(), array( 'name' => $name, 'percentage' => $pct ) );
        $this->typeIds[] = $t['id'];
        return $t;
    }

    // ---------------------------------------------------------------- VAT

    public function testTypesAreListedAndViewed()
    {
        $types = $this->ok( 'expVatServices', 'types' );
        $this->assertNotEmpty( $types );
        $one = $this->ok( 'expVatServices', 'type', array( $types[0]['id'] ) );
        $this->assertSame( $types[0]['name'], $one['name'] );
        $this->fails( 404, 'expVatServices', 'type', array( 99999999 ) );
    }

    public function testCreateUpdateAndRemoveAType()
    {
        $t = $this->newType( 'Test VAT', '19' );
        $this->assertEqualsWithDelta( 19.0, $t['percentage'], 0.001 );
        $u = $this->ok( 'expVatServices', 'updateType', array(), array( 'id' => $t['id'], 'percentage' => '7,5' ) );
        $this->assertEqualsWithDelta( 7.5, $u['percentage'], 0.001 );
        $u = $this->ok( 'expVatServices', 'updateType', array(), array( 'id' => $t['id'], 'name' => 'Renamed' ) );
        $this->assertSame( 'Renamed', $u['name'] );
        $usage = $this->ok( 'expVatServices', 'typeUsage', array( $t['id'] ) );
        $this->assertSame( 0, $usage['rules'] );
        $this->assertSame( array( 'removed' => $t['id'] ), $this->ok( 'expVatServices', 'removeType', array(), array( 'id' => $t['id'] ) ) );
        $this->fails( 404, 'expVatServices', 'type', array( $t['id'] ) );
    }

    public function testTypeInputIsValidated()
    {
        $this->fails( 422, 'expVatServices', 'createType', array(), array( 'name' => ' ', 'percentage' => '5' ) );
        $this->fails( 422, 'expVatServices', 'createType', array(), array( 'name' => 'X', 'percentage' => '101' ) );
        $this->fails( 422, 'expVatServices', 'createType', array(), array( 'name' => 'X', 'percentage' => 'abc' ) );
        $this->fails( 400, 'expVatServices', 'createType', array(), array( 'name' => 'X' ) );
        $t = $this->newType();
        $this->fails( 400, 'expVatServices', 'updateType', array(), array( 'id' => $t['id'] ) );
    }

    public function testRulesCreateUpdateRemove()
    {
        $t = $this->newType( 'Rule VAT', '10' );
        $c = $this->ok( 'expVatServices', 'createCategory', array(), array( 'name' => 'Test category' ) );
        $this->categoryIds[] = $c['id'];
        $r = $this->ok( 'expVatServices', 'createRule', array(), array( 'country_code' => 'DE', 'vat_type' => $t['id'], 'categories' => (string)$c['id'] ) );
        $this->ruleIds[] = $r['id'];
        $this->assertSame( 'DE', $r['country_code'] );
        $this->assertSame( array( $c['id'] ), $r['product_category_ids'] );
        $this->assertSame( 1, $this->ok( 'expVatServices', 'typeUsage', array( $t['id'] ) )['rules'] );
        $u = $this->ok( 'expVatServices', 'updateRule', array(), array( 'id' => $r['id'], 'country_code' => 'FR' ) );
        $this->assertSame( 'FR', $u['country_code'] );
        $this->assertSame( $r['id'], $this->ok( 'expVatServices', 'rule', array( $r['id'] ) )['id'] );
        $this->assertContains( 'FR', $this->ok( 'expVatServices', 'countries' )['with_rules'] );
        $this->ok( 'expVatServices', 'removeRule', array(), array( 'id' => $r['id'] ) );
        $this->fails( 404, 'expVatServices', 'rule', array( $r['id'] ) );
    }

    public function testRuleInputIsValidated()
    {
        $t = $this->newType();
        $this->fails( 422, 'expVatServices', 'createRule', array(), array( 'country_code' => 'germany', 'vat_type' => $t['id'] ) );
        $this->fails( 404, 'expVatServices', 'createRule', array(), array( 'country_code' => 'DE', 'vat_type' => 99999999 ) );
        $this->fails( 422, 'expVatServices', 'createRule', array(), array( 'country_code' => 'DE', 'vat_type' => $t['id'], 'categories' => '99999999' ) );
    }

    public function testCategoriesAreListedAndRemoved()
    {
        $c = $this->ok( 'expVatServices', 'createCategory', array(), array( 'name' => 'Gone soon' ) );
        $this->categoryIds[] = $c['id'];
        $this->assertContains( $c['id'], array_column( $this->ok( 'expVatServices', 'categories' ), 'id' ) );
        $this->ok( 'expVatServices', 'removeCategory', array(), array( 'id' => $c['id'] ) );
        $this->assertNotContains( $c['id'], array_column( $this->ok( 'expVatServices', 'categories' ), 'id' ) );
        $this->fails( 404, 'expVatServices', 'removeCategory', array(), array( 'id' => $c['id'] ) );
    }

    public function testVatOfAProductAndTheSettings()
    {
        $p = $this->createProduct( '10' );
        $v = $this->ok( 'expVatServices', 'forProduct', array( $p ) );
        $this->assertArrayHasKey( 'percent', $v );
        $s = $this->ok( 'expVatServices', 'settings' );
        $this->assertArrayHasKey( 'dynamic', $s );
        $this->assertArrayHasKey( 'country', $this->ok( 'expVatServices', 'userCountry' ) );
        $this->fails( 422, 'expVatServices', 'setUserCountry', array(), array( 'country' => 'x' ) );
    }

    // ---------------------------------------------------------------- currency

    public function testCurrenciesAreListedAndCounted()
    {
        $list = $this->ok( 'expCurrencyServices', 'list' );
        $this->assertSame( count( $list ), $this->ok( 'expCurrencyServices', 'count' )['count'] );
        $this->assertIsArray( $this->ok( 'expCurrencyServices', 'codes' ) );
        $this->assertArrayHasKey( 'base', $this->ok( 'expCurrencyServices', 'baseCurrency' ) );
        $this->assertArrayHasKey( 'type', $this->ok( 'expCurrencyServices', 'rounding' ) );
    }

    public function testExistsChecksTheCode()
    {
        $r = $this->ok( 'expCurrencyServices', 'exists', array( 'xts' ) );
        $this->assertTrue( $r['valid'] );
        $this->assertFalse( $r['exists'] );
        $this->assertFalse( $this->ok( 'expCurrencyServices', 'exists', array( 'XX1' ) )['valid'] );
    }

    public function testCreateUpdateStatusAndRemoveACurrency()
    {
        $this->currencyCodes[] = 'XTS';
        $c = $this->ok( 'expCurrencyServices', 'create', array(), array( 'code' => 'xts', 'symbol' => 'T$', 'locale' => 'eng-US', 'custom_rate' => '2.5' ) );
        $this->assertSame( 'XTS', $c['code'] );
        $this->assertSame( 'active', $c['status'] );
        $this->assertEqualsWithDelta( 2.5, $c['custom_rate'], 0.0001 );
        $this->fails( 409, 'expCurrencyServices', 'create', array(), array( 'code' => 'XTS', 'symbol' => 'T$' ) );
        $u = $this->ok( 'expCurrencyServices', 'update', array(), array( 'code' => 'XTS', 'symbol' => 'TT', 'rate_factor' => '2' ) );
        $this->assertSame( 'TT', $u['symbol'] );
        $this->assertEqualsWithDelta( 5.0, $u['rate'], 0.0001 );
        $s = $this->ok( 'expCurrencyServices', 'setStatus', array(), array( 'code' => 'XTS', 'status' => 'inactive' ) );
        $this->assertSame( 'inactive', $s['status'] );
        $this->assertNotContains( 'XTS', $this->ok( 'expCurrencyServices', 'codes' ) );
        $this->assertSame( array( 'removed' => 'XTS' ), $this->ok( 'expCurrencyServices', 'remove', array(), array( 'code' => 'XTS' ) ) );
        $this->fails( 404, 'expCurrencyServices', 'view', array( 'XTS' ) );
    }

    public function testConvertBetweenTwoTestCurrencies()
    {
        $this->currencyCodes[] = 'XTS';
        $this->currencyCodes[] = 'XTT';
        $this->ok( 'expCurrencyServices', 'create', array(), array( 'code' => 'XTS', 'symbol' => 'S', 'custom_rate' => '2' ) );
        $this->ok( 'expCurrencyServices', 'create', array(), array( 'code' => 'XTT', 'symbol' => 'T', 'custom_rate' => '4' ) );
        $r = $this->ok( 'expCurrencyServices', 'convert', array( '10', 'XTS', 'XTT' ) );
        $this->assertGreaterThan( 0, $r['converted'] );
        $back = $this->ok( 'expCurrencyServices', 'convert', array( (string)$r['converted'], 'XTT', 'XTS' ) );
        $this->assertEqualsWithDelta( 10.0, $back['converted'], 0.1 );
        $same = $this->ok( 'expCurrencyServices', 'convert', array( '7', 'XTS', 'XTS' ) );
        $this->assertEqualsWithDelta( 7.0, $same['converted'], 0.0001 );
        $cross = $this->ok( 'expCurrencyServices', 'crossRate', array( 'XTS', 'XTT' ) );
        $this->assertEqualsWithDelta( $r['converted'] / 10, $cross['rate'], 0.01 );
    }

    public function testCurrencyInputIsValidated()
    {
        $this->fails( 400, 'expCurrencyServices', 'view', array( 'EURO' ) );
        $this->fails( 404, 'expCurrencyServices', 'view', array( 'QQQ' ) );
        $this->fails( 400, 'expCurrencyServices', 'create', array(), array( 'code' => 'ABCD', 'symbol' => 'x' ) );
        $this->fails( 422, 'expCurrencyServices', 'create', array(), array( 'code' => 'XTS', 'symbol' => ' ' ) );
        $this->fails( 400, 'expCurrencyServices', 'convert', array( 'abc', 'USD', 'EUR' ) );
        $this->currencyCodes[] = 'XTS';
        $this->ok( 'expCurrencyServices', 'create', array(), array( 'code' => 'XTS', 'symbol' => 'T' ) );
        $this->fails( 400, 'expCurrencyServices', 'update', array(), array( 'code' => 'XTS' ) );
    }

    public function testPreferredCurrency()
    {
        $p = $this->ok( 'expCurrencyServices', 'preferred' );
        $this->assertArrayHasKey( 'code', $p );
        $this->fails( 422, 'expCurrencyServices', 'setPreferred', array(), array( 'code' => 'QQQ' ) );
    }

    public function testAnonymousCannotChangeCurrenciesOrVat()
    {
        $this->loginAnonymous();
        $this->assertTrue( $this->call( 'expCurrencyServices', 'list' )['ok'] );
        $this->fails( 401, 'expCurrencyServices', 'create', array(), array( 'code' => 'XTS', 'symbol' => 'T' ) );
        $this->fails( 401, 'expVatServices', 'createType', array(), array( 'name' => 'x', 'percentage' => '1' ) );
        $this->fails( 401, 'expVatServices', 'rules' );
    }
}
