<?php
/**
 * @description Release content objects stuck in a locked editing state
 *
 * File containing the unlock.php cronjob
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/unlock.php (#207); this file is the entry point.
return \Exponential\Cronjob\Kernel\Unlock::main( __FILE__, get_defined_vars() );
