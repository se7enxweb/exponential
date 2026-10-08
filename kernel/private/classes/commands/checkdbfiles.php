<?php
/**
 * The code of bin/php/checkdbfiles.php, moved into a class (#207 stage 1). The file bin/php/checkdbfiles.php is one call to it.
 * @description Verify the database update files against the upgrade path; reports missing or stray files
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of bin/php/checkdbfiles.php:
 *
 *
 * File containing the checkdbfiles.php script.
 *
 * @deprecated and unmaintained since 5.0
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace
{


function handleVersionList( $basePath, $subdir,
                            &$fileList, &$missingFileList, &$conflictFileList, &$scannedDirs,
                            $useInfoFiles, $versionList )
{
    $updatePath = $basePath . $subdir;
    if ( is_dir( $updatePath ) )
    {
        if ( !in_array( $updatePath, $scannedDirs ) )
        {
            $dh = opendir( $updatePath );
            if ( $dh )
            {
                while ( ( $file = readdir( $dh ) ) !== false )
                {
                    if ( $file == '.' or
                         $file == '..' or
                         substr( $file, strlen( $file ) - 1, 1 ) == '~' or
                         is_dir( $updatePath . '/' . $file ) )
                        continue;
                    $fileList[] = $updatePath . '/' . $file;
                }
                closedir( $dh );
            }
            $fileList = array_unique( $fileList );
            $scannedDirs[] = $updatePath;
        }
    }

    foreach ( $versionList as $versionEntry )
    {
        $from = $versionEntry[0];
        $to = $versionEntry[1];
        $dir = $updatePath;
        // a third element names a file that does not follow dbupdate-<from>-to-<to>.sql (cluster files, the 6.0 files)
        $name = isset( $versionEntry[2] ) ? $versionEntry[2] : 'dbupdate-' . $from . '-to-' . $to . '.sql';
        $file = $dir . '/' . $name;
        $fileList = array_diff( $fileList, array( $file ) );
        if ( !file_exists( $file ) )
        {
            $missingFileList[] = $file;
        }
        if ( $useInfoFiles )
        {
            $infoFile = $dir . '/' . preg_replace( '/\.sql$/', '', $name ) . '.info';
            $fileList = array_diff( $fileList, array( $infoFile ) );
            if ( !file_exists( $infoFile ) )
            {
                $missingFileList[] = $infoFile;
            }
        }
    }
}

function handleExportVersionList( $basePath, $exportBasePath, $subdir,
                                  &$fileList, &$missingFileList, &$exportMissingFileList, &$conflictFileList, &$scannedDirs,
                                  $useInfoFiles, $versionList )
{
    $updatePath = $basePath . $subdir;
    $exportUpdatePath = $exportBasePath . $subdir;
    foreach ( $versionList as $versionEntry )
    {
        $from = $versionEntry[0];
        $to = $versionEntry[1];
        $name = isset( $versionEntry[2] ) ? $versionEntry[2] : 'dbupdate-' . $from . '-to-' . $to . '.sql';
        $file = $updatePath . '/' . $name;
        $exportFile = $exportUpdatePath . '/' . $name;
        if ( file_exists( $file ) and file_exists( $exportFile ) )
        {
            $srcMD5 = md5_file( $file );
            $dstMD5 = md5_file( $exportFile );
            // If the MD5s differ we flag it as a conflict
            if ( strcmp( $srcMD5, $dstMD5 ) != 0 )
            {
                $conflictFileList[] = $file;
            }
        }
        else
        {
            $exportMissingFileList[] = $file;
        }

        if ( $useInfoFiles )
        {
            $infoFile = $updatePath . '/dbupdate-' . $from . '-to-' . $to . '.info';
            $exportInfoFile = $exportUpdatePath . '/dbupdate-' . $from . '-to-' . $to . '.sql';
            if ( file_exists( $infoFile ) and file_exists( $exportInfoFile ) )
            {
                $srcMD5 = md5_file( $infoFile );
                $dstMD5 = md5_file( $exportInfoFile );
                // If the MD5s differ we flag it as a conflict
                if ( strcmp( $srcMD5, $dstMD5 ) != 0 )
                {
                    $conflictFileList[] = $file;
                }
            }
            else
            {
                $exportMissingFileList[] = $infoFile;
            }
        }
    }
}

function exportSVNVersion( $version, $exportPath )
{
    $versionPath = $exportPath . '/' . $version;
    if ( file_exists( $versionPath ) )
        return true;

    $svn = "svn export http://svn.ez.no/svn/Exponential/stable/$version/update/database \"$versionPath\"";
    exec( $svn, $output, $code );
    if ( $code != 0 )
    {
        print( "Failed to export using:\n$svn\n" );
        return false;
    }
    return file_exists( $versionPath );
}
}

namespace Exponential\Command\Kernel
{

class Checkdbfiles extends \Exponential\Runnable\Command
{
    /**
     * The engines that have an update/database/<engine>/ directory.
     *
     * @return string[]
     */
    public static function databaseTypes()
    {
        return array( 'mysql', 'postgresql', 'sqlite' );
    }

    /**
     * The upgrade path: one entry per directory update/database/<engine>/<branch>/, in version order.
     *
     * An entry holds:
     *  - 'databases': the engines that have this directory (default: mysql and postgresql)
     *  - 'stable', 'unstable': the files of every engine, as array( from, to ) for dbupdate-<from>-to-<to>.sql or
     *    array( from, to, file name ) for a file named otherwise; 'unstable' files live in 'unstable_subdir'
     *  - 'stable_<engine>', 'unstable_<engine>': files that only that engine has (cluster files, differing names)
     *
     * @return array
     */
    public static function versionLists()
    {
        /********************************************************
        *** NOTE: The following arrays do not follow the
        ***       coding standard, the reason for this is
        ***       to make it easy to merge any changes between
        ***       the various Exponential branches.
        *********************************************************/

        $versions = array();
        $versions['4.0'] = array( 'unstable' => array( array( '3.10.0',      '4.0.0alpha1' ),
                                                       array( '4.0.0alpha1', '4.0.0alpha2' ),
                                                       array( '4.0.0alpha2', '4.0.0beta1' ),
                                                       array( '4.0.0beta1',  '4.0.0rc1' ),
                                                       array( '4.0.0rc1',    '4.0.0' ),
                                                     ),
                                  'unstable_subdir' => 'unstable',
                                  'stable' => array( array( '3.10.0', '4.0.0' ) ),
                                );
        $versions['4.1'] = array( 'unstable' => array(  array( '4.0.0',       '4.1.0alpha1' ),
                                                   array( '4.1.0alpha1', '4.1.0alpha2' ),
                                                   array( '4.1.0alpha2', '4.1.0beta1' ),
                                                   array( '4.1.0beta1', '4.1.0rc1' ),
                                                   array( '4.1.0rc1', '4.1.0' )
                                                ),
                             'unstable_subdir' => 'unstable',
                             'stable' => array( array( '4.0.0', '4.1.0' ) ) );
        $versions['4.2'] = array( 'unstable' => array( array( '4.1.0',   '4.2.0alpha1' ),
                                                  array( '4.2.0alpha1', '4.2.0beta1' ),
                                                  array( '4.2.0beta1', '4.2.0rc1' ),
                                                  array( '4.2.0rc1', '4.2.0rc2' ),
                                                  array( '4.2.0rc2', '4.2.0' ),
                                                ),
                             'unstable_subdir' => 'unstable',
                             'stable' => array( array( '4.1.0', '4.2.0' ) )
                            );

        $versions['4.3'] = array( 'unstable' => array( array( '4.2.0', '4.3.0alpha1' ),
                                                  array( '4.3.0alpha1', '4.3.0beta1' ),
                                                  array( '4.3.0beta1', '4.3.0beta2' ),
                                                  array( '4.3.0beta2', '4.3.0rc1' ),
                                                  array( '4.3.0rc1', '4.3.0' ),
                            ),
                     'unstable_subdir' => 'unstable',
                     'stable' => array( array( '4.2.0', '4.3.0' ) ),
                     // the database cluster (DFS) tables, MySQL only
                     'unstable_mysql' => array( array( '4.2.0', '4.3.0alpha1', 'dbupdate-cluster-4.2.0-to-4.3.0alpha1.sql' ) ),
                     'stable_mysql' => array( array( '4.2.0', '4.3.0', 'dbupdate-cluster-4.2.0-to-4.3.0.sql' ) ),
                   );

        // 4.4.0alpha2 changed nothing in the database: no file from 4.4.0alpha1 to 4.4.0alpha2 was ever released
        $versions['4.4'] = array( 'unstable' => array( array( '4.3.0', '4.4.0alpha1' ),
                                                 array( '4.4.0alpha2', '4.4.0alpha3' ),
                                                 array( '4.4.0alpha3', '4.4.0alpha4' ),
                                                 array( '4.4.0alpha4', '4.4.0alpha5' ),
                                                 array( '4.4.0alpha5', '4.4.0beta1' ),
                                                 array( '4.4.0beta1', '4.4.0beta2' ),
                                                 array( '4.4.0beta2', '4.4.0beta3' ),
                                                 array( '4.4.0beta3', '4.4.0' ),

                            ),
                     'unstable_subdir' => 'unstable',
                     'stable' => array( array( '4.3.0', '4.4.0' ) ),
                   );

        $versions['4.5'] = array( 'unstable' => array( array( '4.4.0', '4.5.0alpha1' ),
                                                  array( '4.5.0alpha1', '4.5.0beta1' ),
                                                  array( '4.5.0beta1', '4.5.0beta2' ),
                                                  array( '4.5.0beta2', '4.5.0' ),
                            ),
                     'unstable_subdir' => 'unstable',
                     'stable' => array( array( '4.4.0', '4.5.0' ) ),
                   );

        $versions['4.6'] = array( 'unstable' => array( array( '4.5.0', '4.6.0alpha1' ),
                                                  array( '4.6.0alpha1', '4.6.0beta1' ),
                                                  array( '4.6.0beta1', '4.6.0rc1' ),
                                                  array( '4.6.0rc1', '4.6.0' ),
                            ),
                     'unstable_subdir' => 'unstable',
                     'stable' => array( array( '4.5.0', '4.6.0' ) ),
                   );

        $versions['4.7'] = array( 'unstable' => array( array( '4.6.0', '4.7.0alpha1' ),
                                                  array( '4.7.0alpha1', '4.7.0beta1' ),
                                                  array( '4.7.0beta1', '4.7.0rc1' ),
                                                  array( '4.7.0rc1', '4.7.0' ),
                            ),
                     'unstable_subdir' => 'unstable',
                     'stable' => array( array( '4.6.0', '4.7.0' ) ),
                     'unstable_mysql' => array( array( '4.7.0beta1', '4.7.0rc1', 'dbupdate-cluster-4.7.0beta1-to-4.7.0rc1.sql' ) ),
                     'stable_mysql' => array( array( '4.6.0', '4.7.0', 'dbupdate-cluster-4.6.0-to-4.7.0.sql' ) ),
                   );

        $versions['5.0'] = array( 'unstable' => array( array( '4.7.0', '5.0.0alpha1' ),
                                                  array( '5.0.0alpha1', '5.0.0' ),
                            ),
                     'unstable_subdir' => 'unstable',
                     'stable' => array( array( '4.7.0', '5.0.0' ) ),
                   );

        // Note: DB updates are kept in base sql file regardless of state as of 5.1
        $versions['5.1'] = array( 'unstable' => array( ),
                     'unstable_subdir' => 'unstable',
                     'stable' => array( array( '5.0.0', '5.1.0' ) ),
                   );

        $versions['5.2'] = array( 'unstable' => array( ),
                     'unstable_subdir' => 'unstable',
                     'stable' => array( array( '5.1.0', '5.2.0' ) ),
                     'stable_mysql' => array( array( '5.1.0', '5.2.0', 'dbupdate-cluster-5.1.0-to-5.2.0.sql' ) ),
                   );

        $versions['5.3'] = array( 'unstable' => array( ),
                     'unstable_subdir' => 'unstable',
                     'stable' => array( array( '5.2.0', '5.3.0' ) ),
                   );

        $versions['5.4'] = array( 'unstable' => array( ),
                     'unstable_subdir' => 'unstable',
                     'stable' => array( array( '5.3.0', '5.4.0' ) ),
                     'stable_mysql' => array( array( '5.3.0', '5.4.0', 'dbupdate-cluster-5.3.0-to-5.4.0.sql' ) ),
                   );

        // The Exponential 6.0 line. Its files are named differently on each engine; SQLite starts at 6.0.
        $versions['6.0'] = array( 'databases' => array( 'mysql', 'postgresql', 'sqlite' ),
                     'unstable' => array( ),
                     'unstable_subdir' => 'unstable',
                     'stable' => array( ),
                     'stable_mysql' => array( array( '5.4.0', '6.0.0', 'dbupdate-5.4.0-6.0.0.sql' ),
                                              array( '6.0.0', '6.0.15', 'dbupdate-6.0.0-6.0.15.sql' ) ),
                     'stable_postgresql' => array( array( '5.4', '6.0' ),
                                                   array( '6.0.0', '6.0.15', 'dbupdate-6.0.0-6.0.15.sql' ) ),
                     'stable_sqlite' => array( array( '6.0.0', '6.0.15', 'dbupdate-6.0.0-6.0.15.sql' ) ),
                   );

        // The 6.12, 7.2 and 7.3 files of the upstream legacy line (2016 to 2018); not on the 6.0 path, see
        // doc/install/11-upgrading.md, section 11.3
        $versions['6.12'] = array( 'unstable' => array( ),
                     'unstable_subdir' => 'unstable',
                     'stable' => array( array( '5.4.0', '6.12.0' ) ),
                   );

        $versions['7.2'] = array( 'databases' => array( 'postgresql' ),
                     'unstable' => array( ),
                     'unstable_subdir' => 'unstable',
                     'stable' => array( array( '6.13.0', '7.2.0' ) ),
                   );

        $versions['7.3'] = array( 'unstable' => array( ),
                     'unstable_subdir' => 'unstable',
                     'stable' => array( array( '7.2.0', '7.3.0' ) ),
                   );

        return $versions;
    }

    /**
     * The engines that have the directory of a branch.
     */
    private static function branchDatabases( array $versionList )
    {
        return isset( $versionList['databases'] ) ? $versionList['databases'] : array( 'mysql', 'postgresql' );
    }

    /**
     * The 'stable' or 'unstable' files of a branch for one engine: those of every engine, then the engine's own.
     */
    private static function entriesFor( array $versionList, $state, $dbType )
    {
        if ( !isset( $versionList[$state] ) && !isset( $versionList[$state . '_' . $dbType] ) )
            return false;
        $entries = isset( $versionList[$state] ) ? $versionList[$state] : array();
        if ( isset( $versionList[$state . '_' . $dbType] ) )
            $entries = array_merge( $entries, $versionList[$state . '_' . $dbType] );
        return $entries;
    }

    /**
     * Compares the files under <root>update/database/ with the upgrade path. Reads the file system only.
     *
     * @param string $root the installation root with a trailing slash, or '' for the current directory
     * @return array 'unknown' => files not in the upgrade path, 'missing' => files of the path that do not exist
     */
    public static function check( $root = '' )
    {
        $versions = self::versionLists();
        $dbTypes = self::databaseTypes();
        $fileList = array();
        $missingFileList = array();
        $conflictFileList = array();
        $scannedDirs = array();

        foreach ( $dbTypes as $dbType )
        {
            foreach ( $versions as $branch => $versionList )
            {
                if ( !in_array( $dbType, self::branchDatabases( $versionList ), true ) )
                    continue;
                $basePath = $root . 'update/database/' . $dbType . '/' . $branch;
                $useInfoFiles = isset( $versionList['info_files'] ) ? $versionList['info_files'] : false;
                foreach ( array( 'unstable', 'stable' ) as $state )
                {
                    $entries = self::entriesFor( $versionList, $state, $dbType );
                    if ( $entries === false )
                        continue;
                    $subdir = isset( $versionList[$state . '_subdir'] ) ? '/' . $versionList[$state . '_subdir'] : false;
                    handleVersionList( $basePath, $subdir,
                                       $fileList, $missingFileList, $conflictFileList, $scannedDirs,
                                       $useInfoFiles, $entries );
                }
            }
        }

        // Directories the upgrade path does not know at all: an engine, or a branch of an engine, that is not listed
        $databaseDir = $root . 'update/database';
        foreach ( is_dir( $databaseDir ) ? scandir( $databaseDir ) : array() as $dbType )
        {
            if ( $dbType === '.' || $dbType === '..' || !is_dir( $databaseDir . '/' . $dbType ) )
                continue;
            foreach ( scandir( $databaseDir . '/' . $dbType ) as $branch )
            {
                $branchDir = $databaseDir . '/' . $dbType . '/' . $branch;
                if ( $branch === '.' || $branch === '..' || !is_dir( $branchDir ) )
                    continue;
                if ( in_array( $dbType, $dbTypes, true ) &&
                     isset( $versions[$branch] ) &&
                     in_array( $dbType, self::branchDatabases( $versions[$branch] ), true ) )
                    continue;
                $iterator = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $branchDir, \FilesystemIterator::SKIP_DOTS ) );
                $unknown = array();
                foreach ( $iterator as $file )
                {
                    if ( $file->isFile() )
                        $unknown[] = $file->getPathname();
                }
                sort( $unknown );
                $fileList = array_merge( $fileList, $unknown );
            }
        }

        return array( 'unknown' => array_values( $fileList ), 'missing' => $missingFileList );
    }

    /**
     * The export directories this process created; removeExportDirectory() removes only these.
     *
     * @var string[]
     */
    private static $createdExportDirectories = array();

    /**
     * Creates a new, empty directory for the SVN exports inside $base, named checkdbfiles-export-<pid>-<random>.
     * $base is created when it does not exist, but nothing in it is touched.
     *
     * @param string $base
     * @return string|false the new directory, or false if it could not be created
     */
    public static function createExportDirectory( $base )
    {
        $base = rtrim( $base, '/' );
        if ( $base === '' )
            $base = '.';
        if ( !is_dir( $base ) && !@mkdir( $base, \eZDir::dirMode( 0777 ), true ) && !is_dir( $base ) )
            return false;
        for ( $attempt = 0; $attempt < 10; $attempt++ )
        {
            $dir = $base . '/checkdbfiles-export-' . getmypid() . '-' . bin2hex( random_bytes( 4 ) );
            // mkdir fails if the name exists, so the directory is always one this call made
            if ( @mkdir( $dir, \eZDir::dirMode( 0700 ) ) )
            {
                self::$createdExportDirectories[] = $dir;
                return $dir;
            }
        }
        return false;
    }

    /**
     * Removes a directory createExportDirectory() made in this process, with its contents. Any other path is left
     * alone.
     *
     * @param string $dir
     * @return bool whether it was removed
     */
    public static function removeExportDirectory( $dir )
    {
        $index = array_search( $dir, self::$createdExportDirectories, true );
        if ( $index === false || !is_dir( $dir ) || is_link( $dir ) )
            return false;
        $iterator = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $dir, \FilesystemIterator::SKIP_DOTS ),
                                                    \RecursiveIteratorIterator::CHILD_FIRST );
        foreach ( $iterator as $entry )
        {
            if ( $entry->isDir() && !$entry->isLink() )
                rmdir( $entry->getPathname() );
            else
                unlink( $entry->getPathname() );
        }
        rmdir( $dir );
        unset( self::$createdExportDirectories[$index] );
        return !file_exists( $dir );
    }

    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'basePath', 'branch', 'branches', 'cli', 'conflictFileList', 'currentBranch', 'dbType', 'dbTypes', 'exportBasePath', 'exportMissingFileList', 'exportPath', 'file', 'fileList', 'lowestExportVersion', 'missingFileList', 'options', 'script', 'subdir', 'useInfoFiles', 'versionList', 'versions' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        $cli = $this->cli();
        $script = $this->script( array( 'description' => ( "Exponential DB file verifier\n\n" .
                                                                "Checks the database update files and gives a report on them.\n" .
                                                                "It will show which files are missing and which should not be present.\n" .
                                                                "\n" .
                                                                "For each file with problems it will output a status and the filepath\n" .
                                                                "The status will be one of these:\n" .
                                                                " '?' file is not defined in upgrade path\n" .
                                                                " '!' file defined in upgrade path but missing on filesystem\n" .
                                                                " 'A' file is present in working copy but not in the original stable branch\n" .
                                                                " 'C' file data conflicts with the original stable branch\n" .
                                                                "\n" .
                                                                "Example output:\n" .
                                                                "  checkdbfiles.php\n" .
                                                                "  ? update/database/mysql/3.5/dbupdate-3.5.0-to-3.5.1.sql" ),
                                             'use-session' => false,
                                             'use-modules' => false,
                                             'use-extensions' => true ) );

        $options = $this->startup( "[no-verify-branches][export-path:]",
                                        "",
                                        array( 'no-verify-branches' => "Do not verify the content of the files with previous branches (To avoid SVN usage)",
                                               'export-path' => "Directory in which a new directory for the SVN exports is made and removed again (default var/tmp)"
                                               ) );

        $dbTypes = self::databaseTypes();
        $versions = self::versionLists();
        $branches = array_keys( $versions );

        // Controls the lowest version which will be exported and verified against current data
        $lowestExportVersion = '4.3';

        $exportMissingFileList = array();
        $conflictFileList = array();

        // Check for required md5_file function
        if ( !function_exists( 'md5_file' ) )
        {
            $cli->error( "The function 'md5_file' does not exist in your PHP version" );
            $cli->error( "You must upgrade PHP to a version (4.2.0) that has this function" );
            $script->shutdown( 1 );
        }

        // The upgrade path against the files on disk: '?' and '!'
        $result = self::check( '' );
        $fileList = $result['unknown'];
        $missingFileList = $result['missing'];

        if ( !$options['no-verify-branches'] )
        {
            // A directory of our own, new and empty, inside the export path (default var/tmp): nothing that was there
            // before is ever removed
            $exportBase = $options['export-path'] ? $options['export-path'] : 'var/tmp';
            $exportPath = self::createExportDirectory( $exportBase );
            if ( $exportPath === false )
            {
                $cli->error( "Could not create a directory for the SVN exports in $exportBase" );
                $script->shutdown( 1 );
            }

            // Figure out the current branch, we do not want to export it
            $currentBranch = \ExponentialSDK::VERSION_MAJOR . '.' . \ExponentialSDK::VERSION_MINOR;
            // once an export fails the repository is out of reach: one 'C' line, not one per branch and engine
            $exportFailed = false;

            foreach ( $dbTypes as $dbType )
            {
                foreach ( $versions as $branch => $versionList )
                {
                    if ( $exportFailed ||
                         !in_array( $dbType, self::branchDatabases( $versionList ), true ) ||
                         version_compare( $branch, $lowestExportVersion ) < 0 ||
                         version_compare( $branch, $currentBranch ) >= 0 )
                        continue;
                    $basePath = 'update/database/' . $dbType . '/' . $branch;
                    $useInfoFiles = isset( $versionList['info_files'] ) ? $versionList['info_files'] : false;
                    if ( !exportSVNVersion( $branch, $exportPath ) )
                    {
                        $conflictFileList[] = $basePath;
                        $exportFailed = true;
                        continue;
                    }
                    $exportBasePath = $exportPath . '/' . $branch . '/' . $dbType . '/' . $branch;
                    foreach ( array( 'unstable', 'stable' ) as $state )
                    {
                        $entries = self::entriesFor( $versionList, $state, $dbType );
                        if ( $entries === false )
                            continue;
                        $subdir = isset( $versionList[$state . '_subdir'] ) ? '/' . $versionList[$state . '_subdir'] : false;
                        $scannedDirs = array();
                        handleExportVersionList( $basePath, $exportBasePath, $subdir,
                                                 $fileList, $missingFileList, $exportMissingFileList, $conflictFileList, $scannedDirs,
                                                 $useInfoFiles, $entries );
                    }
                }
            }
        }

        if ( count( $missingFileList ) > 0 or
             count( $exportMissingFileList ) > 0 or
             count( $fileList ) > 0 or
             count( $conflictFileList ) > 0 )
        {
            if ( count( $fileList ) > 0 )
            {
                foreach ( $fileList as $file )
                {
                    print( '? ' . $file . "\n" );
                }
            }

            if ( count( $missingFileList ) > 0 )
            {
                foreach ( $missingFileList as $file )
                {
                    print( '! ' . $file . "\n" );
                }
            }

            if ( count( $exportMissingFileList ) > 0 )
            {
                foreach ( $exportMissingFileList as $file )
                {
                    print( 'A ' . $file . "\n" );
                }
            }

            if ( count( $conflictFileList ) > 0 )
            {
                foreach ( $conflictFileList as $file )
                {
                    print( 'C ' . $file . "\n" );
                }
            }
            $script->setExitCode( 1 );
        }

        if ( !$options['no-verify-branches'] )
        {
            // Remove the directory this run made, and nothing else
            self::removeExportDirectory( $exportPath );
        }

        $script->shutdown();
    }
}

}
