<?php
/**
 * Subitems list columns read from the location (the ezcontentobject_tree row) and its children.
 *
 * Field= picks the column: parent_node_id, parent_name, depth, path_string, path_identification,
 * main_node_id, is_main, sort_field, sort_order, is_hidden, is_invisible, is_container,
 * children_count, subtree_count, child_classes, newest_child, newest_child_name, modified_subnode,
 * view_count. The plain fields cost nothing (the row is already loaded); the children fields cost
 * one count or one small query each, and children_count, child_classes and view_count one query
 * for the whole page (prefetch()).
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

    /** Direct children the current user may read (one count query; one per page when prefetched). */
    protected function fieldChildrenCount( eZContentObjectTreeNode $node )
    {
        return self::memo( 'childrencount', (int)$node->attribute( 'node_id' ), function () use ( $node )
        {
            return (int)$node->childrenCount( true );
        } );
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
        return self::memo( 'childclasses', $nodeID, function () use ( $nodeID )
        {
            $lists = self::childClassLists( array( $nodeID ) );
            return $lists === null ? null : $lists[$nodeID];
        } );
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
        $nodeID = (int)$node->attribute( 'node_id' );
        return self::memo( 'viewcount', $nodeID, function () use ( $nodeID )
        {
            $row = eZViewCounter::fetch( $nodeID, false );
            return is_array( $row ) && isset( $row['count'] ) ? (int)$row['count'] : null;
        } );
    }

    protected static function prefetchSets()
    {
        return array( 'ChildrenCount' => array( 'children_count' ),
                      'ChildClasses' => array( 'child_classes' ),
                      'ViewCount' => array( 'view_count' ) );
    }

    /**
     * The readable children of every node of the page in one grouped count: the query
     * childrenCount() makes (eZContentObjectTreeNode::subTreeCountByNodeID(), depth 1, the user's
     * content/read limitations, visibility and languages), grouped by parent.
     */
    protected function prefetchChildrenCount( array $nodes )
    {
        $ids = self::notMemoised( 'childrencount', self::nodeIDs( $nodes ) );
        $db = self::sqlDatabase();
        if ( !$ids || !$db )
            return;

        $limitation = false;
        $limitationList = eZContentObjectTreeNode::getLimitationList( $limitation );
        $permission = eZContentObjectTreeNode::createPermissionCheckingSQL( $limitationList );
        $parentCondition = $db->generateSQLINStatement( $ids, 'ezcontentobject_tree.parent_node_id', false, true, 'int' );
        $nameLanguageFilter = eZContentLanguage::sqlFilter( 'ezcontentobject_name', 'ezcontentobject' );
        $showInvisible = eZContentObjectTreeNode::createShowInvisibleSQLString( true );
        $languageFilter = eZContentLanguage::languagesSQLFilter( 'ezcontentobject' );

        $rows = $db->arrayQuery(
            "SELECT ezcontentobject_tree.parent_node_id AS parent_id,
                    count( DISTINCT ezcontentobject_tree.node_id ) AS child_count
               FROM ezcontentobject_tree
                    INNER JOIN ezcontentobject ON (ezcontentobject.id = ezcontentobject_tree.contentobject_id)
                    INNER JOIN ezcontentclass ON (ezcontentclass.id = ezcontentobject.contentclass_id)
                    INNER JOIN ezcontentobject_name ON (
                        ezcontentobject_name.contentobject_id = ezcontentobject_tree.contentobject_id AND
                        ezcontentobject_name.content_version = ezcontentobject_tree.contentobject_version
                    )
                    $permission[from]
              WHERE $parentCondition and
                    ezcontentclass.version=0 AND
                    $nameLanguageFilter
                    $showInvisible
                    $permission[where]
                    AND $languageFilter
              GROUP BY ezcontentobject_tree.parent_node_id",
            array(), count( $permission['temp_tables'] ) > 0 ? eZDBInterface::SERVER_SLAVE : false );
        $db->dropTempTableList( $permission['temp_tables'] );
        if ( !is_array( $rows ) )
            return;

        $counts = array_fill_keys( $ids, 0 );
        foreach ( $rows as $row )
            $counts[(int)$row['parent_id']] = (int)$row['child_count'];
        foreach ( $counts as $id => $count )
            self::remember( 'childrencount', $id, $count );
    }

    /** The child classes of every node of the page, one grouped query. */
    protected function prefetchChildClasses( array $nodes )
    {
        $ids = self::notMemoised( 'childclasses', self::nodeIDs( $nodes ) );
        if ( !$ids )
            return;
        $lists = self::childClassLists( $ids );
        if ( $lists === null )
            return;
        foreach ( $lists as $id => $list )
            self::remember( 'childclasses', $id, $list );
    }

    /** The view counter rows of the page's nodes, one query. */
    protected function prefetchViewCount( array $nodes )
    {
        $ids = self::notMemoised( 'viewcount', self::nodeIDs( $nodes ) );
        if ( !$ids )
            return;
        $rows = eZPersistentObject::fetchObjectList( eZViewCounter::definition(), null, array( 'node_id' => array( $ids ) ),
                                                     null, null, false );
        if ( !is_array( $rows ) )
            return;
        $counts = array_fill_keys( $ids, null );
        foreach ( $rows as $row )
        {
            if ( isset( $row['count'] ) )
                $counts[(int)$row['node_id']] = (int)$row['count'];
        }
        foreach ( $counts as $id => $count )
            self::remember( 'viewcount', $id, $count );
    }

    /**
     * The child class lists of nodes: node id => array( '<class identifier> (<n>)', ... ) most
     * frequent first, or null for a node without children; null for all on MongoDB or on error.
     *
     * @param int[] $nodeIDs
     * @return array|null
     */
    protected static function childClassLists( array $nodeIDs )
    {
        $db = self::sqlDatabase();
        if ( !$db )
            return null;
        $parentCondition = $db->generateSQLINStatement( $nodeIDs, 'ezcontentobject_tree.parent_node_id', false, true, 'int' );
        $rows = $db->arrayQuery(
            "SELECT ezcontentobject_tree.parent_node_id AS parent_id, ezcontentclass.identifier AS identifier, COUNT(*) AS cnt
               FROM ezcontentobject_tree, ezcontentobject, ezcontentclass
              WHERE $parentCondition
                AND ezcontentobject_tree.node_id <> ezcontentobject_tree.parent_node_id
                AND ezcontentobject.id = ezcontentobject_tree.contentobject_id
                AND ezcontentclass.id = ezcontentobject.contentclass_id
                AND ezcontentclass.version = 0
              GROUP BY ezcontentobject_tree.parent_node_id, ezcontentclass.identifier
              ORDER BY ezcontentobject_tree.parent_node_id, ezcontentclass.identifier" );
        if ( !is_array( $rows ) )
            return null;

        $counts = array_fill_keys( $nodeIDs, array() );
        foreach ( $rows as $row )
            $counts[(int)$row['parent_id']][(string)$row['identifier']] = (int)$row['cnt'];
        $lists = array();
        foreach ( $counts as $id => $byClass )
        {
            if ( !$byClass )
            {
                $lists[$id] = null;
                continue;
            }
            arsort( $byClass );
            $list = array();
            foreach ( $byClass as $identifier => $count )
                $list[] = $identifier . ' (' . $count . ')';
            $lists[$id] = $list;
        }
        return $lists;
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
