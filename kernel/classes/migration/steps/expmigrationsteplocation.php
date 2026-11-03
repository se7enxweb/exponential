<?php
/**
 * File containing the expMigrationStepLocation class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "location": the nodes (locations) of content objects.
 *
 *   - type: location
 *     mode: move                       create (add a location), update, delete, move, copy, hide, unhide
 *     node: "${ref.welcome}"
 *     parent: path:archive
 *
 * Every mode runs the operation the administration runs for it (addlocation, removelocation, delete, move, hide,
 * sort, updatepriority, updatemainassignment), without the workflows of its trigger unless "workflows: true".
 * "copy" copies the object of a node (its published version) below another parent, as the administration's copy does.
 *
 * Steps of the modes create, hide and unhide can undo themselves (the location is removed again, the node shown or
 * hidden again); the other modes need a "down:" of their migration.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepLocation extends expMigrationStepContentBase implements expMigrationPartlyRevertibleStepType
{
    /** The sort fields a node can have (eZContentObjectTreeNode::sortFieldID()). */
    const SORT_FIELDS = array( 'path', 'published', 'modified', 'section', 'depth', 'class_identifier', 'class_name', 'priority', 'name' );

    /** @return string */
    public function name()
    {
        return 'location';
    }

    /** @return array */
    public function schema()
    {
        $placing = array( 'create', 'update' );
        return array(
            'description' => 'Adds, changes, removes, moves, copies, hides or shows a location (node) of a content object',
            'modes' => array( 'create', 'update', 'delete', 'move', 'copy', 'hide', 'unhide' ),
            'keys' => array(
                'node' => array( 'type' => 'identifier', 'kind' => 'node', 'modes' => array( 'update', 'delete', 'move', 'copy', 'hide', 'unhide' ),
                                 'description' => 'the node the step acts on' ),
                'object' => array( 'type' => 'identifier', 'kind' => 'object', 'modes' => array( 'create', 'delete' ),
                                   'description' => 'create: the object that gets a new location; delete: with parent:, its location below that parent' ),
                'parent' => array( 'type' => 'identifier', 'kind' => 'node', 'modes' => array( 'create', 'delete', 'move', 'copy' ),
                                   'description' => 'the parent node: of the new location (create), of the location to remove (delete with object:), where the node goes (move, copy)' ),
                'priority' => array( 'type' => 'int', 'modes' => $placing, 'description' => 'the priority of the node among its siblings' ),
                'sort_field' => array( 'type' => 'string', 'values' => self::SORT_FIELDS, 'modes' => $placing,
                                       'description' => 'how the children of the node are sorted' ),
                'sort_order' => array( 'type' => 'string', 'values' => array( 'asc', 'desc' ), 'modes' => $placing,
                                       'description' => 'the direction of sort_field' ),
                'main' => array( 'type' => 'bool', 'modes' => $placing, 'description' => 'true: the node becomes the main location of its object' ),
                'missing' => array( 'type' => 'string', 'default' => 'fail', 'values' => array( 'fail', 'skip' ), 'modes' => array( 'update', 'delete', 'move', 'hide', 'unhide' ),
                                    'description' => 'what happens when the node is not there: fail (the default) or skip' ),
                'trash' => array( 'type' => 'bool', 'default' => false, 'modes' => array( 'delete' ),
                                  'description' => 'when the node is the object\'s last location, the object is deleted with it: true puts it in the trash' ),
            ) + self::operationKeys( array(), false ),
            'values' => array(
                'node_id' => 'the id of the node (the new one for create and copy)',
                'object_id' => 'the id of its object (the new one for copy)',
                'parent_node_id' => 'the id of its parent node',
                'remote_id' => 'the remote id of the node',
                'path' => 'its url path (path_identification_string)',
                'hidden' => 'whether it is hidden',
            ),
            'example' => "- type: location\n  mode: create\n  object: remote:news-welcome\n  parent: path:frontpage\n  priority: 10\n  references: { welcome_on_front: node_id }\n",
        );
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function validate( array $step )
    {
        $errors = array();
        $mode = isset( $step['mode'] ) ? $step['mode'] : null;
        switch ( $mode )
        {
            case 'create':
                foreach ( array( 'object', 'parent' ) as $key )
                    if ( !isset( $step[$key] ) )
                        $errors[] = "$key is missing (mode create)";
                break;
            case 'delete':
                if ( isset( $step['node'] ) === ( isset( $step['object'] ) && isset( $step['parent'] ) ) || ( isset( $step['object'] ) !== isset( $step['parent'] ) ) )
                    $errors[] = 'name the location by node:, or by object: and parent: (mode delete)';
                break;
            case 'move':
            case 'copy':
                foreach ( array( 'node', 'parent' ) as $key )
                    if ( !isset( $step[$key] ) )
                        $errors[] = "$key is missing (mode $mode)";
                break;
            case 'update':
            case 'hide':
            case 'unhide':
                if ( !isset( $step['node'] ) )
                    $errors[] = "node is missing (mode $mode)";
                if ( $mode === 'update' && !array_intersect( array( 'priority', 'sort_field', 'sort_order', 'main' ), array_keys( $step ) ) )
                    $errors[] = 'nothing to update: priority, sort_field, sort_order or main';
                break;
        }
        if ( isset( $step['sort_order'] ) && !isset( $step['sort_field'] ) )
            $errors[] = 'sort_order needs sort_field';
        return $errors;
    }

    /**
     * @param array $step
     * @return bool
     */
    public function revertible( array $step )
    {
        return isset( $step['mode'] ) && in_array( $step['mode'], array( 'create', 'hide', 'unhide' ), true );
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function caches( array $step )
    {
        return array( 'content', 'template-block' );
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( array $step, $context )
    {
        $node = isset( $step['node'] ) ? 'node ' . self::named( $step['node'] ) : '';
        $parent = isset( $step['parent'] ) ? self::named( $step['parent'] ) : '';
        switch ( $step['mode'] )
        {
            case 'create':
                return array( 'add a location of the object ' . self::named( $step['object'] ) . " below node $parent" . $this->placingText( $step ) );
            case 'update':
                return array( "change $node:" . $this->placingText( $step ) );
            case 'delete':
                return array( 'remove ' . ( $node !== '' ? $node : 'the location of the object ' . self::named( $step['object'] ) . " below node $parent" )
                              . ' (the object is deleted with its last location' . ( !empty( $step['trash'] ) ? ', to the trash)' : ')' ) );
            case 'move':
                return array( "move $node below node $parent" );
            case 'copy':
                return array( "copy the object of $node below node $parent" );
            case 'hide':
            case 'unhide':
                return array( ( $step['mode'] === 'hide' ? 'hide ' : 'show ' ) . $node . ' and its subtree' );
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
        $mode = $step['mode'];
        if ( $mode === 'create' )
            return $this->create( $step );
        if ( $mode === 'delete' && !isset( $step['node'] ) )
        {
            $objectId = expMigrationIdentifier::find( 'object', $step['object'] );
            $parentId = expMigrationIdentifier::find( 'node', $step['parent'] );
            $nodeId = $objectId !== null && $parentId !== null ? self::nodeBelow( $objectId, $parentId ) : null;
        }
        else
        {
            $nodeId = expMigrationIdentifier::find( 'node', $step['node'] );
        }
        if ( $nodeId === null )
        {
            if ( $mode === 'copy' || !isset( $step['missing'] ) || $step['missing'] !== 'skip' )
                throw new RuntimeException( 'the ' . ( isset( $step['node'] ) ? 'node ' . self::named( $step['node'] ) : 'location' ) . ' is not on this installation'
                                            . ( $mode === 'copy' ? '' : ' (missing: skip lets the step pass)' ) );
            return self::result( array( 'the node is not there: skipped (missing: skip)' ),
                                 array( 'node_id' => null, 'object_id' => null, 'parent_node_id' => null, 'remote_id' => null, 'path' => null, 'hidden' => null ) );
        }
        $node = self::node( $nodeId );
        switch ( $mode )
        {
            case 'update':
                $lines = $this->place( $node, $step );
                return self::result( $lines, self::nodeValues( self::node( $nodeId ) ) );
            case 'delete':
                return $this->remove( $node, $step );
            case 'move':
                return $this->move( $node, expMigrationIdentifier::resolve( 'node', $step['parent'] ), $step );
            case 'copy':
                return $this->copy( $node, expMigrationIdentifier::resolve( 'node', $step['parent'] ), $step );
            case 'hide':
            case 'unhide':
                return self::result( $this->visibility( $node, $mode === 'hide', $step ), self::nodeValues( self::node( $nodeId ) ) );
        }
        throw new RuntimeException( "the mode $mode is not one of create, update, delete, move, copy, hide, unhide" );
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function revert( array $step, $context )
    {
        switch ( isset( $step['mode'] ) ? $step['mode'] : '' )
        {
            case 'create':
                $objectId = expMigrationIdentifier::find( 'object', $step['object'] );
                $parentId = expMigrationIdentifier::find( 'node', $step['parent'] );
                $nodeId = $objectId !== null && $parentId !== null ? self::nodeBelow( $objectId, $parentId ) : null;
                if ( $nodeId === null )
                    return array( 'the location below node ' . self::named( $step['parent'] ) . ' is not there: nothing to undo' );
                $result = $this->remove( self::node( $nodeId ), array( 'trash' => false ) + $step );
                return $result['lines'];
            case 'hide':
            case 'unhide':
                $nodeId = expMigrationIdentifier::find( 'node', $step['node'] );
                if ( $nodeId === null )
                    return array( 'the node ' . self::named( $step['node'] ) . ' is not there: nothing to undo' );
                return $this->visibility( self::node( $nodeId ), $step['mode'] !== 'hide', $step );
        }
        throw new RuntimeException( 'only the modes create, hide and unhide can be undone by themselves; give the migration a down:' );
    }

    // ---- the modes ---------------------------------------------------------------------------------------------

    /**
     * @param array $step
     * @return array
     */
    protected function create( array $step )
    {
        $objectId = expMigrationIdentifier::resolve( 'object', $step['object'] );
        $parentId = expMigrationIdentifier::resolve( 'node', $step['parent'] );
        $existing = self::nodeBelow( $objectId, $parentId );
        $lines = array();
        if ( $existing !== null )
        {
            $lines[] = "the object $objectId has a location below node $parentId already (node $existing)";
            $nodeId = $existing;
        }
        else
        {
            $object = eZContentObject::fetch( $objectId );
            $main = $object instanceof eZContentObject ? (int)$object->attribute( 'main_node_id' ) : 0;
            self::operation( 'addlocation', array( 'node_id' => $main, 'object_id' => $objectId, 'select_node_id_array' => array( $parentId ) ), $step );
            $nodeId = self::nodeBelow( $objectId, $parentId );
            if ( $nodeId === null )
                throw new RuntimeException( "the object $objectId got no location below node $parentId" );
            $lines[] = "added the location $nodeId of the object $objectId below node $parentId";
        }
        $lines = array_merge( $lines, $this->place( self::node( $nodeId ), $step ) );
        return self::result( $lines, self::nodeValues( self::node( $nodeId ) ) );
    }

    /**
     * Priority, sorting and main location of a node.
     *
     * @param eZContentObjectTreeNode $node
     * @param array $step
     * @return string[]
     */
    protected function place( eZContentObjectTreeNode $node, array $step )
    {
        $nodeId = (int)$node->attribute( 'node_id' );
        $lines = array();
        if ( isset( $step['priority'] ) && (int)$node->attribute( 'priority' ) !== (int)$step['priority'] )
        {
            self::operation( 'updatepriority', array( 'node_id' => (int)$node->attribute( 'parent_node_id' ), 'priority' => array( (int)$step['priority'] ),
                                                      'priority_id' => array( $nodeId ) ), $step );
            $lines[] = "set the priority of node $nodeId to " . (int)$step['priority'];
        }
        if ( isset( $step['sort_field'] ) )
        {
            $field = eZContentObjectTreeNode::sortFieldID( (string)$step['sort_field'] );
            $order = isset( $step['sort_order'] ) ? ( $step['sort_order'] === 'asc' ? 1 : 0 ) : (int)$node->attribute( 'sort_order' );
            if ( (int)$node->attribute( 'sort_field' ) !== (int)$field || (int)$node->attribute( 'sort_order' ) !== $order )
            {
                self::operation( 'sort', array( 'node_id' => $nodeId, 'sorting_field' => $field, 'sorting_order' => $order ), $step );
                $lines[] = "sorted the children of node $nodeId by " . $step['sort_field'] . ' ' . ( $order ? 'asc' : 'desc' );
            }
        }
        if ( !empty( $step['main'] ) && (int)$node->attribute( 'main_node_id' ) !== $nodeId )
        {
            self::operation( 'updatemainassignment', array( 'main_assignment_id' => $nodeId, 'object_id' => (int)$node->attribute( 'contentobject_id' ),
                                                            'main_assignment_parent_id' => (int)$node->attribute( 'parent_node_id' ) ), $step );
            $lines[] = "made node $nodeId the main location of the object " . (int)$node->attribute( 'contentobject_id' );
        }
        if ( !$lines )
            $lines[] = "node $nodeId is as the step has it";
        return $lines;
    }

    /**
     * Removes a location; the object goes with its last one.
     *
     * @param eZContentObjectTreeNode $node
     * @param array $step
     * @return array
     */
    protected function remove( eZContentObjectTreeNode $node, array $step )
    {
        $values = self::nodeValues( $node );
        $nodeId = $values['node_id'];
        $locations = count( (array)eZContentObjectTreeNode::fetchByContentObjectID( $values['object_id'] ) );
        if ( $locations > 1 )
        {
            self::operation( 'removelocation', array( 'node_list' => array( $nodeId ) ), $step );
            $line = "removed the location $nodeId of the object " . $values['object_id'];
        }
        else
        {
            self::operation( 'delete', array( 'node_id_list' => array( $nodeId ), 'move_to_trash' => !empty( $step['trash'] ) ), $step );
            $line = "removed node $nodeId, the last location of the object " . $values['object_id'] . ', and the object' . ( !empty( $step['trash'] ) ? ' (to the trash)' : '' );
        }
        eZContentObject::clearCache( array( $values['object_id'] ) );
        return self::result( array( $line ), $values );
    }

    /**
     * @param eZContentObjectTreeNode $node
     * @param int $parentId
     * @param array $step
     * @return array
     */
    protected function move( eZContentObjectTreeNode $node, $parentId, array $step )
    {
        $nodeId = (int)$node->attribute( 'node_id' );
        if ( (int)$node->attribute( 'parent_node_id' ) === (int)$parentId )
            return self::result( array( "node $nodeId is below node $parentId already" ), self::nodeValues( $node ) );
        if ( in_array( (string)$nodeId, explode( '/', trim( (string)self::node( $parentId )->attribute( 'path_string' ), '/' ) ), true ) )
            throw new RuntimeException( "node $nodeId cannot move below node $parentId, which is in its own subtree" );
        if ( self::nodeBelow( (int)$node->attribute( 'contentobject_id' ), $parentId ) !== null )
            throw new RuntimeException( 'the object ' . $node->attribute( 'contentobject_id' ) . " has a location below node $parentId already" );
        self::operation( 'move', array( 'node_id' => $nodeId, 'object_id' => (int)$node->attribute( 'contentobject_id' ), 'new_parent_node_id' => (int)$parentId ), $step );
        $moved = self::node( $nodeId );
        if ( (int)$moved->attribute( 'parent_node_id' ) !== (int)$parentId )
            throw new RuntimeException( "node $nodeId was not moved below node $parentId" );
        return self::result( array( "moved node $nodeId below node $parentId" ), self::nodeValues( $moved ) );
    }

    /**
     * Copies the object of a node (its published version, all languages) below another parent, as the copy of the
     * administration and of WebDAV does.
     *
     * @param eZContentObjectTreeNode $node
     * @param int $parentId
     * @param array $step
     * @return array
     */
    protected function copy( eZContentObjectTreeNode $node, $parentId, array $step )
    {
        $object = $node->object();
        if ( !$object instanceof eZContentObject )
            throw new RuntimeException( 'node ' . $node->attribute( 'node_id' ) . ' has no object' );
        $parent = self::node( $parentId );
        $db = eZDB::instance();
        $db->begin();
        try
        {
            $copy = $object->copy( false );
            $copy->setAttribute( 'section_id', 0 );
            $copy->store();
            $version = $copy->attribute( 'current' );
            foreach ( (array)$version->attribute( 'node_assignments' ) as $assignment )
                $assignment->purge();
            $assignment = eZNodeAssignment::create( array( 'contentobject_id' => $copy->attribute( 'id' ),
                                                           'contentobject_version' => $copy->attribute( 'current_version' ),
                                                           'parent_node' => (int)$parentId,
                                                           'is_main' => 1,
                                                           'sort_field' => $node->attribute( 'sort_field' ),
                                                           'sort_order' => $node->attribute( 'sort_order' ) ) );
            $assignment->store();
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        $copy = self::publish( $copy->attribute( 'id' ), $copy->attribute( 'current_version' ), $step );
        $newNode = $copy->attribute( 'main_node' );
        if ( !$newNode instanceof eZContentObjectTreeNode )
            throw new RuntimeException( 'the copy of the object ' . $object->attribute( 'id' ) . ' has no node' );
        eZContentObjectTreeNode::updateNodeVisibility( $newNode, $parent );
        return self::result( array( 'copied the object ' . $object->attribute( 'id' ) . ' of node ' . $node->attribute( 'node_id' ) . ' below node ' . $parentId
                                    . ': object ' . $copy->attribute( 'id' ) . ', node ' . $newNode->attribute( 'node_id' ) ),
                             self::nodeValues( self::node( (int)$newNode->attribute( 'node_id' ) ) ) );
    }

    /**
     * Hides or shows a node.
     *
     * @param eZContentObjectTreeNode $node
     * @param bool $hide
     * @param array $step
     * @return string[]
     */
    protected function visibility( eZContentObjectTreeNode $node, $hide, array $step )
    {
        $nodeId = (int)$node->attribute( 'node_id' );
        if ( (bool)$node->attribute( 'is_hidden' ) === (bool)$hide )
            return array( "node $nodeId is " . ( $hide ? 'hidden' : 'shown' ) . ' already' );
        self::operation( 'hide', array( 'node_id' => $nodeId ), $step );
        return array( ( $hide ? 'hid' : 'showed' ) . " node $nodeId" );
    }

    /**
     * @param array $step
     * @return string
     */
    protected function placingText( array $step )
    {
        $parts = array();
        if ( isset( $step['priority'] ) )
            $parts[] = 'priority ' . self::named( $step['priority'] );
        if ( isset( $step['sort_field'] ) )
            $parts[] = 'children sorted by ' . $step['sort_field'] . ( isset( $step['sort_order'] ) ? ' ' . $step['sort_order'] : '' );
        if ( !empty( $step['main'] ) )
            $parts[] = 'the main location';
        return $parts ? ', ' . implode( ', ', $parts ) : '';
    }
}
