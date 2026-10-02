<?php
/**
 * @description Remove expired and stale entries from the static page cache
 *
 * File containing the staticcache_cleanup.php cronjob
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/staticcache_cleanup.php (#207); this file is the entry point.
return \Exponential\Cronjob\Kernel\StaticcacheCleanup::main( __FILE__, get_defined_vars() );
