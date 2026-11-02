#!/usr/bin/env php
<?php
/**
 * File containing the expmigrationremotestatus.php script.
 *
 * The migration state of remotes, read in parallel, whether they are in sync, and the latest deploys.
 *
 * @alias exp:migration:remote:status
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expmigrationremotestatus.php; this file is the entry point.
\Exponential\Command\Kernel\Expmigrationremotestatus::main( __FILE__ );
