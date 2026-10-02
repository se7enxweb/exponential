<?php
/**
 * File containing the setup/staticcachestream view.
 *
 * Runs the static cache generator and streams its progress as Server-Sent
 * Events, so the operator watches pages being written instead of waiting on a
 * request that a proxy is free to time out.
 *
 * Same construction as setup/preloadstream: the work is done by a runner in
 * this process, and this file is only the transport.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/views/setup/staticcachestream.php (#207); this file is the entry point.
return \Exponential\View\Kernel\Setup\Staticcachestream::main( __FILE__, get_defined_vars() );
