<?php
/**
 * What a formula keeps when it is opened in the Online Editor and saved again (eZOEXMLInput::inputXML() for
 * loading, eZOEInputParser for saving). Measured losses before the fix:
 *  - characters above U+FFFF were removed on load and replaced by a space on save ("a <emoji> b" -> "a b");
 *  - TeX source with a % comment: "a % comment<line break>+ b" became "a % comment + b" (the rest commented out);
 *  - a line break in a custom attribute vanished ("<mi>a</mi>&#10;<mo>" -> "<mi>a</mi><mo>");
 *  - a leading space in TeX source was stored as U+00A0.
 * No database: the handler and the parser run on in-memory settings, as the editor's save does.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

// The handler of this tree, not one an autoload map of another tree points at
if ( !class_exists( 'eZOEXMLInput', false ) && is_file( dirname( __DIR__, 4 ) . '/extension/ezoe/ezxmltext/handlers/input/ezoexmlinput.php' ) )
    require_once dirname( __DIR__, 4 ) . '/extension/ezoe/ezxmltext/handlers/input/ezoexmlinput.php';

class expOEFormulaTextTest extends PHPUnit\Framework\TestCase
{
    const HEAD = '<?xml version="1.0" encoding="utf-8"?>' . "\n" . '<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/">';

    protected $saved = array();
    protected $savedSchema = null;
    protected $savedSupplementary = null;

    public function setUp(): void
    {
        if ( !class_exists( 'eZOEXMLInput' ) || !class_exists( 'eZOEInputParser' ) )
            $this->markTestSkipped( 'ezoe is not loaded' );
        $ini = eZINI::instance( 'content.ini' );
        $settings = array(
            array( 'CustomTagSettings', 'AvailableCustomTags', array( 'math_latex', 'math_mathml', 'underline' ) ),
            array( 'CustomTagSettings', 'IsInline', array( 'math_latex' => 'true', 'math_mathml' => 'true', 'underline' => 'true' ) ),
            array( 'CustomTagSettings', 'TeXSource', array( 'math_latex' => 'true' ) ),
            array( 'math_latex', 'CustomAttributes', array( 'display', 'alt' ) ),
            array( 'math_mathml', 'CustomAttributes', array( 'display', 'mathml' ) ),
        );
        foreach ( $settings as $setting )
        {
            list( $block, $name, $value ) = $setting;
            $this->saved[] = array( $block, $name, $ini->hasVariable( $block, $name ) ? $ini->variable( $block, $name ) : null );
            $ini->setVariable( $block, $name, $value );
        }
        $this->savedSchema = $GLOBALS['eZXMLSchemaGlobalInstance'] ?? null;
        unset( $GLOBALS['eZXMLSchemaGlobalInstance'] );
        $this->resetTeXSourceCache();
        // characters above U+FFFF are kept only where the database can store them; these tests are about the editor
        $xmlIni = eZINI::instance( 'ezxml.ini' );
        $this->savedSupplementary = $xmlIni->hasVariable( 'InputSettings', 'SupplementaryCharacters' ) ?
                                    $xmlIni->variable( 'InputSettings', 'SupplementaryCharacters' ) : null;
        $xmlIni->setVariable( 'InputSettings', 'SupplementaryCharacters', 'enabled' );
    }

    public function tearDown(): void
    {
        $ini = eZINI::instance( 'content.ini' );
        foreach ( array_reverse( $this->saved ) as $setting )
        {
            list( $block, $name, $value ) = $setting;
            if ( $value === null )
                $ini->removeSetting( $block, $name );
            else
                $ini->setVariable( $block, $name, $value );
        }
        $this->saved = array();
        $xmlIni = eZINI::instance( 'ezxml.ini' );
        if ( $this->savedSupplementary === null )
            $xmlIni->removeSetting( 'InputSettings', 'SupplementaryCharacters' );
        else
            $xmlIni->setVariable( 'InputSettings', 'SupplementaryCharacters', $this->savedSupplementary );
        if ( $this->savedSchema !== null )
            $GLOBALS['eZXMLSchemaGlobalInstance'] = $this->savedSchema;
        else
            unset( $GLOBALS['eZXMLSchemaGlobalInstance'] );
        if ( class_exists( 'eZOEXMLInput' ) )
            $this->resetTeXSourceCache();
    }

    protected function resetTeXSourceCache()
    {
        if ( property_exists( 'eZOEXMLInput', 'texSourceTags' ) )
            ( new ReflectionProperty( 'eZOEXMLInput', 'texSourceTags' ) )->setValue( null, null );
    }

    /** Opens $body in the editor and saves it, as eZOEXMLInput does; returns the saved body */
    protected function openAndSave( $body )
    {
        $xml = self::HEAD . $body . "</section>\n";
        $handler = new eZOEXMLInput( $xml, false, null );
        $html = htmlspecialchars_decode( $handler->inputXML(), ENT_COMPAT );
        $parser = new eZOEInputParser();
        $document = $parser->process( $html );
        $this->assertInstanceOf( DOMDocument::class, $document, implode( ', ', $parser->getMessages() ) );
        $saved = eZXMLTextType::domString( $document );
        $saved = preg_replace( '/^<\?xml[^>]*\?>\s*<section[^>]*>|<\/section>\s*$/', '', $saved );
        // the empty paragraph inputXML() adds at the end, removed by validateInput()
        return preg_replace( '/<paragraph\/>$|<paragraph>\x{00A0}<\/paragraph>$/u', '', $saved );
    }

    protected function latex( $text )
    {
        return '<paragraph>x <custom name="math_latex" custom:display="inline" custom:alt="f">' . $text . '</custom> y</paragraph>';
    }

    public static function supplementaryBodies()
    {
        return array(
            'emoji in text'      => array( "<paragraph>a \u{1F600} b</paragraph>" ),
            'double-struck in TeX' => array( '<paragraph>x <custom name="math_latex" custom:display="inline" custom:alt="A">' . "\u{1D538}" . ' = \mathbb{A}</custom> y</paragraph>' ),
            'italic in MathML'   => array( '<paragraph>x <custom name="math_mathml" custom:display="inline" custom:mathml="&lt;math&gt;&lt;mi&gt;' . "\u{1D465}" . '&lt;/mi&gt;&lt;/math&gt;">x</custom> y</paragraph>' ),
        );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'supplementaryBodies' )]
    public function testCharactersAboveFFFFAreKept( $body )
    {
        $this->assertSame( $body, $this->openAndSave( $body ) );
    }

    public function testACommentInTeXSourceDoesNotSwallowTheNextLine()
    {
        $this->assertSame( $this->latex( 'a + b' ), $this->openAndSave( $this->latex( "a % comment\n+ b" ) ) );
    }

    public function testCommentsAreReadAsTeXReadsThem()
    {
        // the comment eats the line break and the indentation of the next line; \% is a percent sign; \\% is a
        // line break followed by a comment
        $this->assertSame( $this->latex( '\frac{a}{b} = 50\% + c \\\\d' ),
                           $this->openAndSave( $this->latex( "\\frac{a}%\n   {b} = 50\\%\n+ c \\\\% c\nd" ) ) );
    }

    public function testACommentOnTheLastLineStays()
    {
        $this->assertSame( $this->latex( 'a + b % at the end' ), $this->openAndSave( $this->latex( "a\n+ b % at the end" ) ) );
    }

    public function testALineBreakWithoutCommentIsStillASpace()
    {
        // a line break in a custom tag becomes a space, with or without TeXSource
        $this->assertSame( $this->latex( 'a \cdot x' ), $this->openAndSave( $this->latex( "a \\cdot\n  x" ) ) );
    }

    public function testAPercentSignInACustomTagThatIsNoTeXIsText()
    {
        $body = '<paragraph>x <custom name="underline">50 % off' . "\n" . 'from today</custom> y</paragraph>';
        $this->assertSame( '<paragraph>x <custom name="underline">50 % off from today</custom> y</paragraph>', $this->openAndSave( $body ) );
    }

    public function testWithoutTheSettingTeXSourceALineBreakIsOnlyASpace()
    {
        eZINI::instance( 'content.ini' )->setVariable( 'CustomTagSettings', 'TeXSource', array() );
        $this->resetTeXSourceCache();
        $this->assertSame( $this->latex( 'a % comment + b' ), $this->openAndSave( $this->latex( "a % comment\n+ b" ) ) );
    }

    public function testLeadingSpaceInTeXSourceIsNotStoredAsNoBreakSpace()
    {
        $this->assertSame( $this->latex( 'a b' ), $this->openAndSave( $this->latex( ' a  b' ) ) );
    }

    public function testTeXSourceOfOneSpaceGoesToTheEditorAsNoBreakSpaceAsBefore()
    {
        // trimmed to '', the empty-tag path gives the editor &nbsp; (never an empty span), stored as U+00A0
        $xml = self::HEAD . $this->latex( ' ' ) . "</section>\n";
        $html = htmlspecialchars_decode( ( new eZOEXMLInput( $xml, false, null ) )->inputXML(), ENT_COMPAT );
        $this->assertStringContainsString( 'customattributes="display|inlineattribute_separationalt|f">&nbsp;</span>', $html );
        $this->assertSame( $this->latex( "\u{00A0}" ), $this->openAndSave( $this->latex( ' ' ) ) );
    }

    public function testALineBreakInACustomAttributeBecomesASpace()
    {
        $body = '<paragraph>x <custom name="math_mathml" custom:display="inline" custom:mathml="&lt;math&gt;&#10;&lt;mi&gt;a&lt;/mi&gt;&#10;&lt;mo&gt;+&lt;/mo&gt;&lt;/math&gt;">x</custom> y</paragraph>';
        $this->assertSame( '<paragraph>x <custom name="math_mathml" custom:display="inline" custom:mathml="&lt;math&gt; &lt;mi&gt;a&lt;/mi&gt; &lt;mo&gt;+&lt;/mo&gt;&lt;/math&gt;">x</custom> y</paragraph>',
                           $this->openAndSave( $body ) );
    }

    public function testSavingTwiceChangesNothingTheSecondTime()
    {
        foreach ( array( $this->latex( "a % c\n+ b" ), $this->latex( ' a  b' ), "<paragraph>a \u{1F600} b</paragraph>" ) as $body )
        {
            $once = $this->openAndSave( $body );
            $this->assertSame( $once, $this->openAndSave( $once ) );
        }
    }

    public static function texComments()
    {
        return array(
            'no comment'          => array( 'a + b', 'a + b' ),
            'comment'             => array( "a % c\n+ b", 'a + b' ),
            'CRLF'                => array( "a % c\r\n  + b", 'a + b' ),
            'escaped'             => array( "50\\%\nx", "50\\%\nx" ),
            'escaped backslash'   => array( "a \\\\% c\nb", 'a \\\\b' ),
            'escaped after pairs' => array( "a \\\\\\% c\nb", "a \\\\\\% c\nb" ),
            'last line'           => array( 'a % c', 'a % c' ),
            'empty comment'       => array( "\\frac{a}%\n{b}", '\\frac{a}{b}' ),
            // the % ends a control word: a space keeps it apart from the letters of the next line
            'control word'        => array( "\\alpha%\nbeta", '\\alpha beta' ),
            'control word indented' => array( "a \\cdot% times\n  x", 'a \\cdot x' ),
            'control word, brace' => array( "\\alpha%\n{b}", '\\alpha{b}' ),
            'control symbol'      => array( "a\\,%\nb", 'a\\,b' ),
            'letters, no control word' => array( "ab%\ncd", 'abcd' ),
            'line break before letters' => array( "a \\\\alpha%\nbeta", 'a \\\\alphabeta' ),
            'three backslashes'   => array( "\\\\\\alpha%\nbeta", '\\\\\\alpha beta' ),
            'comment lines in a row' => array( "\\alpha%a\n%b\nc", '\\alpha c' ),
            'comment lines, indented' => array( "a%x\n  %y\n  b", 'ab' ),
            'start of the text'   => array( "%c\nx", 'x' ),
            'CR only'             => array( "a%c\rb", 'ab' ),
            'control word at the start' => array( "\\alpha%\nbeta", '\\alpha beta' ),
            'next line not ASCII' => array( "\\alpha%\n\u{00E9}", "\\alpha\u{00E9}" ),
        );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'texComments' )]
    public function testTeXWithoutLineComments( $text, $expected )
    {
        $this->assertSame( $expected, eZOEXMLInput::texWithoutLineComments( $text ) );
    }

    public function testManyCommentsInALongTextTakeLinearTime()
    {
        $text = str_repeat( "\\alpha + x_{1} % a comment on this line\n  ", 20000 ) . 'b';
        $start = microtime( true );
        $result = eZOEXMLInput::texWithoutLineComments( $text );
        $this->assertLessThan( 5.0, microtime( true ) - $start );
        $this->assertSame( str_repeat( '\\alpha + x_{1} ', 20000 ) . 'b', $result );
    }
}
