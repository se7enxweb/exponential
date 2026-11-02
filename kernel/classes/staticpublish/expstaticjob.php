<?php
/**
 * File containing the expStaticJob class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A build, publish or rollback of a static publishing target started from the administration, in the background,
 * modelled on expMigrationJob: a console process of its own (exp:static:build, exp:static:publish or
 * exp:static:rollback --apply, with --format=json), detached from the request, so a long crawl or push never holds a
 * web request open and goes on when the page is closed. The page follows it by its id: the view staticpublish/stream
 * reads its progress file as it grows (expStaticProgress), and a reload finds it again by the id in the address.
 *
 * Files of a job, in <WorkDir>/.jobs/ (var/<site>/static-publish/.jobs/; a folder no target id can name):
 *
 *   <id>.json      the job: id, kind, target, user, siteaccess, pid, started, expected (pages of the last build),
 *                  progress (the file below), arguments (the command line without the php binary; never a secret)
 *   <id>.progress  the progress events (expStaticProgress), written by the command
 *   <id>.out       what the command printed: its exp-static/1 report as JSON once it has ended
 *   <id>.err       what it printed as errors
 *   <id>.pid       the process id, written by the shell before it runs php
 *
 * The id of a job is a run id (expStaticBuild::newRunId()); the command runs with --run-id=<id>, so the publish or
 * rollback row in exp_static_run has the job's id and staticpublish/log/<id> shows both.
 *
 * One job per target at a time: start() refuses while a job of the target is running or another process holds the
 * target's lock (expStaticLock: a console build or the cronjob part). The command takes the lock itself, so two pages
 * pressing Build at once still build once.
 *
 * The user of a job is the signed-in user (--login): the administration checks the policy (staticpublish/build or
 * publish with the Target limitation) and "Verify it's you" before start() is called.
 *
 * @since 6.0.15
 * @package kernel
 */
class expStaticJob
{
    /** The kinds of job and the command each runs. */
    const KINDS = array(
        'build' => 'bin/php/expstaticbuild.php',
        'publish' => 'bin/php/expstaticpublish.php',
        'rollback' => 'bin/php/expstaticrollback.php',
    );

    /** The folder of the jobs below the work directory. */
    const FOLDER = '.jobs';

    /**
     * The option of a publish job that builds these nodes first (node ids): the pages of the nodes are built into the
     * last build and published with it (exp:static:publish --nodes). The website sidebar's "Publish this page" looks
     * for this constant.
     */
    const NODES = 'nodes';

    /** The pattern of a job id (expStaticBuild::newRunId()). */
    const ID_PATTERN = '/^[0-9]{8}-[0-9]{6}-[0-9a-f]{6}$/';

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

    /** @return bool whether a string is a job id */
    public static function validId( $id )
    {
        return is_string( $id ) && preg_match( self::ID_PATTERN, $id );
    }

    /**
     * The folder of the jobs, made when missing.
     *
     * @param string $workDir the folder of the targets (expStaticPublisher::workDirectory())
     * @return string
     */
    public static function directory( $workDir )
    {
        $directory = rtrim( str_replace( '\\', '/', (string)$workDir ), '/' ) . '/' . self::FOLDER;
        if ( !is_dir( $directory ) )
            @mkdir( $directory, eZDir::dirMode( 0775 ), true );
        return $directory;
    }

