<?php
/**
 * The shop's pure logic: eZSimplePrice (prices with and without VAT, discounts, unknown dynamic VAT), eZVatType
 * (the dynamic type, percentages), eZVATManager (country requirement, VAT handler loading), eZCurrencyData (codes,
 * statuses, rate values), eZCurrencyConverter (cross rates between currencies), eZShopFunctions (product datatypes),
 * eZOrderStatus (status groups), the exchange rate handlers' settings and eZPaymentGateway's short description.
 *
 * No database: VAT types, currencies, attributes and orders are built in memory; shop.ini settings are set by the
 * test and put back.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group shop
 */

class eZShopPricesTestOrder
{
    public $items = array();

    public function productItems()
    {
        return $this->items;
    }
}

class eZShopPricesTestUnknownVat extends eZVatType
{
    function getPercentage( $object, $country )
    {
        return -1;
    }
}

class eZShopPricesTestLogger
{
    public $lines = array();

    public function writeTimedString( $line )
    {
        $this->lines[] = $line;
    }
}

class eZShopPricesTest extends PHPUnit\Framework\TestCase
{
    private $saved = array();
    private $globals = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        foreach ( array( 'eZVATManager_isDynamicVatChargingEnabled', 'eZVatType_dynamicVatTypeName' ) as $name )
            $this->globals[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
    }

    protected function tearDown(): void
    {
        foreach ( array_reverse( $this->saved ) as $entry )
        {
            list( $file, $group, $name, $value ) = $entry;
            $ini = eZINI::instance( $file );
            if ( $value === null )
                $ini->removeSetting( $group, $name );
            else
                $ini->setVariable( $group, $name, $value[0] );
        }
        $this->saved = array();
        foreach ( $this->globals as $name => $value )
        {
            if ( $value === null )
                unset( $GLOBALS[$name] );
            else
                $GLOBALS[$name] = $value[0];
        }
    }

    private function setting( $file, $group, $name, $value )
    {
        $ini = eZINI::instance( $file );
        $this->saved[] = array( $file, $group, $name, $ini->hasVariable( $group, $name ) ? array( $ini->variable( $group, $name ) ) : null );
        if ( $value === null )
            $ini->removeSetting( $group, $name );
        else
            $ini->setVariable( $group, $name, $value );
    }

    private static function price( $value, $vatPercent, $vatIncluded, $discount = 0.0, $vatID = 1 )
    {
        $price = new eZSimplePrice( null, null, $value );
        $price->VATType = new eZVatType( array( 'id' => $vatID, 'name' => 'k1 VAT', 'percentage' => $vatPercent ) );
        $price->setVATIncluded( $vatIncluded );
        $price->setDiscountPercent( $discount );
        return $price;
    }

    // ---------------------------------------------------------------- eZSimplePrice

    public function testPriceWithoutVat()
    {
        $price = self::price( 100.0, 25.0, false );
        $this->assertEqualsWithDelta( 100.0, $price->exVATPrice(), 1e-9 );
        $this->assertEqualsWithDelta( 125.0, $price->incVATPrice(), 1e-9 );
        $this->assertSame( 25.0, $price->VATPercent() );
        $this->assertFalse( $price->hasDiscount() );
        $this->assertEqualsWithDelta( 125.0, $price->discountIncVATPrice(), 1e-9 );
    }

    public function testPriceIncludingVat()
    {
        $price = self::price( 125.0, 25.0, true );
        $this->assertEqualsWithDelta( 125.0, $price->incVATPrice(), 1e-9 );
        $this->assertEqualsWithDelta( 100.0, $price->exVATPrice(), 1e-9 );
        $this->assertTrue( $price->attribute( 'is_vat_included' ) );
    }

    public function testDiscountAppliesToBothPrices()
    {
        $price = self::price( 200.0, 10.0, false, 15.0 );
        $this->assertTrue( $price->hasDiscount() );
        $this->assertSame( 15.0, $price->discount() );
        $this->assertEqualsWithDelta( 170.0, $price->discountExVATPrice(), 1e-9 );
        $this->assertEqualsWithDelta( 187.0, $price->discountIncVATPrice(), 1e-9 );
        $this->assertEqualsWithDelta( 187.0, $price->attribute( 'discount_price_inc_vat' ), 1e-9 );
        $this->assertEqualsWithDelta( 170.0, $price->attribute( 'discount_price_ex_vat' ), 1e-9 );
        $this->assertSame( 15.0, $price->attribute( 'discount_percent' ) );
    }

