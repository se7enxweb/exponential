#!/usr/bin/env php
<?php
/**
 * File containing the notificationrun.php script.
 *
 * Runs the notification filter once; --dry-run lists what would be sent.
 *
 * @alias exp:notification:run
 * @alias exp:notify:run
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/notificationrun.php; this file is the entry point.
\Exponential\Command\Kernel\Notificationrun::main( __FILE__ );
