<?php
/**
 * The extension point survey view.
 *
 * The RAD tools page lists the points somebody thought to write down. This one
 * lists what is actually there: every setting in every ini file on this
 * installation that names a class, every directory a handler is looked for in,
 * every interface the kernel declares, and every module view that could be
 * replaced or added to. It is read off disk on every request, so an extension
 * installed this morning is in it this afternoon.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

// The code is in kernel/private/classes/views/setup/radsurvey.php (#207); this file is the entry point.
return \Exponential\View\Kernel\Setup\Radsurvey::main( __FILE__, get_defined_vars() );
