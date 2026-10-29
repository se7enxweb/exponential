#!/usr/bin/env php
<?php
/**
 * File containing the exptemplateconvertstpltotwig.php script: the exp:template:convert:stpl-to-twig command.
 *
 * Converts the templates of a design from one template engine to another; see
 * doc/eng-us/guides/template-conversion.md.
 *
 * @alias exp:template:convert:stpl-to-twig
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/exptemplateconvertstpltotwig.php; this file is the entry point.
\Exponential\Command\Kernel\Exptemplateconvertstpltotwig::main( __FILE__ );
