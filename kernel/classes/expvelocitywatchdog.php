<?php
/**
 * File containing the expVelocityWatchdog class.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * A safety net that starts the default engine again when it is down and
 * nobody meant it to be: exp:velocity watchdog.
 *
 * A restart is a stop followed by a start. When whatever runs it is killed in
 * between -- a terminal closed, a session ended, a caller cut off by a limit --
 * the server stays down until somebody notices (2026-10-17: 28 minutes). The
 * restart itself now runs as one detached unit, so this should not happen
 * again; the watchdog covers whatever still can: a crash, an out-of-memory
 * kill, a stop by some other tool.
 *
 * A systemd timer runs `exp:velocity watchdog run` every [WatchdogSettings]
 * Interval seconds. Each run checks the default engine and starts it only when
 *
 *   - it is not running,
 *   - nobody stopped it on purpose (exp:velocity stop and kill leave the
 *     marker stopped.json beside the pid file; start and restart remove it),
 *   - no restart is under way (restart-in-progress.json, written by the
 *     detached restart; one whose process is gone, or older than
 *     [WatchdogSettings] RestartTimeout, no longer counts),
 *   - the optional [WatchdogSettings] LockFile is not held (a deploy queue);
 *     held for longer than RestartTimeout while the server is down, it no
 *     longer counts either, so a long queue cannot keep the site down,
 *   - and the last three starts it made did not all fail within
 *     [WatchdogSettings] Backoff seconds (it tries again after that).
 *
 * Each run that does anything other than find the server running writes one
 * line to var/log/velocity-watchdog.log. It never stops, kills or restarts a
 * running server, whether it answers or not.
 *
 * Off unless installed: `exp:velocity watchdog install` writes the service
 * and timer units and conf-available/<name>.conf in the configuration tree,
 * enables that file (conf-enabled, as a2enconf does) and starts the timer.
 * `exp:velocity conf disable watchdog` pauses it without root, `watchdog
 * remove` takes it all out again, `watchdog status` says what it is doing.
 *
 * @see expVelocityMarkers
 */
class expVelocityWatchdog
{
    /** The log, relative to the installation. */
    const LOG_FILE = 'var/log/velocity-watchdog.log';

    /** Its own state beside the markers: down since, last start, failures. */
    const STATE_FILE = 'watchdog.json';

    /** Starts that may fail in a row before it waits Backoff seconds. */
    const MAX_FAILURES = 3;

    /** @var expVelocity the default engine */
    protected $velocity;

    /** @var eZINI */
    protected $ini;

    /** @var string */
    protected $rootDir;

    /** @var string|null where the markers are, null = the engine's run directory */
    protected $runDir;

    /** @var array|null the enabled configuration file's values, once read */
    protected $conf;

    /**
     * @param expVelocity|null $velocity the engine to watch; null = the default engine
     * @param string|null $runDir the markers' directory; null = beside the engine's pid file
     */
    public function __construct( expVelocity $velocity = null, $runDir = null )
    {
        $this->velocity = $velocity !== null ? $velocity : expVelocity::create( 'velocity.ini' );
        $this->ini = eZINI::instance( 'velocity.ini' );
        $this->rootDir = rtrim( eZSys::rootDir(), '/' );
        $this->runDir = $runDir;
    }

