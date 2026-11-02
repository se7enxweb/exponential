<?php
/**
 * Remote deploy of migrations over real transports: ssh with scp, sftp and rsync against a throwaway sshd, FTP against
 * a throwaway FTP server, each destination a folder whose migration console scripts are the stand-in
 * fixtures/remotedeploy/fakemigrationcli.php. Skipped unless the servers are given:
 *
 *   EXP_RD_SSH_HOST, EXP_RD_SSH_PORT, EXP_RD_SSH_USER   the sshd (a login with a shell, by key)
 *   EXP_RD_SSH_KEY                                       the client's private key (the remote's Credential=sshkey:env:EXP_RD_SSH_KEY)
 *   EXP_RD_SSH_FINGERPRINT                               the sshd's host key fingerprint (HostKeyFingerprint of one remote)
 *   EXP_RD_SSH_PHP                                       the PHP CLI on the sshd's side (default this PHP)
 *   EXP_RD_DIR                                           a folder the sshd sees at the same path (the destinations)
 *   EXP_RD_FTP_PORT, EXP_RD_FTP_USER, EXP_RD_FTP_PASS, EXP_RD_FTP_ROOT   the FTP server on 127.0.0.1 and its folder
 *
 *  RI-01 — three remotes (ssh/scp, sftp, rsync) deployed at once, the files there, in sync; a second deploy sends nothing
 *  RI-02 — one remote fails: the two that applied are taken back over ssh, all three end where they were
 *  RI-03 — FTP: the files sent (--transfer-only) and read back unchanged; a wrong password is refused, nothing sent
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expMigrationRemoteDeployIntegrationTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $dir;

    /** @var string */
    private $source;

    protected function setUp(): void
    {
        foreach ( array( 'EXP_RD_SSH_HOST', 'EXP_RD_SSH_PORT', 'EXP_RD_SSH_USER', 'EXP_RD_SSH_KEY', 'EXP_RD_DIR' ) as $name )
            if ( (string)getenv( $name ) === '' )
                $this->markTestSkipped( "$name is not set: no throwaway sshd to deploy to" );
        $this->dir = rtrim( getenv( 'EXP_RD_DIR' ), '/' ) . '/run-' . getmypid() . '-' . mt_rand();
        $this->source = $this->dir . '/source';
        mkdir( $this->source . '/migrations', 0777, true );
        expMigrationRemoteProcess::setRunner( null );
    }

    protected function tearDown(): void
    {
        if ( $this->dir !== null )
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

    /** A destination folder the sshd's user owns, with the stand-in console scripts. */
    private function destination( $name, $transport, $fingerprint = '' )
    {
        $path = $this->dir . '/' . $name;
        foreach ( array( 'bin/php', 'migrations', 'var' ) as $sub )
            mkdir( "$path/$sub", 0777, true );
        foreach ( array( 'expmigrationstatus', 'expmigrationmigrate', 'expmigrationrollback' ) as $script )
            copy( __DIR__ . '/fixtures/remotedeploy/fakemigrationcli.php', "$path/bin/php/$script.php" );
        // the sshd's user (another uid, in a container) writes there
        foreach ( array( $this->dir, $path, "$path/bin", "$path/bin/php", "$path/migrations", "$path/var" ) as $folder )
            chmod( $folder, 0777 );
        $settings = array( 'Transport' => $transport, 'Host' => getenv( 'EXP_RD_SSH_HOST' ), 'Port' => getenv( 'EXP_RD_SSH_PORT' ), 'User' => getenv( 'EXP_RD_SSH_USER' ),
                           'Path' => $path, 'Php' => getenv( 'EXP_RD_SSH_PHP' ) ?: PHP_BINARY, 'Credential' => 'sshkey:env:EXP_RD_SSH_KEY', 'Environment' => $name );
        if ( $fingerprint !== '' )
            $settings['HostKeyFingerprint'] = $fingerprint;
        return new expMigrationRemote( $name, $settings );
    }

    private function local()
    {
        $local = array();
        foreach ( glob( $this->source . '/migrations/*.yml' ) as $file )
        {
            $rel = 'migrations/' . basename( $file );
            $local[] = array( 'id' => basename( $file, '.yml' ), 'source' => 'site', 'files' => array( '*' => $rel ), 'paths' => array( $rel => $file ),
                              'checksums' => array( '*' => expMigrationDefinition::checksumOf( file_get_contents( $file ) ) ) );
        }
        return $local;
    }

    private function deploy( array $remotes, array $options = array() )
    {
        $list = array();
        foreach ( $remotes as $remote )
            $list[$remote->id()] = $remote;
        return new expMigrationRemoteDeploy( $list, $options + array( 'root' => $this->source, 'work_dir' => $this->dir . '/work', 'local' => $this->local(),
                                                                      'version' => '6.0.15', 'retries' => 1, 'timeout' => 60, 'audit' => function () {} ) );
    }

    private function done( $name )
    {
        $file = $this->dir . "/$name/var/fake-state.json";
        return is_file( $file ) ? array_keys( json_decode( file_get_contents( $file ), true )['done'] ) : array();
    }

    /** RI-01, RI-02 */
    public function testSshScpSftpRsyncInSyncAndAllTakenBack()
    {
        file_put_contents( $this->source . '/migrations/20261102100000_first.yml', "steps: []\n" );
        file_put_contents( $this->source . '/migrations/20261102100001_second.yml', "steps:\n  - { type: cache }\n" );
        $remotes = array( $this->destination( 'viassh', 'ssh', (string)getenv( 'EXP_RD_SSH_FINGERPRINT' ) ), $this->destination( 'viasftp', 'sftp' ),
                          $this->destination( 'viarsync', 'rsync' ) );

        $deploy = $this->deploy( $remotes, array( 'apply' => true ) );
        $report = $deploy->run();
        $this->assertSame( 'deployed', $report['state'], json_encode( $report, JSON_PRETTY_PRINT ) );
        $this->assertTrue( $report['in_sync'] );
        foreach ( array( 'viassh', 'viasftp', 'viarsync' ) as $name )
        {
            $this->assertFileEquals( $this->source . '/migrations/20261102100001_second.yml', $this->dir . "/$name/migrations/20261102100001_second.yml", $name );
            $this->assertSame( array( '20261102100000_first', '20261102100001_second' ), $this->done( $name ), $name );
            $this->assertSame( 'caption-' . $deploy->deployId(), $report['remotes'][$name]['caption'] );
        }
        $this->assertSame( array(), glob( $this->dir . '/work/.keys/*' ) ?: array(), 'no key file is left' );
        if ( getenv( 'EXP_RD_SSH_FINGERPRINT' ) )
            $this->assertFileExists( $this->dir . '/work/known_hosts/viassh' );

        $status = $this->deploy( $remotes )->status();
        $this->assertTrue( $status['in_sync'], json_encode( $status, JSON_PRETTY_PRINT ) );
        $again = $this->deploy( $remotes, array( 'apply' => true ) )->run();
        $this->assertSame( 'deployed', $again['state'] );
        $this->assertSame( array(), $again['remotes']['viarsync']['send'] );

        // RI-02: viarsync has a migration of its own that fails there
        file_put_contents( $this->source . '/migrations/20261102110000_third.yml', "steps: []\n" );
        file_put_contents( $this->dir . '/viarsync/migrations/20261102120000_breaks.yml', "FAIL\n" );
        file_put_contents( $this->source . '/migrations/20261102130000_fourth.yml', "steps: []\n" );
        $report = $this->deploy( $remotes, array( 'apply' => true ) )->run();
        $this->assertSame( 'rolled-back', $report['state'], json_encode( $report, JSON_PRETTY_PRINT ) );
        $this->assertTrue( $report['in_sync'] );
        $this->assertSame( 'rolled-back', $report['remotes']['viassh']['state'] );
        $this->assertSame( 'rolled-back', $report['remotes']['viasftp']['state'] );
        $this->assertSame( 'restored', $report['remotes']['viarsync']['state'] );
        foreach ( array( 'viassh', 'viasftp', 'viarsync' ) as $name )
            $this->assertSame( array( '20261102100000_first', '20261102100001_second' ), $this->done( $name ), "$name is back where it was" );
    }

    /** RI-03 */
    public function testFtpTransferOnly()
    {
        if ( (string)getenv( 'EXP_RD_FTP_PORT' ) === '' )
            $this->markTestSkipped( 'EXP_RD_FTP_PORT is not set: no throwaway FTP server' );
        file_put_contents( $this->source . '/migrations/20261102100000_first.yml', "steps: []\n" );
        $folder = 'site-' . getmypid() . '-' . mt_rand();
        $remote = new expMigrationRemote( 'viaftp', array( 'Transport' => 'ftp', 'Host' => '127.0.0.1', 'Port' => getenv( 'EXP_RD_FTP_PORT' ),
                                                          'User' => getenv( 'EXP_RD_FTP_USER' ), 'Path' => '/' . $folder, 'Credential' => 'password:env:EXP_RD_FTP_PASS' ) );
        $report = $this->deploy( array( $remote ), array( 'apply' => true, 'transfer_only' => true ) )->run();
        $this->assertSame( 'transferred', $report['state'], json_encode( $report, JSON_PRETTY_PRINT ) );
        $this->assertSame( 'verified', $report['remotes']['viaftp']['state'] );
        $arrived = rtrim( getenv( 'EXP_RD_FTP_ROOT' ), '/' ) . "/$folder/migrations/20261102100000_first.yml";
        $this->assertFileEquals( $this->source . '/migrations/20261102100000_first.yml', $arrived );
        $this->assertStringNotContainsString( (string)getenv( 'EXP_RD_FTP_PASS' ), json_encode( $report ) );

        putenv( 'EXP_RD_FTP_WRONG=not-the-password' );
        $wrong = new expMigrationRemote( 'wrongftp', array( 'Transport' => 'ftp', 'Host' => '127.0.0.1', 'Port' => getenv( 'EXP_RD_FTP_PORT' ),
                                                           'User' => getenv( 'EXP_RD_FTP_USER' ), 'Path' => '/' . $folder . '-wrong', 'Credential' => 'password:env:EXP_RD_FTP_WRONG' ) );
        $report = $this->deploy( array( $wrong ), array( 'apply' => true, 'transfer_only' => true ) )->run();
        $this->assertSame( 'failed', $report['state'] );
        $this->assertStringNotContainsString( 'not-the-password', json_encode( $report ) );
        $this->assertFileDoesNotExist( rtrim( getenv( 'EXP_RD_FTP_ROOT' ), '/' ) . "/$folder-wrong/migrations/20261102100000_first.yml" );
        putenv( 'EXP_RD_FTP_WRONG' );
    }
}
