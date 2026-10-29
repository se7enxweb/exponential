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
 * The optional parameter scope (eZSection::SCOPE_*, default SCOPE_SUBTREE) narrows the work list: SCOPE_OBJECT
 * changes the top node's object only, SCOPE_INHERITED leaves out every location below whose object is not in the
 * section the top node's object had when the job was created (old_section_id), with everything below it.
 *
 * Parameters: node_id, section_id, scope.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
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
        $scope = isset( $params['scope'] ) && $params['scope'] !== '' ? $params['scope'] : eZSection::SCOPE_SUBTREE;
        if ( !eZSection::isValidScope( $scope ) )
            throw new expContentJobException( self::tr( 'The scope "%1" is not known; use %2.',
                                                        array( is_scalar( $scope ) ? $scope : gettype( $scope ), implode( ', ', eZSection::scopes() ) ) ) );
        $validated = array( 'node_id' => (int) $node->attribute( 'node_id' ), 'section_id' => $sectionID,
                            'section_name' => $section->attribute( 'name' ), 'path' => $node->attribute( 'path_string' ),
                            'scope' => $scope );
        if ( $scope === eZSection::SCOPE_INHERITED )
            $validated['old_section_id'] = (int) $node->object()->attribute( 'section_id' );
        return $validated;
    }

    /** SCOPE_OBJECT: one node, whatever the size of its subtree. */
    public function countNodes( array $params )
    {
        if ( isset( $params['scope'] ) && $params['scope'] === eZSection::SCOPE_OBJECT )
            return 1;
        return parent::countNodes( $params );
    }

    protected function buildWorkList( expContentJob $job )
    {
        $p = $job->params();
        $scope = isset( $p['scope'] ) ? $p['scope'] : eZSection::SCOPE_SUBTREE;
        if ( $scope === eZSection::SCOPE_OBJECT )
            return array( (int) $p['node_id'] );
        if ( $scope !== eZSection::SCOPE_INHERITED )
            return parent::buildWorkList( $job );

        // found once, before anything changes: the branches in another section are left out of the work list
        $node = eZContentObjectTreeNode::fetch( (int) $p['node_id'] );
        if ( !$node )
            return array();
        $boundaries = eZContentObjectTreeNode::sectionBoundaryPaths( $node, (int) $p['old_section_id'] );
        $ids = array();
        foreach ( eZDB::instance()->arrayQuery( 'SELECT node_id, path_string FROM ezcontentobject_tree WHERE '
                                                . expContentJobRemoveSubtree::pathCondition( array( $node->attribute( 'path_string' ) ) )
                                                . ' ORDER BY depth ASC, node_id ASC' ) as $r )
        {
            if ( !eZContentObjectTreeNode::pathIsInAny( $r['path_string'], $boundaries ) )
                $ids[] = (int) $r['node_id'];
        }
        return $ids;
    }

    /** SCOPE_OBJECT changes one object: the node is locked, not its subtree. */
    public function locks( array $params )
    {
        $one = isset( $params['scope'] ) && $params['scope'] === eZSection::SCOPE_OBJECT;
        return array( array( 'path' => $params['path'], 'mode' => $one ? expContentJobLock::NODE : expContentJobLock::SUBTREE ) );
    }

    public function describe( array $params )
    {
        $scope = isset( $params['scope'] ) ? $params['scope'] : eZSection::SCOPE_SUBTREE;
        if ( $scope === eZSection::SCOPE_OBJECT )
            return self::tr( 'Assign the section "%1" to the object of node %2', array( $params['section_name'], $params['node_id'] ) );
        if ( $scope === eZSection::SCOPE_INHERITED )
            return self::tr( 'Assign the section "%1" to the subtree of node %2, except branches in another section',
                             array( $params['section_name'], $params['node_id'] ) );
        return self::tr( 'Assign the section "%1" to the subtree of node %2', array( $params['section_name'], $params['node_id'] ) );
    }

    public function prepare( expContentJob $job )
    {
        $first = !$job->checkpoint();
        parent::prepare( $job );
        if ( $first )
        {
            // Who assigns which section at which node (doc/eng-us/bc/6.0/audit.md, content.node.section), a child of the
            // job's run
            $p = $job->params();
            if ( class_exists( 'expAuditHook' ) )
                expAuditHook::emit( 'content.node.section', function () use ( $p, $job ) {
                    $node = eZContentObjectTreeNode::fetch( $p['node_id'] );
                    $object = $node ? $node->object() : null;
                    return array( 'object' => expAuditHook::node( $node ) ?: array( 'type' => 'node', 'id' => (int)$p['node_id'] ),
                                  'target' => expAuditHook::section( (int)$p['section_id'] ),
                                  'before' => array( 'section' => $object ? (int)$object->attribute( 'section_id' ) : null ),
                                  'after' => array( 'section' => (int)$p['section_id'], 'objects' => (int)$job->progress()['total'],
                                                    'scope' => isset( $p['scope'] ) ? $p['scope'] : eZSection::SCOPE_SUBTREE ) );
                } );
        }
    }

    protected function processNodes( expContentJob $job, array $rows, array &$cp )
    {
        $p = $job->params();
        $sectionID = (int) $p['section_id'];
        // SCOPE_INHERITED: an object that left the old section since the job was created keeps its section
        $onlySectionID = isset( $p['scope'] ) && $p['scope'] === eZSection::SCOPE_INHERITED ? (int) $p['old_section_id'] : null;
        $user = eZUser::currentUser();
        $db = eZDB::instance();
        $ids = self::objectIDs( $rows );
        if ( !$ids )
            return;
        $change = array();
        foreach ( $db->arrayQuery( 'SELECT id, section_id FROM ezcontentobject WHERE ' . $db->generateSQLINStatement( $ids, 'id', false, true, 'int' ) ) as $row )
        {
            $objectID = (int) $row['id'];
            if ( (int) $row['section_id'] === $sectionID
                 || ( $onlySectionID !== null && (int) $row['section_id'] !== $onlySectionID ) )
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
