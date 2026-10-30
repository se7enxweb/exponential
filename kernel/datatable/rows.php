<?php
/**
 * Entry point of kernel/datatable/rows.php: the rows of one data table, page by page.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/views/datatable/rows.php (#207); this file is the entry point.
if ( !class_exists( 'Exponential\\View\\Kernel\\Datatable\\Rows' ) )
    return get_defined_vars()['Params']['Module']->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
return \Exponential\View\Kernel\Datatable\Rows::main( __FILE__, get_defined_vars() );
