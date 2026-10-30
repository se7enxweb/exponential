<?php
/**
 * Fixture of expRequestLogRequestEndTest (RE-6): one request in a process of its own that ends the way a download or
 * an ezjscore call ends under PHP-FPM, through eZExecution::cleanExit() and its real exit. No database.
 *
 * Arguments: <installation root> <log directory> <request URI>
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

if ( PHP_SAPI !== 'cli' || $argc < 4 )
{
    exit( 2 );
}

chdir( $argv[1] );
require 'autoload.php';

$_SERVER['REQUEST_TIME_FLOAT'] = microtime( true );
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = $argv[3];
$_SERVER['REMOTE_ADDR'] = '203.0.113.9';
$_SERVER['HTTP_USER_AGENT'] = 'curl/8.5.0';
$GLOBALS['eZCurrentAccess'] = array( 'name' => 'site', 'type' => 1, 'uri_part' => array() );

expRequestLog::setLogDirectory( $argv[2] );
expRequestLog::setSettings( array( 'NotFoundLog' => 'enabled', 'NotFoundLogFile' => '404.log',
                                   'MaskIP' => 'enabled', 'MaxURILength' => '300' ) );
expRequestClassifier::setInstance( new expRequestClassifier( array(
    'compileCache' => false,
    'salt' => 'test-salt',
    'cacheDir' => $argv[2] . '/cache',
    'logDir' => $argv[2],
) ) );

// what ezpKernelWeb::run() does first
expRequestLog::begin();

// the view sends its own answer and ends the request
echo 'file body';
eZExecution::cleanExit();

// never reached
echo 'after the clean exit';
