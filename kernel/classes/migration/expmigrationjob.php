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
 * One job at a time: start() refuses while a job is running or the installation lock is held (exp:upgrade, a console
 * run). The run itself takes the lock, so two pages pressing Run at once still run once.
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
    );

    /** The flags the administration may pass on, per kind. */
    const FLAGS = array(
        'migrate' => array( 'no-caption', 'no-restore', 'include-uncommitted', 'no-commit', 'no-push', 'no-tag', 'force' ),
        'resume' => array( 'no-caption', 'no-restore', 'include-uncommitted', 'no-commit', 'no-push', 'no-tag', 'force' ),
        'rollback' => array( 'expert', 'no-safety-caption', 'force-no-caption', 'no-verify' ),
    );

    /** The options with a value the administration may pass on, per kind. */
    const VALUES = array(
        'migrate' => array( 'only', 'reason', 'environment' ),
        'resume' => array( 'reason', 'environment' ),
        'rollback' => array( 'confirm', 'parts', 'source', 'environment' ),
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
        if ( $lock->isLocked() && !$lock->isHeld() )
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
        if ( @file_put_contents( $base . '.json', json_encode( $job, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ) === false )
            return $refuse( self::REFUSED_DIRECTORY, $directory );
        @touch( $base . '.progress' );

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
        $job += array( 'kind' => '', 'target' => '', 'user' => '', 'siteaccess' => '', 'pid' => 0, 'started' => 0, 'progress' => $base . '.progress' );
        $job['running'] = self::alive( (int)$job['pid'] );
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
