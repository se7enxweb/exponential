<?php
/**
 * @description Flush the in-memory view count buffer to the database
 *
 * File containing the updateviewcount.php cronjob
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/updateviewcount.php (#207); this file is the entry point.
return \Exponential\Cronjob\Kernel\Updateviewcount::main( __FILE__, get_defined_vars() );
