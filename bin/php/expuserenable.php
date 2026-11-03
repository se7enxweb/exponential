#!/usr/bin/env php
<?php
/**
 * File containing the expuserenable.php script.
 *
 * Enable a disabled user account again, as Edit user settings does; an account that was never activated is approved with exp:user:approve instead (a dry run unless --apply).
 *
 * @alias exp:user:enable
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expuserenable.php; this file is the entry point.
\Exponential\Command\Kernel\Expuserenable::main( __FILE__ );
