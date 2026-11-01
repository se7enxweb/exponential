#!/usr/bin/env php
<?php
/**
 * File containing the expstatictargets.php script.
 *
 * List the targets of static publishing (staticcache.ini [Target_<id>]) with their siteaccess, links and folder, and check each one.
 *
 * @alias exp:static:targets
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expstatictargets.php; this file is the entry point.
\Exponential\Command\Kernel\Expstatictargets::main( __FILE__ );
