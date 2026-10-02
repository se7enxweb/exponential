#!/usr/bin/env php
<?php
/**
 * File containing the checkclasses.php script.
 *
 * Loads every class this installation declares and reports the ones php
 * refuses. A class that cannot be loaded is not a quiet problem: the fatal
 * takes the whole request with it, so the page somebody was looking at is gone
 * rather than merely wrong. It is also invisible until something happens to
 * touch that class, which can be months.
 *
 * The loading is done in a child process reading names from stdin and printing
 * each before it tries it, so a class that kills php is identified by being the
 * last name printed. The parent picks up after it and carries on, which is how
 * one run covers everything rather than stopping at the first fault.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/checkclasses.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Checkclasses::main( __FILE__ );
