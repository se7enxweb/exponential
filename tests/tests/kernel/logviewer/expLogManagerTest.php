<?php
/**
 * expLogManager, log management on the log viewer's allow-list (doc/features/6.0/log-rotation.md), in a throwaway
 * installation root (no database, no real log):
 *   - logs() lists the current logs with their archives and sizes, never an archive or a configured (web server) log
 *   - select() resolves keys and ids only among the allowed files: a path outside the log directories, "..", an
 *     archive, a symbolic link out of the directories and a web server log are unknown and never touched
 *   - rotate() rotates by key, id or all (an empty log is left), keeping the number of archives
 *   - prune() removes the archives beyond the limits (number, age, bare .gz), and a dry run removes nothing
 *   - the exp:logs command: --rotate names, --rotate-all, --prune and --dry-run, with the exit codes
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group logviewer
 */

require_once __DIR__ . '/expLogViewerTestFixtures.php';
require_once __DIR__ . '/../../../../kernel/private/classes/commands/explogs.php';

class expLogManagerTestTerminal
{
    public $out = array();
    public $err = array();
    public function output( $text = '' )
    {
        $this->out[] = $text;
    }
    public function error( $text = '' )
    {
        $this->err[] = $text;
    }
    public function __call( $name, $args )
    {
        return null;
    }
}

class expLogManagerTestCommand extends \Exponential\Command\Kernel\Explogs
{
    public function useTerminal( $cli )
    {
        $this->cli = $cli;
    }
    public function callRotate( \expLogManager $manager, $names, $dryRun = false )
    {
        return $this->rotate( $manager, $names, $dryRun );
    }
    public function callPrune( \expLogManager $manager, $names, $dryRun = false )
    {
        return $this->prune( $manager, $names, $dryRun );
    }
    public function callArchives( \expLogManager $manager )
    {
        return $this->archives( $manager );
    }
    public static function split( $value )
    {
        return self::names( $value );
    }
    protected function audit( $action, $id, $size, array $after )
    {
    }
}

class expLogManagerTest extends PHPUnit\Framework\TestCase
{
    private $root;
    private $outside;

    protected function setUp(): void
    {
        $this->root = expLogViewerTestFixtures::root( 'lm' );
        $this->outside = expLogViewerTestFixtures::root( 'lm-outside' );
        file_put_contents( $this->root . '/var/log/error.log', "e\n" );
        file_put_contents( $this->root . '/var/log/error.log.1', "e1\n" );
        file_put_contents( $this->root . '/var/log/debug.log', '' );
        file_put_contents( $this->root . '/var/site/log/storage.log', "s\n" );
        file_put_contents( $this->outside . '/var/log/secret.log', "keep me\n" );
        file_put_contents( $this->outside . '/var/log/access_log', "web\n" );
        symlink( $this->outside . '/var/log/secret.log', $this->root . '/var/log/escape.log' );
    }

    protected function tearDown(): void
    {
        expLogViewerTestFixtures::remove( $this->root );
        expLogViewerTestFixtures::remove( $this->outside );
    }

    private function manager( array $settings = array( 'keep' => 2, 'compress' => false, 'maxAge' => 0 ) )
    {
        $config = expLogViewerTestFixtures::config( $this->root, array( 'LogViewerSettings' => array(
            'Directories' => array( 'var/log', 'var/*/log' ),
            'LogList' => array( 'webserver-access' ),
            'LogPath' => array( 'webserver-access' => $this->outside . '/var/log/access_log' ) ) ) );
        return new expLogManager( new expLogFileLocator( $config, 'var/log' ), $settings );
    }

    public function testLogsListsCurrentFilesWithTheirArchives()
    {
        $logs = $this->manager()->logs();
        $this->assertSame( array( 'debug', 'error', 'site-storage' ), array_keys( $logs ) );
        $this->assertSame( 'var/log/error.log', $logs['error']['id'] );
        $this->assertSame( array( 'error.log.1' ), array_column( $logs['error']['archives'], 'name' ) );
        $this->assertSame( 3, $logs['error']['archive_bytes'] );
        $this->assertArrayNotHasKey( 'webserver-access', $logs );
        $this->assertArrayNotHasKey( 'escape', $logs, 'a symbolic link out of the log directories is not a log' );
    }

    public function testSelectRefusesEverythingOutsideTheAllowList()
    {
        $selection = $this->manager()->select( array( 'error', 'var/site/log/storage.log', '../../etc/passwd', 'var/log/../../x.log',
                                                      'var/log/error.log.1', 'webserver-access', 'escape', '/etc/passwd', 'nope', '' ) );
        $this->assertSame( array( 'error', 'site-storage' ), array_keys( $selection['found'] ) );
        $this->assertSame( array( '../../etc/passwd', 'var/log/../../x.log', 'var/log/error.log.1', 'webserver-access', 'escape', '/etc/passwd', 'nope' ),
                           $selection['unknown'] );
    }

