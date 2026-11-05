<?php
/**
 * Entry point of kernel/exphelp/feedback.php (doc/eng-us/features/6.0/help-content.md).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/views/exphelp/feedback.php; this file is the entry point.
// A server process started before the class existed (a Velocity worker) cannot load it: not available, no 500.
/** @var array $Params the view parameters, set by eZModule when it includes this file */
if ( !class_exists( 'Exponential\\View\\Kernel\\Exphelp\\Feedback' ) || !class_exists( 'expHelp' ) || !class_exists( 'expHelpContentProvider' ) )
    return $Params['Module']->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
// a reader without the interface language switch keeps the language chosen with /(lang)/<key> in the links
method_exists( 'expHelp', 'beginReading' ) && expHelp::beginReading( $Params );
$helpResult = \Exponential\View\Kernel\Exphelp\Feedback::main( __FILE__, get_defined_vars() );
return method_exists( 'expHelp', 'endReading' ) ? expHelp::endReading( $helpResult, $Params['Module'] ) : $helpResult;
