<?php
/**
 * The base of the setup wizard's steps (eZStepInstaller): what a step makes of a kickstart file, the data it keeps
 * for the chosen site type, the character set it picks for the chosen languages, the checks of a SQLite database
 * file, the error messages for each database problem, and the addresses of the new site's siteaccesses.
 *
 * No database. The kickstart file each test needs is written under var/tmp and handed to the step in place of the
 * installation's own (which is put back), so what is in the installation's kickstart.ini does not matter.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/eZSetupStepTestHelper.php';

class eZStepInstallerTest extends PHPUnit\Framework\TestCase
{
    /** @var eZSetupStepTestHelper */
    private $helper;

    public static function setUpBeforeClass(): void
    {
        eZSetupStepTestHelper::bootOnce();
    }

    protected function setUp(): void
    {
        $this->helper = new eZSetupStepTestHelper();
    }

    protected function tearDown(): void
    {
        $this->helper->restore();
    }

    private function installer( array $persistence = array(), array $kickstart = array(), $identifier = 'k1e_step' )
    {
        return $this->helper->step( function ( $tpl, $http, $ini, &$list ) use ( $identifier ) {
            return new eZStepInstaller( $tpl, $http, $ini, $list, $identifier, 'K1e step' );
        }, $persistence, $kickstart );
    }

    // ---------------------------------------------------------------- kickstart

    public function testWithoutKickstartDataTheStepAsks()
    {
        $step = $this->installer();
        $this->assertFalse( $step->kickstartData() );
        $this->assertFalse( $step->hasKickstartData() );
        $this->assertTrue( $step->PersistenceList['use_kickstart']['k1e_step'] );
        $this->assertFalse( $step->kickstartContinueNextStep() );
        $this->assertNull( $step->processPostData() );
        $this->assertNull( $step->init() );
        $this->assertSame( array(), $step->display() );
    }

    public function testKickstartDataOfTheStep()
    {
        $step = $this->installer( array(), array( 'k1e_step' => array( 'Continue' => 'true', 'Value' => 'x' ), 'other_step' => array( 'Value' => 'y' ) ) );
        $this->assertSame( array( 'Continue' => 'true', 'Value' => 'x' ), $step->kickstartData() );
        $this->assertTrue( $step->hasKickstartData() );
        $this->assertTrue( $step->PersistenceList['kickstart']['k1e_step'] );
        $this->assertTrue( $step->kickstartContinueNextStep() );

        $paused = $this->installer( array(), array( 'k1e_step' => array( 'Continue' => 'false' ) ) );
        $this->assertFalse( $paused->kickstartContinueNextStep() );
    }

    public function testAStepReadOnceIsNotKickstartedAgain()
    {
        // the step was already shown with kickstart data: the person's answers count now
        $step = $this->installer( array( 'kickstart' => array( 'k1e_step' => true ) ), array( 'k1e_step' => array( 'Value' => 'x' ) ) );
        $this->assertFalse( $step->PersistenceList['use_kickstart']['k1e_step'] );
        $this->assertFalse( $step->isKickstartAllowed() );
        $this->assertFalse( $step->hasKickstartData() );
    }

    public function testKickstartCanBeSwitchedOffForTheRequest()
    {
        $step = $this->installer( array(), array( 'k1e_step' => array( 'Value' => 'x' ) ) );
        $step->setAllowKickstart( false );
        $this->assertFalse( $step->isKickstartAllowed() );
        $this->assertFalse( $step->hasKickstartData() );
        $step->setAllowKickstart( true );
        $this->assertTrue( $step->hasKickstartData() );
    }

    // ---------------------------------------------------------------- site type data

    public function testExtraSiteData()
    {
        $step = $this->installer();
        $this->assertFalse( $step->chosenSitePackage() );
        $this->assertFalse( $step->extraData( 'title' ) );
        $this->assertFalse( $step->extraSiteData( 'k1e_site', 'title' ) );
        $step->storeExtraSiteData( 'k1e_site', 'title', 'K1e Site' );
        $step->storeExtraSiteData( 'k1e_site', 'url', 'https://k1e.example.invalid' );
        $this->assertSame( array( 'k1e_site' => 'K1e Site' ), $step->extraData( 'title' ) );
        $this->assertSame( 'https://k1e.example.invalid', $step->extraSiteData( 'k1e_site', 'url' ) );
        $this->assertContains( 'access_type', $step->extraDataList() );
    }

    public function testStoringAndReadingTheChosenSiteType()
    {
        $step = $this->installer();
        $step->storeSiteType( array( 'identifier' => 'k1e_site', 'title' => 'K1e', 'access_type' => 'url', 'not_extra' => 'dropped' ) );
        $this->assertSame( 'k1e_site', $step->chosenSitePackage() );
        $this->assertSame( array( 'identifier' => 'k1e_site', 'title' => 'K1e', 'access_type' => 'url' ), $step->chosenSiteType() );
        $this->assertSame( array(), $_POST, 'nothing is posted on without kickstart data' );

        $kickstarted = $this->installer( array(), array( 'k1e_step' => array( 'Value' => 'x' ) ) );
        $kickstarted->storeSiteType( array( 'identifier' => 'k1e_site', 'title' => 'K1e' ) );
        $this->assertSame( 'K1e', $_POST['P_site_extra_data_title-k1e_site'] );
        $this->assertSame( 'k1e_site', $_POST['P_chosen_site_package-0'] );
    }

    // ---------------------------------------------------------------- character sets

    public function testCharsetOfTheChosenLanguages()
    {
        $step = $this->installer();
        $english = eZLocale::create( 'eng-GB' );
        $german = eZLocale::create( 'ger-DE' );
        $charset = $step->findAppropriateCharset( $english, array( $english, $german ), true );
        $common = array_intersect( array_map( array( 'eZCharsetInfo', 'realCharsetCode' ), $english->allowedCharsets() ),
                                   array_map( array( 'eZCharsetInfo', 'realCharsetCode' ), $german->allowedCharsets() ) );
        $this->assertContains( $charset, $common, 'one both languages allow' );
        // the list offered starts with the charset that would be chosen
        $this->assertSame( $charset, array_values( $step->findAppropriateCharsetsList( $english, array( $english, $german ), true ) )[0] );
        $this->assertSame( eZCharsetInfo::realCharsetCode( 'utf-8' ), $step->findAppropriateCharset( $english, array(), true ) );
        $this->assertFalse( $step->findAppropriateCharset( $english, null, false ) );

        $list = $step->findAppropriateCharsetsList( $english, array( $english, $german ), true );
        $this->assertSame( array(), array_values( array_diff( $list, $common ) ), 'only charsets both allow' );
        $this->assertSame( array( eZCharsetInfo::realCharsetCode( 'utf-8' ) ), $step->findAppropriateCharsetsList( $english, array(), true ) );
        $this->assertSame( array(), $step->findAppropriateCharsetsList( $english, array(), false ) );
    }

    // ---------------------------------------------------------------- SQLite database file

    public static function sqliteNameProvider()
    {
        return array(
            'plain' => array( 'k1e.db', false ),
            'sqlite3' => array( 'k1e-site_2.sqlite3', false ),
            'no extension' => array( 'k1e', eZStepInstaller::DB_ERROR_SQLITE_FILE_NAME ),
            'served extension' => array( 'k1e.php', eZStepInstaller::DB_ERROR_SQLITE_FILE_NAME ),
            'directory' => array( 'sub/k1e.db', eZStepInstaller::DB_ERROR_SQLITE_FILE_NAME ),
            'leading dot' => array( '.k1e.db', eZStepInstaller::DB_ERROR_SQLITE_FILE_NAME ),
            'empty' => array( '   ', eZStepInstaller::DB_ERROR_SQLITE_FILE_NAME ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('sqliteNameProvider')]
    public function testSqliteFileNames( $name, $expected )
    {
        $step = $this->installer();
        $check = $step->checkSQLiteDatabaseFile( $name );
        if ( $expected === false && $check['error_code'] !== false )
            $this->assertContains( $check['error_code'], array( eZStepInstaller::DB_ERROR_SQLITE_DIRECTORY_NOT_WRITABLE, eZStepInstaller::DB_ERROR_SQLITE_NOT_A_DATABASE, eZStepInstaller::DB_ERROR_SQLITE_FILE_NOT_WRITABLE ), 'the name itself is fine' );
        else
            $this->assertSame( $expected, $check['error_code'] );
        $this->assertSame( 0, $check['tables'] );
        $this->assertSame( eZSQLite3DB::filePath( trim( $name ) ), $check['path'] );
    }

    public function testAnAbsoluteSqlitePathNeedsAKickstartFile()
    {
        $dir = $this->helper->scratch( 'sqlite' );
        $step = $this->installer();
        $this->assertSame( eZStepInstaller::DB_ERROR_SQLITE_FILE_NAME, $step->checkSQLiteDatabaseFile( $dir . '/k1e.db' )['error_code'] );

        $kickstarted = $this->installer( array(), array( 'k1e_step' => array( 'Value' => 'x' ) ) );
        $this->assertFalse( $kickstarted->checkSQLiteDatabaseFile( $dir . '/k1e.db' )['error_code'], 'a file still to be made' );
        $this->assertSame( eZStepInstaller::DB_ERROR_SQLITE_FILE_NAME, $kickstarted->checkSQLiteDatabaseFile( $dir . '/../x/k1e.db' )['error_code'] );
        $this->assertFalse( $kickstarted->checkSQLiteDatabaseFile( $dir . '/new/dir/k1e.db' )['error_code'], 'a directory the driver can make' );

        touch( $dir . '/empty.db' );
        $this->assertFalse( $kickstarted->checkSQLiteDatabaseFile( $dir . '/empty.db' )['error_code'], 'an empty file becomes the database' );
        file_put_contents( $dir . '/text.db', 'not a database at all' );
        $this->assertSame( eZStepInstaller::DB_ERROR_SQLITE_NOT_A_DATABASE, $kickstarted->checkSQLiteDatabaseFile( $dir . '/text.db' )['error_code'] );
        file_put_contents( $dir . '/real.db', "SQLite format 3\0" . str_repeat( "\0", 84 ) );
        $this->assertFalse( $kickstarted->checkSQLiteDatabaseFile( $dir . '/real.db' )['error_code'] );
        mkdir( $dir . '/folder.db' );
        $this->assertSame( eZStepInstaller::DB_ERROR_SQLITE_NOT_A_DATABASE, $kickstarted->checkSQLiteDatabaseFile( $dir . '/folder.db' )['error_code'] );
        touch( $dir . '/blocker' );
        $this->assertSame( eZStepInstaller::DB_ERROR_SQLITE_DIRECTORY_NOT_WRITABLE, $kickstarted->checkSQLiteDatabaseFile( $dir . '/blocker/k1e.db' )['error_code'], 'a file where the directory has to be' );
    }

    public function testSqliteLeavesNoServerFields()
    {
        $step = $this->installer( array( 'database_info' => array( 'type' => 'sqlite3', 'server' => 'db', 'port' => '3306', 'user' => 'u', 'password' => 'p', 'socket' => 's', 'database' => 'k1e.db' ) ) );
        $step->resetSQLiteServerFields();
        $this->assertSame( array( 'type' => 'sqlite3', 'server' => '', 'port' => '', 'user' => '', 'password' => '', 'socket' => '', 'database' => 'k1e.db' ),
                           $step->PersistenceList['database_info'] );
    }

    // ---------------------------------------------------------------- database errors

    public static function errorProvider()
    {
        $info = array( 'type' => 'mysqli', 'version' => '3.0', 'required_version' => '4.1.1', 'current_charset' => 'latin1', 'requested_charset' => 'utf-8',
                       'sqlite_file' => 'var/storage/sqlite3/k1e<b>.db' );
        return array(
            'file name' => array( eZStepInstaller::DB_ERROR_SQLITE_FILE_NAME, $info, 'var/storage/sqlite3' ),
            'directory' => array( eZStepInstaller::DB_ERROR_SQLITE_DIRECTORY_NOT_WRITABLE, $info, 'var/storage/sqlite3' ),
            'file' => array( eZStepInstaller::DB_ERROR_SQLITE_FILE_NOT_WRITABLE, $info, 'k1e&lt;b&gt;.db' ),
            'not a database' => array( eZStepInstaller::DB_ERROR_SQLITE_NOT_A_DATABASE, $info, 'k1e&lt;b&gt;.db' ),
            'connection mysql' => array( eZStepInstaller::DB_ERROR_CONNECTION_FAILED, $info, 'would not accept the connection' ),
            'connection pgsql' => array( eZStepInstaller::DB_ERROR_CONNECTION_FAILED, array( 'type' => 'pgsql' ) + $info, 'pg_hba.conf' ),
            'connection sqlite' => array( eZStepInstaller::DB_ERROR_CONNECTION_FAILED, array( 'type' => 'sqlite3' ) + $info, 'could not be opened' ),
            'passwords' => array( eZStepInstaller::DB_ERROR_NONMATCH_PASSWORD, $info, 'did not match' ),
            'not empty' => array( eZStepInstaller::DB_ERROR_NOT_EMPTY, $info, 'was not empty' ),
            'no databases' => array( eZStepInstaller::DB_ERROR_NO_DATABASES, $info, 'not got access to any databases' ),
            'no digest' => array( eZStepInstaller::DB_ERROR_NO_DIGEST_PROC, $info, 'pgcrypto' ),
            'version' => array( eZStepInstaller::DB_ERROR_VERSION_INVALID, $info, '4.1.1' ),
            'charset' => array( eZStepInstaller::DB_ERROR_CHARSET_DIFFERS, $info, '[latin1]' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('errorProvider')]
    public function testEachDatabaseErrorHasAMessageWithItsOwnNumber( $code, array $info, $inText )
    {
        $step = $this->installer();
        $error = $step->databaseErrorInfo( array( 'error_code' => $code, 'database_info' => $info, 'site_type' => array( 'database' => 'k1edb' ) ) );
        $this->assertIsArray( $error );
        $this->assertStringContainsString( $inText, $error['text'] );
        $this->assertSame( $code, $error['number'], 'the number names the error that happened' );
        $this->assertArrayHasKey( 'url', $error );
    }

    public function testNoErrorIsNoMessage()
    {
        $this->assertFalse( $this->installer()->databaseErrorInfo( array( 'error_code' => false ) ) );
        $this->assertFalse( $this->installer()->databaseErrorInfo( array( 'error_code' => 999 ) ) );
    }

    // ---------------------------------------------------------------- addresses of the new site

    public function testSiteaccessAddressesByUrl()
    {
        $this->helper->injectSite( array( 'SiteSettings' => array( 'DefaultAccess' => 'k1e_site' ) ) );
        $step = $this->installer();
        $step->storeSiteType( array( 'identifier' => 'k1e_site', 'url' => 'k1e.example.invalid', 'access_type' => 'url',
                                     'access_type_value' => 'ignored', 'admin_access_type_value' => 'k1e_site_admin' ) );
        $this->assertSame( array( 'url' => 'http://k1e.example.invalid/k1e_site', 'admin_url' => 'http://k1e.example.invalid/k1e_site_admin',
                                  'editor_url' => 'http://k1e.example.invalid/editor' ), $step->siteaccessURLs() );
    }

    public function testSiteaccessAddressesByHost()
    {
        $step = $this->installer();
        $step->storeSiteType( array( 'identifier' => 'k1e_site', 'url' => 'https://www.k1e.example.invalid', 'access_type' => 'hostname',
                                     'access_type_value' => 'www.k1e.example.invalid', 'admin_access_type_value' => 'https://admin.k1e.example.invalid' ) );
        $index = eZSys::indexDir( false );
        $this->assertSame( array( 'url' => 'http://www.k1e.example.invalid' . $index, 'admin_url' => 'https://admin.k1e.example.invalid' . $index,
                                  'editor_url' => 'http://edit.k1e.example.invalid' . $index ), $step->siteaccessURLs() );
    }

    public function testSiteaccessAddressesByPort()
    {
        $step = $this->installer();
        $step->storeSiteType( array( 'identifier' => 'k1e_site', 'url' => 'k1e.example.invalid', 'access_type' => 'port',
                                     'access_type_value' => 8080, 'admin_access_type_value' => 8081 ) );
        $urls = $step->siteaccessURLs();
        $this->assertStringContainsString( ':8080', $urls['url'] );
        $this->assertStringContainsString( ':8081', $urls['admin_url'] );
        $this->assertStringContainsString( ':8082', $urls['editor_url'] );
    }
}
