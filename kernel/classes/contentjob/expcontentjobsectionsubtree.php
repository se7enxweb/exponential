<?php
/**
 * File containing the expContentJobSectionSubtree class.
 *
 * The content job 'section': assigns a section to every object of a subtree, with the semantics of
 * eZContentObjectTreeNode::assignSectionToSubTree() (every object with a location in the subtree; the search
 * index's section updated through eZSearch::updateObjectsSection(); the in-memory object cache cleared) as
 * section/assign uses it, in batches, plus:
 *
 *   - the policy per object: an object the requesting user may not give this section (canAssignSectionToObject())
 *     is skipped with a warning; section/assign checks the top node only, then assigns the whole subtree;
 *   - the view caches of the changed objects cleared per batch (section/assign clears all view caches at the end).
 *
 * When the job is created the user must be allowed to assign the section (canAssignSection()) and to give it to
 * the top node's object, or it is refused, as section/assign does. Idempotent: an object in the section already
 * is left alone.
 *
 * Parameters: node_id, section_id.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentJobSectionSubtree extends expContentJobBatchType
{
    protected function validateParams( array $params, eZUser $user )
    {
        $node = $this->requireNode( $params, 'node_id' );
        $sectionID = isset( $params['section_id'] ) ? (int) $params['section_id'] : 0;
        $section = $sectionID > 0 ? eZSection::fetch( $sectionID ) : null;
        if ( !$section )
            throw new expContentJobException( self::tr( 'The section %1 does not exist.', array( $sectionID ) ) );
        if ( !$user->canAssignSection( $sectionID ) || !$user->canAssignSectionToObject( $sectionID, $node->object() ) )
            throw new expContentJobException( self::tr( 'You do not have permission to assign the section "%1" to node %2.',
                                                        array( $section->attribute( 'name' ), $node->attribute( 'node_id' ) ) ) );
        return array( 'node_id' => (int) $node->attribute( 'node_id' ), 'section_id' => $sectionID,
                      'section_name' => $section->attribute( 'name' ), 'path' => $node->attribute( 'path_string' ) );
    }

    public function locks( array $params )
    {
        return array( array( 'path' => $params['path'], 'mode' => expContentJobLock::SUBTREE ) );
    }

    public function describe( array $params )
    {
        return self::tr( 'Assign the section "%1" to the subtree of node %2', array( $params['section_name'], $params['node_id'] ) );
    }

    public function prepare( expContentJob $job )
    {
        $first = !$job->checkpoint();
        parent::prepare( $job );
        if ( $first )
        {
            // Who assigns which section at which node (doc/bc/6.0/audit.md, content.node.section), a child of the
            // job's run
            $p = $job->params();
            if ( class_exists( 'expAuditHook' ) )
                expAuditHook::emit( 'content.node.section', function () use ( $p, $job ) {
                    $node = eZContentObjectTreeNode::fetch( $p['node_id'] );
                    $object = $node ? $node->object() : null;
                    return array( 'object' => expAuditHook::node( $node ) ?: array( 'type' => 'node', 'id' => (int)$p['node_id'] ),
                                  'target' => expAuditHook::section( (int)$p['section_id'] ),
                                  'before' => array( 'section' => $object ? (int)$object->attribute( 'section_id' ) : null ),
                                  'after' => array( 'section' => (int)$p['section_id'], 'objects' => (int)$job->progress()['total'] ) );
                } );
        }
    }

    protected function processNodes( expContentJob $job, array $rows, array &$cp )
    {
        $p = $job->params();
        $sectionID = (int) $p['section_id'];
        $user = eZUser::currentUser();
        $db = eZDB::instance();
        $ids = self::objectIDs( $rows );
        if ( !$ids )
            return;
        $change = array();
        foreach ( $db->arrayQuery( 'SELECT id, section_id FROM ezcontentobject WHERE ' . $db->generateSQLINStatement( $ids, 'id', false, true, 'int' ) ) as $row )
        {
            $objectID = (int) $row['id'];
            if ( (int) $row['section_id'] === $sectionID )
                continue;
            $object = eZContentObject::fetch( $objectID );
            if ( !$object || !$user->canAssignSectionToObject( $sectionID, $object ) )
            {
                $cp['skipped']++;
                $this->warn( $cp, self::tr( 'Object (ID = %1) was not given the section: you do not have permission.', array( $objectID ) ) );
                continue;
            }
            $change[] = $objectID;
        }
        if ( !$change )
            return;
        $db->query( "UPDATE ezcontentobject SET section_id='$sectionID' WHERE " . $db->generateSQLINStatement( $change, 'id', false, true, 'int' ) );
        eZSearch::updateObjectsSection( $change, $sectionID );
        eZContentObject::clearCache( $change );
        self::clearViewCaches( $change );
        $cp['changed'] += count( $change );
    }
}
