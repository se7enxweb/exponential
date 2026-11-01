#!/usr/bin/env php
<?php
/**
 * File containing the expstaticrollback.php script.
 *
 * Roll a static publishing target back to an earlier publish: a new commit with the files of that tag, tagged and pushed; the history stays.
 *
 * @alias exp:static:rollback
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expstaticrollback.php; this file is the entry point.
\Exponential\Command\Kernel\Expstaticrollback::main( __FILE__ );
