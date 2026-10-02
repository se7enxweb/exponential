<?php
/**
 * File containing the expContentJobRemoveSubtree class.
 *
 * The content job 'remove': removes one or more subtrees in batches, deepest nodes first (leaves before their
 * parents), each batch through the kernel's own remove (the 'content_delete' operation when workflows are
 * attached to it, else eZContentOperationCollection::deleteObject(), which is eZContentObjectTreeNode::
 * removeSubtrees() and removeNodeFromTree() per node), so the end state is the one the synchronous remove of
 * content/removeobject leaves:
 *
 *   - an object with another location outside the removed subtrees loses only this location (its main
 *     location moves to another one when this was the main one), as removeNodeFromTree() does;
 *   - an object whose last location goes is moved to the trash or purged, as asked; the trash is not used for
 *     a subtree that holds user objects (isNodeTrashAllowed(), decided per removed subtree as the kernel does);
 *   - URL aliases, view caches, the search index, pending actions, node assignments, bookmarks, recent items,
 *     policies limited to the node: cleared by the kernel's per-node remove, in the batch's transaction.
 *
 * Permissions: when the job is created, the requesting user must be allowed to remove every subtree completely
 * (subtreeRemovalInformation(), as the view asks); in the worker, which runs as that user, the kernel checks
 * can_remove per node again, and a node that is refused stops the job (failed, resumable) instead of being
 * skipped while its parent goes.
 *
 * The batch is chosen from the database each time (the deepest N nodes still there), so a batch run again after
 * kill -9 finds its nodes gone and removes the next ones: nothing is removed twice and nothing is left.
 *
 * Parameters: node_ids (int[]), move_to_trash (bool, default content.ini [RemoveSettings] DefaultRemoveAction).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentJobRemoveSubtree implements expContentJobType
{
    public function validate( array $params, eZUser $user )
    {
        $ids = $this->nodeIDs( $params );
        if ( !$ids )
            throw new expContentJobException( self::tr( 'No node to remove was given.' ) );
        $moveToTrash = array_key_exists( 'move_to_trash', $params ) ? (bool) $params['move_to_trash'] : $this->defaultMoveToTrash();

        $previous = eZUser::currentUser();
        $switched = (int) $previous->attribute( 'contentobject_id' ) !== (int) $user->attribute( 'contentobject_id' );
        if ( $switched )
            eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
        try
        {
            $roots = array();
            foreach ( $ids as $id )
            {
                $node = eZContentObjectTreeNode::fetch( $id );
                if ( !$node instanceof eZContentObjectTreeNode )
                    throw new expContentJobException( self::tr( 'The node %1 does not exist (any more).', array( $id ) ) );
                if ( (int) $node->attribute( 'depth' ) <= 1 )
                    throw new expContentJobException( self::tr( 'The node %1 is a top level node and cannot be removed.', array( $id ) ) );
                $roots[$id] = array( 'node_id' => $id, 'path' => $node->attribute( 'path_string' ) );
            }
            $info = eZContentObjectTreeNode::subtreeRemovalInformation( array_keys( $roots ) );
            foreach ( $info['delete_list'] as $item )
            {
                if ( !$item['can_remove'] )
                {
                    $nodeID = $item['node']->attribute( 'node_id' );
                    throw new expContentJobException( self::tr( 'You do not have permission to remove "%1" (node %2) or something below it.',
                                                                array( $item['node_name'], $nodeID ) ) );
                }
            }
            // a subtree inside another one is removed with it
            uasort( $roots, function ( $a, $b ) { return strlen( $a['path'] ) - strlen( $b['path'] ); } );
            $kept = array();
            foreach ( $roots as $id => $root )
            {
                foreach ( $kept as $outer )
                    if ( strpos( $root['path'], $outer['path'] ) === 0 )
                        continue 2;
                $node = eZContentObjectTreeNode::fetch( $id );
                $root['trash'] = $moveToTrash && $node->isNodeTrashAllowed();
                $kept[$id] = $root;
            }
            return array( 'node_ids' => array_map( 'intval', array_keys( $kept ) ),
                          'move_to_trash' => $moveToTrash,
                          'roots' => array_values( $kept ),
                          'has_pending_object' => !empty( $info['has_pending_object'] ) );
        }
        finally
        {
            if ( $switched )
                eZUser::setCurrentlyLoggedInUser( $previous, $previous->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
        }
    }

    public function countNodes( array $params )
    {
        $paths = array();
        if ( isset( $params['roots'] ) )
        {
            foreach ( $params['roots'] as $root )
                $paths[] = $root['path'];
        }
        else
        {
            foreach ( $this->nodeIDs( $params ) as $id )
            {
                $row = eZContentObjectTreeNode::fetch( $id, false, false );
                if ( is_array( $row ) )
                    $paths[] = $row['path_string'];
            }
        }
        $paths = self::outermost( $paths );
        if ( !$paths )
            return 0;
        $rows = eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS c FROM ezcontentobject_tree WHERE ' . self::pathCondition( $paths ) );
        return $rows ? (int) $rows[0]['c'] : 0;
    }

    public function locks( array $params )
    {
        $locks = array();
        foreach ( $params['roots'] as $root )
            $locks[] = array( 'path' => $root['path'], 'mode' => expContentJobLock::SUBTREE );
        return $locks;
    }

    public function describe( array $params )
    {
        $n = count( $params['roots'] );
        $trash = false;
        foreach ( $params['roots'] as $root )
            $trash = $trash || $root['trash'];
        return self::tr( $trash ? 'Remove %1 subtree(s) (nodes %2) to the trash' : 'Remove %1 subtree(s) (nodes %2) permanently',
                         array( $n, implode( ', ', $params['node_ids'] ) ) );
    }

    public function prepare( expContentJob $job )
    {
        $cp =& $job->checkpoint();
        $cp += array( 'removed_nodes' => 0, 'removed_objects' => 0, 'trashed_objects' => 0, 'location_only' => 0 );
        $job->setProgress( array( 'phase' => 'remove' ) );
    }

    public function runBatch( expContentJob $job, $batchSize )
    {
        $params = $job->params();
        $db = eZDB::instance();
        $roots = $params['roots'];
        $paths = array();
        foreach ( $roots as $root )
            $paths[] = $root['path'];

        $rows = $db->arrayQuery( 'SELECT node_id, path_string, contentobject_id, depth FROM ezcontentobject_tree WHERE '
                                 . self::pathCondition( $paths ) . ' ORDER BY depth DESC, node_id DESC',
                                 array( 'limit' => max( 1, (int) $batchSize ), 'offset' => 0 ) );
        if ( !$rows )
            return array( 'done' => 0, 'finished' => true, 'message' => self::tr( 'Nothing left to remove.' ) );

        // the trash or not, per removed subtree as the kernel decides it
        $groups = array( 1 => array(), 0 => array() );
        $objectIDs = array();
        foreach ( $rows as $row )
        {
            $trash = 0;
            foreach ( $roots as $root )
                if ( strpos( $row['path_string'], $root['path'] ) === 0 )
                    $trash = $root['trash'] ? 1 : 0;
            $groups[$trash][] = (int) $row['node_id'];
            $objectIDs[(int) $row['contentobject_id']] = $trash;
        }

        // objects that have locations outside this batch keep living (a location removed only)
        $before = $this->locationCounts( array_keys( $objectIDs ) );

        foreach ( $groups as $trash => $nodeIDs )
        {
            if ( !$nodeIDs )
                continue;
            if ( eZOperationHandler::operationIsAvailable( 'content_delete' ) )
            {
                $result = eZOperationHandler::execute( 'content', 'delete',
                                                       array( 'node_id_list' => $nodeIDs, 'move_to_trash' => (bool) $trash ),
                                                       null, true );
                if ( is_array( $result ) && isset( $result['status'] ) && $result['status'] != eZModuleOperationInfo::STATUS_CONTINUE )
                    throw new expContentJobException( self::tr( 'A workflow on content/delete stopped the remove (status %1).', array( $result['status'] ) ) );
            }
            else
                eZContentOperationCollection::deleteObject( $nodeIDs, (bool) $trash );
        }

        // every node of the batch must be gone: one left means the kernel refused it (permission)
        $all = array_merge( $groups[1], $groups[0] );
        $left = $db->arrayQuery( 'SELECT node_id FROM ezcontentobject_tree WHERE ' . $db->generateSQLINStatement( $all, 'node_id', false, true, 'int' ) );
        if ( $left )
        {
            $ids = array();
            foreach ( $left as $row )
                $ids[] = $row['node_id'];
            throw expContentJobException::forNode( self::tr( 'The node(s) %1 could not be removed: the user may not remove them (have the permissions changed?). Nothing of this batch was removed.',
                                                             array( implode( ', ', $ids ) ) ), $ids[0] );
        }

        $after = $this->locationCounts( array_keys( $objectIDs ) );
        $cp =& $job->checkpoint();
        $cp['removed_nodes'] += count( $all );
        foreach ( $objectIDs as $objectID => $trash )
        {
            if ( empty( $after[$objectID] ) )
            {
                $cp['removed_objects']++;
                if ( $trash )
                    $cp['trashed_objects']++;
            }
            else if ( isset( $before[$objectID] ) )
                $cp['location_only']++;
        }
        // counted from the database, so the progress is right also after a batch committed before a crash
        $left = $db->arrayQuery( 'SELECT COUNT(*) AS c FROM ezcontentobject_tree WHERE ' . self::pathCondition( $paths ) );
        $remaining = $left ? (int) $left[0]['c'] : 0;
        $total = (int) $job->progress()['total'];
        return array( 'done' => count( $all ), 'done_total' => max( 0, $total - $remaining ), 'finished' => $remaining === 0,
                      'message' => self::tr( '%1 nodes removed', array( $cp['removed_nodes'] ) ) );
    }

    public function afterBatch( expContentJob $job )
    {
        $cp = $job->checkpoint();
        $job->setResult( 'removed_nodes', $cp['removed_nodes'] );
        $job->setResult( 'removed_objects', $cp['removed_objects'] );
        $job->setResult( 'trashed_objects', $cp['trashed_objects'] );
        $job->setResult( 'locations_only', $cp['location_only'] );
    }

    public function finish( expContentJob $job )
    {
        $this->afterBatch( $job );
        $job->setProgress( array( 'message' => self::tr( 'Removed %1 nodes', array( $job->checkpoint()['removed_nodes'] ) ) ) );
    }

    // ---- helpers -------------------------------------------------------------------------------

    /** @return int[] */
    protected function nodeIDs( array $params )
    {
        $ids = array();
        foreach ( isset( $params['node_ids'] ) ? (array) $params['node_ids'] : array() as $id )
            if ( is_numeric( $id ) && (int) $id > 0 )
                $ids[(int) $id] = (int) $id;
        return array_values( $ids );
    }

    protected function defaultMoveToTrash()
    {
        $ini = eZINI::instance( 'content.ini' );
        $action = $ini->hasVariable( 'RemoveSettings', 'DefaultRemoveAction' ) ? $ini->variable( 'RemoveSettings', 'DefaultRemoveAction' ) : 'trash';
        return $action !== 'delete';
    }

    /** objectID => number of locations */
    protected function locationCounts( array $objectIDs )
    {
        if ( !$objectIDs )
            return array();
        $db = eZDB::instance();
        $counts = array();
        foreach ( $db->arrayQuery( 'SELECT contentobject_id, COUNT(*) AS c FROM ezcontentobject_tree WHERE '
                                   . $db->generateSQLINStatement( $objectIDs, 'contentobject_id', false, true, 'int' )
                                   . ' GROUP BY contentobject_id' ) as $row )
            $counts[(int) $row['contentobject_id']] = (int) $row['c'];
        return $counts;
    }

    /**
     * The paths that are not inside another one of the list.
     *
     * @param string[] $paths
     * @return string[]
     */
    public static function outermost( array $paths )
    {
        $paths = array_values( array_unique( array_filter( $paths ) ) );
        usort( $paths, function ( $a, $b ) { return strlen( $a ) - strlen( $b ); } );
        $kept = array();
        foreach ( $paths as $p )
        {
            foreach ( $kept as $outer )
                if ( strpos( $p, $outer ) === 0 )
                    continue 2;
            $kept[] = $p;
        }
        return $kept;
    }

    /**
     * WHERE condition for the nodes of the subtrees (the roots included).
     *
     * @param string[] $paths
     * @return string
     */
    public static function pathCondition( array $paths )
    {
        $db = eZDB::instance();
        $or = array();
        foreach ( $paths as $p )
        {
            if ( expContentJobLock::normalizePath( $p ) === '' )
                continue;
            $or[] = "path_string LIKE '" . $db->escapeString( $p ) . "%'";
        }
        return $or ? '( ' . implode( ' OR ', $or ) . ' )' : '1 = 0';
    }

    protected static function tr( $text, array $args = array() )
    {
        return ezpI18n::tr( 'kernel/contentjob', $text, null, $args );
    }
}
