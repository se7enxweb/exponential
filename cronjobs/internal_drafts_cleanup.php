<?php
/**
 * @description Remove internal drafts that exceed the configured age limit
 *
 * File containing the internal_drafts_cleanup.php.php cronjob
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/internal_drafts_cleanup.php (#207); this file is the entry point.
return \Exponential\Cronjob\Kernel\InternalDraftsCleanup::main( __FILE__, get_defined_vars() );
