<?php
/**
 * Characters above U+FFFF (XML 1.0 #x10000-#x10FFFF) survive the input handler and the input parser.
 * Before, eZXMLInputHandler removed them when the stored XML was opened in an editor ("<mi>&#x1D465;</mi>" in a
 * MathML attribute became "<mi></mi>") and eZXMLInputParser::process() made each of them a space when the editor
 * posted ("a &#x1F600; b" was stored as "a b"). Characters XML does not allow are still removed. No database.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class eZXMLInputSupplementaryCharactersTest extends PHPUnit\Framework\TestCase
{
    const HEAD = '<?xml version="1.0" encoding="utf-8"?><section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/">';

    /** @var array saved [file, block, name, value or null] */
    protected $saved = array();
    protected $savedDB = null;
    protected $hadDB = false;

    public function setUp(): void
    {
        // The tests below are about the handler and the parser; whether the database can store the characters is
        // tested on its own further down
        $this->setSetting( 'ezxml.ini', 'InputSettings', 'SupplementaryCharacters', 'enabled' );
    }

    public function tearDown(): void
    {
        foreach ( array_reverse( $this->saved ) as $setting )
        {
            list( $file, $block, $name, $value ) = $setting;
            $ini = eZINI::instance( $file );
            if ( $value === null )
                $ini->removeSetting( $block, $name );
            else
                $ini->setVariable( $block, $name, $value );
        }
        $this->saved = array();
        if ( $this->hadDB )
            $GLOBALS['eZDBGlobalInstance'] = $this->savedDB;
        $this->hadDB = false;
        $this->savedDB = null;
    }

    protected function setSetting( $file, $block, $name, $value )
    {
        $ini = eZINI::instance( $file );
        $this->saved[] = array( $file, $block, $name, $ini->hasVariable( $block, $name ) ? $ini->variable( $block, $name ) : null );
        $ini->setVariable( $block, $name, $value );
    }

    /** Lets supplementaryCharactersAllowed() read site.ini instead of a database instance */
    protected function withoutDatabaseInstance()
    {
        if ( array_key_exists( 'eZDBGlobalInstance', $GLOBALS ) )
        {
            $this->hadDB = true;
            $this->savedDB = $GLOBALS['eZDBGlobalInstance'];
            unset( $GLOBALS['eZDBGlobalInstance'] );
        }
    }

    public static function databases()
    {
        return array(
            'MySQL, utf-8 (connects as utf8)' => array( 'ezmysqli', 'utf-8', false ),
            'MySQL, utf8mb4'                  => array( 'ezmysqli', 'utf8mb4', true ),
            'old MySQL driver, utf-8'         => array( 'ezmysql', 'utf-8', false ),
            'SQLite'                          => array( 'sqlite3', 'utf-8', true ),
            'PostgreSQL'                      => array( 'ezpostgresql', 'utf-8', true ),
        );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'databases' )]
    public function testAutoKeepsThemOnlyWhereTheDatabaseCanStoreThem( $implementation, $charset, $expected )
    {
        $this->setSetting( 'ezxml.ini', 'InputSettings', 'SupplementaryCharacters', 'auto' );
        $this->setSetting( 'site.ini', 'DatabaseSettings', 'DatabaseImplementation', $implementation );
        $this->setSetting( 'site.ini', 'DatabaseSettings', 'Charset', $charset );
        $this->withoutDatabaseInstance();
        $this->assertSame( $expected, eZXMLInputHandler::supplementaryCharactersAllowed() );
    }

    public function testDisabledReplacesThemAsBefore()
    {
        $this->setSetting( 'ezxml.ini', 'InputSettings', 'SupplementaryCharacters', 'disabled' );
        $handler = new eZXMLInputHandler( "a \u{1F600} b", false, null );
        $this->assertSame( 'a  b', $handler->XMLData );
        $parser = new eZSimplifiedXMLInputParser( 2, eZXMLInputParser::ERROR_NONE, eZXMLInputParser::ERROR_NONE, true );
        $document = $parser->process( "<paragraph>a \u{1F600} b</paragraph>" );
        $this->assertSame( 'a b', $document->documentElement->textContent );
    }

    public function testInvalidUtf8DoesNotLoseTheWholeText()
    {
        $count = 0;
        $result = eZXMLInputHandler::replaceCharactersXMLDoesNotAllow( "a\xC3b \u{1F600} c", '', $count );
        $this->assertSame( "a\u{FFFD}b \u{1F600} c", $result );
        $this->assertSame( 1, $count );
        $handler = new eZXMLInputHandler( "<p>x\xFFy</p>", false, null );
        $this->assertSame( "<p>x\u{FFFD}y</p>", $handler->XMLData );
    }

    public static function supplementaryTexts()
    {
        return array(
            'emoji'              => array( "a \u{1F600} b" ),
            'mathematical italic' => array( "\u{1D465} + 1" ),
            'double-struck'      => array( "\u{1D538} = \\mathbb{A}" ),
            'last plane'         => array( "x\u{10FFFD}y" ),
        );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'supplementaryTexts' )]
    public function testTheInputHandlerKeepsThemInText( $text )
    {
        $xml = self::HEAD . '<paragraph>' . $text . '</paragraph></section>';
        $handler = new eZXMLInputHandler( $xml, false, null );
        $this->assertSame( $xml, $handler->XMLData );
    }

    public function testTheInputHandlerKeepsThemInACustomAttribute()
    {
        $xml = self::HEAD . '<paragraph><custom name="math_mathml" custom:mathml="&lt;math&gt;&lt;mi&gt;' . "\u{1D465}" .
               '&lt;/mi&gt;&lt;/math&gt;">x</custom></paragraph></section>';
        $handler = new eZXMLInputHandler( $xml, false, null );
        $this->assertSame( $xml, $handler->XMLData );
    }

    public function testTheInputHandlerStillRemovesWhatXmlDoesNotAllow()
    {
        $handler = new eZXMLInputHandler( "a\x01b\u{FFFE}c", false, null );
        $this->assertSame( 'abc', $handler->XMLData );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'supplementaryTexts' )]
    public function testTheInputParserKeepsThem( $text )
    {
        $parser = new eZSimplifiedXMLInputParser( 2, eZXMLInputParser::ERROR_NONE, eZXMLInputParser::ERROR_NONE, true );
        $document = $parser->process( '<paragraph>' . $text . '</paragraph>' );
        $this->assertInstanceOf( DOMDocument::class, $document );
        $this->assertSame( $text, $document->documentElement->textContent );
        $this->assertSame( array(), $parser->getMessages() );
    }

    public function testTheInputParserStillReplacesWhatXmlDoesNotAllow()
    {
        $parser = new eZSimplifiedXMLInputParser( 2, eZXMLInputParser::ERROR_NONE, eZXMLInputParser::ERROR_NONE, true );
        $document = $parser->process( "<paragraph>a\x01b</paragraph>" );
        $this->assertSame( 'a b', $document->documentElement->textContent );
        $this->assertCount( 1, $parser->getMessages() );
    }
}
