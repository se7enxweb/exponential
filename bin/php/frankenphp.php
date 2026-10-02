#!/usr/bin/env php
<?php
/**
 * File containing the FrankenPHP control script.
 *
 * @description Control the FrankenPHP engine of the Exponential web server
 * @alias fp
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

require 'autoload.php';

// The code is in kernel/private/classes/commands/frankenphp.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Frankenphp::main( __FILE__ );
