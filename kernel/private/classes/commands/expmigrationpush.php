<?php
/**
 * @description Push the migration runs whose push failed (push state pending) to the remote of the migrations repository; a dry run unless --apply
 * @alias exp:migration:push
 *
 * File containing the exp:migration:push command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expmigrationpush extends ExpmigrationBase
{
    protected function name()
    {
        return 'exp:migration:push';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Push the migration runs that wait\n" .
                             "A run whose push failed keeps its commit and tag in the local migrations repository and the push\n" .
                             "state pending; the cronjob part migrationpush pushes them again, and so does this command. Without\n" .
                             "--apply it lists the runs that wait. The branch and the tags go to [RepositorySettings] Remote, oldest\n" .
                             "run first; each run's push state is recorded.\n" .
                             "\n" .
                             "./console exp:migration:push\n" .
                             "./console exp:migration:push --apply",
        ) + self::SCRIPT );
        return (array)$this->startup( '[apply][dry-run][format:][limit:]', '', array(
            'apply' => 'Make the changes (the default is a dry run that lists them and changes nothing)',
            'dry-run' => 'The default, accepted for scripts that pass it: list what would change, change nothing',
            'format' => 'text (default) or json (the machine output exp-migration/1)',
            'limit' => 'Push at most this many runs (default 20)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $apply = $this->apply( $options );
        if ( isset( $options['limit'] ) && $options['limit'] !== false && ( !ctype_digit( (string)$options['limit'] ) || (int)$options['limit'] < 1 ) )
            $this->usage( '--limit must be a number of at least 1' );
        $limit = isset( $options['limit'] ) && $options['limit'] !== false ? (int)$options['limit'] : 20;
        $db = $this->database( $format );
        $waiting = array();
        foreach ( array_reverse( \expMigrationRecord::runs( $db, 0 ) ) as $run )
            if ( $run['push_state'] === \expMigrationRepository::PENDING )
                $waiting[] = $run['run_id'];
        $report = array( 'mode' => $apply ? 'push' : 'push-dry-run', 'waiting' => $waiting, 'push' => null, 'errors' => array(), 'warnings' => array(),
                         'exit' => \expUpgrade::EXIT_OK );
        if ( $apply && $waiting )
        {
            $result = \expMigrationRepository::fromSettings()->retryPushes( $db, $limit );
            $report['push'] = $result;
            if ( $result['pending'] > 0 || ( $result['tried'] === 0 && $result['messages'] ) )
            {
                $report['errors'][] = $result['tried'] === 0 ? implode( '; ', $result['messages'] ) : $result['pending'] . ' run(s) could not be pushed';
                $report['exit'] = \expUpgrade::EXIT_FAILED;
            }
        }
        $this->finish( $report, $format );
    }

    protected function lines( array $report )
    {
        $lines = array( 'Exponential migrations: ' . ( $report['mode'] === 'push' ? 'push' : 'push, dry run (--apply pushes)' ) );
        if ( !$report['waiting'] )
            $lines[] = 'No run waits for its push.';
        foreach ( $report['waiting'] as $runId )
            $lines[] = '  ' . ( $report['mode'] === 'push' ? 'pending  ' : 'would push ' ) . $runId;
        if ( $report['push'] )
        {
            foreach ( $report['push']['messages'] as $message )
                $lines[] = '  ' . $message;
            $lines[] = sprintf( 'Pushed %d of %d tried, %d still pending', $report['push']['pushed'], $report['push']['tried'], $report['push']['pending'] );
        }
        return $lines;
    }
}

}
