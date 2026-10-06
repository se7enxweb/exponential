<?php
/**
 * File containing the basic auth style
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * HTTP basic authentication against the site's users.
 *
 * The password is checked the way the sign-in checks it (expRestPasswordAuthFilter: eZUser::authenticateHash() with
 * the user's own hash type), so every hash type works: bcrypt and argon2 (php_default), the md5 types and the old
 * ones. It used to compare md5("login\npassword") with ezuser.password_hash in SQL, which only matched users still on
 * the legacy md5_user hash: every user with a modern hash was refused.
 *
 * A personal API key in "Authorization: Bearer expk_..." is accepted here too (expApiKeyRest), so a site that uses
 * basic authentication for its REST interface can still give its users publishing keys.
 */
class ezpRestBasicAuthStyle extends ezpRestAuthenticationStyle implements ezpRestAuthenticationStyleInterface
{
    public function setup( ezcMvcRequest $request )
    {
        if ( class_exists( 'expApiKeyRest' ) )
        {
            $keyAuth = expApiKeyRest::authentication( $request );
            if ( $keyAuth !== null )
                return $keyAuth;
        }

        if ( $request->authentication === null )
        {
            $authRequest = clone $request;
            $authRequest->uri = "{$this->prefix}/auth/http-basic-auth";
            $authRequest->protocol = "http-get";

            return new ezcMvcInternalRedirect( $authRequest );
        }

        $cred = new ezcAuthenticationPasswordCredentials( $request->authentication->identifier,
                                                          $request->authentication->password );

        $auth = new ezcAuthentication( $cred );
        $auth->addFilter( new expRestPasswordAuthFilter() );
        return $auth;
    }

    public function authenticate( ezcAuthentication $auth, ezcMvcRequest $request )
    {
        if ( !$auth->run() )
        {
            if ( class_exists( 'expApiKeyRest' ) && expApiKeyRest::failureStatus( $auth ) !== null )
            {
                // a key that was refused answers like an OAuth token (401 invalid_token, expired_token, 429)
                $request->variables['ezpAuth_redirUrl'] = $request->uri;
                $request->variables['ezpAuth_reason'] = expApiKeyRest::failureStatus( $auth );
                $request->uri = "{$this->prefix}/auth/oauth/login";
                return new ezcMvcInternalRedirect( $request );
            }
            $request->uri = "{$this->prefix}/auth/http-basic-auth";
            return new ezcMvcInternalRedirect( $request );
        }

        if ( class_exists( 'expApiKeyRest' ) && expApiKeyRest::current() !== null )
            return expApiKeyRest::current()->owner();

        // We're in. Get the ezp user and return it
        return expRestPasswordAuthFilter::$user instanceof eZUser
            ? expRestPasswordAuthFilter::$user
            : eZUser::fetchByName( $auth->credentials->id );
    }
}
?>
