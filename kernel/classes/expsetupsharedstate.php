<?php
/**
 * File containing the expSetupSharedState class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Where the web setup wizard keeps what every node of a site must see.
 *
 * The wizard is one request per step. On a site served by several nodes behind
 * a load balancer, var/ belongs to each node, and only the database and the
 * cluster's shared storage are common. The wizard's lease (maintenance mode
 * while it runs), its run (var/log/setup-run.state and setup.log) and the
 * installed marker used to live in var/: a step that landed on another node
 * found no run and no lease there, started over, and the browser's cookie no
 * longer matched the first node's lease.
 *
 * With a shared state directory all of them live there instead:
 *
 * - [SetupSettings] SharedStateDir in site.ini (an absolute path, or one relative
 *   to the installation), or the environment variable EXP_SETUP_SHARED_STATE_DIR;
 * - else, on a site clustered with eZDFS, <MountPointPath>/var/exp-setup on the
 *   NFS mount every node shares (file.ini [eZDFSClusteringSettings]);
 * - else none: a single node keeps everything in var/ exactly as before.
 *
 * The directory is needed before the database exists (the wizard creates it),
 * which is why it is a directory on shared storage and not the cluster's
 * database tables.
 *
 * Writes that more than one node may race on (the lease) are made under lock():
 * a directory created with mkdir(), which is atomic on local file systems and
 * on NFS. Files are replaced by a rename of a temporary file in the same
 * directory, so a reader never sees half a file.
 *
 * The wizard also writes files of its own into the installation: the settings
 * of the new site (settings/override, settings/siteaccess) and the packages it
 * imports (var/storage/packages). push() copies those it changed during the run
 * to the shared directory, with a manifest of their checksums; pull() brings
 * them to the node that serves the next step, or to a node that has not seen
 * them yet once the site is installed.
 */
class expSetupSharedState
{
    /** The environment variable that names the directory (tests, a vhost's SetEnv) */
    const ENV = 'EXP_SETUP_SHARED_STATE_DIR';
    /** Below the eZDFS mount when nothing names the directory */
    const DFS_SUBDIR = 'var/exp-setup';
    /** Seconds after which a lock left behind (a process that died holding it) is broken */
    const LOCK_STALE = 60;
    /** Seconds lock() waits for another node's lock */
    const LOCK_WAIT = 10;
    /** The mirrored files and their manifest */
    const FILES_DIR = 'files';
    const MANIFEST = 'files/manifest.json';

    /** @var array root => resolved directory (string) or false, per process */
    protected static $cache = array();

    /**
     * The shared state directory of the installation at $root.
     *
     * @param string $root the installation root
     * @return string|null an absolute path without trailing slash, null when the site keeps its state in var/
     */
    static function dir( $root )
    {
        $root = rtrim( (string)$root, '/' );
        if ( !array_key_exists( $root, self::$cache ) )
        {
            // The settings describe the installation this process runs, and no other one (a test's
            // installation under var/tmp never takes the live site's shared directory)
            $settings = array( 'env' => (string)getenv( self::ENV ) );
            if ( self::isThisInstallation( $root ) )
                $settings += self::settings();
            $dir = self::resolveDir( $root, $settings );
            self::$cache[$root] = $dir === null ? false : $dir;
        }
        return self::$cache[$root] === false ? null : self::$cache[$root];
    }

    /** Whether the site keeps the wizard's state in a shared directory */
    static function isShared( $root )
    {
        return self::dir( $root ) !== null;
    }

    /** Forgets what dir() resolved (tests, a settings change in a persistent worker) */
    static function reset()
    {
        self::$cache = array();
    }

