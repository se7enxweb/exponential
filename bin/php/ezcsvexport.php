#!/usr/bin/env php
<?php
/**
 * File containing the ezcsvexport.php bin script
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/ezcsvexport.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Ezcsvexport::main( __FILE__ );
