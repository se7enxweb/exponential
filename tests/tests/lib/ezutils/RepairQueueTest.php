<?php
/**
 * The repair queue of the missing-libraries error page (lib/ezutils/classes/ezprepairqueue.php).
 *
 *  RQ-01 — settings() has safe defaults without a settings file
 *  RQ-02 — writeSettings() and settings() round-trip; Enabled accepts true/enabled/1; the file is not world readable
 *  RQ-03 — available() needs Enabled and a key hash
 *  RQ-04 — createKey() returns a 24 character key, stores only its hash, enables the queue
 *  RQ-05 — start() wants POST, a configured key, and the right key
 *  RQ-06 — A right key queues a job (status, token, steps, log), starts the worker once and is used up
 *  RQ-07 — After 5 wrong keys in 15 minutes even the right key is refused; older failures do not count
 *  RQ-08 — A repair that is running (fresh heartbeat) is not started a second time
 *  RQ-09 — handleWebRequest(): not an exp_repair request -> false and no output; status needs the token and never shows it
 *  RQ-10 — handleWebRequest() update: the stale-lock confirmation re-queues the failed run (token, state, 15 minutes)
 *  RQ-11 — handleRunRequest(): status and update always, start only while the libraries are missing
 *  RQ-12 — panelHtml() without a key tells how to create one; with a key it carries the form, the steps and no hash
 *  RQ-13 — logTail() and status() read the state directory; the state directory is closed to the web
 *
 * The class works on settings/override/exprepair.ini.append.php and var/repair/ under root(), which is
 * dirname( __DIR__, 3 ) of the class file. The tests run a COPY of the class placed in a temporary
 * root (var/tmp/207/tests/root/lib/ezutils/classes/), renamed ezpRepairQueueCopy, with the worker
 * spawn replaced by a counter: the real files are never touched and no worker is ever started.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 * @group repairqueue
 */

class RepairQueueTest extends PHPUnit\Framework\TestCase
{
    const COPY = 'ezpRepairQueueCopy';

    private static $root;
    private $saved;

