#!/usr/bin/env php
<?php
/**
 * File containing the expstaticpublish.php script.
 *
 * Publish a target's static build (static publishing): commit it in the target's git repository, tag it static/<target>/<n> and push it, never forced.
 *
 * @alias exp:static:publish
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expstaticpublish.php; this file is the entry point.
\Exponential\Command\Kernel\Expstaticpublish::main( __FILE__ );
