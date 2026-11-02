<?php
/**
 * File containing the expModerationAction class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The actions of Dashboard > Recent content on a selection of objects (doc/eng-us/features/6.0/moderation.md). Each runs
 * the kernel's own operation per object, as the current user, after that user's own policy for that object:
 *
 *   release  release the review hold (state/assign of not_held) and reveal what the auto-hold hid (content/hide)
 *   hold     place the review hold with a reason (state/assign of held)
 *   hide     hide the object's locations (content/hide, the content/hide operation's body)
 *   reveal   reveal them (content/hide)
 *   trash    move the object to the trash (content/remove; its locations must have no sub items)
 *   delete   delete it for good (content/remove; a draft that was never published is purged)
 *   section  assign a section (section/assign, the object's allowed sections)
 *   state    assign an object state (state/assign, the object's allowed states)
 *
 * Each object ends done, refused (the policy, or the object is not in a state the action applies to) or failed (an
 * error), with a sentence why. The whole action is recorded in the audit as content.moderation.action with the counts
 * and the ids; each object's change is recorded by the kernel's operation itself (content.object.state,
 * content.node.hide, content.object.remove ...).
 *
 * A selection over [ModerationSettings] JobThreshold runs as the content job 'moderation' (expContentJobModeration),
 * in batches, with the same per-object code.
 *
 * definitions(), validate() and needsReauth() are pure (expModerationActionTest).
 *
 * @since 6.0.15
 * @package kernel
 */
class expModerationAction
{
    const DONE = 'done';
    const REFUSED = 'refused';
    const FAILED = 'failed';

    /** The most objects one action takes (the largest page) */
    const MAX_OBJECTS = 1000;

    /**
     * The actions: what each needs from the form, whether it destroys (confirmation and "Verify it's you"), the
     * kernel operation it runs and the policy that decides.
     *
     * @return array name => array( reason (bool), section (bool), state (bool), destructive (bool), policy, operation )
     */
    public static function definitions()
    {
        return array(
            'release' => array( 'reason' => false, 'section' => false, 'state' => false, 'destructive' => false,
                                'policy' => 'state/assign (not_held); content/hide to reveal', 'operation' => 'updateobjectstate, content/hide' ),
            'hold'    => array( 'reason' => true, 'section' => false, 'state' => false, 'destructive' => false,
                                'policy' => 'state/assign (held)', 'operation' => 'updateobjectstate' ),
            'hide'    => array( 'reason' => false, 'section' => false, 'state' => false, 'destructive' => false,
                                'policy' => 'content/hide', 'operation' => 'content/hide' ),
            'reveal'  => array( 'reason' => false, 'section' => false, 'state' => false, 'destructive' => false,
                                'policy' => 'content/hide', 'operation' => 'content/hide' ),
            'trash'   => array( 'reason' => false, 'section' => false, 'state' => false, 'destructive' => true,
                                'policy' => 'content/remove', 'operation' => 'content/delete (to the trash)' ),
            'delete'  => array( 'reason' => false, 'section' => false, 'state' => false, 'destructive' => true,
                                'policy' => 'content/remove', 'operation' => 'content/delete, purge' ),
            'section' => array( 'reason' => false, 'section' => true, 'state' => false, 'destructive' => false,
                                'policy' => 'section/assign', 'operation' => 'updatesection' ),
            'state'   => array( 'reason' => false, 'section' => false, 'state' => true, 'destructive' => false,
                                'policy' => 'state/assign', 'operation' => 'updateobjectstate' ),
        );
    }

