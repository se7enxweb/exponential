#!/usr/bin/env php
<?php
/**
 * File containing the expurltest.php script.
 *
 * Test a URL map against a running site: request every old address, follow the redirects and compare where it lands with the new_url.
 *
 * @alias exp:url:test
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expurltest.php; this file is the entry point.
\Exponential\Command\Kernel\Expurltest::main( __FILE__ );
