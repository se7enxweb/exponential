<?php
/**
 * File containing the expKernelTransferQueueHandler class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The expqueue handler of transfer runs (expKernelTransferRunner, doc/eng-us/features/6.0/transfer-runs.md). A tool
 * declares its own job type with it, so the queue's dashboard lists its runs by name:
 *
 *   expqueue.ini.append.php of the tool:
 *   [JobType_xrowextract.export]
 *   Handler=expKernelTransferQueueHandler
 *   Class=bulk
 *   Timeout=3600
 *   Description=An export of Export (xrowextract)
 *
 *   kernelservices.ini of the tool, in its [Service_transfer.<id>] block:
 *   QueueJobType=xrowextract.export
 *
 * The worker calls run() as the user who queued the job, outside any transaction; the handler hands the payload to
 * expKernelTransferRunner::runPayload() with the queue context's progress, heartbeat and cancel. A cancelled task ends
 * the job as cancelled; any other failure is permanent (an export is not repeated by itself: the run's history row
 * says why it failed, and the tool's page can start it again).
 *
 * Declared only when expqueue is active: without it this file defines nothing, and nothing asks for the class.
 *
 * @since 6.0.15
 * @package kernel
 */
if ( interface_exists( 'expQueueHandler' ) && class_exists( 'expQueueHandlerBase' ) )
{
    // @phpstan-ignore-next-line expqueue is an extension: its classes exist only where it is active
    class expKernelTransferQueueHandler extends expQueueHandlerBase
    {
        public function validate( array $payload )
        {
            $problem = expKernelTransferRunner::payloadProblem( $payload );
            return $problem === '' ? null : $problem;
        }

        public function describe( expQueueJob $job )
        {
            $payload = $job->payloadArray();
            $title = isset( $payload['title'] ) ? (string)$payload['title'] : '';
            return $title !== '' ? $title : ( isset( $payload['tool'] ) ? (string)$payload['tool'] : '' );
        }

        public function run( expQueueJob $job, expQueueContext $context )
        {
            $hooks = array(
                'progress' => function ( $done, $total, $message ) use ( $context ) { $context->progress( $done, $total, $message ); },
                'heartbeat' => function () use ( $context ) { $context->heartbeat(); },
                'cancelled' => function () use ( $context ) { return $context->isCancelled(); },
            );
            try
            {
                return expKernelTransferRunner::runPayload( $job->payloadArray(), 'expqueue', (string)$job->attribute( 'uuid' ), $hooks );
            }
            catch ( expKernelTransferCancelledException $e )
            {
                throw new expQueueCancelledException( $e->getMessage(), 0, $e );
            }
            catch ( expQueueException $e )
            {
                throw $e;
            }
            catch ( Throwable $e )
            {
                throw new expQueuePermanentException( $e->getMessage(), 'transfer', $e );
            }
        }
    }
}
