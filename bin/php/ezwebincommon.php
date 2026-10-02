<?php
/**
 * File containing the ezwebincommon.php script.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 * @description Shared helper functions library for eZWebin install and upgrade scripts
 * @long-description Included by ezwebininstall.php and ezwebinupgrade.php. Provides common utility functions for eZWebin package installation, configuration, and upgrade steps. Not intended to be run directly.
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
