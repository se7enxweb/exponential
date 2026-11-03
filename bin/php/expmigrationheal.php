#!/usr/bin/env php
<?php
/**
 * File containing the expmigrationheal.php script.
 *
 * Self-healing before a migration run: a stale run lock, missing migration folders, file modes, caches.
 *
 * @alias exp:migration:heal
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expmigrationheal.php; this file is the entry point.
\Exponential\Command\Kernel\Expmigrationheal::main( __FILE__ );
