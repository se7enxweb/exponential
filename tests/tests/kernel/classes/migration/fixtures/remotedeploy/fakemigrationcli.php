<?php
/**
 * A stand-in for the migration console scripts of a destination installation, for expMigrationRemoteDeployIntegrationTest:
 * copied to <destination>/bin/php/expmigrationstatus.php, expmigrationmigrate.php and expmigrationrollback.php, it
 * answers like them (exp-migration/1, --format=json) from a state file, so a deploy can be tested over real ssh, scp,
 * sftp, rsync and FTP transfers without a database on the destination.
 *
 *   migrations/*.yml               the destination's migrations (source site); a file that contains FAIL fails
 *   var/fake-state.json            done migrations (id => checksum) and the captions (run id => done before the run)
 *   var/fake-captions-off          captions are off (the dry run reports captions pre "off")
 *   var/fake-lock                  a run holds the lock
 *   var/fake-lock-stale            ... and its process is gone: exp:migration:heal --fix=stale-lock --apply removes it
 *   var/fake-host.json             the host its status reports (php, disk_free, extensions); default this PHP,
 *                                  plenty of space, no extension
 *   var/fake-heal.log              one line per fix exp:migration:heal made
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

$root = getcwd();
$command = basename( $argv[0], '.php' );
$args = array_slice( $argv, 1 );
$apply = in_array( '--apply', $args, true );
$runId = '';
$target = '';
foreach ( $args as $arg )
{
    if ( strpos( $arg, '--run-id=' ) === 0 )
        $runId = substr( $arg, 9 );
    else if ( $arg !== '' && $arg[0] !== '-' )
        $target = $arg;
}
$stateFile = "$root/var/fake-state.json";
$state = is_file( $stateFile ) ? json_decode( file_get_contents( $stateFile ), true ) : array( 'done' => array(), 'captions' => array() );
$save = function () use ( &$state, $stateFile ) {
    if ( !is_dir( dirname( $stateFile ) ) )
        mkdir( dirname( $stateFile ), 0775, true );
    file_put_contents( $stateFile, json_encode( $state ) );
};
$files = glob( "$root/migrations/*.yml" ) ?: array();
sort( $files );
$entries = array();
foreach ( $files as $file )
{
    $content = file_get_contents( $file );
    $id = basename( $file, '.yml' );
    $checksum = hash( 'sha256', str_replace( array( "\r\n", "\r" ), "\n", $content ) );
    $entries[] = array( 'id' => $id, 'source' => 'site', 'file' => 'migrations/' . basename( $file ), 'checksum' => $checksum,
                        'state' => isset( $state['done'][$id] ) ? 'done' : 'pending', 'action' => 'none', 'fail' => strpos( $content, 'FAIL' ) !== false );
}
$report = array( 'schema' => 'exp-migration/1', 'command' => $command, 'mode' => $command, 'run_id' => $runId, 'migrations' => array(),
                 'summary' => array( 'failed' => 0, 'applied' => 0 ), 'errors' => array(), 'warnings' => array(), 'exit' => 0 );
$lock = is_file( "$root/var/fake-lock" );

if ( $command === 'expmigrationstatus' )
{
    $host = is_file( "$root/var/fake-host.json" ) ? json_decode( file_get_contents( "$root/var/fake-host.json" ), true ) : null;
    $report += array( 'table' => true, 'version' => getenv( 'FAKE_VERSION' ) ?: '6.0.15', 'lock' => $lock ? array( 'pid' => 1 ) : null, 'runs' => array(),
                      'host' => is_array( $host ) ? $host : array( 'php' => PHP_VERSION, 'disk_free' => 1 << 40, 'extensions' => array() ) );
    $report['migrations'] = $entries;
}
else if ( $command === 'expmigrationmigrate' )
{
    $report['captions'] = array( 'pre' => is_file( "$root/var/fake-captions-off" ) ? 'off' : 'would-take' );
    if ( $apply )
    {
        if ( $lock )
        {
            $report['errors'][] = 'the lock is held';
            $report['exit'] = 1;
        }
        else
        {
            $before = $state['done'];
            $state['captions'][$runId] = $before;
            $report['captions'] = array( 'pre' => array( 'id' => 'caption-' . $runId ) );
            foreach ( $entries as $i => $entry )
            {
                if ( $entry['state'] !== 'pending' )
                    continue;
                if ( $entry['fail'] )
                {
                    $entries[$i]['state'] = 'failed';
                    $entries[$i]['action'] = 'not-run';
                    $report['summary']['failed']++;
                    $report['errors'][] = $entry['id'] . ' failed';
                    $report['exit'] = 1;
                    // the run restores its own caption
                    $state['done'] = $before;
                    $report['restore'] = array( 'state' => 'restored', 'caption' => 'caption-' . $runId );
                    break;
                }
                $state['done'][$entry['id']] = $entry['checksum'];
                $entries[$i]['state'] = 'done';
                $entries[$i]['action'] = 'applied';
                $report['summary']['applied']++;
            }
            $save();
        }
    }
    else
        foreach ( $entries as $i => $entry )
            if ( $entry['state'] === 'pending' )
                $entries[$i]['action'] = 'would-apply';
    $report['migrations'] = $entries;
}
else if ( $command === 'expmigrationrollback' )
{
    if ( !isset( $state['captions'][$target] ) || is_file( "$root/var/fake-rollback-fails" ) )
    {
        $report['errors'][] = "no caption of the run $target";
        $report['exit'] = 1;
    }
    else if ( $apply )
    {
        $state['done'] = $state['captions'][$target];
        $save();
    }
}
else if ( $command === 'expmigrationheal' )
{
    $fixes = array();
    foreach ( $args as $arg )
        if ( strpos( $arg, '--fix=' ) === 0 )
            $fixes = explode( ',', substr( $arg, 6 ) );
    $report['mode'] = 'heal';
    $report['fixes'] = array();
    $report['problems'] = array();
    if ( in_array( 'stale-lock', $fixes, true ) && $lock )
    {
        if ( is_file( "$root/var/fake-lock-stale" ) )
        {
            if ( $apply )
            {
                unlink( "$root/var/fake-lock" );
                unlink( "$root/var/fake-lock-stale" );
            }
            $report['fixes'][] = 'stale-lock: moved the lock of a run whose process is gone aside';
        }
        else
            $report['problems'][] = 'stale-lock: the lock is held and left alone: its process still runs';
    }
    if ( in_array( 'caches', $fixes, true ) )
        $report['fixes'][] = 'caches: cleared the caches tagged ini';
    if ( $apply )
        foreach ( $report['fixes'] as $fix )
            file_put_contents( "$root/var/fake-heal.log", $fix . "\n", FILE_APPEND );
}
echo json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ), "\n";
exit( $report['exit'] );
