#!/usr/bin/env php
<?php
/**
 * File containing the expstaticstatus.php script.
 *
 * The state of a static publishing target: its git repository, the credential (never its value), the last runs, waiting pushes and stale pages.
 *
 * @alias exp:static:status
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expstaticstatus.php; this file is the entry point.
\Exponential\Command\Kernel\Expstaticstatus::main( __FILE__ );
