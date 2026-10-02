<?php
/**
 * File containing the ezpRepairQueue class.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package lib
 */

/**
 * Repairs an installation whose Composer libraries are missing, from the error page that says so
 * (eZExecution::renderErrorCasePage('dependencies')), through a queue: the web request never runs a
 * command. It checks the administrator's one-time repair key, writes a job and starts the worker
 * (bin/php/exprepair.php --run) in the background; the worker runs a fixed list of steps as the
 * owner of the installation (the web server user; when started as root, as under Exponential
 * Velocity, it switches to that user first), so every file it writes has the right owner:
 *
 *   1. libraries  composer install --no-dev --no-interaction --no-plugins --no-scripts
 *                 (no plugins: the installer plugin that places extensions is not run, so
 *                 extension/ -- git checkouts included -- is never touched)
 *   2. autoloads  php bin/php/ezpgenerateautoloads.php -e, then -k
 *   3. caches     php bin/php/ezcache.php --clear-all
 *
 * The page shows the steps, a progress bar and the end of the log, polling the status.
 *
 * Works without the Composer libraries and without a database: plain PHP only, its settings in
 * settings/override/exprepair.ini.append.php (written by bin/php/exprepair.php --create-key):
 *
 *   [RepairSettings]
 *   Enabled=true
 *   KeyHash=<password_hash of the key>
 *   Composer=/usr/local/bin/composer      (optional; else "composer" on the PATH)
 *
 * The key is used once: starting a repair removes it. State lives in var/repair/.
 */
class ezpRepairQueue
{
    const SETTINGS = 'settings/override/exprepair.ini.append.php';
    const STATE = 'var/repair';
    const MAX_FAILURES = 5;
    const FAILURE_WINDOW = 900;

    /** @var array id => title, in order */
    public static $steps = array(
        'libraries' => 'Install the Composer libraries',
        'autoloads' => 'Regenerate the autoload arrays',
        'caches'    => 'Clear the caches',
    );

    // ---- settings ----------------------------------------------------------------------------

    public static function root()
    {
        return dirname( __DIR__, 3 );
    }

    /** @return array Enabled (bool), KeyHash, Composer */
    public static function settings()
    {
        $s = array( 'Enabled' => false, 'KeyHash' => '', 'Composer' => '' );
        $file = self::root() . '/' . self::SETTINGS;
        if ( is_file( $file ) && preg_match_all( '/^(Enabled|KeyHash|Composer)=(.*)$/m', (string) file_get_contents( $file ), $m, PREG_SET_ORDER ) )
        {
            foreach ( $m as $row )
                $s[$row[1]] = trim( $row[2] );
            $s['Enabled'] = in_array( strtolower( (string) $s['Enabled'] ), array( 'true', 'enabled', '1' ), true );
        }
        return $s;
    }

    public static function writeSettings( array $s )
    {
        $file = self::root() . '/' . self::SETTINGS;
        $text = "<?php /* #?ini charset=\"utf-8\"?\n\n# The repair queue of the missing-libraries error page (lib/ezutils/classes/ezprepairqueue.php).\n"
              . "# Written by bin/php/exprepair.php; the key is used once.\n[RepairSettings]\n"
              . 'Enabled=' . ( $s['Enabled'] ? 'true' : 'false' ) . "\nKeyHash=" . $s['KeyHash'] . "\n"
              . ( $s['Composer'] !== '' ? 'Composer=' . $s['Composer'] . "\n" : '' ) . "\n*/ ?>\n";
        if ( !is_dir( dirname( $file ) ) )
            mkdir( dirname( $file ), 0775, true );
        file_put_contents( $file, $text, LOCK_EX );
        @chmod( $file, 0640 );
    }

    /** @return bool whether the page offers the repair */
    public static function available()
    {
        $s = self::settings();
        return $s['Enabled'] && $s['KeyHash'] !== '';
    }

    /**
     * A new one-time key; only its hash is stored.
     * @return string the key, to show once
     */
    public static function createKey()
    {
        $key = substr( strtr( base64_encode( random_bytes( 18 ) ), '+/', 'Ab' ), 0, 24 );
        $s = self::settings();
        $s['Enabled'] = true;
        $s['KeyHash'] = password_hash( $key, PASSWORD_DEFAULT );
        self::writeSettings( $s );
        return $key;
    }

    // ---- state -------------------------------------------------------------------------------

    protected static function dir()
    {
        $d = self::root() . '/' . self::STATE;
        if ( !is_dir( $d ) )
        {
            mkdir( $d, 0770, true );
            file_put_contents( "$d/.htaccess", "Require all denied\n" );
        }
        return $d;
    }

