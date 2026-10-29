<?php
/**
 * File containing the expContentJobTransfer class.
 *
 * The content job 'transfer': one background run of an import or export tool (Export, CIE ...) when the
 * installation has no job queue (expKernelTransferRunner, doc/eng-us/features/6.0/transfer-runs.md). The job holds
 * what expKernelTransferRunner::submit() handed over: the tool (its registry id), the task class (implementing
 * expKernelTransferTask), its parameters, the run's history row and a title. The content jobs page
 * (content/jobs) lists it with the others; the tool's own pages show its progress as before.
 *
 * A transfer run is one piece of work, not batches of content changes over nodes: the task runs in prepare(), which
 * the worker calls outside its batch transaction, so a long export holds no transaction open, its history row is
 * visible while it runs, and content an import creates is committed by the import itself. The single batch then
 * only reports the outcome. A job resumed after a crash runs the task again (the task continues or starts over).
 * A cancel asked for while it runs reaches the task through expKernelTransferContext::isCancelled(); the worker ends
 * the job as cancelled before its batch.
 *
 * Parameters: tool, task, params, run_id, title. The result: the task's result (rows_done, bytes, file_ref ...).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentJobTransfer implements expContentJobType
{
    /** @return string */
    protected static function tr( $text, array $arguments = array() )
    {
        return ezpI18n::tr( 'kernel/transfer', $text, null, $arguments );
    }

    public function validate( array $params, eZUser $user )
    {
        $problem = expKernelTransferRunner::payloadProblem( $params );
        if ( $problem !== '' )
            throw new expContentJobException( self::tr( 'The transfer run cannot be started: %1.', array( '%1' => $problem ) ) );
        $tool = expKernelTransferRegistry::instance()->tool( (string)$params['tool'] );
        if ( !$tool )
            throw new expContentJobException( self::tr( 'The tool %1 is not declared in the transfer registry.', array( '%1' => (string)$params['tool'] ) ) );
        $access = function ( $module, $function ) use ( $user ) {
            $result = $user->hasAccessTo( $module, $function === false ? false : (string)$function );
            return isset( $result['accessWord'] ) && $result['accessWord'] !== 'no';
        };
        if ( !$tool->isAllowed( $access ) )
            throw new expContentJobException( self::tr( 'You may not use %1.', array( '%1' => (string)$tool->get( 'name' ) ) ) );
        return array( 'tool' => (string)$params['tool'], 'task' => (string)$params['task'],
                      'params' => isset( $params['params'] ) ? (array)$params['params'] : array(),
                      'run_id' => isset( $params['run_id'] ) ? (int)$params['run_id'] : 0,
                      'title' => isset( $params['title'] ) ? mb_substr( trim( (string)$params['title'] ), 0, 200 ) : '',
                      'tool_name' => (string)$tool->get( 'name' ) );
    }

    public function countNodes( array $params )
    {
        return isset( $params['params']['rows_total'] ) ? (int)$params['params']['rows_total'] : 0;
    }

    public function locks( array $params )
    {
        // the tool keeps its own state; a transfer run takes no subtree lock
        return array();
    }

    public function describe( array $params )
    {
        $name = isset( $params['tool_name'] ) && $params['tool_name'] !== '' ? $params['tool_name'] : $params['tool'];
        return isset( $params['title'] ) && $params['title'] !== ''
            ? self::tr( '%1: %2', array( '%1' => $name, '%2' => $params['title'] ) )
            : self::tr( 'A run of %1', array( '%1' => $name ) );
    }

    public function prepare( expContentJob $job )
    {
        $cp =& $job->checkpoint();
        if ( !empty( $cp['done'] ) )
            return;
        $id = $job->id();
        $last = 0;
        $hooks = array(
            'progress' => function ( $done, $total, $message ) use ( $job, &$last ) {
                if ( time() - $last < 1 )
                    return;
                $last = time();
                $progress = array( 'done' => (int)$done, 'phase' => 'transfer', 'message' => (string)$message );
                if ( $total !== null )
                    $progress['total'] = (int)$total;
                $job->setProgress( $progress );
                $job->set( 'heartbeat', time() );
                $job->save();
            },
            'heartbeat' => function () use ( $job ) {
                $job->set( 'heartbeat', time() );
                $job->save();
            },
            'cancelled' => function () use ( $id ) {
                $disk = expContentJobStore::read( $id );
                return $disk && !empty( $disk['cancel_requested'] );
            },
            'log' => function ( $line ) use ( $job ) {
                $job->appendLog( (string)$line );
            },
        );
        try
        {
            $result = expKernelTransferRunner::runPayload( $job->params(), 'contentjob', $id, $hooks );
        }
        catch ( expKernelTransferCancelledException $e )
        {
            // the worker sees cancel_requested before its batch and ends the job as cancelled
            $cp['cancelled'] = true;
            return;
        }
        foreach ( $result as $key => $value )
            if ( is_scalar( $value ) )
                $job->setResult( (string)$key, $value );
        $cp['done'] = true;
        $cp['rows_done'] = isset( $result['rows_done'] ) ? (int)$result['rows_done'] : 0;
    }

    public function runBatch( expContentJob $job, $batchSize )
    {
        $cp = $job->checkpoint();
        $done = isset( $cp['rows_done'] ) ? (int)$cp['rows_done'] : 0;
        return array( 'done' => 0, 'finished' => true, 'done_total' => $done,
                      'message' => self::tr( '%1 done', array( '%1' => $done ) ) );
    }

    public function afterBatch( expContentJob $job )
    {
    }

    public function finish( expContentJob $job )
    {
    }
}
