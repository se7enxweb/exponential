#!/usr/bin/env php
<?php
/**
 * File containing the expmigrationdryrun.php script.
 *
 * Dry run of the pending migrations: the plan, and with --simulate a run in a transaction that is rolled back (--diff shows what changed).
 *
 * @alias exp:migration:dry-run
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expmigrationdryrun.php; this file is the entry point.
\Exponential\Command\Kernel\Expmigrationdryrun::main( __FILE__ );
