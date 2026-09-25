<?php
/**
 * File containing the expVelocityEnginesTest class.
 *
 * The parts of Velocity's frankenphp and php engines that can be checked
 * without starting a server: which engine a setting selects, how values are
 * quoted into the Caddyfile, which release asset a machine gets, and that the
 * generated routing never serves an internal file.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class expVelocityEnginesTest extends ezpTestCase
{
    public function tearDown(): void
    {
        ezpINIHelper::restoreINISettings();
        parent::tearDown();
    }

    public function testFactoryPicksTheEngine()
    {
        $this->assertSame( 'expVelocity', get_class( expVelocity::create( 'velocity.ini', 'qbix' ) ) );
        $this->assertSame( 'expVelocityFrankenPHP', get_class( expVelocity::create( 'velocity.ini', 'frankenphp' ) ) );
        $this->assertSame( 'expVelocityPHPServer', get_class( expVelocity::create( 'velocity.ini', 'php' ) ) );
        $this->assertSame( 'expVelocityPHPServer', get_class( expVelocity::create( 'velocity.ini', ' PHP ' ) ) );
    }

    public function testFactoryReadsTheSetting()
    {
        ezpINIHelper::setINISetting( 'velocity.ini', 'ServerSettings', 'Engine', 'frankenphp' );
        $this->assertSame( 'frankenphp', expVelocity::create()->engineName() );

        ezpINIHelper::setINISetting( 'velocity.ini', 'ServerSettings', 'Engine', '' );
        $this->assertSame( 'qbix', expVelocity::create()->engineName() );
    }

    public function testFactoryRefusesAnUnknownEngine()
    {
        $this->expectException( InvalidArgumentException::class );
        expVelocity::create( 'velocity.ini', 'apache' );
    }

    public function testOnlyTheQbixEngineHasTheLayoutAndSsl()
    {
        $this->assertTrue( expVelocity::create( 'velocity.ini', 'qbix' )->supports( 'layout' ) );
        foreach ( array( 'frankenphp', 'php' ) as $engine )
        {
            $velocity = expVelocity::create( 'velocity.ini', $engine );
            $this->assertFalse( $velocity->supports( 'layout' ), $engine );
            $this->assertFalse( $velocity->supports( 'ssl' ), $engine );
        }
    }

    public function testCaddyQuote()
    {
        $this->assertSame( '"/srv/site"', expVelocityFrankenPHP::caddyQuote( '/srv/site' ) );
        $this->assertSame( '"/srv/my site {x}"', expVelocityFrankenPHP::caddyQuote( '/srv/my site {x}' ) );
        $this->assertSame( '"a\\"b"', expVelocityFrankenPHP::caddyQuote( 'a"b' ) );
        $this->assertSame( '"a\\\\b"', expVelocityFrankenPHP::caddyQuote( 'a\\b' ) );
    }

    public static function assetProvider()
    {
        return array(
            array( 'Linux',  'x86_64',  'gnu',      'frankenphp-linux-x86_64-gnu' ),
            array( 'Linux',  'amd64',   'musl',     'frankenphp-linux-x86_64' ),
            array( 'Linux',  'x86_64',  'mimalloc', 'frankenphp-linux-x86_64-mimalloc' ),
            array( 'Linux',  'aarch64', 'gnu',      'frankenphp-linux-aarch64-gnu' ),
            array( 'Linux',  'arm64',   'musl',     'frankenphp-linux-aarch64' ),
            array( 'Darwin', 'arm64',   'gnu',      'frankenphp-mac-arm64' ),
            array( 'Darwin', 'x86_64',  'musl',     'frankenphp-mac-x86_64' ),
            array( 'Linux',  'aarch64', 'mimalloc', null ),
            array( 'Linux',  'x86_64',  'bogus',    null ),
            array( 'Windows','x86_64',  'gnu',      null ),
            array( 'Linux',  'riscv64', 'gnu',      null ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('assetProvider')]
    public function testAssetName( $os, $machine, $variant, $expected )
    {
        $installer = new expVelocityFrankenPHPInstaller( expVelocity::create( 'velocity.ini', 'frankenphp' ) );
        list( $asset, $error ) = $installer->assetName( $os, $machine, $variant );
        $this->assertSame( $expected, $asset );
        if ( $expected === null )
            $this->assertNotEmpty( $error );
    }

    public function testEveryPinnedAssetHasAWellFormedChecksum()
    {
        $installer = new expVelocityFrankenPHPInstaller( expVelocity::create( 'velocity.ini', 'frankenphp' ) );
        foreach ( self::assetProvider() as $row )
            if ( $row[3] !== null )
                $this->assertMatchesRegularExpression( '/^[0-9a-f]{64}$/', $installer->pinnedSha256( $row[3] ), $row[3] );
    }

    public function testCleanVersion()
    {
        $this->assertSame( 'FrankenPHP v1.12.7 PHP 8.5.11 Caddy v2.11.4',
            expVelocityFrankenPHPInstaller::cleanVersion( 'FrankenPHP vv1.12.7 PHP 8.5.11 Caddy v2.11.4 h1:XKxkMTgNSizEvKG6QHue6cAsFOteU2qA61w2tKkCWi0=' ) );
        $this->assertSame( 'FrankenPHP v1.12.7 PHP 8.5.11 Caddy v2.11.4',
            expVelocityFrankenPHPInstaller::cleanVersion( 'FrankenPHP v1.12.7 PHP 8.5.11 Caddy v2.11.4 h1:abc' ) );
    }

    public function testTheCaddyfileRoutesLikeHtaccessRoot()
    {
        ezpINIHelper::setINISetting( 'velocity.ini', 'FrankenPHPSettings', 'Port', '8123' );
        $text = expVelocity::create( 'velocity.ini', 'frankenphp' )->caddyfileText();

        // Never php_server: its default serves every file that exists.
        $this->assertStringNotContainsString( 'php_server', $text );
        $this->assertStringNotContainsString( 'try_files', $text );
        $this->assertStringContainsString( 'file_server @static', $text );
        $this->assertStringContainsString( 'path_regexp static ' . expVelocity::STATIC_PATHS, $text );
        // Scripts and dot paths below an asset directory go to index.php.
        $this->assertStringContainsString( 'not path_regexp ' . expVelocity::NEVER_STATIC, $text );
        $this->assertStringContainsString( 'rewrite @front /index.php', $text );
        $this->assertStringContainsString( 'rewrite @rest /index_rest.php', $text );
        $this->assertStringContainsString( 'http://:8123 {', $text );
        $this->assertStringContainsString( 'auto_https off', $text );
    }

    public function testTheStaticListLeavesInternalFilesOut()
    {
        $static = '~' . expVelocity::STATIC_PATHS . '~';
        foreach ( array( '/design/standard/stylesheets/core.css', '/var/site/storage/images/a/b.jpg',
                         '/extension/ezwebin/design/ezwebin/javascript/x.js', '/share/icons/crystal/a.png',
                         '/extension/sevenx_themes_media/design/media/fonts/inter.woff2',
                         '/favicon.ico', '/robots.txt' ) as $path )
            $this->assertSame( 1, preg_match( $static, $path ), $path );

        foreach ( array( '/settings/site.ini', '/settings/override/site.ini.append.php', '/var/storage/sqlite3/sqlite3.db',
                         '/autoload.php', '/kernel/classes/expvelocity.php', '/.git/config', '/composer.json',
                         '/var/site/cache/ini/x.php', '/bin/php/velocity-router.php', '/index.php' ) as $path )
            $this->assertSame( 0, preg_match( $static, $path ), $path );

        // Listed directories, but never served: a script's source, dot paths.
        $never = '~' . expVelocity::NEVER_STATIC . '~';
        foreach ( array( '/var/storage/packages/7x/a/settings/ini-site.php', '/var/storage/packages/7x/a/.cache/package.xml',
                         '/design/standard/images/x.PHP', '/design/standard/images/x.phtml', '/design/standard/images/.htaccess' ) as $path )
        {
            $this->assertSame( 1, preg_match( $static, $path ), $path );
            $this->assertSame( 1, preg_match( $never, $path ), $path );
        }
        foreach ( array( '/design/standard/stylesheets/core.css', '/favicon.ico', '/design/standard/images/php.png' ) as $path )
            $this->assertSame( 0, preg_match( $never, $path ), $path );
    }

    public function testThePhpEngineCommandLine()
    {
        ezpINIHelper::setINISetting( 'velocity.ini', 'PHPServerSettings', 'Host', '127.0.0.1' );
        ezpINIHelper::setINISetting( 'velocity.ini', 'PHPServerSettings', 'Port', '8124' );
        $velocity = expVelocity::create( 'velocity.ini', 'php' );
        $command = $velocity->command();

        $this->assertSame( PHP_BINARY, $command[0] );
        $at = array_search( '-S', $command, true );
        $this->assertNotFalse( $at );
        $this->assertSame( '127.0.0.1:8124', $command[$at + 1] );
        $this->assertSame( $velocity->router(), end( $command ) );
    }

    public function testTheShippedDefaultIsPhpAndConfigurable()
    {
        $this->assertSame( array( 'php', 'frankenphp', 'qbix' ), expVelocity::engines() );

        ezpINIHelper::setINISetting( 'velocity.ini', 'ServerSettings', 'Engine', 'php' );
        $this->assertSame( 'php', expVelocity::defaultEngine() );
        $this->assertTrue( expVelocity::create()->isDefault() );

        ezpINIHelper::setINISetting( 'velocity.ini', 'ServerSettings', 'Engine', 'frankenphp' );
        $this->assertSame( 'frankenphp', expVelocity::defaultEngine() );
        $this->assertTrue( expVelocity::create( 'velocity.ini', 'frankenphp' )->isDefault() );
        $this->assertFalse( expVelocity::create( 'velocity.ini', 'php' )->isDefault() );
    }

    public function testRoles()
    {
        $this->assertSame( 'development', expVelocity::create( 'velocity.ini', 'php' )->role() );
        $this->assertSame( 'production', expVelocity::create( 'velocity.ini', 'frankenphp' )->role() );
        $this->assertSame( 'experimental', expVelocity::create( 'velocity.ini', 'qbix' )->role() );
    }

    public function testEachEngineHasItsOwnPortAndFallsBack()
    {
        ezpINIHelper::setINISetting( 'velocity.ini', 'ServerSettings', 'Port', '9001' );
        ezpINIHelper::setINISetting( 'velocity.ini', 'ServerSettings', 'Workers', '7' );
        ezpINIHelper::setINISetting( 'velocity.ini', 'FrankenPHPSettings', 'Port', '9002' );
        ezpINIHelper::setINISetting( 'velocity.ini', 'FrankenPHPSettings', 'Workers', '' );
        ezpINIHelper::setINISetting( 'velocity.ini', 'PHPServerSettings', 'Port', '9003' );

        $this->assertSame( 9001, expVelocity::create( 'velocity.ini', 'qbix' )->httpPort() );
        $this->assertSame( 9002, expVelocity::create( 'velocity.ini', 'frankenphp' )->httpPort() );
        $this->assertSame( 9003, expVelocity::create( 'velocity.ini', 'php' )->httpPort() );

        // An empty own value falls back to [ServerSettings].
        $this->assertStringContainsString( 'num_threads 7', expVelocity::create( 'velocity.ini', 'frankenphp' )->caddyfileText() );

        ezpINIHelper::setINISetting( 'velocity.ini', 'PHPServerSettings', 'Port', '' );
        $this->assertSame( 9001, expVelocity::create( 'velocity.ini', 'php' )->httpPort() );
    }

    public function testQbixViewLabelsFollowItsAccessRules()
    {
        $qbix = expVelocity::create( 'velocity.ini', 'qbix' );
        $byPath = function ( array $views ) {
            $out = array();
            foreach ( $views as $v )
                $out[$v[0]] = $v[2];
            return $out;
        };

        $open = $byPath( $qbix->views( false, false, false ) );
        $this->assertStringContainsString( 'not from elsewhere', $open['/Q/stats'] );
        $this->assertStringContainsString( 'never from elsewhere without a token', $open['/Q/phpinfo'] );
        $this->assertSame( 'public', $open['/Q/docs'] );

        $token = $byPath( $qbix->views( true, false, false ) );
        $this->assertStringContainsString( 'with the token', $token['/Q/stats'] );
        $this->assertStringContainsString( 'from this machine too', $token['/Q/phpinfo'] );

        $panel = $byPath( $qbix->views( true, false, true ) );
        $this->assertStringContainsString( 'panel login', $panel['/Q/dashboard'] );
        $this->assertStringContainsString( 'does not open it', $panel['/Q/dashboard'] );

        $remote = $byPath( $qbix->views( false, true, false ) );
        $this->assertStringContainsString( 'Remote=enabled', $remote['/Q/stats'] );
    }

    public function testFollowSymlinksReachesTheEngines()
    {
        // The shipped default; an override on the test machine may differ.
        ezpINIHelper::setINISetting( 'velocity.ini', 'ServerSettings', 'FollowSymlinks', 'disabled' );
        $velocity = expVelocity::create( 'velocity.ini', 'qbix' );
        $this->assertFalse( $velocity->followsSymlinks() );
        $this->assertArrayNotHasKey( 'followSymlinks', $this->qbixWebserverConfig( $velocity ) );
        $this->assertStringContainsString( 'FollowSymlinks', implode( "\n", expVelocity::create( 'velocity.ini', 'frankenphp' )->ignoredSettings() ) );

        ezpINIHelper::setINISetting( 'velocity.ini', 'ServerSettings', 'FollowSymlinks', 'enabled' );
        $velocity = expVelocity::create( 'velocity.ini', 'qbix' );
        $this->assertTrue( $velocity->followsSymlinks() );
        $this->assertTrue( expVelocity::create( 'velocity.ini', 'php' )->followsSymlinks() );
        $this->assertSame( true, $this->qbixWebserverConfig( $velocity )['followSymlinks'] ?? null );
        $this->assertStringNotContainsString( 'FollowSymlinks', implode( "\n", expVelocity::create( 'velocity.ini', 'frankenphp' )->ignoredSettings() ) );
    }

    public function testTheQbixEngineRunsOnlyTheEntryPointsAndServesOnlyTheAssets()
    {
        $velocity = expVelocity::create( 'velocity.ini', 'qbix' );
        $webserver = $this->qbixWebserverConfig( $velocity );
        $this->assertSame( array( '/index.php', '/index_rest.php' ), $webserver['scripts'] ?? null );
        $this->assertSame( 'index_rest.php', $webserver['frontControllers']['^/(api/|index_rest\\.php)'] ?? null );
        $this->assertSame( 'index_treemenu.php', $webserver['frontControllers']['^/([^/]+/)?content/treemenu'] ?? null );

        $write = new ReflectionMethod( $velocity, 'writeServerConfig' );
        $write->setAccessible( true );
        $config = json_decode( file_get_contents( $write->invoke( $velocity ) ), true );
        $this->assertSame( array( expVelocity::STATIC_PATHS ), $config['Q']['web']['static']['paths'] ?? null );

        // The same routes the frankenphp engine's Caddyfile has.
        $caddy = expVelocity::create( 'velocity.ini', 'frankenphp' )->caddyfileText();
        $this->assertStringContainsString( '^/(api/|index_rest\\.php)', $caddy );
        $this->assertStringContainsString( '^/([^/]+/)?content/treemenu', $caddy );
    }

    /**
     * Q.webserver as the qbix engine writes it to var/tmp/velocity-server.json.
     */
    protected function qbixWebserverConfig( expVelocity $velocity )
    {
        $write = new ReflectionMethod( $velocity, 'writeServerConfig' );
        $write->setAccessible( true );
        $path = $write->invoke( $velocity );
        $this->assertIsString( $path );
        $config = json_decode( file_get_contents( $path ), true );
        return $config['Q']['webserver'] ?? array();
    }

    public function testIgnoredSettingsAreNamed()
    {
        ezpINIHelper::setINISetting( 'velocity.ini', 'CacheSettings', 'Enabled', 'enabled' );
        $notes = implode( "\n", expVelocity::create( 'velocity.ini', 'frankenphp' )->ignoredSettings() );
        $this->assertStringContainsString( 'CacheSettings', $notes );

        $notes = implode( "\n", expVelocity::create( 'velocity.ini', 'php' )->ignoredSettings() );
        $this->assertStringContainsString( 'development server', $notes );
    }
}
