<?php
/**
 * A site installed over ODBC (DatabaseImplementation=odbc), without a database: the setup wizard's types, the
 * kickstarter's Type=odbc with its dialect, the parameters and settings the steps carry, and install.php's options.
 *
 *  OSI-01 - eZSetupDatabaseMap(): odbc_sqlserver, odbc_pgsql, odbc_mysql, odbc_db2 with driver odbc and the family of
 *           their database, so every step treats them as that database
 *  OSI-02 - eZSetupODBCType(): the wizard type of a dialect and of its other names; none for SQLite, Oracle, Access
 *  OSI-03 - eZSetupODBCOptions(): [ODBCSettings] from kickstart.ini (Dialect from the type, ODBCDriver, DataSourceName,
 *           ConnectionString, ODBCLayer), empty values left out; nothing for a type that is not ODBC
 *  OSI-04 - eZSetupODBCParameters(): the 'odbc' parameters of eZDB::instance(), the dialect always first
 *  OSI-05 - install.php knows --db=odbc, its dialects and options
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('odbc')]
class expODBCSiteInstallTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        require_once 'kernel/setup/ezsetupcommon.php';
    }

    /** OSI-01 */
    public function testWizardTypes()
    {
        $map = eZSetupDatabaseMap();
        $families = array( 'odbc_sqlserver' => 'sqlserver', 'odbc_pgsql' => 'pgsql', 'odbc_mysql' => 'mysqli', 'odbc_db2' => 'db2' );
        foreach ( $families as $type => $family )
        {
            $this->assertArrayHasKey( $type, $map );
            $this->assertSame( $type, $map[$type]['type'] );
            $this->assertSame( 'odbc', $map[$type]['driver'] );
            $this->assertSame( $family, eZSetupDatabaseFamily( $type ) );
            $this->assertContains( $map[$type]['odbc_dialect'], expODBCDB::SITE_DIALECTS );
        }
        $this->assertTrue( eZSetupIsMySQLFamily( 'odbc_mysql' ) );
        $this->assertFalse( eZSetupIsMySQLFamily( 'odbc_pgsql' ) );
    }

    /** OSI-02 */
    public function testODBCType()
    {
        $this->assertSame( 'odbc_sqlserver', eZSetupODBCType( 'sqlserver' ) );
        $this->assertSame( 'odbc_sqlserver', eZSetupODBCType( 'MSSQL' ) );
        $this->assertSame( 'odbc_pgsql', eZSetupODBCType( 'postgresql' ) );
        $this->assertSame( 'odbc_pgsql', eZSetupODBCType( 'pgsql' ) );
        $this->assertSame( 'odbc_mysql', eZSetupODBCType( 'mariadb' ) );
        $this->assertSame( 'odbc_db2', eZSetupODBCType( 'db2' ) );
        foreach ( array( 'sqlite', 'oracle', 'msaccess', 'generic', '' ) as $dialect )
            $this->assertNull( eZSetupODBCType( $dialect ), $dialect );
    }

    /** OSI-03 */
    public function testODBCOptions()
    {
        $this->assertSame( array( 'Dialect' => 'postgresql', 'Driver' => 'PostgreSQL Unicode', 'ConnectionString' => 'SSLmode=require' ),
                           eZSetupODBCOptions( 'odbc_pgsql', array( 'ODBCDriver' => ' PostgreSQL Unicode ', 'DataSourceName' => '',
                                                                    'ConnectionString' => 'SSLmode=require' ) ) );
        $this->assertSame( array( 'Dialect' => 'mysql', 'DataSourceName' => 'exp-mariadb', 'Layer' => 'odbc' ),
                           eZSetupODBCOptions( 'odbc_mysql', array( 'DSN' => 'exp-mariadb', 'ODBCLayer' => 'odbc' ) ) );
        $this->assertSame( array( 'Dialect' => 'sqlserver' ), eZSetupODBCOptions( 'odbc_sqlserver', array() ) );
        $this->assertSame( array(), eZSetupODBCOptions( 'pgsql', array( 'ODBCDriver' => 'x' ) ) );
    }

    /** OSI-04 */
    public function testODBCParameters()
    {
        $this->assertSame( array( 'Driver' => 'MariaDB Unicode', 'Dialect' => 'mysql' ),
                           eZSetupODBCParameters( array( 'type' => 'odbc_mysql', 'odbc' => array( 'Driver' => 'MariaDB Unicode', 'Dialect' => 'postgresql' ) ) ) );
        $this->assertSame( array( 'Dialect' => 'sqlserver' ), eZSetupODBCParameters( array( 'type' => 'odbc_sqlserver' ) ) );
        $this->assertSame( array(), eZSetupODBCParameters( array( 'type' => 'sqlserver', 'odbc' => array( 'Driver' => 'x' ) ) ) );
        $this->assertSame( array(), eZSetupODBCParameters( array() ) );
    }

    /** OSI-05 */
    public function testInstallerKnowsODBC()
    {
        $source = (string)file_get_contents( 'bin/php/install.php' );
        $this->assertMatchesRegularExpression( "/'odbc'\s*=> array\( 'type' => 'odbc'/", $source );
        foreach ( array( "'db-dialect'", "'db-odbc-driver'", "'db-dsn'", "'db-connection-string'", "'Dialect' => \$odbcDialect" ) as $needle )
            $this->assertStringContainsString( $needle, $source );
        foreach ( expODBCDB::SITE_DIALECTS as $dialect )
            $this->assertMatchesRegularExpression( "/'$dialect'\s*=> array\( 'port'/", $source );
    }
}
?>
