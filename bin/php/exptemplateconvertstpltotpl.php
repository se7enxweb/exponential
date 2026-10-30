#!/usr/bin/env php
<?php
/**
 * File containing the exptemplateconvertstpltotpl.php script: the exp:template:convert:stpl-to-tpl command.
 *
 * Converts the templates of a design from one template engine to another; see
 * doc/eng-us/guides/template-conversion.md.
 *
 * @alias exp:template:convert:stpl-to-tpl
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/exptemplateconvertstpltotpl.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Exptemplateconvertstpltotpl::main( __FILE__ );
