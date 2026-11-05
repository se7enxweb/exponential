<?php
/**
 * A code block (<pre> in the online editor, <literal> in the stored XML) keeps its line breaks, tabs and leading
 * spaces when it is saved. eZXMLInputParser::process() removes every line break of HTML input and makes tabs spaces,
 * so a code block was stored as one long line, its indentation left as runs of spaces in the middle of it (every
 * code example of an imported documentation site, and every block an editor saved). No database.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class eZXMLInputPreformattedWhitespaceTest extends PHPUnit\Framework\TestCase
{
    /**
     * @param string $html what the editor posts
     * @return string[] the texts of the stored literals
     */
    protected function literals( $html )
    {
        $parser = new eZOEInputParser();
        $document = $parser->process( $html );
        $this->assertInstanceOf( DOMDocument::class, $document );
        $texts = array();
        foreach ( $document->getElementsByTagName( 'literal' ) as $literal )
            $texts[] = $literal->textContent;
        return $texts;
    }

    public static function blocks()
    {
        return array(
            'lines and indentation' => array( "<pre>class eZMyExtendedFilter\n{\n    /*!\n     Constructor\n    */\n    public function __construct()\n    {\n        // Empty...\n    }\n}</pre>",
                                              "class eZMyExtendedFilter\n{\n    /*!\n     Constructor\n    */\n    public function __construct()\n    {\n        // Empty...\n    }\n}" ),
            'an empty line inside' => array( "<pre>#The name of the filter.\n[MyFilter]\n\nExtensionName=myextension</pre>", "#The name of the filter.\n[MyFilter]\n\nExtensionName=myextension" ),
            'tabs' => array( "<pre>if ( \$a )\n{\n\treturn 1;\n}</pre>", "if ( \$a )\n{\n\treturn 1;\n}" ),
            'leading spaces of the first line' => array( "<pre>    indented\nnot</pre>", "    indented\nnot" ),
            'CRLF and CR' => array( "<pre>one\r\ntwo\rthree</pre>", "one\ntwo\nthree" ),
            '<br> from the editor' => array( '<pre>one<br />  two<br>three</pre>', "one\n  two\nthree" ),
            'the line break after the opening tag is no content' => array( "<pre class=\"wordwrap\">\nfirst\nsecond</pre>", "first\nsecond" ),
            'entities' => array( "<pre>\$a = array( 'x' =&gt; '&lt;b&gt;' );\n// &amp; done</pre>", "\$a = array( 'x' => '<b>' );\n// & done" ),
        );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'blocks' )]
    public function testTheCodeBlockKeepsItsWhiteSpace( $html, $expected )
    {
        $this->assertSame( array( $expected ), $this->literals( $html ) );
    }

    public function testABlockWithAClassKeepsItsWhiteSpaceToo()
    {
        // (whether the class itself is kept depends on the literal classes of ezxml.ini, not on this)
        $parser = new eZOEInputParser();
        $document = $parser->process( "<pre class=\"wordwrap\">a\n  b</pre>" );
        $this->assertSame( "a\n  b", $document->getElementsByTagName( 'literal' )->item( 0 )->textContent );
    }

    public function testTextOutsideCodeBlocksStillLosesItsLineBreaks()
    {
        $parser = new eZOEInputParser();
        $document = $parser->process( "<p>one\ntwo</p><pre>x\n y</pre><p>three\tfour</p>" );
        $paragraphs = array();
        foreach ( $document->getElementsByTagName( 'paragraph' ) as $paragraph )
            if ( !$paragraph->getElementsByTagName( 'literal' )->length )
                $paragraphs[] = $paragraph->textContent;
        $this->assertSame( array( 'onetwo', 'three four' ), $paragraphs );
        $this->assertSame( "x\n y", $document->getElementsByTagName( 'literal' )->item( 0 )->textContent );
    }

    public function testASavedBlockOpenedInTheEditorAndSavedAgainIsTheSame()
    {
        $code = "<?php\nclass A\n{\n\tpublic \$b = '<x>';\n\n    function c()\n    {\n    }\n}";
        $first = $this->literals( '<pre>' . htmlspecialchars( $code, ENT_NOQUOTES ) . '</pre>' );
        $this->assertSame( array( $code ), $first );
        // the editor gets the stored text back inside <pre> (its lines as line breaks or as <br />) and posts it again
        $this->assertSame( $first, $this->literals( '<pre>' . htmlspecialchars( $first[0], ENT_NOQUOTES ) . '</pre>' ) );
        $this->assertSame( $first, $this->literals( '<pre>' . str_replace( "\n", '<br />', htmlspecialchars( $first[0], ENT_NOQUOTES ) ) . '</pre>' ) );
    }

    public function testInputWithoutCodeBlocksIsUntouched()
    {
        $parser = new eZOEInputParser();
        $this->assertSame( "<p>a\n\tb</p>", $parser->preformattedWhitespace( "<p>a\n\tb</p>" ) );
        $this->assertSame( "<pre>a<br />&#9;b</pre>", $parser->preformattedWhitespace( "<pre>a\n\tb</pre>" ) );
    }
}
