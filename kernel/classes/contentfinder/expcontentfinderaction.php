<?php
/**
 * File containing the expContentFinderAction class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The actions of the content finder (doc/features/6.0/content-finder.md, "Actions"): move an object to the trash,
 * delete it permanently, purge it from the trash, remove a version. Restoring from the trash is the kernel's own
 * content/restore page, which the result links to.
 *
 * plan() says exactly what an action would do (every location, the children below each, the versions) and
 * whether the current user may do it; execute() plans again, refuses when anything changed since the confirmation
 * page was shown, and runs the kernel's own operation: content/delete (eZContentOperationCollection::deleteObject,
 * which removes through eZContentObjectTreeNode::removeSubtrees), the trash service's purge, or
 * eZContentObjectVersion::removeThis(). Nothing here deletes by SQL of its own.
 *
 * Every action needs contentfinder/delete and then the content policies of the operation, checked on the object:
 *   trash, delete   content/remove on every location (eZContentObjectTreeNode::canRemove()) and on every node below
 *                   (subtreeRemovalInformation()), and content/read on every location
 *   purge           content/cleantrash and content/remove, the trash entry visible (content/restore or cleantrash
 *                   and the trash list's permission SQL)
 *   removeversion   who may edit the object (editAccess()), content/versionremove on the version, a status
 *                   content/history removes (draft, archived, rejected, internal draft)
 *
 * The forms carry two tokens: the form token of ezformtoken (checked by it for every POST, and here again) and
 * this class's own, bound to the session; the confirmation carries a third, bound to the action, the target and
 * the plan's fingerprint, so a confirmation cannot be replayed for another object or after the subtree changed.
 */
class expContentFinderAction
{
    /** The actions */
    const ACTIONS = array( 'trash', 'delete', 'purge', 'removeversion' );

    /** The actions that cannot be undone, which need the explicit confirmation (ConfirmPermanent) */
    const PERMANENT = array( 'delete', 'purge', 'removeversion' );

    /** The posted field of the session token */
    const TOKEN_FIELD = 'ContentFinderToken';

    /** The posted field of the confirmation token */
    const CONFIRM_FIELD = 'ContentFinderConfirmToken';

    /** The session variable that holds the secret of the tokens */
    const SESSION_SECRET = 'ContentFinderSecret';

    /** @var string|null A fixed secret for tests */
    protected static $testSecret = null;

    // ------------------------------------------------------------------ tokens

    /**
     * @param string|null $secret a fixed secret (tests); null goes back to the session's
     */
    public static function setTestSecret( $secret )
    {
        self::$testSecret = $secret === null ? null : (string)$secret;
    }

    /**
     * The secret of this session's tokens: random, made on first use and kept in the session.
     *
     * @return string|false false without a session to keep it in
     */
    protected static function secret()
    {
        if ( self::$testSecret !== null )
            return self::$testSecret;
        $http = eZHTTPTool::instance();
        $secret = $http->hasSessionVariable( self::SESSION_SECRET ) ? $http->sessionVariable( self::SESSION_SECRET ) : null;
        if ( !is_string( $secret ) || strlen( $secret ) !== 64 )
        {
            if ( !eZSession::hasStarted() )
                eZSession::start();
            $secret = bin2hex( random_bytes( 32 ) );
            $http->setSessionVariable( self::SESSION_SECRET, $secret );
        }
        return $secret;
    }

    /**
     * @param string $purpose
     * @return string
     */
    protected static function token( $purpose )
    {
        $secret = self::secret();
        if ( $secret === false || $secret === '' )
            return '';
        $user = eZUser::currentUser();
        $userID = $user instanceof eZUser ? (int)$user->attribute( 'contentobject_id' ) : 0;
        return hash_hmac( 'sha256', 'contentfinder|' . $userID . '|' . $purpose, $secret );
    }

    /**
     * The token every finder form carries.
     *
     * @return string
     */
    public static function formToken()
    {
        return self::token( 'form' );
    }

    /**
     * The confirmation token of a plan.
     *
     * @param array $plan from plan()
     * @return string
     */
    public static function confirmToken( array $plan )
    {
        return self::token( 'confirm|' . $plan['fingerprint'] );
    }

    /**
     * @param mixed $posted
     * @param string $expected
     * @return bool
     */
    protected static function matches( $posted, $expected )
    {
        return is_string( $posted ) && $expected !== '' && strlen( $posted ) === strlen( $expected ) && hash_equals( $expected, $posted );
    }

