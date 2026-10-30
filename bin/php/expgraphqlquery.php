#!/usr/bin/env php
<?php
/**
 * File containing the expgraphqlquery.php script: the exp:graphql:query command.
 *
 * The GraphQL API of the kernel; see doc/eng-us/features/6.0/graphql.md.
 *
 * @alias exp:graphql:query
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expgraphqlquery.php; this file is the entry point.
\Exponential\Command\Kernel\Expgraphqlquery::main( __FILE__ );
