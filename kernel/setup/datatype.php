<?php
/**
 * The datatype wizard view.
 *
 * Replaces the three step download-a-file wizard that stood here since 2003.
 * That one asked four questions and handed back one php file with a class in
 * it; this one asks what the datatype has to do, shows every file it would
 * write, and writes a working extension.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

// The code is in kernel/private/classes/views/setup/datatype.php (#207); this file is the entry point.
return \Exponential\View\Kernel\Setup\Datatype::main( __FILE__, get_defined_vars() );
