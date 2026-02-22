#!/usr/bin/env php
<?php
/**
 * tests/bin/ezptestrunner.php — eZScript-style wrapper for PHPUnit 10.
 *
 * Provides a single, convenient entry point that:
 *  1. Validates the PHP + PHPUnit environment
 *  2. Accepts named --suite, --group, --filter parameters
 *  3. Delegates to vendor/bin/phpunit with those parameters
 *  4. Optionally runs the full combined suite (lint + functional + phpunit)
 *     when --all is given, integrating with ezptestrunner_all_tests.php
 *
 * Usage:
 *   php tests/bin/ezptestrunner.php                          # run all PHPUnit suites
 *   php tests/bin/ezptestrunner.php --suite=security         # PHPUnit security suite only
 *   php tests/bin/ezptestrunner.php --suite=kernel-classes   # kernel class tests
 *   php tests/bin/ezptestrunner.php --group=database         # run database group
 *   php tests/bin/ezptestrunner.php --filter=testSec01       # filter by test name
 *   php tests/bin/ezptestrunner.php --all --report           # full combined suite + report
 *   php tests/bin/ezptestrunner.php --list-suites            # list defined suites
 *   php tests/bin/ezptestrunner.php --list-groups            # list defined groups
 *
 * @copyright Copyright (C) Exponential Open Source Project. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 */

set_time_limit( 0 );

// ── Constants ─────────────────────────────────────────────────────────────────
define( 'EZP_ROOT', dirname( __DIR__, 2 ) );
define( 'EZP_PHPUNIT_BIN', EZP_ROOT . '/vendor/bin/phpunit' );
define( 'EZP_PHPUNIT_XML', EZP_ROOT . '/phpunit.xml' );
define( 'EZP_RUN_ALL',     EZP_ROOT . '/tests/bin/ezptestrunner_all_tests.php' );
define( 'MIN_PHP_VERSION', '8.1.0' );
define( 'MIN_PHPUNIT_VERSION', '10.0.0' );

// ── Environment checks ────────────────────────────────────────────────────────
if ( version_compare( PHP_VERSION, MIN_PHP_VERSION ) < 0 )
{
    fwrite( STDERR, "ERROR: PHP " . MIN_PHP_VERSION . " or later required (got " . PHP_VERSION . ")\n" );
    exit( 2 );
}

if ( !file_exists( EZP_PHPUNIT_BIN ) )
{
    fwrite( STDERR, "ERROR: PHPUnit not found at " . EZP_PHPUNIT_BIN . "\n" );
    fwrite( STDERR, "       Run: composer install --dev\n" );
    exit( 2 );
}

if ( !file_exists( EZP_PHPUNIT_XML ) )
{
    fwrite( STDERR, "ERROR: phpunit.xml not found at " . EZP_PHPUNIT_XML . "\n" );
    fwrite( STDERR, "       This file was added in Exponential 6.0.13.\n" );
    fwrite( STDERR, "       See doc/bc/6.0/phpunitvXXXX.md for instructions.\n" );
    exit( 2 );
}

// ── Parse our custom arguments ────────────────────────────────────────────────
$opts = [
    'suite'       => null,   // --suite=<name>   → --testsuite <name>
    'group'       => null,   // --group=<name>   → --group <name>
    'filter'      => null,   // --filter=<pat>   → --filter <pat>
    'all'         => false,  // --all            → run full combined suite
    'report'      => false,  // --report         → pass --report to ezptestrunner_all_tests.php
    'list-suites' => false,  // --list-suites
    'list-groups' => false,  // --list-groups
    'colors'      => true,   // --no-colors
    'verbose'     => false,  // --verbose
    'help'        => false,  // --help
];

$argv_copy = $_SERVER['argv'];
array_shift( $argv_copy ); // drop script name

