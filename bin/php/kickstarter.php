#!/usr/bin/env php
<?php
/**
 * @description Kickstarter runner. Supports "ini" generation and full "run" setup subcommands.
 * @package   kernel
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license   For full copyright and license information view LICENSE file.
 */

$rootDir = dirname( dirname( __DIR__ ) );
chdir( $rootDir );
require_once 'autoload.php';

// The code is in kernel/private/classes/commands/kickstarter.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Kickstarter::main( __FILE__ );
