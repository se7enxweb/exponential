<?php
/**
 * @description Start the data table synchronisations that are due (scheduled and live, by their interval)
 *
 * Data tables cronjob part (frequent group)
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/datatablesync.php (#207); this file is the entry point.
if ( !class_exists( 'Exponential\\Cronjob\\Kernel\\Datatablesync' ) )
    return 0;
return \Exponential\Cronjob\Kernel\Datatablesync::main( __FILE__, get_defined_vars() );
