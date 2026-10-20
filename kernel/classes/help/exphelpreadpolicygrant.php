<?php
/**
 * File containing the expHelpReadPolicyGrant class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Plans and applies the grant of the policy exphelp/read (bin/php/addexphelpreadpolicy.php): reading the help system
 * (doc/en/us/features/6.0/help-system.md). New installations give it to the Editor role (cleandata.sql); an installation
 * from before the help system has to grant it, or editors see no Help control and get "access denied" on the help
 * pages. Administrators have it already through their * / * policy.
 *
 * Only the roles asked for are considered (by ID or name; the Editor role when none is named). A role that already
 * reads the help (exphelp/read, exphelp/*, or * / *) is left alone, so the script can run again. The policy has no
 * limitation: the module's read function has none.
 *
 * plan() works on plain arrays, so it is tested without a database.
 */
class expHelpReadPolicyGrant
{
    const MODULE = 'exphelp';
    const FUNCTION_NAME = 'read';
    const DEFAULT_ROLE = 'Editor';

    /**
     * The role as plain data for plan().
     *
     * @param eZRole $role
     * @return array id, name, policies (each module, function)
     */
    public static function roleData( $role )
    {
        $policies = array();
        foreach ( $role->policyList() as $policy )
        {
            $policies[] = array( 'module' => (string)$policy->attribute( 'module_name' ),
                                 'function' => (string)$policy->attribute( 'function_name' ) );
        }
        return array( 'id' => (int)$role->attribute( 'id' ), 'name' => (string)$role->attribute( 'name' ), 'policies' => $policies );
    }

    /**
     * Decides what each role gets.
     *
     * @param array $roles roles as roleData() returns them
     * @param array $onlyRoles role IDs or names; empty for the Editor role
     * @return array one entry per role: id, name, action ("grant" or "skip"), reason
     */
    public static function plan( array $roles, array $onlyRoles = array() )
    {
        $onlyRoles = $onlyRoles ? array_map( 'strval', $onlyRoles ) : array( self::DEFAULT_ROLE );
        $plan = array();
        foreach ( $roles as $role )
        {
            $entry = array( 'id' => (int)$role['id'], 'name' => (string)$role['name'] );
            if ( !in_array( (string)$role['id'], $onlyRoles, true ) && !in_array( (string)$role['name'], $onlyRoles, true ) )
                $plan[] = $entry + array( 'action' => 'skip', 'reason' => 'not listed' );
            else if ( self::hasRead( $role['policies'] ) )
                $plan[] = $entry + array( 'action' => 'skip', 'reason' => 'already has exphelp/read' );
            else
                $plan[] = $entry + array( 'action' => 'grant', 'reason' => 'listed' );
        }
        return $plan;
    }

    /**
     * Adds the policy to the role.
     *
     * @param eZRole $role
     * @param array $entry a "grant" entry of plan()
     * @return eZPolicy|false
     */
    public static function apply( $role, array $entry )
    {
        if ( !isset( $entry['action'] ) || $entry['action'] !== 'grant' )
            return false;
        return $role->appendPolicy( self::MODULE, self::FUNCTION_NAME, array() );
    }

    /**
     * @param array $policies
     * @return bool whether a policy lets the role read the help already
     */
    public static function hasRead( array $policies )
    {
        foreach ( $policies as $policy )
        {
            if ( $policy['module'] === '*' )
                return true;
            if ( $policy['module'] === self::MODULE && ( $policy['function'] === '*' || $policy['function'] === self::FUNCTION_NAME ) )
                return true;
        }
        return false;
    }
}

?>
