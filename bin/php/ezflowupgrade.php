#!/usr/bin/env php
<?php
/**
 * File containing the ezflowupgrade.php script.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// eZ Flow upgrade Script
// file  bin/php/ezflowupgrade.php


/*!
 define constans
*/

/*!
 define global vars
*/

/*!
 includes
*/
require_once 'autoload.php';

// The code is in kernel/private/classes/commands/ezflowupgrade.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Ezflowupgrade::main( __FILE__ );
