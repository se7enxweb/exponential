#!/usr/bin/env php
<?php
/**
 * File containing the services.php script.
 *
 * Compile, list, show and check the kernel service container (settings/kernelservices.ini).
 *
 * @alias exp:services:compile
 * @alias exp:services:list
 * @alias exp:services:show
 * @alias exp:services:check
 *
 *   ./console exp:services:compile           record the extension activation of every siteaccess
 *   ./console exp:services:list [--tag=...]  the services of a siteaccess (-s)
 *   ./console exp:services:show <id>         one service
 *   ./console exp:services:check             definitions and compiled files; exit 1 on an error
 *
 * With --kernel-services-record this script is the recording process that exp:services:compile starts: it boots
 * the web kernel once per siteaccess and writes var/cache/kernelservices/ (expKernelServiceCompiler::recordMain()).
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

if ( in_array( '--kernel-services-record', $argv, true ) )
    exit( expKernelServiceCompiler::recordMain( $argv ) );

// The code is in kernel/private/classes/commands/services.php; this file is the entry point.
\Exponential\Command\Kernel\Services::main( __FILE__ );
