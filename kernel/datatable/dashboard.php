<?php
/**
 * Entry point of kernel/datatable/dashboard.php: the data tables of the installation.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/views/datatable/dashboard.php (#207); this file is the entry point.
// A server process started before the class existed (a Velocity worker) cannot load it: not available, no 500.
if ( !class_exists( 'Exponential\\View\\Kernel\\Datatable\\Dashboard' ) )
    return get_defined_vars()['Params']['Module']->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
return \Exponential\View\Kernel\Datatable\Dashboard::main( __FILE__, get_defined_vars() );
