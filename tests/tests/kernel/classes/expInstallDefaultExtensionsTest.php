<?php
/**
 * The default extensions of a new installation (expInstallDefaultExtensions): two-step sign-in required for
 * Administrator users and Editors, and sqliimport in the administration, added by the setup step where the site
 * package did not set them up.
 *
 * No database: an extension directory in the test's own temporary directory stands for the installation.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/expInstallDefaultExtensionsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

// the class of this checkout, also where the autoload array was generated in another one
if ( !class_exists( 'expInstallDefaultExtensions' ) )
    require_once __DIR__ . '/../../../../kernel/classes/expinstalldefaultextensions.php';

class expInstallDefaultExtensionsTest extends PHPUnit\Framework\TestCase
{
    private static $root;

    public static function setUpBeforeClass(): void
    {
        self::$root = sys_get_temp_dir() . '/expinstalldefaults-' . getmypid();
        foreach ( array( 'sevenx_authentication_2fa', 'sqliimport', 'expui', 'expqueue' ) as $extension )
            @mkdir( self::$root . '/full/' . $extension, 0700, true );
        @mkdir( self::$root . '/empty', 0700, true );
        @mkdir( self::$root . '/nodeps/sqliimport', 0700, true );
    }

    public static function tearDownAfterClass(): void
    {
        foreach ( array( 'full/sevenx_authentication_2fa', 'full/sqliimport', 'full/expui', 'full/expqueue', 'full', 'empty', 'nodeps/sqliimport', 'nodeps', '' ) as $dir )
            @rmdir( rtrim( self::$root . '/' . $dir, '/' ) );
    }

    public function testThePolicyRequiresASecondStepOfAdministratorsAndEditorsOnly()
    {
        $entry = expInstallDefaultExtensions::twoFactorPolicySettings( 'key' );
        $this->assertSame( 'sevenxauthentication2fa.ini', $entry['name'] );
        $this->assertTrue( $entry['reset_arrays'] );
        $general = $entry['settings']['General'];
        $this->assertSame( 'enabled', $general['Enforce2FA'] );
        $this->assertSame( 'totp', $general['DefaultMethod'] );
        $this->assertSame( array( '9b47a45624b023b1a76c73b74d704acf', '3c160cca19fb135f83bd02d911f04db2' ), $general['EnforcedGroups'] );
        $this->assertSame( array(), $general['ExemptGroups'] );
        $this->assertSame( array(), $general['ExemptEmailDomains'] );
        $this->assertSame( '0', $general['GracePeriodDays'] );
        $this->assertSame( '10', $entry['settings']['Security']['RecoveryCodes'] );
        $this->assertSame( 'key', $entry['settings']['TOTPSettings']['SecretKey'] );
    }

    public function testEachInstallationGetsItsOwnKey()
    {
        $a = expInstallDefaultExtensions::twoFactorPolicySettings();
        $b = expInstallDefaultExtensions::twoFactorPolicySettings();
        $this->assertSame( 48, strlen( base64_decode( $a['settings']['TOTPSettings']['SecretKey'], true ) ) );
        $this->assertNotSame( $a['settings']['TOTPSettings']['SecretKey'], $b['settings']['TOTPSettings']['SecretKey'] );
    }

    public function testAPackageWithoutTwoFactorSettingsGetsTheHandlerAndThePolicy()
    {
        $settings = expInstallDefaultExtensions::withTwoFactorSettings( array( array( 'name' => 'site.ini', 'settings' => array() ) ), self::$root . '/full', 'k' );
        $this->assertCount( 3, $settings );
        $this->assertSame( array( 'sevenxUser2fa', 'standard' ), $settings[1]['settings']['UserSettings']['LoginHandler'] );
        $this->assertSame( 'enabled', $settings[2]['settings']['General']['Enforce2FA'] );
    }

    public function testAPackageThatConfiguredTwoFactorKeepsItsOwn()
    {
        $own = array(
            array( 'name' => 'site.ini', 'settings' => array( 'UserSettings' => array( 'LoginHandler' => array( 'sevenxUser2fa', 'standard' ) ) ) ),
            array( 'name' => 'sevenxauthentication2fa.ini', 'settings' => array( 'General' => array( 'Enforce2FA' => 'disabled' ) ) )
        );
        $this->assertSame( $own, expInstallDefaultExtensions::withTwoFactorSettings( $own, self::$root . '/full' ) );
    }

    public function testWithoutTheExtensionNothingIsWritten()
    {
        $own = array( array( 'name' => 'site.ini', 'settings' => array() ) );
        $this->assertSame( $own, expInstallDefaultExtensions::withTwoFactorSettings( $own, self::$root . '/empty' ) );
        $this->assertSame( array(), expInstallDefaultExtensions::globalExtensions( self::$root . '/empty' ) );
        $this->assertSame( array( 'sevenx_authentication_2fa' ), expInstallDefaultExtensions::globalExtensions( self::$root . '/full' ) );
    }

    public function testImportsComeLastInTheAdministrationWithWhatTheyNeed()
    {
        $this->assertSame( array( 'expai', 'expui', 'expqueue', 'sqliimport' ),
                           expInstallDefaultExtensions::adminAccessExtensions( array( 'expai', '' ), array( 'ezjscore' ), self::$root . '/full' ) );
        // expui and expqueue global (the maintained packages): only sqliimport
        $this->assertSame( array( 'expai', 'sqliimport' ),
                           expInstallDefaultExtensions::adminAccessExtensions( array( 'expai' ), array( 'expui', 'expqueue' ), self::$root . '/full' ) );
        // already there: unchanged
        $this->assertSame( array( 'expai', 'sqliimport' ),
                           expInstallDefaultExtensions::adminAccessExtensions( array( 'expai', 'sqliimport' ), array( 'expui', 'expqueue' ), self::$root . '/full' ) );
    }

    public function testImportsAreLeftOutWithoutTheirDependencies()
    {
        $this->assertSame( array( 'expai' ), expInstallDefaultExtensions::adminAccessExtensions( array( 'expai' ), array(), self::$root . '/nodeps' ) );
        $this->assertSame( array( 'expai' ), expInstallDefaultExtensions::adminAccessExtensions( array( 'expai' ), array(), self::$root . '/empty' ) );
    }

    public function testTheEditorIsMadeWithoutImports()
    {
        $this->assertSame( array( 'expai', 'expui', 'expqueue', 'exp_adminui' ),
                           expInstallDefaultExtensions::editorAccessExtensions( array( 'expai', 'expui', 'expqueue', 'sqliimport', 'exp_adminui' ) ) );
    }

    public function testTheImportsTabFollowsExport()
    {
        $this->assertSame( array( 'setup', 'xrowextract', 'sqliimport', 'bccie_overview', 'newsletter' ),
                           expInstallDefaultExtensions::withImportsTab( array( 'setup', 'xrowextract', 'bccie_overview', 'newsletter' ) ) );
        $this->assertSame( array( 'setup', 'sqliimport', 'newsletter' ),
                           expInstallDefaultExtensions::withImportsTab( array( 'setup', 'newsletter' ) ) );
        $this->assertSame( array( 'content', 'setup', 'sqliimport' ),
                           expInstallDefaultExtensions::withImportsTab( array( 'content', 'setup' ) ) );
        $tabs = array( 'content', 'sqliimport', 'xrowextract' );
        $this->assertSame( $tabs, expInstallDefaultExtensions::withImportsTab( $tabs ) );
    }

    public function testRolesThatGrantTheSecondStep()
    {
        $this->assertTrue( expInstallDefaultExtensions::grants( array( array( '*', '*' ) ), 'setup' ) );
        $this->assertTrue( expInstallDefaultExtensions::grants( array( array( 'user2fa', '*' ) ), 'verify' ) );
        $this->assertTrue( expInstallDefaultExtensions::grants( array( array( 'user2fa', 'setup' ) ), 'setup' ) );
        $this->assertFalse( expInstallDefaultExtensions::grants( array( array( 'user2fa', 'setup' ) ), 'verify' ) );
        $this->assertFalse( expInstallDefaultExtensions::grants( array( array( 'user', '*' ) ), 'setup' ) );
    }
}