    /**
     * The decision, from facts alone: no files, no processes, no clock. The
     * same order run() follows, so the tests cover what the timer does.
     *
     * @param array $s running (bool), paused (bool), stopped (array|null),
     *                 restart (array|null), restartPidAlive (bool), lockHeld (bool),
     *                 downSince (int|null), failures (int), lastAttempt (int|null),
     *                 now (int), timeout (int), backoff (int)
     * @return array( 'action' => none|skip|start, 'reason' => string, 'detail' => string )
     */
    public static function decide( array $s )
    {
        $s += array( 'running' => false, 'paused' => false, 'stopped' => null, 'restart' => null,
                     'restartPidAlive' => false, 'lockHeld' => false, 'downSince' => null,
                     'failures' => 0, 'lastAttempt' => null, 'now' => time(), 'timeout' => 300, 'backoff' => 600 );
        $now = (int)$s['now'];
        $timeout = (int)$s['timeout'];

        if ( $s['running'] )
            return self::verdict( 'none', 'running', '' );
        if ( $s['paused'] )
            return self::verdict( 'skip', 'paused', 'its configuration file is disabled (exp:velocity conf enable watchdog)' );
        if ( is_array( $s['stopped'] ) )
            return self::verdict( 'skip', 'intentionally-stopped',
                'stopped on purpose' . self::since( $s['stopped'], 'since', $now ) . '; exp:velocity start starts it' );

        $stale = false;
        if ( is_array( $s['restart'] ) )
        {
            $age = isset( $s['restart']['started'] ) ? $now - (int)$s['restart']['started'] : PHP_INT_MAX;
            if ( expVelocityMarkers::restartHolds( $s['restart'], (bool)$s['restartPidAlive'], $now, $timeout ) )
                return self::verdict( 'skip', 'restart-in-progress',
                    'restart by pid ' . (int)( $s['restart']['pid'] ?? 0 ) . ' for ' . max( 0, $age ) . ' s (timeout ' . $timeout . ' s)' );
            $stale = $s['restartPidAlive'] ? 'older than ' . $timeout . ' s' : 'its process is gone';
        }

        $down = $s['downSince'] !== null ? max( 0, $now - (int)$s['downSince'] ) : 0;
        if ( $s['lockHeld'] && $down < $timeout )
            return self::verdict( 'skip', 'lock-held', 'the lock file is held; down for ' . $down . ' s (timeout ' . $timeout . ' s)' );

        if ( (int)$s['failures'] >= self::MAX_FAILURES && $s['lastAttempt'] !== null
             && $now - (int)$s['lastAttempt'] < (int)$s['backoff'] )
            return self::verdict( 'skip', 'backoff', (int)$s['failures'] . ' starts failed in a row; next try in '
                . ( (int)$s['backoff'] - ( $now - (int)$s['lastAttempt'] ) ) . ' s' );

        if ( $stale !== false )
            return self::verdict( 'start', 'restart-stale', 'a restart marker was left behind (' . $stale . ')' );
        return self::verdict( 'start', 'down', 'not running' . ( $down ? ', down for ' . $down . ' s' : '' ) );
    }

    protected static function verdict( $action, $reason, $detail )
    {
        return array( 'action' => $action, 'reason' => $reason, 'detail' => $detail );
    }

    protected static function since( array $marker, $key, $now )
    {
        return isset( $marker[$key] ) ? ' ' . max( 0, $now - (int)$marker[$key] ) . ' s ago' : '';
    }

    /**
     * One check, and a start when decide() says so.
     *
     * @return array( ok, action, reason, detail, engine, started )
     */
    public function run()
    {
        $engine = $this->velocity->engineName();
        $result = array( 'ok' => true, 'action' => 'skip', 'reason' => 'overlap', 'detail' => '', 'engine' => $engine, 'started' => false );

        $dir = $this->runDirectory();
        if ( !is_dir( $dir ) )
            eZDir::mkdir( $dir, false, true );
        // One run at a time: a slow start must not meet the next timer tick.
        $own = @fopen( $dir . '/watchdog.lock', 'c' );
        if ( !$own || !flock( $own, LOCK_EX | LOCK_NB ) )
        {
            $result['detail'] = 'another watchdog run is still busy';
            $this->log( $result );
            return $result;
        }

        try
        {
            $now = time();
            $state = $this->readState();
            $running = $this->velocity->isRunning();
            if ( $running )
                $state['downSince'] = null;
            elseif ( empty( $state['downSince'] ) )
                $state['downSince'] = $now;

            $markers = $this->markers();
            $restart = $markers->read( expVelocityMarkers::RESTART );
            $facts = array(
                'running' => $running,
                'paused' => $this->isPaused(),
                'stopped' => $markers->read( expVelocityMarkers::STOPPED ),
                'restart' => $restart,
                'restartPidAlive' => is_array( $restart ) && self::pidAlive( (int)( $restart['pid'] ?? 0 ) ),
                'lockHeld' => $this->lockHeld(),
                'downSince' => $state['downSince'],
                'failures' => (int)( $state['failures'] ?? 0 ),
                'lastAttempt' => $state['lastAttempt'] ?? null,
                'now' => $now,
                'timeout' => $this->restartTimeout(),
                'backoff' => $this->backoff(),
            );
            $result = array_merge( $result, self::decide( $facts ) );

            if ( $result['action'] === 'start' )
            {
                // A restart marker that no longer holds (its process gone, or
                // too old) is taken out before the start, so that it does not
                // outlive the server it was about. Only the one read above: a
                // restart that began in the meantime keeps its own.
                if ( $result['reason'] === 'restart-stale' )
                    $this->clearStaleRestart( $markers, $restart );

                $state['lastAttempt'] = $now;
                $started = $this->startEngine();
                $result['started'] = $started['ok'];
                $result['ok'] = $started['ok'];
                $result['detail'] .= '; start: ' . $started['message'];
                if ( $started['ok'] )
                {
                    $state['failures'] = 0;
                    $state['downSince'] = null;
                    $state['lastStart'] = $now;
                }
                else
                    $state['failures'] = (int)( $state['failures'] ?? 0 ) + 1;
            }
            $state['lastCheck'] = $now;
            $state['lastAction'] = $result['action'] . ' ' . $result['reason'];
            $this->writeState( $state );
        }
        catch ( Exception $e )
        {
            $result['ok'] = false;
            $result['action'] = 'skip';
            $result['reason'] = 'error';
            $result['detail'] = $e->getMessage();
        }

        if ( $result['reason'] !== 'running' )
            $this->log( $result );
        flock( $own, LOCK_UN );
        fclose( $own );
        return $result;
    }

