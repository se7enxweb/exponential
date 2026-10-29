#!/usr/bin/env php
<?php
/**
 * File containing the expfetch.php script: the system card of the console.
 *
 *   ./console exp:expfetch        the card: Exponential, the machine, PHP, the engine, the database and the site
 *   ./console exp:info            the card and an inventory (extensions, siteaccesses, settings, cronjobs, caches)
 *   ./console exp:about           the card and the project (what Exponential is, license, links, contributing)
 *
 * @description The system card: the Exponential "e" next to the facts of this installation, its engine and its machine, with health hints (exp:info adds an inventory, exp:about the project)
 * @alias exp:info
 * @alias exp:about
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/syscard.php (#207); this file is the entry point.
if ( !class_exists( 'Exponential\\Command\\Kernel\\Syscard' ) )
{
    fwrite( STDERR, "The system card command class is not in the autoload array: run php bin/php/ezpgenerateautoloads.php -k\n" );
    exit( 2 );
}
\Exponential\Command\Kernel\Syscard::main( __FILE__ );
