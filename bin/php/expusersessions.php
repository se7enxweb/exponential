#!/usr/bin/env php
<?php
/**
 * File containing the expusersessions.php script.
 *
 * The tracked sessions of an account (Your devices): list them, end all of them or one, as an administrator does on user/sessions (ending is a dry run unless --apply).
 *
 * @alias exp:user:sessions
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expusersessions.php; this file is the entry point.
\Exponential\Command\Kernel\Expusersessions::main( __FILE__ );
