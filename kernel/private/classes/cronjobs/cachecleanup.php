<?php
/**
 * The code of cronjobs/cachecleanup.php, moved into a class (#207 stage 1). The file cronjobs/cachecleanup.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */

namespace Exponential\Cronjob\Kernel
{

class Cachecleanup extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        if ( !( \eZClusterFileHandler::instance() instanceof \eZFSFileHandler ) )
        {
            $cli->output( "The cluster file handler is not file based, use the cluster_maintenance part instead." );
            return;
        }

        $cronIni = \eZINI::instance( 'cronjob.ini' );
        $maxAge = (int)$cronIni->variable( 'CacheCleanupSettings', 'MaxAge' );
        $sleep = (int)$cronIni->variable( 'CacheCleanupSettings', 'IterationSleep' );
        $renameAfterClear = $cronIni->variable( 'CacheCleanupSettings', 'RenameAfterClear' ) === 'enabled';

        $now = time();
        $ageLimit = $maxAge > 0 ? $now - $maxAge : 0;
        $cacheDir = \eZSys::cacheDirectory();
        $contentCacheDir = $cacheDir . '/' . \eZINI::instance()->variable( 'ContentSettings', 'CacheDir' );
        // Not a PHP file: the opcode cache would keep serving an old version of it
        $stateFile = $cacheDir . '/cachecleanup-state.json';
        $state = is_file( $stateFile ) ? json_decode( (string)file_get_contents( $stateFile ), true ) : null;
        if ( !is_array( $state ) )
            $state = array();

        // Not the shared instance: runcronjobs.php creates it before switching to the
        // siteaccess, so it has read the expiry.php of the default var directory.
        $expiryHandler = new \eZExpiryHandler();
        $expiredBefore = function( $name ) use ( $expiryHandler )
        {
            return $expiryHandler->hasTimestamp( $name ) ? (int)$expiryHandler->timestamp( $name ) : 0;
        };
        // A cache-block with ignore_content_expiry or subtree_expiry does not expire
        // with template-block-cache, so only the global timestamp is certain for all.
        $areas = array(
            'view cache' => array( $contentCacheDir,
                                   $expiredBefore( 'content-view-cache' ) ),
            'cache-block' => array( $cacheDir . '/template-block',
                                    $expiredBefore( 'global-template-block-cache' ) ),
        );

        /**
         * Removes the .cache files below $dir modified before $before, and the
         * directories that leaves empty.
         */
        $sweep = function( $dir, $before, &$stats, &$seen ) use ( &$sweep, $sleep )
        {
            // A link back up the tree must not send this round in circles
            $real = realpath( $dir );
            if ( $real === false || isset( $seen[$real] ) )
                return;
            $seen[$real] = true;

            $entries = @scandir( $dir );
            if ( $entries === false )
                return;

            foreach ( $entries as $entry )
            {
                if ( $entry === '.' || $entry === '..' )
                    continue;

                $path = $dir . '/' . $entry;
                // Left behind by an interrupted run; emptied at the end
                if ( $entry === \eZCacheTrash::TRASH_NAME )
                {
                    \eZCacheTrash::register( $path );
                    continue;
                }
                if ( is_dir( $path ) )
                {
                    $sweep( $path, $before, $stats, $seen );
                    // Fails harmlessly if the directory is not empty, or was filled
                    // again meanwhile. Never for a link, which stays.
                    if ( !is_link( $path ) )
                        @rmdir( $path );
                    continue;
                }
                if ( substr( $entry, -6 ) !== '.cache' )
                    continue;

                ++$stats['scanned'];
                if ( $sleep > 0 && $stats['scanned'] % 1000 === 0 )
                    usleep( $sleep );

                $stat = @stat( $path );
                if ( $stat !== false && $stat['mtime'] < $before && @unlink( $path ) )
                {
                    ++$stats['removed'];
                    $stats['bytes'] += $stat['size'];
                }
            }
        };

        // First every rename, so that no cache waits behind a sweep or a delete
        // before its new, empty tree is there to be generated into; nothing is deleted
        // before the end()
        \eZCacheTrash::begin();
        try
        {
            $toSweep = array();
            foreach ( $areas as $name => $area )
            {
                list( $dir, $expiry ) = $area;
                $handled = isset( $state[$name] ) ? (int)$state[$name] : null;
                $state[$name] = $expiry;
                if ( !is_dir( $dir ) )
                    continue;

                if ( $renameAfterClear && $handled !== null && $expiry > $handled )
                {
                    // As a whole into the cache directory's trash; a link is emptied into
                    // a trash inside it instead
                    $complete = \eZCacheTrash::discard( $dir );
                    $cli->output( "$name: cleared at " . date( 'Y-m-d H:i:s', $expiry ) . ", moved aside" );
                    if ( $complete )
                        continue;
                    $cli->output( "$name: not everything could be moved aside, removing the rest file by file" );
                }
                $toSweep[$name] = $area;
            }

            // Then the file-by-file sweep of whatever was not moved aside
            foreach ( $toSweep as $name => $area )
            {
                list( $dir, $expiry ) = $area;
                $stats = array( 'scanned' => 0, 'removed' => 0, 'bytes' => 0 );
                $seen = array();
                $sweep( $dir, max( $expiry, $ageLimit ), $stats, $seen );

                $cli->output( sprintf( "%s: %d files looked at, %d removed, %.1f KB freed",
                                       $name, $stats['scanned'], $stats['removed'], $stats['bytes'] / 1024 ) );
            }

            \eZFile::create( basename( $stateFile ), dirname( $stateFile ), json_encode( $state ), true );

            // Last the deleting, with every cache already generating into its new tree,
            // including whatever an interrupted run left behind
            \eZCacheTrash::register( $cacheDir . '/' . \eZCacheTrash::TRASH_NAME );
            $emptied = \eZCacheTrash::flush();
        }
        finally
        {
            // Always, or the deferred deletes of this process never happen.
            \eZCacheTrash::end();
        }
        if ( $emptied > 0 )
        {
            $cli->output( "Deleted $emptied moved-aside " . ( $emptied === 1 ? 'entry' : 'entries' ) );
        }

        \eZSubtreeCache::removeAllExpiryCacheFromDisk();
    }
}

}
