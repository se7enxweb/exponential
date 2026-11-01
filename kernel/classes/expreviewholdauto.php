<?php
/**
 * File containing the expReviewHoldAuto class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The review hold as moderation (doc/eng-us/features/6.0/review-hold.md, "Auto-hold"): content submitted by visitors
 * is held and hidden until a reviewer releases it, and reviewers place and release holds with a reason.
 *
 * - Auto-hold (content.ini [ReviewHoldSettings]): the first publication of an object of a class in AutoHoldClassList[],
 *   made by a user who is not exempt (ExemptPolicyList[]), is put into review_hold/held and its locations are hidden.
 *   It runs in the publish operation (the step auto-review-hold, eZContentOperationCollection::autoReviewHold), after
 *   the object is published and before the content edit handlers, the view cache and the search index, so the page
 *   never shows it and the search engine indexes it hidden. Hiding is the kernel's own visibility (the hidden subtree,
 *   ShowHiddenNodes), the same as an editor's Hide: no role changes, and visitors keep their read policies.
 * - Place (hold with a reason) and release: the kernel's updateobjectstate operation, so state/assign of the group
 *   decides. Releasing also reveals the locations the auto-hold hid (when the user may hide them, content/hide).
 * - Who placed a hold, when, why, and which locations the auto-hold hid, is kept in the site data table
 *   (ezsite_data, review_hold_<object id>) while the hold lasts (meta()).
 * - Audit: content.object.hold.auto (with the submitter's address and browser as the audit keeps them),
 *   content.object.hold.place and content.object.hold.release (with the reason), besides the kernel's
 *   content.object.state.
 *
 * holds() and exemptBy() are pure (tests/tests/kernel/classes/expReviewHoldAutoTest.php).
 *
 * @since 6.0.15
 * @package kernel
 */
class expReviewHoldAuto
{
    const META_PREFIX = 'review_hold_';
    const REASON_MAX = 500;

    const DONE = 'done';
    const REFUSED = 'refused';
    const FAILED = 'failed';

    /** @var array|null settings handed in by tests: AutoHold, AutoHoldClassList, ExemptPolicyList */
    public static $settings = null;

    /**
     * The settings of [ReviewHoldSettings].
     *
     * @return array enabled (bool), classes (string[]), exempt (string[] module/function)
     */
    public static function settings()
    {
        if ( is_array( self::$settings ) )
            $raw = self::$settings;
        else
        {
            $ini = eZINI::instance( 'content.ini' );
            $raw = array();
            foreach ( array( 'AutoHold', 'AutoHoldClassList', 'ExemptPolicyList' ) as $name )
                $raw[$name] = $ini->hasVariable( 'ReviewHoldSettings', $name ) ? $ini->variable( 'ReviewHoldSettings', $name ) : null;
        }
        $list = function ( $value ) {
            return array_values( array_unique( array_filter( array_map( 'trim', array_map( 'strval', (array)$value ) ), 'strlen' ) ) );
        };
        $exempt = array();
        foreach ( $list( isset( $raw['ExemptPolicyList'] ) ? $raw['ExemptPolicyList'] : array() ) as $entry )
            if ( preg_match( '#^[A-Za-z0-9_]+/[A-Za-z0-9_*]+$#', $entry ) )
                $exempt[] = $entry;
        return array(
            'enabled' => isset( $raw['AutoHold'] ) && in_array( strtolower( trim( (string)$raw['AutoHold'] ) ), array( 'enabled', 'true', '1', 'yes' ), true ),
            'classes' => $list( isset( $raw['AutoHoldClassList'] ) ? $raw['AutoHoldClassList'] : array() ),
            'exempt' => $exempt,
        );
    }

    /**
     * Pure: is this publication auto-held?
     *
     * @param bool $enabled [ReviewHoldSettings] AutoHold
     * @param string $class the class identifier of the object
     * @param string[] $classes AutoHoldClassList[]
     * @param bool $first the object's first publication
     * @param bool $exempt the creator of the version is exempt (exemptBy() found a policy)
     * @param bool $held the object is held already
     * @return bool
     */
    public static function holds( $enabled, $class, array $classes, $first, $exempt, $held )
    {
        return (bool)$enabled && in_array( (string)$class, array_map( 'strval', $classes ), true ) && (bool)$first && !$exempt && !$held;
    }

    /**
     * Pure: the first exempting policy, or null.
     *
     * @param string[] $policies ExemptPolicyList[]
     * @param callable $has function( module, function ): bool, whether the user has it (for the object)
     * @return string|null module/function
     */
    public static function exemptBy( array $policies, $has )
    {
        foreach ( $policies as $policy )
        {
            $parts = explode( '/', (string)$policy, 2 );
            if ( count( $parts ) === 2 && call_user_func( $has, $parts[0], $parts[1] ) )
                return (string)$policy;
        }
        return null;
    }

