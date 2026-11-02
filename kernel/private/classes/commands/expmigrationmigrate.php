<?php
/**
 * @description Run the pending migrations: a dry run unless --apply; captions before and after, restore on failure, commit, tag and push
 * @alias exp:migration:migrate
 *
 * File containing the exp:migration:migrate command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expmigrationmigrate extends ExpmigrationBase
{
    protected function name()
    {
        return 'exp:migration:migrate';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Run the pending migrations\n" .
                             "Without --apply a dry run: every pending migration with what it would change, nothing written.\n" .
                             "With --apply, under the installation lock (shared with exp:upgrade) and as [RunSettings] RunAsUser:\n" .
                             "the code trees are checked for changes that are not committed, a caption is taken, the migrations\n" .
                             "run in order (each in a transaction where the database has one), the run stops at the first failure\n" .
                             "and restores the caption, a caption is taken after a run that passed, and the status file and run log\n" .
                             "are committed, tagged and pushed in the migrations repository. A migration whose file changed after\n" .
                             "it was applied stops the run unless --force --reason=<text> accepts it.\n" .
                             "\n" .
                             "./console exp:migration:migrate\n" .
                             "./console exp:migration:migrate --apply\n" .
                             "./console exp:migration:migrate --only=20261031120000_add_news_class --apply\n" .
                             "./console exp:migration:migrate --path=extension/expnews/migrations --environment=staging --apply --no-push\n" .
                             "./console exp:migration:migrate --apply --max-migrations=20 --max-time=600 --throttle=0.5 --nice=10",
        ) + self::SCRIPT );
        return (array)$this->startup( '[apply][dry-run][only:*][path:*][environment:][format:][login:][no-caption][no-restore][include-uncommitted][no-commit][no-push][no-tag][force][reason:][run-id:][progress:][max-migrations:][max-time:][throttle:][nice:]', '', array(
            'apply' => 'Make the changes (the default is a dry run that lists them and changes nothing)',
            'dry-run' => 'The default, accepted for scripts that pass it: list what would change, change nothing',
            'only' => 'Only these migrations (migration ids, a comma list or repeated)',
            'path' => 'Migration files or folders, relative to the root or absolute (a comma list or repeated): migrate and dry-run take only the migrations in them, check checks them instead of the migration directories',
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
        $this->finish( \expMigrationRunner::migrate( $run ), $format );
    }
}

}
