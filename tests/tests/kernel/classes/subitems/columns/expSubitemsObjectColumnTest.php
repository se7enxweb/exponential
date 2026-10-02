<?php
/**
 * The Object, Version, People, Translations and Workflow columns: checked against real objects.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expSubitemsColumnsTestCase.php';

class expSubitemsObjectColumnTest extends expSubitemsColumnsTestCase
{
    public function testClassColumns()
    {
        $user = $this->adminUserNode();
        $object = $user->attribute( 'object' );
        $this->assertSame( 'user', $this->value( 'classidentifier', $user ) );
        $this->assertSame( (int)$object->attribute( 'contentclass_id' ), $this->value( 'classid', $user ) );
        $this->assertContains( 'Users', $this->value( 'classgroups', $user ) );
        $this->assertSame( 'class_identifier', $this->column( 'classidentifier' )->sortBy() );
        $this->assertSame( 'folder', $this->value( 'classidentifier', $this->mediaRoot() ) );
    }

    public function testSectionColumns()
    {
        foreach ( array( $this->contentRoot(), $this->mediaRoot(), $this->usersRoot() ) as $node )
        {
            $sectionID = (int)$node->attribute( 'object' )->attribute( 'section_id' );
            $this->assertSame( $sectionID, $this->value( 'sectionid', $node ) );
            $this->assertSame( eZSection::fetch( $sectionID )->attribute( 'identifier' ), $this->value( 'sectionidentifier', $node ) );
        }
        $this->assertSame( 'media', $this->value( 'sectionidentifier', $this->mediaRoot() ) );
        $this->assertSame( 'users', $this->value( 'sectionidentifier', $this->usersRoot() ) );
        $this->assertSame( 'section', $this->column( 'sectionid' )->sortBy() );
    }

    public function testTextColumns()
    {
        $node = $this->nodeWithDataType( 'ezxmltext' );
        $words = $this->value( 'wordcount', $node );
        $length = $this->value( 'textlength', $node );
        $this->assertGreaterThan( 0, $words );
        $this->assertGreaterThan( $words, $length, 'more characters than words' );
        $this->assertNull( $this->value( 'wordcount', $this->adminUserNode() ), 'a user has no main text' );
        $this->assertSame( mb_strlen( $this->mediaRoot()->getName() ), $this->value( 'namelength', $this->mediaRoot() ) );
        $this->assertSame( count( $node->attribute( 'data_map' ) ), $this->value( 'attributecount', $node ) );
    }

    public function testPlainText()
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?><section><paragraph>Fit &amp; healthy</paragraph><paragraph>every day</paragraph></section>';
        $this->assertSame( 'Fit & healthy every day', expSubitemsObjectColumn::plainText( $xml ) );
    }

    public function testTranslationColumns()
    {
        $root = $this->contentRoot();
        $object = $root->attribute( 'object' );
        $codes = $object->availableLanguages();
        $this->assertSame( array_values( $codes ), $this->value( 'languagecodes', $root ) );
        $this->assertSame( count( $codes ), $this->value( 'languagecount', $root ) );
        $this->assertSame( $object->initialLanguageCode(), $this->value( 'initiallanguage', $root ) );
        $missing = $this->value( 'missingtranslations', $root );
        $this->assertSame( array(), array_intersect( $missing, $codes ) );
        $this->assertEqualsCanonicalizing( eZContentLanguage::fetchLocaleList(), array_merge( $missing, array_values( $codes ) ) );
        $this->assertSame( (bool)$object->isAlwaysAvailable(), $this->value( 'alwaysavailable', $root ) );
        $this->assertSame( (int)$object->attribute( 'language_mask' ), $this->value( 'languagemask', $root ) );
    }

    public function testLockedState()
    {
        $root = $this->contentRoot();
        $states = $root->attribute( 'object' )->stateIdentifierArray();
        $lock = array_values( array_filter( $states, function ( $s ) { return strpos( $s, 'ez_lock/' ) === 0; } ) );
        $expected = $lock ? $lock[0] === 'ez_lock/locked' : null;
        $this->assertSame( $expected, $this->value( 'locked', $root ) );
    }

    public function testVersionColumns()
    {
        $user = $this->adminUserNode();
        $object = $user->attribute( 'object' );
        $this->assertSame( (int)$object->attribute( 'current_version' ), $this->value( 'version', $user ) );
        $this->assertSame( (int)eZPersistentObject::count( eZContentObjectVersion::definition(), array( 'contentobject_id' => $object->attribute( 'id' ) ) ),
                           $this->value( 'versioncount', $user ) );
        foreach ( array( 'draftcount' => array( 0, 5 ), 'archivedcount' => array( 3 ), 'pendingcount' => array( 2, 7 ), 'rejectedcount' => array( 4 ) ) as $key => $statuses )
        {
            $n = 0;
            foreach ( $statuses as $status )
                $n += (int)eZPersistentObject::count( eZContentObjectVersion::definition(), array( 'contentobject_id' => $object->attribute( 'id' ), 'status' => $status ) );
            $this->assertSame( $n, $this->value( $key, $user ), $key );
        }
        $this->assertSame( (int)$object->currentVersion()->attribute( 'created' ), $this->value( 'versioncreated', $user ) );
        $this->assertSame( 0, $this->value( 'workflowprocesses', $user ) );
        if ( $this->value( 'draftcount', $user ) === 0 )
        {
            $this->assertSame( array(), $this->value( 'draftauthors', $user ) );
            $this->assertNull( $this->value( 'latestdraft', $user ) );
        }
    }

    public function testPeopleColumns()
    {
        $root = $this->contentRoot();
        $object = $root->attribute( 'object' );
        $owner = eZContentObject::fetch( $object->attribute( 'owner_id' ) );
        $this->assertSame( (int)$object->attribute( 'owner_id' ), $this->value( 'ownerid', $root ) );
        $this->assertSame( $owner ? $owner->attribute( 'name' ) : null, $this->value( 'owner', $root ) );
        $contributors = $this->value( 'contributors', $root );
        $this->assertIsArray( $contributors );
        $this->assertNotEmpty( $contributors );
        $this->assertContains( $this->value( 'initialcreator', $root ), $contributors );
    }

    public function testPermissionColumnsForAdmin()
    {
        $user = $this->adminUserNode();
        $this->assertSame( (bool)$user->canEdit(), $this->value( 'canedit', $user ) );
        $this->assertTrue( $this->value( 'canedit', $this->mediaRoot() ), 'the administrator edits everything' );
        $this->assertTrue( $this->value( 'canhide', $this->mediaRoot() ) );
        $this->assertNull( $this->value( 'cancreate', $user ), 'a user is not a container' );
        $this->assertTrue( $this->value( 'cancreate', $this->mediaRoot() ) );
    }

    public function testPermissionColumnsForAnonymous()
    {
        $anonymousID = (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' );
        $anonymous = eZUser::fetch( $anonymousID );
        if ( !$anonymous )
            $this->markTestSkipped( 'no anonymous user' );
        eZUser::setCurrentlyLoggedInUser( $anonymous, $anonymousID );
        // a fresh node object: permissions are cached on the node
        $media = eZContentObjectTreeNode::fetch( $this->mediaRoot()->attribute( 'node_id' ) );
        $this->assertFalse( $this->value( 'canedit', $media ) );
        $this->assertFalse( $this->value( 'canremove', $media ) );
        $this->assertFalse( $this->value( 'canmove', $media ) );
    }
}
