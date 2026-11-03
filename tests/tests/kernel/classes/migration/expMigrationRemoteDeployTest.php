<?php
/**
 * Remote deploy of migrations (expMigrationRemote*), without a database and without a network: the settings, the
 * credentials, the commands every transport builds, the plan, and the all-or-nothing state machine against
 * destination installations in local folders (transport local) whose migration console scripts are the stand-in
 * fixtures/remotedeploy/fakemigrationcli.php; retries through expMigrationRemoteProcess::setRunner().
 *
 *  RD-01 — remotes: settings, validation (a secret in the INI is refused), selection by id, group and all
 *  RD-02 — credentials: kinds, secrets by name or env:, key file only the process reads, removed; redaction
 *  RD-03 — transports: ssh, scp, sftp, rsync, ftp, local operations; no secret on a command line; host key match
 *  RD-04 — the plan: unchanged files not sent, a changed applied file stops, the remote ahead is reported
 *  RD-05 — a deploy to three remotes: dry run changes nothing, apply sends, verifies and switches all in sync
 *  RD-06 — one remote fails: every remote that applied is taken back, all end where they were (in sync)
 *  RD-07 — a remote that cannot be taken back: out-of-sync with every remote's state
 *  RD-08 — the preflight stops everything: a lock, a version, captions off, a missing secret
 *  RD-09 — retries of a connection failure, never of the switch; status and in-sync check; parallel processes
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expMigrationRemoteDeployTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $dir;

    /** @var array[] the audit events */
    private $events = array();

    protected function setUp(): void
    {
        $this->dir = dirname( __DIR__, 5 ) . '/var/tmp/expmigrationremotedeploy-' . getmypid() . '-' . mt_rand();
        mkdir( $this->dir . '/source/migrations', 0775, true );
        $this->events = array();
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
            foreach ( scandir( $path ) as $name )
                if ( $name !== '.' && $name !== '..' )
                    self::remove( "$path/$name" );
            rmdir( $path );
        }
        else if ( file_exists( $path ) || is_link( $path ) )
            unlink( $path );
    }

    /** A source migration file. */
    private function migration( $id, $content = "steps: []\n" )
    {
        file_put_contents( $this->dir . "/source/migrations/$id.yml", $content );
    }

    /** @return array[] the source's migrations as localDefinitions() returns them */
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

    /** A destination installation in a folder, with the stand-in console scripts. */
    private function destination( $name )
    {
        $path = $this->dir . '/' . $name;
        mkdir( "$path/bin/php", 0775, true );
        mkdir( "$path/migrations", 0775, true );
        mkdir( "$path/var", 0775, true );
        foreach ( array( 'expmigrationstatus', 'expmigrationmigrate', 'expmigrationrollback' ) as $script )
            copy( __DIR__ . '/fixtures/remotedeploy/fakemigrationcli.php', "$path/bin/php/$script.php" );
        return new expMigrationRemote( $name, array( 'Transport' => 'local', 'Path' => $path, 'Php' => PHP_BINARY, 'Environment' => $name,
                                                     'Groups' => array( 'all-envs' ) ) );
    }

    /** @return array id => done (migration id => checksum) of a destination */
    private function done( $name )
    {
        $file = $this->dir . "/$name/var/fake-state.json";
        return is_file( $file ) ? json_decode( file_get_contents( $file ), true )['done'] : array();
    }

    private function deploy( array $remotes, array $options = array() )
    {
        $list = array();
        foreach ( $remotes as $remote )
            $list[$remote->id()] = $remote;
        $events =& $this->events;
        $deploy = new expMigrationRemoteDeploy( $list, $options + array(
            'root' => $this->dir . '/source', 'work_dir' => $this->dir . '/work', 'local' => $this->local(), 'version' => '6.0.15', 'retries' => 0,
            'audit' => function ( $name, $data ) use ( &$events ) { $events[] = array( $name, $data ); },
        ) );
        return $deploy;
    }

    /** RD-01 */
    public function testRemotes()
    {
        $good = new expMigrationRemote( 'production1', array( 'Transport' => 'rsync', 'Host' => 'node1.example.com', 'User' => 'deploy', 'Path' => '/var/www/site',
                                                              'Credential' => 'sshkey:deploy_production1', 'Groups' => array( 'production' ), 'Environment' => 'production' ) );
        $this->assertSame( array(), $good->validate() );
        $this->assertSame( 22, $good->port() );
        $this->assertSame( 'rsync://deploy@node1.example.com:22/var/www/site', $good->address() );
        $this->assertTrue( $good->canRun() );

        $secret = new expMigrationRemote( 'x', array( 'Host' => 'h', 'Path' => '/srv/a', 'Password' => 'hunter2' ) );
        $this->assertStringContainsString( 'never goes in an INI file', implode( "\n", $secret->validate() ) );
        $ftp = new expMigrationRemote( 'f', array( 'Transport' => 'ftp', 'Host' => 'h', 'Path' => '/site', 'Credential' => 'sshkey:k' ) );
        $this->assertStringContainsString( 'password:', implode( "\n", $ftp->validate() ) );
        $this->assertSame( 21, $ftp->port() );
        $this->assertFalse( $ftp->canRun() );
        foreach ( array( array( 'Transport' => 'telnet' ), array( 'Path' => 'relative' ), array( 'Path' => '/a/../b' ), array( 'Path' => '/a b' ),
                         array( 'Host' => '-oProxyCommand=x' ), array( 'Php' => 'php; rm' ), array( 'Credential' => 'token:x' ),
                         array( 'HostKeyFingerprint' => 'MD5:aa' ) ) as $bad )
        {
            $remote = new expMigrationRemote( 'b', $bad + array( 'Host' => 'h', 'Path' => '/srv/site', 'Credential' => 'none' ) );
            $this->assertNotSame( array(), $remote->validate(), json_encode( $bad ) );
        }
        $this->assertNotSame( array(), ( new expMigrationRemote( 'all', array( 'Transport' => 'local', 'Path' => '/srv/a' ) ) )->validate() );

        $remotes = array( 'alpha' => new expMigrationRemote( 'alpha', array( 'Groups' => array( 'test' ) ) ), 'stage' => new expMigrationRemote( 'stage', array( 'Groups' => array( 'test' ) ) ),
                          'production1' => $good, 'production2' => new expMigrationRemote( 'production2', array( 'Groups' => array( 'production' ) ) ) );
        $errors = array();
        $this->assertSame( array( 'production1', 'production2' ), array_keys( expMigrationRemote::select( 'production', $remotes, $errors ) ) );
        $this->assertSame( array( 'alpha', 'production2' ), array_keys( expMigrationRemote::select( array( 'production2,alpha' ), $remotes, $errors ) ) );
        $this->assertSame( array_keys( $remotes ), array_keys( expMigrationRemote::select( 'all', $remotes, $errors ) ) );
        $this->assertSame( array(), $errors );
        expMigrationRemote::select( 'nowhere', $remotes, $errors );
        $this->assertCount( 1, $errors );
    }

    /** RD-02 */
    public function testCredentials()
    {
        $this->assertSame( array( 'none', '' ), expMigrationRemoteCredential::parse( '' ) );
        $this->assertSame( array( 'sshkey', 'deploy' ), expMigrationRemoteCredential::parse( 'sshkey:deploy' ) );
        $this->assertSame( array( 'password', 'env:FTP_PASS' ), expMigrationRemoteCredential::parse( 'password:env:FTP_PASS' ) );
        $this->assertSame( '', expMigrationRemoteCredential::parse( 'token:x' )[0] );

        $key = "-----BEGIN OPENSSH PRIVATE KEY-----\nAAAAsecretkeymaterial\n-----END OPENSSH PRIVATE KEY-----";
        $lookup = function ( $name ) use ( $key ) { return $name === 'deploy' ? $key : ( $name === 'pw' ? 'S3cret-pass' : null ); };
        $missing = new expMigrationRemoteCredential( 'sshkey:nothere', array( 'work_dir' => $this->dir . '/work', 'lookup' => $lookup ) );
        $this->assertStringContainsString( 'not set', $missing->problem() );
        $notKey = new expMigrationRemoteCredential( 'sshkey:pw', array( 'work_dir' => $this->dir . '/work', 'lookup' => $lookup ) );
        $this->assertStringContainsString( 'no private key', $notKey->problem() );

        $credential = new expMigrationRemoteCredential( 'sshkey:deploy', array( 'work_dir' => $this->dir . '/work', 'lookup' => $lookup ) );
        $this->assertSame( '', $credential->problem() );
        $ssh = $credential->ssh( $this->dir . '/work/kh' );
        $file = $ssh['options'][array_search( '-i', $ssh['options'], true ) + 1];
        $this->assertSame( 0600, fileperms( $file ) & 0777 );
        $this->assertSame( $key . "\n", file_get_contents( $file ) );
        $this->assertStringNotContainsString( 'secretkeymaterial', implode( ' ', $ssh['options'] ) );
        $this->assertContains( 'StrictHostKeyChecking=accept-new', $ssh['options'] );
        $this->assertSame( 'text [secret sshkey:deploy] end', $credential->redact( "text $key end" ) );
        $credential->cleanup();
        $this->assertFileDoesNotExist( $file );

        putenv( 'EXP_RD_TEST_PASS=env-pass-123' );
        $password = new expMigrationRemoteCredential( 'password:env:EXP_RD_TEST_PASS', array( 'work_dir' => $this->dir . '/work', 'root' => '/srv/root' ) );
        $this->assertSame( '', $password->problem() );
        $ssh = $password->ssh( $this->dir . '/work/kh', true );
        $this->assertSame( 'env-pass-123', $ssh['env']['EXP_STATIC_GIT_TOKEN'] );
        $this->assertSame( 'force', $ssh['env']['SSH_ASKPASS_REQUIRE'] );
        $this->assertSame( '/srv/root/bin/shell/expstaticaskpass.sh', $ssh['env']['SSH_ASKPASS'] );
        $this->assertContains( 'StrictHostKeyChecking=yes', $ssh['options'] );
        $this->assertStringNotContainsString( 'env-pass-123', implode( ' ', $ssh['options'] ) );
        $netrc = $password->netrc( 'ftp.example.com', 'web' );
        $this->assertSame( "machine ftp.example.com login web password env-pass-123\n", file_get_contents( $netrc ) );
        $this->assertSame( 'login failed for [secret password:env:EXP_RD_TEST_PASS]', $password->redact( 'login failed for env-pass-123' ) );
        $password->cleanup();
        $this->assertFileDoesNotExist( $netrc );
        putenv( 'EXP_RD_TEST_PASS' );
    }

    /** RD-03 */
    public function testTransports()
    {
        $lookup = function () { return 'Pa55word!'; };
        $files = array( 'migrations/20261102100000_a.yml' => '/src/migrations/20261102100000_a.yml',
                        'extension/news/migrations/20261102110000_b.yml' => '/src/extension/news/migrations/20261102110000_b.yml' );
        $make = function ( $transport, $credential = 'none' ) use ( $lookup ) {
            $remote = new expMigrationRemote( 'r', array( 'Transport' => $transport, 'Host' => 'node1', 'User' => 'deploy', 'Path' => '/srv/site', 'Port' => 2222,
                                                          'Credential' => $credential, 'Php' => '/usr/bin/php8.3' ) );
            $credential = new expMigrationRemoteCredential( $remote->credential(), array( 'work_dir' => $this->dir . '/work', 'lookup' => $lookup ) );
            $credential->problem();
            return new expMigrationRemoteTransport( $remote, $credential, $this->dir . '/work/known_hosts/r' );
        };

        $run = $make( 'ssh' )->run( array( 'bin/php/expmigrationstatus.php', '--format=json', "it's" ) );
        $this->assertSame( 'ssh', $run['argv'][0] );
        $this->assertSame( array( '--', 'deploy@node1', "cd '/srv/site' && '/usr/bin/php8.3' 'bin/php/expmigrationstatus.php' '--format=json' 'it'\\''s'" ),
                           array_slice( $run['argv'], -3 ) );
        $this->assertContains( '2222', $run['argv'] );

        $ops = $make( 'ssh' )->send( $files, '/src' );
        $this->assertCount( 3, $ops );
        $this->assertStringStartsWith( 'mkdir -p ', end( $ops[0]['argv'] ) );
        $this->assertSame( 'scp', $ops[1]['argv'][0] );
        $this->assertSame( 'deploy@node1:/srv/site/extension/news/migrations/', end( $ops[1]['argv'] ) );
        $this->assertContains( '-P', $ops[1]['argv'] );

        $ops = $make( 'sftp' )->send( $files, '/src' );
        $this->assertCount( 1, $ops );
        $this->assertStringContainsString( "-mkdir /srv/site/extension/news/migrations\n", $ops[0]['stdin'] );
        $this->assertStringContainsString( 'put -p /src/migrations/20261102100000_a.yml /srv/site/migrations/20261102100000_a.yml', $ops[0]['stdin'] );

        $ops = $make( 'rsync' )->send( $files, '/src' );
        $this->assertSame( array( 'rsync', '-rt', '--checksum', '--partial', '--files-from=-' ), array_slice( $ops[0]['argv'], 0, 5 ) );
        $this->assertSame( array( '/src/', 'deploy@node1:/srv/site/' ), array_slice( $ops[0]['argv'], -2 ) );
        $this->assertSame( "extension/news/migrations/20261102110000_b.yml\nmigrations/20261102100000_a.yml\n", $ops[0]['stdin'] );

        $ftp = $make( 'ftps', 'password:pw' );
        $this->assertNull( $ftp->run( array( 'bin/php/expmigrationstatus.php' ) ) );
        $ops = $ftp->send( $files, '/src' );
        $this->assertCount( 2, $ops );
        $this->assertContains( '--ssl-reqd', $ops[0]['argv'] );
        $this->assertContains( '--ftp-create-dirs', $ops[0]['argv'] );
        $this->assertSame( 'ftp://node1:2222/srv/site/extension/news/migrations/20261102110000_b.yml', end( $ops[0]['argv'] ) );
        foreach ( $ops as $op )
            $this->assertStringNotContainsString( 'Pa55word', implode( ' ', $op['argv'] ) );

        $local = new expMigrationRemoteTransport( new expMigrationRemote( 'l', array( 'Transport' => 'local', 'Path' => '/srv/b', 'AllowRootUser' => 'enabled' ) ),
                                                  new expMigrationRemoteCredential( 'none' ), '' );
        $this->assertSame( array( 'php', 'bin/php/x.php', '--allow-root-user' ), $local->run( array( 'bin/php/x.php' ) )['argv'] );
        $this->assertSame( '/srv/b', $local->run( array( 'bin/php/x.php' ) )['cwd'] );
        $this->assertSame( array( 'copy' => array( '/src/migrations/20261102100000_a.yml' => '/srv/b/migrations/20261102100000_a.yml' ) ),
                           $local->send( array_slice( $files, 0, 1 ), '/src' )[0] );

        foreach ( array( 'migrations/a.yml' => true, 'extension/x/migrations/a.yml' => true, '/abs.yml' => false, '../a.yml' => false, 'a/../b' => false,
                         'a b.yml' => false, "a\nb" => false, 'a;b' => false, '' => false ) as $path => $safe )
            $this->assertSame( $safe, expMigrationRemoteTransport::safePath( $path ), $path );

        $blob = 'AAAAC3NzaC1lZDI1NTE5AAAAIAbcdefghijklmnopqrstuvwxyz0123456789ABCDEF';
        $fingerprint = 'SHA256:' . rtrim( base64_encode( hash( 'sha256', base64_decode( $blob ), true ) ), '=' );
        $scan = "# node1 SSH-2.0\n[node1]:2222 ssh-ed25519 $blob\n[node1]:2222 ssh-rsa AAAAB3NzaC1yc2EAAAADAQAB\n";
        $this->assertSame( array( "[node1]:2222 ssh-ed25519 $blob" ), expMigrationRemoteTransport::matchingKeys( $scan, $fingerprint ) );
        $this->assertSame( array(), expMigrationRemoteTransport::matchingKeys( $scan, 'SHA256:' . str_repeat( 'A', 43 ) ) );
    }

    /** RD-04 */
    public function testPlan()
    {
        $local = array(
            array( 'id' => '20261101000000_same', 'source' => 'site', 'files' => array( '*' => 'migrations/20261101000000_same.yml' ),
                   'paths' => array( 'migrations/20261101000000_same.yml' => '/x' ), 'checksums' => array( '*' => 'aaa' ) ),
            array( 'id' => '20261101000001_new', 'source' => 'site', 'files' => array( '*' => 'migrations/20261101000001_new.yml' ),
                   'paths' => array( 'migrations/20261101000001_new.yml' => '/y' ), 'checksums' => array( '*' => 'bbb' ) ),
            array( 'id' => '20261101000002_pendingchanged', 'source' => 'extension:news', 'files' => array( '*' => 'extension/news/migrations/20261101000002_pendingchanged.yml' ),
                   'paths' => array( 'extension/news/migrations/20261101000002_pendingchanged.yml' => '/z' ), 'checksums' => array( '*' => 'ccc' ) ),
        );
        $status = array( 'migrations' => array(
            array( 'id' => '20261101000000_same', 'source' => 'site', 'file' => 'migrations/20261101000000_same.yml', 'checksum' => 'aaa', 'state' => 'done' ),
            array( 'id' => '20261101000002_pendingchanged', 'source' => 'extension:news', 'file' => 'extension/news/migrations/20261101000002_pendingchanged.yml',
                   'checksum' => 'old', 'state' => 'pending' ),
            array( 'id' => '20261031000000_onlythere', 'source' => 'site', 'file' => 'migrations/20261031000000_onlythere.yml', 'checksum' => 'q', 'state' => 'done' ),
            array( 'id' => '6.0.15-x', 'source' => 'kernel', 'file' => '', 'checksum' => '', 'state' => 'done' ),
        ) );
        $plan = expMigrationRemoteDeploy::plan( $local, $status );
        $this->assertSame( array( 'extension/news/migrations/20261101000002_pendingchanged.yml', 'migrations/20261101000001_new.yml' ), $plan['send'] );
        $this->assertSame( array( '20261101000001_new', '20261101000002_pendingchanged' ), $plan['pending'] );
        $this->assertSame( array(), $plan['problems'] );
        $this->assertSame( array( '20261031000000_onlythere' ), $plan['ahead'] );

        $status['migrations'][0]['checksum'] = 'changed';
        $this->assertStringContainsString( 'differs', implode( "\n", expMigrationRemoteDeploy::plan( $local, $status )['problems'] ) );

        $this->assertSame( array(), expMigrationRemoteDeploy::statusProblems( array( 'table' => true, 'lock' => null, 'version' => '6.0.15', 'migrations' => array() ), '6.0.15' ) );
        $this->assertCount( 3, expMigrationRemoteDeploy::statusProblems( array( 'table' => false, 'lock' => array( 'pid' => 3 ), 'version' => '6.0.14',
                                                                               'migrations' => array() ), '6.0.15' ) );
        $this->assertTrue( expMigrationRemoteDeploy::statesInSync( array( 'a' => array( 's 1' => 'done', 's 2' => 'not-needed' ), 'b' => array( 's 1' => 'done', 's 2' => 'done' ) ) ) );
        $this->assertFalse( expMigrationRemoteDeploy::statesInSync( array( 'a' => array( 's 1' => 'done' ), 'b' => array( 's 1' => 'pending' ) ) ) );
        $this->assertFalse( expMigrationRemoteDeploy::statesInSync( array( 'a' => array( 's 1' => 'done' ), 'b' => array() ) ) );
        $this->assertFalse( expMigrationRemoteDeploy::statesInSync( array( 'a' => array( 's 1' => 'failed' ) ) ) );
        $this->assertSame( array( 'dry run: x', 'captions are off there ([CaptionSettings]): without the caption before the run it could not be taken back, so it is not switched' ),
                           expMigrationRemoteDeploy::verifyProblems( array( 'exit' => 1, 'errors' => array( 'x' ), 'captions' => array( 'pre' => 'off' ),
                                                                            'migrations' => array( array( 'id' => 'm', 'action' => 'would-apply' ) ) ), array(), array() ) );
        // nothing to apply there: no caption is taken, and none is needed
        $this->assertSame( array(), expMigrationRemoteDeploy::verifyProblems( array( 'exit' => 0, 'captions' => array( 'pre' => 'off' ), 'migrations' => array() ), array(), array() ) );
    }

    /** RD-05 */
    public function testDeployInSync()
    {
        $this->migration( '20261102100000_first' );
        $this->migration( '20261102100001_second', "steps:\n  - { type: cache }\n" );
        $remotes = array( $this->destination( 'dev' ), $this->destination( 'stage' ), $this->destination( 'production' ) );
        // stage already has the first migration, applied from the same file: it is not sent again
        copy( $this->dir . '/source/migrations/20261102100000_first.yml', $this->dir . '/stage/migrations/20261102100000_first.yml' );
        file_put_contents( $this->dir . '/stage/var/fake-state.json', json_encode( array( 'done' => array( '20261102100000_first' => 'x' ), 'captions' => array() ) ) );

        $dry = $this->deploy( $remotes )->run();
        $this->assertSame( 'planned', $dry['state'], json_encode( $dry['errors'] ) );
        $this->assertSame( 0, $dry['exit'] );
        $this->assertSame( array( 'migrations/20261102100000_first.yml', 'migrations/20261102100001_second.yml' ), $dry['remotes']['dev']['send'] );
        $this->assertSame( array( 'migrations/20261102100001_second.yml' ), $dry['remotes']['stage']['send'] );
        $this->assertFileDoesNotExist( $this->dir . '/dev/migrations/20261102100001_second.yml', 'a dry run sends nothing' );
        $this->assertSame( array( 'prepare', 'preflight' ), array_keys( $dry['phases'] ) );
        $this->assertDirectoryDoesNotExist( $this->dir . '/work/reports', 'a dry run keeps no report' );

        $deploy = $this->deploy( $remotes, array( 'apply' => true ) );
        $report = $deploy->run();
        $this->assertSame( 'deployed', $report['state'], json_encode( $report['errors'] ) );
        $this->assertTrue( $report['in_sync'] );
        $this->assertSame( array( 'prepare', 'preflight', 'transfer', 'verify', 'switch' ), array_keys( $report['phases'] ) );
        foreach ( array( 'dev', 'stage', 'production' ) as $name )
        {
            $this->assertSame( 'applied', $report['remotes'][$name]['state'] );
            $this->assertSame( array( '20261102100000_first', '20261102100001_second' ), array_keys( $this->done( $name ) ) );
            $this->assertSame( 'caption-' . $deploy->deployId(), $report['remotes'][$name]['caption'], 'one run id on every remote' );
        }
        $this->assertSame( array( '20261102100001_second' ), $report['remotes']['stage']['applied'] );
        $this->assertFileExists( $this->dir . '/work/reports/' . $deploy->deployId() . '.json' );
        $this->assertSame( 'system.migration.deploy', end( $this->events )[0] );
        $this->assertSame( 'success', end( $this->events )[1]['result'] );
        $history = expMigrationRemoteDeploy::history( 5, $this->dir . '/work' );
        $this->assertSame( $deploy->deployId(), $history[0]['deploy_id'] );

        $status = $this->deploy( $remotes )->status();
        $this->assertTrue( $status['in_sync'] );
        $this->assertSame( 2, $status['remotes']['production']['migrations']['done'] );
        $this->assertSame( 0, $status['exit'] );

        // a second deploy finds nothing to send or apply
        $again = $this->deploy( $remotes, array( 'apply' => true ) )->run();
        $this->assertSame( 'deployed', $again['state'] );
        $this->assertSame( array(), $again['remotes']['dev']['send'] );
        $this->assertSame( array(), $again['remotes']['dev']['applied'] );
    }

    /** RD-06 */
    public function testOneFailureTakesAllBack()
    {
        $this->migration( '20261102100000_ok' );
        $remotes = array( $this->destination( 'dev' ), $this->destination( 'stage' ), $this->destination( 'production' ) );
        $this->assertSame( 'deployed', $this->deploy( $remotes, array( 'apply' => true ) )->run()['state'] );

        // production has a migration of its own that fails there (the file of the same id differs nowhere: it is only there)
        $this->migration( '20261102110000_next' );
        $this->migration( '20261102120000_more' );
        file_put_contents( $this->dir . '/production/migrations/20261102115000_breaks.yml', "FAIL\n" );
        $report = $this->deploy( $remotes, array( 'apply' => true ) )->run();
        $this->assertSame( 'rolled-back', $report['state'], json_encode( $report ) );
        $this->assertTrue( $report['in_sync'] );
        $this->assertSame( 1, $report['exit'] );
        $this->assertSame( 'rolled-back', $report['remotes']['dev']['state'] );
        $this->assertSame( 'rolled-back', $report['remotes']['stage']['state'] );
        $this->assertSame( 'restored', $report['remotes']['production']['state'] );
        foreach ( array( 'dev', 'stage', 'production' ) as $name )
            $this->assertSame( array( '20261102100000_ok' ), array_keys( $this->done( $name ) ), "$name is back where it was" );
        $names = array_column( $this->events, 0 );
        $this->assertContains( 'system.migration.deploy.rollback', $names );
        $this->assertSame( 'failure', end( $this->events )[1]['result'] );
    }

    /** RD-07 */
    public function testOutOfSync()
    {
        $this->migration( '20261102100000_ok' );
        $remotes = array( $this->destination( 'dev' ), $this->destination( 'production' ) );
        file_put_contents( $this->dir . '/production/migrations/20261102090000_breaks.yml', "FAIL\n" );
        touch( $this->dir . '/dev/var/fake-rollback-fails' );
        $report = $this->deploy( $remotes, array( 'apply' => true ) )->run();
        $this->assertSame( 'out-of-sync', $report['state'] );
        $this->assertFalse( $report['in_sync'] );
        $this->assertSame( 'out-of-sync', $report['remotes']['dev']['state'] );
        $this->assertSame( 'restored', $report['remotes']['production']['state'] );
        $this->assertStringContainsString( 'OUT OF SYNC', implode( "\n", $report['errors'] ) );
    }

    /** RD-08 */
    public function testPreflightStopsEverything()
    {
        $this->migration( '20261102100000_ok' );
        $remotes = array( $this->destination( 'dev' ), $this->destination( 'production' ) );
        touch( $this->dir . '/production/var/fake-lock' );
        $report = $this->deploy( $remotes, array( 'apply' => true ) )->run();
        $this->assertSame( 'refused', $report['state'] );
        $this->assertStringContainsString( 'holds the lock', implode( "\n", $report['errors'] ) );
        $this->assertFileDoesNotExist( $this->dir . '/dev/migrations/20261102100000_ok.yml', 'nothing was sent anywhere' );
        unlink( $this->dir . '/production/var/fake-lock' );

        $report = $this->deploy( $remotes, array( 'apply' => true, 'version' => '6.0.16' ) )->run();
        $this->assertSame( 'refused', $report['state'] );
        $this->assertStringContainsString( 'kernel version', implode( "\n", $report['errors'] ) );

        touch( $this->dir . '/dev/var/fake-captions-off' );
        $report = $this->deploy( $remotes, array( 'apply' => true ) )->run();
        $this->assertSame( 'failed', $report['state'] );
        $this->assertStringContainsString( 'captions are off', implode( "\n", $report['errors'] ) );
        $this->assertArrayNotHasKey( 'switch', $report['phases'] );
        $this->assertSame( array(), $this->done( 'production' ), 'no remote was switched' );
        unlink( $this->dir . '/dev/var/fake-captions-off' );

        $secretless = new expMigrationRemote( 'nokey', array( 'Transport' => 'ssh', 'Host' => 'node9', 'Path' => '/srv/site', 'Credential' => 'sshkey:missing' ) );
        $report = $this->deploy( array( $remotes[0], $secretless ), array( 'apply' => true, 'lookup' => function () { return null; } ) )->run();
        $this->assertSame( 'refused', $report['state'] );
        $this->assertSame( array( 'prepare' ), array_keys( $report['phases'] ) );
        $this->assertSame( 'refused', $report['remotes']['nokey']['state'] );

        $ftp = new expMigrationRemote( 'ftp1', array( 'Transport' => 'ftp', 'Host' => 'h', 'Path' => '/site', 'Credential' => 'password:p' ) );
        $report = $this->deploy( array( $ftp ), array( 'apply' => true, 'lookup' => function () { return 'pw'; } ) )->run();
        $this->assertStringContainsString( 'cannot run the migrations', implode( "\n", $report['errors'] ) );
    }

    /** RD-09 */
    public function testRetriesStatusAndParallel()
    {
        $this->migration( '20261102100000_ok' );
        $remotes = array( $this->destination( 'dev' ), $this->destination( 'stage' ) );
        $calls = array();
        $failed = false;
        expMigrationRemoteProcess::setRunner( function ( array $jobs, $parallel, $timeout ) use ( &$calls, &$failed ) {
            $calls[] = array_keys( $jobs );
            expMigrationRemoteProcess::setRunner( null );
            $results = expMigrationRemoteProcess::runAll( $jobs, $parallel, $timeout );
            expMigrationRemoteProcess::setRunner( $GLOBALS['expRdRunner'] );
            if ( !$failed && isset( $results['stage'] ) )
            {
                $failed = true;
                $results['stage'] = array( 'exit' => 255, 'stdout' => '', 'stderr' => 'Connection reset by peer', 'duration_ms' => 1, 'done' => 0 );
            }
            return $results;
        } );
        $GLOBALS['expRdRunner'] = function ( array $jobs, $parallel, $timeout ) use ( &$calls ) {
            $calls[] = array_keys( $jobs );
            expMigrationRemoteProcess::setRunner( null );
            $results = expMigrationRemoteProcess::runAll( $jobs, $parallel, $timeout );
            expMigrationRemoteProcess::setRunner( $GLOBALS['expRdRunner'] );
            return $results;
        };
        $report = $this->deploy( $remotes, array( 'apply' => true, 'retries' => 2 ) )->run();
        $this->assertSame( 'deployed', $report['state'], json_encode( $report['errors'] ) );
        $this->assertSame( 2, $report['remotes']['stage']['attempts']['preflight'] );
        $this->assertStringContainsString( 'connection failure', $report['remotes']['stage']['fixes'][0] );
        $this->assertSame( array( 'stage' ), $calls[1], 'only the failed remote is tried again' );
        expMigrationRemoteProcess::setRunner( null );
        unset( $GLOBALS['expRdRunner'] );

        $this->assertTrue( expMigrationRemoteDeploy::connectionFailure( array( 'exit' => 255, 'stderr' => 'kex_exchange_identification' ) ) );
        $this->assertFalse( expMigrationRemoteDeploy::connectionFailure( array( 'exit' => 255, 'stderr' => 'Permission denied (publickey)' ) ) );
        $this->assertFalse( expMigrationRemoteDeploy::connectionFailure( array( 'exit' => 1, 'stderr' => '' ) ) );

        // the remotes run at the same time
        $sleep = array( 'argv' => array( PHP_BINARY, '-n', '-d', 'display_errors=0', '-f', __DIR__ . '/fixtures/remotedeploy/sleepabit.php' ), 'env' => array(), 'cwd' => null, 'stdin' => null );
        $started = microtime( true );
        $results = expMigrationRemoteProcess::runAll( array( 'a' => array( $sleep ), 'b' => array( $sleep ), 'c' => array( $sleep ) ), 3, 30 );
        $this->assertLessThan( 1.5, microtime( true ) - $started, 'three 0.6 s processes in parallel' );
        $this->assertSame( array( 0, 0, 0 ), array_column( $results, 'exit' ) );
        $this->assertSame( "slept\n", $results['b']['stdout'] );
        $results = expMigrationRemoteProcess::runAll( array( 'slow' => array( $sleep ) ), 1, 0 );
        $this->assertSame( -1, $results['slow']['exit'], 'a remote over its time is stopped' );
    }
}
