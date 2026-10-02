<?php
/**
 * @description Process scheduled hide and unhide actions on content objects
 *
 * File containing the hide.php cronjob.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/hide.php (#207); this file is the entry point.
return \Exponential\Cronjob\Kernel\Hide::main( __FILE__, get_defined_vars() );
