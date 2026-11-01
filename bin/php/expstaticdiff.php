#!/usr/bin/env php
<?php
/**
 * File containing the expstaticdiff.php script.
 *
 * What the last static build of a target changed, whether its folder still matches the manifest, and whether every link of it works when served below a path.
 *
 * @alias exp:static:diff
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expstaticdiff.php; this file is the entry point.
\Exponential\Command\Kernel\Expstaticdiff::main( __FILE__ );
