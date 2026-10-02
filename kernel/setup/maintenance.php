<?php
/**
 * Entry point of kernel/setup/maintenance.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/*
 Setup > Maintenance: take the site offline for a maintenance window and back,
 the same as bin/php/maintenance.php on|off. The administration stays reachable
 while it is on (and, when ticked, the address it was switched on from), or the page
 that switches it off again could not be reached.
*/

// The code is in kernel/private/classes/views/setup/maintenance.php (#207); this file is the entry point.
return \Exponential\View\Kernel\Setup\Maintenance::main( __FILE__, get_defined_vars() );
