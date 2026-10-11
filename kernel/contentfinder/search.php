<?php
/**
 * Entry point of kernel/contentfinder/search.php: the content finder's search form and results
 * (doc/features/6.0/content-finder.md).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/views/contentfinder/search.php; this file is the entry point.
// A server process started before the class existed (a Velocity worker) cannot load it: not available, no 500.
/** @var array $Params the view parameters, set by eZModule when it includes this file */
if ( !class_exists( 'Exponential\\View\\Kernel\\Contentfinder\\Search' ) || !class_exists( 'expContentFinder' ) )
    return $Params['Module']->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
return \Exponential\View\Kernel\Contentfinder\Search::main( __FILE__, get_defined_vars() );
