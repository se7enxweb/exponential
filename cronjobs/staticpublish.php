<?php
/**
 * @description Static publishing: push the targets whose publish could not be pushed again (runs with push state pending)
 *
 * Static publishing cronjob part ([CronjobPart-staticpublish])
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/staticpublish.php; this file is the entry point.
if ( !class_exists( 'Exponential\\Cronjob\\Kernel\\Staticpublish' ) )
    return 0;
return \Exponential\Cronjob\Kernel\Staticpublish::main( __FILE__, get_defined_vars() );
