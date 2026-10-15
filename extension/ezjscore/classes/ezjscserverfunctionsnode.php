<?php
//
// Definition of ezjscServerFunctionsNode class
//
// Created on: <01-Jun-2010 00:00:00 ls>
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ JSCore extension for eZ Publish
// SOFTWARE RELEASE: 1.x
// COPYRIGHT NOTICE: Copyright (C) 1998 - 2026 7x & Exponential Foundation
// COPYRIGHT NOTICE: Copyright (C) 1999-2014 eZ Systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

/**
 * ezjscServerFunctionsNode class definition that provide node fetch functions
 *
 */
class ezjscServerFunctionsNode extends ezjscServerFunctions
{
    /**
     * Returns a subtree node items for given parent node
     *
     * Following parameters are supported:
     * ezjscnode::subtree::parent_node_id::limit::offset::sort::order
     *
     * @since 1.2
     * @param mixed $args
     * @return array
     */
    public static function subTree( $args )
    {
        $parentNodeID = isset( $args[0] ) ? $args[0] : null;
        $limit = isset( $args[1] ) ? $args[1] : 25;
        $offset = isset( $args[2] ) ? $args[2] : 0;
        $sort = isset( $args[3] ) ? self::sortMap( $args[3] ) : 'published';
        $order = isset( $args[4] ) ? $args[4] : false;
        $objectNameFilter = isset( $args[5] ) ? $args[5] : '';

        if ( !$parentNodeID )
        {
            throw new ezcBaseFunctionalityNotSupportedException( 'Fetch node list', 'Parent node id is not valid' );
        }

        $node = eZContentObjectTreeNode::fetch( $parentNodeID );
        if ( !$node instanceOf eZContentObjectTreeNode )
        {
            throw new ezcBaseFunctionalityNotSupportedException( 'Fetch node list', "Parent node '$parentNodeID' is not valid" );
        }

        $ezjscoreIni = eZINI::instance( 'ezjscore.ini' );
        $hardLimit = (int)$ezjscoreIni->variable( 'ezjscServer_ezjscnode', 'HardLimit' );

        if ( $hardLimit > 0 && $limit > $hardLimit )
        {
            $limit = $hardLimit;
        }

        $params = array( 'Depth' => 1,
                         'Limit' => $limit,
                         'Offset' => $offset,
                         // equal priorities in node id order, as updatePriority()'s renumbering takes them
                         'SortBy' => $sort === 'priority' ? array( array( $sort, $order ), array( 'node_id', $order ) ) : array( array( $sort, $order ) ),
                         'DepthOperator' => 'eq',
                         'ObjectNameFilter' => $objectNameFilter,
                         'AsObject' => true );

       // fetch nodes and total node count
        $count = $node->subTreeCount( $params );
        if ( $count )
        {
            $nodeArray = $node->subTree( $params );
        }
        else
        {
            $nodeArray = array();
        }
        unset( $node );// We have on purpose not checked permission on $node itself, so it should not be used

        // generate json response from node list
        if ( $nodeArray )
        {
            $list = ezjscAjaxContent::nodeEncode( $nodeArray, array( 'formatDate' => 'shortdatetime',
                                                                     'fetchThumbPreview' => true,
                                                                     'fetchSection' => true,
                                                                     'fetchCreator' => true,
                                                                     'fetchClassIcon' => true ), 'raw' );
        }
        else
        {
            $list = array();
        }

        return array( 'parent_node_id' => $parentNodeID,
                      'count' => count( $nodeArray ),
                      'total_count' => (int)$count,
                      'list' => $list,
                      'limit' => $limit,
                      'offset' => $offset,
                      'sort' => $sort,
                      'order' => $order );
    }

