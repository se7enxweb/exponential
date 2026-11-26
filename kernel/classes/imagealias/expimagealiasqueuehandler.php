<?php
/**
 * File containing the expImageAliasQueueHandler class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The expqueue handler of background work on image aliases (expImageAliasJob), for the job types the kernel
 * registers in settings/expqueue.ini:
 *
 *   image.alias.generate  the aliases (and format variants) of a list of image attributes; payload
 *                         expImageAliasJob::generatePayload()
 *   image.alias.bulk      generate, regenerate or purge over a scope; payload expImageAliasJob::bulkPayload()
 *   image.alias.resync    the resync of a staging site; payload expImageAliasJob::resyncPayload()
 *
 * Progress goes to the queue job (one step per image) and the history to imagealias.log. A cancel in Setup > Job
 * queue stops a run between two images. A bulk run that finds bulk work paused (expImageAliasThrottle::pauseBulk())
 * stores where it is (the job's checkpoint) and is deferred; it goes on from there after the pause. Aliases that could
 * not be made because no conversion slot came free in time are tried again later (the job is retried; what was made
 * is not made again). A resync whose source is paused is tried again after the pause.
 *
 * Declared only when expqueue is active: without it this file defines nothing, and nothing asks for the class.
 *
 * @since 6.0.15
 * @package kernel
 */
