#!/usr/bin/env php
<?php
/**
 * File containing the preload.php script to preload your website cache files to speed up page loading of your website by siteaccess name parameter.
 *
 * Warms the main section pages (derived from site.ini [SiteSettings] SiteURL /
 * URLTranslationKeyword) then spiders the entire site via wget (recursive,
 * level 3) to warm all page caches.  Produces rich, colourised terminal output.
 *
 * Usage:
 *   ./bin/php/preload.php [--siteaccess <name>]
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/preload.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Preload::main( __FILE__ );
