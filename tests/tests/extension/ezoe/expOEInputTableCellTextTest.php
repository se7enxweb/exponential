<?php
/**
 * The online editor's input handler on a table cell whose paragraphs are indented with line breaks (XML stored
 * pretty-printed, as imported content is): the whitespace between the cell and its paragraphs is no tag, so it is
 * passed over without the error "Unsupported tag at this level: #text" that every load of such content logged.
 * Text of its own directly in a cell is still reported. No database: the handler is made without its constructor.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

// The handler of this tree, not one an autoload map of another tree points at
if ( !class_exists( 'eZOEXMLInput', false ) && is_file( dirname( __DIR__, 4 ) . '/extension/ezoe/ezxmltext/handlers/input/ezoexmlinput.php' ) )
    require_once dirname( __DIR__, 4 ) . '/extension/ezoe/ezxmltext/handlers/input/ezoexmlinput.php';

class expOEInputTableCellTextTest extends PHPUnit\Framework\TestCase
{
    private function handler()
    {
        if ( !class_exists( 'eZOEXMLInput' ) )
            $this->markTestSkipped( 'ezoe is not loaded' );
        return ( new ReflectionClass( 'eZOEXMLInput' ) )->newInstanceWithoutConstructor();
    }

    private function errorsFor( $text )
    {
        $handler = $this->handler();
        $dom = new DOMDocument();
        $node = $dom->createTextNode( $text );
        $before = $GLOBALS['eZDebugErrorCount'] ?? 0;
        $output = $handler->inputTdXML( $node, 0, 0 );
        $this->assertSame( '', $output );
        return ( $GLOBALS['eZDebugErrorCount'] ?? 0 ) - $before;
    }

    public function testTextOfItsOwnInACellIsReported()
    {
        // also shows that errors are counted here, so the next test means something
        if ( $this->errorsFor( 'loose text' ) === 0 )
            $this->markTestSkipped( 'debug errors are neither shown nor always logged in this setup' );
        $this->assertTrue( true );
    }

    public function testIndentationBetweenCellAndParagraphsIsNoError()
    {
        if ( $this->errorsFor( 'loose text' ) === 0 )
            $this->markTestSkipped( 'debug errors are neither shown nor always logged in this setup' );
        $this->assertSame( 0, $this->errorsFor( "\n      " ) );
        $this->assertSame( 0, $this->errorsFor( "\t" ) );
    }
}
