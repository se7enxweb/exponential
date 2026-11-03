<?php
/**
 * File containing the expMigrationStepUser class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "user": creates, updates, ensures and deletes user accounts.
 *
 *   - type: user
 *     mode: ensure                     create, update, ensure (create when the login is missing), delete
 *     login: editor
 *     email: editor@example.com
 *     password: "${env.EDITOR_PASSWORD}"     never written into the plan, the log or the record
 *     groups: [ "remote:news-editors" ]      the user groups (the first is the main location)
 *     fields: { first_name: News, last_name: Editor }
 *
 * A user is a content object of the class user: the object is made, translated and published as the step "content"
 * does it (its fields through fromString(), workflows only with workflows: true), the account (login, e-mail) through
 * the ezuser datatype's fromString(), then the password is hashed as eZUser hashes it (or a hash given with
 * password_hash: is stored as it is) and the account enabled or disabled (enabled:). Further groups are further
 * locations (the operation addlocation). A password is set by create and update only; ensure leaves the password of
 * an account that exists alone. A create undoes itself (the user is removed again); a delete removes the user for
 * good, with the role assignments of the account.
 *
 * secrets: names the login, the e-mail address and the password by secrets instead (a name of the secret store,
 * Setup > Secrets, or env:<variable>), read when the step runs: a migration generated from a recording or from content
 * names an account this way and never carries its personal data (open question 16, answer a).
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepUser extends expMigrationStepContent
{
    /** The account values secrets: can name. */
    const SECRET_KEYS = array( 'login', 'email', 'password' );

    /** @return string */
    public function name()
    {
        return 'user';
    }

    /** @return array */
    public function schema()
    {
        $writing = array( 'create', 'update', 'ensure' );
        return array(
            'description' => 'Creates, updates, ensures or deletes a user account with its groups, password and fields',
            'modes' => array( 'create', 'update', 'ensure', 'delete' ),
            'keys' => array(
                'login' => array( 'type' => 'string', 'modes' => array( 'create', 'ensure' ),
                                  'description' => 'the login of a new user; ensure finds the user by it' ),
                'user' => array( 'type' => 'identifier', 'kind' => 'user', 'modes' => array( 'update', 'delete' ),
                                 'description' => 'the user to update or delete: login, id or remote:<remote id>' ),
                'email' => array( 'type' => 'string', 'modes' => $writing, 'description' => 'the e-mail address of the account' ),
                'password' => array( 'type' => 'string', 'modes' => array( 'create', 'update', 'ensure' ),
                                     'description' => 'the password, hashed as eZUser hashes it (ensure: only for a new user); use a placeholder, never the password in the file' ),
                'password_hash' => array( 'type' => 'string', 'modes' => array( 'create', 'update', 'ensure' ),
                                          'description' => 'a password hash to store as it is (instead of password:)' ),
                'password_hash_type' => array( 'type' => 'string', 'modes' => array( 'create', 'update', 'ensure' ),
                                               'description' => 'the type of password_hash: (md5_password, md5_user, md5_site, plaintext, password_hash ...)' ),
                'secrets' => array( 'type' => 'map', 'modes' => $writing,
                                    'description' => 'login, email or password => the name of a secret (Setup > Secrets, or env:<variable>) the value is read from when the step runs, instead of login:, email: or password:' ),
                'enabled' => array( 'type' => 'bool', 'modes' => $writing, 'description' => 'whether the account can sign in (default for a new user: true)' ),
                'groups' => array( 'type' => 'list', 'modes' => $writing,
                                   'description' => 'the nodes of the user groups the user is in (the first is its main location); update and ensure add the user to those it is not in' ),
                'remove_groups' => array( 'type' => 'list', 'modes' => array( 'update' ),
                                          'description' => 'the nodes of user groups the user leaves (not the main one)' ),
                'class' => array( 'type' => 'identifier', 'kind' => 'class', 'default' => 'user', 'modes' => array( 'create', 'ensure' ),
                                  'description' => 'the class of a new user (default: user)' ),
                'remote_id' => array( 'type' => 'string', 'modes' => array( 'create', 'ensure' ), 'description' => 'the remote id of a new user\'s object' ),
                'language' => array( 'type' => 'string', 'modes' => $writing,
                                     'description' => 'the language of fields: (default: the default language of content)' ),
                'fields' => array( 'type' => 'map', 'modes' => $writing,
                                   'description' => 'attribute identifier => value (first_name, last_name ...); the account is set by login:, email: and password:' ),
                'missing' => array( 'type' => 'string', 'default' => 'fail', 'values' => array( 'fail', 'skip' ), 'modes' => array( 'update', 'delete' ),
                                    'description' => 'what happens when the user is not there: fail (the default) or skip' ),
            ) + self::operationKeys( $writing ),
            'values' => array(
                'object_id' => 'the id of the user\'s object (the user id)',
                'node_id' => 'the id of its main node',
                'remote_id' => 'its remote id',
                'login' => 'the login',
                'email' => 'the e-mail address',
                'created' => 'true when the step created the user',
                'changed' => 'true when the step changed it (created, updated or deleted)',
            ),
            'example' => "- type: user\n  mode: ensure\n  login: editor\n  email: editor@example.com\n  password: \"\${ref.editor_password}\"\n"
                       . "  groups: [ \"remote:news-editors\" ]\n  fields: { first_name: News, last_name: Editor }\n  references: { editor: object_id }\n",
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
        $secrets = isset( $step['secrets'] ) && is_array( $step['secrets'] ) ? $step['secrets'] : array();
        foreach ( $secrets as $key => $name )
        {
            if ( !in_array( $key, self::SECRET_KEYS, true ) )
                $errors[] = "secrets.$key: secrets names " . implode( ', ', self::SECRET_KEYS );
            else if ( isset( $step[$key] ) )
                $errors[] = "secrets.$key and $key: exclude each other";
            if ( !is_string( $name ) || ( !expMigrationReferences::contains( $name ) && !expSecrets::validName( $name ) ) )
                $errors[] = "secrets.$key: not the name of a secret";
        }
        if ( isset( $secrets['password'] ) && isset( $step['password_hash'] ) )
            $errors[] = 'secrets.password and password_hash: exclude each other';
        if ( in_array( $mode, array( 'create', 'ensure' ), true ) )
        {
            foreach ( array( 'login', 'email' ) as $key )
                if ( ( !isset( $step[$key] ) || trim( (string)$step[$key] ) === '' ) && !isset( $secrets[$key] ) )
                    $errors[] = "$key is missing (mode $mode)";
            if ( empty( $step['groups'] ) )
                $errors[] = "groups is missing: a user is in at least one user group (mode $mode)";
        }
        if ( isset( $step['login'] ) && !expMigrationReferences::contains( $step['login'] ) && ( strpos( (string)$step['login'], '|' ) !== false || trim( (string)$step['login'] ) !== (string)$step['login'] ) )
            $errors[] = 'login: a login has no | and no leading or trailing space';
        if ( isset( $step['email'] ) && !expMigrationReferences::contains( $step['email'] ) && !eZMail::validate( (string)$step['email'] ) )
            $errors[] = 'email: ' . expMigrationStepSchema::show( $step['email'] ) . ' is not an e-mail address';
        if ( isset( $step['password'] ) && isset( $step['password_hash'] ) )
            $errors[] = 'password: and password_hash: exclude each other';
        if ( isset( $step['password_hash'] ) !== isset( $step['password_hash_type'] ) )
            $errors[] = 'password_hash: and password_hash_type: go together';
        if ( in_array( $mode, array( 'update', 'delete' ), true ) && !isset( $step['user'] ) )
            $errors[] = "user is missing (mode $mode)";
        foreach ( array( 'groups', 'remove_groups' ) as $key )
            if ( isset( $step[$key] ) && is_array( $step[$key] ) )
                foreach ( $step[$key] as $i => $group )
                    if ( ( $problem = expMigrationIdentifier::problem( 'node', $group ) ) !== null )
                        $errors[] = "{$key}[$i]: $problem";
        if ( isset( $step['fields'] ) )
            $errors = array_merge( $errors, self::fieldProblems( $step['fields'], 'fields' ) );
        if ( isset( $step['language'] ) && !expMigrationReferences::contains( $step['language'] )
             && ( $problem = expMigrationIdentifier::problem( 'language', $step['language'] ) ) !== null )
            $errors[] = "language: $problem";
        if ( $mode === 'update' && !array_intersect( array_keys( $step ), array( 'email', 'password', 'password_hash', 'secrets', 'enabled', 'groups', 'remove_groups', 'fields' ) ) )
            $errors[] = 'nothing to update';
        return $errors;
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function caches( array $step )
    {
        return array( 'content', 'user_info_cache' );
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
        $groups = isset( $step['groups'] ) && is_array( $step['groups'] ) ? implode( ', ', array_map( array( 'expMigrationStepSchema', 'show' ), $step['groups'] ) ) : '';
        $what = array();
        foreach ( array( 'email', 'enabled' ) as $key )
            if ( isset( $step[$key] ) || isset( $step['secrets'][$key] ) )
                $what[] = $key;
        if ( isset( $step['password'] ) || isset( $step['password_hash'] ) || isset( $step['secrets']['password'] ) )
            $what[] = 'the password';
        $login = isset( $step['login'] ) ? (string)$step['login']
               : ( isset( $step['secrets']['login'] ) ? 'of the secret ' . $step['secrets']['login'] : '' );
        if ( isset( $step['fields'] ) && is_array( $step['fields'] ) )
            $what[] = implode( ', ', array_keys( $step['fields'] ) );
        switch ( $step['mode'] )
        {
            case 'create':
                return array( "create the user $login in the group(s) $groups" );
            case 'ensure':
                return array( "ensure the user $login in the group(s) $groups: create it when missing, change what differs" );
            case 'update':
                $lines = array();
                $target = 'the user ' . expMigrationStepSchema::show( $step['user'] );
                if ( $what )
                    $lines[] = "update $target: " . implode( ', ', $what );
                if ( $groups !== '' )
                    $lines[] = "add $target to the group(s) $groups";
                if ( !empty( $step['remove_groups'] ) && is_array( $step['remove_groups'] ) )
                    $lines[] = "remove $target from the group(s) " . implode( ', ', array_map( array( 'expMigrationStepSchema', 'show' ), $step['remove_groups'] ) );
                return $lines;
            case 'delete':
                return array( 'delete the user ' . expMigrationStepSchema::show( $step['user'] ) . ' for good' );
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
        $step = self::withSecrets( $step );
        switch ( $step['mode'] )
        {
            case 'create':
                if ( eZUser::fetchByName( (string)$step['login'] ) instanceof eZUser )
                    throw new RuntimeException( 'a user with the login ' . $step['login'] . ' is there already (mode ensure changes it)' );
                return $this->createUser( $step );
            case 'ensure':
                $user = eZUser::fetchByName( (string)$step['login'] );
                return $user instanceof eZUser ? $this->updateUser( $user, $step, true ) : $this->createUser( $step );
            case 'update':
            case 'delete':
                $id = expMigrationIdentifier::find( 'user', $step['user'] );
                $user = $id !== null ? eZUser::fetch( $id ) : null;
                if ( !$user instanceof eZUser )
                {
                    if ( !isset( $step['missing'] ) || $step['missing'] !== 'skip' )
                        throw new RuntimeException( 'the user ' . expMigrationStepSchema::show( $step['user'] ) . ' is not on this installation (missing: skip lets the step pass)' );
                    return self::result( array( 'the user ' . expMigrationStepSchema::show( $step['user'] ) . ' is not there: skipped (missing: skip)' ),
                                         array( 'object_id' => null, 'node_id' => null, 'remote_id' => null, 'login' => null, 'email' => null,
                                                'created' => false, 'changed' => false ) );
                }
                if ( $step['mode'] === 'update' )
                    return $this->updateUser( $user, $step, false );
                $object = eZContentObject::fetch( (int)$user->attribute( 'contentobject_id' ) );
                $values = self::userValues( $object, $user );
                $this->delete( $object, array( 'trash' => false ) + $step );
                eZUser::purgeUserCacheByUserId( (int)$values['object_id'] );
                return self::result( array( 'deleted the user ' . $values['login'] . ' (' . $values['object_id'] . ')' ), $values + array( 'created' => false, 'changed' => true ) );
        }
        throw new RuntimeException( 'the mode ' . $step['mode'] . ' is not one of create, update, ensure, delete' );
    }

    /**
     * Removes the user a create made.
     *
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function revert( array $step, $context )
    {
        if ( !$this->revertible( $step ) )
            throw new RuntimeException( 'only a create can be undone by itself; give the migration a down:' );
        $step = self::withSecrets( $step );
        $user = eZUser::fetchByName( (string)$step['login'] );
        if ( !$user instanceof eZUser )
            return array( 'the user ' . $step['login'] . ' is not there: nothing to undo' );
        $object = eZContentObject::fetch( (int)$user->attribute( 'contentobject_id' ) );
        $this->delete( $object, array( 'trash' => false ) + $step );
        return array( 'removed the user ' . $step['login'] . ' (' . $user->attribute( 'contentobject_id' ) . ')' );
    }

    /**
     * A step with the values secrets: names read from the secret store (login, email, password).
     *
     * @param array $step
     * @return array
     * @throws RuntimeException naming the secret (never a value) when it is missing
     */
    public static function withSecrets( array $step )
    {
        if ( !isset( $step['secrets'] ) || !is_array( $step['secrets'] ) )
            return $step;
        foreach ( $step['secrets'] as $key => $name )
            if ( in_array( $key, self::SECRET_KEYS, true ) && !isset( $step[$key] ) )
                $step[$key] = expSecrets::required( (string)$name, "secrets.$key of a user step" );
        unset( $step['secrets'] );
        return $step;
    }

    // ---- the modes ---------------------------------------------------------------------------------------------

    /**
     * @param array $step
     * @return array
     */
    protected function createUser( array $step )
    {
        if ( eZUser::requireUniqueEmail() && eZUser::fetchByEmail( (string)$step['email'] ) )
            throw new RuntimeException( 'another account has the e-mail address ' . $step['email'] . ' (site.ini [UserSettings] RequireUniqueEmail)' );
        $class = eZContentClass::fetch( expMigrationIdentifier::resolve( 'class', $step['class'] ) );
        if ( !$class instanceof eZContentClass )
            throw new RuntimeException( 'no class ' . expMigrationStepSchema::show( $step['class'] ) );
        $account = null;
        foreach ( $class->fetchAttributes() as $attribute )
            if ( $attribute->attribute( 'data_type_string' ) === eZUserType::DATA_TYPE_STRING )
                $account = (string)$attribute->attribute( 'identifier' );
        if ( $account === null )
            throw new RuntimeException( 'the class ' . $class->attribute( 'identifier' ) . ' has no user account attribute (ezuser)' );
        $groups = array_values( (array)$step['groups'] );
        $content = array( 'mode' => 'create', 'class' => (int)$class->attribute( 'id' ), 'parent' => $groups[0],
                          'fields' => ( isset( $step['fields'] ) ? (array)$step['fields'] : array() ) + array( $account => $step['login'] . '|' . $step['email'] ) );
        foreach ( array( 'remote_id', 'language', 'workflows', 'notify' ) as $key )
            if ( isset( $step[$key] ) )
                $content[$key] = $step[$key];
        $this->create( $content );
        $user = eZUser::fetchByName( (string)$step['login'] );
        if ( !$user instanceof eZUser )
            throw new RuntimeException( 'the account ' . $step['login'] . ' was not stored (another account has the login or the e-mail address)' );
        $userId = (int)$user->attribute( 'contentobject_id' );
        $db = eZDB::instance();
        $db->begin();
        try
        {
            self::writePassword( $user, $step );
            $user->store();
            eZUserType::storeIsEnabled( $userId, isset( $step['enabled'] ) ? self::flagOf( $step['enabled'] ) : 1 );
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        $lines = array( 'created the user ' . $step['login'] . " ($userId) in node " . expMigrationStepSchema::show( $groups[0] ) );
        foreach ( array_slice( $groups, 1 ) as $group )
            $lines[] = $this->addGroup( $userId, $group, $step );
        eZUser::purgeUserCacheByUserId( $userId );
        $object = eZContentObject::fetch( $userId );
        return self::result( $lines, self::userValues( $object, eZUser::fetch( $userId ) ) + array( 'created' => true, 'changed' => true ) );
    }

    /**
     * @param eZUser $user
     * @param array $step
     * @param bool $ensure
     * @return array
     */
    protected function updateUser( eZUser $user, array $step, $ensure )
    {
        $userId = (int)$user->attribute( 'contentobject_id' );
        $label = 'the user ' . $user->attribute( 'login' );
        $lines = array();
        $object = eZContentObject::fetch( $userId );
        if ( !empty( $step['fields'] ) )
        {
            $content = array( 'mode' => $ensure ? 'ensure' : 'update', 'fields' => (array)$step['fields'] );
            foreach ( array( 'language', 'workflows', 'notify' ) as $key )
                if ( isset( $step[$key] ) )
                    $content[$key] = $step[$key];
            $result = $this->update( $object, $content, $ensure );
            if ( !empty( $result['values']['changed'] ) )
                $lines = array_merge( $lines, $result['lines'] );
        }
        $account = array();
        $db = eZDB::instance();
        $db->begin();
        try
        {
            if ( isset( $step['email'] ) && (string)$user->attribute( 'email' ) !== (string)$step['email'] )
            {
                $other = eZUser::fetchByEmail( (string)$step['email'] );
                if ( eZUser::requireUniqueEmail() && $other instanceof eZUser && (int)$other->attribute( 'contentobject_id' ) !== $userId )
                    throw new RuntimeException( 'another account has the e-mail address ' . $step['email'] );
                $user->setAttribute( 'email', (string)$step['email'] );
                $account[] = 'email';
            }
            if ( !$ensure && ( isset( $step['password'] ) || isset( $step['password_hash'] ) ) )
            {
                self::writePassword( $user, $step );
                $account[] = 'the password';
            }
            if ( $account )
                $user->store();
            if ( isset( $step['enabled'] ) )
            {
                $setting = eZUserSetting::fetch( $userId );
                if ( !$setting instanceof eZUserSetting || (int)$setting->attribute( 'is_enabled' ) !== self::flagOf( $step['enabled'] ) )
                {
                    eZUserType::storeIsEnabled( $userId, self::flagOf( $step['enabled'] ) );
                    $account[] = $step['enabled'] ? 'enabled' : 'disabled';
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
        if ( $account )
            $lines[] = "updated the account of $label: " . implode( ', ', $account );
        $in = array();
        foreach ( (array)eZContentObjectTreeNode::fetchByContentObjectID( $userId ) as $node )
            $in[(int)$node->attribute( 'parent_node_id' )] = (int)$node->attribute( 'node_id' );
        foreach ( isset( $step['groups'] ) ? (array)$step['groups'] : array() as $group )
            if ( !isset( $in[expMigrationIdentifier::resolve( 'node', $group )] ) )
                $lines[] = $this->addGroup( $userId, $group, $step );
        foreach ( !$ensure && isset( $step['remove_groups'] ) ? (array)$step['remove_groups'] : array() as $group )
        {
            $parentId = expMigrationIdentifier::resolve( 'node', $group );
            if ( !isset( $in[$parentId] ) )
            {
                $lines[] = "$label is not in the group of node $parentId";
                continue;
            }
            if ( $in[$parentId] === (int)$object->attribute( 'main_node_id' ) )
                throw new RuntimeException( "$label cannot leave the group of node $parentId: it is the main location (give the user another main location first)" );
            self::operation( 'removelocation', array( 'node_list' => array( $in[$parentId] ) ), $step );
            $lines[] = "removed $label from the group of node $parentId";
        }
        eZUser::purgeUserCacheByUserId( $userId );
        $changed = (bool)array_filter( $lines, function ( $line ) { return strpos( $line, ' is not in the group' ) === false; } );
        if ( !$lines )
            $lines[] = "$label is as the step has it";
        return self::result( $lines, self::userValues( eZContentObject::fetch( $userId ), eZUser::fetch( $userId ) ) + array( 'created' => false, 'changed' => $changed ) );
    }

    // ---- helpers ---------------------------------------------------------------------------------------------

    /**
     * Sets the password of an account (not stored): password: hashed as eZUser hashes it, or password_hash: as it is.
     *
     * @param eZUser $user
     * @param array $step
     */
    protected static function writePassword( eZUser $user, array $step )
    {
        if ( isset( $step['password'] ) )
        {
            $type = eZUser::hashType();
            $user->setAttribute( 'password_hash', eZUser::createHash( (string)$user->attribute( 'login' ), (string)$step['password'], eZUser::site(), $type ) );
            $user->setAttribute( 'password_hash_type', $type );
        }
        else if ( isset( $step['password_hash'] ) )
        {
            $user->setAttribute( 'password_hash', (string)$step['password_hash'] );
            $user->setAttribute( 'password_hash_type', eZUser::passwordHashTypeID( (string)$step['password_hash_type'] ) );
        }
    }

    /**
     * Adds a user to a further group: a location below the group's node.
     *
     * @param int $userId
     * @param mixed $group a node identifier
     * @param array $step
     * @return string
     */
    protected function addGroup( $userId, $group, array $step )
    {
        $parentId = expMigrationIdentifier::resolve( 'node', $group );
        $object = eZContentObject::fetch( (int)$userId );
        self::operation( 'addlocation', array( 'node_id' => (int)$object->attribute( 'main_node_id' ), 'object_id' => (int)$userId,
                                               'select_node_id_array' => array( $parentId ) ), $step );
        if ( self::nodeBelow( $userId, $parentId ) === null )
            throw new RuntimeException( "the user $userId got no location in the group of node $parentId" );
        return "added the user $userId to the group of node $parentId";
    }

    /**
     * @param mixed $value
     * @return int
     */
    protected static function flagOf( $value )
    {
        return $value ? 1 : 0;
    }

    /**
     * @param eZContentObject|null $object
     * @param eZUser|null $user
     * @return array
     */
    protected static function userValues( $object, $user )
    {
        $values = $object instanceof eZContentObject ? self::objectValues( $object ) : array( 'object_id' => null, 'node_id' => null, 'remote_id' => null );
        return array( 'object_id' => $values['object_id'], 'node_id' => $values['node_id'], 'remote_id' => $values['remote_id'],
                      'login' => $user instanceof eZUser ? (string)$user->attribute( 'login' ) : null,
                      'email' => $user instanceof eZUser ? (string)$user->attribute( 'email' ) : null );
    }
}
