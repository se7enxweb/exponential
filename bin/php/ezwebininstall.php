#!/usr/bin/env php
<?php
/**
 * File containing the ezwebininstall.php script.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 * @description Install the eZWebin site package and configure the site
 * @long-description Installs the eZWebin package into a freshly configured Exponential site. Sets up required content classes, siteaccesses, and design settings. Typically run once during the initial site setup wizard.
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
