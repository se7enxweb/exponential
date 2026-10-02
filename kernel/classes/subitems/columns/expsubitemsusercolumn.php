<?php
/**
 * Subitems list columns for user accounts: the rows below a user group. On any other object
 * every field is null.
 *
 * Field= picks the column: login, email, enabled, locked, last_visit, login_count,
 * failed_logins, roles, role_count. These are personal data: the shipped blocks give each a
 * Policy[] (role/read, and role/assign for the e-mail address), so the column is offered,
 * computed and sent only to users who may manage accounts. Cost: one ezuser row per object,
 * one ezuservisit row for the visit fields, the role fields two queries (the user's
 * locations, then the roles assigned along their paths).
 * Guide: doc/bc/6.0/subitems-table-options.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expSubitemsUserColumn extends expSubitemsFieldColumn
{
    protected function fieldLogin( eZContentObjectTreeNode $node )
    {
        $user = self::user( $node );
        return $user ? (string)$user->attribute( 'login' ) : null;
    }

    protected function fieldEmail( eZContentObjectTreeNode $node )
    {
        $user = self::user( $node );
        if ( !$user )
            return null;
        $email = (string)$user->attribute( 'email' );
        return $email === '' ? null : $email;
    }

    /** The account may log in (Setup > Users: enabled), from ezuser_setting. */
    protected function fieldEnabled( eZContentObjectTreeNode $node )
    {
        $user = self::user( $node );
        return $user ? (bool)$user->isEnabled( false ) : null;
    }

    /** Locked out after too many failed logins ([UserSettings] MaxNumberOfFailedLogin). */
    protected function fieldLocked( eZContentObjectTreeNode $node )
    {
        $user = self::user( $node );
        if ( !$user )
            return null;
        $max = eZUser::maxNumberOfFailedLogin();
        if ( !$max )
            return false;
        $visit = self::visit( $node );
        return $visit !== null && (int)$visit['failed_login_attempts'] >= (int)$max;
    }

    /** The start of the user's last visit; null when the user never logged in. */
    protected function fieldLastVisit( eZContentObjectTreeNode $node )
    {
        $visit = self::visit( $node );
        if ( $visit === null )
            return null;
        $time = (int)$visit['last_visit_timestamp'];
        return $time > 0 ? $time : null;
    }

    protected function fieldLoginCount( eZContentObjectTreeNode $node )
    {
        if ( !self::user( $node ) )
            return null;
        $visit = self::visit( $node );
        return $visit === null ? 0 : (int)$visit['login_count'];
    }

    protected function fieldFailedLogins( eZContentObjectTreeNode $node )
    {
        if ( !self::user( $node ) )
            return null;
        $visit = self::visit( $node );
        return $visit === null ? 0 : (int)$visit['failed_login_attempts'];
    }

    /** The names of the roles that apply to the user, directly or through their groups. */
    protected function fieldRoles( eZContentObjectTreeNode $node )
    {
        $user = self::user( $node );
        if ( !$user )
            return null;
        $objectID = (int)$user->attribute( 'contentobject_id' );
        return self::memo( 'roles', $objectID, function () use ( $user )
        {
            $names = array();
            // the user's own assignments and those of every group above it, as policies are resolved
            foreach ( $user->roles() as $role )
            {
                $name = (string)$role->attribute( 'name' );
                $names[$name] = $name;
            }
            ksort( $names );
            return array_values( $names );
        } );
    }

    protected function fieldRoleCount( eZContentObjectTreeNode $node )
    {
        $roles = $this->fieldRoles( $node );
        return $roles === null ? null : count( $roles );
    }

    /** The eZUser of the node's object, null when it is not a user (memoised). */
    protected static function user( eZContentObjectTreeNode $node )
    {
        $objectID = (int)$node->attribute( 'contentobject_id' );
        return self::memo( 'user', $objectID, function () use ( $objectID )
        {
            $user = eZUser::fetch( $objectID );
            return $user instanceof eZUser ? $user : null;
        } );
    }

    /** The user's ezuservisit row as an array, null for non-users and users who never visited. */
    protected static function visit( eZContentObjectTreeNode $node )
    {
        if ( !self::user( $node ) )
            return null;
        $objectID = (int)$node->attribute( 'contentobject_id' );
        return self::memo( 'visit', $objectID, function () use ( $objectID )
        {
            $row = eZPersistentObject::fetchObject( expSubitemsUserVisitRow::definition(), null, array( 'user_id' => $objectID ), false );
            return is_array( $row ) ? $row : null;
        } );
    }
}