if ( interface_exists( 'expQueueHandler' ) && class_exists( 'expQueueHandlerBase' ) )
{
    class expImageAliasQueueHandler extends expQueueHandlerBase
    {
        public function validate( array $payload )
        {
            // The type is not known here: a payload that fits one of the three is taken, run() checks it again
            if ( expImageAliasJob::generatePayloadError( $payload ) === null )
                return null;
            if ( isset( $payload['action'] ) && $payload == expImageAliasJob::bulkPayload( $payload ) )
                return null;
            if ( !isset( $payload['action'] ) && !isset( $payload['items'] ) && $payload == expImageAliasJob::resyncPayload( $payload ) )
                return null;
            return 'the payload has keys or values an image alias job does not take';
        }

        public function describe( expQueueJob $job )
        {
            $payload = $job->payloadArray();
            $run = isset( $payload['run'] ) ? (string)$payload['run'] : '';
            switch ( $job->attribute( 'type' ) )
            {
                case expImageAliasJob::TYPE_GENERATE:
                    $aliases = array();
                    foreach ( isset( $payload['aliases'] ) ? (array)$payload['aliases'] : array() as $names )
                        $aliases = array_merge( $aliases, (array)$names );
                    return 'image aliases ' . ( isset( $payload['origin'] ) ? $payload['origin'] . ' ' : '' ) . $run . ' ('
                         . count( isset( $payload['items'] ) ? (array)$payload['items'] : array() ) . ' image(s), '
                         . implode( ',', array_slice( $aliases, 0, 6 ) ) . ( count( $aliases ) > 6 ? ', +' . ( count( $aliases ) - 6 ) : '' ) . ')';
                case expImageAliasJob::TYPE_BULK:
                    return 'image aliases ' . ( isset( $payload['action'] ) ? $payload['action'] : 'generate' ) . ' ' . $run . ' ('
                         . expImageAliasStager::describeScope( $payload ) . ')';
            }
            return 'image resync ' . $run . ' (' . expImageAliasStager::describeScope( $payload ) . ')';
        }

        public function run( expQueueJob $job, expQueueContext $context )
        {
            $progress = function ( array $progress ) use ( $context ) {
                $context->progress( $progress['done'], $progress['total'], $progress['path'] );
                $context->throwIfCancelled();
            };
            switch ( $job->attribute( 'type' ) )
            {
                case expImageAliasJob::TYPE_GENERATE:
                    return $this->runGenerate( $job, $progress );
                case expImageAliasJob::TYPE_BULK:
                    return $this->runBulk( $job, $context, $progress );
                case expImageAliasJob::TYPE_RESYNC:
                    return $this->runResync( $job, $progress );
            }
            throw new expQueuePermanentException( 'not an image alias job type: ' . $job->attribute( 'type' ), 'unknown_type' );
        }

        protected function runGenerate( expQueueJob $job, $progress )
        {
            $payload = $job->payloadArray();
            $error = expImageAliasJob::generatePayloadError( $payload );
            if ( $error !== null )
                throw new expQueuePermanentException( $error, 'invalid_payload' );
            $summary = expImageAliasJob::runGenerate( $payload, $progress );
            // Another process held the alias, or no conversion slot came free: later (what is made stays made)
            if ( $summary['counts']['timeout'] > 0 )
                throw new expQueueRetryableException( $summary['counts']['timeout'] . ' alias(es) not made in time (busy)', 30, 'busy' );
            return $summary;
        }

        protected function runBulk( expQueueJob $job, expQueueContext $context, $progress )
        {
            $payload = $job->payloadArray();
            if ( $payload != expImageAliasJob::bulkPayload( $payload ) )
                throw new expQueuePermanentException( 'the payload has keys or values a bulk run does not take', 'invalid_payload' );
            // Paused: asked again later, without reading the scope and without a run in the log
            if ( expImageAliasThrottle::isBulkPaused() )
                return expQueueResult::defer( expImageAliasJob::PAUSE_RECHECK, 'image alias bulk runs are paused' );
            // Goes on where an earlier attempt stopped (a pause, a crash)
            $checkpoint = $context->checkpoint();
            if ( is_array( $checkpoint ) && isset( $checkpoint['after'] ) && is_array( $checkpoint['after'] ) )
                $payload['after'] = $checkpoint['after'];
            $saved = microtime( true );
            $summary = expImageAliasJob::runBulk( $payload, function ( array $step ) use ( $context, $progress, &$saved ) {
                // where it is, at most once a minute (a crash goes on from about there)
                if ( microtime( true ) - $saved >= 60 && $step['after'] !== null )
                {
                    $context->saveCheckpoint( array( 'after' => $step['after'] ) );
                    $saved = microtime( true );
                }
                call_user_func( $progress, $step );
            }, function ( $after ) use ( $context ) {
                if ( $after !== null )
                    $context->saveCheckpoint( array( 'after' => $after ) );
                return true;
            } );
            if ( $summary['result'] === 'paused' )
                return expQueueResult::defer( expImageAliasJob::PAUSE_RECHECK, 'image alias bulk runs are paused' );
            if ( $summary['result'] === 'failed' )
            {
                if ( $summary['after'] !== null )
                    $context->saveCheckpoint( array( 'after' => $summary['after'] ) );
                throw new expQueueRetryableException( 'the bulk run failed (see the debug log)', 60, 'failed' );
            }
            if ( $summary['counts']['timeout'] > 0 )
                $summary['note'] = $summary['counts']['timeout'] . ' alias(es) were busy and are made on demand';
            return $summary;
        }

        protected function runResync( expQueueJob $job, $progress )
        {
            $payload = $job->payloadArray();
            if ( $payload != expImageAliasJob::resyncPayload( $payload ) )
                throw new expQueuePermanentException( 'the payload has keys or values a resync does not take', 'invalid_payload' );
            $summary = expImageAliasJob::runResync( $payload, $progress );
            if ( $summary['result'] === 'paused' )
                throw new expQueueRetryableException( 'the source ' . $summary['source'] . ' is paused', (int)expImageAliasSettings::staging()['pause_seconds'], 'paused' );
            if ( $summary['result'] === 'inactive' )
                throw new expQueuePermanentException( 'this installation does not stage images ([AliasStaging] Environment, SyncFrom, BaseURL)', 'inactive' );
            if ( $summary['result'] === 'failed' )
                throw new expQueueRetryableException( 'the resync failed (see the debug log)', 60, 'failed' );
            return $summary;
        }
    }
}
