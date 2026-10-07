<?php
/**
 * File containing the expSetupLog class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

/**
 * var/log/setup.log: one readable record per installation run.
 *
 * A run opens with an introduction and the environment, records each step
 * (">> Step" ... "<< Step status, time, errors, warnings") with what it wrote
 * to error.log and warning.log (where eZDebug writes them: var/log, or the log
 * directory of the site with site.ini [FileSettings] UseGlobalLogDir=disabled)
 * and what PHP raised, a hint beside
 * each known problem, and - after an installation - health checks that catch
 * a run which returned without an error but left the site unusable. It closes
 * with a summary: every step, the result, each distinct problem once, and a
 * last line saying whether the site is ready.
 *
 * The command-line kickstarter is one process: start() .. finish(). The web
 * setup wizard is one request per step: the first request starts the run, the
 * others resume() it from var/log/setup-run.state and suspend() it at the end
 * of the request; the request that finishes the installation closes it.
 *
 * Writing the log never stops a setup: every public method swallows its own
 * failures. Passwords are masked in everything it records.
 */
class expSetupLog
{
    const LOG_FILE = 'setup.log';
    const LOG_DIR = 'var/log';
    const STATE_FILE = 'setup-run.state';

    protected static $run = null;
    protected static $previousErrorHandler = null;

    /** Known problems: a pattern in the message, and what to do about it. */
    protected static $hints = array(
        '/database is locked|SQLITE_BUSY/i' => 'Another process holds the SQLite database (a web server or Velocity worker). Stop them or retry; the driver waits busy_timeout ms.',
        '/table \S+ already exists/i' => 'The schema was inserted over existing tables (a schema loaded twice, or a database not emptied). Harmless when the table check below finds nothing missing.',
        '/index \S+ already exists/i' => 'An index name used twice. On SQLite index names are database-wide; Exponential qualifies them as <table>__<name>.',
        '/permission denied|failed to open stream|is not writable|could not be written|Couldn.t create/i' => 'A file or directory the setup must write is not writable by the user running it. Check owner and mode of var/, settings/ and the log directory.',
        '/Allowed memory size .* exhausted/i' => 'PHP ran out of memory: raise memory_limit for the setup (the demo content needs several hundred MB).',
        '/Maximum execution time/i' => 'The run hit max_execution_time: raise it, or use the command-line kickstarter, which has no limit.',
        '/could not find driver|Call to undefined function (mysqli_|sqlite|pg_)/i' => 'The PHP extension for the chosen database is not loaded (pdo_sqlite / sqlite3, mysqli, pgsql).',
        '/Access denied for user|password authentication failed/i' => 'The database refused the credentials in the database settings.',
        '/Unknown database|database .* does not exist/i' => 'The database named in the settings does not exist; create it or let the installer do so.',
        '/Class ["\']?\w+["\']? not found/i' => 'A class is missing from the autoload arrays: php bin/php/ezpgenerateautoloads.php -e (and -k for the kernel).',
        '/Undefined module/i' => 'A request reached a module that is not enabled for this siteaccess (a URL alias or an extension not active yet). Usually harmless during setup.',
        '/Can.t initialize (\S+) database/i' => 'An extension schema did not load completely. The table check below says whether anything is missing.',
        '/UNIQUE constraint failed|Duplicate entry/i' => 'A row was inserted twice: data loaded over existing data, or a primary key that is too narrow.',
        '/no such table/i' => 'A table the code expects was never created: see the table check below.',
    );

    public static function start( $mode, array $context = array() )
    {
        try
        {
            return self::doStart( $mode, $context );
        }
        catch ( Throwable $e )
        {
            return null; // the setup log never stops a setup
        }
    }

    protected static function doStart( $mode, array $context = array() )
    {
        $id = date( 'Ymd-His' ) . '-' . substr( md5( uniqid( '', true ) ), 0, 6 );
        self::$run = array( 'id' => $id, 'mode' => $mode, 'started' => microtime( true ),
                            'steps' => array(), 'current' => null, 'problems' => array(),
                            'checks' => array(), 'php' => 0, 'finished' => false );

        self::line( str_repeat( '=', 78 ) );
        self::line( 'BEGIN setup run ' . $id . ' (' . $mode . ')' );
        self::line( str_repeat( '=', 78 ) );
        foreach ( self::introduction() as $l )
            self::line( '  ' . $l );
        self::line( '-- environment' );
        foreach ( self::environment() + $context as $key => $value )
            self::line( sprintf( '  %-22s %s', $key, self::mask( $value ) ) );

        self::$previousErrorHandler = set_error_handler( array( __CLASS__, 'phpError' ) );
        register_shutdown_function( array( __CLASS__, 'shutdown' ) );
        self::markErrorLog( 'BEGIN setup run ' . $id . ' (' . $mode . '). Until "END setup run ' . $id .
                            '", entries below that end in "(setup ' . $id . ', ...)" come from it. Its summary: var/log/setup.log' );
        self::updateLogContext();
        return $id;
    }

