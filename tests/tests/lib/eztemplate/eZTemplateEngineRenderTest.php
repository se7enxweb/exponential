<?php
/**
 * End-to-end tests of the template engine: template source in, output out.
 *
 * Every case is rendered twice, once by the processing interpreter (compilation and node tree caching off) and once
 * through the template compiler (the compiled PHP file is written to a private directory under var/tmp and executed),
 * and both outputs must equal the expected text. This covers, with one table:
 *   - the parser (eZTemplateMultiPassParser, element parser, text/variable/operator/function elements)
 *   - the compiler (eZTemplateCompiler, eZTemplateNodeTool, eZTemplateOptimizer) and the processing path
 *   - the functions def/undef/set, if/elseif/else, foreach (offset, max, reverse, delimiter, break, continue, skip),
 *     for, while, do, switch/case, section, sequence, delimit, literal, set-block, append-block
 *   - the operators of eZTemplateArithmeticOperator, ...ArrayOperator, ...LogicOperator, ...TypeOperator,
 *     ...ControlOperator, ...StringOperator, ...TextOperator, ...DigestOperator, ...UnitOperator, ...Nl2BrOperator
 *
 * No kernel bootstrap, no database and no siteaccess: a bare eZTemplate with the lib/eztemplate autoloads.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group eztemplate
 */

class eZTemplateEngineRenderTest extends PHPUnit\Framework\TestCase
{
    /** @var string directory of this class's template files and compiled templates */
    private static $dir;

    private $savedGlobals = array();

