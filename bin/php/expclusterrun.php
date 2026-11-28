#!/usr/bin/env php
<?php
/**
 * File containing the expclusterrun.php script: the exp:cluster:run command.
 *
 * Runs a console command (or a command line) in this installation on every host of settings/cluster.ini, over ssh; see
 * doc/eng-us/features/6.0/cluster-host-commands.md.
 *
 * @alias exp:cluster:run
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expclusterrun.php; this file is the entry point.
\Exponential\Command\Kernel\Expclusterrun::main( __FILE__ );