    public static function status()
    {
        $f = self::dir() . '/status.json';
        $s = is_file( $f ) ? json_decode( (string) file_get_contents( $f ), true ) : null;
        return is_array( $s ) ? $s : array( 'state' => 'idle' );
    }

    protected static function writeStatus( array $s )
    {
        $f = self::dir() . '/status.json';
        file_put_contents( "$f.tmp", json_encode( $s ) );
        rename( "$f.tmp", $f );
    }

    public static function logTail( $lines = 25 )
    {
        $f = self::dir() . '/repair.log';
        if ( !is_file( $f ) )
            return '';
        $all = preg_split( '/\r?\n/', rtrim( (string) file_get_contents( $f ) ) );
        return implode( "\n", array_slice( $all, -$lines ) );
    }

    // ---- web side ----------------------------------------------------------------------------

    /**
     * Answers the error page's repair requests (only reached while the libraries are missing):
     *   POST exp_repair=start, exp_repair_key  -> starts the repair, JSON {ok, token} or {ok:false, error}
     *   GET  exp_repair=status&token=...       -> JSON status with the log tail
     * @return bool true when the request was one of these and has been answered
     */
    public static function handleWebRequest()
    {
        $action = isset( $_REQUEST['exp_repair'] ) ? (string) $_REQUEST['exp_repair'] : '';
        if ( $action !== 'start' && $action !== 'status' && $action !== 'update' )
            return false;
        if ( !headers_sent() )
        {
            header( 'Content-Type: application/json; charset=utf-8' );
            header( 'Cache-Control: no-store' );
        }
        if ( $action === 'start' )
            echo json_encode( self::start( isset( $_POST['exp_repair_key'] ) ? trim( (string) $_POST['exp_repair_key'] ) : '' ) );
        else if ( $action === 'update' )
            echo json_encode( self::startUpdate( isset( $_POST['token'] ) ? (string) $_POST['token'] : '' ) );
        else
        {
            $status = self::status();
            $token = isset( $_GET['token'] ) ? (string) $_GET['token'] : '';
            if ( !isset( $status['token'] ) || !hash_equals( (string) $status['token'], $token ) )
                echo json_encode( array( 'ok' => false, 'error' => 'unknown' ) );
            else
            {
                unset( $status['token'] );
                $status['ok'] = true;
                $status['log'] = self::logTail();
                $status['titles'] = self::$steps;
                echo json_encode( $status );
            }
        }
        return true;
    }

    /**
     * index.php, before the kernel: the status and update requests of a run, whatever state the
     * libraries are in (the run itself brings them back halfway through). Starting a repair is not
     * answered here: that is only offered while the libraries are missing.
     * @return bool true when answered
     */
    public static function handleRunRequest()
    {
        $action = isset( $_REQUEST['exp_repair'] ) ? (string) $_REQUEST['exp_repair'] : '';
        if ( $action !== 'status' && $action !== 'update' )
            return false;
        return self::handleWebRequest();
    }

    protected static function start( $key )
    {
        if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
            return array( 'ok' => false, 'error' => 'Send the key with POST.' );
        $s = self::settings();
        if ( !$s['Enabled'] || $s['KeyHash'] === '' )
            return array( 'ok' => false, 'error' => 'No repair key is set up on the server.' );

        $failFile = self::dir() . '/failures.json';
        $fails = is_file( $failFile ) ? (array) json_decode( (string) file_get_contents( $failFile ), true ) : array();
        $fails = array_values( array_filter( $fails, function ( $t ) { return $t > time() - self::FAILURE_WINDOW; } ) );
        if ( count( $fails ) >= self::MAX_FAILURES )
            return array( 'ok' => false, 'error' => 'Too many wrong keys. Try again in 15 minutes.' );
        if ( !password_verify( $key, $s['KeyHash'] ) )
        {
            $fails[] = time();
            file_put_contents( $failFile, json_encode( $fails ) );
            return array( 'ok' => false, 'error' => 'That is not the repair key. A key works once: after a repair has started, create a new one (php bin/php/exprepair.php --create-key).' );
        }
        $current = self::status();
        if ( isset( $current['state'] ) && $current['state'] === 'running' && isset( $current['heartbeat'] ) && $current['heartbeat'] > time() - 120 )
            return array( 'ok' => false, 'error' => 'A repair is already running.' );

        // the key is used once
        $s['KeyHash'] = '';
        self::writeSettings( $s );
        @unlink( $failFile );

        $token = bin2hex( random_bytes( 16 ) );
        $steps = array();
        foreach ( self::$steps as $id => $title )
            $steps[$id] = 'waiting';
        self::writeStatus( array( 'state' => 'queued', 'token' => $token, 'steps' => $steps, 'queued' => time(), 'heartbeat' => time() ) );
        file_put_contents( self::dir() . '/repair.log', '[' . date( 'c' ) . "] repair queued from the error page\n" );
        self::spawnWorker();
        return array( 'ok' => true, 'token' => $token );
    }

