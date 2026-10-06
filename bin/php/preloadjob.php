#!/usr/bin/env php
<?php
/**
 * File containing the preloadjob.php script.
 *
 * Runs one preload for Setup > Preload in the background: the page starts it
 * (setup/preloadjob) and follows its progress from the events this script
 * writes, one JSON line each, to var/<var dir>/preload/<id>.jsonl. No web
 * request stays open while the site is crawled, so neither a web server's
 * request time limit nor a proxy that buffers streamed answers can stop it.
 *
 * A file <id>.stop next to the events asks the run to end; it is looked at
 * between two events.
 *
 * Usage (started by setup/preloadjob, not meant to be typed):
 *   php bin/php/preloadjob.php --id=<hex> [--target=<siteaccess>] [--max-pages=<n>] [--max-depth=<n>] [--images]
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// Started detached by the web server, whose end of the pipes is closed: move
// stdin and stdout to /dev/null and stderr to the run's error file (the
// lowest free descriptors are reused, so the order puts them at 0, 1 and 2).
// The error file is <var dir>/preload/<id>.err, named by the page that started
// the run: what goes wrong before the run can write its own status (a missing
// extension, a fatal error while starting) is kept there, and the page shows
// it, instead of the run silently never starting.
if ( isset( $argv ) && preg_grep( '#^--id=[a-f0-9]{16}$#', $argv ) && getenv( 'EXP_PRELOAD_DETACHED' ) === '1' )
{
    $expPreloadErrors = (string)getenv( 'EXP_PRELOAD_ERRORS' );
    if ( !preg_match( '#^/[^\0]*/preload/[a-f0-9]{16}\.err$#', $expPreloadErrors ) || strpos( $expPreloadErrors, '/../' ) !== false )
        $expPreloadErrors = '/dev/null';
    fclose( STDIN );
    fclose( STDOUT );
    fclose( STDERR );
    $expPreloadStdin  = fopen( '/dev/null', 'r' );
    $expPreloadStdout = fopen( '/dev/null', 'w' );
    $expPreloadStderr = @fopen( $expPreloadErrors, 'a' ) ?: fopen( '/dev/null', 'w' );
    ini_set( 'display_errors', 'stderr' );
}

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/preloadjob.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Preloadjob::main( __FILE__ );
