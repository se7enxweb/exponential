#!/usr/bin/env php
<?php
/**
 * File containing the upgradeverify.php script.
 *
 * Upgrade verify: re-detect every step and report drift (a step recorded as done whose effect is missing).
 *
 * @alias exp:upgrade:verify
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/upgradeverify.php; this file is the entry point.
\Exponential\Command\Kernel\Upgradeverify::main( __FILE__ );
