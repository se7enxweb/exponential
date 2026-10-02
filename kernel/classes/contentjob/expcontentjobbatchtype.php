<?php
/**
 * File containing the expContentJobBatchType class.
 *
 * The common ground of the content job types that work on a list of nodes: move, hide, reveal, section, state,
 * addlocation and removelocation. A job of such a type runs in two phases:
 *
 *   main   one batch: the kernel's own operation on the top node (moving it, hiding it ...), in the batch's
 *          transaction, idempotent (a run again after kill -9 sees it done and skips it); types without one
 *          start in the next phase
 *   nodes  the work list (<id>.nodes.json, written once when the job first runs: the subtree's node ids by depth,
 *          or the selected nodes) in batches of BatchSize, each in its own transaction: the heavy per-object part
 *          (view caches, search index, the per-object operation and its policy check)
 *
 * Progress counts work list entries; the checkpoint is the cursor in the list. Every per-node step is idempotent
 * (it looks at the database first), so a batch run again after a crash between its commit and its checkpoint
 * changes nothing twice. What the requesting user may not do to an object is skipped with a warning (the result
 * lists the first 200), the whole job is refused when the user may not do it to the top node at all.
 *
 * A subclass implements validateParams(), locks(), describe(), processNodes() and, where the kernel has one
 * operation for the top, mainStep().
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

abstract class expContentJobBatchType implements expContentJobType
{
    const MAX_WARNINGS = 200;

    /**
     * Checks and normalizes the parameters; runs as the requesting user (validate() switches to them).
     *
     * @param array $params
     * @param eZUser $user
     * @return array
     * @throws expContentJobException
     */
    abstract protected function validateParams( array $params, eZUser $user );

    /**
     * The per-node work of one batch, in the batch's transaction. Idempotent.
     *
     * @param expContentJob $job
     * @param array $rows ezcontentobject_tree rows (node_id, contentobject_id, parent_node_id, path_string, is_hidden, main_node_id) of the
     *                    batch's nodes that still exist
     * @param array $cp the checkpoint, by reference (changed, skipped, warnings)
     */
    abstract protected function processNodes( expContentJob $job, array $rows, array &$cp );

    /**
     * Whether the type has a main step.
     *
     * @return bool
     */
    protected function hasMainStep()
    {
        return false;
    }

    /**
     * The kernel's operation on the top node, in the first batch's transaction. Idempotent.
     *
     * @param expContentJob $job
     * @param array $cp
     * @return string a message
     */
    protected function mainStep( expContentJob $job, array &$cp )
    {
        return '';
    }

    /**
     * The work list, when the job first runs: by default the subtree of params['node_id'] by depth.
     *
     * @param expContentJob $job
     * @return int[]
     */
    protected function buildWorkList( expContentJob $job )
    {
        $params = $job->params();
        return self::subtreeNodeIDs( (int) $params['node_id'] );
    }

    public function validate( array $params, eZUser $user )
    {
        $previous = eZUser::currentUser();
        $switched = (int) $previous->attribute( 'contentobject_id' ) !== (int) $user->attribute( 'contentobject_id' );
        if ( $switched )
            eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
        try
        {
            return $this->validateParams( $params, $user );
        }
        finally
        {
            if ( $switched )
                eZUser::setCurrentlyLoggedInUser( $previous, $previous->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
        }
    }

    /**
     * Default: the nodes of the subtree of node_id (the node included).
     */
    public function countNodes( array $params )
    {
        if ( isset( $params['node_ids'] ) && !isset( $params['node_id'] ) )
            return count( array_unique( array_filter( array_map( 'intval', (array) $params['node_ids'] ) ) ) );
        $row = isset( $params['node_id'] ) ? eZContentObjectTreeNode::fetch( (int) $params['node_id'], false, false ) : null;
        if ( !is_array( $row ) )
            return 0;
        $rows = eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS c FROM ezcontentobject_tree WHERE '
                                              . expContentJobRemoveSubtree::pathCondition( array( $row['path_string'] ) ) );
        return $rows ? (int) $rows[0]['c'] : 0;
    }

    public function prepare( expContentJob $job )
    {
        $cp =& $job->checkpoint();
        if ( !$cp )
        {
            $list = $this->buildWorkList( $job );
            expContentJobStore::writeSide( $job->id(), '.nodes.json', array_values( $list ) );
            $cp = array( 'phase' => $this->hasMainStep() ? 'main' : 'nodes', 'cursor' => 0, 'count' => count( $list ),
                         'changed' => 0, 'skipped' => 0, 'warnings' => array(), 'warning_count' => 0 );
            $job->setProgress( array( 'total' => count( $list ), 'phase' => $cp['phase'] ) );
        }
    }

    public function runBatch( expContentJob $job, $batchSize )
    {
        $cp =& $job->checkpoint();
        if ( $cp['phase'] === 'main' )
        {
            $message = $this->mainStep( $job, $cp );
            $cp['phase'] = 'nodes';
            $job->setProgress( array( 'phase' => 'nodes' ) );
            return array( 'done' => 0, 'done_total' => $cp['cursor'], 'finished' => $cp['count'] === 0, 'message' => $message );
        }
        $list = expContentJobStore::readSide( $job->id(), '.nodes.json' );
        if ( !is_array( $list ) )
            throw new expContentJobException( 'the work list of the job is missing (' . $job->id() . '.nodes.json)' );
        $slice = array_slice( $list, $cp['cursor'], max( 1, (int) $batchSize ) );
        if ( $slice )
        {
            $db = eZDB::instance();
            $rows = $db->arrayQuery( 'SELECT node_id, contentobject_id, parent_node_id, path_string, is_hidden, is_invisible, main_node_id, depth '
                                     . 'FROM ezcontentobject_tree WHERE ' . $db->generateSQLINStatement( $slice, 'node_id', false, true, 'int' )
                                     . ' ORDER BY depth, node_id' );
            $this->processNodes( $job, $rows, $cp );
        }
        $cp['cursor'] += count( $slice );
        return array( 'done' => count( $slice ), 'done_total' => $cp['cursor'], 'finished' => $cp['cursor'] >= count( $list ),
                      'message' => self::tr( '%1 of %2 nodes, %3 changed, %4 skipped', array( $cp['cursor'], count( $list ), $cp['changed'], $cp['skipped'] ) ) );
    }

    public function afterBatch( expContentJob $job )
    {
        $cp = $job->checkpoint();
        $job->setResult( 'changed', $cp['changed'] );
        $job->setResult( 'skipped', $cp['skipped'] );
        $job->setResult( 'warning_count', $cp['warning_count'] );
        $job->setResult( 'warnings', $cp['warnings'] );
    }

    public function finish( expContentJob $job )
    {
        $this->afterBatch( $job );
        $cp = $job->checkpoint();
        $job->setProgress( array( 'phase' => 'done',
                                  'message' => self::tr( '%1 nodes, %2 changed, %3 skipped', array( $cp['count'], $cp['changed'], $cp['skipped'] ) ) ) );
        if ( $cp['warning_count'] )
            $job->appendLog( 'warnings: ' . implode( ' | ', array_slice( $cp['warnings'], 0, 20 ) ) );
    }

    // ---- helpers -------------------------------------------------------------------------------

    /**
     * The node ids of a subtree (the node included), by depth.
     *
     * @param int $nodeID
     * @return int[]
     */
    public static function subtreeNodeIDs( $nodeID )
    {
        $row = eZContentObjectTreeNode::fetch( (int) $nodeID, false, false );
        if ( !is_array( $row ) )
            return array();
        $ids = array();
        foreach ( eZDB::instance()->arrayQuery( 'SELECT node_id FROM ezcontentobject_tree WHERE '
                                                . expContentJobRemoveSubtree::pathCondition( array( $row['path_string'] ) )
                                                . ' ORDER BY depth ASC, node_id ASC' ) as $r )
            $ids[] = (int) $r['node_id'];
        return $ids;
    }

    /**
     * The node of a parameter, or a refusal.
     *
     * @param array $params
     * @param string $key
     * @return eZContentObjectTreeNode
     */
    protected function requireNode( array $params, $key )
    {
        $id = isset( $params[$key] ) ? (int) $params[$key] : 0;
        $node = $id > 0 ? eZContentObjectTreeNode::fetch( $id ) : null;
        if ( !$node instanceof eZContentObjectTreeNode )
            throw new expContentJobException( self::tr( 'The node %1 does not exist (any more).', array( $id ) ) );
        return $node;
    }

    /**
     * The selected node ids of a parameter (int, unique, at least one).
     *
     * @param array $params
     * @return int[]
     */
    protected function requireNodeIDs( array $params )
    {
        $ids = array();
        foreach ( isset( $params['node_ids'] ) ? (array) $params['node_ids'] : array() as $id )
            if ( is_numeric( $id ) && (int) $id > 0 )
                $ids[(int) $id] = (int) $id;
        if ( !$ids )
            throw new expContentJobException( self::tr( 'No node was selected.' ) );
        return array_values( $ids );
    }

    /** The unique object ids of rows. */
    protected static function objectIDs( array $rows )
    {
        $ids = array();
        foreach ( $rows as $row )
            $ids[(int) $row['contentobject_id']] = (int) $row['contentobject_id'];
        return array_values( $ids );
    }

    /** Clears the view caches of objects, as the kernel's per-object operations do. */
    protected static function clearViewCaches( array $objectIDs )
    {
        if ( $objectIDs )
            eZContentCacheManager::clearContentCacheIfNeeded( $objectIDs );
    }

    /** For search engines other than the built-in one: reindex the objects, as the kernel's operations do. */
    protected static function reindexIfNeeded( array $objectIDs )
    {
        if ( eZSearch::getEngine() instanceof eZSearchEngine )
            return;
        foreach ( $objectIDs as $objectID )
            eZContentOperationCollection::registerSearchObject( $objectID );
    }

    protected function warn( array &$cp, $text )
    {
        $cp['warning_count']++;
        if ( count( $cp['warnings'] ) < self::MAX_WARNINGS )
            $cp['warnings'][] = $text;
    }

    protected static function tr( $text, array $args = array() )
    {
        // settings handed in (tests): no INI, so no translation either
        if ( is_array( expContentJob::$settings ) )
        {
            foreach ( array_values( $args ) as $i => $arg )
                $text = str_replace( '%' . ( $i + 1 ), (string) $arg, $text );
            return $text;
        }
        return ezpI18n::tr( 'kernel/contentjob', $text, null, $args );
    }
}
