<?php
/**
 * Content objects of a throwaway subtree: names from the class's name pattern, versions made by updating
 * (numbers, statuses, the previous version, older versions kept), translations (names and data per language,
 * the always available flag, removing a translation), relations between objects (common, embedded, linked,
 * through an object relation attribute; reverse relations; removing them), lookups (by remote id, by node id,
 * several ids, same class), the data map and attribute lookups by identifier, copies, and
 * eZContentFunctions::updateAndPublishObject() with parameters it must refuse.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/expContentModelLiveTestCase.php';

class eZContentObjectLiveTest extends expContentModelLiveTestCase
{
    private function object( $objectID )
    {
        eZContentObject::clearCache();
        $object = eZContentObject::fetch( $objectID );
        $this->assertInstanceOf( 'eZContentObject', $object, "object $objectID" );
        return $object;
    }

    private function update( $objectID, array $attributes, array $more = array() )
    {
        $result = eZContentFunctions::updateAndPublishObject( $this->object( $objectID ), array( 'attributes' => $attributes ) + $more );
        $this->assertTrue( $result, 'updated and published' );
        return $this->object( $objectID );
    }

    // ---------------------------------------------------------------- names

    public function testNameFollowsTheNamePattern()
    {
        $item = static::folder( static::$root['node'], 'Long name' );
        $object = $this->object( $item['object'] );
        $this->assertSame( 'Long name', $object->name() );
        $this->assertSame( 'Long name', $object->attribute( 'name' ) );

        $object = $this->update( $item['object'], array( 'short_name' => 'Short' ) );
        $this->assertSame( 'Short', $object->name(), '<short_name|name>: the short name when there is one' );
        $this->assertSame( 'Long name', $object->name( 1 ), 'the name of version 1' );
        $this->assertSame( 'Short', $object->versionLanguageName( 2 ) );
        $this->assertSame( 'Short', eZContentObjectTreeNode::fetch( $item['node'] )->attribute( 'name' ) );
        $names = $object->names();
        $this->assertSame( 'Short', $names[$object->initialLanguageCode()] );
    }

    // ---------------------------------------------------------------- versions

    public function testVersionsOfAnUpdatedObject()
    {
        $item = static::folder( static::$root['node'], 'Versioned' );
        $object = $this->update( $item['object'], array( 'name' => 'Versioned 2' ) );
        $object = $this->update( $item['object'], array( 'name' => 'Versioned 3' ) );

        $this->assertSame( 3, (int)$object->attribute( 'current_version' ) );
        $this->assertSame( 3, (int)$object->currentVersion()->attribute( 'version' ) );
        $this->assertSame( 3, (int)$object->publishedVersion() );
        $this->assertSame( 2, (int)$object->previousVersion() );
        $this->assertSame( 4, (int)$object->nextVersion() );
        $this->assertSame( 3, (int)$object->getVersionCount() );
        $statuses = array();
        foreach ( $object->versions() as $version )
            $statuses[(int)$version->attribute( 'version' )] = (int)$version->attribute( 'status' );
        ksort( $statuses );
        $this->assertSame( array( 1 => eZContentObjectVersion::STATUS_ARCHIVED, 2 => eZContentObjectVersion::STATUS_ARCHIVED,
                                  3 => eZContentObjectVersion::STATUS_PUBLISHED ), $statuses );
        $this->assertSame( 'Versioned 2', $object->version( 2 )->attribute( 'data_map' )['name']->attribute( 'content' ) );
        $this->assertFalse( (bool)$object->version( 99 ) );
        $this->assertCount( 1, $object->versions( true, array( 'conditions' => array( 'status' => eZContentObjectVersion::STATUS_PUBLISHED ) ) ) );
        $this->assertSame( 'Versioned 3', $object->dataMap()['name']->attribute( 'content' ) );
        $this->assertSame( 'Versioned 2', $object->fetchDataMap( 2 )['name']->attribute( 'content' ) );
    }

    public function testNewDraftCopiesTheCurrentVersion()
    {
        $item = static::folder( static::$root['node'], 'Drafted' );
        $object = $this->object( $item['object'] );
        $draft = $object->createNewVersion();
        $this->assertInstanceOf( 'eZContentObjectVersion', $draft );
        $this->assertSame( 2, (int)$draft->attribute( 'version' ) );
        $this->assertSame( eZContentObjectVersion::STATUS_DRAFT, (int)$draft->attribute( 'status' ) );
        $this->assertSame( 'Drafted', $draft->attribute( 'data_map' )['name']->attribute( 'content' ) );
        $this->assertSame( 1, (int)$this->object( $item['object'] )->attribute( 'current_version' ), 'a draft is not published' );
        $draft->removeThis();
        $this->assertFalse( (bool)$this->object( $item['object'] )->version( 2 ) );
    }

    // ---------------------------------------------------------------- translations

    public function testTranslationAddedAndRemoved()
    {
        if ( !eZContentLanguage::fetchByLocale( 'ger-DE' ) )
            $this->markTestSkipped( 'needs the language ger-DE' );
        $item = static::folder( static::$root['node'], 'Translated' );
        $initial = $this->object( $item['object'] )->initialLanguageCode();
        $object = $this->update( $item['object'], array( 'name' => 'Uebersetzt' ), array( 'language' => 'ger-DE' ) );

        $this->assertEqualsCanonicalizing( array( $initial, 'ger-DE' ), array_keys( $object->allLanguages() ) );
        $this->assertSame( 'Uebersetzt', $object->name( false, 'ger-DE' ) );
        $this->assertSame( 'Translated', $object->name( false, $initial ) );
        $this->assertSame( 'Uebersetzt', $object->fetchDataMap( false, 'ger-DE' )['name']->attribute( 'content' ) );
        $this->assertSame( $initial, $object->initialLanguageCode(), 'the initial language stays' );
        $mask = (int)$object->attribute( 'language_mask' );
        $this->assertSame( 4, $mask & 4, 'ger-DE is in the language mask' );

        $german = eZContentLanguage::fetchByLocale( 'ger-DE' );
        $this->assertTrue( $object->removeTranslation( $german->attribute( 'id' ) ) );
        $object = $this->object( $item['object'] );
        $this->assertSame( array( $initial ), array_keys( $object->allLanguages() ) );
        $this->assertSame( 0, (int)$object->attribute( 'language_mask' ) & 4 );
        $this->assertFalse( $object->removeTranslation( eZContentLanguage::idByLocale( $initial ) ), 'the initial language cannot be removed' );
    }

    // ---------------------------------------------------------------- relations

    public function testCommonRelationsBothWays()
    {
        $a = static::folder( static::$root['node'], 'Relates' );
        $b = static::folder( static::$root['node'], 'Related B' );
        $c = static::folder( static::$root['node'], 'Related C' );
        $object = $this->object( $a['object'] );
        $object->addContentObjectRelation( $b['object'] );
        $object->addContentObjectRelation( $c['object'] );
        $object->addContentObjectRelation( $c['object'] ); // twice: still one relation
        $object = $this->object( $a['object'] );

        $related = $object->relatedContentObjectList();
        $this->assertEqualsCanonicalizing( array( $b['object'], $c['object'] ), $this->ids( $related ) );
        $this->assertSame( 2, (int)$object->relatedContentObjectCount() );
        $this->assertEqualsCanonicalizing( array( $b['object'], $c['object'] ), $this->ids( $object->relatedObjects( false, false, 0, false, array( 'AllRelations' => eZContentObject::RELATION_COMMON ) ) ) );
        $this->assertSame( array(), $this->ids( $object->relatedObjects( false, false, 0, false, array( 'AllRelations' => eZContentObject::RELATION_EMBED ) ) ) );

        $reverse = $this->object( $b['object'] )->reverseRelatedObjectList();
        $this->assertSame( array( $a['object'] ), $this->ids( $reverse ) );
        $this->assertSame( 1, (int)$this->object( $b['object'] )->reverseRelatedObjectCount() );

        $object->removeContentObjectRelation( $b['object'] );
        $object = $this->object( $a['object'] );
        $this->assertSame( array( $c['object'] ), $this->ids( $object->relatedContentObjectList() ) );
        $this->assertSame( array(), $this->ids( $this->object( $b['object'] )->reverseRelatedObjectList() ) );
    }

    public function testEmbeddedAndLinkedRelationsAreKeptApart()
    {
        $a = static::folder( static::$root['node'], 'Embeds' );
        $b = static::folder( static::$root['node'], 'Embedded' );
        $c = static::folder( static::$root['node'], 'Linked' );
        $object = $this->object( $a['object'] );
        $version = $object->attribute( 'current_version' );
        $object->addContentObjectRelation( $b['object'], $version, 0, eZContentObject::RELATION_EMBED );
        $object->addContentObjectRelation( $c['object'], $version, 0, eZContentObject::RELATION_LINK );
        $object = $this->object( $a['object'] );
        $this->assertSame( array( $b['object'] ), $this->ids( $object->embeddedContentObjectList() ) );
        $this->assertSame( array( $c['object'] ), $this->ids( $object->linkedContentObjectList() ) );
        $this->assertSame( 1, (int)$object->embeddedContentObjectCount() );
        $this->assertSame( 1, (int)$object->linkedContentObjectCount() );
        $this->assertSame( array( $a['object'] ), $this->ids( $this->object( $b['object'] )->reverseEmbeddedObjectList() ) );
        $this->assertSame( array( $a['object'] ), $this->ids( $this->object( $c['object'] )->reverseLinkedObjectList() ) );
        $this->assertSame( 1, (int)$this->object( $c['object'] )->reverseLinkedObjectCount() );
        $this->assertSame( 0, (int)$this->object( $c['object'] )->reverseEmbeddedObjectCount() );
        $this->assertSame( array(), $this->ids( $object->relatedObjects( false, false, 0, false, array( 'AllRelations' => eZContentObject::RELATION_COMMON ) ) ), 'no common relation' );
        $this->assertEqualsCanonicalizing( array( $b['object'], $c['object'] ), $this->ids( $object->relatedObjects( false, false, 0, false, array( 'AllRelations' => true ) ) ) );
    }

    public function testObjectRelationAttributeOfAnArticle()
    {
        $image = static::folder( static::$root['node'], 'Picture stand-in' );
        $article = static::createObject( 'article', static::$root['node'], array( 'title' => 'With relation', 'image' => (string)$image['object'],
            'intro' => '<?xml version="1.0" encoding="utf-8"?><section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"><paragraph>x</paragraph></section>' ) );
        $object = $this->object( $article['object'] );
        $attribute = $object->dataMap()['image'];
        $this->assertSame( 'ezobjectrelation', $attribute->attribute( 'data_type_string' ) );
        $this->assertSame( $image['object'], (int)$attribute->attribute( 'data_int' ) );
        $this->assertTrue( $attribute->hasContent() );
        $content = $attribute->content();
        $this->assertInstanceOf( 'eZContentObject', $content );
        $this->assertSame( $image['object'], (int)$content->attribute( 'id' ) );
        $this->assertSame( (string)$image['object'], (string)$attribute->toString() );
        $this->assertSame( 'Picture stand-in', $attribute->title() );
        $this->assertStringContainsString( 'Picture stand-in', json_encode( $attribute->metaData() ), 'the related object\'s text is indexed with the article' );

        // the attribute relation is recorded and found both ways
        $attributeID = (int)$attribute->attribute( 'contentclassattribute_id' );
        $related = $object->relatedObjects( false, false, $attributeID );
        $this->assertSame( array( $image['object'] ), $this->ids( $related ) );
        $this->assertSame( array( $article['object'] ), $this->ids( $this->object( $image['object'] )->reverseRelatedObjectList( false, $attributeID ) ) );

        // an id that is no object is refused
        $attribute->fromString( '999999999' );
        $this->assertNotSame( 999999999, (int)$attribute->attribute( 'data_int' ) );
    }

    private function ids( $objects )
    {
        $ids = array();
        foreach ( (array)$objects as $object )
            $ids[] = (int)$object->attribute( 'id' );
        return $ids;
    }

    // ---------------------------------------------------------------- lookups

    public function testLookups()
    {
        $item = static::folder( static::$root['node'], 'Looked up' );
        $other = static::folder( static::$root['node'], 'Looked up too' );
        $object = $this->object( $item['object'] );
        $this->assertSame( $item['object'], (int)eZContentObject::fetchByRemoteID( $object->remoteID() )->attribute( 'id' ) );
        $this->assertNull( eZContentObject::fetchByRemoteID( 'k1c-no-such-remote-id' ) );
        $this->assertSame( $item['object'], (int)eZContentObject::fetchByNodeID( $item['node'] )->attribute( 'id' ) );
        $this->assertNull( eZContentObject::fetchByNodeID( 999999999 ) );
        $this->assertTrue( eZContentObject::exists( $item['object'] ) );
        $this->assertFalse( eZContentObject::exists( 999999999 ) );
        $both = eZContentObject::fetchIDArray( array( $item['object'], $other['object'] ) );
        $this->assertEqualsCanonicalizing( array( $item['object'], $other['object'] ), array_map( 'intval', array_keys( $both ) ) );
        $this->assertSame( 'folder', $object->contentClassIdentifier() );
        $this->assertSame( 'folder', $object->attribute( 'class_identifier' ) );
        $this->assertSame( static::$root['node'], (int)$object->mainParentNodeID() );
        $this->assertSame( $item['node'], (int)$object->mainNode()->attribute( 'node_id' ) );
        $this->assertTrue( $object->hasVisibleNode() );
        $this->assertSame( (int)eZUser::currentUserID(), (int)$object->owner()->attribute( 'id' ) );
        $same = eZContentObject::fetchSameClassListCount( $object->attribute( 'contentclass_id' ) );
        $this->assertGreaterThanOrEqual( 2, (int)$same );

        $byIdentifier = $object->fetchAttributesByIdentifier( array( 'name', 'short_name' ) );
        $identifiers = array();
        foreach ( $byIdentifier as $attribute )
            $identifiers[] = $attribute->attribute( 'contentclass_attribute_identifier' );
        $this->assertEqualsCanonicalizing( array( 'name', 'short_name' ), $identifiers );
    }

    // ---------------------------------------------------------------- copy

    public function testCopyOfAllVersions()
    {
        $item = static::folder( static::$root['node'], 'Original' );
        $this->update( $item['object'], array( 'name' => 'Original 2' ) );
        $original = $this->object( $item['object'] );
        $db = eZDB::instance();
        $db->begin();
        $copy = $original->copy( true );
        $db->commit();
        static::track( $copy->attribute( 'id' ) );
        $this->assertNotSame( $item['object'], (int)$copy->attribute( 'id' ) );
        $this->assertNotSame( $original->remoteID(), $copy->remoteID() );
        $this->assertSame( 2, (int)$copy->getVersionCount() );
        $this->assertSame( 'Original 2', $copy->name() );

        $db->begin();
        $latestOnly = $original->copy( false );
        $db->commit();
        static::track( $latestOnly->attribute( 'id' ) );
        $this->assertSame( 1, (int)$latestOnly->getVersionCount() );
        $this->assertSame( 'Original 2', $latestOnly->name() );
    }

    // ---------------------------------------------------------------- updateAndPublishObject()

    public static function refusedUpdateProvider()
    {
        return array( 'no attributes' => array( array() ), 'attributes not a list' => array( array( 'attributes' => 'name' ) ),
                      'empty attributes' => array( array( 'attributes' => array() ) ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('refusedUpdateProvider')]
    public function testUpdateWithoutAttributesIsRefused( $params )
    {
        $item = static::folder( static::$root['node'], 'Not updated ' . uniqid() );
        $this->assertFalse( eZContentFunctions::updateAndPublishObject( $this->object( $item['object'] ), $params ) );
        $this->assertSame( 1, (int)$this->object( $item['object'] )->getVersionCount(), 'no version was made' );
        $this->assertSame( 0, (int)eZDB::instance()->transactionCounter(), 'no transaction left open' );
    }
}
