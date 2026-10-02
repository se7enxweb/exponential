#!/usr/bin/env php
<?php
/**
 * File containing the ezgeneratetranslationcache.php script.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// Generate caches for translations
// file  bin/php/ezgeneratetranslationcache.php


/**************************************************************
* script initializing                                         *
***************************************************************/

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/ezgeneratetranslationcache.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Ezgeneratetranslationcache::main( __FILE__ );