    public static function stepBegin( $step )
    {
        try
        {
            return self::doStepBegin( $step );
        }
        catch ( Throwable $e )
        {
            return null; // the setup log never stops a setup
        }
    }

    protected static function doStepBegin( $step )
    {
        if ( !self::$run )
            return;
        if ( self::$run['current'] )
            self::stepEnd( 'unfinished' );
        // The kernel logs are read where eZDebug writes them when the step begins, from the size they have then
        $logDir = self::kernelLogDir();
        self::$run['current'] = array( 'name' => $step, 'started' => microtime( true ),
                                       'problems' => count( self::$run['problems'] ), 'logdir' => $logDir,
                                       'error' => self::size( 'error.log', $logDir ), 'warning' => self::size( 'warning.log', $logDir ) );
        self::line( '>> ' . $step, $step );
        self::updateLogContext();
    }

    public static function stepEnd( $status )
    {
        try
        {
            return self::doStepEnd( $status );
        }
        catch ( Throwable $e )
        {
            return null; // the setup log never stops a setup
        }
    }

    protected static function doStepEnd( $status )
    {
        if ( !self::$run || !self::$run['current'] )
            return;
        // A count of repeated entries still pending belongs to this step
        if ( method_exists( 'eZDebug', 'flushLogRepeats' ) )
            eZDebug::flushLogRepeats();
        $step = self::$run['current'];
        // Each distinct entry once, with how often and when it was written
        foreach ( array( 'ERROR' => array( 'error.log', 'error' ), 'WARNING' => array( 'warning.log', 'warning' ) ) as $level => $log )
        {
            $entries = self::since( $log[0], $step[$log[1]], isset( $step['logdir'] ) ? $step['logdir'] : null );
            $ours = array_filter( $entries, function ( $e ) { return !empty( $e['ours'] ); } );
            $others = array_filter( $entries, function ( $e ) { return empty( $e['ours'] ); } );
            foreach ( self::group( $ours ) as $g )
                self::problem( $level, $g['message'] . ( $g['count'] > 1
                    ? sprintf( ' (x%d, %s .. %s)', $g['count'], $g['first'], $g['last'] )
                    : ( $g['first'] !== '' ? ' (at ' . $g['first'] . ')' : '' ) ), $step['name'], $g['count'] );
            // Written by other processes meanwhile (requests that reached the
            // site while it was being installed): listed, not counted
            foreach ( self::group( $others ) as $g )
                self::line( sprintf( '   (not this run) %s from another request while installing: %s%s', strtolower( $level ),
                                     self::shorten( $g['message'], 200 ), $g['count'] > 1 ? ' (x' . $g['count'] . ')' : '' ), $step['name'] );
            if ( $others )
                self::$run['others'] = ( isset( self::$run['others'] ) ? self::$run['others'] : 0 ) + count( $others );
        }
        $mine = array_slice( self::$run['problems'], $step['problems'] );
        $errors = self::occurrences( $mine, 'ERROR' );
        $warnings = self::occurrences( $mine, 'WARNING' );
        $seconds = microtime( true ) - $step['started'];
        self::$run['steps'][] = array( 'name' => $step['name'], 'status' => $status, 'seconds' => $seconds,
                                       'errors' => $errors, 'warnings' => $warnings );
        self::line( sprintf( '<< %s %s, %.3fs, %d errors, %d warnings', $step['name'], $status, $seconds,
                             $errors, $warnings ), $step['name'] );
        self::$run['current'] = null;
        self::updateLogContext();
    }

    /** A problem: logged in its step, with a hint when it is a known one. */
    public static function problem( $level, $message, $step = null, $count = 1 )
    {
        try
        {
            return self::doProblem( $level, $message, $step, $count );
        }
        catch ( Throwable $e )
        {
            return null; // the setup log never stops a setup
        }
    }

    protected static function doProblem( $level, $message, $step = null, $count = 1 )
    {
        if ( !self::$run )
            return;
        if ( $step === null && self::$run['current'] )
            $step = self::$run['current']['name'];
        $hint = self::hint( $message );
        self::$run['problems'][] = array( 'level' => $level, 'step' => $step, 'message' => $message, 'hint' => $hint, 'count' => max( 1, (int)$count ) );
        $message = self::mask( $message );
        self::$run['problems'][count( self::$run['problems'] ) - 1]['message'] = $message;
        self::line( str_pad( $level, 7 ) . ' ' . $message, $step );
        if ( $hint )
            self::line( '        hint: ' . $hint, $step );
    }

    /** The web setup: continue the run an earlier request started. False when there is none. */
    public static function resume()
    {
        try
        {
            $f = self::setupLogDir() . '/' . self::STATE_FILE;
            if ( self::$run || !file_exists( $f ) )
                return (bool)self::$run;
            $state = @unserialize( (string)file_get_contents( $f ) );
            if ( !is_array( $state ) || empty( $state['id'] ) || !empty( $state['finished'] ) )
                return false;
            // started() is a wall-clock time, carried over as is
            self::$run = $state;
            self::$previousErrorHandler = set_error_handler( array( __CLASS__, 'phpError' ) );
            register_shutdown_function( array( __CLASS__, 'shutdown' ) );
            self::updateLogContext();
            return true;
        }
        catch ( Throwable $e )
        {
            return false;
        }
    }

