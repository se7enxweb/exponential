<?php
/**
 * File containing the expContentJobRemoveLocation class.
 *
 * The content job 'removelocation': removes many selected locations (nodes), keeping their objects, through the
 * kernel's own operation, as content/action RemoveAssignment does it (the 'content_removelocation' operation
 * when workflows are attached, else eZContentOperationCollection::removeNodes(): the node and its URL alias, the
 * node assignment, the main location moved to another location, the search engine, the view caches).
 *
 * Checks, as content/action does: for every selected node the requesting user may edit its object or manage
 * locations, and may remove the node or the location. Refused: a node with children (content/action sends that
 * to the remove dialog; use the 'remove' job), and the last location of an object (that is removing the
 * object). The batches check the last-location rule again (a location removed meanwhile) and skip with a warning.
 * A node gone already is left alone (idempotent).
 *
 * Parameters: node_ids (int[]).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentJobRemoveLocation extends expContentJobBatchType
{
    protected function validateParams( array $params, eZUser $user )
    {
        $ids = $this->requireNodeIDs( $params );
        $perObject = array();
        $paths = array();
        foreach ( $ids as $id )
        {
            $node = eZContentObjectTreeNode::fetch( $id );
            if ( !$node instanceof eZContentObjectTreeNode )
                throw new expContentJobException( self::tr( 'The node %1 does not exist (any more).', array( $id ) ) );
            $object = $node->object();
            if ( !$object->checkAccess( 'edit' ) && !$user->hasManageLocations() )
                throw new expContentJobException( self::tr( 'You do not have permission to remove locations of the object of node %1.', array( $id ) ) );
            if ( !$node->canRemove() && !$node->canRemoveLocation() )
                throw new expContentJobException( self::tr( 'You do not have permission to remove the location %1.', array( $id ) ) );
            if ( $node->childrenCount( false ) > 0 )
                throw new expContentJobException( self::tr( 'The location %1 has children: remove it with its subtree instead.', array( $id ) ) );
            $objectID = (int) $object->attribute( 'id' );
            $perObject[$objectID] = isset( $perObject[$objectID] ) ? $perObject[$objectID] + 1 : 1;
            if ( $perObject[$objectID] >= count( $object->assignedNodes( false ) ) )
                throw new expContentJobException( self::tr( 'Node %1 is the last location of its object: remove the object instead.', array( $id ) ) );
            $paths[] = $node->attribute( 'path_string' );
        }
        return array( 'node_ids' => $ids, 'paths' => $paths );
    }

    public function countNodes( array $params )
    {
        return count( array_unique( array_filter( array_map( 'intval', isset( $params['node_ids'] ) ? (array) $params['node_ids'] : array() ) ) ) );
    }

    public function locks( array $params )
    {
        $locks = array();
        foreach ( $params['paths'] as $path )
            $locks[] = array( 'path' => $path, 'mode' => expContentJobLock::SUBTREE );
        return $locks;
    }

    public function describe( array $params )
    {
        return self::tr( 'Remove %1 selected locations', array( count( $params['node_ids'] ) ) );
    }

    protected function buildWorkList( expContentJob $job )
    {
        return $job->params()['node_ids'];
    }

    protected function processNodes( expContentJob $job, array $rows, array &$cp )
    {
        $db = eZDB::instance();
        $remove = array();
        $leaving = array();
        foreach ( $rows as $row )
        {
            $objectID = (int) $row['contentobject_id'];
            $count = $db->arrayQuery( "SELECT COUNT(*) AS c FROM ezcontentobject_tree WHERE contentobject_id=$objectID" );
            $leaving[$objectID] = isset( $leaving[$objectID] ) ? $leaving[$objectID] + 1 : 1;
            if ( $leaving[$objectID] >= (int) $count[0]['c'] )
            {
                $cp['skipped']++;
                $this->warn( $cp, self::tr( 'Location %1 was not removed: it is the last location of its object.', array( $row['node_id'] ) ) );
                continue;
            }
            $children = $db->arrayQuery( 'SELECT COUNT(*) AS c FROM ezcontentobject_tree WHERE parent_node_id=' . (int) $row['node_id'] );
            if ( (int) $children[0]['c'] > 0 )
            {
                $cp['skipped']++;
                $this->warn( $cp, self::tr( 'Location %1 was not removed: it has children now.', array( $row['node_id'] ) ) );
                continue;
            }
            $remove[] = (int) $row['node_id'];
        }
        if ( !$remove )
            return;
        if ( eZOperationHandler::operationIsAvailable( 'content_removelocation' ) )
        {
            $result = eZOperationHandler::execute( 'content', 'removelocation', array( 'node_list' => $remove ), null, true );
            if ( is_array( $result ) && isset( $result['status'] ) && $result['status'] != eZModuleOperationInfo::STATUS_CONTINUE )
                throw new expContentJobException( self::tr( 'A workflow on content/removelocation stopped the remove (status %1).', array( $result['status'] ) ) );
        }
        else
            eZContentOperationCollection::removeNodes( $remove );
        $left = $db->arrayQuery( 'SELECT node_id FROM ezcontentobject_tree WHERE ' . $db->generateSQLINStatement( $remove, 'node_id', false, true, 'int' ) );
        if ( $left )
            throw new expContentJobException( self::tr( 'The locations %1 could not be removed.', array( implode( ', ', array_column( $left, 'node_id' ) ) ) ) );
        $cp['changed'] += count( $remove );
        eZContentObject::clearCache();
    }
}
