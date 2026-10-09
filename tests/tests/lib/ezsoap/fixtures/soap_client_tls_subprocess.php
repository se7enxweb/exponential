<?php
/**
 * Runs eZSOAPClient in a PHP of its own, for what a test process cannot change: started with -n (no curl) or with an
 * open_basedir. Prints a JSON object with the result and the messages written to eZDebug.
 *
 * Arguments: <mode> [<CA file>]
 *  send-https - send() over HTTPS to 127.0.0.1:1
 *  send-http  - send() over HTTP to 127.0.0.1:1
 *  options    - the cURL options of a call with the CA file
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class eZDebug
{
    public static $messages = array();

    public static function writeError( $message, $label = '' )
    {
        self::$messages[] = array( 'error', $message );
    }

    public static function writeWarning( $message, $label = '' )
    {
        self::$messages[] = array( 'warning', $message );
    }
}

require dirname( __DIR__, 5 ) . '/lib/ezsoap/classes/ezsoapclient.php';

class eZSOAPClientTlsSubprocessClient extends eZSOAPClient
{
    public function options()
    {
        return $this->curlOptions( 'https://127.0.0.1:1/soap', '<x/>', array() );
    }
}

$mode = $argv[1];
$result = array( 'curl' => function_exists( 'curl_init' ) );
if ( $mode === 'options' )
{
    $client = new eZSOAPClientTlsSubprocessClient( '127.0.0.1', '/soap', 1, true );
    $client->setCAFile( $argv[2] );
    $options = $client->options();
    $result['cainfo'] = $options[CURLOPT_CAINFO];
}
else
{
    $client = new eZSOAPClient( '127.0.0.1', '/soap', 1, $mode === 'send-https' );
    $client->setTimeout( 2 );
    $result['send'] = $client->send( null );
    $result['error'] = $client->ErrorString;
}
$result['messages'] = eZDebug::$messages;
echo json_encode( $result );
