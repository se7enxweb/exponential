#!/usr/bin/env php
<?php
/**
 * File containing the expurlundo.php script.
 *
 * Undo a URL map import: every alias, wildcard and redirect it created or changed back as it was.
 *
 * @alias exp:url:undo
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expurlundo.php; this file is the entry point.
\Exponential\Command\Kernel\Expurlundo::main( __FILE__ );
