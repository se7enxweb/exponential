<?php
/**
 * @description Show a recording of changes: every event it captured as the migration step it stands for, or with --plan the steps a migration of it would have
 * @alias exp:migration:record:show
 *
 * File containing the exp:migration:record:show command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expmigrationrecordshow extends ExpmigrationrecordBase
{
    protected function name()
    {
        return 'exp:migration:record:show';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Show a recording of changes\n" .
                             "The recording and every event it captured, in order: when, the audit event (content.object.publish ...),\n" .
                             "the migration step it stands for (type/mode) and what it is about in stable names (remote ids,\n" .
                             "identifiers), with what was left out on purpose (personal data, secrets). --format=json gives the\n" .
                             "events in full (exp-migration-recording-event/1). --plan lists the steps a migration of the recording\n" .
                             "would have instead: the events folded per thing changed. Changes nothing.\n" .
                             "\n" .
                             "./console exp:migration:record:show 20261101-120000-a1b2c3\n" .
                             "./console exp:migration:record:show 20261101-120000-a1b2c3 --plan\n" .
                             "./console exp:migration:record:show 20261101-120000-a1b2c3 --format=json",
        ) + self::SCRIPT );
        return (array)$this->startup( '[format:][plan]', '[recording]', array(
            'format' => 'text (default) or json (the machine output exp-migration/1)',
            'plan' => 'The steps a migration of the recording would have (its events folded per thing changed) instead of every event',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $id = $this->recordingArgument( $options );
        if ( $id === '' )
            $this->usage( 'name the recording, e.g. ./console exp:migration:record:show 20261101-120000-a1b2c3 (exp:migration:record:list lists them)' );
        $db = $this->database( $format );
        $this->tables( $db, $format );
        $row = \expMigrationRecording::get( $db, $id );
        $report = array( 'mode' => !empty( $options['plan'] ) ? 'record-plan' : 'record-show', 'format' => \expMigrationRecordingEvent::FORMAT,
                         'recording' => $row, 'events' => array(), 'plan' => array(), 'errors' => array(), 'warnings' => array() );
        if ( $row === null )
        {
            $report['errors'][] = "no recording $id";
            $this->finish( $report, $format );
        }
        $events = \expMigrationRecording::events( $db, $id );
        foreach ( $events as $e )
        {
            unset( $e['recording_id'] );
            $report['events'][] = $e;
        }
        if ( !empty( $options['plan'] ) )
        {
            $report['plan'] = \expMigrationRecordingGenerator::plan( array_values( array_filter( array_column( $events, 'event' ) ) ) );
            $report['events'] = array();
        }
        $report['recording'] = \expMigrationRecording::summary( $row, $events );
        $this->finish( $report, $format );
    }

    protected function lines( array $report )
    {
        $r = $report['recording'];
        $lines = array( 'Exponential migrations: ' . ( $report['mode'] === 'record-plan' ? 'the steps of a recording' : 'a recording of changes' ) );
        if ( !$r )
            return $lines;
        $lines[] = self::recordingLine( $r );
        if ( !empty( $r['message'] ) )
            $lines[] = '  ' . $r['message'];
        $lines[] = '';
        foreach ( $report['events'] as $e )
        {
            $event = $e['event'] ?: array();
            $lines[] = sprintf( '  %s  %-32s %s', date( 'H:i:s', $e['recorded_at'] ), $e['audit_name'], \expMigrationRecordingEvent::line( $event ) );
            if ( !empty( $event['omitted'] ) )
                $lines[] = '            left out: ' . implode( ', ', $event['omitted'] );
            foreach ( isset( $event['notes'] ) ? (array)$event['notes'] : array() as $note )
                $lines[] = '            note: ' . $note;
        }
        foreach ( $report['plan'] as $i => $p )
        {
            $lines[] = sprintf( '  %3d  %s  (from %s)', $i + 1, \expMigrationRecordingEvent::line( array( 'step' => $p['step'], 'subject' => $p['subject'] ) ),
                                implode( ', ', array_unique( $p['audit'] ) ) );
            if ( $p['omitted'] )
                $lines[] = '            left out: ' . implode( ', ', $p['omitted'] );
        }
        if ( !$report['events'] && !$report['plan'] )
            $lines[] = '  nothing recorded';
        $lines[] = '';
        $lines[] = sprintf( 'Summary: %d event(s)%s', $r['events'], $report['mode'] === 'record-plan' ? ', ' . count( $report['plan'] ) . ' step(s)' : '' );
        return $lines;
    }
}

}
