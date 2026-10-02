#!/usr/bin/env php
<?php
/**
 * File containing the engine phar command.
 *
 * Discovered by the console as exp:phar.
 *
 *   php -d phar.readonly=0 bin/php/console exp:phar build
 *   bin/php/console exp:phar build --force   (rebuild even when current)
 *   bin/php/console exp:phar check
 *   bin/php/console exp:phar info
 *   bin/php/console exp:phar clean
 *
 * Building needs phar.readonly off, which is an ini setting and not something
 * this script can change for itself; the build verb says so rather than
 * failing obscurely.
 *
 * To run the installation from the archive, set EXP_ENGINE_PHAR in the
 * environment of whatever serves it. The autoloader reads kernel and library
 * classes from the archive when it is set and from disk when it is not, and
 * nothing else about the installation changes either way.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/phar.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Phar::main( __FILE__ );
