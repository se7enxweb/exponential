<?php
/**
 * Steps of the setup wizard as the wizard drives them (processPostData(), init(), display()): the administrator,
 * the site access, the e-mail settings, the database choice, security, finetuning and the final page. Each step is
 * given posted variables, a kickstart file of the test's own or none, and a template that records what it is given.
 *
 * No database, no mail; no step here is asked to make a password (that writes var/log/initial-admin-password).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/eZSetupStepTestHelper.php';

#[\PHPUnit\Framework\Attributes\Group('kernel')]
class eZSetupStepsTest extends PHPUnit\Framework\TestCase
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
        // what this PHP has decides whether SAP HANA and MonetDB are offered; the tests say it themselves
        eZStepDatabaseChoice::$HANAAvailable = false;
        eZStepDatabaseChoice::$MonetDBAvailable = false;
    }

    protected function tearDown(): void
    {
        eZStepDatabaseChoice::$HANAAvailable = null;
        eZStepDatabaseChoice::$MonetDBAvailable = null;
        $this->helper->restore();
    }

    private function step( $class, array $persistence = array(), array $kickstart = array() )
    {
        return $this->helper->step( function ( $tpl, $http, $ini, &$list ) use ( $class ) {
            return new $class( $tpl, $http, $ini, $list );
        }, $persistence, $kickstart );
    }

    private function variable( $name )
    {
        return $this->helper->tpl->variable( $name );
    }

    // ---------------------------------------------------------------- site admin

    public static function weakPasswordProvider()
    {
        return array( array( '', true ), array( '   ', true ), array( null, true ), array( array( 'x' ), true ), array( 'publish', true ),
                      array( ' Admin ', true ), array( 'ChangeMe', true ), array( 'k1e-Strong-Passphrase-9', false ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('weakPasswordProvider')]
    public function testWeakDefaultPasswords( $password, $weak )
    {
        $this->assertSame( $weak, eZStepSiteAdmin::isWeakDefaultPassword( $password ) );
    }

    private function postAdmin( array $values )
    {
        $_POST = array();
        foreach ( $values as $name => $value )
            $_POST['eZSetup_site_templates_' . $name] = $value;
    }

    public static function adminFormProvider()
    {
        $good = array( 'first_name' => 'Ada', 'last_name' => 'Example', 'email' => 'ada@k1e.example.invalid',
                       'password1' => 'k1e-Strong-Passphrase-9', 'password2' => 'k1e-Strong-Passphrase-9' );
        return array(
            'good' => array( $good, array() ),
            'no first name' => array( array( 'first_name' => ' ' ) + $good, array( eZStepSiteAdmin::FIRST_NAME_MISSING ) ),
            'no last name' => array( array( 'last_name' => '' ) + $good, array( eZStepSiteAdmin::LAST_NAME_MISSING ) ),
            'no email' => array( array( 'email' => '' ) + $good, array( eZStepSiteAdmin::EMAIL_MISSING ) ),
            'bad email' => array( array( 'email' => 'ada at example' ) + $good, array( eZStepSiteAdmin::EMAIL_INVALID ) ),
            'no password' => array( array( 'password1' => '', 'password2' => '' ) + $good, array( eZStepSiteAdmin::PASSWORD_MISSING ) ),
            'passwords differ' => array( array( 'password2' => 'other' ) + $good, array( eZStepSiteAdmin::PASSWORD_MISSMATCH ) ),
            // long enough, but a variant of a well-known word (base word + ending, built here, not written out)
            'well-known variant' => array( array( 'password1' => 'publish' . 'ing', 'password2' => 'publish' . 'ing' ) + $good, array( eZStepSiteAdmin::PASSWORD_TOO_COMMON ) ),
            'well-known with a year' => array( array( 'password1' => 'Administrator' . '2026', 'password2' => 'Administrator' . '2026' ) + $good, array( eZStepSiteAdmin::PASSWORD_TOO_COMMON ) ),
            'nothing posted' => array( array(), array( eZStepSiteAdmin::FIRST_NAME_MISSING, eZStepSiteAdmin::LAST_NAME_MISSING, eZStepSiteAdmin::EMAIL_MISSING, eZStepSiteAdmin::PASSWORD_MISSING ) ),
            'arrays posted' => array( array( 'first_name' => array( 'x' ), 'last_name' => array(), 'email' => array( 'a' ), 'password1' => array( 'p' ), 'password2' => array( 'p' ) ),
                                      array( eZStepSiteAdmin::FIRST_NAME_MISSING, eZStepSiteAdmin::LAST_NAME_MISSING, eZStepSiteAdmin::EMAIL_MISSING, eZStepSiteAdmin::PASSWORD_MISSING ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('adminFormProvider')]
    public function testAdministratorForm( array $posted, array $errors )
    {
        $this->postAdmin( $posted );
        $step = $this->step( 'eZStepSiteAdmin' );
        $this->assertSame( $errors === array(), $step->processPostData() );
        $this->assertSame( $errors, $step->Error );
        $admin = $step->PersistenceList['admin'];
        if ( $errors === array() )
        {
            $this->assertSame( array( 'first_name' => 'Ada', 'last_name' => 'Example', 'email' => 'ada@k1e.example.invalid', 'password' => 'k1e-Strong-Passphrase-9' ), $admin );
        }
        else
            $this->assertIsString( $admin['password'] );

        $result = $step->display();
        $this->assertSame( '[design:setup/init/site_admin.tpl]', $result['content'] );
        $this->assertSame( $errors !== array(), $this->variable( 'has_errors' ) );
        $flags = array( eZStepSiteAdmin::FIRST_NAME_MISSING => 'first_name_missing', eZStepSiteAdmin::LAST_NAME_MISSING => 'last_name_missing',
                        eZStepSiteAdmin::EMAIL_MISSING => 'email_missing', eZStepSiteAdmin::EMAIL_INVALID => 'email_invalid',
                        eZStepSiteAdmin::PASSWORD_MISSMATCH => 'password_missmatch', eZStepSiteAdmin::PASSWORD_MISSING => 'password_missing',
                        eZStepSiteAdmin::PASSWORD_TOO_COMMON => 'password_too_common' );
        foreach ( $flags as $code => $flag )
            $this->assertSame( isset( $errors[0] ) && $errors[0] === $code ? 1 : 0, $this->variable( $flag ), $flag );
    }

    public function testAdministratorFromAKickstartFile()
    {
        $step = $this->step( 'eZStepSiteAdmin', array(), array( 'site_admin' => array( 'FirstName' => 'Kick', 'LastName' => 'Start',
                                                                                        'Email' => 'kick@k1e.example.invalid', 'Password' => 'k1e-Strong-Passphrase-9', 'Continue' => 'true' ) ) );
        $this->assertTrue( $step->init() );
        $this->assertSame( array( 'first_name' => 'Kick', 'last_name' => 'Start', 'email' => 'kick@k1e.example.invalid', 'password' => 'k1e-Strong-Passphrase-9' ), $step->PersistenceList['admin'] );

        // a password generated earlier in the same setup is kept rather than made again
        $again = $this->step( 'eZStepSiteAdmin', array( 'admin' => array( 'password' => 'k1e-Generated-Before-1' ) ), array( 'site_admin' => array( 'Password' => 'publish' ) ) );
        $this->assertFalse( $again->init() );
        $this->assertSame( 'k1e-Generated-Before-1', $again->PersistenceList['admin']['password'] );
        $this->assertSame( 'Administrator', $again->PersistenceList['admin']['first_name'] );
    }

    public function testAdministratorWithoutKickstart()
    {
        $step = $this->step( 'eZStepSiteAdmin' );
        $this->assertFalse( $step->init() );
        $this->assertSame( array( 'first_name' => 'Administrator', 'last_name' => 'User', 'email' => false, 'password' => false ), $step->PersistenceList['admin'] );
        $kept = $this->step( 'eZStepSiteAdmin', array( 'admin' => array( 'first_name' => 'Ada' ) ) );
        $kept->init();
        $this->assertSame( array( 'first_name' => 'Ada' ), $kept->PersistenceList['admin'] );
        // keeping the data already in the database: nothing to ask
        $keep = $this->step( 'eZStepSiteAdmin', array( 'chosen_site_package' => array( 'k1e_site' ), 'site_extra_data_existing_database' => array( 'k1e_site' => eZStepInstaller::DB_DATA_KEEP ) ) );
        $this->assertTrue( $keep->init() );
    }

    // ---------------------------------------------------------------- site access

    public function testSiteAccessChoice()
    {
        $persistence = array( 'chosen_site_package' => array( 'k1e_site' ) );
        $step = $this->step( 'eZStepSiteAccess', $persistence );
        $this->assertFalse( $step->processPostData(), 'nothing posted' );

        foreach ( array( 'url' => array( 'k1e_site', 'k1e_site_admin', 'editor' ), 'port' => array( 8080, 8081, 8082 ) ) as $type => $values )
        {
            $_POST = array( 'eZSetup_site_access' => $type );
            $step = $this->step( 'eZStepSiteAccess', $persistence );
            $this->assertTrue( $step->processPostData() );
            $siteType = $step->chosenSiteType();
            $this->assertSame( $type, $siteType['access_type'] );
            $this->assertSame( $values, array( $siteType['access_type_value'], $siteType['admin_access_type_value'], eZStepSiteAccess::defaultEditorAccessValue( $type ) ) );
            $this->assertSame( $values[0], $step->PersistenceList['site_extra_data_access_type_value']['k1e_site'] );
        }

        $siteType = array( 'identifier' => 'k1e_site', 'access_type' => 'hostname' );
        $step->setAccessValues( $siteType );
        $host = eZSys::hostName();
        $this->assertSame( "k1e_site.$host", $siteType['access_type_value'] );
        $this->assertSame( "k1e_site-admin.$host", $siteType['admin_access_type_value'] );
        $this->assertSame( "edit.$host", $siteType['editor_access_type_value'] );
        $other = array( 'identifier' => 'k1e_site', 'access_type' => 'other' );
        $step->setAccessValues( $other );
        $this->assertSame( array( 'other', 'other_admin', 'other_editor' ), array( $other['access_type_value'], $other['admin_access_type_value'], $other['editor_access_type_value'] ) );
    }

    public function testSiteAccessFromAKickstartFileAndByDefault()
    {
        $persistence = array( 'chosen_site_package' => array( 'k1e_site' ) );
        $step = $this->step( 'eZStepSiteAccess', $persistence, array( 'site_access' => array( 'Access' => 'port', 'Continue' => 'true' ) ) );
        $this->assertTrue( $step->init() );
        $this->assertSame( 'port', $step->chosenSiteType()['access_type'] );
        $this->assertSame( 8080, $step->chosenSiteType()['access_type_value'] );

        $plain = $this->step( 'eZStepSiteAccess', $persistence );
        $this->assertSame( eZSetupTestInstaller() == 'windows', $plain->init() );
        $this->assertSame( 'url', $plain->chosenSiteType()['access_type'] );
        $result = $plain->display();
        $this->assertSame( '[design:setup/init/site_access.tpl]', $result['content'] );
        $this->assertSame( 'k1e_site', $this->variable( 'site_type' )['identifier'] );
    }

    // ---------------------------------------------------------------- e-mail settings

    public function testEmailTransportChoice()
    {
        $_POST = array( 'eZSetupEmailTransport' => '2', 'eZSetupSMTPServer' => 'smtp.k1e.example.invalid', 'eZSetupSMTPUser' => 'u', 'eZSetupSMTPPassword' => 'p' );
        $step = $this->step( 'eZStepEmailSettings' );
        $this->assertTrue( $step->processPostData() );
        $this->assertSame( array( 'type' => '2', 'result' => false, 'server' => 'smtp.k1e.example.invalid', 'user' => 'u', 'password' => 'p' ), $step->PersistenceList['email_info'] );

        $_POST = array( 'eZSetupEmailTransport' => '1', 'eZSetupSMTPServer' => 'ignored' );
        $sendmail = $this->step( 'eZStepEmailSettings' );
        $sendmail->processPostData();
        $this->assertSame( array( 'type' => '1', 'result' => false ), $sendmail->PersistenceList['email_info'] );
        $this->assertFalse( $sendmail->init(), 'always shown without a kickstart file' );
    }

    public function testEmailFromAKickstartFile()
    {
        $step = $this->step( 'eZStepEmailSettings', array(), array( 'email_settings' => array( 'Type' => 'smtp', 'Server' => 'smtp.k1e.example.invalid', 'User' => 'u', 'Password' => 'p', 'Continue' => 'true' ) ) );
        $this->assertTrue( $step->init() );
        $this->assertSame( 2, $step->PersistenceList['email_info']['type'] );
        $this->assertSame( 'smtp.k1e.example.invalid', $step->PersistenceList['email_info']['server'] );
        $mta = $this->step( 'eZStepEmailSettings', array(), array( 'email_settings' => array( 'Type' => 'mta' ) ) );
        $this->assertFalse( $mta->init() );
        $this->assertSame( eZSys::filesystemType() == 'win32' ? 2 : 1, $mta->PersistenceList['email_info']['type'] );
    }

    public function testEmailPageShowsWhatWasTypedOrElseTheConfiguredServer()
    {
        $this->helper->injectSite( array( 'MailSettings' => array( 'TransportServer' => 'configured.k1e.example.invalid', 'TransportUser' => 'configured-user', 'TransportPassword' => 'configured-password' ) ) );
        $typed = $this->step( 'eZStepEmailSettings', array( 'email_info' => array( 'type' => 2, 'server' => 'typed.k1e.example.invalid', 'user' => 'typed-user', 'password' => 'typed-password' ) ) );
        $result = $typed->display();
        $this->assertSame( '[design:setup/init/email_settings.tpl]', $result['content'] );
        $info = $this->variable( 'email_info' );
        $this->assertSame( array( 'typed.k1e.example.invalid', 'typed-user', 'typed-password' ), array( $info['server'], $info['user'], $info['password'] ) );

        $empty = $this->step( 'eZStepEmailSettings', array( 'email_info' => array( 'type' => 2 ) ) );
        $empty->display();
        $info = $this->variable( 'email_info' );
        $this->assertSame( array( 'configured.k1e.example.invalid', 'configured-user', 'configured-password' ), array( $info['server'], $info['user'], $info['password'] ) );
        $this->assertSame( array( 'type' => eZSys::filesystemType() ), $this->variable( 'system' ) );
    }

    // ---------------------------------------------------------------- database choice

    public function testDatabaseChoice()
    {
        $found = array( 'database_extensions' => array( 'found' => array( 'mysqli', 'sqlite3', 'nonsense', 'mysqli' ) ) );
        $step = $this->step( 'eZStepDatabaseChoice', $found );
        $this->assertSame( array( 'mysqli', 'sqlite3' ), $step->foundDatabaseTypes() );
        $preferred = eZStepDatabaseChoice::preferredDatabaseType();
        $this->assertArrayHasKey( $preferred, eZSetupDatabaseMap() );
        $this->assertSame( array( 'type' => $preferred, 'preferred_missing' => false ), $step->defaultChoice( array( 'mysqli', $preferred ) ) );
        $this->assertSame( array( 'type' => null, 'preferred_missing' => true ), $step->defaultChoice( array() ) );
        if ( $preferred !== 'pgsql' )
            $this->assertSame( array( 'type' => 'pgsql', 'preferred_missing' => true ), $step->defaultChoice( array( 'pgsql' ) ) );

        $_POST = array( 'eZSetupDatabaseType' => 'mysqli' );
        $this->assertTrue( $step->processPostData() );
        $this->assertSame( 'mysqli', $step->PersistenceList['database_info']['type'] );
        $_POST = array( 'eZSetupDatabaseType' => array( 'x' ) );
        $this->assertTrue( $step->processPostData() );
        $this->assertSame( $step->defaultChoice( array( 'mysqli', 'sqlite3' ) )['type'], $step->PersistenceList['database_info']['type'] );
        $_POST = array();
        $none = $this->step( 'eZStepDatabaseChoice' );
        $this->assertFalse( $none->processPostData(), 'no database extension at all' );

        $this->assertFalse( $step->init(), 'two to choose from' );
        $single = $this->step( 'eZStepDatabaseChoice', array( 'database_extensions' => array( 'found' => array( 'pgsql' ) ) ) );
        $this->assertTrue( $single->init(), 'one to choose from: chosen' );
        $this->assertSame( 'pgsql', $single->PersistenceList['database_info']['type'] );
    }

    public static function kickstartDatabaseProvider()
    {
        return array( array( 'postgresql', 'pgsql' ), array( 'mysql', 'mysqli' ), array( 'sqlite', 'sqlite3' ), array( 'oracle', 'oci8' ), array( 'ezoracle', 'oci8' ), array( 'mongodb', 'mongodb' ),
                      // the databases of the PostgreSQL wire protocol
                      array( 'cockroachdb', 'cockroachdb' ), array( 'crdb', 'cockroachdb' ), array( 'yugabyte', 'yugabytedb' ), array( 'neon', 'neon' ),
                      array( 'supabase', 'supabase' ), array( 'aurora-postgresql', 'aurorapostgresql' ),
                      // SQL Server, DuckDB and the databases of the MySQL protocol
                      array( 'sqlserver', 'sqlserver' ), array( 'sqlsrv', 'sqlserver' ), array( 'duckdb', 'duckdb' ), array( 'tidb', 'tidb' ),
                      array( 'planetscale', 'vitess' ), array( 'aurora-mysql', 'aurora' ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('kickstartDatabaseProvider')]
    public function testDatabaseChoiceFromAKickstartFile( $given, $type )
    {
        $step = $this->step( 'eZStepDatabaseChoice', array(), array( 'database_choice' => array( 'Type' => $given, 'Continue' => 'true' ) ) );
        $this->assertTrue( $step->init() );
        $this->assertSame( $type, $step->PersistenceList['database_info']['type'] );
    }

    public function testPostgreSQLWireDatabases()
    {
        $map = eZSetupDatabaseMap();
        foreach ( array( 'cockroachdb', 'yugabytedb', 'neon', 'supabase', 'aurorapostgresql' ) as $type )
        {
            $this->assertSame( $type, $map[$type]['type'] );
            $this->assertSame( $type, $map[$type]['driver'], 'the site.ini alias of its driver' );
            $this->assertSame( 'pgsql', eZSetupDatabaseFamily( $type ), "$type is PostgreSQL to the installer" );
        }
        $this->assertArrayNotHasKey( 'spanner', $map, 'Spanner holds no site' );
        $this->assertSame( 'pgsql', eZSetupDatabaseFamily( 'pgsql' ) );
        $this->assertSame( 'mysqli', eZSetupDatabaseFamily( 'mysqli' ) );

        // listed after PostgreSQL when pgsql is there, never chosen on their own
        $step = $this->step( 'eZStepDatabaseChoice', array( 'database_extensions' => array( 'found' => array( 'sqlite3', 'pgsql' ) ) ) );
        $this->assertSame( array( 'sqlite3', 'pgsql' ), $step->foundDatabaseTypes() );
        $step->display();
        $list = array_column( $this->variable( 'database_list' ), 'type' );
        $this->assertSame( array( 'pgsql', 'cockroachdb', 'yugabytedb', 'neon', 'supabase', 'aurorapostgresql' ), array_slice( $list, array_search( 'pgsql', $list ), 6 ) );
        $_POST = array( 'eZSetupDatabaseType' => 'cockroachdb' );
        $this->assertTrue( $step->processPostData() );
        $this->assertSame( 'cockroachdb', $step->PersistenceList['database_info']['driver'] );
        $_POST = array();
    }

    public function testDatabaseChoicePageListsTheDefaultFirst()
    {
        $step = $this->step( 'eZStepDatabaseChoice', array( 'database_extensions' => array( 'found' => array( 'mongodb', 'mysqli', 'sqlite3' ) ),
                                                            'database_info' => array( 'type' => 'mysqli', 'name' => 'chosen before' ) ) );
        $result = $step->display();
        $this->assertSame( '[design:setup/init/database_choice.tpl]', $result['content'] );
        $map = eZSetupDatabaseMap();
        // the databases of the MySQL protocol come with mysqli; the order of the found ones is what is checked here
        $list = array_values( array_filter( array_column( $this->variable( 'database_list' ), 'type' ), function ( $type ) use ( $map ) {
            return !isset( $map[$type]['php_extension'] );
        } ) );
        $this->assertSame( $step->defaultChoice( array( 'mongodb', 'mysqli', 'sqlite3' ) )['type'], $list[0] );
        $this->assertSame( 'chosen before', $this->variable( 'database_info' )['name'] );
        $this->assertSame( array_values( array_diff( array( 'mongodb', 'mysqli', 'sqlite3' ), array( $list[0] ) ) ), array_slice( $list, 1 ), 'the rest keep their order' );
        $available = $this->variable( 'available_databases' );
        ksort( $available );
        $this->assertSame( array( 'aurora' => true, 'mongodb' => true, 'mysqli' => true, 'singlestore' => true, 'sqlite3' => true, 'tidb' => true,
                                  'vitess' => true, 'xpand' => true ), $available );
    }

    public function testMySQLWireDatabasesComeWithMysqli()
    {
        $step = $this->step( 'eZStepDatabaseChoice', array( 'database_extensions' => array( 'found' => array( 'sqlite3', 'mysqli' ) ) ) );
        $this->assertSame( array( 'sqlite3', 'mysqli' ), $step->foundDatabaseTypes(), 'never chosen on their own' );
        $step->display();
        $list = array_column( $this->variable( 'database_list' ), 'type' );
        $this->assertSame( array( 'sqlite3', 'mysqli', 'tidb', 'vitess', 'singlestore', 'xpand', 'aurora' ), array_slice( $list, 0, 7 ),
                           'the established databases first, the MySQL protocol ones below them' );
        foreach ( array( 'tidb', 'vitess', 'singlestore', 'xpand', 'aurora' ) as $type )
            $this->assertTrue( eZSetupIsMySQLFamily( $type ), $type );
        $_POST = array( 'eZSetupDatabaseType' => 'tidb' );
        $this->assertTrue( $step->processPostData() );
        $this->assertSame( 'tidb', $step->PersistenceList['database_info']['driver'] );
        $_POST = array();
    }

    public function testDatabasesAddedLaterComeBelowOracle()
    {
        $step = $this->step( 'eZStepDatabaseChoice', array( 'database_extensions' => array( 'found' => array( 'sqlsrv', 'oci8', 'pgsql', 'mysqli' ) ) ) );
        $step->display();
        $list = array_column( $this->variable( 'database_list' ), 'type' );
        $first = array_slice( $list, 0, 3 );
        sort( $first );
        $this->assertSame( array( 'mysqli', 'oci8', 'pgsql' ), $first, 'the established databases first' );
        $this->assertNotSame( 'sqlserver', $step->defaultChoice( $step->foundDatabaseTypes() )['type'], 'without SQLite, an established database is the default' );
        $this->assertSame( 'sqlserver', $list[3], 'SQL Server comes right below the established databases' );
        $oracle = array_search( 'oci8', $list );
        foreach ( array( 'tidb', 'aurora', 'cockroachdb', 'aurorapostgresql', 'sqlserver' ) as $type )
            $this->assertGreaterThan( $oracle, array_search( $type, $list ), "$type is below Oracle" );
        $step = $this->step( 'eZStepDatabaseChoice', array( 'database_extensions' => array( 'found' => array( 'pdo_firebird', 'pdo_odbc', 'sqlsrv', 'oci8', 'mysqli' ) ) ) );
        $step->display();
        $list = array_column( $this->variable( 'database_list' ), 'type' );
        $next = array_values( array_intersect( $list, array( 'sqlserver', 'odbc_sqlserver', 'firebird', 'duckdb' ) ) );
        $this->assertSame( array_values( array_intersect( array( 'sqlserver', 'odbc_sqlserver', 'firebird', 'duckdb' ), $next ) ), $next, 'SQL Server, SQL Server (ODBC), Firebird, DuckDB in that order' );
        $this->assertSame( $next, array_slice( $list, 2, count( $next ) ), 'right below Oracle' );
        $this->assertGreaterThan( array_search( 'firebird', $list ), array_search( 'tidb', $list ), 'the rest after them' );
    }

    /**
     * SAP HANA: offered with PDO_ODBC or the odbc extension when SAP's HANA ODBC driver is registered, after the
     * dialects of the generic ODBC driver, among the databases below Oracle; never without an ODBC layer.
     */
    public function testSAPHANAIsOfferedWhenItsODBCDriverIsRegistered()
    {
        foreach ( array( 'odbc', 'pdo_odbc' ) as $layer )
        {
            $step = $this->step( 'eZStepDatabaseChoice', array( 'database_extensions' => array( 'found' => array( 'sqlite3', $layer ) ) ) );
            $this->assertNotContains( 'hana', $step->foundDatabaseTypes(), 'no HANA ODBC driver: not offered' );

            eZStepDatabaseChoice::$HANAAvailable = true;
            $types = $step->foundDatabaseTypes();
            $this->assertContains( 'hana', $types, $layer );
            $this->assertSame( 'hana', end( $types ), 'after the ODBC dialects' );
            $step->display();
            $list = array_column( $this->variable( 'database_list' ), 'type' );
            $this->assertContains( 'hana', $list );
            $this->assertGreaterThan( array_search( 'sqlite3', $list ), array_search( 'hana', $list ), 'below the established databases' );
            $this->assertTrue( $this->variable( 'available_databases' )['hana'] );
            $_POST = array( 'eZSetupDatabaseType' => 'hana' );
            $this->assertTrue( $step->processPostData() );
            $this->assertSame( array( 'hana', 'hana' ), array( $step->PersistenceList['database_info']['type'], $step->PersistenceList['database_info']['driver'] ) );
            $_POST = array();
            eZStepDatabaseChoice::$HANAAvailable = false;
        }

        eZStepDatabaseChoice::$HANAAvailable = true;
        $step = $this->step( 'eZStepDatabaseChoice', array( 'database_extensions' => array( 'found' => array( 'sqlite3', 'pgsql' ) ) ) );
        $this->assertNotContains( 'hana', $step->foundDatabaseTypes(), 'without an ODBC layer of PHP: not offered' );
        $this->assertSame( (bool)eZStepDatabaseChoice::$HANAAvailable, eZStepDatabaseChoice::hanaAvailable() );
    }

    /**
     * MonetDB: no PHP extension, so it is listed last whenever PHP can speak its protocol, never chosen on its own (one
     * database extension still skips the page), and chosen like any database when posted.
     */
    public function testMonetDBIsOfferedWithoutAnExtension()
    {
        $step = $this->step( 'eZStepDatabaseChoice', array( 'database_extensions' => array( 'found' => array( 'sqlite3', 'pgsql' ) ) ) );
        $step->display();
        $this->assertNotContains( 'monetdb', array_column( $this->variable( 'database_list' ), 'type' ), 'PHP cannot speak MAPI: not offered' );
        $this->assertSame( array(), eZStepDatabaseChoice::typesWithoutExtension() );

        eZStepDatabaseChoice::$MonetDBAvailable = true;
        $this->assertSame( array( 'monetdb' ), eZStepDatabaseChoice::typesWithoutExtension() );
        $this->assertSame( array( 'sqlite3', 'pgsql' ), $step->foundDatabaseTypes(), 'not found by an extension' );
        $step->display();
        $list = array_column( $this->variable( 'database_list' ), 'type' );
        $this->assertSame( 'monetdb', end( $list ), 'listed last' );
        $this->assertTrue( $this->variable( 'available_databases' )['monetdb'] );
        $_POST = array( 'eZSetupDatabaseType' => 'monetdb' );
        $this->assertTrue( $step->processPostData() );
        $this->assertSame( array( 'monetdb', 'monetdb' ), array( $step->PersistenceList['database_info']['type'], $step->PersistenceList['database_info']['driver'] ) );
        $_POST = array();

        $single = $this->step( 'eZStepDatabaseChoice', array( 'database_extensions' => array( 'found' => array( 'pgsql' ) ) ) );
        $this->assertTrue( $single->init(), 'one database extension: still chosen without the page' );
        $this->assertSame( 'pgsql', $single->PersistenceList['database_info']['type'] );
    }

    /** The database page's defaults for SAP HANA (the tenant's SQL port) and MonetDB (monetdbd's port and user) */
    public function testDatabasePageDefaultsOfSAPHANAAndMonetDB()
    {
        foreach ( array( 'hana' => array( '39041', '' ), 'monetdb' => array( '50000', 'monetdb' ) ) as $type => $expected )
        {
            $step = $this->step( 'eZStepDatabaseInit', array( 'database_info' => array( 'type' => $type ) ) );
            $this->assertFalse( $step->init(), 'the database page is always shown' );
            $info = $step->PersistenceList['database_info'];
            $this->assertSame( $expected, array( (string)$info['port'], (string)$info['user'] ), $type );
            $this->assertNotSame( '', (string)$info['server'], $type );
            $this->assertNotSame( '', (string)$info['dbname'], $type );
        }
    }

    public static function databaseExtensionProvider()
    {
        return array( array( array( 'sqlsrv' ), true, array( 'sqlserver' ) ),
                      array( array( 'pdo_sqlsrv', 'sqlsrv' ), true, array( 'sqlserver' ) ),
                      array( array( 'ffi' ), true, array( 'duckdb' ) ),
                      // ffi.enable=preload in the web server, or off
                      array( array( 'ffi' ), false, array() ),
                      array( array( 'pdo_firebird' ), true, array( 'firebird' ) ),
                      array( array( 'pdo_ibm' ), true, array( 'db2', 'informix' ) ),
                      array( array( 'oci8', 'FFI', 'pgsql' ), true, array( 'oci8', 'duckdb', 'pgsql' ) ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('databaseExtensionProvider')]
    public function testTheExtensionOfAKernelDriverGivesItsDatabase( array $found, $ffiUsable, array $types )
    {
        eZStepDatabaseChoice::$FFIUsable = $ffiUsable;
        try
        {
            $step = $this->step( 'eZStepDatabaseChoice', array( 'database_extensions' => array( 'found' => $found ) ) );
            $this->assertSame( $types, $step->foundDatabaseTypes() );
            if ( count( $types ) === 1 )
            {
                $this->assertTrue( $step->init(), 'one to choose from: chosen' );
                $this->assertSame( $types[0], $step->PersistenceList['database_info']['type'] );
            }
        }
        finally
        {
            eZStepDatabaseChoice::$FFIUsable = null;
        }
    }

    /**
     * Every database the wizard can install a site on is offered when PHP has its extension: setup.ini
     * [database_extensions] names an extension that gives it (its own name, the kernel driver's extension, or the
     * extension of its protocol family), and the system check has a page for each extension it may miss.
     */
    public function testEveryDatabaseOfTheWizardHasItsExtensionChecked()
    {
        $extensions = eZSetupConfigVariableArray( 'database_extensions', 'Extensions' );
        $reachable = array();
        eZStepDatabaseChoice::$FFIUsable = true;
        try
        {
            foreach ( $extensions as $extension )
            {
                $this->assertFileExists( dirname( __DIR__, 5 ) . '/design/standard/templates/setup/db/' . $extension . '_info.tpl',
                                         "the system check explains how to get $extension" );
                $type = eZStepDatabaseChoice::typeOfExtension( $extension );
                $reachable[$type] = true;
                if ( in_array( $type, array( 'pdo_ibm', 'ibm_db2' ), true ) )
                    $reachable['db2'] = $reachable['informix'] = true;
                // PDO_ODBC and the odbc extension give a site over ODBC, one type per dialect
                if ( in_array( $type, array( 'pdo_odbc', 'odbc' ), true ) )
                    foreach ( array_keys( eZSetupODBCDatabaseMap() ) as $odbcType )
                        $reachable[$odbcType] = true;
            }
        }
        finally
        {
            eZStepDatabaseChoice::$FFIUsable = null;
        }
        // curl gives libSQL, and Turso is listed after it
        $this->assertSame( 'libsql', eZStepDatabaseChoice::typeOfExtension( 'curl' ) );
        // SAP HANA comes with odbc or pdo_odbc when its ODBC driver is registered, MonetDB needs no extension
        // (eZStepDatabaseChoice::typesWithoutExtension()); SAP ASE was never tested against a server and stays with
        // the kickstarter (exp:install --db=ase)
        $this->assertArrayHasKey( 'odbc_pgsql', $reachable );
        $reachable['hana'] = true;
        eZStepDatabaseChoice::$MonetDBAvailable = true;
        foreach ( eZStepDatabaseChoice::typesWithoutExtension() as $type )
            $reachable[$type] = true;
        $notInTheWizard = array( 'ase' );
        foreach ( eZSetupDatabaseMap() as $type => $entry )
        {
            if ( in_array( $type, $notInTheWizard, true ) )
                continue;
            $via = isset( $entry['php_extension'] ) ? $entry['php_extension'] : $type;
            $this->assertArrayHasKey( $via, $reachable, "$type is offered by the wizard when PHP has its extension" );
        }
    }

    /**
     * The libSQL and Turso fields of the database page: the server mode with a URL, TLS and a token that is kept only
     * sealed (never in the answers in clear text, never in the page), the checks of each field, Turso's token, and the
     * embedded file mode.
     */
    public function testLibSQLDatabasePage()
    {
        $post = function ( array $fields ) {
            $_POST = $fields + array( 'eZSetupLibSQLPage' => '1' );
        };
        $info = array( 'type' => 'libsql', 'server' => '127.0.0.1', 'port' => '8080', 'user' => 'root', 'password' => '',
                       'dbname' => 'exponential', 'socket' => '' );
        $post( array( 'eZSetupLibSQLMode' => 'remote', 'eZSetupLibSQLURL' => 'http://10.0.0.5:8080', 'eZSetupLibSQLToken' => 'tok-123' ) );
        $step = $this->step( 'eZStepDatabaseInit', array( 'database_info' => $info ) );
        $this->assertTrue( $step->readLibSQLFields() );
        $answers = $step->PersistenceList['database_info'];
        $this->assertSame( array( 'Mode' => 'remote', 'URL' => 'http://10.0.0.5:8080', 'TLS' => 'disabled' ), $answers['libsql'] );
        $this->assertSame( '', $answers['password'] );
        $this->assertSame( '', $answers['user'] );
        $this->assertStringNotContainsString( 'tok-123', serialize( $answers ) );
        $this->assertSame( 'tok-123', eZStepInstaller::databasePassword( $answers ) );
        $this->assertFalse( eZStepInstaller::libSQLEmbedded( $answers ) );

        // an empty token field keeps the token given before; TLS on
        $post( array( 'eZSetupLibSQLMode' => 'remote', 'eZSetupLibSQLTLS' => '1', 'eZSetupLibSQLToken' => '' ) );
        $step = $this->step( 'eZStepDatabaseInit', array( 'database_info' => $answers ) );
        $this->assertTrue( $step->readLibSQLFields() );
        $this->assertSame( 'enabled', $step->PersistenceList['database_info']['libsql']['TLS'] );
        $this->assertSame( 'tok-123', eZStepInstaller::databasePassword( $step->PersistenceList['database_info'] ) );

        // the checks
        foreach ( array( array( array( 'eZSetupLibSQLURL' => 'ftp://x' ), $info, 'url' ),
                         array( array(), array( 'server' => '' ) + $info, 'server' ),
                         array( array(), array( 'port' => '99999' ) + $info, 'port' ),
                         array( array( 'eZSetupLibSQLCAFile' => '/nonexistent/ca.pem' ), $info, 'cafile' ),
                         array( array( 'eZSetupLibSQLCAFile' => 'relative.pem' ), $info, 'cafile' ),
                         array( array(), array( 'type' => 'turso', 'server' => 'exp-acme.turso.io', 'port' => '443' ) + $info, 'token' ) ) as $case )
        {
            list( $fields, $answersBefore, $problem ) = $case;
            $post( $fields + array( 'eZSetupLibSQLMode' => 'remote' ) );
            $step = $this->step( 'eZStepDatabaseInit', array( 'database_info' => $answersBefore ) );
            $this->assertFalse( $step->readLibSQLFields(), $problem );
            $this->assertSame( $problem, $step->PersistenceList['database_info']['libsql_problem'] );
        }

        // Turso: always remote and TLS, whatever is posted
        $post( array( 'eZSetupLibSQLMode' => 'file', 'eZSetupLibSQLURL' => 'libsql://exp-acme.turso.io', 'eZSetupLibSQLToken' => 't' ) );
        $step = $this->step( 'eZStepDatabaseInit', array( 'database_info' => array( 'type' => 'turso' ) + $info ) );
        $this->assertTrue( $step->readLibSQLFields() );
        $this->assertSame( array( 'Mode' => 'remote', 'URL' => 'libsql://exp-acme.turso.io', 'TLS' => 'enabled' ), $step->PersistenceList['database_info']['libsql'] );

        // the embedded file mode: a file, no server, no token
        $post( array( 'eZSetupLibSQLMode' => 'file', 'eZSetupLibSQLFile' => 'site-libsql.db' ) );
        $step = $this->step( 'eZStepDatabaseInit', array( 'database_info' => $answers ) );
        $this->assertSame( class_exists( 'SQLite3' ), $step->readLibSQLFields() );
        $file = $step->PersistenceList['database_info'];
        $this->assertSame( array( 'Mode' => 'file' ), $file['libsql'] );
        $this->assertSame( 'site-libsql.db', $file['dbname'] );
        $this->assertSame( '', $file['server'] );
        $this->assertArrayNotHasKey( 'libsql_token_sealed', $file );
        $this->assertTrue( eZStepInstaller::libSQLEmbedded( $file ) );
    }

    /** expSetupSecret: sealed and opened with the same key and purpose only; the sealed text is not the value */
    public function testSetupSecret()
    {
        $key = str_repeat( 'k', 32 );
        $sealed = expSetupSecret::seal( 'the token', 'purpose', $key );
        $this->assertIsString( $sealed );
        $this->assertStringNotContainsString( 'the token', $sealed );
        $this->assertSame( 'the token', expSetupSecret::unseal( $sealed, 'purpose', $key ) );
        $this->assertNull( expSetupSecret::unseal( $sealed, 'another purpose', $key ) );
        $this->assertNull( expSetupSecret::unseal( $sealed, 'purpose', str_repeat( 'x', 32 ) ) );
        $this->assertNull( expSetupSecret::unseal( substr( $sealed, 0, -2 ) . 'AA', 'purpose', $key ) );
        $this->assertNotSame( $sealed, expSetupSecret::seal( 'the token', 'purpose', $key ) );
    }
    // ---------------------------------------------------------------- security, finetune, final

    public function testSecurityStep()
    {
        $step = $this->step( 'eZStepSecurity' );
        $this->assertTrue( $step->processPostData() );
        $this->assertSame( file_exists( '.htaccess' ) || eZSys::indexFileName() == '', $step->init() );
        $kick = $this->step( 'eZStepSecurity', array(), array( 'security' => array( 'Continue' => 'true' ) ) );
        $this->assertTrue( $kick->init() );
        $result = $step->display();
        $this->assertSame( '[design:setup/init/security.tpl]', $result['content'] );
        $this->assertSame( realpath( '.' ), $this->variable( 'path' ) );
        $this->assertSame( 'Registration', $this->variable( 'setup_next_step' ) );
    }

    public function testFinetuneStep()
    {
        $_POST = array( 'eZSetup_finetune_button' => '1' );
        $step = $this->step( 'eZStepSystemFinetune' );
        $this->assertFalse( $step->processPostData() );
        $this->assertTrue( $step->PersistenceList['run_finetune'] );
        $_POST = array();
        $this->assertTrue( $step->processPostData() );
        $this->assertFalse( $step->PersistenceList['run_finetune'] );
        $this->assertTrue( $step->init(), 'nothing to run' );
        $fresh = $this->step( 'eZStepSystemFinetune' );
        $this->assertTrue( $fresh->init() );
        $this->assertFalse( $fresh->PersistenceList['run_finetune'] );
    }

    public function testFinalPageNamesTheNewSite()
    {
        $step = $this->step( 'eZStepFinal', array( 'chosen_site_package' => array( 'k1e_site' ), 'site_extra_data_url' => array( 'k1e_site' => 'k1e.example.invalid' ),
                                                  'site_extra_data_access_type' => array( 'k1e_site' => 'port' ),
                                                  'site_extra_data_access_type_value' => array( 'k1e_site' => 8080 ),
                                                  'site_extra_data_admin_access_type_value' => array( 'k1e_site' => 8081 ),
                                                  'final_text' => 'K1e done' ) );
        $this->assertTrue( $step->processPostData() );
        $step->display();
        $siteType = $this->variable( 'site_type' );
        $this->assertStringContainsString( ':8080', $siteType['url'] );
        $this->assertStringContainsString( ':8081', $siteType['admin_url'] );
        $this->assertSame( 'K1e done', $this->variable( 'custom_text' ) );
    }
}
