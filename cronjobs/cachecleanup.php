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

// The code is in kernel/private/classes/cronjobs/cachecleanup.php (#207); this file is the entry point.
return \Exponential\Cronjob\Kernel\Cachecleanup::main( __FILE__, get_defined_vars() );
