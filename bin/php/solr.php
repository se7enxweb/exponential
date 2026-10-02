#!/usr/bin/env php
<?php
/**
 * File containing the Solr search server control script.
 *
 * @description Control the Solr search server, once it is installed and configured
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require 'autoload.php';

// The code is in kernel/private/classes/commands/solr.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Solr::main( __FILE__ );
