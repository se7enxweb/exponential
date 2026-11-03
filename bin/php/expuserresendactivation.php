#!/usr/bin/env php
<?php
/**
 * File containing the expuserresendactivation.php script.
 *
 * Send the activation mail again, with a new key, to an account that never activated, as Users > Unactivated does (a dry run unless --apply).
 *
 * @alias exp:user:resend-activation
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expuserresendactivation.php; this file is the entry point.
\Exponential\Command\Kernel\Expuserresendactivation::main( __FILE__ );