    /** The web setup: keep the run for the next request. */
    public static function suspend()
    {
        try
        {
            if ( !self::$run || self::$run['finished'] )
                return;
            if ( self::$run['current'] )
                self::doStepEnd( 'shown' );
            $f = self::setupLogDir() . '/' . self::STATE_FILE;
            @file_put_contents( $f, serialize( self::$run ), LOCK_EX );
            self::giveToVarOwner( $f );
            self::$run['suspended'] = true;
            // The wizard's page is sent: what this process logs from now on is not the run's
            self::clearLogContext();
        }
        catch ( Throwable $e )
        {
        }
    }

    /** The web setup's first request: close a run left unfinished by an abandoned wizard, start a new one. */
    public static function startWeb( array $context = array() )
    {
        if ( self::resume() )
            self::finish( 'ABANDONED (the wizard was started again)' );
        return self::start( 'web setup wizard', $context );
    }

    /** @return string|null the id of the run in progress (started or resumed) */
    public static function runId()
    {
        return self::$run && !empty( self::$run['id'] ) ? (string)self::$run['id'] : null;
    }

    /** What the setup chose so far, from the wizard's persistence list: never a password. */
    public static function context( $persistenceList )
    {
        $c = array();
        if ( !is_array( $persistenceList ) )
            return $c;
        if ( isset( $persistenceList['chosen_site_package'][0] ) )
            $c['site package'] = $persistenceList['chosen_site_package'][0];
        if ( isset( $persistenceList['database_info']['type'] ) )
            $c['database'] = $persistenceList['database_info']['type'] . ( isset( $persistenceList['database_info']['database'] ) ? ' ' . $persistenceList['database_info']['database'] : '' );
        if ( isset( $persistenceList['regional_info']['primary_language'] ) )
            $c['primary language'] = $persistenceList['regional_info']['primary_language'];
        if ( isset( $persistenceList['regional_info']['languages'] ) && is_array( $persistenceList['regional_info']['languages'] ) )
            $c['site languages'] = implode( ', ', $persistenceList['regional_info']['languages'] );
        if ( isset( $persistenceList['package_info']['language_map'] ) && is_array( $persistenceList['package_info']['language_map'] ) )
        {
            $map = array();
            foreach ( $persistenceList['package_info']['language_map'] as $from => $to )
                if ( $from !== $to )
                    $map[] = $from . ' -> ' . $to;
            $c['package language map'] = $map ? implode( ', ', $map ) : 'none needed';
        }
        return $c;
    }

    /** Record each choice of the setup (see context()) the first time it is known. */
    public static function noteContext( $persistenceList )
    {
        try
        {
            if ( !self::$run )
                return;
            foreach ( self::context( $persistenceList ) as $key => $value )
                if ( !isset( self::$run['context'][$key] ) )
                {
                    self::$run['context'][$key] = $value;
                    self::note( $key . ': ' . self::mask( $value ) );
                }
        }
        catch ( Throwable $e )
        {
        }
    }

    public static function note( $message )
    {
        if ( self::$run )
            self::line( $message, self::$run['current'] ? self::$run['current']['name'] : null );
    }

    /**
     * Close the run. $installed: the site was created, so the health checks
     * run - an installation that returned without an error can still have
     * left the site unusable.
     */
    public static function finish( $status, $installed = false )
    {
        try
        {
            return self::doFinish( $status, $installed );
        }
        catch ( Throwable $e )
        {
            return null; // the setup log never stops a setup
        }
    }

