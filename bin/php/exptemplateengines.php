#!/usr/bin/env php
<?php
/**
 * File containing the exptemplateengines.php script: the exp:template:engines command.
 *
 * Lists the template engines of the installation; see doc/eng-us/guides/template-engines.md.
 *
 * @alias exp:template:engines
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/exptemplateengines.php; this file is the entry point.
\Exponential\Command\Kernel\Exptemplateengines::main( __FILE__ );