    /**
     * Whether a user has a policy of ExemptPolicyList[] for an object: state/assign of the held state, content/hide of
     * a location, moderation/act; any other module/function when the user has it at all.
     *
     * @param eZUser $user
     * @param eZContentObject $object
     * @param string $module
     * @param string $function
     * @return bool
     */
    public static function userHas( eZUser $user, eZContentObject $object, $module, $function )
    {
        if ( $module === 'state' && $function === 'assign' )
        {
            $held = expReviewHold::state( expReviewHold::STATE_HELD );
            return $held !== null && in_array( (int)$held->attribute( 'id' ), array_map( 'intval', (array)$object->allowedAssignStateIDList( $user ) ), true );
        }
        if ( $module === 'content' && $function === 'hide' )
        {
            foreach ( (array)$object->assignedNodes() as $node )
                if ( $node instanceof eZContentObjectTreeNode
                     && $node->checkAccess( 'hide', false, false, false, false, (int)$user->attribute( 'contentobject_id' ) ) == 1 )
                    return true;
            return false;
        }
        $access = $user->hasAccessTo( $module, $function );
        return isset( $access['accessWord'] ) && $access['accessWord'] !== 'no';
    }

    /**
     * The publish operation's step auto-review-hold: holds and hides the first publication of a listed class by a user
     * who is not exempt. Nothing without the state group or with AutoHold disabled.
     *
     * @param int $objectID
     * @param int $versionNum
     * @return int[] the node ids hidden (empty: not held)
     */
    public static function published( $objectID, $versionNum )
    {
        $settings = self::settings();
        if ( !$settings['enabled'] || !$settings['classes'] )
            return array();
        $object = eZContentObject::fetch( (int)$objectID );
        if ( !$object instanceof eZContentObject || !in_array( (string)$object->attribute( 'class_identifier' ), $settings['classes'], true ) )
            return array();
        $held = expReviewHold::state( expReviewHold::STATE_HELD );
        if ( !$held )
        {
            eZDebug::writeWarning( 'AutoHold is enabled but the state group ' . expReviewHold::GROUP . ' does not exist: run the upgrade step 6.0.15-review-hold', __METHOD__ );
            return array();
        }
        $version = $object->version( (int)$versionNum );
        $creatorID = $version instanceof eZContentObjectVersion ? (int)$version->attribute( 'creator_id' ) : (int)$object->attribute( 'owner_id' );
        $creator = $creatorID > 0 ? eZUser::fetch( $creatorID ) : null;
        if ( !$creator instanceof eZUser )
            $creator = eZUser::fetch( (int)eZUser::anonymousId() );
        $exempt = $creator instanceof eZUser
            ? self::exemptBy( $settings['exempt'], function ( $module, $function ) use ( $creator, $object ) { return self::userHas( $creator, $object, $module, $function ); } )
            : null;
        if ( !self::holds( true, (string)$object->attribute( 'class_identifier' ), $settings['classes'], self::firstPublication( (int)$objectID, (int)$versionNum ),
                           $exempt !== null, expReviewHold::isHeld( $object ) ) )
            return array();

        // the system holds it: the visitor's own state/assign is not asked (they have none)
        $object->assignState( $held );
        $hidden = array();
        foreach ( (array)$object->assignedNodes() as $node )
        {
            if ( !$node instanceof eZContentObjectTreeNode || $node->attribute( 'is_hidden' ) )
                continue;
            eZContentOperationCollection::changeHideStatus( (int)$node->attribute( 'node_id' ) );
            $hidden[] = (int)$node->attribute( 'node_id' );
        }
        self::storeMeta( (int)$objectID, array( 'user' => 0, 'name' => '', 'since' => time(), 'reason' => '', 'auto' => true,
                                                'hidden' => $hidden, 'creator' => $creatorID ) );
        eZSearch::updateObjectState( (int)$objectID, array( (int)$held->attribute( 'id' ) ) );
        ezpEvent::getInstance()->notify( 'content/state/assign', array( (int)$objectID, array( (int)$held->attribute( 'id' ) ) ) );
        if ( class_exists( 'expAuditHook' ) )
            expAuditHook::emit( 'content.object.hold.auto', array( 'object' => expAuditHook::object( $object ),
                'target' => array( 'type' => 'version', 'id' => (int)$versionNum ),
                'after' => array( 'held' => true, 'hidden_nodes' => $hidden, 'class' => (string)$object->attribute( 'class_identifier' ), 'creator' => $creatorID ) ) );
        eZDebug::writeNotice( "Object $objectID held for review on its first publication; node(s) " . implode( ', ', $hidden ) . ' hidden', __METHOD__ );
        return $hidden;
    }

