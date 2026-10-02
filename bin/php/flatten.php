#!/usr/bin/env php
<?php
/**
 * File containing the flatten.php script.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

set_time_limit( 0 );

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/flatten.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Flatten::main( __FILE__ );
