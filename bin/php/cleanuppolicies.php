#!/usr/bin/env php
<?php
/**
 * File containing the script to cleanup from database policies defined on module which do not exist in a modules folder
 * according to settings from module.ini/[ModuleSettings]/ExtensionRepositories
 *
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/cleanuppolicies.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Cleanuppolicies::main( __FILE__ );
