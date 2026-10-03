<?php
/**
 * Content job services: ezjscore/call/expcontentjob::<method>[::arg...]
 *
 * The background jobs that run large content operations in batches (remove, copy, move, hide, reveal, section,
 * state, add and remove locations): list and follow them, ask how large an operation is and whether it would run
 * now or as a job, create jobs of every type, and cancel, resume or start them. A user sees and controls their own
 * jobs; the jobs of everybody need the setup/administrate policy.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expContentJobServices extends expContentServiceBase
{
    public static $services = array(
        'list' => array( 'summary' => 'The current user\'s jobs, newest first. state: queued, running, done, failed, cancelled or all', 'access' => array( 'content', 'jobs' ), 'write' => false, 'args' => array( 'state' => 'string', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of jobs' ),
        'listAll' => array( 'summary' => 'The jobs of all users (needs setup/administrate)', 'access' => array( 'setup', 'administrate' ), 'write' => false, 'args' => array( 'state' => 'string', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of jobs' ),
        'active' => array( 'summary' => 'The current user\'s queued and running jobs', 'access' => array( 'content', 'jobs' ), 'write' => false, 'args' => array(), 'returns' => 'jobs' ),
        'summary' => array( 'summary' => 'Counts of the current user\'s jobs per state', 'access' => array( 'content', 'jobs' ), 'write' => false, 'args' => array(), 'returns' => 'state => count' ),
        'get' => array( 'summary' => 'One job with its progress, result and the last log lines', 'access' => array( 'content', 'jobs' ), 'write' => false, 'args' => array( 'job_id' => 'string', 'log_lines' => 'int' ), 'returns' => 'job' ),
        'progress' => array( 'summary' => 'Only the progress of a job: done, total, percent, phase, message', 'access' => array( 'content', 'jobs' ), 'write' => false, 'args' => array( 'job_id' => 'string' ), 'returns' => 'progress' ),
        'log' => array( 'summary' => 'The last lines of the job log', 'access' => array( 'content', 'jobs' ), 'write' => false, 'args' => array( 'job_id' => 'string', 'lines' => 'int' ), 'returns' => 'lines' ),
        'result' => array( 'summary' => 'The result of a finished job', 'access' => array( 'content', 'jobs' ), 'write' => false, 'args' => array( 'job_id' => 'string' ), 'returns' => 'result' ),
        'types' => array( 'summary' => 'The registered job types with their parameters', 'access' => array( 'content', 'jobs' ), 'write' => false, 'args' => array(), 'returns' => 'types' ),
        'settings' => array( 'summary' => 'The limits that decide now or job: SynchronousLimit, NowLimit, BatchSize', 'access' => array( 'content', 'jobs' ), 'write' => false, 'args' => array(), 'returns' => 'settings' ),
        'estimate' => array( 'summary' => 'How many nodes an operation touches and whether it runs now or as a job. params as in create', 'access' => array( 'content', 'jobs' ), 'write' => false, 'args' => array( 'type' => 'string', 'params' => 'json' ), 'returns' => '{nodes, default_mode, now_allowed}' ),
        'lockOf' => array( 'summary' => 'The job that locks a node, if any', 'access' => array( 'content', 'jobs' ), 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => '{locked, job_id}' ),
        'create' => array( 'summary' => 'Creates a job and starts its worker. POST: params (json, see types), spawn (default 1)', 'access' => array( 'content', 'jobs' ), 'write' => true, 'args' => array( 'type' => 'string' ), 'returns' => 'job' ),
        'remove' => array( 'summary' => 'Job: removes nodes. POST: node_ids, move_to_trash, spawn', 'access' => array( 'content', 'remove' ), 'write' => true, 'args' => array(), 'returns' => 'job' ),
        'copy' => array( 'summary' => 'Job: copies a subtree. POST: source_node_id, destination_node_id, all_versions, keep_creator, keep_time, spawn', 'access' => array( 'content', 'create' ), 'write' => true, 'args' => array(), 'returns' => 'job' ),
        'move' => array( 'summary' => 'Job: moves a subtree. POST: node_id, new_parent_node_id, spawn', 'access' => array( 'content', 'move' ), 'write' => true, 'args' => array(), 'returns' => 'job' ),
        'hide' => array( 'summary' => 'Job: hides a subtree. POST: node_id, spawn', 'access' => array( 'content', 'hide' ), 'write' => true, 'args' => array(), 'returns' => 'job' ),
        'reveal' => array( 'summary' => 'Job: reveals a subtree. POST: node_id, spawn', 'access' => array( 'content', 'hide' ), 'write' => true, 'args' => array(), 'returns' => 'job' ),
        'section' => array( 'summary' => 'Job: assigns a section to a subtree. POST: node_id, section_id, spawn', 'access' => array( 'section', 'assign' ), 'write' => true, 'args' => array(), 'returns' => 'job' ),
        'state' => array( 'summary' => 'Job: assigns a state to a subtree. POST: node_id, state_id, spawn', 'access' => array( 'state', 'assign' ), 'write' => true, 'args' => array(), 'returns' => 'job' ),
        'addLocation' => array( 'summary' => 'Job: adds locations. POST: node_ids, target_node_id, spawn', 'access' => array( 'content', 'manage_locations' ), 'write' => true, 'args' => array(), 'returns' => 'job' ),
        'removeLocation' => array( 'summary' => 'Job: removes locations. POST: node_ids, spawn', 'access' => array( 'content', 'manage_locations' ), 'write' => true, 'args' => array(), 'returns' => 'job' ),
        'cancel' => array( 'summary' => 'Cancels a job (a running job stops after its current batch)', 'access' => array( 'content', 'jobs' ), 'write' => true, 'args' => array( 'job_id' => 'string' ), 'returns' => 'job' ),
        'resume' => array( 'summary' => 'Resumes a failed job or one whose worker died', 'access' => array( 'content', 'jobs' ), 'write' => true, 'args' => array( 'job_id' => 'string' ), 'returns' => 'job' ),
        'spawn' => array( 'summary' => 'Starts the worker of a queued job', 'access' => array( 'content', 'jobs' ), 'write' => true, 'args' => array( 'job_id' => 'string' ), 'returns' => '{spawned}' ),
        'purgeFinished' => array( 'summary' => 'Removes finished jobs older than a number of days (needs setup/administrate)', 'access' => array( 'setup', 'administrate' ), 'write' => true, 'args' => array( 'days' => 'int' ), 'returns' => '{removed}' ),
    );

    protected static $states = array( 'queued', 'running', 'done', 'failed', 'cancelled' );

    protected static function isAdmin()
    {
        $a = eZUser::currentUser()->hasAccessTo( 'setup', 'administrate' );
        return $a['accessWord'] === 'yes';
    }

    /** @return expContentJob a job of the current user (or any job for an administrator) */
    protected static function job( $id )
    {
        if ( !expContentJobStore::validID( (string)$id ) )
            throw new expServiceException( 'Not a job id', 400 );
        $job = expContentJob::fetch( (string)$id );
        if ( !$job )
            throw new expServiceException( "Job $id does not exist", 404 );
        if ( $job->userID() !== (int)eZUser::currentUser()->attribute( 'contentobject_id' ) && !self::isAdmin() )
            throw new expServiceException( 'This job belongs to another user', 403 );
        return $job;
    }

    protected static function stateFilter( $state )
    {
        if ( $state === '' || $state === 'all' )
            return null;
        if ( !in_array( $state, self::$states, true ) )
            throw new expServiceException( 'state is all or one of ' . implode( ', ', self::$states ), 400 );
        return array( $state );
    }

    protected static function exportJob( expContentJob $j, $log = 0 )
    {
        $a = $j->toArray( $log );
        foreach ( array( 'created', 'started', 'finished', 'heartbeat' ) as $k )
            $a[$k . '_iso'] = self::iso( $a[$k] );
        unset( $a['server'], $a['batches'] );
        return $a;
    }

    protected static function listPage( $user, array $args )
    {
        $states = self::stateFilter( self::arg( $args, 0, 'string', 'all' ) );
        $items = array();
        foreach ( expContentJob::listFor( $user, $states ) as $j )
            $items[] = self::exportJob( $j );
        usort( $items, function ( $a, $b ) { return $b['created'] - $a['created']; } );
        return self::pageOf( $items, $args, 1, 2 );
    }

    public static function list( $args )
    {
        static::guard( __FUNCTION__ );
        return self::listPage( eZUser::currentUser(), $args );
    }

    public static function listAll( $args )
    {
        static::guard( __FUNCTION__ );
        return self::listPage( null, $args );
    }

    public static function active( $args )
    {
        static::guard( __FUNCTION__ );
        $out = array();
        foreach ( expContentJob::listFor( eZUser::currentUser(), array( 'queued', 'running' ) ) as $j )
            $out[] = self::exportJob( $j );
        return self::ok( $out );
    }

    public static function summary( $args )
    {
        static::guard( __FUNCTION__ );
        $out = array_fill_keys( self::$states, 0 );
        foreach ( expContentJob::listFor( eZUser::currentUser() ) as $j )
            if ( isset( $out[$j->state()] ) )
                $out[$j->state()]++;
        return self::ok( (object)$out );
    }

    public static function get( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( self::exportJob( self::job( self::arg( $args, 0, 'string' ) ), max( 0, min( 200, self::arg( $args, 1, 'int', 20 ) ) ) ) );
    }

    public static function progress( $args )
    {
        static::guard( __FUNCTION__ );
        $j = self::job( self::arg( $args, 0, 'string' ) );
        return self::ok( array_merge( array( 'state' => $j->state() ), $j->progress() ) );
    }

    public static function log( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( array_values( self::job( self::arg( $args, 0, 'string' ) )->log( max( 1, min( 500, self::arg( $args, 1, 'int', 50 ) ) ) ) ) );
    }

    public static function result( $args )
    {
        static::guard( __FUNCTION__ );
        $j = self::job( self::arg( $args, 0, 'string' ) );
        return self::ok( array( 'state' => $j->state(), 'error' => $j->error(), 'result' => (object)$j->result() ) );
    }

    public static function types( $args )
    {
        static::guard( __FUNCTION__ );
        $params = array( 'remove' => 'node_ids[], move_to_trash', 'copy' => 'source_node_id, destination_node_id, all_versions, keep_creator, keep_time',
                         'move' => 'node_id, new_parent_node_id', 'hide' => 'node_id', 'reveal' => 'node_id', 'section' => 'node_id, section_id',
                         'state' => 'node_id, state_id', 'addlocation' => 'node_ids[], target_node_id', 'removelocation' => 'node_ids[]' );
        $out = array();
        foreach ( expContentJob::types() as $name => $class )
            $out[] = array( 'type' => $name, 'class' => $class, 'params' => isset( $params[$name] ) ? $params[$name] : '' );
        return self::ok( $out );
    }

    public static function settings( $args )
    {
        static::guard( __FUNCTION__ );
        $out = array();
        foreach ( array( 'SynchronousLimit', 'NowLimit', 'BatchSize', 'QueuedTimeout', 'MaxAttempts', 'KeepDays' ) as $k )
            $out[$k] = expContentJob::setting( $k );
        return self::ok( $out );
    }

    public static function estimate( $args )
    {
        static::guard( __FUNCTION__ );
        $type = self::arg( $args, 0, 'string' );
        $params = (array)self::arg( $args, 1, 'json' );
        try
        {
            return self::ok( array( 'nodes' => expContentJob::countNodes( $type, $params ), 'default_mode' => expContentJob::defaultMode( $type, $params ),
                                    'now_allowed' => (bool)expContentJob::nowAllowed( $type, $params ) ) );
        }
        catch ( expContentJobException $e )
        {
            throw new expServiceException( $e->getMessage(), 422 );
        }
    }

    public static function lockOf( $args )
    {
        static::guard( __FUNCTION__ );
        $n = self::node( self::arg( $args, 0, 'int' ) );
        $holder = expContentJobLock::check( $n->attribute( 'path_string' ) );
        return self::ok( array( 'locked' => (bool)$holder, 'job_id' => $holder ? $holder->id() : null ) );
    }

    // ------------------------------------------------------------------ writes

    protected static function start( $type, array $params )
    {
        $spawn = self::post( 'spawn', 'bool', true );
        $params['mode'] = 'job';
        try
        {
            $job = expContentJob::create( $type, $params, eZUser::currentUser() );
        }
        catch ( expContentJobException $e )
        {
            throw new expServiceException( $e->getMessage(), $e->getCode() == 409 ? 409 : 403 );
        }
        $spawned = $spawn ? (bool)$job->spawn() : false;
        $out = self::exportJob( expContentJob::fetch( $job->id() ) );
        $out['spawned'] = $spawned;
        return self::ok( $out );
    }

    public static function create( $args )
    {
        static::guard( __FUNCTION__ );
        $type = self::arg( $args, 0, 'string' );
        if ( !isset( expContentJob::types()[$type] ) )
            throw new expServiceException( "Unknown job type '$type', see expcontentjob::types", 404 );
        return self::start( $type, (array)self::post( 'params', 'json' ) );
    }

    public static function remove( $args )
    {
        static::guard( __FUNCTION__ );
        return self::start( 'remove', array( 'node_ids' => array_map( 'intval', self::post( 'node_ids', 'list' ) ), 'move_to_trash' => self::post( 'move_to_trash', 'bool', true ) ) );
    }

    public static function copy( $args )
    {
        static::guard( __FUNCTION__ );
        return self::start( 'copy', array( 'source_node_id' => self::post( 'source_node_id', 'int' ), 'destination_node_id' => self::post( 'destination_node_id', 'int' ),
                                           'all_versions' => self::post( 'all_versions', 'bool', false ), 'keep_creator' => self::post( 'keep_creator', 'bool', false ),
                                           'keep_time' => self::post( 'keep_time', 'bool', false ) ) );
    }

    public static function move( $args )
    {
        static::guard( __FUNCTION__ );
        return self::start( 'move', array( 'node_id' => self::post( 'node_id', 'int' ), 'new_parent_node_id' => self::post( 'new_parent_node_id', 'int' ) ) );
    }

    public static function hide( $args )
    {
        static::guard( __FUNCTION__ );
        return self::start( 'hide', array( 'node_id' => self::post( 'node_id', 'int' ) ) );
    }

    public static function reveal( $args )
    {
        static::guard( __FUNCTION__ );
        return self::start( 'reveal', array( 'node_id' => self::post( 'node_id', 'int' ) ) );
    }

    public static function section( $args )
    {
        static::guard( __FUNCTION__ );
        return self::start( 'section', array( 'node_id' => self::post( 'node_id', 'int' ), 'section_id' => self::post( 'section_id', 'int' ) ) );
    }

    public static function state( $args )
    {
        static::guard( __FUNCTION__ );
        return self::start( 'state', array( 'node_id' => self::post( 'node_id', 'int' ), 'state_id' => self::post( 'state_id', 'int' ) ) );
    }

    public static function addLocation( $args )
    {
        static::guard( __FUNCTION__ );
        return self::start( 'addlocation', array( 'node_ids' => array_map( 'intval', self::post( 'node_ids', 'list' ) ), 'target_node_id' => self::post( 'target_node_id', 'int' ) ) );
    }

    public static function removeLocation( $args )
    {
        static::guard( __FUNCTION__ );
        return self::start( 'removelocation', array( 'node_ids' => array_map( 'intval', self::post( 'node_ids', 'list' ) ) ) );
    }

    public static function cancel( $args )
    {
        static::guard( __FUNCTION__ );
        $j = self::job( self::arg( $args, 0, 'string' ) );
        if ( !$j->canCancel() )
            throw new expServiceException( 'The job is ' . $j->state() . ' and cannot be cancelled', 409 );
        $j->cancel();
        return self::ok( self::exportJob( expContentJob::fetch( $j->id() ) ) );
    }

    public static function resume( $args )
    {
        static::guard( __FUNCTION__ );
        $j = self::job( self::arg( $args, 0, 'string' ) );
        if ( !$j->canResume() )
            throw new expServiceException( 'The job is ' . $j->state() . ' and cannot be resumed', 409 );
        try
        {
            $j->resume();
        }
        catch ( expContentJobException $e )
        {
            throw new expServiceException( $e->getMessage(), 409 );
        }
        return self::ok( self::exportJob( expContentJob::fetch( $j->id() ) ) );
    }

    public static function spawn( $args )
    {
        static::guard( __FUNCTION__ );
        $j = self::job( self::arg( $args, 0, 'string' ) );
        if ( $j->state() !== 'queued' )
            throw new expServiceException( 'Only a queued job can be started, this one is ' . $j->state(), 409 );
        return self::ok( array( 'spawned' => (bool)$j->spawn(), 'job_id' => $j->id() ) );
    }

    public static function purgeFinished( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( array( 'removed' => (int)expContentJob::purgeFinished( max( 1, self::arg( $args, 0, 'int', 30 ) ) ) ) );
    }
}
