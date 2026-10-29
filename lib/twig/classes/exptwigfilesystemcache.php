<?php
/**
 * Twig's file cache of compiled templates, writing with the permissions of the installation's other cache files
 * (eZFile::creationUmask(), from [FileSettings] StorageDirPermissions and StorageFilePermissions). Twig creates its
 * directories with 0777 less the process's umask: Velocity's workers run as root with umask 022, so a template they
 * compiled first left a directory that the web server's PHP-FPM user could not write, and a template compiled later
 * into it failed ("Unable to write in the cache directory").
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
class expTwigFilesystemCache extends \Twig\Cache\FilesystemCache
{
    public function write( string $key, string $content ): void
    {
        $old = umask( eZFile::creationUmask() );
        try
        {
            parent::write( $key, $content );
        }
        finally
        {
            umask( $old );
        }
    }
}
