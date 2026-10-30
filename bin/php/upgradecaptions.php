#!/usr/bin/env php
<?php
/**
 * File containing the upgradecaptions.php script.
 *
 * Upgrade captions: the safe points to roll back to (git_manager's and exp:upgrade's), checked with --verify, pruned with --prune.
 *
 * @alias exp:upgrade:captions
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/upgradecaptions.php; this file is the entry point.
\Exponential\Command\Kernel\Upgradecaptions::main( __FILE__ );
