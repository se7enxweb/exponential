<?php
/**
 * @description Flush the in-memory view count buffer to the database
 *
 * File containing the updateviewcount.php cronjob
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/updateviewcount.php (#207); this file is the entry point.
return \Exponential\Cronjob\Kernel\Updateviewcount::main( __FILE__, get_defined_vars() );
