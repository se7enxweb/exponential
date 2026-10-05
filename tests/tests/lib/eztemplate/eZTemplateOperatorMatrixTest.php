<?php
/**
 * The template operators through eZTemplate, with string templates: every case is rendered four ways.
 *
 * Each case is rendered by the processing interpreter and by the template compiler, and both with its input written
 * as a literal (so the compiler can fold the operator at compile time) and with the same input passed in as a
 * template variable (so the compiled template has to evaluate the operator at run time). All four outputs must equal
 * the expected text. The cases cover what eZTemplateEngineRenderTest does not:
 *   - eZTemplateLogicOperator: lt/gt/le/ge on numbers and on array counts, eq/ne with several parameters,
 *     or/and returning the deciding operand, not on every type, null, choose
 *   - eZTemplateArithmeticOperator: piped forms, mixed int/float, negative numbers, roman, count of strings
 *   - eZTemplateArrayOperator: the deprecated array_* names, hash, contains/compare false cases, extract on
 *     arrays and strings with and without length, insert of several items, remove with a length, unique on strings
 *   - eZTemplateTypeOperator: is_boolean, is_float, is_object, is_class, get_type for every type, get_class
 *   - eZTemplateStringOperator: wash modes, shorten with a custom sequence and trim type, pad with a char, trim with
 *     a character list, upword/upfirst on multi-byte input, count_words of an empty string
 *   - eZTemplateTextOperator: indent with tab and custom fill, concat of numbers
 *   - eZTemplateUnitOperator: si with the binary and decimal prefix modes, a unit other than byte
 *   - eZTemplateDigestOperator: md5/crc32/rot13 of variables and of an empty string
 *   - eZTemplateControlOperator: cond and first_set with variables only
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

class eZTemplateOperatorMatrixTest extends PHPUnit\Framework\TestCase
{
    /** @var string directory of this class's template files and compiled templates */
    private static $dir;

    private $savedGlobals = array();

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        self::$dir = 'var/tmp/phpunit-eztemplate-matrix-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
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
            // logic: comparisons on numbers and on array counts
            'lt piped'                  => array( '{if 2|lt(3)}y{else}n{/if}', '{if $a|lt($b)}y{else}n{/if}', array( 'a' => 2, 'b' => 3 ), 'y' ),
            'lt two parameters'         => array( '{if lt(3,2)}y{else}n{/if}', '{if lt($a,$b)}y{else}n{/if}', array( 'a' => 3, 'b' => 2 ), 'n' ),
            'gt equal values'           => array( '{if gt(2,2)}y{else}n{/if}', '{if gt($a,$b)}y{else}n{/if}', array( 'a' => 2, 'b' => 2 ), 'n' ),
            'ge equal values'           => array( '{if ge(2,2)}y{else}n{/if}', '{if ge($a,$b)}y{else}n{/if}', array( 'a' => 2, 'b' => 2 ), 'y' ),
            'le negative'               => array( '{if le(-5,-4)}y{else}n{/if}', '{if le($a,$b)}y{else}n{/if}', array( 'a' => -5, 'b' => -4 ), 'y' ),
            'lt floats'                 => array( '{if lt(1.5,1.25)}y{else}n{/if}', '{if lt($a,$b)}y{else}n{/if}', array( 'a' => 1.5, 'b' => 1.25 ), 'n' ),
            'gt array counts'           => array( '{if gt(array(1,2,3),array(9))}y{else}n{/if}', '{if gt($a,$b)}y{else}n{/if}', array( 'a' => array( 1, 2, 3 ), 'b' => array( 9 ) ), 'y' ),

            // logic: eq / ne
            'eq piped string'           => array( '{if "a"|eq("a")}y{else}n{/if}', '{if $a|eq($b)}y{else}n{/if}', array( 'a' => 'a', 'b' => 'a' ), 'y' ),
            'eq three equal'            => array( '{if eq(4,4,4)}y{else}n{/if}', '{if eq($a,$a,$b)}y{else}n{/if}', array( 'a' => 4, 'b' => 4 ), 'y' ),
            'eq three, last differs'    => array( '{if eq(4,4,5)}y{else}n{/if}', '{if eq($a,$a,$b)}y{else}n{/if}', array( 'a' => 4, 'b' => 5 ), 'n' ),
            'eq loose number string'    => array( '{if eq(1,"1")}y{else}n{/if}', '{if eq($a,$b)}y{else}n{/if}', array( 'a' => 1, 'b' => '1' ), 'y' ),
            'ne three equal'            => array( '{if ne(4,4,4)}y{else}n{/if}', '{if ne($a,$a,$b)}y{else}n{/if}', array( 'a' => 4, 'b' => 4 ), 'n' ),
            'ne three, last differs'    => array( '{if ne(4,4,5)}y{else}n{/if}', '{if ne($a,$a,$b)}y{else}n{/if}', array( 'a' => 4, 'b' => 5 ), 'y' ),
            'ne piped'                  => array( '{if 1|ne(2)}y{else}n{/if}', '{if $a|ne($b)}y{else}n{/if}', array( 'a' => 1, 'b' => 2 ), 'y' ),

            // logic: or / and return the deciding operand
            'or returns first true'     => array( '{or(0,"","x","y")}', '{or($a,$b,$c,$d)}', array( 'a' => 0, 'b' => '', 'c' => 'x', 'd' => 'y' ), 'x' ),
            'or all false'              => array( '{if or(0,false(),array())}y{else}n{/if}', '{if or($a,$b,$c)}y{else}n{/if}', array( 'a' => 0, 'b' => false, 'c' => array() ), 'n' ),
            'and returns last'          => array( '{and(1,"a","last")}', '{and($a,$b,$c)}', array( 'a' => 1, 'b' => 'a', 'c' => 'last' ), 'last' ),
            'and with a zero'           => array( '{if and(1,0,2)}y{else}n{/if}', '{if and($a,$b,$c)}y{else}n{/if}', array( 'a' => 1, 'b' => 0, 'c' => 2 ), 'n' ),
            'and with an empty array'   => array( '{if and(1,array())}y{else}n{/if}', '{if and($a,$b)}y{else}n{/if}', array( 'a' => 1, 'b' => array() ), 'n' ),

            // logic: not / null / true / false / choose
            'not zero'                  => array( '{if not(0)}y{else}n{/if}', '{if not($a)}y{else}n{/if}', array( 'a' => 0 ), 'y' ),
            'not empty string'          => array( '{if not("")}y{else}n{/if}', '{if not($a)}y{else}n{/if}', array( 'a' => '' ), 'y' ),
            'not string'                => array( '{if not("x")}y{else}n{/if}', '{if not($a)}y{else}n{/if}', array( 'a' => 'x' ), 'n' ),
            'not empty array'           => array( '{if not(array())}y{else}n{/if}', '{if not($a)}y{else}n{/if}', array( 'a' => array() ), 'y' ),
            'not array'                 => array( '{if not(array(1))}y{else}n{/if}', '{if not($a)}y{else}n{/if}', array( 'a' => array( 1 ) ), 'n' ),
            'not piped true'            => array( '{if true()|not}y{else}n{/if}', '{if $a|not}y{else}n{/if}', array( 'a' => true ), 'n' ),
            'null of a string'          => array( '{if "x"|null}y{else}n{/if}', '{if $a|null}y{else}n{/if}', array( 'a' => 'x' ), 'n' ),
            'choose index zero'         => array( '{0|choose("a","b")}', '{$i|choose("a","b")}', array( 'i' => 0 ), 'a' ),
            'choose false'              => array( '{false()|choose("no","yes")}', '{$i|choose("no","yes")}', array( 'i' => false ), 'no' ),
            'choose of three, boolean'  => array( '{true()|choose("a","b","c")}', '{$i|choose("a","b","c")}', array( 'i' => true ), 'b' ),
            'choose of three, 0 or null'=> array( '{0|choose("a","b","c")}', '{$i|choose("a","b","c")}', array( 'i' => null ), 'a' ),
            'choose variable choices'   => array( '{2|choose("a","b","c")}', '{$i|choose($x,$y,$z)}', array( 'i' => 2, 'x' => 'a', 'y' => 'b', 'z' => 'c' ), 'c' ),

            // arithmetic
            'sum piped'                 => array( '{1|sum(2,3)}', '{$a|sum($b,$c)}', array( 'a' => 1, 'b' => 2, 'c' => 3 ), '6' ),
            'sum negative'              => array( '{sum(-2,-3)}', '{sum($a,$b)}', array( 'a' => -2, 'b' => -3 ), '-5' ),
            'sub piped'                 => array( '{10|sub(4)}', '{$a|sub($b)}', array( 'a' => 10, 'b' => 4 ), '6' ),
            'sub to negative'           => array( '{sub(1,4)}', '{sub($a,$b)}', array( 'a' => 1, 'b' => 4 ), '-3' ),
            'mul piped'                 => array( '{3|mul(4)}', '{$a|mul($b)}', array( 'a' => 3, 'b' => 4 ), '12' ),
            'mul by zero'               => array( '{mul(7,0)}', '{mul($a,$b)}', array( 'a' => 7, 'b' => 0 ), '0' ),
            'div piped'                 => array( '{9|div(3)}', '{$a|div($b)}', array( 'a' => 9, 'b' => 3 ), '3' ),
            'div several'               => array( '{div(100,5,2)}', '{div($a,$b,$c)}', array( 'a' => 100, 'b' => 5, 'c' => 2 ), '10' ),
            'mod piped'                 => array( '{10|mod(4)}', '{$a|mod($b)}', array( 'a' => 10, 'b' => 4 ), '2' ),
            'inc'                       => array( '{inc(9)}', '{inc($a)}', array( 'a' => 9 ), '10' ),
            'dec'                       => array( '{dec(0)}', '{dec($a)}', array( 'a' => 0 ), '-1' ),
            'max of floats'             => array( '{max(1.5,2.5,2)}', '{max($a,$b,$c)}', array( 'a' => 1.5, 'b' => 2.5, 'c' => 2 ), '2.5' ),
            'min of negatives'          => array( '{min(-1,-7,3)}', '{min($a,$b,$c)}', array( 'a' => -1, 'b' => -7, 'c' => 3 ), '-7' ),
            'abs piped'                 => array( '{-2.5|abs}', '{$a|abs}', array( 'a' => -2.5 ), '2.5' ),
            'ceil negative'             => array( '{ceil(-1.5)}', '{ceil($a)}', array( 'a' => -1.5 ), '-1' ),
            'floor negative'            => array( '{floor(-1.5)}', '{floor($a)}', array( 'a' => -1.5 ), '-2' ),
            'round down'                => array( '{round(2.4)}', '{round($a)}', array( 'a' => 2.4 ), '2' ),
            'round piped'               => array( '{2.6|round}', '{$a|round}', array( 'a' => 2.6 ), '3' ),
            'int of float'              => array( '{int(3.9)}', '{int($a)}', array( 'a' => 3.9 ), '3' ),
            'float of int string'       => array( '{float("2")}', '{float($a)}', array( 'a' => '2' ), '2' ),
            'count string'              => array( '{count("abc")}', '{count($a)}', array( 'a' => 'abc' ), '3' ),
            'count empty array'         => array( '{count(array())}', '{count($a)}', array( 'a' => array() ), '0' ),
            'roman 4'                   => array( '{4|roman}', '{$a|roman}', array( 'a' => 4 ), 'IV' ),
            'roman 3999'                => array( '{3999|roman}', '{$a|roman}', array( 'a' => 3999 ), 'MMMCMXCIX' ),
            'roman 49'                  => array( '{49|roman}', '{$a|roman}', array( 'a' => 49 ), 'XLIX' ),
            'nested arithmetic'         => array( '{sub(mul(3,4),div(10,5))}', '{sub(mul($a,$b),div($c,$d))}', array( 'a' => 3, 'b' => 4, 'c' => 10, 'd' => 5 ), '10' ),

            // arrays
            'deprecated array_append'   => array( '{array(1)|array_append(2)|implode(",")}', '{$l|array_append($v)|implode(",")}', array( 'l' => array( 1 ), 'v' => 2 ), '1,2' ),
            'deprecated array_prepend'  => array( '{array(2)|array_prepend(1)|implode(",")}', '{$l|array_prepend($v)|implode(",")}', array( 'l' => array( 2 ), 'v' => 1 ), '1,2' ),
            'deprecated array_merge'    => array( '{array(1)|array_merge(array(2))|implode(",")}', '{$l|array_merge($m)|implode(",")}', array( 'l' => array( 1 ), 'm' => array( 2 ) ), '1,2' ),
            'append several'            => array( '{array(1)|append(2,3)|implode(",")}', '{$l|append($a,$b)|implode(",")}', array( 'l' => array( 1 ), 'a' => 2, 'b' => 3 ), '1,2,3' ),
            'prepend several'           => array( '{array(3)|prepend(1,2)|implode(",")}', '{$l|prepend($a,$b)|implode(",")}', array( 'l' => array( 3 ), 'a' => 1, 'b' => 2 ), '1,2,3' ),
            'merge hashes'              => array( '{foreach hash("a",1)|merge(hash("b",2)) as $k => $v}{$k}{$v}{/foreach}', '{foreach $h|merge($g) as $k => $v}{$k}{$v}{/foreach}', array( 'h' => array( 'a' => 1 ), 'g' => array( 'b' => 2 ) ), 'a1b2' ),
            'merge overrides key'       => array( '{hash("a",1)|merge(hash("a",2)).a}', '{$h|merge($g).a}', array( 'h' => array( 'a' => 1 ), 'g' => array( 'a' => 2 ) ), '2' ),
            'hash lookup'               => array( '{hash("x","y").x}', '{hash($k,$v).x}', array( 'k' => 'x', 'v' => 'y' ), 'y' ),
            'array count'               => array( '{array(1,2,3,4)|count}', '{array($a,$b,$c,$d)|count}', array( 'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4 ), '4' ),
            'contains missing'          => array( '{if array(1,2)|contains(3)}y{else}n{/if}', '{if $l|contains($v)}y{else}n{/if}', array( 'l' => array( 1, 2 ), 'v' => 3 ), 'n' ),
            'contains string missing'   => array( '{if "abc"|contains("z")}y{else}n{/if}', '{if $s|contains($v)}y{else}n{/if}', array( 's' => 'abc', 'v' => 'z' ), 'n' ),
            'compare ignores order'     => array( '{if array(1,2)|compare(array(2,1))}y{else}n{/if}', '{if $l|compare($m)}y{else}n{/if}', array( 'l' => array( 1, 2 ), 'm' => array( 2, 1 ) ), 'y' ),
            'compare different'         => array( '{if array(1,2)|compare(array(1,3))}y{else}n{/if}', '{if $l|compare($m)}y{else}n{/if}', array( 'l' => array( 1, 2 ), 'm' => array( 1, 3 ) ), 'n' ),
            'compare strings'           => array( '{if "ab"|compare("ab")}y{else}n{/if}', '{if $s|compare($t)}y{else}n{/if}', array( 's' => 'ab', 't' => 'ab' ), 'y' ),
            'extract without length'    => array( '{"abcdef"|extract(3)}', '{$s|extract($n)}', array( 's' => 'abcdef', 'n' => 3 ), 'def' ),
            'extract array no length'   => array( '{array(1,2,3,4)|extract(2)|implode(",")}', '{$l|extract($n)|implode(",")}', array( 'l' => array( 1, 2, 3, 4 ), 'n' => 2 ), '3,4' ),
            'extract_left array'        => array( '{array(1,2,3)|extract_left(2)|implode(",")}', '{$l|extract_left($n)|implode(",")}', array( 'l' => array( 1, 2, 3 ), 'n' => 2 ), '1,2' ),
            'extract_right array'       => array( '{array(1,2,3)|extract_right(2)|implode(",")}', '{$l|extract_right($n)|implode(",")}', array( 'l' => array( 1, 2, 3 ), 'n' => 2 ), '2,3' ),
            'begins_with false'         => array( '{if "abc"|begins_with("bc")}y{else}n{/if}', '{if $s|begins_with($t)}y{else}n{/if}', array( 's' => 'abc', 't' => 'bc' ), 'n' ),
            'begins_with array'         => array( '{if array(1,2,3)|begins_with(1,2)}y{else}n{/if}', '{if $l|begins_with($a,$b)}y{else}n{/if}', array( 'l' => array( 1, 2, 3 ), 'a' => 1, 'b' => 2 ), 'y' ),
            'ends_with array'           => array( '{if array(1,2,3)|ends_with(2,3)}y{else}n{/if}', '{if $l|ends_with($a,$b)}y{else}n{/if}', array( 'l' => array( 1, 2, 3 ), 'a' => 2, 'b' => 3 ), 'y' ),
            'ends_with array false'     => array( '{if array(1,2,3)|ends_with(1)}y{else}n{/if}', '{if $l|ends_with($a)}y{else}n{/if}', array( 'l' => array( 1, 2, 3 ), 'a' => 1 ), 'n' ),
            'implode empty'             => array( '{array()|implode(",")}x', '{$l|implode(",")}x', array( 'l' => array() ), 'x' ),
            'explode single'            => array( '{"abc"|explode(",")|count}', '{$s|explode(",")|count}', array( 's' => 'abc' ), '1' ),
            'explode keeps empties'     => array( '{"a,,b"|explode(",")|count}', '{$s|explode(",")|count}', array( 's' => 'a,,b' ), '3' ),
            'repeat array'              => array( '{array(1,2)|repeat(2)|implode(",")}', '{$l|repeat($n)|implode(",")}', array( 'l' => array( 1, 2 ), 'n' => 2 ), '1,2,1,2' ),
            'repeat zero'               => array( '{"x"|repeat(0)}e', '{$s|repeat($n)}e', array( 's' => 'x', 'n' => 0 ), 'e' ),
            'insert several'            => array( '{array(1,4)|insert(1,2,3)|implode(",")}', '{$l|insert($i,$a,$b)|implode(",")}', array( 'l' => array( 1, 4 ), 'i' => 1, 'a' => 2, 'b' => 3 ), '1,2,3,4' ),
            'insert into string'        => array( '{"ad"|insert(1,"bc")}', '{$s|insert($i,$t)}', array( 's' => 'ad', 'i' => 1, 't' => 'bc' ), 'abcd' ),
            'remove from string'        => array( '{"abcd"|remove(1,2)}', '{$s|remove($i,$n)}', array( 's' => 'abcd', 'i' => 1, 'n' => 2 ), 'ad' ),
            'remove string, offset var' => array( '{"abcd"|remove(1)}', '{"abcd"|remove($i)}', array( 'i' => 1 ), 'acd' ),
            'remove string, both vars'  => array( '{"abcd"|remove(1,2)}', '{"abcd"|remove($i,$n)}', array( 'i' => 1, 'n' => 2 ), 'ad' ),
            'remove array, offset var'  => array( '{array(1,2,3)|remove(1)|implode(",")}', '{array(1,2,3)|remove($i)|implode(",")}', array( 'i' => 1 ), '1,3' ),
            'remove array, length var'  => array( '{array(1,2,3,4)|remove(1,2)|implode(",")}', '{array(1,2,3,4)|remove($i,$n)|implode(",")}', array( 'i' => 1, 'n' => 2 ), '1,4' ),
            'remove one element'        => array( '{array(1,2,3)|remove(0)|implode(",")}', '{$l|remove($i)|implode(",")}', array( 'l' => array( 1, 2, 3 ), 'i' => 0 ), '2,3' ),
            'replace several'           => array( '{array(1,2,3,4)|replace(1,2,8,9)|implode(",")}', '{$l|replace($i,$n,$a,$b)|implode(",")}', array( 'l' => array( 1, 2, 3, 4 ), 'i' => 1, 'n' => 2, 'a' => 8, 'b' => 9 ), '1,8,9,4' ),
            'unique strings'            => array( '{array("a","b","a")|unique|implode(",")}', '{$l|unique|implode(",")}', array( 'l' => array( 'a', 'b', 'a' ) ), 'a,b' ),
            'array_sum floats'          => array( '{array(0.5,1.5)|array_sum}', '{$l|array_sum}', array( 'l' => array( 0.5, 1.5 ) ), '2' ),
            'array_sum empty'           => array( '{array()|array_sum}', '{$l|array_sum}', array( 'l' => array() ), '0' ),
            'reverse keeps values'      => array( '{array("x","y")|reverse.0}', '{$l|reverse.0}', array( 'l' => array( 'x', 'y' ) ), 'y' ),

            // types
            'is_boolean'                => array( '{if is_boolean(true())}y{else}n{/if}', '{if is_boolean($a)}y{else}n{/if}', array( 'a' => false ), 'y' ),
            'is_boolean of int'         => array( '{if is_boolean(1)}y{else}n{/if}', '{if is_boolean($a)}y{else}n{/if}', array( 'a' => 1 ), 'n' ),
            'is_float'                  => array( '{if is_float(1.5)}y{else}n{/if}', '{if is_float($a)}y{else}n{/if}', array( 'a' => 1.5 ), 'y' ),
            'is_float of int'           => array( '{if is_float(1)}y{else}n{/if}', '{if is_float($a)}y{else}n{/if}', array( 'a' => 1 ), 'n' ),
            'is_integer of float'       => array( '{if is_integer(1.5)}y{else}n{/if}', '{if is_integer($a)}y{else}n{/if}', array( 'a' => 1.5 ), 'n' ),
            'is_numeric of word'        => array( '{if is_numeric("abc")}y{else}n{/if}', '{if is_numeric($a)}y{else}n{/if}', array( 'a' => 'abc' ), 'n' ),
            'is_string of int'          => array( '{if is_string(1)}y{else}n{/if}', '{if is_string($a)}y{else}n{/if}', array( 'a' => 1 ), 'n' ),
            'is_array of string'        => array( '{if is_array("a")}y{else}n{/if}', '{if is_array($a)}y{else}n{/if}', array( 'a' => 'a' ), 'n' ),
            'is_null of zero'           => array( '{if is_null(0)}y{else}n{/if}', '{if is_null($a)}y{else}n{/if}', array( 'a' => 0 ), 'n' ),
            'is_object of array'        => array( '{if is_object(array())}y{else}n{/if}', '{if is_object($a)}y{else}n{/if}', array( 'a' => array() ), 'n' ),
            'get_type integer'          => array( '{get_type(5)}', '{get_type($a)}', array( 'a' => 5 ), 'integer' ),
            'get_type float'            => array( '{get_type(1.5)}', '{get_type($a)}', array( 'a' => 1.5 ), 'double' ),
            'get_type boolean'          => array( '{get_type(true())}', '{get_type($a)}', array( 'a' => true ), 'boolean[true]' ),
            'get_type false'            => array( '{get_type(false())}', '{get_type($a)}', array( 'a' => false ), 'boolean[false]' ),
            'get_type empty string'     => array( '{get_type("")}', '{get_type($a)}', array( 'a' => '' ), 'string[0]' ),

            // strings
            'wash xhtml mode'           => array( '{"<a>"|wash("xhtml")}', '{$s|wash($m)}', array( 's' => '<a>', 'm' => 'xhtml' ), '&lt;a&gt;' ),
            'wash javascript mode'      => array( "{\"it's\"|wash(\"javascript\")}", '{$s|wash($m)}', array( 's' => "it's", 'm' => 'javascript' ), 'it\\047s' ),
            'wash pdf mode'             => array( '{"a b&amp;c"|wash("pdf")}', '{$s|wash($m)}', array( 's' => 'a b&amp;c', 'm' => 'pdf' ), 'a<C:callSpace>b&c' ),
            'wash email mode'           => array( '{"x@y.z"|wash("email")}', '{$s|wash($m)}', array( 's' => 'x@y.z', 'm' => 'email' ), null ),
            'wash plain text'           => array( '{"plain"|wash}', '{$s|wash}', array( 's' => 'plain' ), 'plain' ),
            'shorten custom sequence'   => array( '{"hello world"|shorten(7,"..")}', '{$s|shorten($n,$q)}', array( 's' => 'hello world', 'n' => 7, 'q' => '..' ), 'hello..' ),
            'shorten exact length'      => array( '{"hello"|shorten(5)}', '{$s|shorten($n)}', array( 's' => 'hello', 'n' => 5 ), 'hello' ),
            'shorten unknown trim type' => array( '{"hello world"|shorten(8,"...","left")}', '{$s|shorten($n,$q,$t)}', array( 's' => 'hello world', 'n' => 8, 'q' => '...', 't' => 'left' ), 'hello...' ),
            'shorten in the middle'     => array( '{"abcdefghij"|shorten(7,"...","middle")}', '{$s|shorten($n,$q,$t)}', array( 's' => 'abcdefghij', 'n' => 7, 'q' => '...', 't' => 'middle' ), 'ab...ij' ),
            'pad with default'          => array( '{"ab"|pad(4)}|', '{$s|pad($n)}|', array( 's' => 'ab', 'n' => 4 ), 'ab  |' ),
            'pad longer input'          => array( '{"abcdef"|pad(3,"-")}', '{$s|pad($n,$c)}', array( 's' => 'abcdef', 'n' => 3, 'c' => '-' ), 'abcdef' ),
            'trim with chars'           => array( '{"xxaxx"|trim("x")}', '{$s|trim($c)}', array( 's' => 'xxaxx', 'c' => 'x' ), 'a' ),
            'simplify with char'        => array( '{"a--b---c"|simplify("-")}', '{$s|simplify($c)}', array( 's' => 'a--b---c', 'c' => '-' ), 'a-b-c' ),
            'upcase multibyte'          => array( '{"äöü"|upcase}', '{$s|upcase}', array( 's' => 'äöü' ), 'ÄÖÜ' ),
            'downcase multibyte'        => array( '{"ÄÖÜ"|downcase}', '{$s|downcase}', array( 's' => 'ÄÖÜ' ), 'äöü' ),
            'count_chars multibyte'     => array( '{"äöü"|count_chars}', '{$s|count_chars}', array( 's' => 'äöü' ), '3' ),
            'count_words empty'         => array( '{""|count_words}', '{$s|count_words}', array( 's' => '' ), '0' ),
            'upfirst empty'             => array( '{""|upfirst}x', '{$s|upfirst}x', array( 's' => '' ), 'x' ),
            'chr single'                => array( '{array(97)|chr}', '{$l|chr}', array( 'l' => array( 97 ) ), 'a' ),
            'ord several'               => array( '{"AB"|ord|implode(",")}', '{$s|ord|implode(",")}', array( 's' => 'AB' ), '65,66' ),
            'wrap long word'            => array( '{"aaaa bb"|wrap(4)}', '{$s|wrap($n)}', array( 's' => 'aaaa bb', 'n' => 4 ), "aaaa\nbb" ),

            // text
            'indent tab'                => array( '{"x"|indent(2,"tab")}', '{$s|indent($n,$t)}', array( 's' => 'x', 'n' => 2, 't' => 'tab' ), "\t\tx" ),
            'indent custom'             => array( '{"x"|indent(3,"custom","-")}', '{$s|indent($n,$t,$c)}', array( 's' => 'x', 'n' => 3, 't' => 'custom', 'c' => '-' ), '---x' ),
            'indent every line'         => array( "{\"a\nb\"|indent(1)}", '{$s|indent($n)}', array( 's' => "a\nb", 'n' => 1 ), " a\n b" ),
            'concat numbers'            => array( '{concat(1,2,3)}', '{concat($a,$b,$c)}', array( 'a' => 1, 'b' => 2, 'c' => 3 ), '123' ),
            'concat single'             => array( '{concat("a")}', '{concat($a)}', array( 'a' => 'a' ), 'a' ),

            // units
            'si megabytes'              => array( '{1048576|si("byte")}', '{$a|si($u)}', array( 'a' => 1048576, 'u' => 'byte' ), '1.00 MB' ),
            'si zero bytes'             => array( '{0|si("byte")}', '{$a|si($u)}', array( 'a' => 0, 'u' => 'byte' ), '0 B' ),
            'si fraction'               => array( '{1536|si("byte")}', '{$a|si($u)}', array( 'a' => 1536, 'u' => 'byte' ), '1.50 kB' ),

            // digests
            'md5 empty'                 => array( '{""|md5}', '{$s|md5}', array( 's' => '' ), md5( '' ) ),
            'crc32 of text'             => array( '{"The quick brown fox"|crc32}', '{$s|crc32}', array( 's' => 'The quick brown fox' ), (string)crc32( 'The quick brown fox' ) ),
            'rot13 twice'               => array( '{"Abc"|rot13|rot13}', '{$s|rot13|rot13}', array( 's' => 'Abc' ), 'Abc' ),
            'rot13 keeps digits'        => array( '{"a1z"|rot13}', '{$s|rot13}', array( 's' => 'a1z' ), 'n1m' ),

            // control
            'cond first'                => array( '{cond(true(),"a","b")}', '{cond($t,$a,$b)}', array( 't' => true, 'a' => 'a', 'b' => 'b' ), 'a' ),
            'cond with zero condition'  => array( '{cond(0,"a","b")}', '{cond($t,$a,$b)}', array( 't' => 0, 'a' => 'a', 'b' => 'b' ), 'b' ),
            'first_set first'           => array( '{first_set("a","b")}', '{first_set($a,$b)}', array( 'a' => 'a', 'b' => 'b' ), 'a' ),
            'first_set keeps a zero'    => array( '{first_set(0,"b")}', '{first_set($a,$b)}', array( 'a' => 0, 'b' => 'b' ), '0' ),
        );
    }

    public static function renderProvider()
    {
        $rows = array();
        foreach ( self::caseTable() as $name => $case )
        {
            list( $literal, $withVariables, $variables, $expected ) = $case;
            if ( $expected === null )
                continue;
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

    /**
     * The email mode of wash spells out the address (the replacements come from template.ini, which a bare template
     * engine does not have); whatever it produces, the four renderings agree and the address is not output verbatim.
     */
    public function testWashEmailIsTheSameInEveryRendering()
    {
        $outputs = array();
        foreach ( array( false, true ) as $compiled )
        {
            $outputs[] = $this->render( '{"x@y.z"|wash("email")}', array(), $compiled )[0];
            $outputs[] = $this->render( '{$s|wash($m)}', array( 's' => 'x@y.z', 'm' => 'email' ), $compiled )[0];
        }
        $this->assertCount( 1, array_unique( $outputs ), implode( ' | ', $outputs ) );
    }

    public static function modeProvider()
    {
        return array( 'interpreted' => array( false ), 'compiled' => array( true ) );
    }

    public static function chooseOutOfRangeProvider()
    {
        $rows = array();
        $sources = array(
            'two choices, variable index'   => array( '[{$i|choose("a","b")}]', array( 'i' => 5 ) ),
            'three choices, variable index' => array( '[{$i|choose("a","b","c")}]', array( 'i' => 3 ) ),
            'negative variable index'       => array( '[{$i|choose("a","b","c")}]', array( 'i' => -1 ) ),
            'literal index one past'        => array( '[{2|choose("a","b")}]', array() ),
        );
        foreach ( $sources as $name => $source )
        {
            $rows["$name, interpreted"] = array( $source[0], $source[1], false );
            $rows["$name, compiled"] = array( $source[0], $source[1], true );
        }
        return $rows;
    }

    /**
     * An index outside the choices is an error and chooses nothing. The compiled template took the second choice for
     * any true index when there were two choices, and fell through the switch without an error for three or more.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('chooseOutOfRangeProvider')]
    public function testChooseOutOfRangeIsAnError( $source, $variables, $compiled )
    {
        list( $output, $errors, $warnings ) = $this->render( $source, $variables, $compiled );
        $this->assertNotEmpty( array_merge( $errors, $warnings ) );
        $this->assertSame( '[]', $output );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('modeProvider')]
    public function testEqWithoutParametersWarns( $compiled )
    {
        list( $output, $errors, $warnings ) = $this->render( '{if eq()}y{else}n{/if}', array(), $compiled );
        $this->assertNotEmpty( array_merge( $errors, $warnings ) );
        $this->assertSame( 'n', $output );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('modeProvider')]
    public function testGetClassOfAnObject( $compiled )
    {
        list( $output, $errors ) = $this->render( '{get_class($o)}|{if is_object($o)}y{/if}', array( 'o' => new ArrayObject() ), $compiled );
        $this->assertSame( array(), $errors );
        $this->assertSame( 'arrayobject|y', strtolower( $output ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('modeProvider')]
    public function testRandStaysInRange( $compiled )
    {
        list( $output, $errors ) = $this->render( '{for 1 to 20 as $i}{rand(3,5)},{/for}', array(), $compiled );
        $this->assertSame( array(), $errors );
        foreach ( explode( ',', rtrim( $output, ',' ) ) as $n )
        {
            $this->assertGreaterThanOrEqual( 3, (int)$n );
            $this->assertLessThanOrEqual( 5, (int)$n );
        }
    }
}
