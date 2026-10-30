#!/usr/bin/env php
<?php
/**
 * Trash purge script
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/trashpurge.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Trashpurge::main( __FILE__ );
