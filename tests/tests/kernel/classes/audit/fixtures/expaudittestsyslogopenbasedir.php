<?php
/**
 * Run by expAuditSinksTest (SK-08) as `php -d open_basedir=<the installation> <this file> [old]`: resolves the syslog
 * sink's transports under a real open_basedir and prints them with every warning PHP raised, as JSON.
 * "old" repeats the checks the sink made before it knew open_basedir (file_exists() on the sockets), to prove
 * that they do warn there.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

$root = dirname( __DIR__, 6 );
require_once $root . '/kernel/classes/audit/expauditsink.php';
require_once $root . '/kernel/classes/audit/sinks/expauditsinkbase.php';
require_once $root . '/kernel/classes/audit/sinks/expauditsyslogsink.php';

$warnings = array();
set_error_handler( function ( $errno, $errstr ) use ( &$warnings )
{
    $warnings[] = $errstr;
    return true;
} );

$out = array( 'open_basedir' => (string)ini_get( 'open_basedir' ) );
if ( isset( $argv[1] ) && $argv[1] === 'old' )
{
    file_exists( expAuditSyslogSink::JOURNAL_SOCKET );
    file_exists( expAuditSyslogSink::DEV_LOG );
}
else
{
    foreach ( array( 'local', 'journald', 'devlog' ) as $t )
    {
        $sink = new expAuditSyslogSink( 'syslog', array( 'Transport' => $t ) );
        $out[$t] = array( 'transport' => $sink->transport(), 'problem' => $sink->problem(), 'spooled' => $sink->isSpooled() );
    }
}
$out['warnings'] = $warnings;
echo json_encode( $out ), "\n";
