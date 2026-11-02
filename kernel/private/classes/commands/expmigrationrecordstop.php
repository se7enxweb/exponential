<?php
/**
 * @description Stop a recording of changes: the one named, a user's own (--user) or the site-wide window (--site-wide); its events stay for exp:migration:generate --from-recording; a dry run unless --apply
 * @alias exp:migration:record:stop
 *
 * File containing the exp:migration:record:stop command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expmigrationrecordstop extends ExpmigrationrecordBase
{
    protected function name()
    {
        return 'exp:migration:record:stop';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Stop a recording of changes\n" .
                             "Stops the recording named by its id, or else the session recording of --user (default: [RunSettings]\n" .
                             "RunAsUser), or with --site-wide the site-wide window. Nothing more is recorded; what was recorded stays\n" .
                             "until exp:migration:record:discard, for exp:migration:record:show and exp:migration:generate\n" .
                             "--from-recording. Without --apply it says what it would stop.\n" .
                             "\n" .
                             "./console exp:migration:record:stop --apply\n" .
                             "./console exp:migration:record:stop --site-wide --apply\n" .
                             "./console exp:migration:record:stop 20261101-120000-a1b2c3 --apply",
        ) + self::SCRIPT );
        return (array)$this->startup( '[apply][dry-run][format:][site-wide][user:]', '[recording]', array(
            'apply' => 'Make the changes (the default is a dry run that lists them and changes nothing)',
            'dry-run' => 'The default, accepted for scripts that pass it: list what would change, change nothing',
            'format' => 'text (default) or json (the machine output exp-migration/1)',
            'site-wide' => 'The site-wide recording: every change of every user and of the console (without it: one user\'s own changes)',
            'user' => 'The user, by login, whose own changes a session recording keeps (default: [RunSettings] RunAsUser of migration.ini)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $apply = $this->apply( $options );
        $id = $this->recordingArgument( $options );
        $db = $this->database( $format );
        $this->tables( $db, $format );
        $report = array( 'mode' => $apply ? 'record-stop' : 'record-stop-dry-run', 'recording' => null, 'errors' => array(), 'warnings' => array() );
        if ( $id !== '' )
            $row = \expMigrationRecording::get( $db, $id );
        else if ( !empty( $options['site-wide'] ) )
            $row = \expMigrationRecording::runningSite( $db );
        else
            $row = \expMigrationRecording::runningFor( $db, (int)$this->recordingUser( $options )->attribute( 'contentobject_id' ) );
        if ( $row === null )
        {
            $report['errors'][] = $id !== '' ? "no recording $id" : ( !empty( $options['site-wide'] ) ? 'no site-wide recording is running' : 'this user is not recording' );
            $this->finish( $report, $format );
        }
        if ( $row['state'] !== \expMigrationRecording::RECORDING )
            $report['errors'][] = "the recording {$row['recording_id']} is not running (it is {$row['state']})";
        else if ( $apply )
        {
            $errors = array();
            $row = \expMigrationRecording::stop( $db, $row['recording_id'], $errors );
            $report['errors'] = $errors;
        }
        $report['recording'] = $row;
        $this->finish( $report, $format );
    }

    protected function lines( array $report )
    {
        $lines = array( 'Exponential migrations: ' . ( $report['mode'] === 'record-stop' ? 'stop a recording' : 'stop a recording, dry run (--apply stops it)' ) );
        $r = $report['recording'];
        if ( $r )
        {
            $lines[] = self::recordingLine( $r );
            if ( $report['mode'] === 'record-stop' && !$report['errors'] )
                $lines[] = 'Next: ./console exp:migration:record:show ' . $r['recording_id'] . ', then ./console exp:migration:generate --from-recording=' . $r['recording_id'];
        }
        return $lines;
    }
}

}
