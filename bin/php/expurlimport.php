#!/usr/bin/env php
<?php
/**
 * File containing the expurlimport.php script.
 *
 * Import a URL map (CSV, JSON, .xlsx, .ods, Apache or nginx rules) into the URL translator, the wildcards and the redirects, as one batch that can be undone.
 *
 * @alias exp:url:import
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expurlimport.php; this file is the entry point.
\Exponential\Command\Kernel\Expurlimport::main( __FILE__ );
