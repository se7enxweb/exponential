#!/usr/bin/env php
<?php
/**
 * File containing the cache.php script.
 *
 * @description Every Setup > Cache action from the command line: caches by tag and id, static, HTTP, Velocity, precompressed files, OPcache, APCu
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/cache.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Cache::main( __FILE__ );
