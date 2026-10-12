<?php
/**
 * Search meta data of the object relation datatypes: the searchable attributes of a related object are indexed
 * with the object that points to it, its own relation attributes are left out, so indexing never follows a chain
 * of relations (and needs no recursion guard).
 *
 *  RM-01 - eZContentObjectAttribute::metaDataArray( $attributes, true ) leaves out relation attributes and
 *          attributes that are not searchable; without the flag the relation attributes are asked as before
 *  RM-02 - ezobjectrelation and ezobjectrelationlist are relation datatypes (isRelationType()), ezstring is not
 *  RM-03 - eZObjectRelationType::metaData(): the related object's text attributes, never its relation attributes,
 *          also when the related object points back (a cycle)
 *  RM-04 - eZObjectRelationListType::metaData(): the same for every related object of the list
 *
 * This is what LoveStack's commit 5c089f6250 does; Exponential has had the same behaviour since 2018 (EZP-27101,
 * doc/bc/5.90/relation_indexing.md), so there is no setting for it. No database: attributes, classes and objects
 * are test doubles.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/datatypes/eZObjectRelationMetaDataTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class eZObjectRelationMetaDataTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        chdir( dirname( __DIR__, 4 ) );
    }

    /**
     * An attribute double: searchable or not, of a relation datatype or not, with the meta data its datatype
     * gives. $askedCount counts how often the datatype's metaData() was called.
     */
    private function attribute( $text, $isRelation, $searchable = true, &$askedCount = null )
    {
        $askedCount = 0;
        $counter = &$askedCount;
        $classAttribute = $this->createStub( 'eZContentClassAttribute' );
        $classAttribute->method( 'attribute' )->willReturnCallback( function ( $name ) use ( $searchable ) {
            return $name === 'is_searchable' ? ( $searchable ? 1 : 0 ) : null;
        } );
        $datatype = $this->createStub( 'eZDataType' );
        $datatype->method( 'isRelationType' )->willReturn( $isRelation );
        $datatype->method( 'metaData' )->willReturnCallback( function () use ( $text, &$counter ) {
            $counter++;
            return $text;
        } );
        $attribute = $this->createStub( 'eZContentObjectAttribute' );
        $attribute->method( 'contentClassAttribute' )->willReturn( $classAttribute );
        $attribute->method( 'dataType' )->willReturn( $datatype );
        $attribute->method( 'attribute' )->willReturnCallback( function ( $name ) {
            return $name === 'language_code' ? 'eng-GB' : null;
        } );
        return $attribute;
    }

    private static function texts( $metaData )
    {
        return array_map( function ( $part ) { return $part['text']; }, (array)$metaData );
    }

    /** RM-01 */
    public function testMetaDataArraySkipsRelationAttributes()
    {
        $relationAsked = 0;
        $attributes = array( $this->attribute( 'Title text', false ),
                             $this->attribute( 'Related text', true, true, $relationAsked ),
                             $this->attribute( 'Hidden text', false, false ) );

        $this->assertSame( array( 'Title text' ), self::texts( eZContentObjectAttribute::metaDataArray( $attributes, true ) ) );
        $this->assertSame( 0, $relationAsked );

        $this->assertSame( array( 'Title text', 'Related text' ), self::texts( eZContentObjectAttribute::metaDataArray( $attributes ) ) );
        $this->assertSame( 1, $relationAsked );
    }

    /** RM-02 */
    public function testRelationDatatypes()
    {
        $this->assertTrue( ( new eZObjectRelationType() )->isRelationType() );
        $this->assertTrue( ( new eZObjectRelationListType() )->isRelationType() );
        $this->assertFalse( ( new eZStringType() )->isRelationType() );
    }

    /** RM-03 */
    public function testObjectRelationLeavesOutTheRelatedObjectsRelations()
    {
        $backAsked = 0;
        $relatedAttributes = array( $this->attribute( 'Related title', false ),
                                    $this->attribute( 'Points back', true, true, $backAsked ) );
        $version = $this->createStub( 'eZContentObjectVersion' );
        $version->method( 'translationList' )->willReturn( array( 'eng-GB' ) );
        $version->method( 'contentObjectAttributes' )->willReturn( $relatedAttributes );
        $related = $this->createStub( 'eZContentObject' );
        $related->method( 'attribute' )->willReturnCallback( function ( $name ) use ( $version ) {
            return $name === 'current' ? $version : ( $name === 'id' ? 1001 : null );
        } );

        $type = self::relationTypeRelatedTo( $related );
        $this->assertSame( array( 'Related title' ), self::texts( $type->metaData( $this->attribute( '', true ) ) ) );
        // asked twice, as a cycle would without a guard: the same answer, the relation never followed
        $this->assertSame( array( 'Related title' ), self::texts( $type->metaData( $this->attribute( '', true ) ) ) );
        $this->assertSame( 0, $backAsked );

        $this->assertFalse( self::relationTypeRelatedTo( null )->metaData( $this->attribute( '', true ) ) );
    }

    /** An ezobjectrelation datatype whose related object is $object, without a database */
    private static function relationTypeRelatedTo( $object )
    {
        return new class( $object ) extends eZObjectRelationType {
            private $related;
            public function __construct( $related )
            {
                parent::__construct();
                $this->related = $related;
            }
            function objectAttributeContent( $contentObjectAttribute )
            {
                return $this->related;
            }
        };
    }

    /** RM-04 */
    public function testObjectRelationListLeavesOutTheRelatedObjectsRelations()
    {
        $relationAsked = 0;
        $list = $this->createStub( 'eZContentObjectAttribute' );
        $list->method( 'attribute' )->willReturnCallback( function ( $name ) {
            return $name === 'language_code' ? 'eng-GB' : null;
        } );
        $list->method( 'content' )->willReturn( array(
            'relation_list' => array( array( 'contentobject_id' => 501, 'contentobject_version' => 1 ),
                                      array( 'contentobject_id' => 502, 'contentobject_version' => 1 ),
                                      array( 'contentobject_id' => 0, 'contentobject_version' => 0 ) ),
            'temp' => array(
                501 => array( 'attributes' => array( $this->attribute( 'First', false ),
                                                     $this->attribute( 'First relation', true, true, $relationAsked ) ) ),
                502 => array( 'attributes' => array( $this->attribute( 'Second', false ) ) ) ) ) );

        $this->assertSame( array( 'First', 'Second' ), self::texts( ( new eZObjectRelationListType() )->metaData( $list ) ) );
        $this->assertSame( 0, $relationAsked );
    }
}
