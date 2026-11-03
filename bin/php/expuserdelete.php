#!/usr/bin/env php
<?php
/**
 * File containing the expuserdelete.php script.
 *
 * Remove a user account for good, as the administration does; refuses the anonymous user, the administrator accounts, the user who acts and the last administrator (a dry run unless --apply).
 *
 * @alias exp:user:delete
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expuserdelete.php; this file is the entry point.
\Exponential\Command\Kernel\Expuserdelete::main( __FILE__ );
