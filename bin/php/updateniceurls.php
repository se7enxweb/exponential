#!/usr/bin/env php
<?php
/**
 * File containing the updateniceurls.php bin script
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

set_time_limit ( 0 );

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/updateniceurls.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Updateniceurls::main( __FILE__ );
