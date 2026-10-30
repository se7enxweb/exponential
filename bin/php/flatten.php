#!/usr/bin/env php
<?php
/**
 * File containing the flatten.php script.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

set_time_limit( 0 );

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/flatten.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Flatten::main( __FILE__ );
