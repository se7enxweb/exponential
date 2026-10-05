<?php
/**
 * Operators that gave a different result compiled than interpreted, rendered four ways.
 *
 * Every case is rendered by the processing interpreter and by the template compiler, both with its input written as
 * a literal (so the compiler folds the operator at compile time) and with the same input passed in as a template
 * variable (so the compiled template evaluates it at run time). All four outputs must equal the expected text.
 *
 *   - eZTemplateArithmeticOperator sum, sub, mul and div: the interpreter and the compile time folding cut every
 *     operand to an integer, the run time code of a compiled template did not, so sum(1.5,2.25) was 3 or 3.75
 *     depending on how the template ran. All of them now calculate with the numbers as given.
 *
 * No kernel bootstrap, no database and no siteaccess: a bare eZTemplate with the lib/eztemplate autoloads. Template
 * files and compiled templates go to a private directory under var/tmp that tearDownAfterClass() removes.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group eztemplate
 */

class eZTemplateCompiledInterpretedParityTest extends PHPUnit\Framework\TestCase
{
    /** @var string directory of this class's template files and compiled templates */
    private static $dir;

    private $savedGlobals = array();

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        self::$dir = 'var/tmp/phpunit-eztemplate-parity-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( self::$dir . '/compiled', 0777, true );
    }

    public static function tearDownAfterClass(): void
    {
        self::removeTree( self::$dir );
    }

    protected function setUp(): void
    {
        foreach ( array( 'eZTemplateCompilerSettings', 'eZSiteBasics' ) as $name )
            $this->savedGlobals[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
    }

    protected function tearDown(): void
    {
        foreach ( $this->savedGlobals as $name => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$name] );
            else
                $GLOBALS[$name] = $saved[0];
        }
    }

    private static function removeTree( $path )
    {
        if ( !$path || !file_exists( $path ) )
            return;
        if ( is_dir( $path ) && !is_link( $path ) )
        {
            foreach ( scandir( $path ) as $entry )
            {
                if ( $entry !== '.' && $entry !== '..' )
                    self::removeTree( $path . '/' . $entry );
            }
            rmdir( $path );
        }
        else
            unlink( $path );
    }

    /**
     * Renders $source with $variables, compiled or interpreted, and returns array( output, errors, warnings ).
     */
    private function render( $source, $variables, $compiled )
    {
        if ( $compiled )
        {
            unset( $GLOBALS['eZSiteBasics'] );
            $GLOBALS['eZTemplateCompilerSettings']['compile'] = true;
            $GLOBALS['eZTemplateCompilerSettings']['compilation-directory'] = self::$dir . '/compiled';
        }
        else
        {
            $GLOBALS['eZTemplateCompilerSettings']['compile'] = false;
            $GLOBALS['eZSiteBasics']['no-cache-adviced'] = true;
        }

        $file = self::$dir . '/' . md5( $source ) . '.tpl';
        if ( !file_exists( $file ) )
            file_put_contents( $file, $source );

        $tpl = new eZTemplate();
        $tpl->autoload();
        foreach ( $variables as $name => $value )
            $tpl->setVariable( $name, $value );
        $output = $tpl->fetch( $file );
        return array( $output, $tpl->errorLog(), $tpl->warningLog() );
    }

    /**
     * Cases: name => array( template with literal input, template with variable input, variables, expected ).
     */
    public static function caseTable()
    {
        return array(
            // arithmetic keeps fractions
            'sum of floats'             => array( '{sum(1.5,2.25)}', '{sum($a,$b)}', array( 'a' => 1.5, 'b' => 2.25 ), '3.75' ),
            'sum piped float'           => array( '{1.5|sum(2)}', '{$a|sum($b)}', array( 'a' => 1.5, 'b' => 2 ), '3.5' ),
            'sum of float strings'      => array( '{sum("0.5","0.25")}', '{sum($a,$b)}', array( 'a' => '0.5', 'b' => '0.25' ), '0.75' ),
            'sum, literal and variable' => array( '{sum(1,2.5,0.5)}', '{sum(1,$a,0.5)}', array( 'a' => 2.5 ), '4' ),
            'sum of a word'             => array( '{sum("abc",2)}', '{sum($a,$b)}', array( 'a' => 'abc', 'b' => 2 ), '2' ),
            'sum of integers'           => array( '{sum(1,2,3)}', '{sum($a,$b,$c)}', array( 'a' => 1, 'b' => 2, 'c' => 3 ), '6' ),
            'sub of floats'             => array( '{sub(5,1.5)}', '{sub($a,$b)}', array( 'a' => 5, 'b' => 1.5 ), '3.5' ),
            'sub piped float'           => array( '{2.5|sub(1)}', '{$a|sub($b)}', array( 'a' => 2.5, 'b' => 1 ), '1.5' ),
            'sub, variable second'      => array( '{sub(10,2.5,0.5)}', '{sub(10,$a,0.5)}', array( 'a' => 2.5 ), '7' ),
            'mul of floats'             => array( '{mul(1.5,3)}', '{mul($a,$b)}', array( 'a' => 1.5, 'b' => 3 ), '4.5' ),
            'mul, variable second'      => array( '{mul(2,1.25,2)}', '{mul(2,$a,2)}', array( 'a' => 1.25 ), '5' ),
            'div of floats'             => array( '{div(7.5,2)}', '{div($a,$b)}', array( 'a' => 7.5, 'b' => 2 ), '3.75' ),
            'div by a fraction'         => array( '{div(1,0.5)}', '{div($a,$b)}', array( 'a' => 1, 'b' => 0.5 ), '2' ),
            'div, variable first'       => array( '{div(9,3,2)}', '{div($a,3,2)}', array( 'a' => 9 ), '1.5' ),
            'div by zero'               => array( '{div(4,0)}', '{div($a,$b)}', array( 'a' => 4, 'b' => 0 ), '0' ),
        );
    }

    public static function renderProvider()
    {
        $rows = array();
        foreach ( self::caseTable() as $name => $case )
        {
            list( $literal, $withVariables, $variables, $expected ) = $case;
            foreach ( array( 'interpreted' => false, 'compiled' => true ) as $mode => $compiled )
            {
                $rows["$name, literal, $mode"] = array( $literal, array(), $compiled, $expected );
                $rows["$name, variables, $mode"] = array( $withVariables, $variables, $compiled, $expected );
            }
        }
        return $rows;
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('renderProvider')]
    public function testOutput( $source, $variables, $compiled, $expected )
    {
        list( $output, $errors ) = $this->render( $source, $variables, $compiled );
        $this->assertSame( array(), $errors, 'template errors' );
        $this->assertSame( $expected, $output );
    }
}
