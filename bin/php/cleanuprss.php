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
 * @copyright Copyright (C) 1999 - 2011 Brookins Consulting. All rights reserved.
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or later)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/cleanuprss.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Cleanuprss::main( __FILE__ );
