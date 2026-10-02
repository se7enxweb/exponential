<?php
/**
 * Entry point of kernel/content/job.php: the progress page of a content job (a subtree remove or copy
 * running in the background), and its JSON status with ?json=1.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/views/content/job.php (#207); this file is the entry point.
// A server process started before the class existed (a Velocity worker) cannot load it: not available, no 500.
if ( !class_exists( 'Exponential\\View\\Kernel\\Content\\Job' ) )
    return $Params['Module']->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
return \Exponential\View\Kernel\Content\Job::main( __FILE__, get_defined_vars() );