    /**
     * The second run after composer install refused a lock file that does not match composer.json:
     * composer update instead. Allowed with the token of that failed run, within 15 minutes of it,
     * so the administrator who entered the key confirms it without a new key.
     */
    protected static function startUpdate( $token )
    {
        if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
            return array( 'ok' => false, 'error' => 'Send the request with POST.' );
        $s = self::status();
        if ( !isset( $s['token'] ) || !hash_equals( (string) $s['token'], $token ) )
            return array( 'ok' => false, 'error' => 'unknown' );
        if ( empty( $s['lock_stale'] ) || $s['state'] !== 'failed' || ( isset( $s['finished'] ) && $s['finished'] < time() - 900 ) )
            return array( 'ok' => false, 'error' => 'Start the repair again with a new key.' );
        foreach ( self::$steps as $id => $title )
            $s['steps'][$id] = 'waiting';
        $s['state'] = 'queued';
        $s['mode'] = 'update';
        $s['lock_stale'] = false;
        $s['heartbeat'] = time();
        self::writeStatus( $s );
        self::log( 'the administrator confirmed: update the lock file and install' );
        self::spawnWorker();
        return array( 'ok' => true, 'token' => $token );
    }

    /** Starts bin/php/exprepair.php --run in the background, detached from the web request. */
    protected static function spawnWorker()
    {
        $php = is_file( PHP_BINDIR . '/php' ) ? PHP_BINDIR . '/php' : 'php';
        $cmd = sprintf( 'cd %s && setsid %s bin/php/exprepair.php --run >> %s 2>&1 < /dev/null &',
            escapeshellarg( self::root() ), escapeshellarg( $php ), escapeshellarg( self::dir() . '/worker.out' ) );
        exec( $cmd );
    }

    // ---- worker ------------------------------------------------------------------------------

    /**
     * Runs the queued repair (bin/php/exprepair.php --run). As root it first becomes the owner of the
     * installation. Returns the exit code: 0 done, 1 failed, 2 nothing queued or already running.
     */
    public static function runWorker()
    {
        $root = self::root();
        if ( function_exists( 'posix_getuid' ) && posix_getuid() === 0 )
        {
            $uid = fileowner( "$root/index.php" );
            $gid = filegroup( "$root/index.php" );
            if ( $uid !== 0 )
            {
                $pw = posix_getpwuid( $uid );
                if ( $pw && function_exists( 'posix_initgroups' ) )
                    @posix_initgroups( $pw['name'], $gid );
                if ( !posix_setgid( $gid ) || !posix_setuid( $uid ) )
                {
                    fwrite( STDERR, "could not switch to the installation's owner\n" );
                    return 1;
                }
                putenv( 'HOME=' . ( $pw ? $pw['dir'] : $root ) );
            }
        }
        $lock = fopen( self::dir() . '/worker.lock', 'c' );
        if ( !$lock || !flock( $lock, LOCK_EX | LOCK_NB ) )
            return 2;
        $status = self::status();
        if ( !isset( $status['state'] ) || $status['state'] !== 'queued' )
            return 2;

        $status['state'] = 'running';
        $status['started'] = time();
        $status['user'] = function_exists( 'posix_getpwuid' ) ? ( posix_getpwuid( posix_geteuid() )['name'] ?? '' ) : '';
        self::writeStatus( $status );
        self::log( 'running as ' . $status['user'] . ' in ' . $root );

        $s = self::settings();
        $composer = $s['Composer'] !== '' ? $s['Composer'] : 'composer';
        $php = is_file( PHP_BINDIR . '/php' ) ? PHP_BINDIR . '/php' : 'php';
        $commands = array(
            'libraries' => array( array( $composer, isset( $status['mode'] ) && $status['mode'] === 'update' ? 'update' : 'install',
                                         '--no-dev', '--no-interaction', '--no-plugins', '--no-scripts', '--no-progress' ) ),
            'autoloads' => array( array( $php, 'bin/php/ezpgenerateautoloads.php', '-e' ),
                                  array( $php, 'bin/php/ezpgenerateautoloads.php', '-k', '--exclude=.claude' ) ),
            'caches'    => array( array( $php, 'bin/php/ezcache.php', '--clear-all', '--allow-root-user' ) ),
        );
        $ok = true;
        foreach ( $commands as $id => $list )
        {
            $status['steps'][$id] = 'running';
            $status['heartbeat'] = time();
            self::writeStatus( $status );
            self::log( '== ' . self::$steps[$id] );
            foreach ( $list as $argv )
            {
                if ( self::runCommand( $argv, $status ) !== 0 )
                {
                    $ok = false;
                    break;
                }
            }
            $status['steps'][$id] = $ok ? 'done' : 'failed';
            // composer install refuses a lock file that does not match composer.json (exit code 4):
            // the page then offers to update the lock file and install
            if ( !$ok && $id === 'libraries' && preg_match( '/in the lock file|lock file is not up to date|not present in the lock file/i', self::logTail( 200 ) ) )
                $status['lock_stale'] = true;
            self::writeStatus( $status );
            if ( !$ok )
                break;
        }
        $status['state'] = $ok ? 'done' : 'failed';
        $status['finished'] = time();
        self::writeStatus( $status );
        self::log( $ok ? 'repair done' : 'repair failed' );
        return $ok ? 0 : 1;
    }

