#!/usr/bin/env php
<?php
/**
 * File containing the expmigrationrecordstop.php script.
 *
 * Stop a recording of changes: the one named, a user's own (--user) or the site-wide window (--site-wide); its events stay for exp:migration:generate --from-recording; a dry run unless --apply.
 *
 * @alias exp:migration:record:stop
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expmigrationrecordstop.php; this file is the entry point.
\Exponential\Command\Kernel\Expmigrationrecordstop::main( __FILE__ );
