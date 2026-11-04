<?php
#[\PHPUnit\Framework\Attributes\Group('database')]
/**
 * eZContentOperationCollection::publishNode() through the publish operation: a new object, a new version, a new
 * location, and a node assignment that carries the remote ID of another object's node (what
 * eZNodeAssignment::cloneNodeAssignment() gives the copy of an object). The node a remote ID finds is taken only when
 * it is the object's node below the assignment's parent; before, an "if ( !$existingNode );" with an empty statement
 * made findNode() decide every time, and removing only the semicolon would have let the last case change the other
 * object's node.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group database
 */

#[\PHPUnit\Framework\Attributes\Group('database')]
class eZContentOperationCollectionPublishNodeTest extends ezpDatabaseTestCase
{
    protected $backupGlobals = false;

    private static function nodesOf( $objectID )
    {
        $nodes = eZContentObjectTreeNode::fetchByContentObjectID( (int)$objectID );
        $result = array();
        foreach ( $nodes as $node )
        {
            $result[(int)$node->attribute( 'node_id' )] = array( 'parent' => (int)$node->attribute( 'parent_node_id' ),
                                                                'main' => (int)$node->attribute( 'main_node_id' ) );
        }
        ksort( $result );
        return $result;
    }

    private static function folder( $name, $parentNodeID = 2 )
    {
        $folder = new ezpObject( 'folder', $parentNodeID );
        $folder->name = $name;
        $folder->publish();
        return $folder;
    }

    public function testNewObjectNewVersionAndNewLocation()
    {
        // a new object: one node below the parent, its own main node
        $folder = self::folder( __FUNCTION__ );
        $objectID = (int)$folder->object->attribute( 'id' );
        $nodes = self::nodesOf( $objectID );
        $this->assertCount( 1, $nodes );
        $mainNodeID = key( $nodes );
        $this->assertSame( array( 'parent' => 2, 'main' => $mainNodeID ), $nodes[$mainNodeID] );

        // a new version: the same node, found by the assignment's remote ID and by findNode() alike
        $object = eZContentObject::fetch( $objectID );
        $version = $object->createNewVersion();
        ezpObject::publishContentObject( $object, $version );
        $this->assertSame( array( $mainNodeID => array( 'parent' => 2, 'main' => $mainNodeID ) ), self::nodesOf( $objectID ) );

        // a new location (Media, node 43) in a further version: a second node, the main node stays
        $object = eZContentObject::fetch( $objectID );
        $version = $object->createNewVersion();
        $version->assignToNode( 43 );
        ezpObject::publishContentObject( $object, $version );
        $nodes = self::nodesOf( $objectID );
        $this->assertCount( 2, $nodes );
        $this->assertSame( array( 'parent' => 2, 'main' => $mainNodeID ), $nodes[$mainNodeID] );
        unset( $nodes[$mainNodeID] );
        $this->assertSame( array( 'parent' => 43, 'main' => $mainNodeID ), reset( $nodes ) );
    }

    public function testAnAssignmentWithAnotherObjectsNodeRemoteIdLeavesThatNodeAlone()
    {
        $other = self::folder( __FUNCTION__ . ' other' );
        $otherID = (int)$other->object->attribute( 'id' );
        $otherNodes = self::nodesOf( $otherID );
        $otherNode = eZContentObjectTreeNode::fetch( key( $otherNodes ) );
        $this->assertInstanceOf( 'eZContentObjectTreeNode', $otherNode );

        // a draft whose assignment carries the other object's node remote ID, as a copy's cloned assignment does
        $copy = new ezpObject( 'folder', 2 );
        $copy->name = __FUNCTION__ . ' copy';
        $copyID = (int)$copy->object->attribute( 'id' );
        foreach ( eZNodeAssignment::fetchForObject( $copyID, $copy->object->attribute( 'current_version' ) ) as $assignment )
        {
            $assignment->setAttribute( 'parent_remote_id', $otherNode->attribute( 'remote_id' ) );
            $assignment->store();
        }
        $copy->publish();

        // the copy has a node of its own below node 2
        $copyNodes = self::nodesOf( $copyID );
        $this->assertCount( 1, $copyNodes );
        $copyNodeID = key( $copyNodes );
        $this->assertNotSame( (int)$otherNode->attribute( 'node_id' ), $copyNodeID );
        $this->assertSame( array( 'parent' => 2, 'main' => $copyNodeID ), $copyNodes[$copyNodeID] );

        // the other object's node is still its own and unchanged
        $this->assertSame( $otherNodes, self::nodesOf( $otherID ) );
        $reloaded = eZContentObjectTreeNode::fetch( $otherNode->attribute( 'node_id' ) );
        $this->assertSame( $otherID, (int)$reloaded->attribute( 'contentobject_id' ) );
    }
}
