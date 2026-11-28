#!/usr/bin/env php
<?php
/**
 * File containing the expclusterhosts.php script: the exp:cluster:hosts command.
 *
 * Lists and checks the hosts of settings/cluster.ini; see
 * doc/eng-us/features/6.0/cluster-host-commands.md.
 *
 * @alias exp:cluster:hosts
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expclusterhosts.php; this file is the entry point.
\Exponential\Command\Kernel\Expclusterhosts::main( __FILE__ );
