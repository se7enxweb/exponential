#!/usr/bin/env php
<?php
/**
 * File containing the upgradescripts.php script.
 *
 * Upgrade stage 4, scripts: the PHP upgrade steps, policies and repairs (dry run unless --apply).
 *
 * @alias exp:upgrade:scripts
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/upgradescripts.php; this file is the entry point.
\Exponential\Command\Kernel\Upgradescripts::main( __FILE__ );
