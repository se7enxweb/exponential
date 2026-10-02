#!/usr/bin/env php
<?php
/**
 * File containing the ezsubtreeremove.php script.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 * @description Remove one or more content object subtrees from the content tree
 * @long-description Permanently removes all content objects under the specified subtree nodes. This operation is irreversible. Use --dry-run first to preview what will be deleted.
 */

// Subtree Remove Script
// file  bin/php/ezsubtreeremove.php

// script initializing
require_once 'autoload.php';

// The code is in kernel/private/classes/commands/ezsubtreeremove.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Ezsubtreeremove::main( __FILE__ );
