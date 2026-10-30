#!/usr/bin/env php
<?php
/**
 * File containing the exptemplateconvertdiff.php script: the exp:template:convert:diff command.
 *
 * Prints the conversion of one template to another engine; see doc/eng-us/guides/template-conversion.md.
 *
 * @alias exp:template:convert:diff
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/exptemplateconvertdiff.php; this file is the entry point.
\Exponential\Command\Kernel\Exptemplateconvertdiff::main( __FILE__ );
