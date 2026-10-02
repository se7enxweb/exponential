<?php
/**
 * @description Process pending workflow events and advance stalled workflows
 *
 * File containing the workflow.php cronjob
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/workflow.php (#207); this file is the entry point.
return \Exponential\Cronjob\Kernel\Workflow::main( __FILE__, get_defined_vars() );
