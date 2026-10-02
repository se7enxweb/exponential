#!/usr/bin/env php
<?php
/**
 * Cluster files purge script
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/clusterpurge.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Clusterpurge::main( __FILE__ );
