<?php
/**
 * @description Check all internal and external links in published content for broken URLs
 *
 * File containing the linkcheck.php cronjob
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/linkcheck.php (#207); this file is the entry point.
return \Exponential\Cronjob\Kernel\Linkcheck::main( __FILE__, get_defined_vars() );