    public function testUnknownDynamicVatCountsAsZero()
    {
        $price = self::price( 80.0, 0.0, false, 0.0, -1 );
        $price->ContentObject = 'not an object';
        $vatType = new eZShopPricesTestUnknownVat( array( 'id' => -1, 'percentage' => 0 ) );
        $price->VATType = $vatType;
        $this->assertSame( -1, $price->VATPercent() );
        $this->assertEqualsWithDelta( 80.0, $price->incVATPrice(), 1e-9 );
        $price->setVATIncluded( true );
        $this->assertEqualsWithDelta( 80.0, $price->exVATPrice(), 1e-9 );
    }

    public function testPriceAttributes()
    {
        $price = self::price( 10.0, 20.0, false );
        $this->assertTrue( $price->hasAttribute( 'inc_vat_price' ) );
        $this->assertFalse( $price->hasAttribute( 'k1' ) );
        $this->assertSame( 10.0, $price->attribute( 'price' ) );
        $this->assertEqualsWithDelta( 12.0, $price->attribute( 'inc_vat_price' ), 1e-9 );
        $this->assertEqualsWithDelta( 10.0, $price->attribute( 'ex_vat_price' ), 1e-9 );
        $this->assertSame( 20.0, $price->attribute( 'vat_percent' ) );
        $this->assertFalse( $price->attribute( 'has_discount' ) );
        $this->assertSame( $price->VATType, $price->attribute( 'selected_vat_type' ) );
        $this->assertSame( eZLocale::instance()->currencyShortName(), $price->attribute( 'currency' ) );
        $this->assertNull( $price->attribute( 'k1_unknown' ) );
        $price->setAttribute( 'is_vat_included', '1' );
        $this->assertTrue( $price->VATIncluded() );
        $price->setAttribute( 'is_vat_included', '0' );
        $this->assertFalse( $price->VATIncluded() );
        $price->setAttribute( 'k1_unknown', 1 );
        $this->assertSame( 10.0, $price->price() );
    }

    public function testNewPriceDefaults()
    {
        $price = new eZSimplePrice( null, null );
        $this->assertSame( 0.0, $price->price() );
        $this->assertSame( 0.0, $price->discountPercent() );
        $this->assertFalse( $price->VATIncluded() );
        $price = new eZSimplePrice( null, null, '19.90' );
        $this->assertSame( '19.90', $price->price() );
    }

    // ---------------------------------------------------------------- eZVatType

    public function testDynamicVatType()
    {
        $type = eZVatType::dynamicVatType();
        $this->assertInstanceOf( 'eZVatType', $type );
        $this->assertTrue( $type->isDynamic() );
        $this->assertTrue( $type->attribute( 'is_dynamic' ) );
        $row = eZVatType::dynamicVatType( false );
        $this->assertSame( -1, $row['id'] );
        $this->assertSame( 0.0, $row['percentage'] );
        $this->assertSame( eZVatType::dynamicVatTypeName(), $row['name'] );
    }

    public function testFixedVatType()
    {
        $type = new eZVatType( array( 'id' => 3, 'name' => 'Reduced', 'percentage' => 7.0 ) );
        $this->assertFalse( $type->isDynamic() );
        $this->assertSame( 7.0, $type->getPercentage( false, 'DE' ) );
        $new = eZVatType::create();
        $this->assertNull( $new->attribute( 'id' ) );
        $this->assertSame( 0.0, $new->attribute( 'percentage' ) );
        $this->assertNotSame( '', $new->attribute( 'name' ) );
    }

    public function testDynamicVatTypeNameIsReadOnce()
    {
        unset( $GLOBALS['eZVatType_dynamicVatTypeName'] );
        $this->setting( 'shop.ini', 'VATSettings', 'DynamicVatTypeName', 'k1 by country' );
        $this->assertSame( 'k1 by country', eZVatType::dynamicVatTypeName() );
        $this->setting( 'shop.ini', 'VATSettings', 'DynamicVatTypeName', 'changed' );
        $this->assertSame( 'k1 by country', eZVatType::dynamicVatTypeName() );
    }

    public function testDynamicVatPercentageWithoutHandlerIsUnknown()
    {
        $this->setting( 'shop.ini', 'VATSettings', 'Handler', null );
        $this->assertSame( -1, eZVatType::dynamicVatType()->getPercentage( false, 'DE' ) );
    }

    // ---------------------------------------------------------------- eZVATManager

