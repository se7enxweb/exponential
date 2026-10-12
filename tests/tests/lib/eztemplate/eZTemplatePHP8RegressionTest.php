<?php
/**
 * PHP 8 regression cases of the template engine, from the PHP 7.2-8.3 fixes LoveStack (mugoweb/ezpublish-legacy)
 * collected between 2017 and 2025. Each case renders a template that reached the code a LoveStack fix changed and
 * checks that it renders without an error, a TypeError or a PHP notice, interpreted and compiled.
 *
 * LoveStack commits reproduced here: e828c54709 (get_class() of a non-object), 1bd250d4c6 (string / int division
 * in navigator/google.tpl), 4065ed16b3 (htmlspecialchars() on null), a5f16bd9f7, 4917e57c92 and f627bcf278
 * (string interpolation in the compiled foreach and loop code), 82a7684d6f (eZTemplateOptimizer on a
 * non-countable value), bd4fa8600d (eZTemplateNodeTool::isPHPVariableElement() on null).
 *
 * No kernel bootstrap, no database: a bare eZTemplate, as eZTemplateEngineRenderTest uses.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group eztemplate
 * @group php8
 */

class eZTemplatePHP8RegressionTest extends PHPUnit\Framework\TestCase
{
    /** @var string private directory of this class's templates and compiled templates */
    private static $dir;

    private $savedGlobals = array();

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        self::$dir = 'var/tmp/phpunit-eztemplate-php8-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
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
     * Renders $source interpreted or compiled; returns array( output, PHP notices, thrown error or '' ).
     */
    private function render( $source, array $variables, $compiled )
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

