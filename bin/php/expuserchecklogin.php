#!/usr/bin/env php
<?php
/**
 * File containing the expuserchecklogin.php script.
 *
 * Check whether a password read from standard input signs an account in (right password, enabled, not locked), for scripts and health checks; never counts as a failed sign-in.
 *
 * @alias exp:user:check-login
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expuserchecklogin.php; this file is the entry point.
\Exponential\Command\Kernel\Expuserchecklogin::main( __FILE__ );
