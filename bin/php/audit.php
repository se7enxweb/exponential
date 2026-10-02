#!/usr/bin/env php
<?php
/**
 * File containing the audit script.
 *
 * The audit log: the records of the audit channels (var/<site>/log/audit/<channel>-<date>.jsonl) and their
 * hash chains.
 *
 *   php bin/php/audit.php status|channels
 *   php bin/php/audit.php tail [--channel=access] [--name=access.*] [--lines=20] [--follow] [--json]
 *   php bin/php/audit.php show <event id> [--json]
 *   php bin/php/audit.php verify [--channel=content] [--date=YYYY-MM-DD] [--json]
 *   php bin/php/audit.php checkpoint
 *
 * Also ./console exp:audit ... Guide: doc/bc/6.0/audit.md
 *
 * @description Audit log: tail, show one event, verify the hash chains, list the channels
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/audit.php (#207); this file is the entry point.
if ( !class_exists( 'Exponential\\Command\\Kernel\\Audit' ) )
{
    fwrite( STDERR, "The audit command class is not in the autoload array: run php bin/php/ezpgenerateautoloads.php -k\n" );
    exit( 2 );
}
\Exponential\Command\Kernel\Audit::main( __FILE__ );
