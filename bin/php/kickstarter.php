#!/usr/bin/env php
<?php
/**
 * @description Kickstarter runner: "ini" writes kickstart.ini, "run" installs from it (a dry run unless --apply)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

$rootDir = dirname( dirname( __DIR__ ) );
chdir( $rootDir );
require_once 'autoload.php';

// The code is in kernel/private/classes/commands/kickstarter.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Kickstarter::main( __FILE__ );