    public static function setUpBeforeClass(): void
    {
        $root = dirname( __DIR__, 4 );
        chdir( $root );
        self::$dir = 'var/tmp/phpunit-eztemplate-render-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
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

        // one file per source: the compiler keys its output on the file and its modification time
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

    public static function renderProvider()
    {
        return array(
            // variables and plain text
            'text only'                 => array( 'plain text', array(), 'plain text' ),
            'variable'                  => array( '{$a}', array( 'a' => 'x' ), 'x' ),
            'array element by key'      => array( '{$h.k}', array( 'h' => array( 'k' => 'v' ) ), 'v' ),
            'array element by index'    => array( '{$l[1]}', array( 'l' => array( 'a', 'b' ) ), 'b' ),
            'nested element'            => array( '{$h.k.0}', array( 'h' => array( 'k' => array( 'z' ) ) ), 'z' ),
            'string literal'            => array( '{"q"}', array(), 'q' ),
            'single-quoted literal'     => array( "{'q'}", array(), 'q' ),
            'integer literal'           => array( '{42}', array(), '42' ),
            'negative literal'          => array( '{-3}', array(), '-3' ),
            'float literal'             => array( '{1.25}', array(), '1.25' ),
            'escaped brace text'        => array( 'a{ldelim}b{rdelim}c', array(), 'a{b}c' ),
            'comment'                   => array( 'a{* hidden *}b', array(), 'ab' ),

            // def / set / undef
            'def and output'            => array( '{def $x=3}{$x}{undef $x}', array(), '3' ),
            'def several'               => array( '{def $x=1 $y=2}{$x}{$y}{undef $x $y}', array(), '12' ),
            'set changes value'         => array( '{def $x=1}{set $x=5}{$x}{undef $x}', array(), '5' ),
            'def array'                 => array( '{def $x=array(1,2)}{$x[0]}{$x[1]}{undef $x}', array(), '12' ),

            // if
            'if true'                   => array( '{if true()}y{/if}', array(), 'y' ),
            'if false'                  => array( '{if false()}y{/if}', array(), '' ),
            'if else'                   => array( '{if false()}y{else}n{/if}', array(), 'n' ),
            'if elseif'                 => array( '{if eq($a,1)}one{elseif eq($a,2)}two{else}other{/if}', array( 'a' => 2 ), 'two' ),
            'if elseif else'            => array( '{if eq($a,1)}one{elseif eq($a,2)}two{else}other{/if}', array( 'a' => 3 ), 'other' ),
            'if on string'              => array( '{if $a}y{else}n{/if}', array( 'a' => '' ), 'n' ),
            'if on array'               => array( '{if $a}y{else}n{/if}', array( 'a' => array( 1 ) ), 'y' ),
            'if comparison operators'   => array( '{if and(gt($a,1),lt($a,5),ne($a,3))}y{else}n{/if}', array( 'a' => 4 ), 'y' ),

            // foreach
            'foreach values'            => array( '{foreach $l as $v}{$v}{/foreach}', array( 'l' => array( 1, 2, 3 ) ), '123' ),
            'foreach keys'              => array( '{foreach $h as $k => $v}{$k}={$v};{/foreach}', array( 'h' => array( 'a' => 1, 'b' => 2 ) ), 'a=1;b=2;' ),
            'foreach delimiter'         => array( '{foreach $l as $v}{$v}{delimiter},{/delimiter}{/foreach}', array( 'l' => array( 1, 2, 3 ) ), '1,2,3' ),
            'foreach offset max'        => array( '{foreach $l as $v offset 1 max 2}{$v}{/foreach}', array( 'l' => array( 1, 2, 3, 4 ) ), '23' ),
            'foreach reverse'           => array( '{foreach $l as $v reverse}{$v}{/foreach}', array( 'l' => array( 1, 2, 3 ) ), '321' ),
            'foreach break'             => array( '{foreach $l as $v}{if eq($v,3)}{break}{/if}{$v}{/foreach}', array( 'l' => array( 1, 2, 3, 4 ) ), '12' ),
            'foreach continue'          => array( '{foreach $l as $v}{if eq($v,2)}{continue}{/if}{$v}{/foreach}', array( 'l' => array( 1, 2, 3 ) ), '13' ),
            'foreach skip'              => array( '{foreach $l as $v}{if eq($v,2)}{skip}{/if}{$v}{delimiter}-{/delimiter}{/foreach}', array( 'l' => array( 1, 2, 3 ) ), '1-3' ),
            'foreach empty'             => array( '{foreach $l as $v}{$v}{/foreach}x', array( 'l' => array() ), 'x' ),
            'foreach nested'            => array( '{foreach $l as $r}{foreach $r as $c}{$c}{/foreach};{/foreach}', array( 'l' => array( array( 1, 2 ), array( 3 ) ) ), '12;3;' ),
            'foreach literal array'     => array( '{foreach array("a","b") as $v}{$v}{/foreach}', array(), 'ab' ),

            // for / while / do
            'for'                       => array( '{for 1 to 3 as $i}{$i}{/for}', array(), '123' ),
            'for descending'            => array( '{for 3 to 1 as $i}{$i}{/for}', array(), '321' ),
            'for delimiter'             => array( '{for 1 to 3 as $i}{$i}{delimiter}+{/delimiter}{/for}', array(), '1+2+3' ),
            'while'                     => array( '{def $n=0}{while $n|lt(3)}{$n}{set $n=$n|inc}{/while}{undef $n}', array(), '012' ),
            'do while'                  => array( '{def $n=5}{do}{$n}{set $n=$n|inc}{/do while $n|lt(3)}{undef $n}', array(), '5' ),

            // switch
            'switch match'              => array( '{switch match=$a}{case match=1}a{/case}{case match=2}b{/case}{case}c{/case}{/switch}', array( 'a' => 2 ), 'b' ),
            'switch default'            => array( '{switch match=$a}{case match=1}a{/case}{case}c{/case}{/switch}', array( 'a' => 9 ), 'c' ),
            'switch in'                 => array( '{switch match=$a}{case in=array(1,2)}low{/case}{case}high{/case}{/switch}', array( 'a' => 2 ), 'low' ),

            // section (the function before foreach)
            'section var'               => array( '{section var=v loop=$l}{$v}{/section}', array( 'l' => array( 1, 2 ) ), '12' ),
            'section name item'         => array( '{section name=S loop=$l}{$S:item}{/section}', array( 'l' => array( 'a', 'b' ) ), 'ab' ),
            'section show'              => array( '{section show=$a}y{section-else}n{/section}', array( 'a' => false ), 'n' ),
            'section loop count'        => array( '{section loop=3}x{/section}', array(), 'xxx' ),
            'section delimiter'         => array( '{section var=v loop=$l}{$v}{delimiter};{/delimiter}{/section}', array( 'l' => array( 1, 2, 3 ) ), '1;2;3' ),

            // set-block / append-block / literal / delimit / sequence
            'set-block'                 => array( '{set-block variable=$q}inner{/set-block}{$q|upcase}', array(), 'INNER' ),
            'append-block'              => array( '{append-block variable=$q}a{/append-block}{append-block variable=$q}b{/append-block}{$q|implode(",")}', array(), 'a,b' ),
            'literal'                   => array( '{literal}{x}{$y}{/literal}', array(), '{x}{$y}' ),
            'ldelim rdelim'             => array( '{ldelim}x{rdelim}', array(), '{x}' ),

            // arithmetic operators
            'sum'                       => array( '{sum(1,2,3)}', array(), '6' ),
            'sub'                       => array( '{sub(10,3,2)}', array(), '5' ),
            'mul'                       => array( '{mul(2,3,4)}', array(), '24' ),
            'div'                       => array( '{div(3,2)}', array(), '1.5' ),
            'mod'                       => array( '{mod(7,3)}', array(), '1' ),
            'inc piped'                 => array( '{$a|inc}', array( 'a' => 4 ), '5' ),
            'dec piped'                 => array( '{$a|dec}', array( 'a' => 4 ), '3' ),
            'max'                       => array( '{max(3,9,1)}', array(), '9' ),
            'min'                       => array( '{min(3,9,1)}', array(), '1' ),
            'abs'                       => array( '{abs(-4)}', array(), '4' ),
            'ceil'                      => array( '{ceil(1.2)}', array(), '2' ),
            'floor'                     => array( '{floor(1.8)}', array(), '1' ),
            'round'                     => array( '{round(1.5)}', array(), '2' ),
            'int'                       => array( '{int("12abc")}', array(), '12' ),
            'float'                     => array( '{float("1.5")}', array(), '1.5' ),
            'count array'               => array( '{count($l)}', array( 'l' => array( 1, 2, 3 ) ), '3' ),
            'count piped'               => array( '{$l|count}', array( 'l' => array( 'a' => 1 ) ), '1' ),
            'roman'                     => array( '{1994|roman}', array(), 'MCMXCIV' ),
            'arithmetic on variables'   => array( '{sum($a,mul($b,2))}', array( 'a' => 1, 'b' => 3 ), '7' ),

            // array operators
            'array implode'             => array( '{array(1,2,3)|implode(",")}', array(), '1,2,3' ),
            'hash keys'                 => array( '{foreach hash("a",1,"b",2) as $k => $v}{$k}{$v}{/foreach}', array(), 'a1b2' ),
            'append'                    => array( '{array(1,2)|append(3)|implode("-")}', array(), '1-2-3' ),
            'prepend'                   => array( '{array(2,3)|prepend(1)|implode("-")}', array(), '1-2-3' ),
            'merge'                     => array( '{array(1)|merge(array(2),array(3))|implode("-")}', array(), '1-2-3' ),
            'contains array'            => array( '{if array(1,2)|contains(2)}y{else}n{/if}', array(), 'y' ),
            'contains string'           => array( '{if "abc"|contains("b")}y{else}n{/if}', array(), 'y' ),
            'compare'                   => array( '{if array(1,2)|compare(array(1,2))}y{else}n{/if}', array(), 'y' ),
            'extract string'            => array( '{"abcdef"|extract(1,3)}', array(), 'bcd' ),
            'extract_left'              => array( '{"abcdef"|extract_left(2)}', array(), 'ab' ),
            'extract_right'             => array( '{"abcdef"|extract_right(2)}', array(), 'ef' ),
            'extract array'             => array( '{array(1,2,3,4)|extract(1,2)|implode(",")}', array(), '2,3' ),
            'begins_with'               => array( '{if "abc"|begins_with("ab")}y{else}n{/if}', array(), 'y' ),
            'ends_with'                 => array( '{if "abc"|ends_with("bc")}y{else}n{/if}', array(), 'y' ),
            'ends_with false'           => array( '{if "abc"|ends_with("ab")}y{else}n{/if}', array(), 'n' ),
            'explode'                   => array( '{"a,b,c"|explode(",")|implode(":")}', array(), 'a:b:c' ),
            'repeat'                    => array( '{"x"|repeat(3)}', array(), 'xxx' ),
            'reverse string'            => array( '{"abc"|reverse}', array(), 'cba' ),
            'reverse array'             => array( '{array(1,2,3)|reverse|implode("")}', array(), '321' ),
            'insert'                    => array( '{array(1,3)|insert(1,2)|implode(",")}', array(), '1,2,3' ),
            'remove'                    => array( '{array(1,2,3)|remove(1,1)|implode(",")}', array(), '1,3' ),
            'replace'                   => array( '{array(1,2,3)|replace(1,1,9)|implode(",")}', array(), '1,9,3' ),
            'unique'                    => array( '{array(3,1,3)|unique|implode(",")}', array(), '3,1' ),
            'array_sum'                 => array( '{array(1,2,3)|array_sum}', array(), '6' ),

            // logic and control operators
            'eq true'                   => array( '{if eq(1,1)}y{else}n{/if}', array(), 'y' ),
            'ne'                        => array( '{if ne(1,2)}y{else}n{/if}', array(), 'y' ),
            'le ge'                     => array( '{if and(le(2,2),ge(3,2))}y{else}n{/if}', array(), 'y' ),
            'or'                        => array( '{if or(false(),true())}y{else}n{/if}', array(), 'y' ),
            'not'                       => array( '{if not(false())}y{else}n{/if}', array(), 'y' ),
            'null piped'                => array( '{if $n|null}y{else}n{/if}', array( 'n' => null ), 'y' ),
            'null piped not null'       => array( '{if $n|null}y{else}n{/if}', array( 'n' => 0 ), 'n' ),
            'is_null'                   => array( '{if is_null($n)}y{else}n{/if}', array( 'n' => null ), 'y' ),
            'choose'                    => array( '{1|choose("a","b","c")}', array(), 'b' ),
            'choose boolean'            => array( '{true()|choose("no","yes")}', array(), 'yes' ),
            'cond'                      => array( '{cond(false(),"a",true(),"b","c")}', array(), 'b' ),
            'cond fallback'             => array( '{cond(false(),"a","c")}', array(), 'c' ),
            'first_set'                 => array( '{first_set($missing,$a,"z")}', array( 'a' => 'hello' ), 'hello' ),
            'first_set fallback'        => array( '{first_set($missing,"z")}', array(), 'z' ),

            // type operators
            'is_array'                  => array( '{if is_array($l)}y{else}n{/if}', array( 'l' => array() ), 'y' ),
            'is_string'                 => array( '{if is_string("x")}y{else}n{/if}', array(), 'y' ),
            'is_numeric'                => array( '{if is_numeric("12")}y{else}n{/if}', array(), 'y' ),
            'is_integer'                => array( '{if is_integer($a)}y{else}n{/if}', array( 'a' => 3 ), 'y' ),
            'is_set'                    => array( '{if is_set($a)}y{else}n{/if}', array( 'a' => 1 ), 'y' ),
            'is_set missing'            => array( '{if is_set($missing)}y{else}n{/if}', array(), 'n' ),
            'is_unset missing'          => array( '{if is_unset($missing)}y{else}n{/if}', array(), 'y' ),
            'get_type string'           => array( '{get_type($a)}', array( 'a' => 'xy' ), 'string[2]' ),
            'get_type array'            => array( '{get_type($a)}', array( 'a' => array( 1, 2, 3 ) ), 'array[3]' ),

            // string operators
            'upcase'                    => array( '{"abc"|upcase}', array(), 'ABC' ),
            'downcase'                  => array( '{"ABC"|downcase}', array(), 'abc' ),
            'upfirst'                   => array( '{"abc def"|upfirst}', array(), 'Abc def' ),
            'upword'                    => array( '{"abc def"|upword}', array(), 'Abc Def' ),
            'count_words'               => array( '{"one two three"|count_words}', array(), '3' ),
            'count_chars'               => array( '{"abcd"|count_chars}', array(), '4' ),
            'trim'                      => array( '{"  a  "|trim}', array(), 'a' ),
            'simplify'                  => array( '{"a   b    c"|simplify}', array(), 'a b c' ),
            'wash html'                 => array( '{"<b>&</b>"|wash}', array(), '&lt;b&gt;&amp;&lt;/b&gt;' ),
            'wash variable'             => array( '{$a|wash}', array( 'a' => '"q"' ), '&quot;q&quot;' ),
            'shorten'                   => array( '{"hello world"|shorten(8)}', array(), 'hello...' ),
            'shorten short text'        => array( '{"hi"|shorten(8)}', array(), 'hi' ),
            'pad'                       => array( '{"x"|pad(3,"-")}', array(), 'x--' ),
            'chr'                       => array( '{array(65,66)|chr}', array(), 'AB' ),
            'ord'                       => array( '{"A"|ord|implode(",")}', array(), '65' ),
            'nl2br'                     => array( "{\$a|nl2br}", array( 'a' => "a\nb" ), "a<br />\nb" ),
            'nl2br crlf'                => array( "{\$a|nl2br}", array( 'a' => "a\r\nb" ), "a<br />\r\nb" ),
            'nl2br no newline'          => array( "{\$a|nl2br}", array( 'a' => "ab" ), "ab" ),
            'break'                     => array( "{\$a|break}", array( 'a' => "a\nb" ), "a<br />\nb" ),
            'wrap'                      => array( '{"aaa bbb ccc"|wrap(7)}', array(), "aaa bbb\nccc" ),
            'concat'                    => array( '{concat("a","b","c")}', array(), 'abc' ),
            'concat piped'              => array( '{"a"|concat("b")}', array(), 'ab' ),
            'indent'                    => array( '{"x"|indent(2,"space")}', array(), '  x' ),

            // digest and unit operators
            'md5'                       => array( '{"Hello"|md5}', array(), md5( 'Hello' ) ),
            'crc32'                     => array( '{"Hello"|crc32}', array(), (string)crc32( 'Hello' ) ),
            'rot13'                     => array( '{"Hello"|rot13}', array(), 'Uryyb' ),
            'si bytes'                  => array( '{1024|si("byte")}', array(), '1.00 kB' ),
            'si small bytes'            => array( '{12|si("byte")}', array(), '12 B' ),

            // operator chains and parameters from variables
            'chain'                     => array( '{$a|downcase|upfirst|concat("!")}', array( 'a' => 'HELLO' ), 'Hello!' ),
            'operator in parameter'     => array( '{"a,b"|explode(",")|reverse|implode(":")}', array(), 'b:a' ),
            'variable as parameter'     => array( '{$s|shorten($n)}', array( 's' => 'abcdefghij', 'n' => 6 ), 'abc...' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('renderProvider')]
    public function testInterpretedOutput( $source, $variables, $expected )
    {
        list( $output, $errors ) = $this->render( $source, $variables, false );
        $this->assertSame( array(), $errors, 'template errors' );
        $this->assertSame( $expected, $output );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('renderProvider')]
    public function testCompiledOutput( $source, $variables, $expected )
    {
        list( $output, $errors ) = $this->render( $source, $variables, true );
        $this->assertSame( array(), $errors, 'template errors' );
        $this->assertSame( $expected, $output );
    }

    public function testCompiledTemplateIsWrittenToTheCompilationDirectory()
    {
        $this->render( '{"compiled marker"}', array(), true );
        $files = glob( self::$dir . '/compiled/' . md5( '{"compiled marker"}' ) . '-*.php' );
        $this->assertCount( 1, $files );
        $this->assertStringContainsString( 'compiled marker', file_get_contents( $files[0] ) );
    }

    /**
     * A template edited while the process runs (a persistent worker, a CLI script) is compiled from the new source.
     * The tree parsed from the old source stayed in eZTemplateTreeCache's in-memory table and was compiled again, so
     * the new compiled file, now newer than the source, carried the old template for every process.
     */
    public function testSourceEditedInTheSameProcessIsCompiledFromTheNewSource()
    {
        $source = '{"version one"}';
        $file = self::$dir . '/' . md5( $source ) . '.tpl';
        file_put_contents( $file, $source );
        touch( $file, time() - 100 );
        clearstatcache();
        list( $output ) = $this->render( $source, array(), true );
        $this->assertSame( 'version one', $output );
        $compiled = glob( self::$dir . '/compiled/' . md5( $source ) . '-*.php' );
        $this->assertCount( 1, $compiled );

        // an edit a few seconds later: the source is newer than the compiled file and the cached tree
        file_put_contents( $file, '{"version two"}' );
        touch( $file, time() + 5 );
        clearstatcache();
        list( $output ) = $this->render( $source, array(), true );
        $this->assertSame( 'version two', $output );
    }

    public static function modeProvider()
    {
        return array( 'interpreted' => array( false ), 'compiled' => array( true ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('modeProvider')]
    public function testIncludePassesParametersAndKeepsTheCallersVariables( $compiled )
    {
        $included = self::$dir . '/included-part.tpl';
        file_put_contents( $included, '[{$p}|{$q}]' );
        $source = '{def $q="outer"}{include uri=concat("file:",$inc) p="one" q="inner"}{$q}{undef $q}';
        list( $output, $errors ) = $this->render( $source, array( 'inc' => $included ), $compiled );
        $this->assertSame( array(), $errors );
        $this->assertSame( '[one|inner]outer', $output );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('modeProvider')]
    public function testIncludeOfAMissingFileRendersNothing( $compiled )
    {
        $source = 'a{include uri="file:' . self::$dir . '/no-such-file.tpl"}b';
        list( $output ) = $this->render( $source, array(), $compiled );
        $this->assertSame( 'ab', $output );
    }

    public function testUnknownOperatorIsReported()
    {
        list( $output, $errors, $warnings ) = $this->render( '{"x"|no_such_operator_t1}', array(), false );
        $this->assertNotEmpty( array_merge( $errors, $warnings ) );
    }

    public function testUndefinedVariableRendersEmpty()
    {
        list( $output ) = $this->render( 'a{$no_such_variable}b', array(), false );
        $this->assertSame( 'ab', $output );
    }

    public function testVariablesSetInTheTemplateAreVisibleAfterwards()
    {
        $tpl = new eZTemplate();
        $tpl->autoload();
        $GLOBALS['eZTemplateCompilerSettings']['compile'] = false;
        $GLOBALS['eZSiteBasics']['no-cache-adviced'] = true;
        $file = self::$dir . '/setblock-visible.tpl';
        file_put_contents( $file, '{set-block scope=root variable=$out_t1}done{/set-block}' );
        $tpl->fetch( $file );
        $this->assertTrue( $tpl->hasVariable( 'out_t1' ) );
        $this->assertSame( 'done', $tpl->variable( 'out_t1' ) );
    }
}
