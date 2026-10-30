<?php
/**
 * @description User audit log: removes the rows of expuser_audit older than audit.ini [UserAuditSettings] RetentionDays
 * @link doc/eng-us/features/6.0/user-audit.md
 *
 * File containing the useraudit.php cronjob
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/useraudit.php; this file is the entry point.
return \Exponential\Cronjob\Kernel\Useraudit::main( __FILE__, get_defined_vars() );
