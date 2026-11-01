<?php
/**
 * File containing the expMigrationProcess class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Runs a PHP script of the installation in a process of its own, for the step types autoload and command: the PHP
 * that runs the migration, the script below the root of the installation, its arguments as a list. No shell is
 * involved (proc_open() with an array), so nothing in an argument is ever read as shell syntax; the output (stdout
 * and stderr together) is kept, the time is limited.
 *
 * A test replaces the runner with setRunner().
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationProcess
{
    /** @var callable|null function( array $command, string $cwd, int $timeout ): array( exit code, output ) */
    protected static $runner;

    /**
     * Replaces how processes are run (a test's); null puts proc_open() back.
     *
     * @param callable|null $runner
     */
    public static function setRunner( $runner )
    {
        self::$runner = $runner;
    }

    /**
     * The PHP binary for the scripts: [AdminSettings] PhpCliPath of migration.ini, or the PHP that runs now.
     *
     * @return string
     */
    public static function php()
    {
        $ini = eZINI::instance( 'migration.ini' );
        $path = $ini->hasVariable( 'AdminSettings', 'PhpCliPath' ) ? trim( (string)$ini->variable( 'AdminSettings', 'PhpCliPath' ) ) : '';
        if ( $path !== '' && is_executable( $path ) )
            return $path;
        // under PHP-FPM PHP_BINARY is the FPM binary, which cannot run a script
        return PHP_SAPI === 'cli' || PHP_BINDIR === '' ? PHP_BINARY : PHP_BINDIR . '/php';
    }

    /**
     * The root of the installation a run works on.
     *
     * @param expMigrationContext|null $context
     * @return string
     */
    public static function root( $context = null )
    {
        $root = $context instanceof expMigrationContext ? (string)$context->root() : '';
        if ( $root === '' )
            $root = rtrim( (string)eZSys::rootDir(), '/' );
        return $root !== '' ? $root : getcwd();
    }

    /**
     * Runs a script of the installation.
     *
     * @param string $script below the root (bin/php/console)
     * @param string[] $arguments
     * @param string $root the root of the installation
     * @param int $timeout seconds
     * @return array( int exit code, string output )
     */
    public static function run( $script, array $arguments, $root, $timeout = 600 )
    {
        $command = array_merge( array( self::php(), (string)$script ), array_map( 'strval', $arguments ) );
        if ( self::$runner !== null )
            return call_user_func( self::$runner, $command, (string)$root, (int)$timeout );
        if ( !function_exists( 'proc_open' ) )
            return array( 127, 'proc_open() is not available in this PHP' );
        $pipes = array();
        $process = proc_open( $command, array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'redirect', 1 ) ), $pipes, (string)$root );
        if ( !is_resource( $process ) )
            return array( 127, 'the process could not be started' );
        fclose( $pipes[0] );
        stream_set_blocking( $pipes[1], false );
        $output = '';
        $end = time() + max( 1, (int)$timeout );
        while ( true )
        {
            $chunk = stream_get_contents( $pipes[1] );
            if ( is_string( $chunk ) )
                $output .= $chunk;
            $status = proc_get_status( $process );
            if ( !$status['running'] )
            {
                $rest = stream_get_contents( $pipes[1] );
                if ( is_string( $rest ) )
                    $output .= $rest;
                fclose( $pipes[1] );
                proc_close( $process );
                return array( (int)$status['exitcode'], $output );
            }
            if ( time() > $end )
            {
                proc_terminate( $process );
                fclose( $pipes[1] );
                proc_close( $process );
                return array( 124, $output . "\n(stopped after $timeout seconds)" );
            }
            usleep( 50000 );
        }
    }

    /**
     * The last lines of an output, for a message. Pure.
     *
     * @param string $output
     * @param int $lines
     * @return string
     */
    public static function tail( $output, $lines = 5 )
    {
        $all = preg_split( '/\r\n|\r|\n/', trim( (string)$output ) );
        return implode( ' | ', array_slice( $all, -max( 1, (int)$lines ) ) );
    }
}