    /**
     * @param mixed $posted
     * @return bool
     */
    public static function checkFormToken( $posted )
    {
        return self::matches( $posted, self::formToken() );
    }

    /**
     * @param mixed $posted
     * @param array $plan
     * @return bool
     */
    public static function checkConfirmToken( $posted, array $plan )
    {
        return self::matches( $posted, self::confirmToken( $plan ) );
    }

    /**
     * The form token of ezformtoken, checked here as well: true when the extension is not there or not protecting
     * this request (no session or anonymous user, where this view is refused anyway).
     *
     * @param array $post
     * @return bool
     */
    public static function checkEzFormToken( array $post )
    {
        if ( !class_exists( 'ezxFormToken' ) || !ezxFormToken::isEnabled() )
            return true;
        if ( !eZSession::hasStarted() || !eZUser::isCurrentUserRegistered() )
            return true;
        $field = defined( 'ezxFormToken::FORM_FIELD' ) ? ezxFormToken::FORM_FIELD : 'ezxform_token';
        return isset( $post[$field] ) && self::matches( $post[$field], (string)ezxFormToken::getToken() );
    }

    // ------------------------------------------------------------------ plan

    /**
     * What an action would do, and whether the current user may do it.
     *
     * @param string $action one of ACTIONS
     * @param int $objectID
     * @param int $versionID for removeversion: ezcontentobject_version.id
     * @return array array( 'action', 'allowed', 'reason', 'object_id', 'name', 'class_name', 'nodes', 'children',
     *               'locations', 'versions', 'version', 'move_to_trash', 'permanent', 'reverse_related', 'trash',
     *               'fingerprint' ); reason: '' when allowed, else unknown_action, not_found (also: no access to the
     *               object), no_policy, not_removable, trash_not_allowed, too_many, pending, job_lock, not_in_trash,
     *               version_not_removable
     */
    public static function plan( $action, $objectID, $versionID = 0 )
    {
        $plan = array( 'action' => (string)$action, 'allowed' => false, 'reason' => '', 'object_id' => (int)$objectID,
                       'name' => '', 'class_name' => '', 'nodes' => array(), 'children' => 0, 'locations' => 0,
                       'versions' => 0, 'version' => null, 'move_to_trash' => $action === 'trash',
                       'permanent' => in_array( $action, self::PERMANENT, true ), 'reverse_related' => 0,
                       'trash' => array(), 'limit' => 0, 'fingerprint' => '' );
        if ( !in_array( $action, self::ACTIONS, true ) )
            return self::refused( $plan, 'unknown_action' );

        $object = (int)$objectID > 0 ? eZContentObject::fetch( (int)$objectID ) : null;
        if ( !$object instanceof eZContentObject )
            return self::refused( $plan, 'not_found' );
        $canRead = (bool)$object->canRead();
        $user = eZUser::currentUser();
        $mayRestore = expContentFinder::hasPolicy( 'content', 'restore', $user ) || expContentFinder::hasPolicy( 'content', 'cleantrash', $user );
        $trash = array();
        if ( $object->attribute( 'status' ) == eZContentObject::STATUS_ARCHIVED && $mayRestore )
        {
            $trashNodes = eZContentObjectTrashNode::trashList( array( 'ContentObjectIDList' => array( (int)$objectID ), 'AttributeFilter' => false,
                                                                      'Limit' => 10, 'Offset' => 0, 'SortBy' => array( 'trashed', '0' ) ) );
            foreach ( is_array( $trashNodes ) ? $trashNodes : array() as $trashNode )
                $trash[] = array( 'node_id' => (int)$trashNode->attribute( 'node_id' ),
                                  'path' => (string)$trashNode->attribute( 'path_identification_string' ),
                                  'trashed' => (int)$trashNode->attribute( 'trashed' ) );
        }
        if ( !$canRead && !$trash )
            return self::refused( $plan, 'not_found' );

        $class = $object->contentClass();
        $plan['name'] = (string)$object->attribute( 'name' );
        $plan['class_name'] = $class ? (string)$class->attribute( 'name' ) : '';
        $versionList = $object->versions( false );
        $plan['versions'] = is_array( $versionList ) ? count( $versionList ) : 0;
        $plan['trash'] = $trash;

        if ( !expContentFinder::hasPolicy( 'contentfinder', 'delete', $user ) )
            return self::refused( $plan, 'no_policy' );

        switch ( $action )
        {
            case 'trash':
            case 'delete':
                $plan = self::planRemoval( $plan, $object, $canRead );
                break;
            case 'purge':
                if ( !$trash )
                    $plan = self::refused( $plan, 'not_in_trash' );
                else if ( !expContentFinder::hasPolicy( 'content', 'cleantrash', $user ) || !expContentFinder::hasPolicy( 'content', 'remove', $user ) )
                    $plan = self::refused( $plan, 'no_policy' );
                break;
            case 'removeversion':
                $plan = self::planVersionRemoval( $plan, $object, (int)$versionID, $canRead );
                break;
        }
        if ( $plan['reason'] === '' )
            $plan['allowed'] = true;
        $plan['fingerprint'] = self::fingerprint( $plan );
        return $plan;
    }

