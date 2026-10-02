#!/usr/bin/env php
<?php
/**
 * File containing the ezwebinupgrade.php script.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 * @description Upgrade an existing eZWebin installation to the current package version
 * @long-description Applies incremental upgrades to an eZWebin-based site, updating content class attributes, settings, and data structures to match the current eZWebin package version.
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