    public static function setUpBeforeClass(): void
    {
        $repo = dirname( __DIR__, 4 );
        self::$root = $repo . '/var/tmp/207/tests/root';
        $state = self::$root . "/var/repair";
        if ( is_dir( $state ) )
        {
            $rej = $repo . "/var/tmp/207/tests/rejected";
            if ( !is_dir( $rej ) )
                mkdir( $rej, 0775, true );
            rename( $state, $rej . "/repair-" . date( "YmdHis" ) . "-" . getmypid() );
        }
        $dir = self::$root . '/lib/ezutils/classes';
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0775, true );
        $src = (string) file_get_contents( $repo . '/lib/ezutils/classes/ezprepairqueue.php' );
        $copy = str_replace( 'class ezpRepairQueue', 'class ' . self::COPY, $src, $n1 );
        $copy = str_replace( "        exec( \$cmd );", "        self::\$spawned++;", $copy, $n2 );
        $copy = str_replace( "    const SETTINGS =", "    public static \$spawned = 0;\n    const SETTINGS =", $copy, $n3 );
        if ( $n1 !== 1 || $n2 !== 1 || $n3 !== 1 )
            throw new RuntimeException( 'ezprepairqueue.php changed: the test copy could not be prepared' );
        file_put_contents( $dir . '/ezprepairqueue.php', $copy );
        if ( !class_exists( self::COPY, false ) )
            require $dir . '/ezprepairqueue.php';
    }

    protected function setUp(): void
    {
        $this->saved = array( $_REQUEST, $_POST, $_GET, isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : null );
        $_REQUEST = $_POST = $_GET = array();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->wipe();
        $c = self::COPY;
        $c::$spawned = 0;
    }

    protected function tearDown(): void
    {
        list( $_REQUEST, $_POST, $_GET, $method ) = $this->saved;
        if ( $method === null )
            unset( $_SERVER['REQUEST_METHOD'] );
        else
            $_SERVER['REQUEST_METHOD'] = $method;
        $this->wipe();
    }

    /** empties the temporary root's settings and state (only files this test made) */
    private function wipe()
    {
        $files = array( self::$root . '/settings/override/exprepair.ini.append.php' );
        foreach ( (array) glob( self::$root . '/var/repair/{,.}*', GLOB_BRACE ) as $f )
            if ( is_file( $f ) && basename( $f ) !== '.htaccess' )
                $files[] = $f;
        foreach ( $files as $f )
            if ( is_file( $f ) )
                unlink( $f );
    }

    private function q()
    {
        return self::COPY;
    }

    private function set( $key, $enabled = true )
    {
        $c = $this->q();
        $c::writeSettings( array( 'Enabled' => $enabled, 'KeyHash' => password_hash( $key, PASSWORD_DEFAULT ), 'Composer' => '' ) );
    }

    private function request( $action, array $post = array(), array $get = array() )
    {
        $_REQUEST = array( 'exp_repair' => $action ) + $post + $get;
        $_POST = $post;
        $_GET = $get;
        ob_start();
        $c = $this->q();
        $handled = $c::handleWebRequest();
        $out = ob_get_clean();
        return array( $handled, json_decode( $out, true ), $out );
    }

    /** RQ-01 */
    public function testDefaultsWithoutFile()
    {
        $c = $this->q();
        $this->assertSame( self::$root, $c::root() );
        $this->assertSame( array( 'Enabled' => false, 'KeyHash' => '', 'Composer' => '' ), $c::settings() );
        $this->assertFalse( $c::available() );
    }

    /** RQ-02 */
    public function testSettingsRoundTrip()
    {
        $c = $this->q();
        $c::writeSettings( array( 'Enabled' => true, 'KeyHash' => 'abc$hash', 'Composer' => '/usr/local/bin/composer' ) );
        $s = $c::settings();
        $this->assertTrue( $s['Enabled'] );
        $this->assertSame( 'abc$hash', $s['KeyHash'] );
        $this->assertSame( '/usr/local/bin/composer', $s['Composer'] );
        $file = self::$root . '/' . $c::SETTINGS;
        $this->assertSame( 0, fileperms( $file ) & 0007, 'not readable by others' );
        $this->assertStringStartsWith( '<?php /* #?ini', file_get_contents( $file ) );

        $c::writeSettings( array( 'Enabled' => false, 'KeyHash' => '', 'Composer' => '' ) );
        $this->assertFalse( $c::settings()['Enabled'] );
        $this->assertStringNotContainsString( 'Composer=', file_get_contents( $file ) );

        foreach ( array( 'true' => true, 'enabled' => true, '1' => true, 'false' => false, 'disabled' => false, '' => false ) as $v => $expect )
        {
            file_put_contents( $file, "[RepairSettings]\nEnabled=$v\nKeyHash=x\n" );
            $this->assertSame( $expect, $c::settings()['Enabled'], "Enabled=$v" );
        }
    }

    /** RQ-03 */
    public function testAvailable()
    {
        $c = $this->q();
        $c::writeSettings( array( 'Enabled' => true, 'KeyHash' => '', 'Composer' => '' ) );
        $this->assertFalse( $c::available() );
        $c::writeSettings( array( 'Enabled' => false, 'KeyHash' => 'x', 'Composer' => '' ) );
        $this->assertFalse( $c::available() );
        $c::writeSettings( array( 'Enabled' => true, 'KeyHash' => 'x', 'Composer' => '' ) );
        $this->assertTrue( $c::available() );
    }

    /** RQ-04 */
    public function testCreateKey()
    {
        $c = $this->q();
        $key = $c::createKey();
        $this->assertSame( 24, strlen( $key ) );
        $this->assertMatchesRegularExpression( '/^[A-Za-z0-9]+$/', $key );
        $s = $c::settings();
        $this->assertTrue( $s['Enabled'] );
        $this->assertNotSame( $key, $s['KeyHash'] );
        $this->assertTrue( password_verify( $key, $s['KeyHash'] ) );
        $this->assertStringNotContainsString( $key, file_get_contents( self::$root . '/' . $c::SETTINGS ) );
        $this->assertNotSame( $key, $c::createKey(), 'every key is new' );
        $this->assertFalse( password_verify( $key, $c::settings()['KeyHash'] ), 'the older key no longer works' );
    }

    /** RQ-05 */
    public function testStartRefusals()
    {
        $c = $this->q();
        $_SERVER['REQUEST_METHOD'] = 'GET';
        list( , $r ) = $this->request( 'start', array( 'exp_repair_key' => 'x' ) );
        $this->assertFalse( $r['ok'] );
        $this->assertSame( 'Send the key with POST.', $r['error'] );

        $_SERVER['REQUEST_METHOD'] = 'POST';
        list( , $r ) = $this->request( 'start', array( 'exp_repair_key' => 'x' ) );
        $this->assertSame( 'No repair key is set up on the server.', $r['error'] );

        $this->set( 'right' );
        list( , $r ) = $this->request( 'start', array( 'exp_repair_key' => 'wrong' ) );
        $this->assertFalse( $r['ok'] );
        $this->assertStringContainsString( 'not the repair key', $r['error'] );
        $this->assertSame( 0, $c::$spawned );
        $this->assertSame( array( 'state' => 'idle' ), $c::status() );
        $this->assertTrue( $c::available(), 'a wrong key does not use the key up' );
    }

    /** RQ-06 */
    public function testRightKeyQueuesAndIsUsedUp()
    {
        $c = $this->q();
        $this->set( 'right' );
        list( $handled, $r ) = $this->request( 'start', array( 'exp_repair_key' => '  right  ' ) );
        $this->assertTrue( $handled );
        $this->assertTrue( $r['ok'] );
        $this->assertSame( 32, strlen( $r['token'] ) );
        $this->assertSame( 1, $c::$spawned, 'the worker is started exactly once' );

        $status = $c::status();
        $this->assertSame( 'queued', $status['state'] );
        $this->assertSame( $r['token'], $status['token'] );
        $this->assertSame( array( 'libraries' => 'waiting', 'autoloads' => 'waiting', 'caches' => 'waiting' ), $status['steps'] );
        $this->assertStringContainsString( 'repair queued from the error page', $c::logTail() );

        $this->assertSame( '', $c::settings()['KeyHash'], 'the key is used once' );
        $this->assertFalse( $c::available() );
        list( , $again ) = $this->request( 'start', array( 'exp_repair_key' => 'right' ) );
        $this->assertFalse( $again['ok'] );
        $this->assertSame( 1, $c::$spawned );
    }

    /** RQ-07 */
    public function testFailureLock()
    {
        $c = $this->q();
        $this->set( 'right' );
        for ( $i = 0; $i < $c::MAX_FAILURES; $i++ )
        {
            list( , $r ) = $this->request( 'start', array( 'exp_repair_key' => "bad$i" ) );
            $this->assertStringContainsString( 'not the repair key', $r['error'] );
        }
        list( , $r ) = $this->request( 'start', array( 'exp_repair_key' => 'right' ) );
        $this->assertSame( 'Too many wrong keys. Try again in 15 minutes.', $r['error'] );
        $this->assertSame( 0, $c::$spawned );

        // failures older than the window do not count
        $old = array_fill( 0, $c::MAX_FAILURES, time() - $c::FAILURE_WINDOW - 5 );
        file_put_contents( self::$root . '/var/repair/failures.json', json_encode( $old ) );
        list( , $r ) = $this->request( 'start', array( 'exp_repair_key' => 'right' ) );
        $this->assertTrue( $r['ok'] );
        $this->assertFileDoesNotExist( self::$root . '/var/repair/failures.json', 'a right key clears the failures' );
    }

    /** RQ-08 */
    public function testRunningRepairIsNotStartedTwice()
    {
        $c = $this->q();
        $this->set( 'right' );
        $this->request( 'status' ); // creates the state directory
        file_put_contents( self::$root . '/var/repair/status.json', json_encode( array( 'state' => 'running', 'heartbeat' => time(), 'token' => 't' ) ) );
        list( , $r ) = $this->request( 'start', array( 'exp_repair_key' => 'right' ) );
        $this->assertSame( 'A repair is already running.', $r['error'] );
        $this->assertSame( 0, $c::$spawned );
        $this->assertTrue( $c::available(), 'the key is kept' );

        file_put_contents( self::$root . '/var/repair/status.json', json_encode( array( 'state' => 'running', 'heartbeat' => time() - 600, 'token' => 't' ) ) );
        list( , $r ) = $this->request( 'start', array( 'exp_repair_key' => 'right' ) );
        $this->assertTrue( $r['ok'], 'a worker that went silent does not block' );
    }

    /** RQ-09 */
    public function testHandleWebRequestAndStatus()
    {
        $c = $this->q();
        $_REQUEST = array();
        ob_start();
        $this->assertFalse( $c::handleWebRequest() );
        $this->assertSame( '', ob_get_clean() );
        $_REQUEST = array( 'exp_repair' => 'bogus' );
        ob_start();
        $this->assertFalse( $c::handleWebRequest() );
        ob_end_clean();

        list( $handled, $r ) = $this->request( 'status', array(), array( 'token' => 'nope' ) );
        $this->assertTrue( $handled );
        $this->assertSame( array( 'ok' => false, 'error' => 'unknown' ), $r );

        $this->set( 'right' );
        list( , $start ) = $this->request( 'start', array( 'exp_repair_key' => 'right' ) );
        list( , $r ) = $this->request( 'status', array(), array( 'token' => 'wrong' ) );
        $this->assertSame( 'unknown', $r['error'] );
        list( , $r, $raw ) = $this->request( 'status', array(), array( 'token' => $start['token'] ) );
        $this->assertTrue( $r['ok'] );
        $this->assertSame( 'queued', $r['state'] );
        $this->assertArrayNotHasKey( 'token', $r );
        $this->assertStringNotContainsString( $start['token'], $raw );
        $this->assertSame( $c::$steps, $r['titles'] );
        $this->assertStringContainsString( 'repair queued', $r['log'] );
    }

    /** RQ-10 */
    public function testUpdateConfirmation()
    {
        $c = $this->q();
        $_SERVER['REQUEST_METHOD'] = 'GET';
        list( , $r ) = $this->request( 'update', array( 'token' => 't' ) );
        $this->assertSame( 'Send the request with POST.', $r['error'] );
        $_SERVER['REQUEST_METHOD'] = 'POST';

        list( , $r ) = $this->request( 'update', array( 'token' => 't' ) );
        $this->assertSame( 'unknown', $r['error'] );

        $failed = array( 'state' => 'failed', 'token' => 'tok', 'lock_stale' => true, 'finished' => time(),
                         'steps' => array( 'libraries' => 'failed', 'autoloads' => 'waiting', 'caches' => 'waiting' ) );
        $file = self::$root . '/var/repair/status.json';
        file_put_contents( $file, json_encode( $failed ) );

        list( , $r ) = $this->request( 'update', array( 'token' => 'other' ) );
        $this->assertSame( 'unknown', $r['error'] );
        $this->assertSame( 0, $c::$spawned );

        $old = $failed;
        $old['finished'] = time() - 1000;
        file_put_contents( $file, json_encode( $old ) );
        list( , $r ) = $this->request( 'update', array( 'token' => 'tok' ) );
        $this->assertSame( 'Start the repair again with a new key.', $r['error'] );

        $notStale = $failed;
        $notStale['lock_stale'] = false;
        file_put_contents( $file, json_encode( $notStale ) );
        list( , $r ) = $this->request( 'update', array( 'token' => 'tok' ) );
        $this->assertSame( 'Start the repair again with a new key.', $r['error'] );
        $this->assertSame( 0, $c::$spawned );

        file_put_contents( $file, json_encode( $failed ) );
        list( , $r ) = $this->request( 'update', array( 'token' => 'tok' ) );
        $this->assertSame( array( 'ok' => true, 'token' => 'tok' ), $r );
        $this->assertSame( 1, $c::$spawned );
        $s = $c::status();
        $this->assertSame( 'queued', $s['state'] );
        $this->assertSame( 'update', $s['mode'] );
        $this->assertFalse( $s['lock_stale'] );
        $this->assertSame( array( 'waiting', 'waiting', 'waiting' ), array_values( $s['steps'] ) );
    }

    /** RQ-11 */
    public function testHandleRunRequest()
    {
        $c = $this->q();
        $_REQUEST = array();
        $this->assertFalse( $c::handleRunRequest() );

        $_REQUEST = array( 'exp_repair' => 'nonsense' );
        $this->assertFalse( $c::handleRunRequest() );

        $_REQUEST = array( 'exp_repair' => 'status' );
        $_GET = array( 'token' => 'x' );
        ob_start();
        $handled = $c::handleRunRequest();
        $out = ob_get_clean();
        $this->assertTrue( $handled );
        $this->assertSame( 'unknown', json_decode( $out, true )['error'] );

        // start is answered only while the libraries are missing; the temporary root has no vendor/
        $_REQUEST = array( 'exp_repair' => 'start' );
        $_POST = array( 'exp_repair_key' => 'k' );
        ob_start();
        $handled = $c::handleRunRequest();
        ob_end_clean();
        if ( class_exists( 'Composer\Autoload\ClassLoader', false ) )
            $this->assertFalse( $handled, 'the libraries are loaded: start is left to the kernel' );
        else
            $this->assertTrue( $handled );
    }

    /** RQ-12 */
    public function testPanelHtml()
    {
        $c = $this->q();
        $hint = $c::panelHtml();
        $this->assertStringContainsString( 'php bin/php/exprepair.php --create-key', $hint );
        $this->assertStringNotContainsString( 'exp-repair-form', $hint );

        $key = $c::createKey();
        $panel = $c::panelHtml();
        $this->assertStringContainsString( 'id="exp-repair"', $panel );
        $this->assertStringContainsString( 'exp-repair-form', $panel );
        $this->assertStringContainsString( 'name="exp_repair_key"', $panel );
        foreach ( $c::$steps as $id => $title )
        {
            $this->assertStringContainsString( 'data-step="' . $id . '"', $panel );
            $this->assertStringContainsString( htmlspecialchars( $title, ENT_QUOTES ), $panel );
        }
        $this->assertStringNotContainsString( $key, $panel );
        $this->assertStringNotContainsString( $c::settings()['KeyHash'], $panel );
        $this->assertStringNotContainsString( 'create-key', $panel );
    }

    /** RQ-13 */
    public function testLogTailAndStateDirectory()
    {
        $c = $this->q();
        $this->assertSame( '', $c::logTail() );
        $dir = self::$root . '/var/repair';
        $this->assertTrue( is_dir( $dir ), 'created on first use' );
        $this->assertStringContainsString( 'Require all denied', file_get_contents( $dir . '/.htaccess' ) );
        file_put_contents( $dir . '/repair.log', "one\ntwo\nthree\nfour\n" );
        $this->assertSame( "three\nfour", $c::logTail( 2 ) );
        $this->assertSame( "one\ntwo\nthree\nfour", $c::logTail() );
        file_put_contents( $dir . '/status.json', 'not json' );
        $this->assertSame( array( 'state' => 'idle' ), $c::status() );
    }
}
