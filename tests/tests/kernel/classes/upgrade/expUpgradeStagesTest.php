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
 *  US-07 Composer's environment: on Windows SystemRoot, TEMP and the other variables PHP needs there are passed on
 *        (without SystemRoot PHP's curl extension does not start), elsewhere only the PATH and Composer's own
 *  US-08 the installation's own version for Composer: the kernel's in a release install (no git checkout), none in
 *        a git checkout; a COMPOSER_ROOT_VERSION of the process wins
 *  US-09 Composer writing more to stderr than a pipe holds does not leave the upgrade waiting: output, errors and
 *        the exit code are all collected
 *  US-10 code put in place outside git (--code-applied, an upgrade tool's file patch): the code step counts as in
 *        place without looking at git, the stage-order check and the new process hand the option on, and a value
 *        that is no version or commit is refused
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
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
        foreach ( array( 'expupgradestep', 'expupgraderegistry', 'expupgrade', 'expupgradecomposer', 'expupgradebackup', 'expupgradelock', 'expupgraderollback', 'expupgradecaption',
                         'expupgradecontext', 'expupgradebasestep', 'steps/expupgradestepcodeupdate' ) as $f )
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

    /** US-07 */
    public function testComposerEnvironment()
    {
        $vars = array( 'PATH' => 'C:\\tools\\php;C:\\Windows\\system32', 'SystemRoot' => 'C:\\Windows', 'TEMP' => 'D:\\a\\_temp',
                       'TMP' => 'D:\\a\\_temp', 'APPDATA' => 'C:\\Users\\runner\\AppData\\Roaming', 'COMPOSER_HOME' => 'C:\\composer',
                       'OPENSSL_CONF' => 'C:\\ssl\\openssl.cnf', 'SECRET_TOKEN' => 'never', 'EMPTY' => '' );
        $getenv = function ( $name ) use ( $vars ) {
            return isset( $vars[$name] ) ? $vars[$name] : false;
        };
        $windows = expUpgradeComposer::environment( $getenv, false, true );
        foreach ( array( 'PATH', 'SystemRoot', 'TEMP', 'TMP', 'APPDATA', 'COMPOSER_HOME', 'OPENSSL_CONF' ) as $name )
            $this->assertSame( $vars[$name], $windows[$name], "$name on Windows" );
        $this->assertSame( '1', $windows['COMPOSER_NO_INTERACTION'] );
        $this->assertArrayNotHasKey( 'SECRET_TOKEN', $windows, 'only the variables named are passed' );
        $this->assertArrayNotHasKey( 'COMPOSER_ALLOW_SUPERUSER', $windows );

        $linux = expUpgradeComposer::environment( $getenv, true, false );
        $this->assertArrayNotHasKey( 'SystemRoot', $linux, 'the Windows variables only on Windows' );
        $this->assertSame( 'C:\\composer', $linux['COMPOSER_HOME'] );
        $this->assertSame( '1', $linux['COMPOSER_ALLOW_SUPERUSER'] );
        $none = expUpgradeComposer::environment( function () { return false; }, false, false );
        $this->assertSame( array( 'COMPOSER_NO_INTERACTION' => '1', 'PATH' => '/usr/local/bin:/usr/bin:/bin' ), $none );
    }

    /** US-08 */
    public function testComposerRootVersion()
    {
        $this->assertSame( '6.0.15', expUpgradeComposer::rootVersion( $this->dir, '6.0.15' ), 'a release install: the kernel version' );
        $this->assertNull( expUpgradeComposer::rootVersion( $this->dir, '6.0.15alpha' ), 'only MAJOR.MINOR.RELEASE' );
        $kernel = expUpgradeComposer::rootVersion( $this->dir );
        $this->assertSame( eZPublishSDK::VERSION_MAJOR . '.' . eZPublishSDK::VERSION_MINOR . '.' . eZPublishSDK::VERSION_RELEASE, $kernel );
        @mkdir( $this->dir . '/.git' );
        $this->assertNull( expUpgradeComposer::rootVersion( $this->dir, '6.0.15' ), 'a git checkout: Composer reads the branch' );
        @rmdir( $this->dir . '/.git' );

        $none = function () { return false; };
        $this->assertSame( '6.0.15', expUpgradeComposer::environment( $none, false, true, '6.0.15' )['COMPOSER_ROOT_VERSION'] );
        $this->assertArrayNotHasKey( 'COMPOSER_ROOT_VERSION', expUpgradeComposer::environment( $none, false, false, null ) );
        $given = function ( $name ) { return $name === 'COMPOSER_ROOT_VERSION' ? 'dev-main' : false; };
        $this->assertSame( 'dev-main', expUpgradeComposer::environment( $given, false, false, '6.0.15' )['COMPOSER_ROOT_VERSION'],
                           "the process's own wins" );
    }

    /** US-09 */
    public function testComposerOutputCannotBlockTheUpgrade()
    {
        $started = microtime( true );
        $result = expUpgradeComposer::runCommand( array( PHP_BINARY, __DIR__ . '/fixtures/composer_noisy.php' ), $this->dir,
                                                  array( 'EXP_FIXTURE_MARK' => 'us09', 'PATH' => (string)getenv( 'PATH' ) )
                                                  + expUpgradeComposer::environment( 'getenv' ) );
        $this->assertNotNull( $result );
        $this->assertSame( 3, $result['exit'] );
        $this->assertSame( "done us09\n", $result['output'] );
        $this->assertSame( 20000, substr_count( $result['errors'], "\n" ) );
        $this->assertLessThan( 60, microtime( true ) - $started );
    }

    /** US-10 */
    public function testCodeAppliedOutsideGit()
    {
        $db = new stdClass();
        $step = new expUpgradeStepCodeUpdate();
        // a git checkout behind its upstream: without the option the step would look at git; with it, it does not
        @mkdir( $this->dir . '/.git', 0700, true );
        foreach ( array( 'status', 'dry-run', 'apply' ) as $mode )
        {
            $context = new expUpgradeContext( $db, $mode === 'apply', array( 'root' => $this->dir, 'mode' => $mode, 'code-applied' => 'c071e1a0002e' ) );
            $effect = $step->detect( $context );
            $this->assertSame( expUpgradeStep::PRESENT, $effect['effect'], $mode );
            $this->assertStringContainsString( 'c071e1a0002e', $effect['detail'] );
            $this->assertSame( 'not-needed', expUpgrade::stateOf( $effect, null ), 'in place without exp:upgrade' );
            $this->assertFalse( (bool)$context->option( 'code-fetched', false ), 'nothing fetched' );
        }
        @rmdir( $this->dir . '/.git' );
        $context = new expUpgradeContext( $db, false, array( 'root' => $this->dir, 'mode' => 'status' ) );
        $this->assertSame( expUpgradeStep::NOT_APPLICABLE, $step->detect( $context )['effect'], 'not a checkout, no option: as before' );

        $this->assertSame( array( 'db' => $db, 'registry' => null, 'code-applied' => 'v6.0.16' ),
                           expUpgrade::orderCheckOptions( array( 'code-applied' => 'v6.0.16', 'to' => 'x', 'only' => array( 'a' ) ), $db, null ),
                           'the stage-order check sees the option' );
        $this->assertSame( array( 'db' => $db, 'registry' => null ), expUpgrade::orderCheckOptions( array( 'code-applied' => '' ), $db, null ) );

        foreach ( array( '', 'v6.0.16', '6.0.15', 'c071e1a0002e2c1847feabb68c23268ad16acc9b', 'main', 'origin/main', '6.0.15+build.1' ) as $ok )
            $this->assertSame( '', expUpgrade::codeAppliedProblem( $ok ), $ok );
        foreach ( array( '-x', 'a b', "a\nb", '--force-order', str_repeat( 'a', 101 ), 'a;b' ) as $bad )
            $this->assertNotSame( '', expUpgrade::codeAppliedProblem( $bad ), $bad );

        $args = \Exponential\Command\Kernel\Upgrade::childArguments( array( 'database' ), array( 'code-applied' => 'v6.0.16' ), false );
        $this->assertSame( array( '--stages=database', '--apply', '--format=json', '--no-verify', '--code-applied=v6.0.16' ), $args );
    }
}
