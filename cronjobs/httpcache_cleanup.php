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

$contract = ezpHttpCacheListener::contract();
if ( !$contract )
{
    $cli->output( "The HTTP cache is not enabled here, or has stored nothing yet (settings/httpcache.ini)" );
    return;
}

$start = microtime( true );
$counts = $contract->gc();
$cli->output( sprintf(
    "HTTP cache cleanup: removed %d entries, %d bodies, %d user records, %d temporary files; kept %d entries (%.2fs)",
    $counts['entries'], $counts['bodies'], $counts['records'], $counts['tmp'], $counts['kept'],
    microtime( true ) - $start
) );
