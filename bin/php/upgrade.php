#!/usr/bin/env php
<?php
/**
 * File containing the upgrade.php script.
 *
 * Upgrade the installation in one call: every stage in order (dry run unless --apply).
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/upgrade.php; this file is the entry point.
\Exponential\Command\Kernel\Upgrade::main( __FILE__ );
