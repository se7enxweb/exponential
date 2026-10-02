<?php
/**
 * Subitems list columns read from the location (the ezcontentobject_tree row) and its children.
 *
 * Field= picks the column: parent_node_id, parent_name, depth, path_string, path_identification,
 * main_node_id, is_main, sort_field, sort_order, is_hidden, is_invisible, is_container,
 * children_count, subtree_count, child_classes, newest_child, newest_child_name, modified_subnode,
 * view_count. The plain fields cost nothing (the row is already loaded); the children fields cost
 * one count or one small query each.
 * Guide: doc/bc/6.0/subitems-table-options.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expSubitemsNodeColumn extends expSubitemsFieldColumn
{
    protected function fieldParentNodeId( eZContentObjectTreeNode $node )
    {
        $id = (int)$node->attribute( 'parent_node_id' );
        return $id > 0 ? $id : null;
    }

    /** The parent's name; one node fetch per distinct parent (all rows of a page share it). */
    protected function fieldParentName( eZContentObjectTreeNode $node )
    {
        $parentID = (int)$node->attribute( 'parent_node_id' );
        if ( $parentID <= 1 )
            return null;
        return self::memo( 'parentname', $parentID, function () use ( $parentID )
        {
            $parent = eZContentObjectTreeNode::fetch( $parentID );
            return $parent instanceof eZContentObjectTreeNode ? (string)$parent->getName() : null;
        } );
    }

    protected function fieldDepth( eZContentObjectTreeNode $node )
    {
        return (int)$node->attribute( 'depth' );
    }

    protected function fieldPathString( eZContentObjectTreeNode $node )
    {
        return (string)$node->attribute( 'path_string' );
    }

    protected function fieldPathIdentification( eZContentObjectTreeNode $node )
    {
        $path = (string)$node->attribute( 'path_identification_string' );
        return $path === '' ? null : $path;
    }

    protected function fieldMainNodeId( eZContentObjectTreeNode $node )
    {
        return (int)$node->attribute( 'main_node_id' );
    }

    protected function fieldIsMain( eZContentObjectTreeNode $node )
    {
        return (bool)$node->isMain();
    }

    /** How the node sorts its own children: "published", "name", "priority" ... */
    protected function fieldSortField( eZContentObjectTreeNode $node )
    {
        return eZContentObjectTreeNode::sortFieldName( (int)$node->attribute( 'sort_field' ) );
    }

    protected function fieldSortOrder( eZContentObjectTreeNode $node )
    {
        return (int)$node->attribute( 'sort_order' ) === 1
            ? ezpI18n::tr( 'design/admin/node/view/full', 'Ascending' )
            : ezpI18n::tr( 'design/admin/node/view/full', 'Descending' );
    }

    /** Hidden by an editor (the node itself), not by an ancestor. */
    protected function fieldIsHidden( eZContentObjectTreeNode $node )
    {
        return (int)$node->attribute( 'is_hidden' ) === 1;
    }

    /** Invisible to visitors: hidden itself or below a hidden node. */
    protected function fieldIsInvisible( eZContentObjectTreeNode $node )
    {
        return (int)$node->attribute( 'is_invisible' ) === 1;
    }

    protected function fieldIsContainer( eZContentObjectTreeNode $node )
    {
        return (bool)$node->attribute( 'is_container' );
    }

    /** Direct children the current user may read (one count query). */
    protected function fieldChildrenCount( eZContentObjectTreeNode $node )
    {
        return (int)$node->childrenCount( true );
    }

    /** Every node below, all depths, that the current user may read (one count query). */
    protected function fieldSubtreeCount( eZContentObjectTreeNode $node )
    {
        return (int)$node->subTreeCount( array( 'IgnoreVisibility' => true ) );
    }

    /**
     * The classes of the direct children with their numbers, most frequent first:
     * array( 'article (12)', 'folder (2)' ). One grouped query; counts every child, read
     * access or not (it shows numbers per class, never names).
     */
    protected function fieldChildClasses( eZContentObjectTreeNode $node )
    {
        $nodeID = (int)$node->attribute( 'node_id' );
        $db = eZDB::instance();
        if ( $db->databaseName() === 'mongo' )
            return null;
        $rows = $db->arrayQuery(
            "SELECT ezcontentclass.identifier AS identifier, COUNT(*) AS cnt
               FROM ezcontentobject_tree, ezcontentobject, ezcontentclass
              WHERE ezcontentobject_tree.parent_node_id = $nodeID
                AND ezcontentobject_tree.node_id <> ezcontentobject_tree.parent_node_id
                AND ezcontentobject.id = ezcontentobject_tree.contentobject_id
                AND ezcontentclass.id = ezcontentobject.contentclass_id
                AND ezcontentclass.version = 0
              GROUP BY ezcontentclass.identifier" );
        if ( !is_array( $rows ) || !$rows )
            return null;
        $result = array();
        foreach ( $rows as $row )
            $result[(string)$row['identifier']] = (int)$row['cnt'];
        arsort( $result );
        $list = array();
        foreach ( $result as $identifier => $count )
            $list[] = $identifier . ' (' . $count . ')';
        return $list;
    }

    /** The newest child the user may read, as its publishing time (one limited subtree query). */
    protected function fieldNewestChild( eZContentObjectTreeNode $node )
    {
        $child = $this->newestChild( $node );
        return $child ? (int)$child->attribute( 'object' )->attribute( 'published' ) : null;
    }

    /** The name of the newest child the user may read. */
    protected function fieldNewestChildName( eZContentObjectTreeNode $node )
    {
        $child = $this->newestChild( $node );
        return $child ? (string)$child->getName() : null;
    }

    /** When anything in the subtree last changed (ezcontentobject_tree.modified_subnode). */
    protected function fieldModifiedSubnode( eZContentObjectTreeNode $node )
    {
        $time = (int)$node->attribute( 'modified_subnode' );
        return $time > 0 ? $time : null;
    }

    /** Views counted by the ezview_counter table (filled by the view counter cronjob), null if never counted. */
    protected function fieldViewCount( eZContentObjectTreeNode $node )
    {
        $row = eZViewCounter::fetch( (int)$node->attribute( 'node_id' ), false );
        return is_array( $row ) && isset( $row['count'] ) ? (int)$row['count'] : null;
    }

    protected function newestChild( eZContentObjectTreeNode $node )
    {
        $nodeID = (int)$node->attribute( 'node_id' );
        return self::memo( 'newestchild', $nodeID, function () use ( $nodeID )
        {
            $list = eZContentObjectTreeNode::subTreeByNodeID(
                array( 'Depth' => 1, 'DepthOperator' => 'eq', 'Limit' => 1, 'Offset' => 0,
                       'SortBy' => array( array( 'published', false ) ), 'IgnoreVisibility' => true ),
                $nodeID );
            return is_array( $list ) && isset( $list[0] ) ? $list[0] : null;
        } );
    }
}