    /**
     * Remove the restart marker if it is still the stale one that was read.
     *
     * @param expVelocityMarkers $markers
     * @param array $stale the marker decide() found stale
     * @return bool whether it was removed
     */
    protected function clearStaleRestart( expVelocityMarkers $markers, array $stale )
    {
        $now = $markers->read( expVelocityMarkers::RESTART );
        if ( !is_array( $now ) || $now != $stale )
            return false;
        return $markers->clear( expVelocityMarkers::RESTART );
    }

    /**
     * Start the engine. Run by the timer, the server must not live in the
     * watchdog's own service, whose processes systemd ends with it; it goes
     * into a scope of its own (systemd-run --scope), the way a restart does.
     * Elsewhere (by hand, no systemd) it is started from this process, which
     * detaches it with setsid as always.
     *
     * @return array( ok, message )
     */
    protected function startEngine()
    {
        $systemdRun = getenv( 'INVOCATION_ID' ) !== false ? $this->findExecutable( 'systemd-run' ) : false;
        if ( $systemdRun === false )
        {
            $r = $this->velocity->start();
            return array( 'ok' => !empty( $r['ok'] ), 'message' => (string)( $r['message'] ?? '' ) );
        }

        $unit = 'vc-' . $this->velocity->engineName() . '-watchdog-start-' . date( 'Ymd-His' );
        $command = escapeshellarg( $systemdRun ) . ' --scope --quiet --collect --unit=' . escapeshellarg( $unit )
                 . ' ' . escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( $this->rootDir . '/bin/php/velocity.php' )
                 . ' start --engine=' . escapeshellarg( $this->velocity->engineName() ) . ' --json --allow-root-user 2>&1';
        $out = array();
        exec( 'cd ' . escapeshellarg( $this->rootDir ) . ' && ' . $command, $out, $rc );
        $json = null;
        foreach ( array_reverse( $out ) as $line )
        {
            $line = trim( $line );
            if ( $line !== '' && $line[0] === '{' && is_array( $decoded = json_decode( $line, true ) ) )
            {
                $json = $decoded;
                break;
            }
        }
        $ok = $json !== null ? !empty( $json['ok'] ) : $rc === 0;
        $message = $json !== null && isset( $json['message'] ) ? (string)$json['message'] : trim( implode( ' ', array_slice( $out, -3 ) ) );
        // Started is what counts, and what the next check would see anyway.
        if ( !$ok && $this->velocity->isRunning() )
            $ok = true;
        return array( 'ok' => $ok, 'message' => $message . ' (scope ' . $unit . ')' );
    }

    /**
     * Whether the watchdog's configuration file is installed but disabled
     * (conf-available without conf-enabled): paused, as a2disconf does.
     *
     * @return bool
     */
    public function isPaused()
    {
        $available = $this->confFile( false );
        return $available !== null && is_file( $available ) && !is_file( (string)$this->confFile( true ) );
    }

    /**
     * Whether [WatchdogSettings] LockFile is held by another process.
     *
     * @return bool
     */
    public function lockHeld()
    {
        $file = $this->lockFile();
        if ( $file === '' || !is_file( $file ) )
            return false;
        $fp = @fopen( $file, 'r' );
        if ( !$fp )
            return false;
        $free = flock( $fp, LOCK_SH | LOCK_NB );
        if ( $free )
            flock( $fp, LOCK_UN );
        fclose( $fp );
        return !$free;
    }

