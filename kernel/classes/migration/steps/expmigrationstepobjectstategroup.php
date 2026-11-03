<?php
/**
 * File containing the expMigrationStepObjectStateGroup class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "object_state_group": creates, updates, ensures and deletes object state groups with their states.
 *
 *   - type: object_state_group
 *     mode: ensure                     create, update, ensure, delete
 *     identifier: review
 *     name: Review
 *     names: { ger-DE: Prüfung }
 *     states:
 *         - { identifier: draft, name: Draft, names: { ger-DE: Entwurf } }
 *         - { identifier: approved, name: Approved }
 *
 * Groups and states are stored as state/group_edit and state/edit of the administration store them
 * (eZContentObjectStateGroup, eZContentObjectState, their translations), and checked the same way (isValid(): the
 * identifier, a name in the default language). The first state of a group is the state of every object; a removed
 * state's objects get the group's first state. The groups of the kernel (identifiers starting with "ez") are not
 * changed. A create undoes itself (the group is removed again, with its states).
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepObjectStateGroup extends expMigrationStepStructureBase implements expMigrationPartlyRevertibleStepType
{
    /** The keys of a state of states:. */
    const STATE_KEYS = array( 'identifier', 'language', 'name', 'names', 'description', 'descriptions' );

    /** @return string */
    public function name()
    {
        return 'object_state_group';
    }

    /** @return array */
    public function schema()
    {
        $writing = array( 'create', 'update', 'ensure' );
        return array(
            'description' => 'Creates, updates, ensures or deletes an object state group with its states and the translations of their names',
            'modes' => array( 'create', 'update', 'ensure', 'delete' ),
            'keys' => array(
                'group' => array( 'type' => 'identifier', 'kind' => 'state_group', 'modes' => array( 'update', 'delete' ),
                                  'description' => 'the group to update or delete: its identifier or id' ),
                'identifier' => array( 'type' => 'string', 'modes' => $writing,
                                       'description' => 'the identifier of the group; ensure finds the group by it (update: a new identifier)' ),
            ) + self::nameKeys( $writing, 'the group', true ) + array(
                'states' => array( 'type' => 'list', 'modes' => array( 'create', 'ensure' ),
                                   'description' => 'the states, each a map: identifier, name, names, description, descriptions; ensure adds the missing ones and changes what differs' ),
                'add_states' => array( 'type' => 'list', 'modes' => array( 'update' ), 'description' => 'states to add, as in states:' ),
                'update_states' => array( 'type' => 'map', 'modes' => array( 'update' ),
                                          'description' => 'state identifier => what changes (name, names, description, descriptions, identifier for a new one)' ),
                'remove_states' => array( 'type' => 'list', 'modes' => array( 'update' ),
                                          'description' => 'the identifiers of states to remove; their objects get the first state left' ),
            ) + self::missingKey( array( 'update', 'delete' ), 'the group' ),
            'values' => array(
                'group_id' => 'the id of the group',
                'identifier' => 'its identifier',
                'state_ids' => 'state identifier => id, for every state of the group',
                'created' => 'true when the step created the group',
                'changed' => 'true when the step changed it (created, updated or deleted)',
            ),
            'example' => "- type: object_state_group\n  mode: ensure\n  identifier: review\n  name: Review\n  names: { ger-DE: Prüfung }\n"
                       . "  states:\n    - { identifier: draft, name: Draft, names: { ger-DE: Entwurf } }\n    - { identifier: approved, name: Approved }\n",
        );
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function validate( array $step )
    {
        $mode = isset( $step['mode'] ) ? $step['mode'] : null;
        $errors = array_merge( self::translationProblems( $step ), self::translationProblems( $step, 'description', 'descriptions' ) );
        if ( in_array( $mode, array( 'create', 'ensure' ), true ) && ( !isset( $step['identifier'] ) || (string)$step['identifier'] === '' ) )
            $errors[] = "identifier is missing (mode $mode)";
        if ( isset( $step['identifier'] ) && !expMigrationReferences::contains( $step['identifier'] ) )
            $errors = array_merge( $errors, self::identifierProblems( $step['identifier'], 'identifier' ) );
        if ( $mode === 'create' && !isset( $step['name'] ) && empty( $step['names'] ) )
            $errors[] = 'name is missing: a group has a name in the default language';
        if ( in_array( $mode, array( 'update', 'delete' ), true ) && !isset( $step['group'] ) )
            $errors[] = "group is missing (mode $mode)";
        foreach ( array( 'states', 'add_states' ) as $key )
            if ( isset( $step[$key] ) && is_array( $step[$key] ) )
                foreach ( $step[$key] as $i => $state )
                    $errors = array_merge( $errors, self::stateProblems( $state, "{$key}[$i]", true ) );
        if ( isset( $step['update_states'] ) && is_array( $step['update_states'] ) )
            foreach ( $step['update_states'] as $identifier => $state )
                $errors = array_merge( $errors, self::stateProblems( $state, "update_states.$identifier", false ) );
        if ( isset( $step['remove_states'] ) && is_array( $step['remove_states'] ) )
            foreach ( $step['remove_states'] as $i => $identifier )
                $errors = array_merge( $errors, self::identifierProblems( $identifier, "remove_states[$i]" ) );
        return $errors;
    }

    /**
     * @param mixed $identifier
     * @param string $where
     * @return string[]
     */
    protected static function identifierProblems( $identifier, $where )
    {
        if ( !is_string( $identifier ) || !preg_match( '/^[a-z0-9_]{1,45}$/', $identifier ) )
            return array( "$where: " . expMigrationStepSchema::show( $identifier ) . ' is not a state identifier (a-z, 0-9 and _)' );
        if ( strncmp( $identifier, 'ez', 2 ) === 0 )
            return array( "$where: identifiers starting with \"ez\" are the kernel's" );
        return array();
    }

    /**
     * @param mixed $state
     * @param string $where
     * @param bool $new
     * @return string[]
     */
    protected static function stateProblems( $state, $where, $new )
    {
        if ( !is_array( $state ) || ( $state && array_keys( $state ) === range( 0, count( $state ) - 1 ) ) )
            return array( "$where: a state is a map (identifier, name, names ...)" );
        $errors = array();
        foreach ( array_diff( array_keys( $state ), self::STATE_KEYS ) as $key )
            $errors[] = "$where: the unknown key $key (" . implode( ', ', self::STATE_KEYS ) . ')';
        if ( $new && !isset( $state['identifier'] ) )
            $errors[] = "$where: identifier is missing";
        if ( isset( $state['identifier'] ) )
            $errors = array_merge( $errors, self::identifierProblems( $state['identifier'], "$where.identifier" ) );
        if ( $new && !isset( $state['name'] ) && empty( $state['names'] ) )
            $errors[] = "$where: name is missing";
        foreach ( array_merge( self::translationProblems( $state ), self::translationProblems( $state, 'description', 'descriptions' ) ) as $problem )
            $errors[] = "$where: $problem";
        return $errors;
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function caches( array $step )
    {
        return array( 'state_limitations', 'user_info_cache', 'content' );
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
        $states = function ( $key ) use ( $step ) {
            $list = array();
            foreach ( isset( $step[$key] ) && is_array( $step[$key] ) ? $step[$key] : array() as $state )
                if ( is_array( $state ) && isset( $state['identifier'] ) )
                    $list[] = $state['identifier'];
            return $list;
        };
        switch ( $step['mode'] )
        {
            case 'create':
            case 'ensure':
                $list = $states( 'states' );
                return array( $step['mode'] . ' the object state group ' . $step['identifier'] . self::namesText( $step )
                              . ( $list ? ' with the states ' . implode( ', ', $list ) : '' )
                              . ( $step['mode'] === 'ensure' ? ': create it when missing, change what differs' : '' ) );
            case 'update':
                $group = 'the object state group ' . expMigrationStepSchema::show( $step['group'] );
                $lines = array();
                $changes = array_intersect( array_keys( $step ), array( 'identifier', 'name', 'names', 'description', 'descriptions' ) );
                if ( $changes )
                    $lines[] = "update $group: " . implode( ', ', $changes );
                if ( $list = $states( 'add_states' ) )
                    $lines[] = "add to $group the states " . implode( ', ', $list );
                if ( isset( $step['update_states'] ) && is_array( $step['update_states'] ) )
                    foreach ( $step['update_states'] as $identifier => $state )
                        $lines[] = "change the state $identifier of $group: " . implode( ', ', is_array( $state ) ? array_keys( $state ) : array() );
                if ( isset( $step['remove_states'] ) && is_array( $step['remove_states'] ) )
                    $lines[] = "remove from $group the states " . implode( ', ', $step['remove_states'] );
                return $lines;
            case 'delete':
                return array( 'delete the object state group ' . expMigrationStepSchema::show( $step['group'] ) . ' with its states' );
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
            $group = eZContentObjectStateGroup::fetchByIdentifier( (string)$step['identifier'] );
            if ( $group instanceof eZContentObjectStateGroup && $mode === 'create' )
                throw new RuntimeException( 'the object state group ' . $step['identifier'] . ' is there already (mode ensure changes it)' );
            return $group instanceof eZContentObjectStateGroup ? $this->update( $group, $step, true ) : $this->create( $step );
        }
        $id = expMigrationIdentifier::find( 'state_group', $step['group'] );
        $group = $id !== null ? eZContentObjectStateGroup::fetchById( $id ) : null;
        if ( !$group instanceof eZContentObjectStateGroup )
            return self::missingResult( $step, 'the object state group ' . expMigrationStepSchema::show( $step['group'] ),
                                        array( 'group_id' => null, 'identifier' => null, 'state_ids' => array() ) );
        if ( $group->isInternal() )
            throw new RuntimeException( 'the object state group ' . $group->attribute( 'identifier' ) . ' is the kernel\'s: it is not changed' );
        return $mode === 'update' ? $this->update( $group, $step, false ) : $this->delete( $group );
    }

    /**
     * Removes the group a create made, with its states.
     *
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function revert( array $step, $context )
    {
        if ( !$this->revertible( $step ) )
            throw new RuntimeException( 'only a create can be undone by itself; give the migration a down:' );
        $group = eZContentObjectStateGroup::fetchByIdentifier( (string)$step['identifier'] );
        if ( !$group instanceof eZContentObjectStateGroup )
            return array( 'the object state group ' . $step['identifier'] . ' is not there: nothing to undo' );
        $result = $this->delete( $group );
        return $result['lines'];
    }

    // ---- the modes ---------------------------------------------------------------------------------------------

    /**
     * @param array $step
     * @return array
     */
    protected function create( array $step )
    {
        $language = expMigrationStepContentBase::localeOf( isset( $step['language'] ) ? $step['language'] : null );
        $db = eZDB::instance();
        $db->begin();
        try
        {
            $group = new eZContentObjectStateGroup( array( 'identifier' => (string)$step['identifier'] ) );
            $group->setAttribute( 'default_language_id', (int)eZContentLanguage::idByLocale( $language ) );
            self::translate( $group, $step, $language );
            self::store( $group, 'the object state group ' . $step['identifier'] );
            $added = array();
            foreach ( isset( $step['states'] ) ? (array)$step['states'] : array() as $state )
            {
                $this->addState( $group, $state, $language );
                $added[] = $state['identifier'];
            }
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        $group = eZContentObjectStateGroup::fetchById( (int)$group->attribute( 'id' ) );
        return self::result( array( 'created the object state group ' . $step['identifier'] . ' (' . $group->attribute( 'id' ) . ')'
                                    . ( $added ? ' with the states ' . implode( ', ', $added ) : '' ) ),
                             self::values( $group ) + array( 'created' => true, 'changed' => true ) );
    }

    /**
     * @param eZContentObjectStateGroup $group
     * @param array $step
     * @param bool $ensure
     * @return array
     */
    protected function update( eZContentObjectStateGroup $group, array $step, $ensure )
    {
        $language = expMigrationStepContentBase::localeOf( isset( $step['language'] ) ? $step['language'] : null );
        $label = 'the object state group ' . $group->attribute( 'identifier' );
        $lines = array();
        $db = eZDB::instance();
        $db->begin();
        try
        {
            $changed = array();
            if ( !$ensure && isset( $step['identifier'] ) && (string)$group->attribute( 'identifier' ) !== (string)$step['identifier'] )
            {
                if ( eZContentObjectStateGroup::fetchByIdentifier( (string)$step['identifier'] ) )
                    throw new RuntimeException( 'another object state group has the identifier ' . $step['identifier'] );
                $group->setAttribute( 'identifier', (string)$step['identifier'] );
                $changed[] = 'identifier';
            }
            $changed = array_merge( $changed, self::translate( $group, $step, $language ) );
            if ( $changed )
            {
                self::store( $group, $label );
                $lines[] = "updated $label: " . implode( ', ', $changed );
            }
            $add = $ensure ? array() : ( isset( $step['add_states'] ) ? (array)$step['add_states'] : array() );
            $change = $ensure ? array() : ( isset( $step['update_states'] ) ? (array)$step['update_states'] : array() );
            if ( $ensure )
            {
                foreach ( isset( $step['states'] ) ? (array)$step['states'] : array() as $state )
                {
                    if ( self::stateOf( $group, $state['identifier'] ) instanceof eZContentObjectState )
                        $change[$state['identifier']] = $state;
                    else
                        $add[] = $state;
                }
            }
            foreach ( $add as $state )
            {
                if ( self::stateOf( $group, $state['identifier'] ) instanceof eZContentObjectState )
                    throw new RuntimeException( "$label has the state " . $state['identifier'] . ' already' );
                $this->addState( $group, $state, $language );
                $lines[] = "added to $label the state " . $state['identifier'];
            }
            foreach ( $change as $identifier => $state )
            {
                $existing = self::stateOf( $group, (string)$identifier );
                if ( !$existing instanceof eZContentObjectState )
                    throw new RuntimeException( "$label has no state $identifier" );
                $stateChanges = array();
                if ( !$ensure && isset( $state['identifier'] ) && $state['identifier'] !== (string)$identifier )
                {
                    $existing->setAttribute( 'identifier', $state['identifier'] );
                    $stateChanges[] = 'identifier';
                }
                $stateChanges = array_merge( $stateChanges, self::translate( $existing, (array)$state, $language ) );
                if ( $stateChanges )
                {
                    self::store( $existing, "the state $identifier of $label" );
                    $lines[] = "changed the state $identifier of $label: " . implode( ', ', $stateChanges );
                }
            }
            if ( !$ensure && !empty( $step['remove_states'] ) )
            {
                $ids = array();
                foreach ( (array)$step['remove_states'] as $identifier )
                {
                    $state = self::stateOf( $group, (string)$identifier );
                    if ( !$state instanceof eZContentObjectState )
                        throw new RuntimeException( "$label has no state $identifier to remove" );
                    $ids[] = (int)$state->attribute( 'id' );
                }
                if ( count( $ids ) >= count( self::statesOf( $group ) ) )
                    throw new RuntimeException( "$label keeps at least one state (delete the group to remove them all)" );
                self::removeStates( $group, $ids );
                $lines[] = "removed from $label the states " . implode( ', ', (array)$step['remove_states'] );
            }
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        eZContentObjectState::cleanDefaultsCache();
        $group = eZContentObjectStateGroup::fetchById( (int)$group->attribute( 'id' ) );
        $changedAny = (bool)$lines;
        if ( !$lines )
            $lines[] = "$label is as the step has it";
        return self::result( $lines, self::values( $group ) + array( 'created' => false, 'changed' => $changedAny ) );
    }

    /**
     * @param eZContentObjectStateGroup $group
     * @return array
     */
    protected function delete( eZContentObjectStateGroup $group )
    {
        $values = self::values( $group );
        eZContentObjectStateGroup::removeByID( (int)$values['group_id'] );
        return self::result( array( 'deleted the object state group ' . $values['identifier'] . ' (' . $values['group_id'] . ') with its states' ),
                             $values + array( 'created' => false, 'changed' => true ) );
    }

    // ---- helpers ---------------------------------------------------------------------------------------------

    /**
     * @param eZContentObjectStateGroup $group
     * @param array $state
     * @param string $language
     */
    protected function addState( eZContentObjectStateGroup $group, array $state, $language )
    {
        $own = isset( $state['language'] ) ? expMigrationStepContentBase::localeOf( $state['language'] ) : $language;
        $new = $group->newState( (string)$state['identifier'] );
        $new->setAttribute( 'default_language_id', (int)eZContentLanguage::idByLocale( $own ) );
        self::translate( $new, array( 'language' => $own ) + $state, $own );
        self::store( $new, 'the state ' . $state['identifier'] );
        eZContentObjectState::cleanDefaultsCache();
    }

    /**
     * Writes the names and descriptions of a step into the translations of a group or a state (not stored).
     *
     * @param eZContentObjectStateGroup|eZContentObjectState $item
     * @param array $step
     * @param string $language
     * @return string[] what changed
     */
    protected static function translate( $item, array $step, $language )
    {
        $changed = array();
        $own = isset( $step['language'] ) ? $step['language'] : $language;
        foreach ( array( 'name' => self::translations( array( 'language' => $own ) + $step ),
                         'description' => self::translations( array( 'language' => $own ) + $step, 'description', 'descriptions' ) ) as $field => $texts )
        {
            foreach ( $texts as $locale => $text )
            {
                $translation = $item->translationByLocale( $locale );
                if ( !$translation )
                    throw new RuntimeException( "no translation $locale" );
                // a translation stores name and description, both NOT NULL: the one the step leaves out is empty
                foreach ( array( 'name', 'description' ) as $column )
                    if ( $translation->attribute( $column ) === null )
                        $translation->setAttribute( $column, '' );
                if ( (string)$translation->attribute( $field ) === $text )
                    continue;
                $translation->setAttribute( $field, $text );
                $changed[] = "$field ($locale)";
            }
        }
        return $changed;
    }

    /**
     * Checks a group or a state as the administration does and stores it.
     *
     * @param eZContentObjectStateGroup|eZContentObjectState $item
     * @param string $label
     * @throws RuntimeException when it is not valid
     */
    protected static function store( $item, $label )
    {
        $messages = array();
        if ( !$item->isValid( $messages ) )
            throw new RuntimeException( "$label is not valid: " . implode( '; ', $messages ) );
        $item->store();
    }

    /**
     * @param eZContentObjectStateGroup $group
     * @return array
     */
    protected static function values( eZContentObjectStateGroup $group )
    {
        $states = array();
        foreach ( (array)self::statesOf( $group ) as $state )
            $states[(string)$state->attribute( 'identifier' )] = (int)$state->attribute( 'id' );
        return array( 'group_id' => (int)$group->attribute( 'id' ), 'identifier' => (string)$group->attribute( 'identifier' ), 'state_ids' => $states );
    }

    /**
     * The states of a group in their order, read by their own table (eZContentObjectStateGroup::states() joins three
     * tables with the language filters of the siteaccess, which the MongoDB driver does not run).
     *
     * @param eZContentObjectStateGroup $group
     * @return eZContentObjectState[]
     */
    protected static function statesOf( eZContentObjectStateGroup $group )
    {
        return (array)eZPersistentObject::fetchObjectList( eZContentObjectState::definition(), null, array( 'group_id' => (int)$group->attribute( 'id' ) ),
                                                          array( 'priority' => 'asc' ) );
    }

    /**
     * @param eZContentObjectStateGroup $group
     * @param string $identifier
     * @return eZContentObjectState|null
     */
    protected static function stateOf( eZContentObjectStateGroup $group, $identifier )
    {
        foreach ( self::statesOf( $group ) as $state )
            if ( (string)$state->attribute( 'identifier' ) === (string)$identifier )
                return $state;
        return null;
    }

    /**
     * eZContentObjectStateGroup::removeStatesByID() over the states of statesOf(): the objects of a removed state get the
     * first state that is left, the states left are numbered again.
     *
     * @param eZContentObjectStateGroup $group
     * @param int[] $ids
     */
    protected static function removeStates( eZContentObjectStateGroup $group, array $ids )
    {
        $left = array();
        foreach ( self::statesOf( $group ) as $state )
            if ( !in_array( (int)$state->attribute( 'id' ), $ids, true ) )
                $left[] = $state;
        $db = eZDB::instance();
        $db->begin();
        try
        {
            $newDefault = (int)$left[0]->attribute( 'id' );
            foreach ( $ids as $id )
            {
                $db->query( 'UPDATE ezcobj_state_link SET contentobject_state_id=' . $newDefault . ' WHERE contentobject_state_id=' . (int)$id );
                eZContentObjectState::removeByID( (int)$id );
            }
            foreach ( $left as $priority => $state )
            {
                if ( (int)$state->attribute( 'priority' ) === $priority )
                    continue;
                $state->setAttribute( 'priority', $priority );
                $state->sync( array( 'priority' ) );
            }
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        eZContentObjectState::cleanDefaultsCache();
    }
}
