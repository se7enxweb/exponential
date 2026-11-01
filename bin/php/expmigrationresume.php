#!/usr/bin/env php
<?php
/**
 * File containing the expmigrationresume.php script.
 *
 * Resume a migration run that failed or was interrupted: the migrations still pending run again; a dry run unless --apply.
 *
 * @alias exp:migration:resume
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expmigrationresume.php; this file is the entry point.
\Exponential\Command\Kernel\Expmigrationresume::main( __FILE__ );
