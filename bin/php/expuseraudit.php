#!/usr/bin/env php
<?php
/**
 * File containing the expuseraudit.php script.
 *
 * The user audit log of an account (Users > Audit): what happened to it and what it did, newest first, filtered by event, result and days.
 *
 * @alias exp:user:audit
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expuseraudit.php; this file is the entry point.
\Exponential\Command\Kernel\Expuseraudit::main( __FILE__ );
