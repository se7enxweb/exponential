<?php
/**
 * Entry point of kernel/datatable/schema.php: create or upgrade data tables on a connection.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/views/datatable/schema.php (#207); this file is the entry point.
if ( !class_exists( 'Exponential\\View\\Kernel\\Datatable\\Schema' ) )
    return get_defined_vars()['Params']['Module']->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
return \Exponential\View\Kernel\Datatable\Schema::main( __FILE__, get_defined_vars() );