    // ------------------------------------------------------------ settings

    /**
     * A setting: the enabled configuration file first, then velocity.ini
     * [WatchdogSettings].
     */
    protected function setting( $name, $default )
    {
        $conf = $this->confValues();
        $key = lcfirst( $name );
        if ( isset( $conf[$key] ) && !is_array( $conf[$key] ) )
            return $conf[$key];
        return $this->ini->hasVariable( 'WatchdogSettings', $name ) ? $this->ini->variable( 'WatchdogSettings', $name ) : $default;
    }

    public function interval()
    {
        return max( 15, min( 3600, (int)$this->setting( 'Interval', 45 ) ) );
    }

    public function restartTimeout()
    {
        return max( 30, min( 3600, (int)$this->setting( 'RestartTimeout', 300 ) ) );
    }

    public function backoff()
    {
        return max( 60, min( 86400, (int)$this->setting( 'Backoff', 600 ) ) );
    }

    public function lockFile()
    {
        $file = trim( (string)$this->setting( 'LockFile', '' ) );
        if ( $file === '' )
            return '';
        return $file[0] === '/' ? $file : $this->rootDir . '/' . $file;
    }

    /**
     * The systemd unit name, without .service or .timer: [WatchdogSettings]
     * UnitName, vc-watchdog by default. A host with several installations
     * gives each its own.
     */
    public function unitName()
    {
        $name = preg_replace( '/[^A-Za-z0-9_.@-]+/', '-', trim( (string)$this->ini->variable( 'WatchdogSettings', 'UnitName' ) ) );
        return $name !== '' && $this->ini->hasVariable( 'WatchdogSettings', 'UnitName' ) ? $name : 'vc-watchdog';
    }

    /** The configuration file's name in the tree: the unit's without "vc-". */
    public function confName()
    {
        $name = preg_replace( '/^vc-/', '', $this->unitName() );
        return $name !== '' ? $name : 'watchdog';
    }

    /**
     * conf-enabled/<name>.conf or conf-available/<name>.conf, null without a
     * configuration tree.
     */
    public function confFile( $enabled = true )
    {
        $dir = $this->velocity->layout()->confDir();
        return $dir === null ? null : $dir . '/conf-' . ( $enabled ? 'enabled' : 'available' ) . '/' . $this->confName() . '.conf';
    }

    protected function confValues()
    {
        if ( $this->conf === null )
        {
            $this->conf = array();
            $file = $this->confFile( true );
            if ( $file !== null && is_file( $file ) )
            {
                $data = json_decode( (string)@file_get_contents( $file ), true );
                if ( is_array( $data ) && isset( $data['watchdog'] ) && is_array( $data['watchdog'] ) )
                    $this->conf = $data['watchdog'];
            }
        }
        return $this->conf;
    }

    // ------------------------------------------------------------ files

    public function runDirectory()
    {
        return $this->runDir !== null ? rtrim( $this->runDir, '/' ) : dirname( $this->velocity->pidFile() );
    }

    protected function markers()
    {
        return $this->runDir !== null ? new expVelocityMarkers( $this->runDirectory() ) : expVelocityMarkers::forVelocity( $this->velocity );
    }

    public function logFile()
    {
        return $this->rootDir . '/' . self::LOG_FILE;
    }

    public function readState()
    {
        $data = json_decode( (string)@file_get_contents( $this->runDirectory() . '/' . self::STATE_FILE ), true );
        return is_array( $data ) ? $data : array();
    }

    protected function writeState( array $state )
    {
        $file = $this->runDirectory() . '/' . self::STATE_FILE;
        $tmp = $file . '.' . getmypid();
        if ( @file_put_contents( $tmp, json_encode( $state, JSON_PRETTY_PRINT ) . "\n" ) !== false )
            @rename( $tmp, $file );
    }

    /**
     * One line per run: when, engine, what and why.
     */
    public function log( array $result )
    {
        $line = sprintf( "[%s] %s action=%s reason=%s detail=\"%s\" pid=%d\n", date( 'c' ), $result['engine'],
                         $result['action'], $result['reason'], str_replace( array( '"', "\n" ), array( "'", ' ' ), $result['detail'] ), getmypid() );
        $file = $this->logFile();
        if ( !is_dir( dirname( $file ) ) )
            eZDir::mkdir( dirname( $file ), false, true );
        @file_put_contents( $file, $line, FILE_APPEND | LOCK_EX );
    }

