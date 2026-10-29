<?php
/**
 * Entry point of kernel/setup/templateedit.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// Redirect to visual module which is the correct place for this functionality

// The code is in kernel/private/classes/views/setup/templateedit.php (#207); this file is the entry point.
return \Exponential\View\Kernel\Setup\Templateedit::main( __FILE__, get_defined_vars() );
