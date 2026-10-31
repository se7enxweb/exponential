<?php
/**
 * File containing the expMigrationRunFlow class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What happens around a migration run that writes (expMigrationRunner calls it, under the run's lock):
 *
 *   before()           the code trees are checked: a run is refused while settings/, extension/ or design/ have
 *                      changes that are not committed, unless include-uncommitted (--include-uncommitted) puts them
 *                      into the caption. Then the caption migration-pre is taken (or an unchanged one reused). A
 *                      caption that cannot be taken does not stop the run: a warning says there is no way back.
 *   afterMigrations()  after a failure: a caption of the failed state (migration-failure), then the pre caption
 *                      restored (RestoreOnFailure, the default; --no-restore leaves the state as it is). Oracle has
 *                      no built-in restore: the run stops and the report lists the steps to restore by hand.
 *                      The record of the failed migration is written again after the restore.
 *   afterRun()         after a success the caption migration-post (the re-caption); then, failed or not, the status
 *                      file and run log committed, tagged and pushed (expMigrationRepository); the run's row
 *                      (exp_migration_run) gets caption_pre, caption_post, commit_id, tag_name and push_state, and
 *                      the migrations applied get the commit as their revision.
 *
 * This is the migrations' own rule. exp:upgrade keeps its rules: a caption that fails stops an upgrade, and an
 * upgrade restores only with --rollback-on-fail.
 *
 * The report gets 'captions' (pre, failure, post), 'restore' (state: restored, failed, manual, none, off; caption,
 * failure_caption, parts, steps), 'repository' (commit_id, tag_name, push_state, messages) and 'uncommitted'.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationRunFlow
{
    /** @var expMigrationContext */
    protected $context;

    /** @var expMigrationCaption */
    protected $caption;

    /** @var expMigrationRepository|null */
    protected $repository;

    /** @var array|null the pre caption */
    protected $pre;

    /** @var array[] what uncommitted() found */
    protected $uncommitted = array();

    /**
     * @param expMigrationContext $context
     * @param expMigrationCaption $caption
     * @param expMigrationRepository|null $repository null: nothing is committed
     */
    public function __construct( expMigrationContext $context, expMigrationCaption $caption, $repository = null )
    {
        $this->context = $context;
        $this->caption = $caption;
        $this->repository = $repository;
    }

    /**
     * The flow of migration.ini ([CaptionSettings], [RepositorySettings]) with the options of a run: no-caption,
     * no-restore, include-uncommitted, no-commit, no-tag, no-push.
     *
     * @param expMigrationContext $context
     * @param array $options
     * @return expMigrationRunFlow
     */
    public static function fromSettings( expMigrationContext $context, array $options = array() )
    {
        $options += array( 'root' => $context->root() );
        return new self( $context, new expMigrationCaption( $context, expMigrationCaption::settingsFrom( $options ) ),
                         expMigrationRepository::fromSettings( $options ) );
    }

    /** @return expMigrationCaption */
    public function caption()
    {
        return $this->caption;
    }

    /** @return expMigrationRepository|null */
    public function repository()
    {
        return $this->repository;
    }

    /**
     * A dry run with migrations to apply: what the run with --apply would do and refuse.
     *
     * @param array $report
     */
    public function plan( array &$report )
    {
        $problems = array();
        $changes = $this->caption->uncommitted( $problems );
        $report['uncommitted'] = self::summarizeUncommitted( $changes );
        foreach ( $problems as $problem )
            $report['warnings'][] = $problem;
        if ( $changes && !$this->caption->setting( 'include-uncommitted' ) )
            $report['warnings'][] = self::uncommittedText( $changes ) . ': a run with --apply is refused until they are committed (or given --include-uncommitted)';
        $report['captions'] = array( 'pre' => $this->caption->setting( 'before' ) ? 'would-take' : 'off', 'post' => $this->caption->setting( 'after' ) ? 'would-take' : 'off' );
    }

    /**
     * Before the first migration of a run that writes.
     *
     * @param array $report
     * @param string[] $ids the migrations to apply
     * @return bool false: the run is refused (the report says why)
     */
    public function before( array &$report, array $ids )
    {
        $context = $this->context;
        $report['captions'] = array( 'pre' => null, 'failure' => null, 'post' => null );
        $problems = array();
        $this->uncommitted = $this->caption->uncommitted( $problems );
        $report['uncommitted'] = self::summarizeUncommitted( $this->uncommitted );
        foreach ( $problems as $problem )
        {
            $report['warnings'][] = $problem;
            $context->log( 'warning', $problem );
        }
        $include = (bool)$this->caption->setting( 'include-uncommitted' );
        if ( $this->uncommitted && !$include )
        {
            $report['errors'][] = self::uncommittedText( $this->uncommitted ) . ': commit them first, or run with --include-uncommitted to keep them in the caption';
            $report['exit'] = expUpgrade::EXIT_USAGE;
            return false;
        }
        if ( !$this->caption->setting( 'before' ) )
        {
            $report['warnings'][] = 'no caption before the run (--no-caption or [CaptionSettings] Before=disabled): a failure cannot be restored automatically';
            if ( $this->uncommitted && $include )
                $report['warnings'][] = 'the changes that are not committed are kept nowhere: there is no caption';
            return true;
        }
        $pre = $this->caption->take( expMigrationCaption::PRE, $ids, $include ? $this->uncommitted : array() );
        $report['captions']['pre'] = self::summarizeCaption( $pre );
        if ( !empty( $pre['ok'] ) )
        {
            $this->pre = $pre;
            $context->setRunField( 'caption_pre', $pre['id'] );
            $context->log( 'info', ( !empty( $pre['reused'] ) ? 'reused the caption ' : 'caption ' ) . $pre['id'] . ' (' . $pre['source'] . ')' );
        }
        else
        {
            // decision 3a: a migration run goes on without a caption, with a warning (exp:upgrade stops instead)
            $text = 'no caption could be taken, the run goes on without a way back: ' . implode( '; ', (array)$pre['messages'] );
            $report['warnings'][] = $text;
            $context->log( 'warning', $text );
        }
        return true;
    }

    /**
     * After the migrations of a run: restores the pre caption when one failed.
     *
     * @param array $report
     */
    public function afterMigrations( array &$report )
    {
        $context = $this->context;
        $failed = array();
        $applied = array();
        foreach ( $report['migrations'] as $i => $entry )
            if ( $entry['action'] === 'failed' )
                $failed[] = $i;
            else if ( $entry['action'] === 'applied' )
                $applied[] = $i;
        if ( !$failed )
            return;
        $ids = array();
        foreach ( array_merge( $failed, $applied ) as $i )
            $ids[] = $report['migrations'][$i]['id'];

        if ( !$this->caption->setting( 'restore' ) )
        {
            $report['restore'] = array( 'state' => 'off', 'caption' => $this->pre ? $this->pre['id'] : '' );
            $report['warnings'][] = 'not restored (--no-restore or [CaptionSettings] RestoreOnFailure=disabled)'
                                  . ( $this->pre ? ': ./console exp:upgrade:rollback ' . $this->pre['id'] . ' --apply restores the caption taken before the run' : '' );
            return;
        }
        if ( !in_array( $context->engine(), expUpgradeRollback::RESTORE_ENGINES, true ) || !$this->pre )
        {
            $steps = expUpgradeRollback::manualSteps( $context->engine(), $context->db(), $this->pre ? $this->pre['dir'] : null );
            $report['restore'] = array( 'state' => $this->pre ? 'manual' : 'none', 'caption' => $this->pre ? $this->pre['id'] : '', 'failure_caption' => '',
                                        'parts' => array(), 'steps' => $steps );
            $report['errors'][] = ( $this->pre ? 'no built-in restore for ' . $context->engine() : 'no caption was taken before the run' )
                                . ': nothing was restored; restore by hand:' . "\n" . implode( "\n", $steps );
            $context->log( 'error', 'nothing restored; the steps to restore by hand: ' . implode( ' | ', $steps ) );
            $this->auditRestore( $report['restore'], false );
            return;
        }

        // the state the failure left, kept for the diagnosis, then the way back
        $failure = $this->caption->take( expMigrationCaption::FAILURE, $ids );
        $report['captions']['failure'] = self::summarizeCaption( $failure );
        if ( empty( $failure['ok'] ) )
            $report['warnings'][] = 'the caption of the failed state could not be taken (restored anyway): ' . implode( '; ', (array)$failure['messages'] );
        $rows = array();
        foreach ( $failed as $i )
        {
            $row = $context->record( $report['migrations'][$i]['source'], $report['migrations'][$i]['id'] );
            if ( $row )
                $rows[] = $row;
        }

        $restore = $this->caption->restore( $this->pre['id'] );
        $report['restore'] = array( 'state' => $restore['ok'] ? 'restored' : 'failed', 'caption' => $this->pre['id'],
                                    'failure_caption' => !empty( $failure['ok'] ) ? $failure['id'] : '', 'parts' => $restore['parts'], 'steps' => $restore['manual'] );
        if ( !$restore['ok'] )
        {
            $report['errors'][] = 'the restore of the caption ' . $this->pre['id'] . ' failed: ' . implode( '; ', $restore['errors'] )
                                . ( $restore['manual'] ? "; restore by hand:\n" . implode( "\n", $restore['manual'] ) : '' );
            $context->log( 'error', 'the restore of the caption ' . $this->pre['id'] . ' failed: ' . implode( '; ', $restore['errors'] ) );
            $this->auditRestore( $report['restore'], false );
            return;
        }
        // the restore put the record back to the time of the caption: the captions and the failure are written again
        $this->recordCaption( $this->pre );
        if ( !empty( $failure['ok'] ) )
            $this->recordCaption( $failure );
        foreach ( $rows as $row )
            $context->writeRecord( $row );
        foreach ( $applied as $i )
        {
            $report['migrations'][$i]['state'] = expMigration::PENDING;
            $report['migrations'][$i]['action'] = 'restored';
            $report['migrations'][$i]['messages'][] = 'undone: the caption ' . $this->pre['id'] . ' was restored after the failure';
        }
        $text = 'restored the caption ' . $this->pre['id'] . ' taken before the run (' . implode( ', ', array_keys( $restore['parts'] ) ) . ')'
              . ( !empty( $failure['ok'] ) ? '; the failed state is kept in the caption ' . $failure['id'] : '' );
        $report['warnings'][] = $text;
        $context->log( 'warning', $text );
        $this->auditRestore( $report['restore'], true );
    }

    /**
     * After the record of the run is written: the post caption, the commit, tag and push, the run's row.
     *
     * @param array $report
     */
    public function afterRun( array &$report )
    {
        $context = $this->context;
        $failed = (int)$report['exit'] !== expUpgrade::EXIT_OK;
        $applied = array();
        foreach ( $report['migrations'] as $entry )
            if ( $entry['action'] === 'applied' )
                $applied[] = $entry;
        if ( !isset( $report['captions'] ) )
            $report['captions'] = array( 'pre' => null, 'failure' => null, 'post' => null );
        if ( !$failed && $this->caption->setting( 'after' ) )
        {
            $post = $this->caption->take( expMigrationCaption::POST, array_column( $applied, 'id' ) );
            $report['captions']['post'] = self::summarizeCaption( $post );
            if ( !empty( $post['ok'] ) )
            {
                $context->setRunField( 'caption_post', $post['id'] );
                $context->log( 'info', 'caption ' . $post['id'] . ' after the run' );
            }
            else
                $report['warnings'][] = 'the caption after the run could not be taken: ' . implode( '; ', (array)$post['messages'] );
        }

        if ( $this->repository )
        {
            $repo = $this->repository->record( $context, $report );
            $report['repository'] = $repo;
            foreach ( $repo['messages'] as $message )
                $context->log( 'info', $message );
            foreach ( $repo['problems'] as $problem )
            {
                $report['warnings'][] = $problem;
                $context->log( 'warning', $problem );
            }
            $context->setRunField( 'commit_id', $repo['commit_id'] );
            $context->setRunField( 'tag_name', $repo['tag_name'] );
            $context->setRunField( 'push_state', $repo['push_state'] );
            if ( $repo['commit_id'] !== '' )
                foreach ( $applied as $entry )
                {
                    $row = $context->record( $entry['source'], $entry['id'] );
                    if ( $row && $row['run_id'] === $context->runId() )
                    {
                        $row['revision'] = $repo['commit_id'];
                        $context->writeRecord( $row );
                    }
                }
        }

        $run = $context->runRecord();
        if ( $run )
            $context->writeRun( array_merge( $run, $context->runFields() ) );
    }

    /**
     * One line about the changes that are not committed. Pure.
     *
     * @param array[] $changes as expMigrationCaption::uncommitted()
     * @return string
     */
    public static function uncommittedText( array $changes )
    {
        $parts = array();
        $count = 0;
        foreach ( $changes as $repo )
        {
            $files = array_merge( $repo['changed'], $repo['untracked'] );
            $count += count( $files );
            $parts[] = ( $repo['label'] === 'root' ? '' : $repo['label'] . ': ' ) . implode( ', ', array_slice( $files, 0, 5 ) ) . ( count( $files ) > 5 ? ' ...' : '' );
        }
        return "$count change(s) that are not committed in the code trees (" . implode( '; ', $parts ) . ')';
    }

    /**
     * @param array[] $changes
     * @return array[] label, changed, untracked (counts)
     */
    protected static function summarizeUncommitted( array $changes )
    {
        $out = array();
        foreach ( $changes as $repo )
            $out[] = array( 'label' => $repo['label'], 'changed' => count( $repo['changed'] ), 'untracked' => count( $repo['untracked'] ) );
        return $out;
    }

    /**
     * @param array $caption
     * @return array id, ok, reused, source, kind, messages
     */
    protected static function summarizeCaption( array $caption )
    {
        return array( 'id' => (string)$caption['id'], 'ok' => !empty( $caption['ok'] ), 'reused' => !empty( $caption['reused'] ),
                      'source' => isset( $caption['source'] ) ? (string)$caption['source'] : '', 'kind' => isset( $caption['kind'] ) ? (string)$caption['kind'] : '',
                      'messages' => isset( $caption['messages'] ) ? array_values( (array)$caption['messages'] ) : array() );
    }

    /**
     * Writes the record row of a caption again (expUpgradeRecord::writeCaption()), after a restore removed it.
     *
     * @param array $caption
     */
    protected function recordCaption( array $caption )
    {
        try
        {
            expUpgradeRecord::writeCaption( $this->context->db(), $caption['id'], $caption['kind'], $caption['label'], $caption['source'] );
        }
        catch ( Throwable $e )
        {
            $this->context->log( 'warning', 'the record of the caption ' . $caption['id'] . ' could not be written: ' . $e->getMessage() );
        }
    }

    /**
     * The audit event system.migration.rollback of the automatic restore.
     *
     * @param array $restore
     * @param bool $ok
     */
    protected function auditRestore( array $restore, $ok )
    {
        $this->context->audit( 'system.migration.rollback', array(
            'object' => array( 'type' => 'migration_run', 'id' => $this->context->runId() ),
            'verb' => 'restore',
            'result' => $ok ? 'success' : 'failure',
            'reason' => $ok ? '' : (string)$restore['state'],
            'x' => array( 'caption' => (string)$restore['caption'], 'failure_caption' => isset( $restore['failure_caption'] ) ? (string)$restore['failure_caption'] : '',
                          'engine' => $this->context->engine(), 'parts' => isset( $restore['parts'] ) ? array_keys( (array)$restore['parts'] ) : array() ),
        ) );
    }
}
