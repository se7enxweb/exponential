#!/usr/bin/env php
<?php
/**
 * File containing the contentwaittimeout.php script
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package
 */

/**
 * This script starts parallel publishing processes in order to trigger lock wait timeouts
 * Launch it using $./bin/php/ezexec.phhp contentwaittimeout.php
 *
 * To customize the class, parent node or concurrency level, modify the 3 variables below.
 * @package tests
 */


require_once 'autoload.php';

// The code is in kernel/private/classes/commands/ezpublishingbenchmark.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Ezpublishingbenchmark::main( __FILE__ );
