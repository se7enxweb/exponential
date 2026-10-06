<?php
/**
 * The content operations of eZContentOperationCollection on a throwaway subtree: moving (paths, depths, the
 * subtree below, refused moves into the own subtree), copying, adding and removing locations (the main location
 * moves on), hiding and showing (hidden and invisible flags of the subtree), swapping two nodes, priorities,
 * sort order, sections, the always available flag, deleting to the trash, and the RSS feed of a node.
 *
 * Every test builds its own folders below the class's throwaway root; the base class removes all of it again.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/expContentModelLiveTestCase.php';

class eZContentOperationsLiveTest extends expContentModelLiveTestCase
{
    private function node( $nodeID )
    {
        eZContentObject::clearCache();
        $node = eZContentObjectTreeNode::fetch( $nodeID );
        $this->assertInstanceOf( 'eZContentObjectTreeNode', $node, "node $nodeID" );
        return $node;
    }

    private function base( $name )
    {
        return static::folder( static::$root['node'], $name . ' ' . uniqid() );
    }

    // ---------------------------------------------------------------- move and copy

    public function testMoveUpdatesPathsOfTheWholeSubtree()
    {
        $base = $this->base( 'move' );
        $from = static::folder( $base['node'], 'From' );
        $to = static::folder( $base['node'], 'To' );
        $moved = static::folder( $from['node'], 'Moved' );
        $child = static::folder( $moved['node'], 'Child' );

        $result = eZContentOperationCollection::moveNode( $moved['node'], $moved['object'], $to['node'] );
        $this->assertSame( array( 'status' => true ), $result );

        $movedNode = $this->node( $moved['node'] );
        $toNode = $this->node( $to['node'] );
        $this->assertSame( $to['node'], (int)$movedNode->attribute( 'parent_node_id' ) );
        $this->assertSame( $toNode->attribute( 'path_string' ) . $moved['node'] . '/', $movedNode->attribute( 'path_string' ) );
        $this->assertSame( (int)$toNode->attribute( 'depth' ) + 1, (int)$movedNode->attribute( 'depth' ) );
        $childNode = $this->node( $child['node'] );
        $this->assertSame( $movedNode->attribute( 'path_string' ) . $child['node'] . '/', $childNode->attribute( 'path_string' ) );
        $this->assertStringEndsWith( '/to/moved/child', $childNode->attribute( 'path_identification_string' ) );
        $this->assertSame( 0, $this->node( $from['node'] )->childrenCount() );
        $this->assertSame( 1, $toNode->childrenCount() );
    }

    public function testMoveIntoTheOwnSubtreeIsRefused()
    {
        $base = $this->base( 'move-self' );
        $parent = static::folder( $base['node'], 'Parent' );
        $child = static::folder( $parent['node'], 'Child' );
        $this->assertFalse( eZContentObjectTreeNodeOperations::move( $parent['node'], $child['node'] ) );
        $this->assertFalse( eZContentObjectTreeNodeOperations::move( $parent['node'], $parent['node'] ) );
        $this->assertSame( $base['node'], (int)$this->node( $parent['node'] )->attribute( 'parent_node_id' ) );
    }

    public function testCopyMakesANewObjectAndKeepsTheOriginal()
    {
        $base = $this->base( 'copy' );
        $source = static::folder( $base['node'], 'Source', array( 'short_name' => 'src' ) );
        $target = static::folder( $base['node'], 'Target' );

        $result = eZContentOperationCollection::copyNode( $source['node'], $source['object'], $target['node'] );
        $this->assertNotFalse( $result );
        $children = $this->node( $target['node'] )->children();
        $this->assertCount( 1, $children );
        $copy = $children[0];
        static::track( $copy->attribute( 'contentobject_id' ) );
        $this->assertNotSame( $source['object'], (int)$copy->attribute( 'contentobject_id' ) );
        $this->assertSame( 'src', $copy->attribute( 'name' ), 'the name pattern <short_name|name> of the copy' );
        $this->assertSame( 'Source', $copy->attribute( 'data_map' )['name']->attribute( 'content' ) );
        $this->assertSame( $base['node'], (int)$this->node( $source['node'] )->attribute( 'parent_node_id' ) );
    }

    // ---------------------------------------------------------------- locations

    public function testSecondLocationAndRemovingTheMainOne()
    {
        $base = $this->base( 'locations' );
        $a = static::folder( $base['node'], 'A' );
        $b = static::folder( $base['node'], 'B' );
        $item = static::folder( $a['node'], 'Item' );

        $this->assertSame( array( 'status' => true ), eZContentOperationCollection::addAssignment( $item['node'], $item['object'], array( $b['node'] ) ) );
        eZContentObject::clearCache();
        $object = eZContentObject::fetch( $item['object'] );
        $nodes = $object->assignedNodes();
        $this->assertCount( 2, $nodes );
        $second = null;
        foreach ( $nodes as $n )
        {
            $this->assertSame( $item['node'], (int)$n->attribute( 'main_node_id' ) );
            if ( (int)$n->attribute( 'node_id' ) !== $item['node'] )
                $second = (int)$n->attribute( 'node_id' );
        }
        $this->assertSame( $b['node'], (int)$this->node( $second )->attribute( 'parent_node_id' ) );
        $this->assertFalse( $this->node( $second )->isMain() );
        $this->assertEqualsCanonicalizing( array( $a['node'], $b['node'] ), array_map( 'intval', $object->parentNodeIDArray() ) );

        // adding the same parent again adds nothing
        eZContentOperationCollection::addAssignment( $item['node'], $item['object'], array( $b['node'] ) );
        eZContentObject::clearCache();
        $this->assertCount( 2, eZContentObject::fetch( $item['object'] )->assignedNodes() );

        $this->assertSame( array( 'status' => true ), eZContentOperationCollection::removeNodes( array( $item['node'] ) ) );
        eZContentObject::clearCache();
        $object = eZContentObject::fetch( $item['object'] );
        $this->assertSame( eZContentObject::STATUS_PUBLISHED, (int)$object->attribute( 'status' ) );
        $this->assertSame( $second, (int)$object->attribute( 'main_node_id' ), 'the remaining location is the main one now' );
        $this->assertSame( $second, (int)$this->node( $second )->attribute( 'main_node_id' ) );
    }

    // ---------------------------------------------------------------- visibility

    public function testHidingAndShowingASubtree()
    {
        $base = $this->base( 'hide' );
        $parent = static::folder( $base['node'], 'Parent' );
        $child = static::folder( $parent['node'], 'Child' );
        $hiddenChild = static::folder( $parent['node'], 'Hidden child' );
        eZContentOperationCollection::changeHideStatus( $hiddenChild['node'] );
        $this->assertSame( array( 1, 1 ), $this->visibility( $hiddenChild['node'] ) );

        $this->assertSame( array( 'status' => true ), eZContentOperationCollection::changeHideStatus( $parent['node'] ) );
        $this->assertSame( array( 1, 1 ), $this->visibility( $parent['node'] ), 'hidden and invisible' );
        $this->assertSame( array( 0, 1 ), $this->visibility( $child['node'] ), 'not hidden itself, but invisible' );
        $this->assertSame( array( 1, 1 ), $this->visibility( $hiddenChild['node'] ) );
        $this->assertSame( 'Hidden', $this->node( $parent['node'] )->hiddenStatusString() );
        $this->assertSame( 'Hidden by superior', $this->node( $child['node'] )->hiddenStatusString() );

        eZContentOperationCollection::changeHideStatus( $parent['node'] );
        $this->assertSame( array( 0, 0 ), $this->visibility( $parent['node'] ) );
        $this->assertSame( array( 0, 0 ), $this->visibility( $child['node'] ) );
        $this->assertSame( array( 1, 1 ), $this->visibility( $hiddenChild['node'] ), 'a node hidden itself stays hidden' );
        $this->assertSame( 'Visible', $this->node( $child['node'] )->hiddenStatusString() );
    }

    private function visibility( $nodeID )
    {
        $node = $this->node( $nodeID );
        return array( (int)$node->attribute( 'is_hidden' ), (int)$node->attribute( 'is_invisible' ) );
    }

    public function testHiddenNodesAreLeftOutOfFetchesThatAskSo()
    {
        $base = $this->base( 'hide-fetch' );
        static::folder( $base['node'], 'Shown' );
        $hidden = static::folder( $base['node'], 'Hidden' );
        eZContentOperationCollection::changeHideStatus( $hidden['node'] );
        $all = eZContentObjectTreeNode::subTreeByNodeID( array( 'IgnoreVisibility' => true, 'SortBy' => array( 'name', true ) ), $base['node'] );
        $this->assertSame( array( 'Hidden', 'Shown' ), static::namesOf( $all ) );
        $visible = eZContentObjectTreeNode::subTreeByNodeID( array( 'IgnoreVisibility' => false, 'SortBy' => array( 'name', true ) ), $base['node'] );
        $expected = eZContentObjectTreeNode::showInvisibleNodes() ? array( 'Hidden', 'Shown' ) : array( 'Shown' );
        $this->assertSame( $expected, static::namesOf( $visible ) );
    }

    // ---------------------------------------------------------------- swap, priority, sorting

    public function testSwapExchangesTheObjectsOfTwoNodes()
    {
        $base = $this->base( 'swap' );
        $a = static::folder( $base['node'], 'A' );
        $b = static::folder( $base['node'], 'B' );
        $underA = static::folder( $a['node'], 'Under A' );

        $result = eZContentOperationCollection::swapNode( $a['node'], $b['node'] );
        $this->assertSame( array( 'status' => true ), $result );
        $nodeA = $this->node( $a['node'] );
        $nodeB = $this->node( $b['node'] );
        $this->assertSame( $b['object'], (int)$nodeA->attribute( 'contentobject_id' ), 'node A now shows object B' );
        $this->assertSame( $a['object'], (int)$nodeB->attribute( 'contentobject_id' ) );
        $this->assertSame( 'B', $nodeA->attribute( 'name' ) );
        $this->assertSame( $a['node'], (int)$this->node( $underA['node'] )->attribute( 'parent_node_id' ), 'children stay with the node' );
        $this->assertSame( $a['node'], (int)eZContentObject::fetch( $b['object'] )->attribute( 'main_node_id' ) );
    }

    public function testPrioritiesAndSortOrder()
    {
        $base = $this->base( 'priority' );
        $x = static::folder( $base['node'], 'X' );
        $y = static::folder( $base['node'], 'Y' );
        $z = static::folder( $base['node'], 'Z' );
        $result = eZContentOperationCollection::updatePriority( $base['node'], array( 30, '10', 'x' ), array( $x['node'], $y['node'], $z['node'] ) );
        $this->assertSame( array( 'status' => true ), $result );
        $this->assertSame( 30, (int)$this->node( $x['node'] )->attribute( 'priority' ) );
        $this->assertSame( 10, (int)$this->node( $y['node'] )->attribute( 'priority' ) );
        $this->assertSame( 0, (int)$this->node( $z['node'] )->attribute( 'priority' ), 'a priority that is no number is 0' );

        $sorted = eZContentObjectTreeNode::subTreeByNodeID( array( 'Depth' => 1, 'SortBy' => $this->node( $base['node'] )->sortArray() ), $base['node'] );
        eZContentOperationCollection::changeSortOrder( $base['node'], eZContentObjectTreeNode::SORT_FIELD_PRIORITY, true );
        $node = $this->node( $base['node'] );
        $this->assertSame( eZContentObjectTreeNode::SORT_FIELD_PRIORITY, (int)$node->attribute( 'sort_field' ) );
        $this->assertSame( 1, (int)$node->attribute( 'sort_order' ) );
        $this->assertSame( array( array( 'priority', '1' ) ), array_map( function ( $s ) { return array( $s[0], (string)$s[1] ); }, $node->sortArray() ) );
        $sorted = eZContentObjectTreeNode::subTreeByNodeID( array( 'Depth' => 1, 'SortBy' => $node->sortArray() ), $base['node'] );
        $this->assertSame( array( 'Z', 'Y', 'X' ), static::namesOf( $sorted ) );

        eZContentOperationCollection::changeSortOrder( $base['node'], eZContentObjectTreeNode::SORT_FIELD_NAME, false );
        $node = $this->node( $base['node'] );
        $this->assertSame( eZContentObjectTreeNode::SORT_FIELD_NAME, (int)$node->attribute( 'sort_field' ) );
        $this->assertSame( 0, (int)$node->attribute( 'sort_order' ) );
        $this->assertSame( array( array( 'name', 0 ) ), array_map( function ( $s ) { return array( $s[0], (int)$s[1] ); }, $node->sortArray() ) );
        $sorted = eZContentObjectTreeNode::subTreeByNodeID( array( 'Depth' => 1, 'SortBy' => $node->sortArray() ), $base['node'] );
        $this->assertSame( array( 'Z', 'Y', 'X' ), static::namesOf( $sorted ) );
    }

    public function testDescendingSortOrderGivenAsFalseIsKept()
    {
        // false is descending for both; a numeric column with a default of 1 (ascending) used to store false as 1
        $this->assertSame( eZContentObjectTreeNode::SORT_ORDER_DESC, eZContentObjectTreeNode::create( 1, 1, 1, eZContentObjectTreeNode::SORT_FIELD_NAME, false )->attribute( 'sort_order' ) );
        $this->assertSame( eZContentObjectTreeNode::SORT_ORDER_ASC, eZContentObjectTreeNode::create( 1, 1, 1, eZContentObjectTreeNode::SORT_FIELD_NAME, true )->attribute( 'sort_order' ) );
        $this->assertSame( eZContentObjectTreeNode::SORT_ORDER_DESC, eZContentObjectTreeNode::create( 1, 1, 1, 9, '0' )->attribute( 'sort_order' ) );
        $this->assertSame( eZContentObjectTreeNode::SORT_ORDER_ASC, eZContentObjectTreeNode::create()->attribute( 'sort_order' ) );

        $base = $this->base( 'sort-false' );
        eZContentOperationCollection::changeSortOrder( $base['node'], eZContentObjectTreeNode::SORT_FIELD_NAME, true );
        $this->assertSame( 1, (int)$this->node( $base['node'] )->attribute( 'sort_order' ) );
        eZContentOperationCollection::changeSortOrder( $base['node'], eZContentObjectTreeNode::SORT_FIELD_NAME, false );
        $this->assertSame( 0, (int)$this->node( $base['node'] )->attribute( 'sort_order' ) );
        eZContentOperationCollection::changeSortOrder( $base['node'], eZContentObjectTreeNode::SORT_FIELD_NAME, '1' );
        $this->assertSame( 1, (int)$this->node( $base['node'] )->attribute( 'sort_order' ) );
        eZContentOperationCollection::changeSortOrder( $base['node'], eZContentObjectTreeNode::SORT_FIELD_NAME );
        $this->assertSame( 0, (int)$this->node( $base['node'] )->attribute( 'sort_order' ), 'the default is descending' );
    }

    // ---------------------------------------------------------------- section, always available, trash

    public function testSectionOfASubtree()
    {
        $section = new eZSection( array( 'name' => 'k1c section ' . uniqid(), 'identifier' => 'k1c_section_' . uniqid(), 'navigation_part_identifier' => 'ezcontentnavigationpart' ) );
        $section->store();
        $sectionID = (int)$section->attribute( 'id' );
        $identifier = $section->attribute( 'identifier' );
        try
        {
            $base = $this->base( 'section' );
            $child = static::folder( $base['node'], 'Child' );
            eZContentOperationCollection::updateSection( $base['node'], $sectionID );
            eZContentObject::clearCache();
            $this->assertSame( $sectionID, (int)eZContentObject::fetch( $base['object'] )->attribute( 'section_id' ) );
            $this->assertSame( $sectionID, (int)eZContentObject::fetch( $child['object'] )->attribute( 'section_id' ) );
            $this->assertFalse( eZSection::fetch( $sectionID )->canBeRemoved(), 'a section in use cannot be removed' );

            $byIdentifier = eZSection::fetchByIdentifier( $section->attribute( 'identifier' ) );
            $this->assertSame( $sectionID, (int)$byIdentifier->attribute( 'id' ) );

            $newChild = static::folder( $base['node'], 'New child' );
            eZContentObject::clearCache();
            $this->assertSame( $sectionID, (int)eZContentObject::fetch( $newChild['object'] )->attribute( 'section_id' ), 'a new object takes the section of its parent' );

            $standard = (int)eZContentObject::fetch( static::$root['object'] )->attribute( 'section_id' );
            eZContentOperationCollection::updateSection( $base['node'], $standard );
            eZContentObject::clearCache();
            $this->assertSame( $standard, (int)eZContentObject::fetch( $child['object'] )->attribute( 'section_id' ) );
            $this->assertTrue( eZSection::fetch( $sectionID )->canBeRemoved() );
        }
        finally
        {
            $left = eZSection::fetch( $sectionID );
            if ( $left )
                $left->removeThis();
        }
        // the sections read during the request are kept; a removed one is not found any more
        $this->assertNull( eZSection::fetch( $sectionID ) );
        $this->assertNull( eZSection::fetchByIdentifier( $identifier ) );
    }

    public function testAlwaysAvailableFlag()
    {
        $item = static::folder( static::$root['node'], 'Always ' . uniqid() );
        $object = eZContentObject::fetch( $item['object'] );
        $initial = (bool)$object->isAlwaysAvailable();
        eZContentOperationCollection::updateAlwaysAvailable( $item['object'], !$initial );
        eZContentObject::clearCache();
        $object = eZContentObject::fetch( $item['object'] );
        $this->assertSame( !$initial, (bool)$object->isAlwaysAvailable() );
        $this->assertSame( !$initial, (bool)( (int)$object->attribute( 'language_mask' ) & 1 ) );
        eZContentOperationCollection::updateAlwaysAvailable( $item['object'], $initial );
        eZContentObject::clearCache();
        $this->assertSame( $initial, (bool)eZContentObject::fetch( $item['object'] )->isAlwaysAvailable() );
    }

    public function testDeleteToTheTrash()
    {
        $base = $this->base( 'trash' );
        $item = static::folder( $base['node'], 'Trashed' );
        $this->assertSame( array( 'status' => true ), eZContentOperationCollection::deleteObject( array( $item['node'] ), true ) );
        eZContentObject::clearCache();
        $object = eZContentObject::fetch( $item['object'] );
        $this->assertSame( eZContentObject::STATUS_ARCHIVED, (int)$object->attribute( 'status' ) );
        $this->assertNull( eZContentObjectTreeNode::fetch( $item['node'] ) );
        $rows = eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS c FROM ezcontentobject_trash WHERE contentobject_id=' . (int)$item['object'] );
        $this->assertSame( 1, (int)$rows[0]['c'] );

        static::folder( $base['node'], 'Stays' );
        $info = eZContentObjectTreeNode::subtreeRemovalInformation( array( $base['node'] ) );
        $this->assertSame( 1, (int)$info['total_child_count'], 'only the folder left below, the trashed one is gone from the tree' );

        $deleted = static::folder( $base['node'], 'Deleted' );
        eZContentOperationCollection::deleteObject( array( $deleted['node'] ), false );
        eZContentObject::clearCache();
        $this->assertNull( eZContentObject::fetch( $deleted['object'] ), 'deleted without the trash: gone' );
    }

    // ---------------------------------------------------------------- RSS feed of a node

    public function testFeedForANode()
    {
        $base = $this->base( 'feed' );
        $this->assertSame( array( 'result' => false ), eZRSSFunctionCollection::hasExportByNode( $base['node'] ) );
        $this->assertSame( array( 'status' => true ), eZContentOperationCollection::createFeedForNode( $base['node'] ) );
        $this->assertSame( array( 'result' => true ), eZRSSFunctionCollection::hasExportByNode( $base['node'] ) );
        $rows = eZDB::instance()->arrayQuery( 'SELECT id, title, access_url FROM ezrss_export WHERE node_id=' . (int)$base['node'] );
        $this->assertCount( 1, $rows );
        $this->assertSame( 'rss_feed_' . $base['node'], $rows[0]['access_url'] );
        $this->assertSame( eZContentObject::fetch( $base['object'] )->name(), $rows[0]['title'] );
        $export = eZRSSExport::fetchByName( 'rss_feed_' . $base['node'] );
        $this->assertInstanceOf( 'eZRSSExport', $export );
        $this->assertNotEmpty( $export->itemList(), 'one item per feed item class of the folder' );

        $this->assertSame( array( 'status' => false ), eZContentOperationCollection::createFeedForNode( $base['node'] ), 'a second feed is refused' );
        $this->assertSame( array( 'status' => true ), eZContentOperationCollection::removeFeedForNode( $base['node'] ) );
        $this->assertSame( array( 'result' => false ), eZRSSFunctionCollection::hasExportByNode( $base['node'] ) );
        $this->assertSame( array( 'status' => false ), eZContentOperationCollection::removeFeedForNode( $base['node'] ) );
        $this->assertSame( array(), eZDB::instance()->arrayQuery( 'SELECT id FROM ezrss_export WHERE node_id=' . (int)$base['node'] ) );
    }
}
