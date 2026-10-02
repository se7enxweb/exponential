<?php
/**
 * @description Permanently delete all objects currently held in the trash
 *
 * Trash purge cronjob
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/trashpurge.php (#207); this file is the entry point.
return \Exponential\Cronjob\Kernel\Trashpurge::main( __FILE__, get_defined_vars() );
