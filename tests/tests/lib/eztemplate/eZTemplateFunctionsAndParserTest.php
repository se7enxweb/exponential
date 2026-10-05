<?php
/**
 * The template functions and the parser, through eZTemplate with string templates, interpreted and compiled:
 *   - section: offset, max, reverse, loop over a number and a hash, the index, number, key, iteration variables,
 *     include and exclude filters, section-else, show, delimiter with a modulo
 *   - let/default (the old namespace functions; let keeps a variable that exists already), run-once, set-block and append-block scopes,
 *     include with name= into its own namespace, undef, nested foreach with keys, while and do with delimiters,
 *     for with negative bounds, switch on strings
 *   - object attributes: an object with attribute()/hasAttribute() and its nested attributes, a missing attribute
 *   - the parser: whitespace and line breaks in tags, comments inside expressions, quotes in strings, escaped
 *     characters, a template that is all text, and errors that are reported without stopping the rest of the
 *     template (an unknown function, an unclosed function, a stray end tag, an undefined variable)
 *
 * No kernel bootstrap, no database. Template files and compiled templates go to a private directory under var/tmp.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group eztemplate
 */

/**
 * An object as templates see kernel objects: attribute(), hasAttribute(), attributes().
 */
class eZTemplateFunctionsAndParserTestObject
{
    private $values;

    public function __construct( $values )
    {
        $this->values = $values;
    }

    function attributes()
    {
        return array_keys( $this->values );
    }

    function hasAttribute( $name )
    {
        return array_key_exists( $name, $this->values );
    }

    function attribute( $name )
    {
        return $this->values[$name] ?? null;
    }
}

