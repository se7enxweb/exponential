#!/usr/bin/env php
<?php
/**
 * File containing the systeminfo.php script.
 *
 * The facts and health checks of Setup > System information, for the command line: Exponential, the server, PHP,
 * OPcache, the database, storage, caches, cronjobs, mail, locale and the extensions. Secrets are never printed.
 *
 * @alias exp:system:info
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/systeminfo.php; this file is the entry point.
\Exponential\Command\Kernel\Systeminfo::main( __FILE__ );
