<?php
/**
 * Entry point of kernel/contentcheck/unreferenced.php: the objects below a node that nothing refers to (doc/features/6.0/content-checks.md).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/views/contentcheck/unreferenced.php; this file is the entry point.
// A server process started before the class existed (a Velocity worker) cannot load it: not available, no 500.
/** @var array $Params the view parameters, set by eZModule when it includes this file */
if ( !class_exists( 'Exponential\\View\\Kernel\\Contentcheck\\Unreferenced' ) || !class_exists( 'expContentCheck' ) )
    return $Params['Module']->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
return \Exponential\View\Kernel\Contentcheck\Unreferenced::main( __FILE__, get_defined_vars() );
