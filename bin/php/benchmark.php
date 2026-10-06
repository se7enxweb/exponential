#!/usr/bin/env php
<?php
/**
 * File containing the benchmark command.
 *
 * Discovered by the console as exp:benchmark (exp:bench for short).
 *
 *   bin/php/console exp:benchmark                         the front page, the menu, the admin login
 *   bin/php/console exp:benchmark --compare=A,B           two servers, back to back
 *   bin/php/console exp:benchmark --cold                  past every response cache
 *   bin/php/console exp:benchmark kernel                  the parts of a page, in-process
 *   bin/php/console exp:benchmark micro                   hot paths without HTTP or a database (CI)
 *   bin/php/console exp:benchmark --baseline=base.json    exit 1 when slower than a saved run
 *
 * Guide: doc/guides/benchmarking.md (reference: doc/features/6.0/benchmark.md)
 *
 * @description Measure this installation: page timings over HTTP, A/B between servers, kernel probes, regression check
 * @alias bench
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/benchmark.php; this file is the entry point.
\Exponential\Command\Kernel\Benchmark::main( __FILE__ );
