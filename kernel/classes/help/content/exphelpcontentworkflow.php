<?php
/**
 * The review workflow of help maintained as content (doc/eng-us/specifications/6.0/help-system.md, "Status" and
 * "Permissions"; style guide section 8, "States"): Draft -> Review -> Released, as the object state group help_status.
 *
 * - Who may assign a state is decided by the roles (state/assign of "Help editor" and "Help release"). On top of
 *   that: **the person who wrote a topic does not release it**. refusedStates() takes the state Released out of an
 *   assignment when the current user created the object's current version; eZContentOperationCollection::
 *   updateObjectState() asks it for every assignment, so the rule holds in the Object states tab, the content job
 *   and the help's own buttons alike. exphelp.ini [ContentSettings] ReleaseByAuthor=enabled switches the rule off
 *   (a site with a single editor). The command line (seed-topics --release, build-manuals --release) assigns states
 *   directly: running it is the release decision of the person who runs it.
 * - transitions() lists the steps the current user may take on a help object, for the buttons of exphelp/topic and
 *   exphelp/manual; assign() takes one through the kernel's operation (workflows attached to
 *   content/updateobjectstate run as they do for the Object states tab).
 * - afterStateAssign() records the help_id of a topic set to Released (expHelpContentHelpIds).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpContentWorkflow
{
    /** The steps, in the order the buttons show them: state => the states one may go to from it */
    const STEPS = array(
        expHelpContentSchema::STATE_DRAFT => array( expHelpContentSchema::STATE_REVIEW ),
        expHelpContentSchema::STATE_REVIEW => array( expHelpContentSchema::STATE_RELEASED, expHelpContentSchema::STATE_DRAFT ),
        expHelpContentSchema::STATE_RELEASED => array( expHelpContentSchema::STATE_REVIEW ),
    );

    /**
     * Whether the author of a help object may release it (exphelp.ini [ContentSettings] ReleaseByAuthor)
     *
     * @return bool
     */
    public static function authorMayRelease()
    {
        return (string)expHelpContentSchema::setting( 'ReleaseByAuthor', 'disabled' ) === 'enabled';
    }

    /**
     * Whether an object is one of the help (a help_* class)
     *
     * @param eZContentObject $object
     * @return bool
     */
    public static function isHelpObject( eZContentObject $object )
    {
        return in_array( (string)$object->attribute( 'class_identifier' ), expHelpContentSchema::classIdentifiers(), true );
    }

    /**
     * The user who wrote the object's current version (its creator), 0 when it cannot be told
     *
     * @param eZContentObject $object
     * @return int
     */
    public static function authorId( eZContentObject $object )
    {
        $version = $object->currentVersion();
        if ( $version instanceof eZContentObjectVersion )
            return (int)$version->attribute( 'creator_id' );
        return (int)$object->attribute( 'owner_id' );
    }

    /**
     * Whether a user is the author the rule keeps from releasing
     *
     * @param int $authorId
     * @param int $userId
     * @param bool|null $authorMayRelease default authorMayRelease()
     * @return bool
     */
    public static function isRefusedAuthor( $authorId, $userId, $authorMayRelease = null )
    {
        if ( $authorMayRelease === null )
            $authorMayRelease = self::authorMayRelease();
        return !$authorMayRelease && (int)$authorId > 0 && (int)$authorId === (int)$userId;
    }

    /**
     * The state ids of an assignment that the rule refuses: Released of help_status, for a help object, when the
     * current user wrote its current version. The ids not refused go on as before.
     *
     * @param eZContentObject $object
     * @param int[] $stateIds the states to assign
     * @param int|null $userId default the current user
     * @return int[] the refused state ids
     */
    public static function refusedStates( eZContentObject $object, array $stateIds, $userId = null )
    {
        if ( !$stateIds || !self::isHelpObject( $object ) )
            return array();
        $released = expHelpContentEbooks::releasedState();
        if ( !$released instanceof eZContentObjectState || !in_array( (int)$released->attribute( 'id' ), array_map( 'intval', $stateIds ), true ) )
            return array();
        $userId = $userId === null ? (int)eZUser::currentUserID() : (int)$userId;
        if ( !self::isRefusedAuthor( self::authorId( $object ), $userId ) )
            return array();
        eZDebug::writeNotice( 'Help: the author of object ' . $object->attribute( 'id' ) . ' may not release it; someone else with the release role does',
                              __METHOD__ );
        return array( (int)$released->attribute( 'id' ) );
    }

    /**
     * What the operation calls after it assigned states: records the help_id of a topic set to Released
     *
     * @param int $objectId
     * @param int[] $stateIds
     */
    public static function afterStateAssign( $objectId, array $stateIds )
    {
        try
        {
            $released = expHelpContentEbooks::releasedState();
            if ( !$released instanceof eZContentObjectState || !in_array( (int)$released->attribute( 'id' ), array_map( 'intval', $stateIds ), true ) )
                return;
            $object = eZContentObject::fetch( (int)$objectId );
            if ( $object instanceof eZContentObject )
                self::recordHelpId( $object );
        }
        catch ( Exception $e )
        {
            eZDebug::writeError( 'The released help_id could not be recorded: ' . $e->getMessage(), __METHOD__ );
        }
    }

    /**
     * Records the help_id of a released topic (expHelpContentHelpIds), not of one made from the Markdown help
     *
     * @param eZContentObject $object
     * @return bool whether a row was added
     */
    public static function recordHelpId( eZContentObject $object )
    {
        if ( !isset( expHelpContentSchema::TOPIC_TYPES[(string)$object->attribute( 'class_identifier' )] ) || !expHelpContentHelpIds::available() )
            return false;
        $dataMap = $object->attribute( 'data_map' );
        $helpId = isset( $dataMap['help_id'] ) ? trim( (string)$dataMap['help_id']->attribute( 'content' ) ) : '';
        $topic = array( 'help_id' => $helpId, 'remote_id' => (string)$object->attribute( 'remote_id' ) );
        if ( !expHelpContentHelpIds::isRecorded( $topic ) )
            return false;
        return expHelpContentHelpIds::record( $helpId, (int)$object->attribute( 'id' ) );
    }

    /**
     * The identifier of an object's help_status state ('' none)
     *
     * @param eZContentObject $object
     * @return string
     */
    public static function stateOf( eZContentObject $object )
    {
        foreach ( $object->stateIdentifierArray() as $pair )
        {
            if ( strpos( $pair, expHelpContentSchema::STATE_GROUP . '/' ) === 0 )
                return substr( $pair, strlen( expHelpContentSchema::STATE_GROUP ) + 1 );
        }
        return '';
    }

    /**
     * The steps of the review workflow from a state, with what keeps the user from each (pure, for the tests)
     *
     * @param string $state the current state identifier
     * @param string[] $allowed the state identifiers the user may assign (state/assign)
     * @param bool $isAuthor whether the user wrote the current version (and the rule applies)
     * @return array list of array( state, allowed (bool), reason ('' | 'policy' | 'author') )
     */
    public static function steps( $state, array $allowed, $isAuthor )
    {
        $steps = array();
        foreach ( isset( self::STEPS[$state] ) ? self::STEPS[$state] : array() as $target )
        {
            $reason = '';
            if ( !in_array( $target, $allowed, true ) )
                $reason = 'policy';
            elseif ( $target === expHelpContentSchema::STATE_RELEASED && $isAuthor )
                $reason = 'author';
            $steps[] = array( 'state' => $target, 'allowed' => $reason === '', 'reason' => $reason );
        }
        return $steps;
    }

    /**
     * The steps the current user may see for a help object: the next states, allowed or not and why. Steps refused by
     * the roles are left out (the user cannot take them, and need not be told); a release refused because the user
     * wrote the topic stays, with the reason.
     *
     * @param eZContentObject $object
     * @return array array( state (current identifier), steps (list of array( state, allowed, reason )) )
     */
    public static function transitions( eZContentObject $object )
    {
        $state = self::stateOf( $object );
        $group = eZContentObjectStateGroup::fetchByIdentifier( expHelpContentSchema::STATE_GROUP );
        if ( $state === '' || !$group instanceof eZContentObjectStateGroup )
            return array( 'state' => $state, 'steps' => array() );
        $allowedIds = array_map( 'intval', (array)$object->attribute( 'allowed_assign_state_id_list' ) );
        $allowed = array();
        foreach ( $group->states() as $s )
        {
            if ( in_array( (int)$s->attribute( 'id' ), $allowedIds, true ) )
                $allowed[] = (string)$s->attribute( 'identifier' );
        }
        $isAuthor = self::isRefusedAuthor( self::authorId( $object ), (int)eZUser::currentUserID() );
        $steps = array_values( array_filter( self::steps( $state, $allowed, $isAuthor ), function ( $step ) {
            return $step['reason'] !== 'policy';
        } ) );
        return array( 'state' => $state, 'steps' => $steps );
    }

    /**
     * Takes one step of the workflow for the current user, through the kernel's operation
     *
     * @param eZContentObject $object
     * @param string $target the state identifier
     * @return string '' done, else why not: unknown, policy, author, failed
     */
    public static function assign( eZContentObject $object, $target )
    {
        $group = eZContentObjectStateGroup::fetchByIdentifier( expHelpContentSchema::STATE_GROUP );
        $state = $group instanceof eZContentObjectStateGroup ? $group->stateByIdentifier( (string)$target ) : null;
        if ( !$state instanceof eZContentObjectState || !self::isHelpObject( $object ) )
            return 'unknown';
        $current = self::stateOf( $object );
        if ( !isset( self::STEPS[$current] ) || !in_array( (string)$target, self::STEPS[$current], true ) )
            return 'unknown';
        $transitions = self::transitions( $object );
        $step = null;
        foreach ( $transitions['steps'] as $candidate )
            if ( $candidate['state'] === (string)$target )
                $step = $candidate;
        if ( $step === null )
            return 'policy';
        if ( !$step['allowed'] )
            return $step['reason'];
        $objectId = (int)$object->attribute( 'id' );
        $stateIds = array( (int)$state->attribute( 'id' ) );
        if ( eZOperationHandler::operationIsAvailable( 'content_updateobjectstate' ) )
            eZOperationHandler::execute( 'content', 'updateobjectstate', array( 'object_id' => $objectId, 'state_id_list' => $stateIds ) );
        else
            eZContentOperationCollection::updateObjectState( $objectId, $stateIds );
        eZContentObject::clearCache( array( $objectId ) );
        $fresh = eZContentObject::fetch( $objectId );
        return $fresh instanceof eZContentObject && self::stateOf( $fresh ) === (string)$target ? '' : 'failed';
    }
}

?>
