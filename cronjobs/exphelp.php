<?php
/**
 * @description Help maintained as content: removes old answers to "Was this helpful?" and records the DITA export of the help in git when exphelp.ini [ContentSettings] DitaHistoryDirectory is set
 * @link doc/eng-us/features/6.0/help-content.md
 *
 * File containing the exphelp.php cronjob
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/exphelp.php; this file is the entry point.
return \Exponential\Cronjob\Kernel\Exphelp::main( __FILE__, get_defined_vars() );