    /**
     * Returns a node data for given object / node id
     *
     * Following parameters are supported:
     * ezjscnode::load::embed_id[::attribute[::load_image_size]]
     *
     * eg: ezjscnode::load::ezobject_46::image::large
     * eg: ezjscnode::load::eznode_44::summary
      *eg: ezjscnode::load::44::summary (44 is in this case node id)
     *
     * @since 1.2
     * @param mixed $args
     * @throws InvalidArgumentException
     * @return array
     */
    public static function load( $args )
    {
        $embedObject = false;
        if ( isset( $args[0] ) && $args[0] )
        {
            $embedType = 'eznode';
            if (  is_numeric( $args[0] ) )
                $embedId = $args[0];
            else
                list($embedType, $embedId) = explode('_', $args[0]);

            if ( $embedType === 'eznode' || strcasecmp( $embedType  , 'eznode'  ) === 0 )
                $embedObject = eZContentObject::fetchByNodeID( $embedId );
            else
                $embedObject = eZContentObject::fetch( $embedId );
        }

        if ( !$embedObject instanceof eZContentObject )
        {
           throw new InvalidArgumentException( "Argument 1: '$embedType\_$embedId' does not map to a valid content object" );
        }
        else if ( !$embedObject->canRead() )
        {
            throw new InvalidArgumentException( "Argument 1: '$embedType\_$embedId' is not available" );
        }

        // Params for node to json encoder
        $params    = array('loadImages' => true);
        $params['imagePreGenerateSizes'] = array('small', 'original');

        // look for attribute parameter ( what attribute we should load )
        if ( isset( $args[1] ) && $args[1] )
            $params['dataMap'] = array( $args[1] );

        // what image sizes we want returned with full data ( url++ )
        if ( isset( $args[2] ) && $args[2] )
            $params['imagePreGenerateSizes'][] = $args[2];

        // Simplify and load data in accordance to $params
        return ezjscAjaxContent::simplify( $embedObject, $params );
    }

