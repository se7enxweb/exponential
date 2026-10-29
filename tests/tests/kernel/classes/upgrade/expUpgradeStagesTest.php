<?php
/**
 * The code and libraries stages, the run in a new process, the production rule and the rollback, without a
 * database: what can be told from files and options alone.
 *
 *  US-01 composer.lock against vendor/composer/installed.json: versions, dev references, dev packages only when
 *        vendor/ has them, no lock
 *  US-02 the report of two runs merged (code stage here, the rest in a new process)
 *  US-03 the options of the new process: the run's options handed on, no second backup
 *  US-04 production: [AdminEnvironment] Level, auto with EXP_ENV
 *  US-05 the rollback refuses an unknown backup or part (usage, exit 2) before it touches anything
 *  US-06 the audit events of the upgrade are in the taxonomy
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expUpgradeStagesTest extends PHPUnit\Framework\TestCase
{
    private static $root;

    private $dir;

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 5 );
        foreach ( array( 'expupgradestep', 'expupgraderegistry', 'expupgrade', 'expupgradecomposer', 'expupgradebackup', 'expupgradelock', 'expupgraderollback', 'expupgradecaption' ) as $f )
            require_once self::$root . "/kernel/classes/upgrade/$f.php";
        require_once self::$root . '/kernel/private/classes/runnable/runnable.php';
        require_once self::$root . '/kernel/private/classes/runnable/command.php';
        require_once self::$root . '/kernel/private/classes/commands/upgradebase.php';
        require_once self::$root . '/kernel/private/classes/commands/upgrade.php';
    }

    protected function setUp(): void
    {
        $this->dir = self::$root . '/var/tmp/expUpgradeStagesTest-' . getmypid();
        @mkdir( $this->dir . '/vendor/composer', 0700, true );
    }

    protected function tearDown(): void
    {
        foreach ( array( '/vendor/composer/installed.json', '/composer.lock' ) as $f )
            @unlink( $this->dir . $f );
        @rmdir( $this->dir . '/vendor/composer' );
        @rmdir( $this->dir . '/vendor' );
        @rmdir( $this->dir );
    }

    private function write( $file, array $data )
    {
        file_put_contents( $this->dir . '/' . $file, json_encode( $data ) );
    }

    /** US-01 */
    public function testLockDifferences()
    {
        $this->assertNull( expUpgradeComposer::lockDifferences( $this->dir ), 'no lock' );
        $this->write( 'composer.lock', array(
            'packages' => array(
                array( 'name' => 'a/one', 'version' => '1.2.0' ),
                array( 'name' => 'a/two', 'version' => 'dev-main', 'source' => array( 'reference' => 'abcdef1234567890' ) ),
                array( 'name' => 'a/three', 'version' => '3.0.0' ),
            ),
            'packages-dev' => array( array( 'name' => 'a/test', 'version' => '9.0.0' ) ),
        ) );
        $this->write( 'vendor/composer/installed.json', array( 'dev' => false, 'packages' => array(
            array( 'name' => 'a/one', 'version' => '1.2.0' ),
            array( 'name' => 'a/two', 'version' => 'dev-main', 'source' => array( 'reference' => '0000000000aaaa' ) ),
        ) ) );
        $this->assertSame( array( 'a/three' => 'not installed -> 3.0.0', 'a/two' => 'dev-main@0000000000 -> dev-main@abcdef1234' ),
                           expUpgradeComposer::lockDifferences( $this->dir ), 'a dev package is not asked for when vendor/ has none' );
        $this->assertFalse( expUpgradeComposer::installedWithDev( $this->dir ) );

        $this->write( 'vendor/composer/installed.json', array( 'dev' => true, 'packages' => array(
            array( 'name' => 'a/one', 'version' => '1.2.0' ),
            array( 'name' => 'a/two', 'version' => 'dev-main', 'source' => array( 'reference' => 'abcdef1234567890' ) ),
            array( 'name' => 'A/Three', 'version' => '3.0.0' ),
        ) ) );
        $this->assertSame( array( 'a/test' => 'not installed -> 9.0.0' ), expUpgradeComposer::lockDifferences( $this->dir ) );
    }

    /** US-01: a package Composer would change that is a git checkout of its own is found */
    public function testGitCheckoutsAreFound()
    {
        $made = array( 'extension/mine/.git', 'extension/other', 'vendor/a/lib/.git' );
        foreach ( $made as $d )
            @mkdir( $this->dir . '/' . $d, 0700, true );
        $this->write( 'extension/mine/composer.json', array( 'name' => 'Me/Mine' ) );
        $this->write( 'extension/other/composer.json', array( 'name' => 'me/other' ) );
        $this->write( 'vendor/a/lib/composer.json', array( 'name' => 'a/lib' ) );
        $found = expUpgradeComposer::gitCheckouts( $this->dir, array( 'me/mine', 'me/other', 'a/lib', 'a/none' ) );
        foreach ( array( 'extension/mine/composer.json', 'extension/other/composer.json', 'vendor/a/lib/composer.json' ) as $f )
            @unlink( $this->dir . '/' . $f );
        foreach ( array( 'extension/mine/.git', 'extension/mine', 'extension/other', 'extension', 'vendor/a/lib/.git', 'vendor/a/lib', 'vendor/a' ) as $d )
            @rmdir( $this->dir . '/' . $d );
        $this->assertSame( array( 'a/lib' => 'vendor/a/lib', 'me/mine' => 'extension/mine' ), $found, 'only git checkouts, names compared without case' );
        $this->assertSame( array(), expUpgradeComposer::gitCheckouts( $this->dir, array() ) );
    }

    /** US-02 */
    public function testMergeReports()
    {
        $a = expUpgrade::report( 'apply' );
        $a['stages'] = array( array( 'stage' => 'code' ) );
        $a['summary']['applied'] = 1;
        $a['summary']['done'] = 1;
        $a['backup'] = array( 'dir' => 'x' );
        $a['warnings'] = array( 'w' );
        $b = expUpgrade::report( 'apply' );
        $b['stages'] = array( array( 'stage' => 'database' ), array( 'stage' => 'finish' ) );
        $b['summary']['applied'] = 3;
        $b['summary']['failed'] = 1;
        $b['installation']['engine'] = 'sqlite';
        $b['warnings'] = array( 'w', 'v' );
        $b['errors'] = array( 'e' );
        $b['exit'] = 1;
        $m = expUpgrade::mergeReports( $a, $b );
        $this->assertSame( array( 'code', 'database', 'finish' ), array_column( $m['stages'], 'stage' ) );
        $this->assertSame( 4, $m['summary']['applied'] );
        $this->assertSame( 1, $m['summary']['failed'] );
        $this->assertSame( array( 'dir' => 'x' ), $m['backup'] );
        $this->assertSame( 'sqlite', $m['installation']['engine'] );
        $this->assertSame( array( 'w', 'v' ), $m['warnings'] );
        $this->assertSame( array( 'e' ), $m['errors'] );
        $this->assertSame( 1, $m['exit'] );
    }

    /** US-03 */
    public function testChildArguments()
    {
        $args = \Exponential\Command\Kernel\Upgrade::childArguments( array( 'libraries', 'database' ),
            array( 'skip' => array( 'a,b' ), 'url' => array( 'https://x/' ), 'no-deploy' => true, 'composer' => '/c', 'to' => 'v6.0.16', 'apply' => true ), true );
        $this->assertSame( array( '--stages=libraries,database', '--apply', '--format=json', '--no-verify', '--no-deploy', '--no-backup', '--force-no-backup',
                                  '--composer=/c', '--to=v6.0.16', '--skip=a,b', '--url=https://x/' ), $args );
        $args = \Exponential\Command\Kernel\Upgrade::childArguments( array( 'finish' ), array(), false );
        $this->assertSame( array( '--stages=finish', '--apply', '--format=json', '--no-verify' ), $args, 'no backup made yet: the new process makes it' );
    }

    /** US-04 */
    public function testProduction()
    {
        $ini = function ( $level ) {
            return new class( $level ) {
                private $l;
                public function __construct( $l ) { $this->l = $l; }
                public function hasVariable( $g, $v ) { return $this->l !== null; }
                public function variable( $g, $v ) { return $this->l; }
            };
        };
        $this->assertTrue( expUpgrade::isProduction( $ini( 'production' ) ) );
        $this->assertFalse( expUpgrade::isProduction( $ini( 'staging' ) ) );
        $this->assertFalse( expUpgrade::isProduction( $ini( '' ) ) );
        $this->assertFalse( expUpgrade::isProduction( $ini( null ) ) );
        $env = getenv( 'EXP_ENV' );
        putenv( 'EXP_ENV=live' );
        $this->assertTrue( expUpgrade::isProduction( $ini( 'auto' ) ) );
        putenv( 'EXP_ENV=dev' );
        $this->assertFalse( expUpgrade::isProduction( $ini( 'auto' ) ) );
        putenv( $env === false ? 'EXP_ENV' : "EXP_ENV=$env" );
    }

    /** US-05 */
    public function testRollbackRefusesUnknownBackupOrPart()
    {
        $r = expUpgradeRollback::run( '../etc', array( 'backup-dir' => $this->dir, 'root' => $this->dir ) );
        $this->assertSame( expUpgrade::EXIT_USAGE, $r['exit'] );
        $r = expUpgradeRollback::run( '20261029-101500', array( 'backup-dir' => $this->dir, 'root' => $this->dir ) );
        $this->assertSame( expUpgrade::EXIT_USAGE, $r['exit'], 'no such backup' );
        @mkdir( $this->dir . '/20261029-101500' );
        $r = expUpgradeRollback::run( '20261029-101500', array( 'backup-dir' => $this->dir, 'root' => $this->dir, 'parts' => 'database,nope' ) );
        @rmdir( $this->dir . '/20261029-101500' );
        $this->assertSame( expUpgrade::EXIT_USAGE, $r['exit'] );
        $this->assertStringContainsString( 'unknown part nope', $r['errors'][0] );
        $this->assertSame( array( 'database', 'storage', 'settings', 'libraries', 'code' ), expUpgradeRollback::PARTS );
    }

    /** US-06 */
    public function testAuditEventsAreKnown()
    {
        $source = file_get_contents( self::$root . '/kernel/classes/audit/expaudittaxonomy.php' );
        foreach ( array( 'system.upgrade.run', 'system.upgrade.step', 'system.upgrade.backup', 'system.upgrade.rollback', 'system.upgrade.caption', 'system.upgrade.verify' ) as $name )
            $this->assertStringContainsString( "'$name' =>", $source );
    }
}
