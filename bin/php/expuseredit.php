#!/usr/bin/env php
<?php
/**
 * File containing the expuseredit.php script.
 *
 * Change a user account: e-mail address, login, first and last name, groups added or removed, the main group (a dry run unless --apply).
 *
 * @alias exp:user:edit
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expuseredit.php; this file is the entry point.
\Exponential\Command\Kernel\Expuseredit::main( __FILE__ );
