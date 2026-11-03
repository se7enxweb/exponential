#!/usr/bin/env php
<?php
/**
 * File containing the expuserlist.php script.
 *
 * List user accounts: filter by state (enabled, disabled, unactivated, pending, locked, never signed in, inactive ...), group and search, with paging.
 *
 * @alias exp:user:list
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expuserlist.php; this file is the entry point.
\Exponential\Command\Kernel\Expuserlist::main( __FILE__ );
