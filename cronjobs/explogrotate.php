<?php
/**
 * @description Logs: rotate the logs over their MaxSize, prune and compress their archives (off unless a part runs it)
 *
 * Log rotation cronjob part ([CronjobPart-logs]: php runcronjobs.php logs); doc/eng-us/features/6.0/log-rotation.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/explogrotate.php; this file is the entry point.
return \Exponential\Cronjob\Kernel\Explogrotate::main( __FILE__, get_defined_vars() );
