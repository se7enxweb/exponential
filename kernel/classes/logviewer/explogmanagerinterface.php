<?php
/**
 * What log management offers the views of the log viewer (doc/features/6.0/log-viewer.md, "Managing logs"): every log
 * with its archives and limits, and rotate, prune, compress and empty, always as a plan first (the dry run) and then
 * applied. expLogManager (the kernel's service) and expLogManagerFallback implement it; expLogManagerFactory::get()
 * gives the one to use.
 *
 * Shapes:
 *   logs(): key => array( key, id, path, name, group, size, mtime, lines (estimated), archives (list of array( id, name,
 *           rotation, size, gz, mtime )), archive_bytes, growth (bytes an hour since the last rotation, or null),
 *           limits (array( max_size, keep, compress, max_age_days, source => limit => array( file, section, var ) )),
 *           writable (bool: the web server's user can write the file and its directory), owner, owner_group, managed (bool) )
 *           (group is the viewer's group: kernel, site, extensions, webserver; owner_group the file's owning group)
 *   select( $names ): array( keys => the keys to manage, refused => name => reason (unknown, archive, webserver, outside) )
 *   plan( $keys, $op, $opts ): array( steps => list of array( key, op, from, to, bytes ), unknown => list of keys )
 *           $op rotate | prune | compress | truncate; $opts force (rotate also under the size limit), archive_first
 *           (truncate: copy to .1 first, default true), days (prune: older than this; default the log's max_age_days)
 *           A step's op: delete | rename | copy | gzip | truncate; from and to are file ids (relative to the root).
 *   apply( $plan ): array( ok, error, steps => the plan's steps, each with done (bool) and error )
 *   rotate( $key ), prune( $key, $days ), compress( $key ), truncate( $key, $archiveFirst ): array( ok, error, steps )
 *   Errors: not_allowed | unknown | locked | rename | missing | moved | skipped | empty
 *   effectiveSettings( $key ): the limits with their sources, or null for an unknown key
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

interface expLogManagerInterface
{
    /** @return array key => log (see above) */
    public function logs();

    /**
     * @param string[] $names log keys (or file ids) asked for
     * @return array keys (to manage), refused (name => unknown | archive | webserver | outside)
     */
    public function select( array $names );

    /**
     * @param array|string $keys a list of keys, or 'all'
     * @param string $op rotate | prune | compress | truncate
     * @param array $opts
     * @return array steps, unknown
     */
    public function plan( $keys, $op, array $opts = array() );

    /**
     * @param array $plan from plan()
     * @return array ok, error, steps
     */
    public function apply( array $plan );

    /** @return array ok, error, steps */
    public function rotate( $key );

    /** @return array ok, error, steps */
    public function prune( $key, $days = null );

    /** @return array ok, error, steps */
    public function compress( $key );

    /** @return array ok, error, steps */
    public function truncate( $key, $archiveFirst = true );

    /** @return array|null */
    public function effectiveSettings( $key );
}
