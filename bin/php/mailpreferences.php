#!/usr/bin/env php
<?php
/**
 * File containing the mailpreferences.php script.
 *
 * Show and change the e-mail preferences of a user or an address: categories, master switch, frequency, export, erase.
 *
 * @alias exp:mail:preferences
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/mailpreferences.php; this file is the entry point.
\Exponential\Command\Kernel\Mailpreferences::main( __FILE__ );
