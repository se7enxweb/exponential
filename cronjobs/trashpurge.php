<?php
/**
 * @description Permanently delete all objects currently held in the trash
 *
 * Trash purge cronjob
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/trashpurge.php (#207); this file is the entry point.
return \Exponential\Cronjob\Kernel\Trashpurge::main( __FILE__, get_defined_vars() );
