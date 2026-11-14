#!/usr/bin/env php
<?php
/**
 * File containing the expimagealias.php script: the exp:image:alias command.
 *
 * Image aliases made on demand from the command line: status, bulk generate, regenerate and purge, resync from a
 * staging source, test one image; the same work as Setup > Image aliases.
 *
 * @alias exp:image:alias
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 * @description Image aliases made on demand: status, generate, regenerate, purge, resync and test, in the process or as jobs
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expimagealias.php; this file is the entry point.
\Exponential\Command\Kernel\Expimagealias::main( __FILE__ );
