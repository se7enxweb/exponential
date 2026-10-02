<?php
/**
 * The code of cronjobs/httpcache_cleanup.php, moved into a class (#207 stage 1). The file cronjobs/httpcache_cleanup.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of cronjobs/httpcache_cleanup.php:
 *
 *
 * @description Remove expired, purged and orphaned entries from the role-aware HTTP cache
 *
 * File containing the httpcache_cleanup.php cronjob
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 *
 */

namespace Exponential\Cronjob\Kernel
{

class HttpcacheCleanup extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $contract = \ezpHttpCacheListener::contract();
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
    }
}

}
