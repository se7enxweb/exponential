<?php
/**
 * Audit records of location changes (doc/bc/6.0/audit.md, content.node.add, content.node.remove, content.node.main),
 * written through the audit trail from eZContentOperationCollection.
 *
 *  AL-01 - Adding a location records one content.node.add per new location: the object, the new node as the
 *          target (id, parent), the locations before and after, the main node, the actor
 *  AL-02 - Choosing another main location records content.node.main: the old and the new main node, the new
 *          main node's parent
 *  AL-03 - Removing a location records content.node.remove (the node, its parent, the object, "main"); when the
 *          removed location was the main one, the location that takes its place is recorded as content.node.main
 *          with cause location-remove
 *  AL-04 - Off by default as the catalogue says: content.node.add and content.node.main are recorded only when
 *          switched on; content.node.remove is on
 *  AL-05 - The old names location-assign, location-remove and main-node-update (eZAudit::writeAudit(), and the
 *          files of [AuditSettings] AuditFileNames[] an import reads) are recorded as the three names, with their
 *          attributes as object, target, before and after
 *  AL-06 - The console labels: Location add, Location remove, Main location change
 *
 * Live database, nothing kept: the location changes run inside one database transaction that is rolled back at
 * the end of each test, so the installation's content is as it was. Audit records go to a throwaway directory
 * under var/tmp/audit-tests/ with test settings and keys (expAuditTestFixtures), never to the live audit log.
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 9c931192e0), Copyright (C) Mugo Web, GPL v2.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/content/eZAuditLocationTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group audit
 */

require_once dirname( __DIR__ ) . '/classes/audit/fixtures/expaudittestfixtures.php';

class eZAuditLocationTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $dir;

    /** @var bool a transaction of the test is open */
    private $inTransaction = false;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        ezpLiveInstallation::requireOrSkip();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $admin = eZUser::fetchByName( 'admin' );
        if ( !$admin instanceof eZUser )
            $this->markTestSkipped( 'needs the admin user' );
        eZUser::setCurrentlyLoggedInUser( $admin, $admin->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
        $this->dir = expAuditTestFixtures::setUp( $this->name(), array( 'AuditEventSettings/Enabled' => array( 'content.node.*' ) ) );
    }

    protected function tearDown(): void
    {
        if ( $this->inTransaction )
        {
            eZDB::instance()->rollback();
            $this->inTransaction = false;
        }
        eZContentObject::clearCache();
        expAuditHook::reset();
        expAuditTestFixtures::tearDown();
        parent::tearDown();
    }

    /**
     * An object with one location in the content tree, and a container elsewhere to add a location in.
     *
     * @return array( object, node, parent, target )
     */
    private function fixture()
    {
        $db = eZDB::instance();
        $target = (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'MediaRootNode' );
        $userClasses = array_map( 'intval', eZUser::contentClassIDs() ) ?: array( 0 );
        $rows = $db->arrayQuery( "SELECT t.node_id, t.contentobject_id, t.parent_node_id
                                  FROM ezcontentobject_tree t, ezcontentobject o
                                  WHERE o.id = t.contentobject_id AND o.status = 1 AND t.node_id = t.main_node_id
                                    AND t.depth > 2 AND t.path_string LIKE '/1/2/%' AND t.parent_node_id <> $target
                                    AND o.contentclass_id NOT IN (" . implode( ',', $userClasses ) . ")
                                    AND ( SELECT COUNT(*) FROM ezcontentobject_tree t2 WHERE t2.contentobject_id = t.contentobject_id ) = 1
                                  ORDER BY t.node_id", array( 'limit' => 1 ) );
        if ( !$rows || !$target || !eZContentObjectTreeNode::fetch( $target ) )
            $this->markTestSkipped( 'needs an object with one location below node 2 and the media root' );
        return array( (int)$rows[0]['contentobject_id'], (int)$rows[0]['node_id'], (int)$rows[0]['parent_node_id'], $target );
    }

    private function begin()
    {
        eZDB::instance()->begin();
        $this->inTransaction = true;
    }

    /** @return array[] the content records of the test, in order */
    private function records( $prefix = 'content.node.' )
    {
        expAudit::flush();
        $out = array();
        foreach ( expAuditTestFixtures::records( $this->dir, 'content' ) as $r )
            if ( strpos( $r['name'], $prefix ) === 0 )
                $out[] = $r;
        return $out;
    }

    /** @return int the node of $objectID below $parentID */
    private function nodeBelow( $objectID, $parentID )
    {
        foreach ( eZContentObject::fetch( $objectID )->assignedNodes() as $n )
            if ( (int)$n->attribute( 'parent_node_id' ) === (int)$parentID )
                return (int)$n->attribute( 'node_id' );
        $this->fail( "no location of object $objectID below node $parentID" );
    }

    /** AL-01, AL-02, AL-03 */
    public function testAddMainRemove()
    {
        list( $objectID, $nodeID, $parentID, $target ) = $this->fixture();
        $this->begin();

        eZContentOperationCollection::addAssignment( $nodeID, $objectID, array( $target ) );
        eZContentObject::clearCache();
        $newNodeID = $this->nodeBelow( $objectID, $target );

        $r = $this->records();
        $this->assertCount( 1, $r );
        $add = $r[0];
        $this->assertSame( 'content.node.add', $add['name'] );
        $this->assertSame( 'content', $add['channel'] );
        $this->assertSame( 'add', $add['verb'] );
        $this->assertSame( 'object', $add['object']['type'] );
        $this->assertSame( $objectID, $add['object']['id'] );
        $this->assertSame( 'node', $add['target']['type'] );
        $this->assertSame( $newNodeID, $add['target']['id'] );
        $this->assertSame( $target, $add['target']['parent'] );
        $this->assertSame( array( $nodeID ), $add['before']['locations'] );
        $after = $add['after']['locations'];
        sort( $after );
        $expected = array( $nodeID, $newNodeID );
        sort( $expected );
        $this->assertSame( $expected, $after );
        $this->assertSame( $newNodeID, $add['after']['node'] );
        $this->assertSame( $target, $add['after']['parent'] );
        $this->assertSame( $nodeID, $add['after']['main_node'] );
        $this->assertSame( 14, $add['actor']['user_id'], 'the actor (the fixed test context)' );

        // AL-02
        eZContentOperationCollection::updateMainAssignment( $newNodeID, $objectID, $target );
        eZContentObject::clearCache();
        $r = $this->records();
        $this->assertCount( 2, $r );
        $main = $r[1];
        $this->assertSame( 'content.node.main', $main['name'] );
        $this->assertSame( $objectID, $main['object']['id'] );
        $this->assertSame( $newNodeID, $main['target']['id'] );
        $this->assertSame( array( 'main_node' => $nodeID ), $main['before'] );
        $this->assertSame( $newNodeID, $main['after']['main_node'] );
        $this->assertSame( $target, $main['after']['parent'] );
        $this->assertArrayNotHasKey( 'cause', $main['after'] );

        // AL-03: the main location goes, the other one becomes the main one
        eZContentOperationCollection::removeNodes( array( $newNodeID ) );
        eZContentObject::clearCache();
        $r = $this->records();
        $this->assertCount( 4, $r );
        $remove = $r[2];
        $this->assertSame( 'content.node.remove', $remove['name'] );
        $this->assertSame( 'node', $remove['object']['type'] );
        $this->assertSame( $newNodeID, $remove['object']['id'] );
        $this->assertSame( $target, $remove['before']['parent'] );
        $this->assertSame( $objectID, $remove['before']['object_id'] );
        $this->assertTrue( $remove['before']['main'] );
        $promoted = $r[3];
        $this->assertSame( 'content.node.main', $promoted['name'] );
        $this->assertSame( array( 'main_node' => $newNodeID ), $promoted['before'] );
        $this->assertSame( $nodeID, $promoted['after']['main_node'] );
        $this->assertSame( $parentID, $promoted['after']['parent'] );
        $this->assertSame( 'location-remove', $promoted['after']['cause'] );
        $this->assertSame( $nodeID, (int)eZContentObject::fetch( $objectID )->attribute( 'main_node_id' ) );
    }

    /** AL-03: a location that is not the main one: no main change */
    public function testRemoveSecondaryLocation()
    {
        list( $objectID, $nodeID, , $target ) = $this->fixture();
        $this->begin();
        expAuditHook::muted( 'content.node.add', function () use ( $nodeID, $objectID, $target ) {
            eZContentOperationCollection::addAssignment( $nodeID, $objectID, array( $target ) );
        } );
        eZContentObject::clearCache();
        $newNodeID = $this->nodeBelow( $objectID, $target );
        eZContentOperationCollection::removeNodes( array( $newNodeID ) );
        $r = $this->records();
        $this->assertCount( 1, $r );
        $this->assertSame( 'content.node.remove', $r[0]['name'] );
        $this->assertFalse( $r[0]['before']['main'] );
    }

    /** AL-04 */
    public function testDefaults()
    {
        expAuditTestFixtures::configure( $this->dir );
        $this->assertFalse( expAudit::isOn( 'content.node.add' ), 'content.node.add is off unless switched on' );
        $this->assertFalse( expAudit::isOn( 'content.node.main' ), 'content.node.main is off unless switched on' );
        $catalogue = expAuditTaxonomy::catalogue();
        $this->assertSame( 'off', $catalogue['content.node.add']['default'] );
        $this->assertSame( 'off', $catalogue['content.node.main']['default'] );
        $this->assertSame( 'on', $catalogue['content.node.remove']['default'] );
        $ini = file_get_contents( dirname( __DIR__, 4 ) . '/settings/audit.ini' );
        $this->assertStringNotContainsString( "\nEnabled[]=content.node.add\n", $ini );
        $this->assertStringNotContainsString( "\nEnabled[]=content.node.main\n", $ini );

        // nothing recorded, and the data not even built, while off
        list( $objectID, $nodeID, , $target ) = $this->fixture();
        $this->begin();
        eZContentOperationCollection::addAssignment( $nodeID, $objectID, array( $target ) );
        $this->assertSame( array(), $this->records( 'content.node.add' ) );
        $this->assertSame( array(), eZContentOperationCollection::auditLocationsAdded( $objectID, array(), array( 1 ) ) );
    }

    /** AL-05 */
    public function testOldNames()
    {
        $ini = file_get_contents( dirname( __DIR__, 4 ) . '/settings/audit.ini' );
        foreach ( array( 'location-assign' => 'content.node.add', 'location-remove' => 'content.node.remove',
                         'main-node-update' => 'content.node.main' ) as $old => $new )
        {
            $this->assertStringContainsString( "\nMap[$old]=$new\n", $ini );
            $this->assertStringContainsString( "\nAuditFileNames[$old]=", $ini );
            $this->assertSame( $new, expAuditConfig::defaults()['AuditCompatSettings/Map'][$old] );
        }

        $this->assertTrue( eZAudit::writeAudit( 'location-assign', array( 'Main Node ID' => 275, 'Content object ID' => 273,
                                                                          'Content object name' => 'Workout',
                                                                          'New Locations Parent Node ID Array' => '89, 90',
                                                                          'Comment' => 'Assigned new locations' ) ) );
        $this->assertTrue( eZAudit::writeAudit( 'location-remove', array( 'Removed Node ID' => 301, 'Parent Node ID' => 89,
                                                                          'Content object ID' => 273, 'Content object name' => 'Workout',
                                                                          'Was main node' => 'No' ) ) );
        $this->assertTrue( eZAudit::writeAudit( 'main-node-update', array( 'Content object ID' => 273, 'New Main Node ID' => 302,
                                                                           'New Parent Node ID' => 90, 'Comment' => 'Updated the main location' ) ) );
        $r = $this->records();
        $this->assertCount( 3, $r );
        list( $add, $remove, $main ) = $r;

        $this->assertSame( 'content.node.add', $add['name'] );
        $this->assertSame( 'location-assign', $add['x']['legacy']['name'] );
        $this->assertEquals( array( 'type' => 'object', 'id' => 273, 'name' => 'Workout' ), $add['object'] );
        $this->assertSame( array( 89, 90 ), $add['after']['parents'] );
        $this->assertSame( 275, $add['after']['main_node'] );

        $this->assertSame( 'content.node.remove', $remove['name'] );
        $this->assertEquals( array( 'type' => 'node', 'id' => 301, 'object_id' => 273, 'name' => 'Workout' ), $remove['object'] );
        $this->assertEquals( array( 'parent' => 89, 'main' => false ), $remove['before'] );

        $this->assertSame( 'content.node.main', $main['name'] );
        $this->assertEquals( array( 'type' => 'object', 'id' => 273 ), $main['object'] );
        $this->assertEquals( array( 'type' => 'node', 'id' => 302 ), $main['target'] );
        $this->assertEquals( array( 'main_node' => 302, 'parent' => 90 ), $main['after'] );
        foreach ( $r as $rec )
            $this->assertStringNotContainsString( 'Comment', json_encode( $rec ), 'the free-text comment is never recorded' );
    }

    /** AL-06 */
    public function testLabels()
    {
        $this->assertSame( 'Location add', expAuditTaxonomy::label( 'content.node.add' ) );
        $this->assertSame( 'Location remove', expAuditTaxonomy::label( 'content.node.remove' ) );
        $this->assertSame( 'Main location change', expAuditTaxonomy::label( 'content.node.main' ) );
        $this->assertSame( 'Node move', expAuditTaxonomy::label( 'content.node.move' ), 'the others as before' );
        $registry = expAuditTaxonomy::registry();
        $this->assertSame( 'Location add', $registry['content.node.add']['label'] );
        foreach ( array( 'eng-US', 'ger-DE', 'untranslated' ) as $lang )
        {
            $ts = file_get_contents( dirname( __DIR__, 4 ) . "/share/translations/$lang/translation.ts" );
            foreach ( array( 'Location add', 'Location remove', 'Main location change' ) as $label )
                $this->assertStringContainsString( "<source>$label</source>", $ts, "$lang: $label" );
        }
    }
}