    /**
     * @param array $plan
     * @param string $reason
     * @return array
     */
    protected static function refused( array $plan, $reason )
    {
        $plan['allowed'] = false;
        $plan['reason'] = $reason;
        $plan['fingerprint'] = self::fingerprint( $plan );
        return $plan;
    }

    /**
     * @param array $plan
     * @return string
     */
    protected static function fingerprint( array $plan )
    {
        $nodes = array();
        foreach ( $plan['nodes'] as $node )
            $nodes[] = $node['node_id'] . ':' . $node['children'];
        $trash = array();
        foreach ( $plan['trash'] as $entry )
            $trash[] = $entry['node_id'];
        return implode( '|', array( $plan['action'], $plan['object_id'], implode( ',', $nodes ), implode( ',', $trash ),
                                    $plan['version'] ? $plan['version']['id'] . ':' . $plan['version']['status'] : '',
                                    $plan['versions'], $plan['move_to_trash'] ? 1 : 0 ) );
    }

    /**
     * Moving to the trash or deleting: every location of the object, read and removable by the user, each with the
     * nodes below it, as content/removeobject shows them.
     */
    protected static function planRemoval( array $plan, eZContentObject $object, $canRead )
    {
        if ( !$canRead || $object->attribute( 'status' ) != eZContentObject::STATUS_PUBLISHED )
            return self::refused( $plan, $object->attribute( 'status' ) == eZContentObject::STATUS_ARCHIVED ? 'not_removable' : ( $canRead ? 'not_removable' : 'not_found' ) );
        $nodes = $object->assignedNodes( true );
        $nodeIDs = array();
        foreach ( is_array( $nodes ) ? $nodes : array() as $node )
        {
            if ( !$node->canRead() || !$node->canRemove() )
                return self::refused( $plan, 'not_removable' );
            $nodeIDs[] = (int)$node->attribute( 'node_id' );
        }
        if ( !$nodeIDs )
            return self::refused( $plan, 'not_removable' );
        $plan['locations'] = count( $nodeIDs );

        $info = eZContentObjectTreeNode::subtreeRemovalInformation( $nodeIDs );
        $children = 0;
        foreach ( $info['delete_list'] as $item )
        {
            $plan['nodes'][] = array( 'node_id' => (int)$item['node']->attribute( 'node_id' ),
                                      'path' => (string)$item['node']->attribute( 'path_identification_string' ),
                                      'url_alias' => (string)$item['node']->urlAlias(),
                                      'is_main' => (int)$item['node']->attribute( 'node_id' ) === (int)$object->attribute( 'main_node_id' ),
                                      'children' => (int)$item['child_count'],
                                      'can_remove' => (bool)$item['can_remove'] );
            $children += (int)$item['child_count'];
        }
        $plan['children'] = $children;
        $plan['reverse_related'] = isset( $info['reverse_related_count'] ) ? (int)$info['reverse_related_count'] : 0;

        if ( !$info['can_remove_all'] || count( $plan['nodes'] ) !== count( $nodeIDs ) )
            return self::refused( $plan, 'not_removable' );
        if ( !empty( $info['has_pending_object'] ) )
            return self::refused( $plan, 'pending' );
        if ( $plan['action'] === 'trash' && !$info['move_to_trash'] )
            return self::refused( $plan, 'trash_not_allowed' );
        $contentINI = eZINI::instance( 'content.ini' );
        $limit = $contentINI->hasVariable( 'RemoveSettings', 'MaxNodesRemoveSubtree' ) ? (int)$contentINI->variable( 'RemoveSettings', 'MaxNodesRemoveSubtree' ) : 100;
        $plan['limit'] = $limit;
        if ( $children > $limit )
            return self::refused( $plan, 'too_many' );
        if ( class_exists( 'expContentJobLock' ) )
        {
            try
            {
                if ( expContentJobLock::checkNodes( $nodeIDs ) )
                    return self::refused( $plan, 'job_lock' );
            }
            catch ( Throwable $e )
            {
                eZDebug::writeError( 'Content jobs: ' . $e->getMessage(), __METHOD__ );
            }
        }
        return $plan;
    }

