#!/usr/bin/env php
<?php
/**
 * File containing the expcouchbaseimport.php script: the exp:couchbase:import command.
 *
 * Imports the collections of a Couchbase scope into the site's database or a data table connection; see
 * doc/eng-us/features/6.0/couchbase-driver.md.
 *
 * @alias exp:couchbase:import
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expcouchbaseimport.php; this file is the entry point.
\Exponential\Command\Kernel\Expcouchbaseimport::main( __FILE__ );
