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
 * After a global clear every file of that cache is garbage. Instead of looking
 * at each one, its directory -- content, template-block -- is then renamed into
 * .cleanup-trash in the cache directory -- instant, so a request that stores a
 * new entry at the same moment simply writes into a new, empty tree -- and
 * deleted from there without a stat per file. All renames come first, then the
 * sweep of caches that were not cleared, then the deleting. Entries stored
 * between the clear and this run go with it and are generated again.
 * [CacheCleanupSettings] RenameAfterClear=disabled keeps the file-by-file sweep.
 * A clear through eZCache (administration interface, bin/php/ezcache.php) does
 * that rename itself with site.ini [FileSettings] RenameExpiredCaches, and
 * records the clear in the state file, so it is not moved aside twice.
 * Which clear was last handled is kept in cachecleanup-state.json in the cache
 * directory; on the first run there is none, and the sweep runs.
 *
 * The cache directory itself is never renamed: on a production system it may be
 * a link to another disk, and the trash inside it keeps every rename on one file
 * system. A cache's directory that is itself a link is not renamed either --
 * that would move the link and leave the files -- but emptied into a trash
 * inside it. Deleting never follows a link; the link itself is left alone.
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
$renameAfterClear = $cronIni->variable( 'CacheCleanupSettings', 'RenameAfterClear' ) === 'enabled';

$now = time();
$ageLimit = $maxAge > 0 ? $now - $maxAge : 0;
$cacheDir = eZSys::cacheDirectory();
$contentCacheDir = $cacheDir . '/' . eZINI::instance()->variable( 'ContentSettings', 'CacheDir' );
// Not a PHP file: the opcode cache would keep serving an old version of it
$stateFile = $cacheDir . '/cachecleanup-state.json';
$state = is_file( $stateFile ) ? json_decode( (string)file_get_contents( $stateFile ), true ) : null;
if ( !is_array( $state ) )
    $state = array();

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
        if ( $entry === eZCacheTrash::TRASH_NAME )
        {
            eZCacheTrash::register( $path );
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
eZCacheTrash::begin();
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
            $complete = eZCacheTrash::discard( $dir );
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

    eZFile::create( basename( $stateFile ), dirname( $stateFile ), json_encode( $state ), true );

    // Last the deleting, with every cache already generating into its new tree,
    // including whatever an interrupted run left behind
    eZCacheTrash::register( $cacheDir . '/' . eZCacheTrash::TRASH_NAME );
    $emptied = eZCacheTrash::flush();
}
finally
{
    // Always, or the deferred deletes of this process never happen.
    eZCacheTrash::end();
}
if ( $emptied > 0 )
{
    $cli->output( "Deleted $emptied moved-aside " . ( $emptied === 1 ? 'entry' : 'entries' ) );
}

eZSubtreeCache::removeAllExpiryCacheFromDisk();

?>
