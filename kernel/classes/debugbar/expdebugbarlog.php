<?php
/**
 * File containing the expDebugBarLog class: the log of every change the Exp Debug bar made.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * One JSON object per line in var/<site>/log/debugbar.log ([DebugBarSettings] LogFile in debugbar.ini): who
 * (user id, login, address), what (setting, file, block, variable, scope, path), old -> new, the backup, and what
 * the undo needs (sha1 of the file after the write, what the write created). The log is only appended to; an undo
 * is an entry of its own whose "undoes" names the entry it reverted.
 *
 * A file the log creates gets the owner and group of its directory (the site user), mode 0640, so a write made as
 * root on the command line never leaves a log PHP-FPM cannot append to.
 */
class expDebugBarLog
{
    /** @var string Absolute or root-relative path */
    protected $path;

    /** @var array|null Entries as read, oldest first */
    protected $cache = null;

    /**
     * @param string|null $path null: <var dir>/<log dir>/<LogFile of debugbar.ini>
     */
    public function __construct( $path = null )
    {
        $this->path = $path !== null ? $path : self::defaultPath();
    }

    /** @return string The log of this installation */
    public static function defaultPath()
    {
        $name = 'debugbar.log';
        if ( class_exists( 'eZINI' ) )
        {
            $ini = eZINI::instance( 'debugbar.ini' );
            if ( $ini->hasVariable( 'DebugBarSettings', 'LogFile' ) && basename( $ini->variable( 'DebugBarSettings', 'LogFile' ) ) !== '' )
                $name = basename( $ini->variable( 'DebugBarSettings', 'LogFile' ) );
            // The log directory of the site; site.ini [FileSettings] LogDir may be an absolute path
            return eZSys::logDirectory() . '/' . $name;
        }
        return 'var/log/' . $name;
    }

    /** @return string */
    public function path()
    {
        return $this->path;
    }

    /** A new entry id: <Ymd-His>-<6 hex>. */
    public static function newId()
    {
        return date( 'Ymd-His' ) . '-' . bin2hex( random_bytes( 3 ) );
    }

    /**
     * Who is acting: user id and login (null on the command line without a user) and the address.
     *
     * @return array user_id, user, ip
     */
    public static function actor()
    {
        $actor = array( 'user_id' => null, 'user' => null, 'ip' => null );
        if ( class_exists( 'eZUser' ) )
        {
            try
            {
                $user = eZUser::currentUser();
                if ( $user instanceof eZUser )
                {
                    $actor['user_id'] = (int)$user->attribute( 'contentobject_id' );
                    $actor['user'] = (string)$user->attribute( 'login' );
                }
            }
            catch ( Throwable $e )
            {
            }
        }
        if ( class_exists( 'eZSys' ) )
        {
            $ip = eZSys::clientIP();
            $actor['ip'] = $ip ? (string)$ip : ( eZSys::isShellExecution() ? 'commandline' : null );
        }
        return $actor;
    }

    /**
     * Appends an entry. id, time, user_id, user and ip are filled in when missing.
     *
     * @param array $entry
     * @return array The entry as written
     * @throws RuntimeException when the log cannot be written
     */
    public function append( array $entry )
    {
        $entry += array( 'id' => self::newId(), 'time' => date( 'c' ) ) + self::actor();
        $line = json_encode( $entry, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR ) . "\n";
        $dir = dirname( $this->path );
        if ( !is_dir( $dir ) )
        {
            if ( !@mkdir( $dir, eZDir::dirMode( 0775 ), true ) && !is_dir( $dir ) )
                throw new RuntimeException( "The debug bar log directory $dir cannot be created" );
            self::ownLikeParent( $dir );
        }
        $created = !is_file( $this->path );
        if ( @file_put_contents( $this->path, $line, FILE_APPEND | LOCK_EX ) === false )
            throw new RuntimeException( "The debug bar log {$this->path} cannot be written" );
        if ( $created )
        {
            @chmod( $this->path, eZFile::fileMode( 0640 ) );
            self::ownLikeParent( $this->path );
        }
        if ( $this->cache !== null )
            $this->cache[] = $entry;
        return $entry;
    }

    /** Gives a path the owner and group of its directory, when running as root. */
    protected static function ownLikeParent( $path )
    {
        if ( !function_exists( 'posix_geteuid' ) || posix_geteuid() !== 0 )
            return;
        $parent = dirname( $path );
        $uid = @fileowner( $parent );
        $gid = @filegroup( $parent );
        if ( $uid !== false )
            @chown( $path, $uid );
        if ( $gid !== false )
            @chgrp( $path, $gid );
    }

    /**
     * Every entry, oldest first, each with undone_by (the id of the undo entry, or null).
     *
     * @return array[]
     */
    public function all()
    {
        if ( $this->cache !== null )
            return self::markUndone( $this->cache );
        $entries = array();
        if ( is_file( $this->path ) )
        {
            $fh = @fopen( $this->path, 'r' );
            if ( $fh )
            {
                while ( ( $line = fgets( $fh ) ) !== false )
                {
                    $e = json_decode( $line, true );
                    if ( is_array( $e ) && isset( $e['id'] ) )
                        $entries[] = $e;
                }
                fclose( $fh );
            }
        }
        $this->cache = $entries;
        return self::markUndone( $entries );
    }

    /** Forgets what was read (another process may have appended). */
    public function reset()
    {
        $this->cache = null;
    }

    protected static function markUndone( array $entries )
    {
        $undone = array();
        foreach ( $entries as $e )
        {
            if ( !empty( $e['undoes'] ) )
                $undone[$e['undoes']] = $e['id'];
        }
        foreach ( $entries as $i => $e )
            $entries[$i]['undone_by'] = isset( $undone[$e['id']] ) ? $undone[$e['id']] : null;
        return $entries;
    }

    /**
     * The newest entries first.
     *
     * @param int|null $limit
     * @param string|null $op only entries of this op
     * @return array[]
     */
    public function recent( $limit = 20, $op = null )
    {
        $entries = array_reverse( $this->all() );
        if ( $op !== null )
            $entries = array_values( array_filter( $entries, function ( $e ) use ( $op ) { return isset( $e['op'] ) && $e['op'] === $op; } ) );
        return $limit ? array_slice( $entries, 0, (int)$limit ) : $entries;
    }

    /**
     * @param string $id
     * @return array|null
     */
    public function find( $id )
    {
        foreach ( $this->all() as $e )
        {
            if ( $e['id'] === $id )
                return $e;
        }
        return null;
    }

    /**
     * The entries of a group (one preset applied), newest first.
     *
     * @param string $group
     * @return array[]
     */
    public function group( $group )
    {
        return array_values( array_filter( array_reverse( $this->all() ), function ( $e ) use ( $group ) {
            return isset( $e['group'] ) && $e['group'] === $group && empty( $e['undoes'] );
        } ) );
    }
}
