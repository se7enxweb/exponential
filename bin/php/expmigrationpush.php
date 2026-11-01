#!/usr/bin/env php
<?php
/**
 * File containing the expmigrationpush.php script.
 *
 * Push the migration runs whose push failed (push state pending) to the remote of the migrations repository; a dry run unless --apply.
 *
 * @alias exp:migration:push
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expmigrationpush.php; this file is the entry point.
\Exponential\Command\Kernel\Expmigrationpush::main( __FILE__ );
