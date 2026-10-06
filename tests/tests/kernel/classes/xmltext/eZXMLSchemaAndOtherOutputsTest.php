<?php
/**
 * The schema of the XML text datatype (which tag may hold which, inline or block, custom tags and their inline
 * setting, attributes, defaults, classes and custom attributes, and the settings it is built from), and the two
 * smaller output handlers: the plain one, which shows the stored XML, and the PDF one, which numbers list items
 * and gives tables a width and a border.
 *
 * No database. The PDF output's templates are replaced by markers, as in eZXHTMLXMLOutputTest.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group xmltext
 */

require_once __DIR__ . '/eZXMLTextTestFixtures.php';

class eZXMLTextTestPDFOutput extends eZPDFXMLOutput
{
    function prefetch()
    {
    }

    function renderTag( $element, $content, $vars )
    {
        $currentTag = $this->OutputTags[$element->nodeName] ?? null;
        if ( $currentTag && isset( $currentTag['quickRender'] ) )
            return parent::renderTag( $element, $content, $vars );
        $name = basename( $this->TemplateUri, '.tpl' );
        $shown = array();
        foreach ( array( 'level', 'header_number', 'list_count', 'tag_name', 'width', 'border' ) as $var )
        {
            if ( $this->Tpl->hasVariable( $var, 'xmltagns' ) && (string)$this->Tpl->variable( $var, 'xmltagns' ) !== '' )
                $shown[] = $var . '=' . $this->Tpl->variable( $var, 'xmltagns' );
        }
        return '[' . $name . ( $shown ? ' ' . implode( ' ', $shown ) : '' ) . ']' . $content . '[/' . $name . ']';
    }
}

class eZXMLSchemaAndOtherOutputsTest extends eZXMLTextTestCase
{
    private function element( $xml )
    {
        $dom = new DOMDocument();
        $dom->loadXML( $xml );
        return $dom->documentElement;
    }