    /**
     * The directory for a set of settings. Pure, so the rules are tested without settings files.
     *
     * @param string $root
     * @param array $settings env, shared_dir (site.ini [SetupSettings] SharedStateDir),
     *        file_handler (file.ini [ClusteringSettings] FileHandler), dfs_mount ([eZDFSClusteringSettings] MountPointPath)
     * @return string|null
     */
    static function resolveDir( $root, array $settings )
    {
        $root = rtrim( (string)$root, '/' );
        foreach ( array( 'env', 'shared_dir' ) as $key )
        {
            $dir = isset( $settings[$key] ) ? trim( (string)$settings[$key] ) : '';
            if ( $dir !== '' )
                return self::absolute( $root, $dir );
        }
        $handler = isset( $settings['file_handler'] ) ? strtolower( trim( (string)$settings['file_handler'] ) ) : '';
        $mount = isset( $settings['dfs_mount'] ) ? trim( (string)$settings['dfs_mount'] ) : '';
        if ( $handler === 'ezdfsfilehandler' && $mount !== '' )
            return self::absolute( $root, rtrim( $mount, '/' ) . '/' . self::DFS_SUBDIR );
        return null;
    }

    protected static function absolute( $root, $dir )
    {
        $dir = rtrim( $dir, '/' );
        if ( $dir === '' )
            return null;
        return $dir[0] === '/' ? $dir : $root . '/' . $dir;
    }

    /** Whether $root is the installation whose settings this process reads */
    protected static function isThisInstallation( $root )
    {
        if ( !class_exists( 'eZSys', false ) )
            return false;
        $own = rtrim( (string)eZSys::rootDir(), '/' );
        if ( $own === $root )
            return true;
        $a = @realpath( $own );
        $b = @realpath( $root );
        return $a !== false && $a === $b;
    }

    /** The settings resolveDir() reads from site.ini and file.ini, once they can be loaded */
    protected static function settings()
    {
        $settings = array();
        if ( !class_exists( 'eZINI', false ) )
            return $settings;
        try
        {
            $site = eZINI::instance();
            if ( $site->hasVariable( 'SetupSettings', 'SharedStateDir' ) )
                $settings['shared_dir'] = (string)$site->variable( 'SetupSettings', 'SharedStateDir' );
            $file = eZINI::instance( 'file.ini' );
            if ( $file->hasVariable( 'ClusteringSettings', 'FileHandler' ) )
                $settings['file_handler'] = (string)$file->variable( 'ClusteringSettings', 'FileHandler' );
            if ( $file->hasVariable( 'eZDFSClusteringSettings', 'MountPointPath' ) )
                $settings['dfs_mount'] = (string)$file->variable( 'eZDFSClusteringSettings', 'MountPointPath' );
        }
        catch ( Throwable $e )
        {
        }
        return $settings;
    }

    /**
     * A state file's path: in the shared directory when there is one, else in the installation.
     *
     * @param string $root
     * @param string $shared the file's path below the shared directory
     * @param string $local the file's path below the installation (as today on a single node)
     * @return string
     */
    static function file( $root, $shared, $local )
    {
        $dir = self::dir( $root );
        return $dir !== null ? $dir . '/' . ltrim( $shared, '/' ) : rtrim( $root, '/' ) . '/' . ltrim( $local, '/' );
    }

    /**
     * Whether a file is there now, asked of the file system: a persistent worker's
     * stat cache (PHP's own, Velocity's file layer) would answer what it saw before.
     */
    static function exists( $file )
    {
        if ( class_exists( 'Q_WebServer_CompatFileWrapper', false ) && method_exists( 'Q_WebServer_CompatFileWrapper', 'forgetPath' ) )
            Q_WebServer_CompatFileWrapper::forgetPath( $file );
        else
            clearstatcache( true, $file );
        return is_file( $file );
    }

    /**
     * Writes a file whole: a temporary file in the same directory renamed into place.
     * The temporary name carries the host and the process, which differ between nodes.
     *
     * @param string $file
     * @param string $data
     * @param int|null $mode the file's mode; null: the installation's file mode
     * @return bool
     */
    static function writeAtomic( $file, $data, $mode = null )
    {
        $dir = dirname( $file );
        if ( !is_dir( $dir ) && !@mkdir( $dir, self::dirMode(), true ) && !is_dir( $dir ) )
            return false;
        $tmp = $file . '.tmp-' . self::nodeName() . '-' . getmypid() . '-' . bin2hex( random_bytes( 4 ) );
        if ( @file_put_contents( $tmp, $data ) === false )
            return false;
        @chmod( $tmp, $mode === null ? self::fileMode() : ( (int)$mode & 0777 ) );
        if ( !@rename( $tmp, $file ) )
        {
            @unlink( $tmp );
            return false;
        }
        return true;
    }

