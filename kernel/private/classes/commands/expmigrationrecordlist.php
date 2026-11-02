<?php
/**
 * @description List the recordings of changes, running and stopped, the newest first, with their kind, state and number of events
 * @alias exp:migration:record:list
 *
 * File containing the exp:migration:record:list command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expmigrationrecordlist extends ExpmigrationrecordBase
{
    protected function name()
    {
        return 'exp:migration:record:list';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "List the recordings of changes\n" .
                             "Every recording, the newest first: its id, state (recording or stopped), kind (session or site), how\n" .
                             "many events it has, when it started, whose changes a session recording keeps (the user id) and its\n" .
                             "label. Changes nothing.\n" .
                             "\n" .
                             "./console exp:migration:record:list\n" .
                             "./console exp:migration:record:list --count=10 --format=json",
        ) + self::SCRIPT );
        return (array)$this->startup( '[format:][count:]', '', array(
            'format' => 'text (default) or json (the machine output exp-migration/1)',
            'count' => 'How many recordings to list, the newest first (default 50)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $count = 50;
        if ( isset( $options['count'] ) && $options['count'] !== false )
        {
            if ( !ctype_digit( (string)$options['count'] ) || (int)$options['count'] < 1 )
                $this->usage( '--count must be a number above 0' );
            $count = (int)$options['count'];
        }
        $db = $this->database( $format );
        $this->tables( $db, $format );
        $this->finish( array( 'mode' => 'record-list', 'recordings' => \expMigrationRecording::all( $db, $count ), 'errors' => array(), 'warnings' => array() ), $format );
    }

    protected function lines( array $report )
    {
        $lines = array( 'Exponential migrations: recordings of changes, the newest first' );
        foreach ( $report['recordings'] as $r )
            $lines[] = self::recordingLine( $r );
        if ( !$report['recordings'] )
            $lines[] = '  none (./console exp:migration:record:start --apply starts one)';
        return $lines;
    }
}

}
