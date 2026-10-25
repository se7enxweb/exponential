<?php
/**
 * The cron job the installers write (expCrontabSetup): the lines, the marked block and how it is merged into a
 * crontab without touching other lines, whose crontab is written, the fallback without a crontab, the parts an
 * installation defines, and the run record the dashboard reads.
 *
 * No database and no real crontab: the crontab command is a callable that keeps the crontab in memory.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/expCrontabSetupTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expCrontabSetupTest extends PHPUnit\Framework\TestCase
{
    const ROOT = '/var/www/site';

    private static $tmp;

    public static function setUpBeforeClass(): void
    {
        self::$tmp = sys_get_temp_dir() . '/expcrontabsetup-' . getmypid();
    }

    public static function tearDownAfterClass(): void
    {
        self::removeTree( self::$tmp );
    }

    private static function removeTree( $dir )
    {
        if ( !is_dir( $dir ) )
            return;
        foreach ( (array)scandir( $dir ) as $entry )
        {
            if ( $entry === '.' || $entry === '..' )
                continue;
            $path = $dir . '/' . $entry;
            is_dir( $path ) ? self::removeTree( $path ) : @unlink( $path );
        }
        @rmdir( $dir );
    }

    private static function write( $file, $text )
    {
        @mkdir( dirname( $file ), 0700, true );
        file_put_contents( $file, $text );
    }

    private static function entries()
    {
        return array( array( 'part' => 'global', 'siteaccess' => '', 'schedule' => '*/15 * * * *' ),
                      array( 'part' => 'frequent', 'siteaccess' => '', 'schedule' => '*/5 * * * *' ),
                      array( 'part' => 'expqueue', 'siteaccess' => 'admin', 'schedule' => '* * * * *' ) );
    }

    private static function description( $entries = null )
    {
        return array( 'root' => self::ROOT, 'admin_siteaccess' => 'admin', 'settings' => array( 'command' => '' ),
                      'entries' => $entries === null ? self::entries() : $entries, 'php' => '/usr/bin/php' );
    }

    /**
     * A crontab command that keeps the crontab in $crontab and records each call in $calls.
     */
    private static function fakeCrontab( &$crontab, &$calls, $listStatus = null, $listError = '' )
    {
        return function ( array $argv, $stdin ) use ( &$crontab, &$calls, $listStatus, $listError ) {
            $calls[] = array_slice( $argv, 1 );
            $last = end( $argv );
            if ( $last === '-l' )
            {
                if ( $listStatus !== null )
                    return array( $listStatus, '', $listError );
                if ( $crontab === null )
                    return array( 1, '', "no crontab for site\n" );
                return array( 0, $crontab, '' );
            }
            $crontab = $stdin;
            return array( 0, '', '' );
        };
    }

    public function testLineRunsThePartQuietlyWithItsSiteaccessAndLog()
    {
        $this->assertSame( '*/5 * * * * cd /var/www/site && /usr/bin/php runcronjobs.php -q frequent >> var/log/cronjob-frequent.log 2>&1',
                           expCrontabSetup::line( array( 'part' => 'frequent', 'siteaccess' => '', 'schedule' => '*/5 * * * *' ), self::ROOT, '/usr/bin/php' ) );
        $this->assertSame( '* * * * * cd /var/www/site && /usr/bin/php runcronjobs.php -q -s admin expqueue >> var/log/cronjob-expqueue.log 2>&1',
                           expCrontabSetup::line( array( 'part' => 'expqueue', 'siteaccess' => 'admin', 'schedule' => '* * * * *' ), self::ROOT, '/usr/bin/php' ) );
        // the global part is runcronjobs.php without a part name
        $this->assertSame( '*/15 * * * * cd /var/www/site && /usr/bin/php runcronjobs.php -q >> var/log/cronjob-global.log 2>&1',
                           expCrontabSetup::line( array( 'part' => 'global', 'siteaccess' => '', 'schedule' => '*/15 * * * *' ), self::ROOT, '/usr/bin/php' ) );
        // a path with a space is quoted
        $this->assertStringContainsString( "cd '/srv/my site' && ", expCrontabSetup::line( self::entries()[0], '/srv/my site', 'php' ) );
    }

    public function testLinesAreReadBackAsTheirPartBySetupCronjobs()
    {
        foreach ( self::entries() as $entry )
            $this->assertSame( $entry['part'], expCronjobRunner::partOfCrontabLine( expCrontabSetup::line( $entry, self::ROOT, '/usr/bin/php' ) ) );
    }

    public function testBlockIsMarkedWithTheRoot()
    {
        $block = expCrontabSetup::block( self::entries(), self::ROOT, '/usr/bin/php' );
        $this->assertSame( '# BEGIN Exponential cronjobs: /var/www/site', $block[0] );
        $this->assertSame( '# END Exponential cronjobs: /var/www/site', end( $block ) );
        $this->assertCount( 3 + 3, $block );
        $this->assertSame( array(), expCrontabSetup::block( array(), self::ROOT, 'php' ) );
    }

    public function testMergeAppendsAndKeepsEveryOtherLine()
    {
        $existing = array( 'MAILTO=ops@example.invalid', '# my backup', '0 3 * * * /usr/local/bin/backup', '' );
        $block = expCrontabSetup::block( self::entries(), self::ROOT, '/usr/bin/php' );
        $merged = expCrontabSetup::merge( $existing, $block, self::ROOT );
        $this->assertSame( $existing, array_slice( $merged, 0, 4 ) );
        $this->assertSame( $block, array_slice( $merged, 4 ) );
        // idempotent: the same block again changes nothing
        $this->assertSame( $merged, expCrontabSetup::merge( $merged, $block, self::ROOT ) );
    }

    public function testMergeReplacesTheBlockInItsPlace()
    {
        $old = expCrontabSetup::block( array( self::entries()[1] ), self::ROOT, '/usr/bin/php' );
        $existing = array_merge( array( 'MAILTO=x' ), $old, array( '0 3 * * * backup' ) );
        $new = expCrontabSetup::block( self::entries(), self::ROOT, '/opt/php/bin/php' );
        $merged = expCrontabSetup::merge( $existing, $new, self::ROOT );
        $this->assertSame( array_merge( array( 'MAILTO=x' ), $new, array( '0 3 * * * backup' ) ), $merged );
        // an empty block takes the old one out
        $this->assertSame( array( 'MAILTO=x', '0 3 * * * backup' ), expCrontabSetup::merge( $existing, array(), self::ROOT ) );
    }

    public function testStripTouchesOnlyTheBlockOfThisRoot()
    {
        $site = expCrontabSetup::block( self::entries(), '/var/www/site', 'php' );
        $site2 = expCrontabSetup::block( self::entries(), '/var/www/site2', 'php' );
        $crontab = array_merge( array( 'A=1' ), $site2, $site, array( 'last' ) );
        $this->assertSame( array_merge( array( 'A=1' ), $site2, array( 'last' ) ), expCrontabSetup::strip( $crontab, '/var/www/site' ) );
        $this->assertSame( array_merge( array( 'A=1' ), $site, array( 'last' ) ), expCrontabSetup::strip( $crontab, '/var/www/site2' ) );
        $this->assertSame( array( 'x' ), expCrontabSetup::strip( array( 'x' ), '/var/www/site' ) );
    }

    public function testHandWrittenLinesOfThisInstallationAreFound()
    {
        $crontab = array( '# * * * * * cd /var/www/site && php runcronjobs.php frequent',
                          '0,15,30,45 * * * * cd /var/www/site && php runcronjobs.php -q frequent 2>&1',
                          '* * * * * cd /var/www/site2 && php runcronjobs.php -q -s admin expqueue',
                          '35 6 * * * cd /var/www/site/ && $PHP runcronjobs.php -q 2>&1' );
        $found = expCrontabSetup::scheduledOutside( $crontab, '/var/www/site' );
        $this->assertSame( array( 'frequent', 'global' ), array_keys( $found ) );
    }

    public function testSettingsJoinPartsCountScriptsAndTakeSchedules()
    {
        $settings = array( 'install' => 'enabled', 'parts' => array(), 'admin_parts' => array(), 'schedules' => array(),
                           'command' => '', 'php' => '', 'part_scripts' => array() );
        $settings = expCrontabSetup::mergeSettings( $settings, array(
            'CronjobSettings' => array( 'Scripts' => array( 'unpublish.php', 'indexcontent.php' ) ),
            'CronjobPart-frequent' => array( 'Scripts' => array( 'notification.php' ) ),
            'CronjobPart-unlock' => array( 'Scripts' => array( '' ) ),
            'AdminSettings' => array( 'CrontabSchedule_expqueue' => '* * * * *', 'PhpCliPath' => '' ),
            'CrontabSettings' => array( 'Install' => 'enabled', 'Parts' => array( 'global', 'frequent', 'expqueue', 'unlock' ),
                                        'AdminParts' => array( 'expqueue' ) ) ) );
        // an extension's file: its part, and one script more in the queue's part
        $settings = expCrontabSetup::mergeSettings( $settings, array(
            'CronjobPart-expqueue' => array( 'Scripts' => array( 'expqueue.php' ) ) ) );
        $this->assertSame( array( 'global' => 2, 'frequent' => 1, 'unlock' => 0, 'expqueue' => 1 ), $settings['part_scripts'] );

        $entries = expCrontabSetup::entries( $settings, 'admin' );
        $this->assertSame( array( 'global', 'frequent', 'expqueue' ), array_column( $entries, 'part' ) );
        $this->assertSame( array( '', '', 'admin' ), array_column( $entries, 'siteaccess' ) );
        $this->assertSame( array( '*/15 * * * *', '*/5 * * * *', '* * * * *' ), array_column( $entries, 'schedule' ) );

        $off = expCrontabSetup::mergeSettings( $settings, array( 'CrontabSettings' => array( 'Install' => 'disabled' ) ) );
        $this->assertSame( 'disabled', $off['install'] );
    }

    public function testInstallationFilesGiveThePartsOfItsActiveExtensions()
    {
        $root = self::$tmp . '/install';
        self::write( $root . '/settings/cronjob.ini', file_get_contents( __DIR__ . '/../../../../settings/cronjob.ini' ) );
        self::write( $root . '/settings/override/site.ini.append.php',
                     "<?php /* #?ini charset=\"utf-8\"?\n\n[ExtensionSettings]\nActiveExtensions[]\nActiveExtensions[]=expqueue\nActiveExtensions[]=missing\n*/ ?>\n" );
        self::write( $root . '/settings/siteaccess/admin/site.ini.append.php',
                     "<?php /* #?ini charset=\"utf-8\"?\n\n[ExtensionSettings]\nActiveAccessExtensions[]=sqliimport\n*/ ?>\n" );
        self::write( $root . '/extension/expqueue/settings/cronjob.ini.append.php',
                     "<?php /* #?ini charset=\"utf-8\"?\n\n[CronjobPart-expqueue]\nScripts[]=expqueue.php\n*/ ?>\n" );
        self::write( $root . '/extension/sqliimport/settings/cronjob.ini.append.php',
                     "<?php /* #?ini charset=\"utf-8\"?\n\n[CronjobPart-sqliimport]\nScripts[]=sqliimport_schedule.php\n*/ ?>\n" );
        // installed but not active: its part is not scheduled
        self::write( $root . '/extension/expai/settings/cronjob.ini.append.php',
                     "<?php /* #?ini charset=\"utf-8\"?\n\n[CronjobPart-expai]\nScripts[]=expai_queue.php\n*/ ?>\n" );

        $this->assertSame( 'admin', expCrontabSetup::adminSiteaccess( $root ) );
        $this->assertSame( array( 'expqueue', 'sqliimport' ), expCrontabSetup::activeExtensions( $root, 'admin' ) );
        $description = expCrontabSetup::describe( $root );
        $this->assertSame( array( 'global', 'frequent', 'infrequent', 'expqueue', 'sqliimport' ), array_column( $description['entries'], 'part' ) );
        $this->assertSame( array( '', '', '', 'admin', 'admin' ), array_column( $description['entries'], 'siteaccess' ) );
        $this->assertSame( '* * * * *', $description['entries'][3]['schedule'] );
        $this->assertTrue( expCrontabSetup::enabled( $root ) );

        self::write( $root . '/settings/override/cronjob.ini.append.php',
                     "<?php /* #?ini charset=\"utf-8\"?\n\n[CrontabSettings]\nInstall=disabled\n*/ ?>\n" );
        $this->assertFalse( expCrontabSetup::enabled( $root ) );
    }

    public function testEnvironmentSwitchesItOffForOneRun()
    {
        $before = getenv( 'EXP_INSTALL_CRONTAB' );
        putenv( 'EXP_INSTALL_CRONTAB=0' );
        try
        {
            $this->assertFalse( expCrontabSetup::enabled( self::$tmp . '/none' ) );
        }
        finally
        {
            putenv( $before === false ? 'EXP_INSTALL_CRONTAB' : 'EXP_INSTALL_CRONTAB=' . $before );
        }
        $this->assertTrue( expCrontabSetup::enabled( self::$tmp . '/none' ) );
    }

    public function testWhoseCrontab()
    {
        $this->assertSame( array(), expCrontabSetup::target( 1001, 1001, 'site' )['args'] );
        $this->assertTrue( expCrontabSetup::target( 1001, 1001, 'site' )['ok'] );
        $this->assertSame( array( '-u', 'site' ), expCrontabSetup::target( 0, 1001, 'site' )['args'] );
        // never root's crontab for a site, never another user's
        $this->assertFalse( expCrontabSetup::target( 0, 0, 'root' )['ok'] );
        $this->assertFalse( expCrontabSetup::target( 1002, 1001, 'site' )['ok'] );
        $this->assertFalse( expCrontabSetup::target( null, 1001, 'site' )['ok'] );
    }

    public function testRootWritesTheOwnersCrontabOnceAndKeepsItsLines()
    {
        $crontab = "MAILTO=ops@example.invalid\n0 3 * * * /usr/local/bin/backup\n";
        $calls = array();
        $setup = new expCrontabSetup( self::fakeCrontab( $crontab, $calls ), PHP_BINARY, 0, array( 'uid' => 1001, 'name' => 'site' ) );

        $result = $setup->add( self::description() );
        $this->assertSame( 'written', $result['status'] );
        $this->assertSame( 'site', $result['user'] );
        $this->assertSame( array( array( '-u', 'site', '-l' ), array( '-u', 'site', '-' ), array( '-u', 'site', '-l' ) ), $calls );
        $lines = explode( "\n", rtrim( $crontab, "\n" ) );
        $this->assertSame( array( 'MAILTO=ops@example.invalid', '0 3 * * * /usr/local/bin/backup' ), array_slice( $lines, 0, 2 ) );
        $this->assertSame( $result['block'], array_slice( $lines, 2 ) );

        // a second run reads the crontab and writes nothing
        $calls = array();
        $again = $setup->add( self::description() );
        $this->assertSame( 'unchanged', $again['status'] );
        $this->assertSame( array( array( '-u', 'site', '-l' ) ), $calls );
        $this->assertSame( 1, substr_count( $crontab, 'runcronjobs.php -q -s admin expqueue' ) );
    }

    public function testTheOwnerWritesItsOwnCrontabWithoutUserOption()
    {
        $crontab = null;
        $calls = array();
        $setup = new expCrontabSetup( self::fakeCrontab( $crontab, $calls ), PHP_BINARY, 1001, array( 'uid' => 1001, 'name' => 'site' ) );
        $this->assertSame( 'written', $setup->add( self::description() )['status'] );
        $this->assertSame( array( '-l' ), $calls[0] );
        $this->assertSame( array( '-' ), $calls[1] );
    }

    public function testDryRunWritesNothing()
    {
        $crontab = "A=1\n";
        $calls = array();
        $setup = new expCrontabSetup( self::fakeCrontab( $crontab, $calls ), PHP_BINARY, 1001, array( 'uid' => 1001, 'name' => 'site' ) );
        $result = $setup->add( self::description(), true );
        $this->assertSame( 'dry-run', $result['status'] );
        $this->assertSame( "A=1\n", $crontab );
        $this->assertSame( array( array( '-l' ) ), $calls );
        $this->assertSame( array_merge( array( 'A=1' ), $result['block'] ), $result['after'] );
    }

    public function testAPartAnExistingLineRunsIsLeftOut()
    {
        $crontab = "0,15,30,45 * * * * cd /var/www/site && php runcronjobs.php -q frequent 2>&1\n";
        $calls = array();
        $setup = new expCrontabSetup( self::fakeCrontab( $crontab, $calls ), PHP_BINARY, 1001, array( 'uid' => 1001, 'name' => 'site' ) );
        $result = $setup->add( self::description() );
        $this->assertSame( array( 'frequent' ), array_keys( $result['skipped'] ) );
        $this->assertSame( 1, substr_count( $crontab, 'frequent' ) );
        $this->assertStringContainsString( 'expqueue', $crontab );
    }

    public function testWithoutCrontabTheLinesAreGivenAndNothingFails()
    {
        $calls = array();
        $crontab = null;
        $setup = new expCrontabSetup( self::fakeCrontab( $crontab, $calls ), self::$tmp . '/no/crontab', 1001, array( 'uid' => 1001, 'name' => 'site' ) );
        $result = $setup->add( self::description() );
        $this->assertSame( 'fallback', $result['status'] );
        $this->assertSame( array(), $calls );
        $report = expCrontabSetup::report( $result );
        $this->assertStringContainsString( 'no crontab command', $report[0] );
        $this->assertStringContainsString( 'crontab of site', $report[0] );
        $this->assertSame( '  ' . expCrontabSetup::line( self::entries()[2], self::ROOT, '/usr/bin/php' ), $report[5] );
    }

    public function testARefusedCrontabFallsBack()
    {
        $calls = array();
        $crontab = null;
        $setup = new expCrontabSetup( self::fakeCrontab( $crontab, $calls, 1, "You (site) are not allowed to use this program (crontab)\n" ),
                                      PHP_BINARY, 1001, array( 'uid' => 1001, 'name' => 'site' ) );
        $result = $setup->add( self::description() );
        $this->assertSame( 'fallback', $result['status'] );
        $this->assertStringContainsString( 'not allowed', $result['message'] );
        $this->assertNull( $crontab );
    }

    public function testAnotherUserOrRootOwnedInstallationFallsBack()
    {
        $calls = array();
        $crontab = null;
        $other = new expCrontabSetup( self::fakeCrontab( $crontab, $calls ), PHP_BINARY, 1002, array( 'uid' => 1001, 'name' => 'site' ) );
        $this->assertSame( 'fallback', $other->add( self::description() )['status'] );
        $rootOwned = new expCrontabSetup( self::fakeCrontab( $crontab, $calls ), PHP_BINARY, 0, array( 'uid' => 0, 'name' => 'root' ) );
        $this->assertSame( 'fallback', $rootOwned->add( self::description() )['status'] );
        $this->assertSame( array(), $calls );
    }

    public function testRemoveTakesOutOnlyTheBlock()
    {
        $block = expCrontabSetup::block( self::entries(), self::ROOT, '/usr/bin/php' );
        $crontab = "A=1\n" . implode( "\n", $block ) . "\n0 3 * * * backup\n";
        $calls = array();
        $setup = new expCrontabSetup( self::fakeCrontab( $crontab, $calls ), PHP_BINARY, 1001, array( 'uid' => 1001, 'name' => 'site' ) );
        $dry = $setup->remove( self::description(), true );
        $this->assertSame( 'dry-run', $dry['status'] );
        $this->assertSame( $block, $dry['block'] );
        $this->assertStringContainsString( 'BEGIN Exponential', $crontab );

        $this->assertSame( 'removed', $setup->remove( self::description() )['status'] );
        $this->assertSame( "A=1\n0 3 * * * backup\n", $crontab );
        $this->assertSame( 'absent', $setup->remove( self::description() )['status'] );
    }

    public function testRunRecordAndState()
    {
        $runs = expCrontabSetup::addRun( array(), 'frequent', 'site', 'started', 1000 );
        $runs = expCrontabSetup::addRun( $runs, 'frequent', 'site', 'finished', 1010 );
        $this->assertSame( array( 'frequent' => array( 'started' => 1000, 'finished' => 1010, 'siteaccess' => 'site' ) ), $runs );

        $now = mktime( 12, 0, 0, 10, 24, 2026 );
        $entries = array( array( 'part' => 'frequent', 'siteaccess' => '', 'schedule' => '*/5 * * * *' ),
                          array( 'part' => 'expqueue', 'siteaccess' => 'admin', 'schedule' => '* * * * *' ) );
        $this->assertSame( 'never', expCrontabSetup::runState( $entries, array(), $now )['overall'] );

        $runs = array( 'frequent' => array( 'started' => $now - 120, 'finished' => $now - 110, 'siteaccess' => '' ),
                       'expqueue' => array( 'started' => $now - 30, 'finished' => $now - 29, 'siteaccess' => 'admin' ) );
        $state = expCrontabSetup::runState( $entries, $runs, $now );
        $this->assertSame( 'ok', $state['overall'] );
        $this->assertSame( $now - 30, $state['last'] );

        // a minute part may be quiet for an hour, not more
        $runs['expqueue']['started'] = $now - 3700;
        $state = expCrontabSetup::runState( $entries, $runs, $now );
        $this->assertSame( 'late', $state['overall'] );
        $this->assertSame( array( 'ok', 'late' ), array_column( $state['parts'], 'state' ) );

        unset( $runs['expqueue'] );
        $this->assertSame( array( 'ok', 'never' ), array_column( expCrontabSetup::runState( $entries, $runs, $now )['parts'], 'state' ) );
        $this->assertSame( 'late', expCrontabSetup::runState( $entries, $runs, $now )['overall'] );
    }

    public function testRunRecordFile()
    {
        $root = self::$tmp . '/runs';
        @mkdir( $root, 0700, true );
        $this->assertTrue( expCrontabSetup::recordRun( '', 'site', 'started', 500, $root ) );
        $this->assertTrue( expCrontabSetup::recordRun( 'expqueue', 'admin', 'started', 600, $root ) );
        $this->assertTrue( expCrontabSetup::recordRun( 'expqueue', 'admin', 'finished', 601, $root ) );
        $this->assertSame( array( 'expqueue' => array( 'started' => 600, 'finished' => 601, 'siteaccess' => 'admin' ),
                                  'global' => array( 'started' => 500, 'finished' => 0, 'siteaccess' => 'site' ) ),
                           expCrontabSetup::lastRuns( $root ) );
    }
}
