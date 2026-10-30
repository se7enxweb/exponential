#!/usr/bin/env php
<?php
/**
 * File containing the upgradecode.php script.
 *
 * Upgrade stage 1, code: the git checkouts moved to the target release, fast-forward only, through git_manager (dry run unless --apply).
 *
 * @alias exp:upgrade:code
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/upgradecode.php; this file is the entry point.
\Exponential\Command\Kernel\Upgradecode::main( __FILE__ );
