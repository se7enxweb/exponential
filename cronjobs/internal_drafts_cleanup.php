<?php
/**
 * @description Remove internal drafts that exceed the configured age limit
 *
 * File containing the internal_drafts_cleanup.php.php cronjob
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/internal_drafts_cleanup.php (#207); this file is the entry point.
return \Exponential\Cronjob\Kernel\InternalDraftsCleanup::main( __FILE__, get_defined_vars() );
