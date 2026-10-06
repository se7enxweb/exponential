<?php
/**
 * The XML text datatype (ezxmltext) apart from the parser: the stored XML of new and copied objects, meta data for
 * the search index, titles for object names, emptiness, text export and import, class settings and their package
 * serialization, templates, the package serialization of links and embeds by object, and the simplified XML input
 * handler as the edit view calls it (validation, stored XML, relations, text given back to the editor).
 *
 * No database: the class attribute and the object are held in memory (see eZDatatypeTestFixtures.php).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group xmltext
 */

require_once __DIR__ . '/eZXMLTextTestFixtures.php';

class eZXMLTextTestObject
{
    public $relations = array();

    function appendInputRelationList( $ids, $type )
    {
        $this->relations[$type] = array_merge( $this->relations[$type] ?? array(), $ids );
    }
}

class eZXMLTextTestObjectAttribute extends eZDatatypeTestObjectAttribute
{
    public $testObject;

    function object()
    {
        return $this->testObject;
    }
}

class eZXMLTextTypeTest extends eZXMLTextTestCase
{
    const HEAD = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
    const ROOT = '<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"';

    private function xml( $body )
    {
        return self::HEAD . self::ROOT . ( $body === '' ? '/>' : '>' . $body . '</section>' ) . "\n";
    }

    private function xmlAttribute( $dataText, array $classFields = array(), $dataInt = eZXMLTextType::VERSION_TIMESTAMP )
    {
        $classAttribute = $this->classAttribute( 'ezxmltext', $classFields );
        $row = array( 'id' => 4711, 'contentobject_id' => 812, 'version' => 1, 'language_code' => 'eng-GB', 'language_id' => 2,
                      'contentclassattribute_id' => 901, 'data_type_string' => 'ezxmltext', 'attribute_original_id' => 0,
                      'data_int' => $dataInt, 'data_float' => 0, 'data_text' => $dataText, 'sort_key_int' => 0, 'sort_key_string' => '' );
        $attribute = new eZXMLTextTestObjectAttribute( $row );
        $attribute->testClassAttribute = $classAttribute;
        $attribute->setContentClassAttributeIdentifier( 'k1_field' );
        $attribute->setContentClassAttributeName( 'K1 field' );
        $attribute->testObject = new eZXMLTextTestObject();
        return $attribute;
    }

    public function testNewObjectGetsAnEmptyDocumentAndACopyTheOriginalText()
    {
        $type = $this->dataType( 'ezxmltext' );
        $new = $this->xmlAttribute( null );
        $type->initializeObjectAttribute( $new, false, null );
        $this->assertSame( $this->xml( '' ), $new->attribute( 'data_text' ) );
        $this->assertFalse( $type->hasObjectAttributeContent( $new ) );

        $original = $this->xmlAttribute( $this->xml( '<paragraph>Kept</paragraph>' ) );
        $copy = $this->xmlAttribute( null );
        $type->initializeObjectAttribute( $copy, 3, $original );
        $this->assertSame( $this->xml( '<paragraph>Kept</paragraph>' ), $copy->attribute( 'data_text' ) );
        $this->assertTrue( $type->hasObjectAttributeContent( $copy ) );

        $batch = $type->batchInitializeObjectAttributeData( $this->classAttribute( 'ezxmltext' ) );
        $this->assertSame( "'" . eZDB::instance()->escapeString( $this->xml( '' ) ) . "'", $batch['data_text'] );
        $this->assertTrue( $type->supportsBatchInitializeObjectAttribute() );
    }

    public function testStoreSetsTheFormatTimestamp()
    {
        $attribute = $this->xmlAttribute( $this->xml( '' ), array(), 0 );
        $this->dataType( 'ezxmltext' )->storeObjectAttribute( $attribute );
        $this->assertSame( eZXMLTextType::VERSION_TIMESTAMP, $attribute->attribute( 'data_int' ) );
    }

    public function testRawXmlTextOfOldOrMissingValues()
    {
        $this->assertSame( 'abc', eZXMLTextType::rawXMLText( $this->xmlAttribute( 'abc', array(), 0 ) ) );
        $this->assertEmpty( eZXMLTextType::rawXMLText( $this->xmlAttribute( null, array(), null ) ) );
        $this->assertSame( 'x', eZXMLTextType::rawXMLText( $this->xmlAttribute( 'x' ) ) );
    }

