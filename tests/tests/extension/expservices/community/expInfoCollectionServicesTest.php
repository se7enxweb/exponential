<?php
/**
 * The information collection services on a feedback form created in a test folder under the Media root: the
 * submissions of the tests are removed with the form.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/../commerce/expCommerceTestCase.php';

class expInfoCollectionServicesTest extends expCommerceTestCase
{
    protected $formObjects = array();

    public function tearDown(): void
    {
        if ( self::$bootError === null )
            foreach ( $this->formObjects as $id )
                eZInformationCollection::removeContentObject( $id );
        parent::tearDown();
    }

    protected function newForm()
    {
        $o = $this->createObject( $this->testFolder(), 'feedback_form', array( 'name' => 'Test form ' . uniqid() ) );
        $this->formObjects[] = (int)$o->attribute( 'id' );
        return array( (int)$o->attribute( 'id' ), (int)$o->attribute( 'main_node_id' ) );
    }

    protected function submit( $objectId, $subject = 'Hello', $message = 'A message', $email = 'someone@example.com' )
    {
        return $this->ok( 'expInfoCollectionServices', 'submit', array(), array( 'object_id' => $objectId,
            'fields' => json_encode( array( 'sender_name' => 'Tester', 'subject' => $subject, 'message' => $message, 'email' => $email ) ) ) );
    }

    public function testFieldsListTheFormFields()
    {
        list( $oid, $nid ) = $this->newForm();
        $fields = $this->ok( 'expInfoCollectionServices', 'fields', array( $nid ) );
        $this->assertSame( array( 'sender_name', 'subject', 'message', 'email' ), array_column( $fields, 'identifier' ) );
        $this->assertTrue( $fields[0]['required'] );
        $this->assertTrue( $fields[0]['supported'] );
    }

    public function testSettingsOfAForm()
    {
        list( $oid, $nid ) = $this->newForm();
        $s = $this->ok( 'expInfoCollectionServices', 'settings', array( $nid ) );
        $this->assertTrue( $s['collects'] );
        $this->assertContains( $s['handling'], array( 'multiple', 'unique', 'overwrite' ) );
    }

    public function testSubmitStoresACollection()
    {
        list( $oid, $nid ) = $this->newForm();
        $r = $this->submit( $oid, 'Question', 'How are you?' );
        $this->assertSame( $oid, $r['object_id'] );
        $c = $this->ok( 'expInfoCollectionServices', 'view', array( $r['collection_id'] ) );
        $values = array();
        foreach ( $c['values'] as $v )
            $values[$v['attribute']] = $v['text'];
        $this->assertSame( array( 'sender_name' => 'Tester', 'subject' => 'Question', 'message' => 'How are you?', 'email' => 'someone@example.com' ), $values );
    }

    public function testSubmitValidatesTheFields()
    {
        list( $oid ) = $this->newForm();
        $this->fails( 422, 'expInfoCollectionServices', 'submit', array(), array( 'object_id' => $oid, 'fields' => json_encode( array( 'sender_name' => 'n', 'subject' => 'x', 'message' => 'y', 'email' => 'not-an-email' ) ) ) );
        $this->fails( 422, 'expInfoCollectionServices', 'submit', array(), array( 'object_id' => $oid, 'fields' => json_encode( array( 'subject' => 'x' ) ) ) );
        $this->fails( 422, 'expInfoCollectionServices', 'submit', array(), array( 'object_id' => $oid, 'fields' => json_encode( array( 'nonsense' => 'x' ) ) ) );
        $this->fails( 400, 'expInfoCollectionServices', 'submit', array(), array( 'object_id' => $oid, 'fields' => '5' ) );
        $this->assertSame( 0, $this->ok( 'expInfoCollectionServices', 'count', array( $oid ) )['count'], 'nothing was stored' );
    }

    public function testSubmitToAnObjectThatDoesNotCollect()
    {
        $folder = eZContentObjectTreeNode::fetch( $this->testFolder() );
        $this->fails( 404, 'expInfoCollectionServices', 'submit', array(), array( 'object_id' => $folder->attribute( 'contentobject_id' ), 'fields' => '{}' ) );
        $this->fails( 404, 'expInfoCollectionServices', 'submit', array(), array( 'object_id' => 99999999, 'fields' => '{}' ) );
    }

    public function testCollectionsAreListedNewestFirstAndCounted()
    {
        list( $oid ) = $this->newForm();
        $a = $this->submit( $oid, 'First' )['collection_id'];
        $b = $this->submit( $oid, 'Second' )['collection_id'];
        $list = $this->call( 'expInfoCollectionServices', 'collections', array( $oid, 10, 0 ) );
        $this->assertSame( 2, $list['meta']['total'] );
        $this->assertEqualsCanonicalizing( array( $a, $b ), array_column( $list['data'], 'id' ) );
        $this->assertSame( 2, $this->ok( 'expInfoCollectionServices', 'count', array( $oid ) )['count'] );
        $page = $this->call( 'expInfoCollectionServices', 'collections', array( $oid, 1, 0 ) );
        $this->assertCount( 1, $page['data'] );
        $this->assertTrue( $page['meta']['has_more'] );
    }

    public function testFormsListsTheFormsWithCollections()
    {
        list( $oid ) = $this->newForm();
        $this->submit( $oid );
        $forms = $this->call( 'expInfoCollectionServices', 'forms', array( 100, 0 ) );
        $this->assertTrue( $forms['ok'] );
        $this->assertContains( $oid, array_column( $forms['data'], 'object_id' ) );
    }

    public function testSummaryCountsTheValuesPerField()
    {
        list( $oid ) = $this->newForm();
        $this->submit( $oid );
        $this->submit( $oid );
        $r = $this->call( 'expInfoCollectionServices', 'summary', array( $oid ) );
        $this->assertTrue( $r['ok'] );
        $this->assertSame( 2, $r['meta']['collections'] );
        $this->assertSame( 2, $r['data'][0]['values'] );
    }

    public function testMineListsTheSubmissionsOfTheUser()
    {
        list( $oid ) = $this->newForm();
        $id = $this->submit( $oid )['collection_id'];
        $mine = $this->call( 'expInfoCollectionServices', 'mine', array( 200, 0 ) );
        $this->assertTrue( $mine['ok'] );
        $this->assertContains( $id, array_column( $mine['data'], 'id' ) );
    }

    public function testExportAsCsv()
    {
        list( $oid ) = $this->newForm();
        $this->submit( $oid, '=cmd', 'with "quotes", and comma' );
        $e = $this->ok( 'expInfoCollectionServices', 'export', array( $oid ) );
        $this->assertSame( 1, $e['rows'] );
        $lines = preg_split( '/\r\n/', trim( $e['csv'] ) );
        $this->assertCount( 2, $lines );
        $this->assertStringContainsString( '"Subject"', $lines[0] );
        $this->assertStringContainsString( '"\'=cmd"', $lines[1], 'a formula is quoted' );
        $this->assertStringContainsString( '"with ""quotes"", and comma"', $lines[1] );
    }

    public function testRemoveOneAndAllCollections()
    {
        list( $oid ) = $this->newForm();
        $a = $this->submit( $oid )['collection_id'];
        $this->submit( $oid );
        $this->assertSame( array( 'removed' => $a ), $this->ok( 'expInfoCollectionServices', 'remove', array(), array( 'collection_id' => $a ) ) );
        $this->fails( 404, 'expInfoCollectionServices', 'view', array( $a ) );
        $this->assertSame( 1, $this->ok( 'expInfoCollectionServices', 'count', array( $oid ) )['count'] );
        $this->assertSame( array( 'removed' => 1 ), $this->ok( 'expInfoCollectionServices', 'removeAll', array(), array( 'object_id' => $oid ) ) );
        $this->assertSame( 0, $this->ok( 'expInfoCollectionServices', 'count', array( $oid ) )['count'] );
        $this->fails( 404, 'expInfoCollectionServices', 'remove', array(), array( 'collection_id' => 99999999 ) );
    }

    public function testAnAnonymousVisitorCannotReadTheCollections()
    {
        list( $oid, $nid ) = $this->newForm();
        $id = $this->submit( $oid )['collection_id'];
        $this->loginAnonymous();
        $this->fails( 401, 'expInfoCollectionServices', 'view', array( $id ) );
        $this->fails( 401, 'expInfoCollectionServices', 'export', array( $oid ) );
        $this->assertTrue( $this->call( 'expInfoCollectionServices', 'fields', array( $nid ) )['ok'] );
    }

    public function testAnAnonymousVisitorMaySubmitOnlyWhereTheSettingsAllowIt()
    {
        list( $oid ) = $this->newForm();
        $this->loginAnonymous();
        $r = $this->call( 'expInfoCollectionServices', 'submit', array(), array( 'object_id' => $oid,
            'fields' => json_encode( array( 'sender_name' => 'n', 'subject' => 's', 'message' => 'm', 'email' => 'a@example.com' ) ) ) );
        $object = eZContentObject::fetch( $oid );
        if ( eZInformationCollection::allowAnonymous( $object ) && $object->canRead() )
            $this->assertTrue( $r['ok'], json_encode( $r['error'] ?? null ) );
        else
        {
            $this->assertFalse( $r['ok'] );
            $this->assertContains( $r['error']['code'], array( 401, 403 ) );
        }
    }
}
