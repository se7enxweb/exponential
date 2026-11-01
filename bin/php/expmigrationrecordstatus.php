#!/usr/bin/env php
<?php
/**
 * File containing the expmigrationrecordstatus.php script.
 *
 * The recordings of changes that run: the site-wide window and the user's own recording, with how much each has recorded; repairs the file of running recordings.
 *
 * @alias exp:migration:record:status
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expmigrationrecordstatus.php; this file is the entry point.
\Exponential\Command\Kernel\Expmigrationrecordstatus::main( __FILE__ );
