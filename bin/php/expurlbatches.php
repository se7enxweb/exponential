#!/usr/bin/env php
<?php
/**
 * File containing the expurlbatches.php script.
 *
 * List the URL map imports: who imported which file when, the counts and whether it was undone.
 *
 * @alias exp:url:batches
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expurlbatches.php; this file is the entry point.
\Exponential\Command\Kernel\Expurlbatches::main( __FILE__ );
