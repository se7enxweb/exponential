#!/usr/bin/env php
<?php
/**
 * File containing the ezcsvimport.php bin script
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/ezcsvimport.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Ezcsvimport::main( __FILE__ );
