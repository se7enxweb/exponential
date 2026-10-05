#!/usr/bin/env php
<?php
/**
 * File containing the notificationsubscriptions.php script.
 *
 * Lists the subtree notification subscriptions per user and subtree, or removes those whose content is gone.
 *
 * @alias exp:notification:subscriptions
 * @alias exp:notify:subscriptions
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/notificationsubscriptions.php; this file is the entry point.
\Exponential\Command\Kernel\Notificationsubscriptions::main( __FILE__ );