    /**
     * Removing one version: as content/history removes one.
     */
    protected static function planVersionRemoval( array $plan, eZContentObject $object, $versionID, $canRead )
    {
        $version = $versionID > 0 ? eZContentObjectVersion::fetch( $versionID ) : null;
        if ( !$version instanceof eZContentObjectVersion || (int)$version->attribute( 'contentobject_id' ) !== (int)$object->attribute( 'id' ) )
            return self::refused( $plan, 'not_found' );
        $status = (int)$version->attribute( 'status' );
        if ( !$version->canVersionRead() && !( $canRead && $status === eZContentObjectVersion::STATUS_PUBLISHED ) )
            return self::refused( $plan, 'not_found' );
        $names = expContentFinder::versionStatusNames();
        $plan['version'] = array( 'id' => (int)$version->attribute( 'id' ), 'version' => (int)$version->attribute( 'version' ),
                                  'status' => $status, 'status_name' => isset( $names[$status] ) ? $names[$status] : (string)$status,
                                  'language' => (string)$version->initialLanguageCode() );
        if ( !expContentFinder::isRemovableVersionStatus( $status ) || !$object->editAccess() || !$version->canVersionRemove() )
            return self::refused( $plan, 'version_not_removable' );
        return $plan;
    }

    // ------------------------------------------------------------------ execute

    /**
     * Plans the action again and does it when the plan is still the one confirmed.
     *
     * @param string $action
     * @param int $objectID
     * @param int $versionID
     * @param string $confirmToken the token of the confirmation page
     * @return array array( 'done' => bool, 'reason' => '' | a plan() reason | 'changed' | 'failed', 'plan' => the plan )
     */
    public static function execute( $action, $objectID, $versionID, $confirmToken )
    {
        $plan = self::plan( $action, $objectID, $versionID );
        if ( !$plan['allowed'] )
            return self::outcome( $plan, false, $plan['reason'] );
        if ( !self::checkConfirmToken( $confirmToken, $plan ) )
            return self::outcome( $plan, false, 'changed' );

        $auditData = array( 'object' => class_exists( 'expAuditHook' ) ? expAuditHook::object( (int)$objectID ) : array( 'type' => 'object', 'id' => (int)$objectID ),
                            'target' => self::target( $plan ),
                            'after' => array( 'action' => $plan['action'], 'locations' => $plan['locations'], 'children' => $plan['children'] ) );
        $parent = class_exists( 'expAuditHook' ) ? expAuditHook::begin( 'content.finder.action', $auditData ) : null;
        try
        {
            $run = function () use ( $plan ) { return self::run( $plan ); };
            $done = class_exists( 'expAuditHook' ) ? expAuditHook::withParent( $parent, $run ) : $run();
        }
        catch ( Throwable $e )
        {
            if ( $parent !== null )
                expAuditHook::end( $parent, array( 'result' => 'failed', 'reason' => 'error', 'error' => array( 'message' => get_class( $e ) ) ) );
            eZDebug::writeError( 'Content finder: ' . $plan['action'] . ' of object ' . (int)$objectID . ' failed: ' . $e->getMessage(), __METHOD__ );
            return self::outcome( $plan, false, 'failed' );
        }
        if ( $parent !== null )
            expAuditHook::end( $parent, $done ? array() : array( 'result' => 'failed', 'reason' => 'not_done' ) );
        return self::outcome( $plan, $done, $done ? '' : 'failed' );
    }

