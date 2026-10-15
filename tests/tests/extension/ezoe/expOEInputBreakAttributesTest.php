<?php
/**
 * The online editor's input parser on a <br> that carries attributes. Its output tag declares
 * 'attributes' => false, and eZOEInputParser::setAttributes() ran foreach over that value: one PHP warning
 * "foreach() argument must be of type array|object, false given" per such <br>. The handler writes every empty
 * table cell as <p><br data-mce-bogus="1"/></p>, so an editor that posts an unchanged text back (an engine switch,
 * a save without an edit) warned once per empty cell, before the page's doctype where errors are displayed.
 * No database: the handler and the parser need only the settings.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

// The handler and parser of this tree, not ones an autoload map of another tree points at
foreach ( array( 'eZOEXMLInput' => 'ezoexmlinput.php', 'eZOEInputParser' => 'ezoeinputparser.php' ) as $expOEClass => $expOEFile )
{
    $expOEPath = dirname( __DIR__, 4 ) . '/extension/ezoe/ezxmltext/handlers/input/' . $expOEFile;
    if ( !class_exists( $expOEClass, false ) && is_file( $expOEPath ) )
        require_once $expOEPath;
}
unset( $expOEClass, $expOEFile, $expOEPath );

class expOEInputBreakAttributesTest extends PHPUnit\Framework\TestCase
{
    const SECTION = '<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/">';

    // a table of five empty cells and one with text, as the editor stores it
    const TABLE = '<paragraph><table width="100%" border="1"><tr><th/><th/></tr><tr><td/><td><paragraph>x</paragraph></td></tr><tr><td/><td/></tr></table></paragraph>';

    protected function setUp(): void
    {
        if ( !class_exists( 'eZOEInputParser' ) || !class_exists( 'eZOEXMLInput' ) )
            $this->markTestSkipped( 'ezoe is not loaded' );
    }

    /**
     * Parses editor HTML and returns the stored XML and the PHP warnings and notices raised meanwhile.
     */
    private function parse( $html )
    {
        $raised = array();
        set_error_handler( function( $number, $message, $file, $line ) use ( &$raised )
        {
            $raised[] = $message . ' (' . basename( $file ) . ':' . $line . ')';
            return true;
        }, E_WARNING | E_NOTICE | E_USER_WARNING | E_USER_NOTICE );
        try
        {
            $parser = new eZOEInputParser();
            $dom = $parser->process( $html );
        }
        finally
        {
            restore_error_handler();
        }
        $this->assertInstanceOf( 'DOMDocument', $dom, 'the parser gave no document for: ' . $html );
        return array( $dom->saveXML( $dom->documentElement ), $raised );
    }

    public function testTheEditorHtmlOfATableWithEmptyCellsParsesWithoutWarning()
    {
        $handler = new eZOEXMLInput( '<?xml version="1.0" encoding="utf-8"?>' . self::SECTION . self::TABLE . '</section>', false, null );
        $html = htmlspecialchars_decode( $handler->attribute( 'input_xml' ) );
        // the precondition of the warning: the handler's own markup of an empty cell
        $this->assertSame( 5, substr_count( $html, '<br data-mce-bogus="1"/>' ) );

        list( $xml, $raised ) = $this->parse( $html );
        $this->assertSame( array(), $raised );
        $this->assertSame( self::SECTION . self::TABLE . '</section>', $xml );
    }

    public static function breaks()
    {
        return array(
            'bogus'       => array( '<p>a<br data-mce-bogus="1">b</p>' ),
            'bogus empty' => array( '<p>a<br data-mce-bogus="1"/>b</p>' ),
            'class'       => array( '<p>a<br class="x">b</p>' ),
            'clear'       => array( '<p>a<br clear="all">b</p>' ),
        );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'breaks' )]
    public function testABreakWithAttributesIsALineBreakWithoutWarning( $html )
    {
        list( $xml, $raised ) = $this->parse( $html );
        $this->assertSame( array(), $raised );
        list( $plain ) = $this->parse( '<p>a<br>b</p>' );
        $this->assertSame( $plain, $xml );
    }

    public function testCellAttributesAreStillMappedInAnyCase()
    {
        // the case-insensitive mapping the override exists for ("colSpan" of old Internet Explorer)
        list( $xml, $raised ) = $this->parse( '<table border="0"><tbody><tr><td colSpan="2" WIDTH="50%">c</td></tr></tbody></table>' );
        $this->assertSame( array(), $raised );
        $this->assertStringContainsString( 'xhtml:colspan="2"', $xml );
        $this->assertStringContainsString( 'xhtml:width="50%"', $xml );
    }
}
