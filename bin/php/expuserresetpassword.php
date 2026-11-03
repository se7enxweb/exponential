#!/usr/bin/env php
<?php
/**
 * File containing the expuserresetpassword.php script.
 *
 * Reset a user's password: send the "reset your password" link, set a generated password shown once, or one read from standard input; optionally require a new password at the next sign-in (a dry run unless --apply).
 *
 * @alias exp:user:reset-password
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expuserresetpassword.php; this file is the entry point.
\Exponential\Command\Kernel\Expuserresetpassword::main( __FILE__ );
