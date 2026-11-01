#!/usr/bin/env php
<?php
/**
 * File containing the expstaticdns.php script.
 *
 * Check the DNS records of a custom domain for GitHub Pages: the A, AAAA and CNAME records compared with GitHub's addresses, and what to change.
 *
 * @alias exp:static:dns
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expstaticdns.php; this file is the entry point.
\Exponential\Command\Kernel\Expstaticdns::main( __FILE__ );
