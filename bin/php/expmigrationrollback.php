#!/usr/bin/env php
<?php
/**
 * File containing the expmigrationrollback.php script.
 *
 * Take migrations back: a run (its caption restored), one migration (its down()) or, for experts, any caption; a dry run unless --apply.
 *
 * @alias exp:migration:rollback
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expmigrationrollback.php; this file is the entry point.
\Exponential\Command\Kernel\Expmigrationrollback::main( __FILE__ );
