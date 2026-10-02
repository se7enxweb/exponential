<?php
/**
 * @description Fetch and import configured RSS feeds into the content tree
 *
 * File containing the rssimport.php cronjob
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

//For ezUser, we would make this the ezUser class id but otherwise just pick and choose.

//fetch this class

// The code is in kernel/private/classes/cronjobs/rssimport.php (#207); this file is the entry point.
return \Exponential\Cronjob\Kernel\Rssimport::main( __FILE__, get_defined_vars() );
