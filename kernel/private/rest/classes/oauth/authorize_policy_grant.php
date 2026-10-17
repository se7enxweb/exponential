<?php
/**
 * File containing the expOAuthAuthorizePolicyGrant class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package oauth
 */

/**
 * Plans and applies the upgrade grant of the policy oauth/authorize (bin/php/addoauthauthorizepolicy.php).
 *
 * Before 6.0.15 every signed in user could authorize an OAuth client, because the check in oauth/authorize never
 * denied. To keep exactly that after the upgrade, every role that lets users sign in (user/login, user/*) gets
 * oauth/authorize with the same siteaccesses: an unlimited user/login gives an unlimited oauth/authorize, user/login
 * policies limited by SiteAccess give one oauth/authorize limited to all of their siteaccesses together.
 *
 * Roles that already have oauth/authorize (or oauth/*, or * / *) are left alone, so the script can run again.
 * Roles assigned only to the anonymous user are skipped: an anonymous user can never authorize a client.
 * With a list of roles (--role), only those roles are considered; a listed role without user/login gets an
 * unlimited oauth/authorize, and a listed role assigned only to the anonymous user is granted too (it was asked for).
 *
 * plan() works on plain arrays, so it is tested without a database.
 */
class expOAuthAuthorizePolicyGrant
{
    /**
     * The role as plain data for plan().
     *
     * @param eZRole $role
     * @param int $anonymousUserId site.ini [UserSettings] AnonymousUserID
     * @return array id, name, anonymous_only, policies (each module, function, limitations: identifier => values)
     */
    public static function roleData( $role, $anonymousUserId )
    {
        $policies = array();
        foreach ( $role->policyList() as $policy )
        {
            $limitations = array();
            foreach ( $policy->limitationList( false ) as $limitation )
            {
                $limitations[$limitation->attribute( 'identifier' )] = array_map( 'strval', $limitation->allValues() );
            }
            $policies[] = array( 'module' => (string)$policy->attribute( 'module_name' ),
                                 'function' => (string)$policy->attribute( 'function_name' ),
                                 'limitations' => $limitations );
        }

        $assigned = array();
        foreach ( $role->fetchUserByRole() as $assignment )
        {
            if ( isset( $assignment['user_object'] ) && $assignment['user_object'] instanceof eZContentObject )
                $assigned[] = (int)$assignment['user_object']->attribute( 'id' );
        }
        $assigned = array_values( array_unique( $assigned ) );

        return array( 'id' => (int)$role->attribute( 'id' ),
                      'name' => (string)$role->attribute( 'name' ),
                      'anonymous_only' => $assigned === array( (int)$anonymousUserId ),
                      'policies' => $policies );
    }

    /**
     * Decides what each role gets.
     *
     * @param array $roles roles as roleData() returns them
     * @param array $onlyRoles role IDs or names; empty for every role that lets users sign in
     * @return array one entry per role: id, name, action ("grant" or "skip"), reason, and for a grant the
     *               limitations of the new policy (array() for unlimited, else array( 'SiteAccess' => values ))
     */
    public static function plan( array $roles, array $onlyRoles = array() )
    {
        $onlyRoles = array_map( 'strval', $onlyRoles );
        $plan = array();
        foreach ( $roles as $role )
        {
            $entry = array( 'id' => (int)$role['id'], 'name' => (string)$role['name'] );
            $listed = in_array( (string)$role['id'], $onlyRoles, true ) || in_array( (string)$role['name'], $onlyRoles, true );

            if ( $onlyRoles && !$listed )
            {
                $plan[] = $entry + array( 'action' => 'skip', 'reason' => 'not listed' );
                continue;
            }
            if ( self::hasAuthorize( $role['policies'] ) )
            {
                $plan[] = $entry + array( 'action' => 'skip', 'reason' => 'already has oauth/authorize' );
                continue;
            }

            $login = self::loginSiteAccesses( $role['policies'] );
            if ( $login === false && !$listed )
            {
                $plan[] = $entry + array( 'action' => 'skip', 'reason' => 'no user/login' );
                continue;
            }
            if ( !empty( $role['anonymous_only'] ) && !$listed )
            {
                $plan[] = $entry + array( 'action' => 'skip', 'reason' => 'assigned only to the anonymous user' );
                continue;
            }

            $limitations = is_array( $login ) ? array( 'SiteAccess' => $login ) : array();
            $plan[] = $entry + array( 'action' => 'grant',
                                      'reason' => $login === false ? 'listed' : ( $limitations ? 'user/login limited by siteaccess' : 'user/login' ),
                                      'limitations' => $limitations );
        }
        return $plan;
    }

    /**
     * Adds the planned policy to the role.
     *
     * @param eZRole $role
     * @param array $entry a "grant" entry of plan()
     * @return eZPolicy|false
     */
    public static function apply( $role, array $entry )
    {
        if ( !isset( $entry['action'] ) || $entry['action'] !== 'grant' )
            return false;
        return $role->appendPolicy( expOAuthAuthorizePolicy::MODULE, expOAuthAuthorizePolicy::FUNCTION_NAME,
                                    isset( $entry['limitations'] ) ? $entry['limitations'] : array() );
    }

    /**
     * @param array $policies
     * @return bool whether a policy grants oauth/authorize already
     */
    public static function hasAuthorize( array $policies )
    {
        foreach ( $policies as $policy )
        {
            if ( $policy['module'] === '*' )
                return true;
            if ( $policy['module'] === expOAuthAuthorizePolicy::MODULE
                 && ( $policy['function'] === '*' || $policy['function'] === expOAuthAuthorizePolicy::FUNCTION_NAME ) )
                return true;
        }
        return false;
    }

    /**
     * The siteaccesses the role lets users sign in to.
     *
     * @param array $policies
     * @return array|bool|null false: the role has no user/login; null: user/login without SiteAccess limitation
     *                         (every siteaccess); array: the SiteAccess values (CRC32 checksums) of all its user/login
     */
    public static function loginSiteAccesses( array $policies )
    {
        $found = false;
        $values = array();
        foreach ( $policies as $policy )
        {
            if ( $policy['module'] !== 'user' || ( $policy['function'] !== 'login' && $policy['function'] !== '*' ) )
                continue;
            $found = true;
            if ( !isset( $policy['limitations']['SiteAccess'] ) )
                return null;
            foreach ( (array)$policy['limitations']['SiteAccess'] as $value )
                $values[(string)$value] = (string)$value;
        }
        return $found ? array_values( $values ) : false;
    }
}
