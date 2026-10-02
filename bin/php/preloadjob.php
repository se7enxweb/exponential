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
 *   php bin/php/preloadjob.php --id=<hex> [--target=<siteaccess>] [--max-pages=<n>] [--max-depth=<n>]
 *
 * @copyright Copyright (C) 1998 - 2026 7x and others. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// Started detached by the web server, whose end of the pipes is closed: move
// stdin, stdout and stderr to /dev/null and the run's own log first (the
// lowest free descriptors are reused, so the order puts them at 0, 1 and 2).
if ( isset( $argv ) && preg_grep( '#^--id=[a-f0-9]{16}$#', $argv ) && getenv( 'EXP_PRELOAD_DETACHED' ) === '1' )
{
    fclose( STDIN );
    fclose( STDOUT );
    fclose( STDERR );
    $expPreloadStdin  = fopen( '/dev/null', 'r' );
    $expPreloadStdout = fopen( '/dev/null', 'w' );
    $expPreloadStderr = fopen( '/dev/null', 'w' );
}

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/preloadjob.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Preloadjob::main( __FILE__ );
