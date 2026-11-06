#!/usr/bin/env php
<?php
/**
 * File containing the expurlexport.php script.
 *
 * Export the URL translator, the wildcards and the redirects as a URL map (CSV, JSON, .xlsx, .ods, Apache or nginx rules).
 *
 * @alias exp:url:export
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expurlexport.php; this file is the entry point.
\Exponential\Command\Kernel\Expurlexport::main( __FILE__ );
