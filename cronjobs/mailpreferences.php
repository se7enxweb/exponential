<?php
/**
 * @description E-mail preferences retention: consent log rows of people who are gone, expired confirmations, old suppression entries
 * @link doc/eng-us/guides/mail-preferences-administrator.md
 *
 * File containing the mailpreferences.php cronjob
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/mailpreferences.php; this file is the entry point.
return \Exponential\Cronjob\Kernel\Mailpreferences::main( __FILE__, get_defined_vars() );
