<?php
/**
 * File containing the expCacheClearListener interface
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * A class an extension registers in site.ini [Cache] ClearListeners[] to clear caches it keeps outside the kernel's
 * file and cluster storage (Valkey, Memcached, a CDN ...) whenever the kernel clears its own.
 *
 * eZCache calls cacheItemCleared() after every cache item it clears or purges, whoever asked: bin/php/ezcache.php
 * (--clear-id, --clear-tag, --clear-all, --purge), Setup > Cache in the administration interface, exp:velocity deploy.
 * eZContentCacheManager calls nodeCachesCleared() for the nodes of a content change (publish, move, hide ...),
 * whatever the static cache handler is, and cacheItemCleared() with the content item when it expires every view cache.
 *
 * A listener whose class does not exist (its extension is not active) is left out. One that throws is reported as a
 * warning and the clear goes on: a cache server that does not answer never stops the kernel's own clear.
 *
 * @package kernel
 */
interface expCacheClearListener
{
    /**
     * A cache item was cleared or purged.
     *
     * @param array $cacheItem the item as eZCache::fetchList() lists it ('id', 'name', 'tag', 'path' ...), with
     *        'purge' true for a purge
     */
    public function cacheItemCleared( array $cacheItem );

    /**
     * The view caches of these nodes were cleared for a content change.
     *
     * @param array $nodeIDList node ids, as the static cache handler receives them: the changed nodes and the nodes
     *        related to them, then their path up to the root
     */
    public function nodeCachesCleared( array $nodeIDList );
}
