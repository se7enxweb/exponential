#!/usr/bin/env php
<?php
/**
 * File containing the ezconvertdbcharset.php script.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 * @description Convert all Exponential database tables to use UTF-8 character set
 * @long-description Connects to the configured database and alters all tables to UTF8 charset and utf8_general_ci collation. Run after migrating to a new server or when setting up a legacy-charset site.
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/ezconvertdbcharset.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Ezconvertdbcharset::main( __FILE__ );