    protected static function doFinish( $status, $installed = false )
    {
        if ( !self::$run || self::$run['finished'] )
            return;
        if ( self::$run['current'] )
            self::stepEnd( 'unfinished' );
        self::$run['finished'] = true;

        if ( $installed )
        {
            self::line( '-- checks' );
            foreach ( self::healthChecks() as $c )
            {
                self::$run['checks'][] = $c;
                self::line( sprintf( '  %-4s %s', $c[0], $c[1] ) );
                if ( $c[0] !== 'PASS' && $c[2] !== '' )
                    self::line( '       what to do: ' . $c[2] );
            }
        }

        $seconds = microtime( true ) - self::$run['started'];
        $errors = self::occurrences( self::$run['problems'], 'ERROR' );
        $warnings = self::occurrences( self::$run['problems'], 'WARNING' );
        $failedChecks = count( array_filter( self::$run['checks'], function ( $c ) { return $c[0] === 'FAIL'; } ) );
        $warnChecks = count( array_filter( self::$run['checks'], function ( $c ) { return $c[0] === 'WARN'; } ) );

        self::line( '-- summary' );
        foreach ( self::$run['steps'] as $s )
            self::line( sprintf( '  %-24s %-10s %8.3fs  %2d errors  %2d warnings', $s['name'], $s['status'], $s['seconds'], $s['errors'], $s['warnings'] ) );
        $problems = self::distinctProblems();
        if ( $problems )
        {
            self::line( '  problems, first seen:' );
            foreach ( array_slice( $problems, 0, 60 ) as $p )
                self::line( sprintf( '    %s %s [%s]%s', $p['level'], self::shorten( preg_replace( '/ \((x\d+|at) .*\)$/', '', $p['message'] ), 150 ), $p['step'],
                                     $p['count'] > 1 ? ' x' . $p['count'] : '' ) );
            if ( count( $problems ) > 60 )
                self::line( '    ... and ' . ( count( $problems ) - 60 ) . ' more distinct problems above' );
        }
        // The verdict, framed so it cannot be missed
        $passedChecks = count( array_filter( self::$run['checks'], function ( $c ) { return $c[0] === 'PASS'; } ) );
        $result = strtoupper( $status ) . sprintf( ' - %d steps in %.1fs - %d %s - %d %s', count( self::$run['steps'] ), $seconds,
                  $errors, $errors === 1 ? 'error' : 'errors', $warnings, $warnings === 1 ? 'warning' : 'warnings' );
        if ( $installed )
            $result .= sprintf( ' - checks: %d passed, %d failed%s', $passedChecks, $failedChecks, $warnChecks ? ', ' . $warnChecks . ' to look at' : '' );
        if ( $installed && $failedChecks )
            $next = 'The site is installed but not usable yet: see the FAIL lines under "-- checks".';
        elseif ( $installed && $errors )
            $next = sprintf( 'The site passed its checks, but %d %s logged while installing: read "problems, first seen" above; each is a lead.', $errors, $errors === 1 ? 'error was' : 'errors were' );
        elseif ( $installed && $warnings )
            $next = sprintf( 'The site is ready. %d %s logged: see "problems, first seen" above.', $warnings, $warnings === 1 ? 'warning was' : 'warnings were' );
        elseif ( $installed )
            $next = 'The site is ready.';
        elseif ( strpos( $status, 'ABORTED' ) === 0 || strpos( $status, 'FAILED' ) === 0 )
            $next = 'Nothing was left finished: the first ERROR above is where it went wrong.';
        else
            $next = '';
        self::line( str_repeat( '#', 78 ) );
        self::line( 'RESULT  ' . $result );
        if ( $next !== '' )
            self::line( 'NEXT    ' . $next );
        if ( !empty( self::$run['others'] ) )
            self::line( sprintf( 'NOTE    %d %s in error.log came from other requests while the site was being installed (not counted above: marked "(not this run)").',
                                 self::$run['others'], self::$run['others'] === 1 ? 'entry' : 'entries' ) );
        self::line( str_repeat( '#', 78 ) );
        self::line( 'END setup run ' . self::$run['id'] );
        self::line( '' );
        self::clearLogContext();
        self::markErrorLog( 'END setup run ' . self::$run['id'] . ': ' . $result . ( $next !== '' ? "\n" . $next : '' ) . "\nIts summary: var/log/setup.log" );
        @unlink( self::setupLogDir() . '/' . self::STATE_FILE );
        if ( self::$previousErrorHandler !== null )
            set_error_handler( self::$previousErrorHandler );
        else
            restore_error_handler();
    }

    /** PHP warnings and notices raised during the run, passed on unchanged. */
    public static function phpError( $errno, $errstr, $errfile = '', $errline = 0 )
    {
        if ( self::$run && !self::$run['finished'] && ( error_reporting() & $errno ) )
            self::problem( 'WARNING', self::phpErrorName( $errno ) . ': ' . $errstr . ' in ' . self::relative( $errfile ) . ':' . $errline );
        if ( self::$previousErrorHandler )
            return call_user_func( self::$previousErrorHandler, $errno, $errstr, $errfile, $errline );
        return false;
    }

    /** A run that ends without finish(): a fatal error, exit() or a killed request. */
    public static function shutdown()
    {
        try
        {
            if ( !self::$run || self::$run['finished'] )
                return;
            $e = error_get_last();
            $fatal = $e && in_array( $e['type'], array( E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR ) );
            if ( $fatal )
                self::problem( 'ERROR', 'PHP fatal error: ' . $e['message'] . ' in ' . self::relative( $e['file'] ) . ':' . $e['line'] );
            // A web request that ends normally hands the run to the next one
            if ( !$fatal && strpos( self::$run['mode'], 'web' ) === 0 )
            {
                if ( empty( self::$run['suspended'] ) )
                    self::suspend();
                return;
            }
            self::finish( 'ABORTED' . ( self::$run['current'] ? ' during ' . self::$run['current']['name'] : '' ) );
        }
        catch ( Throwable $e )
        {
        }
    }

