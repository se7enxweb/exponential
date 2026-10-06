<?php
/**
 * A throwaway content class (identifier k1c_...) built through the API as the class editor stores it: a class
 * group, three attributes stored as the defined version, lookups by id, identifier and remote id, the id and
 * identifier caches, attributes in placement order, searchable attributes, translated names, groups, the name
 * and URL alias patterns of objects of the class, object counts, the classes containing a datatype, whether the
 * class can be removed, and its removal with its objects.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/expContentModelLiveTestCase.php';

class eZContentClassLiveTest extends expContentModelLiveTestCase
{
    protected static $classID;
    protected static $identifier;
    protected static $groupID;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $suffix = substr( md5( uniqid( '', true ) ), 0, 8 );
        static::$identifier = 'k1c_' . $suffix;
        $locale = eZContentClass::fetchByIdentifier( 'folder' )->attribute( 'top_priority_language_locale' );

        $group = eZContentClassGroup::create();
        $group->setAttribute( 'name', 'k1c group ' . $suffix );
        $group->store();
        static::$groupID = (int)$group->attribute( 'id' );

        $class = eZContentClass::create( false, array( 'name' => 'K1c thing', 'identifier' => static::$identifier,
                                                       'contentobject_name' => '<title> (<code>)', 'url_alias_name' => '<code|title>',
                                                       'is_container' => 1 ), $locale );
        $class->store();
        static::$classID = (int)$class->attribute( 'id' );
        eZContentClassClassGroup::create( static::$classID, $class->attribute( 'version' ), static::$groupID, $group->attribute( 'name' ) )->store();

        $attributes = array();
        foreach ( array( array( 'title', 'ezstring', 1, 1 ), array( 'code', 'ezinteger', 0, 1 ), array( 'body', 'eztext', 0, 0 ) ) as $i => $def )
        {
            $attribute = eZContentClassAttribute::create( static::$classID, $def[1], array( 'identifier' => $def[0], 'name' => ucfirst( $def[0] ),
                                                                                         'is_required' => $def[2], 'is_searchable' => $def[3],
                                                                                         'placement' => 10 * ( 3 - $i ) ), $locale );
            $attribute->dataType()->initializeClassAttribute( $attribute );
            $attribute->store();
            $attributes[] = $attribute;
        }
        $class->storeDefined( $attributes );
        eZContentClass::expireCache();
        eZContentObject::clearCache();
    }

    public static function tearDownAfterClass(): void
    {
        // the objects of the class first (the base class), then the class and its group
        parent::tearDownAfterClass();
        if ( static::$classID )
        {
            eZContentClassOperations::remove( static::$classID );
            eZContentClass::expireCache();
            $db = eZDB::instance();
            $db->query( 'DELETE FROM ezcontentclass_classgroup WHERE group_id=' . (int)static::$groupID );
            eZContentClassGroup::removeSelected( static::$groupID );
            if ( eZContentClass::fetch( static::$classID ) )
                throw new RuntimeException( 'the test class is still there' );
        }
        static::$classID = null;
    }

    private function k1cClass()
    {
        $class = eZContentClass::fetch( static::$classID );
        $this->assertInstanceOf( 'eZContentClass', $class );
        return $class;
    }

    private function thing( $title, $code = null )
    {
        $attributes = array( 'title' => $title );
        if ( $code !== null )
            $attributes['code'] = (string)$code;
        return static::createObject( static::$identifier, static::$root['node'], $attributes );
    }

    public function testLookups()
    {
        $class = $this->k1cClass();
        $this->assertSame( static::$identifier, $class->attribute( 'identifier' ) );
        $this->assertSame( static::$classID, (int)eZContentClass::fetchByIdentifier( static::$identifier )->attribute( 'id' ) );
        $this->assertSame( static::$classID, (int)eZContentClass::fetchByRemoteID( $class->remoteID() )->attribute( 'id' ) );
        $this->assertSame( static::$classID, (int)eZContentClass::classIDByIdentifier( static::$identifier ) );
        $this->assertSame( static::$identifier, eZContentClass::classIdentifierByID( static::$classID ) );
        $this->assertTrue( (bool)eZContentClass::exists( static::$classID ) );
        $this->assertTrue( (bool)eZContentClass::exists( static::$identifier, eZContentClass::VERSION_STATUS_DEFINED, false, true ) );
        $this->assertFalse( (bool)eZContentClass::exists( 'k1c_no_such_class', eZContentClass::VERSION_STATUS_DEFINED, false, true ) );
        $this->assertNull( eZContentClass::fetchByIdentifier( 'k1c_no_such_class' ) );
        $this->assertSame( eZContentClass::VERSION_STATUS_DEFINED, (int)$class->attribute( 'version' ) );
        $this->assertSame( eZContentClass::VERSION_STATUS_DEFINED, (int)$class->versionStatus() );
        $this->assertSame( 1, (int)$class->versionCount() );
        $this->assertSame( 'K1c thing', $class->name() );
        $this->assertTrue( (bool)$class->attribute( 'is_container' ) );
    }

    public function testAttributesInPlacementOrder()
    {
        $class = $this->k1cClass();
        $identifiers = array();
        foreach ( $class->fetchAttributes() as $attribute )
            $identifiers[] = $attribute->attribute( 'identifier' );
        // given with the placements 30, 20, 10: stored in that order, numbered from 1
        $this->assertSame( array( 'body', 'code', 'title' ), $identifiers );
        $placements = array();
        foreach ( $class->fetchAttributes() as $attribute )
            $placements[] = (int)$attribute->attribute( 'placement' );
        $this->assertSame( array( 1, 2, 3 ), $placements );

        $this->assertSame( array( 'body', 'code', 'title' ), array_keys( $class->dataMap() ) );
        $this->assertSame( 'ezinteger', $class->fetchAttributeByIdentifier( 'code' )->attribute( 'data_type_string' ) );
        $this->assertNull( $class->fetchAttributeByIdentifier( 'k1c_none' ) );
        $searchable = array();
        foreach ( $class->fetchSearchableAttributes() as $attribute )
            $searchable[] = $attribute->attribute( 'identifier' );
        $this->assertSame( array( 'code', 'title' ), $searchable );

        $title = $class->fetchAttributeByIdentifier( 'title' );
        $this->assertTrue( (bool)$title->attribute( 'is_required' ) );
        $this->assertSame( (int)$title->attribute( 'id' ), (int)eZContentClassAttribute::classAttributeIDByIdentifier( static::$identifier . '/title' ) );
        $this->assertSame( 'title', eZContentClassAttribute::classAttributeIdentifierByID( $title->attribute( 'id' ) ) );
        $this->assertSame( 'Title', $title->attribute( 'name' ) );
        $this->assertSame( 'eZStringType', get_class( $title->dataType() ) );
    }

    public function testAdjustAttributePlacementsNumbersFromOneKeepingTheOrder()
    {
        $class = $this->k1cClass();
        $attributes = array();
        foreach ( array( 30, 5, 17 ) as $placement )
            $attributes[] = new eZContentClassAttribute( array( 'placement' => $placement, 'data_type_string' => 'ezstring' ) );
        $class->adjustAttributePlacements( $attributes );
        $this->assertSame( array( 3, 1, 2 ), array_map( function ( $a ) { return (int)$a->attribute( 'placement' ); }, $attributes ) );
        $class->adjustAttributePlacements( 'not a list' );
    }

    public function testGroups()
    {
        $class = $this->k1cClass();
        $this->assertSame( array( static::$groupID ), array_map( 'intval', $class->fetchGroupIDList() ) );
        $this->assertTrue( (bool)$class->inGroup( static::$groupID ) );
        $this->assertFalse( (bool)$class->inGroup( 999999999 ) );
        $groups = $class->fetchGroupList();
        $this->assertCount( 1, $groups );
        $members = eZContentClassClassGroup::fetchClassList( eZContentClass::VERSION_STATUS_DEFINED, static::$groupID );
        $this->assertSame( array( static::$classID ), array_map( function ( $c ) { return (int)$c->attribute( 'id' ); }, $members ) );
    }

    public function testObjectNamesFromThePatterns()
    {
        $class = $this->k1cClass();
        $this->assertSame( 0, (int)$class->objectCount() );
        $this->assertTrue( $class->isRemovable(), 'a class without objects at the top can be removed' );

        $thing = $this->thing( 'Widget', 42 );
        $object = eZContentObject::fetch( $thing['object'] );
        $this->assertSame( 'Widget (42)', $object->name() );
        $this->assertSame( 'Widget (42)', $class->contentObjectName( $object ) );
        $this->assertSame( '42', $class->urlAliasName( $object ) );
        $this->assertStringEndsWith( '/42', eZContentObjectTreeNode::fetch( $thing['node'] )->attribute( 'path_identification_string' ) );

        $noCode = $this->thing( 'Gadget' );
        $object = eZContentObject::fetch( $noCode['object'] );
        $this->assertSame( 'Gadget ()', $object->name() );
        $this->assertSame( 'Gadget', $class->urlAliasName( $object ), '<code|title>: the title when there is no code' );

        eZContentObject::clearCache();
        $class = $this->k1cClass();
        $this->assertSame( 2, (int)$class->objectCount() );
        $this->assertCount( 2, $class->objectList() );
        $this->assertSame( 'Widget (42)', $class->buildContentObjectName( '<title> (<code>)', eZContentObject::fetch( $thing['object'] )->dataMap() ) );
    }

    public function testClassesContainingADatatype()
    {
        $this->assertContains( static::$classID, array_map( 'intval', eZContentClass::fetchIDListContainingDatatype( 'eztext' ) ) );
        $this->assertNotContains( static::$classID, array_map( 'intval', eZContentClass::fetchIDListContainingDatatype( 'ezmatrix' ) ) );
    }

    public function testTranslatedNames()
    {
        $class = $this->k1cClass();
        $locale = $class->alwaysAvailableLanguageLocale();
        $this->assertSame( 'K1c thing', $class->name( $locale ) );
        $this->assertTrue( $class->hasNameInLanguage( $locale ) );
        $this->assertFalse( $class->hasNameInLanguage( 'k1c-XX' ) );
        $this->assertSame( 'K1c thing', eZContentClass::nameFromSerializedString( $class->attribute( 'serialized_name_list' ) ) );
        $this->assertContains( $locale, array_keys( $class->nameList() ) );
    }

    public function testClassListsInclude()
    {
        $ids = array();
        foreach ( eZContentClass::fetchList( eZContentClass::VERSION_STATUS_DEFINED, true ) as $class )
            $ids[] = (int)$class->attribute( 'id' );
        $this->assertContains( static::$classID, $ids );
        $rows = eZContentClass::fetchList( eZContentClass::VERSION_STATUS_DEFINED, false, false, null, null, array( static::$identifier ) );
        $this->assertSame( array( static::$classID ), array_map( 'intval', array_column( $rows, 'id' ) ) );
    }
}
