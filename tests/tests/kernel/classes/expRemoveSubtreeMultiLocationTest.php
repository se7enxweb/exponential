<?php
/**
 * Removing a subtree that holds more than one location of the same object, against the installation's own
 * database (the kernel started once on the admin siteaccess, as expSubitemsColumnsTestCase does). Every test
 * builds its own content below the media root and removes all of it again in tearDown(), from the trash too.
 *
 *  RM-01 a node fetched before another location of its object was removed (the main one) carries a stale
 *        main-node flag; removing it must still take the object with it (trash: one trash entry)
 *  RM-02 removeSubtrees(), move to trash: an object with both locations inside is trashed once; an object with
 *        a location outside keeps exactly that one, main node and main assignment on it
 *  RM-03 removeSubtrees(), delete: the same with the objects purged
 *
 * Run (as the site's user): sudo -u alpha php vendor/bin/phpunit tests/tests/kernel/classes/expRemoveSubtreeMultiLocationTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expRemoveSubtreeMultiLocationTest extends PHPUnit\Framework\TestCase
{
    /** @var eZScript|null */
    protected static $script = null;

    /** @var string|null */
    protected static $bootError = null;

    /** @var int[] objects this test created */
    protected $created = array();

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        if ( self::$script !== null || self::$bootError !== null )
            return;
        try
        {
            $root = dirname( __DIR__, 4 );
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
        $admin = eZUser::fetchByName( 'admin' );
        if ( $admin instanceof eZUser )
            eZUser::setCurrentlyLoggedInUser( $admin, $admin->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
        eZContentLanguage::setCronjobMode( true );
        $this->created = array();
    }

    public function tearDown(): void
    {
        if ( self::$bootError === null && $this->created )
        {
            $db = eZDB::instance();
            $ids = $this->created;
            $roots = array();
            foreach ( $db->arrayQuery( 'SELECT node_id FROM ezcontentobject_tree WHERE ' . $db->generateSQLINStatement( $ids, 'contentobject_id', false, true, 'int' ) ) as $r )
                $roots[] = (int)$r['node_id'];
            if ( $roots )
                eZContentObjectTreeNode::removeSubtrees( $roots, false );
            eZContentObject::clearCache();
            foreach ( $db->arrayQuery( 'SELECT id, status FROM ezcontentobject WHERE ' . $db->generateSQLINStatement( $ids, 'id', false, true, 'int' ) ) as $r )
            {
                $db->begin();
                if ( (int)$r['status'] === eZContentObject::STATUS_ARCHIVED )
                    \Exponential\Service\Trash::purgeObjects( array( (int)$r['id'] ) );
                else if ( $object = eZContentObject::fetch( (int)$r['id'] ) )
                    $object->purge();
                $db->commit();
            }
            $left = $db->arrayQuery( 'SELECT COUNT(*) AS c FROM ezcontentobject WHERE ' . $db->generateSQLINStatement( $ids, 'id', false, true, 'int' ) );
            $this->assertSame( 0, (int)$left[0]['c'], 'the test content is gone again' );
        }
        parent::tearDown();
    }

    // ---- RM-01 -----------------------------------------------------------------------------------

    public function testStaleMainNodeFlagStillRemovesTheObject()
    {
        $folder = $this->folder( $this->mediaRootID(), 'RM-01 ' . uniqid() );
        $a = $this->folder( $folder['node'], 'a' );
        $b = $this->folder( $folder['node'], 'b' );
        $x = $this->folder( $a['node'], 'x' );
        $second = $this->addLocation( $x, $b['node'] );

        // both fetched first, as a batch of a subtree's children is
        $main = eZContentObjectTreeNode::fetch( $x['node'] );
        $other = eZContentObjectTreeNode::fetch( $second );
        $this->assertNotEquals( $other->attribute( 'node_id' ), $other->attribute( 'main_node_id' ) );

        $db = eZDB::instance();
        $db->begin();
        $main->removeNodeFromTree( true );
        eZContentObject::clearCache();
        $this->assertSame( $second, $this->mainNodeID( $x['object'] ), 'the main location moved to the other one' );
        $other->removeNodeFromTree( true );
        $db->commit();

        $this->assertSame( array(), $this->nodes( $x['object'] ), 'no location left' );
        $this->assertSame( eZContentObject::STATUS_ARCHIVED, $this->objectStatus( $x['object'] ), 'the object is in the trash' );
        $this->assertSame( 1, $this->trashRows( $x['object'] ), 'as one trash entry' );
    }

    // ---- RM-02 / RM-03 ---------------------------------------------------------------------------

    public function testRemoveSubtreeToTrash()
    {
        $this->subtreeCase( true );
    }

    public function testRemoveSubtreeDelete()
    {
        $this->subtreeCase( false );
    }

    protected function subtreeCase( $trash )
    {
        $tag = ( $trash ? 'RM-02 ' : 'RM-03 ' ) . uniqid();
        $outside = $this->folder( $this->mediaRootID(), "$tag outside" );
        $f = $this->folder( $this->mediaRootID(), "$tag F" );
        $s1 = $this->folder( $f['node'], 'S1' );
        $s2 = $this->folder( $f['node'], 'S2' );
        // main location in S2: removed before the one in S1 (the subtree goes path descending)
        $x = $this->folder( $s2['node'], 'x' );
        $this->addLocation( $x, $s1['node'] );
        $x2 = $this->folder( $s1['node'], 'x2' );
        $this->addLocation( $x2, $s2['node'] );
        $y = $this->folder( $s2['node'], 'y' );
        $yOut = $this->addLocation( $y, $outside['node'] );
        $y2 = $this->folder( $outside['node'], 'y2' );
        $this->addLocation( $y2, $s1['node'] );

        eZContentObjectTreeNode::removeSubtrees( array( $f['node'] ), $trash );
        eZContentObject::clearCache();

        foreach ( array( $f, $s1, $s2, $x, $x2 ) as $item )
        {
            $this->assertSame( array(), $this->nodes( $item['object'] ), "object {$item['object']} has no location" );
            if ( $trash )
            {
                $this->assertSame( eZContentObject::STATUS_ARCHIVED, $this->objectStatus( $item['object'] ), "object {$item['object']} is in the trash" );
                $this->assertSame( 1, $this->trashRows( $item['object'] ), "object {$item['object']} has one trash entry" );
            }
            else
            {
                $this->assertNull( $this->objectStatus( $item['object'] ), "object {$item['object']} is purged" );
                $this->assertSame( 0, $this->trashRows( $item['object'] ) );
            }
        }
        foreach ( array( array( $y, $yOut ), array( $y2, $y2['node'] ) ) as $pair )
        {
            list( $item, $keptNode ) = $pair;
            $this->assertSame( array( $keptNode ), $this->nodes( $item['object'] ), "object {$item['object']} keeps its outside location only" );
            $this->assertSame( $keptNode, $this->mainNodeID( $item['object'] ), 'and it is the main one' );
            $this->assertSame( eZContentObject::STATUS_PUBLISHED, $this->objectStatus( $item['object'] ) );
            $this->assertSame( array( $outside['node'] => 1 ), $this->assignments( $item['object'] ), 'one assignment, main, under the outside parent' );
        }
    }

    // ---- helpers ---------------------------------------------------------------------------------

    protected function mediaRootID()
    {
        return (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'MediaRootNode' );
    }

    protected function folder( $parentNodeID, $name )
    {
        $object = eZContentFunctions::createAndPublishObject( array( 'parent_node_id' => $parentNodeID, 'class_identifier' => 'folder',
                                                                     'creator_id' => eZUser::currentUserID(), 'attributes' => array( 'name' => $name ) ) );
        $this->assertInstanceOf( 'eZContentObject', $object, "folder $name created" );
        $this->created[] = (int)$object->attribute( 'id' );
        return array( 'object' => (int)$object->attribute( 'id' ), 'node' => (int)$object->attribute( 'main_node_id' ) );
    }

    /** Adds a location under $parentNodeID and returns its node id. */
    protected function addLocation( array $item, $parentNodeID )
    {
        eZContentOperationCollection::addAssignment( $item['node'], $item['object'], array( $parentNodeID ) );
        eZContentObject::clearCache();
        $rows = eZDB::instance()->arrayQuery( 'SELECT node_id FROM ezcontentobject_tree WHERE contentobject_id=' . (int)$item['object'] . ' AND parent_node_id=' . (int)$parentNodeID );
        $this->assertCount( 1, $rows, 'the location was added' );
        return (int)$rows[0]['node_id'];
    }

    protected function nodes( $objectID )
    {
        $ids = array();
        foreach ( eZDB::instance()->arrayQuery( 'SELECT node_id FROM ezcontentobject_tree WHERE contentobject_id=' . (int)$objectID . ' ORDER BY node_id' ) as $r )
            $ids[] = (int)$r['node_id'];
        return $ids;
    }

    protected function mainNodeID( $objectID )
    {
        $rows = eZDB::instance()->arrayQuery( 'SELECT DISTINCT main_node_id FROM ezcontentobject_tree WHERE contentobject_id=' . (int)$objectID );
        $this->assertCount( 1, $rows, 'every location names the same main node' );
        return (int)$rows[0]['main_node_id'];
    }

    protected function objectStatus( $objectID )
    {
        $rows = eZDB::instance()->arrayQuery( 'SELECT status FROM ezcontentobject WHERE id=' . (int)$objectID );
        return $rows ? (int)$rows[0]['status'] : null;
    }

    protected function trashRows( $objectID )
    {
        $rows = eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS c FROM ezcontentobject_trash WHERE contentobject_id=' . (int)$objectID );
        return (int)$rows[0]['c'];
    }

    /** parent node => is_main of the current version's node assignments */
    protected function assignments( $objectID )
    {
        $out = array();
        foreach ( eZDB::instance()->arrayQuery( 'SELECT a.parent_node, a.is_main FROM eznode_assignment a, ezcontentobject o
                                                  WHERE o.id = a.contentobject_id AND a.contentobject_version = o.current_version
                                                    AND a.contentobject_id = ' . (int)$objectID ) as $r )
            $out[(int)$r['parent_node']] = (int)$r['is_main'];
        return $out;
    }
}
