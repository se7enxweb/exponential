<?php
/**
 * Router for PHP's built-in web server, as the php engine of exp:velocity
 * runs it (php -S host:port -t <root> bin/php/velocity-router.php).
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The console lists every script in bin/php as a command; this one is only
// meaningful as the built-in server's router.
if ( PHP_SAPI !== 'cli-server' )
{
    fwrite( STDERR, "velocity-router.php is the router of PHP's built-in web server; start it with exp:velocity start (Engine=php)\n" );
    exit( 1 );
}

// The code is in kernel/private/classes/commands/velocity-router.php (#207); this file is the entry point.
// It runs before any autoloader exists, so the class file is loaded directly; the front controller is required
// here, at the top level, where the kernel expects its globals.
require_once __DIR__ . '/../../kernel/private/classes/commands/velocity-router.php';
$velocityScript = \Exponential\Command\Kernel\VelocityRouter::route( dirname( __DIR__, 2 ) );
if ( !is_string( $velocityScript ) )
    return $velocityScript;
require $velocityScript;
