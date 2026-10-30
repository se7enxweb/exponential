#!/usr/bin/env php
<?php
/**
 * File containing the ezwebinupgrade.php script.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// eZWebin upgrade Script
// file  bin/php/ezwebinupgrade.php


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

// The code is in kernel/private/classes/commands/ezwebinupgrade.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Ezwebinupgrade::main( __FILE__ );
