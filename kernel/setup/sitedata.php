<?php
/**
 * Entry point of kernel/setup/sitedata.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/views/setup/sitedata.php; this file is the entry point.
// A server process started before the class existed (a Velocity worker) cannot load it: not available, no 500.
$scope = get_defined_vars();
if ( !class_exists( 'Exponential\\View\\Kernel\\Setup\\Sitedata' ) )
    return $scope['Params']['Module']->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
return \Exponential\View\Kernel\Setup\Sitedata::main( __FILE__, $scope );
