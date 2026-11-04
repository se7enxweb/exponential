<?php
/**
 * A {foreach} or {for} whose body includes the same template again (a tree or a menu) goes on with the outer loop
 * after the inner one ends, compiled as interpreted.
 *
 *   - LR-01 {foreach}: the compiled code keeps its loop state in variables named after the loop and saves them on a
 *           stack before an included copy of itself runs. It set them to empty values before saving them, so the
 *           outer loop came back empty and stopped after the first item that recursed.
 *   - LR-02 {for}: the same with its three variables.
 *   - LR-03 several levels, reverse, an offset, and a second foreach after the recursive one.
 *   - LR-04 a sequence (its variables are named after the loop too and saved with its state; its template variable
 *           kept until the outermost loop ends), a delimiter, a break, a {for} index read after the include.
 *
 * Each case is rendered interpreted and compiled, without template errors and without PHP notices: the state is saved
 * only while a loop of the same code runs, so compact() never names an undefined variable.
 *
 * No kernel bootstrap, no database and no siteaccess: a bare eZTemplate with the lib/eztemplate autoloads. Template
 * files and compiled templates go to a private directory under var/tmp that tearDownAfterClass() removes.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group eztemplate
 */

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('eztemplate')]
class eZTemplateCompiledLoopRecursionTest extends PHPUnit\Framework\TestCase
{
    /** @var string directory of this class's template files and compiled templates */
    private static $dir;

