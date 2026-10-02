<?php
/**
 * @description Resume content jobs whose worker died and start queued jobs nobody started
 *
 * Content jobs cronjob part (frequent group)
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/contentjobs.php (#207); this file is the entry point.
return \Exponential\Cronjob\Kernel\Contentjobs::main( __FILE__, get_defined_vars() );
