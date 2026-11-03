<?php
/**
 * File containing the expMigrationJob class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A migration run started from the administration, in the background (decision 15b of #287): a console process of
 * its own (exp:migration:migrate, :resume or :rollback with --apply), detached from the request, so a long run never
 * holds a web request open and goes on when the page is closed. The page follows it by its id (setup's
 * cronjobsstream pattern): the module view migration/stream reads its progress file and its run log as they grow,
 * and a reload finds it again by the id in the address.
 *
 * Files of a job, in var/<site>/migration/jobs/:
 *
 *   <id>.json      the job: id, kind, target, user, siteaccess, pid, started (a Unix time), progress (the file below),
 *                  arguments (the command line without the php binary; never a secret)
 *   <id>.progress  the progress events (expMigrationProgress), written by the run
 *   <id>.out       what the command printed: its exp-migration/1 report as JSON once it has ended
 *   <id>.err       what it printed as errors
 *   <id>.pid       the process id, written by the shell before it runs php (the cronjob runner's way: the pid of
 *                  the job, not of a shell that has already ended)
 *
 * The id of a job is a run id (expMigrationContext::newRunId()); a migrate or resume job runs with --run-id=<id>, so
 * the run in exp_migration_run and its log (migrations/runs/<id>.log) have the job's id. A rollback names no run of
 * its own; its job id only names its files.
 *
 * A deploy job (kind deploy: exp:migration:deploy, Setup > Migrations > Deploys) runs a deploy to remotes, a dry
 * run of one (the preflight), the next stage of a staged one (--continue) or its rollback (--rollback); its <id>.json
 * names the deploy as target and what it does as action (dry-run, apply, transfer-only, continue, rollback). A new
 * deploy has the job's id as its deploy id. It does not wait for the installation lock (the remotes change, not this
 * installation); the deploy takes a lock of its own.
 *
 * One job at a time: start() refuses while a job is running or the installation lock is held (exp:upgrade, a console
 * run). The run itself takes the lock, so two pages pressing Run at once still run once.
 *
 * With migration.ini [AdminSettings] Background=queue the job is handed to expqueue instead (when it is active, its
 * tables are installed, the job type [AdminSettings] QueueJobType is in expqueue.ini and a worker runs; otherwise it
 * starts as a process as above): the job's files are the same, its <id>.json names the queue job (queue), and the
 * worker runs the same command line (runQueued(), through expMigrationQueueHandler), so the pages follow it the same
 * way. Until the worker starts it, the job counts as running (queued), so a second one is refused as before.
 *
 * The user of a job is the signed-in user (--login, resolved by expMigrationContext::resolveUser() in the run): the
 * administration checks the policy migration/run or migration/rollback and "Verify it's you" before start() is called.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationJob
{
    /** The kinds of job and the command each runs. */
    const KINDS = array(
        'migrate' => 'bin/php/expmigrationmigrate.php',
        'resume' => 'bin/php/expmigrationresume.php',
        'rollback' => 'bin/php/expmigrationrollback.php',
        'deploy' => 'bin/php/expmigrationdeploy.php',
    );

    /** The flags the administration may pass on, per kind. */
    const FLAGS = array(
        'migrate' => array( 'no-caption', 'no-restore', 'include-uncommitted', 'no-commit', 'no-push', 'no-tag', 'force' ),
        'resume' => array( 'no-caption', 'no-restore', 'include-uncommitted', 'no-commit', 'no-push', 'no-tag', 'force' ),
        'rollback' => array( 'expert', 'no-safety-caption', 'force-no-caption', 'no-verify' ),
        'deploy' => array( 'apply', 'transfer-only', 'staged' ),
    );

    /** The options with a value the administration may pass on, per kind. */
    const VALUES = array(
        'migrate' => array( 'only', 'reason', 'environment', 'max-migrations', 'max-time', 'throttle', 'nice' ),
        'resume' => array( 'reason', 'environment', 'max-migrations', 'max-time', 'throttle', 'nice' ),
        'rollback' => array( 'confirm', 'parts', 'source', 'environment' ),
        'deploy' => array( 'remotes', 'only', 'continue', 'rollback' ),
    );

    /** Why start() refused (the view translates these). */
    const REFUSED_KIND = 'kind';
    const REFUSED_TARGET = 'target';
    const REFUSED_USER = 'user';
    const REFUSED_BUSY = 'busy';
    const REFUSED_LOCKED = 'locked';
    const REFUSED_PHP = 'php';
    const REFUSED_PROC_OPEN = 'proc-open';
    const REFUSED_DIRECTORY = 'directory';
    const REFUSED_START = 'start';
    const REFUSED_PID = 'pid';

    /**
     * The folder of the jobs, made when missing.
     *
     * @param string|null $directory another folder (tests)
     * @return string
     */
    public static function directory( $directory = null )
    {
        if ( $directory === null )
        {
            $directory = rtrim( (string)eZSys::varDirectory(), '/' ) . '/migration/jobs';
            // absolute: the run is given the path of its progress file and runs in the root, a reader may not
            if ( $directory[0] !== '/' )
                $directory = rtrim( (string)eZSys::rootDir(), '/' ) . '/' . $directory;
        }
        $directory = rtrim( (string)$directory, '/' );
        if ( !is_dir( $directory ) )
            eZDir::mkdir( $directory, false, true );
        return $directory;
    }

    /**
     * The command line of a job, without the php binary. Pure.
     *
     * @param string $kind migrate, resume or rollback
     * @param string $id the job's id (a run id)
     * @param array $options target (rollback: a run, migration or caption id; resume: the run, or '' for the newest
     *                       failed one), the flags of FLAGS and the values of VALUES for the kind; others are ignored
     * @param string $login the user the run runs as
     * @param string $siteaccess the siteaccess whose settings the run reads ('' for the default one)
     * @param string $progressFile
     * @param bool $allowRoot whether the server runs as root (Velocity does): the command then needs --allow-root-user
     * @return string[] the arguments, unescaped
     * @throws InvalidArgumentException for an unknown kind, an id that is no run id, an empty login or a target that
     *                                  is no id (one starting with - would be read as an option)
     */
    public static function arguments( $kind, $id, array $options, $login, $siteaccess, $progressFile, $allowRoot = false )
    {
        if ( !isset( self::KINDS[$kind] ) )
            throw new InvalidArgumentException( 'not a kind of migration job: ' . $kind );
        if ( !expMigrationRecord::validRunId( (string)$id ) )
            throw new InvalidArgumentException( 'not a run id: ' . $id );
        if ( (string)$login === '' )
            throw new InvalidArgumentException( 'a job needs the user it runs as' );
        $arguments = array( self::KINDS[$kind] );
        $target = isset( $options['target'] ) ? trim( (string)$options['target'] ) : '';
        if ( $kind === 'rollback' && $target === '' )
            throw new InvalidArgumentException( 'a rollback needs what it takes back' );
        if ( $target !== '' && !preg_match( '/^[A-Za-z0-9][A-Za-z0-9_.:-]{0,149}$/', $target ) )
            throw new InvalidArgumentException( 'not a run, migration or caption id: ' . $target );
        if ( $kind === 'deploy' )
            return self::deployArguments( $id, $options, $login, $siteaccess, $progressFile, $allowRoot );
        if ( $target !== '' && $kind !== 'migrate' )
            $arguments[] = $target;
        $arguments[] = '--apply';
        $arguments[] = '--format=json';
        if ( $allowRoot )
            $arguments[] = '--allow-root-user';
        $arguments[] = '--login=' . $login;
        if ( (string)$siteaccess !== '' )
            $arguments[] = '--siteaccess=' . $siteaccess;
        if ( $kind !== 'rollback' )
            $arguments[] = '--run-id=' . $id;
        $arguments[] = '--progress=' . $progressFile;
        foreach ( self::FLAGS[$kind] as $flag )
            if ( !empty( $options[$flag] ) )
                $arguments[] = '--' . $flag;
        foreach ( self::VALUES[$kind] as $name )
        {
            if ( !isset( $options[$name] ) )
                continue;
            $value = is_array( $options[$name] ) ? implode( ',', array_map( 'trim', $options[$name] ) ) : trim( (string)$options[$name] );
            if ( $value !== '' )
                $arguments[] = '--' . $name . '=' . $value;
        }
        return $arguments;
    }

    /**
     * The command line of a deploy job (exp:migration:deploy): a new deploy has the job's id as its deploy id; the
     * next stage of a staged deploy (option continue) and the rollback of a deploy (option rollback) name the deploy
     * and keep their job's id only for their files. Without the flag apply a new deploy is a dry run (the preflight).
     * Pure.
     *
     * @param string $id
     * @param array $options apply, transfer-only, staged (flags); remotes, only (ids or lists), continue or rollback
     *                       (a deploy id)
     * @param string $login
     * @param string $siteaccess
     * @param string $progressFile
     * @param bool $allowRoot
     * @return string[]
     * @throws InvalidArgumentException for a new deploy without remotes, a remote, group or migration id with other
     *                                  characters, or a continue or rollback that is no deploy id
     */
    protected static function deployArguments( $id, array $options, $login, $siteaccess, $progressFile, $allowRoot )
    {
        $arguments = array( self::KINDS['deploy'] );
        $list = function ( $name ) use ( $options ) {
            $values = array();
            foreach ( isset( $options[$name] ) ? (array)$options[$name] : array() as $value )
                foreach ( explode( ',', (string)$value ) as $part )
                    if ( ( $part = trim( $part ) ) !== '' )
                    {
                        if ( !preg_match( '/^[A-Za-z0-9][A-Za-z0-9_.-]{0,149}$/', $part ) )
                            throw new InvalidArgumentException( "not a $name id: $part" );
                        $values[] = $part;
                    }
            return array_values( array_unique( $values ) );
        };
        $remotes = $list( 'remotes' );
        $only = $list( 'only' );
        $continue = isset( $options['continue'] ) ? trim( (string)$options['continue'] ) : '';
        $rollback = isset( $options['rollback'] ) ? trim( (string)$options['rollback'] ) : '';
        foreach ( array( $continue, $rollback ) as $deployId )
            if ( $deployId !== '' && !expMigrationRecord::validRunId( $deployId ) )
                throw new InvalidArgumentException( 'not a deploy id: ' . $deployId );
        if ( $continue !== '' && $rollback !== '' )
            throw new InvalidArgumentException( 'a job either continues a deploy or takes it back' );
        if ( $continue === '' && $rollback === '' && !$remotes )
            throw new InvalidArgumentException( 'a deploy needs the remotes it goes to' );
        if ( $continue !== '' )
            $arguments[] = '--continue=' . $continue;
        else if ( $rollback !== '' )
            $arguments[] = '--rollback=' . $rollback;
        else
        {
            $arguments[] = '--remotes=' . implode( ',', $remotes );
            if ( $only )
                $arguments[] = '--only=' . implode( ',', $only );
            if ( !empty( $options['transfer-only'] ) )
                $arguments[] = '--transfer-only';
            else if ( !empty( $options['staged'] ) )
                $arguments[] = '--staged';
            $arguments[] = '--run-id=' . $id;
        }
        if ( $continue !== '' || $rollback !== '' || !empty( $options['apply'] ) || !empty( $options['transfer-only'] ) )
            $arguments[] = '--apply';
        $arguments[] = '--format=json';
        if ( $allowRoot )
            $arguments[] = '--allow-root-user';
        $arguments[] = '--login=' . $login;
        if ( (string)$siteaccess !== '' )
            $arguments[] = '--siteaccess=' . $siteaccess;
        $arguments[] = '--progress=' . $progressFile;
        return $arguments;
    }

    /**
     * The shell command that starts a job detached: the shell writes its own pid and then becomes php, so the pid
     * written is the run's; stdout and stderr go to the job's files. Pure.
     *
     * @param string $php the php command line binary
     * @param string[] $arguments arguments()
     * @param string $root the installation's root (the working directory)
     * @param string $base the job's files without their suffix (<directory>/<id>)
     * @return string
     */
    public static function commandLine( $php, array $arguments, $root, $base )
    {
        $inner = sprintf( 'echo $$ > %s; exec %s %s', escapeshellarg( $base . '.pid' ), escapeshellarg( $php ),
                          implode( ' ', array_map( 'escapeshellarg', $arguments ) ) );
        return sprintf( 'cd %s && nohup sh -c %s > %s 2> %s < /dev/null &', escapeshellarg( $root ), escapeshellarg( $inner ),
                        escapeshellarg( $base . '.out' ), escapeshellarg( $base . '.err' ) );
    }

    /**
     * Starts a job.
     *
     * @param string $kind migrate, resume or rollback
     * @param array $options see arguments()
     * @param string $login the signed-in user's login
     * @param array $settings siteaccess (default this request's), root, directory, php (a binary), id (a fixed id:
     *                        tests), lock (an expMigrationLock: tests), wait (seconds to wait for the pid, default 2)
     * @return array ok (bool), id, refused (a REFUSED_* code, '' when started), detail (what the code does not say)
     */
    public static function start( $kind, array $options, $login, array $settings = array() )
    {
        $refuse = function ( $code, $detail = '' ) {
            return array( 'ok' => false, 'id' => '', 'refused' => $code, 'detail' => (string)$detail );
        };
        if ( !isset( self::KINDS[$kind] ) )
            return $refuse( self::REFUSED_KIND, $kind );
        if ( $kind === 'rollback' && ( !isset( $options['target'] ) || trim( (string)$options['target'] ) === '' ) )
            return $refuse( self::REFUSED_TARGET );
        if ( (string)$login === '' )
            return $refuse( self::REFUSED_USER, $login );

        $directory = self::directory( isset( $settings['directory'] ) ? $settings['directory'] : null );
        if ( !is_dir( $directory ) || !is_writable( $directory ) )
            return $refuse( self::REFUSED_DIRECTORY, $directory );
        $running = self::running( $directory );
        if ( $running )
            return $refuse( self::REFUSED_BUSY, $running['id'] );
        $lock = isset( $settings['lock'] ) ? $settings['lock'] : expMigrationLock::installation();
        // a deploy changes the remotes, not this installation: its own lock (expMigrationRemoteDeploy) is enough
        if ( $kind !== 'deploy' && $lock->isLocked() && !$lock->isHeld() )
            return $refuse( self::REFUSED_LOCKED, expMigrationLock::describe( $lock->holder() ) );

        $php = isset( $settings['php'] ) ? (string)$settings['php'] : self::phpBinary();
        if ( $php === '' )
            return $refuse( self::REFUSED_PHP );
        if ( !function_exists( 'proc_open' ) )
            return $refuse( self::REFUSED_PROC_OPEN );
        // the run resolves the user by its login (expMigrationContext::resolveUser()): one that does not exist would
        // only fail there
        if ( !expMigrationContext::resolveUser( (string)$login ) )
            return $refuse( self::REFUSED_USER, $login );

        $id = isset( $settings['id'] ) ? (string)$settings['id'] : expMigrationContext::newRunId();
        $root = isset( $settings['root'] ) ? (string)$settings['root'] : (string)eZSys::rootDir();
        $siteaccess = isset( $settings['siteaccess'] ) ? (string)$settings['siteaccess'] : self::currentSiteaccess();
        $base = $directory . '/' . $id;
        try
        {
            $arguments = self::arguments( $kind, $id, $options, $login, $siteaccess, $base . '.progress',
                                          function_exists( 'posix_geteuid' ) && posix_geteuid() === 0 );
        }
        catch ( InvalidArgumentException $e )
        {
            return $refuse( self::REFUSED_TARGET, $e->getMessage() );
        }
        $job = array( 'id' => $id, 'kind' => $kind, 'target' => isset( $options['target'] ) ? trim( (string)$options['target'] ) : '',
                      'user' => (string)$login, 'siteaccess' => $siteaccess, 'pid' => 0, 'started' => time(), 'progress' => $base . '.progress',
                      'arguments' => $arguments );
        // a deploy job names its deploy: the one it continues or takes back, or (a new deploy) its own id
        if ( $kind === 'deploy' )
        {
            $job['target'] = !empty( $options['continue'] ) ? trim( (string)$options['continue'] )
                           : ( !empty( $options['rollback'] ) ? trim( (string)$options['rollback'] ) : $id );
            $job['action'] = !empty( $options['continue'] ) ? 'continue' : ( !empty( $options['rollback'] ) ? 'rollback'
                           : ( !empty( $options['transfer-only'] ) ? 'transfer-only' : ( !empty( $options['apply'] ) ? 'apply' : 'dry-run' ) ) );
        }
        if ( @file_put_contents( $base . '.json', json_encode( $job, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ) === false )
            return $refuse( self::REFUSED_DIRECTORY, $directory );
        @touch( $base . '.progress' );

        // Background=queue: the worker of expqueue runs it (runQueued()); a queue that cannot take it now leaves it to
        // a process of its own
        $queueType = array_key_exists( 'queue', $settings ) ? (string)$settings['queue'] : self::queueType();
        if ( $queueType !== '' )
        {
            $uuid = self::enqueue( $queueType, $id, $login );
            if ( $uuid !== '' )
            {
                $job['queue'] = $uuid;
                @file_put_contents( $base . '.json', json_encode( $job, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
                self::prune( $directory );
                return array( 'ok' => true, 'id' => $id, 'refused' => '', 'detail' => '' );
            }
        }

        $process = proc_open( self::commandLine( $php, $arguments, $root, $base ), array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes, $root );
        if ( !is_resource( $process ) )
            return $refuse( self::REFUSED_START );
        foreach ( $pipes as $pipe )
            fclose( $pipe );
        // waits for the shell, which has already ended, never for the run it left going
        proc_close( $process );

        $pid = 0;
        $wait = isset( $settings['wait'] ) ? (float)$settings['wait'] : 2.0;
        for ( $i = 0; $i < max( 1, (int)( $wait * 20 ) ); $i++ )
        {
            clearstatcache( true, $base . '.pid' );
            if ( is_file( $base . '.pid' ) && ( $pid = (int)trim( (string)@file_get_contents( $base . '.pid' ) ) ) > 0 )
                break;
            usleep( 50000 );
        }
        if ( $pid < 1 )
            return array( 'ok' => false, 'id' => $id, 'refused' => self::REFUSED_PID, 'detail' => $base . '.err' );
        $job['pid'] = $pid;
        @file_put_contents( $base . '.json', json_encode( $job, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
        self::prune( $directory );
        return array( 'ok' => true, 'id' => $id, 'refused' => '', 'detail' => '' );
    }

    /**
     * A job as the pages show it.
     *
     * @param string $id
     * @param string|null $directory
     * @return array|null the job (see the class) with running (bool), ended (bool), report (the command's report once
     *                    it has ended, null before or when it printed none), errors (what it printed as errors, the
     *                    last 4 KB, and what it printed instead of a report), exit (the report's exit code, null before the end), reported (whether there is
     *                    an exit code); null when there is no such job
     */
    public static function get( $id, $directory = null )
    {
        if ( !expMigrationRecord::validRunId( (string)$id ) )
            return null;
        $base = self::directory( $directory ) . '/' . $id;
        $job = is_file( $base . '.json' ) ? json_decode( (string)@file_get_contents( $base . '.json' ), true ) : null;
        if ( !is_array( $job ) || !isset( $job['id'] ) || $job['id'] !== $id )
            return null;
        $job += array( 'kind' => '', 'action' => '', 'target' => '', 'user' => '', 'siteaccess' => '', 'pid' => 0, 'started' => 0, 'progress' => $base . '.progress' );
        $job['running'] = self::alive( (int)$job['pid'] );
        // handed to expqueue and not started by its worker yet: waiting counts as running
        $job['queued'] = false;
        if ( !$job['running'] && (int)$job['pid'] < 1 && !empty( $job['queue'] ) && !is_file( $base . '.out' ) )
            $job['running'] = $job['queued'] = self::queueWaiting( (string)$job['queue'] );
        $job['ended'] = !$job['running'];
        $job['report'] = null;
        $job['exit'] = null;
        if ( $job['ended'] )
        {
            $job['report'] = self::report( is_file( $base . '.out' ) ? (string)@file_get_contents( $base . '.out' ) : '' );
            if ( is_array( $job['report'] ) && isset( $job['report']['exit'] ) )
                $job['exit'] = (int)$job['report']['exit'];
        }
        $job['reported'] = $job['exit'] !== null;
        $errors = is_file( $base . '.err' ) ? (string)@file_get_contents( $base . '.err', false, null, max( 0, (int)@filesize( $base . '.err' ) - 4096 ) ) : '';
        // an ended job without a report: what it printed instead says why (a refused start, a fatal error)
        if ( $job['ended'] && $job['report'] === null && is_file( $base . '.out' ) )
            $errors .= "\n" . (string)@file_get_contents( $base . '.out', false, null, max( 0, (int)@filesize( $base . '.out' ) - 4096 ) );
        $job['errors'] = trim( $errors );
        return $job;
    }

    /**
     * The jobs, newest first.
     *
     * @param int $limit
     * @param string|null $directory
     * @return array[] get() of each
     */
    public static function all( $limit = 20, $directory = null )
    {
        $directory = self::directory( $directory );
        $ids = array();
        foreach ( (array)glob( $directory . '/*.json' ) as $file )
        {
            $id = basename( (string)$file, '.json' );
            if ( expMigrationRecord::validRunId( $id ) )
                $ids[] = $id;
        }
        rsort( $ids, SORT_STRING );
        $jobs = array();
        foreach ( array_slice( $ids, 0, max( 0, (int)$limit ) ) as $id )
        {
            $job = self::get( $id, $directory );
            if ( $job )
                $jobs[] = $job;
        }
        return $jobs;
    }

    /**
     * The job that is running, if one is.
     *
     * @param string|null $directory
     * @return array|null
     */
    public static function running( $directory = null )
    {
        foreach ( self::all( 10, $directory ) as $job )
            if ( $job['running'] )
                return $job;
        return null;
    }

    /**
     * The newest deploy job of a deploy: the one that made it, the next stage or its rollback.
     *
     * @param string $deployId
     * @param string|null $directory
     * @param int $limit how many of the newest jobs are looked at
     * @return array|null
     */
    public static function forDeploy( $deployId, $directory = null, $limit = 50 )
    {
        foreach ( self::all( $limit, $directory ) as $job )
            if ( $job['kind'] === 'deploy' && ( $job['target'] === (string)$deployId || $job['id'] === (string)$deployId ) )
                return $job;
        return null;
    }

    /**
     * The report a command printed with --format=json: the last JSON object of its output. Pure.
     *
     * @param string $output
     * @return array|null
     */
    public static function report( $output )
    {
        $output = trim( (string)$output );
        // the report starts with its schema (finish() puts it first); a line printed before it may hold braces
        $start = strrpos( $output, "{\n    \"schema\"" );
        if ( $start === false )
            $start = strpos( $output, '{' );
        if ( $start === false )
            return null;
        $report = json_decode( substr( $output, $start ), true );
        return is_array( $report ) && isset( $report['schema'] ) ? $report : null;
    }

    /**
     * Whether a process id is a migration command still running. /proc is read where there is one, so a pid the
     * system gave to another process since does not count.
     *
     * @param int $pid
     * @return bool
     */
    public static function alive( $pid )
    {
        $pid = (int)$pid;
        if ( $pid < 1 )
            return false;
        if ( is_dir( '/proc' ) )
        {
            if ( !is_dir( '/proc/' . $pid ) )
                return false;
            $cmdline = @file_get_contents( '/proc/' . $pid . '/cmdline' );
            return $cmdline === false || $cmdline === '' || strpos( $cmdline, 'expmigration' ) !== false;
        }
        return function_exists( 'posix_kill' ) && posix_kill( $pid, 0 );
    }

    /**
     * The php command line binary: migration.ini [AdminSettings] PhpCliPath, then the cronjob console's
     * (cronjob.ini [AdminSettings] PhpCliPath and the usual places). PHP_BINARY is never used: under php-fpm it is
     * the fpm binary.
     *
     * @return string '' when none can be run
     */
    public static function phpBinary()
    {
        $ini = eZINI::instance( 'migration.ini' );
        $configured = $ini->hasVariable( 'AdminSettings', 'PhpCliPath' ) ? trim( (string)$ini->variable( 'AdminSettings', 'PhpCliPath' ) ) : '';
        if ( $configured !== '' && is_file( $configured ) && is_executable( $configured ) )
            return $configured;
        if ( !class_exists( 'expCronjobRunner', false ) )
            require_once 'kernel/setup/expcronjobrunner.php';
        $php = expCronjobRunner::phpBinary();
        return $php === false ? '' : (string)$php;
    }

    /**
     * The name of this request's siteaccess, '' without one.
     *
     * @return string
     */
    public static function currentSiteaccess()
    {
        $access = isset( $GLOBALS['eZCurrentAccess']['name'] ) ? (string)$GLOBALS['eZCurrentAccess']['name'] : '';
        return preg_match( '/^[A-Za-z0-9_-]+$/', $access ) ? $access : '';
    }

    /**
     * The expqueue job type a job goes to: [AdminSettings] QueueJobType when Background=queue and expqueue can take
     * it now (active, tables installed, the type in expqueue.ini, a worker running), else '' (a process of its own).
     *
     * @param eZINI|null $ini migration.ini
     * @return string
     */
    public static function queueType( $ini = null )
    {
        $ini = $ini ?: eZINI::instance( 'migration.ini' );
        $mode = $ini->hasVariable( 'AdminSettings', 'Background' ) ? trim( (string)$ini->variable( 'AdminSettings', 'Background' ) ) : 'process';
        if ( $mode !== 'queue' )
            return '';
        $type = $ini->hasVariable( 'AdminSettings', 'QueueJobType' ) ? trim( (string)$ini->variable( 'AdminSettings', 'QueueJobType' ) ) : 'migration.run';
        return self::queueProblem( $type ) === '' ? $type : '';
    }

    /**
     * Why expqueue cannot take a job of the type now, '' when it can.
     *
     * @param string $type
     * @return string
     */
    public static function queueProblem( $type )
    {
        if ( !class_exists( 'expQueue' ) || !class_exists( 'expQueueConfig' ) )
            return 'expqueue is not active';
        if ( (string)$type === '' )
            return 'no [AdminSettings] QueueJobType';
        try
        {
            if ( !expQueue::installed() )
                return 'the tables of expqueue are not installed';
            $definition = expQueueConfig::instance()->jobType( (string)$type );
            if ( !$definition )
                return "expqueue.ini has no [JobType_$type]";
            if ( class_exists( 'expQueueWorker' ) && !expQueueWorker::live() )
                return 'no queue worker is running';
        }
        catch ( Throwable $e )
        {
            return $e->getMessage();
        }
        return '';
    }

    /**
     * Hands a job to expqueue, as the user of the job.
     *
     * @param string $type
     * @param string $id the job's id
     * @param string $login
     * @return string the queue job's uuid, '' when it could not be queued
     */
    protected static function enqueue( $type, $id, $login )
    {
        try
        {
            $user = expMigrationContext::resolveUser( (string)$login );
            $options = $user ? array( 'user_id' => (int)$user->attribute( 'contentobject_id' ) ) : array();
            $queued = expQueue::enqueue( (string)$type, array( 'job' => (string)$id ), $options );
            return is_object( $queued ) ? (string)$queued->attribute( 'uuid' ) : '';
        }
        catch ( Throwable $e )
        {
            eZDebug::writeWarning( 'the migration job could not be queued, it starts as a process: ' . $e->getMessage(), __METHOD__ );
            return '';
        }
    }

    /**
     * Whether a queue job is still to run or running (not done, dead or cancelled).
     *
     * @param string $uuid
     * @return bool
     */
    public static function queueWaiting( $uuid )
    {
        if ( !class_exists( 'expQueue' ) )
            return false;
        try
        {
            $queued = expQueue::fetch( (string)$uuid );
            return is_object( $queued ) && !$queued->isFinal();
        }
        catch ( Throwable $e )
        {
            return false;
        }
    }

    /**
     * Runs a queued job in this process's child and waits for it (the queue worker's side, expMigrationQueueHandler):
     * the command line start() wrote, its output into the job's files, its pid into the job, as a job started as a
     * process has them.
     *
     * @param string $id the job's id
     * @param callable|null $heartbeat called about every second while the run goes on
     * @param array $settings directory, php, root (tests)
     * @return int the run's exit code (4 when it could not be started)
     */
    public static function runQueued( $id, $heartbeat = null, array $settings = array() )
    {
        if ( !expMigrationRecord::validRunId( (string)$id ) )
            return expUpgrade::EXIT_USAGE;
        $base = self::directory( isset( $settings['directory'] ) ? $settings['directory'] : null ) . '/' . $id;
        $job = is_file( $base . '.json' ) ? json_decode( (string)@file_get_contents( $base . '.json' ), true ) : null;
        if ( !is_array( $job ) || !isset( $job['arguments'] ) || !is_array( $job['arguments'] ) || !$job['arguments']
             || !in_array( $job['arguments'][0], self::KINDS, true ) )
            return expUpgrade::EXIT_USAGE;
        $php = isset( $settings['php'] ) ? (string)$settings['php'] : self::phpBinary();
        $root = isset( $settings['root'] ) ? (string)$settings['root'] : (string)eZSys::rootDir();
        if ( $php === '' || !function_exists( 'proc_open' ) )
            return expUpgrade::EXIT_INTERNAL;
        $pipes = array();
        $process = proc_open( array_merge( array( $php ), array_map( 'strval', $job['arguments'] ) ),
                              array( 0 => array( 'file', '/dev/null', 'r' ), 1 => array( 'file', $base . '.out', 'w' ), 2 => array( 'file', $base . '.err', 'w' ) ),
                              $pipes, $root );
        if ( !is_resource( $process ) )
            return expUpgrade::EXIT_INTERNAL;
        $status = proc_get_status( $process );
        $job['pid'] = (int)$status['pid'];
        @file_put_contents( $base . '.pid', (string)$job['pid'] );
        @file_put_contents( $base . '.json', json_encode( $job, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
        while ( $status['running'] )
        {
            if ( is_callable( $heartbeat ) )
                call_user_func( $heartbeat );
            usleep( 500000 );
            $status = proc_get_status( $process );
        }
        proc_close( $process );
        return (int)$status['exitcode'];
    }

    /**
     * Removes the files of the oldest jobs beyond migration.ini [AdminSettings] JobHistory (default 50); a job that
     * is running is kept.
     *
     * @param string $directory
     */
    protected static function prune( $directory )
    {
        $ini = eZINI::instance( 'migration.ini' );
        $keep = $ini->hasVariable( 'AdminSettings', 'JobHistory' ) ? (int)$ini->variable( 'AdminSettings', 'JobHistory' ) : 50;
        $keep = $keep > 0 ? $keep : 50;
        $ids = array();
        foreach ( (array)glob( $directory . '/*.json' ) as $file )
        {
            $id = basename( (string)$file, '.json' );
            if ( expMigrationRecord::validRunId( $id ) )
                $ids[] = $id;
        }
        rsort( $ids, SORT_STRING );
        foreach ( array_slice( $ids, $keep ) as $id )
        {
            $job = self::get( $id, $directory );
            if ( $job && $job['running'] )
                continue;
            foreach ( array( '.json', '.progress', '.out', '.err', '.pid' ) as $suffix )
                if ( is_file( $directory . '/' . $id . $suffix ) )
                    @unlink( $directory . '/' . $id . $suffix );
        }
    }
}
