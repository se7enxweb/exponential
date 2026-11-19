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
 * Three job types, registered in the kernel's settings/expqueue.ini (read when the expqueue extension is active and
 * its tables are installed; expqueue.ini.append.php of a site may change class, timeout and attempts):
 *
 * - image.alias.generate: the aliases of one image attribute or a list of them, and their format variants
 *   (generatePayload()). Queued by a request that answered the placeholder because the alias was not made in time
 *   (queueMiss(), the visitors' priority), by pre-generation after publishing (expImageAliasPublish) and by the
 *   administration. Made exactly as a request makes them (expImageAliasServer::complete()): the same per-alias lock,
 *   which on a database cluster lives in the shared storage, and the same conversion slots.
 * - image.alias.bulk: generate, regenerate or purge the aliases of every published image in a scope: the whole
 *   installation, a node, a subtree, classes, attributes, aliases, "changed since" (bulkPayload()). Runs at bulk
 *   priority, with a lower process priority ([AliasSettings] BulkNice), stops for a pause
 *   (expImageAliasThrottle::pauseBulk()) and goes on where it stopped.
 * - image.alias.resync: the resync of a staging site from its source (expImageAliasStager::resync()).
 *
 * Priorities (expqueue serves its classes by weight, then by priority within a class, aging the waiting jobs):
 * a visitor's miss runs in the class interactive at 80, pre-generation after publishing in editorial at 60, work
 * started in the administration in interactive at 60, bulk runs in bulk at 40 (priorityFor()).
 *
 * Without expqueue (not active, tables not installed, the type not configured) the start*() methods run the work at
 * once in the calling process, with the same progress callback. A request never does that: queueMiss() and
 * pre-generation queue or do nothing.
 *
 * Every run writes its history to imagealias.log (run-start, a line per alias or file made or failed, run-end with the
 * counts) under a run id; a queued job reports its progress to expqueue (Setup > Job queue) and can be cancelled
 * there.
 *
 * @since 6.0.15
 * @package kernel
 */
class expImageAliasJob
{
    const TYPE_RESYNC = 'image.alias.resync';
    const TYPE_GENERATE = 'image.alias.generate';
    const TYPE_BULK = 'image.alias.bulk';

    /** Where work comes from; it sets the queue class and priority */
    const ORIGINS = array( 'visitor', 'publish', 'admin', 'bulk' );

    /** The queue class and priority of each origin, and the classes to fall back to when a site has not got one */
    const PRIORITIES = array(
        'visitor' => array( 'class' => 'interactive', 'priority' => 80, 'fallback' => array( 'editorial' ) ),
        'admin' => array( 'class' => 'interactive', 'priority' => 60, 'fallback' => array( 'editorial' ) ),
        'publish' => array( 'class' => 'editorial', 'priority' => 60, 'fallback' => array( 'interactive' ) ),
        'bulk' => array( 'class' => 'bulk', 'priority' => 40, 'fallback' => array() ),
    );

    /** What a bulk run does */
    const BULK_ACTIONS = array( 'generate', 'regenerate', 'purge' );

    /** At most this many image attributes in one generate job */
    const MAX_ITEMS = 500;

    /** Seconds a queued bulk run waits before it looks at a pause again */
    const PAUSE_RECHECK = 30;

    /** @var callable|null stands in for expQueue::enqueue( $type, $payload, $options ) (tests) */
    protected static $queue = null;

    /**
     * Puts $enqueue in the place of expQueue::enqueue() (tests): the queue counts as available, and $enqueue gets
     * ( $type, $payload, $options ) and returns the job (an object with attribute( 'id' )) or throws; null: expqueue.
     *
     * @param callable|null $enqueue
     */
    public static function setQueue( $enqueue )
    {
        self::$queue = $enqueue;
    }

    /**
     * @param string $type
     * @param array $payload
     * @param array $options
     * @return object the queued job
     */
    protected static function queueJob( $type, array $payload, array $options )
    {
        if ( self::$queue !== null )
            return call_user_func( self::$queue, $type, $payload, $options );
        return expQueue::enqueue( $type, $payload, $options );
    }

    /**
     * Whether a job of $type goes to expqueue: the extension is active, its tables are installed and expqueue.ini
     * names the type.
     *
     * @param string $type
     * @return bool
     */
    public static function queueAvailable( $type )
    {
        if ( self::$queue !== null )
            return true;
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
     * The queue class and priority of work from $origin. A class the site's expqueue.ini does not have is replaced
     * by the next of the origin's fallbacks, else left to the job type (the priority stays).
     *
     * @param string $origin visitor, publish, admin or bulk
     * @param string[]|null $classes the configured classes (null: expqueue.ini)
     * @return array class (null = the type's), priority
     */
    public static function priorityFor( $origin, ?array $classes = null )
    {
        $def = isset( self::PRIORITIES[$origin] ) ? self::PRIORITIES[$origin] : self::PRIORITIES['admin'];
        if ( $classes === null )
            $classes = class_exists( 'expQueueConfig' ) ? array_keys( expQueueConfig::instance()->classes() ) : array();
        foreach ( array_merge( array( $def['class'] ), $def['fallback'] ) as $class )
        {
            if ( in_array( $class, $classes, true ) )
                return array( 'class' => $class, 'priority' => $def['priority'] );
        }
        return array( 'class' => null, 'priority' => $def['priority'] );
    }

    /**
     * @param string $prefix
     * @return string a new run id (prefix, time, random)
     */
    public static function newRunId( $prefix = 'ia' )
    {
        return preg_replace( '/[^a-z]/', '', (string)$prefix ) . date( 'YmdHis' ) . '-' . bin2hex( random_bytes( 3 ) );
    }

    // ------------------------------------------------------------------------------------------------ payloads

    /**
     * The aliases of a payload: siteaccess => alias names (the siteaccess whose image.ini defines them; "-" = the
     * worker's own), only valid names, never 'original'.
     *
     * @param mixed $aliases a list of names (the worker's own siteaccess) or siteaccess => names
     * @return array
     */
    public static function aliasSets( $aliases )
    {
        if ( !is_array( $aliases ) )
            return array();
        if ( $aliases && array_keys( $aliases ) === range( 0, count( $aliases ) - 1 ) && !is_array( reset( $aliases ) ) )
            $aliases = array( '-' => $aliases );
        $out = array();
        $count = 0;
        foreach ( $aliases as $siteAccess => $names )
        {
            $siteAccess = (string)$siteAccess;
            if ( $siteAccess !== '-' && !preg_match( '/^[A-Za-z0-9_-]{1,100}$/', $siteAccess ) )
                continue;
            foreach ( (array)$names as $name )
            {
                $name = (string)$name;
                if ( $name === 'original' || !expImageAliasUrl::validAliasName( $name ) || $count >= 200 )
                    continue;
                if ( !isset( $out[$siteAccess] ) || !in_array( $name, $out[$siteAccess], true ) )
                {
                    $out[$siteAccess][] = $name;
                    $count++;
                }
            }
        }
        return $out;
    }

    /**
     * @param mixed $formats
     * @return string[] webp / avif, each once
     */
    public static function formatList( $formats )
    {
        $out = array();
        foreach ( (array)$formats as $format )
        {
            $format = strtolower( trim( (string)$format ) );
            if ( in_array( $format, array( 'webp', 'avif' ), true ) && !in_array( $format, $out, true ) )
                $out[] = $format;
        }
        return $out;
    }

    /**
     * The payload of image.alias.generate, from what a producer gives: only known keys and valid values.
     *
     * @param array $input items (a list of array( attribute, version, language ), or of hashes with those keys),
     *                     aliases (names, or siteaccess => names), formats (webp, avif), origin, run
     * @return array
     */
    public static function generatePayload( array $input )
    {
        $items = array();
        $seen = array();
        foreach ( isset( $input['items'] ) && is_array( $input['items'] ) ? $input['items'] : array() as $item )
        {
            if ( !is_array( $item ) )
                continue;
            $attribute = isset( $item['attribute'] ) ? $item['attribute'] : ( isset( $item[0] ) ? $item[0] : 0 );
            $version = isset( $item['version'] ) ? $item['version'] : ( isset( $item[1] ) ? $item[1] : 0 );
            $language = isset( $item['language'] ) ? $item['language'] : ( isset( $item[2] ) ? $item[2] : '' );
            if ( !is_numeric( $attribute ) || !is_numeric( $version ) || (int)$attribute <= 0 || (int)$version <= 0
                 || !expImageAliasUrl::validLanguage( (string)$language ) )
                continue;
            $key = (int)$attribute . '-' . (int)$version . '-' . $language;
            if ( isset( $seen[$key] ) || count( $items ) >= self::MAX_ITEMS )
                continue;
            $seen[$key] = true;
            $items[] = array( (int)$attribute, (int)$version, (string)$language );
        }
        $payload = array( 'items' => $items, 'aliases' => self::aliasSets( isset( $input['aliases'] ) ? $input['aliases'] : array() ) );
        $formats = self::formatList( isset( $input['formats'] ) ? $input['formats'] : array() );
        if ( $formats )
            $payload['formats'] = $formats;
        $payload['origin'] = isset( $input['origin'] ) && in_array( $input['origin'], self::ORIGINS, true ) ? $input['origin'] : 'admin';
        if ( isset( $input['run'] ) && preg_match( '/^[A-Za-z0-9_.-]{1,64}$/', (string)$input['run'] ) )
            $payload['run'] = (string)$input['run'];
        return $payload;
    }

    /**
     * Why a generate payload cannot run, or null when it can.
     *
     * @param array $payload
     * @return string|null
     */
    public static function generatePayloadError( array $payload )
    {
        if ( $payload != self::generatePayload( $payload ) )
            return 'the payload has keys or values a generate job does not take';
        if ( !$payload['items'] )
            return 'no image attribute (items)';
        if ( !$payload['aliases'] )
            return 'no alias (aliases)';
        return null;
    }

    /**
     * The payload of image.alias.bulk: action, the scope (node, subtree, since, limit, class[], attribute[], alias[]
     * as in a resync), aliases (siteaccess => names, as generatePayload(); filled from the related siteaccesses by
     * startBulk()), formats, after (where a stopped run goes on: array( attribute id, language )), run.
     *
     * @param array $input
     * @return array
     */
    public static function bulkPayload( array $input )
    {
        $payload = array( 'action' => isset( $input['action'] ) && in_array( $input['action'], self::BULK_ACTIONS, true ) ? $input['action'] : 'generate' );
        foreach ( array( 'node', 'subtree', 'since', 'limit' ) as $key )
        {
            if ( isset( $input[$key] ) && is_numeric( $input[$key] ) && (int)$input[$key] > 0 )
                $payload[$key] = (int)$input[$key];
        }
        foreach ( array( 'class', 'attribute', 'alias' ) as $key )
        {
            if ( empty( $input[$key] ) )
                continue;
            $values = array();
            foreach ( (array)$input[$key] as $value )
            {
                $value = trim( (string)$value );
                if ( preg_match( '/^[A-Za-z0-9_.-]{1,100}$/', $value ) && !in_array( $value, $values, true ) )
                    $values[] = $value;
            }
            if ( $values )
                $payload[$key] = $values;
        }
        $aliases = self::aliasSets( isset( $input['aliases'] ) ? $input['aliases'] : array() );
        if ( $aliases )
            $payload['aliases'] = $aliases;
        $formats = self::formatList( isset( $input['formats'] ) ? $input['formats'] : array() );
        if ( $formats )
            $payload['formats'] = $formats;
        if ( isset( $input['after'] ) && is_array( $input['after'] ) && isset( $input['after'][0], $input['after'][1] )
             && is_numeric( $input['after'][0] ) && (int)$input['after'][0] > 0 && expImageAliasUrl::validLanguage( (string)$input['after'][1] ) )
            $payload['after'] = array( (int)$input['after'][0], (string)$input['after'][1] );
        if ( isset( $input['run'] ) && preg_match( '/^[A-Za-z0-9_.-]{1,64}$/', (string)$input['run'] ) )
            $payload['run'] = (string)$input['run'];
        return $payload;
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

    // ------------------------------------------------------------------------------------------------ producers

    /**
     * Queues a job, or returns null when expqueue cannot take it (or refused it).
     *
     * @param string $type
     * @param array $payload
     * @param string $origin
     * @param string $key the idempotency key ('' none)
     * @return array|null job (id), duplicate (bool)
     */
    protected static function enqueue( $type, array $payload, $origin, $key )
    {
        if ( !self::queueAvailable( $type ) )
            return null;
        $priority = self::priorityFor( $origin );
        $options = array( 'priority' => $priority['priority'] );
        if ( $priority['class'] !== null )
            $options['class'] = $priority['class'];
        if ( $key !== '' )
            $options['idempotency_key'] = $key;
        try
        {
            $job = self::queueJob( $type, $payload, $options );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeWarning( "The job $type could not be queued: " . $e->getMessage(), __METHOD__ );
            return null;
        }
        return array( 'job' => (int)$job->attribute( 'id' ), 'duplicate' => !empty( $job->duplicate ) );
    }

    /**
     * A request answered the placeholder for an alias (or a variant) it could not make in time: the job queue makes it
     * at the visitors' priority. One job per image, alias and hour, however many requests miss it. Nothing when
     * expqueue cannot take the job: the next request tries again (the swap script asks again).
     *
     * @param int $attribute
     * @param int $version
     * @param string $language
     * @param string $aliasName
     * @param string[] $formats
     * @param string|null $target the file expected (the log)
     * @return int|null the job id
     */
    public static function queueMiss( $attribute, $version, $language, $aliasName, array $formats = array(), $target = null )
    {
        $siteAccess = expImageAliasSettings::currentSiteAccess();
        $payload = self::generatePayload( array( 'items' => array( array( $attribute, $version, $language ) ),
                                                 'aliases' => array( $siteAccess !== '' ? $siteAccess : '-' => array( $aliasName ) ),
                                                 'formats' => $formats, 'origin' => 'visitor' ) );
        if ( self::generatePayloadError( $payload ) !== null )
            return null;
        $key = 'miss:' . sha1( json_encode( array( $payload['items'], $payload['aliases'], isset( $payload['formats'] ) ? $payload['formats'] : array() ) ) ) . ':' . date( 'YmdH' );
        $queued = self::enqueue( self::TYPE_GENERATE, $payload, 'visitor', $key );
        if ( $queued === null )
            return null;
        expImageAliasLog::write( 'queue', array( 'result' => $queued['duplicate'] ? 'duplicate' : 'queued', 'alias' => $aliasName . ( $formats ? '.' . implode( ',', $formats ) : '' ),
                                                 'job' => $queued['job'], 'origin' => 'visitor', 'path' => $target !== null ? $target : '-' ) );
        return $queued['job'];
    }

    /**
     * Pre-generation after a publication (expImageAliasPublish): queues the aliases of the images of a published
     * version, at the priority of publishing. Never runs them now: null when expqueue cannot take the job.
     *
     * @param array $items array( attribute, version, language ) each
     * @param array $aliases siteaccess => alias names
     * @param string[] $formats
     * @param int $objectID
     * @param int $version
     * @return array|null job, duplicate, run
     */
    public static function queuePublish( array $items, array $aliases, array $formats, $objectID, $version )
    {
        $payload = self::generatePayload( array( 'items' => $items, 'aliases' => $aliases, 'formats' => $formats, 'origin' => 'publish',
                                                 'run' => self::newRunId( 'pub' ) ) );
        if ( self::generatePayloadError( $payload ) !== null )
            return null;
        $queued = self::enqueue( self::TYPE_GENERATE, $payload, 'publish', 'publish:' . (int)$objectID . ':' . (int)$version );
        return $queued === null ? null : $queued + array( 'run' => $payload['run'] );
    }

    /**
     * Starts a generate run: queued when expqueue takes it (and $queue allows it), else run now.
     *
     * @param array $input see generatePayload()
     * @param bool $queue false: always run now
     * @param callable|null $progress for a run now: function( array $progress ) done, total, path, counts, run
     * @return array mode (queue, sync or invalid), run, job, summary (a run now), error
     */
    public static function startGenerate( array $input, $queue = true, $progress = null )
    {
        $payload = self::generatePayload( $input );
        $error = self::generatePayloadError( $payload );
        if ( $error !== null )
            return array( 'mode' => 'invalid', 'run' => null, 'job' => null, 'summary' => null, 'error' => $error );
        if ( !isset( $payload['run'] ) )
            $payload['run'] = self::newRunId( 'gen' );
        if ( $queue )
        {
            $key = $payload['origin'] === 'publish' ? 'publish:' . sha1( json_encode( array( $payload['items'], $payload['aliases'] ) ) ) : $payload['run'];
            $queued = self::enqueue( self::TYPE_GENERATE, $payload, $payload['origin'], $key );
            if ( $queued !== null )
                return array( 'mode' => 'queue', 'run' => $payload['run'], 'job' => $queued['job'], 'summary' => null, 'error' => null );
        }
        return array( 'mode' => 'sync', 'run' => $payload['run'], 'job' => null, 'summary' => self::runGenerate( $payload, $progress ), 'error' => null );
    }

    /**
     * Starts a bulk run: queued when expqueue takes it, else run now. Without aliases in $input it covers every alias
     * of the related siteaccesses (expImageAliasSettings::siteAccessAliasPlan()), narrowed by alias[]; formats
     * default to both (each alias gets those its Formats[] lists).
     *
     * @param array $input see bulkPayload()
     * @param bool $queue
     * @param callable|null $progress
     * @param array $options for a run now: on_pause => stop (default) or wait; sync_fallback => false: when the queue
     *                       is there but refuses the job (a quota, a database error), nothing runs and mode is refused
     *                       (the admin page must not run a whole site inside a web request)
     * @return array mode (queue, sync or refused), run, job, summary
     */
    public static function startBulk( array $input, $queue = true, $progress = null, array $options = array() )
    {
        $payload = self::bulkPayload( $input );
        if ( !isset( $payload['aliases'] ) && $payload['action'] !== 'purge' )
        {
            $plan = expImageAliasSettings::siteAccessAliasPlan();
            $payload['aliases'] = self::aliasSets( self::filterPlan( $plan, isset( $payload['alias'] ) ? $payload['alias'] : array() ) );
        }
        if ( !isset( $input['formats'] ) && $payload['action'] !== 'purge' )
            $payload['formats'] = array( 'webp', 'avif' );
        if ( !isset( $payload['run'] ) )
            $payload['run'] = self::newRunId( 'bulk' );
        if ( $queue )
        {
            $queued = self::enqueue( self::TYPE_BULK, $payload, 'bulk', $payload['run'] );
            if ( $queued !== null )
                return array( 'mode' => 'queue', 'run' => $payload['run'], 'job' => $queued['job'], 'summary' => null );
            if ( isset( $options['sync_fallback'] ) && $options['sync_fallback'] === false && self::queueAvailable( self::TYPE_BULK ) )
                return array( 'mode' => 'refused', 'run' => null, 'job' => null, 'summary' => null );
        }
        return array( 'mode' => 'sync', 'run' => $payload['run'], 'job' => null, 'summary' => self::runBulk( $payload, $progress, null, $options ) );
    }

    /**
     * A siteaccess plan (siteaccess => aliases) narrowed to $aliases (all when empty).
     *
     * @param array $plan
     * @param string[] $aliases
     * @return array
     */
    public static function filterPlan( array $plan, array $aliases )
    {
        if ( !$aliases )
            return $plan;
        $out = array();
        foreach ( $plan as $siteAccess => $names )
        {
            $names = array_values( array_intersect( (array)$names, $aliases ) );
            if ( $names )
                $out[$siteAccess] = $names;
        }
        return $out;
    }

    /**
     * Starts a resync: queued when expqueue takes it (and $queue allows it), else run now.
     *
     * @param array $options see expImageAliasStager::resync()
     * @param bool $queue false: always run now
     * @param callable|null $progress for a run now (see expImageAliasStager::resync())
     * @param bool $syncFallback false: when the queue is there but refuses the job, nothing runs (mode refused)
     * @return array mode (queue, sync or refused), run (the run id in the log), job (the queue job id, or null), summary
     *               (of a run now, else null)
     */
    public static function startResync( array $options, $queue = true, $progress = null, $syncFallback = true )
    {
        $payload = self::resyncPayload( $options );
        if ( !isset( $payload['run'] ) )
            $payload['run'] = expImageAliasStager::newRunId();
        if ( $queue && self::queueAvailable( self::TYPE_RESYNC ) )
        {
            // A quota or a database error is a refusal, as in enqueue(), not an exception for the page or the command
            try
            {
                $job = self::queueJob( self::TYPE_RESYNC, $payload, array( 'idempotency_key' => $payload['run'] ) + self::queueOptions( 'bulk' ) );
                return array( 'mode' => 'queue', 'run' => $payload['run'], 'job' => (int)$job->attribute( 'id' ), 'summary' => null );
            }
            catch ( Throwable $e )
            {
                eZDebug::writeWarning( 'The job ' . self::TYPE_RESYNC . ' could not be queued: ' . $e->getMessage(), __METHOD__ );
                if ( !$syncFallback )
                    return array( 'mode' => 'refused', 'run' => null, 'job' => null, 'summary' => null );
            }
        }
        return array( 'mode' => 'sync', 'run' => $payload['run'], 'job' => null, 'summary' => self::runResync( $payload, $progress ) );
    }

    /**
     * @param string $origin
     * @return array the expqueue options of class and priority for $origin
     */
    protected static function queueOptions( $origin )
    {
        $priority = self::priorityFor( $origin );
        return $priority['class'] !== null ? array( 'class' => $priority['class'], 'priority' => $priority['priority'] )
                                           : array( 'priority' => $priority['priority'] );
    }

    // ------------------------------------------------------------------------------------------------ runs

    /**
     * Runs a generate payload (the queue handler and the synchronous fallback).
     *
     * @param array $payload see generatePayload()
     * @param callable|null $progress function( array $progress ): done, total, path, counts, run; it may throw to stop
     * @return array run, result (done, invalid), origin, items, counts (present, made, timeout, failed, unknown, gone,
     *               variants_made, variants_present, variants_timeout, variants_failed), ms
     */
    public static function runGenerate( array $payload, $progress = null )
    {
        $started = microtime( true );
        $payload = self::generatePayload( $payload );
        $run = isset( $payload['run'] ) ? $payload['run'] : self::newRunId( 'gen' );
        $summary = array( 'run' => $run, 'result' => 'done', 'origin' => $payload['origin'], 'items' => count( $payload['items'] ),
                          'counts' => self::emptyCounts(), 'ms' => 0 );
        if ( self::generatePayloadError( $payload ) !== null )
        {
            $summary['result'] = 'invalid';
            return $summary;
        }
        $formats = isset( $payload['formats'] ) ? $payload['formats'] : array();
        $logged = $payload['origin'] !== 'visitor';
        $aliasCount = 0;
        foreach ( $payload['aliases'] as $names )
            $aliasCount += count( $names );
        if ( $logged )
            expImageAliasLog::write( 'run-start', array( 'result' => 'generate', 'run' => $run, 'alias' => '-', 'origin' => $payload['origin'],
                                                         'images' => count( $payload['items'] ), 'aliases' => $aliasCount ) );
        $server = expImageAliasServer::forJob();
        $done = 0;
        foreach ( $payload['items'] as $item )
        {
            $attribute = $server->publishedImageAttribute( $item[0], $item[1], $item[2] );
            if ( $attribute === null )
                $summary['counts']['gone']++;
            else
            {
                foreach ( $payload['aliases'] as $siteAccess => $names )
                    self::completeAliases( $server, $attribute, $siteAccess, $names, $formats, $summary['counts'] );
            }
            $done++;
            if ( $progress !== null )
                call_user_func( $progress, array( 'run' => $run, 'done' => $done, 'total' => count( $payload['items'] ),
                                                  'path' => 'attribute ' . implode( '-', $item ), 'counts' => $summary['counts'] ) );
        }
        $summary['ms'] = (int)round( ( microtime( true ) - $started ) * 1000 );
        if ( $logged )
            expImageAliasLog::write( 'run-end', array( 'result' => $summary['result'], 'ms' => $summary['ms'], 'run' => $run, 'alias' => '-',
                                                       'origin' => $payload['origin'] ) + $summary['counts'] );
        return $summary;
    }

    /**
     * Makes the aliases $names of one image, as the siteaccess $siteAccess defines them, and counts the outcome.
     *
     * @param expImageAliasServer $server
     * @param eZContentObjectAttribute $attribute
     * @param string $siteAccess '-' = this process's own
     * @param string[] $names
     * @param string[] $formats
     * @param array $counts
     */
    protected static function completeAliases( expImageAliasServer $server, $attribute, $siteAccess, array $names, array $formats, array &$counts )
    {
        expImageAliasSettings::withSiteAccess( $siteAccess === '-' ? '' : $siteAccess, function () use ( $server, $attribute, $names, $formats, &$counts ) {
            foreach ( $names as $name )
            {
                $result = $server->complete( $attribute, $name, $formats );
                switch ( $result['alias'] )
                {
                    case 'present':
                        $counts['present']++;
                        break;
                    case 'ok':
                        $counts['made']++;
                        break;
                    case 'timeout':
                        $counts['timeout']++;
                        break;
                    case 'unknown':
                        $counts['unknown']++;
                        break;
                    case 'no-image':
                        $counts['gone']++;
                        return;
                    default:
                        $counts['failed']++;
                }
                foreach ( $result['variants'] as $variant )
                {
                    if ( $variant === 'ok' )
                        $counts['variants_made']++;
                    elseif ( $variant === 'present' )
                        $counts['variants_present']++;
                    elseif ( $variant === 'timeout' )
                        // no slot or lock in time: deferred (a retry or the next visit makes it), not failed
                        $counts['variants_timeout']++;
                    else
                        $counts['variants_failed']++;
                }
            }
        } );
    }

    /**
     * @return array the counters of a generate or bulk run
     */
    public static function emptyCounts()
    {
        return array( 'present' => 0, 'made' => 0, 'timeout' => 0, 'failed' => 0, 'unknown' => 0, 'gone' => 0, 'purged' => 0,
                      'variants_made' => 0, 'variants_present' => 0, 'variants_timeout' => 0, 'variants_failed' => 0 );
    }

    /**
     * Runs a bulk payload (the queue handler and the synchronous fallback): every published image of the scope, in
     * the order of their attribute ids, at a lower process priority.
     *
     * @param array $payload see bulkPayload()
     * @param callable|null $progress function( array $progress ): done, total, path, counts, run; it may throw to stop
     * @param callable|null $paused function( array $after ): what to do while bulk runs are paused (a queued run saves
     *                              where it is and is deferred); returns true to stop the run, false to look again
     * @param array $options on_pause => stop (the default without $paused) or wait (look again every 5 s)
     * @return array run, result (done, paused, failed), action, images, total, after, counts, ms, scope
     */
    public static function runBulk( array $payload, $progress = null, $paused = null, array $options = array() )
    {
        $started = microtime( true );
        $payload = self::bulkPayload( $payload );
        $run = isset( $payload['run'] ) ? $payload['run'] : self::newRunId( 'bulk' );
        $scope = expImageAliasStager::describeScope( $payload );
        $summary = array( 'run' => $run, 'result' => 'done', 'action' => $payload['action'], 'images' => 0, 'total' => 0,
                          'after' => isset( $payload['after'] ) ? $payload['after'] : null, 'counts' => self::emptyCounts(), 'ms' => 0, 'scope' => $scope );
        $formats = isset( $payload['formats'] ) ? $payload['formats'] : array();
        $aliasFilter = isset( $payload['alias'] ) ? $payload['alias'] : array();
        $sets = isset( $payload['aliases'] ) ? $payload['aliases'] : array();
        expImageAliasLog::write( 'run-start', array( 'result' => $payload['action'], 'run' => $run, 'alias' => '-', 'origin' => 'bulk',
                                                     'resumed' => isset( $payload['after'] ) ? 1 : 0, 'path' => $scope ) );
        $nice = expImageAliasThrottle::lowerPriority();
        $server = expImageAliasServer::forJob();
        try
        {
            $scopeOptions = array_intersect_key( $payload, array_flip( array( 'node', 'subtree', 'since', 'limit', 'class', 'attribute' ) ) );
            $summary['total'] = expImageAliasStager::countImages( $scopeOptions );
            $after = isset( $payload['after'] ) ? $payload['after'] : null;
            $done = 0;
            foreach ( expImageAliasStager::imageFiles( $scopeOptions ) as $image )
            {
                $done++;
                if ( $after !== null && ( $image['attribute'] < $after[0] || ( $image['attribute'] === $after[0] && strcmp( $image['language'], $after[1] ) <= 0 ) ) )
                    continue;
                while ( expImageAliasThrottle::isBulkPaused() )
                {
                    $stop = $paused !== null ? (bool)call_user_func( $paused, $summary['after'] )
                                             : ( !isset( $options['on_pause'] ) || $options['on_pause'] !== 'wait' );
                    if ( $stop )
                    {
                        $summary['result'] = 'paused';
                        break 2;
                    }
                    sleep( 5 );
                }
                $attribute = $server->publishedImageAttribute( $image['attribute'], $image['version'], $image['language'] );
                if ( $attribute === null )
                    $summary['counts']['gone']++;
                else
                {
                    if ( $payload['action'] !== 'generate' )
                        self::purgeImage( $attribute, $aliasFilter, $summary['counts'] );
                    if ( $payload['action'] !== 'purge' )
                    {
                        foreach ( $sets as $siteAccess => $names )
                            self::completeAliases( $server, $attribute, $siteAccess, $names, $formats, $summary['counts'] );
                    }
                }
                $summary['images']++;
                $summary['after'] = array( $image['attribute'], $image['language'] );
                if ( $progress !== null )
                    call_user_func( $progress, array( 'run' => $run, 'done' => $done, 'total' => $summary['total'], 'path' => $image['label'],
                                                      'counts' => $summary['counts'], 'after' => $summary['after'] ) );
            }
        }
        catch ( Throwable $e )
        {
            // A cancel from the queue (or another stop by the progress callback) goes on to the caller after the log
            if ( class_exists( 'expQueueCancelledException' ) && $e instanceof expQueueCancelledException )
            {
                self::endBulkLog( $summary, 'cancelled', $started, $nice );
                throw $e;
            }
            $summary['result'] = 'failed';
            eZDebug::writeError( 'The bulk image alias run failed: ' . $e->getMessage(), __METHOD__ );
        }
        self::endBulkLog( $summary, $summary['result'], $started, $nice );
        return $summary;
    }

    /**
     * @param array $summary
     * @param string $result
     * @param float $started
     * @param int|null $nice
     */
    protected static function endBulkLog( array &$summary, $result, $started, $nice )
    {
        expImageAliasThrottle::restorePriority( $nice );
        $extra = array();
        if ( $summary['counts']['purged'] > 0 )
            $extra['response_cache'] = $summary['response_cache'] = self::clearResponseCaches();
        $summary['ms'] = (int)round( ( microtime( true ) - $started ) * 1000 );
        expImageAliasLog::write( 'run-end', array( 'result' => $result, 'ms' => $summary['ms'], 'run' => $summary['run'], 'alias' => '-',
                                                   'origin' => 'bulk', 'images' => $summary['images'], 'total' => $summary['total'] )
                                            + $summary['counts'] + $extra + array( 'path' => $summary['scope'] ) );
    }

    /**
     * After a run removed alias files: Velocity's response cache may still hold the answer index_image.php gave for
     * one of them (a storage path it made on demand), and would go on serving the removed file. The cache keeps its
     * entries in the server's own memory and APCu as well as on disk, which no other process can reach entry by
     * entry, so its generation is moved on (expCacheManager::clearVelocityCache(), as `exp:velocity cache clear`):
     * every response stored before now is a miss, pages included, and is made again on its next request. Nothing
     * when Velocity is not part of the installation.
     *
     * @return string cleared, none (no Velocity) or failed
     */
    public static function clearResponseCaches()
    {
        if ( !class_exists( 'expCacheManager' ) || !class_exists( 'expVelocity' ) )
            return 'none';
        try
        {
            $result = expCacheManager::clearVelocityCache();
        }
        catch ( Throwable $e )
        {
            eZDebug::writeWarning( 'Velocity\'s response cache could not be cleared after removing image aliases: ' . $e->getMessage(), __METHOD__ );
            return 'failed';
        }
        if ( !empty( $result['ok'] ) )
            return 'cleared';
        $message = isset( $result['message'] ) ? (string)$result['message'] : '';
        return strpos( $message, 'not part of this installation' ) !== false ? 'none' : 'failed';
    }

    /**
     * Removes the aliases of one image (all of them, or those in $aliases), as Setup > Cache's image alias clearing
     * does: files that older versions still name stay, the XML entries go.
     *
     * @param eZContentObjectAttribute $attribute
     * @param string[] $aliases
     * @param array $counts
     */
    protected static function purgeImage( $attribute, array $aliases, array &$counts )
    {
        $handler = $attribute->attribute( 'content' );
        if ( !$handler instanceof eZImageAliasHandler )
            return;
        $list = $handler->aliasList( false );
        unset( $list['original'] );
        $names = $aliases ? array_values( array_intersect( array_keys( $list ), $aliases ) ) : array_keys( $list );
        if ( !$names )
            return;
        $counts['purged'] += $handler->purgeAliases( $names );
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
