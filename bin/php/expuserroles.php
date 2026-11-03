#!/usr/bin/env php
<?php
/**
 * File containing the expuserroles.php script.
 *
 * Role assignments: list those of a user or group, assign a role with or without a subtree or section limitation, unassign it, as role/assign does (changes are a dry run unless --apply).
 *
 * @alias exp:user:roles
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expuserroles.php; this file is the entry point.
\Exponential\Command\Kernel\Expuserroles::main( __FILE__ );
