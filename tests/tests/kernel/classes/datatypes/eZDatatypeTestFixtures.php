<?php
/**
 * Stand-ins shared by the datatype tests in this directory, which call the datatypes the way the content and
 * class edit views do, without a database:
 *
 *  - eZDatatypeTestObjectAttribute: a content object attribute whose class attribute is held in memory (the real
 *    one fetches it by id) and whose store() only counts calls.
 *  - eZDatatypeTestClassAttribute: a class attribute whose store() only counts calls.
 *  - eZDatatypeTestNoDatabase: put in place of the database while a test runs, when no other test has opened
 *    one, so a datatype that queries is noticed at once (it throws) instead of reaching alpha's database here
 *    and failing on CI, which has none.
 *  - eZDatatypeTestCase: the base class, which sets the working directory, keeps $_POST, the time zone and the
 *    database instance and puts them back.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class eZDatatypeTestObjectAttribute extends eZContentObjectAttribute
{
    /** @var eZContentClassAttribute */
    public $testClassAttribute;

    public $storeCount = 0;

    function contentClassAttribute()
    {
        return $this->testClassAttribute;
    }

    function store( $fieldFilters = null )
    {
        $this->storeCount++;
    }

    function storeData()
    {
        $this->storeCount++;
    }
}

class eZDatatypeTestClassAttribute extends eZContentClassAttribute
{
    public $storeCount = 0;

    function store( $fieldFilters = null )
    {
        $this->storeCount++;
    }

    function storeDefined()
    {
        $this->storeCount++;
    }
}

class eZDatatypeTestNoDatabase extends eZDBInterface
{
    public function __construct()
    {
    }

    private function refuse( $what )
    {
        throw new RuntimeException( "This test must not use the database ($what)" );
    }

    function query( $sql, $server = false )
    {
        $this->refuse( $sql );
    }

    function arrayQuery( $sql, $params = array(), $server = false )
    {
        $this->refuse( $sql );
    }

    function isConnected()
    {
        return false;
    }

    function escapeString( $str )
    {
        return addslashes( (string)$str );
    }

    function databaseName()
    {
        return 'none';
    }
}

abstract class eZDatatypeTestCase extends PHPUnit\Framework\TestCase
{
    private $savedPost;
    private $savedTimezone;
    private $savedDb;
    private $hadDb;

