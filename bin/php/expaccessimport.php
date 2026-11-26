#!/usr/bin/env php
<?php
/**
 * File containing the expaccessimport.php script: the exp:access:import command.
 *
 * Imports the tables of a Microsoft Access file into the site's database or a data table connection; see
 * doc/eng-us/features/6.0/ms-access-support.md.
 *
 * @alias exp:access:import
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expaccessimport.php; this file is the entry point.
\Exponential\Command\Kernel\Expaccessimport::main( __FILE__ );
