#!/usr/bin/env php
<?php
/**
 * File containing the expmigrationremotelist.php script.
 *
 * The remotes migrations can be deployed to, with their groups and any problem of their settings; connects to none.
 *
 * @alias exp:migration:remote:list
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expmigrationremotelist.php; this file is the entry point.
\Exponential\Command\Kernel\Expmigrationremotelist::main( __FILE__ );
