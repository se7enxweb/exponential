<?php
/**
 * Entry point of exphelp/extensions, the documentation of the active extensions (doc/eng-us/features/6.0/help-system.md).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/views/exphelp/extensions.php; this file is the entry point.
// A server process started before the class existed (a Velocity worker) cannot load it: not available, no 500.
/** @var array $Params the view parameters, set by eZModule when it includes this file */
if ( !class_exists( 'Exponential\\View\\Kernel\\Exphelp\\Extensions' ) || !method_exists( 'expHelpDocumentIndex', 'extensions' ) )
    return $Params['Module']->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
return \Exponential\View\Kernel\Exphelp\Extensions::main( __FILE__, get_defined_vars() );