    /**
     * @return bool no version of the object but this one was ever published (the publish operation has archived the
     *              previous published version before this step)
     */
    protected static function firstPublication( $objectID, $versionNum )
    {
        $rows = eZDB::instance()->arrayQuery( 'SELECT version FROM ezcontentobject_version WHERE contentobject_id = ' . (int)$objectID
                                              . ' AND status IN (' . (int)eZContentObjectVersion::STATUS_PUBLISHED . ', ' . (int)eZContentObjectVersion::STATUS_ARCHIVED . ')' );
        foreach ( (array)$rows as $row )
            if ( (int)$row['version'] !== (int)$versionNum )
                return false;
        return true;
    }

    /**
     * Who placed the hold, when, why; for an auto-hold the locations it hid. Null when nothing is kept.
     *
     * @param int $objectID
     * @return array|null user, name, since, reason, auto, hidden (node ids), creator
     */
    public static function meta( $objectID )
    {
        $row = eZSiteData::fetchByName( self::META_PREFIX . (int)$objectID );
        if ( !$row instanceof eZSiteData )
            return null;
        $data = json_decode( (string)$row->attribute( 'value' ), true );
        return is_array( $data ) ? $data : null;
    }

    /**
     * The kept data of many objects in one query (the moderation list).
     *
     * @param int[] $objectIDs
     * @return array object id => meta
     */
    public static function metaList( array $objectIDs )
    {
        $names = array();
        foreach ( $objectIDs as $id )
            if ( (int)$id > 0 )
                $names[] = "'" . self::META_PREFIX . (int)$id . "'";
        if ( !$names )
            return array();
        $out = array();
        foreach ( array_chunk( array_unique( $names ), 500 ) as $chunk )
        {
            foreach ( (array)eZDB::instance()->arrayQuery( 'SELECT name, value FROM ezsite_data WHERE name IN (' . implode( ', ', $chunk ) . ')' ) as $row )
            {
                $data = json_decode( (string)$row['value'], true );
                if ( is_array( $data ) )
                    $out[(int)substr( (string)$row['name'], strlen( self::META_PREFIX ) )] = $data;
            }
        }
        return $out;
    }

    protected static function storeMeta( $objectID, array $data )
    {
        $name = self::META_PREFIX . (int)$objectID;
        $row = eZSiteData::fetchByName( $name );
        if ( !$row instanceof eZSiteData )
            $row = eZSiteData::create( $name, '' );
        $row->setAttribute( 'value', json_encode( $data ) );
        $row->store();
    }

    protected static function removeMeta( $objectID )
    {
        $row = eZSiteData::fetchByName( self::META_PREFIX . (int)$objectID );
        if ( $row instanceof eZSiteData )
            $row->remove();
    }

    /**
     * A reason as it is kept: one line without control characters, at most REASON_MAX characters. Pure.
     *
     * @param mixed $text
     * @return string
     */
    public static function reason( $text )
    {
        $text = preg_replace( '/[\x00-\x1F\x7F]+/u', ' ', (string)$text );
        $text = trim( (string)preg_replace( '/\s+/u', ' ', (string)$text ) );
        return function_exists( 'mb_substr' ) ? mb_substr( $text, 0, self::REASON_MAX, 'UTF-8' ) : substr( $text, 0, self::REASON_MAX );
    }

    /**
     * Places a hold with a reason, as the current user, through the kernel's updateobjectstate operation.
     *
     * @param eZContentObject $object
     * @param string $reason
     * @return array status (done, refused, failed), message (an untranslated English sentence)
     */
    public static function place( eZContentObject $object, $reason )
    {
        $held = expReviewHold::state( expReviewHold::STATE_HELD );
        if ( !$held )
            return array( 'status' => self::FAILED, 'message' => 'The review hold is not set up (the upgrade step 6.0.15-review-hold).' );
        $reason = self::reason( $reason );
        if ( $reason === '' )
            return array( 'status' => self::REFUSED, 'message' => 'Say why it is held.' );
        if ( expReviewHold::isHeld( $object ) )
            return array( 'status' => self::REFUSED, 'message' => 'It is held already.' );
        if ( !in_array( (int)$held->attribute( 'id' ), array_map( 'intval', (array)$object->attribute( 'allowed_assign_state_id_list' ) ), true ) )
            return array( 'status' => self::REFUSED, 'message' => 'You may not hold it (state/assign).' );
        $id = (int)$object->attribute( 'id' );
        eZOperationHandler::execute( 'content', 'updateobjectstate', array( 'object_id' => $id, 'state_id_list' => array( (int)$held->attribute( 'id' ) ) ) );
        eZContentObject::clearCache( array( $id ) );
        $after = eZContentObject::fetch( $id );
        if ( !$after instanceof eZContentObject || !expReviewHold::isHeld( $after ) )
            return array( 'status' => self::FAILED, 'message' => 'The hold was not placed (a workflow may hold the change).' );
        $user = eZUser::currentUser();
        $userObject = $user->contentObject();
        self::storeMeta( $id, array( 'user' => (int)$user->attribute( 'contentobject_id' ),
                                     'name' => $userObject instanceof eZContentObject ? (string)$userObject->attribute( 'name' ) : (string)$user->attribute( 'login' ),
                                     'since' => time(), 'reason' => $reason, 'auto' => false, 'hidden' => array() ) );
        if ( class_exists( 'expAuditHook' ) )
            expAuditHook::emit( 'content.object.hold.place', array( 'object' => expAuditHook::object( $after ),
                'before' => array( 'held' => false ), 'after' => array( 'held' => true, 'reason' => $reason ) ) );
        return array( 'status' => self::DONE, 'message' => 'Held.' );
    }

