<?php
/**
 * @description Remove expired and old view cache and cache-block files from disk
 *
 * The view cache and cache-block expire by timestamp: clearing them only moves
 * a timestamp in expiry.php, and the files stay on disk until the same key is
 * generated again. Keys that never come back (removed nodes, old view
 * parameters, changed role combinations) stay there for good. This removes
 *
 * - files older than the last global expiry of their cache, which can never be
 *   served again,
 * - files older than [CacheCleanupSettings] MaxAge, whatever their expiry; a
 *   cold entry that was still valid costs one regeneration,
 * - the renamed subtree directories that DelayedCacheBlockCleanup leaves in
 *   template-block-expiry.
 *
 * Only for the file system handler: with eZDFS the cluster database is the
 * source of truth, and cluster_maintenance purges it.
 *
 *   php runcronjobs.php -s <siteaccess> cache_cleanup
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

if ( !( eZClusterFileHandler::instance() instanceof eZFSFileHandler ) )
{
    $cli->output( "The cluster file handler is not file based, use the cluster_maintenance part instead." );
    return;
}

$cronIni = eZINI::instance( 'cronjob.ini' );
$maxAge = (int)$cronIni->variable( 'CacheCleanupSettings', 'MaxAge' );
$sleep = (int)$cronIni->variable( 'CacheCleanupSettings', 'IterationSleep' );

$now = time();
$ageLimit = $maxAge > 0 ? $now - $maxAge : 0;
$cacheDir = eZSys::cacheDirectory();
$contentCacheDir = $cacheDir . '/' . eZINI::instance()->variable( 'ContentSettings', 'CacheDir' );

// Not the shared instance: runcronjobs.php creates it before switching to the
// siteaccess, so it has read the expiry.php of the default var directory.
$expiryHandler = new eZExpiryHandler();
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
$sweep = function( $dir, $before, &$stats ) use ( &$sweep, $sleep )
{
    $entries = @scandir( $dir );
    if ( $entries === false )
        return;

    foreach ( $entries as $entry )
    {
        if ( $entry === '.' || $entry === '..' )
            continue;

        $path = $dir . '/' . $entry;
        if ( is_dir( $path ) && !is_link( $path ) )
        {
            $sweep( $path, $before, $stats );
            // Fails harmlessly if the directory is not empty, or was filled again meanwhile
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

foreach ( $areas as $name => $area )
{
    list( $dir, $expiry ) = $area;
    $stats = array( 'scanned' => 0, 'removed' => 0, 'bytes' => 0 );
    if ( is_dir( $dir ) )
        $sweep( $dir, max( $expiry, $ageLimit ), $stats );

    $cli->output( sprintf( "%s: %d files looked at, %d removed, %.1f KB freed",
                           $name, $stats['scanned'], $stats['removed'], $stats['bytes'] / 1024 ) );
}

eZSubtreeCache::removeAllExpiryCacheFromDisk();

?>
