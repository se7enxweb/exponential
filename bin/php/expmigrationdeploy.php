#!/usr/bin/env php
<?php
/**
 * File containing the expmigrationdeploy.php script.
 *
 * Deploy this installation's migrations to one or many remotes at once, in sync: preflight and verify on all, then switch all, or take all back.
 *
 * @alias exp:migration:deploy
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expmigrationdeploy.php; this file is the entry point.
\Exponential\Command\Kernel\Expmigrationdeploy::main( __FILE__ );