    /**
     * The command line of a job, without the php binary. Pure.
     *
     * @param string $kind build, publish or rollback
     * @param string $id the job's id
     * @param string $target the target id
     * @param array $options to (rollback: the tag or its number), message (publish, rollback), nodes (publish: node ids
     *                       built first, NODES)
     * @param string $login the user the command runs as
     * @param string $siteaccess the siteaccess whose settings the command reads ('' for the default one)
     * @param string $progressFile
     * @param bool $allowRoot whether the server runs as root (Velocity does): the command then needs --allow-root-user
     * @return string[] the arguments, unescaped
     * @throws InvalidArgumentException for an unknown kind, an id that is no job id, a target that is no target id, an
     *                                  empty login, a rollback without a tag or nodes that are no node ids
     */
    public static function arguments( $kind, $id, $target, array $options, $login, $siteaccess, $progressFile, $allowRoot = false )
    {
        if ( !isset( self::KINDS[$kind] ) )
            throw new InvalidArgumentException( 'not a kind of static publishing job: ' . $kind );
        if ( !self::validId( (string)$id ) )
            throw new InvalidArgumentException( 'not a job id: ' . $id );
        if ( !preg_match( expStaticTarget::ID_PATTERN, (string)$target ) )
            throw new InvalidArgumentException( 'not a target id: ' . $target );
        if ( (string)$login === '' || strpos( (string)$login, "\0" ) !== false )
            throw new InvalidArgumentException( 'a job needs the user it runs as' );
        $arguments = array( self::KINDS[$kind], (string)$target );
        if ( $kind === 'rollback' )
        {
            $to = isset( $options['to'] ) ? trim( (string)$options['to'] ) : '';
            if ( !preg_match( '#^([0-9]{1,9}|static/[a-z0-9][a-z0-9_-]{0,49}/[0-9]{1,9})$#', $to ) )
                throw new InvalidArgumentException( 'a rollback needs the tag or number of a publish: ' . $to );
            $arguments[] = $to;
            $arguments[] = '--apply';
        }
        if ( $kind === 'build' )
            $arguments[] = '--full';
        if ( $kind === 'publish' && !empty( $options[self::NODES] ) )
        {
            $nodes = array();
            foreach ( (array)$options[self::NODES] as $node )
            {
                if ( !preg_match( '/^[1-9][0-9]{0,9}$/', (string)$node ) )
                    throw new InvalidArgumentException( 'not a node id: ' . $node );
                $nodes[(int)$node] = (int)$node;
            }
            $arguments[] = '--build';
            $arguments[] = '--nodes=' . implode( ',', $nodes );
        }
        $arguments[] = '--format=json';
        if ( $allowRoot )
            $arguments[] = '--allow-root-user';
        $arguments[] = '--login=' . $login;
        if ( (string)$siteaccess !== '' )
            $arguments[] = '--siteaccess=' . $siteaccess;
        $arguments[] = '--run-id=' . $id;
        $arguments[] = '--progress=' . $progressFile;
        $message = isset( $options['message'] ) ? trim( preg_replace( '/[\x00-\x08\x0B-\x1F\x7F]/', '', (string)$options['message'] ) ) : '';
        if ( $message !== '' && $kind !== 'build' )
            $arguments[] = '--message=' . mb_substr( $message, 0, 500 );
        return $arguments;
    }

    /**
     * The shell command that starts a job detached (expMigrationJob::commandLine()). Pure.
     *
     * @param string $php
     * @param string[] $arguments
     * @param string $root
     * @param string $base the job's files without their suffix
     * @return string
     */
    public static function commandLine( $php, array $arguments, $root, $base )
    {
        return expMigrationJob::commandLine( $php, $arguments, $root, $base );
    }

