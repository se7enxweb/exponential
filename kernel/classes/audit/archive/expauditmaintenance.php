<?php
/**
 * The audit's scheduled work (doc/bc/6.0/audit.md, "Rotation, archives and retention (Q8)"), run by the cronjob
 * part audit (cronjobs/audit.php), and task by task by exp:audit and the console's archives view.
 *
 * Every run: the incremental index (when the index exists: expAuditIndexer, which has its own lock), the sink
 * spools, the alert rules' cronjob pass (new records, the "audit disabled" check, closed windows).
 * Once a day, on the first run after [AuditRotationSettings] RotateAfter (HH:MM, the site's time zone): rotation
 * by day of every channel (system.audit.file.close for a channel nobody wrote to since midnight UTC), verification
 * of every channel (system.audit.verify, system.audit.chain.broken), archiving of the days older than LiveDays,
 * retention of archives older than ArchiveDays, pseudonymisation of the index (when it exists), and the checkpoint.
 * A run takes <LogDir>/.cron.lock, so two runs never overlap; the day of the last daily run is in
 * <LogDir>/.cron-daily.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditMaintenance
{
    /** @var callable|null receives one line of output */
    protected $say;

    /** @var array */
    protected $config;

    /** @var int|null Fixed clock, epoch seconds (tests) */
    protected $now;

    /**
     * @param callable|null $say
     * @param int|null $now
     */
    public function __construct( $say = null, $now = null )
    {
        $this->say = $say;
        $this->config = expAuditConfig::get();
        $this->now = $now;
    }

    protected function say( $line )
    {
        if ( $this->say )
            call_user_func( $this->say, $line );
    }

    /** @return int */
    protected function now()
    {
        return $this->now !== null ? (int)$this->now : time();
    }

    /**
     * One cronjob run.
     *
     * @param array $options daily (true: do the daily tasks now, whatever the time), noIndex
     * @return array what was done: locked, index, sinks, alerts, daily (null when not due)
     */
    public function run( array $options = array() )
    {
        $report = array( 'locked' => false, 'index' => null, 'sinks' => array(), 'alerts' => null, 'daily' => null );
        $dir = $this->config['logDir'];
        if ( !expAuditWriter::ensureDirectory( $dir ) )
            return $report + array( 'error' => "$dir cannot be created" );
        $lockFile = $dir . '/.cron.lock';
        $created = !is_file( $lockFile );
        $lock = @fopen( $lockFile, 'c' );
        if ( !$lock )
            return $report + array( 'error' => "$lockFile cannot be opened" );
        if ( $created )
            expAuditWriter::ownLikeParent( $lockFile, 0640 );
        if ( !flock( $lock, LOCK_EX | LOCK_NB ) )
        {
            fclose( $lock );
            $report['locked'] = true;
            $this->say( 'Audit: another run is in progress' );
            return $report;
        }
        $runId = expAudit::requestId();
        expAudit::setRun( $runId );
        try
        {
            if ( empty( $options['noIndex'] ) )
                $report['index'] = $this->index();
            $report['sinks'] = $this->sinks();
            if ( class_exists( 'expAuditAlertEvaluator' ) )
            {
                $report['alerts'] = expAuditAlertEvaluator::cronjob();
                if ( $report['alerts']['alerts'] || $report['alerts']['disabled'] )
                    $this->say( sprintf( 'Audit alerts: %d records evaluated, %d alerts%s', $report['alerts']['evaluated'], $report['alerts']['alerts'],
                                         $report['alerts']['disabled'] ? ', audit found disabled' : '' ) );
            }
            if ( !empty( $options['daily'] ) || $this->dailyDue() )
                $report['daily'] = $this->daily();
        }
        catch ( Throwable $e )
        {
            $report['error'] = $e->getMessage();
            if ( class_exists( 'eZDebug' ) )
                eZDebug::writeError( 'The audit cronjob part: ' . $e->getMessage(), __METHOD__ );
        }
        finally
        {
            expAudit::flush();
            expAudit::setRun( null );
            flock( $lock, LOCK_UN );
            fclose( $lock );
        }
        return $report;
    }

    /**
     * Whether the daily tasks are due: the first run after RotateAfter on a day they have not run yet.
     *
     * @return bool
     */
    public function dailyDue()
    {
        $tz = expAuditScheduleRule::timeZone();
        $d = new DateTime( '@' . $this->now() );
        $d->setTimezone( $tz );
        $after = trim( (string)expAuditConfig::value( 'AuditRotationSettings', 'RotateAfter', '00:15' ) );
        if ( !preg_match( '/^(\d{1,2}):(\d{2})$/', $after, $m ) )
            $m = array( '', '0', '15' );
        if ( (int)$d->format( 'G' ) * 60 + (int)$d->format( 'i' ) < (int)$m[1] * 60 + (int)$m[2] )
            return false;
        $last = @file_get_contents( $this->config['logDir'] . '/.cron-daily' );
        return trim( (string)$last ) !== $d->format( 'Y-m-d' );
    }

    /**
     * The daily tasks.
     *
     * @return array rotated, verified, archived, purged, pseudonymised, checkpoint
     */
    public function daily()
    {
        $tz = expAuditScheduleRule::timeZone();
        $d = new DateTime( '@' . $this->now() );
        $d->setTimezone( $tz );
        $stateFile = $this->config['logDir'] . '/.cron-daily';
        $created = !is_file( $stateFile );
        @file_put_contents( $stateFile, $d->format( 'Y-m-d' ) );
        if ( $created )
            expAuditWriter::ownLikeParent( $stateFile, 0640 );

        $out = array( 'rotated' => $this->rotate(), 'verified' => $this->verify(), 'archived' => array(), 'purged' => array(),
                      'pseudonymised' => null, 'checkpoint' => null );
        $archiver = new expAuditArchiver( $this->config, null, $this->now );
        foreach ( $this->config['channels'] as $channel )
        {
            $a = $archiver->archive( $channel );
            if ( $a )
            {
                $out['archived'][$channel] = $a;
                $this->say( "Audit: archived $channel: " . implode( ', ', array_keys( $a ) ) );
            }
            $p = $archiver->purge( $channel );
            if ( $p['purged'] )
            {
                $out['purged'][$channel] = $p;
                $this->say( "Audit: retention removed " . count( $p['purged'] ) . " archived day(s) of $channel" );
            }
        }
        $out['pseudonymised'] = $this->pseudonymise();
        if ( $this->config['checkpoints'] )
            $out['checkpoint'] = expAudit::checkpoint( null, null, false );
        return $out;
    }

    /**
     * Rotation by day of every channel.
     *
     * @param bool $dryRun
     * @param string|null $only one channel
     * @return array channel => closed file
     */
    public function rotate( $dryRun = false, $only = null )
    {
        $out = array();
        $writer = expAudit::writerFor( $this->config );
        if ( $this->now !== null )
        {
            $today = gmdate( 'Y-m-d', $this->now );
            $writer->setClock( function () use ( $today ) { return $today; } );
        }
        foreach ( $this->config['channels'] as $channel )
        {
            if ( $only !== null && $channel !== $only )
                continue;
            try
            {
                $closed = $writer->rotate( $channel, $dryRun );
            }
            catch ( Throwable $e )
            {
                $this->say( "Audit: rotation of $channel failed: " . $e->getMessage() );
                continue;
            }
            if ( $closed )
            {
                $out[$channel] = $closed;
                if ( !$dryRun )
                    expAudit::event( 'system.audit.rotate', array( 'object' => array( 'type' => 'file', 'id' => $closed['file'] ),
                                                                   'after' => array( 'channel' => $channel, 'file' => $closed['file'],
                                                                                     'records' => $closed['seq'], 'last_hash' => $closed['hash'] ) ) );
            }
        }
        return $out;
    }

    /**
     * Verifies every channel (live files, linked to the archives) and records the result.
     *
     * @return array channel => result
     */
    public function verify()
    {
        $archiver = new expAuditArchiver( $this->config, null, $this->now );
        $verifier = $archiver->verifier();
        $out = array();
        foreach ( $verifier->verifyAll() as $c => $res )
        {
            $out[$c] = $res['result'];
            expAudit::event( 'system.audit.verify', array( 'object' => array( 'type' => 'channel', 'id' => $c ),
                                                           'result' => $res['result'] === 'broken' ? 'failed' : 'success',
                                                           'after' => array( 'result' => $res['result'], 'records' => $res['records'], 'files' => $res['files'],
                                                                             'via' => 'cronjob' ) ) );
            if ( $res['result'] === 'broken' )
            {
                $first = $res['breaks'][0];
                expAudit::event( 'system.audit.chain.broken', array( 'object' => array( 'type' => 'file', 'id' => $first['file'] ),
                                                                     'result' => 'failed', 'reason' => $first['kind'],
                                                                     'after' => array( 'channel' => $c, 'file' => $first['file'], 'line' => $first['line'],
                                                                                       'kind' => $first['kind'], 'breaks' => count( $res['breaks'] ), 'found_by' => 'cronjob' ) ) );
                $this->say( "Audit: the $c chain is BROKEN at {$first['file']} line {$first['line']} ({$first['kind']})" );
            }
        }
        return $out;
    }

    /** @return array|null The incremental index run (stage 4's indexer), null without an index */
    public function index()
    {
        if ( !class_exists( 'expAuditIndexer' ) || !class_exists( 'expAuditIndexSettings' ) || expAuditConfig::isOverridden() )
            return null;
        try
        {
            $settings = expAuditIndexSettings::get();
            if ( empty( $settings['index'] ) )
                return null;
            $indexer = new expAuditIndexer();
            $r = $indexer->run( array( 'maxSeconds' => 120 ) );
            if ( !empty( $r['rows'] ) )
                $this->say( 'Audit index: ' . $r['rows'] . ' rows' );
            return $r;
        }
        catch ( Throwable $e )
        {
            return array( 'ok' => false, 'error' => $e->getMessage() );
        }
    }

    /** @return array|null The index pseudonymisation (stage 4's indexer), null without an index */
    public function pseudonymise()
    {
        if ( !class_exists( 'expAuditIndexer' ) || !method_exists( 'expAuditIndexer', 'pseudonymise' ) || expAuditConfig::isOverridden() )
            return null;
        try
        {
            $indexer = new expAuditIndexer();
            return $indexer->pseudonymise();
        }
        catch ( Throwable $e )
        {
            return array( 'ok' => false, 'error' => $e->getMessage() );
        }
    }

    /** @return array The spools delivered */
    public function sinks()
    {
        if ( !class_exists( 'expAuditSinkRegistry' ) )
            return array();
        $out = expAuditSinkRegistry::deliverSpools();
        foreach ( $out as $name => $r )
        {
            if ( $r['delivered'] || $r['failed'] )
                $this->say( sprintf( 'Audit sink %s: %d delivered, %d waiting%s', $name, $r['delivered'], $r['remaining'],
                                     $r['failed'] ? ' (failed: ' . $r['error'] . ')' : '' ) );
        }
        return $out;
    }
}
