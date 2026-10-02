#!/usr/bin/env php
<?php
/**
 * File containing the script to cleanup versions according content.ini/[VersionManagement]/DefaultVersionHistoryLimit
 * and VersionHistoryClass settings
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/cleanupversions.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Cleanupversions::main( __FILE__ );
