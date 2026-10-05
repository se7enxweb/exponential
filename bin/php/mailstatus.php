#!/usr/bin/env php
<?php
/**
 * File containing the mailstatus.php script.
 *
 * Show the e-mail preference system: tables, gate, footer, categories, suppression, consent log, the gate's last decisions and problems.
 *
 * @alias exp:mail:status
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/mailstatus.php; this file is the entry point.
\Exponential\Command\Kernel\Mailstatus::main( __FILE__ );
