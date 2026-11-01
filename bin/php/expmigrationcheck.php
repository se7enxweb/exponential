#!/usr/bin/env php
<?php
/**
 * File containing the expmigrationcheck.php script.
 *
 * Check migration files without running them: names, headers, order, PHP syntax and classes (the lint for CI).
 *
 * @alias exp:migration:check
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expmigrationcheck.php; this file is the entry point.
\Exponential\Command\Kernel\Expmigrationcheck::main( __FILE__ );
