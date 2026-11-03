#!/usr/bin/env php
<?php
/**
 * File containing the expuserunlock.php script.
 *
 * Unlock an account locked after too many wrong passwords: its failed sign-ins back to 0, as Users > Hygiene does (a dry run unless --apply).
 *
 * @alias exp:user:unlock
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expuserunlock.php; this file is the entry point.
\Exponential\Command\Kernel\Expuserunlock::main( __FILE__ );
