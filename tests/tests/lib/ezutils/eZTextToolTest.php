<?php
/**
 * eZTextTool (lib/ezutils): highlightHTML(), concat(), concatDelimited() and arrayFlatten(), called statically as
 * documented. They were instance methods, and a static call of an instance method is an error since PHP 8.
 *
 * Plain PHP, no kernel bootstrap.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezutils
 */

class eZTextToolTest extends PHPUnit\Framework\TestCase
{
    public static function setUpBeforeClass(): void
    {
        require_once dirname( __DIR__, 4 ) . '/lib/ezutils/classes/eztexttool.php';
    }

    public function testHighlightHtmlEscapesAndMarksTags()
    {
        $this->assertSame( "<font color='red'>&lt;b&gt;</font>x<font color='red'>&lt;/b&gt;</font>", eZTextTool::highlightHTML( '<b>x</b>' ) );
        $this->assertSame( 'a &amp; b', eZTextTool::highlightHTML( 'a &amp; b' ) );
        $this->assertSame( "1 &lt; 2", eZTextTool::highlightHTML( '1 < 2' ) );
    }

    public static function flattenProvider()
    {
        return array(
            'flat'              => array( array( 1, 2, 3 ), array( 1, 2, 3 ) ),
            'empty'             => array( array(), array() ),
            'nested in middle'  => array( array( 1, array( 2, 3 ), 4 ), array( 1, 2, 3, 4 ) ),
            'nested first'      => array( array( array( 1 ), 2, array( 3 ) ), array( 1, 2, 3 ) ),
            'deep'              => array( array( 1, array( 2, array( 3, array( 4 ) ) ), 5 ), array( 1, 2, 3, 4, 5 ) ),
            'empty sub-arrays'  => array( array( array(), 'a', array() ), array( 'a' ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('flattenProvider')]
    public function testArrayFlattenKeepsTheOrder( $input, $expected )
    {
        $this->assertSame( $expected, eZTextTool::arrayFlatten( $input ) );
    }

    public function testConcat()
    {
        $this->assertNull( eZTextTool::concat() );
        $this->assertSame( 'abc', eZTextTool::concat( 'a', 'b', 'c' ) );
        $this->assertSame( 'abcd', eZTextTool::concat( 'a', array( 'b', array( 'c' ) ), 'd' ) );
        $this->assertSame( '12', eZTextTool::concat( 1, 2 ) );
    }

    public function testConcatDelimited()
    {
        $this->assertNull( eZTextTool::concatDelimited() );
        $this->assertNull( eZTextTool::concatDelimited( ',' ), 'a delimiter alone gives nothing' );
        $this->assertSame( 'a', eZTextTool::concatDelimited( ',', 'a' ) );
        $this->assertSame( 'a, b, c', eZTextTool::concatDelimited( ', ', 'a', array( 'b', 'c' ) ) );
    }

    public function testInstanceCallsStillWork()
    {
        $tool = new eZTextTool();
        $this->assertSame( 'ab', $tool->concat( 'a', 'b' ) );
        $this->assertNull( $tool->highlightPHP() );
    }
}
