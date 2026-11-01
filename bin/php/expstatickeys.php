#!/usr/bin/env php
<?php
/**
 * File containing the expstatickeys.php script.
 *
 * Make or show the SSH deploy key of a static publishing target: ed25519, the private half in the secret store, the public half to add to the repository.
 *
 * @alias exp:static:keys
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expstatickeys.php; this file is the entry point.
\Exponential\Command\Kernel\Expstatickeys::main( __FILE__ );
