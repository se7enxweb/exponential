<?php
/**
 * File containing the setup/preloadjob view.
 *
 * The preloader console's back end (expPreloadJob), answering JSON:
 *   POST setup/preloadjob with Action=start, MaxPages, MaxDepth, SiteAccess
 *        -> { "id": "..." }
 *   POST setup/preloadjob with Action=stop, JobID
 *   GET  setup/preloadjob/<id>/<offset>
 *        -> { "events": [...], "offset": n, "done": bool }
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/views/setup/preloadjob.php (#207); this file is the entry point.
return \Exponential\View\Kernel\Setup\Preloadjob::main( __FILE__, get_defined_vars() );
