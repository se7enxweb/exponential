<?php
/**
 * @description Discard a recording of changes: the recording and every event it captured are removed (a running one stops recording); a dry run unless --apply
 * @alias exp:migration:record:discard
 *
 * File containing the exp:migration:record:discard command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expmigrationrecorddiscard extends ExpmigrationrecordBase
{
    protected function name()
    {
        return 'exp:migration:record:discard';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Discard a recording of changes\n" .
                             "Removes the recording and every event it captured; a recording that runs stops. What was changed\n" .
                             "while it recorded stays changed: only the record of it goes. Without --apply it says what it would\n" .
                             "remove.\n" .
                             "\n" .
                             "./console exp:migration:record:discard 20261101-120000-a1b2c3\n" .
                             "./console exp:migration:record:discard 20261101-120000-a1b2c3 --apply",
        ) + self::SCRIPT );
        return (array)$this->startup( '[apply][dry-run][format:]', '[recording]', array(
            'apply' => 'Make the changes (the default is a dry run that lists them and changes nothing)',
            'dry-run' => 'The default, accepted for scripts that pass it: list what would change, change nothing',
            'format' => 'text (default) or json (the machine output exp-migration/1)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $apply = $this->apply( $options );
        $id = $this->recordingArgument( $options );
        if ( $id === '' )
            $this->usage( 'name the recording, e.g. ./console exp:migration:record:discard 20261101-120000-a1b2c3 --apply' );
        $db = $this->database( $format );
        $this->tables( $db, $format );
        $row = \expMigrationRecording::get( $db, $id );
        $report = array( 'mode' => $apply ? 'record-discard' : 'record-discard-dry-run', 'recording' => $row, 'discarded' => false,
                         'errors' => array(), 'warnings' => array() );
        if ( $row === null )
            $report['errors'][] = "no recording $id";
        else if ( $apply )
        {
            $errors = array();
            $report['discarded'] = \expMigrationRecording::discard( $db, $id, $errors );
            $report['errors'] = $errors;
        }
        $this->finish( $report, $format );
    }

    protected function lines( array $report )
    {
        $lines = array( 'Exponential migrations: ' . ( $report['mode'] === 'record-discard' ? 'discard a recording' : 'discard a recording, dry run (--apply removes it)' ) );
        if ( $report['recording'] )
        {
            $lines[] = self::recordingLine( $report['recording'] );
            $lines[] = $report['discarded'] ? 'Discarded: the recording and its events are removed' : 'Would remove the recording and its ' . (int)$report['recording']['event_count'] . ' event(s)';
        }
        return $lines;
    }
}

}
