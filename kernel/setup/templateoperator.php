<?php
/**
 * The template extension wizard view.
 *
 * Replaces the three step download-a-file wizard that stood here since 2003.
 * That one asked five questions, ignored three of the answers, and handed back
 * one php file. This one writes a working extension carrying any mixture of
 * operators, functions, fetch functions and fetch aliases, with the
 * registration that makes each of them reachable.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

// The code is in kernel/private/classes/views/setup/templateoperator.php (#207); this file is the entry point.
return \Exponential\View\Kernel\Setup\Templateoperator::main( __FILE__, get_defined_vars() );
