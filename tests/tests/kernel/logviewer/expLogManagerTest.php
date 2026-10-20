<?php
/**
 * expLogManager, log management on the log viewer's allow-list (doc/en/us/features/6.0/log-rotation.md), in a throwaway
 * installation root (no database, no real log):
 *   - logs(): current logs with archives, sizes, growth, limits with their sources, owner; never an archive, a link
 *     out of the directories or a configured (web server) log
 *   - effectiveSettings(): logviewer.ini [RotationSettings] per key over the site.ini defaults
 *   - select()/plan(): keys and ids only among the allowed files; everything else unknown and never touched
 *   - plan() changes nothing (the dry run); apply() rotates, prunes, compresses and empties, checked again on disk
 *   - a forged plan step that names a file outside the log's own files is refused
 *   - rotate()/prune()/compress()/truncate() shortcuts
 *   - the exp:logs command: the names list, --dry-run, the exit codes
 *   - the cronjob part rotates only the logs over MaxSize
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
    public function callManage( \expLogManager $manager, $op, $names, $dryRun = false, array $opts = array() )
    {
        return $this->manage( $manager, $op, $names, $dryRun, $opts );
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

    private function manager( array $settings = array( 'keep' => 2, 'max_size' => 1000, 'compress' => false, 'max_age_days' => 0 ) )
    {
        $config = expLogViewerTestFixtures::config( $this->root, array( 'LogViewerSettings' => array(
            'Directories' => array( 'var/log', 'var/*/log' ),
            'LogList' => array( 'webserver-access' ),
            'LogPath' => array( 'webserver-access' => $this->outside . '/var/log/access_log' ) ) ) );
        return new expLogManager( $config, new expLogFileLocator( $config, 'var/log' ), $settings );
    }

    private function path( $name )
    {
        return $this->root . '/' . $name;
    }

    public function testLogsListsCurrentFilesWithArchivesGrowthAndLimits()
    {
        touch( $this->path( 'var/log/error.log.1' ), time() - 7200 );
        $logs = $this->manager()->logs();
        $this->assertSame( array( 'debug', 'error', 'site-storage' ), array_keys( $logs ) );
        $error = $logs['error'];
        $this->assertSame( 'var/log/error.log', $error['id'] );
        $this->assertSame( array( 'var/log/error.log.1' ), array_column( $error['archives'], 'id' ) );
        $this->assertSame( 3, $error['archive_bytes'] );
        $this->assertSame( 1, $error['growth'], '2 bytes in 2 hours' );
        $this->assertSame( 2, $error['limits']['keep'] );
        $this->assertSame( 'test', $error['limits']['source']['keep']['file'] );
        $this->assertNotSame( '', $error['owner'] );
        $this->assertArrayHasKey( 'writable', $error );
        $this->assertArrayNotHasKey( 'webserver-access', $logs );
        $this->assertArrayNotHasKey( 'escape', $logs, 'a symbolic link out of the log directories is not a log' );
    }

    public function testEffectiveSettingsTakeThePerKeyOverrides()
    {
        $manager = $this->manager( array( 'keep' => 3, 'max_size' => 100, 'compress' => false, 'max_age_days' => 0,
                                          'overrides' => array( 'Keep' => array( 'debug' => '1' ), 'Compress' => array( 'debug' => 'enabled' ),
                                                                'MaxSize' => array( 'debug' => 'not a number' ) ) ) );
        $debug = $manager->effectiveSettings( 'debug' );
        $this->assertSame( 1, $debug['keep']['value'] );
        $this->assertSame( array( 'file' => 'logviewer.ini', 'section' => 'RotationSettings', 'var' => 'Keep[debug]' ), $debug['keep']['source'] );
        $this->assertTrue( $debug['compress']['value'] );
        $this->assertSame( 100, $debug['max_size']['value'], 'an invalid override is ignored' );
        $this->assertSame( 3, $manager->effectiveSettings( 'error' )['keep']['value'] );
    }

    public function testSelectAndPlanRefuseEverythingOutsideTheAllowList()
    {
        $names = array( 'error', 'var/site/log/storage.log', '../../etc/passwd', 'var/log/../../x.log', 'var/log/error.log.1',
                        'webserver-access', 'escape', '/etc/passwd', 'nope' );
        $selection = $this->manager()->select( $names );
        $this->assertSame( array( 'error', 'site-storage' ), array_keys( $selection['found'] ) );
        $this->assertCount( 7, $selection['unknown'] );
        $plan = $this->manager()->plan( $names, 'rotate' );
        $this->assertSame( array( 'error', 'site-storage' ), array_column( $plan['steps'], 'key' ) );
        $this->assertCount( 7, $plan['unknown'] );
    }

    public function testPlanChangesNothingAndApplyRotates()
    {
        $manager = $this->manager();
        $plan = $manager->plan( array( 'error', 'debug' ), 'rotate' );
        $this->assertSame( array( array( 'key' => 'error', 'op' => 'rotate', 'from' => 'error.log', 'to' => 'error.log.1', 'bytes' => 2 ) ), $plan['steps'],
                           'an empty log has no step' );
        $this->assertSame( "e\n", file_get_contents( $this->path( 'var/log/error.log' ) ) );

        $result = $manager->apply( $plan );
        $this->assertTrue( $result['ok'] );
        $this->assertTrue( $result['steps'][0]['done'] );
        $this->assertSame( "e\n", file_get_contents( $this->path( 'var/log/error.log.1' ) ) );
        $this->assertSame( "e1\n", file_get_contents( $this->path( 'var/log/error.log.2' ) ) );
        $this->assertSame( '', file_get_contents( $this->path( 'var/log/error.log' ) ) );
        $this->assertSame( "web\n", file_get_contents( $this->outside . '/var/log/access_log' ), 'the web server log is never touched' );
        $this->assertSame( "keep me\n", file_get_contents( $this->outside . '/var/log/secret.log' ) );
    }

    public function testRotateAllTakesOnlyLogsOverMaxSizeUnlessForced()
    {
        file_put_contents( $this->path( 'var/site/log/storage.log' ), str_repeat( 's', 50 ) );
        $manager = $this->manager( array( 'keep' => 2, 'max_size' => 10, 'compress' => false, 'max_age_days' => 0 ) );
        $this->assertSame( array( 'site-storage' ), array_column( $manager->plan( 'all', 'rotate' )['steps'], 'key' ) );
        $this->assertSame( array( 'site-storage' ), array_column( $manager->plan( 'all', 'rotate', array( 'force' => false ) )['steps'], 'key' ) );
        $this->assertSame( array( 'error', 'site-storage' ), array_column( $manager->plan( 'all', 'rotate', array( 'force' => true ) )['steps'], 'key' ) );
    }

    public function testPruneCompressAndTruncate()
    {
        file_put_contents( $this->path( 'var/log/error.log.2' ), 'two' );
        file_put_contents( $this->path( 'var/log/error.log.3' ), 'old' );
        file_put_contents( $this->path( 'var/log/error.log.gz' ), 'bare' );
        $manager = $this->manager();

        $plan = $manager->plan( array( 'error' ), 'prune' );
        $this->assertSame( array( 'error.log.3' => 'number', 'error.log.gz' => 'bare' ), array_column( $plan['steps'], 'reason', 'from' ) );
        $this->assertFileExists( $this->path( 'var/log/error.log.3' ) );
        $this->assertTrue( $manager->prune( 'error' )['ok'] );
        $this->assertFileDoesNotExist( $this->path( 'var/log/error.log.3' ) );
        $this->assertFileDoesNotExist( $this->path( 'var/log/error.log.gz' ) );

        touch( $this->path( 'var/log/error.log.1' ), time() - 3 * 86400 );
        $this->assertSame( array( 'error.log.1' ), array_column( $manager->plan( array( 'error' ), 'prune', array( 'days' => 1 ) )['steps'], 'from' ) );

        $compressed = $manager->compress( 'error' );
        $this->assertTrue( $compressed['ok'] );
        $this->assertSame( array( 'error.log.2' ), array_column( $compressed['steps'], 'from' ), '.1 is never compressed' );
        $this->assertSame( 'two', gzdecode( file_get_contents( $this->path( 'var/log/error.log.2.gz' ) ) ) );

        $this->assertTrue( $manager->truncate( 'site-storage', false )['ok'] );
        $this->assertSame( '', file_get_contents( $this->path( 'var/site/log/storage.log' ) ) );
        $this->assertFileDoesNotExist( $this->path( 'var/site/log/storage.log.1' ), 'in place: no copy' );
        file_put_contents( $this->path( 'var/site/log/storage.log' ), 'again' );
        $this->assertTrue( $manager->truncate( 'site-storage' )['ok'] );
        $this->assertSame( 'again', file_get_contents( $this->path( 'var/site/log/storage.log.1' ) ), 'archive first by default' );

        $this->assertSame( 'unknown', $manager->rotate( 'webserver-access' )['error'] );
    }

    public function testAForgedPlanStepIsRefused()
    {
        $manager = $this->manager();
        $forged = array( 'steps' => array(
            array( 'key' => 'error', 'op' => 'delete', 'from' => '../../../' . basename( $this->outside ) . '/var/log/secret.log', 'to' => '', 'bytes' => 0 ),
            array( 'key' => 'error', 'op' => 'delete', 'from' => 'debug.log', 'to' => '', 'bytes' => 0 ),
            array( 'key' => 'error', 'op' => 'delete', 'from' => 'error.log', 'to' => '', 'bytes' => 0 ),
            array( 'key' => 'webserver-access', 'op' => 'rotate', 'from' => 'access_log', 'to' => '', 'bytes' => 0 ) ) );
        $result = $manager->apply( $forged );
        $this->assertFalse( $result['ok'] );
        $this->assertSame( array( 'not_allowed', 'not_allowed', 'not_allowed', 'not_allowed' ), array_column( $result['steps'], 'error' ) );
        $this->assertFileExists( $this->outside . '/var/log/secret.log' );
        $this->assertFileExists( $this->path( 'var/log/debug.log' ) );
        $this->assertFileExists( $this->path( 'var/log/error.log' ) );
    }

    public function testCommandArgumentsAndExitCodes()
    {
        $this->assertSame( array( 'error', 'debug', 'var/log/x.log' ), expLogManagerTestCommand::split( ' error, ,debug,var/log/x.log ' ) );
        $command = expLogManagerTestCommand::create( 'bin/php/explogs.php' );
        $terminal = new expLogManagerTestTerminal();
        $command->useTerminal( $terminal );

        $this->assertSame( 0, $command->callManage( $this->manager(), 'rotate', array( 'error' ), true, array( 'force' => true ) ), 'dry run' );
        $this->assertStringContainsString( 'would rotate', implode( "\n", $terminal->out ) );
        $this->assertSame( "e\n", file_get_contents( $this->path( 'var/log/error.log' ) ), 'a dry run changes nothing' );

        $this->assertSame( 2, $command->callManage( $this->manager(), 'rotate', array( 'error', '../../etc/passwd' ) ) );
        $this->assertStringContainsString( 'not a log that can be managed: ../../etc/passwd', implode( "\n", $terminal->err ) );
        $this->assertSame( 1, $command->callManage( $this->manager(), 'rotate', array( 'debug' ) ), 'nothing to do: an empty log' );
        $this->assertSame( 0, $command->callManage( $this->manager( array( 'keep' => 1, 'max_size' => 1, 'compress' => false, 'max_age_days' => 0 ) ), 'prune', 'all' ) );
        $this->assertFileDoesNotExist( $this->path( 'var/log/error.log.2' ) );
        $this->assertSame( 0, $command->callArchives( $this->manager() ) );
    }

    public function testTheCronjobPartRotatesOnlyLogsOverMaxSize()
    {
        file_put_contents( $this->path( 'var/site/log/storage.log' ), str_repeat( 's', 50 ) );
        $manager = $this->manager( array( 'keep' => 2, 'max_size' => 10, 'compress' => false, 'max_age_days' => 0 ) );
        $result = $manager->apply( $manager->plan( 'all', 'rotate', array( 'force' => false ) ) );
        $this->assertTrue( $result['ok'] );
        $this->assertFileExists( $this->path( 'var/site/log/storage.log.1' ) );
        $this->assertSame( "e\n", file_get_contents( $this->path( 'var/log/error.log' ) ), 'under MaxSize: left' );
        $this->assertFileExists( __DIR__ . '/../../../../cronjobs/explogrotate.php' );
    }
}
