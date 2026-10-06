<?php
/**
 * The defaults every datatype inherits from eZDataType: registration and creation, template names, attributes,
 * display information from datatype.ini, content actions of information collectors, the package serialization
 * through the object_serialize_map and the data_int/data_float/data_text fallback, and the many no-op defaults.
 *
 * No database (see eZDatatypeTestFixtures.php).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group datatypes
 */

require_once __DIR__ . '/eZDatatypeTestFixtures.php';

class eZDataTypeBaseTestPlainType extends eZDataType
{
    public function __construct( $properties = array() )
    {
        parent::__construct( 'k1plain', 'K1 plain', $properties );
    }
}

class eZDataTypeBaseTest extends eZDatatypeTestCase
{
    public function testCreateReturnsOneInstancePerTypeAndNothingForUnknownTypes()
    {
        $this->assertSame( eZDataType::create( 'ezstring' ), eZDataType::create( 'ezstring' ) );
        $this->assertInstanceOf( 'eZStringType', eZDataType::create( 'ezstring' ) );
        $this->assertNull( eZDataType::create( 'k1nosuchtype' ) );
        $this->assertContains( 'ezstring', eZDataType::allowedTypes() );
        $registered = eZDataType::registeredDataTypes();
        $this->assertArrayHasKey( 'ezstring', $registered );
        $this->assertInstanceOf( 'eZStringType', $registered['ezstring'] );
    }

    public function testNamesTemplatesAndAttributes()
    {
        $type = new eZDataTypeBaseTestPlainType();
        $this->assertSame( 'k1plain', $type->isA() );
        $this->assertSame( 'k1plain', $type->viewTemplate( null ) );
        $this->assertSame( 'k1plain', $type->editTemplate( null ) );
        $this->assertSame( 'k1plain', $type->informationTemplate( null ) );
        $collection = null;
        $this->assertSame( 'k1plain', $type->resultTemplate( $collection ) );
        $this->assertTrue( $type->hasAttribute( 'information' ) );
        $this->assertFalse( $type->hasAttribute( 'nothing' ) );
        $info = $type->attribute( 'information' );
        $this->assertSame( 'k1plain', $info['string'] );
        $this->assertSame( 'K1 plain', $info['name'] );
        $this->assertNull( $type->attribute( 'nothing' ) );
        $this->assertTrue( $type->isTranslatable() );
    }

