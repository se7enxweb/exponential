<?php
/**
 * File containing the expContentJobStateSubtree class.
 *
 * The content job 'state': sets an object state (of one state group) on every object of a subtree, per object
 * through the kernel's own operation, as content/action's state assignment does it (the 'content_updateobjectstate'
 * operation when workflows are attached, else eZContentOperationCollection::updateObjectState(): the state link,
 * the audit, the search engine, the content/state/assign event and the view cache of the object).
 *
 * The policy per object: an object for which the requesting user may not assign this state (it is not in the
 * object's allowed_assign_state_id_list, from state/assign) is skipped with a warning, where the kernel's
 * operation drops it silently. When the job is created the user must be allowed to assign the state to the top
 * node's object, or it is refused. Idempotent: an object in the state already is left alone.
 *
 * Parameters: node_id, state_id.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentJobStateSubtree extends expContentJobBatchType
{
    protected function validateParams( array $params, eZUser $user )
    {
        $node = $this->requireNode( $params, 'node_id' );
        $stateID = isset( $params['state_id'] ) ? (int) $params['state_id'] : 0;
        $state = $stateID > 0 ? eZContentObjectState::fetchById( $stateID ) : null;
        if ( !$state )
            throw new expContentJobException( self::tr( 'The object state %1 does not exist.', array( $stateID ) ) );
        $allowed = array_map( 'intval', (array) $node->object()->attribute( 'allowed_assign_state_id_list' ) );
        if ( !in_array( $stateID, $allowed, true ) )
            throw new expContentJobException( self::tr( 'You do not have permission to set the state "%1" on node %2.',
                                                        array( $state->attribute( 'identifier' ), $node->attribute( 'node_id' ) ) ) );
        $group = $state->attribute( 'group' );
        return array( 'node_id' => (int) $node->attribute( 'node_id' ), 'state_id' => $stateID,
                      'state_name' => ( $group ? $group->attribute( 'identifier' ) . '/' : '' ) . $state->attribute( 'identifier' ),
                      'path' => $node->attribute( 'path_string' ) );
    }

    public function locks( array $params )
    {
        return array( array( 'path' => $params['path'], 'mode' => expContentJobLock::SUBTREE ) );
    }

    public function describe( array $params )
    {
        return self::tr( 'Set the state "%1" on the subtree of node %2', array( $params['state_name'], $params['node_id'] ) );
    }

    protected function processNodes( expContentJob $job, array $rows, array &$cp )
    {
        $stateID = (int) $job->params()['state_id'];
        foreach ( self::objectIDs( $rows ) as $objectID )
        {
            $states = $this->objectStates( $objectID );
            if ( $states === null )
                continue;
            $decision = self::decide( $stateID, $states['current'], $states['allowed'] );
            if ( $decision === 'already' )
                continue;
            if ( $decision === 'denied' )
            {
                $cp['skipped']++;
                $this->warn( $cp, self::tr( 'Object (ID = %1) was not given the state: you do not have permission.', array( $objectID ) ) );
                continue;
            }
            $this->assignState( $objectID, $stateID );
            $cp['changed']++;
        }
    }

    /**
     * What to do with an object: 'already' (it has the state), 'denied' (the user may not assign it to this
     * object), 'assign'.
     *
     * @param int $stateID
     * @param int[] $current the object's state ids
     * @param int[] $allowed the state ids the user may assign to it
     * @return string
     */
    public static function decide( $stateID, array $current, array $allowed )
    {
        if ( in_array( (int) $stateID, array_map( 'intval', $current ), true ) )
            return 'already';
        if ( !in_array( (int) $stateID, array_map( 'intval', $allowed ), true ) )
            return 'denied';
        return 'assign';
    }

    /**
     * The object's current states and the states the requesting user may assign to it.
     *
     * @param int $objectID
     * @return array|null array( 'current' => int[], 'allowed' => int[] ), null when there is no such object
     */
    protected function objectStates( $objectID )
    {
        $object = eZContentObject::fetch( $objectID );
        if ( !$object )
            return null;
        return array( 'current' => (array) $object->attribute( 'state_id_array' ),
                      'allowed' => (array) $object->attribute( 'allowed_assign_state_id_list' ) );
    }

    /**
     * Assigns the state through the kernel's operation (the 'content_updateobjectstate' operation when workflows are
     * attached, else eZContentOperationCollection::updateObjectState()).
     *
     * @param int $objectID
     * @param int $stateID
     */
    protected function assignState( $objectID, $stateID )
    {
        if ( eZOperationHandler::operationIsAvailable( 'content_updateobjectstate' ) )
            eZOperationHandler::execute( 'content', 'updateobjectstate', array( 'object_id' => $objectID, 'state_id_list' => array( $stateID ) ), null, true );
        else
            eZContentOperationCollection::updateObjectState( $objectID, array( $stateID ) );
        eZContentObject::clearCache( array( $objectID ) );
    }
}
