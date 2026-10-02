<?php
/**
 * File containing the expContentJobMoveSubtree class.
 *
 * The content job 'move': moves a subtree under a new parent. The main step is the kernel's own move of the top
 * node, as content/action MoveNode does it (the 'content_move' operation when workflows are attached, else
 * eZContentOperationCollection::moveNode(): the path strings of the subtree, the URL alias and path identification
 * of the subtree, the section when the main location moves into another section, the node assignment, the
 * reverse relations and the cache of the top). The batches then clear the view cache of every object in the
 * moved subtree (their URLs changed) and, for a search engine other than the built-in one, reindex them.
 *
 * Checks (when the job is created, as the requesting user, as content/action does): the user may move the node
 * (canMoveFrom), may create its class under the new parent (canMoveTo), and the new parent is not the node or
 * inside its subtree. Locks: the subtree (at its old place, then at its new place) and the new parent as a target.
 * The move is idempotent: a run again after kill -9 finds the node under its new parent and does not move it again.
 *
 * Parameters: node_id, new_parent_node_id.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentJobMoveSubtree extends expContentJobBatchType
{
    protected function validateParams( array $params, eZUser $user )
    {
        $node = $this->requireNode( $params, 'node_id' );
        $newParent = $this->requireNode( $params, 'new_parent_node_id' );
        $nodeID = (int) $node->attribute( 'node_id' );
        if ( (int) $node->attribute( 'depth' ) <= 1 )
            throw new expContentJobException( self::tr( 'The node %1 is a top level node and cannot be moved.', array( $nodeID ) ) );
        if ( !$node->canMoveFrom() )
            throw new expContentJobException( self::tr( 'You do not have permission to move the node %1.', array( $nodeID ) ) );
        $object = $node->object();
        if ( !$newParent->canMoveTo( $object->attribute( 'contentclass_id' ) ) )
            throw new expContentJobException( self::tr( 'You do not have permission to place this kind of content under the node %1.',
                                                        array( $newParent->attribute( 'node_id' ) ) ) );
        if ( in_array( $nodeID, array_map( 'intval', $newParent->pathArray() ), true ) )
            throw new expContentJobException( self::tr( 'A node cannot be moved under itself or one of its own children.' ) );
        return array( 'node_id' => $nodeID, 'object_id' => (int) $object->attribute( 'id' ),
                      'new_parent_node_id' => (int) $newParent->attribute( 'node_id' ),
                      'old_parent_node_id' => (int) $node->attribute( 'parent_node_id' ),
                      'source_path' => $node->attribute( 'path_string' ), 'destination_path' => $newParent->attribute( 'path_string' ) );
    }

    public function locks( array $params )
    {
        return array( array( 'path' => $params['source_path'], 'mode' => expContentJobLock::SUBTREE ),
                      array( 'path' => $params['destination_path'], 'mode' => expContentJobLock::NODE ) );
    }

    public function describe( array $params )
    {
        return self::tr( 'Move the subtree of node %1 under node %2', array( $params['node_id'], $params['new_parent_node_id'] ) );
    }

    protected function hasMainStep()
    {
        return true;
    }

    protected function mainStep( expContentJob $job, array &$cp )
    {
        $p = $job->params();
        $node = eZContentObjectTreeNode::fetch( $p['node_id'] );
        if ( !$node )
            throw new expContentJobException( self::tr( 'The node %1 does not exist any more.', array( $p['node_id'] ) ) );
        if ( (int) $node->attribute( 'parent_node_id' ) === (int) $p['new_parent_node_id'] )
            return self::tr( 'The node is under its new parent already.' );
        if ( !eZContentObjectTreeNode::fetch( $p['new_parent_node_id'], false, false ) )
            throw new expContentJobException( self::tr( 'The new parent node %1 does not exist any more.', array( $p['new_parent_node_id'] ) ) );
        if ( eZOperationHandler::operationIsAvailable( 'content_move' ) )
        {
            $result = eZOperationHandler::execute( 'content', 'move', array( 'node_id' => $p['node_id'], 'object_id' => $p['object_id'],
                                                                           'new_parent_node_id' => $p['new_parent_node_id'] ), null, true );
            if ( is_array( $result ) && isset( $result['status'] ) && $result['status'] != eZModuleOperationInfo::STATUS_CONTINUE )
                throw new expContentJobException( self::tr( 'A workflow on content/move stopped the move (status %1).', array( $result['status'] ) ) );
        }
        else
        {
            $result = eZContentOperationCollection::moveNode( $p['node_id'], $p['object_id'], $p['new_parent_node_id'] );
            if ( empty( $result['status'] ) )
                throw new expContentJobException( self::tr( 'The kernel could not move the node %1.', array( $p['node_id'] ) ) );
        }
        $moved = eZContentObjectTreeNode::fetch( $p['node_id'], false, false );
        if ( !is_array( $moved ) || (int) $moved['parent_node_id'] !== (int) $p['new_parent_node_id'] )
            throw new expContentJobException( self::tr( 'The node %1 was not moved.', array( $p['node_id'] ) ) );
        $this->verifyPaths( $job, $moved );
        $cp['changed']++;
        return self::tr( 'Moved; now the caches of the subtree' );
    }

    /**
     * The moved subtree's paths must be exactly right before the move is committed: the top's path is the new
     * parent's path and its id, every node of the work list has a path under it that ends in '/' and ends in its
     * own id. Anything else (a database driver building the paths wrongly) throws, so the batch is rolled back
     * and the subtree stays where it was, intact.
     *
     * @param expContentJob $job
     * @param array $moved the top node's row after the move
     */
    protected function verifyPaths( expContentJob $job, array $moved )
    {
        $p = $job->params();
        $db = eZDB::instance();
        $parent = $db->arrayQuery( 'SELECT path_string FROM ezcontentobject_tree WHERE node_id=' . (int) $p['new_parent_node_id'] );
        $expected = ( $parent ? $parent[0]['path_string'] : '' ) . (int) $p['node_id'] . '/';
        if ( $moved['path_string'] !== $expected )
            throw expContentJobException::forNode( self::tr( 'The move was undone: the database gave node %1 the path %2 instead of %3.',
                                                             array( $p['node_id'], $moved['path_string'], $expected ) ), $p['node_id'] );
        $list = expContentJobStore::readSide( $job->id(), '.nodes.json' );
        $bad = 0;
        $first = '';
        foreach ( array_chunk( (array) $list, 500 ) as $chunk )
        {
            foreach ( $db->arrayQuery( 'SELECT node_id, path_string FROM ezcontentobject_tree WHERE '
                                       . $db->generateSQLINStatement( $chunk, 'node_id', false, true, 'int' ) ) as $row )
            {
                $path = $row['path_string'];
                $ok = strpos( $path, $expected ) === 0 && substr( $path, -1 ) === '/'
                      && substr( $path, -strlen( $row['node_id'] . '/' ) - 1 ) === '/' . $row['node_id'] . '/';
                if ( !$ok )
                {
                    $bad++;
                    if ( $first === '' )
                        $first = $row['node_id'] . ' (' . $path . ')';
                }
            }
        }
        if ( $bad )
            throw expContentJobException::forNode( self::tr( 'The move was undone: the database built %1 wrong paths in the moved subtree, the first %2.',
                                                             array( $bad, $first ) ), $p['node_id'] );
    }

    protected function processNodes( expContentJob $job, array $rows, array &$cp )
    {
        $objectIDs = self::objectIDs( $rows );
        self::clearViewCaches( $objectIDs );
        self::reindexIfNeeded( $objectIDs );
    }

    public function afterBatch( expContentJob $job )
    {
        parent::afterBatch( $job );
        $p = $job->params();
        if ( empty( $job->result()['new_path'] ) )
        {
            $row = eZContentObjectTreeNode::fetch( $p['node_id'], false, false );
            if ( is_array( $row ) && (int) $row['parent_node_id'] === (int) $p['new_parent_node_id'] )
            {
                // the subtree is at its new place: lock it there too
                expContentJobLock::add( $job->id(), array( 'path' => $row['path_string'], 'mode' => expContentJobLock::SUBTREE ) );
                $job->setResult( 'new_path', $row['path_string'] );
            }
        }
    }
}
