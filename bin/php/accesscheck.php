#!/usr/bin/env php
<?php
/**
 * File containing the accesscheck.php script.
 *
 * Checks what a user may do with a node or object, and which limitation refuses it.
 *
 * @alias exp:access:check
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/accesscheck.php; this file is the entry point.
\Exponential\Command\Kernel\Accesscheck::main( __FILE__ );
