#!/usr/bin/env php
<?php
/**
 * File containing the preload.php script to preload your website cache files to speed up page loading of your website by siteaccess name parameter.
 *
 * Warms the section pages of the site of a siteaccess (site.ini [SiteSettings] SiteURL and
 * URLTranslationKeyword), then follows its links, and reports the broken ones with the pages that link to
 * them. The same run as Setup > Preload: one at a time, and listed there. Guide: doc/guides/preloading-caches.md
 *
 * Usage:
 *   php bin/php/preload.php --siteaccess=<name> [--max-pages=<n>] [--max-depth=<n>] [--images] [--dry-run]
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/preload.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Preload::main( __FILE__ );
