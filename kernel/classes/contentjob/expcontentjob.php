<?php
/**
 * File containing the expContentJob class.
 *
 * A content job: a large subtree remove or copy that runs in batches in a background worker instead of in the
 * web request, so it never fails from a request time limit, a memory limit or one huge transaction. Small
 * operations stay synchronous (below content.ini [ContentJobSettings] SynchronousLimit nodes).
 *
 *   if ( expContentJob::shouldRunAsJob( 'remove', $params ) )
 *   {
 *       $job = expContentJob::create( 'remove', array( 'node_ids' => $ids, 'move_to_trash' => true ), $user );
 *       $job->spawn();                               // the detached worker: bin/php/expcontentjob.php run <id>
 *   }
 *   $job = expContentJob::fetch( $id );
 *   $job->state(); $job->progress(); $job->log( 50 ); $job->cancel(); $job->resume();
 *
 * create() validates the parameters, checks the user's permissions for the whole operation and the subtree
 * locks, and writes the job; it throws expContentJobException with a message for the user when it refuses.
 * The worker (expContentJobWorker) runs the batches as the requesting user; the cronjob part 'contentjobs'
 * resumes a job whose worker died, so a job finishes even when the spawn fails.
 *
 * States: queued, running, done, failed (with the error; resumable), cancelled.
 *
 * The job types are registered in content.ini [ContentJobSettings] JobTypes[<name>]=<class implementing
 * expContentJobType>: remove (expContentJobRemoveSubtree) and copy (expContentJobCopySubtree).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentJob
{
    const STATE_QUEUED = 'queued';
    const STATE_RUNNING = 'running';
    const STATE_DONE = 'done';
    const STATE_FAILED = 'failed';
    const STATE_CANCELLED = 'cancelled';

    /** @var array|null settings used instead of content.ini (tests) */
    public static $settings = null;

    /** @var array the defaults of [ContentJobSettings] */
    public static $defaults = array(
        'SynchronousLimit' => 50,
        'BatchSize' => 50,
        'BatchPause' => 0,
        'QueuedGrace' => 60,
        'QueuedTimeout' => 600,
        'MaxAttempts' => 5,
        'KeepDays' => 30,
        'PhpBinary' => '',
        'NowLimit' => 1000,
        'JobTypes' => array( 'remove' => 'expContentJobRemoveSubtree', 'copy' => 'expContentJobCopySubtree', 'move' => 'expContentJobMoveSubtree',
                             'hide' => 'expContentJobHideSubtree', 'reveal' => 'expContentJobRevealSubtree', 'section' => 'expContentJobSectionSubtree',
                             'state' => 'expContentJobStateSubtree', 'addlocation' => 'expContentJobAddLocation', 'removelocation' => 'expContentJobRemoveLocation' ),
    );

    /** @var array the job's data, as stored */
    protected $data;

    /**
     * @param array $data
     */
    public function __construct( array $data )
    {
        $this->data = $data + array( 'checkpoint' => array(), 'result' => array(), 'progress' => array() );
    }

    // ---- settings and types ------------------------------------------------------------------

    /**
     * A setting of content.ini [ContentJobSettings], or its default.
     *
     * @param string $name
     * @return mixed
     */
    public static function setting( $name )
    {
        if ( is_array( self::$settings ) )
            return array_key_exists( $name, self::$settings ) ? self::$settings[$name] : self::$defaults[$name];
        $default = isset( self::$defaults[$name] ) ? self::$defaults[$name] : null;
        if ( !class_exists( 'eZINI' ) )
            return $default;
        $ini = eZINI::instance( 'content.ini' );
        if ( !$ini->hasVariable( 'ContentJobSettings', $name ) )
            return $default;
        $value = $ini->variable( 'ContentJobSettings', $name );
        if ( is_array( $default ) )
            return is_array( $value ) && $value ? $value : $default;
        if ( is_int( $default ) )
            return is_numeric( $value ) ? (int) $value : $default;
        return $value;
    }

    /**
     * The registered job types: name => class.
     *
     * @return array
     */
    public static function types()
    {
        $types = array();
        foreach ( (array) self::setting( 'JobTypes' ) as $name => $class )
        {
            if ( is_string( $name ) && preg_match( '/^[a-z][a-z0-9_]*$/', $name ) && is_string( $class ) && $class !== '' )
                $types[$name] = $class;
        }
        return $types;
    }

    /**
     * The handler of a job type.
     *
     * @param string $type
     * @return expContentJobType
     * @throws expContentJobException for an unknown type or a class that is not a job type
     */
    public static function handler( $type )
    {
        $types = self::types();
        if ( !isset( $types[$type] ) )
            throw new expContentJobException( "Unknown content job type '$type'." );
        $class = $types[$type];
        if ( !class_exists( $class ) )
            throw new expContentJobException( "The class $class of content job type '$type' does not exist." );
        $handler = new $class();
        if ( !$handler instanceof expContentJobType )
            throw new expContentJobException( "The class $class of content job type '$type' does not implement expContentJobType." );
        return $handler;
    }

    // ---- creating and finding ----------------------------------------------------------------

    /**
     * Validates the operation, checks the permissions and the locks, and writes the job (queued). Does not
     * start the worker: call spawn().
     *
     * @param string $type 'remove', 'copy' or another registered type
     * @param array $params see the type
     * @param eZUser $user the requesting user; the worker runs the operation as this user
     * @return expContentJob
     * @throws expContentJobException with a message for the user when refused
     */
    public static function create( $type, array $params, eZUser $user )
    {
        $handler = self::handler( $type );
        // the now-or-job choice is the caller's, not the type's: kept with the job, not handed to the type
        $mode = isset( $params['mode'] ) && in_array( $params['mode'], array( 'job', 'now', 'auto' ), true ) ? $params['mode'] : 'auto';
        unset( $params['mode'] );
        $normalized = $handler->validate( $params, $user );
        $total = (int) $handler->countNodes( $normalized );
        $locks = $handler->locks( $normalized );
        $siteaccess = '';
        if ( isset( $GLOBALS['eZCurrentAccess']['name'] ) )
            $siteaccess = (string) $GLOBALS['eZCurrentAccess']['name'];
        $now = time();
        $data = array(
            'id' => expContentJobStore::newID(),
            'type' => $type,
            'state' => self::STATE_QUEUED,
            'params' => $normalized,
            'description' => $handler->describe( $normalized ),
            'user_id' => (int) $user->attribute( 'contentobject_id' ),
            'siteaccess' => preg_match( '/^[A-Za-z0-9_-]+$/', $siteaccess ) ? $siteaccess : '',
            'created' => $now,
            'started' => 0,
            'finished' => 0,
            'heartbeat' => $now,
            'attempts' => 0,
            'stalls' => 0,
            'error' => '',
            'cancel_requested' => false,
            'progress' => array( 'done' => 0, 'total' => $total, 'batch' => 0, 'phase' => '', 'message' => '' ),
            'checkpoint' => array(),
            'result' => array(),
            'worker' => array(),
            'worker_pid' => 0,
            'mode' => $mode,
            'created_by' => self::currentUserID(),
            'server' => array( 'created' => self::serverInfo() ),
            'batches' => array(),
            'cancelled_by' => null,
            'cancelled_at' => 0,
            'resumed' => array(),
            'error_node_id' => 0,
            'locks' => $locks,
        );
        $id = $data['id'];
        expContentJobLock::acquire( $id, $locks, function () use ( $id, $data ) {
            expContentJobStore::write( $id, $data );
        } );
        $job = new self( $data );
        $job->appendLog( 'queued: ' . $data['description'] . ' (user ' . $data['user_id'] . ', ' . $total . ' nodes)' );
        return $job;
    }

    /**
     * Where this process runs: apache-fpm (PHP-FPM behind Apache), velocity (Exponential Velocity: the CLI SAPI
     * answering a web request), cli (a command or the cronjob), with the host name and the pid.
     *
     * @return array( 'server' => string, 'sapi' => string, 'host' => string, 'pid' => int )
     */
    public static function serverInfo()
    {
        if ( PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg' )
            $server = ( !empty( $_SERVER['REQUEST_METHOD'] ) && !empty( $_SERVER['HTTP_HOST'] ) ) ? 'velocity' : 'cli';
        else if ( strpos( PHP_SAPI, 'fpm' ) !== false )
            $server = 'apache-fpm';
        else
            $server = PHP_SAPI;
        return array( 'server' => $server, 'sapi' => PHP_SAPI, 'host' => php_uname( 'n' ), 'pid' => getmypid() );
    }

    /**
     * The eZ user id of the current request or command (the anonymous user for the cronjob); 0 when settings
     * are handed in (tests, no kernel).
     *
     * @return int
     */
    public static function currentUserID()
    {
        if ( is_array( self::$settings ) || !class_exists( 'eZUser' ) )
            return 0;
        try
        {
            return (int) eZUser::currentUserID();
        }
        catch ( Throwable $e )
        {
            return 0;
        }
    }

    /**
     * @param string $id
     * @return expContentJob|null
     */
    public static function fetch( $id )
    {
        $data = expContentJobStore::read( (string) $id );
        if ( $data && self::expireUnstarted( $data ) )
            $data = expContentJobStore::read( (string) $id );
        return $data ? new self( $data ) : null;
    }

    /**
     * The jobs of a user (all jobs for null), newest first, optionally only in some states.
     *
     * @param eZUser|null $user
     * @param string[]|string|null $states
     * @return expContentJob[]
     */
    public static function listFor( ?eZUser $user = null, $states = null )
    {
        $userID = $user ? (int) $user->attribute( 'contentobject_id' ) : null;
        $states = $states === null ? null : (array) $states;
        $jobs = array();
        foreach ( expContentJobStore::ids() as $id )
        {
            $data = expContentJobStore::read( $id );
            if ( !$data )
                continue;
            if ( self::expireUnstarted( $data ) )
                $data = expContentJobStore::read( $id );
            if ( $userID !== null && (int) $data['user_id'] !== $userID )
                continue;
            if ( $states !== null && !in_array( $data['state'], $states, true ) )
                continue;
            $jobs[] = new self( $data );
        }
        return $jobs;
    }

    /**
     * How many nodes the operation touches.
     *
     * @param string $type
     * @param array $params
     * @return int
     */
    public static function countNodes( $type, array $params )
    {
        return (int) self::handler( $type )->countNodes( $params );
    }

    /**
     * Whether the operation runs as a job. The user's choice in $params['mode'] decides: 'job' always, 'now'
     * never (refused above NowLimit nodes); 'auto' or no mode: defaultMode().
     *
     * @param string $type
     * @param array $params
     * @return bool
     * @throws expContentJobException for 'now' above NowLimit (the message says so)
     */
    public static function shouldRunAsJob( $type, array $params )
    {
        $mode = isset( $params['mode'] ) ? (string) $params['mode'] : 'auto';
        if ( $mode === 'job' )
            return true;
        if ( $mode === 'now' )
        {
            self::checkNow( $type, $params );
            return false;
        }
        return self::defaultMode( $type, $params ) === 'job';
    }

    /**
     * The choice offered first: 'job' from SynchronousLimit nodes on (or always with a limit of 0), else 'now'.
     *
     * @param string $type
     * @param array $params
     * @return string 'job' or 'now'
     */
    public static function defaultMode( $type, array $params )
    {
        $limit = (int) self::setting( 'SynchronousLimit' );
        if ( $limit <= 0 )
            return 'job';
        return self::countNodes( $type, $params ) >= $limit ? 'job' : 'now';
    }

    /**
     * Whether running the operation now (in the request) is allowed: not above NowLimit nodes (0 = no ceiling).
     *
     * @param string $type
     * @param array $params
     * @return bool
     */
    public static function nowAllowed( $type, array $params )
    {
        $ceiling = (int) self::setting( 'NowLimit' );
        return $ceiling <= 0 || self::countNodes( $type, $params ) <= $ceiling;
    }

    /**
     * Refuses running the operation now above NowLimit nodes.
     *
     * @param string $type
     * @param array $params
     * @throws expContentJobException
     */
    public static function checkNow( $type, array $params )
    {
        if ( !self::nowAllowed( $type, $params ) )
        {
            $text = 'This operation touches %1 nodes, more than the %2 that can be done at once in the request. Run it as a background job.';
            $args = array( self::countNodes( $type, $params ), (int) self::setting( 'NowLimit' ) );
            if ( !is_array( self::$settings ) && class_exists( 'ezpI18n' ) )
                $text = ezpI18n::tr( 'kernel/contentjob', $text, null, $args );
            else
                $text = str_replace( array( '%1', '%2' ), $args, $text );
            throw new expContentJobException( $text, 413 );
        }
    }

    // ---- reading -----------------------------------------------------------------------------

    public function id() { return $this->data['id']; }
    public function type() { return $this->data['type']; }
    public function state() { return $this->data['state']; }
    public function params() { return $this->data['params']; }
    public function description() { return isset( $this->data['description'] ) ? $this->data['description'] : ''; }
    public function userID() { return (int) $this->data['user_id']; }
    public function siteaccess() { return isset( $this->data['siteaccess'] ) ? $this->data['siteaccess'] : ''; }
    public function error() { return (string) $this->data['error']; }
    public function created() { return (int) $this->data['created']; }
    public function started() { return (int) $this->data['started']; }
    public function finished() { return (int) $this->data['finished']; }
    public function heartbeat() { return (int) $this->data['heartbeat']; }
    public function attempts() { return (int) $this->data['attempts']; }
    public function result() { return (array) $this->data['result']; }
    public function cancelRequested() { return !empty( $this->data['cancel_requested'] ); }
    public function isActive() { return in_array( $this->data['state'], array( self::STATE_QUEUED, self::STATE_RUNNING ), true ); }

    /**
     * The progress: done, total, percent, batch (batches done), message, phase.
     *
     * @return array
     */
    public function progress()
    {
        $p = (array) $this->data['progress'] + array( 'done' => 0, 'total' => 0, 'batch' => 0, 'phase' => '', 'message' => '' );
        $done = (int) $p['done'];
        $total = (int) $p['total'];
        if ( $this->data['state'] === self::STATE_DONE )
            $percent = 100;
        else if ( isset( $p['percent'] ) )
            $percent = (int) $p['percent'];
        else
            $percent = $total > 0 ? (int) floor( 100 * min( $done, $total ) / $total ) : 0;
        return array( 'done' => $done, 'total' => $total, 'percent' => max( 0, min( 100, $percent ) ),
                      'batch' => (int) $p['batch'], 'batches' => (int) $p['batch'], 'message' => (string) $p['message'],
                      'phase' => (string) $p['phase'] );
    }

    /**
     * The last lines of the worker log.
     *
     * @param int $lines
     * @return string[]
     */
    public function log( $lines = 50 )
    {
        return expContentJobStore::lines( $this->id(), '.log', max( 1, (int) $lines ) );
    }

    /**
     * Whether a worker holds the job's run lock right now.
     *
     * @return bool
     */
    public function workerAlive()
    {
        $file = expContentJobStore::path( $this->id(), '.run.lock' );
        if ( !is_file( $file ) )
            return false;
        $fh = @fopen( $file, 'r' );
        if ( !$fh )
            return false;
        $free = flock( $fh, LOCK_EX | LOCK_NB );
        if ( $free )
            flock( $fh, LOCK_UN );
        fclose( $fh );
        return !$free;
    }

    /**
     * A failed job, or a queued or running one without a worker (it died, or the spawn failed).
     *
     * @return bool
     */
    public function canResume()
    {
        if ( $this->data['state'] === self::STATE_FAILED )
            return true;
        return $this->isActive() && !$this->workerAlive();
    }

    /** @return bool */
    public function canCancel()
    {
        return in_array( $this->data['state'], array( self::STATE_QUEUED, self::STATE_RUNNING, self::STATE_FAILED ), true )
            && !$this->cancelRequested();
    }

    /**
     * Whether the cronjob part should start this job: running without a worker, or queued without a worker
     * for longer than QueuedGrace seconds (the spawn failed).
     *
     * @return bool
     */
    public function isStale()
    {
        if ( !$this->isActive() || $this->workerAlive() )
            return false;
        if ( $this->data['state'] === self::STATE_RUNNING )
            return true;
        return $this->heartbeat() < time() - (int) self::setting( 'QueuedGrace' );
    }

    /**
     * Everything for a JSON status answer (no checkpoint data).
     *
     * @param int $logLines
     * @return array
     */
    public function toArray( $logLines = 0 )
    {
        $a = array(
            'id' => $this->id(), 'type' => $this->type(), 'state' => $this->state(), 'description' => $this->description(),
            'params' => $this->params(), 'user_id' => $this->userID(), 'error' => $this->error(),
            'created' => $this->created(), 'started' => $this->started(), 'finished' => $this->finished(),
            'heartbeat' => $this->heartbeat(), 'attempts' => $this->attempts(), 'progress' => $this->progress(),
            'result' => $this->result(), 'cancel_requested' => $this->cancelRequested(),
            'can_cancel' => $this->canCancel(), 'can_resume' => $this->canResume(),
        );
        // what part B's job pages show: where it runs, who did what, the batches
        foreach ( array( 'mode' => 'auto', 'created_by' => 0, 'server' => array(), 'worker_pid' => 0, 'batches' => array(),
                         'cancelled_by' => null, 'cancelled_at' => 0, 'resumed' => array(), 'error_node_id' => 0, 'last_batch' => null ) as $key => $default )
            $a[$key] = isset( $this->data[$key] ) ? $this->data[$key] : $default;
        if ( $logLines > 0 )
            $a['log'] = $this->log( $logLines );
        return $a;
    }

    // ---- acting ------------------------------------------------------------------------------

    /**
     * Cancels the job: a queued or failed job (or one whose worker died) at once, a running job after its
     * current batch. A cancelled remove leaves what was not removed yet; a cancelled copy leaves the partial
     * copy (result()['new_root_node_id']).
     *
     * @return bool whether the job is cancelled or will be
     */
    public function cancel()
    {
        $alive = $this->workerAlive();
        $now = time();
        $released = false;
        $changed = false;
        $by = self::currentUserID();
        $data = expContentJobStore::update( $this->id(), function ( array &$d ) use ( $alive, $now, $by, &$released, &$changed ) {
            if ( !in_array( $d['state'], array( self::STATE_QUEUED, self::STATE_RUNNING, self::STATE_FAILED ), true ) )
                return false;
            $changed = true;
            $d['cancelled_by'] = $by;
            $d['cancelled_at'] = $now;
            if ( $d['state'] === self::STATE_RUNNING && $alive )
            {
                $d['cancel_requested'] = true;
                return true;
            }
            $d['state'] = self::STATE_CANCELLED;
            $d['cancel_requested'] = true;
            $d['finished'] = $now;
            $released = true;
            return true;
        } );
        if ( !$data || !$changed )
            return false;
        $this->data = $data + $this->data;
        if ( $released )
        {
            expContentJobLock::release( $this->id() );
            $this->appendLog( 'cancelled' );
        }
        else if ( !empty( $data['cancel_requested'] ) )
            $this->appendLog( 'cancel requested: the worker stops after the current batch' );
        return $data['state'] === self::STATE_CANCELLED || !empty( $data['cancel_requested'] );
    }

    /**
     * Puts a failed job (or one whose worker died) back in the queue and starts a worker.
     *
     * @param bool $spawn false: only queue it (the caller runs the worker itself)
     * @return bool whether the job was queued again
     */
    public function resume( $spawn = true )
    {
        if ( !$this->canResume() )
            return false;
        // a job whose worker could not be started gave its locks back: it takes them again, or stays failed
        if ( $this->locks() && !expContentJobLock::locksOf( $this->id() ) )
        {
            try
            {
                expContentJobLock::acquire( $this->id(), $this->locks() );
            }
            catch ( expContentJobException $e )
            {
                $this->appendLog( 'not resumed: ' . $e->getMessage() );
                return false;
            }
        }
        $now = time();
        $by = self::currentUserID();
        $data = expContentJobStore::update( $this->id(), function ( array &$d ) use ( $now, $by ) {
            if ( $d['state'] === self::STATE_DONE || $d['state'] === self::STATE_CANCELLED )
                return false;
            if ( $d['state'] === self::STATE_FAILED )
                $d['stalls'] = 0;
            $resumed = isset( $d['resumed'] ) ? (array) $d['resumed'] : array();
            $resumed[] = array( 'by' => $by, 'at' => $now, 'was' => $d['state'] );
            $d['resumed'] = array_slice( $resumed, -50 );
            $d['state'] = self::STATE_QUEUED;
            $d['error'] = '';
            $d['heartbeat'] = $now;
            return true;
        } );
        if ( !$data || $data['state'] !== self::STATE_QUEUED )
            return false;
        $this->data = $data;
        $this->appendLog( 'resumed' );
        if ( $spawn )
            $this->spawn();
        return true;
    }

    /**
     * Starts the detached worker: php bin/php/expcontentjob.php run <id> [-s <siteaccess>] in a session of its own
     * (setsid -f), its output appended to <id>.out. As root (Velocity) the worker becomes the site user before it
     * does anything.
     *
     * Started through pipes only, with a small shell that closes every descriptor above 2 it inherited and does
     * the redirections itself: inside Exponential Velocity file:// is a user-space stream wrapper, so proc_open()
     * cannot hand a ('file', ...) descriptor to a child, and a persistent server worker holds its listening sockets
     * and client connections, which the job must not inherit. The shell's error output is read for a moment, so a
     * start that fails says why.
     *
     * When the worker cannot be started the job does not wait for a scheduler that may not exist: it fails at once
     * with the reason (resumable), and a job that has not done a batch yet gives back its locks.
     *
     * @return bool whether the worker was started
     */
    public function spawn()
    {
        if ( !$this->isActive() )
            return false;
        $error = $this->startWorker();
        if ( $error === null )
            return true;
        $this->failUnstarted( $error );
        return false;
    }

    /**
     * Starts the worker process.
     *
     * @return string|null null when started, else why not
     */
    protected function startWorker()
    {
        $disabled = array_map( 'trim', explode( ',', (string) ini_get( 'disable_functions' ) ) );
        if ( !function_exists( 'proc_open' ) || in_array( 'proc_open', $disabled, true ) )
            return 'proc_open() is disabled in this PHP (disable_functions)';
        $argv = self::$spawnArgv ? call_user_func( self::$spawnArgv, $this ) : self::workerArgv( $this->id(), $this->siteaccess() );
        if ( !self::$spawnArgv && !is_executable( $argv[0] ) )
            return 'the PHP command line binary ' . $argv[0] . ' is not executable (set content.ini [ContentJobSettings] PhpBinary)';
        $out = expContentJobStore::path( $this->id(), '.out' );
        if ( !is_file( $out ) )
        {
            @touch( $out );
            @chmod( $out, 0660 );
            expContentJobStore::fixOwner( $out );
        }
        $setsid = is_executable( '/usr/bin/setsid' ) ? '/usr/bin/setsid' : ( is_executable( '/bin/setsid' ) ? '/bin/setsid' : '' );
        $shell = is_executable( '/bin/bash' ) ? '/bin/bash' : '/bin/sh';
        // $1 the output file, the rest the command: close what the parent left open above 2, then start the
        // command detached with its own stdin/stdout/stderr; the shell's own errors go to the pipe we read
        $script = 'out=$1; shift; '
                // setsid -f returns before the program runs: check it can be run, so a wrong binary is reported here
                . 'if ! command -v "$1" >/dev/null 2>&1; then echo "cannot run $1: not found or not executable" >&2; exit 127; fi; '
                . 'if ! : >> "$out"; then echo "cannot write $out" >&2; exit 1; fi; '
                . 'for f in /proc/$$/fd/*; do n=${f##*/}; case $n in 0|1|2) ;; *[!0-9]*) ;; *) eval "exec $n>&-" 2>/dev/null ;; esac; done; '
                . ( $setsid !== '' ? 'exec ' . $setsid . ' -f "$@" < /dev/null >> "$out" 2>&1'
                                   : '"$@" < /dev/null >> "$out" 2>&1 & exit 0' );
        $cmd = array_merge( array( $shell, '-c', $script, 'expcontentjob-spawn', $out ), $argv );
        $pipes = array();
        $proc = @proc_open( $cmd, array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ),
                            $pipes, self::rootDir() );
        if ( !is_resource( $proc ) )
        {
            $last = error_get_last();
            return 'proc_open() failed' . ( $last ? ': ' . $last['message'] : '' );
        }
        fclose( $pipes[0] );
        // the shell's error output, until it has started the worker (a second at most)
        $err = '';
        stream_set_blocking( $pipes[2], false );
        $until = microtime( true ) + 2;
        while ( !feof( $pipes[2] ) && microtime( true ) < $until )
        {
            $read = array( $pipes[2] );
            $w = $e = null;
            if ( @stream_select( $read, $w, $e, 0, 200000 ) )
            {
                $chunk = fread( $pipes[2], 8192 );
                if ( $chunk === '' || $chunk === false )
                    break;
                $err .= $chunk;
            }
        }
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        $code = proc_close( $proc );
        if ( $code !== 0 )
            return 'the start of the worker failed with exit code ' . $code . ( trim( $err ) !== '' ? ': ' . trim( $err ) : '' );
        return null;
    }

    /**
     * The worker could not be started: the job fails at once with the reason instead of waiting for a
     * scheduler. A job that has not done a batch yet gives back its locks (resume() takes them again).
     *
     * @param string $reason
     */
    protected function failUnstarted( $reason )
    {
        $now = time();
        $release = false;
        $message = 'The background worker could not be started: ' . $reason . '. Resume the job to try again.';
        $data = expContentJobStore::update( $this->id(), function ( array &$d ) use ( $now, $message, &$release ) {
            if ( $d['state'] !== self::STATE_QUEUED )
                return false;
            $d['state'] = self::STATE_FAILED;
            $d['error'] = $message;
            $d['finished'] = $now;
            $release = empty( $d['progress']['batch'] );
            return true;
        } );
        if ( $data )
            $this->data = $data;
        if ( $release )
            expContentJobLock::release( $this->id() );
        $this->appendLog( 'the worker could not be started: ' . $reason . ( $release ? ' (nothing was changed; the locks are given back)' : '' ) );
    }

    /**
     * A queued job that no worker started within QueuedTimeout seconds (no scheduler, the spawn lost) fails and
     * gives back its locks if it has not done a batch, so it never blocks a subtree for ever. Called whenever the
     * locks or the job lists are read. The cronjob part, where it runs, starts such jobs well before
     * (QueuedGrace).
     *
     * @param array $d the job's data
     * @return bool whether the job was expired now
     */
    public static function expireUnstarted( array $d )
    {
        if ( !self::unstartedTooLong( $d ) )
            return false;
        $job = new self( $d );
        $job->failUnstarted( 'no worker started it within ' . (int) self::setting( 'QueuedTimeout' ) . ' seconds' );
        return $job->state() === self::STATE_FAILED;
    }

    /**
     * Whether a job is queued without a worker for longer than QueuedTimeout (no side effects).
     *
     * @param array $d the job's data
     * @return bool
     */
    public static function unstartedTooLong( array $d )
    {
        $timeout = (int) self::setting( 'QueuedTimeout' );
        if ( $timeout <= 0 || $d['state'] !== self::STATE_QUEUED || (int) $d['heartbeat'] >= time() - $timeout )
            return false;
        return !( new self( $d ) )->workerAlive();
    }

    /**
     * The locks the job took when it was created (resume() takes them again after they were given back).
     *
     * @return array
     */
    public function locks()
    {
        return isset( $this->data['locks'] ) ? (array) $this->data['locks'] : array();
    }

    /** @var callable|null builds the spawned command instead of workerArgv() (tests) */
    public static $spawnArgv = null;

    /**
     * The installation's directory (the worker's working directory).
     *
     * @return string
     */
    public static function rootDir()
    {
        if ( class_exists( 'eZSys' ) && !is_array( self::$settings ) )
            return eZSys::rootDir();
        return dirname( __DIR__, 2 );
    }

    /**
     * The worker's argument list (without the detaching).
     *
     * @param string $id
     * @param string $siteaccess
     * @return string[]
     */
    public static function workerArgv( $id, $siteaccess = '' )
    {
        $php = (string) self::setting( 'PhpBinary' );
        if ( $php === '' || !is_executable( $php ) )
            $php = is_file( PHP_BINDIR . '/php' ) ? PHP_BINDIR . '/php' : 'php';
        $argv = array( $php, 'bin/php/expcontentjob.php', 'run', (string) $id, '-q' );
        if ( $siteaccess !== '' )
            array_push( $argv, '-s', $siteaccess );
        return $argv;
    }

    /**
     * The worker's command line, for display.
     *
     * @param string $id
     * @param string $siteaccess
     * @return string
     */
    public static function workerCommand( $id, $siteaccess = '' )
    {
        return implode( ' ', array_map( 'escapeshellarg', self::workerArgv( $id, $siteaccess ) ) );
    }

    // ---- for the worker and the types --------------------------------------------------------

    /**
     * The type's checkpoint, by reference: what it keeps between batches.
     *
     * @return array
     */
    public function &checkpoint()
    {
        if ( !isset( $this->data['checkpoint'] ) || !is_array( $this->data['checkpoint'] ) )
            $this->data['checkpoint'] = array();
        return $this->data['checkpoint'];
    }

    /**
     * Sets a value of the result.
     *
     * @param string $key
     * @param mixed $value
     */
    public function setResult( $key, $value )
    {
        $this->data['result'][$key] = $value;
    }

    /**
     * Changes the progress (keys done, total, phase, message, percent).
     *
     * @param array $progress
     */
    public function setProgress( array $progress )
    {
        $this->data['progress'] = $progress + (array) $this->data['progress'];
    }

    /**
     * Sets a field of the job's data (worker use).
     *
     * @param string $key
     * @param mixed $value
     */
    public function set( $key, $value )
    {
        $this->data[$key] = $value;
    }

    /**
     * A field of the job's data.
     *
     * @param string $key
     * @return mixed
     */
    public function get( $key )
    {
        return isset( $this->data[$key] ) ? $this->data[$key] : null;
    }

    /**
     * Writes the job (the worker's checkpoint). A cancel requested meanwhile on disk is kept.
     */
    public function save()
    {
        $mine = $this->data;
        $data = expContentJobStore::update( $this->id(), function ( array &$d ) use ( $mine ) {
            $cancel = !empty( $d['cancel_requested'] );
            // a cancel of a dead worker finished the job on disk: keep that
            if ( $d['state'] === self::STATE_CANCELLED && $mine['state'] !== self::STATE_CANCELLED )
                $mine['state'] = self::STATE_CANCELLED;
            $d = $mine;
            $d['cancel_requested'] = $cancel || !empty( $mine['cancel_requested'] );
            return true;
        } );
        if ( $data )
            $this->data = $data;
    }

    /**
     * Re-reads the job from the store (the worker, between batches, to see a cancel).
     */
    public function reload()
    {
        $data = expContentJobStore::read( $this->id() );
        if ( $data )
            $this->data = $data;
    }

    /**
     * Adds a line to the job's log.
     *
     * @param string $line
     */
    public function appendLog( $line )
    {
        $lines = array();
        foreach ( preg_split( '/\r?\n/', (string) $line ) as $l )
            $lines[] = '[' . date( 'Y-m-d H:i:s' ) . '] ' . $l;
        try
        {
            expContentJobStore::append( $this->id(), '.log', $lines );
        }
        catch ( expContentJobException $e )
        {
            // a log line is never worth failing the job for
        }
    }

    // ---- housekeeping ------------------------------------------------------------------------

    /**
     * Removes finished (done or cancelled) jobs older than $days days, with their files.
     *
     * @param int|null $days null: KeepDays
     * @return int the number of jobs removed
     */
    public static function purgeFinished( $days = null )
    {
        $days = $days === null ? (int) self::setting( 'KeepDays' ) : (int) $days;
        if ( $days <= 0 )
            return 0;
        $limit = time() - $days * 86400;
        $removed = 0;
        foreach ( expContentJobStore::ids() as $id )
        {
            $data = expContentJobStore::read( $id );
            if ( $data && in_array( $data['state'], array( self::STATE_DONE, self::STATE_CANCELLED ), true )
                 && (int) $data['finished'] > 0 && (int) $data['finished'] < $limit )
            {
                expContentJobStore::remove( $id );
                $removed++;
            }
        }
        return $removed;
    }
}
