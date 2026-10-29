#!/usr/bin/env php
<?php
/**
 * File containing the exptemplateconverttwigtostpl.php script: the exp:template:convert:twig-to-stpl command.
 *
 * Converts the templates of a design from one template engine to another; see
 * doc/eng-us/guides/template-conversion.md.
 *
 * @alias exp:template:convert:twig-to-stpl
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/exptemplateconverttwigtostpl.php; this file is the entry point.
\Exponential\Command\Kernel\Exptemplateconverttwigtostpl::main( __FILE__ );
