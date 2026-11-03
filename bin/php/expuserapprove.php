#!/usr/bin/env php
<?php
/**
 * File containing the expuserapprove.php script.
 *
 * Approve a waiting registration (activate the account, or accept its approval item and publish it), or reject it with --reject, as Users > Unactivated and the approval item do (a dry run unless --apply).
 *
 * @alias exp:user:approve
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expuserapprove.php; this file is the entry point.
\Exponential\Command\Kernel\Expuserapprove::main( __FILE__ );
