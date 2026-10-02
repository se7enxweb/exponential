#!/usr/bin/env php
<?php
/**
 * File containing the web server control script.
 *
 * @description Control the Exponential web server (php, frankenphp or qbix engines)
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

require 'autoload.php';

// The code is in kernel/private/classes/commands/webserver.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Webserver::main( __FILE__ );
