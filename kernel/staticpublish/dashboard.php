<?php
/**
 * Entry point of kernel/staticpublish/dashboard.php: the targets of static publishing with their state, and the switch.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/views/staticpublish/dashboard.php; this file is the entry point.
// A server process started before the class existed (a Velocity worker) cannot load it: not available, no 500.
if ( !class_exists( 'Exponential\\View\\Kernel\\Staticpublish\\Dashboard' ) )
    return get_defined_vars()['Params']['Module']->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
return \Exponential\View\Kernel\Staticpublish\Dashboard::main( __FILE__, get_defined_vars() );
