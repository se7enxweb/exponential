<?php
/**
 * @description Make the image aliases left in the backlog that no request has made (settings/image.ini [AliasSettings] Backlog)
 *
 * Image alias backlog cronjob part (frequent group)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/imagealiasbacklog.php; this file is the entry point.
if ( !class_exists( 'Exponential\\Cronjob\\Kernel\\Imagealiasbacklog' ) )
    return 0;
return \Exponential\Cronjob\Kernel\Imagealiasbacklog::main( __FILE__, get_defined_vars() );