    public function testNoOpDefaults()
    {
        $type = new eZDataTypeBaseTestPlainType();
        $attribute = $this->objectAttribute( 'ezstring' );
        $classAttribute = $this->classAttribute( 'ezstring' );
        $http = $this->post( array() );
        $result = null;
        $this->assertFalse( $type->isHTTPFileInsertionSupported() );
        $this->assertFalse( $type->isRegularFileInsertionSupported() );
        $this->assertFalse( $type->isSimpleStringInsertionSupported() );
        $this->assertFalse( $type->isRelationType() );
        $this->assertNull( $type->insertSimpleString( null, 1, 'eng-GB', $attribute, 'x', $result ) );
        $this->assertFalse( $type->hasStoredFileInformation( null, 1, 'eng-GB', $attribute ) );
        $this->assertFalse( $type->storedFileInformation( null, 1, 'eng-GB', $attribute ) );
        $this->assertNull( $type->productOptionInformation( $attribute, 1, null ) );
        $this->assertSame( '', $type->objectAttributeContent( $attribute ) );
        $this->assertFalse( $type->hasObjectAttributeContent( $attribute ) );
        $this->assertSame( '', $type->classAttributeContent( $classAttribute ) );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateClassAttributeHTTPInput( $http, 'ContentClass', $classAttribute ) );
        $this->assertNull( $type->fetchClassAttributeHTTPInput( $http, 'ContentClass', $classAttribute ) );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute ) );
        $this->assertNull( $type->fetchObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateCollectionAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute ) );
        $this->assertNull( $type->fetchCollectionAttributeHTTPInput( null, null, $http, 'ContentObjectAttribute', $attribute ) );
        $this->assertFalse( $type->hasInformationCollection() );
        $this->assertSame( '', $type->title( $attribute ) );
        $this->assertFalse( $type->isIndexable() );
        $this->assertFalse( $type->isInformationCollector() );
        $this->assertFalse( $type->isAddToBasketValidationRequired() );
        $this->assertSame( '', $type->sortKey( $attribute ) );
        $this->assertFalse( $type->sortKeyType() );
        $this->assertFalse( $type->customSorting() );
        $this->assertSame( '', $type->metaData( $attribute ) );
        $this->assertSame( '', $type->toString( $attribute ) );
        $this->assertFalse( $type->templateList() );
        $this->assertFalse( $type->supportsBatchInitializeObjectAttribute() );
        $this->assertSame( array(), $type->batchInitializeObjectAttributeData( $classAttribute ) );
        $this->assertTrue( $type->isClassAttributeRemovable( $classAttribute ) );
        $value = null;
        $this->assertFalse( $type->fetchActionValue( 'other', 'remove', $value ) );
        $this->assertTrue( $type->fetchActionValue( 'remove_5', 'remove', $value ) );
        $this->assertSame( '5', $value );
        $this->assertSame( 'share/db_data.dba', $type->getDBAFileName() );
    }

    public function testDisplayInformationFollowsDatatypeIni()
    {
        $type = eZDataType::create( 'ezmatrix' );
        $attribute = $this->objectAttribute( 'ezmatrix' );
        $grouped = eZINI::instance( 'datatype.ini' )->variable( 'EditSettings', 'GroupedInput' );
        $info = $type->objectDisplayInformation( $attribute );
        $this->assertSame( in_array( 'ezmatrix', $grouped ), $info['edit']['grouped_input'] );
        $this->assertSame( array( 'edit', 'view', 'collection', 'result' ), array_keys( $info ) );

        $merged = $type->objectDisplayInformation( $this->objectAttribute( 'k1plain' ), array( 'view' => array( 'grouped_input' => true, 'extra' => 1 ) ) );
        $this->assertSame( array( 'grouped_input' => true, 'extra' => 1 ), $merged['view'] );
        $this->assertFalse( $merged['edit']['grouped_input'] );

        $class = $type->classDisplayInformation( $this->classAttribute( 'k1plain' ), array( 'view' => array( 'x' => 2 ) ) );
        $this->assertSame( array( 'grouped_input' => false ), $class['edit'] );
        $this->assertSame( array( 'x' => 2 ), $class['view'] );
        $this->assertSame( array( 'edit' => array( 'grouped_input' => false ), 'view' => array() ), $type->classDisplayInformation( $this->classAttribute( 'k1plain' ) ) );
    }

    public function testContentActionsOfInformationCollectors()
    {
        $type = new eZDataTypeBaseTestPlainType();
        $this->assertSame( array(), $type->contentActionList( $this->classAttribute( 'ezstring' ) ) );
        $actions = $type->contentActionList( $this->classAttribute( 'ezstring', array( 'is_information_collector' => 1 ) ) );
        $this->assertSame( 'ActionCollectInformation', $actions[0]['action'] );
        $this->assertSame( array(), $type->contentActionList( 'not an attribute' ) );
    }

    public function testUnsupportedSerializationIsMarked()
    {
        list( $attributeNode, $parametersNode ) = $this->serializationNodes();
        ( new eZDataTypeBaseTestPlainType() )->serializeContentClassAttribute( $this->classAttribute( 'k1plain' ), $attributeNode, $parametersNode );
        $this->assertSame( 'true', $attributeNode->getAttribute( 'unsupported' ) );
        list( $attributeNode, $parametersNode ) = $this->serializationNodes();
        ( new eZDataTypeBaseTestPlainType( array( 'serialize_supported' => true ) ) )->serializeContentClassAttribute( $this->classAttribute( 'k1plain' ), $attributeNode, $parametersNode );
        $this->assertFalse( $attributeNode->hasAttribute( 'unsupported' ) );
    }

    public function testObjectSerializationWithoutAMapKeepsTheThreeFields()
    {
        $type = new eZDataTypeBaseTestPlainType();
        $attribute = $this->objectAttribute( 'ezstring', array(), array( 'data_int' => 7, 'data_float' => 1.5, 'data_text' => 'text & more' ) );
        $node = $type->serializeContentObjectAttribute( null, $attribute );
        $this->assertSame( 'k1plain', $node->getAttribute( 'type' ) );
        $this->assertSame( 'k1_field', $node->getAttributeNS( 'http://ez.no/ezobject', 'identifier' ) );
        $copy = $this->objectAttribute( 'ezstring' );
        $type->unserializeContentObjectAttribute( null, $copy, $node );
        $this->assertEquals( 7, $copy->attribute( 'data_int' ) );
        $this->assertEquals( 1.5, $copy->attribute( 'data_float' ) );
        $this->assertSame( 'text & more', $copy->attribute( 'data_text' ) );
    }

    public static function mappedValueProvider()
    {
        return array(
            'integer' => array( 'ezinteger', 'data_int', 42 ),
            'integer without a value' => array( 'ezinteger', 'data_int', null ),
            'float' => array( 'ezfloat', 'data_float', 2.5 ),
            'float without a value' => array( 'ezfloat', 'data_float', null ),
            'text line' => array( 'ezstring', 'data_text', 'Hello' ),
            'checkbox' => array( 'ezboolean', 'data_int', 1 ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('mappedValueProvider')]
    public function testObjectSerializationThroughTheMapKeepsTheValue( $dataTypeString, $field, $value )
    {
        $type = eZDataType::create( $dataTypeString );
        $node = $type->serializeContentObjectAttribute( null, $this->objectAttribute( $dataTypeString, array(), array( $field => $value ) ) );
        $copy = $this->objectAttribute( $dataTypeString, array(), array( $field => 99 ) );
        $type->unserializeContentObjectAttribute( null, $copy, $node );
        if ( $value === null )
        {
            $this->assertNull( $copy->attribute( $field ), 'no value stays no value' );
            $this->assertFalse( $type->hasObjectAttributeContent( $copy ) );
        }
        else
        {
            $this->assertEquals( $value, $copy->attribute( $field ) );
        }
    }

    public function testMappedElementMissingFromThePackageLeavesTheField()
    {
        $type = eZDataType::create( 'ezinteger' );
        $dom = new DOMDocument();
        $dom->loadXML( '<attribute/>' );
        $copy = $this->objectAttribute( 'ezinteger', array(), array( 'data_int' => 5 ) );
        $type->unserializeContentObjectAttribute( null, $copy, $dom->documentElement );
        $this->assertSame( 5, $copy->attribute( 'data_int' ) );
    }
}
