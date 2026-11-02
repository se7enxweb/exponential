#!/usr/bin/env php
<?php
/**
 * File containing the expmigrationreposplit.php script.
 *
 * Split the migrations folder of the installation's git checkout into a repository of its own with its history; a dry run unless --apply.
 *
 * @alias exp:migration:repo:split
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expmigrationreposplit.php; this file is the entry point.
\Exponential\Command\Kernel\Expmigrationreposplit::main( __FILE__ );
