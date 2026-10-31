<?php
/**
 * @description Migrations: push the record of the runs whose push failed to the migrations repository's remote again
 *
 * Migration push cronjob part ([CronjobPart-migrationpush])
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/migrationpush.php; this file is the entry point.
if ( !class_exists( 'Exponential\\Cronjob\\Kernel\\Migrationpush' ) )
    return 0;
return \Exponential\Cronjob\Kernel\Migrationpush::main( __FILE__, get_defined_vars() );