    /**
     * Updating priority sorting for given node
     *
     * Posted: ContentNodeID (the parent), Priority[] and PriorityID[] (the new priorities of its children), and
     * ContentObjectID (the object whose content cache is cleared, as content/action's UpdatePriorityButton does).
     *
     * admin4's sub items table, when the children are reordered by drag and drop, posts instead (each list a text
     * "node_id:priority,node_id:priority", so a page of 500 rows stays one field, far below max_input_vars):
     *
     *  - PriorityList: the new priorities (in place of Priority[] and PriorityID[])
     *  - ExpectedPriorityList: the priorities the table showed. When one of these nodes is no longer a child or has
     *    another priority now (someone else changed the order), nothing is written and the answer is
     *    array( 'conflict' => true, 'nodes' => the ids that differ ).
     *  - Renumber=1 with PageOrder ("id,id,...", the rows of the page in their new order) and PageOffset: when the
     *    page cannot be given distinct priorities between its neighbours (equal priorities across a page border),
     *    every child is numbered again in steps of 10 in the parent's sort direction, the page's rows in their new
     *    order at their place (renumberedOrder()).
     *
     * ExpectedPriorityList is required with PriorityList and Renumber and must name every node to be written (the
     * PriorityList's, or PageOrder's); these modes need a parent that sorts its children by priority. The check and
     * the write run in one transaction (rows locked on MySQL, PostgreSQL and Oracle), rolled back together on an
     * error; a workflow that stops or defers the operation is an error, not "changed". With ExpectedPriorityList the answer is array( 'conflict' => false, 'changed' => array( node_id => array( old,
     * new ) ) ) for the children whose priority changed, which is what an undo posts back.
     *
     * @since 1.2
     * @param mixed $args
     * @return array
     */
    public static function updatePriority( $args )
    {
        $http = eZHTTPTool::instance();

        $lists = $http->hasPostVariable( 'PriorityList' ) || $http->hasPostVariable( 'Renumber' );
        if ( !$http->hasPostVariable('ContentNodeID')
                || ( !$lists && ( !$http->hasPostVariable('PriorityID') || !$http->hasPostVariable('Priority') ) ) )
        {
            return array();
        }

        $contentNodeID = $http->postVariable('ContentNodeID');
        if ( !is_string( $contentNodeID ) || !preg_match( '/^[1-9][0-9]{0,9}$/', $contentNodeID ) )
        {
            throw new InvalidArgumentException( 'Argument ContentNodeID is not a node id' );
        }

        $contentNode = eZContentObjectTreeNode::fetch( (int)$contentNodeID );
        if ( !$contentNode instanceof eZContentObjectTreeNode )
        {
           throw new InvalidArgumentException( "Argument ContentNodeID: '$contentNodeID' does not exist" );
        }
        else if ( !$contentNode->canEdit() )
        {
            throw new InvalidArgumentException( "Argument ContentNodeIDs: '$contentNodeID' is not available" );
        }
        $contentNodeID = (int)$contentNode->attribute( 'node_id' );

        $expected = null;
        if ( $lists )
        {
            // reordering is for a parent that sorts its children by priority (the table only offers it there)
            if ( (int)$contentNode->attribute( 'sort_field' ) !== eZContentObjectTreeNode::SORT_FIELD_PRIORITY )
                throw new InvalidArgumentException( "Node '$contentNodeID' does not sort its children by priority" );
            // the new modes always check what the table showed: without the expected priorities nothing is written
            if ( !$http->hasPostVariable( 'ExpectedPriorityList' ) )
                throw new InvalidArgumentException( 'ExpectedPriorityList is required with PriorityList and Renumber' );
            $expected = self::parsePriorityList( $http->postVariable( 'ExpectedPriorityList', '' ) );
            // every list is read before the transaction starts: a malformed one throws with nothing open
            $renumber = $http->postVariable( 'Renumber', '' ) === '1';
            $page = $renumber ? self::parseIDList( $http->postVariable( 'PageOrder', '' ) ) : array();
            $set = $renumber ? array() : self::parsePriorityList( $http->postVariable( 'PriorityList', '' ) );
            // nothing is written unchecked: every node to be written is one whose shown priority came along
            $unchecked = self::uncheckedNodes( $renumber ? $page : array_keys( $set ), $expected );
            if ( $unchecked )
                throw new InvalidArgumentException( 'Not in ExpectedPriorityList: ' . implode( ',', array_slice( $unchecked, 0, 10 ) ) );
            // the check and the write in one transaction; MySQL, PostgreSQL and Oracle lock the rows read (FOR
            // UPDATE), so two drops at the same moment cannot both pass the check
            $db = eZDB::instance();
            $db->begin();
            try
            {
                if ( $renumber )
                {
                    $children = self::childPriorities( $contentNodeID, null, (int)$contentNode->attribute( 'sort_order' ) === 1 );
                    $set = self::renumberedOrder( array_keys( $children ), $page, (int)$http->postVariable( 'PageOffset', 0 ),
                                                  (int)$contentNode->attribute( 'sort_order' ) === 1 );
                }
                else
                {
                    $children = self::childPriorities( $contentNodeID, array_keys( $set + $expected ) );
                }
                $conflicts = self::priorityConflicts( $expected, $children );
                if ( $conflicts )
                {
                    $db->commit();
                    return array( 'conflict' => true, 'nodes' => $conflicts );
                }
                $changed = array();
                foreach ( $set as $nodeID => $priority )
                {
                    // a node that is not a child is not written (updatePriority() matches the parent too)
                    if ( isset( $children[$nodeID] ) && $children[$nodeID] !== $priority )
                        $changed[$nodeID] = array( $children[$nodeID], $priority );
                }
                if ( !$changed )
                {
                    $db->commit();
                    return array( 'conflict' => false, 'changed' => new stdClass() );
                }
                $priorityArray = array();
                foreach ( $changed as $pair )
                    $priorityArray[] = $pair[1];
                $status = self::runUpdatePriority( $contentNodeID, $priorityArray, array_keys( $changed ) );
                $db->commit();
            }
            catch ( Throwable $e )
            {
                // nothing half written: the check and the write go back together
                $db->rollback();
                throw $e;
            }
            self::clearObjectCache( $http );
            // a workflow on pre_updatepriority that stopped or queued the operation: the priorities are not the new
            // ones (yet), and the table must not show them as saved
            if ( $status !== eZModuleOperationInfo::STATUS_CONTINUE )
                throw new RuntimeException( "The new order was not written: a workflow stopped or deferred it (operation status $status)" );
            return array( 'conflict' => false, 'changed' => $changed );
        }

        self::runUpdatePriority( $contentNodeID, $http->postVariable( 'Priority' ), $http->postVariable( 'PriorityID' ) );
        self::clearObjectCache( $http );
    }

