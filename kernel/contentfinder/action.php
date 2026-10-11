<?php
/**
 * Entry point of kernel/contentfinder/action.php: the confirmation page and the actions of the content finder
 * (doc/features/6.0/content-finder.md).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/views/contentfinder/action.php; this file is the entry point.
// A server process started before the class existed (a Velocity worker) cannot load it: not available, no 500.
if ( !class_exists( 'Exponential\\View\\Kernel\\Contentfinder\\Action' ) || !class_exists( 'expContentFinderAction' ) )
    return $Params['Module']->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
return \Exponential\View\Kernel\Contentfinder\Action::main( __FILE__, get_defined_vars() );
