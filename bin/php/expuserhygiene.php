#!/usr/bin/env php
<?php
/**
 * File containing the expuserhygiene.php script.
 *
 * The counts of Users > Hygiene: accounts per state (disabled, unactivated, pending, locked, never signed in, inactive, orphaned, duplicate logins and addresses), each with the command that lists them.
 *
 * @alias exp:user:hygiene
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expuserhygiene.php; this file is the entry point.
\Exponential\Command\Kernel\Expuserhygiene::main( __FILE__ );
