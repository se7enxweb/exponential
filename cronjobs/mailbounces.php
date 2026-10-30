<?php
/**
 * @description The bounce mailbox: hard bounces and spam complaints go on the suppression list (mailpreferences.ini [BounceSettings])
 * @link doc/eng-us/guides/mail-preferences-administrator.md
 *
 * File containing the mailbounces.php cronjob
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/mailbounces.php; this file is the entry point.
return \Exponential\Cronjob\Kernel\Mailbounces::main( __FILE__, get_defined_vars() );
