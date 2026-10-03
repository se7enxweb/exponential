<?php
/**
 * Class, class group and attribute services. Class writes use a test group and test classes that tearDown removes.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expContentServicesTestCase.php';

class expClassServicesTest extends expContentServicesTestCase
{
    public function testDeclarations()
    {
        $this->assertDeclarations( 'expClassServices', 30 );
        $this->assertDeclarations( 'expClassGroupServices', 12 );
        $this->assertDeclarations( 'expAttributeServices', 18 );
    }

    public function testListGetAndIdentifiers()
    {
        $e = $this->envelope( 'expClassServices', 'list', array( 0, 5 ) );
        $this->assertCount( 5, $e['data'] );
        $this->assertGreaterThan( 20, $e['meta']['total'] );
        $c = $this->ok( 'expClassServices', 'get', array( 'folder' ) );
        $this->assertSame( 'folder', $c['identifier'] );
        $this->assertContains( 'name', array_column( $c['attributes'], 'identifier' ) );
        $this->assertSame( $c['id'], $this->ok( 'expClassServices', 'get', array( $c['id'] ) )['id'] );
        $this->assertNotEmpty( $this->ok( 'expClassServices', 'identifiers' ) );
        $this->assertGreaterThan( 20, $this->ok( 'expClassServices', 'count' )['count'] );
        $this->fails( 404, 'expClassServices', 'get', array( 'no_such_class' ) );
        $this->assertSame( $c['id'], $this->ok( 'expClassServices', 'getByRemoteId', array( $c['remote_id'] ) )['id'] );
    }

    public function testSearchAndContainersAndUsage()
    {
        $e = $this->envelope( 'expClassServices', 'search', array( 'folder' ) );
        $this->assertContains( 'folder', array_column( $e['data'], 'identifier' ) );
        $this->assertContains( 'folder', array_column( $this->ok( 'expClassServices', 'containers' ), 'identifier' ) );
        $u = $this->envelope( 'expClassServices', 'usage', array( 3 ) );
        $this->assertLessThanOrEqual( 3, count( $u['data'] ) );
        $this->assertArrayHasKey( 'objects', $u['data'][0] );
    }

    public function testAttributeReads()
    {
        $attrs = $this->ok( 'expClassServices', 'attributes', array( 'folder' ) );
        $this->assertSame( 'name', $attrs[0]['identifier'] );
        $one = $this->ok( 'expClassServices', 'attribute', array( $attrs[0]['id'] ) );
        $this->assertSame( 'ezstring', $one['data_type'] );
        $this->assertSame( 'name', $this->ok( 'expClassServices', 'attributeByIdentifier', array( 'folder', 'name' ) )['identifier'] );
        $this->fails( 404, 'expClassServices', 'attributeByIdentifier', array( 'folder', 'zzz' ) );
        $this->assertContains( 'name', array_column( $this->ok( 'expClassServices', 'requiredAttributes', array( 'folder' ) ), 'identifier' ) );
        $this->assertIsArray( $this->ok( 'expClassServices', 'searchableAttributes', array( 'folder' ) ) );
        $this->assertIsArray( $this->ok( 'expClassServices', 'collectorAttributes', array( 'folder' ) ) );
    }

    public function testGroupsNamesLanguagesPatternsRemovable()
    {
        $this->assertNotEmpty( $this->ok( 'expClassServices', 'groups', array( 'folder' ) ) );
        $this->assertNotEmpty( (array)$this->ok( 'expClassServices', 'names', array( 'folder' ) ) );
        $this->assertNotEmpty( $this->ok( 'expClassServices', 'languages', array( 'folder' ) ) );
        $this->assertArrayHasKey( 'object_name', $this->ok( 'expClassServices', 'patterns', array( 'folder' ) ) );
        $r = $this->ok( 'expClassServices', 'removable', array( 'folder' ) );
        $this->assertArrayHasKey( 'removable', $r );
        $this->assertGreaterThan( 0, $this->ok( 'expClassServices', 'objectCount', array( 'folder' ) )['count'] );
        $this->assertContains( 'folder', array_column( $this->ok( 'expClassServices', 'canInstantiate' ), 'identifier' ) );
    }

    public function testDataTypesAndSortFields()
    {
        $types = $this->ok( 'expClassServices', 'dataTypes' );
        $this->assertContains( 'ezstring', array_column( $types, 'identifier' ) );
        $this->assertContains( 'name', $this->ok( 'expClassServices', 'sortFields' ) );
        $this->assertIsArray( $this->ok( 'expClassServices', 'drafts' ) );
    }

    public function testClassServicesNeedClassPolicy()
    {
        $this->loginAnonymous();
        $this->fails( 401, 'expClassServices', 'list' );
    }

    public function testClassGroupReads()
    {
        $e = $this->envelope( 'expClassGroupServices', 'list' );
        $this->assertGreaterThan( 0, $e['meta']['total'] );
        $this->assertArrayHasKey( 'class_count', $e['data'][0] );
        $g = $e['data'][0];
        $this->assertSame( $g['id'], $this->ok( 'expClassGroupServices', 'get', array( $g['id'] ) )['id'] );
        $this->assertSame( $g['id'], $this->ok( 'expClassGroupServices', 'getByName', array( $g['name'] ) )['id'] );
        $this->assertSame( $e['meta']['total'], $this->ok( 'expClassGroupServices', 'count' )['count'] );
        $this->fails( 404, 'expClassGroupServices', 'get', array( 99999 ) );
        $cc = $this->ok( 'expClassGroupServices', 'classCount', array( $g['id'] ) )['count'];
        $classes = $this->envelope( 'expClassGroupServices', 'classes', array( $g['id'] ) );
        $this->assertSame( $cc, $classes['meta']['total'] );
        $this->assertNotEmpty( $this->ok( 'expClassGroupServices', 'ofClass', array( 'folder' ) ) );
        $this->assertIsBool( $this->ok( 'expClassGroupServices', 'isEmpty', array( $g['id'] ) )['empty'] );
    }

    public function testClassGroupLifecycle()
    {
        $name = 'exp test group ' . uniqid();
        $g = $this->ok( 'expClassGroupServices', 'create', array( $name ) );
        $gid = $g['id'];
        $this->cleanup( function () use ( $gid ) { eZContentClassGroup::removeSelected( $gid ); eZContentClassClassGroup::removeGroupMembers( $gid ); } );
        $this->fails( 409, 'expClassGroupServices', 'create', array( $name ) );
        $this->fails( 422, 'expClassGroupServices', 'create', array( ' ' ) );
        $renamed = $this->ok( 'expClassGroupServices', 'rename', array( $gid, $name . ' b' ) );
        $this->assertSame( $name . ' b', $renamed['name'] );
        $this->assertTrue( $this->ok( 'expClassGroupServices', 'isEmpty', array( $gid ) )['empty'] );
        $this->ok( 'expClassGroupServices', 'remove', array( $gid ) );
        $this->fails( 404, 'expClassGroupServices', 'get', array( $gid ) );
    }

    public function testClassDraftWorkflowPublishAndRemove()
    {
        $group = $this->ok( 'expClassGroupServices', 'create', array( 'exp test group ' . uniqid() ) );
        $gid = $group['id'];
        $identifier = 'exp_test_' . substr( uniqid(), -8 );
        $draft = $this->ok( 'expClassServices', 'createDraft', array(), array( 'name' => 'Exp test class', 'identifier' => $identifier, 'group_id' => $gid, 'is_container' => '1' ) );
        $cid = $draft['id'];
        $this->cleanup( function () use ( $cid, $gid ) {
            foreach ( array( 0, 1 ) as $v )
            {
                $c = eZContentClass::fetch( $cid, true, $v );
                if ( $c ) { $c->remove( true, $v ); }
            }
            eZContentClassClassGroup::removeClassMembers( $cid, 0 );
            eZContentClassClassGroup::removeClassMembers( $cid, 1 );
            eZContentClassGroup::removeSelected( $gid );
            eZContentClassClassGroup::removeGroupMembers( $gid );
        } );
        $this->assertSame( $identifier, $draft['identifier'] );
        $this->assertTrue( $draft['is_container'] );
        $this->fails( 422, 'expClassServices', 'publishDraft', array( $cid ) );
        $a1 = $this->ok( 'expClassServices', 'addAttributeDraft', array( $cid ), array( 'data_type' => 'ezstring', 'identifier' => 'title', 'name' => 'Title', 'is_required' => '1' ) );
        $a2 = $this->ok( 'expClassServices', 'addAttributeDraft', array( $cid ), array( 'data_type' => 'eztext', 'identifier' => 'body', 'name' => 'Body' ) );
        $this->fails( 409, 'expClassServices', 'updateAttributeDraft', array( $cid, $a2['id'] ), array( 'identifier' => 'title' ) );
        $this->fails( 422, 'expClassServices', 'addAttributeDraft', array( $cid ), array( 'data_type' => 'nonexistent', 'identifier' => 'x' ) );
        $upd = $this->ok( 'expClassServices', 'updateAttributeDraft', array( $cid, $a2['id'] ), array( 'name' => 'Body text', 'is_searchable' => '0' ) );
        $this->assertFalse( $upd['is_searchable'] );
        $moved = $this->ok( 'expClassServices', 'moveAttributeDraft', array( $cid, $a2['id'], 'top' ) );
        $this->assertSame( 'body', $moved[0]['identifier'] );
        $this->fails( 400, 'expClassServices', 'moveAttributeDraft', array( $cid, $a2['id'], 'sideways' ) );
        $upClass = $this->ok( 'expClassServices', 'updateDraft', array( $cid ), array( 'object_name_pattern' => '<title>', 'description' => 'A test class' ) );
        $this->assertSame( '<title>', $upClass['object_name_pattern'] );
        $this->assertCount( 2, $this->ok( 'expClassServices', 'draft', array( $cid ) )['attributes'] );
        $this->assertContains( $cid, array_column( $this->ok( 'expClassServices', 'drafts' ), 'id' ) );
        $pub = $this->ok( 'expClassServices', 'publishDraft', array( $cid ) );
        $this->assertSame( 0, $pub['version'] );
        $this->assertCount( 2, $pub['attributes'] );
        $this->assertSame( $identifier, $this->ok( 'expClassServices', 'get', array( $identifier ) )['identifier'] );
        $this->fails( 404, 'expClassServices', 'draft', array( $cid ) );
        $this->fails( 409, 'expClassServices', 'createDraft', array(), array( 'name' => 'Dup', 'identifier' => $identifier, 'group_id' => $gid ) );
        $this->ok( 'expClassGroupServices', 'addClass', array( 1, $identifier ) );
        $this->fails( 409, 'expClassGroupServices', 'addClass', array( 1, $identifier ) );
        $this->ok( 'expClassGroupServices', 'removeClass', array( 1, $identifier ) );
        $this->fails( 409, 'expClassGroupServices', 'removeClass', array( $gid, $identifier ) );
        $this->fails( 409, 'expClassGroupServices', 'remove', array( $gid ) );
        $named = $this->ok( 'expClassServices', 'setName', array( $cid, 'eng-US', 'Exp test class renamed' ) );
        $this->assertSame( 'Exp test class renamed', $named['name'] );
        $this->assertTrue( $this->ok( 'expClassServices', 'removable', array( $identifier ) )['removable'] );
        $this->ok( 'expClassServices', 'remove', array( $cid ) );
        $this->fails( 404, 'expClassServices', 'get', array( $identifier ) );
        $this->ok( 'expClassGroupServices', 'remove', array( $gid ) );
    }

    public function testEditDraftAndDiscard()
    {
        $group = $this->ok( 'expClassGroupServices', 'create', array( 'exp test group ' . uniqid() ) );
        $gid = $group['id'];
        $identifier = 'exp_test_' . substr( uniqid(), -8 );
        $draft = $this->ok( 'expClassServices', 'createDraft', array(), array( 'name' => 'Exp edit class', 'identifier' => $identifier, 'group_id' => $gid ) );
        $cid = $draft['id'];
        $this->cleanup( function () use ( $cid, $gid ) {
            foreach ( array( 0, 1 ) as $v ) { $c = eZContentClass::fetch( $cid, true, $v ); if ( $c ) { $c->remove( true, $v ); } }
            eZContentClassClassGroup::removeClassMembers( $cid, 0 );
            eZContentClassClassGroup::removeClassMembers( $cid, 1 );
            eZContentClassGroup::removeSelected( $gid );
            eZContentClassClassGroup::removeGroupMembers( $gid );
        } );
        $this->ok( 'expClassServices', 'addAttributeDraft', array( $cid ), array( 'data_type' => 'ezstring', 'identifier' => 'title', 'name' => 'Title' ) );
        $this->ok( 'expClassServices', 'publishDraft', array( $cid ) );
        $edit = $this->ok( 'expClassServices', 'editDraft', array( $cid ) );
        $this->assertSame( 1, $edit['version'] );
        $this->assertCount( 1, $edit['attributes'] );
        $extra = $this->ok( 'expClassServices', 'addAttributeDraft', array( $cid ), array( 'data_type' => 'ezinteger', 'identifier' => 'count', 'name' => 'Count' ) );
        $removed = $this->ok( 'expClassServices', 'removeAttributeDraft', array( $cid, $extra['id'] ) );
        $this->assertSame( $extra['id'], $removed['removed'] );
        $d = $this->ok( 'expClassServices', 'discardDraft', array( $cid ) );
        $this->assertTrue( $d['class_still_defined'] );
        $this->fails( 404, 'expClassServices', 'draft', array( $cid ) );
        $this->assertSame( $identifier, $this->ok( 'expClassServices', 'get', array( $cid ) )['identifier'] );
        $copy = $this->ok( 'expClassServices', 'copy', array( $cid ) );
        $copyId = $copy['id'];
        $this->cleanup( function () use ( $copyId ) {
            foreach ( array( 0, 1 ) as $v ) { $c = eZContentClass::fetch( $copyId, true, $v ); if ( $c ) { $c->remove( true, $v ); } }
            eZContentClassClassGroup::removeClassMembers( $copyId, 0 );
            eZContentClassClassGroup::removeClassMembers( $copyId, 1 );
        } );
        $this->assertSame( 'copy_of_' . $identifier, $copy['identifier'] );
        $this->assertCount( 1, $copy['attributes'] );
        $this->ok( 'expClassServices', 'discardDraft', array( $copyId ) );
    }

    public function testObjectAttributeReads()
    {
        $id = (int)eZContentObjectTreeNode::fetch( 43 )->attribute( 'contentobject_id' );
        $a = $this->ok( 'expAttributeServices', 'get', array( $id, 'name' ) );
        $this->assertSame( 'Media', $a['value'] );
        $this->assertSame( 'Media', $this->ok( 'expAttributeServices', 'value', array( $id, 'name' ) ) );
        $this->assertSame( 'Media', $this->ok( 'expAttributeServices', 'string', array( $id, 'name' ) )['string'] );
        $this->assertSame( 'Media', $this->ok( 'expAttributeServices', 'title', array( $id, 'name' ) )['title'] );
        $this->assertTrue( $this->ok( 'expAttributeServices', 'hasContent', array( $id, 'name' ) )['has_content'] );
        $this->assertSame( 'ezstring', $this->ok( 'expAttributeServices', 'dataType', array( $id, 'name' ) )['identifier'] );
        $this->assertSame( 'name', $this->ok( 'expAttributeServices', 'classAttribute', array( $id, 'name' ) )['identifier'] );
        $byId = $this->ok( 'expAttributeServices', 'getById', array( $a['id'], $a['version'] ) );
        $this->assertSame( $a['id'], $byId['id'] );
        $this->assertNotEmpty( $this->ok( 'expAttributeServices', 'history', array( $id, 'name' ) ) );
        $this->assertNotEmpty( (array)$this->ok( 'expAttributeServices', 'languages', array( $id, 'name' ) ) );
        $this->assertSame( array(), $this->ok( 'expAttributeServices', 'missingRequired', array( $id ) ) );
        $this->fails( 404, 'expAttributeServices', 'get', array( $id, 'zzz' ) );
    }

    public function testDataTypeCatalogue()
    {
        $this->assertContains( 'ezstring', array_column( $this->ok( 'expAttributeServices', 'dataTypes' ), 'identifier' ) );
        $info = $this->ok( 'expAttributeServices', 'dataTypeInfo', array( 'ezstring' ) );
        $this->assertGreaterThan( 0, $info['class_count'] );
        $this->assertFalse( $this->ok( 'expAttributeServices', 'dataTypeInfo', array( 'ezimage' ) )['can_be_set_remotely'] );
        $this->fails( 404, 'expAttributeServices', 'dataTypeInfo', array( 'nonexistent' ) );
        $this->assertNotEmpty( $this->ok( 'expAttributeServices', 'dataTypeClasses', array( 'ezstring' ) ) );
        $e = $this->envelope( 'expAttributeServices', 'byDataType', array( 'ezboolean', 3 ) );
        $this->assertLessThanOrEqual( 3, count( $e['data'] ) );
    }

    public function testObjectAttributeWrites()
    {
        $n = $this->createFolder( 'attr test ' . uniqid() );
        $id = $this->objectOf( $n );
        $a = $this->ok( 'expAttributeServices', 'set', array( $id, 'name' ), array( 'value' => 'Set by service' ) );
        $this->assertSame( 'Set by service', $a['value'] );
        $o = $this->ok( 'expAttributeServices', 'setMany', array( $id ), array( 'values' => '{"name":"Many","short_name":"M"}' ) );
        $this->assertSame( 'M', $o['name'] );
        $this->assertSame( 'M', $this->ok( 'expAttributeServices', 'value', array( $id, 'short_name' ) ) );
        $c = $this->ok( 'expAttributeServices', 'clear', array( $id, 'short_name' ) );
        $this->assertFalse( $c['has_content'] );
        $this->fails( 422, 'expAttributeServices', 'set', array( $id, 'nope' ), array( 'value' => 'x' ) );
    }

    public function testDraftAttributeWrites()
    {
        $n = $this->createFolder( 'draft test ' . uniqid() );
        $obj = eZContentObject::fetch( $this->objectOf( $n ) );
        $draft = $obj->createNewVersion();
        $v = (int)$draft->attribute( 'version' );
        $id = (int)$obj->attribute( 'id' );
        $a = $this->ok( 'expAttributeServices', 'setDraft', array( $id, $v, 'name' ), array( 'value' => 'Draft name' ) );
        $this->assertSame( 'Draft name', $a['value'] );
        $many = $this->ok( 'expAttributeServices', 'setDraftMany', array( $id, $v ), array( 'values' => '{"name":"Draft two","short_name":"d"}' ) );
        $this->assertCount( 2, $many );
        $val = $this->ok( 'expAttributeServices', 'validateDraft', array( $id, $v ) );
        $this->assertTrue( $val['valid'] );
        $this->fails( 409, 'expAttributeServices', 'setDraft', array( $id, 1, 'name' ), array( 'value' => 'x' ) );
    }
}
