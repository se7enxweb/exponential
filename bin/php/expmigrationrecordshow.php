#!/usr/bin/env php
<?php
/**
 * File containing the expmigrationrecordshow.php script.
 *
 * Show a recording of changes: every event it captured as the migration step it stands for, or with --plan the steps a migration of it would have.
 *
 * @alias exp:migration:record:show
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expmigrationrecordshow.php; this file is the entry point.
\Exponential\Command\Kernel\Expmigrationrecordshow::main( __FILE__ );
