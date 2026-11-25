<?php
/**
 * A second session for the live SQL Server tests, run as its own PHP process (the tests cannot block on two
 * connections in one process): it runs the statements of its scenario and prints what happened as JSON.
 *
 * Usage: php expSQLServerPeer.php <scenario> <table>
 *   deadlock-peer   BEGIN; UPDATE row 2; wait 1 s; UPDATE row 1 (blocks on the test's lock); COMMIT. Its deadlock
 *                   priority is HIGH, so the test's session is the victim.
 *   hold-row        BEGIN; UPDATE row 1; wait 3 s; ROLLBACK (the test runs into its lock timeout meanwhile)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

chdir( dirname( __DIR__, 6 ) );
require_once 'vendor/autoload.php';
require_once dirname( __DIR__ ) . '/expSQLServerTestKit.php';
expSQLServerTestKit::quietDebug();

$scenario = isset( $argv[1] ) ? $argv[1] : '';
$table = isset( $argv[2] ) ? $argv[2] : '';
$db = expSQLServerTestKit::liveDriver();
if ( !$db instanceof expSQLServerDB )
{
    echo json_encode( array( 'ok' => false, 'error' => (string)$db ) );
    exit( 1 );
}
$db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
$t = expSQLServerDialect::quoteIdentifier( $table );
$out = array( 'ok' => true );
try
{
    if ( $scenario === 'deadlock-peer' )
    {
        $db->query( 'SET DEADLOCK_PRIORITY HIGH' );
        $db->begin();
        $db->query( "UPDATE $t SET v = v + 1 WHERE id = 2" );
        $db->query( "WAITFOR DELAY '00:00:01'" );
        $db->query( "UPDATE $t SET v = v + 1 WHERE id = 1" );
        $db->commit();
    }
    else if ( $scenario === 'hold-row' )
    {
        $db->begin();
        $db->query( "UPDATE $t SET v = v + 1 WHERE id = 1" );
        $db->query( "WAITFOR DELAY '00:00:03'" );
        $db->rollback();
    }
}
catch ( eZDBException $e )
{
    $out = array( 'ok' => false, 'error' => $e->getMessage(), 'code' => $e->getCode() );
}
echo json_encode( $out );
