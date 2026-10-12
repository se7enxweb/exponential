#!/usr/bin/env php
<?php
/**
 * File containing the explovestack.php script.
 *
 * Checks an installation and its database for what a site coming from LoveStack brings along (read-only).
 *
 * @alias exp:lovestack
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/explovestack.php; this file is the entry point.
\Exponential\Command\Kernel\Explovestack::main( __FILE__ );
