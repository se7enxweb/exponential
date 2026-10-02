<?php
/**
 * @description Audit: sink spools, alert rules, rotation by day, archives, retention and checkpoints
 *
 * Audit cronjob part ([CronjobPart-audit], also in the frequent group)
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/audit.php (#207); this file is the entry point.
return \Exponential\Cronjob\Kernel\Audit::main( __FILE__, get_defined_vars() );