    public static function checkProvider()
    {
        return array(
            array( 'section', 'paragraph', true ), array( 'section', 'header', true ), array( 'section', 'section', true ),
            array( 'section', 'strong', false ), array( 'section', 'table', false ), array( 'section', 'line', false ),
            array( 'paragraph', 'strong', true ), array( 'paragraph', 'table', true ), array( 'paragraph', 'line', true ),
            array( 'paragraph', 'header', false ), array( 'paragraph', 'paragraph', false ), array( 'paragraph', 'li', false ),
            array( 'table', 'tr', true ), array( 'table', 'td', false ), array( 'tr', 'td', true ), array( 'tr', 'th', true ),
            array( 'td', 'paragraph', true ), array( 'td', 'strong', false ), array( 'ul', 'li', true ), array( 'ul', 'paragraph', false ),
            array( 'li', 'paragraph', true ), array( 'header', 'strong', true ), array( 'header', 'line', true ),
            array( 'line', 'strong', true ), array( 'line', 'paragraph', false ), array( 'strong', 'emphasize', true ),
            array( 'strong', 'paragraph', false ), array( 'literal', '#text', true ), array( 'link', 'strong', true ),
            array( 'anchor', '#text', false ), array( 'embed', '#text', false ), array( 'nosuchparent', 'strong', false ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('checkProvider')]
    public function testCheck( $parent, $child, $expected )
    {
        $this->assertSame( $expected, eZXMLSchema::instance()->check( $parent, $child ) );
    }

    public function testCheckOfUnknownChildrenAndCustomTags()
    {
        $schema = eZXMLSchema::instance();
        $this->assertNull( $schema->check( 'section', 'nosuchtag' ) );
        $this->assertTrue( $schema->check( 'section', 'custom' ), 'custom is neither inline nor block by name' );
        $this->assertTrue( $schema->check( 'paragraph', $this->element( '<custom name="strike"/>' ) ) );
        $this->assertFalse( $schema->check( 'section', $this->element( '<custom name="strike"/>' ) ) );
        $this->assertTrue( $schema->check( 'paragraph', $this->element( '<custom name="factbox"/>' ) ) );
        $this->assertFalse( $schema->check( 'line', $this->element( '<custom name="factbox"/>' ) ) );
        // a block custom tag may hold anything that is not inline
        $this->assertTrue( $schema->check( $this->element( '<custom name="factbox"/>' ), 'header' ) );
        // the schema lets a custom tag hold block children even when it is marked inline
        $this->assertTrue( $schema->check( $this->element( '<custom name="factbox" inline="true"/>' ), 'header' ) );
    }

    public function testIsInline()
    {
        $schema = eZXMLSchema::instance();
        $this->assertTrue( $schema->isInline( 'strong' ) );
        $this->assertTrue( $schema->isInline( '#text' ) );
        $this->assertFalse( $schema->isInline( 'paragraph' ) );
        $this->assertNull( $schema->isInline( 'nosuchtag' ) );
        $this->assertSame( array( 'strike' => 'true', 'sup' => 'true', 'quote' => 'false' ), $schema->isInline( 'custom' ) );
        $this->assertTrue( $schema->isInline( $this->element( '<custom name="strike"/>' ) ) );
        $this->assertFalse( $schema->isInline( $this->element( '<custom name="quote"/>' ) ) );
        $this->assertFalse( $schema->isInline( $this->element( '<custom name="factbox"/>' ) ) );
        $this->assertFalse( $schema->isInline( $this->element( '<custom/>' ) ) );
    }

    public function testExistsAttributesAndDefaults()
    {
        $schema = eZXMLSchema::instance();
        $this->assertTrue( $schema->exists( 'paragraph' ) );
        $this->assertFalse( $schema->exists( 'p' ) );
        $this->assertTrue( $schema->exists( $this->element( '<custom name="factbox"/>' ) ) );
        $this->assertFalse( $schema->exists( $this->element( '<custom name="nope"/>' ) ) );
        $this->assertFalse( $schema->exists( $this->element( '<custom/>' ) ) );
        $this->assertTrue( $schema->exists( $this->element( '<table/>' ) ) );
        $this->assertFalse( $schema->exists( $this->element( '<blink/>' ) ) );

        $this->assertTrue( $schema->hasAttributes( $this->element( '<link/>' ) ) );
        $this->assertFalse( $schema->hasAttributes( $this->element( '<line/>' ) ) );
        $this->assertFalse( $schema->hasAttributes( $this->element( '<blink/>' ) ) );
        $this->assertSame( array( 'class', 'align' ), $schema->attributes( $this->element( '<paragraph/>' ) ) );
        $this->assertFalse( $schema->attributes( $this->element( '<blink/>' ) ) );
        $this->assertTrue( $schema->childrenRequired( $this->element( '<table/>' ) ) );
        $this->assertFalse( $schema->childrenRequired( $this->element( '<anchor/>' ) ) );
        $this->assertFalse( $schema->childrenRequired( $this->element( '<blink/>' ) ) );

        $this->assertSame( '_self', $schema->attrDefaultValue( 'link', 'target' ) );
        $this->assertSame( array(), $schema->attrDefaultValue( 'link', 'title' ) );
        $this->assertSame( array( 'align' => '', 'view' => 'embed', 'class' => '' ), $schema->attrDefaultValues( 'embed' ) );
        $this->assertSame( array(), $schema->attrDefaultValues( 'paragraph' ) );
        $this->assertSame( array(), $schema->attrDefaultValues( null ) );
        $this->assertContains( 'embed-inline', $schema->availableElements() );
    }

    public function testClassesAndCustomAttributes()
    {
        $schema = eZXMLSchema::instance();
        $this->assertSame( array( 'lead' ), $schema->getClassesList( 'paragraph' ) );
        $this->assertSame( array(), $schema->getClassesList( 'nosuchtag' ) );
        $schema->addAvailableClass( 'paragraph', 'note' );
        $schema->addAvailableClass( 'nosuchtag', 'x' );
        $this->assertSame( array( 'lead', 'note' ), $schema->getClassesList( 'paragraph' ) );
        $this->assertSame( array( 'x' ), $schema->getClassesList( 'nosuchtag' ) );

        $this->assertSame( array( 'note' ), $schema->customAttributes( 'paragraph' ) );
        $this->assertSame( array( 'note' ), $schema->customAttributes( $this->element( '<paragraph/>' ) ) );
        $this->assertSame( array( 'title' ), $schema->customAttributes( $this->element( '<custom name="factbox"/>' ) ) );
        $this->assertSame( array(), $schema->customAttributes( $this->element( '<custom name="nope"/>' ) ) );
        $this->assertSame( array(), $schema->customAttributes( $this->element( '<custom/>' ) ) );
        $this->assertSame( array(), $schema->customAttributes( 'nosuchtag' ) );
        $schema->addCustomAttribute( 'strong', 'tone' );
        $schema->addCustomAttribute( $this->element( '<custom name="strike"/>' ), 'reason' );
        $schema->addCustomAttribute( $this->element( '<header/>' ), 'kicker' );
        $this->assertSame( array( 'tone' ), $schema->customAttributes( 'strong' ) );
        $this->assertSame( array( 'reason' ), $schema->customAttributes( $this->element( '<custom name="strike"/>' ) ) );
        $this->assertSame( array( 'kicker' ), $schema->customAttributes( 'header' ) );
    }

    public function testSchemaIsBuiltFromTheSettings()
    {
        // the real constructor, against the content.ini this checkout has
        $schema = new eZXMLSchema();
        $ini = eZINI::instance( 'content.ini' );
        $this->assertSame( $ini->variable( 'CustomTagSettings', 'AvailableCustomTags' ), $schema->Schema['custom']['tagList'] );
        $this->assertSame( $ini->variable( 'paragraph', 'AllowEmpty' ) == 'true' ? false : true, $schema->Schema['paragraph']['childrenRequired'] );
        foreach ( $schema->Schema['custom']['tagList'] as $tag )
            $this->assertArrayHasKey( $tag, $schema->Schema['custom']['customAttributes'] );
        foreach ( array_keys( $schema->Schema ) as $tag )
            $this->assertIsArray( $schema->Schema[$tag]['classesList'] );
        $this->assertSame( $GLOBALS['eZXMLSchemaGlobalInstance'], eZXMLSchema::instance() );
    }

    // ---------------------------------------------------------------- other output handlers

    public function testPlainOutputShowsTheXml()
    {
        $output = new eZPlainXMLOutput( '<section><paragraph>a & b</paragraph></section>', false );
        $this->assertSame( '<pre>&lt;section&gt;&lt;paragraph&gt;a &amp; b&lt;/paragraph&gt;&lt;/section&gt;</pre>', $output->outputText() );
    }

    private function pdf( $body )
    {
        $output = new eZXMLTextTestPDFOutput( '<?xml version="1.0" encoding="utf-8"?><section>' . $body . '</section>', false );
        $this->assertSame( 'design:content/datatype/pdf/ezxmltags/', $output->TemplatesPath );
        return $output->outputText();
    }

    public function testPdfListItemsAreNumbered()
    {
        $this->assertSame( '[ol][li list_count=1 tag_name=ol]a[/li][li list_count=2 tag_name=ol]b[/li][/ol]',
                           $this->pdf( '<paragraph><ol><li><paragraph>a</paragraph></li><li><paragraph>b</paragraph></li></ol></paragraph>' ) );
    }

    public function testPdfTablesHaveAWidthAndABorder()
    {
        $this->assertStringStartsWith( '[table width=100% border=1]', $this->pdf( '<paragraph><table><tr><td><paragraph>x</paragraph></td></tr></table></paragraph>' ) );
        $this->assertStringStartsWith( '[table width=50% border=0]', $this->pdf( '<paragraph><table width="50%" border="0"><tr><td><paragraph>x</paragraph></td></tr></table></paragraph>' ) );
    }

    public function testPdfHeadersAfterATableKeepTheirLevel()
    {
        $html = $this->pdf( '<section><header>A</header><paragraph><table><tr><td><paragraph>x</paragraph></td></tr></table></paragraph><section><header>B</header></section></section>' );
        $this->assertStringContainsString( '[header level=1 header_number=1]A[/header]', $html );
        $this->assertStringContainsString( '[header level=2 header_number=1.1]B[/header]', $html );
    }
}
