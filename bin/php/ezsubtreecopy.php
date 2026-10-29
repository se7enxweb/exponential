#!/usr/bin/env php
<?php
/**
 * File containing the ezsubtreecopy.php script.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// Subtree Copy Script
// file  bin/php/ezsubtreecopy.php

// script initializing
require_once 'autoload.php';

// The code is in kernel/private/classes/commands/ezsubtreecopy.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Ezsubtreecopy::main( __FILE__ );