    /**
     * After an installation: what a working site needs, each as
     * array( PASS|WARN|FAIL, what was found, what to do ).
     */
    protected static function healthChecks()
    {
        $checks = array();
        try
        {
            $db = eZDB::instance();
            if ( !$db || !$db->isConnected() )
                return array( array( 'FAIL', 'no database connection after the installation', 'check the database settings in settings/override/site.ini.append.php' ) );
            $checks[] = array( 'PASS', 'database connected: ' . $db->databaseName() . ' ' . $db->DB, '' );

            // Every table the kernel and the active extensions declare
            $declared = self::declaredTables();
            $reader = eZDbSchema::instance( $db );
            $live = $reader ? $reader->schema( array( 'format' => 'local' ) ) : array();
            $missing = array_diff( array_keys( $declared ), array_keys( is_array( $live ) ? $live : array() ) );
            $checks[] = $missing
                ? array( 'FAIL', count( $missing ) . ' of ' . count( $declared ) . ' declared tables missing: ' . implode( ' ', array_slice( $missing, 0, 12 ) ), 'Setup > System Upgrade > Check database consistency lists the SQL that creates them; look above for the step that failed to' )
                : array( 'PASS', 'all ' . count( $declared ) . ' tables declared by the kernel and the active extensions exist', '' );

            // Content classes: how many, and which
            $classes = $db->arrayQuery( 'SELECT identifier FROM ezcontentclass WHERE version = 0 ORDER BY identifier' );
            $attributes = $db->arrayQuery( 'SELECT COUNT(*) AS n FROM ezcontentclass_attribute WHERE version = 0' );
            $groups = $db->arrayQuery( 'SELECT COUNT(*) AS n FROM ezcontentclassgroup' );
            $identifiers = array_map( function ( $r ) { return $r['identifier']; }, $classes );
            $checks[] = $classes
                ? array( 'PASS', count( $classes ) . ' content classes installed (' . (int)( $groups[0]['n'] ?? 0 ) . ' class groups, ' . (int)( $attributes[0]['n'] ?? 0 ) . ' attributes)', '' )
                : array( 'FAIL', 'no content classes installed', 'the base data or the site package did not load its classes' );
            foreach ( array_chunk( $identifiers, 8 ) as $chunk )
                $checks[] = array( 'INFO', '  ' . implode( ', ', $chunk ), '' );

            // Content, and the content root visible
            $objects = $db->arrayQuery( 'SELECT COUNT(*) AS n FROM ezcontentobject' );
            $n = (int)( $objects[0]['n'] ?? 0 );
            $checks[] = $n > 0 ? array( 'PASS', $n . ' content objects', '' )
                               : array( 'FAIL', 'no content objects', 'the site package did not install its content; see the CreateSites step' );
            $root = $db->arrayQuery( 'SELECT is_hidden, is_invisible FROM ezcontentobject_tree WHERE node_id = 2' );
            if ( !$root )
                $checks[] = array( 'FAIL', 'the content root (node 2) does not exist', 'the base data did not load' );
            elseif ( $root[0]['is_hidden'] || $root[0]['is_invisible'] )
                $checks[] = array( 'FAIL', 'the content root (node 2) is hidden, so every page is invisible', 'unhide node 2 (admin: Content structure > node 2 > Reveal)' );
            else
                $checks[] = array( 'PASS', 'the content root (node 2) is visible', '' );

            // Class and attribute names the admin can show
            $bad = 0;
            foreach ( array( 'ezcontentclass', 'ezcontentclass_attribute' ) as $t )
                foreach ( $db->arrayQuery( "SELECT serialized_name_list AS s FROM $t WHERE version = 0" ) as $r )
                {
                    $a = @unserialize( (string)$r['s'] );
                    if ( !is_array( $a ) || !array_filter( array_keys( $a ), 'is_string' ) || array_filter( array_keys( $a ), 'is_int' ) )
                        $bad++;
                }
            $checks[] = $bad ? array( 'FAIL', $bad . ' class or attribute names stored without a language (shown blank in the admin)', 'the site installer\'s postInstallRepairClassNameLists step repairs them' )
                             : array( 'PASS', 'every class and attribute name is stored with a language', '' );
        }
        catch ( Exception $e )
        {
            $checks[] = array( 'FAIL', 'the database checks stopped: ' . $e->getMessage(), '' );
        }

        // Settings the site needs
        $override = 'settings/override/site.ini.append.php';
        $checks[] = file_exists( $override ) ? array( 'PASS', $override . ' written', '' )
                                             : array( 'FAIL', $override . ' missing', 'the CreateSites step did not write the site settings' );
        $ini = eZINI::instance( 'site.ini', 'settings/override', null, null, false, true );
        $missingSa = array();
        foreach ( (array)$ini->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) as $sa )
            if ( $sa !== '' && !is_dir( 'settings/siteaccess/' . $sa ) )
                $missingSa[] = $sa;
        $checks[] = $missingSa ? array( 'FAIL', 'siteaccess settings missing for: ' . implode( ' ', $missingSa ), 'the site package did not create those siteaccesses' )
                               : array( 'PASS', 'every listed siteaccess has its settings directory', '' );

