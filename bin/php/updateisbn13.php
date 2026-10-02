#!/usr/bin/env php
<?php
/**
 * File containing the updateisbn13.php bin script
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * This script updates the different ranges used by the ISBN standard to
 * calculate the length of Registration group, Registrant and Publication element
 *
 * It gets the values from xml file normally provided by International ISBN Agency
 * http://www.isbn-international.org/agency?rmxml=1
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/updateisbn13.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Updateisbn13::main( __FILE__ );