    public function testUserCountryRequirement()
    {
        $this->setting( 'shop.ini', 'VATSettings', 'RequireUserCountry', 'false' );
        $this->assertFalse( eZVATManager::isUserCountryRequired() );
        $this->setting( 'shop.ini', 'VATSettings', 'RequireUserCountry', 'true' );
        $this->assertTrue( eZVATManager::isUserCountryRequired() );
        $this->setting( 'shop.ini', 'VATSettings', 'RequireUserCountry', null );
        $this->assertTrue( eZVATManager::isUserCountryRequired() );
    }

    public function testVatHandlerNotConfiguredOrMissing()
    {
        $this->setting( 'shop.ini', 'VATSettings', 'Handler', null );
        $this->assertTrue( eZVATManager::loadVATHandler() );
        $this->assertNull( eZVATManager::getVAT( false, 'DE' ) );
        unset( $GLOBALS['eZVATManager_isDynamicVatChargingEnabled'] );
        $this->assertFalse( eZVATManager::isDynamicVatChargingEnabled() );

        $this->setting( 'shop.ini', 'VATSettings', 'Handler', 'k1nosuch' );
        $this->setting( 'shop.ini', 'VATSettings', 'ExtensionDirectories', array( 'k1-no-such-extension' ) );
        $this->assertFalse( eZVATManager::loadVATHandler() );
        $this->assertNull( eZVATManager::getVAT( false, 'DE' ) );
    }

    // ---------------------------------------------------------------- eZCurrencyData

