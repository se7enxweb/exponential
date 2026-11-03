#!/usr/bin/env php
<?php
/**
 * File containing the expusergroups.php script.
 *
 * User groups: list the group tree with its members, create, rename and move a group, remove an empty group to the trash (changes are a dry run unless --apply).
 *
 * @alias exp:user:groups
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expusergroups.php; this file is the entry point.
\Exponential\Command\Kernel\Expusergroups::main( __FILE__ );
