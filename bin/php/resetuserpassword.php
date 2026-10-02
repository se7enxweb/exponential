#!/usr/bin/env php
<?php
/**
 * @description Reset a user password with admin authentication or root bypass.
 *
 * Process:
 * 1. Parse CLI options.
 * 2. If --allow-root-user is used, require the OS root user and skip admin auth.
 * 3. Otherwise authenticate an admin user with -a / -ap and verify the
 *    Administrator role.
 * 4. Resolve the target user (-u, default admin) and the new password (-p),
 *    or generate a random one.
 * 5. Validate and set the new bcrypt password.
 * 6. Persist and report the result.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/resetuserpassword.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Resetuserpassword::main( __FILE__ );
