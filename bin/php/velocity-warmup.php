<?php
/**
 * Exponential Velocity parent warm-up.
 *
 * Run once, in the pool's parent process, before it forks its workers
 * (wired through Q.webserver.warmup). It loads the Exponential kernel and
 * renders a few representative pages, so the class tables, parsed
 * configuration, compiled templates and the arena they live in are built HERE,
 * in the parent -- and then handed to every worker copy-on-write by fork().
 *
 * Without this each worker builds that state privately on its first request:
 * measured at ~39 MB for a cold front-page render, held per worker. With it a
 * worker inherits the warmed arena shared and its first render was measured at
 * ~5 MB private. Across hundreds of workers that is the difference between
 * tens of gigabytes and a few.
 *
 * Two rules make it safe to run before a fork:
 *   1. It opens no resource a child must not share. The kernel connects to the
 *      database during a render, so the connection is closed and the global
 *      instance nulled before returning; each worker reconnects with its own.
 *   2. It renders only anonymous, side-effect-light GETs, and never exits: a
 *      throw here is caught by the pool, which then lets workers warm lazily
 *      the old way. The warm-up is an optimisation, never a dependency.
 *
 * It prints nothing on the happy path; the pool reports how much it warmed.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The parent's working directory is the document root (--root). autoload.php,
// the kernel and the configuration are all resolved from there.
$root = getcwd();
if (!is_file($root . '/autoload.php')) {
    // Nothing to warm without the app; let workers warm themselves.
    return;
}

// A clean, anonymous request context. No cookies, no session, no auth -- the
// warmed state is inherited by every worker, so it must belong to none of them.
$_COOKIE = array();
$_SESSION = array();
$_POST = array();
$_FILES = array();
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'localhost';
unset($_SERVER['HTTP_COOKIE'], $_SERVER['HTTP_AUTHORIZATION']);

// The globals that exist before Exponential is loaded: the server's own. What
// the kernel and the render add after this is removed again at the end (see
// "Every other global the render left" in VelocityWarmup::run()), except what
// the server keeps across requests anyway.
//
// The pool requires this file inside a function, so its variables are not
// globals: the snapshot and the root are handed to VelocityWarmup::run()
// through $GLOBALS by name. Kept in local variables only, run() found neither:
// it checked /var/maintenance.json instead of the site's, and with no snapshot
// it removed every global but the superglobals and the kept ones.
$GLOBALS['__warmupGlobalsBefore'] = array_flip(array_keys($GLOBALS));
$GLOBALS['root'] = $root;

require $root . '/autoload.php';

// The code is in kernel/private/classes/commands/velocity-warmup.php (#207); this file is the entry point.
\Exponential\Command\Kernel\VelocityWarmup::main( __FILE__ );
