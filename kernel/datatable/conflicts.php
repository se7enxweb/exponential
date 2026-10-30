<?php
/**
 * Entry point of kernel/datatable/conflicts.php: the open conflicts of the two-way synchronisations.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/views/datatable/conflicts.php (#207); this file is the entry point.
if ( !class_exists( 'Exponential\\View\\Kernel\\Datatable\\Conflicts' ) )
    return get_defined_vars()['Params']['Module']->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
return \Exponential\View\Kernel\Datatable\Conflicts::main( __FILE__, get_defined_vars() );