    /**
     * The audit record of a refused action (the view records it; a done action is recorded by execute()).
     *
     * @param array $plan
     * @param string $reason
     */
    public static function auditRefusal( array $plan, $reason )
    {
        if ( !class_exists( 'expAuditHook' ) )
            return;
        expAuditHook::emit( 'content.finder.action', function () use ( $plan, $reason ) {
            return array( 'object' => array( 'type' => 'object', 'id' => (int)$plan['object_id'] ),
                          'target' => self::target( $plan ),
                          'result' => 'refused', 'reason' => (string)$reason,
                          'after' => array( 'action' => $plan['action'] ) );
        } );
    }

    /**
     * @param array $plan
     * @return array
     */
    protected static function target( array $plan )
    {
        if ( $plan['action'] === 'removeversion' )
            return array( 'type' => 'version', 'id' => $plan['version'] ? (int)$plan['version']['id'] : 0,
                          'version' => $plan['version'] ? (int)$plan['version']['version'] : 0 );
        $ids = array();
        foreach ( $plan['action'] === 'purge' ? $plan['trash'] : $plan['nodes'] as $node )
            $ids[] = (int)$node['node_id'];
        return array( 'type' => $plan['action'] === 'purge' ? 'trash' : 'node', 'id' => implode( ',', $ids ) );
    }

    /**
     * @param array $plan
     * @param bool $done
     * @param string $reason
     * @return array
     */
    protected static function outcome( array $plan, $done, $reason )
    {
        return array( 'done' => (bool)$done, 'reason' => (string)$reason, 'plan' => $plan );
    }

    /**
     * Runs the kernel operation of a confirmed plan.
     *
     * @param array $plan
     * @return bool whether the object (or version) is gone from where it was
     */
    protected static function run( array $plan )
    {
        $objectID = (int)$plan['object_id'];
        switch ( $plan['action'] )
        {
            case 'trash':
            case 'delete':
                $nodeIDs = array();
                foreach ( $plan['nodes'] as $node )
                    $nodeIDs[] = (int)$node['node_id'];
                $moveToTrash = $plan['action'] === 'trash';
                if ( eZOperationHandler::operationIsAvailable( 'content_delete' ) )
                    eZOperationHandler::execute( 'content', 'delete', array( 'node_id_list' => $nodeIDs, 'move_to_trash' => $moveToTrash ), null, true );
                else
                    eZContentOperationCollection::deleteObject( $nodeIDs, $moveToTrash );
                eZContentObject::clearCache( array( $objectID ) );
                $after = eZContentObject::fetch( $objectID, true );
                if ( $moveToTrash )
                    return $after instanceof eZContentObject && $after->attribute( 'status' ) == eZContentObject::STATUS_ARCHIVED;
                return !$after instanceof eZContentObject;

            case 'purge':
                // the trash service purges any object it is given: only one that is still in the trash
                eZContentObject::clearCache( array( $objectID ) );
                $current = eZContentObject::fetch( $objectID );
                if ( !$current instanceof eZContentObject || (int)$current->attribute( 'status' ) !== eZContentObject::STATUS_ARCHIVED )
                    return false;
                if ( !class_exists( 'Exponential\\Service\\Trash' ) )
                    require_once 'kernel/private/classes/services/trash.php';
                \Exponential\Service\Trash::purgeObjects( array( $objectID ) );
                eZContentObject::clearCache( array( $objectID ) );
                return !eZContentObject::fetch( $objectID, true ) instanceof eZContentObject;

            case 'removeversion':
                $version = eZContentObjectVersion::fetch( (int)$plan['version']['id'] );
                if ( !$version instanceof eZContentObjectVersion )
                    return false;
                $removed = array( 'version' => (int)$version->attribute( 'version' ), 'status' => (int)$version->attribute( 'status' ),
                                  'language' => (string)$version->initialLanguageCode() );
                $db = eZDB::instance();
                $db->begin();
                $version->removeThis();
                $db->commit();
                // as content/history records it (doc/bc/6.0/audit.md, content.version.remove)
                if ( class_exists( 'expAuditHook' ) )
                    expAuditHook::emit( 'content.version.remove', function () use ( $objectID, $removed ) {
                        $o = expAuditHook::object( $objectID );
                        return array( 'object' => array( 'type' => 'version', 'id' => (string)$removed['version'], 'object_id' => $objectID,
                                                         'name' => isset( $o['name'] ) ? $o['name'] : null ),
                                      'before' => array( 'versions' => array( $removed ) ) );
                    } );
                return !eZContentObjectVersion::fetch( (int)$plan['version']['id'] ) instanceof eZContentObjectVersion;
        }
        return false;
    }
}

?>
