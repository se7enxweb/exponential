<?php
/**
 * Runs the oauth/authorize view with stand-ins for the kernel classes it calls before the login, in a process of its
 * own (ezpOauthAuthorizeRedirectTest). The request is the JSON object of the first argument ("get" and "post"); the
 * known client is "known-client" with the end point https://client.example/callback.
 *
 * Prints one JSON object and exits: the status line and the Location the view answered with (expModuleViewOauthAuthorizeResponse(), whose
 * header() calls a CLI process cannot see), or the step the view reached ("login" when it asked for the user,
 * "authorization" when it went on to the client's authorization of the user).
 *
 * Optional keys of the request: "user", the signed in user as the result of hasAccessTo( 'oauth', 'authorize' )
 * ({"accessWord": ...}; without it the view stops at "login"), "siteaccess" (the current siteaccess name) and
 * "requirePolicy" (rest.ini [OAuthSettings] AuthorizeRequiresPolicy; not set when missing), "ini" (other settings of
 * [OAuthSettings], name => value) and "authorized" (true: the user has authorized the client, so the view issues the
 * code or token; the stored object is returned as "saved"). "public-client" is a known client without a secret.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

$request = json_decode( $argv[1], true );
$_GET = isset( $request['get'] ) ? $request['get'] : array();
$_POST = isset( $request['post'] ) ? $request['post'] : array();
$GLOBALS['harnessRequest'] = $request;
if ( isset( $request['siteaccess'] ) )
    $GLOBALS['eZCurrentAccess'] = array( 'name' => $request['siteaccess'] );

function harnessResult( array $result )
{
    echo json_encode( $result );
    exit( 0 );
}

function expModuleViewOauthAuthorizeResponse( $httpHeader, $location = null )
{
    $result = array( 'status' => $httpHeader, 'location' => $location );
    // the authorization code or token the view stored, when it got that far
    if ( isset( $GLOBALS['harnessSaved'] ) )
        $result['saved'] = $GLOBALS['harnessSaved'];
    harnessResult( $result );
}

// The stand-ins are declared through eval(): written as class declarations here, the autoload generator (which
// walks tests/ with -s) would map the kernel's class names to this script, and loading a class would run it.
eval( <<<'STANDINS'
class eZHTTPTool
{
    public static function instance() { return new self(); }
    public function hasPostVariable( $name ) { return isset( $_POST[$name] ); }
    public function postVariable( $name ) { return $_POST[$name]; }
    public function hasGetVariable( $name ) { return isset( $_GET[$name] ); }
    public function getVariable( $name ) { return $_GET[$name]; }
}

class eZINI
{
    public static function instance( $fileName ) { return new self(); }
    public function hasVariable( $block, $name )
    {
        if ( isset( $GLOBALS['harnessRequest']['ini'][$name] ) )
            return true;
        if ( $name === 'TokenTTL' )
            return true;
        return $name === 'AuthorizeRequiresPolicy' && isset( $GLOBALS['harnessRequest']['requirePolicy'] );
    }
    public function variable( $block, $name )
    {
        if ( isset( $GLOBALS['harnessRequest']['ini'][$name] ) )
            return $GLOBALS['harnessRequest']['ini'][$name];
        return $name === 'AuthorizeRequiresPolicy' ? $GLOBALS['harnessRequest']['requirePolicy'] : '3600';
    }
}

class ezpRestClient
{
    public $endpoint_uri = 'https://client.example/callback';
    public $client_secret = 'harness-secret';

    public static function fetchByClientId( $clientId )
    {
        if ( $clientId === 'no-endpoint-client' )
        {
            $client = new self();
            $client->endpoint_uri = '';
            return $client;
        }
        if ( $clientId === 'public-client' )
        {
            $client = new self();
            $client->client_secret = '';
            return $client;
        }
        return $clientId === 'known-client' ? new self() : false;
    }

    public function isEndPointValid( $endPointUri )
    {
        return $endPointUri === $this->endpoint_uri;
    }

    public function isAuthorizedByUser( $scope, $user )
    {
        if ( empty( $GLOBALS['harnessRequest']['authorized'] ) )
            harnessResult( array( 'reached' => 'authorization' ) );
        return true;
    }
}

class ezpRestToken
{
    public static function generateToken( $vary ) { return 'harness-code'; }
}

class ezpRestAuthcode
{
    public $id, $client_id, $user_id, $expirytime, $scope, $code_challenge, $code_challenge_method;
}

class ezcPersistentSessionInstance
{
    public static function get() { return new self(); }
    public function save( $object )
    {
        $GLOBALS['harnessSaved'] = array( 'class' => get_class( $object ) ) + get_object_vars( $object );
        unset( $GLOBALS['harnessSaved']['expirytime'] );
    }
}

class eZSiteAccess
{
    public static function current() { return isset( $GLOBALS['eZCurrentAccess'] ) ? $GLOBALS['eZCurrentAccess'] : null; }
}

class eZUser
{
    public static function currentUser()
    {
        if ( !isset( $GLOBALS['harnessRequest']['user'] ) )
            harnessResult( array( 'reached' => 'login' ) );
        return new self();
    }
    public function isRegistered() { return true; }
    public function attribute( $name ) { return 14; }
    public function hasAccessTo( $module, $function )
    {
        return $module === 'oauth' && $function === 'authorize' ? $GLOBALS['harnessRequest']['user'] : array( 'accessWord' => 'no' );
    }
}
STANDINS
);

require_once dirname( __DIR__, 6 ) . '/kernel/private/rest/classes/oauth/authorize_policy.php';
require_once dirname( __DIR__, 6 ) . '/kernel/private/rest/classes/oauth/pkce.php';

$Params = array( 'Module' => null );
require dirname( __DIR__, 6 ) . '/kernel/private/modules/oauth/authorize.php';
harnessResult( array( 'reached' => 'end' ) );
