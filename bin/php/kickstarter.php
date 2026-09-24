#!/usr/bin/env php
<?php
/**
 * @description Kickstarter runner. Supports "ini" generation and full "run" setup subcommands.
 * @package   kernel
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license   For full copyright and license information view LICENSE file.
 */

$rootDir = dirname( dirname( __DIR__ ) );
chdir( $rootDir );
require_once 'autoload.php';

$cli = eZCLI::instance();
$argv = $GLOBALS['argv'];
$subcommand = isset( $argv[1] ) ? $argv[1] : null;
$forwardArgs = array_slice( $argv, 2 );

function showKickstarterHelp( $cli )
{
    $cli->output( 'Usage: ./bin/php/console exp:kickstarter <command> [options]' );
    $cli->output( '       ./bin/php/kickstarter.php <command> [options]' );
    $cli->output( '' );
    $cli->output( 'Commands:' );
    $cli->output( '  ini                  Interactive kickstart.ini generator (default)' );
    $cli->output( '  run                  Run the setup wizard. Use --dry-run to test remote packages (stops before CreateSites) or --force to install.' );
    $cli->output( '  help, --help, -h     Show this help' );
    $cli->output( '' );
    $cli->output( 'Run options:' );
    $cli->output( '  --start-step=<step>  First step to run (default: welcome)' );
    $cli->output( '  --stop-step=<step>   Last step to run (default: final)' );
    $cli->output( '  --dry-run            Validate kickstart.ini, then run DatabaseChoice..Registration to test remote packages (stops before CreateSites)' );
    $cli->output( '  --list-steps         List all setup steps and exit' );
    $cli->output( '' );
    $cli->output( 'Ini options:' );
    $cli->output( '  --defaults, -d       Copy kickstart.ini-dist values to kickstart.ini' );
    $cli->output( '  --yes, -y            Accept sensible defaults and write kickstart.ini' );
    $cli->output( '  --help, -h           Show ini help' );
}

if ( $subcommand === null || $subcommand === 'help' || $subcommand === '--help' || $subcommand === '-h' )
{
    showKickstarterHelp( $cli );
    exit( 0 );
}

if ( $subcommand === 'ini' )
{
    $generator = new expKickstarterIni(
        $rootDir,
        $rootDir . '/kickstart.ini-dist',
        $rootDir . '/kickstart.ini',
        $argv
    );
    $generator->run();
}
elseif ( $subcommand === 'run' )
{
    $runner = new expKickstarter( $rootDir, $forwardArgs );
    $exitCode = $runner->run();
    exit( $exitCode );
}
else
{
    $cli->error( 'Unknown command: ' . $subcommand );
    showKickstarterHelp( $cli );
    exit( 1 );
}
