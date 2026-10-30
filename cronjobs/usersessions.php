<?php
/**
 * @description Signed-in sessions ("Your devices"): ends the sessions not used for [Session] SessionTimeout, removes ended ones after [UserSessionSettings] RetentionDays
 * @link doc/eng-us/features/6.0/user-sessions.md
 *
 * File containing the usersessions.php cronjob
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/usersessions.php; this file is the entry point.
return \Exponential\Cronjob\Kernel\Usersessions::main( __FILE__, get_defined_vars() );
