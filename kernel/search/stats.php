<?php
/**
 * Entry point of kernel/search/stats.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The sizes on offer are configured, not written here; the preference holds
// the position in that list, which is what it has always held.

// The code is in kernel/private/classes/views/search/stats.php (#207); this file is the entry point.
return \Exponential\View\Kernel\Search\Stats::main( __FILE__, get_defined_vars() );
