<?php
/**
 * @description The recordings of changes that run: the site-wide window and the user's own recording, with how much each has recorded; repairs the file of running recordings
 * @alias exp:migration:record:status
 *
 * File containing the exp:migration:record:status command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expmigrationrecordstatus extends ExpmigrationrecordBase
{
    protected function name()
    {
        return 'exp:migration:record:status';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "The recordings of changes that run\n" .
                             "Every recording that runs (the site-wide window and the session recordings of users), how many events\n" .
                             "each has, and whether --user (default: [RunSettings] RunAsUser) is recorded now. When the file of the\n" .
                             "running recordings in var/ does not match the database (var/ was cleared, a server was added) it is\n" .
                             "written again, and a warning says so. Changes nothing else.\n" .
                             "\n" .
                             "./console exp:migration:record:status\n" .
                             "./console exp:migration:record:status --user=editor --format=json",
        ) + self::SCRIPT );
        return (array)$this->startup( '[format:][user:]', '', array(
            'format' => 'text (default) or json (the machine output exp-migration/1)',
            'user' => 'The user, by login, whose own changes a session recording keeps (default: [RunSettings] RunAsUser of migration.ini)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $db = $this->database( $format );
        $this->tables( $db, $format );
        $user = $this->recordingUser( $options );
        $userId = (int)$user->attribute( 'contentobject_id' );
        $closed = \expMigrationRecording::closeEnded( $db );
        $running = \expMigrationRecording::running( $db );
        $report = array( 'mode' => 'record-status', 'enabled' => \expMigrationRecorder::settings()['enabled'], 'user' => (string)$user->attribute( 'login' ),
                         'user_id' => $userId, 'running' => $running, 'recorded_now' => false, 'errors' => array(), 'warnings' => array() );
        foreach ( $closed as $row )
            $report['warnings'][] = 'the window of the recording ' . $row['recording_id'] . ' ended at ' . date( 'Y-m-d H:i', (int)$row['stopped_at'] ) . ': stopped';
        $fromFile = array_column( \expMigrationRecording::active(), 'recording_id' );
        $fromDb = array_column( $running, 'recording_id' );
        sort( $fromFile );
        sort( $fromDb );
        if ( $fromFile !== $fromDb )
        {
            \expMigrationRecording::sync( $db );
            $report['warnings'][] = 'the file of the running recordings did not match the database; written again: ' . \expMigrationRecording::activeFile();
        }
        foreach ( $running as $row )
            if ( $row['kind'] === \expMigrationRecording::MODE_SITE || (int)$row['user_id'] === $userId )
                $report['recorded_now'] = true;
        if ( !$report['enabled'] )
            $report['warnings'][] = 'recording is switched off: [RecordingSettings] Recording=disabled in migration.ini';
        $this->finish( $report, $format );
    }

    protected function lines( array $report )
    {
        $lines = array( 'Exponential migrations: recordings of changes that run' );
        foreach ( $report['running'] as $r )
            $lines[] = self::recordingLine( $r );
        if ( !$report['running'] )
            $lines[] = '  none';
        $lines[] = 'The changes of ' . $report['user'] . ' are ' . ( $report['recorded_now'] ? '' : 'not ' ) . 'recorded now';
        return $lines;
    }
}

}
