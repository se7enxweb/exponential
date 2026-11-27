#!/usr/bin/env php
<?php
/**
 * File containing the expcouchdbimport.php script: the exp:couchdb:import command.
 *
 * Imports the documents of an Apache CouchDB database into the site's database or a data table connection; see
 * doc/eng-us/features/6.0/couchdb-driver.md.
 *
 * @alias exp:couchdb:import
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expcouchdbimport.php; this file is the entry point.
\Exponential\Command\Kernel\Expcouchdbimport::main( __FILE__ );
