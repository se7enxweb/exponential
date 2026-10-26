<?php
/**
 * File containing the expPackageArchiveModes class.
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

/**
 * The extract callback of a package archive (expPackageArchive::open()): every file it extracts gets 0644, every
 * directory 0755, each within the limits of the installation (eZFile::fileMode(), eZDir::dirMode(),
 * EZP_FILE_MODE_MAX/EZP_DIR_MODE_MAX).
 *
 * ezcArchive applies the mode the archive gives after the callback (archive.php, chmod( $fileName, $perms )).
 * Without the callback, measured with PHP 8.5.4: a file packed as 04755 came out set-uid, a
 * directory packed as 0777 world writable, one packed as 0000 unreadable. Owner and group stay what the archive
 * says; ezcArchive applies them only when PHP runs as root.
 */
class expPackageArchiveModes extends ezcArchiveCallback
{
    const FILE_MODE = 0644;
    const DIRECTORY_MODE = 0755;

    function createFileCallback( $fileName, &$permissions, &$userId, &$groupId )
    {
        $permissions = eZFile::fileMode( self::FILE_MODE );
    }

    function createDirectoryCallback( $dirName, &$permissions, &$userId, &$groupId )
    {
        $permissions = eZDir::dirMode( self::DIRECTORY_MODE );
    }
}

?>
