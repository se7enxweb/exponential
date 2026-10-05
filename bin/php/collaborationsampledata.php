#!/usr/bin/env php
<?php
/**
 * File containing the collaborationsampledata.php script.
 *
 * Builds sample content for the collaboration tool, or removes it again.
 *
 * @alias exp:collaboration:sample-data
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/collaborationsampledata.php; this file is the entry point.
\Exponential\Command\Kernel\Collaborationsampledata::main( __FILE__ );
