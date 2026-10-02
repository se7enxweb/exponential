<?php
/**
 * @description Index pending content modifications in the search engine
 *
 * File containing the indexcontent.php cronjob
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/indexcontent.php (#207); this file is the entry point.
return \Exponential\Cronjob\Kernel\Indexcontent::main( __FILE__, get_defined_vars() );
