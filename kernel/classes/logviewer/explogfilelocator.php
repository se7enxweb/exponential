<?php
/**
 * Which log files the viewer may open, and where they are (doc/en/us/features/6.0/log-viewer.md, "Which files").
 *
 * A file is named by its path relative to the installation root ("var/log/error.log"), its id. resolve() accepts an
 * id only when
 *  - it is a plain relative path: no "..", no ".", no backslash, no NUL or control character, no leading slash;
 *  - its directory resolves (realpath) to one of the allowed directories of [LogViewerSettings] Directories[],
 *    which themselves resolve inside the installation root (a symbolic link to elsewhere is never followed);
 *  - its name matches FilePatterns[] and no ExcludePatterns[];
 *  - it is a regular, readable file, and a symbolic link only when its target is a file in an allowed directory.
 * Only the files directly in an allowed directory are listed; subdirectories are never walked.
 *
 * Nothing here locks or opens a log file: listing is one scandir() and one lstat() per file.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expLogFileLocator
{
    const GROUP_KERNEL = 'kernel';
    const GROUP_SITE = 'site';
    const GROUP_EXTENSIONS = 'extensions';
    const GROUP_WEBSERVER = 'webserver';
    const GROUP_ARCHIVES = 'archives';

    /** The shape of a log key: what a permalink /setup/logs/view/<key> may name */
    const KEY_PATTERN = '/^[A-Za-z0-9][A-Za-z0-9._\-]{0,63}$/';

    /** The longest id accepted */
    const MAX_ID_LENGTH = 512;

    /** @var expLogViewerConfig */
    protected $config;

    /** @var string the log directory eZDebug writes to, relative to the root ({log_dir}) */
    protected $logDir;

    /** @var array|null real directory path => id of the directory (relative), this instance */
    protected $directories = null;

    /** @var array|null the configured logs, this instance (configuredLogs()) */
    protected $configured = null;

    /** @var array|null the listing, this instance (listFiles()) */
    protected $listing = null;

    /** @var array|null LogLabel[] by key, this instance (labelOf()) */
    protected $labels = null;

    /**
     * @param expLogViewerConfig $config
     * @param string|null $logDir {log_dir}; null: eZDebug's log directory (var/log without eZDebug)
     */
    public function __construct( expLogViewerConfig $config, $logDir = null )
    {
        $this->config = $config;
        if ( $logDir === null )
        {
            $logDir = 'var/log';
            if ( class_exists( 'eZDebug', false ) )
            {
                $dir = (string)eZDebug::instance()->logDirectory();
                if ( $dir !== '' )
                    $logDir = $dir;
            }
        }
        $this->logDir = trim( str_replace( '\\', '/', (string)$logDir ), '/' );
    }

    /** @return expLogViewerConfig */
    public function config()
    {
        return $this->config;
    }

    /**
     * The allowed directories: real path => id (relative to the root). Each entry of Directories[] is expanded
     * ({log_dir}, one * per name) and kept only when it resolves to a directory inside the root.
     *
     * @return array
     */
    public function directories()
    {
        if ( $this->directories !== null )
            return $this->directories;
        $root = $this->config->root();
        $out = array();
        foreach ( $this->config->lst( 'LogViewerSettings', 'Directories' ) as $entry )
        {
            $entry = str_replace( '{log_dir}', $this->logDir, str_replace( '\\', '/', $entry ) );
            if ( strpos( $entry, "\0" ) !== false || preg_match( '#(^|/)\.\.?(/|$)#', $entry ) )
                continue;
            $pattern = $entry[0] === '/' ? $entry : $root . '/' . $entry;
            $pattern = rtrim( $pattern, '/' );
            $candidates = strpbrk( $pattern, '*?[' ) !== false ? (array)glob( $pattern, GLOB_ONLYDIR | GLOB_NOSORT ) : array( $pattern );
            foreach ( $candidates as $candidate )
            {
                $real = realpath( $candidate );
                if ( $real === false || !is_dir( $real ) )
                    continue;
                $real = str_replace( '\\', '/', $real );
                if ( !self::inside( $real, $root ) )
                    continue;
                $out[$real] = ltrim( substr( $real, strlen( $root ) ), '/' );
            }
        }
        ksort( $out );
        return $this->directories = $out;
    }

    /**
     * Whether a real path is the root or below it.
     *
     * @param string $path
     * @param string $root
     * @return bool
     */
    public static function inside( $path, $root )
    {
        $root = rtrim( $root, '/' );
        return $root !== '' && ( $path === $root || strncmp( $path, $root . '/', strlen( $root ) + 1 ) === 0 );
    }

    /**
     * Whether an id has the shape of one (nothing on disk is read). Pure.
     *
     * @param string $id
     * @return bool
     */
    public static function validId( $id )
    {
        if ( !is_string( $id ) || $id === '' || strlen( $id ) > self::MAX_ID_LENGTH )
            return false;
        if ( preg_match( '/[\x00-\x1F\x7F\\\\]/', $id ) || $id[0] === '/' || substr( $id, -1 ) === '/' )
            return false;
        foreach ( explode( '/', $id ) as $part )
            if ( $part === '' || $part === '.' || $part === '..' )
                return false;
        return true;
    }

    /**
     * Whether a file name is one the viewer shows (FilePatterns[], not ExcludePatterns[]).
     *
     * @param string $name
     * @return bool
     */
    public function nameAllowed( $name )
    {
        if ( $name === '' || $name[0] === '.' )
            return false;
        foreach ( $this->config->lst( 'LogViewerSettings', 'ExcludePatterns' ) as $p )
            if ( fnmatch( $p, $name ) )
                return false;
        foreach ( $this->config->lst( 'LogViewerSettings', 'FilePatterns' ) as $p )
            if ( fnmatch( $p, $name ) )
                return true;
        return false;
    }

    /**
     * The file of an id, when the viewer may open it.
     *
     * @param string $id
     * @return array|null id, path (real), name, dir (id of the directory), group, base (the current file's name),
     *                    rotation (int, 0 for the current file), gz (bool), level (of the file, or ''), size, mtime, ino
     */
    public function resolve( $id )
    {
        if ( !self::validId( $id ) )
            return null;
        if ( $id[0] === '@' )
            return $this->resolveConfigured( $id );
        $name = basename( $id );
        if ( !$this->nameAllowed( $name ) )
            return null;
        $root = $this->config->root();
        $dirs = $this->directories();
        $dirReal = realpath( $root . '/' . dirname( $id ) );
        if ( $dirReal === false )
            return null;
        $dirReal = str_replace( '\\', '/', $dirReal );
        if ( !isset( $dirs[$dirReal] ) )
            return null;
        $path = $dirReal . '/' . $name;
        $lstat = @lstat( $path );
        if ( $lstat === false )
            return null;
        if ( is_link( $path ) )
        {
            $target = realpath( $path );
            if ( $target === false || !isset( $dirs[str_replace( '\\', '/', dirname( $target ) )] ) || !$this->nameAllowed( basename( $target ) ) )
                return null;
        }
        if ( !is_file( $path ) || !is_readable( $path ) )
            return null;
        $stat = @stat( $path );
        if ( $stat === false )
            return null;
        $info = $this->describe( $dirs[$dirReal], $name );
        $info['path'] = $path;
        $info['size'] = (int)$stat['size'];
        $info['mtime'] = (int)$stat['mtime'];
        $info['ino'] = (int)$stat['ino'];
        return $info;
    }

    /**
     * What an allowed file is, from its directory id and name. Pure.
     *
     * @param string $dirID
     * @param string $name
     * @return array
     */
    public function describe( $dirID, $name )
    {
        $gz = substr( $name, -3 ) === '.gz';
        $plain = $gz ? substr( $name, 0, -3 ) : $name;
        $rotation = 0;
        $base = $plain;
        if ( preg_match( '/^(.+)\.(\d{1,4})$/', $plain, $m ) )
        {
            $base = $m[1];
            $rotation = (int)$m[2];
        }
        if ( $gz && $rotation === 0 && $base === $plain )
            $rotation = -1; // a compressed file without a number: an archive all the same
        $configured = $dirID !== '' && $dirID[0] === '@' ? $this->configuredLogs() : array();
        $ini = isset( $configured[substr( $dirID, 1 )] ) ? $configured[substr( $dirID, 1 )] : null;
        if ( $ini !== null )
            $base = $ini['base'];
        if ( $rotation !== 0 || $gz )
            $group = self::GROUP_ARCHIVES;
        else if ( $ini !== null )
            $group = $ini['group'];
        else if ( $this->isKernelName( $base ) && $this->isKernelDirectory( $dirID ) )
            $group = self::GROUP_KERNEL;
        else if ( $this->isSiteDirectory( $dirID ) )
            $group = self::GROUP_SITE;
        else
            $group = self::GROUP_EXTENSIONS;
        $key = self::keyFor( $dirID, $base );
        $label = $ini !== null ? $ini['label'] : $this->labelOf( $key );
        return array( 'id' => ( $dirID !== '' ? $dirID . '/' : '' ) . $name, 'name' => $name, 'dir' => $dirID, 'group' => $group,
                      'base' => $base, 'base_id' => ( $dirID !== '' ? $dirID . '/' : '' ) . $base,
                      'rotation' => $rotation, 'gz' => $gz, 'archive' => $group === self::GROUP_ARCHIVES,
                      'level' => $this->fileLevel( $name ), 'key' => $key,
                      'label' => $label !== '' ? self::translateLabel( $label ) : $base,
                      'labeled' => $label !== '',
                      'configured' => $ini !== null );
    }

    /**
     * The label of a log of the log directories by its key ([LogViewerSettings] LogLabel[<key>], e.g. LogLabel[404]
     * for var/log/404.log), or ''.
     *
     * @param string $key
     * @return string
     */
    public function labelOf( $key )
    {
        if ( $this->labels === null )
            $this->labels = $this->config->hash( 'LogViewerSettings', 'LogLabel' );
        $key = (string)$key;
        return isset( $this->labels[$key] ) ? (string)$this->labels[$key] : '';
    }

    /**
     * A label of the settings in the language of the administration (context kernel/logviewer), when the kernel's
     * translation system is running; the label itself otherwise (tests, the command line before it starts).
     *
     * @param string $label
     * @return string
     */
    public static function translateLabel( $label )
    {
        if ( class_exists( 'ezpI18n', false ) && class_exists( 'eZTranslatorManager', false ) )
        {
            $translated = ezpI18n::tr( 'kernel/logviewer', $label );
            if ( is_string( $translated ) && $translated !== '' )
                return $translated;
        }
        return $label;
    }

    /**
     * The key of a log in the permalinks (doc/en/us/features/6.0/log-viewer.md, "Permalinks"). Pure.
     *  - a log configured in LogList[] (directory id "@<key>"): its key;
     *  - var/log/<name>.log: <name>, e.g. error, debug, expmcp;
     *  - var/<site>/log/<name>.log: <site>-<name>, e.g. site-storage;
     *  - any other allowed directory: the directory with - for /, then -<name>.
     * Characters other than letters, digits, . _ - become -.
     *
     * @param string $dirID
     * @param string $base the current file's name
     * @return string
     */
    public static function keyFor( $dirID, $base )
    {
        if ( $dirID !== '' && $dirID[0] === '@' )
            return substr( $dirID, 1 );
        $name = preg_replace( '/\.log$/', '', (string)$base );
        if ( $dirID === 'var/log' )
            $key = $name;
        else if ( preg_match( '#^var/([^/]+)/log$#', $dirID, $m ) )
            $key = $m[1] . '-' . $name;
        else
            $key = str_replace( '/', '-', $dirID ) . '-' . $name;
        $key = preg_replace( '/[^A-Za-z0-9._\-]+/', '-', $key );
        return ltrim( substr( $key, 0, 64 ), '.-_' );
    }

    /**
     * The logs configured by key ([LogViewerSettings] LogList[], LogPath[], LogLabel[], LogGroup[]): the web server's
     * logs and any other file outside the log directories. A key without a path (the default of the web server's
     * keys) or whose path is not a regular file is left out. The path is the administrator's choice in the settings,
     * so it may be outside the installation root; its rotations are the files <name>.N and <name>.N.gz beside it.
     *
     * @return array key => key, path (real), dir (real directory), base, label, group
     */
    public function configuredLogs()
    {
        if ( $this->configured !== null )
            return $this->configured;
        $out = array();
        $paths = $this->config->hash( 'LogViewerSettings', 'LogPath' );
        $labels = $this->config->hash( 'LogViewerSettings', 'LogLabel' );
        $groups = $this->config->hash( 'LogViewerSettings', 'LogGroup' );
        $known = array( self::GROUP_KERNEL, self::GROUP_SITE, self::GROUP_EXTENSIONS, self::GROUP_WEBSERVER );
        foreach ( $this->config->lst( 'LogViewerSettings', 'LogList' ) as $key )
        {
            if ( !preg_match( self::KEY_PATTERN, $key ) || !isset( $paths[$key] ) || trim( (string)$paths[$key] ) === '' )
                continue;
            $path = str_replace( '\\', '/', trim( (string)$paths[$key] ) );
            if ( strpos( $path, "\0" ) !== false )
                continue;
            if ( $path[0] !== '/' )
                $path = $this->config->root() . '/' . $path;
            $real = realpath( $path );
            if ( $real === false || !is_file( $real ) )
                continue;
            $real = str_replace( '\\', '/', $real );
            $group = isset( $groups[$key] ) && in_array( trim( (string)$groups[$key] ), $known, true ) ? trim( (string)$groups[$key] ) : self::GROUP_WEBSERVER;
            $out[$key] = array( 'key' => $key, 'path' => $real, 'dir' => dirname( $real ), 'base' => basename( $real ),
                                'label' => isset( $labels[$key] ) ? trim( (string)$labels[$key] ) : '', 'group' => $group );
        }
        return $this->configured = $out;
    }

    /**
     * A file of a configured log: "@<key>/<name>", where <name> is the log's file or one of its rotations.
     *
     * @param string $id
     * @return array|null as resolve()
     */
    protected function resolveConfigured( $id )
    {
        if ( !preg_match( '#^@([^/]+)/([^/]+)$#', $id, $m ) )
            return null;
        $logs = $this->configuredLogs();
        if ( !isset( $logs[$m[1]] ) )
            return null;
        $log = $logs[$m[1]];
        $name = $m[2];
        if ( !self::isRotationName( $log['base'], $name ) )
            return null;
        $path = $log['dir'] . '/' . $name;
        if ( !is_file( $path ) || !is_readable( $path ) )
            return null;
        // a rotation that is a link leading out of the log's directory is not the log's
        $target = realpath( $path );
        if ( $target === false || dirname( str_replace( '\\', '/', $target ) ) !== $log['dir'] )
            return null;
        $stat = @stat( $path );
        if ( $stat === false )
            return null;
        $info = $this->describe( '@' . $log['key'], $name );
        $info['path'] = $path;
        $info['size'] = (int)$stat['size'];
        $info['mtime'] = (int)$stat['mtime'];
        $info['ino'] = (int)$stat['ino'];
        return $info;
    }

    /**
     * Whether a name is a log's own file or one of its rotations (<base>, <base>.N, <base>.N.gz, <base>.gz). Pure.
     *
     * @param string $base
     * @param string $name
     * @return bool
     */
    public static function isRotationName( $base, $name )
    {
        return $name === $base || $name === $base . '.gz' || preg_match( '/^' . preg_quote( $base, '/' ) . '\.\d{1,4}(\.gz)?$/', $name ) === 1;
    }

    /**
     * The file of a log key and rotation (0: the current file), when the viewer may open it.
     *
     * @param string|int $key an integer is a key of digits only (404) as PHP gives it from an array key
     * @param int $rotation
     * @return array|null as resolve()
     */
    public function resolveKey( $key, $rotation = 0 )
    {
        if ( is_int( $key ) )
            $key = (string)$key;
        if ( !is_string( $key ) || !preg_match( self::KEY_PATTERN, $key ) )
            return null;
        $rotation = (int)$rotation;
        $found = null;
        foreach ( $this->listFiles() as $list )
        {
            foreach ( $list as $info )
            {
                if ( $info['key'] !== $key || $info['rotation'] !== $rotation )
                    continue;
                // a plain rotation before a compressed one of the same number
                if ( $found === null || ( $found['gz'] && !$info['gz'] ) )
                    $found = $info;
            }
        }
        return $found !== null ? $this->resolve( $found['id'] ) : null;
    }

    /** @return bool the name is one of KernelLogs[] */
    protected function isKernelName( $name )
    {
        foreach ( $this->config->lst( 'LogViewerSettings', 'KernelLogs' ) as $p )
            if ( fnmatch( $p, $name ) )
                return true;
        return false;
    }

    /** @return bool the directory is the global log directory or eZDebug's */
    protected function isKernelDirectory( $dirID )
    {
        return $dirID === 'var/log' || $dirID === $this->logDir;
    }

    /** @return bool the directory is a site's own log directory (var/<site>/log), not the global one */
    protected function isSiteDirectory( $dirID )
    {
        return $dirID !== 'var/log' && preg_match( '#^var/[^/]+/log$#', $dirID ) === 1;
    }

    /**
     * The level of a file's lines that do not name one (FileLevels[]), or ''.
     *
     * @param string $name
     * @return string
     */
    public function fileLevel( $name )
    {
        foreach ( $this->config->lst( 'LogViewerSettings', 'FileLevels' ) as $rule )
        {
            $parts = explode( ';', $rule, 2 );
            if ( count( $parts ) === 2 && fnmatch( trim( $parts[0] ), $name ) && in_array( trim( $parts[1] ), expLogParser::$levels, true ) )
                return trim( $parts[1] );
        }
        return '';
    }

    /**
     * Every file the viewer may open, grouped: kernel, site, extensions, archives (each group sorted; the archives
     * after their file, by rotation number). One scandir() per directory and one stat() per file.
     *
     * @return array group => list of file info (as resolve(), without path)
     */
    public function listFiles()
    {
        if ( $this->listing !== null )
            return $this->listing;
        $groups = array( self::GROUP_KERNEL => array(), self::GROUP_SITE => array(), self::GROUP_EXTENSIONS => array(), self::GROUP_WEBSERVER => array(),
                         self::GROUP_ARCHIVES => array() );
        $seen = array();
        foreach ( $this->directories() as $real => $dirID )
        {
            $names = @scandir( $real );
            if ( !is_array( $names ) )
                continue;
            foreach ( $names as $name )
            {
                if ( !$this->nameAllowed( $name ) )
                    continue;
                $path = $real . '/' . $name;
                if ( is_link( $path ) )
                {
                    $target = realpath( $path );
                    if ( $target === false || !isset( $this->directories[str_replace( '\\', '/', dirname( $target ) )] ) )
                        continue;
                }
                $stat = @stat( $path );
                if ( $stat === false || ( $stat['mode'] & 0170000 ) !== 0100000 )
                    continue;
                $info = $this->describe( $dirID, $name );
                if ( isset( $seen[$info['id']] ) )
                    continue;
                $seen[$info['id']] = true;
                $info['size'] = (int)$stat['size'];
                $info['mtime'] = (int)$stat['mtime'];
                $info['ino'] = (int)$stat['ino'];
                $info['readable'] = is_readable( $path );
                $groups[$info['group']][] = $info;
            }
        }
        // the logs configured by key (the web server's ...): the file and its rotations beside it
        foreach ( $this->configuredLogs() as $key => $log )
        {
            $names = @scandir( $log['dir'] );
            if ( !is_array( $names ) )
                continue;
            foreach ( $names as $name )
            {
                if ( !self::isRotationName( $log['base'], $name ) )
                    continue;
                $path = $log['dir'] . '/' . $name;
                $target = realpath( $path );
                if ( $target === false || dirname( str_replace( '\\', '/', $target ) ) !== $log['dir'] )
                    continue;
                $stat = @stat( $path );
                if ( $stat === false || ( $stat['mode'] & 0170000 ) !== 0100000 )
                    continue;
                $info = $this->describe( '@' . $key, $name );
                $info['size'] = (int)$stat['size'];
                $info['mtime'] = (int)$stat['mtime'];
                $info['ino'] = (int)$stat['ino'];
                $info['readable'] = is_readable( $path );
                $groups[$info['group']][] = $info;
            }
        }
        $kernelOrder = array_flip( $this->config->lst( 'LogViewerSettings', 'KernelLogs' ) );
        foreach ( $groups as $group => &$list )
        {
            usort( $list, function ( $a, $b ) use ( $group, $kernelOrder ) {
                if ( $group === expLogFileLocator::GROUP_KERNEL )
                {
                    $ka = isset( $kernelOrder[$a['name']] ) ? $kernelOrder[$a['name']] : 1000;
                    $kb = isset( $kernelOrder[$b['name']] ) ? $kernelOrder[$b['name']] : 1000;
                    if ( $ka !== $kb )
                        return $ka < $kb ? -1 : 1;
                }
                $c = strcmp( $a['dir'] . '/' . $a['base'], $b['dir'] . '/' . $b['base'] );
                if ( $c !== 0 )
                    return $c;
                if ( $a['rotation'] !== $b['rotation'] )
                    return $a['rotation'] < $b['rotation'] ? -1 : 1;
                return strcmp( $a['name'], $b['name'] );
            } );
        }
        unset( $list );
        return $this->listing = $groups;
    }

    /**
     * The rotations of a file that exist (.1 .. .N and .gz), oldest last, as resolve() describes them.
     *
     * @param array $file a resolved file
     * @return array
     */
    public function rotationsOf( array $file )
    {
        $out = array();
        $real = dirname( $file['path'] );
        $names = @scandir( $real );
        if ( !is_array( $names ) )
            return $out;
        foreach ( $names as $name )
        {
            if ( $name === $file['name'] || strncmp( $name, $file['base'] . '.', strlen( $file['base'] ) + 1 ) !== 0 )
                continue;
            $info = $this->describe( $file['dir'], $name );
            if ( $info['base'] !== $file['base'] || !$info['archive'] )
                continue;
            $resolved = $this->resolve( $info['id'] );
            if ( $resolved !== null )
                $out[] = $resolved;
        }
        usort( $out, function ( $a, $b ) {
            $ra = $a['rotation'] < 0 ? PHP_INT_MAX : $a['rotation'];
            $rb = $b['rotation'] < 0 ? PHP_INT_MAX : $b['rotation'];
            return $ra === $rb ? strcmp( $a['name'], $b['name'] ) : ( $ra < $rb ? -1 : 1 );
        } );
        return $out;
    }

    /**
     * The file opened when the page names none: DefaultFile[] when allowed, else the first kernel log, else the
     * first file listed.
     *
     * @return string|null an id
     */
    public function defaultID()
    {
        $default = (string)$this->config->get( 'LogViewerSettings', 'DefaultFile' );
        if ( $default !== '' && $this->resolve( $default ) !== null )
            return $default;
        foreach ( $this->listFiles() as $list )
            foreach ( $list as $info )
                if ( !$info['archive'] && $info['readable'] )
                    return $info['id'];
        return null;
    }
}
