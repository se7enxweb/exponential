#!/usr/bin/env php
<?php
/**
 * File containing the expclusterenable.php script: the exp:cluster:enable command.
 *
 * The one-node DFS cluster of an installation; see doc/eng-us/features/6.0/cluster-commands.md.
 *
 * @alias exp:cluster:enable
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expclusterenable.php; this file is the entry point.
\Exponential\Command\Kernel\Expclusterenable::main( __FILE__ );
