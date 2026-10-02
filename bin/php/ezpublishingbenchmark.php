#!/usr/bin/env php
<?php
/**
 * File containing the contentwaittimeout.php script
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
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
