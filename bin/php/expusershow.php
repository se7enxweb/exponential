#!/usr/bin/env php
<?php
/**
 * File containing the expusershow.php script.
 *
 * Show one user account: groups, roles with their limitation, enabled or locked, last visit, registration state, two-step sign-in and active sessions (never a password hash).
 *
 * @alias exp:user:show
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expusershow.php; this file is the entry point.
\Exponential\Command\Kernel\Expusershow::main( __FILE__ );
