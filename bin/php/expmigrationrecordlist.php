#!/usr/bin/env php
<?php
/**
 * File containing the expmigrationrecordlist.php script.
 *
 * List the recordings of changes, running and stopped, the newest first, with their kind, state and number of events.
 *
 * @alias exp:migration:record:list
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expmigrationrecordlist.php; this file is the entry point.
\Exponential\Command\Kernel\Expmigrationrecordlist::main( __FILE__ );
