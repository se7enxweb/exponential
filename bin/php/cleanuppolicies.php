#!/usr/bin/env php
<?php
/**
 * File containing the script to cleanup from database policies defined on module which do not exist in a modules folder
 * according to settings from module.ini/[ModuleSettings]/ExtensionRepositories
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/cleanuppolicies.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Cleanuppolicies::main( __FILE__ );
