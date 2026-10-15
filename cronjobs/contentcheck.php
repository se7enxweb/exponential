<?php
/**
 * @description Build the reference index of the content checks (unused assets), for installations where a page may not build it (contentcheck.ini BuildOnRequest=false)
 * @link doc/features/6.0/content-checks.md
 *
 * File containing the contentcheck.php cronjob
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/contentcheck.php; this file is the entry point.
return \Exponential\Cronjob\Kernel\Contentcheck::main( __FILE__, get_defined_vars() );