    /**
     * Pure: the action's form checked.
     *
     * @param array $in action, ids (object ids), reason, section_id, state_id
     * @return array action, ids, reason, section_id, state_id, errors (codes: action, ids, too_many, reason, section, state)
     */
    public static function validate( array $in )
    {
        $defs = self::definitions();
        $action = isset( $in['action'] ) && is_string( $in['action'] ) && isset( $defs[$in['action']] ) ? $in['action'] : '';
        $ids = array();
        foreach ( isset( $in['ids'] ) ? (array)$in['ids'] : array() as $id )
            if ( is_scalar( $id ) && ctype_digit( (string)$id ) && (int)$id > 0 )
                $ids[(int)$id] = (int)$id;
        $out = array( 'action' => $action, 'ids' => array_values( $ids ),
                      'reason' => class_exists( 'expReviewHoldAuto' ) ? expReviewHoldAuto::reason( isset( $in['reason'] ) ? $in['reason'] : '' ) : trim( (string)( isset( $in['reason'] ) ? $in['reason'] : '' ) ),
                      'section_id' => isset( $in['section_id'] ) && ctype_digit( (string)$in['section_id'] ) ? (int)$in['section_id'] : 0,
                      'state_id' => isset( $in['state_id'] ) && ctype_digit( (string)$in['state_id'] ) ? (int)$in['state_id'] : 0,
                      'errors' => array() );
        if ( $action === '' )
            $out['errors'][] = 'action';
        if ( !$out['ids'] )
            $out['errors'][] = 'ids';
        if ( count( $out['ids'] ) > self::MAX_OBJECTS )
            $out['errors'][] = 'too_many';
        if ( $action !== '' )
        {
            if ( $defs[$action]['reason'] && $out['reason'] === '' )
                $out['errors'][] = 'reason';
            if ( $defs[$action]['section'] && $out['section_id'] <= 0 )
                $out['errors'][] = 'section';
            if ( $defs[$action]['state'] && $out['state_id'] <= 0 )
                $out['errors'][] = 'state';
        }
        return $out;
    }

    /** Pure: whether the action destroys (asks for a confirmation and "Verify it's you") */
    public static function isDestructive( $action )
    {
        $defs = self::definitions();
        return isset( $defs[$action] ) && $defs[$action]['destructive'];
    }

    /** Pure: whether a selection runs in the background */
    public static function runsAsJob( $count, $threshold )
    {
        return (int)$threshold > 0 && (int)$count > (int)$threshold;
    }

    /**
     * Pure: the counts of a report.
     *
     * @param array $report list of array( id, name, status, message )
     * @return array done, refused, failed, total
     */
    public static function counts( array $report )
    {
        $c = array( self::DONE => 0, self::REFUSED => 0, self::FAILED => 0 );
        foreach ( $report as $entry )
            if ( isset( $c[$entry['status']] ) )
                $c[$entry['status']]++;
        return $c + array( 'total' => count( $report ) );
    }

    /**
     * Runs an action on objects as the current user.
     *
     * @param array $form validate() without errors
     * @param int[] $ids the objects of this call (a batch of a job, or all)
     * @return array the report: list of array( id, name, status, message )
     */
    public static function run( array $form, array $ids )
    {
        $report = array();
        foreach ( $ids as $id )
        {
            $id = (int)$id;
            eZContentObject::clearCache( array( $id ) );
            $object = eZContentObject::fetch( $id );
            if ( !$object instanceof eZContentObject )
            {
                $report[] = array( 'id' => $id, 'name' => '', 'status' => self::FAILED, 'message' => 'It does not exist any more.' );
                continue;
            }
            $name = (string)$object->attribute( 'name' );
            $db = eZDB::instance();
            try
            {
                $result = self::one( $form, $object );
            }
            catch ( Throwable $e )
            {
                // a transaction an operation left open would hold every row after it
                while ( $db->transactionCounter() > 0 && method_exists( $db, 'rollback' ) )
                    $db->rollback();
                eZDebug::writeError( "Moderation {$form['action']} of object $id: " . $e->getMessage(), __METHOD__ );
                $result = array( self::FAILED, $e->getMessage() );
            }
            $report[] = array( 'id' => $id, 'name' => $name, 'status' => $result[0], 'message' => $result[1] );
        }
        return $report;
    }

