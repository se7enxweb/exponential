<?php
/**
 * File containing the eZCacheTrash class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

/**
 * Removes a cache directory by renaming it out of the way first.
 *
 * Deleting a cache file by file takes time, and while it runs, requests are
 * already generating the cache again into the same tree -- so the delete can
 * take entries that were only just written. A rename is instant: the directory
 * leaves its place in one step, the next request writes into a new, empty tree,
 * and the old one is deleted where nothing else looks.
 *
 * Everything goes into one trash, .cleanup-trash in the cache directory, named
 * after the cache and when it was moved: template-compiled-20260924-151112-<id>,
 * content-..., template-block-... The id keeps two clears in the same second
 * apart. A directory outside the cache directory, like the global INI cache in
 * var/cache, goes into the trash of the directory it is in. Where a rename into
 * that trash would cross a file system -- a part of the cache linked to another
 * disk -- a trash next to the directory is used instead, which is always on
 * the same one. A directory that is itself a symbolic link is not renamed --
 * that would move the link and leave the files -- but emptied into a trash
 * inside it. Deleting never follows a link.
 *
 * Renames happen at once, deleting is collected: between begin() and end()
 * nothing is deleted, so a clear of several caches moves all of them aside
 * before it deletes any. discard() outside begin()/end() deletes straight away.
 *
 * Only for directories on the local file system; a cache kept by a clustered
 * handler other than eZFSFileHandler goes through that handler as before.
 */
class eZCacheTrash
{
    const TRASH_NAME = '.cleanup-trash';

    /** @var int How many begin() calls are waiting for their end() */
    private static $depth = 0;

    /** @var array Trash directories to empty at the outermost end(), as keys */
    private static $trashes = array();

    /**
     * Whether directories are renamed before they are deleted.
     * site.ini [FileSettings] RenameBeforeDelete.
     *
     * @return bool
     */
    static function isEnabled()
    {
        $ini = eZINI::instance();
        return !$ini->hasVariable( 'FileSettings', 'RenameBeforeDelete' )
            || $ini->variable( 'FileSettings', 'RenameBeforeDelete' ) !== 'disabled';
    }

    /**
     * Holds back deleting until the matching end().
     */
    static function begin()
    {
        ++self::$depth;
    }

    /**
     * Ends a begin(); the outermost one empties every trash collected.
     */
    static function end()
    {
        if ( self::$depth > 0 )
            --self::$depth;
        if ( self::$depth === 0 )
            self::flush();
    }

    /**
     * Removes the directory $dir: renamed aside now, deleted at the outermost
     * end(). Returns false if anything could not be moved; the caller then
     * deletes what is left the old way.
     *
     * @param string $dir
     * @return bool
     */
    static function discard( $dir )
    {
        if ( !is_dir( $dir ) )
            return true;

        self::begin();
        $trashDir = self::trashDirectoryFor( $dir );
        $name = self::entryName( $dir );
        // What an earlier, interrupted delete left there goes as well
        self::register( $trashDir . '/' . self::TRASH_NAME );

        $complete = false;
        if ( !is_link( $dir )
             && ( self::moveInto( $dir, $trashDir, $name )
                  // Across file systems a rename fails; next to it, it cannot
                  || ( $trashDir !== dirname( $dir ) && self::moveInto( $dir, dirname( $dir ), $name ) ) ) )
        {
            $complete = true;
        }
        else
        {
            $moved = 0;
            $seen = array();
            $complete = self::moveContentsAside( $dir, $moved, $seen );
        }
        self::end();
        return $complete;
    }

    /**
     * Renames everything in $dir into $dir/.cleanup-trash, one entry at a time.
     * A link is not moved but descended into. $moved counts the entries moved;
     * returns false if anything stayed.
     *
     * @param string $dir
     * @param int $moved
     * @param array $seen Real paths already handled, against loops through links
     * @return bool
     */
    static function moveContentsAside( $dir, &$moved, array &$seen )
    {
        $real = realpath( $dir );
        if ( $real === false || isset( $seen[$real] ) )
            return true;
        $seen[$real] = true;

        $entries = @scandir( $dir );
        if ( $entries === false )
            return false;

        self::register( $dir . '/' . self::TRASH_NAME );
        $complete = true;
        foreach ( $entries as $entry )
        {
            if ( $entry === '.' || $entry === '..' || $entry === self::TRASH_NAME )
                continue;

            $path = $dir . '/' . $entry;
            if ( is_link( $path ) )
            {
                if ( is_dir( $path ) && !self::moveContentsAside( $path, $moved, $seen ) )
                    $complete = false;
                continue;
            }
            if ( self::moveInto( $path, $dir, $entry . '-' . self::stamp() ) )
                ++$moved;
            else
                $complete = false;
        }
        return $complete;
    }

