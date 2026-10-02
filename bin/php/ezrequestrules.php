#!/usr/bin/env php
<?php
/**
 * File containing the ezrequestrules.php script.
 *
 * Shows and checks the request rules of a siteaccess (requestrules.ini), and
 * explains what they decide for an address and a user, without sending a
 * request. Guide: doc/bc/6.0/view_full_security.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/ezrequestrules.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Ezrequestrules::main( __FILE__ );
