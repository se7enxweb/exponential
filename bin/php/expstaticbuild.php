#!/usr/bin/env php
<?php
/**
 * File containing the expstaticbuild.php script.
 *
 * Build a static copy of a target's siteaccess into its folder (static publishing): crawled as the anonymous user, links rewritten, files collected, extras and manifest written.
 *
 * @alias exp:static:build
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expstaticbuild.php; this file is the entry point.
\Exponential\Command\Kernel\Expstaticbuild::main( __FILE__ );
