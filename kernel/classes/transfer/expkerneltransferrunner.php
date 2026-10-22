<?php
/**
 * File containing the expKernelTransferRunner class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * One way for every import and export tool to run its work in the background (doc/eng-us/features/6.0/transfer-runs.md),
 * on whichever engine the installation has:
 *
 *   - expqueue, the job queue, when it is active, its tables are installed, the tool declares its job type in
 *     expqueue.ini ([JobType_<type>] Handler=expKernelTransferQueueHandler, named by the tool's QueueJobType=) and a
 *     queue worker is alive (transfer.ini [TransferSettings] QueueNeedsWorker);
 *   - otherwise the kernel's content jobs, which every installation has (content.ini [ContentJobSettings]
 *     JobTypes[transfer]=expContentJobTransfer, a worker process started for the job);
 *   - otherwise "process": submit() says so and the tool runs its work as it did before (its own process).
 *
 *   $run = expKernelTransferRunner::submit( 'transfer.bccie', 'bccieTransferTask', array( 'job' => $jobID ), array(
 *       'title' => 'Form 42 as CSV', 'history' => array( 'direction' => 'export', 'scope' => 'data', 'tool_ref' => $jobID ) ) );
 *   // $run: engine (expqueue, contentjob, process), job_ref, run_id, error
 *   if ( $run['engine'] === 'process' )
 *       ...start it the old way, which may call expKernelTransferRunner::execute() itself...
 *
 * The engine is chosen per tool: transfer.ini [TransferSettings] ToolEngine[<tool id>], else the tool's Engine= in
 * its registry block, else [TransferSettings] Engine; auto (the default) takes the first available of expqueue,
 * contentjob, process; a named engine that is not available falls back the same way. Whatever runs it, the task
 * (expKernelTransferTask) is called through execute(), which keeps the run's history row (expKernelTransferHistory):
 * queued when submitted, running, then done, failed or cancelled with the task's counts.
 *
 * @since 6.0.15
 * @package kernel
 */
class expKernelTransferRunner
{
    /** The engines, in the order auto takes them */
    const ENGINES = array( 'expqueue', 'contentjob', 'process' );

    /** The content job type of transfer runs */
    const CONTENT_JOB_TYPE = 'transfer';

    /** @var array|null test overrides of the engine probes: engine => bool */
    protected static $probes = null;

    /**
     * A setting of transfer.ini [TransferSettings].
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function setting( $name, $default = null )
    {
        try
        {
            $ini = eZINI::instance( 'transfer.ini' );
            return $ini->hasVariable( 'TransferSettings', $name ) ? $ini->variable( 'TransferSettings', $name ) : $default;
        }
        catch ( Throwable $e )
        {
            return $default;
        }
    }

    /**
     * The engine asked for a tool: ToolEngine[<tool id>], the tool's Engine=, [TransferSettings] Engine.
     *
     * @param string $toolID
     * @return string auto, expqueue, contentjob or process
     */
    public static function preferredEngine( $toolID )
    {
        $toolID = expKernelTransferHistory::toolID( $toolID );
        $perTool = (array)self::setting( 'ToolEngine', array() );
        $candidates = array( isset( $perTool[$toolID] ) ? $perTool[$toolID] : '' );
        $tool = expKernelTransferRegistry::instance()->tool( $toolID );
        if ( $tool )
            $candidates[] = $tool->get( 'engine' );
        $candidates[] = self::setting( 'Engine', 'auto' );
        foreach ( $candidates as $candidate )
        {
            $candidate = strtolower( trim( (string)$candidate ) );
            if ( $candidate !== '' && $candidate !== 'auto' && in_array( $candidate, self::ENGINES, true ) )
                return $candidate;
        }
        return 'auto';
    }

    /**
     * The engine that runs (pure): the preferred one when it is available, else the first available in ENGINES
     * order; process is always available.
     *
     * @param string $preferred auto or an engine
     * @param array $available engine => bool
     * @return string
     */
    public static function choose( $preferred, array $available )
    {
        $available['process'] = true;
        if ( $preferred !== 'auto' && !empty( $available[$preferred] ) )
            return $preferred;
        foreach ( self::ENGINES as $engine )
            if ( !empty( $available[$engine] ) )
                return $engine;
        return 'process';
    }

    /**
     * The expqueue job type of a tool's runs (its QueueJobType=), '' when it declares none.
     *
     * @param string $toolID
     * @return string
     */
    public static function queueJobType( $toolID )
    {
        $tool = expKernelTransferRegistry::instance()->tool( expKernelTransferHistory::toolID( $toolID ) );
        return $tool ? (string)$tool->get( 'queue_job_type' ) : '';
    }

