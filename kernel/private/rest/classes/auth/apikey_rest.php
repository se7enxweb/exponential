<?php
/**
 * File containing the expApiKeyRest class
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * How the REST layer resolves a personal API key (doc/guides/api-keys.md, "For developers").
 *
 * 1. ezpRestAuthConfiguration::filter() calls reset() first, so nothing of the previous request of a persistent
 *    worker is left.
 * 2. The authentication style (ezpRestOauthAuthenticationStyle, or ezpRestBasicAuthStyle) asks authentication():
 *    when the Authorization header is "Bearer expk_..." (or "OAuth expk_..."), it gets an ezcAuthentication with
 *    expApiKeyAuthFilter instead of its own filter. A key is only taken from the header: a key in the query string
 *    or the body would end up in access logs, so it is refused (reason transport) and never looked up.
 * 3. After the style has signed the owner in, filter() calls authorize(): the route needs a scope
 *    ([ApiKeySettings] RouteScopes[], else DefaultReadScope for GET), the key must have it, the owner must still
 *    hold the scope's policy, and a write route is checked against the owner's rights on the node it writes
 *    ([ApiKeySettings] RouteGuards[]). A refusal answers 403 insufficient_scope.
 */
class expApiKeyRest
{
    /**
     * Forgets the key of the previous request (persistent workers keep statics).
     */
    public static function reset()
    {
        expApiKeyAuthFilter::$key = null;
        expApiKeyAuthFilter::$reason = null;
    }

    /**
     * The key of the current request, once authenticated.
     *
     * @return expApiKey|null
     */
    public static function current()
    {
        return expApiKeyAuthFilter::$key instanceof expApiKey ? expApiKeyAuthFilter::$key : null;
    }

    /**
     * The token of an "Authorization: Bearer <token>" or "Authorization: OAuth <token>" header, when it is a key.
     *
     * @param string|null $header the header value (null: read it from the request)
     * @return string|null
     */
    public static function keyFromHeader( $header = null )
    {
        if ( $header === null )
            $header = ezpOauthUtility::getAuthorizationHeader();
        if ( !is_string( $header ) || !preg_match( '/^\s*(?:Bearer|OAuth)\s+(\S+)\s*$/i', $header, $m ) )
            return null;
        return expApiKey::looksLikeKey( $m[1] ) ? $m[1] : null;
    }

    /**
     * Whether a key was sent where it must not be: in the query string or the body.
     *
     * @param ezcMvcRequest $request
     * @return bool
     */
    public static function keyInWrongPlace( ezcMvcRequest $request )
    {
        foreach ( array( 'oauth_token', 'access_token', 'api_key' ) as $name )
        {
            if ( isset( $request->get[$name] ) && expApiKey::looksLikeKey( $request->get[$name] ) )
                return true;
            if ( isset( $request->post[$name] ) && expApiKey::looksLikeKey( $request->post[$name] ) )
                return true;
        }
        return false;
    }

    /**
     * The authentication for a request that presents a key, or null when it presents none (the style then does
     * what it always did).
     *
     * @param ezcMvcRequest $request
     * @return ezcAuthentication|null
     */
    public static function authentication( ezcMvcRequest $request )
    {
        self::reset();
        $key = self::keyFromHeader();
        if ( $key === null )
        {
            if ( !self::keyInWrongPlace( $request ) )
                return null;
            // a key outside the header: refused without a lookup (expApiKeyAuthFilter records it as transport)
            $key = '';
        }
        $auth = new ezcAuthentication( new ezcAuthenticationIdCredentials( $key ) );
        $auth->addFilter( new expApiKeyAuthFilter() );
        return $auth;
    }

    /**
     * The ezpOauthFilter-style status of a failed key authentication, from the run's statuses.
     *
     * @param ezcAuthentication $auth
     * @return int|null
     */
    public static function failureStatus( ezcAuthentication $auth )
    {
        foreach ( $auth->getStatus() as $status )
            if ( key( $status ) === 'expApiKeyAuthFilter' )
                return current( $status );
        return null;
    }

    /**
     * Whether the key of the request may run this route. Records a refusal.
     *
     * @param expApiKey $key
     * @param ezcMvcRoutingInformation $info
     * @param ezcMvcRequest $request
     * @param eZUser $owner
     * @return string|null null when allowed, else the reason (scope, policy, permission)
     */
    public static function authorize( expApiKey $key, ezcMvcRoutingInformation $info, ezcMvcRequest $request, eZUser $owner )
    {
        $ini = eZINI::instance( 'rest.ini' );
        $routeScopes = $ini->hasVariable( 'ApiKeySettings', 'RouteScopes' ) ? (array)$ini->variable( 'ApiKeySettings', 'RouteScopes' ) : array();
        $default = $ini->hasVariable( 'ApiKeySettings', 'DefaultReadScope' ) ? (string)$ini->variable( 'ApiKeySettings', 'DefaultReadScope' ) : 'read';
        $scope = expApiKey::routeScope( (string)$info->controllerClass, (string)$info->action, (string)$request->protocol, $routeScopes, $default );

        $reason = null;
        $catalogue = expApiKey::scopeCatalogue( $ini );
        if ( $scope === null || !in_array( $scope, $key->scopeList(), true ) || !isset( $catalogue[$scope] ) )
            $reason = 'scope';
        else
        {
            list( $module, $function ) = explode( '/', $catalogue[$scope]['policy'], 2 );
            $access = $owner->hasAccessTo( $module, $function );
            if ( $access['accessWord'] === 'no' )
                $reason = 'policy';
            else
            {
                $guards = $ini->hasVariable( 'ApiKeySettings', 'RouteGuards' ) ? (array)$ini->variable( 'ApiKeySettings', 'RouteGuards' ) : array();
                $guard = isset( $guards[$info->controllerClass . '_' . $info->action] ) ? $guards[$info->controllerClass . '_' . $info->action] : '';
                if ( $guard !== '' && !self::guardAllows( $guard, $request ) )
                    $reason = 'permission';
            }
        }

        if ( $reason !== null && class_exists( 'expAudit' ) )
            expAudit::event( 'access.apikey.use.failed', array( 'object' => $key->auditObject(), 'result' => 'refused',
                                                                'reason' => $reason,
                                                                'after' => array( 'route' => $info->controllerClass . '_' . $info->action,
                                                                                  'scope' => $scope ) ) );
        return $reason;
    }

    /**
     * The owner's rights on the node a write route acts on (the current user is the owner by now), checked by
     * expRestContentPermission, the helper the ezprestapi content controller asks too, so a key and an OAuth token
     * get the same answer.
     *
     * create: parentNodeID, classIdentifier and languageLocale of the POST, content/create below the parent;
     * edit:   the route's nodeId, content/edit; remove: the route's nodeId, content/remove of every location of
     *         the object and everything below them.
     *
     * Only a refusal of the rights stops the key here. A request that names no node, or one that does not exist,
     * goes on to the controller, which answers 400 or 404 for it, as it does for any other authentication.
     *
     * @param string $guard create, edit or remove
     * @param ezcMvcRequest $request
     * @return bool
     */
    public static function guardAllows( $guard, ezcMvcRequest $request )
    {
        if ( !in_array( $guard, array( expRestContentPermission::CREATE, expRestContentPermission::EDIT, expRestContentPermission::REMOVE ), true ) )
            // an unknown guard name allows nothing
            return false;
        $refusal = expRestContentPermission::forRequest( $guard, $request );
        return $refusal === null || $refusal['status'] !== 403;
    }
}
?>
