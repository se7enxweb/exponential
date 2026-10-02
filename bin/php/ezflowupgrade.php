#!/usr/bin/env php
<?php
/**
 * File containing the ezflowupgrade.php script.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 * @description Upgrade eZ Flow block and zone structures to the current version
 * @long-description Migrates eZ Flow block, zone, and page content from a legacy data format to the current model. Run once after upgrading Exponential CMS on sites that use the eZ Flow extension.
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
