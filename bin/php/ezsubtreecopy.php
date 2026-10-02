#!/usr/bin/env php
<?php
/**
 * File containing the ezsubtreecopy.php script.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 * @description Copy a content object subtree to a new location in the content tree
 * @long-description Recursively copies a subtree of content objects from a source node to a destination node. Object relations and URL aliases are updated. Supports --dry-run to preview the operation before committing.
 */

// Subtree Copy Script
// file  bin/php/ezsubtreecopy.php

// script initializing
require_once 'autoload.php';

// The code is in kernel/private/classes/commands/ezsubtreecopy.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Ezsubtreecopy::main( __FILE__ );
