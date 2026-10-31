<?php
/**
 * File containing ezpRestNoAuthStyle class
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * This auth style is used when no authentication is required for current REST request
 * A default user will be returned. UserID to use has to be set in rest.ini/[Authentication].DefaultUserID (fallback is anonymous user)
 */
class ezpRestNoAuthStyle extends ezpRestAuthenticationStyle implements ezpRestAuthenticationStyleInterface
{
    /**
     * @see ezpRestAuthenticationStyleInterface::setup()
     */
    public function setup( ezcMvcRequest $request )
    {
        // Use either rest.ini/[Authentication].DefaultUserID if provided, or AnonymousUserID
        $defaultUserID = (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' );
        $restDefaultUserID = eZINI::instance( 'rest.ini' )->variable( 'Authentication', 'DefaultUserID' );
        if ( $restDefaultUserID !== '' )
        {
            $defaultUserID = (int)$restDefaultUserID;
        }

        $cred = new ezcAuthenticationIdCredentials( $defaultUserID );
        $auth = new ezcAuthentication( $cred );
        $auth->addFilter( new ezpNativeUserAuthFilter() );

        return $auth;
    }

    /**
     * @see ezpRestAuthenticationStyleInterface::authenticate()
     */
    public function authenticate( ezcAuthentication $auth, ezcMvcRequest $request )
    {
        $user = static::cachedAnonymousUser( (int)$auth->credentials->id );
        if ( $user instanceof eZUser )
            return $user;

        if ( !$auth->run() && $request->uri !== "{$this->prefix}/fatal" ) // /fatal URI should never fail
        {
            throw new ezpUserNotFoundException( $auth->credentials->id );
        }
        else
        {
            return eZUser::fetch( $auth->credentials->id );
        }
    }

    /**
     * The anonymous user from the user cache (var/cache/user-info, site.ini [RoleSettings] EnableCaching), the way
     * eZUser::instance() takes the user of every web request; null when the default user is another user, caching is
     * off, or the cache does not know the user, and then the database is asked as before.
     *
     * Asking the database meant opening it: a request with a cached answer (a persisted GraphQL query sent as a
     * form) did nothing else with it, and on SQLite every new connection reads the whole schema first, 4-5 ms of a
     * 15 ms answer. The user cache is the same data, kept and expired by the kernel (eZUser::purgeUserCacheByUserId()
     * when the user changes or goes, eZUser::cleanupCache() and ezcache for the rest).
     *
     * @param int $userId the default user of the REST request
     * @return eZUser|null
     */
    protected static function cachedAnonymousUser( $userId )
    {
        if ( $userId <= 0 || $userId !== (int)eZUser::anonymousId()
             || eZINI::instance()->variable( 'RoleSettings', 'EnableCaching' ) !== 'true' )
            return null;
        $userCache = static::anonymousUserCache();
        if ( !isset( $userCache['info'][$userId]['contentobject_id'] ) || (int)$userCache['info'][$userId]['contentobject_id'] !== $userId )
            return null;
        $user = new eZUser( $userCache['info'][$userId] );
        $user->setUserCache( $userCache );
        return $user;
    }

    /**
     * @return array the user cache of the anonymous user (eZUser::getUserCacheByAnonymousId())
     */
    protected static function anonymousUserCache()
    {
        return eZUser::getUserCacheByAnonymousId();
    }
}
?>
