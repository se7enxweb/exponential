#!/usr/bin/env php
<?php
/**
 * File containing the expstaticpush.php script.
 *
 * Push the static publishing targets whose push failed again (runs with push state pending), never forced.
 *
 * @alias exp:static:push
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expstaticpush.php; this file is the entry point.
\Exponential\Command\Kernel\Expstaticpush::main( __FILE__ );
