#!/usr/bin/env php
<?php
/**
 * File containing the expmigrationconvert.php script.
 *
 * Convert YAML migrations of the migration bundle of the Symfony based platform into YAML migrations of Exponential; a dry run unless --apply.
 *
 * @alias exp:migration:convert
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expmigrationconvert.php; this file is the entry point.
\Exponential\Command\Kernel\Expmigrationconvert::main( __FILE__ );
