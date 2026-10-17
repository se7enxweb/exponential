<?php
/**
 * Runs the oauth/authorize view with stand-ins for the kernel classes it calls before the login, in a process of its
 * own (ezpOauthAuthorizeRedirectTest). The request is the JSON object of the first argument ("get" and "post"); the
 * known client is "known-client" with the end point https://client.example/callback.
 *
 * Prints one JSON object and exits: the status line and the Location the view answered with (response(), whose
 * header() calls a CLI process cannot see), or the step the view reached ("login" when it asked for the user).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

$request = json_decode( $argv[1], true );
$_GET = isset( $request['get'] ) ? $request['get'] : array();
$_POST = isset( $request['post'] ) ? $request['post'] : array();

function harnessResult( array $result )
{
    echo json_encode( $result );
    exit( 0 );
}

function response( $httpHeader, $location = null )
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
    public function variable( $block, $name ) { return '3600'; }
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
}

class eZUser
{
    public static function currentUser() { harnessResult( array( 'reached' => 'login' ) ); }
}
STANDINS
);

$Params = array( 'Module' => null );
require dirname( __DIR__, 6 ) . '/kernel/private/modules/oauth/authorize.php';
harnessResult( array( 'reached' => 'end' ) );
