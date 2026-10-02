#!/usr/bin/env php
<?php
/**
 * File containing the clusterize.php script.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/*

NOTE:

 Please read doc/features/3.8/clustering.txt and set up clustering
 before running this script.

*/

error_reporting( E_ALL | E_NOTICE );

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/clusterize.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Clusterize::main( __FILE__ );
