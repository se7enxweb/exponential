#!/usr/bin/env php
<?php
/**
 * File containing the expuserwhoami.php script.
 *
 * Which account the console acts as (the user of [UserSettings] UserCreatorID, or --as), with its roles and whether it has every right.
 *
 * @alias exp:user:whoami
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expuserwhoami.php; this file is the entry point.
\Exponential\Command\Kernel\Expuserwhoami::main( __FILE__ );
