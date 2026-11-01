<?php
/**
 * File containing the expMigrationStepUserGroup class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "user_group": creates, updates, ensures and deletes user groups.
 *
 *   - type: user_group
 *     mode: ensure                     create, update, ensure, delete
 *     parent: 5                        the group it is in (node 5: Users of the clean data)
 *     remote_id: news-editors
 *     name: News editors
 *     names: { ger-DE: Nachrichtenredaktion }
 *     description: Write and publish the news
 *
 * A user group is a content object of the class user_group, so the step is the content step with the keys of a
 * group: it is created, published, translated and deleted exactly as "content" does it (each language a version of its
 * own, workflows only with workflows: true). Roles are assigned to it with the step "role" (mode assign). A create with
 * a remote_id undoes itself (the group is removed again).
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepUserGroup extends expMigrationStepContent
{
    /** @return string */
    public function name()
    {
        return 'user_group';
    }

    /** @return array */
    public function schema()
    {
        $writing = array( 'create', 'update', 'ensure' );
        return array(
            'description' => 'Creates, updates, ensures or deletes a user group, with its name and description in one or more languages',
            'modes' => array( 'create', 'update', 'ensure', 'delete' ),
            'keys' => array(
                'parent' => array( 'type' => 'identifier', 'kind' => 'node', 'modes' => array( 'create', 'ensure' ),
                                   'description' => 'the node of the group the new group is in (create, ensure)' ),
                'group' => array( 'type' => 'identifier', 'kind' => 'object', 'modes' => array( 'update', 'delete' ),
                                  'description' => 'the group to update or delete: an object identifier (remote:<remote id>, id, path:)' ),
                'remote_id' => array( 'type' => 'string', 'modes' => array( 'create', 'ensure' ),
                                      'description' => 'the remote id of a new group; ensure finds the group by it' ),
                'class' => array( 'type' => 'identifier', 'kind' => 'class', 'default' => 'user_group', 'modes' => array( 'create', 'ensure' ),
                                  'description' => 'the class of a new group (default: user_group)' ),
                'language' => array( 'type' => 'string', 'modes' => $writing,
                                     'description' => 'the language of name: and description: (default: the default language of content)' ),
                'name' => array( 'type' => 'string', 'modes' => $writing, 'description' => 'the name of the group' ),
                'description' => array( 'type' => 'string', 'modes' => $writing, 'description' => 'the description of the group' ),
                'names' => array( 'type' => 'map', 'modes' => $writing, 'description' => 'language => the name of the group in it: its translations' ),
                'fields' => array( 'type' => 'map', 'modes' => $writing, 'description' => 'further attributes of the class: attribute identifier => value' ),
                'missing' => array( 'type' => 'string', 'default' => 'fail', 'values' => array( 'fail', 'skip' ), 'modes' => array( 'update', 'delete' ),
                                    'description' => 'what happens when the group is not there: fail (the default) or skip' ),
            ) + self::operationKeys( $writing ),
            'values' => array(
                'object_id' => 'the id of the group\'s object',
                'node_id' => 'the id of its main node (the parent: of users and groups in it)',
                'remote_id' => 'its remote id',
                'name' => 'its name',
                'created' => 'true when the step created the group',
                'changed' => 'true when the step changed it (created, updated or deleted)',
            ),
            'example' => "- type: user_group\n  mode: ensure\n  parent: 5\n  remote_id: news-editors\n  name: News editors\n"
                       . "  names: { ger-DE: Nachrichtenredaktion }\n  references: { editors: node_id, editors_object: object_id }\n",
        );
    }

    /**
     * The step as the content step takes it. Pure.
     *
     * @param array $step
     * @return array
     */
    public static function contentStep( array $step )
    {
        $content = array( 'mode' => $step['mode'] );
        foreach ( array( 'parent', 'remote_id', 'class', 'language', 'missing', 'workflows', 'notify' ) as $key )
            if ( array_key_exists( $key, $step ) )
                $content[$key] = $step[$key];
        if ( isset( $step['group'] ) )
            $content['object'] = $step['group'];
        if ( !isset( $content['class'] ) && in_array( $step['mode'], array( 'create', 'ensure' ), true ) )
            $content['class'] = 'user_group';
        $fields = isset( $step['fields'] ) && is_array( $step['fields'] ) ? $step['fields'] : array();
        foreach ( array( 'name', 'description' ) as $key )
            if ( isset( $step[$key] ) )
                $fields[$key] = $step[$key];
        if ( $fields )
            $content['fields'] = $fields;
        if ( isset( $step['names'] ) && is_array( $step['names'] ) )
            foreach ( $step['names'] as $language => $name )
                $content['translations'][$language] = array( 'name' => $name );
        if ( $step['mode'] === 'delete' )
            $content['trash'] = false;
        return $content;
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function validate( array $step )
    {
        $errors = parent::validate( self::contentStep( $step ) );
        $mode = isset( $step['mode'] ) ? $step['mode'] : null;
        if ( in_array( $mode, array( 'create', 'ensure' ), true ) && !isset( $step['name'] ) )
            $errors[] = "name is missing (mode $mode)";
        return array_values( array_map( function ( $error ) {
            return str_replace( array( 'name the object by object: or by node: (one of them, mode update)', 'name the object by object: or by node: (one of them, mode delete)' ),
                                'group is missing', $error );
        }, $errors ) );
    }

    /**
     * @param array $step
     * @return bool
     */
    public function revertible( array $step )
    {
        return parent::revertible( self::contentStep( $step ) );
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( array $step, $context )
    {
        $lines = parent::plan( self::contentStep( $step ), $context );
        return array_map( function ( $line ) {
            return preg_replace( array( '/^create a "?user_group"? /', '/^ensure the "?user_group"? /', '/the object /' ),
                                 array( 'create a user group ', 'ensure the user group ', 'the user group ' ), $line );
        }, $lines );
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return array
     */
    public function apply( array $step, $context )
    {
        return parent::apply( self::contentStep( $step ), $context );
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function revert( array $step, $context )
    {
        return parent::revert( self::contentStep( $step ), $context );
    }
}
