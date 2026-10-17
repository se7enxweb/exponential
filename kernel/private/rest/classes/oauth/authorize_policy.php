<?php
/**
 * File containing the expOAuthAuthorizePolicy class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package oauth
 */

/**
 * Decides whether a signed in user may authorize an OAuth client in oauth/authorize.
 *
 * The user needs the policy oauth/authorize. Its optional SiteAccess limitation works like the one of user/login:
 * a limited policy without a SiteAccess limitation allows every siteaccess, one with it only the siteaccesses it
 * lists (stored as CRC32 checksums of their names).
 *
 * The check can be switched off with rest.ini [OAuthSettings] AuthorizeRequiresPolicy=disabled, which restores the
 * behavior of 6.0.14 and earlier (every signed in user may authorize a client). It is meant only as a temporary
 * setting during an upgrade, until bin/php/addoauthauthorizepolicy.php has granted the policy.
 */
class expOAuthAuthorizePolicy
{
    const MODULE = 'oauth';
    const FUNCTION_NAME = 'authorize';

    /**
     * Whether oauth/authorize requires the policy: everything but an explicit "disabled" enforces it.
     *
     * @param eZINI $restINI rest.ini
     * @return bool
     */
    public static function isEnforced( $restINI )
    {
        if ( !$restINI->hasVariable( 'OAuthSettings', 'AuthorizeRequiresPolicy' ) )
            return true;
        return strtolower( trim( (string)$restINI->variable( 'OAuthSettings', 'AuthorizeRequiresPolicy' ) ) ) !== 'disabled';
    }

    /**
     * Evaluates the result of eZUser::hasAccessTo( 'oauth', 'authorize' ) for a siteaccess.
     *
     * @param array|mixed $access the access result: accessWord yes, no or limited (with its policies)
     * @param string $siteAccessName the siteaccess oauth/authorize runs in
     * @return bool true only when the result grants the function in that siteaccess
     */
    public static function accessResultAllows( $access, $siteAccessName )
    {
        if ( !is_array( $access ) || !isset( $access['accessWord'] ) )
            return false;

        if ( $access['accessWord'] === 'yes' )
            return true;

        if ( $access['accessWord'] !== 'limited' || empty( $access['policies'] ) || !is_array( $access['policies'] ) )
            return false;

        // the values are stored by eZSys::ezcrc32(), signed or unsigned depending on [SiteSettings]
        // 64bitCompatibilityMode; both forms are accepted, so the check needs no site.ini
        $checksum = crc32( (string)$siteAccessName );
        $siteAccessCRCs = array( (string)$checksum, sprintf( '%u', $checksum ) );
        foreach ( $access['policies'] as $policy )
        {
            if ( !is_array( $policy ) )
                continue;
            // a policy without a SiteAccess limitation allows every siteaccess
            if ( !isset( $policy['SiteAccess'] ) )
                return true;
            if ( array_intersect( $siteAccessCRCs, array_map( 'strval', (array)$policy['SiteAccess'] ) ) )
                return true;
        }
        return false;
    }

    /**
     * Whether the user may authorize a client in the given siteaccess.
     *
     * @param eZUser $user
     * @param string $siteAccessName
     * @param eZINI|null $restINI rest.ini, read when not given
     * @return bool
     */
    public static function userMayAuthorize( $user, $siteAccessName, $restINI = null )
    {
        if ( $restINI === null )
            $restINI = eZINI::instance( 'rest.ini' );
        if ( !self::isEnforced( $restINI ) )
            return true;
        return self::accessResultAllows( $user->hasAccessTo( self::MODULE, self::FUNCTION_NAME ), $siteAccessName );
    }

    /**
     * The name of the current siteaccess, or '' when there is none (CLI).
     *
     * @return string
     */
    public static function currentSiteAccessName()
    {
        $access = class_exists( 'eZSiteAccess' ) ? eZSiteAccess::current() : null;
        return is_array( $access ) && isset( $access['name'] ) ? (string)$access['name'] : '';
    }
}
