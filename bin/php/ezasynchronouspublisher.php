#!/usr/bin/env php
<?php
/**
 * Entry point of bin/php/ezasynchronouspublisher.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */


declare( ticks=1 );

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/ezasynchronouspublisher.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Ezasynchronouspublisher::main( __FILE__ );
