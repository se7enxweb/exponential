#!/usr/bin/env php
<?php
/**
 * File containing the ezwebininstall.php script.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// eZWebin install Script
// file  bin/php/ezwebininstall.php


/*!
 define constans
*/

/*!
 define global vars
*/


/*!
 includes
*/
include_once( 'bin/php/ezwebincommon.php' );
require_once 'autoload.php';

// The code is in kernel/private/classes/commands/ezwebininstall.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Ezwebininstall::main( __FILE__ );
