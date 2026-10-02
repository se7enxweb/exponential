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
 * Who moved an object to the trash, and from where. The kernel has no column for it, and a schema change
 * would have to reach every database engine, so it is kept in one small JSON file under the var directory:
 *
 *   <VarDir>/trash/trashed.json   { "<object id>": { node_id, trashed, user_id, user_name, via, recorded } }
 *
 * - Written by eZContentObjectTrashNode::storeToTrash(), the one place every trash move passes through
 *   (the admin's Delete, the content jobs, removeSubtrees() from scripts and cronjobs).
 * - Forgotten by eZContentObjectTrashNode::purgeForObject(), which runs on purge and on restore, so the
 *   file holds about as many entries as the trash holds objects.
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
     * Where the trash move came from: "web <siteaccess>" or "cli <script>".
     *
     * @return string
     */
    public static function via()
    {
        if ( PHP_SAPI === 'cli' && !isset( $_SERVER['REQUEST_URI'] ) )
        {
            $script = isset( $_SERVER['argv'][0] ) ? basename( (string)$_SERVER['argv'][0] ) : 'php';
            // ezexec.php runs another script: name that one
            if ( $script === 'ezexec.php' && isset( $_SERVER['argv'][1] ) )
                $script = basename( (string)$_SERVER['argv'][1] );
            return 'cli ' . $script;
        }
        $access = isset( $GLOBALS['eZCurrentAccess']['name'] ) ? $GLOBALS['eZCurrentAccess']['name'] : '';
        return trim( 'web ' . $access );
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
                @mkdir( $dir, 0775, true );
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
                    @chmod( $tmp, 0664 );
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
