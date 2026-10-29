#!/usr/bin/env php
<?php
/**
 * File containing the upgradefinish.php script.
 *
 * Upgrade stage 5, finish: deploy the changes (exp:velocity deploy --kernel) and check the site answers (dry run unless --apply).
 *
 * @alias exp:upgrade:finish
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/upgradefinish.php; this file is the entry point.
\Exponential\Command\Kernel\Upgradefinish::main( __FILE__ );
