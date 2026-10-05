#!/usr/bin/env php
<?php
/**
 * File containing the mailgate.php script.
 *
 * Test the mail gate: who of the given addresses would get a mail of a category, and what the decorated mail looks like.
 *
 * @alias exp:mail:gate
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/mailgate.php; this file is the entry point.
\Exponential\Command\Kernel\Mailgate::main( __FILE__ );
