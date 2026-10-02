#!/usr/bin/env php
<?php
/**
 * File containing the updateisbn13.php bin script
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
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
