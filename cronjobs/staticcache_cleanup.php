<?php
/**
 * @description Remove expired and stale entries from the static page cache
 *
 * File containing the staticcache_cleanup.php cronjob
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/staticcache_cleanup.php (#207); this file is the entry point.
return \Exponential\Cronjob\Kernel\StaticcacheCleanup::main( __FILE__, get_defined_vars() );
