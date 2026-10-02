#!/usr/bin/env php
<?php
/**
 * File containing the cache warming command.
 *
 * Discovered by the console as exp:warm.
 *
 *   bin/php/console exp:warm                 warm everything
 *   bin/php/console exp:warm --limit=40      the first 40 pages only
 *   bin/php/console exp:warm --verbose       name each page that had to render
 *   bin/php/console exp:warm --json
 *
 * Run it on a timer shorter than the cache window. Measured here: a page from
 * the cache takes about 70ms and the same page rendered takes 1000 to 1600ms,
 * so whoever asks first after an entry expires waits over a second. This makes
 * that first asker a script instead of a visitor.
 *
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license   For full copyright and license information view LICENSE file.
 * @package   kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/warm.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Warm::main( __FILE__ );
