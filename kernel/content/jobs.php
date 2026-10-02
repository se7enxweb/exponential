<?php
/**
 * Entry point of kernel/content/jobs.php: the list of content jobs (subtree removes and copies in the
 * background), the user's own, or everybody's with the content/jobs policy.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/views/content/jobs.php (#207); this file is the entry point.
// A server process started before the class existed (a Velocity worker) cannot load it: not available, no 500.
if ( !class_exists( 'Exponential\\View\\Kernel\\Content\\Jobs' ) )
    return $Params['Module']->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
return \Exponential\View\Kernel\Content\Jobs::main( __FILE__, get_defined_vars() );