        $notices = array();
        set_error_handler( function ( $no, $str, $f, $l ) use ( &$notices )
        {
            $notices[] = $str . ' (' . basename( $f ) . ':' . $l . ')';
            return true;
        } );
        $output = null;
        $thrown = '';
        try
        {
            $tpl = new eZTemplate();
            $tpl->autoload();
            foreach ( $variables as $name => $value )
                $tpl->setVariable( $name, $value );
            $output = $tpl->fetch( $file );
        }
        catch ( Throwable $e )
        {
            $thrown = get_class( $e ) . ': ' . $e->getMessage();
        }
        restore_error_handler();
        return array( $output, $notices, $thrown );
    }

    private function assertRendersCleanly( $source, array $variables, $expected )
    {
        foreach ( array( false, true ) as $compiled )
        {
            $mode = $compiled ? 'compiled' : 'interpreted';
            list( $output, $notices, $thrown ) = $this->render( $source, $variables, $compiled );
            $this->assertSame( '', $thrown, "$mode: $source threw" );
            $this->assertSame( array(), $notices, "$mode: $source raised PHP notices" );
            $this->assertSame( $expected, $output, "$mode: $source" );
        }
    }

    /** e828c54709: get_class() of a non-object throws a TypeError on PHP 8; the operator gives '' instead */
    public function testGetClassOfANonObjectIsEmpty()
    {
        $this->assertRendersCleanly( '[{$a|get_class}]', array( 'a' => 'text' ), '[]' );
        $this->assertRendersCleanly( '[{get_class($a)}]', array( 'a' => 42 ), '[]' );
        $this->assertRendersCleanly( '[{$a|get_class}]', array( 'a' => new ArrayObject() ), '[arrayobject]' );
    }

    /** e828c54709: is_class() of a non-object is false, not a TypeError */
    public function testIsClassOfANonObjectIsFalse()
    {
        $this->assertRendersCleanly( '{if $a|is_class("ArrayObject")}y{else}n{/if}', array( 'a' => 'text' ), 'n' );
        $this->assertRendersCleanly( '{if is_class("ArrayObject",$a)}y{else}n{/if}', array( 'a' => null ), 'n' );
        $this->assertRendersCleanly( '{if $a|is_class("ArrayObject")}y{else}n{/if}', array( 'a' => new ArrayObject() ), 'y' );
    }

    /**
     * 1bd250d4c6: navigator/google.tpl divides $item_count by $item_limit and the offset by $item_limit; view
     * parameters and template variables arrive as strings, an empty or non-numeric one included.
     */
    public function testNavigatorPageArithmeticOnStrings()
    {
        $source = '{int( ceil( div( $item_count, $item_limit ) ) )}/'
                . '{int( ceil( div( first_set( $view_parameters.offset, 0 ), $item_limit ) ) )}';
        $this->assertRendersCleanly( $source, array( 'item_count' => '25', 'item_limit' => '10',
                                                     'view_parameters' => array( 'offset' => '20' ) ), '3/2' );
        $this->assertRendersCleanly( $source, array( 'item_count' => '25', 'item_limit' => 10,
                                                     'view_parameters' => array( 'offset' => '' ) ), '3/0' );
        $this->assertRendersCleanly( $source, array( 'item_count' => '', 'item_limit' => '10',
                                                     'view_parameters' => array( 'offset' => 'abc' ) ), '0/0' );
    }

    /** 4065ed16b3: wash of null (an unset attribute) is '' without the PHP 8.1 null deprecation */
    public function testWashOfNull()
    {
        $this->assertRendersCleanly( '[{$a|wash}]', array( 'a' => null ), '[]' );
        $this->assertRendersCleanly( '[{$a|wash(xhtml)}]', array( 'a' => null ), '[]' );
        $this->assertRendersCleanly( '[{$a|wash(email)}]', array( 'a' => null ), '[]' );
        $this->assertRendersCleanly( '[{$a|wash}]', array( 'a' => '<b>' ), '[&lt;b&gt;]' );
    }

    /** a5f16bd9f7, 4917e57c92, f627bcf278: the compiled foreach and section/loop code */
    public function testCompiledLoopsWithSequences()
    {
        $this->assertRendersCleanly( '{foreach $l as $k => $v}{$k}{$v}{/foreach}', array( 'l' => array( 'a' => 1, 'b' => 2 ) ), 'a1b2' );
        $this->assertRendersCleanly( '{section var=v loop=$l sequence=array("x","y")}{$v}{$v.sequence}{/section}',
                                     array( 'l' => array( 1, 2, 3 ) ), '1x2y3x' );
        $this->assertRendersCleanly( '{foreach $l as $v sequence array("x","y") as $s}{$v}{$s}{/foreach}',
                                     array( 'l' => array( 1, 2, 3 ) ), '1x2y3x' );
    }

    /**
     * a5f16bd9f7, 4917e57c92, f627bcf278: "${name}" interpolation is deprecated from PHP 8.2; no template engine
     * class (nor eZURLAliasML, which the same LoveStack series changed) may use it, in its own code or in the
     * code it writes for compiled templates.
     */
    public function testNoDollarBraceInterpolationInTemplateEngineSources()
    {
        $root = dirname( __DIR__, 4 );
        $files = glob( $root . '/lib/eztemplate/classes/*.php' );
        $files[] = $root . '/kernel/classes/ezurlaliasml.php';
        $files[] = $root . '/kernel/common/ezobjectforwarder.php';
        $found = array();
        foreach ( $files as $file )
        {
            foreach ( token_get_all( file_get_contents( $file ) ) as $token )
            {
                if ( is_array( $token ) && $token[0] === T_DOLLAR_OPEN_CURLY_BRACES )
                    $found[] = basename( $file ) . ':' . $token[2];
            }
        }
        $this->assertSame( array(), $found, 'deprecated "${...}" string interpolation' );
    }

    /** 82a7684d6f: the data_map optimisation of eZTemplateOptimizer meets a non-countable value */
    public function testOptimizerOnANonCountableValue()
    {
        $notices = array();
        set_error_handler( function ( $no, $str ) use ( &$notices ) { $notices[] = $str; return true; } );
        try
        {
            $php = null;
            $data = 'text';
            $resourceData = array();
            $result = eZTemplateOptimizer::optimizeVariable( false, $php, new eZTemplate(), $data, $resourceData );
        }
        finally
        {
            restore_error_handler();
        }
        $this->assertSame( 0, $result );
        $this->assertSame( array(), $notices );
    }

    /** bd4fa8600d, be26d66906: eZTemplateNodeTool::isPHPVariableElement() of null is false */
    public function testIsPHPVariableElementOfNull()
    {
        $this->assertFalse( eZTemplateNodeTool::isPHPVariableElement( null ) );
        $this->assertFalse( eZTemplateNodeTool::isPHPVariableElement( array() ) );
        $this->assertTrue( eZTemplateNodeTool::isPHPVariableElement( array( array( eZTemplate::TYPE_PHP_VARIABLE, 'x' ) ) ) );
    }
}
