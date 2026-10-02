#!/usr/bin/env php
<?php
/**
 * File containing the FrankenPHP control script.
 *
 * @description Control the FrankenPHP engine of the Exponential web server
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require 'autoload.php';

// The code is in kernel/private/classes/commands/frankenphp.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Frankenphp::main( __FILE__ );
