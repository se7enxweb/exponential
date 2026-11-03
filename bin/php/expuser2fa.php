#!/usr/bin/env php
<?php
/**
 * File containing the expuser2fa.php script.
 *
 * Two-step sign-in and passkeys of an account: show them; reset the second step for someone who lost the phone, remove the passkeys of a lost device (changes are a dry run unless --apply).
 *
 * @alias exp:user:2fa
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expuser2fa.php; this file is the entry point.
\Exponential\Command\Kernel\Expuser2fa::main( __FILE__ );
