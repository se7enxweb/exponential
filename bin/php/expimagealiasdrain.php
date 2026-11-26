#!/usr/bin/env php
<?php
/**
 * A drainer of the image alias backlog: makes the image aliases waiting in the backlog of this server, as a process of
 * its own. Web requests start it after their answer (settings/image.ini [AliasSettings] BacklogDrainer=process).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 * @description Make the image aliases waiting in the backlog of this server, as a process of its own (started by a web request)
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expimagealiasdrain.php; this file is the entry point.
\Exponential\Command\Kernel\Expimagealiasdrain::main( __FILE__ );
