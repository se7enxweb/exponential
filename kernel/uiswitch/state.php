<?php
/**
 * Entry point of kernel/uiswitch/state.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/views/uiswitch/state.php; this file is the entry point.
// A server process started before the classes existed (a Velocity worker) cannot load them: not available, no 500.
if ( !class_exists( 'Exponential\\View\\Kernel\\Uiswitch\\State' ) || !class_exists( 'expUISwitch' ) )
    return isset( $Params['Module'] ) ? $Params['Module']->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) : null;
return \Exponential\View\Kernel\Uiswitch\State::main( __FILE__, get_defined_vars() );
