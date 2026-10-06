<?php
/**
 * Information collection on a throwaway feedback form: collections stored as the collector does it, counted and
 * listed per object, creator and user identifier, with sorting and limits; their attributes in class order and
 * by identifier; counts per attribute and per value; the collection settings of the form (type, mail, display,
 * user data handling, anonymous users); removing one collection and all of an object.
 *
 * Nothing is mailed: the collections are written directly, not through the collect view.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/expContentModelLiveTestCase.php';

class eZInformationCollectionLiveTest extends expContentModelLiveTestCase
{
    protected static $form;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        if ( !eZContentClass::fetchByIdentifier( 'feedback_form' ) )
            self::markTestSkipped( "needs the content class 'feedback_form'" );
        static::$form = static::createObject( 'feedback_form', static::$root['node'], array( 'name' => 'k1c form' ) );
    }

    public static function tearDownAfterClass(): void
    {
        if ( static::$form )
            eZInformationCollection::removeContentObject( static::$form['object'] );
        parent::tearDownAfterClass();
    }

    protected function tearDown(): void
    {
        if ( static::$form )
            eZInformationCollection::removeContentObject( static::$form['object'] );
        parent::tearDown();
    }

    /**
     * Stores a collection of the form with the given texts per attribute identifier.
     *
     * @return eZInformationCollection
     */
    private function collect( array $values, $userIdentifier, $creatorID = false, $created = false )
    {
        $object = eZContentObject::fetch( static::$form['object'] );
        $collection = eZInformationCollection::create( static::$form['object'], $userIdentifier, $creatorID );
        if ( $created )
        {
            $collection->setAttribute( 'created', $created );
            $collection->setAttribute( 'modified', $created );
        }
        $collection->store();
        foreach ( $object->dataMap() as $identifier => $attribute )
        {
            if ( !$attribute->attribute( 'contentclass_attribute' )->attribute( 'is_information_collector' ) )
                continue;
            $collected = eZInformationCollectionAttribute::create( $collection->attribute( 'id' ) );
            $collected->setAttribute( 'contentclass_attribute_id', $attribute->attribute( 'contentclassattribute_id' ) );
            $collected->setAttribute( 'contentobject_attribute_id', $attribute->attribute( 'id' ) );
            $collected->setAttribute( 'contentobject_id', static::$form['object'] );
            $collected->setAttribute( 'data_text', isset( $values[$identifier] ) ? $values[$identifier] : '' );
            $collected->store();
        }
        return $collection;
    }

    public function testCollectionsAreCountedAndListed()
    {
        $this->assertSame( 0, (int)eZInformationCollection::fetchCollectionCountForObject( static::$form['object'] ) );
        $first = $this->collect( array( 'sender_name' => 'Ann', 'email' => 'ann@k1c.example.invalid', 'subject' => 'One', 'message' => 'Hi' ), 'k1c-user-a', 14, time() - 100 );
        $second = $this->collect( array( 'sender_name' => 'Bob', 'email' => 'bob@k1c.example.invalid', 'subject' => 'Two', 'message' => 'Hello' ), 'k1c-user-b', 10, time() - 50 );
        $third = $this->collect( array( 'sender_name' => 'Ann again', 'email' => 'ann@k1c.example.invalid', 'subject' => 'Three', 'message' => 'Me' ), 'k1c-user-a', 14, time() );

        $this->assertSame( 3, (int)eZInformationCollection::fetchCollectionCountForObject( static::$form['object'] ) );
        $this->assertFalse( eZInformationCollection::fetchCollectionCountForObject( 'x' ) );
        $this->assertSame( 3, (int)eZInformationCollection::fetchCollectionsCount( static::$form['object'] ) );
        $this->assertSame( 2, (int)eZInformationCollection::fetchCollectionsCount( static::$form['object'], false, 'k1c-user-a' ) );
        $this->assertSame( 1, (int)eZInformationCollection::fetchCollectionsCount( static::$form['object'], 10 ) );

        $ids = function ( $list ) { return array_map( function ( $c ) { return (int)$c->attribute( 'id' ); }, $list ); };
        $byCreated = eZInformationCollection::fetchCollectionsList( static::$form['object'], false, false, false, array( 'created', true ) );
        $this->assertSame( $ids( array( $first, $second, $third ) ), $ids( $byCreated ) );
        $newest = eZInformationCollection::fetchCollectionsList( static::$form['object'], false, false, array( 'limit' => 2, 'offset' => 0 ), array( 'created', false ) );
        $this->assertSame( $ids( array( $third, $second ) ), $ids( $newest ) );
        $ofA = eZInformationCollection::fetchCollectionsList( static::$form['object'], false, 'k1c-user-a', false, array( 'created', true ) );
        $this->assertSame( $ids( array( $first, $third ) ), $ids( $ofA ) );
        $rows = eZInformationCollection::fetchCollectionsList( static::$form['object'], 10, false, false, false, false );
        $this->assertSame( (int)$second->attribute( 'id' ), (int)$rows[0]['id'] );

        $byUser = eZInformationCollection::fetchByUserIdentifier( 'k1c-user-b', static::$form['object'] );
        $this->assertSame( (int)$second->attribute( 'id' ), (int)$byUser->attribute( 'id' ) );
        $this->assertNull( eZInformationCollection::fetchByUserIdentifier( 'k1c-nobody', static::$form['object'] ) );
        $this->assertSame( (int)$first->attribute( 'id' ), (int)eZInformationCollection::fetch( $first->attribute( 'id' ) )->attribute( 'id' ) );
    }

    public function testAttributesOfACollection()
    {
        $collection = $this->collect( array( 'sender_name' => 'Cleo', 'email' => 'cleo@k1c.example.invalid', 'subject' => 'Sub', 'message' => 'Text' ), 'k1c-user-c' );
        $collection = eZInformationCollection::fetch( $collection->attribute( 'id' ) );
        $attributes = $collection->informationCollectionAttributes();
        $this->assertCount( 4, $attributes );
        $map = $collection->dataMap();
        $this->assertEqualsCanonicalizing( array( 'sender_name', 'subject', 'message', 'email' ), array_keys( $map ) );
        $this->assertSame( 'cleo@k1c.example.invalid', $map['email']->attribute( 'data_text' ) );
        $this->assertSame( 'Cleo', $map['sender_name']->attribute( 'data_text' ) );
        $this->assertSame( static::$form['object'], (int)$collection->object()->attribute( 'id' ) );
        $this->assertCount( 4, $collection->informationCollectionAttributes( false ) );

        // in class order
        $placements = array();
        foreach ( $attributes as $attribute )
            $placements[] = (int)$attribute->attribute( 'contentclass_attribute' )->attribute( 'placement' );
        $sorted = $placements;
        sort( $sorted );
        $this->assertSame( $sorted, $placements );

        $emailAttributeID = (int)$map['email']->attribute( 'contentobject_attribute_id' );
        $this->assertSame( 1, (int)eZInformationCollection::fetchCountForAttribute( $emailAttributeID, false ) );
        $this->collect( array( 'email' => 'dan@k1c.example.invalid' ), 'k1c-user-d' );
        $this->assertSame( 2, (int)eZInformationCollection::fetchCountForAttribute( $emailAttributeID, false ) );
        $counts = eZInformationCollection::fetchCountList( $emailAttributeID );
        $this->assertSame( 2, (int)array_sum( $counts ) );
    }

    public function testRemoval()
    {
        $one = $this->collect( array( 'email' => 'one@k1c.example.invalid' ), 'k1c-1' );
        $this->collect( array( 'email' => 'two@k1c.example.invalid' ), 'k1c-2' );
        eZInformationCollection::removeCollection( 'not a number' );
        $this->assertSame( 2, (int)eZInformationCollection::fetchCollectionCountForObject( static::$form['object'] ) );
        eZInformationCollection::removeCollection( $one->attribute( 'id' ) );
        $this->assertSame( 1, (int)eZInformationCollection::fetchCollectionCountForObject( static::$form['object'] ) );
        $this->assertSame( array(), eZDB::instance()->arrayQuery( 'SELECT id FROM ezinfocollection_attribute WHERE informationcollection_id=' . (int)$one->attribute( 'id' ) ) );
        eZInformationCollection::removeContentObject( static::$form['object'] );
        $this->assertSame( 0, (int)eZInformationCollection::fetchCollectionCountForObject( static::$form['object'] ) );
    }

    public function testSettingsOfTheForm()
    {
        $object = eZContentObject::fetch( static::$form['object'] );
        $type = eZInformationCollection::typeForObject( $object );
        $this->assertNotEmpty( $type );
        $this->assertSame( $type, eZInformationCollection::templateForObject( $object ) );
        $this->assertIsBool( eZInformationCollection::sendOutEmail( $object ) );
        $this->assertIsBool( eZInformationCollection::allowAnonymous( $object ) );
        $this->assertContains( eZInformationCollection::displayHandling( $object ), array( 'result', 'redirect', 'node' ) );
        $this->assertContains( eZInformationCollection::userDataHandling( $object ), array( 'multiple', 'unique', 'overwrite' ) );
        foreach ( array( 'typeForObject', 'sendOutEmail', 'allowAnonymous', 'displayHandling', 'userDataHandling', 'redirectURL' ) as $method )
            $this->assertFalse( eZInformationCollection::$method( null ), "$method without an object" );
        $this->assertIsArray( eZInformationCollection::attributeHideList() );

        $user = eZUser::fetchByName( 'admin' );
        $this->assertSame( md5( 'ezuser-' . $user->attribute( 'contentobject_id' ) ), eZInformationCollection::generateUserIdentifier( $user ) );
    }

    public static function sortParamProvider()
    {
        return array(
            array( array( 'created', true ), array( 'created' => 'asc' ) ),
            array( array( 'created', false ), array( 'created' => 'desc' ) ),
            array( array( 'k1c_unknown', true ), null ),
            array( array( 'created' ), null ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('sortParamProvider')]
    public function testSortArrayFromParam( $param, $expected )
    {
        $this->assertSame( $expected, eZInformationCollection::getSortArrayFromParam( eZInformationCollection::definition(), $param ) );
    }
}
