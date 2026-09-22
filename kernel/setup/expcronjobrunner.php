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


if ( !class_exists( 'expCronjobRunner', false ) ) {
class expCronjobRunner
{
    /**
     * The part name used for the scripts in cronjob.ini's own
     * [CronjobSettings] group, which runcronjobs.php runs when given no part.
     */
    const GLOBAL_PART = 'global';

    /**
     * Runs one of this class's own static methods and always comes back with
     * something to report.
     *
     * Whatever goes wrong underneath - a disk that will not take the log, a php
     * binary that has gone away, a process that cannot be signalled - the
     * answer has to be an answer. The console is waiting for one, and an error
     * page tells it nothing it can act on while leaving its controls disabled
     * with nothing running.
     *
     * It lives here rather than in the view because a view file is included
     * once per request, and a plain function declared in one cannot be declared
     * again in a process that runs the view more than once.
     *
     * @param string $method
     * @param array $arguments
     * @return array ok, message.
     */
    public static function attempt( $method, array $arguments = array() )
    {
        try
        {
            $result = call_user_func_array( array( 'expCronjobRunner', $method ), $arguments );

            if ( !is_array( $result ) || !isset( $result['ok'] ) )
                return array( 'ok' => false, 'message' => 'The cronjob console got no answer from ' . $method . '.' );

            if ( !isset( $result['message'] ) )
                $result['message'] = $result['ok'] ? 'Done.' : 'It did not work, and did not say why.';

            return $result;
        }
        catch ( Exception $e )
        {
            eZDebug::writeError( $e->getMessage(), __METHOD__ );
            return array( 'ok' => false, 'message' => 'The cronjob console failed: ' . $e->getMessage() );
        }
    }

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
        return self::launchTarget( $part, false, $siteaccess );
    }

    /**
     * Runs one named script of a part, rather than the whole part.
     *
     * @param string $part
     * @param string $script A script name from that part.
     * @param string $siteaccess
     * @return array ok, message, pid.
     */
    public static function launchScript( $part, $script, $siteaccess )
    {
        return self::launchTarget( $part, $script, $siteaccess );
    }

    /**
     * Starts a part, or one script of it, and leaves it running.
     *
     * @param string $part
     * @param string|false $script false for the whole part.
     * @param string $siteaccess
     * @return array
     */
    private static function launchTarget( $part, $script, $siteaccess )
    {
        $refuse = function ( $message ) { return array( 'ok' => false, 'message' => $message, 'pid' => 0, 'command' => '' ); };

        if ( !self::isLaunchable( $part ) )
            return $refuse( 'No cronjob part named "' . $part . '" can be launched from here.' );

        if ( $script !== false && !self::partHasScript( $part, $script ) )
            return $refuse( 'The "' . $part . '" part has no script called "' . $script . '".' );

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

        $root = self::installationRoot();

        $runner = $root . '/runcronjobs.php';
        if ( !file_exists( $runner ) )
            return $refuse( 'runcronjobs.php is not where it should be: ' . $runner );

        // A log of its own, so "what did the sitemap job say last night" is a
        // question with one answer rather than a search through everything that
        // has run since.
        $log = self::targetLogFile( $part, $script );
        $error = self::errorFile();

        $arguments = array( escapeshellarg( $runner ), '-s', escapeshellarg( $siteaccess ), '--no-colors' );
        if ( $script !== false )
        {
            // One script. The part is not named as well: runcronjobs.php looks
            // the script up in the directories cronjob.ini gives it.
            $arguments[] = '--script=' . escapeshellarg( $script );
        }
        else if ( $part !== self::GLOBAL_PART )
        {
            // The part is only named when it is not the global one:
            // runcronjobs.php runs the [CronjobSettings] scripts when it is
            // given no part at all.
            $arguments[] = escapeshellarg( $part );
        }

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

        $header = sprintf( "\n===== %s | %s | siteaccess %s =====\n",
                           date( 'Y-m-d H:i:s T' ),
                           $script === false ? 'part ' . $part : 'script ' . $script . ' of ' . $part,
                           $siteaccess );
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

        $started = time();
        file_put_contents( self::stateFile(), json_encode( array(
            'pid'        => $pid,
            'part'       => $part,
            'script'     => $script === false ? '' : $script,
            'siteaccess' => $siteaccess,
            'started'    => $started,
            'log'        => $log ) ) );

        self::recordStart( array( 'part' => $part,
                                  'script' => $script === false ? '' : $script,
                                  'siteaccess' => $siteaccess,
                                  'pid' => $pid,
                                  'started' => $started,
                                  'finished' => null,
                                  'errors' => 0,
                                  'log' => $log ) );

        $what = $script === false ? 'the "' . $part . '" part' : $script;
        return array( 'ok' => true, 'pid' => $pid, 'command' => $command,
                      'message' => 'Started ' . $what . ' for ' . $siteaccess . ' as process ' . $pid . '.' );
    }

    /**
     * Whether a part lists the given script.
     */
    public static function partHasScript( $part, $script )
    {
        foreach ( self::parts() as $candidate )
        {
            if ( $candidate['name'] !== $part )
                continue;

            foreach ( $candidate['scripts'] as $entry )
            {
                if ( $entry['name'] === $script )
                    return $entry['path'] !== false;
            }
        }

        return false;
    }

    /**
     * A name that is safe to put in a path.
     */
    private static function safeName( $name )
    {
        $name = preg_replace( '/[^A-Za-z0-9_.-]/', '_', (string)$name );
        return $name === '' ? 'unnamed' : $name;
    }

    /**
     * Where the output of one part, or one script of it, is kept.
     *
     * A log each, so what a particular job said last night is one file rather
     * than a search through everything that has run since. A whole part writes
     * to _part.log beside the scripts it ran.
     *
     * @param string $part
     * @param string|false $script
     * @return string
     */
    public static function targetLogFile( $part, $script = false )
    {
        $name = $script === false ? '_part' : basename( $script, '.php' );

        return self::varPath( 'cronjobs/' . self::safeName( $part ) . '/' . self::safeName( $name ) . '.log' );
    }

    /**
     * The file the run history is kept in.
     */
    public static function historyFile()
    {
        return self::varPath( 'cronjobs/history.json' );
    }

    /**
     * Adds a run to the history, newest first.
     */
    private static function recordStart( array $entry )
    {
        $history = self::readHistory();
        array_unshift( $history, $entry );

        // Enough to see a pattern, not enough to grow without end.
        $keep = (int)self::setting( 'HistoryLength', 100 );
        if ( $keep < 1 )
            $keep = 100;
        $history = array_slice( $history, 0, $keep );

        self::writeHistory( $history );
    }

    private static function readHistory()
    {
        $file = self::historyFile();
        if ( !file_exists( $file ) )
            return array();

        $history = json_decode( (string)file_get_contents( $file ), true );

        return is_array( $history ) ? $history : array();
    }

    private static function writeHistory( array $history )
    {
        $encoded = json_encode( $history );
        if ( $encoded !== false )
            file_put_contents( self::historyFile(), $encoded );
    }

    /**
     * What has run, newest first.
     *
     * A run is recorded when it starts, because that is the only moment this
     * process knows about it - the job outlives the request that asked for it.
     * When it has ended, nothing tells us; so a run that is no longer alive is
     * settled here, on the first read after it finished: the time taken comes
     * from its log, and the errors from counting what the log says.
     *
     * @param int $limit
     * @return array
     */
    public static function history( $limit = 20 )
    {
        $history = self::readHistory();
        $changed = false;

        foreach ( $history as $index => $entry )
        {
            if ( isset( $entry['finished'] ) && $entry['finished'] !== null )
                continue;

            $pid = isset( $entry['pid'] ) ? (int)$entry['pid'] : 0;
            if ( $pid > 0 && self::pidIsRunningCronjob( $pid ) )
                continue;   // still going

            $log = isset( $entry['log'] ) ? $entry['log'] : '';
            $finished = ( $log !== '' && file_exists( $log ) ) ? filemtime( $log ) : time();
            if ( isset( $entry['started'] ) && $finished < $entry['started'] )
                $finished = $entry['started'];

            $history[$index]['finished'] = $finished;
            $history[$index]['errors'] = self::countErrors( $log, isset( $entry['started'] ) ? $entry['started'] : 0 );
            $changed = true;
        }

        if ( $changed )
            self::writeHistory( $history );

        foreach ( $history as $index => $entry )
        {
            $history[$index]['running'] = ( $entry['finished'] === null );
            $history[$index]['seconds'] = $entry['finished'] === null
                                        ? time() - (int)$entry['started']
                                        : (int)$entry['finished'] - (int)$entry['started'];
            $history[$index]['label'] = $entry['script'] !== ''
                                      ? $entry['script'] . ' (' . $entry['part'] . ')'
                                      : self::label( $entry['part'] );
        }

        return array_slice( $history, 0, max( 1, (int)$limit ) );
    }

    /**
     * How many lines of a log look like something went wrong.
     *
     * Only the part of the log written by this run is counted, which is why
     * the run's start time is passed in: a log is appended to, and the errors
     * of a week ago are not the errors of this run.
     */
    private static function countErrors( $log, $since )
    {
        if ( $log === '' || !file_exists( $log ) )
            return 0;

        $text = self::tail( $log, 262144 );
        $lines = explode( "\n", $text );

        // Everything after the last run header, which carries the time.
        $start = 0;
        foreach ( $lines as $index => $line )
        {
            if ( strpos( $line, '=====' ) === 0 )
                $start = $index;
        }
        $lines = array_slice( $lines, $start );

        $errors = 0;
        foreach ( $lines as $line )
        {
            if ( preg_match( '/\b(error|fatal|exception|failed|failure|warning)\b/i', $line ) )
                $errors++;
        }

        return $errors;
    }

    /**
     * Scripts sitting in the cronjob directories that no part names.
     *
     * They are on disk and they never run, because nothing in cronjob.ini
     * refers to them. Worth showing beside the ones that do run, so the
     * difference between what is installed and what is scheduled is visible
     * rather than something to be worked out.
     *
     * @return array
     */
    public static function availableScripts()
    {
        $referenced = array();
        foreach ( self::parts() as $part )
        {
            foreach ( $part['scripts'] as $entry )
                $referenced[$entry['name']] = true;
        }

        $found = array();
        foreach ( self::scriptDirectories() as $directory )
        {
            foreach ( (array)glob( rtrim( $directory, '/' ) . '/*.php' ) as $file )
            {
                $name = basename( $file );
                if ( isset( $referenced[$name] ) || isset( $found[$name] ) )
                    continue;

                $found[$name] = array( 'name' => $name,
                                       'path' => $file,
                                       'directory' => dirname( $file ) );
            }
        }

        ksort( $found );

        return array_values( $found );
    }

    /**
     * The crontab line that would run a part on a schedule.
     *
     * Written out in full, with the same php binary and the same root this
     * console uses, so it can be pasted into a crontab and be right rather
     * than nearly right.
     *
     * The schedule is a suggestion, and can be set per part in cronjob.ini
     * [AdminSettings] as CrontabSchedule_<part>.
     *
     * @param string $part
     * @param string $siteaccess
     * @return string
     */
    /**
     * The crontab as it actually is, for the user this site runs as.
     *
     * Read, not guessed. The lines this console suggests are generated from
     * this installation's own paths, and a generated line says nothing about
     * whether anything is scheduled - so what is really there is read as well,
     * and the two are shown apart.
     *
     * @return array available, lines, note.
     */
    /**
     * The account the web server, and so this page, runs as.
     *
     * Which matters, because crontab -l reads that account's crontab and no
     * other - entries under a different user are invisible from here.
     *
     * @return string the user name, or the numeric id when it cannot be named.
     */
    public static function systemUser()
    {
        if ( function_exists( 'posix_geteuid' ) && function_exists( 'posix_getpwuid' ) )
        {
            $info = @posix_getpwuid( posix_geteuid() );
            if ( is_array( $info ) && isset( $info['name'] ) && $info['name'] !== '' )
                return $info['name'];
        }

        $env = getenv( 'USER' );
        if ( $env !== false && $env !== '' )
            return $env;

        return function_exists( 'posix_geteuid' ) ? (string) posix_geteuid() : 'unknown';
    }

    public static function installedCrontab()
    {
        // exec, not shell_exec: an empty crontab and an unreadable one both give
        // no output, and only the exit status tells them apart. crontab -l exits
        // 0 for a crontab that exists, 1 when the user has none.
        if ( !function_exists( 'exec' ) )
            return array( 'available' => false, 'lines' => array(),
                          'note' => 'exec is disabled, so the crontab cannot be read from here.' );

        $raw = array();
        $status = 1;
        @exec( 'crontab -l 2>/dev/null', $raw, $status );

        if ( $status !== 0 )
            return array( 'available' => false, 'lines' => array(),
                          'note' => 'The user this site runs as (' . self::systemUser() . ') has no crontab, '
                                  . 'or crontab is not on the path. The entries below would be added to it.' );

        $lines = array();
        foreach ( $raw as $line )
        {
            $line = trim( $line );
            if ( $line === '' || $line[0] === '#' )
                continue;
            $lines[] = $line;
        }

        return array( 'available' => true, 'lines' => $lines, 'note' => '' );
    }

    /**
     * Which parts of this installation are actually scheduled, and by what line.
     *
     * A crontab holds entries for every site on the machine, so only lines that
     * name this installation's own root count. The part is whatever bare word
     * is left after the script and its options.
     *
     * @return array part name => the crontab line that runs it.
     */
    public static function scheduledParts()
    {
        $crontab = self::installedCrontab();
        if ( !$crontab['available'] )
            return array();

        $root = self::installationRoot();
        $scheduled = array();

        foreach ( $crontab['lines'] as $line )
        {
            if ( strpos( $line, 'runcronjobs.php' ) === false )
                continue;
            if ( strpos( $line, $root ) === false )
                continue;   // some other installation on the same machine

            // Everything after the script name, minus its options, leaves the
            // part - or nothing at all, which is the global one.
            $after = substr( $line, strpos( $line, 'runcronjobs.php' ) + strlen( 'runcronjobs.php' ) );
            $after = str_replace( array( ';', '&&' ), ' ', $after );
            $after = preg_replace( '#>\s*/dev/null.*#', '', $after );

            $part = self::GLOBAL_PART;
            $tokens = preg_split( '/\s+/', trim( $after ) );
            for ( $i = 0; $i < count( $tokens ); $i++ )
            {
                $token = $tokens[$i];
                if ( $token === '' )
                    continue;
                if ( $token === '-s' || $token === '--siteaccess' )
                {
                    $i++;   // the siteaccess that follows it
                    continue;
                }
                if ( $token[0] === '-' )
                    continue;

                $part = $token;
                break;
            }

            if ( !isset( $scheduled[$part] ) )
                $scheduled[$part] = $line;
        }

        return $scheduled;
    }

    /**
     * Where this installation actually lives.
     *
     * Not eZSys::rootDir(), which is the document root of whatever host served
     * the request: the administration interface is reached through its own
     * vhost, whose directory is a set of symlinks into the real one, so a
     * crontab line built from it named a path that works by accident and reads
     * as the wrong installation. This class sits in kernel/setup, so the root
     * is two directories above it, resolved through any symlinks.
     *
     * @return string
     */
    public static function installationRoot()
    {
        $root = realpath( dirname( __FILE__ ) . '/../..' );
        if ( $root !== false && $root !== '' )
            return rtrim( $root, '/' );

        $root = rtrim( (string)eZSys::rootDir(), '/' );

        return $root !== '' ? $root : rtrim( getcwd(), '/' );
    }

    /**
     * The schedule alone, without the command.
     */
    public static function crontabSchedule( $part )
    {
        $defaults = array( 'frequent' => '*/5 * * * *',
                           self::GLOBAL_PART => '*/15 * * * *',
                           'infrequent' => '17 * * * *' );

        return (string)self::setting( 'CrontabSchedule_' . $part,
                                      isset( $defaults[$part] ) ? $defaults[$part] : '0 * * * *' );
    }

    public static function crontabLine( $part, $siteaccess )
    {
        $schedule = self::crontabSchedule( $part );

        $php = self::phpBinary();
        if ( $php === false )
            $php = 'php';

        $command = $php . ' runcronjobs.php -s ' . $siteaccess;
        if ( $part !== self::GLOBAL_PART )
            $command .= ' ' . $part;

        return $schedule . ' cd ' . self::installationRoot() . ' && ' . $command . ' >/dev/null 2>&1';
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
}


?>
