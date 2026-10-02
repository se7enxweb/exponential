<?php
/**
 * File containing the expContentJobWorker class.
 *
 * Runs a content job in batches: bin/php/expcontentjob.php run <id>, started detached by expContentJob::spawn()
 * (or by the cronjob part 'contentjobs', or in the foreground by the command). Like the repair queue's worker
 * (lib/ezutils/classes/ezprepairqueue.php) it becomes the site user first when started as root (dropPrivileges(),
 * called by the command before the kernel reads a setting or opens the database), and holds the job's run
 * lock (<id>.run.lock, flock) for as long as it runs, so a second worker for the same job leaves at once.
 *
 * Per batch: a transaction of its own (begin, the type's runBatch(), commit; rolled back and the job failed on
 * any error), then the type's afterBatch(), the checkpoint and heartbeat written to the job file, a log line with
 * the batch's time and peak memory, the in-memory caches cleared (memory stays flat over any number of batches),
 * a cancel looked for, and the pause (BatchPause ms). The operations run as the requesting eZ user.
 *
 * A worker that is killed (kill -9, a reboot) leaves the job 'running' with a free run lock: the cronjob part
 * resumes it, and the type's idempotent batches continue where the last checkpoint stopped. A job that is
 * resumed MaxAttempts times without a single batch done in between is failed rather than retried for ever.
 *
 * Exit codes of run(): 0 done or cancelled, 1 failed, 2 not runnable (another worker runs it, or its state is
 * final), 3 unknown job.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentJobWorker
{
    /** @var callable|null receives a line of progress (the command's output) */
    public $output = null;

    /** @var resource|null the run lock */
    protected $runLock = null;

    /** @var string|null the audit event of this run (content.job.start), the parent of what its batches record */
    protected $auditParent = null;

    /** @var float when this run started (microtime) */
    protected $auditStarted = 0.0;

    /**
     * As root, becomes the site user: the owner of the installation's var/ directory (index.php may belong to
     * root on a managed host). Call before the kernel reads a setting, writes a cache or opens the database.
     *
     * @param string $root the installation's directory
     * @return string|null an error, or null (done, or not root, or nothing to switch to)
     */
    public static function dropPrivileges( $root )
    {
        if ( !function_exists( 'posix_geteuid' ) || posix_geteuid() !== 0 )
            return null;
        $uid = 0;
        $gid = 0;
        foreach ( array( "$root/var", "$root/index.php" ) as $candidate )
        {
            if ( file_exists( $candidate ) && fileowner( $candidate ) !== 0 )
            {
                $uid = fileowner( $candidate );
                $gid = filegroup( $candidate );
                break;
            }
        }
        if ( $uid === 0 )
            return null;
        $pw = posix_getpwuid( $uid );
        if ( $pw && function_exists( 'posix_initgroups' ) )
            @posix_initgroups( $pw['name'], $gid );
        if ( !posix_setgid( $gid ) || !posix_setuid( $uid ) )
            return "could not switch to the installation's owner (uid $uid)";
        putenv( 'HOME=' . ( $pw ? $pw['dir'] : $root ) );
        umask( 0007 );
        return null;
    }

    /**
     * Runs a job until it is done, failed or cancelled.
     *
     * @param string $id
     * @return int exit code (see the class)
     */
    public function run( $id )
    {
        if ( !expContentJobStore::validID( $id ) )
            return 3;
        $job = expContentJob::fetch( $id );
        if ( !$job )
            return 3;
        if ( !$this->takeRunLock( $id ) )
        {
            $this->say( "job $id: another worker is running it" );
            return 2;
        }
        try
        {
            return $this->runLocked( $id );
        }
        finally
        {
            $this->releaseRunLock();
        }
    }

    /**
     * The run, with the run lock held.
     *
     * @param string $id
     * @return int
     */
    protected function runLocked( $id )
    {
        $job = expContentJob::fetch( $id );
        if ( !$job )
            return 3;
        $state = $job->state();
        if ( $state !== expContentJob::STATE_QUEUED && $state !== expContentJob::STATE_RUNNING )
        {
            $this->say( "job $id is $state: nothing to run" );
            return 2;
        }
        if ( $job->cancelRequested() )
            return $this->cancelled( $job );

        // a worker that starts again and again without finishing a batch is not helped by one more start
        $batchAtStart = (int) $job->progress()['batch'];
        $lastBatch = $job->get( 'last_start_batch' );
        $stalls = ( $lastBatch !== null && (int) $lastBatch === $batchAtStart && $job->attempts() > 0 ) ? (int) $job->get( 'stalls' ) + 1 : 0;
        $maxAttempts = max( 1, (int) expContentJob::setting( 'MaxAttempts' ) );
        $job->set( 'attempts', $job->attempts() + 1 );
        $job->set( 'stalls', $stalls );
        $job->set( 'last_start_batch', $batchAtStart );
        if ( $stalls >= $maxAttempts )
        {
            $previous = $job->error();
            return $this->fail( $job, 'stopped after ' . $stalls . ' starts without a batch done (at batch ' . $batchAtStart . ')'
                                      . ( $previous !== '' ? ': ' . $previous : '' ) );
        }

        $now = time();
        if ( !$job->started() )
            $job->set( 'started', $now );
        $job->set( 'state', expContentJob::STATE_RUNNING );
        $job->set( 'heartbeat', $now );
        $job->set( 'error', '' );
        $job->set( 'worker', array( 'pid' => getmypid(), 'user' => self::processUser(), 'host' => php_uname( 'n' ) ) );
        $job->set( 'worker_pid', getmypid() );
        $job->set( 'error_node_id', 0 );
        $server = (array) $job->get( 'server' );
        $server['worker'] = expContentJob::serverInfo() + array( 'user' => self::processUser() );
        $job->set( 'server', $server );
        $job->save();
        $job->appendLog( 'worker started: pid ' . getmypid() . ', as ' . self::processUser() . ', attempt ' . $job->attempts()
                         . ( $batchAtStart ? ', resuming after batch ' . $batchAtStart : '' ) );

        try
        {
            $this->switchUser( $job );
            $this->auditStart( $job, $state, $batchAtStart );
            $handler = expContentJob::handler( $job->type() );
            $this->audited( function () use ( $handler, $job ) { $handler->prepare( $job ); } );
            $job->save();
        }
        catch ( Throwable $e )
        {
            return $this->fail( $job, $e->getMessage() );
        }

        $batchSize = max( 1, (int) expContentJob::setting( 'BatchSize' ) );
        $pause = max( 0, (int) expContentJob::setting( 'BatchPause' ) );
        while ( true )
        {
            // a cancel asked for meanwhile (the page, the command)
            $disk = expContentJobStore::read( $id );
            if ( $disk && !empty( $disk['cancel_requested'] ) )
            {
                $job->set( 'cancel_requested', true );
                return $this->cancelled( $job );
            }

            $t0 = microtime( true );
            if ( function_exists( 'memory_reset_peak_usage' ) )
                memory_reset_peak_usage();
            $this->begin();
            try
            {
                $r = $this->audited( function () use ( $handler, $job, $batchSize ) { return $handler->runBatch( $job, $batchSize ); } );
                if ( !$this->commit() )
                    throw new expContentJobException( 'the batch could not be committed (database error)' );
            }
            catch ( Throwable $e )
            {
                $this->rollback();
                $this->clearCaches();
                return $this->fail( $job, $e->getMessage(), $e instanceof expContentJobException ? $e->nodeID : 0 );
            }
            try
            {
                $this->audited( function () use ( $handler, $job ) { $handler->afterBatch( $job ); } );
            }
            catch ( Throwable $e )
            {
                // committed: the batch is done; the next run repairs the side files (idempotent batches)
                return $this->fail( $job, $e->getMessage() );
            }
            $seconds = microtime( true ) - $t0;
            $peak = memory_get_peak_usage();
            $p = $job->progress();
            $job->setProgress( array( 'done' => isset( $r['done_total'] ) ? (int) $r['done_total'] : $p['done'] + (int) $r['done'], 'batch' => $p['batch'] + 1,
                                      'message' => isset( $r['message'] ) ? (string) $r['message'] : '' ) );
            $job->set( 'heartbeat', time() );
            $job->set( 'stalls', 0 );
            $job->set( 'last_start_batch', null );
            $job->set( 'last_batch', array( 'seconds' => round( $seconds, 3 ), 'peak_mb' => round( $peak / 1048576, 2 ),
                                            'memory_mb' => round( memory_get_usage() / 1048576, 2 ), 'done' => (int) $r['done'] ) );
            // the last 200 batches, for the job page (average, slowest, time left, peak memory)
            $batches = (array) $job->get( 'batches' );
            $batches[] = array( 'n' => $p['batch'] + 1, 'done' => (int) $r['done'], 'seconds' => round( $seconds, 3 ),
                                'peak_mb' => round( $peak / 1048576, 2 ), 'at' => time() );
            $job->set( 'batches', array_slice( $batches, -200 ) );
            $job->save();
            $p = $job->progress();
            $line = sprintf( 'batch %d: %d done (%d/%d, %d%%) in %.2f s, peak %.1f MB, memory %.1f MB%s',
                             $p['batch'], $r['done'], $p['done'], $p['total'], $p['percent'], $seconds, $peak / 1048576,
                             memory_get_usage() / 1048576, $p['phase'] !== '' ? ', ' . $p['phase'] : '' );
            $job->appendLog( $line );
            $this->say( $line );
            $this->clearCaches();
            if ( !empty( $r['finished'] ) )
                break;
            if ( $pause > 0 )
                usleep( $pause * 1000 );
        }

        try
        {
            $this->audited( function () use ( $handler, $job ) { $handler->finish( $job ); } );
        }
        catch ( Throwable $e )
        {
            return $this->fail( $job, $e->getMessage() );
        }
        $job->set( 'state', expContentJob::STATE_DONE );
        $job->set( 'finished', time() );
        $job->set( 'heartbeat', time() );
        $job->save();
        expContentJobLock::release( $id );
        $this->auditEnd( $job, 'content.job.finish', 'success', array( 'nodes_done' => (int) $job->progress()['done'],
                                                                         'result' => $job->result() ) );
        $job->appendLog( 'done' . ( $job->progress()['message'] !== '' ? ': ' . $job->progress()['message'] : '' ) );
        $this->say( "job $id done" );
        return 0;
    }

    /**
     * Fails the job: state failed, the error kept, the locks kept (it is resumable).
     *
     * @param expContentJob $job
     * @param string $error
     * @param int $nodeID the node the error is about (error_node_id), 0 when none
     * @return int 1
     */
    protected function fail( expContentJob $job, $error, $nodeID = 0 )
    {
        $job->set( 'state', expContentJob::STATE_FAILED );
        $job->set( 'error', (string) $error );
        $job->set( 'error_node_id', (int) $nodeID );
        $job->set( 'finished', time() );
        $job->set( 'heartbeat', time() );
        $job->save();
        $job->appendLog( 'failed: ' . $error );
        $this->say( 'job ' . $job->id() . ' failed: ' . $error );
        $this->auditEnd( $job, 'content.job.fail', 'failed', array( 'error' => (string) $error, 'node_id' => (int) $nodeID ) );
        return 1;
    }

    /**
     * Ends a cancelled job and gives back its locks.
     *
     * @param expContentJob $job
     * @return int 0
     */
    protected function cancelled( expContentJob $job )
    {
        $job->set( 'state', expContentJob::STATE_CANCELLED );
        $job->set( 'finished', time() );
        $job->set( 'heartbeat', time() );
        $job->save();
        expContentJobLock::release( $job->id() );
        $what = $job->type() === 'copy' && !empty( $job->result()['new_root_node_id'] )
              ? ' (the partial copy is left in place: node ' . $job->result()['new_root_node_id'] . ')' : '';
        $job->appendLog( 'cancelled after batch ' . $job->progress()['batch'] . $what );
        $this->say( 'job ' . $job->id() . ' cancelled' );
        $this->auditEnd( $job, 'content.job.cancel', 'success', array( 'state' => expContentJob::STATE_CANCELLED,
                                                                         'after_batch' => (int) $job->progress()['batch'] ) );
        return 0;
    }

    // ---- the audit (doc/bc/6.0/audit.md, content.job.*) ---------------------------------------

    /**
     * Starts the run's audit record: the job id on every event of the run, content.job.start as the parent of
     * the events the batches raise. The actor is the job's user (switchUser() made it the current one), the
     * process's own user is the impersonator.
     */
    protected function auditStart( expContentJob $job, $state, $batchAtStart )
    {
        if ( !class_exists( 'expAuditHook' ) || !class_exists( 'expAudit' ) )
            return;
        try
        {
            expAudit::setJob( $job->id() );
            $this->auditStarted = microtime( true );
            $this->auditParent = expAuditHook::begin( 'content.job.start', array(
                'object' => self::auditJob( $job ),
                'actor' => $this->auditActor( $job ),
                'before' => array( 'state' => (string) $state ),
                'after' => array( 'state' => expContentJob::STATE_RUNNING, 'attempts' => $job->attempts(),
                                  'resumed_after_batch' => (int) $batchAtStart ) ) );
        }
        catch ( Throwable $e )
        {
            $this->auditParent = null;
        }
    }

    /**
     * Runs a step of the job type with the run's record as the parent of what it records.
     *
     * @param callable $code
     * @return mixed
     */
    protected function audited( $code )
    {
        if ( $this->auditParent === null || !class_exists( 'expAuditHook' ) )
            return call_user_func( $code );
        return expAuditHook::withParent( $this->auditParent, $code );
    }

    /**
     * Ends the run's record and records how the run ended (finish, fail or cancel), then flushes, so the trail
     * of a job is on disk when its state is.
     */
    protected function auditEnd( expContentJob $job, $name, $result, array $after )
    {
        if ( !class_exists( 'expAuditHook' ) || !class_exists( 'expAudit' ) )
            return;
        try
        {
            $after['ms'] = $this->auditStarted ? (int) round( ( microtime( true ) - $this->auditStarted ) * 1000 ) : null;
            if ( $this->auditParent !== null )
                expAuditHook::end( $this->auditParent );
            $this->auditParent = null;
            expAuditHook::emit( $name, array( 'object' => self::auditJob( $job ), 'actor' => $this->auditActor( $job ),
                                              'result' => $result, 'reason' => $result === 'failed' ? 'error' : ( $name === 'content.job.cancel' ? 'cancelled' : null ),
                                              'before' => array( 'state' => expContentJob::STATE_RUNNING ),
                                              'after' => $after ) );
            expAudit::flush();
            expAudit::setJob( null );
        }
        catch ( Throwable $e )
        {
        }
    }

    /** @return array the job as an audit object: id, type, root node */
    public static function auditJob( expContentJob $job )
    {
        $p = $job->params();
        $root = null;
        foreach ( array( 'node_id', 'source_node_id', 'target_node_id' ) as $key )
        {
            if ( isset( $p[$key] ) && is_numeric( $p[$key] ) )
            {
                $root = (int) $p[$key];
                break;
            }
        }
        if ( $root === null && isset( $p['roots'][0]['node_id'] ) )
            $root = (int) $p['roots'][0]['node_id'];
        return array( 'type' => 'job', 'id' => $job->id(), 'job_type' => $job->type(), 'node' => $root );
    }

    /** @return array the job's user as the actor, the process's user as the impersonator */
    protected function auditActor( expContentJob $job )
    {
        $user = eZUser::fetch( $job->userID() );
        return array( 'user_id' => $job->userID(), 'login' => $user ? (string) $user->attribute( 'login' ) : null,
                      'impersonator' => array( 'user_id' => null, 'login' => null, 'os_user' => self::processUser() ) );
    }

    // ---- what tests replace ------------------------------------------------------------------

    /** Runs the job as the user who requested it. */
    protected function switchUser( expContentJob $job )
    {
        $user = eZUser::fetch( $job->userID() );
        if ( !$user instanceof eZUser )
            throw new expContentJobException( 'the user ' . $job->userID() . ' who requested the job does not exist any more' );
        eZUser::setCurrentlyLoggedInUser( $user, $job->userID(), eZUser::NO_SESSION_REGENERATE );
        // every language counts, whatever the siteaccess lists: a job works on all translations of its nodes, as the
        // cronjobs do (a node only in a language the siteaccess does not list would otherwise not be found)
        eZContentLanguage::setCronjobMode( true );
        // a database error inside a batch throws, so the batch is rolled back and the job fails cleanly
        eZDB::setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
    }

    protected function begin()
    {
        eZDB::instance()->begin();
    }

    /** @return bool */
    protected function commit()
    {
        return eZDB::instance()->commit() !== false;
    }

    protected function rollback()
    {
        $db = eZDB::instance();
        // nested begins of the kernel left open by an exception: back to the outermost level
        $guard = 0;
        while ( $db->transactionCounter() > 0 && $guard++ < 100 )
            $db->rollback();
    }

    /** Clears the in-memory caches between batches, so memory stays flat. */
    protected function clearCaches()
    {
        eZContentObject::clearCache();
        unset( $GLOBALS['eZContentClassObjectCache'], $GLOBALS['eZContentObjectTreeNodeCache'] );
        if ( class_exists( 'eZContentCacheManager', false ) )
        {
            $prop = new ReflectionProperty( 'eZContentCacheManager', 'additionalNodeIDsPerObject' );
            $prop->setAccessible( true );
            $prop->setValue( null, array() );
        }
        if ( class_exists( 'eZDebug', false ) )
            eZDebug::instance()->reset();
        gc_collect_cycles();
    }

    // ---- helpers -----------------------------------------------------------------------------

    protected function takeRunLock( $id )
    {
        $file = expContentJobStore::path( $id, '.run.lock' );
        $fh = @fopen( $file, 'c' );
        if ( !$fh )
            return false;
        expContentJobStore::fixOwner( $file );
        if ( !flock( $fh, LOCK_EX | LOCK_NB ) )
        {
            fclose( $fh );
            return false;
        }
        $this->runLock = $fh;
        return true;
    }

    protected function releaseRunLock()
    {
        if ( $this->runLock )
        {
            flock( $this->runLock, LOCK_UN );
            fclose( $this->runLock );
            $this->runLock = null;
        }
    }

    protected function say( $line )
    {
        if ( $this->output )
            call_user_func( $this->output, $line );
    }

    /** @return string the name of the process's user */
    public static function processUser()
    {
        if ( function_exists( 'posix_geteuid' ) )
        {
            $pw = posix_getpwuid( posix_geteuid() );
            if ( $pw )
                return $pw['name'];
            return (string) posix_geteuid();
        }
        return (string) get_current_user();
    }
}