    // ------------------------------------------------------------ install

    /**
     * The service unit's text.
     */
    public function serviceUnit()
    {
        return "# Written by exp:velocity watchdog install; exp:velocity watchdog remove takes it out.\n"
             . "[Unit]\n"
             . "Description=Exponential Velocity watchdog for " . $this->rootDir . "\n"
             . "After=network-online.target\n\n"
             . "[Service]\n"
             . "Type=oneshot\n"
             . "WorkingDirectory=" . $this->rootDir . "\n"
             . "ExecStart=" . PHP_BINARY . " " . $this->rootDir . "/bin/php/velocity.php watchdog run --allow-root-user\n"
             // The server it starts goes into a scope of its own; should one
             // still be left here, it is not ended with this oneshot service.
             . "KillMode=process\n"
             . "TimeoutStartSec=600\n"
             // Every check would put its banner and verdict into the journal
             // (about 2,000 runs a day); what it did is in its own log, and
             // errors still reach the journal on stderr.
             . "StandardOutput=null\n";
    }

    public function timerUnit()
    {
        return "# Written by exp:velocity watchdog install; exp:velocity watchdog remove takes it out.\n"
             . "[Unit]\n"
             . "Description=Exponential Velocity watchdog for " . $this->rootDir . " every " . $this->interval() . " s\n\n"
             . "[Timer]\n"
             . "OnBootSec=60\n"
             . "OnUnitActiveSec=" . $this->interval() . "\n"
             . "AccuracySec=5\n\n"
             . "[Install]\n"
             . "WantedBy=timers.target\n";
    }

