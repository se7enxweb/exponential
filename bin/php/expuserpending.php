#!/usr/bin/env php
<?php
/**
 * File containing the expuserpending.php script.
 *
 * List the registrations that wait: for the e-mail address to be confirmed, for an administrator, or for an approval item, newest first.
 *
 * @alias exp:user:pending
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expuserpending.php; this file is the entry point.
\Exponential\Command\Kernel\Expuserpending::main( __FILE__ );