    /**
     * Runs $fn while holding the lock of $dir, a directory created with mkdir()
     * (atomic on local file systems and on NFS). A lock older than LOCK_STALE
     * seconds is taken to be left by a process that died and is broken.
     *
     * @param string $dir the directory whose files the lock guards
     * @param callable $fn
     * @param float|null $wait seconds to wait for the lock (LOCK_WAIT)
     * @param string $name the lock directory's name in $dir
     * @return mixed what $fn returned, null when the lock could not be had
     */
    static function lock( $dir, callable $fn, $wait = null, $name = '.exp-setup.lock' )
    {
        $dir = rtrim( $dir, '/' );
        if ( !is_dir( $dir ) && !@mkdir( $dir, self::dirMode(), true ) && !is_dir( $dir ) )
            return null;
        $lock = $dir . '/' . $name;
        $deadline = microtime( true ) + ( $wait === null ? self::LOCK_WAIT : (float)$wait );
        while ( !@mkdir( $lock, 0777 ) )
        {
            clearstatcache( true, $lock );
            $since = @filemtime( $lock );
            if ( $since !== false && $since < time() - self::LOCK_STALE )
            {
                // Moved aside first: of two processes that found it stale, one breaks it
                $aside = $lock . '.stale-' . self::nodeName() . '-' . getmypid() . '-' . bin2hex( random_bytes( 4 ) );
                if ( @rename( $lock, $aside ) )
                    @rmdir( $aside );
                continue;
            }
            if ( microtime( true ) >= $deadline )
                return null;
            usleep( 20000 );
        }
        try
        {
            return $fn();
        }
        finally
        {
            @rmdir( $lock );
        }
    }

    /** @return array|false a JSON state file's content, false when missing or unreadable */
    static function readJson( $file )
    {
        if ( !self::exists( $file ) )
            return false;
        $data = json_decode( (string)@file_get_contents( $file ), true );
        return is_array( $data ) ? $data : false;
    }

