<?php
/**
 * The trash before the database update: ezcontentobject_trash without the columns trashed_by and trashed_via
 * (update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql not run yet).
 *
 * The code may be deployed before the update. Moving content to the trash must not fail then (the INSERT may not
 * name the missing columns, and a failed query inside the transaction would stop the request), who moved it goes to
 * <VarDir>/trash/trashed.json as before, and fetching a trash row, the trash view with its filter by user, restoring
 * and purging keep working.
 *
 * Runs only against an installation whose trash table lacks the columns, for instance a copy of a site's database
 * from before the update; on an updated database it is skipped. Each test makes its own folder with a child below
 * the media root and purges everything it made afterwards.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/trash/eZContentObjectTrashOldSchemaTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class eZContentObjectTrashOldSchemaTest extends PHPUnit\Framework\TestCase
{
    /** @var int[] object ids: folder, child */
    protected $objectIDs = array();

    /** @var int[] node ids: folder, child */
    protected $nodeIDs = array();

    /** @var int */
    protected $adminID = 0;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        ezpLiveInstallation::requireOrSkip();
        if ( eZContentObjectTrashNode::hasTrashedByColumns( true ) )
            self::markTestSkipped( 'the trash table has the columns trashed_by and trashed_via (the database update has run)' );
    }

    public function setUp(): void
    {
        parent::setUp();
        foreach ( array( 'Exponential\\Service\\TrashRecord' => 'trashrecord.php', 'Exponential\\Service\\TrashList' => 'trashlist.php' ) as $class => $file )
            if ( !class_exists( $class ) )
                require_once 'kernel/private/classes/services/' . $file;
        $admin = eZUser::fetchByName( 'admin' );
        $this->assertInstanceOf( 'eZUser', $admin, 'the admin user exists' );
        eZUser::setCurrentlyLoggedInUser( $admin, $admin->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
        $this->adminID = (int)$admin->attribute( 'contentobject_id' );
        $parent = (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'MediaRootNode' );
        $this->objectIDs = $this->nodeIDs = array();
        foreach ( array( 'TrashOldSchemaTest ' . uniqid(), 'TrashOldSchemaTest child' ) as $name )
        {
            $object = eZContentFunctions::createAndPublishObject( array( 'parent_node_id' => $parent, 'class_identifier' => 'folder',
                                                                         'attributes' => array( 'name' => $name ) ) );
            $this->assertInstanceOf( 'eZContentObject', $object, "test folder $name created" );
            $this->objectIDs[] = (int)$object->attribute( 'id' );
            $parent = (int)$object->attribute( 'main_node_id' );
            $this->nodeIDs[] = $parent;
        }
    }

    public function tearDown(): void
    {
        $db = eZDB::instance();
        if ( $this->nodeIDs && eZContentObjectTreeNode::fetch( $this->nodeIDs[0] ) )
            eZContentObjectTreeNode::removeSubtrees( array( $this->nodeIDs[0] ), false );
        foreach ( $this->objectIDs as $objectID )
        {
            $object = eZContentObject::fetch( $objectID );
            if ( $object )
            {
                $db->begin();
                $object->purge();
                $db->commit();
            }
        }
        eZContentObject::clearCache();
        $records = Exponential\Service\TrashRecord::all();
        $left = array();
        foreach ( $this->objectIDs as $objectID )
        {
            if ( eZContentObject::fetch( $objectID ) || isset( $records[(string)$objectID] ) )
                $left[] = $objectID;
        }
        parent::tearDown();
        $this->assertSame( array(), $left, 'no object and no file entry of the test is left' );
    }

    /** Trashing, the row, the view, the filters, restoring and purging, all without the columns. */
    public function testTheTrashWorksWithoutTheColumns()
    {
        $this->assertFalse( eZContentObjectTrashNode::hasTrashedByColumns(), 'the table lacks the columns' );

        eZContentObjectTreeNode::removeSubtrees( array( $this->nodeIDs[0] ), true );
        eZContentObject::clearCache();

        // the row, fetched with a definition that names only existing columns
        $trashNode = eZContentObjectTrashNode::fetchByContentObjectID( $this->objectIDs[0] );
        $this->assertInstanceOf( 'eZContentObjectTrashNode', $trashNode, 'the trash row is there and can be read' );
        $this->assertSame( $this->nodeIDs[0], (int)$trashNode->attribute( 'node_id' ) );

        // who: in the old file, matching the row
        $records = Exponential\Service\TrashRecord::all();
        foreach ( $this->objectIDs as $objectID )
        {
            $node = eZContentObjectTrashNode::fetchByContentObjectID( $objectID );
            $entry = Exponential\Service\TrashRecord::entryFor( $records, $objectID, $node->attribute( 'node_id' ), $node->attribute( 'trashed' ) );
            $this->assertIsArray( $entry, "object $objectID: an entry in the file" );
            $this->assertSame( $this->adminID, (int)$entry['user_id'] );
        }

        // the view and its filters
        $context = Exponential\Service\TrashList::context();
        $this->assertSame( $this->adminID, (int)$context['records'][$this->objectIDs[0]]['user_id'], 'the view knows who' );
        $mine = function ( $value ) use ( $context ) {
            $params = Exponential\Service\TrashList::listParams( Exponential\Service\TrashList::filters( array( 'trashed_by' => $value ) ), $context );
            $this->assertArrayNotHasKey( 'TrashedBy', $params, 'no SQL on a column that does not exist' );
            $this->assertArrayNotHasKey( 'TrashedByUnknown', $params );
            $ids = array();
            foreach ( (array)eZContentObjectTrashNode::trashList( array_merge( $params, array( 'Limit' => 1000 ) ) ) as $node )
                if ( in_array( (int)$node->attribute( 'contentobject_id' ), $this->objectIDs ) )
                    $ids[] = (int)$node->attribute( 'contentobject_id' );
            sort( $ids );
            $this->assertIsNumeric( eZContentObjectTrashNode::trashListCount( $params ) );
            return $ids;
        };
        $this->assertSame( $this->objectIDs, $mine( (string)$this->adminID ) );
        $this->assertSame( array(), $mine( 'unknown' ) );

        // restoring the child below the media root (its parent is in the trash), as content/restore does
        $object = eZContentObject::fetch( $this->objectIDs[1] );
        $version = $object->attribute( 'current' );
        $db = eZDB::instance();
        $db->begin();
        foreach ( $version->attribute( 'node_assignments' ) as $assignment )
            $assignment->purge();
        $version->assignToNode( (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'MediaRootNode' ), true );
        $object->setAttribute( 'status', eZContentObject::STATUS_DRAFT );
        $object->store();
        $version->setAttribute( 'status', eZContentObjectVersion::STATUS_DRAFT );
        $version->store();
        $object->restoreObjectAttributes();
        $db->commit();
        eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => $this->objectIDs[1], 'version' => $version->attribute( 'version' ) ) );
        eZContentObjectTrashNode::purgeForObject( $this->objectIDs[1] );
        eZContentObject::clearCache();
        $this->assertNull( eZContentObjectTrashNode::fetchByContentObjectID( $this->objectIDs[1] ), 'restored: out of the trash' );
        $this->assertArrayNotHasKey( (string)$this->objectIDs[1], Exponential\Service\TrashRecord::all(), 'restored: entry forgotten' );
        $this->nodeIDs[1] = (int)eZContentObject::fetch( $this->objectIDs[1] )->attribute( 'main_node_id' );

        // purging the folder
        $db->begin();
        eZContentObject::fetch( $this->objectIDs[0] )->purge();
        $db->commit();
        eZContentObject::clearCache();
        $this->assertNull( eZContentObjectTrashNode::fetchByContentObjectID( $this->objectIDs[0] ) );
        $this->assertArrayNotHasKey( (string)$this->objectIDs[0], Exponential\Service\TrashRecord::all(), 'purged: entry forgotten' );
    }
}
