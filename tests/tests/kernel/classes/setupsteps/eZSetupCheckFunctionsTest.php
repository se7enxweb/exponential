<?php
/**
 * The system checks of the setup wizard (kernel/setup/ezsetuptests.php) and its persistence helpers
 * (kernel/setup/ezsetupcommon.php): the table of checks, running a list of them (unknown and ignored ones), version
 * comparison, the PHP version, extension and function checks against settings given to them, directory and file
 * permission checks on directories made under var/tmp, the time zone check, and how the wizard carries what it was
 * told from one step to the next in posted variables.
 *
 * No database. The upload check is not run here: it writes a file into the system's temporary directory.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once dirname( __DIR__ ) . '/radwizards/expRadWizardTestHelper.php';

class eZSetupCheckFunctionsTest extends PHPUnit\Framework\TestCase
{
    private $injected;
    private $scratch;
    private $post;
    private $timezone;

    public static function setUpBeforeClass(): void
    {
        expRadWizardTestHelper::boot();
        require_once 'kernel/setup/ezsetupcommon.php';
        require_once 'kernel/setup/ezsetuptests.php';
    }

    protected function setUp(): void
    {
        expRadWizardTestHelper::boot();
        $this->injected = expRadWizardTestINI::injected();
        $this->post = $_POST;
        $_POST = array();
        $this->timezone = date_default_timezone_get();
    }

    protected function tearDown(): void
    {
        eZINI::injectSettings( $this->injected );
        $_POST = $this->post;
        date_default_timezone_set( $this->timezone );
        if ( $this->scratch )
            expRadWizardTestHelper::removeTree( $this->scratch );
    }

    private function setupIni( array $blocks )
    {
        expRadWizardTestHelper::injectIni( 'setup.ini', $blocks );
    }

    public function testEveryCheckOfTheTableExists()
    {
        foreach ( eZSetupTestTable() as $name => $check )
            $this->assertTrue( function_exists( $check[0] ), "$name: {$check[0]}" );
        $this->assertSame( 1, EZ_SETUP_TEST_SUCCESS );
        $this->assertSame( 2, EZ_SETUP_TEST_FAILURE );
    }

    public static function versionProvider()
    {
        return array(
            array( '8.1.2', '8.1.2', 0 ), array( '8.1.10', '8.1.9', 1 ), array( '8.0', '8.1', -1 ), array( '8.1.0', '8.1', 0 ),
            array( '10.0', '9.9.9', 1 ), array( '8.1.2RC1', '8.1.2', 0 ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('versionProvider')]
    public function testVersionCompare( $a, $b, $expected )
    {
        $this->assertSame( $expected, eZSetupPrvtVersionCompare( explode( '.', $a ), explode( '.', $b ) ) );
    }

    public function testPhpVersionCheck()
    {
        $this->setupIni( array( 'k1e_php' => array( 'MinimumVersion' => '7.0.0', 'UnstableVersions' => '' ) ) );
        $result = eZSetupTestPhpVersion( 'k1e_php' );
        $this->assertTrue( $result['result'] );
        $this->assertSame( phpversion(), $result['current_version'] );
        $this->assertFalse( $result['warning_version'] );

        $this->setupIni( array( 'k1e_php' => array( 'MinimumVersion' => '99.0', 'UnstableVersions' => '' ) ) );
        $this->assertFalse( eZSetupTestPhpVersion( 'k1e_php' )['result'] );

        $this->setupIni( array( 'k1e_php' => array( 'MinimumVersion' => '7.0.0', 'UnstableVersions' => '5.0;' . phpversion() ) ) );
        $unstable = eZSetupTestPhpVersion( 'k1e_php' );
        $this->assertFalse( $unstable['result'] );
        $this->assertTrue( $unstable['warning_version'] );

        $this->assertTrue( eZSetupTestPhpVersion( 'phpversion' )['result'], 'this PHP passes the shipped minimum' );
    }

    public function testExtensionAndFunctionChecks()
    {
        $this->setupIni( array( 'k1e_all' => array( 'Extensions' => 'json;k1e_no_such_extension', 'Require' => 'all' ),
                             'k1e_one' => array( 'Extensions' => 'json;k1e_no_such_extension', 'Require' => 'one' ),
                             'k1e_none' => array( 'Extensions' => 'k1e_no_such_extension', 'Require' => 'one' ),
                             'k1e_fn' => array( 'Functions' => 'STRLEN;k1e_no_such_function', 'Require' => 'one' ),
                             'k1e_fn_all' => array( 'Functions' => 'strlen;k1e_no_such_function', 'Require' => 'all' ) ) );
        $all = eZSetupTestExtension( 'k1e_all' );
        $this->assertFalse( $all['result'] );
        $this->assertSame( array( 'json' ), $all['found_extensions'] );
        $this->assertSame( array( 'k1e_no_such_extension' ), $all['failed_extensions'] );
        $this->assertTrue( eZSetupTestExtension( 'k1e_one' )['result'] );
        $this->assertFalse( eZSetupTestExtension( 'k1e_none' )['result'] );
        $functions = eZSetupTestFunctionExists( 'k1e_fn' );
        $this->assertTrue( $functions['result'] );
        $this->assertSame( array( 'strlen' ), $functions['found_extensions'] );
        $this->assertFalse( eZSetupTestFunctionExists( 'k1e_fn_all' )['result'] );
    }

    public function testRunningAListOfChecks()
    {
        $this->setupIni( array( 'k1e_php' => array( 'MinimumVersion' => '7.0', 'UnstableVersions' => '' ) ) );
        $persistence = array( 'imagemagick_program' => array( 'extra_path' => '/opt/k1e/bin' ) );
        $run = eZSetupRunTests( array( 'phpversion', 'k1e_not_a_check', 'variables_order', 'timezone' ), 'k1e', $persistence );
        $this->assertSame( array( 'phpversion', 'variables_order', 'timezone' ), array_column( $run['results'], 1 ) );
        $this->assertSame( '/opt/k1e/bin', $GLOBALS['eZSetupCheckExecutable_imagemagick_program_ExtraPath'] );
        unset( $GLOBALS['eZSetupCheckExecutable_imagemagick_program_ExtraPath'] );
        $this->assertSame( 'phpversion', $run['persistence_list'][0][0] );
        $this->assertSame( count( array_filter( $run['results'], function ( $r ) { return $r[0] === EZ_SETUP_TEST_SUCCESS; } ) ), $run['success_count'] );

        // a check the person chose to ignore is not run at all
        $_POST = array( 'phpversion_Ignore' => '1', 'timezone_Ignore' => '0' );
        $none = null;
        $run = eZSetupRunTests( array( 'phpversion', 'timezone' ), 'k1e', $none );
        $this->assertSame( array( 'timezone' ), array_column( $run['results'], 1 ) );
    }

    public function testTimeZoneCheck()
    {
        date_default_timezone_set( 'Europe/Berlin' );
        $this->assertTrue( eZSetupTestTimeZone( null )['result'] );
        date_default_timezone_set( 'UTC' );
        $this->assertSame( trim( (string)ini_get( 'date.timezone' ) ) !== '', eZSetupTestTimeZone( null )['result'] );
    }

    public function testDirectoryPermissionsCreateWhatIsMissing()
    {
        $this->scratch = expRadWizardTestHelper::scratch( 'setup-dirs' );
        $relative = substr( $this->scratch, strlen( expRadWizardTestHelper::root() ) + 1 );
        touch( $this->scratch . '/a-file' );
        mkdir( $this->scratch . '/existing' );
        $this->setupIni( array( 'k1e_dirs' => array( 'CheckList' => "$relative/existing;$relative/made;$relative/made/here;$relative/a-file;$relative/not/made" ) ) );
        $result = eZSetupTestDirectoryPermissions( 'k1e_dirs' );
        $this->assertDirectoryExists( $this->scratch . '/made/here' );
        $codes = array_column( $result['result_elements'], 'result', 'file' );
        // a directory is made only where its parent is (the shipped list names every parent first)
        $this->assertSame( array( "$relative/existing" => 1, "$relative/made" => 1, "$relative/made/here" => 1, "$relative/a-file" => 4, "$relative/not/made" => 2 ), $codes );
        $this->assertDirectoryDoesNotExist( $this->scratch . '/not' );
        $this->assertFalse( $result['result'], 'a file where a directory belongs fails the check' );
        $this->assertArrayHasKey( 4, $result['result_elements_by_error_code'] );
        $this->assertSame( realpath( '.' ), $result['current_path'] );
        $this->assertTrue( eZSetupPrvtAreDirAndFilesWritable( $this->scratch ) );
    }

    public function testFilePermissionsSayWhatWasCheckedForEachFile()
    {
        $this->scratch = expRadWizardTestHelper::scratch( 'setup-files' );
        $relative = substr( $this->scratch, strlen( expRadWizardTestHelper::root() ) + 1 );
        touch( $this->scratch . '/site.ini.append.php' );
        $this->setupIni( array( 'k1e_files' => array( 'CheckList' => "$relative;$relative/site.ini.append.php;$relative/missing.ini" ) ) );
        $result = eZSetupTestFilePermissions( 'k1e_files' );
        $this->assertTrue( $result['result'] );
        $elements = $result['result_elements'];
        $this->assertSame( array( $relative, "$relative/site.ini.append.php", "$relative/missing.ini" ), array_column( $elements, 'file' ) );
        $ini = eZINI::instance();
        $this->assertSame( $ini->variable( 'FileSettings', 'StorageDirPermissions' ), $elements[0]['permission'] ?? null, 'a directory is checked with the directory permissions' );
        $this->assertSame( $ini->variable( 'FileSettings', 'StorageFilePermissions' ), $elements[1]['permission'] ?? null, 'a file with the file permissions' );
        $this->assertArrayNotHasKey( 'permission', $elements[2], 'a missing file is not checked' );
        // the error template prints a chmod command for every element whose result is not true
        $this->assertSame( array( true, true, true ), array_column( $elements, 'result' ) );
    }

    public function testPersistenceIsCarriedInPostedVariables()
    {
        eZSetupSetPersistencePostVariable( 'database_info', array( 'type' => 'sqlite3', 'server' => '' ) );
        eZSetupSetPersistencePostVariable( 'title', 'K1e site' );
        $this->assertSame( array( 'P_database_info-type' => 'sqlite3', 'P_database_info-server' => '', 'P_title-0' => 'K1e site' ), $_POST );
        $_POST['P_bad name-x'] = 'ignored';
        $_POST['unrelated'] = 'ignored';
        $this->assertSame( array( 'database_info' => array( 'type' => 'sqlite3', 'server' => '' ), 'title' => array( 'K1e site' ) ), eZSetupFetchPersistenceList() );
    }

    public function testMergingWhatTheChecksFound()
    {
        $list = array( 'php_session' => array( 'found' => array( 'session' ), 'checked' => array( 'session' ) ) );
        eZSetupMergePersistenceList( $list, array(
            array( 'php_session', array( 'found' => array( 'value' => array( 'json' ), 'merge' => false ),
                                         'checked' => array( 'value' => array( 'session', 'json' ), 'merge' => true, 'unique' => true ),
                                         'result' => array( 'value' => true ) ) ),
            array( 'phpversion', array( 'found' => array( 'value' => '8.5' ) ) ),
        ) );
        $this->assertSame( array( 'json' ), $list['php_session']['found'] );
        $this->assertSame( array( 'session', 'json' ), array_values( $list['php_session']['checked'] ) );
        $this->assertTrue( $list['php_session']['result'] );
        $this->assertSame( array( 'found' => '8.5' ), $list['phpversion'] );
    }

    public function testDatabaseMap()
    {
        $map = eZSetupDatabaseMap();
        $this->assertSame( array( 'mysqli', 'pgsql', 'sqlite3', 'mongodb', 'oci8' ), array_keys( $map ) );
        foreach ( $map as $type => $info )
        {
            $this->assertSame( $type, $info['type'] );
            foreach ( array( 'driver', 'name', 'required_version', 'has_demo_data', 'supports_unicode' ) as $key )
                $this->assertArrayHasKey( $key, $info, "$type $key" );
        }
        $this->assertSame( 'ezoracle', $map['oci8']['extension'] );
        $this->assertTrue( eZSetupActivateDatabaseExtension( $map['mysqli'] ) );
        $this->assertTrue( eZSetupActivateDatabaseExtension( 'not an array' ) );
        $this->assertIsArray( eZSetupCriticalTests() );
        $this->assertIsArray( eZSetupOptionalTests() );
    }
}