    /**
     * The kernel's own way of writing priorities, as content/action's UpdatePriorityButton: the content/updatepriority
     * operation (with its pre_ and post_updatepriority triggers) when it is available, else
     * eZContentOperationCollection::updatePriority() (cache, search index and audit).
     *
     * @param int $parentNodeID
     * @param mixed $priorityArray
     * @param mixed $priorityIDArray
     * @return int the operation's status (eZModuleOperationInfo::STATUS_*)
     */
    protected static function runUpdatePriority( $parentNodeID, $priorityArray, $priorityIDArray )
    {
        if ( eZOperationHandler::operationIsAvailable( 'content_updatepriority' ) )
        {
            $operationResult = eZOperationHandler::execute( 'content', 'updatepriority',
                                                             array( 'node_id' => $parentNodeID,
                                                                    'priority' => $priorityArray,
                                                                    'priority_id' => $priorityIDArray ), null, true );
            if ( is_array( $operationResult ) && isset( $operationResult['status'] ) && is_int( $operationResult['status'] ) )
                return $operationResult['status'];
            return eZModuleOperationInfo::STATUS_CONTINUE;
        }
        eZContentOperationCollection::updatePriority( $parentNodeID, $priorityArray, $priorityIDArray );
        return eZModuleOperationInfo::STATUS_CONTINUE;
    }

    /**
     * The view cache of the posted ContentObjectID (the parent's object), as content/action does after new priorities.
     *
     * @param eZHTTPTool $http
     */
    protected static function clearObjectCache( eZHTTPTool $http )
    {
        if ( $http->hasPostVariable( 'ContentObjectID' ) )
        {
            $objectID = (int)$http->postVariable( 'ContentObjectID' );
            if ( $objectID > 0 )
                eZContentCacheManager::clearContentCacheIfNeeded( $objectID );
        }
    }

    /**
     * "node_id:priority,node_id:priority" as array( node_id => priority ); '' is an empty list. Node ids are
     * positive, priorities whole numbers in the range of the priority column.
     *
     * @param mixed $text
     * @return array
     * @throws InvalidArgumentException for anything else, or more than 10000 entries
     */
    public static function parsePriorityList( $text )
    {
        if ( !is_string( $text ) )
            throw new InvalidArgumentException( 'A priority list is a text' );
        $list = array();
        $text = trim( $text );
        if ( $text === '' )
            return $list;
        $pairs = explode( ',', $text );
        if ( count( $pairs ) > 10000 )
            throw new InvalidArgumentException( 'A priority list has at most 10000 entries' );
        foreach ( $pairs as $pair )
        {
            if ( !preg_match( '/^\s*([1-9][0-9]{0,9}):(-?[0-9]{1,10})\s*$/', $pair, $m )
                 || (int)$m[2] > 2147483647 || (int)$m[2] < -2147483648 )
                throw new InvalidArgumentException( 'Not a priority list entry: ' . substr( $pair, 0, 40 ) );
            $list[(int)$m[1]] = (int)$m[2];
        }
        return $list;
    }

    /**
     * "id,id,id" as a list of positive node ids, in its order, each once.
     *
     * @param mixed $text
     * @return int[]
     * @throws InvalidArgumentException
     */
    public static function parseIDList( $text )
    {
        if ( !is_string( $text ) || !preg_match( '/^\s*[1-9][0-9]{0,9}(\s*,\s*[1-9][0-9]{0,9})*\s*$/', $text ) )
            throw new InvalidArgumentException( 'Not a list of node ids' );
        $ids = array_map( 'intval', preg_split( '/\s*,\s*/', trim( $text ) ) );
        if ( count( $ids ) > 10000 || count( array_unique( $ids ) ) !== count( $ids ) )
            throw new InvalidArgumentException( 'Not a list of node ids' );
        return $ids;
    }

    /**
     * The nodes to be written ($nodeIDs: PriorityList's ids, or PageOrder's) whose shown priority did not come along
     * in $expected, as a list of node ids; empty when every one is checked. updatepriority writes nothing then.
     *
     * @param int[] $nodeIDs
     * @param array $expected node_id => priority the client showed
     * @return int[]
     */
    public static function uncheckedNodes( array $nodeIDs, array $expected )
    {
        $unchecked = array();
        foreach ( $nodeIDs as $nodeID )
        {
            if ( !array_key_exists( (int)$nodeID, $expected ) )
                $unchecked[] = (int)$nodeID;
        }
        return $unchecked;
    }

