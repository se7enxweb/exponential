<?php
/**
 * eZCurrencyConverter rounding: the rounding type from shop.ini [MathSettings] RoundingType (none, round, ceil,
 * floor) and the one set with setRoundingType(), applied by convert().
 *
 * roundingType() looked for constants named EZ_CURRENCY_CONVERTER_ROUNDING_TYPE_*, which the class does not have
 * (its constants are ROUNDING_TYPE_*), so every setting came out as none, and convert() compared the type with those
 * names as strings, so no type ever rounded: neither the setting nor setRoundingType( ROUNDING_TYPE_ROUND ), which
 * eZShopFunctions::convertAdditionalPrice() uses.
 *
 * No database: the currencies are stand-ins with a fixed rate, put into the converter's currency list, and the math
 * handler is eZPHPMath. The shop.ini settings are set by the test and put back.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group shop
 */

class eZCurrencyConverterRoundingTest extends PHPUnit\Framework\TestCase
{
    private $saved = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance( 'shop.ini' );
        foreach ( $this->saved as $name => $value )
        {
            if ( $value === null )
                $ini->removeSetting( 'MathSettings', $name );
            else
                $ini->setVariable( 'MathSettings', $name, $value[0] );
        }
        $this->saved = array();
    }

    private function setting( $name, $value )
    {
        $ini = eZINI::instance( 'shop.ini' );
        if ( !array_key_exists( $name, $this->saved ) )
            $this->saved[$name] = $ini->hasVariable( 'MathSettings', $name ) ? array( $ini->variable( 'MathSettings', $name ) ) : null;
        $ini->setVariable( 'MathSettings', $name, $value );
    }

    /**
     * A converter from AAA (rate 1) to BBB (rate $rate), with eZPHPMath and the given rounding precision and no
     * rounding target.
     */
    private function converter( $rate )
    {
        $converter = new eZCurrencyConverter();
        $converter->setMathHandler( eZPHPMath::create( 'ezphpmath', array( 'scale' => 10 ) ) );
        $converter->CurrencyList = array( 'AAA' => new eZCurrencyConverterRoundingTestCurrency( 1 ),
                                          'BBB' => new eZCurrencyConverterRoundingTestCurrency( $rate ) );
        $converter->setRoundingPrecision( 2 );
        $converter->setRoundingTarget( false );
        return $converter;
    }

    public static function settingProvider()
    {
        return array(
            'none'          => array( 'none', eZCurrencyConverter::ROUNDING_TYPE_NONE ),
            'round'         => array( 'round', eZCurrencyConverter::ROUNDING_TYPE_ROUND ),
            'ceil'          => array( 'ceil', eZCurrencyConverter::ROUNDING_TYPE_CEIL ),
            'floor'         => array( 'floor', eZCurrencyConverter::ROUNDING_TYPE_FLOOR ),
            'upper case'    => array( 'Floor', eZCurrencyConverter::ROUNDING_TYPE_FLOOR ),
            'unknown value' => array( 'sideways', eZCurrencyConverter::ROUNDING_TYPE_NONE ),
            'empty'         => array( '', eZCurrencyConverter::ROUNDING_TYPE_NONE ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('settingProvider')]
    public function testRoundingTypeFromTheSetting( $setting, $expected )
    {
        $this->setting( 'RoundingType', $setting );
        $converter = new eZCurrencyConverter();
        $this->assertSame( $expected, $converter->roundingType() );
    }

    public static function convertProvider()
    {
        // 10 AAA at a rate of 1.23456 are 12.3456 BBB, at 1.23412 they are 12.3412
        return array(
            'none'               => array( eZCurrencyConverter::ROUNDING_TYPE_NONE, 1.23456, 12.3456 ),
            'round up'           => array( eZCurrencyConverter::ROUNDING_TYPE_ROUND, 1.23456, 12.35 ),
            'round down'         => array( eZCurrencyConverter::ROUNDING_TYPE_ROUND, 1.23412, 12.34 ),
            'ceil'               => array( eZCurrencyConverter::ROUNDING_TYPE_CEIL, 1.23412, 12.35 ),
            'floor'              => array( eZCurrencyConverter::ROUNDING_TYPE_FLOOR, 1.23456, 12.34 ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('convertProvider')]
    public function testConvertAppliesTheRoundingType( $type, $rate, $expected )
    {
        $converter = $this->converter( $rate );
        $converter->setRoundingType( $type );
        $this->assertEqualsWithDelta( $expected, (float)$converter->convert( 'AAA', 'BBB', 10 ), 0.0000001 );
    }

    public static function settingConvertProvider()
    {
        return array(
            'round' => array( 'round', 12.35 ),
            'ceil'  => array( 'ceil', 12.35 ),
            'floor' => array( 'floor', 12.34 ),
            'none'  => array( 'none', 12.3456 ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('settingConvertProvider')]
    public function testConvertAppliesTheSetting( $setting, $expected )
    {
        $this->setting( 'RoundingType', $setting );
        $converter = $this->converter( 1.23456 );
        $this->assertEqualsWithDelta( $expected, (float)$converter->convert( 'AAA', 'BBB', 10 ), 0.0000001 );
    }

    public function testNoRoundingWhenNotApplied()
    {
        $converter = $this->converter( 1.23456 );
        $converter->setRoundingType( eZCurrencyConverter::ROUNDING_TYPE_ROUND );
        $this->assertEqualsWithDelta( 12.3456, (float)$converter->convert( 'AAA', 'BBB', 10, false ), 0.0000001 );
    }
}

/**
 * A currency with a fixed rate, in place of an eZCurrencyData row.
 */
class eZCurrencyConverterRoundingTestCurrency
{
    public $Rate;

    public function __construct( $rate )
    {
        $this->Rate = $rate;
    }

    public function rateValue()
    {
        return $this->Rate;
    }
}
