<?php
/**
 * File containing the expMigrationStepContentClassGroup class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "content_class_group": creates, renames, ensures and deletes a group of content classes.
 *
 *   - type: content_class_group
 *     mode: ensure                     create, update (rename), ensure (create when missing), delete
 *     name: News
 *
 * A group is named by its name (class groups have no identifier). A rename changes the name the classes of the group
 * carry too, as the administration does. A group that still has classes is not deleted. A create undoes itself (the
 * group is removed again while it has no classes).
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepContentClassGroup extends expMigrationStepStructureBase implements expMigrationPartlyRevertibleStepType
{
    /** @return string */
    public function name()
    {
        return 'content_class_group';
    }

    /** @return array */
    public function schema()
    {
        return array(
            'description' => 'Creates, renames, ensures or deletes a group of content classes',
            'modes' => array( 'create', 'update', 'ensure', 'delete' ),
            'keys' => array(
                'group' => array( 'type' => 'identifier', 'kind' => 'class_group', 'modes' => array( 'update', 'delete' ),
                                  'description' => 'the group to rename or delete: its name or id' ),
                'name' => array( 'type' => 'string', 'modes' => array( 'create', 'update', 'ensure' ),
                                 'description' => 'the name of the group (update: its new name)' ),
            ) + self::missingKey( array( 'update', 'delete' ), 'the group' ),
            'values' => array(
                'group_id' => 'the id of the group',
                'name' => 'its name',
                'created' => 'true when the step created the group',
                'changed' => 'true when the step changed it (created, renamed or deleted)',
            ),
            'example' => "- type: content_class_group\n  mode: ensure\n  name: News\n  references: { news_group: group_id }\n",
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
        if ( $mode !== 'delete' && ( !isset( $step['name'] ) || trim( (string)$step['name'] ) === '' ) )
            $errors[] = "name is missing (mode $mode)";
        if ( in_array( $mode, array( 'update', 'delete' ), true ) && !isset( $step['group'] ) )
            $errors[] = "group is missing (mode $mode)";
        return $errors;
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function caches( array $step )
    {
        return array( 'classid' );
    }

    /**
     * @param array $step
     * @return bool
     */
    public function revertible( array $step )
    {
        return isset( $step['mode'] ) && $step['mode'] === 'create';
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( array $step, $context )
    {
        switch ( $step['mode'] )
        {
            case 'create':
                return array( 'create the class group "' . $step['name'] . '"' );
            case 'ensure':
                return array( 'ensure the class group "' . $step['name'] . '": create it when missing' );
            case 'update':
                return array( 'rename the class group ' . expMigrationStepSchema::show( $step['group'] ) . ' to "' . $step['name'] . '"' );
            case 'delete':
                return array( 'delete the class group ' . expMigrationStepSchema::show( $step['group'] ) . ' (only when it has no classes)' );
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
        if ( $mode === 'create' || $mode === 'ensure' )
        {
            $existing = eZContentClassGroup::fetchByName( (string)$step['name'] );
            if ( $existing instanceof eZContentClassGroup )
            {
                if ( $mode === 'create' )
                    throw new RuntimeException( 'the class group "' . $step['name'] . '" is there already (mode ensure accepts it)' );
                return self::result( array( 'the class group "' . $step['name'] . '" is there (' . $existing->attribute( 'id' ) . ')' ),
                                     self::values( $existing ) + array( 'created' => false, 'changed' => false ) );
            }
            $group = eZContentClassGroup::create( eZUser::currentUserID() );
            $group->setAttribute( 'name', (string)$step['name'] );
            $group->store();
            return self::result( array( 'created the class group "' . $step['name'] . '" (' . $group->attribute( 'id' ) . ')' ),
                                 self::values( $group ) + array( 'created' => true, 'changed' => true ) );
        }
        $id = expMigrationIdentifier::find( 'class_group', $step['group'] );
        $group = $id !== null ? eZContentClassGroup::fetch( $id ) : null;
        if ( !$group instanceof eZContentClassGroup )
            return self::missingResult( $step, 'the class group ' . expMigrationStepSchema::show( $step['group'] ), array( 'group_id' => null, 'name' => null ) );
        if ( $mode === 'update' )
        {
            $old = (string)$group->attribute( 'name' );
            if ( $old === (string)$step['name'] )
                return self::result( array( "the class group \"$old\" has that name" ), self::values( $group ) + array( 'created' => false, 'changed' => false ) );
            $db = eZDB::instance();
            $db->begin();
            try
            {
                $group->setAttribute( 'name', (string)$step['name'] );
                $group->setAttribute( 'modified', time() );
                $group->setAttribute( 'modifier_id', eZUser::currentUserID() );
                $group->store();
                eZContentClassClassGroup::update( null, (int)$group->attribute( 'id' ), (string)$step['name'] );
            }
            catch ( Throwable $e )
            {
                // nothing of a transaction that failed half way is committed by a later commit()
                if ( $db->transactionCounter() > 0 )
                    $db->rollback();
                throw $e;
            }
            $db->commit();
            return self::result( array( "renamed the class group \"$old\" to \"" . $step['name'] . '"' ), self::values( $group ) + array( 'created' => false, 'changed' => true ) );
        }
        return $this->delete( $group );
    }

    /**
     * Removes the group a create made.
     *
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function revert( array $step, $context )
    {
        if ( !$this->revertible( $step ) )
            throw new RuntimeException( 'only a create can be undone by itself; give the migration a down:' );
        $group = eZContentClassGroup::fetchByName( (string)$step['name'] );
        if ( !$group instanceof eZContentClassGroup )
            return array( 'the class group "' . $step['name'] . '" is not there: nothing to undo' );
        $result = $this->delete( $group );
        return $result['lines'];
    }

    /**
     * @param eZContentClassGroup $group
     * @return array
     */
    protected function delete( eZContentClassGroup $group )
    {
        $id = (int)$group->attribute( 'id' );
        $classes = eZContentClassClassGroup::fetchClassList( null, $id, false );
        if ( $classes )
            throw new RuntimeException( 'the class group "' . $group->attribute( 'name' ) . '" has ' . count( $classes ) . ' class(es): move or delete them first' );
        $values = self::values( $group );
        $db = eZDB::instance();
        $db->begin();
        try
        {
            eZContentClassGroup::removeSelected( $id );
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        return self::result( array( 'deleted the class group "' . $values['name'] . "\" ($id)" ), $values + array( 'created' => false, 'changed' => true ) );
    }

    /**
     * @param eZContentClassGroup $group
     * @return array
     */
    protected static function values( eZContentClassGroup $group )
    {
        return array( 'group_id' => (int)$group->attribute( 'id' ), 'name' => (string)$group->attribute( 'name' ) );
    }
}
