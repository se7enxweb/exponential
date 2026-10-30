<?php
/**
 * Entry point of kernel/datatable/table.php: one data table, described from its class and from its database.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/views/datatable/table.php (#207); this file is the entry point.
if ( !class_exists( 'Exponential\\View\\Kernel\\Datatable\\Table' ) )
    return get_defined_vars()['Params']['Module']->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
return \Exponential\View\Kernel\Datatable\Table::main( __FILE__, get_defined_vars() );
