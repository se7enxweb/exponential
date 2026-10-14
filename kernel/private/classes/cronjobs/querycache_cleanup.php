<?php
/**
 * The code of cronjobs/querycache_cleanup.php. The file cronjobs/querycache_cleanup.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 *
 * Removes the result files of the SQL query cache (querycache.ini Storage=file, or auto without APCu) that can no
 * longer be answered: older than MaxAge or from before the last clear. A lookup never answers them anyway; without
 * this run they stay on disk until the same statement is stored again.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Cronjob\Kernel
{

class QuerycacheCleanup extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        // the including function's $cli (a cronjob part is run by runcronjobs.php), or the CLI's own
        $cli = isset( $scope['cli'] ) && $scope['cli'] instanceof \eZCLI ? $scope['cli'] : \eZCLI::instance();

        if ( !is_dir( \eZDBQueryCache::entriesDir() ) )
        {
            $cli->output( "The SQL query cache keeps no result files here (settings/querycache.ini Mode, Storage)" );
            return;
        }

        $start = microtime( true );
        $counts = \eZDBQueryCache::cleanupFiles();
        $cli->output( sprintf(
            "SQL query cache cleanup: removed %d result files (%d KB), %d temporary files; kept %d (%.2fs)",
            $counts['removed'], round( $counts['bytes'] / 1024 ), $counts['tmp'], $counts['kept'],
            microtime( true ) - $start
        ) );
    }
}

}
