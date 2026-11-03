<?php
/**
 * File containing the expMigrationStepRole class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "role": creates, updates, ensures and deletes roles with their policies and limitations, and assigns
 * a role to users and user groups or takes it from them.
 *
 *   - type: role
 *     mode: ensure                     create, update, ensure, delete, assign, unassign
 *     name: News editor
 *     policies:
 *         - { module: content, function: read }
 *         - { module: content, function: edit, limitations: { Class: [ news_article ], Subtree: [ "path:news" ] } }
 *   - type: role
 *     mode: assign
 *     role: News editor
 *     groups: [ "remote:news-editors" ]     user groups (or any object), as identifiers of objects
 *     users: [ editor ]                       users: login, id or remote:
 *     subtree: "path:news"                    or section: <identifier>; neither: the whole site
 *
 * Policies are written as the administration writes them (eZSiteInstaller::addPoliciesForRole(), eZRole::appendPolicy())
 * and limitation values may name what they limit to the way the rest of a migration does: Class and ParentClass take
 * class identifiers, Section and UserSection section identifiers, Node node identifiers, Subtree and UserSubtree node
 * identifiers (stored as the node's path), SiteAccess siteaccess names, StateGroup_<group> state identifiers; numbers
 * are taken as they are. An update adds (add_policies:) and removes (remove_policies:, by module and function)
 * policies. The caches of roles and the policy cache of users are cleared, as the role views of the administration
 * clear them.
 *
 * A create and an assign undo themselves (the role is removed, the assignment taken back), and an unassign is undone
 * by assigning again.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepRole extends expMigrationStepStructureBase implements expMigrationPartlyRevertibleStepType
{
    /** The keys of a policy. */
    const POLICY_KEYS = array( 'module', 'function', 'limitations' );

    /** @return string */
    public function name()
    {
        return 'role';
    }

    /** @return array */
    public function schema()
    {
        $assigning = array( 'assign', 'unassign' );
        return array(
            'description' => 'Creates, updates, ensures or deletes a role with its policies and limitations, or assigns it to users and user groups or takes it from them',
            'modes' => array( 'create', 'update', 'ensure', 'delete', 'assign', 'unassign' ),
            'keys' => array(
                'role' => array( 'type' => 'identifier', 'kind' => 'role', 'modes' => array( 'update', 'delete', 'assign', 'unassign' ),
                                 'description' => 'the role: its name or id' ),
                'name' => array( 'type' => 'string', 'modes' => array( 'create', 'update', 'ensure' ),
                                 'description' => 'the name of the role; ensure finds the role by it (update: a new name)' ),
                'policies' => array( 'type' => 'list', 'modes' => array( 'create', 'ensure' ),
                                     'description' => 'the policies, each a map: module, function (* for all) and limitations (identifier => list of values); ensure adds those the role does not have' ),
                'add_policies' => array( 'type' => 'list', 'modes' => array( 'update' ), 'description' => 'policies to add, as in policies:' ),
                'remove_policies' => array( 'type' => 'list', 'modes' => array( 'update' ),
                                            'description' => 'policies to remove, each a map: module and function (without function: every policy of the module)' ),
                'users' => array( 'type' => 'list', 'modes' => $assigning, 'description' => 'the users: logins, ids or remote:<remote id>' ),
                'groups' => array( 'type' => 'list', 'modes' => $assigning, 'description' => 'the user groups (or other objects): object identifiers' ),
                'subtree' => array( 'type' => 'identifier', 'kind' => 'node', 'modes' => $assigning,
                                    'description' => 'the assignment is limited to the subtree of this node' ),
                'section' => array( 'type' => 'identifier', 'kind' => 'section', 'modes' => $assigning,
                                    'description' => 'the assignment is limited to this section' ),
            ) + self::missingKey( array( 'update', 'delete', 'assign', 'unassign' ), 'the role' ),
            'values' => array(
                'role_id' => 'the id of the role',
                'name' => 'its name',
                'created' => 'true when the step created the role',
                'changed' => 'true when the step changed it (created, updated, deleted, assigned or unassigned)',
            ),
            'example' => "- type: role\n  mode: ensure\n  name: News editor\n  policies:\n    - { module: content, function: read }\n"
                       . "    - { module: content, function: edit, limitations: { Class: [ news_article ], Subtree: [ \"path:news\" ] } }\n"
                       . "- type: role\n  mode: assign\n  role: News editor\n  groups: [ \"remote:news-editors\" ]\n  subtree: \"path:news\"\n",
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
        if ( in_array( $mode, array( 'create', 'ensure' ), true ) && ( !isset( $step['name'] ) || trim( (string)$step['name'] ) === '' ) )
            $errors[] = "name is missing (mode $mode)";
        if ( in_array( $mode, array( 'update', 'delete', 'assign', 'unassign' ), true ) && !isset( $step['role'] ) )
            $errors[] = "role is missing (mode $mode)";
        foreach ( array( 'policies', 'add_policies' ) as $key )
            if ( isset( $step[$key] ) && is_array( $step[$key] ) )
                foreach ( $step[$key] as $i => $policy )
                    $errors = array_merge( $errors, self::policyProblems( $policy, "{$key}[$i]", true ) );
        if ( isset( $step['remove_policies'] ) && is_array( $step['remove_policies'] ) )
            foreach ( $step['remove_policies'] as $i => $policy )
                $errors = array_merge( $errors, self::policyProblems( $policy, "remove_policies[$i]", false ) );
        if ( $mode === 'update' && !isset( $step['name'] ) && !isset( $step['add_policies'] ) && !isset( $step['remove_policies'] ) )
            $errors[] = 'nothing to update: name:, add_policies: or remove_policies: is missing';
        if ( in_array( $mode, array( 'assign', 'unassign' ), true ) )
        {
            if ( empty( $step['users'] ) && empty( $step['groups'] ) )
                $errors[] = "users: or groups: is missing (mode $mode)";
            if ( isset( $step['subtree'] ) && isset( $step['section'] ) )
                $errors[] = 'an assignment is limited by subtree: or by section:, not both';
            foreach ( array( 'users' => 'user', 'groups' => 'object' ) as $key => $kind )
                if ( isset( $step[$key] ) && is_array( $step[$key] ) )
                    foreach ( $step[$key] as $i => $who )
                        if ( ( $problem = expMigrationIdentifier::problem( $kind, $who ) ) !== null )
                            $errors[] = "{$key}[$i]: $problem";
        }
        return $errors;
    }

    /**
     * The problems of a policy map. Pure.
     *
     * @param mixed $policy
     * @param string $where
     * @param bool $full whether limitations may be given and function is required
     * @return string[]
     */
    public static function policyProblems( $policy, $where, $full )
    {
        if ( !is_array( $policy ) || ( $policy && array_keys( $policy ) === range( 0, count( $policy ) - 1 ) ) )
            return expMigrationReferences::contains( $policy ) ? array() : array( "$where: a policy is a map (module, function, limitations)" );
        $errors = array();
        foreach ( array_diff( array_keys( $policy ), $full ? self::POLICY_KEYS : array( 'module', 'function' ) ) as $key )
            $errors[] = "$where: the unknown key $key";
        if ( !isset( $policy['module'] ) || !is_string( $policy['module'] ) || !preg_match( '/^(\*|[a-z][a-z0-9_]*)$/', $policy['module'] ) )
            $errors[] = "$where: module is missing (a module name, or * for all)";
        if ( $full && ( !isset( $policy['function'] ) || !is_string( $policy['function'] ) || !preg_match( '/^(\*|[a-z][a-z0-9_]*)$/', $policy['function'] ) ) )
            $errors[] = "$where: function is missing (a function of the module, or * for all)";
        if ( isset( $policy['limitations'] ) )
        {
            if ( !is_array( $policy['limitations'] ) || ( $policy['limitations'] && array_keys( $policy['limitations'] ) === range( 0, count( $policy['limitations'] ) - 1 ) ) )
                $errors[] = "$where.limitations: a map of limitation => list of values (Class: [ article ])";
            else
                foreach ( $policy['limitations'] as $identifier => $values )
                    if ( !preg_match( '/^[A-Za-z][A-Za-z0-9_]*$/', (string)$identifier ) )
                        $errors[] = "$where.limitations: $identifier is not a limitation (Class, Section, Subtree ...)";
                    else if ( is_array( $values ) && $values && array_keys( $values ) !== range( 0, count( $values ) - 1 ) )
                        $errors[] = "$where.limitations.$identifier: a value or a list of values";
        }
        return $errors;
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function caches( array $step )
    {
        return array( 'user_info_cache', 'content' );
    }

    /**
     * @param array $step
     * @return bool
     */
    public function revertible( array $step )
    {
        return isset( $step['mode'] ) && in_array( $step['mode'], array( 'create', 'assign', 'unassign' ), true );
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( array $step, $context )
    {
        $policies = function ( $key ) use ( $step ) {
            $list = array();
            foreach ( isset( $step[$key] ) && is_array( $step[$key] ) ? $step[$key] : array() as $policy )
                if ( is_array( $policy ) && isset( $policy['module'] ) )
                    $list[] = $policy['module'] . '/' . ( isset( $policy['function'] ) ? $policy['function'] : '*' )
                            . ( !empty( $policy['limitations'] ) && is_array( $policy['limitations'] ) ? ' (' . implode( ', ', array_keys( $policy['limitations'] ) ) . ')' : '' );
            return $list;
        };
        $role = isset( $step['role'] ) ? 'the role ' . expMigrationStepSchema::show( $step['role'] ) : '';
        switch ( $step['mode'] )
        {
            case 'create':
            case 'ensure':
                $list = $policies( 'policies' );
                return array( $step['mode'] . ' the role "' . $step['name'] . '"' . ( $list ? ' with the policies ' . implode( ', ', $list ) : '' )
                              . ( $step['mode'] === 'ensure' ? ': create it when missing, add the policies it does not have' : '' ) );
            case 'update':
                $lines = array();
                if ( isset( $step['name'] ) )
                    $lines[] = "rename $role to \"" . $step['name'] . '"';
                if ( $list = $policies( 'add_policies' ) )
                    $lines[] = "add to $role the policies " . implode( ', ', $list );
                if ( $list = $policies( 'remove_policies' ) )
                    $lines[] = "remove from $role the policies " . implode( ', ', $list );
                return $lines;
            case 'delete':
                return array( "delete $role with its policies and assignments" );
            case 'assign':
            case 'unassign':
                return array( ( $step['mode'] === 'assign' ? 'assign ' : 'unassign ' ) . $role . ( $step['mode'] === 'assign' ? ' to ' : ' from ' ) . $this->whoText( $step )
                              . self::limitText( $step ) );
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
            $role = eZRole::fetchByName( (string)$step['name'] );
            if ( $role instanceof eZRole && $mode === 'create' )
                throw new RuntimeException( 'the role "' . $step['name'] . '" is there already (mode ensure adds what it lacks)' );
            return $role instanceof eZRole ? $this->ensure( $role, $step ) : $this->create( $step );
        }
        $role = self::findRole( $step['role'] );
        if ( !$role instanceof eZRole )
            return self::missingResult( $step, 'the role ' . expMigrationStepSchema::show( $step['role'] ), array( 'role_id' => null, 'name' => null ) );
        switch ( $mode )
        {
            case 'update':
                return $this->update( $role, $step );
            case 'delete':
                return $this->delete( $role );
            case 'assign':
                return $this->assign( $role, $step, true );
            case 'unassign':
                return $this->assign( $role, $step, false );
        }
        throw new RuntimeException( "the mode $mode is not one of create, update, ensure, delete, assign, unassign" );
    }

    /**
     * Undoes a create (the role is removed), an assign (taken back) or an unassign (assigned again).
     *
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function revert( array $step, $context )
    {
        if ( !$this->revertible( $step ) )
            throw new RuntimeException( 'only a create, an assign and an unassign can be undone by themselves; give the migration a down:' );
        if ( $step['mode'] === 'create' )
        {
            $role = eZRole::fetchByName( (string)$step['name'] );
            if ( !$role instanceof eZRole )
                return array( 'the role "' . $step['name'] . '" is not there: nothing to undo' );
            $result = $this->delete( $role );
            return $result['lines'];
        }
        $role = self::findRole( $step['role'] );
        if ( !$role instanceof eZRole )
            return array( 'the role ' . expMigrationStepSchema::show( $step['role'] ) . ' is not there: nothing to undo' );
        $result = $this->assign( $role, $step, $step['mode'] !== 'assign' );
        return $result['lines'];
    }

    // ---- the modes ---------------------------------------------------------------------------------------------

    /**
     * @param array $step
     * @return array
     */
    protected function create( array $step )
    {
        $policies = isset( $step['policies'] ) ? (array)$step['policies'] : array();
        $db = eZDB::instance();
        $db->begin();
        try
        {
            $installer = self::installer();
            $installer->addPoliciesForRole( array( 'role_name' => (string)$step['name'], 'create_role' => true, 'policies' => self::policyDefinitions( $policies ) ) );
            self::checkInstaller( $installer, 'creating the role "' . $step['name'] . '"' );
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        self::expire();
        $role = eZRole::fetchByName( (string)$step['name'] );
        if ( !$role instanceof eZRole )
            throw new RuntimeException( 'the role "' . $step['name'] . '" was not created' );
        return self::result( array( 'created the role "' . $step['name'] . '" (' . $role->attribute( 'id' ) . ') with ' . count( $policies ) . ' policies' ),
                             self::values( $role ) + array( 'created' => true, 'changed' => true ) );
    }

    /**
     * Adds to a role the policies of the step it does not have.
     *
     * @param eZRole $role
     * @param array $step
     * @return array
     */
    protected function ensure( eZRole $role, array $step )
    {
        $missing = $this->missingPolicies( $role, isset( $step['policies'] ) ? (array)$step['policies'] : array() );
        if ( !$missing )
            return self::result( array( 'the role "' . $role->attribute( 'name' ) . '" has every policy of the step' ),
                                 self::values( $role ) + array( 'created' => false, 'changed' => false ) );
        $this->addPolicies( $role, $missing );
        return self::result( array( 'added to the role "' . $role->attribute( 'name' ) . '" the policies ' . implode( ', ', self::policyNames( $missing ) ) ),
                             self::values( $role ) + array( 'created' => false, 'changed' => true ) );
    }

    /**
     * @param eZRole $role
     * @param array $step
     * @return array
     */
    protected function update( eZRole $role, array $step )
    {
        $lines = array();
        $db = eZDB::instance();
        $db->begin();
        try
        {
            if ( isset( $step['name'] ) && (string)$role->attribute( 'name' ) !== (string)$step['name'] )
            {
                if ( eZRole::fetchByName( (string)$step['name'] ) instanceof eZRole )
                    throw new RuntimeException( 'another role has the name "' . $step['name'] . '"' );
                $old = (string)$role->attribute( 'name' );
                $role->setAttribute( 'name', (string)$step['name'] );
                $role->store();
                $lines[] = "renamed the role \"$old\" to \"" . $step['name'] . '"';
            }
            if ( isset( $step['remove_policies'] ) )
            {
                foreach ( (array)$step['remove_policies'] as $policy )
                {
                    $function = isset( $policy['function'] ) && $policy['function'] !== '*' ? (string)$policy['function'] : false;
                    $before = count( $role->policyList() );
                    $role->removePolicy( (string)$policy['module'], $function );
                    unset( $role->Policies );
                    $removed = $before - count( $role->policyList() );
                    $lines[] = "removed $removed polic" . ( $removed === 1 ? 'y' : 'ies' ) . ' ' . $policy['module'] . '/' . ( $function === false ? '*' : $function )
                             . ' from the role "' . $role->attribute( 'name' ) . '"';
                }
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
        if ( isset( $step['add_policies'] ) && $step['add_policies'] )
        {
            $this->addPolicies( $role, (array)$step['add_policies'] );
            $lines[] = 'added to the role "' . $role->attribute( 'name' ) . '" the policies ' . implode( ', ', self::policyNames( (array)$step['add_policies'] ) );
        }
        self::expire();
        $changed = (bool)$lines;
        if ( !$lines )
            $lines[] = 'the role "' . $role->attribute( 'name' ) . '" is as the step has it';
        return self::result( $lines, self::values( $role ) + array( 'created' => false, 'changed' => $changed ) );
    }

    /**
     * @param eZRole $role
     * @return array
     */
    protected function delete( eZRole $role )
    {
        $values = self::values( $role );
        $db = eZDB::instance();
        $db->begin();
        try
        {
            $role->removeThis();
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        self::expire();
        return self::result( array( 'deleted the role "' . $values['name'] . '" (' . $values['role_id'] . ')' ), $values + array( 'created' => false, 'changed' => true ) );
    }

    /**
     * Assigns a role (or takes it back) for the users and groups of a step, with its limitation.
     *
     * @param eZRole $role
     * @param array $step
     * @param bool $assign
     * @return array
     */
    protected function assign( eZRole $role, array $step, $assign )
    {
        list( $ident, $value, $storedIdent, $storedValue ) = self::limitation( $step );
        $ids = array();
        foreach ( isset( $step['users'] ) ? (array)$step['users'] : array() as $user )
            $ids[] = expMigrationIdentifier::resolve( 'user', $user );
        foreach ( isset( $step['groups'] ) ? (array)$step['groups'] : array() as $group )
            $ids[] = expMigrationIdentifier::resolve( 'object', $group );
        $existing = array();
        foreach ( $role->fetchUserByRole() as $row )
        {
            $object = $row['user_object'];
            if ( $object instanceof eZContentObject && (string)$row['limit_ident'] === $storedIdent && (string)$row['limit_value'] === $storedValue )
                $existing[(int)$object->attribute( 'id' )] = (int)$row['user_role_id'];
        }
        $lines = array();
        $db = eZDB::instance();
        $db->begin();
        try
        {
            foreach ( array_unique( $ids ) as $id )
            {
                $label = 'the role "' . $role->attribute( 'name' ) . '"' . ( $assign ? ' to ' : ' from ' ) . "object $id" . self::limitText( $step );
                if ( $assign === isset( $existing[$id] ) )
                {
                    $lines[] = ( $assign ? 'assigned ' : 'not assigned ' ) . $label . ' already';
                    continue;
                }
                if ( $assign )
                    $role->assignToUser( $id, $ident, $value );
                else
                    $role->removeUserAssignmentByID( $existing[$id] );
                $lines[] = ( $assign ? 'assigned ' : 'unassigned ' ) . $label;
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
        self::expire();
        $changed = (bool)array_filter( $lines, function ( $line ) { return substr( $line, -8 ) !== ' already'; } );
        return self::result( $lines, self::values( $role ) + array( 'created' => false, 'changed' => $changed ) );
    }

    // ---- helpers ---------------------------------------------------------------------------------------------

    /**
     * @param array $policies
     * @return array the policy definitions of eZSiteInstaller::addPoliciesForRole(), limitation values resolved
     */
    protected static function policyDefinitions( array $policies )
    {
        $definitions = array();
        foreach ( $policies as $policy )
        {
            $definition = array( 'module' => (string)$policy['module'], 'function' => (string)$policy['function'] );
            $limitations = self::limitationValues( isset( $policy['limitations'] ) ? (array)$policy['limitations'] : array() );
            if ( $limitations )
                $definition['limitation'] = $limitations;
            $definitions[] = $definition;
        }
        return $definitions;
    }

    /**
     * Adds policies to a role through eZSiteInstaller::addPoliciesForRole().
     *
     * @param eZRole $role
     * @param array $policies
     */
    protected function addPolicies( eZRole $role, array $policies )
    {
        $db = eZDB::instance();
        $db->begin();
        try
        {
            $installer = self::installer();
            $installer->addPoliciesForRole( array( 'role_name' => (string)$role->attribute( 'name' ), 'create_role' => false, 'policies' => self::policyDefinitions( $policies ) ) );
            self::checkInstaller( $installer, 'adding policies to the role "' . $role->attribute( 'name' ) . '"' );
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        self::expire();
    }

    /**
     * The policies of a list a role does not have (same module, function and limitation values).
     *
     * @param eZRole $role
     * @param array $policies
     * @return array
     */
    protected function missingPolicies( eZRole $role, array $policies )
    {
        $have = array();
        unset( $role->Policies );
        foreach ( (array)$role->policyList() as $policy )
        {
            $limitations = array();
            foreach ( (array)$policy->attribute( 'limitations' ) as $limitation )
            {
                $values = array();
                foreach ( (array)$limitation->attribute( 'values' ) as $value )
                    $values[] = (string)$value->attribute( 'value' );
                sort( $values );
                $limitations[(string)$limitation->attribute( 'identifier' )] = $values;
            }
            ksort( $limitations );
            $have[] = $policy->attribute( 'module_name' ) . '/' . $policy->attribute( 'function_name' ) . json_encode( $limitations );
        }
        $missing = array();
        foreach ( $policies as $policy )
        {
            $limitations = array();
            foreach ( self::limitationValues( isset( $policy['limitations'] ) ? (array)$policy['limitations'] : array() ) as $identifier => $values )
            {
                $values = array_map( 'strval', (array)$values );
                sort( $values );
                $limitations[$identifier] = $values;
            }
            ksort( $limitations );
            if ( !in_array( $policy['module'] . '/' . $policy['function'] . json_encode( $limitations ), $have, true ) )
                $missing[] = $policy;
        }
        return $missing;
    }

    /**
     * Limitation values as they are stored: class, section, node and state identifiers resolved, subtrees as paths,
     * siteaccesses as the checksum of their name.
     *
     * @param array $limitations identifier => value or values
     * @return array identifier => values
     * @throws RuntimeException for a value that names nothing here
     */
    public static function limitationValues( array $limitations )
    {
        $resolved = array();
        foreach ( $limitations as $identifier => $values )
        {
            $list = array();
            foreach ( (array)$values as $value )
            {
                $numeric = is_int( $value ) || ( is_string( $value ) && ctype_digit( $value ) );
                switch ( true )
                {
                    case in_array( $identifier, array( 'Class', 'ParentClass' ), true ):
                        $list[] = expMigrationIdentifier::resolve( 'class', $value );
                        break;
                    case in_array( $identifier, array( 'Section', 'UserSection' ), true ):
                        $list[] = expMigrationIdentifier::resolve( 'section', $value );
                        break;
                    case $identifier === 'Node':
                        $list[] = expMigrationIdentifier::resolve( 'node', $value );
                        break;
                    case in_array( $identifier, array( 'Subtree', 'UserSubtree' ), true ):
                        if ( is_string( $value ) && preg_match( '#^/(\d+/)+$#', $value ) )
                        {
                            $list[] = $value;
                            break;
                        }
                        $node = eZContentObjectTreeNode::fetch( expMigrationIdentifier::resolve( 'node', $value ) );
                        $list[] = (string)$node->attribute( 'path_string' );
                        break;
                    case $identifier === 'SiteAccess':
                        $list[] = $numeric ? (int)$value : eZSys::ezcrc32( (string)$value );
                        break;
                    case strpos( $identifier, 'StateGroup_' ) === 0:
                        $list[] = $numeric ? (int)$value : expMigrationIdentifier::resolve( 'state', substr( $identifier, 11 ) . '/' . $value );
                        break;
                    default:
                        $list[] = $value;
                }
            }
            $resolved[$identifier] = $list;
        }
        return $resolved;
    }

    /**
     * The limitation of an assignment: what assignToUser() takes and what ezuser_role stores.
     *
     * @param array $step
     * @return array array( ident, value, stored ident, stored value )
     */
    protected static function limitation( array $step )
    {
        if ( isset( $step['subtree'] ) )
        {
            $nodeId = expMigrationIdentifier::resolve( 'node', $step['subtree'] );
            $node = eZContentObjectTreeNode::fetch( $nodeId );
            return array( 'subtree', $nodeId, 'Subtree', (string)$node->attribute( 'path_string' ) );
        }
        if ( isset( $step['section'] ) )
        {
            $sectionId = expMigrationIdentifier::resolve( 'section', $step['section'] );
            return array( 'section', $sectionId, 'Section', (string)$sectionId );
        }
        return array( '', '', '', '' );
    }

    /**
     * @param mixed $identifier
     * @return eZRole|null
     */
    protected static function findRole( $identifier )
    {
        $id = expMigrationIdentifier::find( 'role', $identifier );
        $role = $id !== null ? eZRole::fetch( $id ) : null;
        return $role instanceof eZRole ? $role : null;
    }

    /** Clears the caches of roles and the policies cached for users, as the role views do. */
    protected static function expire()
    {
        eZRole::expireCache();
        eZUser::cleanupCache();
    }

    /**
     * @param array $policies
     * @return string[] module/function of each
     */
    protected static function policyNames( array $policies )
    {
        $names = array();
        foreach ( $policies as $policy )
            $names[] = $policy['module'] . '/' . ( isset( $policy['function'] ) ? $policy['function'] : '*' );
        return $names;
    }

    /**
     * @param array $step
     * @return string
     */
    protected function whoText( array $step )
    {
        $who = array();
        foreach ( isset( $step['users'] ) && is_array( $step['users'] ) ? $step['users'] : array() as $user )
            $who[] = 'the user ' . expMigrationStepSchema::show( $user );
        foreach ( isset( $step['groups'] ) && is_array( $step['groups'] ) ? $step['groups'] : array() as $group )
            $who[] = 'the object ' . expMigrationStepSchema::show( $group );
        return implode( ', ', $who );
    }

    /**
     * @param array $step
     * @return string
     */
    protected static function limitText( array $step )
    {
        if ( isset( $step['subtree'] ) )
            return ' in the subtree of node ' . expMigrationStepSchema::show( $step['subtree'] );
        if ( isset( $step['section'] ) )
            return ' in the section ' . expMigrationStepSchema::show( $step['section'] );
        return '';
    }

    /**
     * @param eZRole $role
     * @return array
     */
    protected static function values( eZRole $role )
    {
        return array( 'role_id' => (int)$role->attribute( 'id' ), 'name' => (string)$role->attribute( 'name' ) );
    }
}
