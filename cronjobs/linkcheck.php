<?php
/**
 * @description Check all internal and external links in published content for broken URLs
 *
 * File containing the linkcheck.php cronjob
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/linkcheck.php (#207); this file is the entry point.
return \Exponential\Cronjob\Kernel\Linkcheck::main( __FILE__, get_defined_vars() );