    /**
     * Which engines can take a tool's runs here, with the reason when one cannot.
     *
     * @param string $toolID
     * @return array engine => array( available => bool, reason => string )
     */
    public static function engines( $toolID )
    {
        $out = array();
        $out['expqueue'] = self::probe( 'expqueue', function () use ( $toolID ) { return self::queueProbe( self::queueJobType( $toolID ) ); } );
        $out['contentjob'] = self::probe( 'contentjob', function () {
            if ( !class_exists( 'expContentJob' ) )
                return 'the content jobs are not available';
            if ( !isset( expContentJob::types()[self::CONTENT_JOB_TYPE] ) )
                return 'content.ini [ContentJobSettings] has no JobTypes[transfer]';
            return '';
        } );
        $out['process'] = array( 'available' => true, 'reason' => '' );
        return $out;
    }

    /**
     * The engine a tool's next run goes to.
     *
     * @param string $toolID
     * @return string expqueue, contentjob or process
     */
    public static function engine( $toolID )
    {
        $available = array();
        foreach ( self::engines( $toolID ) as $engine => $state )
            $available[$engine] = $state['available'];
        return self::choose( self::preferredEngine( $toolID ), $available );
    }

    /**
     * Hands a run to the engine: the history row (queued), then the engine's job. With engine process nothing is
     * started: the tool runs it its own way.
     *
     * @param string $toolID the registry id
     * @param string $taskClass a class implementing expKernelTransferTask
     * @param array $params the task's parameters (JSON values, no secret)
     * @param array $options title, user (eZUser, the current user by default), run_id (a history row the tool
     *                       started itself), history (columns for the new row: direction, kind, scope, scope_ref,
     *                       format, tool_ref, detail_url, rows_total ...), engine (instead of the chosen one),
     *                       queue (expqueue options: class, priority, resource, idempotency_key ...)
     * @return array engine, job_ref, run_id, error ('' or why the chosen engine refused, then engine is process)
     */
    public static function submit( $toolID, $taskClass, array $params = array(), array $options = array() )
    {
        $toolID = expKernelTransferHistory::toolID( $toolID );
        self::task( $taskClass );
        $user = isset( $options['user'] ) && $options['user'] instanceof eZUser ? $options['user'] : eZUser::currentUser();
        $engine = isset( $options['engine'] ) && in_array( $options['engine'], self::ENGINES, true ) ? $options['engine'] : self::engine( $toolID );
        $title = isset( $options['title'] ) ? (string)$options['title'] : '';
        $runID = isset( $options['run_id'] ) ? (int)$options['run_id'] : 0;
        if ( $runID <= 0 )
            $runID = expKernelTransferHistory::start( $toolID, ( isset( $options['history'] ) ? (array)$options['history'] : array() ) + array(
                'title' => $title, 'state' => expKernelTransferRun::STATE_QUEUED, 'engine' => $engine,
                'user_id' => (int)$user->attribute( 'contentobject_id' ) ) );
        $payload = array( 'tool' => $toolID, 'task' => (string)$taskClass, 'params' => $params, 'run_id' => $runID, 'title' => $title );
        $error = '';
        $jobRef = '';
        if ( $engine === 'expqueue' )
        {
            try
            {
                $queue = isset( $options['queue'] ) ? (array)$options['queue'] : array();
                $job = expQueue::enqueue( self::queueJobType( $toolID ), $payload, $queue + array( 'user_id' => (int)$user->attribute( 'contentobject_id' ) ) );
                $jobRef = (string)$job->attribute( 'uuid' );
            }
            catch ( Throwable $e )
            {
                $error = $e->getMessage();
                $engine = isset( self::engines( $toolID )['contentjob'] ) && self::engines( $toolID )['contentjob']['available'] ? 'contentjob' : 'process';
            }
        }
        if ( $engine === 'contentjob' )
        {
            try
            {
                $job = expContentJob::create( self::CONTENT_JOB_TYPE, $payload + array( 'mode' => 'job' ), $user );
                $jobRef = (string)$job->id();
                $job->spawn();
            }
            catch ( Throwable $e )
            {
                $error = $e->getMessage();
                $engine = 'process';
                $jobRef = '';
            }
        }
        if ( $runID > 0 )
            expKernelTransferHistory::update( $runID, array( 'engine' => $engine, 'job_ref' => $jobRef ) + ( $error !== '' ? array( 'detail' => array( 'engine_error' => $error ) ) : array() ) );
        if ( $error !== '' )
            eZDebug::writeWarning( "transfer run of $toolID: $error (now $engine)", __METHOD__ );
        return array( 'engine' => $engine, 'job_ref' => $jobRef, 'run_id' => $runID, 'error' => $error );
    }

