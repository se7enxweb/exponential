<?php
/**
 * File containing the setup/preloadstream view.
 *
 * Runs the preloader and streams its progress as Server-Sent Events.
 *
 * The reference implementation in exponentialbasic spawned the command line
 * script with popen and relayed its stdout. This runs expPreloadRunner in the
 * same process instead, so there is no dependency on shell_exec being enabled,
 * on wget being installed, or on the web user being able to find a cli php
 * binary - and the exit path is an ordinary return rather than an exit code
 * recovered from a pipe.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/views/setup/preloadstream.php (#207); this file is the entry point.
return \Exponential\View\Kernel\Setup\Preloadstream::main( __FILE__, get_defined_vars() );
