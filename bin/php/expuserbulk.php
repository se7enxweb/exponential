#!/usr/bin/env php
<?php
/**
 * File containing the expuserbulk.php script.
 *
 * Disable, enable, delete, approve, reject or unlock every account the list filters select, each as the single command does it; --apply needs --confirm with the number the dry run showed.
 *
 * @alias exp:user:bulk
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expuserbulk.php; this file is the entry point.
\Exponential\Command\Kernel\Expuserbulk::main( __FILE__ );
