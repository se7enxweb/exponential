#!/usr/bin/env php
<?php
/**
 * File containing the upgradesteps.php script.
 *
 * Upgrade steps: the registered steps with id, stage, version, engines and description.
 *
 * @alias exp:upgrade:steps
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/upgradesteps.php; this file is the entry point.
\Exponential\Command\Kernel\Upgradesteps::main( __FILE__ );