foreach ( $argv_copy as $arg )
{
    if ( preg_match( '/^--suite=(.+)$/', $arg, $m ) )        { $opts['suite']   = $m[1]; continue; }
    if ( preg_match( '/^--group=(.+)$/', $arg, $m ) )        { $opts['group']   = $m[1]; continue; }
    if ( preg_match( '/^--filter=(.+)$/', $arg, $m ) )       { $opts['filter']  = $m[1]; continue; }
    if ( $arg === '--all' )                                    { $opts['all']     = true;  continue; }
    if ( $arg === '--report' )                                 { $opts['report']  = true;  continue; }
    if ( $arg === '--list-suites' )                            { $opts['list-suites'] = true; continue; }
    if ( $arg === '--list-groups' )                            { $opts['list-groups'] = true; continue; }
    if ( $arg === '--no-colors' )                              { $opts['colors']  = false; continue; }
    if ( $arg === '--verbose' || $arg === '-v' )               { $opts['verbose'] = true;  continue; }
    if ( $arg === '--help' || $arg === '-h' )                  { $opts['help']    = true;  continue; }
}

// ── Help ─────────────────────────────────────────────────────────────────────
if ( $opts['help'] )
{
    echo <<<EOH
ezptestrunner.php — Exponential / eZ Publish Legacy PHPUnit 10 wrapper

Usage:
  php tests/bin/ezptestrunner.php [options]

Options:
  --suite=<name>    Run a specific PHPUnit testsuite (defined in phpunit.xml)
                    Known suites: security, kernel-classes, kernel-content,
                                  kernel-datatypes, lib
  --group=<name>    Run tests in a specific @group (e.g. database)
  --filter=<regex>  Run only tests matching the given regex/method name
  --all             Run the full combined test suite (lint + functional + phpunit)
  --report          When combined with --all, write a dated report file
  --list-suites     List all defined testsuites in phpunit.xml
  --list-groups     List all @group annotations in the test suite
  --no-colors       Disable ANSI colour output
  --verbose, -v     Verbose test output
  --help, -h        Show this help

Examples:
  php tests/bin/ezptestrunner.php --suite=security
  php tests/bin/ezptestrunner.php --filter=testSec01
  php tests/bin/ezptestrunner.php --all --report
  php tests/bin/ezptestrunner.php --list-suites

EOH;
    exit( 0 );
}

// ── Full combined mode ────────────────────────────────────────────────────────
if ( $opts['all'] )
{
    $args = '';
    if ( $opts['suite'] )  $args .= ' ' . escapeshellarg( $opts['suite'] );
    if ( $opts['report'] ) $args .= ' --report';

    $cmd = 'php ' . escapeshellarg( EZP_RUN_ALL ) . $args;
    echo "Running full combined suite: $cmd\n\n";
    passthru( $cmd, $exitCode );
    exit( $exitCode );
}

// ── PHPUnit direct mode ───────────────────────────────────────────────────────
$phpunit  = escapeshellarg( 'php' ) . ' ' . escapeshellarg( EZP_PHPUNIT_BIN );
$phpunit .= ' --configuration ' . escapeshellarg( EZP_PHPUNIT_XML );
$phpunit .= $opts['colors'] ? ' --colors=always' : ' --colors=never';
if ( $opts['verbose'] )     $phpunit .= ' --verbose';

if ( $opts['list-suites'] ) { $phpunit .= ' --list-suites'; }
elseif ( $opts['list-groups'] ) { $phpunit .= ' --list-groups'; }
else {
    if ( $opts['suite'] )   $phpunit .= ' --testsuite '   . escapeshellarg( $opts['suite'] );
    if ( $opts['group'] )   $phpunit .= ' --group '       . escapeshellarg( $opts['group'] );
    if ( $opts['filter'] )  $phpunit .= ' --filter '      . escapeshellarg( $opts['filter'] );
}

// Announce what we're running
echo "ezptestrunner: " . $phpunit . "\n\n";

passthru( $phpunit, $exitCode );
exit( $exitCode );
