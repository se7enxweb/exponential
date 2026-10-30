<?php
/**
 * The module table wizard view.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/views/setup/moduletable.php (#207); this file is the entry point.
if ( !class_exists( 'Exponential\\View\\Kernel\\Setup\\Moduletable' ) )
    return get_defined_vars()['Params']['Module']->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
return \Exponential\View\Kernel\Setup\Moduletable::main( __FILE__, get_defined_vars() );
