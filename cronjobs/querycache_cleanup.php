<?php
/**
 * @description Remove the result files of the SQL query cache that are older than MaxAge or from before the last clear
 *
 * File containing the querycache_cleanup.php cronjob
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/querycache_cleanup.php; this file is the entry point.
return \Exponential\Cronjob\Kernel\QuerycacheCleanup::main( __FILE__, get_defined_vars() );
