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
 * "requirePolicy" (rest.ini [OAuthSettings] AuthorizeRequiresPolicy; not set when missing).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
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
    harnessResult( array( 'status' => $httpHeader, 'location' => $location ) );
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
        return $name !== 'AuthorizeRequiresPolicy' || isset( $GLOBALS['harnessRequest']['requirePolicy'] );
    }
    public function variable( $block, $name )
    {
        return $name === 'AuthorizeRequiresPolicy' ? $GLOBALS['harnessRequest']['requirePolicy'] : '3600';
    }
}

class ezpRestClient
{
    public $endpoint_uri = 'https://client.example/callback';

    public static function fetchByClientId( $clientId )
    {
        if ( $clientId === 'no-endpoint-client' )
        {
            $client = new self();
            $client->endpoint_uri = '';
            return $client;
        }
        return $clientId === 'known-client' ? new self() : false;
    }

    public function isEndPointValid( $endPointUri )
    {
        return $endPointUri === $this->endpoint_uri;
    }

    public function isAuthorizedByUser( $scope, $user ) { harnessResult( array( 'reached' => 'authorization' ) ); }
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
    public function hasAccessTo( $module, $function )
    {
        return $module === 'oauth' && $function === 'authorize' ? $GLOBALS['harnessRequest']['user'] : array( 'accessWord' => 'no' );
    }
}
STANDINS
);

require_once dirname( __DIR__, 6 ) . '/kernel/private/rest/classes/oauth/authorize_policy.php';

$Params = array( 'Module' => null );
require dirname( __DIR__, 6 ) . '/kernel/private/modules/oauth/authorize.php';
harnessResult( array( 'reached' => 'end' ) );
