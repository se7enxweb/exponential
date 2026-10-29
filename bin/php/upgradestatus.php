#!/usr/bin/env php
<?php
/**
 * File containing the upgradestatus.php script.
 *
 * Upgrade status: every upgrade step with its state (done, pending, not-needed, failed, unknown).
 *
 * @alias exp:upgrade:status
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/upgradestatus.php; this file is the entry point.
\Exponential\Command\Kernel\Upgradestatus::main( __FILE__ );
