#!/usr/bin/env php
<?php
/**
 * File containing the configphp.php script.
 *
 * List, read, set and unset the define() settings of config.php (set and unset: dry run unless --apply).
 *
 *   ./console exp:config:list [--only-set] [--format=text|json]
 *   ./console exp:config:get <NAME> [--format=text|json]
 *   ./console exp:config:set <NAME> <value> [--type=<type>] [--apply]
 *   ./console exp:config:unset <NAME> [--apply]
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// A config.php that does not compile would stop autoload.php before the command could say so: autoload.php checks
// it first and leaves it unread (EXP_CONFIG_PHP_SKIP); the command refuses it with the error.
define( 'EXP_CONFIG_PHP_TOLERATE', true );

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/configphp.php; this file is the entry point.
\Exponential\Command\Kernel\Configphp::main( __FILE__ );
