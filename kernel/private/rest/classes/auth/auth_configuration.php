<?php
/**
 * File containing ezpRestAuthConfiguration
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * Class controlling authentication inside REST layer.
 *
 * This class sets up the defaults for which routes require auth and which
 * can be omitted. This class acts as a compatibility bridge between the REST-
 * layer and the traditional eZ Publish permission configuration.
 */
class ezpRestAuthConfiguration
{
    protected $info = null;
    protected $req = null;

    protected $filter = null;

    public function __construct( ezcMvcRoutingInformation $info, ezpRestRequest $req )
    {
        $this->info = $info;
        $this->req = $req;
    }

    public function setFilter( ezpRestAuthenticationStyle $filter )
    {
        $this->filter = $filter;
    }

    public function filter()
    {
        if ( eZINI::instance( 'rest.ini' )->variable( 'Authentication', 'RequireHTTPS') === 'enabled' &&
             $this->req->isEncrypted === false )
        {
            // When an unencrypted connection is identified, we have to alter the
            // flag to avoid infinite loop, when the request is rerouted to the error controller.
            // This should be improved in the future.
            $this->req->isEncrypted = true;
            throw new ezpRestHTTPSRequiredException();
        }

        // Nothing of a personal API key of an earlier request of this worker is kept.
        if ( class_exists( 'expApiKeyRest' ) )
            expApiKeyRest::reset();
        // Nor the password sign-in of an earlier request (signedInWithPassword())
        expRestPasswordAuthFilter::$user = null;
        expRestPasswordAuthFilter::$viaAppPassword = false;

        // 0. Check if the given route needs authentication.
        if ( !$this->shallAuthenticate() )
        {
            $this->filter = new ezpRestNoAuthStyle();
        }
        else if ( $this->filter === null )
        {
            $opt = new ezpExtensionOptions();
            $opt->iniFile = 'rest.ini';
            $opt->iniSection = 'Authentication';
            $opt->iniVariable = 'AuthenticationStyle';
            $authFilter = eZExtension::getHandlerClass( $opt );
            if ( !$authFilter instanceof ezpRestAuthenticationStyle )
            {
                throw new ezpRestAuthStyleNotFoundException();
            }

            $this->filter = $authFilter;
        }

        // 1. Initialize the context needed for authenticating the user.
        $auth = $this->filter->setup( $this->req );
        if ( $auth instanceof ezcMvcInternalRedirect )
            return $auth;

        // 2.Perform the authentication
        // Result of authentication filter can be a valid ezp user (auth succeeded) or an internal redirect (ezcMvcInternalRedirect)
        $user = $this->filter->authenticate( $auth, $this->req );
        if ( $user instanceof eZUser )
        {
            // A password alone (basic authentication, not a personal API key and not an app password) is not enough
            // for an account that needs a second sign-in step: 401 second_factor_required, or 403
            // second_factor_setup_required (expSignInSecondFactor), before the request is signed in
            if ( class_exists( 'expSignInSecondFactor' ) && self::signedInWithPassword( $this->filter ) )
            {
                $refusal = expSignInSecondFactor::check( $user, expSignInSecondFactor::CHANNEL_REST, expSignInSecondFactor::presentedCode() );
                if ( $refusal !== null )
                {
                    $this->req->variables['ezpAuth_redirUrl'] = $this->req->uri;
                    $this->req->variables['ezpAuth_reason'] = $refusal['reason'];
                    $this->req->variables['ezpAuth_message'] = $refusal['message'];
                    $this->req->uri = eZINI::instance( 'rest.ini' )->variable( 'System', 'ApiPrefix' ) . '/auth/oauth/login';
                    return new ezcMvcInternalRedirect( $this->req );
                }
            }
            // An account marked "change password at next sign-in" sets it in the web interface first: 403
            // password_change_required (expPasswordChangeRequired), before the request is signed in
            if ( class_exists( 'expPasswordChangeRequired' ) && expPasswordChangeRequired::refusesSignIn( $user, 'rest' ) )
            {
                $this->req->variables['ezpAuth_redirUrl'] = $this->req->uri;
                $this->req->variables['ezpAuth_reason'] = expPasswordChangeRequired::REST_ERROR;
                $this->req->uri = eZINI::instance( 'rest.ini' )->variable( 'System', 'ApiPrefix' ) . '/auth/oauth/login';
                return new ezcMvcInternalRedirect( $this->req );
            }
            $userID = $user->id();
            if ( class_exists( 'expSignInSecondFactor' ) && expRestPasswordAuthFilter::$viaAppPassword )
            {
                // An app password signs in for this request only: no session, so no cookie comes of it
                expSignInSecondFactor::signInForRequest( $user );
            }
            else if ( $userID != eZUser::currentUserID() )
            {
                eZUser::setCurrentlyLoggedInUser( $user, $userID );
            }
            $this->filter->setUser( $user );

            // 3. A personal API key may run only the routes its scopes cover, within its owner's rights.
            if ( class_exists( 'expApiKeyRest' ) && expApiKeyRest::current() !== null )
            {
                $reason = expApiKeyRest::authorize( expApiKeyRest::current(), $this->info, $this->req, $user );
                if ( $reason !== null )
                {
                    $this->req->variables['ezpAuth_redirUrl'] = $this->req->uri;
                    $this->req->variables['ezpAuth_reason'] = ezpOauthFilter::STATUS_TOKEN_INSUFFICIENT_SCOPE;
                    $this->req->uri = eZINI::instance( 'rest.ini' )->variable( 'System', 'ApiPrefix' ) . '/auth/oauth/login';
                    return new ezcMvcInternalRedirect( $this->req );
                }
            }
        }
        else if ( $user instanceof ezcMvcInternalRedirect )
        {
            return $user;
        }
    }

    /**
     * Whether the request was signed in by a login and password (ezpRestBasicAuthStyle with expRestPasswordAuthFilter),
     * not by an OAuth token, a personal API key or an app password.
     *
     * @param mixed $filter the authentication style of the request
     * @return bool
     */
    public static function signedInWithPassword( $filter )
    {
        if ( !$filter instanceof ezpRestBasicAuthStyle )
            return false;
        if ( class_exists( 'expApiKeyRest' ) && expApiKeyRest::current() !== null )
            return false;
        return expRestPasswordAuthFilter::$user instanceof eZUser && !expRestPasswordAuthFilter::$viaAppPassword;
    }

    /**
     * Checks if authentication should be requested or not
     * @return bool
     */
    private function shallAuthenticate()
    {
        $shallAuthenticate = true;
        if ( eZINI::instance( 'rest.ini' )->variable( 'Authentication', 'RequireAuthentication' ) !== 'enabled' )
        {
            $shallAuthenticate = false;
        }
        else
        {
            $routeFilter = ezpRestRouteFilterInterface::getRouteFilter();
            $shallAuthenticate = $routeFilter->shallDoActionWithRoute( $this->info );
        }

        return $shallAuthenticate;
    }
}

?>
