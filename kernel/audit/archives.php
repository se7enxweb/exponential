<?php
/**
 * Entry point of kernel/audit/archives.php: the live files and archives of each audit channel (read-only).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/views/audit/archives.php (#207); this file is the entry point.
// A server process started before the class existed (a Velocity worker) cannot load it: not available, no 500.
if ( !class_exists( 'Exponential\\View\\Kernel\\Audit\\Archives' ) )
    return $Params['Module']->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
return \Exponential\View\Kernel\Audit\Archives::main( __FILE__, get_defined_vars() );
