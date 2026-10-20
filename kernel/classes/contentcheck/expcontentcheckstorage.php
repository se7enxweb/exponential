<?php
/**
 * File containing the expContentCheckStorage class.
 *
 * The storage check (doc/en/us/features/6.0/content-checks.md, "Storage files"): the files below var/<site>/storage that no
 * content uses (orphan files), and the file-system problems of the files content does use: a wrong owner or mode,
 * an empty file, and a name or path that does not match what the database says.
 *
 * A file counts as used when
 *   - ezimagefile lists its path (every image and alias file of every version);
 *   - the image XML of the attribute named by its directory (<attribute id>-<version>-<language>) names it;
 *   - ezbinaryfile or ezmedia lists its name (files and media are stored as original/<type>/<name>);
 *   - the reference index of the content checks found its path in a field, a layout, a setting or a template.
 * Every version and every object state counts (the trash and old versions too): an image of an old version is
 * still shown in the history and restored with it. A file younger than MinAge is never an orphan: an upload writes
 * the file before its row.
 *
 * The scan walks the directories of ScanDirectoryList[] in batches (scanStep()), so a large storage tree is read
 * over several requests or by the CLI in one go; the state and the result of the last scan are kept in the cache
 * directory (lastScan()). With a cluster file handler other than the file system the files are read from its table
 * (ezdfsfile, ezdbfile), the owner and mode are not checked and nothing can be moved: report only.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentCheckStorage
{
    /** The format of the scan state; another one is started again */
    const FORMAT = 1;

    /** The scan state, in the cache directory */
    const STATE_FILE = 'contentcheck/storage-scan.json';

    /** The phases of a scan, in order */
    const PHASES = array( 'files', 'images', 'binaries', 'done' );

    /** The kinds of problem of a used file */
    const PROBLEMS = array( 'owner', 'mode', 'empty', 'name', 'path', 'type' );

    /** @var array|null state for tests (no file) */
    protected static $testState = null;

    /** @var string|null a storage directory for tests */
    protected static $testStorage = null;

    // ------------------------------------------------------------------ settings

    /**
     * The storage directory, relative to the installation (var/<site>/storage).
     *
     * @return string
     */
    public static function storageDirectory()
    {
        if ( self::$testStorage !== null )
            return self::$testStorage;
        return rtrim( (string)eZSys::storageDirectory(), '/' );
    }

    /**
     * @param string|null $directory a storage directory for tests; null goes back to the real one
     */
    public static function setTestStorage( $directory )
    {
        self::$testStorage = $directory === null ? null : rtrim( (string)$directory, '/' );
    }

    /**
     * The directories below the storage that are scanned, relative to it.
     *
     * @return string[]
     */
    public static function scanDirectories()
    {
        $list = expContentCheck::setting( 'StorageSettings', 'ScanDirectoryList', array( 'images', 'images-versioned', 'original' ) );
        $clean = array();
        foreach ( is_array( $list ) ? $list : array() as $dir )
        {
            $dir = self::cleanRelative( $dir );
            if ( $dir !== null && !in_array( $dir, $clean, true ) )
                $clean[] = $dir;
        }
        return $clean;
    }

    /**
     * The paths below the storage that are never listed (prefixes, relative to it): caches other code keeps there.
     *
     * @return string[]
     */
    public static function ignoredPaths()
    {
        $list = expContentCheck::setting( 'StorageSettings', 'IgnorePathList', array( 'images/_video-thumbnails' ) );
        $clean = array();
        foreach ( is_array( $list ) ? $list : array() as $path )
        {
            $path = self::cleanRelative( $path );
            if ( $path !== null )
                $clean[] = $path;
        }
        return $clean;
    }

    /**
     * A path relative to the storage: no leading or trailing slash, no "." or ".." part, no control character.
     *
     * @param mixed $path
     * @return string|null
     */
    public static function cleanRelative( $path )
    {
        if ( !is_string( $path ) )
            return null;
        $path = trim( str_replace( '\\', '/', $path ), '/ ' );
        if ( $path === '' || strlen( $path ) > 1024 || preg_match( '/[\x00-\x1f\x7f]/', $path ) )
            return null;
        foreach ( explode( '/', $path ) as $part )
        {
            if ( $part === '' || $part === '.' || $part === '..' )
                return null;
        }
        return $path;
    }

    /**
     * Whether a path relative to the storage lies below one of the ignored prefixes.
     *
     * @param string $relative
     * @param string[]|null $ignored
     * @return bool
     */
    public static function isIgnored( $relative, $ignored = null )
    {
        foreach ( $ignored === null ? self::ignoredPaths() : $ignored as $prefix )
        {
            if ( $relative === $prefix || strncmp( $relative, $prefix . '/', strlen( $prefix ) + 1 ) === 0 )
                return true;
        }
        return false;
    }

    /**
     * Files read in one step of the scan.
     *
     * @return int
     */
    public static function batchSize()
    {
        return expContentCheck::intSetting( 'StorageSettings', 'ScanBatchSize', 2000, 50, 50000 );
    }

    /**
     * Orphans and problems kept in the result of a scan (the totals count on).
     *
     * @return int
     */
    public static function maxListed()
    {
        return expContentCheck::intSetting( 'StorageSettings', 'MaxListed', 5000, 100, 100000 );
    }

    /**
     * A file younger than this (seconds) is never an orphan.
     *
     * @return int
     */
    public static function minAge()
    {
        return expContentCheck::intSetting( 'StorageSettings', 'MinAge', 86400, 0, 31536000 );
    }

    /**
     * The mode every used file should have at least: StorageSettings FileMode, else site.ini [FileSettings]
     * StorageFilePermissions, limited by eZFile::fileMode().
     *
     * @return int
     */
    public static function expectedMode()
    {
        $mode = (string)expContentCheck::setting( 'StorageSettings', 'FileMode', '' );
        if ( $mode === '' )
        {
            $ini = eZINI::instance();
            $mode = $ini->hasVariable( 'FileSettings', 'StorageFilePermissions' ) ? (string)$ini->variable( 'FileSettings', 'StorageFilePermissions' ) : '0666';
        }
        // never wider than the installation's limit (EZP_FILE_MODE_MAX): a file at that limit is not a problem
        return eZFile::fileMode( preg_match( '/^0?[0-7]{3}$/', $mode ) ? octdec( $mode ) & 0777 : 0666 );
    }

    /**
     * The owner every used file should have: StorageSettings ExpectedOwner (a user name or id), else the owner of
     * the storage directory. null when it cannot be known (no such user, a cluster).
     *
     * @return int|null user id
     */
    public static function expectedOwner()
    {
        $owner = trim( (string)expContentCheck::setting( 'StorageSettings', 'ExpectedOwner', '' ) );
        if ( $owner === '' )
        {
            $dir = self::storageDirectory();
            $uid = is_dir( $dir ) ? @fileowner( $dir ) : false;
            return $uid === false ? null : (int)$uid;
        }
        if ( preg_match( '/^[0-9]{1,10}$/', $owner ) )
            return (int)$owner;
        if ( function_exists( 'posix_getpwnam' ) )
        {
            $info = @posix_getpwnam( $owner );
            if ( is_array( $info ) && isset( $info['uid'] ) )
                return (int)$info['uid'];
        }
        return null;
    }

    /**
     * The name of a user id, or the id.
     *
     * @param int|null $uid
     * @return string
     */
    public static function userName( $uid )
    {
        if ( $uid === null )
            return '';
        if ( function_exists( 'posix_getpwuid' ) )
        {
            $info = @posix_getpwuid( (int)$uid );
            if ( is_array( $info ) && isset( $info['name'] ) )
                return (string)$info['name'];
        }
        return (string)(int)$uid;
    }

    /**
     * The cluster file handler, when it is not the file system: then the files are read from its table and nothing
     * can be moved or fixed.
     *
     * @return string '' for the file system, else the table the files are listed in ('' too when it is not known)
     */
    public static function clusterTable()
    {
        $ini = eZINI::instance( 'file.ini' );
        $handler = $ini->hasVariable( 'ClusteringSettings', 'FileHandler' ) ? (string)$ini->variable( 'ClusteringSettings', 'FileHandler' ) : 'eZFSFileHandler';
        $tables = array( 'ezdfsfilehandler' => 'ezdfsfile', 'ezdbfilehandler' => 'ezdbfile' );
        $key = strtolower( $handler );
        return isset( $tables[$key] ) ? $tables[$key] : '';
    }

    /**
     * Whether files can be moved and fixed here (the file system, not a cluster).
     *
     * @return bool
     */
    public static function canChangeFiles()
    {
        return self::clusterTable() === '';
    }

    // ------------------------------------------------------------------ judging a file (no database)

    /**
     * The problems of a used file from its stat: owner, mode, empty.
     *
     * @param array $stat array( 'size' => int, 'uid' => int, 'mode' => int )
     * @param int|null $expectedOwner
     * @param int $expectedMode
     * @return string[] owner | mode | empty
     */
    public static function fileProblems( array $stat, $expectedOwner, $expectedMode )
    {
        $problems = array();
        if ( $expectedOwner !== null && isset( $stat['uid'] ) && (int)$stat['uid'] !== (int)$expectedOwner )
            $problems[] = 'owner';
        if ( isset( $stat['mode'] ) && ( (int)$stat['mode'] & (int)$expectedMode ) !== (int)$expectedMode )
            $problems[] = 'mode';
        if ( isset( $stat['size'] ) && (int)$stat['size'] === 0 )
            $problems[] = 'empty';
        return $problems;
    }

    /**
     * The attribute id named by the directory of an image file: images/.../<attribute id>-<version>-<language>/<file>.
     *
     * @param string $relative path relative to the storage
     * @return array|null array( attribute id, version )
     */
    public static function imageAttributeOf( $relative )
    {
        $dir = basename( dirname( (string)$relative ) );
        if ( preg_match( '/^([0-9]{1,10})-([0-9]{1,10})(-[A-Za-z]{2,3}-[A-Za-z]{2,4})?$/', $dir, $m ) )
            return array( (int)$m[1], (int)$m[2] );
        return null;
    }

    /**
     * Whether an image XML names a file: as the url, or as dirpath plus filename, of the original or an alias.
     *
     * @param string $xml the attribute's data_text
     * @param string $path the file's path from the installation (var/<site>/storage/images/...)
     * @return bool
     */
    public static function imageXmlNames( $xml, $path )
    {
        $xml = (string)$xml;
        if ( $xml === '' )
            return false;
        foreach ( array( $path, htmlspecialchars( $path, ENT_QUOTES | ENT_XML1 ) ) as $needle )
        {
            if ( strpos( $xml, '"' . $needle . '"' ) !== false )
                return true;
        }
        foreach ( self::imageXmlFiles( $xml ) as $file )
        {
            if ( $file['url'] === $path || ( $file['dirpath'] !== '' && $file['dirpath'] . '/' . $file['filename'] === $path ) )
                return true;
        }
        return false;
    }

    /**
     * The files an image XML names: the original and the aliases, with url, dirpath and filename.
     *
     * @param string $xml
     * @return array list of array( 'name' => original|<alias>, 'url', 'dirpath', 'filename' )
     */
    public static function imageXmlFiles( $xml )
    {
        $files = array();
        if ( !preg_match_all( '#<(original|alias)\b([^>]*)/?>#', (string)$xml, $tags, PREG_SET_ORDER ) )
            return $files;
        foreach ( $tags as $tag )
        {
            $attributes = array();
            if ( preg_match_all( '/([a-z_\-]+)="([^"]*)"/', $tag[2], $pairs, PREG_SET_ORDER ) )
            {
                foreach ( $pairs as $pair )
                    $attributes[$pair[1]] = html_entity_decode( $pair[2], ENT_QUOTES | ENT_XML1, 'UTF-8' );
            }
            $files[] = array( 'name' => $tag[1] === 'original' ? 'original' : ( isset( $attributes['name'] ) ? $attributes['name'] : '' ),
                              'url' => isset( $attributes['url'] ) ? $attributes['url'] : '',
                              'dirpath' => isset( $attributes['dirpath'] ) ? rtrim( $attributes['dirpath'], '/' ) : '',
                              'filename' => isset( $attributes['filename'] ) ? $attributes['filename'] : '' );
        }
        return $files;
    }

    /**
     * The name and path problems of an image XML: a url that is not dirpath/filename ('path'), and a file that is
     * missing under its name but there in another case ('name'). A file missing altogether is not one of them (the
     * missing files check reports it).
     *
     * @param string $xml
     * @param callable|null $exists function( $path ): bool; null: is_file
     * @param callable|null $list function( $dir ): string[] the names in a directory; null: scandir
     * @return array list of array( 'kind' => path|name, 'path' => string, 'detail' => string )
     */
    public static function imageXmlMismatches( $xml, $exists = null, $list = null )
    {
        $exists = $exists ?: 'is_file';
        $list = $list ?: function ( $dir ) { $names = @scandir( $dir ); return is_array( $names ) ? $names : array(); };
        $found = array();
        $seen = array();
        foreach ( self::imageXmlFiles( $xml ) as $file )
        {
            if ( $file['url'] === '' || isset( $seen[$file['url']] ) )
                continue;
            $seen[$file['url']] = true;
            if ( $file['dirpath'] !== '' && $file['filename'] !== '' && $file['dirpath'] . '/' . $file['filename'] !== $file['url'] )
                $found[] = array( 'kind' => 'path', 'path' => $file['url'], 'detail' => $file['dirpath'] . '/' . $file['filename'] );
            if ( !call_user_func( $exists, $file['url'] ) )
            {
                $base = basename( $file['url'] );
                foreach ( call_user_func( $list, dirname( $file['url'] ) ) as $name )
                {
                    if ( $name !== $base && strcasecmp( $name, $base ) === 0 )
                    {
                        $found[] = array( 'kind' => 'name', 'path' => $file['url'], 'detail' => dirname( $file['url'] ) . '/' . $name );
                        break;
                    }
                }
            }
        }
        return $found;
    }

    /**
     * Where ezbinaryfile and ezmedia expect a file: original/<first part of the MIME type>/<file name>.
     *
     * @param string $filename
     * @param string $mimeType
     * @param string|null $storage
     * @return string|null
     */
    public static function binaryPath( $filename, $mimeType, $storage = null )
    {
        $filename = (string)$filename;
        if ( $filename === '' || strpos( $filename, '/' ) !== false || $filename === '.' || $filename === '..' )
            return null;
        $type = explode( '/', (string)$mimeType );
        $type = preg_match( '/^[a-z][a-z0-9.+\-]{0,39}$/i', $type[0] ) ? $type[0] : 'application';
        return ( $storage === null ? self::storageDirectory() : $storage ) . '/original/' . $type . '/' . $filename;
    }

    // ------------------------------------------------------------------ which files are used (database)

    /**
     * Which of the given files content uses.
     *
     * @param string[] $paths paths from the installation (var/<site>/storage/...)
     * @param array|null $index the reference index (expContentCheckReferences::index()); null: not consulted
     * @return array path => array( 'object_id' => int, 'how' => imagefile|imagexml|binaryfile|media|index )
     */
    public static function usedAmong( array $paths, $index = null )
    {
        $db = eZDB::instance();
        $storage = self::storageDirectory();
        $used = array();
        $left = array_values( array_unique( array_map( 'strval', $paths ) ) );

        foreach ( array_chunk( $left, 200 ) as $chunk )
        {
            $in = array();
            foreach ( $chunk as $path )
                $in[] = "'" . $db->escapeString( $path ) . "'";
            foreach ( (array)$db->arrayQuery( 'SELECT ezimagefile.filepath AS filepath, MIN( ezcontentobject_attribute.contentobject_id ) AS object_id' .
                                              ' FROM ezimagefile LEFT JOIN ezcontentobject_attribute' .
                                              ' ON ezcontentobject_attribute.id = ezimagefile.contentobject_attribute_id' .
                                              ' WHERE ezimagefile.filepath IN ( ' . implode( ', ', $in ) . ' )' .
                                              ' GROUP BY ezimagefile.filepath' ) as $row )
                $used[(string)$row['filepath']] = array( 'object_id' => (int)$row['object_id'], 'how' => 'imagefile' );
        }
        $left = array_values( array_diff( $left, array_keys( $used ) ) );

        // images: the XML of the attribute their directory names
        $byAttribute = array();
        foreach ( $left as $path )
        {
            $relative = substr( $path, strlen( $storage ) + 1 );
            if ( strncmp( $relative, 'images', 6 ) !== 0 )
                continue;
            $attribute = self::imageAttributeOf( $relative );
            if ( $attribute )
                $byAttribute[$attribute[0]][] = $path;
        }
        foreach ( array_chunk( array_keys( $byAttribute ), 200 ) as $chunk )
        {
            foreach ( (array)$db->arrayQuery( 'SELECT id, contentobject_id, data_text FROM ezcontentobject_attribute' .
                                              ' WHERE id IN ( ' . expContentCheck::inList( $chunk ) . ' )' ) as $row )
            {
                foreach ( $byAttribute[(int)$row['id']] as $path )
                {
                    if ( !isset( $used[$path] ) && self::imageXmlNames( (string)$row['data_text'], $path ) )
                        $used[$path] = array( 'object_id' => (int)$row['contentobject_id'], 'how' => 'imagexml' );
                }
            }
        }
        $left = array_values( array_diff( $left, array_keys( $used ) ) );

        // files and media: their name in ezbinaryfile / ezmedia
        $names = array();
        foreach ( $left as $path )
        {
            $relative = substr( $path, strlen( $storage ) + 1 );
            if ( strncmp( $relative, 'original/', 9 ) === 0 )
                $names[basename( $path )][] = $path;
        }
        foreach ( array( 'ezbinaryfile' => 'binaryfile', 'ezmedia' => 'media' ) as $table => $how )
        {
            foreach ( array_chunk( array_keys( $names ), 200 ) as $chunk )
            {
                $in = array();
                foreach ( $chunk as $name )
                    $in[] = "'" . $db->escapeString( (string)$name ) . "'";
                foreach ( (array)$db->arrayQuery( 'SELECT ' . $table . '.filename AS filename, MIN( ezcontentobject_attribute.contentobject_id ) AS object_id' .
                                                  ' FROM ' . $table . ' LEFT JOIN ezcontentobject_attribute' .
                                                  ' ON ezcontentobject_attribute.id = ' . $table . '.contentobject_attribute_id' .
                                                  ' AND ezcontentobject_attribute.version = ' . $table . '.version' .
                                                  ' WHERE ' . $table . '.filename IN ( ' . implode( ', ', $in ) . ' )' .
                                                  ' GROUP BY ' . $table . '.filename' ) as $row )
                {
                    foreach ( $names[(string)$row['filename']] as $path )
                    {
                        if ( !isset( $used[$path] ) )
                            $used[$path] = array( 'object_id' => (int)$row['object_id'], 'how' => $how );
                    }
                }
            }
        }

        // anything else that names the path: rich text, layouts, settings, templates
        if ( is_array( $index ) && !empty( $index['storage'] ) )
        {
            foreach ( $paths as $path )
            {
                if ( !isset( $used[$path] ) && isset( $index['storage'][$path] ) )
                    $used[$path] = array( 'object_id' => 0, 'how' => 'index' );
            }
        }
        return $used;
    }

    // ------------------------------------------------------------------ the scan

    /**
     * A new scan state.
     *
     * @return array
     */
    public static function newState()
    {
        $queue = array();
        foreach ( self::scanDirectories() as $dir )
            $queue[] = $dir;
        $user = eZUser::currentUser();
        return array( 'format' => self::FORMAT, 'id' => bin2hex( random_bytes( 8 ) ), 'started' => time(), 'finished' => 0,
                      'seconds' => 0.0, 'steps' => 0,
                      'user_id' => $user instanceof eZUser ? (int)$user->attribute( 'contentobject_id' ) : 0,
                      'storage' => self::storageDirectory(), 'cluster' => self::clusterTable(),
                      'phase' => 'files', 'table' => 'ezbinaryfile', 'queue' => $queue, 'dir' => '', 'offset' => 0, 'cursor' => '', 'after' => 0,
                      'index' => 'none', 'index_built' => 0,
                      'expected_owner' => self::expectedOwner(), 'expected_mode' => self::expectedMode(),
                      'counts' => array( 'files' => 0, 'bytes' => 0, 'used' => 0, 'orphans' => 0, 'orphan_bytes' => 0,
                                         'recent' => 0, 'ignored' => 0, 'skipped' => 0, 'problems' => array_fill_keys( self::PROBLEMS, 0 ),
                                         'objects' => 0 ),
                      'orphans' => array(), 'problems' => array(), 'errors' => array() );
    }

    /**
     * The state of the last scan, finished or not; null when there is none.
     *
     * @return array|null
     */
    public static function lastScan()
    {
        if ( self::$testState !== null )
            return self::$testState ?: null;
        $file = self::statePath();
        if ( !is_file( $file ) )
            return null;
        $data = json_decode( (string)@file_get_contents( $file ), true );
        return is_array( $data ) && isset( $data['format'] ) && (int)$data['format'] === self::FORMAT ? $data : null;
    }

    /**
     * @param array|false|null $state a state for tests (false: none); null goes back to the file
     */
    public static function setTestState( $state )
    {
        self::$testState = $state;
    }

    /**
     * @return string
     */
    public static function statePath()
    {
        return eZSys::cacheDirectory() . '/' . self::STATE_FILE;
    }

    /**
     * Keeps a scan state (the file is written whole, then renamed: a reader never sees half of it).
     *
     * @param array $state
     * @return bool
     */
    public static function saveState( array $state )
    {
        if ( self::$testState !== null )
        {
            self::$testState = $state;
            return true;
        }
        $file = self::statePath();
        $json = json_encode( $state, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR );
        $ok = eZFile::create( basename( $file ), dirname( $file ), (string)$json, true );
        if ( $ok )
            @chmod( $file, eZFile::fileMode( octdec( (string)eZINI::instance()->variable( 'FileSettings', 'StorageFilePermissions' ) ) & 0666 ) );
        return (bool)$ok;
    }

    /**
     * Removes the scan state (after files were moved: the list would name files that are gone).
     */
    public static function clearState()
    {
        if ( self::$testState !== null )
        {
            self::$testState = false;
            return;
        }
        $file = self::statePath();
        if ( is_file( $file ) )
            @unlink( $file );
    }

    /**
     * The share of the scan done, 0 to 100 (an estimate: the files phase counts for 80).
     *
     * @param array $state
     * @return int
     */
    public static function percent( array $state )
    {
        if ( $state['phase'] === 'done' )
            return 100;
        if ( $state['phase'] === 'files' )
        {
            $total = count( self::scanDirectories() );
            $left = count( $state['queue'] ) + ( $state['dir'] !== '' ? 1 : 0 );
            return $total > 0 ? max( 1, min( 79, (int)floor( 80 * ( $total - $left ) / $total ) ) ) : 1;
        }
        return $state['phase'] === 'images' ? 85 : 95;
    }

    /**
     * Runs one step of a scan: up to batchSize() files (or one chunk of attributes), then keeps the state.
     *
     * @param array|null $state null starts a new scan
     * @param float $seconds the time a step may take before it stops at the end of a batch
     * @return array the state
     */
    public static function scanStep( $state = null, $seconds = 4.0 )
    {
        $start = microtime( true );
        if ( !is_array( $state ) || !empty( $state['finished'] ) )
            $state = self::newState();
        if ( $state['index'] === 'none' )
        {
            $index = expContentCheckReferences::index();
            $state['index'] = is_array( $index ) ? ( empty( $index['failed'] ) ? 'ok' : 'incomplete' ) : 'missing';
            $state['index_built'] = is_array( $index ) ? (int)$index['built'] : 0;
        }
        $index = $state['index'] === 'none' || $state['index'] === 'missing' ? null : expContentCheckReferences::index();
        do
        {
            if ( $state['phase'] === 'files' )
                $state = $state['cluster'] !== '' ? self::stepCluster( $state, $index ) : self::stepFiles( $state, $index );
            else if ( $state['phase'] === 'images' )
                $state = self::stepImages( $state );
            else if ( $state['phase'] === 'binaries' )
                $state = self::stepBinaries( $state );
            if ( $state['phase'] === 'done' )
                break;
        }
        while ( microtime( true ) - $start < (float)$seconds );
        $state['steps']++;
        $state['seconds'] = round( (float)$state['seconds'] + microtime( true ) - $start, 2 );
        if ( $state['phase'] === 'done' )
            $state['finished'] = time();
        self::saveState( $state );
        return $state;
    }

    /**
     * Runs a whole scan (the CLI).
     *
     * @param callable|null $progress function( array $state ) after each step
     * @return array the finished state
     */
    public static function scanAll( $progress = null )
    {
        $state = self::scanStep( null, 30.0 );
        while ( $state['phase'] !== 'done' )
        {
            if ( $progress )
                call_user_func( $progress, $state );
            $state = self::scanStep( $state, 30.0 );
        }
        return $state;
    }

    /**
     * One batch of files from the file system.
     *
     * @param array $state
     * @param array|null $index
     * @return array
     */
    protected static function stepFiles( array $state, $index )
    {
        $storage = (string)$state['storage'];
        $batch = array();
        $limit = self::batchSize();
        while ( count( $batch ) < $limit )
        {
            if ( $state['dir'] === '' )
            {
                if ( !$state['queue'] )
                {
                    $state['phase'] = 'images';
                    break;
                }
                $state['dir'] = array_shift( $state['queue'] );
                $state['offset'] = 0;
            }
            $names = @scandir( $storage . '/' . $state['dir'] );
            if ( !is_array( $names ) )
            {
                $state['dir'] = '';
                continue;
            }
            $names = array_values( array_diff( $names, array( '.', '..' ) ) );
            $subdirs = array();
            $total = count( $names );
            for ( $i = (int)$state['offset']; $i < $total && count( $batch ) < $limit; $i++ )
            {
                $name = $names[$i];
                $relative = $state['dir'] . '/' . $name;
                $path = $storage . '/' . $relative;
                $state['offset'] = $i + 1;
                if ( is_link( $path ) )
                {
                    $state['counts']['skipped']++;
                    continue;
                }
                if ( is_dir( $path ) )
                {
                    if ( !self::isIgnored( $relative ) )
                        $subdirs[] = $relative;
                    continue;
                }
                if ( $name[0] === '.' )
                    continue;
                $batch[$path] = $relative;
            }
            // sub-directories go to the front of the queue, in name order, so the walk is depth first and stable
            if ( $subdirs )
                $state['queue'] = array_merge( $subdirs, $state['queue'] );
            if ( $state['offset'] >= $total )
                $state['dir'] = '';
        }
        return self::judgeBatch( $state, $batch, $index, function ( $path ) {
            $stat = @lstat( $path );
            return is_array( $stat ) ? array( 'size' => (int)$stat['size'], 'mtime' => (int)$stat['mtime'], 'uid' => (int)$stat['uid'],
                                              'mode' => (int)$stat['mode'] & 07777 ) : null;
        } );
    }

    /**
     * One batch of files from the cluster table (name order).
     *
     * @param array $state
     * @param array|null $index
     * @return array
     */
    protected static function stepCluster( array $state, $index )
    {
        $db = eZDB::instance();
        $table = (string)$state['cluster'];
        $storage = (string)$state['storage'];
        if ( !expContentCheckReferences::tableExists( $table ) )
        {
            $state['errors'][] = 'cluster_table';
            $state['phase'] = 'images';
            return $state;
        }
        $rows = (array)$db->arrayQuery( 'SELECT name, size, mtime FROM ' . $table .
                                        " WHERE name LIKE '" . $db->escapeString( $storage ) . "/%'" .
                                        " AND name > '" . $db->escapeString( (string)$state['cursor'] ) . "' AND expired = 0 ORDER BY name",
                                        array( 'limit' => self::batchSize() ) );
        if ( !$rows )
        {
            $state['phase'] = 'images';
            return $state;
        }
        $batch = array();
        $stats = array();
        $dirs = self::scanDirectories();
        foreach ( $rows as $row )
        {
            $path = (string)$row['name'];
            $state['cursor'] = $path;
            if ( strncmp( $path, $storage . '/', strlen( $storage ) + 1 ) !== 0 )
                continue;
            $relative = substr( $path, strlen( $storage ) + 1 );
            $top = explode( '/', $relative );
            if ( !in_array( $top[0], $dirs, true ) || self::isIgnored( $relative ) )
                continue;
            $batch[$path] = $relative;
            $stats[$path] = array( 'size' => (int)$row['size'], 'mtime' => (int)$row['mtime'] );
        }
        return self::judgeBatch( $state, $batch, $index, function ( $path ) use ( $stats ) {
            return isset( $stats[$path] ) ? $stats[$path] : null;
        } );
    }

    /**
     * Judges a batch of files: used or orphan, and the problems of the used ones.
     *
     * @param array $state
     * @param array $batch path => relative path
     * @param array|null $index
     * @param callable $stat function( $path ): array|null
     * @return array
     */
    protected static function judgeBatch( array $state, array $batch, $index, $stat )
    {
        if ( !$batch )
            return $state;
        $used = self::usedAmong( array_keys( $batch ), $index );
        $now = time();
        $minAge = self::minAge();
        $max = self::maxListed();
        $expectedOwner = $state['cluster'] === '' ? $state['expected_owner'] : null;
        foreach ( $batch as $path => $relative )
        {
            $info = call_user_func( $stat, $path );
            if ( !$info )
                continue;
            $state['counts']['files']++;
            $state['counts']['bytes'] += $info['size'];
            if ( isset( $used[$path] ) )
            {
                $state['counts']['used']++;
                $problems = $state['cluster'] === '' ? self::fileProblems( $info, $expectedOwner, (int)$state['expected_mode'] )
                                                     : ( $info['size'] === 0 ? array( 'empty' ) : array() );
                foreach ( $problems as $kind )
                    $state = self::addProblem( $state, $kind, $relative, $info, $used[$path]['object_id'], '' );
                continue;
            }
            if ( $now - $info['mtime'] < $minAge )
            {
                $state['counts']['recent']++;
                continue;
            }
            $state['counts']['orphans']++;
            $state['counts']['orphan_bytes'] += $info['size'];
            if ( count( $state['orphans'] ) < $max )
                $state['orphans'][] = array( 'p' => $relative, 's' => $info['size'], 'm' => $info['mtime'],
                                             'u' => isset( $info['uid'] ) ? $info['uid'] : null,
                                             'md' => isset( $info['mode'] ) ? $info['mode'] : null );
        }
        return $state;
    }

    /**
     * @param array $state
     * @param string $kind
     * @param string $relative
     * @param array $info
     * @param int $objectID
     * @param string $detail
     * @return array
     */
    protected static function addProblem( array $state, $kind, $relative, array $info, $objectID, $detail )
    {
        $state['counts']['problems'][$kind]++;
        if ( count( $state['problems'] ) < self::maxListed() )
            $state['problems'][] = array( 'k' => $kind, 'p' => $relative, 's' => isset( $info['size'] ) ? $info['size'] : -1,
                                          'm' => isset( $info['mtime'] ) ? $info['mtime'] : 0,
                                          'u' => isset( $info['uid'] ) ? $info['uid'] : null,
                                          'md' => isset( $info['mode'] ) ? $info['mode'] : null,
                                          'o' => (int)$objectID, 'd' => (string)$detail );
        return $state;
    }

    /**
     * One chunk of image attributes: name and path mismatches of their XML (versions that count, see expContentCheck).
     *
     * @param array $state
     * @return array
     */
    protected static function stepImages( array $state )
    {
        $db = eZDB::instance();
        $ids = array();
        foreach ( (array)$db->arrayQuery( "SELECT id FROM ezcontentobject_attribute WHERE data_type_string = 'ezimage' AND id > " . (int)$state['after'] .
                                          ' GROUP BY id ORDER BY id', array( 'limit' => 200 ) ) as $row )
            $ids[] = (int)$row['id'];
        if ( !$ids )
        {
            $state['phase'] = 'binaries';
            $state['table'] = 'ezbinaryfile';
            $state['after'] = 0;
            return $state;
        }
        $state['after'] = max( $ids );
        $storage = (string)$state['storage'];
        foreach ( (array)$db->arrayQuery( 'SELECT a.id, a.contentobject_id, a.data_text FROM ezcontentobject_attribute a, ezcontentobject_version v' .
                                          ' WHERE a.id IN ( ' . expContentCheck::inList( $ids ) . ' )' .
                                          ' AND v.contentobject_id = a.contentobject_id AND v.version = a.version' .
                                          ' AND v.status IN ( ' . implode( ', ', expContentCheck::REFERENCE_VERSION_STATUSES ) . ' )' ) as $row )
        {
            foreach ( self::imageXmlMismatches( (string)$row['data_text'] ) as $found )
            {
                if ( strncmp( $found['path'], $storage . '/', strlen( $storage ) + 1 ) !== 0 )
                    continue;
                $state = self::addProblem( $state, $found['kind'], substr( $found['path'], strlen( $storage ) + 1 ), array(),
                                           (int)$row['contentobject_id'], substr( $found['detail'], strpos( $found['detail'], $storage ) === 0 ? strlen( $storage ) + 1 : 0 ) );
            }
        }
        return $state;
    }

    /**
     * One chunk of files and media: a file that is not where its MIME type puts it, but in another type's directory.
     * The state's 'table' says which table is read (ezbinaryfile, then ezmedia), 'after' the last attribute id.
     *
     * @param array $state
     * @return array
     */
    protected static function stepBinaries( array $state )
    {
        $db = eZDB::instance();
        $storage = (string)$state['storage'];
        $table = isset( $state['table'] ) && $state['table'] === 'ezmedia' ? 'ezmedia' : 'ezbinaryfile';
        $ids = array();
        foreach ( (array)$db->arrayQuery( 'SELECT contentobject_attribute_id AS id FROM ' . $table . ' WHERE contentobject_attribute_id > ' . (int)$state['after'] .
                                          ' GROUP BY contentobject_attribute_id ORDER BY contentobject_attribute_id', array( 'limit' => 200 ) ) as $row )
            $ids[] = (int)$row['id'];
        if ( !$ids )
        {
            if ( $table === 'ezbinaryfile' )
            {
                $state['table'] = 'ezmedia';
                $state['after'] = 0;
            }
            else
                $state['phase'] = 'done';
            return $state;
        }
        $state['after'] = max( $ids );
        $types = null;
        $seen = array();
        foreach ( (array)$db->arrayQuery( 'SELECT t.filename, t.mime_type, a.contentobject_id FROM ' . $table . ' t' .
                                          ' LEFT JOIN ezcontentobject_attribute a ON a.id = t.contentobject_attribute_id AND a.version = t.version' .
                                          ' WHERE t.contentobject_attribute_id IN ( ' . expContentCheck::inList( $ids ) . ' )' ) as $row )
        {
            $expected = self::binaryPath( $row['filename'], $row['mime_type'], $storage );
            if ( $expected === null || isset( $seen[$expected] ) || is_file( $expected ) )
                continue;
            $seen[$expected] = true;
            if ( $types === null )
            {
                $types = @scandir( $storage . '/original' );
                $types = is_array( $types ) ? array_values( array_diff( $types, array( '.', '..' ) ) ) : array();
            }
            foreach ( $types as $type )
            {
                $other = $storage . '/original/' . $type . '/' . $row['filename'];
                if ( $other !== $expected && is_file( $other ) )
                {
                    $state = self::addProblem( $state, 'type', substr( $expected, strlen( $storage ) + 1 ), array(), (int)$row['contentobject_id'],
                                               substr( $other, strlen( $storage ) + 1 ) );
                    break;
                }
            }
        }
        return $state;
    }

    // ------------------------------------------------------------------ the result of a scan, as pages show it

    /**
     * Takes files an action changed out of the last scan, so its lists stay true: quarantined files leave the
     * orphans, fixed files leave the owner and mode problems. A restore or a delete only marks the scan as changed.
     *
     * @param string $action
     * @param string[] $items paths below the storage (quarantine, permissions) or quarantine items
     */
    public static function forget( $action, array $items )
    {
        $state = self::lastScan();
        if ( !$state )
            return;
        $state['changed'] = time();
        $gone = array_flip( $items );
        if ( $action === 'quarantine' )
        {
            $kept = array();
            foreach ( $state['orphans'] as $orphan )
            {
                if ( isset( $gone[$orphan['p']] ) )
                {
                    $state['counts']['orphans']--;
                    $state['counts']['orphan_bytes'] -= (int)$orphan['s'];
                    $state['counts']['files']--;
                    $state['counts']['bytes'] -= (int)$orphan['s'];
                    continue;
                }
                $kept[] = $orphan;
            }
            $state['orphans'] = $kept;
        }
        else if ( $action === 'permissions' )
        {
            $kept = array();
            foreach ( $state['problems'] as $problem )
            {
                if ( isset( $gone[$problem['p']] ) && in_array( $problem['k'], array( 'owner', 'mode' ), true ) )
                {
                    $state['counts']['problems'][$problem['k']]--;
                    continue;
                }
                $kept[] = $problem;
            }
            $state['problems'] = $kept;
        }
        self::saveState( $state );
    }

    /**
     * The orphans of a scan in the order asked for.
     *
     * @param array $state
     * @param string $sort path | size | age
     * @param string $order asc | desc
     * @return array
     */
    public static function sortedOrphans( array $state, $sort, $order )
    {
        $orphans = $state['orphans'];
        $key = array( 'path' => 'p', 'size' => 's', 'age' => 'm' );
        $field = isset( $key[$sort] ) ? $key[$sort] : 'p';
        usort( $orphans, function ( $a, $b ) use ( $field ) {
            $c = $field === 'p' ? strcmp( $a['p'], $b['p'] ) : ( $a[$field] <=> $b[$field] );
            return $c !== 0 ? $c : strcmp( $a['p'], $b['p'] );
        } );
        // the age ascending means the newest first: an older file has a smaller time
        $descending = $order === 'desc';
        if ( $field === 'm' )
            $descending = !$descending;
        return $descending ? array_reverse( $orphans ) : $orphans;
    }

    /**
     * The problems of a scan whose object lies below the node and may be read by the current user (problems of no
     * object, e.g. a path only a setting names, are shown with the whole tree only), with the object's name and node.
     *
     * @param array $state
     * @param int $nodeID the start location (1: the whole tree)
     * @param string[] $kinds the kinds asked for
     * @return array list of array( 'kind', 'path', 'size', 'mtime', 'owner', 'mode', 'object_id', 'node_id', 'name', 'detail' )
     */
    public static function problemsBelow( array $state, $nodeID, array $kinds )
    {
        $node = expContentCheck::readableNode( (int)$nodeID );
        if ( !$node )
            return array();
        $pattern = expContentCheck::subtreePattern( (string)$node->attribute( 'path_string' ) );
        $objectIDs = array();
        foreach ( $state['problems'] as $problem )
        {
            if ( in_array( $problem['k'], $kinds, true ) && (int)$problem['o'] > 0 )
                $objectIDs[(int)$problem['o']] = true;
        }
        $found = array();
        if ( $objectIDs && $pattern !== null )
        {
            $db = eZDB::instance();
            $read = expContentCheck::readPermission();
            try
            {
                foreach ( array_chunk( array_keys( $objectIDs ), 500 ) as $chunk )
                {
                    foreach ( (array)$db->arrayQuery( 'SELECT ezcontentobject.id AS object_id, ezcontentobject.name AS name, ezcontentobject_tree.node_id AS node_id' .
                                                      ' FROM ezcontentobject, ezcontentobject_tree' . $read['from'] .
                                                      ' WHERE ezcontentobject_tree.contentobject_id = ezcontentobject.id' .
                                                      ' AND ezcontentobject_tree.node_id = ezcontentobject_tree.main_node_id' .
                                                      ' AND ezcontentobject.id IN ( ' . expContentCheck::inList( $chunk ) . ' )' .
                                                      " AND ezcontentobject_tree.path_string LIKE '" . $pattern . "' " . $read['where'] ) as $row )
                        $found[(int)$row['object_id']] = array( 'name' => (string)$row['name'], 'node_id' => (int)$row['node_id'] );
                }
            }
            finally
            {
                expContentCheck::dropPermission( $read );
            }
        }
        $wholeTree = (int)$nodeID === 1;
        $rows = array();
        foreach ( $state['problems'] as $problem )
        {
            if ( !in_array( $problem['k'], $kinds, true ) )
                continue;
            $objectID = (int)$problem['o'];
            if ( $objectID > 0 ? !isset( $found[$objectID] ) : !$wholeTree )
                continue;
            $rows[] = array( 'kind' => $problem['k'], 'path' => $problem['p'], 'size' => (int)$problem['s'], 'mtime' => (int)$problem['m'],
                             'owner' => $problem['u'] === null ? '' : self::userName( (int)$problem['u'] ),
                             'mode' => $problem['md'] === null ? '' : sprintf( '%04o', (int)$problem['md'] ),
                             'object_id' => $objectID, 'node_id' => $objectID > 0 ? $found[$objectID]['node_id'] : 0,
                             'name' => $objectID > 0 ? $found[$objectID]['name'] : '', 'detail' => (string)$problem['d'],
                             'fixable' => in_array( $problem['k'], array( 'owner', 'mode' ), true ) );
        }
        return $rows;
    }
}

?>
