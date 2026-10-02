<?php
/**
 * @description Process pending workflow events and advance stalled workflows
 *
 * File containing the workflow.php cronjob
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/workflow.php (#207); this file is the entry point.
return \Exponential\Cronjob\Kernel\Workflow::main( __FILE__, get_defined_vars() );
