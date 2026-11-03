#!/usr/bin/env php
<?php
/**
 * File containing the expuserexport.php script.
 *
 * Export the accounts the list filters select as CSV or JSON: login, e-mail, names, state, groups, last visit (never a password or its hash).
 *
 * @alias exp:user:export
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expuserexport.php; this file is the entry point.
\Exponential\Command\Kernel\Expuserexport::main( __FILE__ );
