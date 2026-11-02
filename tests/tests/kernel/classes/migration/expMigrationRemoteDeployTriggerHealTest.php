<?php
/**
 * Remote deploy, part 3, without a database and without a network: the signed HTTP trigger of FTP remotes
 * (expMigrationTrigger), self-healing (expMigrationHeal, exp:migration:heal) and the host checks of the preflight
 * (free disk space, PHP and extension versions). Destinations are local folders with the stand-in console scripts
 * fixtures/remotedeploy/fakemigrationcli.php; FTP and HTTP are played by expMigrationRemoteProcess::setRunner().
 *
 *  TH-01 — the signature: signed afresh, expired, from the future, another body, another key, malformed
 *  TH-02 — source addresses: exact, CIDR (IPv4, IPv6), IPv4-mapped, an empty list takes nothing
 *  TH-03 — the trigger's answers: 405, 404, 403, 413, 503, 401, 409 (replay), 429, 400, 200 with the run's report
 *  TH-04 — requests: the arguments of a console script become a request and back a command line, nothing else
 *  TH-05 — FTP remotes with Trigger: settings, the operation http, curl's configuration on stdin (nothing on argv)
 *  TH-06 — exp:migration:heal: a stale holder file, a lock whose process is gone (moved aside), left alone otherwise;
 *          paths and modes; a dry run changes nothing
 *  TH-07 — host checks: disk space against the transfer and margin, PHP major/minor/warn/off, extensions
 *  TH-08 — a deploy with SelfHeal[]: a stale lock fixed before the preflight, caches after the switch; a live lock and
 *          too little disk space stop it
 *  TH-09 — a deploy to an FTP remote through its trigger: sent, verified by a dry run there, switched, taken back
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expMigrationRemoteDeployTriggerHealTest extends PHPUnit\Framework\TestCase
{
    const KEY = 'trigger-secret-0123456789abcdef';

    /** @var string */
    private $dir;

    protected function setUp(): void
    {
        $this->dir = dirname( __DIR__, 5 ) . '/var/tmp/expmigrationtriggerheal-' . getmypid() . '-' . mt_rand();
        mkdir( $this->dir . '/source/migrations', 0775, true );
        expMigrationRemoteProcess::setRunner( null );
    }

    protected function tearDown(): void
    {
        expMigrationRemoteProcess::setRunner( null );
        self::remove( $this->dir );
    }

    private static function remove( $path )
    {
        if ( is_dir( $path ) && !is_link( $path ) )
        {
            @chmod( $path, 0775 );
            foreach ( scandir( $path ) as $name )
                if ( $name !== '.' && $name !== '..' )
                    self::remove( "$path/$name" );
            rmdir( $path );
        }
        else if ( file_exists( $path ) || is_link( $path ) )
            unlink( $path );
    }

    private function migration( $id, $content = "steps: []\n" )
    {
        file_put_contents( $this->dir . "/source/migrations/$id.yml", $content );
    }

    private function local()
    {
        $local = array();
        foreach ( glob( $this->dir . '/source/migrations/*.yml' ) as $file )
        {
            $rel = 'migrations/' . basename( $file );
            $local[] = array( 'id' => basename( $file, '.yml' ), 'source' => 'site', 'files' => array( '*' => $rel ), 'paths' => array( $rel => $file ),
                              'checksums' => array( '*' => expMigrationDefinition::checksumOf( file_get_contents( $file ) ) ) );
        }
        return $local;
    }

    /** A destination folder with the stand-in console scripts (exp:migration:heal included). */
    private function folder( $name )
    {
        $path = $this->dir . '/' . $name;
        foreach ( array( 'bin/php', 'migrations', 'var' ) as $sub )
            mkdir( "$path/$sub", 0775, true );
        foreach ( array( 'expmigrationstatus', 'expmigrationmigrate', 'expmigrationrollback', 'expmigrationheal' ) as $script )
            copy( __DIR__ . '/fixtures/remotedeploy/fakemigrationcli.php', "$path/bin/php/$script.php" );
        return $path;
    }

    private function remote( $name, array $settings = array() )
    {
        return new expMigrationRemote( $name, $settings + array( 'Transport' => 'local', 'Path' => $this->folder( $name ), 'Php' => PHP_BINARY, 'Environment' => $name ) );
    }

    private function deploy( array $remotes, array $options = array() )
    {
        $list = array();
        foreach ( $remotes as $remote )
            $list[$remote->id()] = $remote;
        return new expMigrationRemoteDeploy( $list, $options + array(
            'root' => $this->dir . '/source', 'work_dir' => $this->dir . '/work', 'local' => $this->local(), 'version' => '6.0.15', 'retries' => 0,
            'audit' => function () {}, 'host' => array( 'php' => PHP_VERSION, 'disk_free' => 1 << 40, 'extensions' => array() ),
            'lookup' => function ( $name ) { return $name === 'trig' ? self::KEY : ( $name === 'ftp' ? 'ftp-pass-1' : null ); },
        ) );
    }

    private function trigger( array $settings = array(), $root = null )
    {
        return new expMigrationTrigger( $settings + array( 'enabled' => true, 'secret' => 'trig', 'addresses' => array( '192.0.2.10', '2001:db8::/32' ), 'php' => PHP_BINARY ),
                                        array( 'root' => $root ?: $this->dir, 'nonce_dir' => $this->dir . '/nonces',
                                               'lookup' => function ( $name ) { return $name === 'trig' ? self::KEY : null; } ) );
    }

    /** TH-01 */
    public function testSignature()
    {
        $body = '{"action":"status"}';
        $token = expMigrationTrigger::sign( self::KEY, $body, 1000, str_repeat( 'a', 32 ) );
        $this->assertMatchesRegularExpression( '/^v1\.1000\.a{32}\.[A-Za-z0-9_-]{43}$/', $token );
        $this->assertSame( '', expMigrationTrigger::problem( $token, $body, self::KEY, 300, 1100 ) );
        $this->assertSame( 'the signature has expired', expMigrationTrigger::problem( $token, $body, self::KEY, 300, 1301 ) );
        $this->assertSame( 'the signature was issued in the future', expMigrationTrigger::problem( $token, $body, self::KEY, 300, 900 ) );
        $this->assertSame( 'the signature does not match', expMigrationTrigger::problem( $token, '{"action":"apply"}', self::KEY, 300, 1100 ) );
        $this->assertSame( 'the signature does not match', expMigrationTrigger::problem( $token, $body, 'another-key-0123456789', 300, 1100 ) );
        $this->assertSame( 'no valid signature', expMigrationTrigger::problem( 'v1.1000.zz.abc', $body, self::KEY, 300, 1100 ) );
        $this->assertSame( 'no valid signature', expMigrationTrigger::problem( null, $body, self::KEY, 300, 1100 ) );
        $this->assertNotSame( expMigrationTrigger::sign( self::KEY, $body ), expMigrationTrigger::sign( self::KEY, $body ), 'a fresh nonce every time' );
    }

    /** TH-02 */
    public function testAddresses()
    {
        $allowed = array( '192.0.2.10', '198.51.100.0/24', '2001:db8::/32', '10.0.0.0/9' );
        foreach ( array( '192.0.2.10', '198.51.100.77', '2001:db8:1::5', '::ffff:198.51.100.1', '10.127.255.255' ) as $address )
            $this->assertTrue( expMigrationTrigger::addressAllowed( $address, $allowed ), $address );
        foreach ( array( '192.0.2.11', '198.51.101.1', '2001:db9::1', '10.128.0.1', '', 'localhost', '192.0.2.10, 1.2.3.4' ) as $address )
            $this->assertFalse( expMigrationTrigger::addressAllowed( $address, $allowed ), $address );
        $this->assertFalse( expMigrationTrigger::addressAllowed( '192.0.2.10', array() ) );
        $this->assertFalse( expMigrationTrigger::addressAllowed( '192.0.2.10', array( '192.0.2.0/33', 'nonsense' ) ) );
    }

    /** TH-03 */
    public function testAnswers()
    {
        $trigger = $this->trigger();
        $body = '{"action":"status","runs":1}';
        $now = time();
        $sign = function ( $b = null, $t = null ) use ( $body, $now ) { return expMigrationTrigger::sign( self::KEY, $b === null ? $body : $b, $t === null ? $now : $t ); };
        $this->assertSame( 405, $trigger->handle( 'GET', '192.0.2.10', $sign(), $body, $now )['status'] );
        $this->assertSame( 404, $this->trigger( array( 'enabled' => false ) )->handle( 'POST', '192.0.2.10', $sign(), $body, $now )['status'] );
        $this->assertSame( 403, $trigger->handle( 'POST', '203.0.113.5', $sign(), $body, $now )['status'] );
        $this->assertSame( 403, $this->trigger( array( 'addresses' => array() ) )->handle( 'POST', '192.0.2.10', $sign(), $body, $now )['status'] );
        $big = str_repeat( 'x', expMigrationTrigger::MAX_BODY + 1 );
        $this->assertSame( 413, $trigger->handle( 'POST', '192.0.2.10', $sign( $big ), $big, $now )['status'] );
        $this->assertSame( 503, $this->trigger( array( 'secret' => 'nothere' ) )->handle( 'POST', '192.0.2.10', $sign(), $body, $now )['status'] );
        $this->assertSame( 401, $trigger->handle( 'POST', '192.0.2.10', null, $body, $now )['status'] );
        $this->assertSame( 401, $trigger->handle( 'POST', '192.0.2.10', $sign( null, $now - 400 ), $body, $now )['status'] );
        $this->assertSame( 401, $trigger->handle( 'POST', '192.0.2.10', $sign(), '{"action":"apply"}', $now )['status'] );

        $calls = array();
        expMigrationRemoteProcess::setRunner( function ( array $jobs ) use ( &$calls ) {
            $calls[] = $jobs['trigger'][0];
            return array( 'trigger' => array( 'exit' => 0, 'stdout' => '{"schema":"exp-migration/1","mode":"status","migrations":[],"errors":[],"exit":0}',
                                              'stderr' => '', 'duration_ms' => 1, 'done' => 1 ) );
        } );
        $token = $sign();
        $answer = $trigger->handle( 'POST', '2001:db8::7', $token, $body, $now );
        $this->assertSame( 200, $answer['status'] );
        $this->assertSame( 'exp-migration/1', $answer['body']['schema'] );
        $this->assertSame( array( PHP_BINARY, 'bin/php/expmigrationstatus.php', '--format=json', '--runs=1' ), $calls[0]['argv'] );
        $this->assertSame( $this->dir, $calls[0]['cwd'] );
        $this->assertSame( 409, $trigger->handle( 'POST', '2001:db8::7', $token, $body, $now )['status'], 'a nonce is taken once' );
        $bad = '{"action":"shell","cmd":"id"}';
        $this->assertSame( 400, $trigger->handle( 'POST', '192.0.2.10', $sign( $bad ), $bad, $now )['status'] );
        $bad = '{"action":"apply","run_id":"x;id"}';
        $this->assertSame( 400, $trigger->handle( 'POST', '192.0.2.10', $sign( $bad ), $bad, $now )['status'] );
        $this->assertCount( 1, $calls, 'nothing ran for a refused request' );

        expMigrationRemoteProcess::setRunner( function () {
            return array( 'trigger' => array( 'exit' => 255, 'stdout' => '', 'stderr' => 'PHP Fatal error', 'duration_ms' => 1, 'done' => 0 ) );
        } );
        $answer = $trigger->handle( 'POST', '192.0.2.10', $sign(), $body, $now );
        $this->assertSame( 502, $answer['status'] );
        $this->assertStringContainsString( 'PHP Fatal error', $answer['body']['error'] );

        $limited = $this->trigger( array( 'per_minute' => 2 ) );
        $statuses = array();
        for ( $i = 0; $i < 3; $i++ )
            $statuses[] = $limited->handle( 'POST', '192.0.2.10', $sign(), $body, $now )['status'];
        $this->assertSame( 429, end( $statuses ), 'the third request of the minute is over the limit' );
    }

    /** TH-04 */
    public function testRequests()
    {
        $cases = array(
            array( array( 'bin/php/expmigrationstatus.php', '--format=json', '--runs=1' ), array( 'action' => 'status', 'runs' => 1 ),
                   array( 'p', 'bin/php/expmigrationstatus.php', '--format=json', '--runs=1' ) ),
            array( array( 'bin/php/expmigrationmigrate.php', '--format=json', '--only=a,b' ), array( 'action' => 'dry-run', 'only' => array( 'a', 'b' ) ),
                   array( 'p', 'bin/php/expmigrationmigrate.php', '--format=json', '--only=a,b' ) ),
            array( array( 'bin/php/expmigrationmigrate.php', '--apply', '--format=json', '--run-id=20261102-120000-abcdef' ),
                   array( 'action' => 'apply', 'run_id' => '20261102-120000-abcdef', 'only' => array() ),
                   array( 'p', 'bin/php/expmigrationmigrate.php', '--format=json', '--apply', '--run-id=20261102-120000-abcdef' ) ),
            array( array( 'bin/php/expmigrationrollback.php', '20261102-120000-abcdef', '--apply', '--format=json' ),
                   array( 'action' => 'rollback', 'run_id' => '20261102-120000-abcdef' ),
                   array( 'p', 'bin/php/expmigrationrollback.php', '20261102-120000-abcdef', '--apply', '--format=json' ) ),
            array( array( 'bin/php/expmigrationheal.php', '--apply', '--format=json', '--fix=stale-lock,modes', '--stale-lock-age=600' ),
                   array( 'action' => 'heal', 'fix' => array( 'stale-lock', 'modes' ), 'stale_lock_age' => 600 ),
                   array( 'p', 'bin/php/expmigrationheal.php', '--apply', '--format=json', '--fix=stale-lock,modes', '--stale-lock-age=600' ) ),
        );
        foreach ( $cases as $case )
        {
            $request = expMigrationTrigger::requestFromArgs( $case[0] );
            $this->assertSame( $case[1], $request );
            $this->assertSame( $case[2], expMigrationTrigger::commandOf( json_decode( json_encode( $request ), true ), 'p' ) );
        }
        $this->assertNull( expMigrationTrigger::requestFromArgs( array( 'bin/php/ezcache.php', '--clear-all' ) ) );
        $this->assertSame( 'p', expMigrationTrigger::commandOf( array( 'action' => 'status' ), 'p', true )[0] );
        $this->assertContains( '--allow-root-user', expMigrationTrigger::commandOf( array( 'action' => 'status' ), 'p', true ) );
        foreach ( array( null, 'x', array(), array( 'action' => 'rm' ), array( 'action' => 'dry-run', 'only' => array( '--apply' ) ),
                         array( 'action' => 'heal', 'fix' => array( 'everything' ) ), array( 'action' => 'heal' ), array( 'action' => 'rollback' ) ) as $bad )
            $this->assertIsString( expMigrationTrigger::commandOf( $bad, 'p' ), json_encode( $bad ) );
    }

    /** TH-05 */
    public function testFtpRemoteWithTrigger()
    {
        $settings = array( 'Transport' => 'ftp', 'Host' => 'ftp.example.com', 'User' => 'web', 'Path' => '/site', 'Credential' => 'password:ftp',
                           'Trigger' => 'https://www.example.com/migration/trigger', 'TriggerSecret' => 'trig' );
        $remote = new expMigrationRemote( 'f', $settings );
        $this->assertSame( array(), $remote->validate() );
        $this->assertTrue( $remote->canRun() );
        $this->assertSame( 'https://www.example.com/migration/trigger', $remote->toArray()['trigger'] );
        $this->assertFalse( ( new expMigrationRemote( 'f', array( 'Trigger' => '' ) + $settings ) )->canRun() );
        foreach ( array( array( 'Trigger' => 'http://www.example.com/migration/trigger' ), array( 'Trigger' => 'ftp://x/y' ), array( 'TriggerSecret' => '' ),
                         array( 'TriggerSecret' => 'pa ss"word' ), array( 'Transport' => 'ssh', 'Credential' => 'none' ) ) as $bad )
            $this->assertNotSame( array(), ( new expMigrationRemote( 'f', $bad + $settings ) )->validate(), json_encode( $bad ) );
        $this->assertTrue( expMigrationTrigger::validUrl( 'http://127.0.0.1:8080/migration/trigger' ) );

        $credential = new expMigrationRemoteCredential( 'password:ftp', array( 'work_dir' => $this->dir . '/work', 'lookup' => function () { return 'ftp-pass-1'; } ) );
        $credential->problem();
        $transport = new expMigrationRemoteTransport( $remote, $credential, $this->dir . '/work/kh' );
        $this->assertNull( $transport->run( array( 'bin/php/expmigrationstatus.php', '--format=json' ) ), 'no key: no request' );
        $transport->setTriggerKey( self::KEY );
        $op = $transport->run( array( 'bin/php/expmigrationmigrate.php', '--apply', '--format=json', '--run-id=r1' ) );
        $this->assertSame( 'https://www.example.com/migration/trigger', $op['http']['url'] );
        $this->assertSame( array( 'action' => 'apply', 'run_id' => 'r1', 'only' => array() ), json_decode( $op['http']['body'], true ) );

        $config = expMigrationTrigger::curlConfig( $op['http']['url'], self::KEY, '{"a":"x\\"y"}', 1000, str_repeat( 'b', 32 ) );
        $this->assertStringContainsString( 'url = "https://www.example.com/migration/trigger"', $config );
        $this->assertStringContainsString( 'data-binary = "{\\"a\\":\\"x\\\\\\"y\\"}"', $config );
        $this->assertStringContainsString( 'header = "X-Exp-Migration-Trigger: ' . expMigrationTrigger::sign( self::KEY, '{"a":"x\\"y"}', 1000, str_repeat( 'b', 32 ) ) . '"', $config );
        $this->assertStringNotContainsString( self::KEY, $config, 'the key signs, it is never sent' );
        $credential->cleanup();
    }

    /** TH-06 */
    public function testHeal()
    {
        $var = $this->dir . '/dest/var';
        mkdir( $var, 0775, true );
        $lock = expMigrationLock::installation( $var . '/upgrade.lock' );
        // a holder file left by a run that ended
        file_put_contents( $lock->holderFile(), json_encode( array( 'purpose' => 'migration run old', 'pid' => 4242, 'host' => 'h1', 'started' => 1 ) ) );
        $dry = ( new expMigrationHeal( array( 'lock' => $lock, 'directories' => array() ) ) )->run( array( 'stale-lock' ) );
        $this->assertStringStartsWith( 'would: stale-lock: removed the holder file', $dry['fixes'][0] );
        $this->assertFileExists( $lock->holderFile() );
        $done = ( new expMigrationHeal( array( 'apply' => true, 'lock' => $lock, 'directories' => array() ) ) )->run( array( 'stale-lock' ) );
        $this->assertStringStartsWith( 'stale-lock: removed the holder file', $done['fixes'][0] );
        $this->assertFileDoesNotExist( $lock->holderFile() );

        // a lock still held by a process that inherited it, its run's process gone
        $child = proc_open( array( PHP_BINARY, '-r', '$h = fopen( $argv[1], "c" ); flock( $h, LOCK_EX ); echo "locked\n"; fflush( STDOUT ); sleep( 30 );', $lock->file() ),
                            array( 1 => array( 'pipe', 'w' ) ), $pipes );
        $this->assertSame( "locked\n", fgets( $pipes[1] ) );
        file_put_contents( $lock->holderFile(), json_encode( array( 'purpose' => 'migration run r9', 'pid' => 4242, 'host' => 'h1', 'started' => 1000 ) ) );
        $heal = function ( array $o ) use ( $lock ) {
            return ( new expMigrationHeal( $o + array( 'apply' => true, 'lock' => $lock, 'directories' => array(), 'host' => 'h1', 'now' => 10000,
                                                       'alive' => function () { return false; }, 'owner' => function () { return true; } ) ) )->run( array( 'stale-lock' ) );
        };
        $this->assertStringContainsString( 'not on this host', $heal( array( 'host' => 'h2' ) )['problems'][0] );
        $this->assertStringContainsString( 'still runs', $heal( array( 'alive' => function () { return true; } ) )['problems'][0] );
        $this->assertStringContainsString( 'younger than the stale lock age', $heal( array( 'stale_lock_age' => 20000 ) )['problems'][0] );
        $this->assertStringContainsString( 'another user', $heal( array( 'owner' => function () { return false; } ) )['problems'][0] );
        $this->assertTrue( $lock->isLocked(), 'left alone' );
        $report = $heal( array() );
        $this->assertSame( array(), $report['problems'] );
        $this->assertStringContainsString( 'moved the lock of a run whose process is gone aside to upgrade.lock.stale-10000', $report['fixes'][0] );
        $this->assertFalse( $lock->isLocked(), 'a new run can take the lock' );
        $this->assertTrue( $lock->acquire( array( 'purpose' => 'next run' ) ) );
        $lock->release();
        proc_terminate( $child );
        proc_close( $child );

        // paths and modes
        $missing = $this->dir . '/dest/migrations';
        $report = ( new expMigrationHeal( array( 'directories' => array( $missing ) ) ) )->run( array( 'paths' ) );
        $this->assertSame( array( "would: paths: made the missing folder $missing" ), $report['fixes'] );
        $this->assertDirectoryDoesNotExist( $missing );
        $options = array( 'apply' => true, 'directories' => array( $missing ), 'dir_mode' => 0775, 'file_mode' => 0664, 'owner' => function () { return true; } );
        ( new expMigrationHeal( $options ) )->run( array( 'paths' ) );
        $this->assertDirectoryExists( $missing );
        file_put_contents( "$missing/a.yml", "steps: []\n" );
        chmod( "$missing/a.yml", 0600 );
        $report = ( new expMigrationHeal( $options ) )->run( array( 'modes' ) );
        $this->assertStringContainsString( 'modes: gave ', $report['fixes'][0] );
        clearstatcache();
        $this->assertSame( 0664, fileperms( "$missing/a.yml" ) & 0777 );
        $this->assertSame( array(), ( new expMigrationHeal( $options ) )->run( array( 'modes' ) )['fixes'], 'nothing left to fix' );
        $report = ( new expMigrationHeal( array( 'apply' => true, 'caches' => function ( array $caches ) { return array( 'cleared ' . implode( ',', $caches ) ); } ) ) )->run( array( 'caches' ) );
        $this->assertSame( array( 'caches: cleared tag:ini,template-override' ), $report['fixes'] );
        $unknown = array();
        $this->assertSame( array( 'stale-lock', 'modes' ), expMigrationHeal::normalize( 'modes, stale-lock,bogus', $unknown ) );
        $this->assertSame( array( 'bogus' ), $unknown );
    }

    /** TH-07 */
    public function testHostChecks()
    {
        $source = array( 'php' => '8.4.3', 'disk_free' => 0, 'extensions' => array( 'news' => '2.1.0', 'shop' => '1.0.0', 'blog' => '' ) );
        $remote = array( 'php' => '8.4.9', 'disk_free' => 60 * 1048576, 'extensions' => array( 'news' => '2.1.0', 'shop' => '1.0.1', 'blog' => '' ) );
        $ok = expMigrationRemoteDeploy::hostProblems( $remote, $source, 5 * 1048576, array( 'news' ), array() );
        $this->assertSame( array(), $ok['problems'] );
        $this->assertSame( array(), $ok['warnings'], 'shop differs, but none of its migrations is sent' );
        $disk = expMigrationRemoteDeploy::hostProblems( $remote, $source, 15 * 1048576, array(), array() );
        $this->assertSame( array( 'only 60.0 MB free there; the transfer needs 15.0 MB and the margin 50.0 MB ([RemoteSettings] DiskMargin)' ), $disk['problems'] );
        $this->assertSame( array(), expMigrationRemoteDeploy::hostProblems( $remote, $source, null, array(), array() )['problems'], 'no transfer, no disk check' );
        $minor = expMigrationRemoteDeploy::hostProblems( array( 'php' => '8.3.1' ) + $remote, $source, 0, array(), array( 'php_check' => 'minor' ) );
        $this->assertStringContainsString( 'PHP there is 8.3.1, here 8.4.3', $minor['problems'][0] );
        $this->assertSame( array(), expMigrationRemoteDeploy::hostProblems( array( 'php' => '8.3.1' ) + $remote, $source, 0, array(), array() )['problems'], 'major: 8.3 and 8.4 pass' );
        $this->assertNotSame( array(), expMigrationRemoteDeploy::hostProblems( array( 'php' => '7.4.33' ) + $remote, $source, 0, array(), array() )['problems'] );
        $this->assertCount( 1, expMigrationRemoteDeploy::hostProblems( array( 'php' => '7.4.33' ) + $remote, $source, 0, array(), array( 'php_check' => 'warn' ) )['warnings'] );
        $this->assertSame( array(), expMigrationRemoteDeploy::hostProblems( array( 'php' => '7.4.33' ) + $remote, $source, 0, array(), array( 'php_check' => 'off' ) )['problems'] );
        $ext = expMigrationRemoteDeploy::hostProblems( $remote, $source, 0, array( 'shop' ), array() );
        $this->assertSame( array( 'the extension shop is 1.0.1 there, here 1.0.0 ([RemoteSettings] ExtensionCheck=migrations)' ), $ext['problems'] );
        $all = expMigrationRemoteDeploy::hostProblems( array( 'extensions' => array( 'news' => '2.1.0' ) ) + $remote, $source, 0, array(), array( 'extension_check' => 'all' ) );
        $this->assertCount( 2, $all['problems'] );
        $this->assertStringContainsString( 'the extension shop is not active there', implode( "\n", $all['problems'] ) );
        $this->assertCount( 2, expMigrationRemoteDeploy::hostProblems( array( 'extensions' => array( 'news' => '2.1.0' ) ) + $remote, $source, 0, array( 'shop' ), array( 'extension_check' => 'warn' ) )['warnings'] );
        $old = expMigrationRemoteDeploy::hostProblems( null, $source, 10, array( 'shop' ), array() );
        $this->assertSame( array(), $old['problems'] );
        $this->assertStringContainsString( 'an older kernel', $old['warnings'][0] );
        $this->assertSame( array( 'news', 'shop' ), expMigrationRemoteDeploy::extensionsOf( array( array( 'source' => 'extension:news' ), array( 'source' => 'site' ),
                                                                                                    array( 'source' => 'extension:shop' ), array( 'source' => 'extension:news' ) ) ) );
    }

    /** TH-08 */
    public function testDeployWithSelfHealing()
    {
        $this->migration( '20261102100000_a' );
        $a = $this->remote( 'a' );
        $b = $this->remote( 'b' );
        touch( $a->path() . '/var/fake-lock' );
        touch( $a->path() . '/var/fake-lock-stale' );

        $refused = $this->deploy( array( $a, $b ), array( 'apply' => true, 'store' => false ) )->run();
        $this->assertSame( 'refused', $refused['state'], 'without SelfHeal[] the lock stops the deploy' );

        $report = $this->deploy( array( $a, $b ), array( 'apply' => true, 'store' => false, 'heal' => array( 'stale-lock', 'caches' ) ) )->run();
        $this->assertSame( 'deployed', $report['state'], implode( "\n", $report['errors'] ) );
        $this->assertSame( array( 'heal stale-lock: moved the lock of a run whose process is gone aside', 'heal caches: cleared the caches tagged ini' ),
                           $report['remotes']['a']['fixes'] );
        $this->assertSame( array( 'heal caches: cleared the caches tagged ini' ), $report['remotes']['b']['fixes'] );
        $this->assertGreaterThan( 0, $report['remotes']['a']['send_bytes'] );
        $this->assertSame( PHP_VERSION, $report['remotes']['a']['host']['php'] );

        // a lock whose process still runs is only reported
        $this->migration( '20261102110000_b' );
        touch( $a->path() . '/var/fake-lock' );
        $report = $this->deploy( array( $a, $b ), array( 'apply' => true, 'store' => false, 'heal' => array( 'stale-lock' ) ) )->run();
        $this->assertSame( 'refused', $report['state'] );
        $this->assertStringContainsString( 'its process still runs', implode( "\n", $report['warnings'] ) );
        unlink( $a->path() . '/var/fake-lock' );

        // too little disk space there stops it before anything is sent
        file_put_contents( $b->path() . '/var/fake-host.json', json_encode( array( 'php' => PHP_VERSION, 'disk_free' => 1024, 'extensions' => array() ) ) );
        $report = $this->deploy( array( $a, $b ), array( 'apply' => true, 'store' => false ) )->run();
        $this->assertSame( 'refused', $report['state'] );
        $this->assertStringContainsString( 'b: only 0.0 MB free there', implode( "\n", $report['errors'] ) );
        $this->assertFileDoesNotExist( $a->path() . '/migrations/20261102110000_b.yml' );
    }

    /** TH-09 */
    public function testDeployThroughTheTrigger()
    {
        $this->migration( '20261102100000_a' );
        $this->migration( '20261102110000_b' );
        $dest = $this->folder( 'ftp1' );
        $ftp = new expMigrationRemote( 'ftp1', array( 'Transport' => 'ftp', 'Host' => '127.0.0.1', 'Port' => 2121, 'User' => 'web', 'Path' => '/site',
                                                      'Credential' => 'password:ftp', 'Trigger' => 'http://127.0.0.1:8099/migration/trigger', 'TriggerSecret' => 'trig' ) );
        $plain = new expMigrationRemote( 'ftp2', array( 'Transport' => 'ftp', 'Host' => '127.0.0.1', 'Path' => '/site2', 'Credential' => 'password:ftp' ) );
        $other = $this->remote( 'local1' );
        $trigger = $this->trigger( array( 'addresses' => array( '127.0.0.1' ) ), $dest );
        $requests = array();
        $runner = function ( array $jobs, $parallel, $timeout ) use ( &$runner, &$requests, $trigger, $dest ) {
            $results = array();
            foreach ( $jobs as $key => $ops )
            {
                $results[$key] = array( 'exit' => 0, 'stdout' => '', 'stderr' => '', 'duration_ms' => 1, 'done' => 0 );
                foreach ( $ops as $op )
                {
                    if ( isset( $op['http'] ) )
                    {
                        // the destination's view: signed now, as the process does
                        $requests[] = json_decode( $op['http']['body'], true )['action'];
                        $body = $op['http']['body'];
                        expMigrationRemoteProcess::setRunner( null );
                        $answer = $trigger->handle( 'POST', '127.0.0.1', expMigrationTrigger::sign( $op['http']['key'], $body ), $body );
                        expMigrationRemoteProcess::setRunner( $runner );
                        $results[$key]['stdout'] = json_encode( $answer['body'] );
                        $results[$key]['exit'] = $answer['status'] === 200 ? 0 : 22;
                    }
                    else if ( isset( $op['argv'] ) && $op['argv'][0] === 'curl' && in_array( '-T', $op['argv'], true ) )
                    {
                        // an FTP upload into the destination folder
                        $local = $op['argv'][array_search( '-T', $op['argv'], true ) + 1];
                        $rel = preg_replace( '#^ftp://[^/]+/site/#', '', end( $op['argv'] ) );
                        copy( $local, "$dest/$rel" );
                    }
                    else
                    {
                        expMigrationRemoteProcess::setRunner( null );
                        $real = expMigrationRemoteProcess::runAll( array( $key => array( $op ) ), 1, $timeout );
                        expMigrationRemoteProcess::setRunner( $runner );
                        $results[$key] = $real[$key];
                    }
                    if ( $results[$key]['exit'] !== 0 )
                        break;
                    $results[$key]['done']++;
                }
            }
            return $results;
        };
        expMigrationRemoteProcess::setRunner( $runner );

        $refused = $this->deploy( array( $plain ), array( 'apply' => true, 'store' => false ) )->run();
        $this->assertSame( 'refused', $refused['state'] );
        $this->assertStringContainsString( 'give it the HTTP trigger', implode( "\n", $refused['errors'] ) );

        $report = $this->deploy( array( $ftp, $other ), array( 'apply' => true, 'store' => false, 'heal' => array( 'stale-lock' ) ) )->run();
        $this->assertSame( 'deployed', $report['state'], implode( "\n", $report['errors'] ) );
        $this->assertSame( array( 'heal', 'status', 'dry-run', 'apply' ), $requests );
        $this->assertSame( array( '20261102100000_a', '20261102110000_b' ), array_keys( json_decode( file_get_contents( "$dest/var/fake-state.json" ), true )['done'] ) );
        $this->assertTrue( $report['in_sync'] );

        // the second remote fails: the FTP remote is taken back through its trigger
        $this->migration( '20261102120000_c' );
        file_put_contents( $other->path() . '/migrations/20261102115000_breaks.yml', "FAIL\n" );
        $requests = array();
        $report = $this->deploy( array( $ftp, $other ), array( 'apply' => true, 'store' => false ) )->run();
        $this->assertSame( 'rolled-back', $report['state'], implode( "\n", $report['errors'] ) );
        $this->assertSame( 'apply', $requests[2] );
        $this->assertSame( 'rolled-back', $report['remotes']['ftp1']['state'] );
        $this->assertSame( array( 'status', 'dry-run', 'apply', 'rollback' ), $requests );
        $this->assertCount( 2, json_decode( file_get_contents( "$dest/var/fake-state.json" ), true )['done'] );
    }
}