    public function confText()
    {
        $values = array(
            'root' => $this->rootDir,
            'unit' => $this->unitName(),
            'interval' => $this->interval(),
            'restartTimeout' => $this->restartTimeout(),
            'backoff' => $this->backoff(),
            'lockFile' => (string)$this->setting( 'LockFile', '' ),
        );
        return json_encode( array( '_generated' => 'created by exp:velocity watchdog install; values here win over velocity.ini [WatchdogSettings]',
                                   'watchdog' => $values ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n";
    }

    public function unitDir()
    {
        $dir = trim( (string)( $this->ini->hasVariable( 'WatchdogSettings', 'UnitDir' ) ? $this->ini->variable( 'WatchdogSettings', 'UnitDir' ) : '' ) );
        return $dir !== '' ? rtrim( $dir, '/' ) : '/etc/systemd/system';
    }

    /**
     * Install and start: unit files, conf-available + conf-enabled, timer on.
     * An existing configuration file is kept (it may carry local values).
     *
     * @return array( ok, message, actions )
     */
    public function install()
    {
        $actions = array();
        $systemctl = $this->findExecutable( 'systemctl' );
        if ( $systemctl === false )
            return array( 'ok' => false, 'message' => 'needs systemd (systemctl not found); run `exp:velocity watchdog run` from cron instead, e.g. every minute', 'actions' => $actions );
        if ( function_exists( 'posix_geteuid' ) && posix_geteuid() !== 0 )
            return array( 'ok' => false, 'message' => 'needs root (it writes ' . $this->unitDir() . ')', 'actions' => $actions );

        $unit = $this->unitName();
        foreach ( array( '.service' => $this->serviceUnit(), '.timer' => $this->timerUnit() ) as $suffix => $text )
        {
            $file = $this->unitDir() . '/' . $unit . $suffix;
            if ( @file_get_contents( $file ) !== $text )
            {
                if ( @file_put_contents( $file, $text ) === false )
                    return array( 'ok' => false, 'message' => 'could not write ' . $file, 'actions' => $actions );
                $actions[] = 'wrote ' . $file;
            }
        }

        $available = $this->confFile( false );
        if ( $available !== null )
        {
            if ( !is_dir( dirname( $available ) ) )
                eZDir::mkdir( dirname( $available ), false, true );
            if ( !is_file( $available ) )
            {
                @file_put_contents( $available, $this->confText() );
                $actions[] = 'wrote ' . $available;
            }
            $enabled = $this->confFile( true );
            if ( !is_file( $enabled ) )
            {
                if ( !is_dir( dirname( $enabled ) ) )
                    eZDir::mkdir( dirname( $enabled ), false, true );
                if ( is_link( $enabled ) )
                    @unlink( $enabled );
                @symlink( '../conf-available/' . basename( $available ), $enabled );
                $actions[] = 'enabled ' . $enabled;
            }
            $this->conf = null;
        }

        foreach ( array( 'daemon-reload', 'enable --now ' . escapeshellarg( $unit . '.timer' ) ) as $args )
        {
            exec( escapeshellarg( $systemctl ) . ' ' . $args . ' 2>&1', $out, $rc );
            if ( $rc !== 0 )
                return array( 'ok' => false, 'message' => 'systemctl ' . $args . ' failed: ' . implode( ' ', $out ), 'actions' => $actions );
            $actions[] = 'systemctl ' . $args;
        }
        return array( 'ok' => true, 'message' => 'installed: ' . $unit . '.timer every ' . $this->interval() . ' s', 'actions' => $actions );
    }

    /**
     * Stop and take out: timer off, unit files and configuration files gone.
     *
     * @return array( ok, message, actions )
     */
    public function remove()
    {
        $actions = array();
        $unit = $this->unitName();
        $systemctl = $this->findExecutable( 'systemctl' );
        if ( $systemctl !== false )
        {
            exec( escapeshellarg( $systemctl ) . ' disable --now ' . escapeshellarg( $unit . '.timer' ) . ' 2>&1', $out, $rc );
            $actions[] = 'systemctl disable --now ' . $unit . '.timer' . ( $rc ? ' (was not enabled)' : '' );
        }
        foreach ( array( $this->unitDir() . '/' . $unit . '.timer', $this->unitDir() . '/' . $unit . '.service',
                         $this->confFile( true ), $this->confFile( false ) ) as $file )
        {
            if ( $file !== null && ( is_file( $file ) || is_link( $file ) ) && @unlink( $file ) )
                $actions[] = 'removed ' . $file;
        }
        if ( $systemctl !== false )
            exec( escapeshellarg( $systemctl ) . ' daemon-reload 2>&1' );
        $this->conf = null;
        return array( 'ok' => true, 'message' => $actions ? 'removed' : 'was not installed', 'actions' => $actions );
    }

    /**
     * What it is doing.
     *
     * @return array
     */
    public function status()
    {
        $unit = $this->unitName();
        $systemctl = $this->findExecutable( 'systemctl' );
        $active = '';
        if ( $systemctl !== false )
        {
            exec( escapeshellarg( $systemctl ) . ' is-active ' . escapeshellarg( $unit . '.timer' ) . ' 2>/dev/null', $out );
            $active = trim( implode( '', $out ) );
        }
        $markers = $this->markers();
        $state = $this->readState();
        return array(
            'engine' => $this->velocity->engineName(),
            'installed' => is_file( $this->unitDir() . '/' . $unit . '.timer' ),
            'timer' => $active !== '' ? $active : 'unknown',
            'unit' => $unit,
            'paused' => $this->isPaused(),
            'conf' => $this->confFile( false ),
            'interval' => $this->interval(),
            'restartTimeout' => $this->restartTimeout(),
            'backoff' => $this->backoff(),
            'lockFile' => $this->lockFile(),
            'running' => $this->velocity->isRunning(),
            'stopped' => $markers->read( expVelocityMarkers::STOPPED ),
            'restart' => $markers->read( expVelocityMarkers::RESTART ),
            'lastCheck' => isset( $state['lastCheck'] ) ? date( 'c', (int)$state['lastCheck'] ) : null,
            'lastAction' => $state['lastAction'] ?? null,
            'lastStart' => isset( $state['lastStart'] ) ? date( 'c', (int)$state['lastStart'] ) : null,
            'failures' => (int)( $state['failures'] ?? 0 ),
            'log' => $this->logFile(),
        );
    }

    // ------------------------------------------------------------ helpers

    public static function pidAlive( $pid )
    {
        return expVelocityMarkers::pidAlive( $pid );
    }

    protected function findExecutable( $name )
    {
        foreach ( array_unique( array_filter( array_merge( explode( PATH_SEPARATOR, (string)getenv( 'PATH' ) ),
                  array( '/usr/local/sbin', '/usr/local/bin', '/usr/sbin', '/usr/bin', '/sbin', '/bin' ) ) ) ) as $dir )
        {
            $candidate = rtrim( $dir, '/' ) . '/' . $name;
            if ( is_file( $candidate ) && is_executable( $candidate ) )
                return $candidate;
        }
        return false;
    }
}
