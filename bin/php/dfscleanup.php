#!/usr/bin/env php
<?php
/**
 * File containing the script to cleanup files in a DFS setup
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/dfscleanup.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Dfscleanup::main( __FILE__ );
