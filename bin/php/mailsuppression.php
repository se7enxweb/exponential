#!/usr/bin/env php
<?php
/**
 * File containing the mailsuppression.php script.
 *
 * The suppression list of the e-mail gate: list, check, add and lift entries (only hashes are stored).
 *
 * @alias exp:mail:suppression
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/mailsuppression.php; this file is the entry point.
\Exponential\Command\Kernel\Mailsuppression::main( __FILE__ );
