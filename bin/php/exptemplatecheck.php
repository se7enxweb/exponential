#!/usr/bin/env php
<?php
/**
 * File containing the exptemplatecheck.php script: the exp:template:check command.
 *
 * Checks templates of every engine for valid syntax; see doc/eng-us/guides/template-syntax-check.md.
 *
 * @alias exp:template:check
 * @alias exp:template:lint
 * @alias exp:template:convert:check
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/exptemplatecheck.php; this file is the entry point.
\Exponential\Command\Kernel\Exptemplatecheck::main( __FILE__ );
