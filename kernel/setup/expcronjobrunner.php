<?php
/**
 * File containing the expCronjobRunner class.
 *
 * Lists the cronjob parts this installation defines and launches one from the
 * administration interface, leaving it to run after the request has finished.
 *
 * Ported into the kernel from the nl_cronjobs extension
 * (github.com/se7enxweb/nl_cronjobs), which added a tab to the eZ Publish back
 * office for the same purpose. The idea is kept; the mechanics are not:
 *
 * - The extension took its script root from $_SERVER['DOCUMENT_ROOT'], which is
 *   neither the eZ root on a vhost that serves from a subdirectory nor set at
 *   all on the command line. The root here is eZSys::rootDir().
 * - It ran "php runcronjobs.php <part>" with PhpCliPath defaulting to the bare
 *   word "php", and no check that it resolved to anything. A php-fpm process
 *   usually has no "php" on its PATH, so the launch failed silently.
 *   phpBinary() resolves a real cli binary and says so when it cannot.
 *   PHP_BINARY is deliberately not trusted: under fpm it is the fpm binary.
 * - It passed no siteaccess, so every cronjob ran against the default one - not
 *   what an administration interface launching a job for a chosen site wants.
 *   The siteaccess is chosen and passed as -s.
 * - Its Process object called proc_close() in its destructor, which blocks
 *   until the child exits, so the "background" launch held the web request open
 *   for the whole cronjob and timed out on anything slow. The child is detached
 *   through a shell here, so the request returns immediately.
 * - Nothing recorded what had been started, so a second press launched a second
 *   copy of a job already running. A state file records the pid, part,
 *   siteaccess and start time, and a launch is refused while one is running.
 *
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

class expCronjobRunner
{
    /**
     * The part name used for the scripts in cronjob.ini's own
     * [CronjobSettings] group, which runcronjobs.php runs when given no part.
     */
    const GLOBAL_PART = 'global';

    /**
     * Settings read from cronjob.ini [AdminSettings], with the values used when
     * the section is absent so an installation that has not been updated still
     * works.
     */
    private static function setting( $name, $default )
    {
        $ini = eZINI::instance( 'cronjob.ini' );
        if ( !$ini->hasVariable( 'AdminSettings', $name ) )
            return $default;

        $value = $ini->variable( 'AdminSettings', $name );
        if ( is_string( $value ) && trim( $value ) === '' )
            return $default;

        return $value;
    }

    /**
     * A path below the var directory of the current siteaccess, created if it
     * is not there yet. Logs are per installation state, so they belong in var
     * with the rest of it.
     *
     * @param string $relative
     * @return string
     */
    public static function varPath( $relative )
    {
        $path = rtrim( eZSys::varDirectory(), '/' ) . '/' . ltrim( $relative, '/' );
        $dir = dirname( $path );
        if ( !is_dir( $dir ) )
            eZDir::mkdir( $dir, false, true );

        return $path;
    }

    public static function logFile()
    {
        return self::varPath( self::setting( 'LogFile', 'cronjobs/output.log' ) );
    }

    public static function errorFile()
    {
        return self::varPath( self::setting( 'ErrorFile', 'cronjobs/error.log' ) );
    }

    private static function stateFile()
    {
        return self::varPath( 'cronjobs/state.json' );
    }

    /**
     * Parts an operator is not allowed to launch from the interface.
     *
     * cluster_maintenance and unlock are destructive and belong to an
     * administrator on a shell, not to a button.
     *
     * @return array
     */
    public static function forbiddenParts()
    {
        return (array)self::setting( 'ForbiddenParts', array( 'cluster_maintenance', 'unlock' ) );
    }

    /**
     * Every directory a cronjob script may live in, the kernel's and every
     * extension's.
     *
     * @return array
     */
    private static function scriptDirectories()
    {
        $ini = eZINI::instance( 'cronjob.ini' );
        $directories = (array)$ini->variable( 'CronjobSettings', 'ScriptDirectories' );
        $extensionDirectories = (array)$ini->variable( 'CronjobSettings', 'ExtensionDirectories' );

        return array_merge( $directories, eZExtension::expandedPathList( $extensionDirectories, 'cronjobs' ) );
    }

    /**
     * The cronjob parts this installation defines.
     *
     * The global scripts of [CronjobSettings] are presented as a part of their
     * own named "global", because that is what they are to an operator, even
     * though runcronjobs.php addresses them by passing no part at all.
     *
     * Every script is resolved to a file now rather than at launch, so a part
     * naming a script that no longer exists says so on the page instead of
     * failing quietly in a log.
     *
     * @return array
     */
    public static function parts()
    {
        $ini = eZINI::instance( 'cronjob.ini' );
        $directories = self::scriptDirectories();
        $forbidden = self::forbiddenParts();

        $groups = array( self::GLOBAL_PART => 'CronjobSettings' );
        foreach ( array_keys( $ini->groups() ) as $group )
        {
            if ( preg_match( '/^CronjobPart-(.+)$/i', $group, $match ) )
                $groups[$match[1]] = $group;
        }

        $parts = array();
        foreach ( $groups as $name => $group )
        {
            if ( !$ini->hasVariable( $group, 'Scripts' ) )
                continue;

            $scripts = array();
            $missing = 0;
            foreach ( (array)$ini->variable( $group, 'Scripts' ) as $script )
            {
                $path = false;
                foreach ( $directories as $directory )
                {
                    if ( file_exists( $directory . '/' . $script ) )
                    {
                        $path = $directory . '/' . $script;
                        break;
                    }
                }
                if ( $path === false )
                    $missing++;

                $scripts[] = array( 'name' => $script, 'path' => $path );
            }

            if ( !$scripts )
                continue;

            $parts[] = array(
                'name'      => $name,
                'group'     => $group,
                'label'     => self::label( $name ),
                'forbidden' => in_array( $name, $forbidden, true ),
                'scripts'   => $scripts,
                'missing'   => $missing );
        }

        return $parts;
    }

    /**
     * A part name as it should be read by a person: frequent becomes Frequent,
     * cluster_maintenance becomes Cluster maintenance.
     */
    private static function label( $name )
    {
        return ucfirst( str_replace( '_', ' ', $name ) );
    }

    /**
     * Whether a part exists and may be launched from the interface.
     *
     * Everything that reaches a command line goes through this first, so a
     * part name can only ever be one this installation defines.
     *
     * @param string $part
     * @return bool
     */
    public static function isLaunchable( $part )
    {
        foreach ( self::parts() as $candidate )
        {
            if ( $candidate['name'] === $part )
                return !$candidate['forbidden'] && $candidate['missing'] < count( $candidate['scripts'] );
        }

        return false;
    }

    /**
     * The siteaccesses a cronjob may be run for.
     *
     * A cronjob acts on content, and which content depends on the siteaccess it
     * runs under, so this is a choice rather than an assumption. The
     * administration siteaccess is included: some jobs, indexing among them,
     * are normally run under it.
     *
     * @return array
     */
    public static function siteAccessList()
    {
        $ini = eZINI::instance( 'site.ini' );
        $list = array();
        foreach ( array( 'RelatedSiteAccessList', 'AvailableSiteAccessList' ) as $variable )
        {
            if ( $ini->hasVariable( 'SiteAccessSettings', $variable ) )
                $list = array_merge( $list, (array)$ini->variable( 'SiteAccessSettings', $variable ) );
            if ( $list )
                break;
        }

        return array_values( array_unique( array_filter( $list, 'strlen' ) ) );
    }

    /**
     * A php command line binary that can actually be executed.
     *
     * Configured first, then the usual locations. PHP_BINARY is not consulted:
     * in a web request it names the fpm or apache binary, which cannot run a
     * script.
     *
     * @return string|false
     */
    public static function phpBinary()
    {
        $candidates = array();

        $configured = trim( (string)self::setting( 'PhpCliPath', '' ) );
        if ( $configured !== '' )
            $candidates[] = $configured;

        $candidates[] = PHP_BINDIR . '/php';
        $candidates[] = '/usr/bin/php';
        $candidates[] = '/usr/local/bin/php';

        foreach ( $candidates as $candidate )
        {
            if ( $candidate === '' || !is_file( $candidate ) || !is_executable( $candidate ) )
                continue;
            return $candidate;
        }

        return false;
    }

    /**
     * What is running, if anything.
     *
     * @return array running, pid, part, siteaccess, started, elapsed.
     */
    public static function status()
    {
        $empty = array( 'running' => false, 'pid' => 0, 'part' => '', 'siteaccess' => '',
                        'started' => 0, 'elapsed' => 0 );

        $file = self::stateFile();
        if ( !file_exists( $file ) )
            return $empty;

        $state = json_decode( (string)file_get_contents( $file ), true );
        if ( !is_array( $state ) || !isset( $state['pid'] ) )
            return $empty;

        $state = $state + $empty;
        $state['running'] = self::pidIsRunningCronjob( (int)$state['pid'] );
        $state['elapsed'] = $state['started'] ? time() - (int)$state['started'] : 0;

        // A last defence for a host with no /proc to read, where a reused pid
        // cannot be told apart from the job that recorded it: nothing is
        // believed to be running for longer than twice the time a cronjob
        // script is allowed to take. Without it a stale state file could block
        // every launch until somebody deleted it by hand.
        $maxTime = (int)eZINI::instance( 'cronjob.ini' )->variable( 'CronjobSettings', 'MaxScriptExecutionTime' );
        if ( $maxTime > 0 && $state['elapsed'] > 2 * $maxTime )
            $state['running'] = false;

        return $state;
    }

    /**
     * Whether a pid is still one of our cronjobs, rather than merely a live
     * process.
     *
     * Process ids are reused. A state file left behind by a job that ended -
     * after a crash, a reboot, or a kill that never got to rewrite it - will
     * eventually name a pid belonging to something else entirely, and a plain
     * liveness test then reports a cronjob running forever: every card says
     * "Another job is running" and nothing can be launched again. The command
     * line is read as well, so only a process that really is runcronjobs.php
     * counts.
     *
     * Where /proc is not mounted there is nothing to read, so liveness alone
     * has to do; the age check below covers that case instead.
     */
    private static function pidIsRunningCronjob( $pid )
    {
        if ( !self::pidIsAlive( $pid ) )
            return false;

        $cmdline = '/proc/' . (int)$pid . '/cmdline';
        if ( is_readable( $cmdline ) )
        {
            $command = (string)@file_get_contents( $cmdline );
            return strpos( $command, 'runcronjobs.php' ) !== false;
        }

        return true;
    }

    /**
     * Whether a pid is still a live process.
     *
     * /proc is asked first because it needs no signal permission; posix_kill is
     * the fallback where /proc is not mounted.
     */
    private static function pidIsAlive( $pid )
    {
        if ( $pid < 1 )
            return false;

        if ( is_dir( '/proc' ) )
            return is_dir( '/proc/' . $pid );

        if ( function_exists( 'posix_kill' ) )
            return posix_kill( $pid, 0 );

        return false;
    }

    /**
     * Launches a cronjob part, which keeps running after this request ends.
     *
     * @param string $part A part name from parts(), or 'global'.
     * @param string $siteaccess A siteaccess from siteAccessList().
     * @return array ok, message, pid, command.
     */
    public static function launch( $part, $siteaccess )
    {
        $refuse = function ( $message ) { return array( 'ok' => false, 'message' => $message, 'pid' => 0, 'command' => '' ); };

        if ( !self::isLaunchable( $part ) )
            return $refuse( 'No cronjob part named "' . $part . '" can be launched from here.' );

        if ( !in_array( $siteaccess, self::siteAccessList(), true ) )
            return $refuse( 'No siteaccess named "' . $siteaccess . '" is served by this installation.' );

        $status = self::status();
        if ( $status['running'] )
            return $refuse( 'The "' . $status['part'] . '" part is still running as process ' . $status['pid'] . '. Wait for it, or stop it first.' );

        $php = self::phpBinary();
        if ( $php === false )
            return $refuse( 'No php command line binary was found. Set cronjob.ini [AdminSettings] PhpCliPath to its full path.' );

        if ( !function_exists( 'proc_open' ) )
            return $refuse( 'proc_open is disabled, so a cronjob cannot be started from the interface. Run it from a shell.' );

        $root = rtrim( eZSys::rootDir(), '/' );
        if ( $root === '' )
            $root = rtrim( getcwd(), '/' );

        $script = $root . '/runcronjobs.php';
        if ( !file_exists( $script ) )
            return $refuse( 'runcronjobs.php is not where it should be: ' . $script );

        $log = self::logFile();
        $error = self::errorFile();

        // The part is only named when it is not the global one: runcronjobs.php
        // runs the [CronjobSettings] scripts when it is given no part at all.
        $arguments = array( escapeshellarg( $script ), '-s', escapeshellarg( $siteaccess ), '--no-colors' );
        if ( $part !== self::GLOBAL_PART )
            $arguments[] = escapeshellarg( $part );

        $pidFile = self::varPath( 'cronjobs/run.pid' );
        @unlink( $pidFile );

        // The job writes its own process id rather than the shell reporting it.
        // "cd x && nohup job &" backgrounds the whole and-list, so $! there is
        // the pid of a subshell that exits as soon as it has started the job -
        // a pid that is already gone by the time anything checks it, which made
        // the "is one already running" guard useless and let a second copy of a
        // job start on top of the first. Here the inner shell records its own
        // pid and then execs php, so the pid recorded IS the job's.
        $inner = sprintf( 'echo $$ > %s; exec %s %s',
                          escapeshellarg( $pidFile ), escapeshellarg( $php ), implode( ' ', $arguments ) );

        // Detached, so the shell exits at once and the cronjob is reparented
        // rather than held open by this request. Everything the job prints is
        // appended to the log the console reads.
        $command = sprintf(
            'cd %s && nohup sh -c %s >> %s 2>> %s < /dev/null &',
            escapeshellarg( $root ), escapeshellarg( $inner ),
            escapeshellarg( $log ), escapeshellarg( $error ) );

        $header = sprintf( "\n===== %s | part %s | siteaccess %s =====\n",
                           date( 'Y-m-d H:i:s T' ), $part, $siteaccess );
        file_put_contents( $log, $header, FILE_APPEND );

        $descriptors = array( 0 => array( 'pipe', 'r' ),
                              1 => array( 'pipe', 'w' ),
                              2 => array( 'pipe', 'w' ) );
        $process = proc_open( $command, $descriptors, $pipes, $root );
        if ( !is_resource( $process ) )
            return $refuse( 'The cronjob could not be started.' );

        fclose( $pipes[0] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        // Safe to close: this waits for the shell, which has already exited,
        // not for the cronjob it left running.
        proc_close( $process );

        // Give the job a moment to record itself. Two seconds is generous for
        // a shell that does one write before exec.
        $pid = 0;
        for ( $attempt = 0; $attempt < 40; $attempt++ )
        {
            clearstatcache();
            if ( file_exists( $pidFile ) )
            {
                $pid = (int)trim( (string)file_get_contents( $pidFile ) );
                if ( $pid > 0 )
                    break;
            }
            usleep( 50000 );
        }

        if ( $pid < 1 )
            return $refuse( 'The cronjob was started but did not report its process id, so it cannot be followed or stopped from here. Check ' . $error . '.' );

        file_put_contents( self::stateFile(), json_encode( array(
            'pid'        => $pid,
            'part'       => $part,
            'siteaccess' => $siteaccess,
            'started'    => time(),
            'log'        => $log ) ) );

        return array( 'ok' => true, 'pid' => $pid, 'command' => $command,
                      'message' => 'Started the "' . $part . '" part for ' . $siteaccess . ' as process ' . $pid . '.' );
    }

    /**
     * Asks a running cronjob to stop.
     *
     * A term rather than a kill: a cronjob part holds a mutex per script, and a
     * process killed outright leaves it locked until the lock ages out.
     *
     * @return array ok, message.
     */
    public static function stop()
    {
        $status = self::status();
        if ( !$status['running'] )
            return array( 'ok' => false, 'message' => 'No cronjob is running.' );

        if ( !function_exists( 'posix_kill' ) )
            return array( 'ok' => false, 'message' => 'posix_kill is not available, so the process cannot be signalled from here.' );

        if ( !posix_kill( (int)$status['pid'], 15 ) )
            return array( 'ok' => false, 'message' => 'Process ' . $status['pid'] . ' could not be signalled. It may belong to another user.' );

        return array( 'ok' => true, 'message' => 'Asked process ' . $status['pid'] . ' to stop.' );
    }

    /**
     * Empties the log files and forgets the last run.
     */
    public static function clearLogs()
    {
        foreach ( array( self::logFile(), self::errorFile() ) as $file )
            file_put_contents( $file, '' );

        $state = self::stateFile();
        $status = self::status();
        if ( file_exists( $state ) && !$status['running'] )
            @unlink( $state );

        return array( 'ok' => true, 'message' => 'Cleared the cronjob output and error logs.' );
    }

    /**
     * The last lines of a log file.
     *
     * Reads from the end rather than loading the file, because a cronjob log
     * left to itself for a month is not small.
     *
     * @param string $file
     * @param int $maxBytes
     * @return string
     */
    public static function tail( $file, $maxBytes = 65536 )
    {
        if ( !file_exists( $file ) )
            return '';

        $size = filesize( $file );
        $handle = @fopen( $file, 'rb' );
        if ( !$handle )
            return '';

        if ( $size > $maxBytes )
            fseek( $handle, $size - $maxBytes );
        $content = (string)stream_get_contents( $handle );
        fclose( $handle );

        if ( $size > $maxBytes )
            $content = "... earlier output not shown ...\n" . substr( $content, (int)strpos( $content, "\n" ) + 1 );

        return $content;
    }
}

?>
