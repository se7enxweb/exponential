<?php
/**
 * @description Process and dispatch pending notification events to subscribers
 *
 * File containing the notification.php cronjob
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/notification.php (#207); this file is the entry point.
return \Exponential\Cronjob\Kernel\Notification::main( __FILE__, get_defined_vars() );
