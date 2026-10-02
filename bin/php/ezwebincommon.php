<?php
/**
 * File containing the ezwebincommon.php script.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// eZWebin install/updagrade helper routines.
// file  bin/php/ezwebincommon.php


/*!
 define constans
*/
define( "EZ_INSTALL_PACKAGE_EXTRA_ACTION_QUIT", 'q' );
define( "EZ_INSTALL_PACKAGE_EXTRA_ACTION_SKIP_PACKAGE", 's' );

/*!
 define global vars
*/
global $cli;
global $script;


/*!
 includes
*/
require_once 'autoload.php';

// The code is in kernel/private/classes/commands/ezwebincommon.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Ezwebincommon::main( __FILE__ );
