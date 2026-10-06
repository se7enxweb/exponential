<?php
/**
 * expVelocityConfigLayout, the Debian Apache style configuration tree of Velocity: where the tree and the state
 * live ([LayoutSettings] ConfDir, StateDir), the site name, the split of the generated configuration into
 * ports.conf, one file per module and the site file (lossless, paths and secrets kept in the site file), the
 * engine's merge and comparison, generated files and administrator-owned ones, the switches of disabled modules,
 * the engine's file order, envvars, describe() and a2ensite-style toggling.
 *
 * No server is started and nothing outside var/tmp is touched: the Velocity object is a stand-in with its own
 * settings, the tree is made under var/tmp and removed in tearDown(). apply() and the retirement of the old file
 * under /etc are not called.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class expVelocityConfigLayoutTestVelocity extends expVelocity
{
    public $settings = array();
    public $root;

    public function __construct( $root )
    {
        $this->root = $root;
    }

    public function layoutSetting( $variable, $default = null )
    {
        return array_key_exists( $variable, $this->settings ) ? $this->settings[$variable] : $default;
    }

    public function absolutePath( $path )
    {
        return $path !== '' && $path[0] === '/' ? $path : rtrim( $this->root . '/' . $path, '/' );
    }

    public function assets()
    {
        return array( 'certificate' => null, 'certificateKey' => null );
    }

    public function engineCtl()
    {
        return false;
    }
}

class expVelocityConfigLayoutTestLayout extends expVelocityConfigLayout
{
    public function generated( $file, array $content, $mode, $link = null )
    {
        $this->writeGenerated( $file, $content, $mode, $link );
    }

    public function once( $file, array $content )
    {
        $this->writeOnce( $file, $content );
    }
}

class expVelocityConfigLayoutTest extends PHPUnit\Framework\TestCase
{
    private $root;
    private $velocity;
    private $layout;
    private $siteURL = false;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->root = getcwd() . '/var/tmp/phpunit-k1b-velocity-layout-' . getmypid() . '-' . mt_rand();
        mkdir( $this->root, 0755, true );
        $this->velocity = new expVelocityConfigLayoutTestVelocity( $this->root );
        $this->velocity->settings = array( 'ConfDir' => $this->root . '/etc/vc', 'StateDir' => $this->root . '/lib/vc', 'SiteName' => 'k1.example.invalid' );
        $this->layout = new expVelocityConfigLayoutTestLayout( $this->velocity );
    }

    protected function tearDown(): void
    {
        if ( $this->siteURL !== false )
        {
            $ini = eZINI::instance( 'site.ini' );
            if ( $this->siteURL === null )
                $ini->removeSetting( 'SiteSettings', 'SiteURL' );
            else
                $ini->setVariable( 'SiteSettings', 'SiteURL', $this->siteURL[0] );
        }
        if ( is_dir( $this->root ) )
            eZDir::recursiveDelete( $this->root );
    }

    private function tree()
    {
        $dir = $this->layout->confDir();
        foreach ( array( 'mods-available', 'mods-enabled', 'conf-available', 'conf-enabled', 'sites-available', 'sites-enabled' ) as $sub )
            mkdir( "$dir/$sub", 0755, true );
        return $dir;
    }

    private static function config()
    {
        return array( 'Q' => array(
            'web' => array(
                'http2' => array( 'enabled' => true ),
                'static' => array( 'maxAge' => 3600, 'dir' => '/srv/k1/static' ),
                'cache' => array( 'enabled' => true, 'dir' => '/srv/k1/cache', 'ttl' => 60 ),
                'https' => array( 'cert' => '/srv/k1/cert.pem', 'key' => '/srv/k1/key.pem' ),
            ),
            'webserver' => array(
                'brandName' => 'Exponential', 'brandUrl' => 'https://k1.example.invalid/', 'maintainerMail' => 'ops@k1.example.invalid',
                'keepAlive' => 5, 'forkPerRequest' => false, 'spareWorkers' => 4, 'documentRoot' => '/srv/k1',
                'log' => array( 'level' => 'info', 'dir' => '/srv/k1/log' ),
            ),
            'dashboard' => array( 'enabled' => true, 'token' => 'k1-secret-token' ),
        ) );
    }

    // ---------------------------------------------------------------- where

    public function testConfDirFromTheSetting()
    {
        $this->assertSame( $this->root . '/etc/vc', $this->layout->confDir() );
        $this->velocity->settings['ConfDir'] = 'relative/etc/';
        $this->assertSame( $this->root . '/relative/etc', $this->layout->confDir() );
        foreach ( array( 'disabled', 'None', 'false', 'OFF' ) as $off )
        {
            $this->velocity->settings['ConfDir'] = $off;
            $this->assertNull( $this->layout->confDir(), $off );
            $this->assertNull( $this->layout->siteFile() );
        }
    }

    public function testStateDirFromTheSetting()
    {
        $this->assertSame( $this->root . '/lib/vc', $this->layout->stateDir() );
        $this->assertSame( $this->root . '/lib/vc/sites/k1.example.invalid.json', $this->layout->metadataFile() );
    }

    public function testStateDirInsideTheInstallationMovesTheOldOne()
    {
        $this->velocity->settings['StateDir'] = 'auto';
        $this->velocity->settings['ConfDir'] = $this->root . '/conf';
        mkdir( $this->root . '/var/vc/lib', 0755, true );
        file_put_contents( $this->root . '/var/vc/lib/kept.txt', 'x' );
        $this->assertSame( $this->root . '/var/vc/qbix/lib', $this->layout->stateDir() );
        $this->assertFileExists( $this->root . '/var/vc/qbix/lib/kept.txt' );
        $this->assertDirectoryDoesNotExist( $this->root . '/var/vc/lib' );
        $this->assertSame( array( 'moved var/vc/lib to var/vc/qbix/lib' ), $this->layout->actions );
    }

    public static function siteNameProvider()
    {
        return array(
            'given' => array( 'My Site', 'my-site' ),
            'host' => array( 'www.K1.example.invalid', 'www.k1.example.invalid' ),
            'odd characters' => array( '..a_b/c..', 'a-b-c' ),
            'nothing left' => array( '...', 'default' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('siteNameProvider')]
    public function testSiteName( $setting, $expected )
    {
        $this->velocity->settings['SiteName'] = $setting;
        $this->assertSame( $expected, $this->layout->siteName() );
    }

    public function testSiteNameFromSiteUrl()
    {
        $ini = eZINI::instance( 'site.ini' );
        $this->siteURL = $ini->hasVariable( 'SiteSettings', 'SiteURL' ) ? array( $ini->variable( 'SiteSettings', 'SiteURL' ) ) : null;
        $this->velocity->settings['SiteName'] = '';
        $ini->setVariable( 'SiteSettings', 'SiteURL', 'Shop.K1.example.invalid:8080/index.php' );
        $this->assertSame( 'shop.k1.example.invalid', $this->layout->siteName() );
        $ini->setVariable( 'SiteSettings', 'SiteURL', 'https://secure.k1.example.invalid/' );
        $this->assertSame( 'secure.k1.example.invalid', $this->layout->siteName() );
        $ini->setVariable( 'SiteSettings', 'SiteURL', '' );
        $this->assertSame( strtolower( basename( $this->root ) ), $this->layout->siteName() );
        $this->assertSame( $this->root . '/etc/vc/sites-available/' . $this->layout->siteName() . '.conf', $this->layout->siteFile( false ) );
    }

    // ---------------------------------------------------------------- split, merge, same

    public function testSplitIntoPortsModulesAndSite()
    {
        $parts = $this->layout->split( self::config(), array( 'http' => '8080', 'https' => 8443 ) );
        $this->assertSame( array( 'Q' => array( 'webserver' => array( 'port' => 8080 ), 'web' => array( 'https' => array( 'port' => 8443 ) ) ) ), $parts['ports'] );
        $this->assertSame( array( 'http2', 'static', 'cache', 'keepalive', 'brand', 'log', 'pool', 'dashboard' ), array_keys( $parts['mods'] ) );
        $this->assertSame( array( 'Q' => array( 'web' => array( 'cache' => array( 'enabled' => true, 'ttl' => 60 ) ) ) ), $parts['mods']['cache'] );
        $this->assertSame( array( 'brandName' => 'Exponential', 'brandUrl' => 'https://k1.example.invalid/', 'maintainerMail' => 'ops@k1.example.invalid' ),
                           $parts['mods']['brand']['Q']['webserver'] );
        $this->assertSame( array( 'forkPerRequest' => false, 'spareWorkers' => 4 ), $parts['mods']['pool']['Q']['webserver'] );
        $this->assertSame( array( 'enabled' => true ), $parts['mods']['dashboard']['Q']['dashboard'] );
        // This installation's paths and secrets stay in the site file
        $site = $parts['site']['Q'];
        $this->assertSame( array( 'dir' => '/srv/k1/static' ), $site['web']['static'] );
        $this->assertSame( array( 'dir' => '/srv/k1/cache' ), $site['web']['cache'] );
        $this->assertSame( array( 'cert' => '/srv/k1/cert.pem', 'key' => '/srv/k1/key.pem' ), $site['web']['https'] );
        $this->assertSame( array( 'token' => 'k1-secret-token' ), $site['dashboard'] );
        $this->assertSame( array( 'documentRoot' => '/srv/k1', 'log' => array( 'dir' => '/srv/k1/log' ) ), $site['webserver'] );
        $this->assertArrayNotHasKey( 'http2', $site['web'] );
    }

    public function testSplitIsLossless()
    {
        $ports = array( 'http' => 80, 'https' => null );
        $parts = $this->layout->split( self::config(), $ports );
        $this->assertArrayNotHasKey( 'web', $parts['ports']['Q'] );
        $merged = expVelocityConfigLayout::merge( array(), $parts['ports'] );
        foreach ( $parts['mods'] as $settings )
            $merged = expVelocityConfigLayout::merge( $merged, $settings );
        $merged = expVelocityConfigLayout::merge( $merged, $parts['site'] );
        $this->assertTrue( expVelocityConfigLayout::same( $merged, expVelocityConfigLayout::merge( self::config(), $parts['ports'] ) ) );
    }

    public function testModuleWithoutGeneratedSettingsGetsNoFile()
    {
        $parts = $this->layout->split( array( 'Q' => array( 'webserver' => array( 'keepAlive' => 5 ) ) ), array( 'http' => 80 ) );
        $this->assertSame( array( 'keepalive' => array( 'Q' => array( 'webserver' => array( 'keepAlive' => 5 ) ) ) ), $parts['mods'] );
    }

    public function testSplitOfAnEmptyConfiguration()
    {
        $parts = $this->layout->split( array(), array( 'http' => 80 ) );
        $this->assertSame( array(), $parts['mods'] );
        $this->assertSame( array(), $parts['site'] );
    }

    public function testMergeIsDeepAndOverlayWins()
    {
        $this->assertSame( array( 'a' => array( 'b' => 2, 'c' => 3 ), 'd' => 'x', 'e' => 1 ),
                           expVelocityConfigLayout::merge( array( 'a' => array( 'b' => 1, 'c' => 3 ), 'd' => array( 'old' ) ),
                                                          array( 'a' => array( 'b' => 2 ), 'd' => 'x', 'e' => 1 ) ) );
    }

    public function testSameIgnoresMapOrderAndTheMarker()
    {
        $this->assertTrue( expVelocityConfigLayout::same( array( 'a' => 1, 'b' => array( 'c' => 2, 'd' => 3 ) ),
                                                         array( '_generated' => 'exp:velocity', 'b' => array( 'd' => 3, 'c' => 2 ), 'a' => 1 ) ) );
        $this->assertFalse( expVelocityConfigLayout::same( array( 'a' => 1 ), array( 'a' => '1' ) ) );
        $this->assertFalse( expVelocityConfigLayout::same( array( 'a' => 1 ), array( 'a' => 1, 'b' => 2 ) ) );
        $this->assertFalse( expVelocityConfigLayout::same( array( 'a' => 1, 'c' => 2 ), array( 'a' => 1, 'b' => 2 ) ) );
        $this->assertTrue( expVelocityConfigLayout::same( 'x', 'x' ) );
        $this->assertFalse( expVelocityConfigLayout::same( array(), 'x' ) );
    }

    // ---------------------------------------------------------------- files

    public function testGeneratedFileIsWrittenEnabledAndUpdated()
    {
        $dir = $this->tree();
        $this->layout->generated( "$dir/mods-available/cache.conf", array( 'Q' => array( 'web' => array( 'cache' => array( 'ttl' => 60 ) ) ) ), 0644, "$dir/mods-enabled/cache.conf" );
        $this->assertSame( array( "created $dir/mods-available/cache.conf", "enabled $dir/mods-enabled/cache.conf" ), $this->layout->actions );
        $this->assertTrue( is_link( "$dir/mods-enabled/cache.conf" ) );
        $this->assertSame( '../mods-available/cache.conf', readlink( "$dir/mods-enabled/cache.conf" ) );
        $content = json_decode( file_get_contents( "$dir/mods-available/cache.conf" ), true );
        $this->assertSame( 'exp:velocity', $content['_generated'] );
        $this->assertSame( 60, $content['Q']['web']['cache']['ttl'] );

        $this->layout->actions = array();
        $this->layout->generated( "$dir/mods-available/cache.conf", array( 'Q' => array( 'web' => array( 'cache' => array( 'ttl' => 60 ) ) ) ), 0644, "$dir/mods-enabled/cache.conf" );
        $this->assertSame( array(), $this->layout->actions, 'unchanged content is not rewritten' );

        unlink( "$dir/mods-enabled/cache.conf" );
        $this->layout->generated( "$dir/mods-available/cache.conf", array( 'Q' => array( 'web' => array( 'cache' => array( 'ttl' => 90 ) ) ) ), 0644, "$dir/mods-enabled/cache.conf" );
        $this->assertSame( array( "updated $dir/mods-available/cache.conf" ), $this->layout->actions );
        $this->assertFalse( file_exists( "$dir/mods-enabled/cache.conf" ), 'a module the administrator disabled stays disabled' );
    }

    public function testFileTakenOverByAnAdministratorIsKept()
    {
        $dir = $this->tree();
        file_put_contents( "$dir/ports.conf", '{"Q":{"webserver":{"port":81}}}' );
        $this->layout->generated( "$dir/ports.conf", array( 'Q' => array( 'webserver' => array( 'port' => 80 ) ) ), 0644 );
        $this->assertSame( array( "kept $dir/ports.conf (edited by an administrator)" ), $this->layout->actions );
        $this->assertSame( '{"Q":{"webserver":{"port":81}}}', file_get_contents( "$dir/ports.conf" ) );
    }

    public function testWriteOnceNeverOverwrites()
    {
        $dir = $this->tree();
        $this->layout->once( "$dir/vc.conf", array( 'a' => 1 ) );
        $this->layout->once( "$dir/vc.conf", array( 'a' => 2 ) );
        $this->assertSame( array( 'a' => 1 ), json_decode( file_get_contents( "$dir/vc.conf" ), true ) );
        $this->assertSame( array( "created $dir/vc.conf" ), $this->layout->actions );
    }

    public function testDisabledSwitchingModule()
    {
        $dir = $this->tree();
        $mods = array( 'cache' => array( 'Q' => array() ) );
        $this->assertSame( array(), $this->layout->disabledSwitches( $dir, $mods ), 'a module seen the first time is not counted' );
        file_put_contents( "$dir/mods-available/cache.conf", '{}' );
        $this->assertSame( array( 'cache' => 'Q.web.cache.enabled' ), $this->layout->disabledSwitches( $dir, $mods ) );
        symlink( '../mods-available/cache.conf', "$dir/mods-enabled/cache.conf" );
        $this->assertSame( array(), $this->layout->disabledSwitches( $dir, $mods ) );
        $this->assertSame( array(), $this->layout->disabledSwitches( $dir, array() ) );
    }

    public function testEngineFilesInTheEnginesOrder()
    {
        $dir = $this->tree();
        foreach ( array( 'vc.conf', 'qbix.conf', 'ports.conf', 'mods-enabled/b.conf', 'mods-enabled/a.json', 'mods-enabled/a.conf',
                         'conf-enabled/z.conf', 'conf-available/off.conf', 'sites-enabled/k1.conf' ) as $file )
            file_put_contents( "$dir/$file", '{}' );
        mkdir( "$dir/mods-enabled/dir.conf" );
        $this->assertSame( array( "$dir/vc.conf", "$dir/ports.conf", "$dir/mods-enabled/a.conf", "$dir/mods-enabled/a.json",
                                  "$dir/mods-enabled/b.conf", "$dir/conf-enabled/z.conf" ), $this->layout->engineFiles( $dir ) );
    }

    public function testEnvvars()
    {
        $dir = $this->tree();
        $this->assertSame( array(), $this->layout->envvars( $dir ) );
        $this->assertSame( array(), $this->layout->envvars( null ) );
        file_put_contents( "$dir/envvars", "# comment\n\nexport A=1\nB = 2\nC=\"quoted value\"\nD='single'\nE=\"unbalanced'\n 1BAD=x\nF=\nexport  G=a=b\n" );
        $this->assertSame( array( 'A' => '1', 'C' => 'quoted value', 'D' => 'single', 'E' => "\"unbalanced'", 'F' => '', 'G' => 'a=b' ),
                           $this->layout->envvars( $dir ) );
    }

    public function testDescribe()
    {
        $dir = $this->tree();
        file_put_contents( "$dir/sites-available/k1.conf", '{}' );
        symlink( '../sites-available/k1.conf', "$dir/sites-enabled/k1.conf" );
        file_put_contents( "$dir/mods-available/cache.conf", '{}' );
        file_put_contents( "$dir/envvars", "X=1\n" );
        $description = $this->layout->describe();
        $this->assertSame( $dir, $description['confDir'] );
        $this->assertSame( 'k1.example.invalid', $description['site'] );
        $this->assertSame( array( 'available' => array( 'k1.conf' ), 'enabled' => array( 'k1.conf' ) ), $description['pairs']['sites'] );
        $this->assertSame( array( 'available' => array( 'cache.conf' ), 'enabled' => array() ), $description['pairs']['mods'] );
        $this->assertSame( array( 'X' ), $description['envvars'] );
        $this->velocity->settings['ConfDir'] = 'disabled';
        $this->assertSame( array(), $this->layout->describe()['pairs'] );
    }

    // ---------------------------------------------------------------- toggle

    public function testToggle()
    {
        $dir = $this->tree();
        file_put_contents( "$dir/conf-available/extra.conf", '{}' );
        $this->assertSame( array( 'ok' => true, 'message' => 'enabled conf extra; restart to apply' ), $this->layout->toggle( 'conf', 'extra', true ) );
        $this->assertSame( '../conf-available/extra.conf', readlink( "$dir/conf-enabled/extra.conf" ) );
        $this->assertSame( array( 'ok' => true, 'message' => 'conf extra already enabled' ), $this->layout->toggle( 'conf', 'extra', true ) );
        $this->assertSame( array( 'ok' => true, 'message' => 'disabled conf extra; restart to apply' ), $this->layout->toggle( 'conf', 'extra', false ) );
        $this->assertFalse( file_exists( "$dir/conf-enabled/extra.conf" ) );
        $this->assertSame( array( 'ok' => true, 'message' => 'conf extra already disabled' ), $this->layout->toggle( 'conf', 'extra', false ) );
    }

    public function testToggleRefusals()
    {
        $dir = $this->tree();
        $this->assertSame( array( 'ok' => false, 'message' => 'no mods-available/none.conf' ), $this->layout->toggle( 'mod', 'none', true ) );
        $this->assertSame( array( 'ok' => false, 'message' => 'unknown kind module' ), $this->layout->toggle( 'module', 'x', true ) );
        $this->assertSame( array( 'ok' => false, 'message' => 'invalid name ../x' ), $this->layout->toggle( 'site', '../x', true ) );
        file_put_contents( "$dir/sites-enabled/plain.conf", '{}' );
        $this->assertSame( array( 'ok' => true, 'message' => "$dir/sites-enabled/plain.conf is a file, not a link; left alone" ),
                           $this->layout->toggle( 'site', 'plain', false ) );
        $this->assertFileExists( "$dir/sites-enabled/plain.conf" );
        $this->velocity->settings['ConfDir'] = 'disabled';
        $this->assertSame( array( 'ok' => false, 'message' => 'layout disabled' ), $this->layout->toggle( 'site', 'x', true ) );
    }
}
