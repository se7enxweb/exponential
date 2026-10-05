#!/usr/bin/env php
<?php
/**
 * File containing the notificationstatus.php script.
 *
 * Shows the notification system: pending events, digest items, subscriptions, the last runs and problems.
 *
 * @alias exp:notification:status
 * @alias exp:notify:status
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/notificationstatus.php; this file is the entry point.
\Exponential\Command\Kernel\Notificationstatus::main( __FILE__ );
