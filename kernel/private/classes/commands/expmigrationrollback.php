<?php
/**
 * @description Take migrations back: a run (its caption restored), one migration (its down()) or, for experts, any caption; a dry run unless --apply
 * @alias exp:migration:rollback
 *
 * File containing the exp:migration:rollback command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expmigrationrollback extends ExpmigrationBase
{
    protected function name()
    {
        return 'exp:migration:rollback';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Take migrations back\n" .
                             "The argument names what to take back, found in this order:\n" .
                             "  a run id         the caption taken before the run is restored (exp:upgrade's rollback: a safety\n" .
                             "                   caption of the current state first, then database, storage and settings)\n" .
                             "  a migration id   its down(), when it has one; the record of it is removed\n" .
                             "  a caption id     any earlier caption, for experts: --expert --confirm=<the caption id>\n" .
                             "Without --apply it lists what would be restored or undone. Oracle has no built-in restore: the\n" .
                             "command prints the steps to restore by hand.\n" .
                             "\n" .
                             "./console exp:migration:rollback 20261031-120000-a1b2c3\n" .
                             "./console exp:migration:rollback 20261031-120000-a1b2c3 --apply\n" .
                             "./console exp:migration:rollback 20261031120000_add_news_class --apply\n" .
                             "./console exp:migration:rollback 2026-10-29_10-15-00 --expert --confirm=2026-10-29_10-15-00 --apply",
        ) + self::SCRIPT );
        return (array)$this->startup( '[apply][dry-run][format:][source:][environment:][login:][expert][confirm:][parts:][backup-dir:][no-safety-caption][force-no-caption][no-verify][progress:]', '[target]', array(
            'apply' => 'Make the changes (the default is a dry run that lists them and changes nothing)',
            'dry-run' => 'The default, accepted for scripts that pass it: list what would change, change nothing',
            'format' => 'text (default) or json (the machine output exp-migration/1)',
            'source' => 'The source: site or extension:<name> (generate: where the file goes; rollback: which migration, when two sources have its id)',
            'environment' => 'The environment (default the installation\'s: [AdminEnvironment] Level, or EXP_ENV with Level=auto; check: every environment); only the migrations for it count',
            'login' => 'Run as this user (default: [RunSettings] RunAsUser of migration.ini)',
            'expert' => 'Restore a caption that is not the one taken before a run (with --confirm)',
            'confirm' => 'The id of the caption typed again, to restore it with --expert',
            'parts' => 'Only these parts of the caption, a comma list of database, storage, settings, libraries, code (default [CaptionSettings] RestoreParts)',
            'backup-dir' => 'Where the captions made without git_manager are (default var/backup/upgrade)',
            'no-safety-caption' => 'Restore without taking a caption of the current state first; refused on production without --force-no-caption',
            'force-no-caption' => 'With --no-safety-caption on a production installation: restore without a caption all the same',
            'no-verify' => 'Skip the verification after the restore',
            'progress' => 'Append the progress of the run to this file, one JSON object per line (what the administration shows while the run goes on)',
        ) );
    }

    /** @var string run, migration or caption: what the argument named */
    protected $kind = '';

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $apply = $this->apply( $options );
        $target = $this->argument( $options );
        if ( $target === '' )
            $this->usage( 'name what to take back: a run id, a migration id or a caption id (./console exp:migration:status lists runs and migrations)' );
        $selection = $this->selection( $options );
        $progress = $this->progress( $options );
        $db = $this->database( $format );
        $restore = array( 'apply' => $apply, 'db' => $db, 'command' => $this->name(),
                          'no-caption' => !empty( $options['no-safety-caption'] ), 'force-no-caption' => !empty( $options['force-no-caption'] ),
                          'no-verify' => !empty( $options['no-verify'] ) );
        foreach ( array( 'parts', 'backup-dir' ) as $name )
            if ( isset( $options[$name] ) && $options[$name] !== false && $options[$name] !== '' )
                $restore[$name] = (string)$options[$name];

        if ( \expMigrationRecord::validRunId( $target ) && \expMigrationRecord::run( $db, $target ) )
        {
            $this->kind = 'run';
            $this->restoreStarts( $progress );
            $this->finish( $this->restoreEnds( $progress, \expMigrationRollback::run( $target, $restore ) + array( 'target' => array( 'kind' => 'run', 'id' => $target ) ) ), $format );
            return;
        }
        $errors = array();
        $context = new \expMigrationContext( $db, false, $selection + array( 'no-mirror' => true ) );
        foreach ( \expMigrationRunner::migrations( $context->environment(), $errors ) as $migration )
            if ( $migration->id() === $target )
            {
                $this->kind = 'migration';
                $one = $selection + array( 'apply' => $apply, 'db' => $db ) + array_intersect_key( $progress, array( 'progress' => true ) );
                if ( isset( $options['source'] ) && $options['source'] !== false && $options['source'] !== '' )
                    $one['source'] = (string)$options['source'];
                $this->finish( \expMigrationRollback::migration( $target, $one ) + array( 'target' => array( 'kind' => 'migration', 'id' => $target ) ), $format );
                return;
            }
        if ( \expUpgradeCaption::find( $target, isset( $restore['backup-dir'] ) ? $restore['backup-dir'] : null ) !== null )
        {
            $this->kind = 'caption';
            $restore['expert'] = !empty( $options['expert'] );
            if ( isset( $options['confirm'] ) && $options['confirm'] !== false )
                $restore['confirm'] = (string)$options['confirm'];
            $this->restoreStarts( $progress );
            $this->finish( $this->restoreEnds( $progress, \expMigrationRollback::caption( $target, $restore ) + array( 'target' => array( 'kind' => 'caption', 'id' => $target ) ) ), $format );
            return;
        }
        $this->finish( array( 'mode' => 'rollback', 'target' => array( 'kind' => '', 'id' => $target ),
                              'errors' => array_merge( array( "$target is no run, no migration and no caption of this installation" ), $errors ),
                              'warnings' => array(), 'exit' => \expUpgrade::EXIT_USAGE ), $format );
    }

    /**
     * The progress of a restore, which has no steps: the events run and phase restore.
     *
     * @param array $progress the result of progress()
     */
    protected function restoreStarts( array $progress )
    {
        if ( isset( $progress['progress'] ) )
        {
            call_user_func( $progress['progress'], 'run', array( 'mode' => 'restore', 'total' => 0, 'ids' => array() ) );
            call_user_func( $progress['progress'], 'phase', array( 'name' => 'restore' ) );
        }
    }

    /**
     * The event end of a restore.
     *
     * @param array $progress the result of progress()
     * @param array $report
     * @return array the report
     */
    protected function restoreEnds( array $progress, array $report )
    {
        if ( isset( $progress['progress'] ) )
            call_user_func( $progress['progress'], 'end', array( 'exit' => isset( $report['exit'] ) ? (int)$report['exit'] : 0, 'summary' => array() ) );
        return $report;
    }

    protected function lines( array $report )
    {
        if ( $this->kind === 'migration' )
            return self::runLines( $report );
        if ( $this->kind === 'run' || $this->kind === 'caption' )
            return self::restoreLines( $report );
        return array();
    }
}

}
