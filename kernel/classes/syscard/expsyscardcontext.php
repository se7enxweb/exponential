<?php
/**
 * File containing the expSysCardContext class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Everything a system card provider reads from the machine goes through its context, so a test can hand a provider
 * made-up values instead of the real machine:
 *
 *   $value = $context->get( 'php.version', function () { return PHP_VERSION; } );
 *   $url   = $context->ini( 'site.ini', 'SiteSettings', 'SiteURL', '' );
 *   $text  = $context->file( '/etc/os-release' );
 *
 * A real context (expSysCardContext::real()) calls the closure, reads the INI files with eZINI and the files from
 * disk. A fake context (expSysCardContext::fake( $values )) answers from $values only and never touches the machine:
 *
 *   expSysCardContext::fake( array(
 *       'php.version' => '8.5.11',
 *       'ini'   => array( 'site.ini' => array( 'SiteSettings' => array( 'SiteURL' => 'www.example.org' ) ) ),
 *       'files' => array( '/etc/os-release' => "PRETTY_NAME=\"AlmaLinux 9.6\"\n" ),
 *       'env'   => array( 'TERM' => 'xterm-256color' ),
 *   ) );
 *
 * The context also carries the card being made (card, info or about), the installation root and a deadline: a
 * provider that walks a directory or runs a program stops when the deadline has passed.
 */
class expSysCardContext
{
    const MODE_CARD = 'card';
    const MODE_INFO = 'info';
    const MODE_ABOUT = 'about';

    /** @var bool */
    protected $fake = false;
    /** @var array the values of a fake context */
    protected $values = array();
    /** @var string */
    public $mode = self::MODE_CARD;
    /** @var string the installation root, without a trailing slash */
    public $root = '.';
    /** @var int */
    public $now = 0;
    /** @var float|null microtime() after which slow work stops */
    public $deadline = null;
    /** @var array memo of get() */
    protected $memo = array();

    /**
     * @param string $mode
     * @param string|null $root
     * @return expSysCardContext a context that reads the machine
     */
    public static function real( $mode = self::MODE_CARD, $root = null )
    {
        $context = new self();
        $context->mode = self::mode( $mode );
        $context->root = rtrim( $root !== null ? $root : ( class_exists( 'eZSys' ) ? (string)eZSys::rootDir() : (string)getcwd() ), '/' );
        if ( $context->root === '' )
            $context->root = (string)getcwd();
        $context->now = time();
        return $context;
    }

    /**
     * @param array $values key => value; 'ini', 'files', 'env', 'dirs' and 'commands' are maps of their own
     * @param string $mode
     * @return expSysCardContext a context that answers from $values only
     */
    public static function fake( array $values = array(), $mode = self::MODE_CARD )
    {
        $context = new self();
        $context->fake = true;
        $context->values = $values;
        $context->mode = self::mode( $mode );
        $context->root = isset( $values['root'] ) ? rtrim( (string)$values['root'], '/' ) : '/srv/exponential';
        $context->now = isset( $values['now'] ) ? (int)$values['now'] : 1792483200; // 2026-10-20 08:00 UTC
        return $context;
    }

    /**
     * @param string $mode
     * @return string card, info or about
     */
    public static function mode( $mode )
    {
        return in_array( $mode, array( self::MODE_CARD, self::MODE_INFO, self::MODE_ABOUT ), true ) ? $mode : self::MODE_CARD;
    }

    /**
     * @return bool
     */
    public function isFake()
    {
        return $this->fake;
    }

    /**
     * A copy for another card (the same machine).
     *
     * @param string $mode
     * @return expSysCardContext
     */
    public function withMode( $mode )
    {
        $copy = clone $this;
        $copy->mode = self::mode( $mode );
        return $copy;
    }

    /**
     * A value: from the fake values, or what $real returns (once per context).
     *
     * @param string $key
     * @param callable|null $real
     * @param mixed $default when the fake values do not have it, or $real throws
     * @return mixed
     */
    public function get( $key, $real = null, $default = null )
    {
        if ( $this->fake )
            return array_key_exists( $key, $this->values ) ? $this->values[$key] : $default;
        if ( array_key_exists( $key, $this->memo ) )
            return $this->memo[$key];
        $value = $default;
        if ( is_callable( $real ) )
        {
            try
            {
                $value = call_user_func( $real );
            }
            catch ( Throwable $e )
            {
                $value = $default;
            }
        }
        return $this->memo[$key] = $value;
    }

    /**
     * Sets a value that get() returns from now on (what the command knows already: the terminal's size).
     *
     * @param string $key
     * @param mixed $value
     * @return expSysCardContext
     */
    public function set( $key, $value )
    {
        if ( $this->fake )
            $this->values[$key] = $value;
        else
            $this->memo[$key] = $value;
        return $this;
    }

    /**
     * An INI setting.
     *
     * @param string $file site.ini, velocity.ini ...
     * @param string $block
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function ini( $file, $block, $name, $default = null )
    {
        if ( $this->fake )
            return isset( $this->values['ini'][$file][$block] ) && array_key_exists( $name, $this->values['ini'][$file][$block] )
                 ? $this->values['ini'][$file][$block][$name] : $default;
        if ( !class_exists( 'eZINI' ) )
            return $default;
        try
        {
            if ( !eZINI::exists( $file ) )
                return $default;
            $ini = eZINI::instance( $file );
            return $ini->hasVariable( $block, $name ) ? $ini->variable( $block, $name ) : $default;
        }
        catch ( Throwable $e )
        {
            return $default;
        }
    }

    /**
     * The names of the blocks of an INI file.
     *
     * @param string $file
     * @return string[]
     */
    public function iniGroups( $file )
    {
        if ( $this->fake )
            return isset( $this->values['ini'][$file] ) ? array_keys( $this->values['ini'][$file] ) : array();
        if ( !class_exists( 'eZINI' ) )
            return array();
        try
        {
            return eZINI::exists( $file ) ? array_keys( eZINI::instance( $file )->groups() ) : array();
        }
        catch ( Throwable $e )
        {
            return array();
        }
    }