    /**
     * Starts a job.
     *
     * @param string $kind build, publish or rollback
     * @param string $target the target id
     * @param array $options see arguments()
     * @param string $login the signed-in user's login
     * @param array $settings work_dir (the folder of the targets; default the publisher's), siteaccess (default this
     *                        request's), root, php (a binary), id (a fixed id: tests), expected (pages), wait (seconds
     *                        to wait for the pid, default 2)
     * @return array ok (bool), id, refused (a REFUSED_* code, '' when started), detail
     */
    public static function start( $kind, $target, array $options, $login, array $settings = array() )
    {
        $refuse = function ( $code, $detail = '' ) {
            return array( 'ok' => false, 'id' => '', 'refused' => $code, 'detail' => (string)$detail );
        };
        if ( !isset( self::KINDS[$kind] ) )
            return $refuse( self::REFUSED_KIND, $kind );
        if ( !preg_match( expStaticTarget::ID_PATTERN, (string)$target ) )
            return $refuse( self::REFUSED_TARGET, $target );
        if ( (string)$login === '' )
            return $refuse( self::REFUSED_USER, $login );

        $workDir = isset( $settings['work_dir'] ) ? (string)$settings['work_dir'] : self::workDirectory();
        $directory = self::directory( $workDir );
        if ( !is_dir( $directory ) || !is_writable( $directory ) )
            return $refuse( self::REFUSED_DIRECTORY, $directory );
        $running = self::running( $workDir, $target );
        if ( $running )
            return $refuse( self::REFUSED_BUSY, $running['id'] );
        $problem = '';
        $lock = expStaticLock::acquire( $workDir, (string)$target, 'check', $problem );
        if ( $lock === null )
            return $refuse( self::REFUSED_LOCKED, $problem );
        $lock->release();

        $php = isset( $settings['php'] ) ? (string)$settings['php'] : expMigrationJob::phpBinary();
        if ( $php === '' )
            return $refuse( self::REFUSED_PHP );
        if ( !function_exists( 'proc_open' ) )
            return $refuse( self::REFUSED_PROC_OPEN );

        $id = isset( $settings['id'] ) ? (string)$settings['id'] : expStaticBuild::newRunId();
        $root = isset( $settings['root'] ) ? (string)$settings['root'] : (string)eZSys::rootDir();
        $siteaccess = isset( $settings['siteaccess'] ) ? (string)$settings['siteaccess'] : expMigrationJob::currentSiteaccess();
        $base = $directory . '/' . $id;
        try
        {
            $arguments = self::arguments( $kind, $id, $target, $options, $login, $siteaccess, $base . '.progress',
                                          function_exists( 'posix_geteuid' ) && posix_geteuid() === 0 );
        }
        catch ( InvalidArgumentException $e )
        {
            return $refuse( self::REFUSED_TARGET, $e->getMessage() );
        }
        $job = array( 'id' => $id, 'kind' => $kind, 'target' => (string)$target, 'to' => isset( $options['to'] ) ? (string)$options['to'] : '',
                      'user' => (string)$login, 'siteaccess' => $siteaccess, 'pid' => 0, 'started' => time(),
                      'expected' => isset( $settings['expected'] ) ? max( 0, (int)$settings['expected'] ) : 0, 'progress' => $base . '.progress',
                      'arguments' => $arguments );
        if ( @file_put_contents( $base . '.json', json_encode( $job, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ) === false )
            return $refuse( self::REFUSED_DIRECTORY, $directory );
        @touch( $base . '.progress' );

        $process = proc_open( self::commandLine( $php, $arguments, $root, $base ), array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes, $root );
        if ( !is_resource( $process ) )
            return $refuse( self::REFUSED_START );
        foreach ( $pipes as $pipe )
            fclose( $pipe );
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

    /** @return string the folder of the targets, as the publisher has it */
    public static function workDirectory()
    {
        $dummy = new expStaticTarget( 'job' );
        $publisher = new expStaticPublisher( $dummy, array( 'root' => rtrim( (string)eZSys::rootDir(), '/' ) ) );
        return $publisher->workDirectory();
    }

    /**
     * A job as the pages show it.
     *
     * @param string $id
     * @param string|null $workDir
     * @return array|null the job (see the class) with running, ended, report (the command's report once it has ended,
     *                    or null), exit (null before the end or without a report), errors (what it printed as errors)
     */
    public static function get( $id, $workDir = null )
    {
        if ( !self::validId( (string)$id ) )
            return null;
        $base = self::directory( $workDir === null ? self::workDirectory() : $workDir ) . '/' . $id;
        $job = is_file( $base . '.json' ) ? json_decode( (string)@file_get_contents( $base . '.json' ), true ) : null;
        if ( !is_array( $job ) || !isset( $job['id'] ) || $job['id'] !== $id )
            return null;
        $job += array( 'kind' => '', 'target' => '', 'to' => '', 'user' => '', 'siteaccess' => '', 'pid' => 0, 'started' => 0, 'expected' => 0,
                       'progress' => $base . '.progress' );
        $job['running'] = self::alive( (int)$job['pid'] );
        $job['ended'] = !$job['running'];
        $job['report'] = null;
        $job['exit'] = null;
        if ( $job['ended'] )
        {
            $job['report'] = expMigrationJob::report( is_file( $base . '.out' ) ? (string)@file_get_contents( $base . '.out' ) : '' );
            if ( is_array( $job['report'] ) && isset( $job['report']['exit'] ) )
                $job['exit'] = (int)$job['report']['exit'];
        }
        $errors = is_file( $base . '.err' ) ? (string)@file_get_contents( $base . '.err', false, null, max( 0, (int)@filesize( $base . '.err' ) - 4096 ) ) : '';
        if ( $job['ended'] && $job['report'] === null && is_file( $base . '.out' ) )
            $errors .= "\n" . (string)@file_get_contents( $base . '.out', false, null, max( 0, (int)@filesize( $base . '.out' ) - 4096 ) );
        $job['errors'] = trim( $errors );
        return $job;
    }

    /**
     * The jobs, newest first.
     *
     * @param string|null $workDir
     * @param string|null $target only this target's
     * @param int $limit
     * @return array[]
     */
    public static function all( $workDir = null, $target = null, $limit = 20 )
    {
        $workDir = $workDir === null ? self::workDirectory() : $workDir;
        $ids = array();
        foreach ( (array)glob( self::directory( $workDir ) . '/*.json' ) as $file )
        {
            $id = basename( (string)$file, '.json' );
            if ( self::validId( $id ) )
                $ids[] = $id;
        }
        rsort( $ids, SORT_STRING );
        $jobs = array();
        foreach ( $ids as $id )
        {
            $job = self::get( $id, $workDir );
            if ( !$job || ( $target !== null && $job['target'] !== $target ) )
                continue;
            $jobs[] = $job;
            if ( count( $jobs ) >= $limit )
                break;
        }
        return $jobs;
    }

    /**
     * The job of a target that is running, if one is.
     *
     * @param string|null $workDir
     * @param string|null $target
     * @return array|null
     */
    public static function running( $workDir = null, $target = null )
    {
        foreach ( self::all( $workDir, $target, 10 ) as $job )
            if ( $job['running'] )
                return $job;
        return null;
    }

    /**
     * The last build job of a target that has ended with a report, if there is one.
     *
     * @param string|null $workDir
     * @param string $target
     * @return array|null
     */
    public static function lastBuild( $workDir, $target )
    {
        foreach ( self::all( $workDir, $target, 20 ) as $job )
            if ( $job['kind'] === 'build' && $job['ended'] )
                return $job;
        return null;
    }

    /**
     * Whether a process id is a static publishing command still running.
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
            return $cmdline === false || $cmdline === '' || strpos( $cmdline, 'expstatic' ) !== false;
        }
        return function_exists( 'posix_kill' ) && posix_kill( $pid, 0 );
    }

    /**
     * Removes the files of the oldest jobs beyond staticcache.ini [StaticPublishSettings] JobHistory (default 50); a
     * job that is running is kept.
     *
     * @param string $directory
     */
    protected static function prune( $directory )
    {
        $ini = eZINI::instance( 'staticcache.ini' );
        $keep = $ini->hasVariable( 'StaticPublishSettings', 'JobHistory' ) ? (int)$ini->variable( 'StaticPublishSettings', 'JobHistory' ) : 50;
        $keep = $keep > 0 ? $keep : 50;
        $ids = array();
        foreach ( (array)glob( $directory . '/*.json' ) as $file )
        {
            $id = basename( (string)$file, '.json' );
            if ( self::validId( $id ) )
                $ids[] = $id;
        }
        rsort( $ids, SORT_STRING );
        foreach ( array_slice( $ids, $keep ) as $id )
        {
            $pid = (int)trim( (string)@file_get_contents( $directory . '/' . $id . '.pid' ) );
            if ( self::alive( $pid ) )
                continue;
            foreach ( array( '.json', '.progress', '.out', '.err', '.pid' ) as $suffix )
                if ( is_file( $directory . '/' . $id . $suffix ) )
                    @unlink( $directory . '/' . $id . $suffix );
        }
    }
}
