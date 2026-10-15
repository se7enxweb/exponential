<?php
/**
 * File containing the expContentCheckTrash class.
 *
 * Moves unreferenced objects to the trash (doc/features/6.0/content-checks.md, "Moving to the trash"): the list
 * posts the ticked objects, a confirmation page shows what will happen to each, and the confirmed objects go to the
 * trash through the kernel's own removal (the operation content/delete with move_to_trash, as content/removeobject
 * does it), which records each removal in the audit as content.node.remove.trash. Nothing is deleted permanently;
 * the objects can be restored from the trash.
 *
 * An object is moved only when, at the time of the confirmation and again when it is done:
 *   - it is a published asset and nothing uses it, judged against a reference index built at that moment
 *     (expContentCheckUnreferenced::unreferencedAmong(), expContentCheckReferences);
 *   - the user may read it and remove every one of its locations (content/read, content/remove);
 *   - none of its locations has sub-items (the action is for single objects; a container is removed the usual way);
 *   - the class allows the trash, it has no pending version and no content job holds it.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentCheckTrash
{
    /**
     * At most this many objects are moved in one confirmation.
     *
     * @return int
     */
    public static function batchLimit()
    {
        return expContentCheck::intSetting( 'UnreferencedSettings', 'MaxTrashBatch', 50, 1, 200 );
    }

    /**
     * What moving the given objects to the trash would do, object by object.
     *
     * @param int[] $objectIDs
     * @return array array( 'items' => array of array( 'object_id', 'name', 'class_name', 'path', 'node_ids', 'allowed',
     *               'reason' ), 'allowed' => object ids that would be moved, 'too_many' => bool, 'fingerprint' => string );
     *               reason: '' | not_found | not_published | referenced | not_removable | has_children | trash_not_allowed
     *               | pending | job_lock
     */
    public static function plan( array $objectIDs )
    {
        $limit = self::batchLimit();
        $requested = expContentCheck::idList( $objectIDs, $limit + 1 );
        $tooMany = count( $requested ) > $limit;
        $requested = array_slice( $requested, 0, $limit );

        $unreferenced = $requested ? array_flip( expContentCheckUnreferenced::unreferencedAmong( $requested ) ) : array();
        $objects = array();
        if ( $requested )
        {
            $fetched = eZContentObject::fetchIDArray( $requested );
            foreach ( is_array( $fetched ) ? $fetched : array() as $object )
                $objects[(int)$object->attribute( 'id' )] = $object;
        }

        $items = array();
        $allowed = array();
        foreach ( $requested as $objectID )
        {
            $item = self::planObject( $objectID, isset( $objects[$objectID] ) ? $objects[$objectID] : null, isset( $unreferenced[$objectID] ) );
            if ( $item['allowed'] )
                $allowed[] = $objectID;
            $items[] = $item;
        }
        sort( $allowed );
        return array( 'items' => $items, 'allowed' => $allowed, 'too_many' => $tooMany,
                      'fingerprint' => 'trash|' . implode( ',', $allowed ) );
    }

    /**
     * @param int $objectID
     * @param eZContentObject|null $object
     * @param bool $unreferenced
     * @return array
     */
    protected static function planObject( $objectID, $object, $unreferenced )
    {
        $item = array( 'object_id' => (int)$objectID, 'name' => '', 'class_name' => '', 'path' => '', 'node_ids' => array(),
                       'allowed' => false, 'reason' => '' );
        if ( !$object instanceof eZContentObject || !$object->canRead() )
            return self::refused( $item, 'not_found' );
        $item['name'] = (string)$object->attribute( 'name' );
        $class = $object->contentClass();
        $item['class_name'] = $class ? (string)$class->attribute( 'name' ) : '';
        $mainNode = $object->attribute( 'main_node' );
        $item['path'] = $mainNode instanceof eZContentObjectTreeNode ? (string)$mainNode->attribute( 'path_identification_string' ) : '';
        if ( (int)$object->attribute( 'status' ) !== eZContentObject::STATUS_PUBLISHED )
            return self::refused( $item, 'not_published' );
        if ( !$unreferenced )
            return self::refused( $item, 'referenced' );

        $nodes = $object->assignedNodes( true );
        $nodeIDs = array();
        foreach ( is_array( $nodes ) ? $nodes : array() as $node )
        {
            if ( !$node->canRead() || !$node->canRemove() )
                return self::refused( $item, 'not_removable' );
            // every sub-item counts, also a hidden one or one the user may not read
            if ( self::childCount( (int)$node->attribute( 'node_id' ) ) > 0 )
                return self::refused( $item, 'has_children' );
            $nodeIDs[] = (int)$node->attribute( 'node_id' );
        }
        if ( !$nodeIDs )
            return self::refused( $item, 'not_removable' );
        sort( $nodeIDs );
        $item['node_ids'] = $nodeIDs;

        $info = eZContentObjectTreeNode::subtreeRemovalInformation( $nodeIDs );
        if ( empty( $info['can_remove_all'] ) )
            return self::refused( $item, 'not_removable' );
        if ( !empty( $info['has_pending_object'] ) )
            return self::refused( $item, 'pending' );
        if ( empty( $info['move_to_trash'] ) )
            return self::refused( $item, 'trash_not_allowed' );
        if ( class_exists( 'expContentJobLock' ) )
        {
            try
            {
                if ( expContentJobLock::checkNodes( $nodeIDs ) )
                    return self::refused( $item, 'job_lock' );
            }
            catch ( Throwable $e )
            {
                eZDebug::writeError( 'Content jobs: ' . $e->getMessage(), __METHOD__ );
                return self::refused( $item, 'job_lock' );
            }
        }
        $item['allowed'] = true;
        return $item;
    }

    /**
     * The sub-items of a node, hidden ones and ones the user may not read included.
     *
     * @param int $nodeID
     * @return int
     */
    protected static function childCount( $nodeID )
    {
        $rows = eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS row_count FROM ezcontentobject_tree WHERE parent_node_id = ' . (int)$nodeID );
        return isset( $rows[0]['row_count'] ) ? (int)$rows[0]['row_count'] : 0;
    }

    /**
     * @param array $item
     * @param string $reason
     * @return array
     */
    protected static function refused( array $item, $reason )
    {
        $item['allowed'] = false;
        $item['reason'] = (string)$reason;
        return $item;
    }

    /**
     * Plans again and moves the objects to the trash when the plan is still the one confirmed: the same objects
     * allowed, for the same user (the token). Each object goes through the kernel's removal on its own, so one that
     * fails leaves the others done.
     *
     * @param int[] $objectIDs
     * @param mixed $confirmToken the token of the confirmation page
     * @return array array( 'done' => bool, 'reason' => '' | changed | nothing, 'moved' => object ids in the trash now,
     *               'failed' => object ids not moved, 'plan' => the plan )
     */
    public static function execute( array $objectIDs, $confirmToken )
    {
        $plan = self::plan( $objectIDs );
        $outcome = array( 'done' => false, 'reason' => '', 'moved' => array(), 'failed' => array(), 'plan' => $plan );
        if ( !$plan['allowed'] )
        {
            $outcome['reason'] = 'nothing';
            return $outcome;
        }
        if ( !expContentCheck::matches( $confirmToken, expContentCheck::confirmToken( $plan['fingerprint'] ) ) )
        {
            $outcome['reason'] = 'changed';
            return $outcome;
        }
        foreach ( $plan['items'] as $item )
        {
            if ( !$item['allowed'] )
                continue;
            if ( self::moveToTrash( $item ) )
                $outcome['moved'][] = $item['object_id'];
            else
                $outcome['failed'][] = $item['object_id'];
        }
        $outcome['done'] = count( $outcome['moved'] ) > 0;
        // what refers to what changed: the next list builds the reference index again
        if ( $outcome['moved'] )
            expContentCheckReferences::clearCache();
        return $outcome;
    }

    /**
     * Moves one object to the trash as content/removeobject does.
     *
     * @param array $item an allowed item of plan()
     * @return bool whether the object is in the trash now
     */
    protected static function moveToTrash( array $item )
    {
        $objectID = (int)$item['object_id'];
        try
        {
            if ( eZOperationHandler::operationIsAvailable( 'content_delete' ) )
                eZOperationHandler::execute( 'content', 'delete', array( 'node_id_list' => $item['node_ids'], 'move_to_trash' => true ), null, true );
            else
                eZContentOperationCollection::deleteObject( $item['node_ids'], true );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( 'Content checks: moving object ' . $objectID . ' to the trash failed: ' . $e->getMessage(), __METHOD__ );
            return false;
        }
        eZContentObject::clearCache( array( $objectID ) );
        $after = eZContentObject::fetch( $objectID, true );
        return $after instanceof eZContentObject && (int)$after->attribute( 'status' ) === eZContentObject::STATUS_ARCHIVED;
    }
}

?>