    public static function setUpBeforeClass(): void
    {
        // The kernel registers its shutdown and exception handlers on first use; do it here, outside a test, so
        // the first test is not reported as one that left an exception handler behind
        chdir( dirname( __DIR__, 5 ) );
        eZExecution::registerShutdownHandler();
    }

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->savedPost = $_POST;
        $_POST = array();
        $this->savedTimezone = date_default_timezone_get();
        date_default_timezone_set( 'UTC' );
        $this->hadDb = array_key_exists( 'eZDBGlobalInstance', $GLOBALS );
        $this->savedDb = $this->hadDb ? $GLOBALS['eZDBGlobalInstance'] : null;
        if ( !eZDB::hasInstance() )
            eZDB::setInstance( new eZDatatypeTestNoDatabase() );
    }

    protected function tearDown(): void
    {
        $_POST = $this->savedPost;
        date_default_timezone_set( $this->savedTimezone );
        if ( $this->hadDb )
            $GLOBALS['eZDBGlobalInstance'] = $this->savedDb;
        else
            unset( $GLOBALS['eZDBGlobalInstance'] );
    }

    /**
     * A class attribute of the datatype, with the given fields.
     *
     * @return eZDatatypeTestClassAttribute
     */
    protected function classAttribute( $dataTypeString, array $fields = array() )
    {
        $row = array_merge( array( 'id' => 901, 'version' => 0, 'contentclass_id' => 77, 'identifier' => 'k1_field',
                                   'data_type_string' => $dataTypeString, 'is_required' => 0, 'is_searchable' => 1,
                                   'is_information_collector' => 0, 'can_translate' => 0, 'placement' => 1,
                                   'data_int1' => 0, 'data_int2' => 0, 'data_int3' => 0, 'data_int4' => 0,
                                   'data_float1' => 0, 'data_float2' => 0, 'data_float3' => 0, 'data_float4' => 0,
                                   'data_text1' => '', 'data_text2' => '', 'data_text3' => '', 'data_text4' => '',
                                   'data_text5' => '', 'category' => '',
                                   'serialized_name_list' => serialize( array( 'eng-GB' => 'K1 field', 'always-available' => 'eng-GB' ) ) ),
                             $fields );
        return new eZDatatypeTestClassAttribute( $row );
    }

    /**
     * An object attribute of the datatype with an in-memory class attribute.
     *
     * @return eZDatatypeTestObjectAttribute
     */
    protected function objectAttribute( $dataTypeString, $classFields = array(), array $fields = array() )
    {
        $classAttribute = $classFields instanceof eZContentClassAttribute ? $classFields : $this->classAttribute( $dataTypeString, $classFields );
        $row = array_merge( array( 'id' => 4711, 'contentobject_id' => 812, 'version' => 1, 'language_code' => 'eng-GB',
                                   'language_id' => 2, 'contentclassattribute_id' => $classAttribute->attribute( 'id' ),
                                   'data_type_string' => $dataTypeString, 'attribute_original_id' => 0,
                                   'data_int' => null, 'data_float' => 0, 'data_text' => '',
                                   'sort_key_int' => 0, 'sort_key_string' => '' ),
                            $fields );
        $attribute = new eZDatatypeTestObjectAttribute( $row );
        $attribute->testClassAttribute = $classAttribute;
        // the real ones are looked up by the class attribute id
        $attribute->setContentClassAttributeIdentifier( $classAttribute->attribute( 'identifier' ) );
        $attribute->setContentClassAttributeName( $classAttribute->attribute( 'name' ) );
        return $attribute;
    }

    /**
     * @return eZDataType
     */
    protected function dataType( $dataTypeString )
    {
        $type = eZDataType::create( $dataTypeString );
        $this->assertInstanceOf( 'eZDataType', $type, "datatype $dataTypeString" );
        return $type;
    }

    protected function post( array $variables )
    {
        $_POST = $variables;
        return eZHTTPTool::instance();
    }

    /**
     * The DOM nodes a class attribute is serialized into for a package.
     *
     * @return DOMElement[] array( attribute node, parameters node )
     */
    protected function serializationNodes()
    {
        $dom = new DOMDocument( '1.0', 'utf-8' );
        $attributeNode = $dom->createElement( 'attribute' );
        $dom->appendChild( $attributeNode );
        $parametersNode = $dom->createElement( 'datatype-parameters' );
        $attributeNode->appendChild( $parametersNode );
        return array( $attributeNode, $parametersNode );
    }

    /**
     * Serializes the class attribute's parameters and reads them into a fresh class attribute of the same type.
     *
     * @return array( eZContentClassAttribute copy, string parameters XML )
     */
    protected function roundTripClassParameters( eZContentClassAttribute $classAttribute )
    {
        $type = $classAttribute->dataType();
        list( $attributeNode, $parametersNode ) = $this->serializationNodes();
        $type->serializeContentClassAttribute( $classAttribute, $attributeNode, $parametersNode );
        $xml = $parametersNode->ownerDocument->saveXML( $parametersNode );

        $dom = new DOMDocument( '1.0', 'utf-8' );
        $dom->loadXML( '<attribute>' . $xml . '</attribute>' );
        $copy = $this->classAttribute( $classAttribute->attribute( 'data_type_string' ) );
        $type->unserializeContentClassAttribute( $copy, $dom->documentElement, $dom->documentElement->firstChild );
        return array( $copy, $xml );
    }
}
