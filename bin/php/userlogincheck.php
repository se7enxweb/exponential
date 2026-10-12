#!/usr/bin/env php
<?php
/**
 * File containing the userlogincheck.php script.
 *
 * Lists the logins more than one user account has, and adds the unique index on ezuser.login once there are none.
 *
 *   ./console exp:user-login-check               report only, changes nothing
 *   ./console exp:user-login-check --fix-index   add the unique index after a clean report
 *
 * @alias exp:user-login-check
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/userlogincheck.php; this file is the entry point.
if ( !class_exists( 'Exponential\\Command\\Kernel\\Userlogincheck' ) )
{
    fwrite( STDERR, "The user login check command class is not in the autoload array: run php bin/php/ezpgenerateautoloads.php -k\n" );
    exit( 2 );
}
\Exponential\Command\Kernel\Userlogincheck::main( __FILE__ );
