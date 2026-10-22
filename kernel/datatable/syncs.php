<?php
/**
 * Entry point of kernel/datatable/syncs.php: the synchronisations between content and data tables.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/views/datatable/syncs.php (#207); this file is the entry point.
if ( !class_exists( 'Exponential\\View\\Kernel\\Datatable\\Syncs' ) )
    return get_defined_vars()['Params']['Module']->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
return \Exponential\View\Kernel\Datatable\Syncs::main( __FILE__, get_defined_vars() );
