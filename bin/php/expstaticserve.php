#!/usr/bin/env php
<?php
/**
 * File containing the expstaticserve.php script.
 *
 * Preview the static build of a target locally: PHP's built-in web server serves its folder below the target's base path, as GitHub Pages serves a project site.
 *
 * @alias exp:static:serve
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expstaticserve.php; this file is the entry point.
\Exponential\Command\Kernel\Expstaticserve::main( __FILE__ );
