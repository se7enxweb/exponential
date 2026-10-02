<?php
/**
 * File containing the expContentJobAddLocation class.
 *
 * The content job 'addlocation': gives the objects of many selected nodes a new location under one target node,
 * per object through the kernel's own operation, as content/action AddAssignment does it (the 'content_addlocation'
 * operation when workflows are attached, else eZContentOperationCollection::addAssignment(): the new node with
 * its URL alias, the search engine, the user policy cache of a user object, the view cache).
 *
 * Checks, as content/action does: when the job is created, for every selected node the requesting user may edit
 * its object or manage locations; per object in the batches, the kernel's own create / add-location check under
 * the target (an object that may not be placed there is skipped with a warning, where the kernel skips it
 * silently). An object that has a location under the target already is left alone (idempotent).
 *
 * Parameters: node_ids (int[]), target_node_id.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentJobAddLocation extends expContentJobBatchType
{
    protected function validateParams( array $params, eZUser $user )
    {
        $target = $this->requireNode( $params, 'target_node_id' );
        $ids = $this->requireNodeIDs( $params );
        foreach ( $ids as $id )
        {
            $node = eZContentObjectTreeNode::fetch( $id );
            if ( !$node instanceof eZContentObjectTreeNode )
                throw new expContentJobException( self::tr( 'The node %1 does not exist (any more).', array( $id ) ) );
            $object = $node->object();
            if ( !$object->checkAccess( 'edit' ) && !$user->hasManageLocations() )
                throw new expContentJobException( self::tr( 'You do not have permission to add a location to the object of node %1.', array( $id ) ) );
        }
        return array( 'node_ids' => $ids, 'target_node_id' => (int) $target->attribute( 'node_id' ),
                      'target_path' => $target->attribute( 'path_string' ) );
    }

    public function countNodes( array $params )
    {
        return count( array_unique( array_filter( array_map( 'intval', isset( $params['node_ids'] ) ? (array) $params['node_ids'] : array() ) ) ) );
    }

    public function locks( array $params )
    {
        // the target as a target; the selected nodes so that nobody removes or moves them meanwhile
        $locks = array( array( 'path' => $params['target_path'], 'mode' => expContentJobLock::NODE ) );
        $db = eZDB::instance();
        foreach ( array_chunk( $params['node_ids'], 500 ) as $chunk )
            foreach ( $db->arrayQuery( 'SELECT path_string FROM ezcontentobject_tree WHERE ' . $db->generateSQLINStatement( $chunk, 'node_id', false, true, 'int' ) ) as $row )
                $locks[] = array( 'path' => $row['path_string'], 'mode' => expContentJobLock::NODE );
        return $locks;
    }

    public function describe( array $params )
    {
        return self::tr( 'Add a location under node %1 for %2 selected nodes', array( $params['target_node_id'], count( $params['node_ids'] ) ) );
    }

    protected function buildWorkList( expContentJob $job )
    {
        return $job->params()['node_ids'];
    }

    protected function processNodes( expContentJob $job, array $rows, array &$cp )
    {
        $target = (int) $job->params()['target_node_id'];
        $db = eZDB::instance();
        if ( !eZContentObjectTreeNode::fetch( $target, false, false ) )
            throw new expContentJobException( self::tr( 'The target node %1 does not exist any more.', array( $target ) ) );
        $useOperation = eZOperationHandler::operationIsAvailable( 'content_addlocation' );
        $seen = array();
        foreach ( $rows as $row )
        {
            $objectID = (int) $row['contentobject_id'];
            if ( isset( $seen[$objectID] ) )
                continue;
            $seen[$objectID] = true;
            $has = $db->arrayQuery( "SELECT node_id FROM ezcontentobject_tree WHERE contentobject_id=$objectID AND parent_node_id=$target" );
            if ( $has )
                continue;
            if ( (int) $row['node_id'] === $target || strpos( $db->arrayQuery( "SELECT path_string FROM ezcontentobject_tree WHERE node_id=$target" )[0]['path_string'],
                                                              $row['path_string'] ) === 0 )
            {
                $cp['skipped']++;
                $this->warn( $cp, self::tr( 'Node (ID = %1) was not given a location under itself.', array( $row['node_id'] ) ) );
                continue;
            }
            if ( $useOperation )
                eZOperationHandler::execute( 'content', 'addlocation', array( 'node_id' => (int) $row['node_id'], 'object_id' => $objectID,
                                                                              'select_node_id_array' => array( $target ) ), null, true );
            else
                eZContentOperationCollection::addAssignment( (int) $row['node_id'], $objectID, array( $target ) );
            eZContentObject::clearCache( array( $objectID ) );
            if ( $db->arrayQuery( "SELECT node_id FROM ezcontentobject_tree WHERE contentobject_id=$objectID AND parent_node_id=$target" ) )
                $cp['changed']++;
            else
            {
                $cp['skipped']++;
                $this->warn( $cp, self::tr( 'Object (ID = %1) was not placed under node %2: you do not have permission to create it there.', array( $objectID, $target ) ) );
            }
        }
    }
}
