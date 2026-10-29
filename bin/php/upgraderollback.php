#!/usr/bin/env php
<?php
/**
 * File containing the upgraderollback.php script.
 *
 * Upgrade rollback: put the database, settings, libraries and code back to a backup of exp:upgrade (dry run unless --apply).
 *
 * @alias exp:upgrade:rollback
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/upgraderollback.php; this file is the entry point.
\Exponential\Command\Kernel\Upgraderollback::main( __FILE__ );
