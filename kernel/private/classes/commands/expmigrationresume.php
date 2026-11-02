<?php
/**
 * @description Resume a migration run that failed or was interrupted: the migrations still pending run again; a dry run unless --apply
 * @alias exp:migration:resume
 *
 * File containing the exp:migration:resume command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expmigrationresume extends ExpmigrationBase
{
    protected function name()
    {
        return 'exp:migration:resume';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Resume a migration run\n" .
                             "The run named, or the newest run that failed or was interrupted (its record says running, but no\n" .
                             "process holds the installation lock any more). A run that is still running, that finished or that\n" .
                             "was rolled back is not resumed. Resuming is a new run, in the environment of the old one, of the\n" .
                             "migrations still pending: those the old run did not reach, the one that failed, and those the\n" .
                             "restore after the failure took back. With --apply the record of an interrupted run is closed as\n" .
                             "failed first. The options of the run are those of exp:migration:migrate.\n" .
                             "\n" .
                             "./console exp:migration:resume\n" .
                             "./console exp:migration:resume 20261031-120000-a1b2c3 --apply",
        ) + self::SCRIPT );
        return (array)$this->startup( '[apply][dry-run][environment:][format:][login:][no-caption][no-restore][include-uncommitted][no-commit][no-push][no-tag][force][reason:][run-id:][progress:][max-migrations:][max-time:][throttle:][nice:]', '[run]', array(
            'apply' => 'Make the changes (the default is a dry run that lists them and changes nothing)',
            'dry-run' => 'The default, accepted for scripts that pass it: list what would change, change nothing',
            'environment' => 'The environment (default the installation\'s: [AdminEnvironment] Level, or EXP_ENV with Level=auto; check: every environment); only the migrations for it count',
            'format' => 'text (default) or json (the machine output exp-migration/1)',
            'login' => 'Run as this user (default: [RunSettings] RunAsUser of migration.ini)',
            'no-caption' => 'No caption before and after the run: a failure cannot be restored automatically',
            'no-restore' => 'After a failure, leave the installation as the failure left it instead of restoring the caption taken before the run',
            'include-uncommitted' => 'Run although settings/, extension/ or design/ have changes that are not committed, and keep them in the caption',
            'no-commit' => 'Do not commit the status file and run log to the migrations repository (no tag and no push either)',
            'no-push' => 'Commit and tag, but do not push',
            'no-tag' => 'Commit and push, but do not tag the run',
            'force' => 'With --reason: accept migrations whose file changed after they were applied (they are not run again)',
            'reason' => 'Why a changed migration is accepted (needed with --force; kept in the record and the audit log)',
            'run-id' => 'The id of the run to start (default a new one: <date>-<time>-<6 hex digits>); the administration names it before it starts the run',
            'progress' => 'Append the progress of the run to this file, one JSON object per line (what the administration shows while the run goes on)',
            'max-migrations' => 'Apply at most this many migrations; the rest stay pending for the next run (default [RunSettings] MaxMigrations, empty: all)',
            'max-time' => 'Start no further migration after this many seconds; the rest stay pending for the next run (default [RunSettings] MaxTime)',
            'throttle' => 'Pause this many seconds between two migrations and between the batches of a long migration (default [RunSettings] Throttle)',
            'nice' => 'Run at this lower priority, 1 to 19 as nice(1) (default [RunSettings] Nice)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $apply = $this->apply( $options );
        $run = $this->selection( $options ) + $this->flow( $options ) + $this->progress( $options ) + $this->longRun( $options ) + array( 'apply' => $apply );
        $run['db'] = $this->database( $format );
        $runId = $this->argument( $options );
        $this->finish( \expMigrationResume::run( $runId !== '' ? $runId : null, $run ), $format );
    }
}

}