    /** One command, its output appended to the log; no shell, the argument list is fixed. */
    protected static function runCommand( array $argv, array &$status )
    {
        self::log( '$ ' . implode( ' ', $argv ) );
        $log = fopen( self::dir() . '/repair.log', 'a' );
        $proc = proc_open( $argv, array( 0 => array( 'file', '/dev/null', 'r' ), 1 => $log, 2 => $log ), $pipes, self::root(),
                           array( 'HOME' => getenv( 'HOME' ) ?: self::root(), 'PATH' => getenv( 'PATH' ) ?: '/usr/local/bin:/usr/bin:/bin',
                                  'COMPOSER_NO_INTERACTION' => '1' ) );
        if ( !is_resource( $proc ) )
        {
            self::log( 'could not start ' . $argv[0] );
            return 127;
        }
        while ( ( $info = proc_get_status( $proc ) ) && $info['running'] )
        {
            $status['heartbeat'] = time();
            self::writeStatus( $status );
            usleep( 500000 );
        }
        proc_close( $proc );
        fclose( $log );
        $code = isset( $info['exitcode'] ) ? (int) $info['exitcode'] : 1;
        self::log( 'exit code ' . $code );
        return $code;
    }

    protected static function log( $line )
    {
        file_put_contents( self::dir() . '/repair.log', '[' . date( 'H:i:s' ) . '] ' . $line . "\n", FILE_APPEND );
    }

    // ---- the page's panel ---------------------------------------------------------------------