    /**
     * Whether an INI value means "on" (enabled, true, 1, on, yes).
     *
     * @param string $file
     * @param string $block
     * @param string $name
     * @param string $default
     * @return bool
     */
    public function iniEnabled( $file, $block, $name, $default = 'disabled' )
    {
        return self::isOn( $this->ini( $file, $block, $name, $default ) );
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isOn( $value )
    {
        return in_array( strtolower( trim( (string)$value ) ), array( 'enabled', 'true', '1', 'on', 'yes' ), true );
    }

    /**
     * An environment variable.
     *
     * @param string $name
     * @return string|null null when it is not set
     */
    public function env( $name )
    {
        if ( $this->fake )
            return isset( $this->values['env'] ) && array_key_exists( $name, $this->values['env'] ) ? (string)$this->values['env'][$name] : null;
        $value = getenv( $name );
        return $value === false ? null : (string)$value;
    }

    /**
     * A path below the root when it is relative.
     *
     * @param string $path
     * @return string
     */
    public function path( $path )
    {
        $path = (string)$path;
        return $path !== '' && $path[0] === '/' ? $path : $this->root . '/' . $path;
    }

    /**
     * The content of a file, or null.
     *
     * @param string $path absolute, or relative to the root
     * @param int $limit read at most this many bytes
     * @return string|null
     */
    public function file( $path, $limit = 1048576 )
    {
        $path = $this->path( $path );
        if ( $this->fake )
            return isset( $this->values['files'] ) && array_key_exists( $path, $this->values['files'] ) ? (string)$this->values['files'][$path] : null;
        if ( !@is_file( $path ) || !@is_readable( $path ) )
            return null;
        $text = @file_get_contents( $path, false, null, 0, $limit );
        return $text === false ? null : $text;
    }

    /**
     * @param string $path
     * @return bool
     */
    public function isFile( $path )
    {
        $path = $this->path( $path );
        if ( $this->fake )
            return isset( $this->values['files'] ) && array_key_exists( $path, $this->values['files'] );
        return @is_file( $path );
    }

    /**
     * The entries of a directory (names, sorted, without . and ..), or an empty list.
     *
     * @param string $path
     * @return string[]
     */
    public function entries( $path )
    {
        $path = $this->path( $path );
        if ( $this->fake )
            return isset( $this->values['dirs'][$path] ) ? array_values( (array)$this->values['dirs'][$path] ) : array();
        $list = @scandir( $path );
        if ( !is_array( $list ) )
            return array();
        return array_values( array_diff( $list, array( '.', '..' ) ) );
    }

    /**
     * The files matching a glob pattern.
     *
     * @param string $pattern
     * @return string[]
     */
    public function glob( $pattern )
    {
        $pattern = $this->path( $pattern );
        if ( $this->fake )
        {
            $found = array();
            foreach ( isset( $this->values['files'] ) ? array_keys( $this->values['files'] ) : array() as $file )
                if ( fnmatch( $pattern, $file ) )
                    $found[] = $file;
            sort( $found );
            return $found;
        }
        $found = @glob( $pattern );
        return is_array( $found ) ? $found : array();
    }

    /**
     * Seconds left before the deadline (a large number when there is none).
     *
     * @return float
     */
    public function remaining()
    {
        return $this->deadline === null ? 3600.0 : max( 0.0, $this->deadline - microtime( true ) );
    }

    /**
     * The output of a program, with a time limit. The program is killed when the limit has passed.
     *
     * @param string[] $command the program and its arguments (no shell)
     * @param float $timeout seconds
     * @return string|null null when it could not run, failed or timed out
     */
    public function command( array $command, $timeout = 1.0 )
    {
        $key = implode( ' ', $command );
        if ( $this->fake )
            return isset( $this->values['commands'] ) && array_key_exists( $key, $this->values['commands'] ) ? $this->values['commands'][$key] : null;
        if ( !function_exists( 'proc_open' ) || empty( $command ) )
            return null;
        $timeout = min( (float)$timeout, $this->remaining() );
        if ( $timeout <= 0 )
            return null;
        $pipes = array();
        $process = @proc_open( $command, array( 0 => array( 'file', '/dev/null', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'file', '/dev/null', 'w' ) ), $pipes );
        if ( !is_resource( $process ) )
            return null;
        stream_set_blocking( $pipes[1], false );
        $output = '';
        $end = microtime( true ) + $timeout;
        while ( true )
        {
            $read = array( $pipes[1] );
            $write = null;
            $except = null;
            $left = $end - microtime( true );
            if ( $left <= 0 )
            {
                proc_terminate( $process, 9 );
                fclose( $pipes[1] );
                proc_close( $process );
                return null;
            }
            $ready = @stream_select( $read, $write, $except, 0, (int)min( 200000, $left * 1000000 ) );
            if ( $ready === false )
                break;
            if ( $ready > 0 )
            {
                $chunk = fread( $pipes[1], 65536 );
                if ( $chunk === '' || $chunk === false )
                {
                    if ( feof( $pipes[1] ) )
                        break;
                }
                else
                    $output .= $chunk;
            }
        }
        fclose( $pipes[1] );
        $status = proc_close( $process );
        return $status === 0 ? $output : null;
    }
}
