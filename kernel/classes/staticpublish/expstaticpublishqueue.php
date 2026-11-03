<?php
/**
 * File containing the expStaticPublishQueue class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Publishing on content publish (target trigger "publish"): the queue of the pages that changed since a target was
 * last published, kept in exp_static_page.
 *
 *   nodes()  called by eZContentCacheManager whenever the view cache of nodes is cleared (a publish, a move, a
 *            hide, ...): for every target with Triggers[]=publish, the rows of those nodes are marked stale with the
 *            time (stale = unix time); a node the target has no page of yet gets a row of its own
 *            (path .queue/<node id>, no hash), so a new page is built too. Cheap: nothing is read or written while
 *            static publishing is off or no target has the trigger.
 *   due()    the targets whose stale rows are older than the debounce ([StaticPublishSettings] PublishDebounce,
 *            default 120 seconds after the last change), with the node ids to build
 *   done()   after a publish of those nodes: their queue rows are removed (the pages' own rows are rewritten by
 *            expStaticStatus::writePages()).
 *
 * The cronjob part staticpublish builds the due targets incrementally and publishes them. A build that fails leaves
 * the rows stale; the next run tries again.
 *
 * @since 6.0.15
 * @package kernel
 */
class expStaticPublishQueue
{
    /** The path of a queued node the target has no page of yet */
    const QUEUE_PREFIX = '.queue/';

    /** The default debounce in seconds */
    const DEBOUNCE = 120;

    /** The most nodes one run builds incrementally; more make a full build */
    const MAX_NODES = 200;

    /**
     * The ids of the targets that publish on content publish.
     *
     * @param eZINI|null $ini
     * @return string[]
     */
    public static function targets( $ini = null )
    {
        if ( !expStaticTarget::enabled( $ini ) )
            return array();
        $ids = array();
        foreach ( expStaticTarget::all( $ini ) as $id => $target )
            if ( in_array( 'publish', (array)$target->setting( 'Triggers' ), true ) )
                $ids[] = (string)$id;
        return $ids;
    }

    /**
     * Marks the pages of nodes stale for every target that publishes on content publish.
     *
     * @param int[] $nodeIds
     * @param eZDBInterface|null $db default eZDB::instance()
     * @param string[]|null $targets default targets()
     * @param int|null $now
     * @return int rows marked
     */
    public static function nodes( array $nodeIds, $db = null, $targets = null, $now = null )
    {
        try
        {
            $targets = $targets === null ? self::targets() : array_values( array_map( 'strval', (array)$targets ) );
            $nodeIds = array_values( array_unique( array_filter( array_map( 'intval', $nodeIds ) ) ) );
            if ( !$targets || !$nodeIds )
                return 0;
            $db = $db ?: eZDB::instance();
            if ( !$db || expStaticStatus::exists( $db ) !== true )
                return 0;
            $now = $now === null ? time() : (int)$now;
            $marked = 0;
            $db->begin();
            try
            {
                foreach ( $targets as $target )
                {
                    $have = array();
                    $rows = expKernelTableSchema::tryArrayQuery( $db, 'SELECT * FROM ' . expStaticStatus::PAGE_TABLE . " WHERE target = '" . $db->escapeString( $target ) . "'"
                                                                    . ' AND node_id IN ( ' . implode( ', ', $nodeIds ) . ' )' );
                    foreach ( (array)$rows as $row )
                    {
                        $row = expStaticStatus::normalize( array_change_key_case( (array)$row, CASE_LOWER ), expStaticStatus::PAGE_COLUMNS );
                        $have[$row['node_id']] = true;
                        $row['stale'] = $now;
                        self::replace( $db, $row );
                        $marked++;
                    }
                    foreach ( $nodeIds as $nodeId )
                        if ( !isset( $have[$nodeId] ) )
                        {
                            self::replace( $db, expStaticStatus::normalize( array( 'target' => $target, 'path' => self::QUEUE_PREFIX . $nodeId, 'node_id' => $nodeId,
                                                                                   'hash' => '', 'page_bytes' => 0, 'built_at' => 0, 'stale' => $now ), expStaticStatus::PAGE_COLUMNS ) );
                            $marked++;
                        }
                }
            }
            catch ( Throwable $e )
            {
                // nothing of a transaction that failed half way is committed by a later commit()
                if ( $db->transactionCounter() > 0 )
                    $db->rollback();
                throw $e;
            }
            $db->commit();
            return $marked;
        }
        catch ( Throwable $e )
        {
            // marking pages stale never stops a publish of content
            if ( class_exists( 'eZDebug' ) )
                eZDebug::writeWarning( 'Static publishing: the pages could not be marked stale: ' . $e->getMessage(), __METHOD__ );
            return 0;
        }
    }

