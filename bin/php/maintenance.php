#!/usr/bin/env php
<?php
/**
 * File containing the maintenance script.
 *
 * Takes the site offline for a maintenance window and back: while it is on,
 * every page request is answered with the maintenance page (503, never
 * cached); images, styles and scripts are still served.
 *
 *   php bin/php/maintenance.php on [--message="..."] [--until=30m|2h|"2026-09-28 06:00"]
 *                                  [--allow-ip=1.2.3.4,5.6.7.8] [--allow-admin]
 *   php bin/php/maintenance.php off
 *   php bin/php/maintenance.php status
 *
 * Also ./console exp:maintenance on|off|status. An installation run by the
 * kickstarter switches it on and off by itself.
 *
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/maintenance.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Maintenance::main( __FILE__ );
