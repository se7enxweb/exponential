#!/usr/bin/env php
<?php
/**
 * File containing the exptemplateconverttwigtotpl.php script: the exp:template:convert:twig-to-tpl command.
 *
 * Converts the templates of a design from one template engine to another; see
 * doc/eng-us/guides/template-conversion.md.
 *
 * @alias exp:template:convert:twig-to-tpl
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/exptemplateconverttwigtotpl.php; this file is the entry point.
\Exponential\Command\Kernel\Exptemplateconverttwigtotpl::main( __FILE__ );
