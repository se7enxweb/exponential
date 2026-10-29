#!/usr/bin/env php
<?php
/**
 * File containing the mailconsent.php script.
 *
 * The consent log of the e-mail preferences: list, export as CSV, retention cleanup.
 *
 * @alias exp:mail:consent
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/mailconsent.php; this file is the entry point.
\Exponential\Command\Kernel\Mailconsent::main( __FILE__ );
