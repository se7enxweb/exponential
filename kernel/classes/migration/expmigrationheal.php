<?php
/**
 * File containing the expMigrationHeal class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The fixes a deploy may make on a destination before it runs the migrations there (exp:migration:heal, called by
 * expMigrationRemoteDeploy when the source's [RemoteSettings] SelfHeal[] names them). Each fix is opt-in, bounded and
 * reported; what cannot be fixed safely is only reported:
 *
 *   stale-lock  the installation lock (expMigrationLock, var/<site>/upgrade.lock): a holder file whose run has ended is
 *               removed; a lock still held is moved aside only when its holder ran on this host, its process is gone
 *               (a child process that inherited the lock may still hold it), it is older than the stale lock age, and
 *               the lock file belongs to the user of this process. Anything else is left alone and reported.
 *   modes       the migration folders and var/<site>/migration: a folder or file of this process's user that lacks
 *               the permissions of site.ini [FileSettings] StorageDirPermissions / StorageFilePermissions gets them
 *               (permissions are only added, never taken away; another user's files are reported).
 *   caches      the caches a migration's settings and templates need fresh: ini and template-override (ezcache).
 *   paths       a missing migration folder of the site ([MigrationSettings] DirectoryList[]) and var/<site>/migration
 *               are made.
 *
 * Without apply only what would be done is reported. The report is exp-migration/1 with mode heal, fixes (what was or
 * would be done) and problems (what was found and left).
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationHeal
{
    /** The fixes, in the order they run */
    const FIXES = array( 'stale-lock', 'paths', 'modes', 'caches' );

    /** The default age, in seconds, from which a held lock whose process is gone counts as stale */
    const STALE_LOCK_AGE = 7200;

    /** At most this many entries are looked at for the modes */
    const MAX_ENTRIES = 20000;

    /** @var array */
    protected $options;

    /** @var string[] */
    protected $fixes = array();

    /** @var string[] */
    protected $problems = array();

    /** @var string[] */
    protected $warnings = array();

    /**
     * @param array $options apply (bool), stale_lock_age (seconds), lock (expMigrationLock, default the installation's),
     *        directories (the folders the modes and paths fixes work on, default the site's migration folders and
     *        var/<site>/migration), host (this host's name), now, alive (callable pid => bool), owner (callable
     *        file => bool: the file belongs to this process's user), caches (callable string[] => string[] lines),
     *        dir_mode, file_mode
     */
    public function __construct( array $options = array() )
    {
        $this->options = $options + array( 'apply' => false, 'stale_lock_age' => self::STALE_LOCK_AGE, 'lock' => null, 'directories' => null,
                                           'host' => (string)php_uname( 'n' ), 'now' => null, 'alive' => null, 'owner' => null, 'caches' => null,
                                           'dir_mode' => null, 'file_mode' => null );
    }

    /**
     * Which of the names are fixes; the others in $unknown. Pure.
     *
     * @param string|string[] $names a comma list or a list
     * @param string[] $unknown
     * @return string[] in the order of FIXES
     */
    public static function normalize( $names, array &$unknown = array() )
    {
        $wanted = array();
        foreach ( (array)$names as $name )
            foreach ( explode( ',', (string)$name ) as $part )
                if ( ( $part = strtolower( trim( $part ) ) ) !== '' )
                {
                    if ( in_array( $part, self::FIXES, true ) )
                        $wanted[$part] = true;
                    else
                        $unknown[] = $part;
                }
        return array_values( array_filter( self::FIXES, function ( $fix ) use ( $wanted ) { return isset( $wanted[$fix] ); } ) );
    }

    /**
     * Runs the fixes.
     *
     * @param string[] $fixes normalize()d
     * @return array the report (exp-migration/1, mode heal)
     */
    public function run( array $fixes )
    {
        $this->fixes = $this->problems = $this->warnings = array();
        foreach ( $fixes as $fix )
        {
            try
            {
                switch ( $fix )
                {
                    case 'stale-lock': $this->staleLock(); break;
                    case 'paths': $this->paths(); break;
                    case 'modes': $this->modes(); break;
                    case 'caches': $this->caches(); break;
                }
            }
            catch ( Throwable $e )
            {
                $this->problems[] = "$fix: " . $e->getMessage();
            }
        }
        return array( 'schema' => expMigrationRunner::REPORT, 'mode' => 'heal', 'apply' => (bool)$this->options['apply'], 'fixes_asked' => array_values( $fixes ),
                      'fixes' => $this->fixes, 'problems' => $this->problems, 'migrations' => array(), 'errors' => array(), 'warnings' => $this->warnings,
                      'exit' => expUpgrade::EXIT_OK );
    }

    /** A fix made, or one that would be made. */
    protected function fixed( $text )
    {
        $this->fixes[] = ( $this->options['apply'] ? '' : 'would: ' ) . $text;
    }

    /** @return int */
    protected function now()
    {
        return $this->options['now'] === null ? time() : (int)$this->options['now'];
    }

    /** @return bool whether a process runs on this host */
    protected function alive( $pid )
    {
        if ( is_callable( $this->options['alive'] ) )
            return (bool)call_user_func( $this->options['alive'], (int)$pid );
        if ( (int)$pid <= 0 )
            return false;
        if ( function_exists( 'posix_kill' ) )
            return @posix_kill( (int)$pid, 0 ) || ( function_exists( 'posix_get_last_error' ) && posix_get_last_error() === 1 );
        return is_dir( '/proc/' . (int)$pid );
    }

    /** @return bool whether a file belongs to the user of this process (root may touch every file) */
    protected function owned( $file )
    {
        if ( is_callable( $this->options['owner'] ) )
            return (bool)call_user_func( $this->options['owner'], $file );
        if ( !function_exists( 'posix_geteuid' ) )
            return is_writable( $file );
        $uid = posix_geteuid();
        return $uid === 0 || @fileowner( $file ) === $uid;
    }

    /** Fix stale-lock. */
    protected function staleLock()
    {
        $lock = $this->options['lock'] ?: expMigrationLock::installation();
        $holder = $lock->holder();
        if ( $holder === null )
            return;
        $who = expMigrationLock::describe( $holder );
        if ( !empty( $holder['stale'] ) )
        {
            if ( $this->options['apply'] && is_file( $lock->holderFile() ) && !@unlink( $lock->holderFile() ) )
            {
                $this->problems[] = 'stale-lock: the holder file of an ended run could not be removed (' . $who . ')';
                return;
            }
            $this->fixed( 'stale-lock: removed the holder file of an ended run (' . $who . ')' );
            return;
        }
        $age = !empty( $holder['started'] ) ? $this->now() - (int)$holder['started'] : 0;
        $pid = isset( $holder['pid'] ) ? (int)$holder['pid'] : 0;
        $host = isset( $holder['host'] ) ? (string)$holder['host'] : '';
        $max = max( 60, (int)$this->options['stale_lock_age'] );
        if ( $host === '' || $host !== (string)$this->options['host'] )
            $why = 'it is held on ' . ( $host !== '' ? $host : 'an unknown host' ) . ', not on this host: its process cannot be checked';
        else if ( $pid <= 0 || $this->alive( $pid ) )
            $why = 'its process ' . ( $pid > 0 ? $pid . ' ' : '' ) . 'still runs';
        else if ( empty( $holder['started'] ) || $age < $max )
            $why = 'it is ' . $age . ' s old, younger than the stale lock age of ' . $max . ' s';
        else if ( !$this->owned( $lock->file() ) )
            $why = 'the lock file belongs to another user';
        else
            $why = '';
        if ( $why !== '' )
        {
            $this->problems[] = 'stale-lock: the lock is held (' . $who . ') and left alone: ' . $why;
            return;
        }
        $aside = $lock->file() . '.stale-' . $this->now();
        if ( $this->options['apply'] )
        {
            if ( !@rename( $lock->file(), $aside ) )
            {
                $this->problems[] = 'stale-lock: the lock file could not be moved aside (' . $who . ')';
                return;
            }
            if ( is_file( $lock->holderFile() ) )
                @rename( $lock->holderFile(), $aside . '.holder' );
        }
        $this->fixed( 'stale-lock: moved the lock of a run whose process is gone aside to ' . basename( $aside ) . ' (' . $who . ', ' . $age . ' s old)' );
    }

    /** @return string[] the folders the paths and modes fixes work on */
    protected function directories()
    {
        if ( $this->options['directories'] !== null )
            return array_values( (array)$this->options['directories'] );
        $dirs = array();
        $settings = expMigrationLocator::settings( eZINI::instance( 'migration.ini' ) );
        $root = rtrim( (string)eZSys::rootDir(), '/' );
        foreach ( $settings['directories'] as $dir )
            $dirs[] = $dir[0] === '/' ? rtrim( $dir, '/' ) : $root . '/' . trim( $dir, '/' );
        $var = rtrim( (string)eZSys::varDirectory(), '/' );
        $dirs[] = ( $var !== '' && $var[0] === '/' ? $var : $root . '/' . $var ) . '/migration';
        return $dirs;
    }

    /** @return int */
    protected function dirMode()
    {
        return $this->options['dir_mode'] !== null ? (int)$this->options['dir_mode'] : (int)eZDir::dirMode( 0775 );
    }

    /** @return int */
    protected function fileMode()
    {
        return $this->options['file_mode'] !== null ? (int)$this->options['file_mode'] : (int)eZFile::fileMode( 0664 );
    }

    /** Fix paths. */
    protected function paths()
    {
        foreach ( $this->directories() as $dir )
        {
            if ( is_dir( $dir ) )
                continue;
            if ( file_exists( $dir ) )
            {
                $this->problems[] = "paths: $dir is a file, not a folder";
                continue;
            }
            if ( $this->options['apply'] && !@mkdir( $dir, eZDir::dirMode( $this->dirMode() ), true ) && !is_dir( $dir ) )
            {
                $this->problems[] = "paths: $dir could not be made";
                continue;
            }
            $this->fixed( "paths: made the missing folder $dir" );
        }
    }

    /** Fix modes. */
    protected function modes()
    {
        $count = 0;
        $changed = 0;
        $foreign = 0;
        $want = array( 'dir' => $this->dirMode() & 0777, 'file' => $this->fileMode() & 0777 );
        foreach ( $this->directories() as $dir )
        {
            if ( !is_dir( $dir ) )
                continue;
            $stack = array( $dir );
            while ( $stack && $count < self::MAX_ENTRIES )
            {
                $path = array_pop( $stack );
                $count++;
                $isDir = is_dir( $path ) && !is_link( $path );
                if ( is_link( $path ) )
                    continue;
                $perms = @fileperms( $path );
                $need = $want[$isDir ? 'dir' : 'file'];
                if ( $perms !== false && ( $perms & $need ) !== $need )
                {
                    if ( !$this->owned( $path ) )
                        $foreign++;
                    else if ( !$this->options['apply'] || ( $isDir ? @chmod( $path, eZDir::dirMode( ( $perms & 0777 ) | $need ) ) : @chmod( $path, eZFile::fileMode( ( $perms & 0777 ) | $need ) ) ) )
                        $changed++;
                    else
                        $foreign++;
                }
                if ( $isDir && is_readable( $path ) )
                    foreach ( (array)@scandir( $path ) as $name )
                        if ( $name !== '.' && $name !== '..' && $name !== false )
                            $stack[] = $path . '/' . $name;
            }
        }
        if ( $changed )
            $this->fixed( 'modes: gave ' . $changed . ' folder(s) and file(s) the permissions of [FileSettings] (' . sprintf( '%o', $want['dir'] ) . ' / ' . sprintf( '%o', $want['file'] ) . ')' );
        if ( $foreign )
            $this->problems[] = 'modes: ' . $foreign . ' folder(s) and file(s) lack the permissions and belong to another user: not changed';
        if ( $count >= self::MAX_ENTRIES )
            $this->warnings[] = 'modes: stopped after ' . self::MAX_ENTRIES . ' entries';
    }

    /** Fix caches. */
    protected function caches()
    {
        $caches = array( 'tag:ini', 'template-override' );
        if ( !$this->options['apply'] )
        {
            $this->fixed( 'caches: clear ' . implode( ', ', $caches ) );
            return;
        }
        $warnings = array();
        $lines = is_callable( $this->options['caches'] ) ? (array)call_user_func( $this->options['caches'], $caches ) : expMigrationAfterRun::clear( $caches, $warnings );
        foreach ( $lines as $line )
            $this->fixed( 'caches: ' . $line );
        foreach ( $warnings as $warning )
            $this->warnings[] = 'caches: ' . $warning;
    }
}
