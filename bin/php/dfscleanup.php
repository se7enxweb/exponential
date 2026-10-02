#!/usr/bin/env php
<?php
/**
 * File containing the script to cleanup files in a DFS setup
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/dfscleanup.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Dfscleanup::main( __FILE__ );
