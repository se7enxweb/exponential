<?php
/**
 * "exp:kickstarter ini --yes" writes no credentials of an installed siteaccess (they name a live database), and
 * its default site package is one that exists: the one exp:install installs. Nothing is written.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class KickstarterIniDefaultsTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    private static function generator( array $args )
    {
        $root = self::root();
        return new expKickstarterIni( $root, $root . '/kickstart.ini-dist', $root . '/var/tmp/kickstart-ini-defaults-test.ini',
                                      array_merge( array( 'ini' ), $args ) );
    }

    /**
     * A generator on a root that holds one installed siteaccess naming a database, the way a live site does
     * (var/tmp/kickstart-ini-defaults-test/, made up values).
     */
    private static function generatorOnInstalledRoot( array $args )
    {
        $root = self::root() . '/var/tmp/kickstart-ini-defaults-test';
        $dir = $root . '/settings/siteaccess/livesite';
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0775, true );
        file_put_contents( $dir . '/site.ini.append.php', "<?php /* #?ini charset=\"utf-8\"?\n\n[DatabaseSettings]\n"
            . "Server=live-db.example.invalid\nDatabase=live_database\nUser=live_user\nPassword=made-up-live-password\n*/ ?>\n" );
        return new expKickstarterIni( $root, self::root() . '/kickstart.ini-dist', $root . '/kickstart.ini',
                                      array_merge( array( 'ini' ), $args ) );
    }

    public function testYesTakesNoCredentialsFromInstalledSiteaccesses()
    {
        $generator = self::generatorOnInstalledRoot( array( '--yes' ) );
        $this->assertSame( '', $generator->fieldDefault( 'database_init', 'Password' ) );
        $this->assertSame( 'root', $generator->fieldDefault( 'database_init', 'User' ) );
        $this->assertSame( 'localhost', $generator->fieldDefault( 'database_init', 'Server' ) );
        $this->assertSame( 'exponential.db', $generator->fieldDefault( 'database_init', 'Database' ) );
    }

    public function testDefaultDatabaseNameIsValidForTheDefaultType()
    {
        $generator = self::generator( array( '--yes' ) );
        $type = $generator->fieldDefault( 'database_choice', 'Type' );
        $this->assertSame( 'sqlite3', $type );
        foreach ( array( 'database_init', 'site_details' ) as $section )
            $this->assertMatchesRegularExpression( eZStepInstaller::SQLITE_FILE_NAME_REGEXP, $generator->fieldDefault( $section, 'Database' ),
                                                   "[$section] Database must be a file name SQLite accepts" );
        $this->assertSame( 'exponential.db', expKickstarterIni::defaultDatabaseName( 'sqlite' ) );
        $this->assertSame( 'exponential', expKickstarterIni::defaultDatabaseName( 'mysqli' ) );
        $this->assertSame( 'exponential', expKickstarterIni::defaultDatabaseName( 'pgsql' ) );
        $this->assertSame( 'localhost:1521/FREEPDB1', expKickstarterIni::defaultDatabaseName( 'oci8' ) );
    }

    public function testYesWritesAnInstallingActionMarksTheReviewLinesAndModeOwnerOnly()
    {
        $root = self::root() . '/var/tmp/kickstart-ini-defaults-test-write';
        if ( !is_dir( $root ) )
            mkdir( $root, 0775, true );
        $file = $root . '/kickstart.ini';
        // a file left with a wider mode by an earlier version is narrowed too
        file_put_contents( $file, "[site_details]\nTitle=Kept title\n" );
        chmod( $file, 0644 );

        $generator = new expKickstarterIni( $root, self::root() . '/kickstart.ini-dist', $file, array( 'ini', '--yes' ) );
        $this->assertSame( $file, $generator->writeNonInteractive() );

        clearstatcache();
        $this->assertSame( '600', sprintf( '%o', fileperms( $file ) & 0777 ), 'the file holds passwords' );
        $content = (string)file_get_contents( $file );
        $this->assertStringContainsString( "\nDatabaseAction=ignore\n", $content, 'an action that installs and drops nothing' );
        $this->assertStringNotContainsString( 'DatabaseAction=skip', $content );
        $this->assertStringNotContainsString( 'DatabaseAction=remove', $content );
        $this->assertSame( 2, substr_count( $content, "\nDatabase=exponential.db\n" ) );
        $this->assertStringNotContainsString( 'Database=ezp', $content );
        $this->assertMatchesRegularExpression( '/# REVIEW: [^\n]+\nDatabaseAction=ignore\n/', $content );
        $this->assertMatchesRegularExpression( '/# REVIEW: [^\n]+\nURL=/', $content );
        $this->assertStringContainsString( 'Title=My Exponential Site', $content, '--yes applies its defaults over an existing file' );
        foreach ( explode( "\n", $content ) as $line )
            $this->assertDoesNotMatchRegularExpression( '/^;/', $line, 'comments start with #, which eZINI skips' );
    }

    public function testFromInstalledTakesTheDatabaseButNeverThePassword()
    {
        $generator = self::generatorOnInstalledRoot( array( '--yes', '--from-installed' ) );
        $this->assertSame( 'live_database', $generator->fieldDefault( 'database_init', 'Database' ) );
        $this->assertSame( 'live_user', $generator->fieldDefault( 'database_init', 'User' ) );
        $this->assertSame( '', $generator->fieldDefault( 'database_init', 'Password' ) );
    }

    public function testDefaultPackageIsTheOneExpInstallInstalls()
    {
        $package = self::generator( array( '--yes' ) )->fieldDefault( 'site_types', 'Site_package' );
        $this->assertNotSame( 'sevenx_site', $package, 'no such package exists' );
        $this->assertMatchesRegularExpression( "/'package'\\s*=>\\s*'" . preg_quote( $package, '/' ) . "'/",
                                               file_get_contents( self::root() . '/bin/php/install.php' ) );
        $packages = glob( self::root() . '/var/storage/packages/*', GLOB_ONLYDIR );
        if ( $packages )
            $this->assertNotEmpty( glob( self::root() . '/var/storage/packages/*/' . $package, GLOB_ONLYDIR ),
                                   'the default package is in var/storage/packages' );
    }

    public function testHelpDoesNotCallIniTheDefaultCommand()
    {
        $source = file_get_contents( self::root() . '/kernel/private/classes/commands/kickstarter.php' );
        $this->assertDoesNotMatchRegularExpression( '/ini\s+Interactive kickstart\.ini generator \(default\)/', $source );
    }
}