    /** The panel for the error page: the key form and the progress UI, or how to set up a key. */
    public static function panelHtml()
    {
        $esc = function ( $v ) { return htmlspecialchars( (string) $v, ENT_QUOTES, 'UTF-8' ); };
        if ( !self::available() )
        {
            return '<p class="repair-hint">To repair from this page, create a one-time repair key on the server, as the web server user:'
                 . ' <code>php bin/php/exprepair.php --create-key</code></p>';
        }
        $steps = '';
        foreach ( self::$steps as $id => $title )
            $steps .= '<li data-step="' . $esc( $id ) . '"><span class="st">waiting</span> ' . $esc( $title ) . '</li>';
        return <<<HTML
<section id="exp-repair">
<h2>Repair from here</h2>
<form id="exp-repair-form"><label>Repair key <input type="text" name="exp_repair_key" autocomplete="off" autocapitalize="off" spellcheck="false" required></label>
<button type="submit">Start the repair</button><span id="exp-repair-error" role="alert"></span></form>
<div id="exp-repair-progress" hidden>
<div class="bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><span></span></div>
<p class="elapsed"></p><ol class="steps">{$steps}</ol>
<pre class="log" aria-live="polite"></pre>
<div class="lockstale" hidden><p><strong>The lock file (composer.lock) does not match composer.json</strong>, so composer install cannot use it.
Updating it resolves the versions composer.json asks for and writes a new composer.lock, then installs them
(composer update --no-dev, without plugins: extension/ is not touched).</p>
<button type="button" id="exp-repair-update">Update the lock file and install</button></div>
<p class="finished" hidden><button type="button" id="exp-repair-reload">Open the page again</button> <span class="auto"></span></p>
</div>
</section>
<style>
#exp-repair{margin-top:2rem;padding:1rem 1.2rem;border:1px solid #ddd;border-radius:8px;text-align:left}
#exp-repair h2{margin-top:0;font-size:1.2rem}#exp-repair input{padding:.4rem;margin:0 .5rem}
#exp-repair button{padding:.45rem 1rem;background:#FED82F;border:0;font-weight:700;cursor:pointer}
#exp-repair-error{color:#b00020;margin-left:.5rem}
#exp-repair .bar{height:12px;background:#eee;border-radius:6px;overflow:hidden;margin:1rem 0 .3rem}
#exp-repair .bar span{display:block;height:100%;width:0;background:#f26a21;transition:width .4s}
#exp-repair .steps{padding-left:1.2rem}#exp-repair .st{display:inline-block;min-width:5.5em;font-size:.8em;font-weight:700;text-transform:uppercase;color:#888}
#exp-repair .st.running{color:#f26a21}#exp-repair .st.done{color:#2e7d32}#exp-repair .st.failed{color:#b00020}
#exp-repair .log{max-height:16rem;overflow:auto;background:#111;color:#ddd;padding:.7rem;font-size:.8rem;white-space:pre-wrap}
</style>
<script>
(function () {
  var form = document.getElementById('exp-repair-form'), box = document.getElementById('exp-repair-progress'), token = '', t0 = 0;
  var err = document.getElementById('exp-repair-error'), url = location.pathname;
  function poll() {
    fetch(url + '?exp_repair=status&token=' + encodeURIComponent(token), {cache: 'no-store'}).then(function (r) { return r.json(); }).then(function (s) {
      err.textContent = ''; if (!s.ok) { err.textContent = 'The repair status is not available.'; return; }
      var ids = Object.keys(s.steps || {}), done = 0;
      ids.forEach(function (id) {
        var st = box.querySelector('[data-step="' + id + '"] .st'); st.textContent = s.steps[id]; st.className = 'st ' + s.steps[id];
        if (s.steps[id] === 'done') done++; if (s.steps[id] === 'running') done += .5;
      });
      var pct = Math.round(100 * done / ids.length); box.querySelector('.bar span').style.width = pct + '%';
      box.querySelector('.bar').setAttribute('aria-valuenow', pct);
      box.querySelector('.elapsed').textContent = (s.state === 'queued' ? 'Waiting for the worker' : s.state === 'running' ? 'Working' : s.state === 'done' ? 'Done' : 'Failed')
        + ' - ' + Math.round((Date.now() - t0) / 1000) + ' s' + (s.user ? ' - as ' + s.user : '');
      var log = box.querySelector('.log'); log.textContent = s.log || ''; log.scrollTop = log.scrollHeight;
      if (s.state === 'failed' && s.lock_stale) {
        var up = box.querySelector('.lockstale'); up.hidden = false;
        return;
      }
      if (s.state === 'done' || s.state === 'failed') {
        var fin = box.querySelector('.finished'); fin.hidden = false;
        if (s.state === 'done') { var n = 5, a = fin.querySelector('.auto'); (function tick() { a.textContent = 'Opening again in ' + n + ' s'; if (n-- <= 0) location.reload(); else setTimeout(tick, 1000); })(); }
        return;
      }
      setTimeout(poll, 2000);
    }).catch(function () { err.textContent = 'Waiting for the status...'; setTimeout(poll, 3000); });
  }
  form.addEventListener('submit', function (e) {
    e.preventDefault(); err.textContent = '';
    var body = new URLSearchParams(); body.set('exp_repair', 'start'); body.set('exp_repair_key', form.exp_repair_key.value);
    fetch(url, {method: 'POST', body: body, cache: 'no-store'}).then(function (r) { return r.json(); }).then(function (s) {
      if (!s.ok) { err.textContent = s.error || 'The repair could not start.'; return; }
      token = s.token; t0 = Date.now(); form.hidden = true; box.hidden = false; poll();
    }).catch(function () { err.textContent = 'No answer from the server.'; });
  });
  document.getElementById('exp-repair-reload').addEventListener('click', function () { location.reload(); });
  document.getElementById('exp-repair-update').addEventListener('click', function () {
    if (!confirm('Update composer.lock to what composer.json asks for, and install those versions?')) return;
    var body = new URLSearchParams(); body.set('exp_repair', 'update'); body.set('token', token);
    fetch(url, {method: 'POST', body: body, cache: 'no-store'}).then(function (r) { return r.json(); }).then(function (s) {
      if (!s.ok) { err.textContent = s.error || 'The update could not start.'; return; }
      box.querySelector('.lockstale').hidden = true; box.querySelector('.finished').hidden = true; t0 = Date.now(); poll();
    }).catch(function () { err.textContent = 'No answer from the server.'; });
  });
})();
</script>
HTML;
    }
}