    /**
     * Notes a trash directory to empty, if there is one.
     *
     * @param string $trash
     */
    static function register( $trash )
    {
        if ( is_dir( $trash ) && !is_link( $trash ) )
            self::$trashes[$trash] = true;
    }

    /**
     * Empties every trash noted and returns how many entries went. The trash
     * directories themselves stay: another process may be renaming into one
     * right now, and would fail if it vanished under it. Every entry has a name
     * of its own, and anything another process deleted meanwhile is skipped.
     *
     * @return int
     */
    static function flush()
    {
        $emptied = 0;
        $trashes = array_keys( self::$trashes );
        self::$trashes = array();
        foreach ( $trashes as $trash )
        {
            $entries = @scandir( $trash );
            if ( $entries === false )
                continue;
            foreach ( $entries as $entry )
            {
                if ( $entry === '.' || $entry === '..' )
                    continue;
                self::remove( $trash . '/' . $entry );
                ++$emptied;
            }
        }
        return $emptied;
    }

    /**
     * The directory whose trash $dir goes into: the cache directory for
     * anything inside it, otherwise the directory $dir is in.
     *
     * @param string $dir
     * @return string
     */
    private static function trashDirectoryFor( $dir )
    {
        $cacheDir = rtrim( eZSys::cacheDirectory(), '/' );
        return self::isInside( $dir, $cacheDir ) ? $cacheDir : dirname( $dir );
    }

    /**
     * What $dir is called in the trash: its path below the cache directory,
     * or its own name, followed by when it was moved.
     *
     * @param string $dir
     * @return string
     */
    private static function entryName( $dir )
    {
        $cacheDir = rtrim( eZSys::cacheDirectory(), '/' );
        $name = basename( $dir );
        if ( self::isInside( $dir, $cacheDir ) )
        {
            $real = realpath( $dir );
            $relative = $real !== false && realpath( $cacheDir ) !== false && strpos( $real, realpath( $cacheDir ) . '/' ) === 0
                ? substr( $real, strlen( realpath( $cacheDir ) ) + 1 )
                : substr( rtrim( $dir, '/' ), strlen( $cacheDir ) + 1 );
            if ( $relative !== '' && $relative !== false )
                $name = str_replace( '/', '-', $relative );
        }
        return $name . '-' . self::stamp();
    }

    /**
     * When, and an id of its own: two clears in the same second must not meet.
     *
     * @return string
     */
    private static function stamp()
    {
        return date( 'Ymd-His' ) . '-' . substr( md5( uniqid( 'ezcachetrash' . getmypid(), true ) ), 0, 8 );
    }

    /**
     * Whether $path lies below $dir, by name or by real path.
     *
     * @param string $path
     * @param string $dir
     * @return bool
     */
    private static function isInside( $path, $dir )
    {
        if ( strpos( rtrim( $path, '/' ) . '/', $dir . '/' ) === 0 && rtrim( $path, '/' ) !== $dir )
            return true;
        $realPath = realpath( $path );
        $realDir = realpath( $dir );
        return $realPath !== false && $realDir !== false && strpos( $realPath, $realDir . '/' ) === 0;
    }

    /**
     * Renames $path into the trash inside $parent as $name.
     *
     * @param string $path
     * @param string $parent
     * @param string $name
     * @return bool
     */
    private static function moveInto( $path, $parent, $name )
    {
        $trash = $parent . '/' . self::TRASH_NAME;
        if ( !is_dir( $trash ) )
            eZDir::mkdir( $trash, false, true );
        if ( !@rename( $path, $trash . '/' . $name ) )
            return false;
        self::$trashes[$trash] = true;
        return true;
    }

    /**
     * Deletes $path and everything below it. A link is removed, never
     * followed: what it points at is not this cache's to delete.
     *
     * @param string $path
     * @return bool
     */
    private static function remove( $path )
    {
        if ( is_link( $path ) || !is_dir( $path ) )
            return @unlink( $path );
        $entries = @scandir( $path );
        if ( $entries !== false )
        {
            foreach ( $entries as $entry )
            {
                if ( $entry !== '.' && $entry !== '..' )
                    self::remove( $path . '/' . $entry );
            }
        }
        return @rmdir( $path );
    }
}

?>