class eZTemplateFunctionsAndParserTest extends PHPUnit\Framework\TestCase
{
    private static $dir;
    private $savedGlobals = array();

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        self::$dir = 'var/tmp/phpunit-eztemplate-functions-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( self::$dir . '/compiled', 0777, true );
    }

    public static function tearDownAfterClass(): void
    {
        self::removeTree( self::$dir );
    }

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        foreach ( array( 'eZTemplateCompilerSettings', 'eZSiteBasics' ) as $name )
            $this->savedGlobals[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
        // run-once runs once per request and placement: each rendering here is a request of its own
        unset( $GLOBALS['eZTemplateRunOnceKeys'] );
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
        if ( is_dir( $path ) )
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

    private function render( $source, $variables, $compiled, $extraFiles = array() )
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
        foreach ( $extraFiles as $name => $content )
        {
            if ( !file_exists( self::$dir . '/' . $name ) )
                file_put_contents( self::$dir . '/' . $name, $content );
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

    public static function caseTable()
    {
        $object = new eZTemplateFunctionsAndParserTestObject( array(
            'name' => 'Front page',
            'node' => new eZTemplateFunctionsAndParserTestObject( array( 'id' => 2, 'children' => array( 'a', 'b' ) ) ),
        ) );
        return array(
            // section
            'section offset max'        => array( '{section var=v loop=$l offset=1 max=2}{$v}{/section}', array( 'l' => array( 1, 2, 3, 4 ) ), '23' ),
            'section reverse'           => array( '{section var=v loop=$l reverse}{$v}{/section}', array( 'l' => array( 1, 2, 3 ) ), '321' ),
            'section over a number'     => array( '{section var=v loop=3}{$v}{/section}', array(), '123' ),
            'section hash keys'         => array( '{section var=v loop=$h}{$v.key}={$v.item};{/section}', array( 'h' => array( 'a' => 1, 'b' => 2 ) ), 'a=1;b=2;' ),
            'section index number'      => array( '{section var=v loop=$l}{$v.index}/{$v.number} {/section}', array( 'l' => array( 'x', 'y' ) ), '0/1 1/2 ' ),
            'section name variables'    => array( '{section name=S loop=$l}{$S:index}{$S:item}{/section}', array( 'l' => array( 'x', 'y' ) ), '0x1y' ),
            'section exclude'           => array( '{section var=v loop=$l}{section-exclude match=eq($v,2)}{$v}{/section}', array( 'l' => array( 1, 2, 3 ) ), '13' ),
            'section exclude include'   => array( '{section var=v loop=$l}{section-exclude match=true()}{section-include match=eq($v,2)}{$v}{/section}', array( 'l' => array( 1, 2, 3 ) ), '2' ),
            'section else when hidden'  => array( '{section var=v loop=$l show=$l}{$v}{section-else}none{/section}', array( 'l' => array() ), 'none' ),
            'section empty loop'        => array( '[{section var=v loop=$l}{$v}{section-else}none{/section}]', array( 'l' => array() ), '[]' ),
            'section show true'         => array( '{section show=$a}yes{section-else}no{/section}', array( 'a' => 1 ), 'yes' ),
            'section delimiter modulo'  => array( '{section var=v loop=$l}{$v}{delimiter modulo=2}|{/delimiter}{/section}', array( 'l' => array( 1, 2, 3, 4, 5 ) ), '12|34|5' ),

            // let / default / set / run-once / blocks
            'let'                       => array( '{let x=5}{$x}{/let}', array(), '5' ),
            'let does not leak'         => array( '{let x=5}{/let}[{$x}]', array( 'x' => 'outer' ), '[outer]' ),
            'default keeps a value'     => array( '{default x=5}{$x}{/default}', array( 'x' => 'set' ), 'set' ),
            'default fills a gap'       => array( '{default y=5}{$y}{/default}', array(), '5' ),
            'run-once'                  => array( '{for 1 to 3 as $i}{run-once}once{/run-once}{$i}{/for}', array(), 'once123' ),
            'append-block scope'        => array( '{append-block variable=$a}1{/append-block}{append-block variable=$a}2{/append-block}{$a|count}', array(), '2' ),
            'set-block in a loop'       => array( '{foreach array(1,2) as $i}{set-block variable=$s}<{$i}>{/set-block}{/foreach}{$s}', array(), '<2>' ),
            'undef then default'        => array( '{def $x=1}{undef $x}{if is_set($x)}set{else}gone{/if}', array(), 'gone' ),

            // loops
            'foreach nested keys'       => array( '{foreach $h as $k => $row}{foreach $row as $c}{$k}{$c}{/foreach}{/foreach}', array( 'h' => array( 'a' => array( 1, 2 ), 'b' => array( 3 ) ) ), 'a1a2b3' ),
            'foreach over an object'    => array( '{foreach $o.node.children as $c}{$c}{/foreach}', array( 'o' => $object ), 'ab' ),
            'while delimiter'           => array( '{def $n=0}{while $n|lt(3)}{$n}{delimiter},{/delimiter}{set $n=inc($n)}{/while}{undef $n}', array(), '0,1,2' ),
            'for negative bounds'       => array( '{for -2 to 1 as $i}{$i};{/for}', array(), '-2;-1;0;1;' ),
            'for from variables'        => array( '{for $a to $b as $i}{$i}{/for}', array( 'a' => 2, 'b' => 4 ), '234' ),
            'switch on a string'        => array( '{switch match=$s}{case match="b"}B{/case}{case match="a"}A{/case}{case}?{/case}{/switch}', array( 's' => 'a' ), 'A' ),

            // objects
            'object attribute'          => array( '{$o.name}', array( 'o' => $object ), 'Front page' ),
            'nested object attribute'   => array( '{$o.node.id}', array( 'o' => $object ), '2' ),
            'attribute by variable'     => array( '{$o[$k]}', array( 'o' => $object, 'k' => 'name' ), 'Front page' ),
            'object in a condition'     => array( '{if $o.node}y{/if}', array( 'o' => $object ), 'y' ),

            // parser
            'line breaks in a tag'      => array( "{def\n  \$x = 3\n}{\$x\n}{undef \$x}", array(), '3' ),
            'spaces in parameters'      => array( '{sum( 1 , 2 )}', array(), '3' ),
            'double quotes in single'   => array( '{\'say "hi"\'}', array(), 'say "hi"' ),
            'escaped quote'             => array( '{"a\"b"}', array(), 'a"b' ),
            'escaped single quote'      => array( "{'it\\'s'}", array(), "it's" ),
            'text only, many lines'     => array( "line 1\nline 2\n\n", array(), "line 1\nline 2\n\n" ),
            'comment between text'      => array( "a{* multi\nline *}b", array(), 'ab' ),
            'unicode text'              => array( 'grüße {"日本"}', array(), 'grüße 日本' ),
        );
    }

    public static function renderProvider()
    {
        $rows = array();
        foreach ( self::caseTable() as $name => $case )
        {
            $rows["$name, interpreted"] = array( $case[0], $case[1], false, $case[2] );
            $rows["$name, compiled"] = array( $case[0], $case[1], true, $case[2] );
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

    public static function modeProvider()
    {
        return array( 'interpreted' => array( false ), 'compiled' => array( true ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('modeProvider')]
    public function testIncludeWithANameUsesItsOwnNamespace( $compiled )
    {
        $part = 'inc-ns-part.tpl';
        list( $output, $errors ) = $this->render(
            '{include uri=concat("file:",$p) name=Part value="inner"}[{$value}]',
            array( 'p' => self::$dir . '/' . $part, 'value' => 'outer' ), $compiled,
            array( $part => '<{$value}>' ) );
        $this->assertSame( array(), $errors );
        $this->assertSame( '<inner>[outer]', $output );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('modeProvider')]
    public function testMissingObjectAttributeIsReportedAndRendersEmpty( $compiled )
    {
        $object = new eZTemplateFunctionsAndParserTestObject( array( 'name' => 'x' ) );
        list( $output, $errors, $warnings ) = $this->render( 'a{$o.missing}b', array( 'o' => $object ), $compiled );
        $this->assertSame( 'ab', $output );
    }

    public static function brokenProvider()
    {
        return array(
            'unknown function'      => array( 'a{no_such_function_x3}b' ),
            'stray end tag'         => array( 'a{/if}b' ),
            'unterminated tag'      => array( 'a{$x' ),
        );
    }

    /**
     * A broken template is reported in the error or warning log, and the text around the broken part is kept.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('brokenProvider')]
    public function testBrokenTemplatesAreReported( $source )
    {
        list( $output, $errors, $warnings ) = $this->render( $source, array(), false );
        $this->assertNotEmpty( array_merge( $errors, $warnings ), 'something is reported' );
        $this->assertStringStartsWith( 'a', (string)$output );
    }

    /**
     * An end tag that closes nothing is reported and ignored. The parser popped its empty tag stack, lost the root
     * node and dropped everything after the tag.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('modeProvider')]
    public function testStrayEndTagKeepsTheRestOfTheTemplate( $compiled )
    {
        // a source of its own per mode: the tree cache would otherwise skip the parse that reports the tag
        list( $output, $errors ) = $this->render( 'a{/if}b{"c"}{* ' . ( $compiled ? 'compiled' : 'interpreted' ) . ' *}', array(), $compiled );
        $this->assertNotEmpty( $errors );
        $this->assertSame( 'abc', $output );
    }

    public function testRedefiningAVariableWarns()
    {
        list( $output, $errors, $warnings ) = $this->render( '{def $x=1}{def $x=2}{$x}{undef $x}', array(), false );
        $this->assertNotEmpty( array_merge( $errors, $warnings ) );
    }

    public function testSetOfAnUndefinedVariableIsAnError()
    {
        list( $output, $errors, $warnings ) = $this->render( '{set $never_defined_x3=1}x', array(), false );
        $this->assertNotEmpty( array_merge( $errors, $warnings ) );
        $this->assertSame( 'x', $output );
    }
}
