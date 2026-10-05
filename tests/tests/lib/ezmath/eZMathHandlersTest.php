<?php
/**
 * The math handlers of lib/ezmath that the currency converter of the shop uses (shop.ini [MathSettings]):
 *   - eZPHPMath: add, sub, mul, div, pow on floats, intval/fractval of a number's text, round/ceil/floor to a precision,
 *     with and without a target (the last decimals forced to given digits, as for prices ending in 9 or 99),
 *     positive and negative numbers, whole numbers, create() by the lower-case handler name the converter passes
 *   - eZBCMath: the same through the bcmath extension, with its scale and its trimmed string results
 *
 * Plain PHP, no kernel bootstrap. The eZBCMath cases are skipped when the bcmath extension is missing.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezmath
 */

class eZMathHandlersTest extends PHPUnit\Framework\TestCase
{
    public static function setUpBeforeClass(): void
    {
        $root = dirname( __DIR__, 4 );
        require_once $root . '/lib/ezmath/classes/mathhandlers/ezphpmath.php';
        require_once $root . '/lib/ezmath/classes/mathhandlers/ezbcmath.php';
    }

    private function bc( $scale = 6 )
    {
        if ( !function_exists( 'bcadd' ) )
            $this->markTestSkipped( 'the bcmath extension is not loaded' );
        return new eZBCMath( array( 'scale' => $scale ) );
    }

    public function testPhpArithmetic()
    {
        $m = new eZPHPMath();
        $this->assertEqualsWithDelta( 0.3, $m->add( '0.1', '0.2' ), 1e-12 );
        $this->assertSame( 0.75, $m->sub( 1, '0.25' ) );
        $this->assertSame( 3.0, $m->mul( '1.5', 2 ) );
        $this->assertSame( 0.25, $m->div( 1, 4 ) );
        $this->assertSame( 1024, $m->pow( 2, 10 ) );
        $this->assertSame( 0.5, $m->pow( 2, -1 ) );
    }

    public function testBcArithmeticKeepsTheScale()
    {
        $m = $this->bc();
        $this->assertSame( 6, $m->scale() );
        $this->assertSame( '0.300000', $m->add( '0.1', '0.2' ) );
        $this->assertSame( '0.750000', $m->sub( 1, '0.25' ) );
        $this->assertSame( '3.000000', $m->mul( '1.5', 2 ) );
        $this->assertSame( '0.333333', $m->div( 1, 3 ) );
        $this->assertSame( '1024.000000', $m->pow( 2, 10 ) );
        $m->setScale( 2 );
        $this->assertSame( '0.33', $m->div( 1, 3 ) );
    }

    public function testBcDefaultScale()
    {
        if ( !function_exists( 'bcadd' ) )
            $this->markTestSkipped( 'the bcmath extension is not loaded' );
        $this->assertSame( eZBCMath::DEFAULT_SCALE, ( new eZBCMath() )->scale() );
        $this->assertSame( eZBCMath::DEFAULT_SCALE, ( new eZBCMath( array( 'scale' => 'many' ) ) )->scale() );
    }

