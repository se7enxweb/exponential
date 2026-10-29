#!/usr/bin/env php
<?php
/**
 * URL Alias Integrity Verification Script
 *
 * Detects and reports potential corruption issues in ezurlalias_ml table.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/verify_aliases.php (#207); this file is the entry point.
\Exponential\Command\Kernel\VerifyAliases::main( __FILE__ );
