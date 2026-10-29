<?php
/**
 * Entry point of kernel/user/login.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

//$Module->setExitStatus( EZ_MODULE_STATUS_SHOW_LOGIN_PAGE );

// The code is in kernel/private/classes/views/user/login.php (#207); this file is the entry point.
return \Exponential\View\Kernel\User\Login::main( __FILE__, get_defined_vars() );