    static function writeJson( $file, array $data )
    {
        return self::writeAtomic( $file, json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n" );
    }

    /**
     * The directories below the installation whose files the wizard writes and
     * every node needs: the settings of the new site and the package repository.
     *
     * @return string[] paths relative to the installation
     */
    static function mirroredPaths()
    {
        $paths = array( 'settings/override', 'settings/siteaccess' );
        try
        {
            if ( class_exists( 'eZPackage' ) )
                $paths[] = (string)eZPackage::repositoryPath();
        }
        catch ( Throwable $e )
        {
        }
        return array_values( array_unique( array_filter( $paths, array( __CLASS__, 'isSafeRelative' ) ) ) );
    }

    /** A relative path inside the installation: no leading slash, no "..", no NUL */
    static function isSafeRelative( $path )
    {
        $path = (string)$path;
        return $path !== '' && $path[0] !== '/' && strpos( $path, "\0" ) === false
            && !in_array( '..', explode( '/', str_replace( '\\', '/', $path ) ), true );
    }

    /**
     * Copies the files below $paths that changed since $since and differ from the
     * manifest into the shared directory, and records them in its manifest. A
     * manifest of another run is started afresh.
     *
     * @param string $root the installation
     * @param string $dir the shared state directory
     * @param string[] $paths relative to $root
     * @param int|float $since unix time: files older than this are not the run's
     * @param string $runId the setup run
     * @return int|false the number of files copied, false when the lock or a write failed
     */
    static function push( $root, $dir, array $paths, $since, $runId )
    {
        $root = rtrim( $root, '/' );
        $dir = rtrim( $dir, '/' );
        $result = self::lock( $dir, function () use ( $root, $dir, $paths, $since, $runId ) {
            $manifest = self::readJson( $dir . '/' . self::MANIFEST );
            if ( !is_array( $manifest ) || ( $manifest['run'] ?? null ) !== (string)$runId || !is_array( $manifest['files'] ?? null ) )
                $manifest = array( 'run' => (string)$runId, 'files' => array() );
            $copied = 0;
            foreach ( $paths as $path )
            {
                if ( !self::isSafeRelative( $path ) )
                    continue;
                foreach ( self::filesBelow( $root . '/' . $path ) as $file )
                {
                    $mtime = @filemtime( $file );
                    if ( $mtime === false || $mtime < (int)floor( (float)$since ) )
                        continue;
                    $relative = substr( $file, strlen( $root ) + 1 );
                    $sha = @sha1_file( $file );
                    if ( $sha === false || ( isset( $manifest['files'][$relative]['sha1'] ) && $manifest['files'][$relative]['sha1'] === $sha ) )
                        continue;
                    $data = @file_get_contents( $file );
                    // The copy keeps the file's mode: a settings file holds the database password
                    $mode = @fileperms( $file );
                    $mode = $mode === false ? 0640 : ( $mode & 0777 );
                    if ( $data === false || !self::writeAtomic( $dir . '/' . self::FILES_DIR . '/' . $relative, $data, $mode ) )
                        return false;
                    $manifest['files'][$relative] = array( 'sha1' => $sha, 'size' => strlen( $data ), 'mode' => sprintf( '%04o', $mode ),
                                                           'node' => self::nodeName(), 'time' => time() );
                    $copied++;
                }
            }
            if ( ( $copied > 0 || !self::exists( $dir . '/' . self::MANIFEST ) ) && !self::writeJson( $dir . '/' . self::MANIFEST, $manifest ) )
                return false;
            return $copied;
        } );
        return $result === null ? false : $result;
    }

    /**
     * Brings the files of $runId's manifest to this node where they are missing or differ.
     *
     * @param string $root
     * @param string $dir the shared state directory
     * @param string $runId only this run's files: never an earlier run's over the settings of today
     * @return int|false the number of files written, false when the lock or a write failed
     */
    static function pull( $root, $dir, $runId )
    {
        $root = rtrim( $root, '/' );
        $dir = rtrim( $dir, '/' );
        if ( (string)$runId === '' || !self::exists( $dir . '/' . self::MANIFEST ) )
            return 0;
        $result = self::lock( $dir, function () use ( $root, $dir, $runId ) {
            $manifest = self::readJson( $dir . '/' . self::MANIFEST );
            if ( !is_array( $manifest ) || ( $manifest['run'] ?? null ) !== (string)$runId || !is_array( $manifest['files'] ?? null ) )
                return 0;
            $written = 0;
            foreach ( $manifest['files'] as $relative => $entry )
            {
                if ( !self::isSafeRelative( $relative ) || !isset( $entry['sha1'] ) )
                    continue;
                $local = $root . '/' . $relative;
                if ( is_file( $local ) && @sha1_file( $local ) === $entry['sha1'] )
                    continue;
                $data = @file_get_contents( $dir . '/' . self::FILES_DIR . '/' . $relative );
                $mode = isset( $entry['mode'] ) && preg_match( '/^0?[0-7]{3}$/', (string)$entry['mode'] ) ? octdec( (string)$entry['mode'] ) : 0640;
                if ( $data === false || sha1( $data ) !== $entry['sha1'] || !self::writeAtomic( $local, $data, $mode ) )
                    return false;
                $written++;
            }
            return $written;
        } );
        return $result === null ? false : $result;
    }

    /** @return string[] the regular files below $dir, recursively; none when it is not a directory */
    protected static function filesBelow( $dir )
    {
        if ( !is_dir( $dir ) )
            return array();
        $files = array();
        $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
        foreach ( $iterator as $info )
        {
            // A temporary file of writeAtomic() or another writer is not the run's
            if ( $info->isFile() && !$info->isLink() && strpos( $info->getFilename(), '.tmp-' ) === false )
                $files[] = $info->getPathname();
        }
        sort( $files );
        return $files;
    }

    /** This node's name, as the state files record it */
    static function nodeName()
    {
        $name = function_exists( 'gethostname' ) ? (string)gethostname() : '';
        $name = preg_replace( '/[^A-Za-z0-9._-]/', '', $name );
        return $name !== '' ? $name : 'node';
    }

    protected static function fileMode()
    {
        return class_exists( 'eZFile' ) ? eZFile::fileMode( 0666 ) : 0664;
    }

    protected static function dirMode()
    {
        return 0777 & ~umask();
    }
}
