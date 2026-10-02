<?php
/**
 * @description Remove expired, purged and orphaned entries from the role-aware HTTP cache
 *
 * File containing the httpcache_cleanup.php cronjob
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/httpcache_cleanup.php (#207); this file is the entry point.
return \Exponential\Cronjob\Kernel\HttpcacheCleanup::main( __FILE__, get_defined_vars() );
