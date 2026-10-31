#!/usr/bin/env php
<?php
/**
 * File containing the expgraphqlstarter.php script: the exp:graphql:starter command.
 *
 * The GraphQL API of the kernel; see doc/eng-us/guides/graphql-walkthrough.md.
 *
 * @alias exp:graphql:starter
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expgraphqlstarter.php; this file is the entry point.
\Exponential\Command\Kernel\Expgraphqlstarter::main( __FILE__ );
