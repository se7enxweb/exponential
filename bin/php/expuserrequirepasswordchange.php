#!/usr/bin/env php
<?php
/**
 * File containing the expuserrequirepasswordchange.php script.
 *
 * Require a new password at the account's next sign-in, or remove that mark with --clear, as the account's edit form does (a dry run unless --apply).
 *
 * @alias exp:user:require-password-change
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expuserrequirepasswordchange.php; this file is the entry point.
\Exponential\Command\Kernel\Expuserrequirepasswordchange::main( __FILE__ );