    /**
     * Runs a task now, in this process, and keeps its history row: running, then done, failed or cancelled with
     * what the task returned. What every engine calls; a tool's own process (engine process) may call it too.
     *
     * @param string $taskClass
     * @param array $params
     * @param expKernelTransferContext $context
     * @return array the task's result
     * @throws expKernelTransferCancelledException when the task stopped because of a cancel
     * @throws Exception when it failed (the history row is failed with the message first)
     */
    public static function execute( $taskClass, array $params, expKernelTransferContext $context )
    {
        $runID = $context->runID();
        try
        {
            $task = self::task( $taskClass );
        }
        catch ( Throwable $e )
        {
            if ( $runID > 0 )
                expKernelTransferHistory::finish( $runID, expKernelTransferRun::STATE_FAILED, array( 'error' => $e->getMessage() ) );
            throw $e;
        }
        if ( $runID > 0 )
            expKernelTransferHistory::update( $runID, array( 'state' => expKernelTransferRun::STATE_RUNNING, 'engine' => $context->engine(),
                                                             'job_ref' => $context->jobRef() ) );
        try
        {
            $result = $task->run( $params, $context );
        }
        catch ( expKernelTransferCancelledException $e )
        {
            if ( $runID > 0 )
                expKernelTransferHistory::finish( $runID, expKernelTransferRun::STATE_CANCELLED, self::progressColumns( $context ) );
            throw $e;
        }
        catch ( Throwable $e )
        {
            if ( $runID > 0 )
                expKernelTransferHistory::finish( $runID, expKernelTransferRun::STATE_FAILED,
                                                  array( 'error' => $e->getMessage() ) + self::progressColumns( $context ) );
            throw $e;
        }
        $result = is_array( $result ) ? $result : array();
        if ( $runID > 0 )
        {
            $state = isset( $result['state'] ) && $result['state'] === expKernelTransferRun::STATE_FAILED ? expKernelTransferRun::STATE_FAILED : expKernelTransferRun::STATE_DONE;
            $columns = $result;
            unset( $columns['state'], $columns['tool'], $columns['user_id'], $columns['id'] );
            expKernelTransferHistory::finish( $runID, $state, $columns + self::progressColumns( $context ) );
        }
        return $result;
    }

    /**
     * What an engine's worker calls with a job's payload: the context with the engine's callables, then execute().
     *
     * @param array $payload tool, task, params, run_id (as submit() made it)
     * @param string $engine
     * @param string $jobRef
     * @param array $hooks progress, heartbeat, cancelled, log
     * @return array the task's result
     */
    public static function runPayload( array $payload, $engine, $jobRef, array $hooks = array() )
    {
        $problem = self::payloadProblem( $payload );
        if ( $problem !== '' )
            throw new RuntimeException( $problem );
        $context = new expKernelTransferContext( $engine, (string)$payload['tool'], isset( $payload['run_id'] ) ? (int)$payload['run_id'] : 0, $jobRef, $hooks );
        return self::execute( (string)$payload['task'], isset( $payload['params'] ) ? (array)$payload['params'] : array(), $context );
    }

    /**
     * What is wrong with a payload (pure apart from the class check), '' when nothing.
     *
     * @param array $payload
     * @return string
     */
    public static function payloadProblem( array $payload )
    {
        if ( !isset( $payload['tool'] ) || !preg_match( '/^transfer\.[A-Za-z0-9_.-]+$/', (string)$payload['tool'] ) )
            return 'the payload names no tool (transfer.<id>)';
        if ( !isset( $payload['task'] ) || !is_string( $payload['task'] ) || !preg_match( '/^[A-Za-z_][A-Za-z0-9_]*$/', $payload['task'] ) )
            return 'the payload names no task class';
        if ( !class_exists( $payload['task'] ) || !is_subclass_of( $payload['task'], 'expKernelTransferTask' ) )
            return 'the class ' . $payload['task'] . ' does not implement expKernelTransferTask';
        if ( isset( $payload['params'] ) && !is_array( $payload['params'] ) )
            return 'the parameters are not a list';
        return '';
    }

