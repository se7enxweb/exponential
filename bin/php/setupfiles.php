#!/usr/bin/env php
<?php
/**
 * File containing the setupfiles.php script.
 *
 * Put .htaccess and config.php into place from the templates of the installation (dry run unless --apply).
 *
 *   ./console exp:setup:htaccess [--static] [--force] [--apply]
 *   ./console exp:setup:config [--force] [--apply]
 *   ./console exp:setup:status
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// A config.php that does not compile would stop autoload.php, and with it the command that replaces it
// (exp:setup:config --force --apply): autoload.php checks it first and leaves it unread (EXP_CONFIG_PHP_SKIP).
define( 'EXP_CONFIG_PHP_TOLERATE', true );

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/setupfiles.php; this file is the entry point.
\Exponential\Command\Kernel\Setupfiles::main( __FILE__ );