    /**
     * The action on one object.
     *
     * @return array( status, message )
     */
    protected static function one( array $form, eZContentObject $object )
    {
        $id = (int)$object->attribute( 'id' );
        $nodes = array_values( array_filter( (array)$object->assignedNodes(), function ( $n ) { return $n instanceof eZContentObjectTreeNode; } ) );
        switch ( $form['action'] )
        {
            case 'release':
                $r = expReviewHoldAuto::release( $object, $form['reason'] );
                return array( $r['status'], $r['message'] );

            case 'hold':
                $r = expReviewHoldAuto::place( $object, $form['reason'] );
                return array( $r['status'], $r['message'] );

            case 'hide':
            case 'reveal':
                $hide = $form['action'] === 'hide';
                if ( !$nodes )
                    return array( self::REFUSED, 'It has no location (not published).' );
                $changed = 0;
                foreach ( $nodes as $node )
                {
                    if ( (bool)$node->attribute( 'is_hidden' ) === $hide )
                        continue;
                    if ( !$node->canHide() )
                        return array( self::REFUSED, $hide ? 'You may not hide it (content/hide).' : 'You may not reveal it (content/hide).' );
                    eZContentOperationCollection::changeHideStatus( (int)$node->attribute( 'node_id' ) );
                    $changed++;
                }
                if ( !$changed )
                    return array( self::REFUSED, $hide ? 'It is hidden already.' : 'It is not hidden.' );
                eZContentCacheManager::clearContentCacheIfNeeded( $id );
                return array( self::DONE, $hide ? 'Hidden.' : 'Revealed.' );

            case 'trash':
            case 'delete':
                $trash = $form['action'] === 'trash';
                if ( (int)$object->attribute( 'status' ) === eZContentObject::STATUS_ARCHIVED )
                    return array( self::REFUSED, 'It is in the trash: empty or restore it there.' );
                if ( !$nodes )
                {
                    if ( $trash )
                        return array( self::REFUSED, 'A draft that was never published cannot go to the trash: delete it.' );
                    if ( !$object->canRemove() && (int)$object->attribute( 'owner_id' ) !== (int)eZUser::currentUserID() )
                        return array( self::REFUSED, 'You may not delete it (content/remove).' );
                    $object->purge();
                    return array( self::DONE, 'Deleted.' );
                }
                $nodeIDs = array();
                foreach ( $nodes as $node )
                {
                    if ( !$node->canRemove() )
                        return array( self::REFUSED, 'You may not remove it (content/remove).' );
                    if ( (int)$node->childrenCount( false ) > 0 )
                        return array( self::REFUSED, 'It has sub items: remove it in the content structure, where they are listed.' );
                    $nodeIDs[] = (int)$node->attribute( 'node_id' );
                }
                if ( eZOperationHandler::operationIsAvailable( 'content_delete' ) )
                    eZOperationHandler::execute( 'content', 'delete', array( 'node_id_list' => $nodeIDs, 'move_to_trash' => $trash ? 1 : 0 ), null, true );
                else
                    eZContentOperationCollection::deleteObject( $nodeIDs, $trash );
                eZContentObject::clearCache( array( $id ) );
                $after = eZContentObject::fetch( $id );
                if ( $trash ? ( $after instanceof eZContentObject && (int)$after->attribute( 'status' ) !== eZContentObject::STATUS_ARCHIVED )
                            : $after instanceof eZContentObject )
                    return array( self::FAILED, 'It was not removed (a workflow may hold the removal).' );
                return array( self::DONE, $trash ? 'Moved to the trash.' : 'Deleted.' );

            case 'section':
                $section = eZSection::fetch( (int)$form['section_id'] );
                if ( !$section instanceof eZSection )
                    return array( self::FAILED, 'The section does not exist.' );
                if ( (int)$object->attribute( 'section_id' ) === (int)$form['section_id'] )
                    return array( self::REFUSED, 'It is in this section already.' );
                $allowed = eZUser::currentUser()->canAssignToObjectSectionList( $object );
                if ( !in_array( '*', (array)$allowed, true ) && !in_array( (int)$form['section_id'], array_map( 'intval', (array)$allowed ), true ) )
                    return array( self::REFUSED, 'You may not assign this section (section/assign).' );
                $main = (int)$object->attribute( 'main_node_id' );
                if ( $main > 0 )
                    eZContentOperationCollection::updateSection( $main, (int)$form['section_id'], true, eZSection::SCOPE_OBJECT );
                else
                {
                    $object->setAttribute( 'section_id', (int)$form['section_id'] );
                    $object->store();
                }
                eZContentObject::clearCache( array( $id ) );
                $after = eZContentObject::fetch( $id );
                if ( !$after instanceof eZContentObject || (int)$after->attribute( 'section_id' ) !== (int)$form['section_id'] )
                    return array( self::FAILED, 'The section was not assigned.' );
                eZContentCacheManager::clearContentCacheIfNeeded( $id );
                return array( self::DONE, 'Section assigned.' );

            case 'state':
                $state = eZContentObjectState::fetchById( (int)$form['state_id'] );
                if ( !$state instanceof eZContentObjectState )
                    return array( self::FAILED, 'The state does not exist.' );
                if ( in_array( (int)$form['state_id'], array_map( 'intval', (array)$object->attribute( 'state_id_array' ) ), true ) )
                    return array( self::REFUSED, 'It is in this state already.' );
                if ( !in_array( (int)$form['state_id'], array_map( 'intval', (array)$object->attribute( 'allowed_assign_state_id_list' ) ), true ) )
                    return array( self::REFUSED, 'You may not assign this state (state/assign).' );
                eZOperationHandler::execute( 'content', 'updateobjectstate', array( 'object_id' => $id, 'state_id_list' => array( (int)$form['state_id'] ) ) );
                eZContentObject::clearCache( array( $id ) );
                $after = eZContentObject::fetch( $id );
                if ( !$after instanceof eZContentObject || !in_array( (int)$form['state_id'], array_map( 'intval', (array)$after->attribute( 'state_id_array' ) ), true ) )
                    return array( self::FAILED, 'The state was not assigned (a workflow may hold the change).' );
                return array( self::DONE, 'State assigned.' );
        }
        return array( self::FAILED, 'There is no such action.' );
    }

