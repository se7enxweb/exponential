#!/usr/bin/env php
<?php
/**
 * File containing the expurlsuggest.php script.
 *
 * Suggest new targets for the rows of a URL map that have none, by how alike their old address is to the addresses and names of this site's pages.
 *
 * @alias exp:url:suggest
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expurlsuggest.php; this file is the entry point.
\Exponential\Command\Kernel\Expurlsuggest::main( __FILE__ );