    public static function partsProvider()
    {
        return array(
            'decimal'         => array( '12.75', false, '12', '75' ),
            'decimal, 1 digit' => array( '12.7543', 1, '12', '7' ),
            'whole number'    => array( '12', false, '12', 0 ),
            'negative'        => array( '-3.5', false, '-3', '5' ),
            'leading point'   => array( '.5', false, '', '5' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('partsProvider')]
    public function testIntvalAndFractvalSplitTheText( $number, $precision, $int, $fract )
    {
        $m = new eZPHPMath();
        $this->assertSame( $int, $m->intval( $number ) );
        $this->assertSame( $fract, $m->fractval( $number, $precision ) );
    }

    public function testMagnitude()
    {
        $m = new eZPHPMath();
        $this->assertSame( '1.5', $m->magnitude( -1.5 ) );
        $this->assertSame( '1.5', $m->magnitude( '1.5' ) );
        $this->assertSame( '7', $m->magnitude( -7 ) );
    }

    /**
     * Cases: value, precision, target, round, ceil, floor (compared as numbers).
     */
    public static function roundingProvider()
    {
        return array(
            'ceil up'                 => array( 1.21, 1, false, 1.2, 1.3, 1.2 ),
            'exact at the precision'  => array( 1.2, 1, false, 1.2, 1.2, 1.2 ),
            'half'                    => array( 1.25, 1, false, 1.3, 1.3, 1.2 ),
            'fewer decimals'          => array( 1.5, 3, false, 1.5, 1.5, 1.5 ),
            'whole number'            => array( 3, 2, false, 3, 3, 3 ),
            'whole number, precision 0' => array( 3, 0, false, 3, 3, 3 ),
            'precision 0'             => array( 2.4, 0, false, 2, 3, 2 ),
            'small value'             => array( 0.005, 2, false, 0.01, 0.01, 0 ),
            'carry into the integer'  => array( 1.99, 1, false, 2, 2, 1.9 ),
            'negative'                => array( -1.21, 1, false, -1.2, -1.2, -1.3 ),
            'negative half'           => array( -1.25, 1, false, -1.3, -1.2, -1.3 ),
            'negative whole'          => array( -4, 1, false, -4, -4, -4 ),
            'target 9'                => array( 1.234, 2, '9', 1.29, 1.29, 1.29 ),
            'target 5'                => array( 1.234, 2, '5', 1.25, 1.25, 1.25 ),
            'target 99'               => array( 12.3456, 2, '99', 12.99, 12.99, 12.99 ),
            'target longer than precision' => array( 12.3456, 2, '995', 12.99, 12.99, 12.99 ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('roundingProvider')]
    public function testPhpRounding( $value, $precision, $target, $round, $ceil, $floor )
    {
        $m = new eZPHPMath();
        $this->assertEqualsWithDelta( $round, (float)$m->round( $value, $precision, $target ), 1e-9, 'round' );
        $this->assertEqualsWithDelta( $ceil, (float)$m->ceil( $value, $precision, $target ), 1e-9, 'ceil' );
        $this->assertEqualsWithDelta( $floor, (float)$m->floor( $value, $precision, $target ), 1e-9, 'floor' );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('roundingProvider')]
    public function testBcRounding( $value, $precision, $target, $round, $ceil, $floor )
    {
        $m = $this->bc();
        $this->assertEqualsWithDelta( $round, (float)$m->round( $value, $precision, $target ), 1e-9, 'round' );
        $this->assertEqualsWithDelta( $ceil, (float)$m->ceil( $value, $precision, $target ), 1e-9, 'ceil' );
        $this->assertEqualsWithDelta( $floor, (float)$m->floor( $value, $precision, $target ), 1e-9, 'floor' );
    }

    public function testBcResultsHaveNoTrailingZeros()
    {
        $m = $this->bc();
        $this->assertSame( '1.3', $m->ceil( 1.21, 1, false ) );
        $this->assertSame( '1', $m->floor( 1.0, 2, false ) );
        $this->assertSame( '-1.3', $m->floor( -1.21, 1, false ) );
        $this->assertSame( '1.2', $m->round( '1.24', 1, false ) );
    }

    /**
     * At scale 0 bcmath gives whole numbers without a decimal point; their zeros are digits, not padding.
     */
    public function testBcScaleZeroKeepsTheZerosOfWholeNumbers()
    {
        $m = $this->bc( 0 );
        $this->assertSame( '10', $m->ceil( 10, 0, false ) );
        $this->assertSame( '100', $m->floor( 100, 0, false ) );
        $this->assertSame( '20', $m->trimZeros( '20' ) );
        $this->assertSame( '2', $m->trimZeros( '2.000' ) );
        $this->assertSame( '2.5', $m->trimZeros( '2.500' ) );
    }

    public function testCreateByTheLowerCaseNameTheConverterPasses()
    {
        $root = dirname( __DIR__, 4 );
        $cwd = getcwd();
        chdir( $root );
        try
        {
            $this->assertInstanceOf( eZPHPMath::class, eZPHPMath::create( 'ezphpmath' ) );
            if ( function_exists( 'bcadd' ) )
            {
                $bc = eZPHPMath::create( 'ezbcmath', array( 'scale' => 3 ) );
                $this->assertInstanceOf( eZBCMath::class, $bc );
                $this->assertSame( 3, $bc->scale() );
            }
            $this->assertFalse( eZPHPMath::create( 'nosuchmathhandler' ) );
        }
        finally
        {
            chdir( $cwd );
        }
    }
}
