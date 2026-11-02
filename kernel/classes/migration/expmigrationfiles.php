<?php
/**
 * File containing the expMigrationFiles class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The code and design files a migration writes (the step type code_file, the second stage of open question 28: the
 * first stage is git only). Every write goes through here:
 *
 *   - only below the allowed roots of migration.ini [FileStepSettings] AllowedRootList[] (design and extension by
 *     default), never a root itself, never .., an absolute path, a URL, a .git folder, settings/override or a link
 *     that leads out of the root
 *   - the file as it was is copied first to <root>/var/backup/migration/files/<migration id>/<path> (readable by its
 *     owner only), and the change is listed in <root>/var/backup/migration/files/<migration id>.json: the path,
 *     whether the file existed, its SHA-256 before and after, its mode
 *   - the write is atomic (a file next to it, then renamed), with the mode of the file it replaces (a new file: the
 *     installation's file mode); as root, owner and group of the folder it is in
 *
 * restore() puts one change back: only while the file is still the one the migration wrote (its SHA-256 is the one
 * after the change), the old content verified by its SHA-256 afterwards, or the file removed when the migration made
 * it. The runner puts back the changes of a migration that fails (expMigrationContext::takeFileChanges()), the step's
 * revert() those of a migration that is rolled back.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationFiles
{
    /** The folder of the backups and change lists, below the installation. */
    const BACKUP_DIRECTORY = 'var/backup/migration/files';

    /** The allowed roots when migration.ini names none. */
    const DEFAULT_ROOTS = array( 'design', 'extension' );

    /** Path parts that are never written, wherever they are. */
    const DENIED_PARTS = array( '.git', '.svn', '.hg' );

    /** Paths below a root that are never written (secrets, the installation's own settings). */
    const DENIED_PATHS = array( 'settings/override', 'var', 'vendor' );

    /**
     * The allowed roots: [FileStepSettings] AllowedRootList[] of migration.ini, relative to the installation, without
     * a slash at either end. An empty list allows nothing (the step type is switched off).
     *
     * @param eZINI|null $ini
     * @return string[]
     */
    public static function allowedRoots( $ini = null )
    {
        $ini = $ini === null ? eZINI::instance( 'migration.ini' ) : $ini;
        if ( !$ini->hasVariable( 'FileStepSettings', 'AllowedRootList' ) )
            return self::DEFAULT_ROOTS;
        $roots = array();
        foreach ( (array)$ini->variable( 'FileStepSettings', 'AllowedRootList' ) as $root )
        {
            $root = trim( str_replace( '\\', '/', (string)$root ), "/ \t" );
            if ( $root !== '' && self::shapeProblem( $root ) === null && !in_array( $root, $roots, true ) )
                $roots[] = $root;
        }
        return $roots;
    }

    /**
     * The problem of a path a step writes, or null: relative to the installation, below one of the roots. Pure.
     *
     * @param string $path
     * @param string[] $roots allowedRoots()
     * @return string|null
     */
    public static function pathProblem( $path, array $roots )
    {
        $path = trim( (string)$path );
        $problem = self::shapeProblem( $path );
        if ( $problem !== null )
            return $problem;
        $path = self::normalize( $path );
        if ( !$roots )
            return 'no file can be written: migration.ini [FileStepSettings] AllowedRootList[] names no folder';
        $root = self::rootOf( $path, $roots );
        if ( $root === null )
            return "$path is not below a folder files may be written to (" . implode( ', ', $roots ) . ')';
        if ( $root === $path )
            return "$path is the folder itself: name a file below it";
        foreach ( explode( '/', $path ) as $part )
            if ( in_array( $part, self::DENIED_PARTS, true ) )
                return "$path is in a $part folder, which a migration never writes";
        foreach ( self::DENIED_PATHS as $denied )
            if ( $path === $denied || strpos( $path, $denied . '/' ) === 0 || strpos( $path, '/' . $denied . '/' ) !== false )
                return "$path is below $denied, which a migration never writes";
        return null;
    }

    /**
     * The problem of the shape of a path: empty, a URL, absolute, .. Pure.
     *
     * @param string $path
     * @return string|null
     */
    protected static function shapeProblem( $path )
    {
        if ( $path === '' )
            return 'a path below the installation (design/mysite/templates/page.tpl)';
        if ( strpos( $path, "\0" ) !== false || preg_match( '#^[a-z][a-z0-9+.-]*:#i', $path ) )
            return "$path is no path (a URL or a stream is not written)";
        if ( $path[0] === '/' || $path[0] === '\\' || preg_match( '#^[A-Za-z]:#', $path ) )
            return "$path is absolute: name the file by its path below the installation";
        foreach ( preg_split( '#[/\\\\]+#', $path ) as $part )
            if ( $part === '..' )
                return "$path leaves the installation (..)";
        return null;
    }

    /**
     * A path with / only, no ./ and no slash at the end. Pure.
     *
     * @param string $path
     * @return string
     */
    public static function normalize( $path )
    {
        $parts = array();
        foreach ( preg_split( '#[/\\\\]+#', trim( (string)$path ) ) as $part )
            if ( $part !== '' && $part !== '.' )
                $parts[] = $part;
        return implode( '/', $parts );
    }

    /**
     * The root a path is below (or is), or null. Pure.
     *
     * @param string $path normalized
     * @param string[] $roots
     * @return string|null
     */
    public static function rootOf( $path, array $roots )
    {
        foreach ( $roots as $root )
            if ( $path === $root || strpos( $path, $root . '/' ) === 0 )
                return $root;
        return null;
    }

    /**
     * The file of a path on disk, checked: the path is allowed, and the nearest folder that exists is really below
     * the root (no link leads out of it); the file itself, when it exists, is no link and no folder.
     *
     * @param string $path relative to the installation
     * @param string $installation the installation's root
     * @param string[] $roots
     * @return string the absolute path
     * @throws RuntimeException
     */
    public static function resolve( $path, $installation, array $roots )
    {
        $problem = self::pathProblem( $path, $roots );
        if ( $problem !== null )
            throw new RuntimeException( "path: $problem" );
        $path = self::normalize( $path );
        $installation = rtrim( (string)$installation, '/' );
        $rootReal = realpath( $installation . '/' . self::rootOf( $path, $roots ) );
        if ( $rootReal === false || !is_dir( $rootReal ) )
            throw new RuntimeException( 'path: the folder ' . self::rootOf( $path, $roots ) . ' is not in the installation' );
        $file = $installation . '/' . $path;
        $dir = dirname( $file );
        while ( !is_dir( $dir ) && strlen( $dir ) > strlen( $installation ) )
            $dir = dirname( $dir );
        $dirReal = realpath( $dir );
        if ( $dirReal === false || ( $dirReal !== $rootReal && strpos( $dirReal, $rootReal . '/' ) !== 0 ) )
            throw new RuntimeException( "path: $path leads out of " . self::rootOf( $path, $roots ) . ' (a link)' );
        if ( is_link( $file ) )
            throw new RuntimeException( "path: $path is a link; a migration writes files, not links" );
        if ( is_dir( $file ) )
            throw new RuntimeException( "path: $path is a folder" );
        return $file;
    }

    /**
     * The SHA-256 of a file, or null when there is none.
     *
     * @param string $file
     * @return string|null
     */
    public static function checksum( $file )
    {
        clearstatcache( true, $file );
        if ( !is_file( $file ) )
            return null;
        $hash = hash_file( 'sha256', $file );
        return $hash === false ? null : $hash;
    }

    /**
     * The change list of a migration: path => change.
     *
     * @param string $installation
     * @param string $migrationId
     * @return array[]
     */
    public static function changes( $installation, $migrationId )
    {
        $file = self::listFile( $installation, $migrationId );
        if ( !is_file( $file ) )
            return array();
        $data = json_decode( (string)@file_get_contents( $file ), true );
        return is_array( $data ) && isset( $data['files'] ) && is_array( $data['files'] ) ? $data['files'] : array();
    }

    /**
     * The file of the change list of a migration.
     *
     * @param string $installation
     * @param string $migrationId
     * @return string
     * @throws InvalidArgumentException for an id that is no file name
     */
    public static function listFile( $installation, $migrationId )
    {
        return self::backupFolder( $installation, $migrationId ) . '.json';
    }

    /**
     * The folder of the backups of a migration.
     *
     * @param string $installation
     * @param string $migrationId
     * @return string
     * @throws InvalidArgumentException
     */
    public static function backupFolder( $installation, $migrationId )
    {
        if ( !preg_match( '/^[A-Za-z0-9][A-Za-z0-9_.-]{0,149}$/', (string)$migrationId ) )
            throw new InvalidArgumentException( 'not a migration id: ' . $migrationId );
        return rtrim( (string)$installation, '/' ) . '/' . self::BACKUP_DIRECTORY . '/' . $migrationId;
    }

    /**
     * Writes (or, with $content null, removes) a file: the backup first, then the change list, then the file.
     *
     * @param string $installation
     * @param string $path relative to the installation, allowed
     * @param string|null $content the new content; null removes the file
     * @param string $migrationId
     * @param string[] $roots
     * @return array|null the change (path, existed, before, after, mode, backup), null when the file is so already
     * @throws RuntimeException
     */
    public static function write( $installation, $path, $content, $migrationId, array $roots )
    {
        $file = self::resolve( $path, $installation, $roots );
        $path = self::normalize( $path );
        $before = self::checksum( $file );
        $after = $content === null ? null : hash( 'sha256', (string)$content );
        if ( $before === $after )
            return null;
        $existed = $before !== null;
        $mode = $existed ? ( fileperms( $file ) & 0777 ) : 0664;
        $backup = '';
        if ( $existed )
        {
            $copy = self::backupFolder( $installation, $migrationId ) . '/' . $path;
            self::makeFolder( dirname( $copy ), 0700, $installation );
            if ( !@copy( $file, $copy ) || self::checksum( $copy ) !== $before )
                throw new RuntimeException( "$path could not be backed up to " . self::relative( $copy, $installation ) . '; nothing written' );
            @chmod( $copy, eZFile::fileMode( 0600 ) );
            $backup = self::relative( $copy, $installation );
        }
        $change = array( 'path' => $path, 'existed' => $existed, 'before' => $before, 'after' => $after, 'mode' => sprintf( '%04o', $mode ),
                         'backup' => $backup, 'at' => time() );
        $changes = self::changes( $installation, $migrationId );
        $changes[$path] = $change;
        self::writeList( $installation, $migrationId, $changes );
        if ( $content === null )
        {
            if ( !@unlink( $file ) )
                throw new RuntimeException( "$path could not be removed" );
        }
        else
            self::writeAtomic( $file, (string)$content, $mode, $installation );
        if ( self::checksum( $file ) !== $after )
            throw new RuntimeException( "$path is not what was written (SHA-256)" );
        return $change;
    }

    /**
     * Puts one change back: the old file from its backup, or the new file removed.
     *
     * @param string $installation
     * @param array $change write()
     * @param bool $force true: also when the file changed since the migration wrote it
     * @return string what was done
     * @throws RuntimeException
     */
    public static function restore( $installation, array $change, $force = false )
    {
        $path = (string)$change['path'];
        $installation = rtrim( (string)$installation, '/' );
        $file = $installation . '/' . self::normalize( $path );
        if ( is_link( $file ) || is_dir( $file ) )
            throw new RuntimeException( "$path is a link or a folder now; nothing put back" );
        $now = self::checksum( $file );
        if ( !$force && $now !== $change['after'] )
            throw new RuntimeException( "$path changed after the migration wrote it (SHA-256 " . ( $now === null ? 'none: removed' : substr( $now, 0, 12 ) )
                                        . ', written ' . ( $change['after'] === null ? 'none: removed' : substr( (string)$change['after'], 0, 12 ) ) . '); nothing put back' );
        if ( empty( $change['existed'] ) )
        {
            if ( $now !== null && !@unlink( $file ) )
                throw new RuntimeException( "$path could not be removed" );
            return "removed $path again (the migration made it)";
        }
        $copy = $installation . '/' . (string)$change['backup'];
        if ( (string)$change['backup'] === '' || self::checksum( $copy ) !== $change['before'] )
            throw new RuntimeException( "the backup of $path is missing or not the old file (SHA-256); nothing put back" );
        $mode = isset( $change['mode'] ) && preg_match( '/^0?[0-7]{3,4}$/', (string)$change['mode'] ) ? octdec( (string)$change['mode'] ) : 0664;
        self::makeFolder( dirname( $file ), 0775, $installation );
        self::writeAtomic( $file, (string)file_get_contents( $copy ), $mode, $installation );
        if ( self::checksum( $file ) !== $change['before'] )
            throw new RuntimeException( "$path is not the old file after it was put back (SHA-256)" );
        return "put back $path from " . $change['backup'];
    }

    /**
     * Writes the change list of a migration (atomically, readable by its owner only).
     *
     * @param string $installation
     * @param string $migrationId
     * @param array[] $changes
     * @throws RuntimeException
     */
    protected static function writeList( $installation, $migrationId, array $changes )
    {
        $file = self::listFile( $installation, $migrationId );
        self::makeFolder( dirname( $file ), 0700, $installation );
        $json = json_encode( array( 'schema' => 'exp-migration-files/1', 'migration' => $migrationId, 'files' => $changes ),
                             JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        self::writeAtomic( $file, $json . "\n", 0600, $installation );
    }

    /**
     * Writes a file atomically with a mode; as root, the owner and group of its folder.
     *
     * @param string $file
     * @param string $content
     * @param int $mode
     * @param string $installation
     * @throws RuntimeException
     */
    protected static function writeAtomic( $file, $content, $mode, $installation )
    {
        $dir = dirname( $file );
        self::makeFolder( $dir, 0775, $installation );
        $temp = $dir . '/.' . basename( $file ) . '.' . getmypid() . '.tmp';
        if ( @file_put_contents( $temp, $content ) !== strlen( $content ) )
        {
            @unlink( $temp );
            throw new RuntimeException( self::relative( $file, $installation ) . ' could not be written' );
        }
        @chmod( $temp, eZFile::fileMode( $mode ) );
        self::ownLike( $temp, $dir );
        if ( !@rename( $temp, $file ) )
        {
            @unlink( $temp );
            throw new RuntimeException( self::relative( $file, $installation ) . ' could not be written (rename)' );
        }
        clearstatcache( true, $file );
    }

    /**
     * Makes a folder and its missing parents, each owned (as root) like the nearest folder that exists.
     *
     * @param string $dir
     * @param int $mode
     * @param string $installation
     * @throws RuntimeException
     */
    protected static function makeFolder( $dir, $mode, $installation )
    {
        if ( is_dir( $dir ) )
            return;
        $missing = array();
        for ( $d = $dir; !is_dir( $d ); $d = dirname( $d ) )
        {
            if ( dirname( $d ) === $d )
                break;
            $missing[] = $d;
        }
        $parent = isset( $d ) ? $d : dirname( $dir );
        foreach ( array_reverse( $missing ) as $one )
        {
            if ( !@mkdir( $one, eZDir::dirMode( $mode ) ) && !is_dir( $one ) )
                throw new RuntimeException( 'the folder ' . self::relative( $one, $installation ) . ' could not be made' );
            @chmod( $one, eZDir::dirMode( $mode ) );
            self::ownLike( $one, $parent );
        }
    }

    /**
     * As root: gives a file the owner and group of a folder (a later run as the site's user can still write it).
     *
     * @param string $path
     * @param string $like
     */
    protected static function ownLike( $path, $like )
    {
        if ( !function_exists( 'posix_geteuid' ) || posix_geteuid() !== 0 )
            return;
        $uid = @fileowner( $like );
        $gid = @filegroup( $like );
        if ( $uid !== false && $gid !== false )
        {
            @chown( $path, $uid );
            @chgrp( $path, $gid );
        }
    }

    /**
     * A path relative to the installation, for messages. Pure.
     *
     * @param string $file
     * @param string $installation
     * @return string
     */
    public static function relative( $file, $installation )
    {
        $installation = rtrim( (string)$installation, '/' ) . '/';
        return strpos( (string)$file, $installation ) === 0 ? substr( (string)$file, strlen( $installation ) ) : (string)$file;
    }
}
