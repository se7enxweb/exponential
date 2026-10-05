#!/usr/bin/env php
<?php
/**
 * File containing the mailbounces.php script.
 *
 * Read the bounce mailbox: hard bounces and spam complaints go on the suppression list.
 *
 * @alias exp:mail:bounces
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/mailbounces.php; this file is the entry point.
\Exponential\Command\Kernel\Mailbounces::main( __FILE__ );
