<?php
/**
 * File containing the expDependencyOrder class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A stable topological sort shared by the kernel's ordered registries (upgrade steps, migrations).
 *
 * The caller gives the ids in their priority order (whatever "earlier" means to it: stage and version, timestamp) and
 * what each id waits for. The result keeps the priority order wherever the dependencies allow it: at each point the
 * earliest id whose dependencies are all placed comes next. An id that waits for something not in the list waits for
 * nothing (the caller reports such a requirement itself). Ids that can never be placed, because they are part of a
 * cycle or wait for one, are appended at the end in priority order and returned in $blocked.
 *
 * Pure: no settings, no database. O((n + e) log n).
 *
 * @since 6.0.15
 * @package kernel
 */
class expDependencyOrder
{
    /**
     * The ids in the order they run.
     *
     * @param string[] $ids in priority order; a repeated id counts once, at its first place
     * @param array $requires id => string[] the ids it waits for
     * @param string[] $blocked receives the ids that could not be placed (a cycle), in priority order
     * @return string[] every id once: the placed ones in order, then the blocked ones
     */
    public static function sort( array $ids, array $requires, &$blocked = array() )
    {
        $rank = array();
        $byRank = array();
        foreach ( $ids as $id )
        {
            $id = (string)$id;
            if ( isset( $rank[$id] ) )
                continue;
            $rank[$id] = count( $byRank );
            $byRank[] = $id;
        }

        $waiting = array();
        $dependents = array();
        $heap = new SplMinHeap();
        foreach ( $byRank as $r => $id )
        {
            $deps = array();
            if ( isset( $requires[$id] ) )
                foreach ( (array)$requires[$id] as $dep )
                    if ( isset( $rank[(string)$dep] ) )
                        $deps[(string)$dep] = true;
            $waiting[$id] = count( $deps );
            foreach ( array_keys( $deps ) as $dep )
                $dependents[$dep][] = $id;
            if ( $waiting[$id] === 0 )
                $heap->insert( $r );
        }

        $ordered = array();
        $placed = array();
        while ( !$heap->isEmpty() )
        {
            $id = $byRank[$heap->extract()];
            $ordered[] = $id;
            $placed[$id] = true;
            if ( isset( $dependents[$id] ) )
                foreach ( $dependents[$id] as $next )
                    if ( --$waiting[$next] === 0 )
                        $heap->insert( $rank[$next] );
        }

        $blocked = array();
        foreach ( $byRank as $id )
            if ( !isset( $placed[$id] ) )
                $blocked[] = $id;
        return array_merge( $ordered, $blocked );
    }

    /**
     * One cycle among blocked ids, as a path that ends where it starts (a, b, c, a), for a readable message.
     *
     * @param string[] $blocked as sort() returned them
     * @param array $requires id => string[], as given to sort()
     * @return string[] the cycle; empty when $blocked holds none
     */
    public static function cycle( array $blocked, array $requires )
    {
        $in = array_flip( array_map( 'strval', $blocked ) );
        foreach ( array_keys( $in ) as $start )
        {
            // walk from $start, always to the first blocked dependency; within the blocked set every id waits for
            // another blocked id, so the walk must come back to an id it has seen
            $path = array();
            $seen = array();
            $id = (string)$start;
            while ( $id !== null && !isset( $seen[$id] ) )
            {
                $seen[$id] = count( $path );
                $path[] = $id;
                $next = null;
                foreach ( isset( $requires[$id] ) ? (array)$requires[$id] : array() as $dep )
                    if ( isset( $in[(string)$dep] ) )
                    {
                        $next = (string)$dep;
                        break;
                    }
                $id = $next;
            }
            if ( $id !== null )
            {
                $loop = array_slice( $path, $seen[$id] );
                $loop[] = $id;
                return $loop;
            }
        }
        return array();
    }
}