    /**
     * Releases a hold as the current user (state/assign of not_held) and reveals the locations an auto-hold hid, where
     * the user may hide them (content/hide); a location that stays hidden is said in the message.
     *
     * @param eZContentObject $object
     * @param string $reason optional
     * @return array status, message, revealed (node ids), hidden (node ids left hidden)
     */
    public static function release( eZContentObject $object, $reason = '' )
    {
        $free = expReviewHold::state( expReviewHold::STATE_FREE );
        if ( !$free )
            return array( 'status' => self::FAILED, 'message' => 'The review hold is not set up (the upgrade step 6.0.15-review-hold).', 'revealed' => array(), 'hidden' => array() );
        if ( !expReviewHold::isHeld( $object ) )
            return array( 'status' => self::REFUSED, 'message' => 'It is not held.', 'revealed' => array(), 'hidden' => array() );
        if ( !in_array( (int)$free->attribute( 'id' ), array_map( 'intval', (array)$object->attribute( 'allowed_assign_state_id_list' ) ), true ) )
            return array( 'status' => self::REFUSED, 'message' => 'You may not release it (state/assign).', 'revealed' => array(), 'hidden' => array() );
        $id = (int)$object->attribute( 'id' );
        $meta = self::meta( $id );
        eZOperationHandler::execute( 'content', 'updateobjectstate', array( 'object_id' => $id, 'state_id_list' => array( (int)$free->attribute( 'id' ) ) ) );
        eZContentObject::clearCache( array( $id ) );
        $after = eZContentObject::fetch( $id );
        if ( !$after instanceof eZContentObject || expReviewHold::isHeld( $after ) )
            return array( 'status' => self::FAILED, 'message' => 'The hold was not released (a workflow may hold the change).', 'revealed' => array(), 'hidden' => array() );
        $revealed = array();
        $left = array();
        foreach ( is_array( $meta ) && !empty( $meta['hidden'] ) ? array_map( 'intval', (array)$meta['hidden'] ) : array() as $nodeID )
        {
            $node = eZContentObjectTreeNode::fetch( $nodeID );
            if ( !$node instanceof eZContentObjectTreeNode || !$node->attribute( 'is_hidden' ) )
                continue;
            if ( !$node->canHide() )
            {
                $left[] = $nodeID;
                continue;
            }
            eZContentOperationCollection::changeHideStatus( $nodeID );
            $revealed[] = $nodeID;
        }
        if ( $left && is_array( $meta ) )
            self::storeMeta( $id, array( 'hidden' => $left, 'released' => time() ) + $meta );
        else
            self::removeMeta( $id );
        if ( $revealed )
            eZContentCacheManager::clearContentCacheIfNeeded( $id );
        $reason = self::reason( $reason );
        if ( class_exists( 'expAuditHook' ) )
            expAuditHook::emit( 'content.object.hold.release', array( 'object' => expAuditHook::object( $after ),
                'before' => array( 'held' => true, 'auto' => is_array( $meta ) && !empty( $meta['auto'] ), 'reason' => is_array( $meta ) && isset( $meta['reason'] ) ? (string)$meta['reason'] : null ),
                'after' => array( 'held' => false, 'reason' => $reason, 'revealed_nodes' => $revealed, 'still_hidden' => $left ) ) );
        return array( 'status' => self::DONE, 'message' => $left ? 'Released; still hidden: you may not reveal it (content/hide).' : 'Released.',
                      'revealed' => $revealed, 'hidden' => $left );
    }
}