        // Files the web server must be able to write
        $webUser = self::ownerName( 'var' );
        $foreign = array();
        foreach ( array( 'var/cache', 'var/log', 'var/site', 'var/storage', 'settings/override' ) as $dir )
            if ( is_dir( $dir ) && ( $o = self::ownerName( $dir ) ) !== $webUser )
                $foreign[] = "$dir ($o)";
        if ( $webUser !== 'root' )
        {
            $rootOwned = self::countRootOwned( array( 'var/cache', 'var/site/cache', 'var/log', 'var/site/storage', 'var/storage' ), 20000 );
            if ( $rootOwned )
                $foreign[] = $rootOwned . ' files owned by root under var/ (a command run as root wrote them)';
        }
        $checks[] = $foreign
            ? array( 'WARN', 'owned by someone other than var/\'s owner (' . $webUser . '): ' . implode( ', ', $foreign ), 'run chown -R ' . $webUser . ' on them, or the web server cannot update caches and logs' )
            : array( 'PASS', 'var/ and settings/override owned by ' . $webUser, '' );

        return $checks;
    }

    /** Tables declared by share/db_schema.dba and every active extension's share/db_schema.dba. */
    protected static function declaredTables()
    {
        $tables = array();
        $files = array( 'share/db_schema.dba' );
        foreach ( eZExtension::activeExtensions() as $ext )
        {
            $path = eZExtension::extensionPath( $ext );
            if ( $path && file_exists( "$path/share/db_schema.dba" ) )
                $files[] = "$path/share/db_schema.dba";
        }
        foreach ( $files as $f )
        {
            $s = eZDbSchema::read( $f, true );
            $s = ( is_array( $s ) && isset( $s['schema'] ) ) ? $s['schema'] : $s;
            if ( is_array( $s ) )
                foreach ( $s as $t => $def )
                    if ( $t !== '_info' && is_array( $def ) )
                        $tables[$t] = $f;
        }
        return $tables;
    }

    protected static function distinctProblems()
    {
        $seen = array();
        foreach ( self::$run['problems'] as $p )
        {
            $key = $p['level'] . preg_replace( '/\d+|\(x#.*$|\(at .*$/', '#', $p['message'] );
            if ( isset( $seen[$key] ) )
                $seen[$key]['count'] += $p['count'];
            else
                $seen[$key] = $p;
        }
        return array_values( $seen );
    }

    /** Entries grouped by message (numbers folded), in first-seen order, with count and first/last time. */
    protected static function group( array $entries )
    {
        $g = array();
        foreach ( $entries as $e )
        {
            $key = preg_replace( '/\d+/', '#', $e['message'] );
            if ( isset( $g[$key] ) )
            {
                $g[$key]['count']++;
                $g[$key]['last'] = $e['time'];
            }
            else
                $g[$key] = array( 'message' => $e['message'], 'count' => 1, 'first' => $e['time'], 'last' => $e['time'] );
        }
        return array_values( $g );
    }

    protected static function occurrences( array $problems, $level )
    {
        $n = 0;
        foreach ( $problems as $p )
            if ( $p['level'] === $level )
                $n += isset( $p['count'] ) ? $p['count'] : 1;
        return $n;
    }

    protected static function hint( $message )
    {
        foreach ( self::$hints as $pattern => $hint )
            if ( preg_match( $pattern, $message ) )
                return $hint;
        return '';
    }

    protected static function introduction()
    {
        return array(
            'This file records each run of the Exponential setup: the command-line',
            'kickstarter (bin/php/kickstarter.php) and the web setup wizard.',
            'Every line of one run carries the same run id, so one run can be read on',
            'its own:  grep "<run id>" var/log/setup.log',
            'Each step opens with ">> Step" and closes with "<< Step status, time,',
            'errors, warnings". Errors and warnings are what the step wrote to',
            'var/log/error.log and warning.log and what PHP raised while it ran; a',
            'known problem is followed by a hint. After an installation, "-- checks"',
            'tests what a working site needs, and the summary lists every step, the',
            'result and each distinct problem once.',
        );
    }

    protected static function environment()
    {
        return array(
            'Exponential' => ExponentialSDK::version(),
            'PHP' => PHP_VERSION . ' (' . PHP_SAPI . ')',
            'operating system' => php_uname( 's' ) . ' ' . php_uname( 'r' ),
            'user' => self::currentUser(),
            'document root' => getcwd(),
            'memory_limit' => ini_get( 'memory_limit' ),
            'max_execution_time' => ini_get( 'max_execution_time' ),
        );
    }

    protected static function currentUser()
    {
        if ( function_exists( 'posix_geteuid' ) )
        {
            $pw = posix_getpwuid( posix_geteuid() );
            return is_array( $pw ) ? $pw['name'] : (string)posix_geteuid();
        }
        return get_current_user();
    }

    /** Files owned by root below $dirs, counted up to $limit. */
    protected static function countRootOwned( array $dirs, $limit )
    {
        $n = 0;
        foreach ( $dirs as $dir )
        {
            if ( !is_dir( $dir ) )
                continue;
            $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::SELF_FIRST );
            foreach ( $it as $f )
            {
                if ( @fileowner( $f->getPathname() ) === 0 )
                    $n++;
                if ( --$limit <= 0 )
                    return $n;
            }
        }
        return $n;
    }

    /** Run as root: a file this class created is handed to var/'s owner, so the web server can append to it. */
    protected static function giveToVarOwner( $file )
    {
        if ( function_exists( 'posix_geteuid' ) && posix_geteuid() === 0 && file_exists( 'var' ) && ( $uid = fileowner( 'var' ) ) !== 0 )
        {
            @chown( $file, $uid );
            @chgrp( $file, filegroup( 'var' ) );
        }
    }

    /** Passwords never reach the log. */
    protected static function mask( $text )
    {
        return preg_replace( array( '/(password\s*[=:]\s*)(\S+)/i', '/(--password[= ])(\S+)/i', '/(:\/\/[^:\/\s]+:)([^@\s]+)(@)/' ),
                             array( '$1****', '$1****', '$1****$3' ), (string)$text );
    }

    protected static function ownerName( $path )
    {
        if ( !file_exists( $path ) )
            return '';
        $uid = fileowner( $path );
        if ( function_exists( 'posix_getpwuid' ) && ( $pw = posix_getpwuid( $uid ) ) )
            return $pw['name'];
        return (string)$uid;
    }

    protected static function phpErrorName( $errno )
    {
        $names = array( E_WARNING => 'PHP warning', E_NOTICE => 'PHP notice', E_DEPRECATED => 'PHP deprecated',
                        E_USER_WARNING => 'warning', E_USER_NOTICE => 'notice', E_USER_DEPRECATED => 'deprecated' );
        return isset( $names[$errno] ) ? $names[$errno] : 'PHP error ' . $errno;
    }

    protected static function relative( $file )
    {
        $root = getcwd() . '/';
        return strpos( (string)$file, $root ) === 0 ? substr( $file, strlen( $root ) ) : (string)$file;
    }

    protected static function shorten( $text, $max )
    {
        return strlen( $text ) > $max ? substr( $text, 0, $max - 3 ) . '...' : $text;
    }

    /** One line, written as eZLog writes its files: rotated when too large, created with LogFilePermissions. */
    protected static function line( $text, $step = null )
    {
        $elapsed = self::$run ? sprintf( '+%.3fs', microtime( true ) - self::$run['started'] ) : '';
        $prefix = '[ ' . date( 'M d Y H:i:s' ) . ' ] [ ' . ( self::$run ? self::$run['id'] : '-' ) . ' ' . $elapsed . ' ]';
        if ( $step )
            $prefix .= ' [ ' . $step . ' ]';
        $file = self::setupLogDir() . '/' . self::LOG_FILE;
        $existed = file_exists( $file );
        // Every run starts a file of its own: setup.log is the latest run alone, the
        // runs before it setup.log.1, .2 ... (eZLog's rotation, as many as it keeps)
        $beginning = self::$run && empty( self::$run['lines'] );
        if ( $existed && $beginning && filesize( $file ) > 0 && eZLog::rotateLog( $file ) )
            $existed = false;
        elseif ( $existed && !self::$run && filesize( $file ) > eZLog::maxLogSize() && eZLog::rotateLog( $file ) )
            $existed = false;
        if ( self::$run )
            self::$run['lines'] = ( isset( self::$run['lines'] ) ? self::$run['lines'] : 0 ) + 1;
        if ( !$existed && !is_dir( self::setupLogDir() ) )
            eZDir::mkdir( self::setupLogDir(), false, true );
        $fh = @fopen( $file, 'a' );
        if ( !$fh )
            return;
        @flock( $fh, LOCK_EX );
        @fwrite( $fh, rtrim( $prefix . ' ' . $text ) . "\n" );
        @flock( $fh, LOCK_UN );
        @fclose( $fh );
        if ( !$existed )
            @chmod( $file, octdec( eZINI::instance()->variable( 'FileSettings', 'LogFilePermissions' ) ) );
        if ( !$existed || @fileowner( $file ) === 0 )
            self::giveToVarOwner( $file );
    }

    /**
     * Where setup.log and the web setup's state are kept: var/log, or the
     * directory in EXP_SETUP_LOG_DIR (tests, so they never rotate the log of a
     * real installation away). The kernel's own logs are read from
     * kernelLogDir().
     */
    protected static function setupLogDir()
    {
        $dir = getenv( 'EXP_SETUP_LOG_DIR' );
        return is_string( $dir ) && $dir !== '' ? rtrim( $dir, '/' ) : self::LOG_DIR;
    }

    /**
     * Where eZDebug writes error.log and warning.log: var/log, or the log
     * directory of the site when site.ini [FileSettings] UseGlobalLogDir is
     * disabled (multi-site hosting, eZSiteAccess::updateLogDirectory()).
     */
    protected static function kernelLogDir()
    {
        try
        {
            if ( class_exists( 'eZDebug' ) && method_exists( 'eZDebug', 'logDirectory' ) )
            {
                $dir = rtrim( (string)eZDebug::instance()->logDirectory(), '/' );
                if ( $dir !== '' )
                    return $dir;
            }
        }
        catch ( Throwable $e )
        {
        }
        return self::LOG_DIR;
    }

    /**
     * What error.log and the other kernel logs add to each entry while the
     * run is going on: the run id, and the step when one is running.
     */
    protected static function updateLogContext()
    {
        if ( !self::$run || !empty( self::$run['finished'] ) || !method_exists( 'eZDebug', 'setLogContext' ) )
            return;
        $context = 'setup ' . self::$run['id'];
        if ( self::$run['current'] )
            $context .= sprintf( ', step %d "%s"', count( self::$run['steps'] ) + 1, self::$run['current']['name'] );
        eZDebug::setLogContext( $context );
    }

    protected static function clearLogContext()
    {
        if ( method_exists( 'eZDebug', 'setLogContext' ) )
            eZDebug::setLogContext( '' );
    }

    /**
     * An entry in error.log where a run begins and ends, so what lies between
     * can be read as that run's. Its label (expSetupLog) is not a problem:
     * since() leaves these out.
     */
    protected static function markErrorLog( $text )
    {
        $f = self::kernelLogDir() . '/error.log';
        $ip = eZSys::serverVariable( 'HOSTNAME', true );
        if ( !$ip )
            $ip = php_uname( 'n' );
        @file_put_contents( $f, '[ ' . date( 'M d Y H:i:s' ) . ' ] [' . $ip . "] expSetupLog:\n" . $text . "\n", FILE_APPEND | LOCK_EX );
    }

    protected static function size( $log, $dir = null )
    {
        $f = ( $dir !== null ? $dir : self::kernelLogDir() ) . '/' . $log;
        clearstatcache( true, $f );
        return file_exists( $f ) ? filesize( $f ) : 0;
    }

    /**
     * The entries a kernel log gained since $offset, each reduced to its
     * message: eZLog and eZDebug start an entry with "[ date ][ siteaccess ]
     * [ command or URL ]" and eZDebug puts its label on a line of its own.
     */
    protected static function since( $log, $offset, $dir = null )
    {
        $f = ( $dir !== null ? $dir : self::kernelLogDir() ) . '/' . $log;
        clearstatcache( true, $f );
        if ( !file_exists( $f ) )
            return array();
        $size = filesize( $f );
        if ( $size < $offset )
            $offset = 0;
        if ( $size <= $offset )
            return array();
        $text = file_get_contents( $f, false, null, $offset );
        $out = array();
        $repeat = '/^eZDebug: ' . str_replace( array( '%d', '%s' ), array( '(\d+)', '\S+' ), preg_quote( eZDebug::REPEAT_MESSAGE, '/' ) ) . '/';
        foreach ( preg_split( '/\n(?=\[ )/', trim( $text ) ) as $entry )
        {
            $time = preg_match( '/^\[ \w+ \d+ \d+ (\d\d:\d\d:\d\d) \]/', trim( $entry ), $m ) ? $m[1] : '';
            $raw = $entry;
            $entry = preg_replace( '/^(\[[^\]]*\]\s*){1,3}/', '', trim( $entry ) );
            // The run's own BEGIN/END markers are not problems
            if ( strpos( $entry, 'expSetupLog:' ) === 0 )
                continue;
            // The "(setup <id>, step N ...)" line eZDebug adds while a run goes on.
            // An entry without this run's line was written by another process:
            // a visitor's request that reached the site while it was being
            // installed. Not the installation's problem, so kept apart.
            $ours = !self::$run || strpos( $entry, '(setup ' . self::$run['id'] ) !== false;
            // The script's own fatal-error handler writes without eZDebug, so
            // without that line, but names the command in its header
            // ("[ date ][ siteaccess ][ php bin/php/kickstarter.php ... ]")
            if ( !$ours && PHP_SAPI === 'cli' && !empty( $_SERVER['argv'][0] ) )
            {
                $header = strtok( ltrim( $raw ), "\n" );
                $ours = preg_match( '/^\[[^\]]*\]\s*\[[^\]]*\]\s*\[[^\]]*' . preg_quote( basename( $_SERVER['argv'][0] ), '/' ) . '/', $header ) === 1;
            }
            $entry = preg_replace( '/\n {4}\(setup [^\n]*\)\s*$/', '', $entry );
            $entry = trim( preg_replace( '/\s+/', ' ', $entry ) );
            if ( $entry === '' )
                continue;
            // "The entry above was written N more times": N more of the one before
            if ( preg_match( $repeat, $entry, $r ) && $out )
            {
                $previous = end( $out );
                for ( $i = min( (int)$r[1], 100000 ); $i > 0; $i-- )
                    $out[] = array( 'message' => $previous['message'], 'time' => $time, 'ours' => $previous['ours'] );
                continue;
            }
            $out[] = array( 'message' => $entry, 'time' => $time, 'ours' => $ours );
        }
        return $out;
    }
}
?>
