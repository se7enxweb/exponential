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
        $this->assertSame( 'ezp', $generator->fieldDefault( 'database_init', 'Database' ) );
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
