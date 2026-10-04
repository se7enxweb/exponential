<?php
/**
 * File containing the expProcessTools class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and others. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Finds the programs a background job needs: the PHP command line and setsid.
 *
 * is_executable() and is_file() obey open_basedir, while proc_open() does not:
 * on a host that confines PHP to its site directory, /usr/bin/setsid and the
 * PHP command line exist and can be run, but every test of them from PHP says
 * "no". A candidate hidden by open_basedir is therefore proven by running it
 * (setsid --version, php -v, five seconds at most) instead of by looking at it.
 *
 * Results are kept for the request. The finders return a path or false, and
 * the matching *Error() methods say what is missing.
 */
class expProcessTools
{
    const PROBE_SECONDS = 5;

    private static $cache = array();

    /**
     * The PHP command line, preferring the running major.minor: PHP_BINARY
     * under the CLI, then PHP_BINDIR/php, then the usual places of a versioned
     * one, then a plain "php" in the usual places.
     *
     * @return string|false
     */
    public static function phpCli()
    {
        if ( array_key_exists( 'php', self::$cache ) )
            return self::$cache['php'];
        $found = false;
        if ( PHP_SAPI === 'cli' && PHP_BINARY !== '' && self::runs( PHP_BINARY, '-v' ) )
            $found = PHP_BINARY;
        if ( $found === false )
        {
            $running = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;
            $nodot = PHP_MAJOR_VERSION . PHP_MINOR_VERSION;
            $candidates = array( PHP_BINDIR . '/php',
                                 '/opt/plesk/php/' . $running . '/bin/php',
                                 '/opt/alt/php' . $nodot . '/usr/bin/php',
                                 '/opt/cpanel/ea-php' . $nodot . '/root/usr/bin/php',
                                 '/opt/remi/php' . $nodot . '/root/usr/bin/php',
                                 '/usr/local/bin/php' . $running,
                                 '/usr/bin/php' . $running,
                                 '/usr/local/bin/php',
                                 '/usr/bin/php' );
            $other = false;
            foreach ( array_unique( $candidates ) as $file )
            {
                if ( !self::usable( $file, '-v' ) )
                    continue;
                // an executable of another major.minor is a last resort only
                if ( self::versionOf( $file ) === $running )
                {
                    $found = $file;
                    break;
                }
                if ( $other === false )
                    $other = $file;
            }
            if ( $found === false )
                $found = $other;
        }
        return self::$cache['php'] = $found;
    }

    /**
     * setsid, which detaches a worker from the request that started it.
     *
     * @return string|false
     */
    public static function setsid()
    {
        if ( array_key_exists( 'setsid', self::$cache ) )
            return self::$cache['setsid'];
        $found = false;
        foreach ( array( '/usr/bin/setsid', '/bin/setsid', '/usr/local/bin/setsid', '/usr/sbin/setsid' ) as $file )
        {
            if ( self::usable( $file, '--version' ) )
            {
                $found = $file;
                break;
            }
        }
        return self::$cache['setsid'] = $found;
    }

    /**
     * Why a background job cannot start, or '' when it can.
     *
     * @return string
     */
    public static function error()
    {
        $disabled = array_map( 'trim', explode( ',', (string)ini_get( 'disable_functions' ) ) );
        if ( !function_exists( 'proc_open' ) || in_array( 'proc_open', $disabled, true ) )
            return 'proc_open() is disabled in this PHP (disable_functions)';
        $missing = array();
        if ( self::setsid() === false )
            $missing[] = 'setsid (from util-linux, not found in /usr/bin or /bin or not runnable)';
        if ( self::phpCli() === false )
            $missing[] = 'the PHP command line (not found next to ' . PHP_BINDIR . ' or in the usual places, or not runnable)';
        if ( $missing )
            return 'Missing: ' . implode( ' and ', $missing ) . '.';
        return '';
    }

    /**
     * Forgets what was found (tests).
     */
    public static function reset()
    {
        self::$cache = array();
    }

    /**
     * Whether a file can be run: seen as executable, or, when open_basedir hides
     * it, proven by running it.
     */
    private static function usable( $file, $probe )
    {
        if ( !self::hidden( $file ) )
            return is_executable( $file ) && self::runs( $file, $probe );
        return self::runs( $file, $probe );
    }

    private static function hidden( $file )
    {
        $basedir = (string)ini_get( 'open_basedir' );
        if ( $basedir === '' )
            return false;
        foreach ( explode( PATH_SEPARATOR, $basedir ) as $dir )
        {
            $dir = rtrim( $dir, '/' );
            if ( $dir !== '' && ( $file === $dir || strpos( $file, $dir . '/' ) === 0 ) )
                return false;
        }
        return true;
    }

    /**
     * Runs "<file> <arg>" with the output discarded; true when it ends with 0 in time.
     */
    private static function runs( $file, $arg )
    {
        return self::probe( $file, $arg ) !== false;
    }

    /**
     * Runs "<file> <arg>" and returns its output, or false when it does not
     * start, ends with another status than 0 or takes longer than PROBE_SECONDS.
     */
    private static function probe( $file, $arg )
    {
        if ( !function_exists( 'proc_open' ) )
            return false;
        $disabled = array_map( 'trim', explode( ',', (string)ini_get( 'disable_functions' ) ) );
        if ( in_array( 'proc_open', $disabled, true ) )
            return false;
        $pipes = array();
        $process = @proc_open( array( $file, $arg ), array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes );
        if ( !is_resource( $process ) )
            return false;
        fclose( $pipes[0] );
        stream_set_blocking( $pipes[1], false );
        stream_set_blocking( $pipes[2], false );
        $out = '';
        $until = microtime( true ) + self::PROBE_SECONDS;
        $status = null;
        while ( microtime( true ) < $until )
        {
            $out .= (string)fread( $pipes[1], 8192 );
            fread( $pipes[2], 8192 );
            $info = proc_get_status( $process );
            if ( !$info['running'] )
            {
                $status = $info['exitcode'];
                $out .= (string)stream_get_contents( $pipes[1] );
                break;
            }
            usleep( 20000 );
        }
        if ( $status === null )
        {
            proc_terminate( $process, 9 );
            proc_close( $process );
            fclose( $pipes[1] );
            fclose( $pipes[2] );
            return false;
        }
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        proc_close( $process );
        // proc_get_status() reports the exit code once only: a 0 or a failure is read here
        return ( $status === 0 || ( $status === -1 && $out !== '' ) ) ? $out : false;
    }

    /**
     * "8.5" of a PHP command line, or '' when it does not say.
     */
    private static function versionOf( $file )
    {
        $out = self::probe( $file, '-v' );
        if ( is_string( $out ) && preg_match( '/^PHP (\d+\.\d+)\./', $out, $m ) )
            return $m[1];
        return '';
    }
}
