<?php
/**
 * @description Process scheduled hide and unhide actions on content objects
 *
 * File containing the hide.php cronjob.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/hide.php (#207); this file is the entry point.
return \Exponential\Cronjob\Kernel\Hide::main( __FILE__, get_defined_vars() );
