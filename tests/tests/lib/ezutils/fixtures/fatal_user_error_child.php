<?php
/**
 * Run by eZFatalUserErrorTest in a child PHP process: reaches one of the places that used to pass E_USER_ERROR to
 * trigger_error(), with no error handler installed, and prints AFTER when the script goes on. The parent checks
 * that it stops, how, and that PHP raised no deprecation on the way.
 *
 * Usage: php fatal_user_error_child.php debug|expiry
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

chdir( dirname( __DIR__, 5 ) );
require 'autoload.php';

$case = isset( $argv[1] ) ? $argv[1] : '';

// eZDebug writes nothing anywhere, unless the case asks it to hand its errors to PHP
$debug = new eZDebug();
foreach ( $debug->AlwaysLog as $level => $always )
    $debug->AlwaysLog[$level] = false;
$GLOBALS['eZDebugGlobalInstance'] = $debug;
$GLOBALS['eZDebugEnabled'] = false;
unset( $GLOBALS['eZDebugAlwaysLog'] );

if ( $case === 'debug' )
{
    // what eZDebug does between eZScript::startup() and initialize(), and while it writes a log file
    $GLOBALS['eZDebugEnabled'] = true;
    $debug->HandleType = eZDebug::HANDLE_TO_PHP;
    eZDebug::writeError( 'x1 the fatal message', 'x1label' );
}
else if ( $case === 'expiry' )
{
    $handler = ( new ReflectionClass( 'eZExpiryHandler' ) )->newInstanceWithoutConstructor();
    $handler->CacheFile = new class
    {
        public function processFile( $callback )
        {
            return false;
        }
    };
    $handler->restore();
}
else
{
    fwrite( STDERR, "unknown case\n" );
    exit( 3 );
}
echo "AFTER\n";
