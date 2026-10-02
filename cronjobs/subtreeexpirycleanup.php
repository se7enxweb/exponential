<?php
/**
 * @description Clean up subtree expiry entries from the URL cache tables
 *
 * File containing the subtreeexpirycleanup.php cronjob
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/subtreeexpirycleanup.php (#207); this file is the entry point.
return \Exponential\Cronjob\Kernel\Subtreeexpirycleanup::main( __FILE__, get_defined_vars() );
