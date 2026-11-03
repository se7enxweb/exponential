#!/usr/bin/env php
<?php
/**
 * File containing the expuserdisable.php script.
 *
 * Disable a user account and end its sessions, as Edit user settings does; refuses the protected accounts and the last administrator (a dry run unless --apply).
 *
 * @alias exp:user:disable
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expuserdisable.php; this file is the entry point.
\Exponential\Command\Kernel\Expuserdisable::main( __FILE__ );