    /**
     * The nodes of $expected whose priority is not the expected one, or which are not among $current (no longer a
     * child), as a sorted list of node ids.
     *
     * @param array $expected node_id => priority the client showed
     * @param array $current node_id => priority now
     * @return int[]
     */
    public static function priorityConflicts( array $expected, array $current )
    {
        $conflicts = array();
        foreach ( $expected as $nodeID => $priority )
        {
            if ( !array_key_exists( $nodeID, $current ) || (int)$current[$nodeID] !== (int)$priority )
                $conflicts[] = (int)$nodeID;
        }
        sort( $conflicts );
        return $conflicts;
    }

    /**
     * Every child numbered again in steps of 10: the children in their current order ($childIDs) without the rows of
     * the page, the page's rows ($pageIDs, in their new order) put back at $offset, then 10, 20, 30... from the
     * first to the last for an ascending parent, the other way round for a descending one (the first row gets the
     * highest value). Rows of the page that are no longer children are left out.
     *
     * @param int[] $childIDs the children in the order the list shows them
     * @param int[] $pageIDs the rows of the page in their new order
     * @param int $offset where the page starts
     * @param bool $ascending the parent's sort direction
     * @return array node_id => priority
     */
    public static function renumberedOrder( array $childIDs, array $pageIDs, $offset, $ascending )
    {
        $childIDs = array_map( 'intval', $childIDs );
        $children = array_flip( $childIDs );
        $page = array_values( array_filter( array_map( 'intval', $pageIDs ), function ( $id ) use ( $children ) { return isset( $children[$id] ); } ) );
        $onPage = array_flip( $page );
        $rest = array_values( array_filter( $childIDs, function ( $id ) use ( $onPage ) { return !isset( $onPage[$id] ); } ) );
        $offset = max( 0, min( (int)$offset, count( $rest ) ) );
        $order = array_merge( array_slice( $rest, 0, $offset ), $page, array_slice( $rest, $offset ) );
        $count = count( $order );
        $priorities = array();
        foreach ( $order as $i => $id )
            $priorities[$id] = $ascending ? ( $i + 1 ) * 10 : ( $count - $i ) * 10;
        return $priorities;
    }

    /**
     * The priorities of the children of a node, node_id => priority: the given ones, or all of them in the order a
     * list sorted by priority shows them (ties by node id, as subTree() and expsubitems::rows sort them).
     *
     * @param int $parentNodeID
     * @param int[]|null $nodeIDs
     * @param bool $ascending
     * @return array
     */
    protected static function childPriorities( $parentNodeID, $nodeIDs, $ascending = true )
    {
        $db = eZDB::instance();
        $where = 'parent_node_id=' . (int)$parentNodeID;
        if ( $nodeIDs !== null )
        {
            if ( !$nodeIDs )
                return array();
            $where .= ' AND ' . $db->generateSQLINStatement( array_map( 'intval', $nodeIDs ), 'node_id', false, true, 'int' );
        }
        $dir = $ascending ? 'ASC' : 'DESC';
        $lock = in_array( $db->databaseName(), array( 'mysql', 'postgresql', 'oracle' ), true ) ? ' FOR UPDATE' : '';
        $rows = $db->arrayQuery( "SELECT node_id, priority FROM ezcontentobject_tree WHERE $where ORDER BY priority $dir, node_id $dir$lock" );
        $priorities = array();
        foreach ( $rows as $row )
            $priorities[(int)$row['node_id']] = (int)$row['priority'];
        return $priorities;
    }

    /**
     * A helper function which maps sort keys from encoded JSON node
     * to supported values
     *
     * @since 1.2
     * @param string $sort
     * @return string
     */
    protected static function sortMap( $sort )
    {
        switch ( $sort )
        {
            case 'modified_date':
                $sortKey = 'modified';
                break;
            case 'published_date':
                $sortKey = 'published';
                break;
            case 'hidden_status_string':
                $sortKey = 'visibility';
                break;
            default:
                $sortKey = $sort;
        }

        return $sortKey;
    }
}

?>
