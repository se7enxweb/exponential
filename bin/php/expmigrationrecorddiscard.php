#!/usr/bin/env php
<?php
/**
 * File containing the expmigrationrecorddiscard.php script.
 *
 * Discard a recording of changes: the recording and every event it captured are removed (a running one stops recording); a dry run unless --apply.
 *
 * @alias exp:migration:record:discard
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expmigrationrecorddiscard.php; this file is the entry point.
\Exponential\Command\Kernel\Expmigrationrecorddiscard::main( __FILE__ );