    private $savedGlobals = array();

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        self::$dir = 'var/tmp/phpunit-eztemplate-loop-recursion-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( self::$dir . '/compiled', 0777, true );
    }

    public static function tearDownAfterClass(): void
    {
        self::removeTree( self::$dir );
    }

    protected function setUp(): void
    {
        foreach ( array( 'eZTemplateCompilerSettings', 'eZSiteBasics', 'eZCurrentAccess' ) as $name )
            $this->savedGlobals[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
        // the compiler reads the current siteaccess (eZExtension::prependSiteAccess()); run alone, no test set it yet
        if ( !isset( $GLOBALS['eZCurrentAccess']['name'] ) )
            $GLOBALS['eZCurrentAccess'] = array( 'name' => 'phpunit' );
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
     * Writes $source as the template $name (where "%self%" names the template itself for {include}) and renders it
     * with $variables, compiled or interpreted. Returns array( output without white space, errors, PHP notices ).
     */
    private function render( $name, $source, array $variables, $compiled )
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

        $file = self::$dir . '/' . $name . '.tpl';
        file_put_contents( $file, str_replace( '%self%', 'file:' . $file, $source ) );

        $notices = array();
        set_error_handler( function ( $no, $str, $f, $l ) use ( &$notices )
        {
            $notices[] = $str . ' (' . basename( $f ) . ':' . $l . ')';
            return true;
        } );
        try
        {
            $tpl = new eZTemplate();
            $tpl->autoload();
            foreach ( $variables as $variable => $value )
                $tpl->setVariable( $variable, $value );
            $output = $tpl->fetch( $file );
        }
        finally
        {
            restore_error_handler();
        }
        return array( preg_replace( '/\s+/', '', (string)$output ), $tpl->errorLog(), $notices );
    }

    private function assertBothWays( $name, $source, array $variables, $expected )
    {
        foreach ( array( 'interpreted' => false, 'compiled' => true ) as $mode => $compiled )
        {
            list( $output, $errors, $notices ) = $this->render( $name, $source, $variables, $compiled );
            $this->assertSame( $expected, $output, $mode );
            $this->assertSame( array(), $errors, $mode . ': no template errors' );
            $this->assertSame( array(), $notices, $mode . ': no PHP notices (compact() of undefined variables)' );
        }
    }

    private static function tree()
    {
        return array(
            array( 'id' => 1, 'children' => array( array( 'id' => 11, 'children' => array() ) ) ),
            array( 'id' => 2, 'children' => array() ),
            array( 'id' => 3, 'children' => array() ),
        );
    }

    /** LR-01 */
    public function testForeachGoesOnAfterARecursiveInclude()
    {
        $this->assertBothWays( 'foreach_tree',
            '{foreach $nodes as $n}<li>{$n.id}{if $n.children}<ul>{include uri="%self%" nodes=$n.children}</ul>{/if}</li>{/foreach}',
            array( 'nodes' => self::tree() ),
            '<li>1<ul><li>11</li></ul></li><li>2</li><li>3</li>' );
    }

    /** LR-02 */
    public function testForGoesOnAfterARecursiveInclude()
    {
        $this->assertBothWays( 'for_countdown',
            '{for 1 to 3 as $i}[{$depth}.{$i}{if $depth|gt(0)}{include uri="%self%" depth=$depth|sub(1)}{/if}]{/for}',
            array( 'depth' => 1 ),
            '[1.1[0.1][0.2][0.3]][1.2[0.1][0.2][0.3]][1.3[0.1][0.2][0.3]]' );
    }

    /** LR-03 */
    public function testDeeperTreesOffsetReverseAndALoopAfterwards()
    {
        $deep = array(
            array( 'id' => 1, 'children' => array(
                array( 'id' => 11, 'children' => array(
                    array( 'id' => 111, 'children' => array( array( 'id' => 1111, 'children' => array() ) ) ),
                    array( 'id' => 112, 'children' => array() ) ) ),
                array( 'id' => 12, 'children' => array() ) ) ),
            array( 'id' => 2, 'children' => array( array( 'id' => 21, 'children' => array() ) ) ),
            array( 'id' => 3, 'children' => array() ),
        );
        $this->assertBothWays( 'foreach_deep',
            '{foreach $nodes as $n}({$n.id}{if $n.children}{include uri="%self%" nodes=$n.children}{/if}){/foreach}',
            array( 'nodes' => $deep ),
            '(1(11(111(1111))(112))(12))(2(21))(3)' );

        $this->assertBothWays( 'foreach_reverse',
            '{foreach $nodes as $n reverse}({$n.id}{if $n.children}{include uri="%self%" nodes=$n.children}{/if}){/foreach}',
            array( 'nodes' => $deep ),
            '(3)(2(21))(1(12)(11(112)(111(1111))))' );

        $this->assertBothWays( 'foreach_offset',
            '{foreach $nodes as $n offset $start}({$n.id}{if $n.children}{include uri="%self%" nodes=$n.children start=0}{/if}){/foreach}',
            array( 'nodes' => $deep, 'start' => 1 ),
            '(2(21))(3)' );

        $this->assertBothWays( 'foreach_then_another',
            '{foreach $nodes as $n}({$n.id}{if $n.children}{include uri="%self%" nodes=$n.children}{/if}){/foreach}|{foreach $nodes as $n}{$n.id};{/foreach}',
            array( 'nodes' => self::tree() ),
            '(1(11)|11;)(2)(3)|1;2;3;' );
    }

    /** LR-04 */
    public function testSequenceDelimiterAndBreak()
    {
        $this->assertBothWays( 'foreach_sequence',
            '{foreach $nodes as $n sequence array(a,b) as $s}({$s}{$n.id}{if $n.children}{include uri="%self%" nodes=$n.children}{/if}{$s}){/foreach}',
            array( 'nodes' => self::tree() ),
            '(a1(a11a)a)(b2b)(a3a)' );

        $this->assertBothWays( 'for_sequence',
            '{for 1 to 3 as $i sequence array(x,y) as $s}[{$s}{$depth}.{$i}{if $depth|gt(0)}{include uri="%self%" depth=$depth|sub(1)}{/if}{$s}]{/for}',
            array( 'depth' => 1 ),
            '[x1.1[x0.1x][y0.2y][x0.3x]x][y1.2[x0.1x][y0.2y][x0.3x]x][x1.3[x0.1x][y0.2y][x0.3x]x]' );

        // the template variables live in the namespace the include shares: after it they hold the inner loop's last
        // values, interpreted as compiled (a compiled {for} gave the value past its end)
        $this->assertBothWays( 'for_index_after_include',
            '{for 1 to 2 as $i}[{$i}{if $d|gt(0)}{include uri="%self%" d=$d|sub(1)}{/if}{$i}]{/for}',
            array( 'd' => 1 ),
            '[1[11][22]2][2[11][22]2]' );

        $this->assertBothWays( 'foreach_delimiter',
            '{foreach $nodes as $n}{delimiter},{/delimiter}{$n.id}{if $n.children}[{include uri="%self%" nodes=$n.children}]{/if}{/foreach}',
            array( 'nodes' => self::tree() ),
            '1[11],2,3' );

        $this->assertBothWays( 'foreach_break',
            '{foreach $nodes as $n}{if $n.id|eq(3)}{break}{/if}({$n.id}{if $n.children}{include uri="%self%" nodes=$n.children}{/if}){/foreach}',
            array( 'nodes' => self::tree() ),
            '(1(11))(2)' );
    }
    /** LR-05 */
    public function testWhileAndDoWithASequenceGoOnAfterARecursiveInclude()
    {
        $this->assertBothWays( 'while_sequence',
            '{def $i=0}{while $i|lt(2) sequence array(a,b) as $s}{set $i=$i|inc}[{$s}{$d}.{$i}{if $d|gt(0)}{include uri="%self%" name=sub d=$d|sub(1)}{/if}{$s}]{/while}{undef $i}',
            array( 'd' => 1 ),
            '[a1.1[a0.1a][b0.2b]a][b1.2[a0.1a][b0.2b]b]' );

        $this->assertBothWays( 'do_sequence',
            '{def $i=0}{do}{set $i=$i|inc}[{$s}{$d}.{$i}{if $d|gt(0)}{include uri="%self%" name=sub d=$d|sub(1)}{/if}{$s}]{/do while $i|lt(2) sequence array(a,b) as $s}{undef $i}',
            array( 'd' => 1 ),
            '[a1.1[a0.1a][b0.2b]a][b1.2[a0.1a][b0.2b]b]' );
    }

    /** LR-06 */
    public function testSectionContinueSkipAndNestedLoopsInRecursion()
    {
        $this->assertBothWays( 'section_tree',
            '{section var=n loop=$nodes sequence=array(a,b)}({$n.sequence}{$n.item.id}{section show=$n.item.children|count}{include uri="%self%" name=sub nodes=$n.item.children}{/section}){delimiter},{/delimiter}{/section}',
            array( 'nodes' => self::tree() ),
            '(a1(a11)),(b2),(a3)' );

        $this->assertBothWays( 'foreach_continue_skip',
            '{foreach $nodes as $n}{delimiter};{/delimiter}{if $n.id|eq(2)}{skip}{/if}{if $n.id|eq(11)}{continue}{/if}({$n.id}{if $n.children}{include uri="%self%" nodes=$n.children}{/if}){/foreach}',
            array( 'nodes' => self::tree() ),
            '(1);(3)' );

        $this->assertBothWays( 'nested_loops',
            '{foreach $nodes as $n}{for 1 to 2 as $j}<{$n.id}.{$j}{if and( $n.children, $j|eq(1) )}{include uri="%self%" nodes=$n.children}{/if}>{/for}{/foreach}',
            array( 'nodes' => self::tree() ),
            '<1.1<11.1><11.2>><11.2><2.1><2.2><3.1><3.2>' );
    }
    /** LR-07 */
    public function testIncludeParametersAreRestoredAtEveryLevel()
    {
        $this->assertBothWays( 'include_restore',
            '<{$d}{if $d|gt(0)}{include uri="%self%" d=$d|sub(1) e=$d}{/if}{$d}.{$e}>',
            array( 'd' => 3, 'e' => 'x' ),
            '<3<2<1<00.0>1.1>2.2>3.x>' );
    }
}
