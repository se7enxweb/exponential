<?php
/**
 * File containing the expImageAliasJob class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Background work on image aliases, through expqueue when it is there, else synchronously (the fallback).
 *
 * The resync of staged images (expImageAliasStager::resync()) is the job type image.alias.resync. It goes to expqueue
 * when the extension is active, its tables are installed and the site's expqueue.ini.append.php names the job type:
 *
 *   [JobType_image.alias.resync]
 *   Handler=expImageAliasQueueHandler
 *   Class=bulk
 *   Timeout=86400
 *   Description=Fetches the images of a staging site from its source installation
 *
 * Otherwise startResync() runs it at once, in the calling process. Either way the run writes its history to
 * imagealias.log (run-start, one line per file it fetched or could not fetch, run-end with the counts), under the run
 * id startResync() returns, and a queued job reports its progress to expqueue (Setup > Job queue).
 *
 * @since 6.0.15
 * @package kernel
 */
class expImageAliasJob
{
    const TYPE_RESYNC = 'image.alias.resync';

    /**
     * Whether a job of $type goes to expqueue: the extension is active, its tables are installed and expqueue.ini
     * names the type.
     *
     * @param string $type
     * @return bool
     */
    public static function queueAvailable( $type )
    {
        if ( !class_exists( 'expQueue' ) || !class_exists( 'expQueueConfig' ) || !interface_exists( 'expQueueHandler' ) )
            return false;
        try
        {
            return expQueue::installed() && expQueueConfig::instance()->jobType( (string)$type ) !== null;
        }
        catch ( Throwable $e )
        {
            return false;
        }
    }

    /**
     * The options of a resync as a job payload: only the known keys, with their types (never a secret).
     *
     * @param array $options see expImageAliasStager::resync()
     * @return array
     */
    public static function resyncPayload( array $options )
    {
        $payload = array();
        foreach ( array( 'node', 'subtree', 'since', 'limit' ) as $key )
        {
            if ( isset( $options[$key] ) && (int)$options[$key] > 0 )
                $payload[$key] = (int)$options[$key];
        }
        foreach ( array( 'class', 'attribute', 'alias' ) as $key )
        {
            if ( empty( $options[$key] ) )
                continue;
            $values = array();
            foreach ( (array)$options[$key] as $value )
            {
                $value = trim( (string)$value );
                if ( preg_match( '/^[A-Za-z0-9_.-]{1,100}$/', $value ) )
                    $values[] = $value;
            }
            if ( $values )
                $payload[$key] = array_values( array_unique( $values ) );
        }
        foreach ( array( 'overwrite', 'dry_run' ) as $key )
        {
            if ( !empty( $options[$key] ) )
                $payload[$key] = true;
        }
        if ( isset( $options['run'] ) && preg_match( '/^[A-Za-z0-9_.-]{1,64}$/', (string)$options['run'] ) )
            $payload['run'] = (string)$options['run'];
        return $payload;
    }

    /**
     * Starts a resync: queued when expqueue takes it (and $queue allows it), else run now.
     *
     * @param array $options see expImageAliasStager::resync()
     * @param bool $queue false: always run now
     * @param callable|null $progress for a run now (see expImageAliasStager::resync())
     * @return array mode (queue or sync), run (the run id in the log), job (the queue job id, or null), summary (of a
     *               run now, else null)
     */
    public static function startResync( array $options, $queue = true, $progress = null )
    {
        $payload = self::resyncPayload( $options );
        if ( !isset( $payload['run'] ) )
            $payload['run'] = expImageAliasStager::newRunId();
        if ( $queue && self::queueAvailable( self::TYPE_RESYNC ) )
        {
            $job = expQueue::enqueue( self::TYPE_RESYNC, $payload, array( 'idempotency_key' => $payload['run'] ) );
            return array( 'mode' => 'queue', 'run' => $payload['run'], 'job' => (int)$job->attribute( 'id' ), 'summary' => null );
        }
        return array( 'mode' => 'sync', 'run' => $payload['run'], 'job' => null, 'summary' => self::runResync( $payload, $progress ) );
    }

    /**
     * Runs a resync payload (the queue handler and the synchronous fallback).
     *
     * @param array $payload
     * @param callable|null $progress
     * @return array the summary (expImageAliasStager::resync())
     */
    public static function runResync( array $payload, $progress = null )
    {
        $stager = new expImageAliasStager();
        return $stager->resync( self::resyncPayload( $payload ), $progress );
    }
}
