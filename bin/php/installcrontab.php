#!/usr/bin/env php
<?php
/**
 * File containing the installcrontab.php script.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/installcrontab.php; this file is the entry point.
\Exponential\Command\Kernel\Installcrontab::main( __FILE__ );
