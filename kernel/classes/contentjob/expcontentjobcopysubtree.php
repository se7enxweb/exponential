<?php
/**
 * File containing the expContentJobCopySubtree class.
 *
 * The content job 'copy': copies a subtree under a new parent in batches, level by level from the top, with the
 * semantics of content/copysubtree and bin/php/ezsubtreecopy.php (copyPublishContentObject() and copySubtree()
 * there), only batched and resumable:
 *
 *   - each object is copied once (eZContentObject::copy(), all versions or the current one), its node
 *     assignments inside the copied subtree re-pointed at the new parents, the others dropped, the first one made
 *     main when the main one was outside, and published through the 'content_publish' operation; an object with
 *     several locations inside the subtree is copied when all its parents there have been copied, and gets all
 *     those locations (multi-location objects stay multi-location in the copy);
 *   - the new nodes get the priority and hidden/invisible state of their source; keep creator and keep time as
 *     the options say, for the current or all versions;
 *   - what the user may not read, or may not create at the new place, is not copied, with a warning, and neither
 *     is what is below it, as the view does;
 *   - at the end, in batches too: related-object links (ezcontentobject_link), node and object ids in XML text
 *     (links, embeds, objects), ezobjectrelationlist items and ezobjectrelation attributes that point into the
 *     copied subtree are re-pointed at the copies.
 *
 * Resumable and idempotent: the work list (the source node ids, by depth) is written once to <id>.nodes.json;
 * the old -> new node and object ids to <id>.map after each committed batch. Every copy gets a remote id made of
 * the job id and the source object id, written in the batch's transaction: a batch run again after kill -9
 * between its commit and its checkpoint finds the copies by that remote id and maps them instead of copying again.
 *
 * Parameters: source_node_id, destination_node_id (int), all_versions, keep_creator, keep_time (bool).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentJobCopySubtree implements expContentJobType
{
    const MAX_WARNINGS = 200;

    /** @var array src node id => new node id (the source root's parent => the destination included) */
    protected $nodeMap = array();
    /** @var array new node id => src node id */
    protected $nodeRev = array();
    /** @var array src object id => new object id */
    protected $objectMap = array();
    /** @var array src node id => true, the source subtree */
    protected $sourceSet = array();
    /** @var string[] map lines of the current batch, written after its commit */
    protected $pending = array();
    /** @var string|null the job id the maps were loaded for */
    protected $loadedFor = null;

    public function validate( array $params, eZUser $user )
    {
        $srcID = isset( $params['source_node_id'] ) ? (int) $params['source_node_id'] : 0;
        $dstID = isset( $params['destination_node_id'] ) ? (int) $params['destination_node_id'] : 0;
        $src = $srcID > 0 ? eZContentObjectTreeNode::fetch( $srcID ) : null;
        $dst = $dstID > 0 ? eZContentObjectTreeNode::fetch( $dstID ) : null;
        if ( !$src instanceof eZContentObjectTreeNode )
            throw new expContentJobException( self::tr( 'The node %1 to copy does not exist.', array( $srcID ) ) );
        if ( !$dst instanceof eZContentObjectTreeNode )
            throw new expContentJobException( self::tr( 'The destination node %1 does not exist.', array( $dstID ) ) );
        if ( $srcID === $dstID )
            throw new expContentJobException( self::tr( 'A subtree cannot be copied into itself.' ) );

        $previous = eZUser::currentUser();
        $switched = (int) $previous->attribute( 'contentobject_id' ) !== (int) $user->attribute( 'contentobject_id' );
        if ( $switched )
            eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
        try
        {
            if ( !$src->attribute( 'can_read' ) )
                throw new expContentJobException( self::tr( 'You do not have permission to read the node %1.', array( $srcID ) ) );
            $classID = $src->attribute( 'object' )->attribute( 'contentclass_id' );
            if ( $dst->checkAccess( 'create', $classID ) != 1 )
                throw new expContentJobException( self::tr( 'You do not have permission to create this kind of content under the node %1.', array( $dstID ) ) );
        }
        finally
        {
            if ( $switched )
                eZUser::setCurrentlyLoggedInUser( $previous, $previous->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
        }
        return array( 'source_node_id' => $srcID, 'destination_node_id' => $dstID,
                      'source_path' => $src->attribute( 'path_string' ), 'destination_path' => $dst->attribute( 'path_string' ),
                      'source_parent_node_id' => (int) $src->attribute( 'parent_node_id' ),
                      'all_versions' => !empty( $params['all_versions'] ),
                      'keep_creator' => !empty( $params['keep_creator'] ),
                      'keep_time' => !empty( $params['keep_time'] ) );
    }

    public function countNodes( array $params )
    {
        $path = isset( $params['source_path'] ) ? $params['source_path'] : null;
        if ( $path === null )
        {
            $row = eZContentObjectTreeNode::fetch( (int) $params['source_node_id'], false, false );
            if ( !is_array( $row ) )
                return 0;
            $path = $row['path_string'];
        }
        $rows = eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS c FROM ezcontentobject_tree WHERE '
                                              . expContentJobRemoveSubtree::pathCondition( array( $path ) ) );
        return $rows ? (int) $rows[0]['c'] : 0;
    }

    public function locks( array $params )
    {
        return array( array( 'path' => $params['source_path'], 'mode' => expContentJobLock::SUBTREE ),
                      array( 'path' => $params['destination_path'], 'mode' => expContentJobLock::NODE ) );
    }

    public function describe( array $params )
    {
        return self::tr( 'Copy the subtree of node %1 under node %2', array( $params['source_node_id'], $params['destination_node_id'] ) );
    }

    public function prepare( expContentJob $job )
    {
        $params = $job->params();
        $id = $job->id();
        $cp =& $job->checkpoint();
        if ( !$cp )
        {
            $src = eZContentObjectTreeNode::fetch( $params['source_node_id'] );
            if ( !$src )
                throw new expContentJobException( self::tr( 'The node %1 to copy does not exist any more.', array( $params['source_node_id'] ) ) );
            $nodes = array();
            foreach ( eZDB::instance()->arrayQuery( 'SELECT node_id FROM ezcontentobject_tree WHERE '
                                                    . expContentJobRemoveSubtree::pathCondition( array( $src->attribute( 'path_string' ) ) )
                                                    . ' ORDER BY depth ASC, node_id ASC' ) as $row )
                $nodes[] = (int) $row['node_id'];
            expContentJobStore::writeSide( $id, '.nodes.json', $nodes );
            $cp = array( 'phase' => 'copy', 'pass' => 0, 'cursor' => 0, 'pass_list' => null, 'deferred' => array(),
                         'pass_progress' => false, 'black_nodes' => array(), 'black_objects' => array(),
                         'warnings' => array(), 'warning_count' => 0, 'fix_cursor' => 0, 'nodes' => count( $nodes ) );
            $job->setProgress( array( 'total' => count( $nodes ), 'phase' => 'copy' ) );
        }
        if ( !eZContentObjectTreeNode::fetch( $params['destination_node_id'], false, false ) )
            throw new expContentJobException( self::tr( 'The destination node %1 does not exist any more.', array( $params['destination_node_id'] ) ) );
        $this->loadMaps( $job );
    }

    /**
     * The work list and the id maps, from the job's side files.
     */
    protected function loadMaps( expContentJob $job )
    {
        $params = $job->params();
        $this->nodeMap = array( $params['source_parent_node_id'] => $params['destination_node_id'] );
        $this->nodeRev = array( $params['destination_node_id'] => $params['source_parent_node_id'] );
        $this->objectMap = array();
        $this->pending = array();
        $nodes = expContentJobStore::readSide( $job->id(), '.nodes.json' );
        if ( !is_array( $nodes ) )
            throw new expContentJobException( 'the work list of the job is missing (' . $job->id() . '.nodes.json)' );
        $this->sourceSet = array_fill_keys( $nodes, true );
        foreach ( expContentJobStore::lines( $job->id(), '.map' ) as $line )
        {
            $parts = explode( ' ', trim( $line ) );
            if ( count( $parts ) !== 3 )
                continue;
            list( $kind, $old, $new ) = $parts;
            if ( $kind === 'n' )
            {
                $this->nodeMap[(int) $old] = (int) $new;
                $this->nodeRev[(int) $new] = (int) $old;
            }
            else if ( $kind === 'o' )
                $this->objectMap[(int) $old] = (int) $new;
        }
        $this->loadedFor = $job->id();
    }

    public function runBatch( expContentJob $job, $batchSize )
    {
        if ( $this->loadedFor !== $job->id() )
            $this->loadMaps( $job );
        $cp =& $job->checkpoint();
        $this->pending = array();
        if ( $cp['phase'] === 'copy' )
            return $this->copyBatch( $job, $cp, $batchSize );
        return $this->relationBatch( $job, $cp, $batchSize );
    }

    /**
     * One batch of the copy phase: the next $batchSize source nodes of the current pass.
     */
    protected function copyBatch( expContentJob $job, array &$cp, $batchSize )
    {
        $list = $cp['pass_list'] === null ? expContentJobStore::readSide( $job->id(), '.nodes.json' ) : $cp['pass_list'];
        $slice = array_slice( $list, $cp['cursor'], $batchSize );
        $done = 0;
        $copied = 0;
        foreach ( $slice as $srcNodeID )
        {
            $outcome = $this->copyNode( $job, $cp, (int) $srcNodeID );
            if ( $outcome === 'notready' )
                $cp['deferred'][] = (int) $srcNodeID;
            else
            {
                $done++;
                $cp['pass_progress'] = true;
                if ( $outcome === 'copied' )
                    $copied++;
            }
        }
        $cp['cursor'] += count( $slice );
        if ( $cp['cursor'] >= count( $list ) )
        {
            // the end of a pass: what waited for a parent gets another pass, as long as passes make progress
            if ( $cp['deferred'] && $cp['pass_progress'] && $cp['pass'] < $cp['nodes'] )
            {
                $cp['pass_list'] = $cp['deferred'];
                $cp['deferred'] = array();
                $cp['cursor'] = 0;
                $cp['pass']++;
                $cp['pass_progress'] = false;
            }
            else
            {
                foreach ( $cp['deferred'] as $nodeID )
                {
                    $cp['black_nodes'][$nodeID] = true;
                    $this->warn( $cp, self::tr( 'Node (ID = %1) was not copied: its parent node was not copied.', array( $nodeID ) ) );
                    $done++;
                }
                $cp['deferred'] = array();
                $cp['pass_list'] = null;
                $cp['phase'] = 'relations';
                $cp['fix_cursor'] = 0;
            }
        }
        $total = max( 1, $cp['nodes'] );
        $handled = $job->progress()['done'] + $done;
        $job->setProgress( array( 'phase' => $cp['phase'], 'percent' => (int) floor( 90 * min( $handled, $total ) / $total ) ) );
        return array( 'done' => $done, 'finished' => false,
                      'message' => self::tr( '%1 nodes copied', array( count( $this->nodeMap ) - 1 ) ) );
    }

    /**
     * Copies the object of one source node (with all its locations inside the subtree), as copyPublishContentObject().
     *
     * @return string copied, skipped, blacklisted or notready
     */
    protected function copyNode( expContentJob $job, array &$cp, $srcNodeID )
    {
        if ( isset( $this->nodeMap[$srcNodeID] ) )
            return 'skipped';
        if ( isset( $cp['black_nodes'][$srcNodeID] ) )
            return 'blacklisted';
        $params = $job->params();
        $srcNode = eZContentObjectTreeNode::fetch( $srcNodeID );
        if ( !$srcNode instanceof eZContentObjectTreeNode )
        {
            $cp['black_nodes'][$srcNodeID] = true;
            $this->warn( $cp, self::tr( 'Node (ID = %1) was not copied: it does not exist any more.', array( $srcNodeID ) ) );
            return 'blacklisted';
        }
        $sourceObject = $srcNode->object();
        $sourceObjectID = (int) $sourceObject->attribute( 'id' );
        // the object was copied with its other locations, but not to this one (its parent was not copied)
        if ( isset( $this->objectMap[$sourceObjectID] ) || isset( $cp['black_objects'][$sourceObjectID] ) )
            return 'blacklisted';

        $isRoot = $srcNodeID === (int) $params['source_node_id'];
        $srcNodeList = $sourceObject->attribute( 'assigned_nodes' );

        // a copy committed by a run that died before its checkpoint: map it instead of copying again
        $remoteID = self::remoteID( $job->id(), $sourceObjectID );
        $existing = eZContentObject::fetchByRemoteID( $remoteID );
        if ( $existing instanceof eZContentObject )
        {
            if ( (int) $existing->attribute( 'status' ) !== eZContentObject::STATUS_PUBLISHED )
                throw new expContentJobException( 'the copy of object ' . $sourceObjectID . ' (object ' . $existing->attribute( 'id' ) . ') exists but is not published' );
            $this->mapCopy( $sourceObjectID, $srcNodeList, $existing, false, null );
            return 'copied';
        }

        if ( !$sourceObject->attribute( 'can_read' ) )
        {
            $cp['black_objects'][$sourceObjectID] = true;
            $this->warn( $cp, self::tr( 'Object (ID = %1) was not copied: you do not have permission to read the object.', array( $sourceObjectID ) ) );
            foreach ( $srcNodeList as $node )
            {
                if ( $this->inSubtree( (int) $node->attribute( 'parent_node_id' ), $isRoot ) )
                {
                    $cp['black_nodes'][(int) $node->attribute( 'node_id' )] = true;
                    $this->warn( $cp, self::tr( 'Node (ID = %1) was not copied: you do not have permission to read object (ID = %2).',
                                                array( $node->attribute( 'node_id' ), $sourceObjectID ) ) );
                }
            }
            return 'blacklisted';
        }

        // all parents of the object inside the subtree must have been copied
        $isReadyToPublish = false;
        $classID = $sourceObject->attribute( 'contentclass_id' );
        foreach ( $srcNodeList as $node )
        {
            $nodeID = (int) $node->attribute( 'node_id' );
            if ( isset( $cp['black_nodes'][$nodeID] ) )
                continue;
            $parentID = (int) $node->attribute( 'parent_node_id' );
            if ( !$this->inSubtree( $parentID, $isRoot ) )
                continue;
            if ( isset( $cp['black_nodes'][$parentID] ) )
            {
                $cp['black_nodes'][$nodeID] = true;
                $this->warn( $cp, self::tr( 'Node (ID = %1) was not copied: parent node (ID = %2) was not copied.', array( $nodeID, $parentID ) ) );
                continue;
            }
            if ( !isset( $this->nodeMap[$parentID] ) )
                return 'notready';
            $newParent = eZContentObjectTreeNode::fetch( $this->nodeMap[$parentID] );
            if ( !$newParent instanceof eZContentObjectTreeNode )
                throw new expContentJobException( 'the copied parent node ' . $this->nodeMap[$parentID] . ' does not exist any more' );
            if ( $newParent->checkAccess( 'create', $classID ) != 1 )
            {
                $cp['black_nodes'][$nodeID] = true;
                $this->warn( $cp, self::tr( 'Node (ID = %1) was not copied: you do not have permission to create.', array( $nodeID ) ) );
                continue;
            }
            $isReadyToPublish = true;
        }
        if ( !$isReadyToPublish )
        {
            $cp['black_objects'][$sourceObjectID] = true;
            $this->warn( $cp, self::tr( 'Object (ID = %1) was not copied: none of its nodes could be copied.', array( $sourceObjectID ) ) );
            return 'blacklisted';
        }

        $db = eZDB::instance();
        $db->begin();
        $newObject = $sourceObject->copy( $params['all_versions'] );
        // the section is set again by updateSectionID() at publishing (0 = a new object)
        $newObject->setAttribute( 'section_id', 0 );
        $newObject->setAttribute( 'remote_id', $remoteID );
        $newObject->store();

        $curVersion = $newObject->attribute( 'current_version' );
        $curVersionObject = $newObject->attribute( 'current' );
        $assignmentsForRemoving = array();
        $foundMainAssignment = false;
        foreach ( $curVersionObject->attribute( 'node_assignments' ) as $assignment )
        {
            $parentNodeID = (int) $assignment->attribute( 'parent_node' );
            if ( !$this->inSubtree( $parentNodeID, $isRoot ) || isset( $cp['black_nodes'][$parentNodeID] ) )
            {
                $assignmentsForRemoving[] = $assignment->attribute( 'id' );
                continue;
            }
            if ( !isset( $this->nodeMap[$parentNodeID] ) )
                throw new expContentJobException( 'cannot publish the copy of object ' . $sourceObjectID . ': its parent ' . $parentNodeID . ' is not copied yet' );
            if ( $assignment->attribute( 'is_main' ) )
                $foundMainAssignment = true;
            $assignment->setAttribute( 'parent_node', $this->nodeMap[$parentNodeID] );
            $assignment->store();
        }
        eZNodeAssignment::purgeByID( $assignmentsForRemoving );
        if ( !$foundMainAssignment )
        {
            $assignments = $curVersionObject->attribute( 'node_assignments' );
            if ( isset( $assignments[0] ) )
            {
                $assignments[0]->setAttribute( 'is_main', 1 );
                $assignments[0]->store();
            }
        }
        $db->commit();

        eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => $newObject->attribute( 'id' ), 'version' => $curVersion ) );
        $newObjectID = (int) $newObject->attribute( 'id' );
        eZContentObject::clearCache( array( $newObjectID ) );
        $newObject = eZContentObject::fetch( $newObjectID );
        $newNodeList = $newObject ? $newObject->attribute( 'assigned_nodes' ) : array();
        if ( !$newNodeList )
        {
            if ( $newObject )
                $newObject->purge();
            $cp['black_objects'][$sourceObjectID] = true;
            $this->warn( $cp, self::tr( 'Cannot publish object (Name: %1, ID: %2).', array( $srcNode->getName(), $sourceObjectID ) ) );
            return 'blacklisted';
        }

        $this->mapCopy( $sourceObjectID, $srcNodeList, $newObject, true, $cp );

        // keep creator and time, as the view does
        $isModified = false;
        if ( $params['keep_time'] )
        {
            $newObject->setAttribute( 'published', $sourceObject->attribute( 'published' ) );
            $newObject->setAttribute( 'modified', $sourceObject->attribute( 'modified' ) );
            $isModified = true;
        }
        if ( $params['keep_creator'] )
        {
            $newObject->setAttribute( 'owner_id', $sourceObject->attribute( 'owner_id' ) );
            $isModified = true;
        }
        if ( $isModified )
            $newObject->store();
        if ( $params['keep_time'] || $params['keep_creator'] )
        {
            $pairs = array();
            if ( $params['all_versions'] )
            {
                foreach ( $sourceObject->versions() as $srcVersion )
                    $pairs[] = array( $srcVersion, $newObject->version( $srcVersion->attribute( 'version' ) ) );
            }
            else
                $pairs[] = array( $sourceObject->attribute( 'current' ), $newObject->attribute( 'current' ) );
            foreach ( $pairs as $pair )
            {
                list( $srcVersion, $newVersion ) = $pair;
                if ( !is_object( $newVersion ) )
                    continue;
                if ( $params['keep_time'] )
                {
                    $newVersion->setAttribute( 'created', $srcVersion->attribute( 'created' ) );
                    $newVersion->setAttribute( 'modified', $srcVersion->attribute( 'modified' ) );
                }
                if ( $params['keep_creator'] )
                    $newVersion->setAttribute( 'creator_id', $srcVersion->attribute( 'creator_id' ) );
                $newVersion->store();
            }
        }
        return 'copied';
    }

    /**
     * Records a copy in the maps: the object, and each new node against the source location under the matching
     * parent; with $store the new nodes get the source's priority and visibility (a fresh copy).
     */
    protected function mapCopy( $sourceObjectID, array $srcNodeList, eZContentObject $newObject, $store, $cp )
    {
        $newObjectID = (int) $newObject->attribute( 'id' );
        $this->objectMap[$sourceObjectID] = $newObjectID;
        $this->pending[] = "o $sourceObjectID $newObjectID";
        foreach ( $newObject->attribute( 'assigned_nodes' ) as $newNode )
        {
            $newParentNodeID = (int) $newNode->attribute( 'parent_node_id' );
            if ( !isset( $this->nodeRev[$newParentNodeID] ) )
            {
                eZDebug::writeError( "Cannot find new parent node ID $newParentNodeID in the copied nodes", __METHOD__ );
                continue;
            }
            $srcParentNodeID = $this->nodeRev[$newParentNodeID];
            foreach ( $srcNodeList as $srcNode )
            {
                if ( (int) $srcNode->attribute( 'parent_node_id' ) !== $srcParentNodeID )
                    continue;
                if ( $store )
                {
                    $newParentNode = $newNode->fetchParent();
                    $newNode->setAttribute( 'priority', $srcNode->attribute( 'priority' ) );
                    $newNode->setAttribute( 'is_hidden', $srcNode->attribute( 'is_hidden' ) );
                    if ( $newParentNode && ( $newParentNode->attribute( 'is_invisible' ) || $newParentNode->attribute( 'is_hidden' ) ) )
                        $newNode->setAttribute( 'is_invisible', 1 );
                    else
                        $newNode->setAttribute( 'is_invisible', $srcNode->attribute( 'is_invisible' ) );
                    $newNode->store();
                }
                $srcID = (int) $srcNode->attribute( 'node_id' );
                $newID = (int) $newNode->attribute( 'node_id' );
                $this->nodeMap[$srcID] = $newID;
                $this->nodeRev[$newID] = $srcID;
                $this->pending[] = "n $srcID $newID";
                break;
            }
        }
    }

    /**
     * Whether a parent node counts as inside the copied subtree: for the root, its own parent (and what has
     * been copied), for the rest, the source subtree.
     */
    protected function inSubtree( $parentNodeID, $isRoot )
    {
        if ( $isRoot )
            return isset( $this->nodeMap[$parentNodeID] );
        return isset( $this->sourceSet[$parentNodeID] );
    }

    /**
     * One batch of the relation phase: the next $batchSize copied objects get their links, XML and relation
     * attributes re-pointed into the copy (copySubtree() steps 3, 5 and 6, and ezobjectrelation).
     */
    protected function relationBatch( expContentJob $job, array &$cp, $batchSize )
    {
        $objects = array_values( $this->objectMap );
        sort( $objects );
        $slice = array_slice( $objects, $cp['fix_cursor'], $batchSize );
        if ( $slice )
        {
            $this->fixLinks( $slice );
            $this->fixXMLText( $slice );
            $this->fixRelationLists( $slice );
            $this->fixRelations( $slice );
        }
        $cp['fix_cursor'] += count( $slice );
        $finished = $cp['fix_cursor'] >= count( $objects );
        $job->setProgress( array( 'phase' => 'relations',
                                  'percent' => 90 + (int) floor( 10 * min( $cp['fix_cursor'], max( 1, count( $objects ) ) ) / max( 1, count( $objects ) ) ) ) );
        return array( 'done' => 0, 'finished' => $finished,
                      'message' => self::tr( 'Relations of %1 of %2 copied objects updated', array( $cp['fix_cursor'], count( $objects ) ) ) );
    }

    protected function fixLinks( array $newObjectIDs )
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( 'SELECT id, to_contentobject_id FROM ezcontentobject_link WHERE '
                                 . $db->generateSQLINStatement( $newObjectIDs, 'from_contentobject_id', false, false, 'int' ) );
        foreach ( $rows as $row )
        {
            $to = (int) $row['to_contentobject_id'];
            if ( isset( $this->objectMap[$to] ) )
                $db->query( 'UPDATE ezcontentobject_link SET to_contentobject_id=' . (int) $this->objectMap[$to] . ' WHERE id=' . (int) $row['id'] );
        }
    }

    protected function fixXMLText( array $newObjectIDs )
    {
        foreach ( $newObjectIDs as $contentObjectID )
        {
            $attributeList = eZPersistentObject::fetchObjectList( eZContentObjectAttribute::definition(), null,
                                                                  array( 'contentobject_id' => $contentObjectID, 'data_type_string' => 'ezxmltext' ) );
            foreach ( (array) $attributeList as $xmlAttribute )
            {
                $xmlText = (string) $xmlAttribute->attribute( 'data_text' );
                $fixed = $this->fixXMLString( $xmlText );
                if ( $fixed !== $xmlText )
                {
                    $xmlAttribute->setAttribute( 'data_text', $fixed );
                    $xmlAttribute->store();
                }
            }
        }
    }

    /**
     * The XML text with node_id and object_id of links and embeds, and id of objects, inside the copied subtree
     * changed to the copies (the scan of copySubtree() step 5, literal blocks skipped).
     *
     * @param string $xmlText
     * @return string
     */
    public function fixXMLString( $xmlText )
    {
        $xmlTextLen = strlen( $xmlText );
        $curPos = 0;
        while ( $curPos < $xmlTextLen )
        {
            $literalTagBeginPos = strpos( $xmlText, '<literal', $curPos );
            if ( $literalTagBeginPos )
            {
                $literalTagEndPos = strpos( $xmlText, '</literal>', $literalTagBeginPos );
                if ( $literalTagEndPos === false )
                    break;
                $curPos = $literalTagEndPos + 9;
            }
            if ( ( $tagBeginPos = strpos( $xmlText, '<link', $curPos ) ) !== false or
                 ( $tagBeginPos = strpos( $xmlText, '<a', $curPos ) ) !== false or
                 ( $tagBeginPos = strpos( $xmlText, '<embed', $curPos ) ) !== false )
            {
                $tagEndPos = strpos( $xmlText, '>', $tagBeginPos + 1 );
                if ( $tagEndPos === false )
                    break;
                $tagText = substr( $xmlText, $tagBeginPos, $tagEndPos - $tagBeginPos );
                if ( ( $pos = strpos( $tagText, ' node_id="' ) ) !== false )
                    $newTag = $this->replaceID( $tagText, $pos + 10, $this->nodeMap );
                else if ( ( $pos = strpos( $tagText, ' object_id="' ) ) !== false )
                    $newTag = $this->replaceID( $tagText, $pos + 12, $this->objectMap );
                else
                    $newTag = $tagText;
                if ( $newTag !== $tagText )
                {
                    $xmlText = substr_replace( $xmlText, $newTag, $tagBeginPos, $tagEndPos - $tagBeginPos );
                    $xmlTextLen = strlen( $xmlText );
                    $tagEndPos += strlen( $newTag ) - strlen( $tagText );
                }
                $curPos = $tagEndPos;
            }
            else if ( ( $tagBeginPos = strpos( $xmlText, '<object', $curPos ) ) !== false )
            {
                $tagEndPos = strpos( $xmlText, '>', $tagBeginPos + 1 );
                if ( !$tagEndPos )
                    break;
                $tagText = substr( $xmlText, $tagBeginPos, $tagEndPos - $tagBeginPos );
                $newTag = ( $pos = strpos( $tagText, ' id="' ) ) !== false ? $this->replaceID( $tagText, $pos + 5, $this->objectMap ) : $tagText;
                if ( $newTag !== $tagText )
                {
                    $xmlText = substr_replace( $xmlText, $newTag, $tagBeginPos, $tagEndPos - $tagBeginPos );
                    $xmlTextLen = strlen( $xmlText );
                    $tagEndPos += strlen( $newTag ) - strlen( $tagText );
                }
                $curPos = $tagEndPos;
            }
            else
                break;
        }
        return $xmlText;
    }

    /** The tag with the number at $idPos replaced through $map (unchanged when it is not in the map). */
    protected function replaceID( $tagText, $idPos, array $map )
    {
        $quoteEndPos = strpos( $tagText, '"', $idPos );
        if ( $quoteEndPos === false )
            return $tagText;
        $id = (int) substr( $tagText, $idPos, $quoteEndPos - $idPos );
        if ( !isset( $map[$id] ) )
            return $tagText;
        return substr_replace( $tagText, (string) $map[$id], $idPos, $quoteEndPos - $idPos );
    }

    protected function fixRelationLists( array $newObjectIDs )
    {
        $db = eZDB::instance();
        foreach ( $newObjectIDs as $contentObjectID )
        {
            $attributeList = eZPersistentObject::fetchObjectList( eZContentObjectAttribute::definition(), null,
                                                                  array( 'contentobject_id' => $contentObjectID, 'data_type_string' => 'ezobjectrelationlist' ) );
            foreach ( (array) $attributeList as $attribute )
            {
                $text = (string) $attribute->attribute( 'data_text' );
                if ( $text === '' )
                    continue;
                $dom = eZObjectRelationListType::parseXML( $text );
                $modified = false;
                foreach ( $dom->getElementsByTagName( 'relation-item' ) as $item )
                {
                    $oid = (int) $item->getAttribute( 'contentobject-id' );
                    if ( isset( $this->objectMap[$oid] ) )
                    {
                        $item->setAttribute( 'contentobject-id', $this->objectMap[$oid] );
                        $modified = true;
                    }
                    $nid = (int) $item->getAttribute( 'node-id' );
                    if ( $nid && isset( $this->nodeMap[$nid] ) )
                    {
                        $newNodeID = $this->nodeMap[$nid];
                        $item->setAttribute( 'node-id', $newNodeID );
                        $newNode = eZContentObjectTreeNode::fetch( $newNodeID, false, false );
                        if ( is_array( $newNode ) )
                            $item->setAttribute( 'parent-node-id', $newNode['parent_node_id'] );
                        $modified = true;
                    }
                }
                if ( $modified )
                {
                    $db->query( "UPDATE ezcontentobject_attribute SET data_text='" . $db->escapeString( eZObjectRelationListType::domString( $dom ) )
                                . "' WHERE id=" . (int) $attribute->attribute( 'id' ) . ' AND version=' . (int) $attribute->attribute( 'version' ) );
                }
            }
        }
    }

    /** ezobjectrelation: the related object id is in data_int. */
    protected function fixRelations( array $newObjectIDs )
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( "SELECT id, version, data_int FROM ezcontentobject_attribute WHERE data_type_string='ezobjectrelation' AND "
                                 . $db->generateSQLINStatement( $newObjectIDs, 'contentobject_id', false, false, 'int' ) );
        foreach ( $rows as $row )
        {
            $oid = (int) $row['data_int'];
            if ( $oid && isset( $this->objectMap[$oid] ) )
                $db->query( 'UPDATE ezcontentobject_attribute SET data_int=' . (int) $this->objectMap[$oid]
                            . ' WHERE id=' . (int) $row['id'] . ' AND version=' . (int) $row['version'] );
        }
    }

    public function afterBatch( expContentJob $job )
    {
        if ( $this->pending )
        {
            expContentJobStore::append( $job->id(), '.map', $this->pending );
            $this->pending = array();
        }
        $params = $job->params();
        $rootSrc = (int) $params['source_node_id'];
        if ( isset( $this->nodeMap[$rootSrc] ) && empty( $job->result()['new_root_node_id'] ) )
        {
            $newRoot = $this->nodeMap[$rootSrc];
            $job->setResult( 'new_root_node_id', $newRoot );
            $row = eZContentObjectTreeNode::fetch( $newRoot, false, false );
            if ( is_array( $row ) )
                expContentJobLock::add( $job->id(), array( 'path' => $row['path_string'], 'mode' => expContentJobLock::SUBTREE ) );
        }
        $cp = $job->checkpoint();
        $job->setResult( 'copied_nodes', count( $this->nodeMap ) - 1 );
        $job->setResult( 'copied_objects', count( $this->objectMap ) );
        $job->setResult( 'warning_count', $cp['warning_count'] );
        $job->setResult( 'warnings', $cp['warnings'] );
    }

    public function finish( expContentJob $job )
    {
        $this->afterBatch( $job );
        $r = $job->result();
        $job->setProgress( array( 'phase' => 'done', 'percent' => 100,
                                  'message' => self::tr( 'Copied %1 nodes and %2 objects', array( $r['copied_nodes'], $r['copied_objects'] ) )
                                               . ( $r['warning_count'] ? ', ' . self::tr( '%1 warnings', array( $r['warning_count'] ) ) : '' ) ) );
        if ( $r['warning_count'] )
            $job->appendLog( 'warnings: ' . implode( ' | ', array_slice( $r['warnings'], 0, 20 ) ) );
    }

    /**
     * The remote id of the copy of an object made by a job: unique per job and source object, so a batch run
     * again finds the copy it committed before.
     *
     * @param string $jobID
     * @param int $sourceObjectID
     * @return string
     */
    public static function remoteID( $jobID, $sourceObjectID )
    {
        return md5( 'expcontentjob:' . $jobID . ':' . (int) $sourceObjectID );
    }

    protected function warn( array &$cp, $text )
    {
        $cp['warning_count']++;
        if ( count( $cp['warnings'] ) < self::MAX_WARNINGS )
            $cp['warnings'][] = $text;
    }

    /** The id maps (tests and the reports). */
    public function maps()
    {
        return array( 'nodes' => $this->nodeMap, 'objects' => $this->objectMap );
    }

    protected static function tr( $text, array $args = array() )
    {
        return ezpI18n::tr( 'kernel/contentjob', $text, null, $args );
    }
}
