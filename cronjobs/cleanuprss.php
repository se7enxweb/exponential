<?php
/**
 * @description Trim the content the RSS import has created, keeping the newest items of each feed
 *
 * File containing the cleanuprss.php cronjob.
 *
 * Does nothing until content.ini [RSSImportCleanupSettings] names the classes
 * it may remove, so it is safe to schedule before it has been configured.
 *
 * Ported from the bccleanuprss extension by Brookins Consulting.
 *
 * @copyright Copyright (C) 1999 - 2011 Brookins Consulting. All rights reserved.
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or later)
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/cleanuprss.php (#207); this file is the entry point.
return \Exponential\Cronjob\Kernel\Cleanuprss::main( __FILE__, get_defined_vars() );
