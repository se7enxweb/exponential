<?php
/**
 * The code of bin/php/kickstarter.php, moved into a class (#207 stage 1). The file bin/php/kickstarter.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of bin/php/kickstarter.php:
 *
 *
 * @description Kickstarter runner. Supports "ini" generation and full "run" setup subcommands.
 * @package   kernel
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license   For full copyright and license information view LICENSE file.
 *
 */

namespace
{


/**
 * Runs this script again as a child with the same arguments and PHP settings, shows its output as it comes and
 * copies it to var/log/kickstart.log, after moving the logs of earlier runs one place on (kickstart.log.1 ..
 * kickstart.log.9). Returns the child's exit status.
 */
function expKickstarterRunLogged( $rootDir, $argv )
{
    $logDir = $rootDir . '/var/log';
    $log = $logDir . '/kickstart.log';
    if ( !is_dir( $logDir ) )
        @mkdir( $logDir, 0775, true );
    for ( $i = 9; $i >= 1; --$i )
    {
        $from = $i === 1 ? $log : $log . '.' . ( $i - 1 );
        if ( is_file( $from ) )
            @rename( $from, $log . '.' . $i );
    }
    $fh = @fopen( $log, 'w' );
    if ( !$fh )
    {
        fwrite( STDERR, "Cannot write $log; running without the log.\n" );
        putenv( 'EXP_KICKSTART_LOG_CHILD=1' );
    }

    // What to mask: every Password= of kickstart.ini
    $secrets = array();
    if ( is_file( $rootDir . '/kickstart.ini' ) )
    {
        foreach ( file( $rootDir . '/kickstart.ini', FILE_IGNORE_NEW_LINES ) as $line )
        {
            if ( preg_match( '/^\s*Password\s*=\s*(.+?)\s*$/i', $line, $m ) && strlen( $m[1] ) >= 3 )
                $secrets[] = $m[1];
        }
    }
    $mask = function( $text ) use ( $secrets )
    {
        return $secrets ? str_replace( $secrets, '********', $text ) : $text;
    };

    $cmd = array( PHP_BINARY );
    foreach ( array( 'memory_limit', 'date.timezone', 'max_execution_time' ) as $setting )
    {
        $value = ini_get( $setting );
        if ( $value !== false && $value !== '' )
            $cmd[] = '-d' . $setting . '=' . $value;
    }
    // $argv[0] is the script as it was started (kickstarter.php, or the console that included it), the rest its
    // arguments; the child runs in the same directory, so a relative path still resolves.
    $cmd = array_merge( $cmd, $argv );

    if ( $fh )
    {
        fwrite( $fh, '# kickstart ' . date( 'Y-m-d H:i:s T' ) . ' in ' . $rootDir . "\n# " . $mask( implode( ' ', $argv ) ) . "\n" );
    }
    $env = getenv();
    $env['EXP_KICKSTART_LOG_CHILD'] = '1';
    $proc = proc_open( $cmd, array( 0 => STDIN, 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes, $rootDir, $env );
    if ( !is_resource( $proc ) )
    {
        fwrite( STDERR, "Cannot start the kickstart run.\n" );
        return 1;
    }
    stream_set_blocking( $pipes[1], false );
    stream_set_blocking( $pipes[2], false );
    $open = array( 1 => $pipes[1], 2 => $pipes[2] );
    while ( $open )
    {
        $read = array_values( $open );
        $write = $except = null;
        if ( @stream_select( $read, $write, $except, 1 ) === false )
            break;
        foreach ( $open as $n => $pipe )
        {
            $chunk = fread( $pipe, 65536 );
            if ( $chunk !== false && $chunk !== '' )
            {
                fwrite( $n === 1 ? STDOUT : STDERR, $chunk );
                if ( $fh )
                {
                    fwrite( $fh, $mask( $chunk ) );
                    fflush( $fh );
                }
            }
            if ( feof( $pipe ) )
            {
                fclose( $pipe );
                unset( $open[$n] );
            }
        }
    }
    $status = proc_close( $proc );
    if ( $fh )
    {
        fwrite( $fh, "\n# exit status $status, " . date( 'Y-m-d H:i:s T' ) . "\n" );
        fclose( $fh );
    }
    return $status;
}

function showKickstarterHelp( $cli )
{
    $cli->output( 'Usage: ./bin/php/console exp:kickstarter <command> [options]' );
    $cli->output( '       ./bin/php/kickstarter.php <command> [options]' );
    $cli->output( '' );
    $cli->output( 'Commands:' );
    $cli->output( '  ini                  Interactive kickstart.ini generator (default)' );
    $cli->output( '  run                  Run the setup wizard. Use --dry-run to test the database and remote packages (stops after SiteDetails) or --force to install.' );
    $cli->output( '  help, --help, -h     Show this help' );
    $cli->output( '' );
    $cli->output( 'Run options:' );
    $cli->output( '  --start-step=<step>  First step to run (default: welcome)' );
    $cli->output( '                       A run cannot resume an earlier one: a later start is refused when a step' );
    $cli->output( '                       needs results of steps before it, and the message names where to start' );
    $cli->output( '  --stop-step=<step>   Last step to run (default: final)' );
    $cli->output( '  --dry-run            Validate kickstart.ini, then run DatabaseChoice..SiteDetails to test the database and remote packages (writes no password, sends no mail, installs nothing)' );
    $cli->output( '  --list-steps         List all setup steps and exit' );
    $cli->output( '' );
    $cli->output( 'Every run is also logged to var/log/kickstart.log (passwords masked); earlier runs are kept as' );
    $cli->output( 'kickstart.log.1 (the last) to kickstart.log.9. EXP_KICKSTART_LOG=0 turns the log off.' );
    $cli->output( '' );
    $cli->output( 'Ini options:' );
    $cli->output( '  --defaults, -d       Copy kickstart.ini-dist values to kickstart.ini' );
    $cli->output( '  --yes, -y            Accept sensible defaults and write kickstart.ini' );
    $cli->output( '  --help, -h           Show ini help' );
}
}

namespace Exponential\Command\Kernel
{

class Kickstarter extends \Exponential\Runnable\Command
{
    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'cli', 'exitCode', 'forwardArgs', 'generator', 'rootDir', 'runner', 'subcommand' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        $cli = $this->cli();
        $argv = $GLOBALS['argv'];
        $subcommand = isset( $argv[1] ) ? $argv[1] : null;
        $forwardArgs = array_slice( $argv, 2 );