    public static function codeProvider()
    {
        return array( array( 'EUR', 0 ), array( 'USD', 0 ), array( 'eur', 2 ), array( 'EU', 2 ), array( 'EURO', 2 ), array( 'E1R', 2 ), array( '', 2 ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('codeProvider')]
    public function testCurrencyCodeValidation( $code, $expected )
    {
        $this->assertSame( $expected, eZCurrencyData::validateCurrencyCode( $code ) );
    }

    public function testCurrencyStatus()
    {
        $this->assertSame( '1', eZCurrencyData::statusStringToNumeric( 'active' ) );
        $this->assertSame( '2', eZCurrencyData::statusStringToNumeric( 'Inactive' ) );
        $this->assertSame( '1', eZCurrencyData::statusStringToNumeric( '1' ) );
        $this->assertSame( 2, eZCurrencyData::statusStringToNumeric( 2 ) );
        $this->assertFalse( eZCurrencyData::statusStringToNumeric( 'paused' ) );
        $currency = new eZCurrencyData( array( 'code' => 'EUR', 'status' => 1 ) );
        $this->assertTrue( $currency->isActive() );
        $currency->setStatus( 'inactive' );
        $this->assertSame( '2', $currency->attribute( 'status' ) );
        $this->assertFalse( $currency->isActive() );
        $currency->setStatus( 'paused' );
        $this->assertSame( '2', $currency->attribute( 'status' ), 'an unknown status changes nothing' );
    }

    public static function rateProvider()
    {
        return array(
            'custom wins' => array( '1.5', '2.0', '1.0000', '2.00000' ),
            'auto with factor' => array( '0.8', '0.0000', '1.1', '0.88000' ),
            'none' => array( '0.0000', '0.0000', '1.0000', '0.00000' ),
            'custom with factor' => array( '9', '2', '0.5', '1.00000' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('rateProvider')]
    public function testRateValue( $auto, $custom, $factor, $expected )
    {
        $currency = new eZCurrencyData( array( 'code' => 'K1A', 'auto_rate_value' => $auto, 'custom_rate_value' => $custom, 'rate_factor' => $factor ) );
        $this->assertSame( $expected, $currency->rateValue() );
        $this->assertSame( $expected, $currency->attribute( 'rate_value' ) );
    }

    public function testRateValueIsKeptUntilInvalidated()
    {
        $currency = new eZCurrencyData( array( 'code' => 'K1A', 'auto_rate_value' => '2', 'custom_rate_value' => '0', 'rate_factor' => '1' ) );
        $this->assertSame( '2.00000', $currency->rateValue() );
        $currency->setAttribute( 'auto_rate_value', '3' );
        $this->assertSame( '2.00000', $currency->rateValue() );
        $currency->invalidateRateValue();
        $this->assertSame( '3.00000', $currency->rateValue() );
    }

    public function testCurrencyErrorMessages()
    {
        $messages = array();
        foreach ( array( eZCurrencyData::ERROR_INVALID_CURRENCY_CODE, eZCurrencyData::ERROR_CURRENCY_EXISTS, eZCurrencyData::ERROR_UNKNOWN, 99 ) as $code )
            $messages[] = eZCurrencyData::errorMessage( $code );
        $this->assertSame( 'Invalid characters in currency code.', $messages[0] );
        $this->assertSame( 'Currency already exists.', $messages[1] );
        $this->assertSame( 'Unknown error.', $messages[2] );
        $this->assertSame( 'Unknown error.', $messages[3] );
    }

    public function testCreateRefusesAnInvalidCode()
    {
        $this->assertSame( eZCurrencyData::ERROR_INVALID_CURRENCY_CODE, eZCurrencyData::create( 'E1', '€', 'ger-DE', '1', '0', '1' ) );
        $this->assertSame( eZCurrencyData::ERROR_INVALID_CURRENCY_CODE, eZCurrencyData::canCreate( 'euro' ) );
    }

    // ---------------------------------------------------------------- eZCurrencyConverter

    private static function converter( array $rates )
    {
        $converter = new eZCurrencyConverter();
        $converter->setMathHandler( eZPHPMath::create( 'ezphpmath', array( 'scale' => 10 ) ) );
        $converter->setRoundingType( eZCurrencyConverter::ROUNDING_TYPE_NONE );
        $list = array();
        foreach ( $rates as $code => $rate )
            $list[$code] = new eZCurrencyData( array( 'code' => $code, 'auto_rate_value' => '0', 'custom_rate_value' => (string)$rate, 'rate_factor' => '1' ) );
        $converter->CurrencyList = $list;
        return $converter;
    }

    public function testCrossRate()
    {
        $converter = self::converter( array( 'EUR' => 1, 'USD' => 1.25, 'NOK' => 10 ) );
        $this->assertEqualsWithDelta( 1.25, (float)$converter->crossRate( 'EUR', 'USD' ), 1e-9 );
        $this->assertEqualsWithDelta( 8.0, (float)$converter->crossRate( 'USD', 'NOK' ), 1e-9 );
        $this->assertEqualsWithDelta( 0.1, (float)$converter->crossRate( 'NOK', 'EUR' ), 1e-9 );
        $this->assertEqualsWithDelta( 25.0, (float)$converter->convert( 'USD', 'NOK', 3.125 ), 1e-9 );
    }

    public function testConvertLeavesTheValueAloneWhenNothingToConvert()
    {
        $converter = self::converter( array( 'EUR' => 1, 'USD' => 1.25 ) );
        $this->assertSame( 12.5, $converter->convert( 'EUR', 'EUR', 12.5 ) );
        $this->assertSame( 12.5, $converter->convert( false, 'USD', 12.5 ) );
        $this->assertSame( 12.5, $converter->convert( 'EUR', false, 12.5 ) );
        $this->assertSame( 0, $converter->convert( 'EUR', 'USD', 0 ) );
    }

    public function testCurrencyWithoutRateGivesZero()
    {
        $converter = self::converter( array( 'EUR' => 0, 'USD' => 1.25 ) );
        $this->assertSame( 0, $converter->crossRate( 'EUR', 'USD' ) );
        $this->assertEqualsWithDelta( 0.0, (float)$converter->convert( 'EUR', 'USD', 10 ), 1e-9 );
    }

    public function testConverterSettingsAreKept()
    {
        $converter = new eZCurrencyConverter();
        $converter->setRoundingPrecision( 3 );
        $converter->setRoundingTarget( '0.05' );
        $this->assertSame( 3, $converter->roundingPrecision() );
        $this->assertSame( '0.05', $converter->roundingTarget() );
        $this->assertSame( eZCurrencyConverter::instance(), eZCurrencyConverter::instance() );
    }

    // ---------------------------------------------------------------- eZShopFunctions

    public function testProductDatatypes()
    {
        $this->setting( 'site.ini', 'ShopSettings', 'ProductDatatypeStringList', null );
        $this->assertSame( array( 'ezprice', 'ezmultiprice' ), eZShopFunctions::productDatatypeStringList() );
        $this->assertTrue( eZShopFunctions::isProductDatatype( 'ezprice' ) );
        $this->assertFalse( eZShopFunctions::isProductDatatype( 'ezstring' ) );
        $this->setting( 'site.ini', 'ShopSettings', 'ProductDatatypeStringList', array( 'ezprice', 'k1price' ) );
        $this->assertTrue( eZShopFunctions::isProductDatatype( 'k1price' ) );
        $this->assertFalse( eZShopFunctions::isProductDatatype( 'ezmultiprice' ) );
    }

    public function testProductTypes()
    {
        $this->assertTrue( eZShopFunctions::isSimplePriceProductType( 'ezprice' ) );
        $this->assertFalse( eZShopFunctions::isSimplePriceProductType( 'ezmultiprice' ) );
        $this->assertTrue( eZShopFunctions::isMultiPriceProductType( 'ezmultiprice' ) );
        $this->assertFalse( eZShopFunctions::isMultiPriceProductType( false ) );
        $this->assertFalse( eZShopFunctions::productTypeByClass( null ) );
        $this->assertFalse( eZShopFunctions::productTypeByObject( 'not an object' ) );
        $this->assertFalse( eZShopFunctions::isProductClass( null ) );
        $this->assertFalse( eZShopFunctions::priceAttribute( null ) );
        $this->assertSame( '', eZShopFunctions::priceAttributeIdentifier( null ) );
        $this->assertSame( 12.5, eZShopFunctions::convertAdditionalPrice( false, 12.5 ) );
    }

    // ---------------------------------------------------------------- eZOrderStatus

    public function testOrderStatusGroups()
    {
        $this->assertSame( array( 3, 12, 13, 16, 17 ), eZOrderStatus::finishedStatusIDs() );
        $this->assertSame( array( 6, 13, 17 ), eZOrderStatus::noRevenueStatusIDs() );
        $this->assertSame( array( 1, 4, 6, 11 ), eZOrderStatus::waitingForCustomerStatusIDs() );
        $this->assertSame( array(), array_intersect( eZOrderStatus::waitingForCustomerStatusIDs(), eZOrderStatus::finishedStatusIDs() ) );
        $this->assertTrue( ( new eZOrderStatus( array( 'status_id' => eZOrderStatus::SHIPPED ) ) )->isInternal() );
        $this->assertFalse( ( new eZOrderStatus( array( 'status_id' => eZOrderStatus::CUSTOM ) ) )->attribute( 'is_internal' ) );
    }

    // ---------------------------------------------------------------- exchange rate handlers

    public function testBaseExchangeRateHandler()
    {
        $handler = new eZExchangeRatesUpdateHandler();
        $this->assertSame( '', $handler->baseCurrency() );
        $this->assertFalse( $handler->rateList() );
        $handler->setRateList( array( 'USD' => '1.1' ) );
        $handler->initialize( array( 'BaseCurrency' => 'NOK' ) );
        $this->assertSame( array( 'USD' => '1.1' ), $handler->rateList() );
        $this->assertSame( 'NOK', $handler->baseCurrency() );
        $error = $handler->requestRates();
        $this->assertSame( eZExchangeRatesUpdateHandler::FAILED, $error['code'] );
    }

    public function testEcbHandlerSettings()
    {
        $this->setting( 'shop.ini', 'ECBExchangeRatesSettings', 'ServerName', 'ecb.k1.example.invalid' );
        $this->setting( 'shop.ini', 'ECBExchangeRatesSettings', 'ServerPort', '8080' );
        $this->setting( 'shop.ini', 'ECBExchangeRatesSettings', 'RatesURI', '/rates.xml' );
        $handler = eZExchangeRatesUpdateHandler::create( 'eZECB' );
        $this->assertInstanceOf( 'eZECBHandler', $handler );
        $this->assertSame( 'ecb.k1.example.invalid', $handler->serverName() );
        $this->assertSame( '8080', $handler->serverPort() );
        $this->assertSame( '/rates.xml', $handler->ratesURI() );
        $this->assertSame( 'EUR', $handler->baseCurrency() );
        $handler->initialize( array( 'ServerName' => 'a', 'ServerPort' => 1, 'RatesURI' => '/b', 'BaseCurrency' => 'USD' ) );
        $this->assertSame( array( 'a', 1, '/b', 'USD' ), array( $handler->serverName(), $handler->serverPort(), $handler->ratesURI(), $handler->baseCurrency() ) );
    }

    public function testUnknownExchangeRateHandler()
    {
        $this->assertFalse( eZExchangeRatesUpdateHandler::create( 'k1nosuch' ) );
    }

    // ---------------------------------------------------------------- payment gateway

    public function testShortDescription()
    {
        $gateway = new eZPaymentGateway();
        $gateway->logger = new eZShopPricesTestLogger();
        $order = new eZShopPricesTestOrder();
        $order->items = array( array( 'object_name' => 'Tea' ), array( 'object_name' => 'Cup' ) );
        $this->assertSame( 'Tea,Cup', $gateway->createShortDescription( $order, 0 ) );
        $this->assertSame( 'Tea,Cup', $gateway->createShortDescription( $order, 7 ) );
        $this->assertSame( 'Tea...', $gateway->createShortDescription( $order, 6 ) );
        $order->items = array();
        $this->assertSame( '', $gateway->createShortDescription( $order, 5 ) );
        $this->assertContains( 'descText=Tea...', $gateway->logger->lines );
    }
}
