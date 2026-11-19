#!/usr/bin/env php
<?php
/**
 * File containing the expclusterdisable.php script: the exp:cluster:disable command.
 *
 * The one-node DFS cluster of an installation; see doc/eng-us/features/6.0/cluster-commands.md.
 *
 * @alias exp:cluster:disable
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expclusterdisable.php; this file is the entry point.
\Exponential\Command\Kernel\Expclusterdisable::main( __FILE__ );
