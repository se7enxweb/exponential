<?php
/**
 * File containing the setup/preload view.
 *
 * Renders the preloader's console. The page itself does no work: it opens an
 * EventSource against setup/preloadstream and prints what arrives, so the
 * operator sees each page warm as it happens instead of waiting on one long
 * request that a proxy is free to time out.
 *
 * Ported from kernel/ezsitemanager/admin/preload.php in exponentialbasic, which
 * did the same thing against a hand rolled template engine.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/views/setup/preload.php (#207); this file is the entry point.
return \Exponential\View\Kernel\Setup\Preload::main( __FILE__, get_defined_vars() );
