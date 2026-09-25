<?php
/**
 * File containing the ezjscCacheManager class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

class ezjscCacheManager
{
    /**
     * Recursively expires the ezjscore public cache folder
     * @param array $cacheItem
     */
    public static function clearCache( array $cacheItem )
    {
        $dirList = array( 'javascript', 'stylesheets' );
        $path = eZSys::cacheDirectory() . '/' . $cacheItem['path'];
        $fileHandler = eZClusterFileHandler::instance();
        // On the local file system renamed aside first, like the kernel's caches;
        // site.ini [FileSettings] RenameBeforeDelete=disabled deletes as before
        if ( $fileHandler instanceof eZFSFileHandler && eZCacheTrash::isEnabled() )
        {
            foreach ( $dirList as $dir )
            {
                if ( is_dir( $path . '/' . $dir ) )
                    eZCache::removeDirectory( $path . '/' . $dir );
            }
            return;
        }
        $fileHandler->fileDeleteByDirList( $dirList, $path, '' );
    }
}
