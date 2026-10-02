<?php
/**
 * Entry point of kernel/content/move.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/*!
  This script is just a wrapper for action.php with action set to 'MoveNodeRequest'
  and has been created for moving operation to be simply invoked using URI like /content/move/NODE_ID.
*/

// The code is in kernel/private/classes/views/content/move.php (#207); this file is the entry point.
return \Exponential\View\Kernel\Content\Move::main( __FILE__, get_defined_vars() );