        if ( $subcommand === null || $subcommand === 'help' || $subcommand === '--help' || $subcommand === '-h' )
        {
            showKickstarterHelp( $cli );
            exit( 0 );
        }

        if ( $subcommand === 'ini' )
        {
            $generator = new \expKickstarterIni(
                $rootDir,
                $rootDir . '/kickstart.ini-dist',
                $rootDir . '/kickstart.ini',
                $argv
            );
            $generator->run();
        }
        elseif ( $subcommand === 'run' )
        {
            // Every run's output also goes to var/log/kickstart.log, one file per run: the previous runs are kept as
            // kickstart.log.1 (the last) to kickstart.log.9. The output is written by print, by fwrite on STDOUT (the
            // progress line) and by fputs on STDERR (errors), which no output buffer sees, so the run is done by a child
            // process whose stdout and stderr pass through here: shown as before and copied to the log. The passwords of
            // kickstart.ini are masked in the log. EXP_KICKSTART_LOG=0 turns it off.
            if ( getenv( 'EXP_KICKSTART_LOG_CHILD' ) !== '1' && getenv( 'EXP_KICKSTART_LOG' ) !== '0' && function_exists( 'proc_open' ) )
            {
                exit( expKickstarterRunLogged( $rootDir, $argv ) );
            }
            $runner = new \expKickstarter( $rootDir, $forwardArgs );
            $exitCode = $runner->run();
            exit( $exitCode );
        }
        else
        {
            $cli->error( 'Unknown command: ' . $subcommand );
            showKickstarterHelp( $cli );
            exit( 1 );
        }
    }
}

}
