#!/usr/bin/env php
<?php
/**
 * File containing the expmigrationmigrate.php script.
 *
 * Run the pending migrations: a dry run unless --apply; captions before and after, restore on failure, commit, tag and push.
 *
 * @alias exp:migration:migrate
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expmigrationmigrate.php; this file is the entry point.
\Exponential\Command\Kernel\Expmigrationmigrate::main( __FILE__ );
