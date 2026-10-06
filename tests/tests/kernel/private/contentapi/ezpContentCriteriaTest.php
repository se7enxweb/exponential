<?php
/**
 * The content API's query building without the database: ezpContentCriteria with its accept and deny sets, each
 * criterion's translation and description (location, class, depth, limit, sorting), the translation into fetch
 * parameters by ezpContentRepository, ezpContentLocation, and ezpContentFieldSet / ezpContentField over attributes
 * built in memory.
 *
 * No database: nodes and attributes are built from rows, nothing is stored or fetched.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class ezpContentCriteriaTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
    }

    private static function location( $nodeId )
    {
        return ezpContentLocation::fromNode( new eZContentObjectTreeNode( array( 'node_id' => $nodeId, 'path_string' => "/1/$nodeId/", 'is_main' => 1 ) ) );
    }

    private static function translate( ezpContentCriteria $criteria )
    {
        $method = new ReflectionMethod( 'ezpContentRepository', 'translateFetchParams' );
        return $method->invoke( null, $criteria );
    }

    // ---------------------------------------------------------------- criteria set

    public function testCriteriaSetCollectsAndIterates()
    {
        $set = new ezpContentCriteriaSet();
        $this->assertCount( 0, $set );
        $set[] = 'a';
        $set['ignored key'] = 'b';
        $this->assertCount( 2, $set );
        $this->assertSame( array( 'a', 'b' ), iterator_to_array( $set ) );
        $this->assertSame( array( 'a', 'b' ), iterator_to_array( $set ), 'a second loop starts again' );
    }

    // ---------------------------------------------------------------- single criteria

    public function testDepth()
    {
        $depth = ezpContentCriteria::depth( '3' );
        $this->assertSame( array( 'type' => 'param', 'name' => array( 'Depth' ), 'value' => array( 3 ) ), $depth->translate() );
        $this->assertSame( 'With depth 3', (string)$depth );
        $this->assertSame( 'With depth 1', (string)ezpContentCriteria::depth() );
    }

    public function testSorting()
    {
        $asc = ezpContentCriteria::sorting( 'published', 'asc' );
        $this->assertSame( array( array( 'published', true ) ), $asc->translate()['value'] );
        $this->assertSame( array( 'SortBy' ), $asc->translate()['name'] );
        $this->assertSame( 'With sortKey published and asc sortOrder', (string)$asc );
        $desc = ezpContentCriteria::sorting( 'name', 'anything else' );
        $this->assertSame( array( array( 'name', false ) ), $desc->translate()['value'] );
        $this->assertSame( 'With sortKey name and desc sortOrder', (string)$desc );
    }

    public function testLimitKeepsOnlySensibleValues()
    {
        $limit = ezpContentCriteria::limit();
        $this->assertSame( array( 0, null ), $limit->translate()['value'] );
        $this->assertSame( $limit, $limit->offset( '5' )->limit( '10' ) );
        $this->assertSame( array( 'type' => 'param', 'name' => array( 'Offset', 'Limit' ), 'value' => array( 5, 10 ) ), $limit->translate() );
        $limit->offset( -1 )->limit( 0 );
        $this->assertSame( array( 5, 10 ), $limit->translate()['value'] );
        $limit->offset( 0 )->limit( -3 );
        $this->assertSame( array( 0, 10 ), $limit->translate()['value'] );
        $this->assertSame( 'With offset : 0 / limit : 10', (string)$limit );
    }

    public function testLocation()
    {
        $criteria = ezpContentCriteria::location();
        $this->assertSame( $criteria, $criteria->subtree( self::location( 42 ) ) );
        $this->assertSame( array( 'type' => 'location', 'value' => 42 ), $criteria->translate() );
        $this->assertSame( 'part of subtree 42', (string)$criteria );
    }

    public static function classDescriptionProvider()
    {
        return array(
            'none' => array( array(), 'N/A' ),
            'one' => array( array( 'article' ), 'Content class is article' ),
            'two' => array( array( 'article', 'folder' ), 'Content class is one of article or folder' ),
            'three' => array( array( 'article', 'folder', 'image' ), 'Content class is one of article, folder or image' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('classDescriptionProvider')]
    public function testClassDescription( $classes, $expected )
    {
        $criteria = ezpContentCriteria::contentClass();
        foreach ( $classes as $class )
            $this->assertSame( $criteria, $criteria->is( $class ) );
        $this->assertSame( $expected, (string)$criteria );
        $this->assertSame( array( 'type' => 'param', 'name' => array( 'ClassFilterType', 'ClassFilterArray' ), 'value' => array( 'include', $classes ) ),
                           $criteria->translate() );
    }

    // ---------------------------------------------------------------- whole criteria

    public function testEmptyCriteriaDescription()
    {
        $this->assertSame( "Criteria\n- 0 accept criteria\n- 0 deny criteria\n", (string)new ezpContentCriteria() );
    }

    public function testCriteriaDescriptionListsEachCriterion()
    {
        $criteria = new ezpContentCriteria();
        $criteria->accept[] = ezpContentCriteria::depth( 2 );
        $criteria->accept[] = ezpContentCriteria::contentClass()->is( 'article' );
        $criteria->deny[] = ezpContentCriteria::depth( 5 );
        $this->assertSame( "Criteria\n- 2 accept criteria:\n    - With depth 2\n    - Content class is article\n"
                         . "- 1 deny criteria:\n    - With depth 5\n", (string)$criteria );
    }

    public function testRepositoryTranslatesAcceptedCriteriaIntoFetchParameters()
    {
        $criteria = new ezpContentCriteria();
        $criteria->accept[] = ezpContentCriteria::location()->subtree( self::location( 2 ) );
        $criteria->accept[] = ezpContentCriteria::location()->subtree( self::location( 43 ) );
        $criteria->accept[] = ezpContentCriteria::contentClass()->is( 'article' )->is( 'blog_post' );
        $criteria->accept[] = ezpContentCriteria::limit()->offset( 10 )->limit( 5 );
        $criteria->accept[] = ezpContentCriteria::sorting( 'published', 'desc' );
        $criteria->accept[] = ezpContentCriteria::depth( 3 );
        $criteria->deny[] = ezpContentCriteria::depth( 9 );
        $params = self::translate( $criteria );
        $this->assertSame( array( 2, 43 ), $params->rootNodeId );
        $this->assertSame( array(
            'ClassFilterType' => 'include', 'ClassFilterArray' => array( 'article', 'blog_post' ),
            'Offset' => 10, 'Limit' => 5,
            'SortBy' => array( 'published', false ),
            'Depth' => 3,
        ), $params->params );
    }

    public function testNoCriteriaNoParameters()
    {
        $params = self::translate( new ezpContentCriteria() );
        $this->assertSame( array(), $params->rootNodeId );
        $this->assertSame( array(), $params->params );
    }

    public function testUnfinishedCriteriaReturnNothing()
    {
        $this->assertNull( ezpContentCriteria::k1Unknown() );
        $this->assertNull( ezpContentCriteria::custom( 'x', array() ) );
        $this->assertInstanceOf( 'ezpContentFieldCriteria', ezpContentCriteria::field( 'title' ) );
    }

    // ---------------------------------------------------------------- location

    public function testLocationGivesOnlyItsNodeProperties()
    {
        $location = self::location( 7 );
        $this->assertInstanceOf( 'ezpLocation', $location );
        $this->assertSame( 7, $location->node_id );
        $this->assertSame( '/1/7/', $location->path_string );
        $this->expectException( 'ezcBasePropertyNotFoundException' );
        $location->contentobject_id;
    }

    // ---------------------------------------------------------------- fields

    private static function attribute( $identifier, $text )
    {
        $attribute = new eZContentObjectAttribute( array( 'id' => 1, 'data_type_string' => 'ezstring', 'data_text' => $text, 'language_code' => 'eng-GB' ) );
        $attribute->ContentClassAttributeIdentifier = $identifier;
        return $attribute;
    }

    public function testFieldSetFromDataMap()
    {
        $set = ezpContentFieldSet::fromDataMap( array( self::attribute( 'title', 'Hello' ), self::attribute( 'intro', 'World' ) ) );
        $this->assertTrue( isset( $set->title ) );
        $this->assertFalse( isset( $set->body ) );
        $this->assertInstanceOf( 'ezpContentField', $set->title );
        $this->assertSame( 'Hello', (string)$set->title );
        $this->assertSame( 'World', $set->intro->data_text );
        $this->assertSame( 'eng-GB', $set->intro->language_code );
        $names = array();
        foreach ( $set as $identifier => $field )
            $names[$identifier] = (string)$field;
        $this->assertSame( array( 'title' => 'Hello', 'intro' => 'World' ), $names );
    }

    public function testUnknownLanguageIsRefused()
    {
        $set = ezpContentFieldSet::fromDataMap( array( self::attribute( 'title', 'x' ) ) );
        $this->expectException( Exception::class );
        $this->expectExceptionMessage( 'Language ger-DE could not be found on this ezpContent' );
        $set['ger-DE'];
    }

    public function testFieldForwardsToTheAttribute()
    {
        $field = ezpContentField::fromContentObjectAttribute( self::attribute( 'title', 'Text' ) );
        $this->assertSame( 'Text', $field->toString() );
        $this->assertSame( 'ezstring', $field->attribute( 'data_type_string' ) );
        $this->assertSame( '', (string)new ezpContentField() );
    }

    public function testFieldRefusesUnknownMethodsAndProperties()
    {
        $field = ezpContentField::fromContentObjectAttribute( self::attribute( 'title', 'Text' ) );
        try
        {
            $field->k1NoSuchMethod();
            $this->fail( 'no exception for the method' );
        }
        catch ( ezcBasePropertyNotFoundException $e )
        {
        }
        $this->expectException( 'ezcBasePropertyNotFoundException' );
        $field->k1_no_such_property;
    }
}
