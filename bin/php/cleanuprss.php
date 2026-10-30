#!/usr/bin/env php
<?php
/**
 * File containing the cleanuprss.php script.
 *
 * Trims the content the RSS import has created, keeping the newest items of
 * each feed and removing the rest.
 *
 * Ported from the bccleanuprss extension by Brookins Consulting.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/cleanuprss.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Cleanuprss::main( __FILE__ );
