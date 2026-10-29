#!/usr/bin/env php
<?php
/**
 * File containing the userpasswordchangerequired.php script.
 *
 * Shows, sets or clears "change password at next sign-in" of a user account.
 *
 *   ./console exp:user-password-change-required status --login=editor1
 *   ./console exp:user-password-change-required set --login=editor1
 *   ./console exp:user-password-change-required clear --user-id=14
 *
 * @alias exp:user-password-change-required
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/userpasswordchangerequired.php; this file is the entry point.
if ( !class_exists( 'Exponential\\Command\\Kernel\\Userpasswordchangerequired' ) )
{
    fwrite( STDERR, "The password change command class is not in the autoload array: run php bin/php/ezpgenerateautoloads.php -k\n" );
    exit( 2 );
}
\Exponential\Command\Kernel\Userpasswordchangerequired::main( __FILE__ );
