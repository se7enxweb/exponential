<?php
/**
 * File containing the Exponential\Service\TrashRecord class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

namespace Exponential\Service;

/**
 * The file where who moved an object to the trash, and from where, was kept before the trash rows had the columns
 * trashed_by and trashed_via. The kernel writes the columns now; this class reads what the file still holds, for
 * the trash view and for update/common/scripts/6.0/movetrashrecords.php, which copies it into the columns:
 *
 *   <VarDir>/trash/trashed.json   { "<object id>": { node_id, trashed, user_id, user_name, via, recorded } }
 *
 * - No longer written by the kernel; record() is kept for code that called it.
 * - Forgotten by eZContentObjectTrashNode::purgeForObject(), which runs on purge and on restore, so the
 *   file shrinks as the old trash goes.
 * - An entry counts only while it matches the trash row (same node id and trashed time); anything trashed
 *   before the recording started, or moved to the trash past storeToTrash(), has no entry and is "unknown".
 * - Written atomically (a temporary file renamed over the old one) under an exclusive lock; when root writes
 *   it (a CLI script), the files are given to the owner of the var directory, so the web server can write next.
 * - A failure to record never stops the trash move: errors go to the debug log.
 *
 * Guide: doc/bc/6.0/trash.md
 */
class TrashRecord
{
    /**
     * @return string the JSON file
     */
    public static function file()
    {
        return \eZSys::varDirectory() . '/trash/trashed.json';
    }

    /**
     * Records that the current user moved this trash node's object to the trash.
     *
     * @deprecated the trash row carries it in trashed_by and trashed_via
     *
     * @param \eZContentObjectTrashNode $trashNode the row just stored
     * @return bool
     */
    public static function record( $trashNode )
    {
        $objectID = (int)$trashNode->attribute( 'contentobject_id' );
        if ( $objectID <= 0 )
            return false;

        $user = \eZUser::currentUser();
        $userID = $user ? (int)$user->attribute( 'contentobject_id' ) : 0;
        $userName = '';
        if ( $user )
        {
            $userObject = $user->attribute( 'contentobject' );
            $userName = $userObject ? (string)$userObject->attribute( 'name' ) : (string)$user->attribute( 'login' );
        }

        $entry = array( 'node_id' => (int)$trashNode->attribute( 'node_id' ),
                        'trashed' => (int)$trashNode->attribute( 'trashed' ),
                        'user_id' => $userID,
                        'user_name' => $userName,
                        'via' => self::via(),
                        'recorded' => time() );

        return self::update( function ( array $map ) use ( $objectID, $entry )
        {
            $map[(string)$objectID] = $entry;
            return $map;
        } );
    }

    /**
     * Forgets the entries of these objects (purged, or restored).
     *
     * @param int|int[] $objectIDs
     * @return bool
     */
    public static function forget( $objectIDs )
    {
        $objectIDs = array_map( 'intval', (array)$objectIDs );
        // every purge passes here: write only when one of them has an entry
        $map = self::all();
        $known = false;
        foreach ( $objectIDs as $id )
            $known = $known || isset( $map[(string)$id] );
        if ( !$known )
            return true;
        return self::update( function ( array $map ) use ( $objectIDs )
        {
            foreach ( $objectIDs as $id )
                unset( $map[(string)$id] );
            return $map;
        } );
    }

    /**
     * All entries, keyed by object id.
     *
     * @return array
     */
    public static function all()
    {
        $file = self::file();
        if ( !is_file( $file ) )
            return array();
        $json = @file_get_contents( $file );
        $map = $json ? json_decode( $json, true ) : null;
        return is_array( $map ) ? $map : array();
    }

    /**
     * The entry for a trash row, or null when none was recorded for this very trash move.
     *
     * @param array $map from all()
     * @param int $objectID
     * @param int $nodeID the trash row's node id
     * @param int $trashed the trash row's trashed time
     * @return array|null
     */
    public static function entryFor( array $map, $objectID, $nodeID, $trashed )
    {
        if ( !isset( $map[(string)(int)$objectID] ) )
            return null;
        $entry = $map[(string)(int)$objectID];
        if ( !is_array( $entry ) || (int)$entry['node_id'] !== (int)$nodeID || (int)$entry['trashed'] !== (int)$trashed )
            return null;
        return $entry;
    }