    public function testRotateByKeyAndAllLeavesEmptyLogsAndUnknownNames()
    {
        $manager = $this->manager();
        $done = $manager->rotate( array( 'error', 'webserver-access' ) );
        $this->assertTrue( $done['results']['error']['ok'] );
        $this->assertSame( 'var/log/error.log', $done['results']['error']['id'] );
        $this->assertSame( array( 'webserver-access' ), $done['unknown'] );
        $this->assertSame( "e\n", file_get_contents( $this->root . '/var/log/error.log.1' ) );
        $this->assertSame( "e1\n", file_get_contents( $this->root . '/var/log/error.log.2' ) );
        $this->assertSame( '', file_get_contents( $this->root . '/var/log/error.log' ) );
        $this->assertSame( "web\n", file_get_contents( $this->outside . '/var/log/access_log' ), 'the web server log is never touched' );

        $all = $this->manager()->rotate();
        $this->assertSame( 'empty', $all['results']['debug']['error'] );
        $this->assertSame( 'empty', $all['results']['error']['error'], 'just rotated: empty, left' );
        $this->assertTrue( $all['results']['site-storage']['ok'] );
        $this->assertFileExists( $this->root . '/var/site/log/storage.log.1' );
        $this->assertSame( "keep me\n", file_get_contents( $this->outside . '/var/log/secret.log' ) );
        $this->assertFileDoesNotExist( $this->outside . '/var/log/secret.log.1' );
    }

    public function testRotateFileRefusesArchivesAndConfiguredLogs()
    {
        $manager = $this->manager();
        $this->assertSame( 'not_allowed', $manager->rotateFile( array( 'path' => $this->root . '/var/log/error.log.1', 'archive' => true, 'group' => 'archives' ) )['error'] );
        $this->assertSame( 'not_allowed', $manager->rotateFile( array( 'path' => $this->outside . '/var/log/access_log', 'archive' => false, 'configured' => true, 'group' => 'webserver' ) )['error'] );
    }

    public function testPruneByNumberAndAgeWithDryRun()
    {
        file_put_contents( $this->root . '/var/log/error.log.3', 'old' );
        file_put_contents( $this->root . '/var/log/error.log.gz', 'bare' );
        $dry = $this->manager()->prune( null, true );
        $this->assertSame( array( 'error.log.3', 'error.log.gz' ), array_column( $dry['results']['error'], 'name' ) );
        $this->assertSame( 7, $dry['bytes'] );
        $this->assertFileExists( $this->root . '/var/log/error.log.3' );

        $done = $this->manager()->prune( array( 'error' ) );
        $this->assertSame( 'var/log/error.log', $done['ids']['error'] );
        $this->assertFileDoesNotExist( $this->root . '/var/log/error.log.3' );
        $this->assertFileExists( $this->root . '/var/log/error.log.1' );

        touch( $this->root . '/var/log/error.log.1', time() - 3 * 86400 );
        $aged = $this->manager( array( 'keep' => 2, 'compress' => false, 'maxAge' => 86400 ) )->prune();
        $this->assertSame( 'age', $aged['results']['error'][0]['reason'] );
        $this->assertFileDoesNotExist( $this->root . '/var/log/error.log.1' );
    }

    public function testCommandArgumentsAndExitCodes()
    {
        $this->assertSame( array( 'error', 'debug', 'var/log/x.log' ), expLogManagerTestCommand::split( ' error, ,debug,var/log/x.log ' ) );

        $command = expLogManagerTestCommand::create( 'bin/php/explogs.php' );
        $terminal = new expLogManagerTestTerminal();
        $command->useTerminal( $terminal );

        $this->assertSame( 0, $command->callRotate( $this->manager(), array( 'error' ), true ), 'dry run' );
        $this->assertStringContainsString( 'would rotate error', implode( "\n", $terminal->out ) );
        $this->assertSame( "e\n", file_get_contents( $this->root . '/var/log/error.log' ), 'a dry run changes nothing' );

        $this->assertSame( 2, $command->callRotate( $this->manager(), array( 'error', '../../etc/passwd' ) ), 'an unknown name fails' );
        $this->assertStringContainsString( 'not a log that can be rotated: ../../etc/passwd', implode( "\n", $terminal->err ) );
        $this->assertSame( 1, $command->callRotate( $this->manager(), array( 'debug' ) ), 'nothing rotated: an empty log' );
        $this->assertSame( 0, $command->callRotate( $this->manager(), null ), '--rotate-all' );

        $this->assertSame( 0, $command->callPrune( $this->manager( array( 'keep' => 1, 'compress' => false, 'maxAge' => 0 ) ), null, true ) );
        $this->assertFileExists( $this->root . '/var/log/error.log.2' );
        $this->assertSame( 0, $command->callPrune( $this->manager( array( 'keep' => 1, 'compress' => false, 'maxAge' => 0 ) ), null ) );
        $this->assertFileDoesNotExist( $this->root . '/var/log/error.log.2' );
        $this->assertSame( 0, $command->callArchives( $this->manager() ) );
    }
}