    /**
     * Records the action in the audit: the counts and the ids per outcome (at most 1000 each).
     *
     * @param array $form
     * @param array $report
     * @param string $mode now or job
     * @param string $jobID
     */
    public static function audit( array $form, array $report, $mode = 'now', $jobID = '' )
    {
        if ( !class_exists( 'expAuditHook' ) )
            return;
        $ids = array( self::DONE => array(), self::REFUSED => array(), self::FAILED => array() );
        foreach ( $report as $entry )
            if ( isset( $ids[$entry['status']] ) && count( $ids[$entry['status']] ) < self::MAX_OBJECTS )
                $ids[$entry['status']][] = (int)$entry['id'];
        $after = array( 'action' => $form['action'], 'mode' => $mode, 'counts' => self::counts( $report ), 'done' => $ids[self::DONE],
                        'refused' => $ids[self::REFUSED], 'failed' => $ids[self::FAILED] );
        if ( $form['reason'] !== '' )
            $after['reason'] = $form['reason'];
        if ( !empty( $form['section_id'] ) && $form['action'] === 'section' )
            $after['section'] = (int)$form['section_id'];
        if ( !empty( $form['state_id'] ) && $form['action'] === 'state' )
            $after['state'] = (int)$form['state_id'];
        if ( $jobID !== '' )
            $after['job'] = (string)$jobID;
        expAuditHook::emit( 'content.moderation.action', array( 'target' => array( 'type' => 'selection', 'id' => count( $report ) ), 'after' => $after ) );
    }
}
