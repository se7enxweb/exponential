<?php
/**
 * File containing the expMigrationQueueHandler class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The expqueue handler of migration runs started in Setup > Migrations with migration.ini [AdminSettings]
 * Background=queue (expMigrationJob). The site registers the job type in its expqueue.ini.append.php:
 *
 *   [JobType_migration.run]
 *   Handler=expMigrationQueueHandler
 *   Class=bulk
 *   Timeout=86400
 *   Description=A migration run started in Setup > Migrations
 *
 * The payload is the job's id only ({"job": "<run id>"}); the command line is in the job's file, written by the
 * administration when it queued the job. The worker runs it as a process of its own (expMigrationJob::runQueued())
 * and keeps the queue job alive while it runs. A run that ends with a failure is a permanent failure of the queue
 * job: a migration run is never repeated by itself (the page says what failed; exp:migration:resume goes on).
 *
 * Declared only when expqueue is active: without it this file defines nothing, and nothing asks for the class.
 *
 * @since 6.0.15
 * @package kernel
 */
if ( interface_exists( 'expQueueHandler' ) && class_exists( 'expQueueHandlerBase' ) )
{
    class expMigrationQueueHandler extends expQueueHandlerBase
    {
        public function validate( array $payload )
        {
            return isset( $payload['job'] ) && expMigrationRecord::validRunId( (string)$payload['job'] ) ? null : 'the payload names no migration job';
        }

        public function describe( expQueueJob $job )
        {
            $payload = $job->payloadArray();
            return 'migration job ' . ( isset( $payload['job'] ) ? (string)$payload['job'] : '' );
        }

        public function run( expQueueJob $job, expQueueContext $context )
        {
            $payload = $job->payloadArray();
            $exit = expMigrationJob::runQueued( isset( $payload['job'] ) ? (string)$payload['job'] : '', function () use ( $context ) {
                $context->heartbeat();
            } );
            if ( $exit !== 0 )
                throw new expQueuePermanentException( 'the migration run ended with exit ' . $exit, 'migration' );
            return array( 'exit' => $exit );
        }
    }
}
