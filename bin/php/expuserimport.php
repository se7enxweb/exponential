#!/usr/bin/env php
<?php
/**
 * File containing the expuserimport.php script.
 *
 * Import accounts from a CSV or JSON file, each created as exp:user:create creates it, with the activation mail or disabled; never a password from the file (a dry run that checks every row unless --apply).
 *
 * @alias exp:user:import
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expuserimport.php; this file is the entry point.
\Exponential\Command\Kernel\Expuserimport::main( __FILE__ );
