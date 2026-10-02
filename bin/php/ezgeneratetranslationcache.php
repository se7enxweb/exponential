#!/usr/bin/env php
<?php
/**
 * File containing the ezgeneratetranslationcache.php script.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 * @description Generate translation cache files for all configured locales
 * @long-description Pre-generates cached translation files for configured locales and siteaccesses to speed up the first page load after a cache clear. Usage: ./bin/php/ezgeneratetranslationcache.php -s <siteaccess>
 */

// Generate caches for translations
// file  bin/php/ezgeneratetranslationcache.php


/**************************************************************
* script initializing                                         *
***************************************************************/

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/ezgeneratetranslationcache.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Ezgeneratetranslationcache::main( __FILE__ );
