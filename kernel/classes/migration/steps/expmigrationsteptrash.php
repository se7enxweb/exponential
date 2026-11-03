<?php
/**
 * File containing the expMigrationStepTrash class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "trash": moves content to the trash, restores it from there, and purges it for good.
 *
 *   - type: trash
 *     mode: move                       move, restore, purge
 *     node: "path:news/old-article"    the object of the node (with all its locations) goes to the trash
 *   - type: trash
 *     mode: restore
 *     object: remote:old-article       an object in the trash ...
 *     parent: "path:news"              ... restored below this node (default: where it was)
 *   - type: trash
 *     mode: purge
 *     object: remote:old-article       removed for good (or all: true empties the whole trash)
 *
 * A move runs the delete operation of the content module with "move to trash", as the administration's remove; a
 * restore does what content/restore does (the object's version placed again, published through the publish
 * operation, its trash entry removed, its reverse relations fixed); a purge goes through the trash service of the
 * trash view (Exponential\Service\Trash). Each checks afterwards that the object is where the step put it.
 *
 * A move undoes itself (the object is restored to where it was), and a restore too (it goes back to the trash); a
 * purge does not.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepTrash extends expMigrationStepContentBase implements expMigrationPartlyRevertibleStepType
{
    /** @return string */
    public function name()
    {
        return 'trash';
    }

    /** @return array */
    public function schema()
    {
        return array(
            'description' => 'Moves content to the trash, restores it from the trash, or purges it for good',
            'modes' => array( 'move', 'restore', 'purge' ),
            'keys' => array(
                'object' => array( 'type' => 'identifier', 'kind' => 'object',
                                   'description' => 'the object: move puts it in the trash; restore and purge take it from there (its id or remote:<remote id>)' ),
                'node' => array( 'type' => 'identifier', 'kind' => 'node', 'modes' => array( 'move' ),
                                 'description' => 'a node of the object that goes to the trash (or object:)' ),
                'parent' => array( 'type' => 'identifier', 'kind' => 'node', 'modes' => array( 'restore' ),
                                   'description' => 'the node a restored object is placed below (default: the parent it had, when it is still there)' ),
                'all' => array( 'type' => 'bool', 'default' => false, 'modes' => array( 'purge' ),
                                'description' => 'true: the whole trash is emptied (instead of object:)' ),
                'missing' => array( 'type' => 'string', 'default' => 'fail', 'values' => array( 'fail', 'skip' ),
                                    'description' => 'what happens when the object is not there (move) or not in the trash (restore, purge): fail (the default) or skip' ),
            ) + self::operationKeys( array( 'move', 'restore' ), false ),
            'values' => array(
                'object_id' => 'the id of the object',
                'remote_id' => 'its remote id',
                'name' => 'its name',
                'node_id' => 'its main node after a restore (null otherwise)',
                'purged' => 'how many objects a purge removed',
                'changed' => 'true when the step changed something',
            ),
            'example' => "- type: trash\n  mode: move\n  node: \"path:news/old-article\"\n"
                       . "- type: trash\n  mode: restore\n  object: remote:old-article\n  parent: \"path:archive\"\n",
        );
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function validate( array $step )
    {
        $mode = isset( $step['mode'] ) ? $step['mode'] : null;
        $errors = array();
        if ( $mode === 'move' && isset( $step['object'] ) === isset( $step['node'] ) )
            $errors[] = 'name what goes to the trash by object: or by node: (one of them)';
        if ( $mode === 'restore' && !isset( $step['object'] ) )
            $errors[] = 'object is missing: the object in the trash (mode restore)';
        if ( $mode === 'purge' && isset( $step['object'] ) === !empty( $step['all'] ) )
            $errors[] = 'name what is purged by object: or by all: true (one of them)';
        if ( isset( $step['object'] ) && is_string( $step['object'] ) && strpos( trim( $step['object'] ), 'path:' ) === 0 && $mode !== 'move' )
            $errors[] = 'object: an object in the trash has no path; name it by its id or remote:<remote id>';
        return $errors;
    }

    /**
     * @param array $step
     * @return bool
     */
    public function revertible( array $step )
    {
        return isset( $step['mode'] ) && in_array( $step['mode'], array( 'move', 'restore' ), true );
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( array $step, $context )
    {
        $what = isset( $step['object'] ) ? 'the object ' . self::named( $step['object'] ) : 'the object of node ' . self::named( isset( $step['node'] ) ? $step['node'] : null );
        switch ( $step['mode'] )
        {
            case 'move':
                return array( "move $what to the trash, with all its locations" );
            case 'restore':
                return array( "restore $what from the trash below " . ( isset( $step['parent'] ) ? 'node ' . self::named( $step['parent'] ) : 'the parent it had' ) );
            case 'purge':
                return array( !empty( $step['all'] ) ? 'empty the trash: every object in it is removed for good' : "purge $what from the trash for good" );
        }
        return parent::plan( $step, $context );
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return array
     */
    public function apply( array $step, $context )
    {
        switch ( $step['mode'] )
        {
            case 'move':
                $object = self::findObject( $step );
                if ( $object === null || (int)$object->attribute( 'status' ) !== eZContentObject::STATUS_PUBLISHED )
                    return $this->missing( $step, $object === null ? 'is not on this installation' : 'is not published (in the trash already?)' );
                return $this->move( $object, $step );
            case 'restore':
                $object = self::trashed( $step['object'] );
                if ( $object === null )
                    return $this->missing( $step, 'is not in the trash' );
                return $this->restore( $object, $step );
            case 'purge':
                if ( !empty( $step['all'] ) )
                    return $this->purgeAll();
                $object = self::trashed( $step['object'] );
                if ( $object === null )
                    return $this->missing( $step, 'is not in the trash' );
                return $this->purge( $object );
        }
        throw new RuntimeException( 'the mode ' . $step['mode'] . ' is not one of move, restore, purge' );
    }

    /**
     * A move: the object restored to where it was. A restore: the object back to the trash.
     *
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function revert( array $step, $context )
    {
        if ( !$this->revertible( $step ) )
            throw new RuntimeException( 'a purge cannot be undone; restore the caption taken before the migration' );
        if ( $step['mode'] === 'move' )
        {
            // the node of node: is gone with the object: only an object named by object: is found again
            if ( !isset( $step['object'] ) )
                throw new RuntimeException( 'a move named by node: cannot find its object in the trash again; name it by object: or give the migration a down:' );
            $object = self::trashed( $step['object'] );
            if ( $object === null )
                return array( 'the object ' . self::named( $step['object'] ) . ' is not in the trash: nothing to undo' );
            $result = $this->restore( $object, array( 'parent' => null ) + $step );
            return $result['lines'];
        }
        $id = expMigrationIdentifier::find( 'object', $step['object'] );
        $object = $id !== null ? eZContentObject::fetch( $id ) : null;
        if ( !$object instanceof eZContentObject || (int)$object->attribute( 'status' ) !== eZContentObject::STATUS_PUBLISHED )
            return array( 'the object ' . self::named( $step['object'] ) . ' is not published: nothing to undo' );
        $result = $this->move( $object, $step );
        return $result['lines'];
    }

    /**
     * @param eZContentObject $object
     * @param array $step
     * @return array
     */
    protected function move( eZContentObject $object, array $step )
    {
        $id = (int)$object->attribute( 'id' );
        $values = self::values( $object );
        $runAs = eZUser::currentUser();
        $runAsId = (int)eZUser::currentUserID();
        $nodeIds = array();
        foreach ( (array)eZContentObjectTreeNode::fetchByContentObjectID( $id ) as $node )
            $nodeIds[] = (int)$node->attribute( 'node_id' );
        if ( !$nodeIds )
            throw new RuntimeException( "the object $id has no location to remove" );
        self::operation( 'delete', array( 'node_id_list' => $nodeIds, 'move_to_trash' => true ), $step );
        if ( (int)eZUser::currentUserID() !== $runAsId && $runAsId !== $id && $runAs instanceof eZUser )
            eZUser::setCurrentlyLoggedInUser( $runAs, $runAsId, eZUser::NO_SESSION_REGENERATE );
        eZContentObject::clearCache( array( $id ) );
        $row = eZContentObject::fetch( $id, false );
        if ( !is_array( $row ) || (int)$row['status'] !== eZContentObject::STATUS_ARCHIVED )
            throw new RuntimeException( "the object $id \"" . $values['name'] . '" was not moved to the trash (may the user the migration runs as remove it?)' );
        return self::result( array( "moved the object $id \"" . $values['name'] . '" to the trash (' . count( $nodeIds ) . ' location(s))' ),
                             $values + array( 'node_id' => null, 'purged' => 0, 'changed' => true ) );
    }

    /**
     * Places an object of the trash again and publishes it, as content/restore does.
     *
     * @param eZContentObject $object
     * @param array $step
     * @return array
     */
    protected function restore( eZContentObject $object, array $step )
    {
        $id = (int)$object->attribute( 'id' );
        if ( isset( $step['parent'] ) )
        {
            $parentId = expMigrationIdentifier::resolve( 'node', $step['parent'] );
        }
        else
        {
            $trashNode = eZContentObjectTrashNode::fetchByContentObjectID( $id );
            $original = $trashNode instanceof eZContentObjectTrashNode ? $trashNode->originalParent() : null;
            if ( !$original instanceof eZContentObjectTreeNode )
                throw new RuntimeException( "the parent the object $id had is gone or moved: name the node to restore it below with parent:" );
            $parentId = (int)$original->attribute( 'node_id' );
        }
        self::node( $parentId );
        $version = $object->attribute( 'current' );
        if ( !$version instanceof eZContentObjectVersion )
            throw new RuntimeException( "the object $id has no current version to restore" );
        $db = eZDB::instance();
        $db->begin();
        try
        {
            foreach ( (array)$version->attribute( 'node_assignments' ) as $assignment )
                $assignment->purge();
            $version->assignToNode( $parentId, 1 );
            $object->setAttribute( 'status', eZContentObject::STATUS_DRAFT );
            $object->store();
            $version->setAttribute( 'status', eZContentObjectVersion::STATUS_DRAFT );
            $version->store();
            $object->restoreObjectAttributes();
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        // the publish of a restore is the restore, as content/restore audits it
        $publish = function () use ( $id, $version, $step ) {
            return self::operation( 'publish', array( 'object_id' => $id, 'version' => (int)$version->attribute( 'version' ) ), $step );
        };
        if ( class_exists( 'expAuditHook' ) )
            expAuditHook::muted( array( 'content.object.create', 'content.object.publish', 'content.object.translate' ), $publish );
        else
            $publish();
        $db->begin();
        try
        {
            eZContentObjectTrashNode::purgeForObject( $id );
            eZContentObject::fixReverseRelations( $id, 'restore', false );
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        eZContentObject::clearCache( array( $id ) );
        $object = eZContentObject::fetch( $id );
        if ( !$object instanceof eZContentObject || (int)$object->attribute( 'status' ) !== eZContentObject::STATUS_PUBLISHED || !(int)$object->attribute( 'main_node_id' ) )
            throw new RuntimeException( "the object $id was not restored" );
        if ( (int)$object->attribute( 'contentclass_id' ) === (int)eZINI::instance()->variable( 'UserSettings', 'UserClassID' ) )
            eZUser::purgeUserCacheByUserId( $id );
        eZContentCacheManager::clearContentCacheIfNeeded( $id, (int)$version->attribute( 'version' ) );
        $mainNodeId = (int)$object->attribute( 'main_node_id' );
        return self::result( array( "restored the object $id \"" . $object->attribute( 'name' ) . "\" below node $parentId (node $mainNodeId)" ),
                             self::values( $object ) + array( 'node_id' => $mainNodeId, 'purged' => 0, 'changed' => true ) );
    }

    /**
     * @param eZContentObject $object
     * @return array
     */
    protected function purge( eZContentObject $object )
    {
        $id = (int)$object->attribute( 'id' );
        $values = self::values( $object );
        $purged = \Exponential\Service\Trash::purgeObjects( array( $id ) );
        eZContentObject::clearCache( array( $id ) );
        if ( eZContentObject::fetch( $id, false ) )
            throw new RuntimeException( "the object $id was not purged" );
        return self::result( array( "purged the object $id \"" . $values['name'] . '" from the trash' ),
                             $values + array( 'node_id' => null, 'purged' => (int)$purged, 'changed' => true ) );
    }

    /**
     * @return array
     */
    protected function purgeAll()
    {
        $result = \Exponential\Service\Trash::emptyTrash( 100, 0 );
        $purged = is_array( $result ) ? (int)$result['purged'] : (int)$result;
        eZContentObject::clearCache();
        $left = eZContentObject::fetchObjectList( eZContentObject::definition(), array( 'id' ), array( 'status' => eZContentObject::STATUS_ARCHIVED ),
                                                  null, array( 'limit' => 1, 'offset' => 0 ), false );
        if ( ( is_array( $result ) && empty( $result['ok'] ) ) || $left )
            throw new RuntimeException( 'the trash was not emptied' );
        return self::result( array( $purged ? "emptied the trash: $purged object(s) purged" : 'the trash is empty: nothing to purge' ),
                             array( 'object_id' => null, 'remote_id' => null, 'name' => null, 'node_id' => null, 'purged' => $purged, 'changed' => $purged > 0 ) );
    }

    /**
     * An object in the trash (archived), or null.
     *
     * @param mixed $identifier
     * @return eZContentObject|null
     */
    protected static function trashed( $identifier )
    {
        $id = expMigrationIdentifier::find( 'object', $identifier );
        if ( $id === null )
            return null;
        eZContentObject::clearCache( array( $id ) );
        $object = eZContentObject::fetch( $id );
        return $object instanceof eZContentObject && (int)$object->attribute( 'status' ) === eZContentObject::STATUS_ARCHIVED ? $object : null;
    }

    /**
     * @param eZContentObject $object
     * @return array
     */
    protected static function values( eZContentObject $object )
    {
        return array( 'object_id' => (int)$object->attribute( 'id' ), 'remote_id' => (string)$object->attribute( 'remote_id' ),
                      'name' => (string)$object->attribute( 'name' ) );
    }

    /**
     * @param array $step
     * @param string $why
     * @return array
     * @throws RuntimeException unless missing: skip
     */
    protected function missing( array $step, $why )
    {
        $what = isset( $step['object'] ) ? 'the object ' . self::named( $step['object'] ) : ( isset( $step['node'] ) ? 'the object of node ' . self::named( $step['node'] ) : 'the object' );
        if ( !isset( $step['missing'] ) || $step['missing'] !== 'skip' )
            throw new RuntimeException( "$what $why (missing: skip lets the step pass)" );
        return self::result( array( "$what $why: skipped (missing: skip)" ),
                             array( 'object_id' => null, 'remote_id' => null, 'name' => null, 'node_id' => null, 'purged' => 0, 'changed' => false ) );
    }
}
