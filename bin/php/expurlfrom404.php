#!/usr/bin/env php
<?php
/**
 * File containing the expurlfrom404.php script.
 *
 * Start a URL map from the not-found log: the addresses visitors asked for and did not find, the most asked first, as rows to fill in.
 *
 * @alias exp:url:from-404
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expurlfrom404.php; this file is the entry point.
\Exponential\Command\Kernel\Expurlfrom404::main( __FILE__ );
