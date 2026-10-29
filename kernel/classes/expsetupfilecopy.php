<?php
/**
 * File containing the expSetupFileCopy class.
 *
 * Puts the files an installation ships as templates into place, as administrators do with "cp -va":
 *
 *   .htaccess_root          -> .htaccess    (exp:setup:htaccess)
 *   .htaccess_root_static   -> .htaccess    (exp:setup:htaccess --static)
 *   config.php-RECOMMENDED  -> config.php   (exp:setup:config)
 *
 * and writes a changed file safely for exp:config:set and exp:config:unset. The rules, the same for every write:
 *
 *  - An existing target is never replaced silently: plan() says whether it is missing, the same or different
 *    (with a diff), and a different one is replaced only with force.
 *  - An existing target is backed up first, to var/backup/<kind>/<YYYYMMDD-HHMMSS>/<name>, with its mode, and
 *    with its owner and group when run as root. The backup directories are created with mode 0700: config.php
 *    can hold passwords, and var/backup is not served by the shipped .htaccess.
 *  - The new content goes to a temporary file in the same directory, which gets its mode and owner and is then
 *    renamed over the target, so the web server never reads half a file.
 *  - Mode: an existing target keeps its mode, a new one gets the mode of its template (as cp -a does), unless a mode
 *    is given (--mode). Owner and group: an existing target keeps them; a new one, when run as root, gets the
 *    owner and group of its template (cp -a), or of the installation directory when the template is root's, or
 *    the ones given (--owner=user[:group]). Run as another user, files belong to that user and --owner is refused
 *    unless it names that user. The modification time is the time of the write, not the template's: a replaced
 *    config.php must look changed to OPcache.
 *
 * Guide: doc/eng-us/guides/setup-files-from-the-console.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expSetupFileCopy
{
    /** The files that can be put into place: name => source, static source, target, backup kind. */
    const FILES = array(
        'htaccess' => array( 'source' => '.htaccess_root', 'static' => '.htaccess_root_static', 'target' => '.htaccess' ),
        'config' => array( 'source' => 'config.php-RECOMMENDED', 'static' => null, 'target' => 'config.php' ),
    );

    /** Where backups go, below the installation root. */
    const BACKUP_DIR = 'var/backup';

    /**
     * The installation root: the given directory, else the directory of this kernel.
     *
     * @param string|null $root
     * @return string without a trailing slash
     */
    public static function root( $root = null )
    {
        if ( $root !== null && $root !== '' )
        {
            $real = realpath( $root );
            return $real !== false ? $real : rtrim( $root, '/' );
        }
        $real = realpath( __DIR__ . '/../..' );
        return $real !== false ? $real : rtrim( getcwd(), '/' );
    }

    /**
     * What putting a source into place would do.
     *
     * @param string $root
     * @param string $source relative to the root
     * @param string $target relative to the root
     * @return array state ('missing-source', 'new', 'same', 'differs', 'not-a-file'), source, target (absolute paths),
     *               diff (for 'differs', from the target to the source, as diff -u)
     */
    public static function plan( $root, $source, $target )
    {
        $sourcePath = $root . '/' . $source;
        $targetPath = $root . '/' . $target;
        $plan = array( 'source' => $sourcePath, 'target' => $targetPath, 'state' => 'new', 'diff' => '' );
        if ( !is_file( $sourcePath ) || !is_readable( $sourcePath ) )
        {
            $plan['state'] = 'missing-source';
            return $plan;
        }
        if ( is_link( $targetPath ) || ( file_exists( $targetPath ) && !is_file( $targetPath ) ) )
        {
            $plan['state'] = 'not-a-file';
            return $plan;
        }
        if ( !file_exists( $targetPath ) )
            return $plan;
        $old = (string)file_get_contents( $targetPath );
        $new = (string)file_get_contents( $sourcePath );
        if ( $old === $new )
        {
            $plan['state'] = 'same';
            return $plan;
        }
        $plan['state'] = 'differs';
        $plan['diff'] = expIniEditor::unifiedDiff( $old, $new, $target, $source );
        return $plan;
    }

    /**
     * Puts a source into place, for a plan whose state is 'new', or 'differs' with force (checked by the caller).
     *
     * @param string $root
     * @param array $plan plan()
     * @param string $kind the backup kind (setup)
     * @param array $options mode (an octal string or null), owner ("user[:group]" or null)
     * @return array ok, backup (the path or ''), mode (int), owner, group (names), message (when not ok)
     */
    public static function apply( $root, array $plan, $kind, array $options = array() )
    {
        $content = file_get_contents( $plan['source'] );
        if ( $content === false )
            return array( 'ok' => false, 'message' => 'cannot read ' . $plan['source'] );
        return self::write( $root, $plan['target'], $content, $kind, $options + array( 'template' => $plan['source'] ) );
    }

    /**
     * Writes content to a file safely: backup of an existing file, temporary file in the same directory with the
     * mode and owner, an optional check of the temporary file, rename.
     *
     * @param string $root the installation root (for the backup directory)
     * @param string $target the absolute path
     * @param string $content
     * @param string $kind the backup kind: setup, config
     * @param array $options mode (octal string), owner ("user[:group]"), template (the file a new target takes its
     *                       mode and owner from), check (callable( $tmpFile ): null or an error message), stamp (the
     *                       backup directory name, for tests)
     * @return array ok, backup, mode, owner, group, message
     */
    public static function write( $root, $target, $content, $kind, array $options = array() )
    {
        $existed = is_file( $target );
        $owner = self::ownership( $target, $existed, $root, $options );
        if ( isset( $owner['error'] ) )
            return array( 'ok' => false, 'message' => $owner['error'] );
        $mode = self::targetMode( $target, $existed, $options );
        if ( is_string( $mode ) )
            return array( 'ok' => false, 'message' => $mode );

        $backup = '';
        if ( $existed )
        {
            $backup = self::backup( $root, $target, $kind, isset( $options['stamp'] ) ? $options['stamp'] : null );
            if ( $backup === null )
                return array( 'ok' => false, 'message' => 'cannot back up ' . $target . ' to ' . $root . '/' . self::BACKUP_DIR . '/' . $kind . '/: nothing was changed' );
        }

        $dir = dirname( $target );
        $tmp = $dir . '/.' . basename( $target ) . '.exp-' . getmypid() . '-' . mt_rand( 1000, 9999 ) . '.tmp';
        // created empty, made private, then filled: config.php may hold passwords, and no other user may read the
        // temporary file while it is written
        $written = false;
        $handle = @fopen( $tmp, 'x' );
        if ( $handle !== false )
        {
            @chmod( $tmp, \eZFile::fileMode( 0600 ) );
            $written = @fwrite( $handle, $content );
            fclose( $handle );
        }
        if ( $written === false || $written !== strlen( $content ) )
        {
            @unlink( $tmp );
            return array( 'ok' => false, 'message' => "cannot write a temporary file in $dir: nothing was changed", 'backup' => $backup );
        }
        @chmod( $tmp, \eZFile::fileMode( $mode ) );
        if ( $owner['uid'] !== null && self::isRoot() )
        {
            @chown( $tmp, $owner['uid'] );
            @chgrp( $tmp, $owner['gid'] );
        }
        if ( isset( $options['check'] ) && is_callable( $options['check'] ) )
        {
            $problem = call_user_func( $options['check'], $tmp );
            if ( $problem !== null )
            {
                @unlink( $tmp );
                return array( 'ok' => false, 'message' => $problem . ': nothing was changed', 'backup' => $backup );
            }
        }
        if ( !@rename( $tmp, $target ) )
        {
            @unlink( $tmp );
            return array( 'ok' => false, 'message' => "cannot rename the new file to $target: nothing was changed", 'backup' => $backup );
        }
        clearstatcache( true, $target );
        if ( function_exists( 'opcache_invalidate' ) )
            @opcache_invalidate( $target, true );
        $stat = @stat( $target );
        return array(
            'ok' => true,
            'backup' => $backup,
            'created' => !$existed,
            'mode' => $stat ? $stat['mode'] & 07777 : $mode,
            'owner' => $stat ? self::userName( $stat['uid'] ) : '',
            'group' => $stat ? self::groupName( $stat['gid'] ) : '',
        );
    }

    /**
     * Copies a file to var/backup/<kind>/<stamp>/<name> with its mode (and owner and group when run as root). The
     * directories are created with mode 0700 and, as root, belong to the file's owner.
     *
     * @param string $root
     * @param string $file
     * @param string $kind
     * @param string|null $stamp the directory name (default: the time, YYYYMMDD-HHMMSS, with -2, -3 ... if taken)
     * @return string|null the backup path, null when it could not be written
     */
    public static function backup( $root, $file, $kind, $stamp = null )
    {
        $base = $root . '/' . self::BACKUP_DIR . '/' . $kind;
        $stat = @stat( $file );
        if ( $stamp === null )
        {
            $stamp = date( 'Ymd-His' );
            $try = $stamp;
            for ( $i = 2; file_exists( $base . '/' . $try . '/' . basename( $file ) ); ++$i )
                $try = $stamp . '-' . $i;
            $stamp = $try;
        }
        $dir = $base . '/' . $stamp;
        // one directory at a time: var/ (if missing) as any directory of the installation, var/backup/ and below
        // with mode 0700; as root each new one belongs to the file's owner, so the site can still use var/backup/
        $path = $root;
        foreach ( array_merge( explode( '/', self::BACKUP_DIR ), array( $kind, $stamp ) ) as $i => $part )
        {
            $path .= '/' . $part;
            if ( is_dir( $path ) )
                continue;
            $mode = $i === 0 ? 0755 : 0700;
            $made = @mkdir( $path, \eZDir::dirMode( $mode ) );
            if ( !$made && !is_dir( $path ) )
                return null;
            @chmod( $path, \eZDir::dirMode( $mode ) );
            if ( $stat && self::isRoot() )
            {
                @chown( $path, $stat['uid'] );
                @chgrp( $path, $stat['gid'] );
            }
        }
        $path = $dir . '/' . basename( $file );
        if ( !@copy( $file, $path ) )
            return null;
        if ( $stat )
        {
            @chmod( $path, \eZFile::fileMode( $stat['mode'] & 07777 ) );
            if ( self::isRoot() )
            {
                @chown( $path, $stat['uid'] );
                @chgrp( $path, $stat['gid'] );
            }
            @touch( $path, $stat['mtime'] );
        }
        return $path;
    }

    /**
     * The mode of the written file: --mode, else the existing target's, else the template's, else 0644.
     *
     * @return int|string the mode, or an error message
     */
    protected static function targetMode( $target, $existed, array $options )
    {
        if ( isset( $options['mode'] ) && $options['mode'] !== null && $options['mode'] !== '' )
        {
            if ( !preg_match( '/^0?[0-7]{3,4}$/', (string)$options['mode'] ) )
                return '--mode=' . $options['mode'] . ' is not an octal mode such as 0644';
            $mode = octdec( (string)$options['mode'] );
            if ( ( $mode & 0600 ) !== 0600 )
                return '--mode=' . $options['mode'] . ' does not let the owner read and write the file';
            return $mode;
        }
        if ( $existed )
            return fileperms( $target ) & 07777;
        if ( isset( $options['template'] ) && is_file( $options['template'] ) )
            return fileperms( $options['template'] ) & 07777;
        return 0644;
    }

    /**
     * The owner and group of the written file (uid, gid), or null for "the running user".
     *
     * @return array uid, gid; or error
     */
    protected static function ownership( $target, $existed, $root, array $options )
    {
        $spec = isset( $options['owner'] ) ? (string)$options['owner'] : '';
        if ( $spec !== '' )
        {
            $parsed = self::parseOwner( $spec );
            if ( isset( $parsed['error'] ) )
                return $parsed;
            if ( !self::isRoot() )
            {
                $me = function_exists( 'posix_geteuid' ) ? posix_geteuid() : getmyuid();
                if ( $parsed['uid'] !== $me )
                    return array( 'error' => "--owner=$spec: only root can give a file to another user (running as " . self::userName( $me ) . ')' );
            }
            return $parsed;
        }
        if ( !self::isRoot() )
            return array( 'uid' => null, 'gid' => null );
        if ( $existed )
        {
            $stat = stat( $target );
            return array( 'uid' => $stat['uid'], 'gid' => $stat['gid'] );
        }
        // a new file: the template's owner (cp -a), or the installation directory's when the template is root's
        foreach ( array( isset( $options['template'] ) ? $options['template'] : '', $root ) as $from )
        {
            $stat = $from !== '' ? @stat( $from ) : false;
            if ( $stat && $stat['uid'] !== 0 )
                return array( 'uid' => $stat['uid'], 'gid' => $stat['gid'] );
        }
        return array( 'uid' => 0, 'gid' => 0 );
    }

    /**
     * "user", "user:group", "uid:gid" as ids.
     *
     * @param string $spec
     * @return array uid, gid; or error
     */
    public static function parseOwner( $spec )
    {
        $parts = explode( ':', (string)$spec, 2 );
        $user = $parts[0];
        $group = isset( $parts[1] ) ? $parts[1] : '';
        if ( $user === '' )
            return array( 'error' => "--owner=$spec names no user" );
        if ( ctype_digit( $user ) )
        {
            $uid = (int)$user;
            $info = function_exists( 'posix_getpwuid' ) ? posix_getpwuid( $uid ) : false;
            $gid = $info ? $info['gid'] : $uid;
        }
        else
        {
            $info = function_exists( 'posix_getpwnam' ) ? posix_getpwnam( $user ) : false;
            if ( !$info )
                return array( 'error' => "--owner=$spec: there is no user $user" );
            $uid = $info['uid'];
            $gid = $info['gid'];
        }
        if ( $group !== '' )
        {
            if ( ctype_digit( $group ) )
                $gid = (int)$group;
            else
            {
                $ginfo = function_exists( 'posix_getgrnam' ) ? posix_getgrnam( $group ) : false;
                if ( !$ginfo )
                    return array( 'error' => "--owner=$spec: there is no group $group" );
                $gid = $ginfo['gid'];
            }
        }
        return array( 'uid' => $uid, 'gid' => $gid );
    }

    /** @return bool the process runs as root */
    public static function isRoot()
    {
        return function_exists( 'posix_geteuid' ) && posix_geteuid() === 0;
    }

    /** @return string the name of a user id (the id when unknown) */
    public static function userName( $uid )
    {
        $info = function_exists( 'posix_getpwuid' ) ? @posix_getpwuid( (int)$uid ) : false;
        return $info ? $info['name'] : (string)$uid;
    }

    /** @return string the name of a group id (the id when unknown) */
    public static function groupName( $gid )
    {
        $info = function_exists( 'posix_getgrgid' ) ? @posix_getgrgid( (int)$gid ) : false;
        return $info ? $info['name'] : (string)$gid;
    }

    /**
     * A path below the root as it is written in messages: relative, else absolute.
     *
     * @param string $root
     * @param string $path
     * @return string
     */
    public static function relative( $root, $path )
    {
        return strpos( $path, $root . '/' ) === 0 ? substr( $path, strlen( $root ) + 1 ) : $path;
    }

    /**
     * Which template the current .htaccess is a copy of: 'root' (.htaccess_root), 'static' (.htaccess_root_static),
     * 'changed' (neither), 'missing'.
     *
     * @param string $root
     * @return string
     */
    public static function htaccessState( $root )
    {
        $target = $root . '/' . self::FILES['htaccess']['target'];
        if ( !is_file( $target ) )
            return 'missing';
        $content = (string)file_get_contents( $target );
        foreach ( array( 'root' => 'source', 'static' => 'static' ) as $state => $key )
        {
            $source = $root . '/' . self::FILES['htaccess'][$key];
            if ( is_file( $source ) && (string)file_get_contents( $source ) === $content )
                return $state;
        }
        return 'changed';
    }

    /**
     * A file checked with "php -l" (the PHP binary of this process when it is the command line one), else with the
     * parser of this process.
     *
     * @param string $file
     * @return string|null the error, null when the file compiles
     */
    public static function lint( $file )
    {
        $binary = defined( 'PHP_BINARY' ) && PHP_BINARY !== '' && PHP_SAPI === 'cli' ? PHP_BINARY : '';
        if ( $binary !== '' && function_exists( 'exec' ) && is_executable( $binary ) )
        {
            $output = array();
            $code = 0;
            @exec( escapeshellarg( $binary ) . ' -n -l ' . escapeshellarg( $file ) . ' 2>&1', $output, $code );
            if ( $code === 0 )
                return null;
            $text = trim( implode( ' ', array_filter( $output, function ( $l ) { return trim( $l ) !== '' && strpos( $l, 'Errors parsing' ) === false; } ) ) );
            return 'php -l: ' . ( $text !== '' ? str_replace( $file, basename( $file ), $text ) : 'exit code ' . $code );
        }
        $error = expConfigPhpEditor::syntaxError( (string)file_get_contents( $file ) );
        return $error === null ? null : 'syntax error: ' . $error;
    }
}
