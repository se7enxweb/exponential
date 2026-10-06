<?php
/**
 * The template fetch functions of the content module (eZContentFunctionCollection) on a throwaway subtree:
 * object, version, node (by id, path and remote id), the list and tree fetches and their counts with filters,
 * versions and their count, assigned nodes, objects by user, the same-attribute node list, classes and class
 * attributes, translations and locales, sort fields, navigation parts, the relation type mask, and the
 * "not found" errors they give for what does not exist.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/expContentModelLiveTestCase.php';

class eZContentFunctionCollectionLiveTest extends expContentModelLiveTestCase
{
    protected static $a;
    protected static $b;
    protected static $c;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::$a = static::folder( static::$root['node'], 'Fetch A', array( 'short_name' => 'same' ) );
        static::$b = static::folder( static::$root['node'], 'Fetch B', array( 'short_name' => 'same' ) );
        static::$c = static::folder( static::$a['node'], 'Fetch C' );
        eZContentFunctions::updateAndPublishObject( eZContentObject::fetch( static::$c['object'] ), array( 'attributes' => array( 'name' => 'Fetch C2' ) ) );
        eZContentObject::clearCache();
    }

    private function notFound( $result )
    {
        $this->assertSame( array( 'error' => array( 'error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND ) ), $result );
    }

    public function testObjectVersionAndNode()
    {
        $result = eZContentFunctionCollection::fetchContentObject( static::$a['object'] );
        $this->assertSame( static::$a['object'], (int)$result['result']->attribute( 'id' ) );
        $remote = eZContentObject::fetch( static::$a['object'] )->remoteID();
        $this->assertSame( static::$a['object'], (int)eZContentFunctionCollection::fetchContentObject( false, $remote )['result']->attribute( 'id' ) );
        $this->notFound( eZContentFunctionCollection::fetchContentObject( 999999999 ) );
        $this->notFound( eZContentFunctionCollection::fetchContentObject( false, 'k1c-none' ) );
        $this->assertSame( static::$a['object'], (int)eZContentFunctionCollection::fetchObject( static::$a['object'] )['result']->attribute( 'id' ) );

        $version = eZContentFunctionCollection::fetchContentVersion( static::$c['object'], 2 );
        $this->assertSame( 2, (int)$version['result']->attribute( 'version' ) );
        $this->notFound( eZContentFunctionCollection::fetchContentVersion( static::$c['object'], 9 ) );

        $node = eZContentFunctionCollection::fetchContentNode( static::$c['node'], false, false );
        $this->assertSame( 'Fetch C2', $node['result']->attribute( 'name' ) );
        $nodeObject = eZContentObjectTreeNode::fetch( static::$c['node'] );
        $byRemote = eZContentFunctionCollection::fetchContentNode( false, false, false, $nodeObject->attribute( 'remote_id' ) );
        $this->assertSame( static::$c['node'], (int)$byRemote['result']->attribute( 'node_id' ) );
        $byPath = eZContentFunctionCollection::fetchContentNode( false, $nodeObject->urlAlias(), false );
        $this->assertSame( static::$c['node'], (int)$byPath['result']->attribute( 'node_id' ) );
        $this->notFound( eZContentFunctionCollection::fetchContentNode( 999999999, false, false ) );
        $this->notFound( eZContentFunctionCollection::fetchContentNode( false, false, false ) );
    }

    public function testTreeAndListFetchesWithCounts()
    {
        $tree = eZContentFunctionCollection::fetchObjectTree( static::$root['node'], array( 'name', true ), false, false, false, false, false, false,
                                                              false, false, false, false, false, false, false, false, false, true, false );
        $this->assertSame( array( 'Fetch C2', 'same', 'same' ), static::namesOf( $tree['result'] ) );

        $list = eZContentFunctionCollection::fetchObjectTree( static::$root['node'], array( 'name', true ), false, false, false, false, 1, 'eq',
                                                              false, false, false, false, false, false, false, false, false, true, false );
        $this->assertCount( 2, $list['result'] );

        $limited = eZContentFunctionCollection::fetchObjectTree( static::$root['node'], array( 'name', true ), false, false, 1, 1, false, false,
                                                                 false, false, false, false, false, false, false, false, false, true, false );
        $this->assertSame( array( 'same' ), static::namesOf( $limited['result'] ) );

        $count = eZContentFunctionCollection::fetchObjectTreeCount( static::$root['node'], false, false, false, false, false, false, false,
                                                                    false, false, false, false, false );
        $this->assertSame( 3, (int)$count['result'] );
        $count = eZContentFunctionCollection::fetchObjectTreeCount( static::$root['node'], false, false, false, false, false, 1, 'eq',
                                                                    false, false, false, false, false );
        $this->assertSame( 2, (int)$count['result'] );
        $this->notFound( eZContentFunctionCollection::fetchObjectTreeCount( 'x', false, false, false, false, false, false, false,
                                                                            false, false, false, false, false ) );
        $rows = eZContentFunctionCollection::fetchObjectTree( static::$root['node'], array( 'name', false ), false, false, false, false, 1, 'eq',
                                                              false, false, false, false, false, false, false, false, false, false, false );
        $this->assertIsArray( $rows['result'][0] );
    }

    public function testVersionsAndAssignedNodes()
    {
        $object = eZContentObject::fetch( static::$c['object'] );
        $versions = eZContentFunctionCollection::fetchVersionList( $object, 0, 10 );
        $this->assertCount( 2, $versions['result'] );
        $this->assertSame( 2, (int)eZContentFunctionCollection::fetchVersionCount( $object )['result'] );
        $this->assertSame( 1, (int)eZContentFunctionCollection::fetchAssignedNodeCount( static::$c['object'] )['result'] );
        $nodes = eZContentFunctionCollection::fetchAssignedNodes( static::$c['object'] );
        $this->assertSame( array( static::$c['node'] ), static::nodeIDsOf( $nodes['result'] ) );
        $this->assertGreaterThanOrEqual( 3, (int)eZContentFunctionCollection::fetchObjectCountByUserID( eZContentClass::classIDByIdentifier( 'folder' ), eZUser::currentUserID() )['result'] );
    }

    public function testSameAttributeValue()
    {
        $class = eZContentClass::fetchByIdentifier( 'folder' );
        $shortName = $class->fetchAttributeByIdentifier( 'short_name' );
        $this->assertFalse( eZContentFunctionCollection::fetchSameClassAttributeNodeList( $shortName->attribute( 'id' ), 'same', 'string' ), 'int, float or text only' );
        $result = eZContentFunctionCollection::fetchSameClassAttributeNodeList( $shortName->attribute( 'id' ), 'same', 'text' );
        $ids = static::nodeIDsOf( $result['result'] );
        $this->assertContains( static::$a['node'], $ids );
        $this->assertContains( static::$b['node'], $ids );
        $this->assertNotContains( static::$c['node'], $ids );
    }

    public function testClassesAndAttributes()
    {
        $folderID = (int)eZContentClass::classIDByIdentifier( 'folder' );
        $this->assertSame( 'folder', eZContentFunctionCollection::fetchClass( $folderID )['result']->attribute( 'identifier' ) );
        $this->notFound( eZContentFunctionCollection::fetchClass( 999999999 ) );
        $attributes = eZContentFunctionCollection::fetchClassAttributeList( $folderID, 0 );
        $this->assertContains( 'name', array_map( function ( $a ) { return $a->attribute( 'identifier' ); }, $attributes['result'] ) );
        $first = $attributes['result'][0];
        $this->assertSame( (int)$first->attribute( 'id' ), (int)eZContentFunctionCollection::fetchClassAttribute( $first->attribute( 'id' ), 0 )['result']->attribute( 'id' ) );
        $list = eZContentFunctionCollection::fetchClassList( true, false, null, null );
        $this->assertContains( $folderID, array_map( function ( $c ) { return (int)$c->attribute( 'id' ); }, $list['result'] ) );
    }

    public function testLanguagesLocalesAndLists()
    {
        $translations = eZContentFunctionCollection::fetchTranslationList();
        $this->assertNotEmpty( $translations['result'] );
        $codes = eZContentFunctionCollection::fetchPrioritizedLanguageCodes();
        $this->assertNotEmpty( $codes['result'] );
        $languages = eZContentFunctionCollection::fetchPrioritizedLanguages();
        $this->assertSame( $codes['result'], array_map( function ( $l ) { return $l->attribute( 'locale' ); }, $languages['result'] ) );
        $locale = eZContentFunctionCollection::fetchLocale( 'eng-GB' );
        $this->assertSame( 'eng-GB', $locale['result']->localeFullCode() );
        $this->assertNotEmpty( eZContentFunctionCollection::fetchLocaleList( false )['result'] );

        $sortFields = eZContentFunctionCollection::fetchAvailableSortFieldList();
        $this->assertArrayHasKey( eZContentObjectTreeNode::SORT_FIELD_NAME, $sortFields['result'] );
        $this->assertNotEmpty( eZContentFunctionCollection::fetchNavigationParts()['result'] );
        $part = eZContentFunctionCollection::fetchNavigationPart( 'ezcontentnavigationpart' );
        $this->assertSame( 'ezcontentnavigationpart', $part['result']['identifier'] );
        $this->assertNotEmpty( eZContentFunctionCollection::fetchSectionList()['result'] );
        $countries = eZContentFunctionCollection::fetchCountryList( 'Alpha2', 'DE' );
        $this->assertSame( 'DE', $countries['result']['Alpha2'] );
    }

    public static function relationMaskProvider()
    {
        return array(
            array( false, eZContentObject::relationTypeMask( false ) ),
            array( true, eZContentObject::relationTypeMask( true ) ),
            array( array( 'k1c_unknown' ), 0 ),
            array( array( 'common' ), eZContentObject::RELATION_COMMON ),
            array( array( 'xml_embed', 'xml_link' ), eZContentObject::RELATION_EMBED | eZContentObject::RELATION_LINK ),
            array( array( 'attribute' ), eZContentObject::RELATION_ATTRIBUTE ),
            array( 'common', eZContentObject::relationTypeMask( false ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('relationMaskProvider')]
    public function testRelationTypeMask( $types, $expected )
    {
        $this->assertSame( $expected, eZContentFunctionCollection::contentobjectRelationTypeMask( $types ) );
    }
}
