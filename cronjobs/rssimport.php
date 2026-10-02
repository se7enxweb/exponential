<?php
/**
 * @description Fetch and import configured RSS feeds into the content tree
 *
 * File containing the rssimport.php cronjob
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

//For ezUser, we would make this the ezUser class id but otherwise just pick and choose.

//fetch this class

// The code is in kernel/private/classes/cronjobs/rssimport.php (#207); this file is the entry point.
return \Exponential\Cronjob\Kernel\Rssimport::main( __FILE__, get_defined_vars() );
