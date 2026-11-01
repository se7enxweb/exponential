#!/usr/bin/env php
<?php
/**
 * File containing the expmigrationrecordstart.php script.
 *
 * Start a recording of changes: one user's own changes (a session recording) or every change of the site (--site-wide), kept as the steps of a YAML migration; a dry run unless --apply.
 *
 * @alias exp:migration:record:start
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expmigrationrecordstart.php; this file is the entry point.
\Exponential\Command\Kernel\Expmigrationrecordstart::main( __FILE__ );