    public static function metaDataProvider()
    {
        return array(
            'paragraphs are separated' => array( '<paragraph>One</paragraph><paragraph>Two</paragraph>', 'One Two' ),
            'lines and inline tags' => array( '<paragraph><line>a <strong>b</strong></line><line>c</line></paragraph>', 'a  b c' ),
            'headers and lists' => array( '<section><header>T</header><paragraph><ul><li><paragraph>i</paragraph></li></ul></paragraph></section>', 'T i' ),
            'empty' => array( '', '' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('metaDataProvider')]
    public function testMetaDataIsTheTextWithSpaces( $body, $expected )
    {
        $this->assertSame( $expected, $this->dataType( 'ezxmltext' )->metaData( $this->xmlAttribute( $this->xml( $body ) ) ) );
    }

    public function testMetaDataOfNoOrBrokenXml()
    {
        $type = $this->dataType( 'ezxmltext' );
        foreach ( array( null, '', '   ', '<section><paragraph>broken', 'not xml' ) as $text )
            $this->assertSame( '', $type->metaData( $this->xmlAttribute( $text ) ), var_export( $text, true ) );
    }

    public function testConcatTextContent()
    {
        $this->assertSame( '', eZXMLTextType::concatTextContent( null ) );
        $dom = new DOMDocument();
        $dom->loadXML( '<a>x<b>y</b><c/>z</a>' );
        $this->assertSame( 'x y z ', eZXMLTextType::concatTextContent( $dom->documentElement ) );
    }

    public static function titleProvider()
    {
        return array(
            'paragraph' => array( '<paragraph>Hello <strong>you</strong></paragraph>', 'Hello ' ),
            'header' => array( '<section><header>Title</header><paragraph>x</paragraph></section>', 'Title' ),
            'line' => array( '<paragraph><line>First line</line><line>Second</line></paragraph>', 'First line' ),
            'strong first' => array( '<paragraph><strong>Bold</strong> rest</paragraph>', 'Bold' ),
            'empty document' => array( '', '' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('titleProvider')]
    public function testTitleIsTheFirstText( $body, $expected )
    {
        $this->assertSame( $expected, $this->dataType( 'ezxmltext' )->title( $this->xmlAttribute( $this->xml( $body ) ) ) );
    }

    public function testTitleOfNoOrBrokenXml()
    {
        $type = $this->dataType( 'ezxmltext' );
        $this->assertSame( '', $type->title( $this->xmlAttribute( null ) ) );
        $this->assertSame( '', $type->title( $this->xmlAttribute( '  ' ) ) );
        $this->assertSame( 'not xml', $type->title( $this->xmlAttribute( 'not xml' ) ) );
    }

    public function testIsEmpty()
    {
        $this->assertTrue( ( new eZXMLText( null, null ) )->attribute( 'is_empty' ) );
        $this->assertTrue( ( new eZXMLText( '', null ) )->attribute( 'is_empty' ) );
        $this->assertTrue( ( new eZXMLText( false, null ) )->attribute( 'is_empty' ) );
        $this->assertTrue( ( new eZXMLText( '<section><paragraph>broken', null ) )->attribute( 'is_empty' ) );
        $this->assertTrue( ( new eZXMLText( $this->xml( '' ), null ) )->attribute( 'is_empty' ) );
        $this->assertFalse( ( new eZXMLText( $this->xml( '<paragraph>x</paragraph>' ), null ) )->attribute( 'is_empty' ) );
    }

    public function testXmlTextAttributes()
    {
        $text = new eZXMLText( $this->xml( '<paragraph>x</paragraph>' ), null );
        $this->assertSame( array( 'input', 'output', 'pdf_output', 'xml_data', 'is_empty' ), $text->attributes() );
        $this->assertTrue( $text->hasAttribute( 'xml_data' ) );
        $this->assertFalse( $text->hasAttribute( 'nothing' ) );
        $this->assertSame( $this->xml( '<paragraph>x</paragraph>' ), $text->attribute( 'xml_data' ) );
        $this->assertNull( @$text->attribute( 'nothing' ) );
        $this->assertInstanceOf( 'eZXMLInputHandler', $text->attribute( 'input' ) );
        $this->assertSame( $text->attribute( 'input' ), $text->attribute( 'input' ) );
        $this->assertInstanceOf( 'eZXMLOutputHandler', $text->attribute( 'output' ) );
        $this->assertSame( $text->attribute( 'output' ), $text->attribute( 'output' ) );
        $this->assertInstanceOf( 'eZXMLOutputHandler', $text->attribute( 'pdf_output' ) );
    }

    public function testToStringAndFromString()
    {
        $type = $this->dataType( 'ezxmltext' );
        $attribute = $this->xmlAttribute( $this->xml( '<paragraph>x</paragraph>' ) );
        $this->assertSame( $this->xml( '<paragraph>x</paragraph>' ), $type->toString( $attribute ) );
        $this->assertNotFalse( $type->fromString( $attribute, $this->xml( '<paragraph>y</paragraph>' ) ) );
        $this->assertSame( $this->xml( '<paragraph>y</paragraph>' ), $attribute->attribute( 'data_text' ) );
        $this->assertFalse( $type->fromString( $attribute, array( 'x' ) ) );
        $this->assertSame( $this->xml( '<paragraph>y</paragraph>' ), $attribute->attribute( 'data_text' ) );
    }

    public function testClassSettings()
    {
        $type = $this->dataType( 'ezxmltext' );
        $classAttribute = $this->classAttribute( 'ezxmltext', array( 'data_int1' => null ) );
        $type->initializeClassAttribute( $classAttribute );
        $this->assertSame( 10, $classAttribute->attribute( 'data_int1' ) );
        $this->assertSame( 1, $classAttribute->storeCount );

        $this->assertFalse( $type->fetchClassAttributeHTTPInput( $this->post( array() ), 'ContentClass', $classAttribute ) );
        $this->assertTrue( $type->fetchClassAttributeHTTPInput( $this->post( array( 'ContentClass_ezxmltext_cols_901' => '7', 'ContentClass_ezxmltext_tagpreset_901' => 'mini' ) ), 'ContentClass', $classAttribute ) );
        $this->assertSame( 7, $classAttribute->attribute( 'data_int1' ) );
        $this->assertSame( 'mini', $classAttribute->attribute( 'data_text2' ) );
        $this->assertTrue( $type->fetchClassAttributeHTTPInput( $this->post( array( 'ContentClass_ezxmltext_cols_901' => array( 7 ), 'ContentClass_ezxmltext_tagpreset_901' => array( 'x' ) ) ), 'ContentClass', $classAttribute ) );
        $this->assertSame( 10, $classAttribute->attribute( 'data_int1' ) );
        $this->assertSame( '', $classAttribute->attribute( 'data_text2' ) );

        list( $copy, $xml ) = $this->roundTripClassParameters( $this->classAttribute( 'ezxmltext', array( 'data_int1' => 15 ) ) );
        $this->assertSame( '<datatype-parameters><text-column-count>15</text-column-count></datatype-parameters>', $xml );
        $this->assertSame( '15', $copy->attribute( 'data_int1' ) );
        $dom = new DOMDocument();
        $dom->loadXML( '<attribute><datatype-parameters/></attribute>' );
        $kept = $this->classAttribute( 'ezxmltext', array( 'data_int1' => 12 ) );
        $type->unserializeContentClassAttribute( $kept, $dom->documentElement, $dom->documentElement->firstChild );
        $this->assertSame( 12, $kept->attribute( 'data_int1' ) );
    }

    public function testTemplatesAndFlags()
    {
        $type = $this->dataType( 'ezxmltext' );
        $this->assertSame( array( array( 'regexp', '#^content/datatype/[a-zA-Z]+/ezxmltags/#' ) ), $type->templateList() );
        $this->assertTrue( $type->isIndexable() );
        $this->assertFalse( $type->isInformationCollector() );
        $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $this->post( array() ), 'x', $this->xmlAttribute( '' ) ) );
    }

    public function testObjectSerializationOfObjectLinksWithoutAnObject()
    {
        // links and embeds by object id: the remote id is looked up; with no object (an id no object has) the id is
        // dropped and nothing else is added. Anchors and plain text are exported as they are.
        $type = $this->dataType( 'ezxmltext' );
        $attribute = $this->xmlAttribute( $this->xml( '<paragraph><link anchor_name="a">x</link>text</paragraph>' ) );
        $node = $type->serializeContentObjectAttribute( null, $attribute );
        $this->assertSame( 'k1_field', $node->getAttributeNS( 'http://ez.no/ezobject', 'identifier' ) );
        $this->assertSame( 'ezxmltext', $node->getAttribute( 'type' ) );
        $this->assertStringContainsString( '<paragraph><link anchor_name="a">x</link>text</paragraph>', $node->ownerDocument->saveXML( $node ) );

        $empty = $type->serializeContentObjectAttribute( null, $this->xmlAttribute( '' ) );
        $this->assertFalse( $empty->hasChildNodes() );
        $broken = $type->serializeContentObjectAttribute( null, $this->xmlAttribute( '<section><paragraph>' ) );
        $this->assertFalse( $broken->hasChildNodes() );
    }

    public function testUnserializeWithoutLinksTakesTheFirstElement()
    {
        $type = $this->dataType( 'ezxmltext' );
        $dom = new DOMDocument();
        $dom->loadXML( '<ezobject:attribute xmlns:ezobject="http://ez.no/object/">' . "\n" . '<section><paragraph>Imported</paragraph></section></ezobject:attribute>' );
        $attribute = $this->xmlAttribute( '' );
        $type->unserializeContentObjectAttribute( null, $attribute, $dom->documentElement );
        $this->assertSame( '<section><paragraph>Imported</paragraph></section>', $attribute->attribute( 'data_text' ) );

        $this->assertFalse( $type->postUnserializeContentObjectAttribute( null, $attribute ), 'nothing remote to resolve' );
        $this->assertFalse( $type->postUnserializeContentObjectAttribute( null, $this->xmlAttribute( '' ) ) );
        $this->assertFalse( $type->postUnserializeContentObjectAttribute( null, $this->xmlAttribute( '<broken' ) ) );
    }

    // ---------------------------------------------------------------- the simplified input handler

    private function input( $attribute )
    {
        return new eZSimplifiedXMLInput( $attribute->attribute( 'data_text' ), false, $attribute );
    }

    protected function tearDown(): void
    {
        foreach ( array_keys( $GLOBALS ) as $key )
        {
            if ( strpos( $key, 'originalInput_4711' ) === 0 || strpos( $key, 'isInputValid_4711' ) === 0 )
                unset( $GLOBALS[$key] );
        }
        parent::tearDown();
    }

    public function testValidateInputStoresTheXmlAndTheRelations()
    {
        $attribute = $this->xmlAttribute( $this->xml( '' ) );
        $input = $this->input( $attribute );
        $http = $this->post( array( 'ContentObjectAttribute_data_text_4711' => "Hello <b>you</b>\n\n<embed href=\"ezobject://7\" />\n<a href=\"ezobject://8\">l</a>" ) );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $input->validateInput( $http, 'ContentObjectAttribute', $attribute ) );
        $this->assertStringContainsString( '>Hello <strong>you</strong></paragraph>', $attribute->attribute( 'data_text' ) );
        $this->assertSame( array( eZContentObject::RELATION_EMBED => array( '7' ), eZContentObject::RELATION_LINK => array( '8' ) ), $attribute->testObject->relations );
        $this->assertTrue( $GLOBALS['isInputValid_4711'] );
    }

    public function testValidateInputRefusesWhatTheParserStops()
    {
        $attribute = $this->xmlAttribute( $this->xml( '<paragraph>old</paragraph>' ) );
        $http = $this->post( array( 'ContentObjectAttribute_data_text_4711' => '<a href="javascript:alert(1)">x</a>' ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $this->input( $attribute )->validateInput( $http, 'ContentObjectAttribute', $attribute ) );
        $this->assertStringContainsString( 'Using scripts in links is not allowed', $attribute->validationError() );
        $this->assertFalse( $GLOBALS['isInputValid_4711'] );
        $this->assertSame( $this->xml( '<paragraph>old</paragraph>' ), $attribute->attribute( 'data_text' ) );
        // the editor gets the text back as typed
        $this->assertSame( '<a href="javascript:alert(1)">x</a>', $this->input( $attribute )->inputXML() );
    }

    public function testValidateInputRefusesAnArrayAndRequiresContent()
    {
        $attribute = $this->xmlAttribute( $this->xml( '' ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $this->input( $attribute )->validateInput( $this->post( array( 'ContentObjectAttribute_data_text_4711' => array( 'x' ) ) ), 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( '', $GLOBALS['originalInput_4711'] );

        $required = $this->xmlAttribute( $this->xml( '' ), array( 'is_required' => 1 ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $this->input( $required )->validateInput( $this->post( array( 'ContentObjectAttribute_data_text_4711' => '' ) ), 'ContentObjectAttribute', $required ) );
        $this->assertSame( 'Content required', $required->validationError() );

        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $this->input( $attribute )->validateInput( $this->post( array() ), 'ContentObjectAttribute', $attribute ) );
    }

    public function testValidateInputOfATextStartingWithAnEmptyLine()
    {
        // empty paragraphs not allowed (the default): the empty line is dropped like any other
        $attribute = $this->xmlAttribute( $this->xml( '' ) );
        $state = $this->input( $attribute )->validateInput( $this->post( array( 'ContentObjectAttribute_data_text_4711' => "\nText" ) ), 'ContentObjectAttribute', $attribute );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $state, (string)$attribute->validationError() );
        $this->assertStringContainsString( '>Text</paragraph>', $attribute->attribute( 'data_text' ) );

        // allowed: it is kept as an empty first paragraph
        eZXMLSchema::instance()->Schema['paragraph']['childrenRequired'] = false;
        $attribute = $this->xmlAttribute( $this->xml( '' ) );
        $state = $this->input( $attribute )->validateInput( $this->post( array( 'ContentObjectAttribute_data_text_4711' => "\nText" ) ), 'ContentObjectAttribute', $attribute );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $state, (string)$attribute->validationError() );
        $this->assertSame( 2, substr_count( $attribute->attribute( 'data_text' ), '<paragraph' ) );
    }

    public function testInputXmlOfStoredText()
    {
        $attribute = $this->xmlAttribute( $this->xml( '<section><header>T</header><paragraph>x</paragraph></section>' ) );
        $this->assertSame( "<header level=\"1\">T</header>\nx", $this->input( $attribute )->inputXML() );
        $this->assertSame( 'text in broken', $this->input( $this->xmlAttribute( '<section><paragraph>text in broken' ) )->inputXML() );
        $this->assertSame( '', $this->input( $this->xmlAttribute( '' ) )->inputXML() );
        $this->assertSame( '', $this->input( $this->xmlAttribute( null ) )->inputXML() );
    }

    public function testInputHandlerBase()
    {
        $attribute = $this->xmlAttribute( "a\x01b" );
        $handler = new eZXMLInputHandler( "a\x01b", false, $attribute );
        $this->assertSame( 'ab', $handler->xmlData() );
        $this->assertSame( array( 'input_xml', 'aliased_type', 'aliased_handler', 'edit_template_name', 'information_template_name' ), $handler->attributes() );
        $this->assertTrue( $handler->hasAttribute( 'input_xml' ) );
        $this->assertFalse( $handler->hasAttribute( 'x' ) );
        $this->assertNull( $handler->attribute( 'input_xml' ) );
        $this->assertNull( @$handler->attribute( 'x' ) );
        $this->assertFalse( @$handler->attribute( 'aliased_type' ) );
        $this->assertSame( 'ezxmltext', $handler->attribute( 'edit_template_name' ) );
        $this->assertSame( 'ezxmltext', $handler->attribute( 'information_template_name' ) );
        $this->assertTrue( $handler->isValid() );
        $this->assertSame( eZInputValidator::STATE_INVALID, $handler->validateInput( null, 'x', $attribute ) );
        $this->assertNull( $handler->convertInput( 'x' ) );
        $this->assertNull( $handler->customObjectAttributeHTTPAction( null, 'x', $attribute ) );
        $this->assertInstanceOf( 'eZXMLInputHandler', $handler->attribute( 'aliased_handler' ) );
    }

    public function testInputHandlerTemplateNamesWithASuffix()
    {
        $attribute = $this->xmlAttribute( '' );
        $handler = new eZXMLTextTestSuffixInputHandler( '', false, $attribute );
        $this->assertSame( 'ezxmltext_mine', $handler->editTemplateName() );
        $this->assertSame( 'ezxmltext_info', $handler->informationTemplateName() );
    }
}

class eZXMLTextTestSuffixInputHandler extends eZXMLInputHandler
{
    function editTemplateSuffix( &$contentobjectAttribute )
    {
        return 'mine';
    }

    function informationTemplateSuffix( &$contentobjectAttribute )
    {
        return 'info';
    }
}
