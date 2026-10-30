<?php
/**
 * File containing the expUpgradeStepOAuthAuthorizePolicy class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * oauth/authorize for the roles that let users sign in, so they keep authorizing REST clients, through
 * expOAuthAuthorizePolicyGrant as bin/php/addoauthauthorizepolicy.php with its default options.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepOAuthAuthorizePolicy extends expUpgradeScriptStep
{
    public function id()
    {
        return '6.0.15-oauth-authorize-policy';
    }

    public function description()
    {
        return 'oauth/authorize for the roles that let users sign in (bin/php/addoauthauthorizepolicy.php)';
    }

    /** @return array the grant entries with their roles */
    protected function grants()
    {
        $anonymous = expOAuthAuthorizePolicyGrant::anonymousObjectIds( (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' ) );
        $roles = array();
        $data = array();
        foreach ( eZRole::fetchList() as $role )
        {
            $roles[(int)$role->attribute( 'id' )] = $role;
            $data[] = expOAuthAuthorizePolicyGrant::roleData( $role, $anonymous );
        }
        $out = array();
        foreach ( expOAuthAuthorizePolicyGrant::plan( $data, array() ) as $entry )
            if ( $entry['action'] === 'grant' && isset( $roles[$entry['id']] ) )
                $out[] = array( $roles[$entry['id']], $entry );
        return $out;
    }

    protected function changes( expUpgradeContext $context )
    {
        $lines = array();
        foreach ( $this->grants() as $pair )
            $lines[] = sprintf( 'grant oauth/authorize to %s (%d): %s', $pair[1]['name'], $pair[1]['id'], $pair[1]['reason'] );
        return $lines;
    }

    protected function write( expUpgradeContext $context )
    {
        $done = array();
        foreach ( $this->grants() as $pair )
        {
            expOAuthAuthorizePolicyGrant::apply( $pair[0], $pair[1] );
            $done[] = sprintf( 'granted oauth/authorize to %s (%d)', $pair[1]['name'], $pair[1]['id'] );
        }
        eZRole::expireCache();
        return $done;
    }
}
