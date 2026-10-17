<?php
/**
 * The code of bin/php/addoauthauthorizepolicy.php.
 * @description Grant oauth/authorize to the roles that let users sign in (6.0.15 upgrade; --dry-run lists them first)
 * Guide: doc/bc/6.0/oauth-authorize-policy.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Addoauthauthorizepolicy extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = $this->cli();
        $script = $this->script(
            array(
                'description' => "Grant the policy oauth/authorize to every role that lets users sign in (user/login),\n"
                    . "with the same siteaccess limitation, so the users who could authorize OAuth clients before 6.0.15\n"
                    . "still can. Roles that have the policy already are left alone; the script can run again.\n\n"
                    . "Example: php bin/php/addoauthauthorizepolicy.php --dry-run\n"
                    . "         php bin/php/addoauthauthorizepolicy.php --role=Editor --role=5",
                'use-session' => false,
                'use-modules' => true,
                'use-extensions' => true
            )
        );
        $options = $this->startup(
            "[dry-run][role:*]",
            '',
            array(
                'dry-run' => "List what would be granted, change nothing",
                'role' => "Only this role (ID or name), may be repeated; a listed role without user/login gets an unlimited policy"
            )
        );

        $dryRun = (bool)$options['dry-run'];
        $onlyRoles = $options['role'] ? (array)$options['role'] : array();
        $anonymousUserId = (int)\eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' );

        $roles = array();
        $roleData = array();
        foreach ( \eZRole::fetchList() as $role )
        {
            $roles[(int)$role->attribute( 'id' )] = $role;
            $roleData[] = \expOAuthAuthorizePolicyGrant::roleData( $role, $anonymousUserId );
        }

        $plan = \expOAuthAuthorizePolicyGrant::plan( $roleData, $onlyRoles );
        $known = array();
        $granted = 0;
        foreach ( $plan as $entry )
        {
            $known[] = (string)$entry['id'];
            $known[] = $entry['name'];
            $label = sprintf( "%-30s (%d)", $entry['name'], $entry['id'] );
            if ( $entry['action'] !== 'grant' )
            {
                $cli->output( "skip   $label: {$entry['reason']}" );
                continue;
            }
            $limits = empty( $entry['limitations'] )
                ? 'every siteaccess'
                : 'SiteAccess ' . implode( ', ', $this->siteAccessNames( $entry['limitations']['SiteAccess'] ) );
            $cli->output( ( $dryRun ? 'would grant' : 'grant' ) . " $label: oauth/authorize, $limits ({$entry['reason']})" );
            if ( !$dryRun )
                \expOAuthAuthorizePolicyGrant::apply( $roles[$entry['id']], $entry );
            $granted++;
        }

        foreach ( $onlyRoles as $name )
        {
            if ( !in_array( (string)$name, $known, true ) )
                $cli->warning( "No role '$name'" );
        }

        if ( $granted && !$dryRun )
            \eZRole::expireCache();

        $cli->output();
        $cli->output( $dryRun ? "$granted role(s) would get oauth/authorize (dry run, nothing changed)"
                              : "$granted role(s) got oauth/authorize" );
        $script->shutdown();
    }

    /**
     * @param array $checksums SiteAccess limitation values
     * @return array the siteaccess names, or the checksum where no siteaccess has it
     */
    private function siteAccessNames( array $checksums )
    {
        $names = array();
        foreach ( (array)\eZINI::instance()->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) as $name )
        {
            $checksum = crc32( $name );
            $names[(string)$checksum] = $name;
            $names[sprintf( '%u', $checksum )] = $name;
        }
        $result = array();
        foreach ( $checksums as $value )
            $result[] = isset( $names[(string)$value] ) ? $names[(string)$value] : (string)$value;
        return $result;
    }
}

}
