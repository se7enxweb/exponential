<?php
/**
 * The Users, Media, Relations and Technical columns, and the examples of the Custom group (two
 * templates, two handlers): checked against real objects.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expSubitemsColumnsTestCase.php';

class expSubitemsMediaUserColumnTest extends expSubitemsColumnsTestCase
{
    public function testUserColumns()
    {
        $node = $this->adminUserNode();
        $user = eZUser::fetch( $node->attribute( 'contentobject_id' ) );
        $this->assertSame( 'admin', $this->value( 'userlogin', $node ) );
        $email = $this->value( 'useremail', $node );
        $this->assertSame( $user->attribute( 'email' ), $email );
        $this->assertTrue( $this->value( 'userenabled', $node ) );
        $this->assertFalse( $this->value( 'userlocked', $node ) );
        $this->assertGreaterThanOrEqual( 0, $this->value( 'userlogincount', $node ) );
        $this->assertGreaterThanOrEqual( 0, $this->value( 'userfailedlogins', $node ) );
        $last = $this->value( 'userlastvisit', $node );
        $this->assertTrue( $last === null || ( is_int( $last ) && $last > 0 ) );

        $roles = array();
        foreach ( $user->roles() as $role )
            $roles[$role->attribute( 'name' )] = true;
        $expected = array_keys( $roles );
        sort( $expected );
        $this->assertSame( $expected, $this->value( 'userroles', $node ) );
        $this->assertSame( count( $expected ), $this->value( 'userrolecount', $node ) );
        $this->assertNotEmpty( $expected, 'the admin has a role' );
    }

    public function testUserColumnsOfAGroupAreNull()
    {
        $this->assertNull( $this->value( 'userlogin', $this->usersRoot() ), 'a user group is not a user' );
        $this->assertNull( $this->value( 'userroles', $this->usersRoot() ) );
    }

    public function testImageColumns()
    {
        $node = $this->nodeWithDataType( 'ezimage' );
        $this->assertTrue( $this->value( 'hasimage', $node ) );
        $this->assertGreaterThanOrEqual( 1, $this->value( 'imagecount', $node ) );
        $width = $this->value( 'imagewidth', $node );
        $height = $this->value( 'imageheight', $node );
        $this->assertGreaterThan( 0, $width );
        $this->assertGreaterThan( 0, $height );
        $this->assertSame( $width . ' x ' . $height, $this->value( 'imagedimensions', $node ) );
        $this->assertStringStartsWith( 'image/', $this->value( 'imagemime', $node ) );
        $size = $this->value( 'imagesize', $node );
        $this->assertGreaterThan( 0, $size );
        $this->assertSame( expSubitemsFieldColumn::formatBytes( $size ), $this->column( 'imagesize' )->html( $node, $size ) );
        $this->assertSame( (string)$size, $this->column( 'imagesize' )->text( $node, $size ), 'the CSV keeps the bytes' );
        $this->assertIsString( $this->value( 'imagealt', $node ) );
    }

    public function testMediaColumnsAreNullWithoutSuchAttributes()
    {
        $user = $this->adminUserNode(); // a user has no image or file attribute
        foreach ( array( 'hasimage', 'imagedimensions', 'imagesize', 'filename', 'filesize', 'filemime', 'filedownloads' ) as $key )
            $this->assertNull( $this->value( $key, $this->mediaRoot() ), "$key of a folder" );
        $this->assertNull( $this->value( 'imagecount', $this->mediaRoot() ) );
        $this->assertNotNull( $user );
    }

    public function testFileColumns()
    {
        $node = $this->nodeWithDataType( 'ezbinaryfile' );
        $name = $this->value( 'filename', $node );
        $this->assertNotEmpty( $name );
        $this->assertGreaterThanOrEqual( 0, $this->value( 'filedownloads', $node ) );
        $this->assertMatchesRegularExpression( '#^[a-z]+/[a-z0-9.+\-]+$#i', $this->value( 'filemime', $node ) );
        $size = $this->value( 'filesize', $node );
        $this->assertTrue( $size === null || $size > 0 );
    }

    public function testRelationColumns()
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( 'SELECT from_contentobject_id AS id FROM ezcontentobject_link GROUP BY from_contentobject_id ORDER BY COUNT(*) DESC', array( 'limit' => 1 ) );
        if ( !$rows )
            $this->markTestSkipped( 'no relations in this database' );
        $object = eZContentObject::fetch( (int)$rows[0]['id'] );
        $node = $object ? $object->attribute( 'main_node' ) : null;
        if ( !$node )
            $this->markTestSkipped( 'the related object has no location' );
        $count = (int)$object->relatedObjectCount( false, false, false, array( 'AllRelations' => true ) );
        $this->assertSame( $count, $this->value( 'relatedcount', $node ) );
        $this->assertCount( min( $count, 10 ), $this->value( 'relatednames', $node ), 'the admin reads them all, at most 10' );
        $this->assertSame( (int)$object->relatedObjectCount( false, false, true, array( 'AllRelations' => true ) ), $this->value( 'reverserelatedcount', $node ) );
        $this->assertSame( 0, $this->value( 'relatedcount', $this->usersRoot() ) );
    }

    public function testTagColumns()
    {
        if ( !class_exists( 'eZTagsObject' ) )
        {
            $this->assertNull( $this->value( 'tags', $this->contentRoot() ) );
            return;
        }
        $node = $this->nodeWithDataType( 'eztags' );
        $tags = $this->value( 'tags', $node );
        $this->assertIsArray( $tags );
        $this->assertSame( count( $tags ), $this->value( 'tagcount', $node ) );
        $this->assertNull( $this->value( 'tags', $this->mediaRoot() ), 'a folder has no tags attribute' );
    }

    public function testRatingColumns()
    {
        if ( !class_exists( 'ezsrRatingObject' ) )
            $this->markTestSkipped( 'ezstarrating is not active' );
        $rating = eZPersistentObject::fetchObject( ezsrRatingObject::definition(), null, array( 'rating_count' => array( '>', 0 ) ) );
        if ( !$rating )
        {
            $this->assertNull( $this->value( 'rating', $this->contentRoot() ) );
            return;
        }
        $node = eZContentObject::fetch( $rating->attribute( 'contentobject_id' ) )->attribute( 'main_node' );
        $stats = ezsrRatingObject::stats( $rating->attribute( 'contentobject_id' ) );
        $this->assertSame( round( (float)$stats['rating_average'], 1 ), $this->value( 'rating', $node ) );
        $this->assertSame( (int)$stats['rating_count'], $this->value( 'ratingcount', $node ) );
        $this->assertNull( $this->value( 'rating', $this->mediaRoot() ), 'never rated' );
    }

    public function testSearchWords()
    {
        $user = $this->adminUserNode();
        $engine = eZINI::instance()->variable( 'SearchSettings', 'SearchEngine' );
        if ( $engine !== 'eZSearchEngine' )
        {
            $this->assertNull( $this->value( 'searchwords', $user ) );
            return;
        }
        $expected = (int)eZPersistentObject::count( expSubitemsSearchWordLinkRow::definition(), array( 'contentobject_id' => $user->attribute( 'contentobject_id' ) ) );
        $this->assertSame( $expected, $this->value( 'searchwords', $user ) );
    }

    public function testTemplateColumns()
    {
        $root = $this->contentRoot();
        $column = $this->column( 'statusbadge' );
        $this->assertInstanceOf( 'expSubitemsTemplateColumn', $column );
        $this->assertSame( 'Visible', $column->value( $root ) );
        $this->assertStringContainsString( 'exp-subitems-badge-visible', $column->html( $root, $column->value( $root ) ) );

        $teaser = $this->column( 'teaser' );
        $this->assertInstanceOf( 'expSubitemsTemplateColumn', $teaser );
        $value = $teaser->value( $this->usersRoot() ); // the users group has a description
        $this->assertIsString( $value );
        $this->assertLessThanOrEqual( 123, mb_strlen( $value ) );
        $this->assertSame( '', $teaser->value( $this->adminUserNode() ), 'a user has none of the attributes' );
    }

    public function testHandlerColumns()
    {
        $root = $this->contentRoot();
        $edit = $this->column( 'editlink' );
        $this->assertInstanceOf( 'expSubitemsCallableColumn', $edit );
        $this->assertStringEndsWith( 'content/edit/' . $root->attribute( 'contentobject_id' ), $edit->value( $root ) );
        $this->assertStringStartsWith( '<a href="', $edit->html( $root, $edit->value( $root ) ) );

        $days = $this->column( 'daysonline' );
        $published = (int)$root->attribute( 'object' )->attribute( 'published' );
        $this->assertSame( (int)floor( ( time() - $published ) / 86400 ), $days->value( $root ) );
        $this->assertSame( 'published', $days->sortBy() );
    }
}
