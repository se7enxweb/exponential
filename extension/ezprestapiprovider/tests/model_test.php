<?php
/**
 * File containing ezpRestContentServiceModeltest class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

/**
 * Tests for model layer of REST content services
 * @backupGlobals disabled
 */
class ezpRestContentServiceModelTest extends ezpDatabaseTestCase
{
    /**
     * Tests the metadata returned by model for given content
     * @group restContentServices
     */
    public function testGetMetadataByContent()
    {
        $res = ezpRestContentModel::getMetadataByContent( ezpContent::fromNodeId( 2 ) );
        self::assertInternalType( PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $res );

        $expectedKeys = array(
            'objectName',
            'classIdentifier',
            'datePublished',
            'dateModified',
            'objectRemoteId',
            'objectId'
        );

        foreach ( $expectedKeys as $key )
        {
            self::assertArrayHasKey( $key, $res, "Content must contain $key metadata" );
        }
    }

    /**
     * Tests metadata returned by model for given location
     * @group restContentServices
     */
    public function testGetMetadataByLocation()
    {
        $res = ezpRestContentModel::getMetadataByLocation( ezpContentLocation::fetchByNodeId( 2 ) );
        self::assertInternalType( PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $res );

        $expectedKeys = array(
            'nodeId',
            'nodeRemoteId',
            'fullUrl'
        );

        foreach ( $expectedKeys as $key )
        {
            self::assertArrayHasKey( $key, $res, "Content location must contain $key metadata" );
            switch ( $key )
            {
                case 'nodeId':
                    self::assertInternalType( PHPUnit_Framework_Constraint_IsType::TYPE_INT, $res[$key], 'NodeId must be an integer' );
                    break;

                case 'nodeRemoteId':
                case 'fullUrl':
                    self::assertInternalType( PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $res[$key] );
                    break;
            }
        }
    }

    /**
     * Tests fields returned by model for given content
     * @group restContentServices
     */
    public function testGetFieldsByContent()
    {
        $content = ezpContent::fromNodeId( 2 );
        $res = ezpRestContentModel::getFieldsByContent( $content );
        self::assertInternalType( PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $res );

        foreach ( $content->fields as $fieldName => $field )
        {
            self::assertArrayHasKey( $fieldName, $res, "Result does not contain '$fieldName' field, present for given content" );
            $attributeOutput = ezpRestContentModel::attributeOutputData( $field );
            self::assertSame( $attributeOutput, $res[$fieldName], "Result for field '$fieldName' must be the same as returned by ezpRestContentModel::attributeOutputData()" );
        }
    }

    /**
     * Tests fields data that will be returned to the output
     * @group restContentServices
     */
    public function testAttributeOutputData()
    {
        $content = ezpContent::fromNodeId( 2 );
        $expectedKeys = array( 'type', 'identifier', 'value', 'id', 'classattribute_id' );

        // Browse all the fields and compare result provided by ezpRestContentModel::attributeOutputData() with manually generated data
        foreach ( $content->fields as $fieldName => $field )
        {
            $aAttributeOutput = ezpRestContentModel::attributeOutputData( $field );
            foreach ( $expectedKeys as $key )
            {
                self::assertArrayHasKey( $key, $aAttributeOutput, "Content field must have '$key' metadata" );
                switch ( $key )
                {
                    case 'type':
                        self::assertInternalType( PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $aAttributeOutput[$key] );
                        self::assertEquals( $field->data_type_string, $aAttributeOutput[$key] );
                    break;

                    case 'identifier':
                        self::assertInternalType( PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $aAttributeOutput[$key] );
                        self::assertEquals( $field->contentclass_attribute_identifier, $aAttributeOutput[$key] );
                    break;

                    case 'value':
                        // Value can be either string or boolean
                        self::assertTrue( is_string( $aAttributeOutput[$key] ) || is_bool( $aAttributeOutput[$key] ) );
                    break;

                    case 'id':
                        self::assertInternalType( PHPUnit_Framework_Constraint_IsType::TYPE_INT, $aAttributeOutput[$key] );
                        self::assertEquals( $field->id, $aAttributeOutput[$key] );
                    break;

                    case 'classattribute_id':
                        self::assertInternalType( PHPUnit_Framework_Constraint_IsType::TYPE_INT, $aAttributeOutput[$key] );
                        self::assertEquals( $field->contentclassattribute_id, $aAttributeOutput[$key] );
                    break;
                }
            }
        }
    }

    /**
     * Data provider for request objects
     */
    public function requestObjectProvider()
    {
        $r1 = new ezpRestRequest();
        $r1->uri = '/api/ezp/content/node/2';
        $r1->contentVariables = array( 'Translation' => 'eng-GB', 'OutputFormat' => 'xhtml' );
        $r1->protocol = 'http-get';
        $r1->host = 'ezpublish.dev';

        $r2 = clone $r1;
        $r2->uri = '/api/ezp/content/node/43'; // Media
        $r2->get = array();

        return array(
            array( $r1, 2 ),
            array( $r2, 43 )
        );
    }

    /**
     * Tests service links for content fields
     * @group restContentServices
     * @dataProvider requestObjectProvider
     * @param ezpRestRequest $request
     * @param int $nodeId
     */
    public function testFieldsLinksByContent( ezpRestRequest $request, $nodeId )
    {
        $mvcConfig = new ezpMvcConfiguration();
        $mvcConfig->runPreRoutingFilters( $request );
        $baseUri = $request->getBaseURI();
        $contentQueryString = $request->getContentQueryString( true );

        $content = ezpContent::fromNodeId( $nodeId );
        $links = ezpRestContentModel::getFieldsLinksByContent( $content, $request );
        self::assertInternalType( PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $links );
        self::assertArrayHasKey( '*', $links, 'Links for fields services must contain a wildcard (*) pointing to a service listing all fields content' ); // * stands for all fields
        self::assertEquals( $baseUri.'/fields'.$contentQueryString, $links['*'] );

        // We must have one entry per field
        foreach ( $content->fields as $fieldName => $field )
        {
            self::assertArrayHasKey( $fieldName, $links, "Service link missing for $fieldName" );
            self::assertEquals( $baseUri.'/field/'.$fieldName.$contentQueryString, $links[$fieldName], "Wrong service link for $fieldName" );
        }
    }
}
?>
