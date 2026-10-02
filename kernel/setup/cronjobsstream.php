<?php
/**
 * File containing the setup/cronjobsstream view.
 *
 * Follows a running cronjob's output and relays it as Server-Sent Events, so
 * the console shows the job as it works rather than a page that has to be
 * reloaded to learn anything.
 *
 * The same transport as setup/preloadstream and setup/staticcachestream. The
 * difference is what is being followed: those run the work in this process,
 * this one tails a log file written by a process that outlives the request, and
 * stops when that process is gone and its output has been read to the end.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/views/setup/cronjobsstream.php (#207); this file is the entry point.
return \Exponential\View\Kernel\Setup\Cronjobsstream::main( __FILE__, get_defined_vars() );