    /** One row written again (DELETE and INSERT: what every engine's driver takes). */
    private static function replace( $db, array $row )
    {
        $db->query( 'DELETE FROM ' . expStaticStatus::PAGE_TABLE . " WHERE target = '" . $db->escapeString( $row['target'] ) . "' AND path = '" . $db->escapeString( $row['path'] ) . "'" );
        $values = array();
        foreach ( $row as $value )
            $values[] = is_int( $value ) ? (string)$value : "'" . $db->escapeString( (string)$value ) . "'";
        $db->query( 'INSERT INTO ' . expStaticStatus::PAGE_TABLE . ' ( ' . implode( ', ', array_keys( $row ) ) . ' ) VALUES ( ' . implode( ', ', $values ) . ' )' );
    }

    /**
     * The stale rows of a target.
     *
     * @param eZDBInterface|null $db
     * @param string $target
     * @return array[] path => row
     */
    public static function stale( $db, $target )
    {
        return array_filter( expStaticStatus::pages( $db, $target ), function ( $row ) { return $row['stale'] > 0; } );
    }

    /**
     * Whether a target's queue is due, and what to build. Pure apart from the read.
     *
     * @param eZDBInterface|null $db
     * @param string $target
     * @param int $debounce seconds after the last change
     * @param int|null $now
     * @return array|null null when nothing waits or the last change is too recent; else nodes (ids), full (bool:
     *         more than MAX_NODES), last (the time of the last change)
     */
    public static function due( $db, $target, $debounce = self::DEBOUNCE, $now = null )
    {
        $rows = self::stale( $db, $target );
        if ( !$rows )
            return null;
        $now = $now === null ? time() : (int)$now;
        $last = max( array_column( $rows, 'stale' ) );
        if ( $now - $last < (int)$debounce )
            return null;
        $nodes = array_values( array_unique( array_filter( array_map( 'intval', array_column( $rows, 'node_id' ) ) ) ) );
        sort( $nodes );
        return array( 'nodes' => $nodes, 'full' => count( $nodes ) > self::MAX_NODES, 'last' => $last );
    }

    /**
     * After a publish: the queue rows of the nodes built are removed, and the stale marks older than the build are
     * cleared (a change made while it ran stays).
     *
     * @param eZDBInterface|null $db
     * @param string $target
     * @param int $startedAt when the build started
     * @return int rows changed
     */
    public static function done( $db, $target, $startedAt )
    {
        if ( !$db || expStaticStatus::exists( $db ) !== true )
            return 0;
        $n = 0;
        $db->begin();
        try
        {
            foreach ( self::stale( $db, $target ) as $path => $row )
            {
                if ( $row['stale'] >= (int)$startedAt )
                    continue;
                if ( strpos( (string)$path, self::QUEUE_PREFIX ) === 0 )
                    $db->query( 'DELETE FROM ' . expStaticStatus::PAGE_TABLE . " WHERE target = '" . $db->escapeString( (string)$target ) . "' AND path = '" . $db->escapeString( (string)$path ) . "'" );
                else
                {
                    $row['stale'] = 0;
                    self::replace( $db, $row );
                }
                $n++;
            }
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        return $n;
    }
}