    /**
     * Copies the entries of the file into the trash rows they belong to (same object, node id and trashed time)
     * whose trashed_by is still 0. Rows that have a trashed_by keep it.
     *
     * @param \eZDBInterface $db
     * @param bool $dryRun count only, change nothing
     * @param int[]|null $objectIDs only the entries of these objects; null: all
     * @return array( 'entries' => int in the file, 'moved' => int rows given a trashed_by,
     *                'kept' => int rows that had one already, 'orphans' => int entries without a matching row )
     */
    public static function moveToColumns( $db, $dryRun = false, $objectIDs = null )
    {
        $stats = array( 'entries' => 0, 'moved' => 0, 'kept' => 0, 'orphans' => 0 );
        $map = self::all();
        if ( is_array( $objectIDs ) )
            $map = array_intersect_key( $map, array_flip( array_map( 'strval', array_map( 'intval', $objectIDs ) ) ) );
        $stats['entries'] = count( $map );
        if ( !$map )
            return $stats;
        $rows = $db->arrayQuery( 'SELECT node_id, contentobject_id, trashed, trashed_by FROM ezcontentobject_trash' );
        $matched = array();
        if ( !$dryRun )
            $db->begin();
        foreach ( is_array( $rows ) ? $rows : array() as $row )
        {
            $entry = self::entryFor( $map, $row['contentobject_id'], $row['node_id'], $row['trashed'] );
            if ( !$entry )
                continue;
            $matched[(string)(int)$row['contentobject_id']] = true;
            if ( (int)$row['trashed_by'] > 0 )
            {
                $stats['kept']++;
                continue;
            }
            $userID = isset( $entry['user_id'] ) ? (int)$entry['user_id'] : 0;
            if ( $userID <= 0 )
                continue;
            $via = isset( $entry['via'] ) ? (string)$entry['via'] : '';
            $via = function_exists( 'mb_substr' ) ? mb_substr( $via, 0, 100, 'UTF-8' ) : substr( $via, 0, 100 );
            if ( !$dryRun )
                $db->query( 'UPDATE ezcontentobject_trash SET trashed_by = ' . $userID . ", trashed_via = '" . $db->escapeString( $via ) . "' "
                          . 'WHERE node_id = ' . (int)$row['node_id'] . ' AND trashed_by = 0' );
            $stats['moved']++;
        }
        if ( !$dryRun )
            $db->commit();
        $stats['orphans'] = count( array_diff_key( $map, $matched ) );
        return $stats;
    }

    /**
     * Removes the file. The lock file stays: a process started before the update may still hold its lock.
     *
     * @return bool there is no file any more
     */
    public static function removeFile()
    {
        $file = self::file();
        return !is_file( $file ) || @unlink( $file );
    }

    /**
     * Whether the trash table has the columns trashed_by and trashed_via (the database update has run).
     *
     * @param \eZDBInterface $db
     * @return bool
     */
    public static function columnsExist( $db )
    {
        $rows = $db->arrayQuery( 'SELECT * FROM ezcontentobject_trash', array( 'limit' => 1 ) );
        if ( !is_array( $rows ) )
            return false;
        if ( $rows )
            return array_key_exists( 'trashed_by', $rows[0] ) && array_key_exists( 'trashed_via', $rows[0] );
        // an empty trash: ask for the columns themselves
        $probe = $db->arrayQuery( 'SELECT trashed_by, trashed_via FROM ezcontentobject_trash', array( 'limit' => 1 ) );
        return is_array( $probe );
    }

    /**
     * Where the trash move came from: "web <siteaccess>" or "cli <script>".
     *
     * @return string
     */
    public static function via()
    {
        return \eZContentObjectTrashNode::currentVia();
    }

    /**
     * Reads, changes and writes the map under an exclusive lock, atomically.
     *
     * @param callable $change array -> array
     * @return bool
     */
    protected static function update( $change )
    {
        try
        {
            $file = self::file();
            $dir = dirname( $file );
            if ( !is_dir( $dir ) )
            {
                @mkdir( $dir, \eZDir::dirMode( 0775 ), true );
                self::giveToSiteUser( $dir );
            }
            $lock = @fopen( $file . '.lock', 'c' );
            if ( !$lock )
            {
                \eZDebug::writeError( "Cannot open $file.lock", __METHOD__ );
                return false;
            }
            self::giveToSiteUser( $file . '.lock' );
            flock( $lock, LOCK_EX );
            try
            {
                $map = call_user_func( $change, self::all() );
                $tmp = $file . '.' . getmypid() . '.' . mt_rand() . '.tmp';
                $ok = @file_put_contents( $tmp, json_encode( $map, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ) !== false;
                if ( $ok )
                {
                    @chmod( $tmp, \eZFile::fileMode( 0664 ) );
                    self::giveToSiteUser( $tmp );
                    $ok = @rename( $tmp, $file );
                }
                if ( !$ok )
                    \eZDebug::writeError( "Cannot write $file", __METHOD__ );
                return $ok;
            }
            finally
            {
                flock( $lock, LOCK_UN );
                fclose( $lock );
            }
        }
        catch ( \Throwable $e )
        {
            \eZDebug::writeError( $e->getMessage(), __METHOD__ );
            return false;
        }
    }

    /**
     * Running as root (a CLI script): give the file to the owner of the var directory.
     *
     * @param string $path
     */
    protected static function giveToSiteUser( $path )
    {
        if ( !function_exists( 'posix_geteuid' ) || posix_geteuid() !== 0 || !file_exists( $path ) )
            return;
        $varDir = \eZSys::varDirectory();
        $owner = @fileowner( $varDir );
        $group = @filegroup( $varDir );
        if ( $owner !== false && $owner !== 0 )
            @chown( $path, $owner );
        if ( $group !== false && $group !== 0 )
            @chgrp( $path, $group );
    }
}
