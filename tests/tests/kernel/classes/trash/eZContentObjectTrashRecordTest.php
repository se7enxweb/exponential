<?php
/**
 * Who moved an object to the trash, and what the trash view shows about it.
 *
 * eZContentObjectTrashNode::createFromNode() writes the current user and where the removal came from into
 * the trash row (trashed_by, trashed_via). What <VarDir>/trash/trashed.json holds from before those columns
 * (Exponential\Service\TrashRecord) is still read for rows without a trashed_by, copied into the columns by
 * moveToColumns(), and forgotten by purgeForObject(). Exponential\Service\TrashList describes each trash row
 * for content/trash: the original place with names, whether the parent still exists, the nodes below it,
 * the filters. Needs the columns trashed_by and trashed_via (update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql).
 *
 * These tests run against the installation's own database, with the kernel started once (eZScript,
 * the admin siteaccess): no test database. Each test creates a folder of its own below the media root
 * with a child and a grandchild, trashes it and purges everything it made afterwards; the last
 * assertions of each test are that none of its objects and none of its trash records is left.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/trash/eZContentObjectTrashRecordTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class eZContentObjectTrashRecordTest extends PHPUnit\Framework\TestCase
{
    /** @var eZScript|null */
    protected static $script = null;

    /** @var string|null why the kernel could not be started */
    protected static $bootError = null;

    /** @var int[] object ids: folder, child, grandchild */
    protected $objectIDs = array();

    /** @var int[] node ids: folder, child, grandchild */
    protected $nodeIDs = array();

    /** @var int */
    protected $adminID = 0;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        ezpLiveInstallation::requireOrSkip();
        if ( self::$script !== null || self::$bootError !== null )
            return;
        try
        {
            $root = dirname( __DIR__, 5 );
            chdir( $root );
            if ( !class_exists( 'eZScript' ) )
                require_once $root . '/autoload.php';
            $script = eZScript::instance( array( 'use-session' => false, 'use-modules' => true, 'use-extensions' => true ) );
            $script->startup();
            $script->setUseSiteAccess( 'admin' );
            $script->initialize();
            eZExecution::setCleanExit();
            if ( !eZDB::instance()->isConnected() )
                throw new RuntimeException( 'no database connection' );
            self::$script = $script;
        }
        catch ( Throwable $e )
        {
            self::$bootError = $e->getMessage();
        }
    }

    public function setUp(): void
    {
        parent::setUp();
        if ( self::$bootError !== null )
            $this->markTestSkipped( 'Kernel not available: ' . self::$bootError );
        foreach ( array( 'Exponential\\Service\\TrashRecord' => 'trashrecord.php', 'Exponential\\Service\\TrashList' => 'trashlist.php' ) as $class => $file )
            if ( !class_exists( $class ) )
                require_once 'kernel/private/classes/services/' . $file;

        $admin = eZUser::fetchByName( 'admin' );
        $this->assertInstanceOf( 'eZUser', $admin, 'the admin user exists' );
        eZUser::setCurrentlyLoggedInUser( $admin, $admin->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
        $this->adminID = (int)$admin->attribute( 'contentobject_id' );

        $parent = (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'MediaRootNode' );
        $this->objectIDs = array();
        $this->nodeIDs = array();
        foreach ( array( 'TrashRecordTest ' . uniqid(), 'child', 'grandchild' ) as $name )
        {
            $object = eZContentFunctions::createAndPublishObject( array(
                'parent_node_id' => $parent, 'class_identifier' => 'folder',
                'attributes' => array( 'name' => $this->objectIDs ? "TrashRecordTest $name" : $name ) ) );
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

        $leftObjects = $leftRecords = array();
        $records = Exponential\Service\TrashRecord::all();
        foreach ( $this->objectIDs as $objectID )
        {
            if ( eZContentObject::fetch( $objectID ) )
                $leftObjects[] = $objectID;
            if ( isset( $records[(string)$objectID] ) )
                $leftRecords[] = $objectID;
        }
        parent::tearDown();
        $this->assertSame( array(), $leftObjects, 'no object of the test is left' );
        $this->assertSame( array(), $leftRecords, 'no trash record of the test is left' );
    }

    protected function trash()
    {
        eZContentObjectTreeNode::removeSubtrees( array( $this->nodeIDs[0] ), true );
        eZContentObject::clearCache();
    }

    /** @return eZContentObjectTrashNode[] the test's trash nodes, keyed by object id */
    protected function trashNodes()
    {
        $nodes = array();
        foreach ( $this->objectIDs as $objectID )
        {
            $node = eZContentObjectTrashNode::fetchByContentObjectID( $objectID );
            $this->assertInstanceOf( 'eZContentObjectTrashNode', $node, "object $objectID is in the trash" );
            $nodes[$objectID] = $node;
        }
        return $nodes;
    }

    /** Trashing writes the user and where it came from into the trash row of every node of the subtree. */
    public function testTrashingStoresWhoAndWhereInTheRow()
    {
        $this->trash();
        $records = Exponential\Service\TrashRecord::all();
        foreach ( $this->trashNodes() as $objectID => $node )
        {
            $this->assertSame( $this->adminID, (int)$node->attribute( 'trashed_by' ), "object $objectID: trashed_by" );
            $this->assertStringStartsWith( 'cli ', $node->attribute( 'trashed_via' ), "object $objectID: trashed_via" );
            $this->assertArrayNotHasKey( (string)$objectID, $records, 'nothing is written to the old file' );
        }
    }

    /** The view's description: original place with names, parent state, nodes below, languages, who. */
    public function testTrashListDescribesTheItems()
    {
        $this->trash();
        $context = Exponential\Service\TrashList::context();
        $nodes = $this->trashNodes();
        $items = array();
        foreach ( Exponential\Service\TrashList::describe( array_values( $nodes ), $context ) as $item )
            $items[$item['object_id']] = $item;
        list( $folder, $child, $grandchild ) = $this->objectIDs;

        $this->assertSame( 2, $items[$folder]['subtree_count'] );
        $this->assertSame( 1, $items[$child]['subtree_count'] );
        $this->assertSame( 0, $items[$grandchild]['subtree_count'] );

        $this->assertSame( 'exists', $items[$folder]['parent_state'], 'the media root still exists' );
        $this->assertSame( 'trash', $items[$child]['parent_state'] );
        $this->assertSame( $folder, $items[$child]['parent_trash_object_id'] );

        $mediaRoot = eZContentObjectTreeNode::fetch( (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'MediaRootNode' ) );
        $path = $items[$grandchild]['path'];
        $this->assertSame( (string)$mediaRoot->attribute( 'name' ), $path[count( $path ) - 3]['name'] );
        $this->assertSame( 'tree', $path[count( $path ) - 3]['state'] );
        $this->assertSame( array( 'trash', 'trash' ), array( $path[count( $path ) - 2]['state'], $path[count( $path ) - 1]['state'] ) );
        $this->assertSame( 'TrashRecordTest child', $path[count( $path ) - 1]['name'] );

        foreach ( $items as $item )
        {
            $this->assertSame( $this->adminID, $item['trashed_by']['user_id'] );
            $this->assertNotEmpty( $item['languages'] );
            $this->assertSame( array(), $item['other_locations'], 'no location is left in the tree' );
            $this->assertNotFalse( $item['section_name'] );
            $this->assertGreaterThan( 0, $item['published'] );
        }

        $summary = Exponential\Service\TrashList::summary( $context );
        $this->assertGreaterThanOrEqual( 3, $summary['items'] );
        $this->assertGreaterThanOrEqual( 2, $summary['below'] );
        $this->assertSame( $summary['items'], $summary['top'] + $summary['below'] );
        $this->assertGreaterThanOrEqual( 3, $summary['recorded'] );
    }

    /** The filters: trashed by, class, date range and "unknown". */
    public function testFilters()
    {
        $this->trash();
        $context = Exponential\Service\TrashList::context();
        $folderClassID = (int)eZContentClass::fetchByIdentifier( 'folder' )->attribute( 'id' );
        $today = date( 'Y-m-d' );
        $mine = function ( array $viewParameters ) use ( $context )
        {
            $filters = Exponential\Service\TrashList::filters( $viewParameters );
            $params = array_merge( Exponential\Service\TrashList::listParams( $filters, $context ), array( 'AttributeFilter' => false ) );
            $ids = array();
            foreach ( eZContentObjectTrashNode::trashList( $params ) as $node )
                $ids[] = (int)$node->attribute( 'contentobject_id' );
            return array_values( array_intersect( $this->objectIDs, $ids ) );
        };
        $this->assertSame( $this->objectIDs, $mine( array( 'trashed_by' => (string)$this->adminID ) ) );
        $this->assertSame( array(), $mine( array( 'trashed_by' => 'unknown' ) ) );
        $this->assertArrayHasKey( 'TrashedBy', Exponential\Service\TrashList::listParams( Exponential\Service\TrashList::filters( array( 'trashed_by' => (string)$this->adminID ) ), $context ),
                                  'filtered in SQL, not by a list of every object' );
        $this->assertSame( $this->objectIDs, $mine( array( 'class' => (string)$folderClassID, 'from' => $today, 'to' => $today ) ) );
        $this->assertSame( array(), $mine( array( 'to' => date( 'Y-m-d', strtotime( '-2 days' ) ) ) ) );

        $filters = Exponential\Service\TrashList::filters( array( 'trashed_by' => 'x1', 'class' => '-3', 'from' => '2026-02-30', 'to' => $today ) );
        $this->assertSame( array( 'trashed_by' => false, 'class' => false, 'from' => false, 'to' => $today ), $filters, 'invalid values are dropped' );
        $this->assertSame( '/(to)/' . $today, Exponential\Service\TrashList::filterURI( $filters ) );
    }

    /** A row trashed before the columns existed: the old file is read, copied into the row, and forgotten on purge. */
    public function testOldRecordsAreReadMovedAndForgotten()
    {
        $this->trash();
        $grandchild = $this->objectIDs[2];
        $node = eZContentObjectTrashNode::fetchByContentObjectID( $grandchild );
        $nodeID = (int)$node->attribute( 'node_id' );
        // as it was before the columns: no trashed_by in the row, an entry in the file
        $db = eZDB::instance();
        $db->query( "UPDATE ezcontentobject_trash SET trashed_by = 0, trashed_via = '' WHERE node_id = $nodeID" );
        $node = eZContentObjectTrashNode::fetchByContentObjectID( $grandchild );
        $this->assertTrue( Exponential\Service\TrashRecord::record( $node ) );
        $this->assertArrayHasKey( (string)$grandchild, Exponential\Service\TrashRecord::all() );

        $context = Exponential\Service\TrashList::context();
        $this->assertSame( $this->adminID, (int)$context['records'][$grandchild]['user_id'], 'the view reads the old file' );

        $stats = Exponential\Service\TrashRecord::moveToColumns( $db, true, $this->objectIDs );
        $this->assertSame( 1, $stats['moved'] );
        $this->assertSame( 0, (int)eZContentObjectTrashNode::fetchByContentObjectID( $grandchild )->attribute( 'trashed_by' ), 'a dry run changes nothing' );

        // only the test's own entries: the installation's other ones stay where they are
        Exponential\Service\TrashRecord::moveToColumns( $db, false, $this->objectIDs );
        $moved = eZContentObjectTrashNode::fetchByContentObjectID( $grandchild );
        $this->assertSame( $this->adminID, (int)$moved->attribute( 'trashed_by' ), 'copied into the row' );
        $this->assertStringStartsWith( 'cli ', $moved->attribute( 'trashed_via' ) );
        $again = Exponential\Service\TrashRecord::moveToColumns( $db, false, $this->objectIDs );
        $this->assertSame( 0, $again['moved'], 'a second run changes nothing' );

        eZContentObjectTrashNode::purgeForObject( $grandchild );
        $this->assertArrayNotHasKey( (string)$grandchild, Exponential\Service\TrashRecord::all(), 'purging forgets the entry' );
    }
}
