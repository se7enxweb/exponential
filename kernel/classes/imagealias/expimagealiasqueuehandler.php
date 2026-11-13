<?php
/**
 * File containing the expImageAliasQueueHandler class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The expqueue handler of background work on image aliases (expImageAliasJob). The site registers the job type in
 * its expqueue.ini.append.php:
 *
 *   [JobType_image.alias.resync]
 *   Handler=expImageAliasQueueHandler
 *   Class=bulk
 *   Timeout=86400
 *   Description=Fetches the images of a staging site from its source installation
 *
 * The payload is the resync's scope and options (expImageAliasJob::resyncPayload()) with its run id. Progress goes to
 * the queue job (one step per image) and the history to imagealias.log. A source that is paused stops the run; the
 * job is tried again after the pause (files already fetched are not fetched again in the default mode).
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
            if ( $payload != expImageAliasJob::resyncPayload( $payload ) )
                return 'the payload has keys or values a resync does not take';
            return null;
        }

        public function describe( expQueueJob $job )
        {
            $payload = $job->payloadArray();
            return 'image resync ' . ( isset( $payload['run'] ) ? (string)$payload['run'] : '' ) . ' (' . expImageAliasStager::describeScope( $payload ) . ')';
        }

        public function run( expQueueJob $job, expQueueContext $context )
        {
            if ( $job->attribute( 'type' ) !== expImageAliasJob::TYPE_RESYNC )
                throw new expQueuePermanentException( 'not an image alias job type: ' . $job->attribute( 'type' ), 'unknown_type' );
            $summary = expImageAliasJob::runResync( $job->payloadArray(), function ( array $progress ) use ( $context ) {
                $context->progress( $progress['done'], $progress['total'], $progress['path'] );
                $context->throwIfCancelled();
            } );
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
