<?php
/**
 * File containing the expContentJobLock class.
 *
 * Subtree locks of the content jobs. A job locks what it works on from the moment it is created until it is
 * done or cancelled (a failed job keeps its locks: it is resumable, and half of its work is done): a remove
 * locks each removed subtree, a copy its source subtree, its destination node and, once created, the subtree
 * of the copy. A second job, or a synchronous remove, move or copy in a view, that overlaps a lock is refused
 * with a clear message instead of being queued silently.
 *
 * Two kinds of lock:
 *   subtree  the node and everything below it: an operation on an ancestor, the node itself or a descendant
 *            overlaps it
 *   node     only the node as a target (a copy's destination): an operation on the node or an ancestor
 *            overlaps it, one on a sibling or a descendant does not
 *
 * The locks live in locks.json in the store's directory, changed only under the store's 'locks' flock, so two
 * jobs created at the same moment cannot both get an overlapping lock. An entry of a job that is no longer
 * active (or no longer exists) is ignored and dropped on the next change, so a lost update cannot block a
 * subtree for ever.
 *
 *   expContentJobLock::check( $node->attribute( 'path_string' ) )   false, or the expContentJob holding a lock
 *   expContentJobLock::checkNodes( array( 42, 43 ) )                  the same for node ids
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentJobLock
{
    const SUBTREE = 'subtree';
    const NODE = 'node';

    /** The states in which a job holds its locks */
    public static $holdingStates = array( 'queued', 'running', 'failed' );

    /**
     * Whether two locks overlap.
     *
     * @param array $a array( 'path' => '/1/2/42/', 'mode' => 'subtree'|'node' )
     * @param array $b
     * @return bool
     */
    public static function overlaps( array $a, array $b )
    {
        $pa = self::normalizePath( $a['path'] );
        $pb = self::normalizePath( $b['path'] );
        if ( $pa === '' || $pb === '' )
            return false;
        $ma = isset( $a['mode'] ) ? $a['mode'] : self::SUBTREE;
        $mb = isset( $b['mode'] ) ? $b['mode'] : self::SUBTREE;
        $aUnderB = strpos( $pa, $pb ) === 0;   // b is a or an ancestor of a
        $bUnderA = strpos( $pb, $pa ) === 0;
        if ( $ma === self::SUBTREE && $mb === self::SUBTREE )
            return $aUnderB || $bUnderA;
        if ( $ma === self::SUBTREE && $mb === self::NODE )
            return $bUnderA;
        if ( $ma === self::NODE && $mb === self::SUBTREE )
            return $aUnderB;
        return false;
    }

    /**
     * '/1/2/42/' form; '' for anything that is not a path string.
     *
     * @param string $path
     * @return string
     */
    public static function normalizePath( $path )
    {
        $path = trim( (string) $path );
        if ( !preg_match( '#^/?[0-9]+(/[0-9]+)*/?$#', $path ) )
            return '';
        return '/' . trim( $path, '/' ) . '/';
    }

    /**
     * The job holding a lock that overlaps an operation on the subtree at $pathString.
     *
     * @param string $pathString
     * @param string|null $ignoreJobID a job whose own locks do not count (the worker of that job)
     * @return expContentJob|false
     */
    public static function check( $pathString, $ignoreJobID = null )
    {
        $probe = array( 'path' => $pathString, 'mode' => self::SUBTREE );
        foreach ( self::active() as $jobID => $locks )
        {
            if ( $jobID === $ignoreJobID )
                continue;
            foreach ( $locks as $lock )
            {
                if ( self::overlaps( $probe, $lock ) )
                {
                    $job = expContentJob::fetch( $jobID );
                    if ( $job )
                        return $job;
                }
            }
        }
        return false;
    }

    /**
     * check() for node ids.
     *
     * @param int[] $nodeIDs
     * @param string|null $ignoreJobID
     * @return expContentJob|false
     */
    public static function checkNodes( array $nodeIDs, $ignoreJobID = null )
    {
        foreach ( $nodeIDs as $nodeID )
        {
            $row = eZContentObjectTreeNode::fetch( (int) $nodeID, false, false );
            if ( is_array( $row ) && isset( $row['path_string'] ) )
            {
                $job = self::check( $row['path_string'], $ignoreJobID );
                if ( $job )
                    return $job;
            }
        }
        return false;
    }

    /**
     * The locks of the active jobs: array( jobID => array( lock, ... ) ).
     *
     * @return array
     */
    public static function active()
    {
        $all = self::readIndex();
        $active = array();
        foreach ( $all as $jobID => $locks )
        {
            $data = expContentJobStore::read( (string) $jobID );
            // a job no worker started within QueuedTimeout fails and gives its locks back (never blocks for ever)
            if ( $data && expContentJob::expireUnstarted( $data ) )
                continue;
            if ( $data && in_array( $data['state'], self::$holdingStates, true ) )
                $active[(string) $jobID] = $locks;
        }
        return $active;
    }

    /**
     * Takes the locks for a job, or refuses with the job that holds an overlapping one. $onLocked runs while
     * the lock index is still locked (create() writes the job file there), so no other job can slip in between.
     *
     * @param string $jobID
     * @param array $locks
     * @param callable|null $onLocked
     * @throws expContentJobException
     */
    public static function acquire( $jobID, array $locks, $onLocked = null )
    {
        expContentJobStore::withLock( 'locks', function () use ( $jobID, $locks, $onLocked ) {
            $index = self::activeIndex();
            foreach ( $index as $otherID => $otherLocks )
            {
                if ( $otherID === $jobID )
                    continue;
                foreach ( $otherLocks as $other )
                    foreach ( $locks as $lock )
                        if ( self::overlaps( $lock, $other ) )
                            throw new expContentJobException( self::refusal( $otherID ), 409 );
            }
            $index[$jobID] = array_values( $locks );
            if ( $onLocked )
                call_user_func( $onLocked );
            self::writeIndex( $index );
        } );
    }

    /**
     * Adds a lock to a job that already holds locks (the copy, once its new subtree exists).
     *
     * @param string $jobID
     * @param array $lock
     */
    public static function add( $jobID, array $lock )
    {
        expContentJobStore::withLock( 'locks', function () use ( $jobID, $lock ) {
            $index = self::activeIndex();
            $locks = isset( $index[$jobID] ) ? $index[$jobID] : array();
            foreach ( $locks as $existing )
                if ( $existing['path'] === $lock['path'] && $existing['mode'] === $lock['mode'] )
                    return;
            $locks[] = $lock;
            $index[$jobID] = $locks;
            self::writeIndex( $index );
        } );
    }

    /**
     * Gives back every lock of a job.
     *
     * @param string $jobID
     */
    public static function release( $jobID )
    {
        expContentJobStore::withLock( 'locks', function () use ( $jobID ) {
            $index = self::activeIndex();
            unset( $index[$jobID] );
            self::writeIndex( $index );
        } );
    }

    /**
     * The locks a job holds now.
     *
     * @param string $jobID
     * @return array
     */
    public static function locksOf( $jobID )
    {
        $index = self::readIndex();
        return isset( $index[$jobID] ) ? $index[$jobID] : array();
    }

    /**
     * The message for a refused operation.
     *
     * @param string $jobID
     * @return string
     */
    public static function refusal( $jobID )
    {
        $data = expContentJobStore::read( $jobID );
        $verbs = array( 'remove' => 'removed', 'copy' => 'copied', 'move' => 'moved', 'hide' => 'hidden', 'reveal' => 'revealed',
                        'section' => 'given a section', 'state' => 'given a state', 'addlocation' => 'given locations',
                        'removelocation' => 'losing locations' );
        $what = $data && isset( $verbs[$data['type']] ) ? $verbs[$data['type']] : 'changed';
        $state = $data ? $data['state'] : 'queued';
        return self::tr( 'This part of the content tree is being %1 by a background job (%2, %3). Try again when the job is done, or cancel it.',
                         array( $what, $jobID, $state ) );
    }

    /**
     * Translated text when the kernel's i18n is there (tests run without it).
     */
    protected static function tr( $text, array $args )
    {
        // settings handed in (tests): no INI, so no translation either
        if ( !is_array( expContentJob::$settings ) && class_exists( 'ezpI18n' ) )
            return ezpI18n::tr( 'kernel/contentjob', $text, null, $args );
        foreach ( $args as $i => $arg )
            $text = str_replace( '%' . ( $i + 1 ), $arg, $text );
        return $text;
    }

    /** The index as stored. */
    protected static function readIndex()
    {
        $file = expContentJobStore::directory() . '/locks.json';
        if ( !is_file( $file ) )
            return array();
        $index = json_decode( (string) @file_get_contents( $file ), true );
        return is_array( $index ) ? $index : array();
    }

    /** The index without the entries of jobs that no longer hold locks (call under the 'locks' lock). */
    protected static function activeIndex()
    {
        $index = array();
        foreach ( self::readIndex() as $jobID => $locks )
        {
            $data = expContentJobStore::read( (string) $jobID );
            // under the 'locks' lock: an unstarted job past QueuedTimeout no longer counts (expired on the next read)
            if ( $data && expContentJob::unstartedTooLong( $data ) )
                continue;
            if ( $data && in_array( $data['state'], self::$holdingStates, true ) )
                $index[(string) $jobID] = $locks;
        }
        return $index;
    }

    protected static function writeIndex( array $index )
    {
        expContentJobStore::writeJSON( expContentJobStore::directory() . '/locks.json', (object) $index );
    }
}
