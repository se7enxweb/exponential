#!/usr/bin/env php
<?php
/**
 * File containing the upgradedatabase.php script.
 *
 * Upgrade stage 3, database: the schema and data steps on every engine (dry run unless --apply).
 *
 * @alias exp:upgrade:database
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/upgradedatabase.php; this file is the entry point.
\Exponential\Command\Kernel\Upgradedatabase::main( __FILE__ );
