<?php
/**
 * File containing the expLogManagerInterface interface.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Log management (doc/features/6.0/log-rotation.md, "For developers"): what the command line, the cronjob part "logs"
 * and the log viewer's actions use. The kernel's implementation is expLogManager; another one (a cluster that keeps
 * its logs elsewhere) implements the same.
 *
 * Every operation is a plan first (plan(): nothing changes) that apply() carries out step by step. A step result has
 * done (bool) and error ('' | not_allowed | unknown | locked | rename | missing | moved | skipped | empty | unlink |
 * compress); missing, moved and skipped mean the disk changed since the plan and count as done.
 */
interface expLogManagerInterface
{
    /**
     * @return array key => key, id, path, name, label, group (kernel, site, extensions), size, mtime, lines (about),
     *               archives [{id, name, rotation, size, gz, mtime}], archive_bytes, growth (bytes an hour or null),
     *               limits {max_size, keep, compress, max_age_days, source{<limit>: {file, section, var}}}, writable,
     *               owner, owner_group, managed (false: the web server's, shown read only). key is a string, also for
     *               a key of digits only (404), which is an integer as the array key: compare keys as strings.
     */
    public function logs();

    /**
     * @param string $key
     * @return array max_size, keep, compress, max_age_days => {value, source{file, section, var}}
     */
    public function effectiveSettings( $key );

    /**
     * @param string[] $names keys or ids
     * @return array found (key => resolved file), unknown (names)
     */
    public function select( array $names );

    /**
     * @param string[]|string $keys keys or ids, or 'all'
     * @param string $op rotate | prune | compress | truncate
     * @param array $opts force, days, archive_first, from
     * @return array op, steps [{key, op, from, to, bytes[, reason]}], unknown []
     */
    public function plan( $keys, $op, array $opts = array() );

    /**
     * @param array $plan
     * @return array ok, error, steps (each with done and error)
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
}
