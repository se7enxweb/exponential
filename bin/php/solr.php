#!/usr/bin/env php
<?php
/**
 * File containing the Solr search server control script.
 *
 * @description Control the Solr search server, once it is installed and configured
 * @alias search
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

require 'autoload.php';

// The code is in kernel/private/classes/commands/solr.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Solr::main( __FILE__ );