    /**
     * Cancels a run through its engine (queued: at once; running: the task stops at its next throwIfCancelled()).
     *
     * @param string $engine
     * @param string $jobRef
     * @return bool whether the engine took the cancel
     */
    public static function cancel( $engine, $jobRef )
    {
        try
        {
            if ( $engine === 'expqueue' && class_exists( 'expQueue' ) )
                return expQueue::cancel( (string)$jobRef, 'cancelled by the tool' ) !== null;
            if ( $engine === 'contentjob' && class_exists( 'expContentJob' ) )
            {
                $job = expContentJob::fetch( (string)$jobRef );
                if ( $job && $job->canCancel() )
                {
                    $job->cancel();
                    return true;
                }
            }
        }
        catch ( Throwable $e )
        {
            eZDebug::writeWarning( "cancel of $engine job $jobRef: " . $e->getMessage(), __METHOD__ );
        }
        return false;
    }

    /**
     * The state of a run's job in its engine: queued, running, done, failed, cancelled, or '' when unknown.
     *
     * @param string $engine
     * @param string $jobRef
     * @return string
     */
    public static function jobState( $engine, $jobRef )
    {
        try
        {
            if ( $engine === 'expqueue' && class_exists( 'expQueue' ) )
            {
                $job = expQueue::fetch( (string)$jobRef );
                if ( !$job )
                    return '';
                $state = (string)$job->attribute( 'state' );
                if ( in_array( $state, array( 'done', 'running', 'cancelled' ), true ) )
                    return $state;
                return $state === 'dead' ? 'failed' : 'queued';
            }
            if ( $engine === 'contentjob' && class_exists( 'expContentJob' ) )
            {
                $job = expContentJob::fetch( (string)$jobRef );
                return $job ? (string)$job->state() : '';
            }
        }
        catch ( Throwable $e )
        {
        }
        return '';
    }

    /**
     * Replaces the engine probes (tests): engine => bool; null for the real ones.
     *
     * @param array|null $probes
     */
    public static function setProbes( ?array $probes )
    {
        self::$probes = $probes;
    }

    // ---- helpers ---------------------------------------------------------------------------------------------------

    /**
     * @param string $taskClass
     * @return expKernelTransferTask
     */
    protected static function task( $taskClass )
    {
        if ( !is_string( $taskClass ) || !class_exists( $taskClass ) || !is_subclass_of( $taskClass, 'expKernelTransferTask' ) )
            throw new RuntimeException( 'The class ' . ( is_string( $taskClass ) ? $taskClass : '?' ) . ' does not implement expKernelTransferTask.' );
        return new $taskClass();
    }

    /** @return array the last progress of a context, as history columns */
    protected static function progressColumns( expKernelTransferContext $context )
    {
        $last = $context->lastProgress();
        $columns = array();
        if ( $last['done'] > 0 )
            $columns['rows_done'] = $last['done'];
        if ( $last['total'] !== null )
            $columns['rows_total'] = $last['total'];
        if ( $last['failed'] !== null )
            $columns['rows_failed'] = $last['failed'];
        return $columns;
    }

    /**
     * @param string $engine
     * @param callable $check returns '' when the engine is available, else why not
     * @return array available, reason
     */
    protected static function probe( $engine, $check )
    {
        if ( is_array( self::$probes ) && array_key_exists( $engine, self::$probes ) )
            return array( 'available' => (bool)self::$probes[$engine], 'reason' => self::$probes[$engine] ? '' : 'switched off for the test' );
        try
        {
            $reason = (string)$check();
        }
        catch ( Throwable $e )
        {
            $reason = $e->getMessage();
        }
        return array( 'available' => $reason === '', 'reason' => $reason );
    }

    /**
     * Why expqueue cannot take a job type, '' when it can.
     *
     * @param string $type
     * @return string
     */
    protected static function queueProbe( $type )
    {
        if ( !class_exists( 'expQueue' ) || !class_exists( 'expQueueConfig' ) )
            return 'expqueue is not active';
        if ( $type === '' )
            return 'the tool declares no QueueJobType';
        if ( !expQueue::installed() )
            return 'the tables of expqueue are not installed';
        $def = expQueueConfig::instance()->jobType( $type );
        if ( !$def )
            return "expqueue.ini has no [JobType_$type]";
        if ( !class_exists( $def['handler'] ) )
            return 'the handler ' . $def['handler'] . ' of ' . $type . ' does not exist';
        $needsWorker = strtolower( (string)self::setting( 'QueueNeedsWorker', 'enabled' ) ) !== 'disabled';
        if ( $needsWorker && class_exists( 'expQueueWorker' ) && !expQueueWorker::live() )
            return 'no queue worker is running';
        return '';
    }
}
