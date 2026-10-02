<?php
/**
 * File containing the setup/cronjobs view.
 *
 * The cronjobs console: what parts this installation defines, which scripts
 * each one runs, what is running now, and the output of the last run.
 *
 * Launching, stopping and clearing are actions on this view rather than views
 * of their own, so they go through the module's post action handling and its
 * single managecronjobs policy, the way setup/cache does.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/views/setup/cronjobs.php (#207); this file is the entry point.
return \Exponential\View\Kernel\Setup\Cronjobs::main( __FILE__, get_defined_vars() );
