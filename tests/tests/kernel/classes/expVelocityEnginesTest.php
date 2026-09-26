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
                         '/favicon.ico', '/robots.txt', '/sw.js', '/var/site/storage/original/image/logo.svg' ) as $path )
            $this->assertSame( 1, preg_match( $static, $path ), $path );

        foreach ( array( '/settings/site.ini', '/settings/override/site.ini.append.php', '/var/storage/sqlite3/sqlite3.db',
                         '/autoload.php', '/kernel/classes/expvelocity.php', '/.git/config', '/composer.json',
                         '/var/site/cache/ini/x.php', '/bin/php/velocity-router.php', '/index.php',
                         '/var/site/storage/original/application/contract.pdf', '/sw.js.bak' ) as $path )
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
        $this->assertSame( array( '/index.php', '/index_rest.php', '/index_treemenu.php' ), $webserver['scripts'] ?? null );
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

    public function testUrlsArePrintableAddresses()
    {
        ezpINIHelper::setINISetting( 'velocity.ini', 'PHPServerSettings', 'Port', '8125' );
        ezpINIHelper::setINISetting( 'velocity.ini', 'PHPServerSettings', 'Host', '0.0.0.0' );
        ezpINIHelper::setINISetting( 'site.ini', 'SiteAccessSettings', 'MatchOrder', 'uri;host' );
        ezpINIHelper::setINISetting( 'site.ini', 'SiteAccessSettings', 'AvailableSiteAccessList', array( 'site', 'admin' ) );
        $php = expVelocity::create( 'velocity.ini', 'php' );
        $urls = $php->urls();
        $this->assertSame( array( array( 'Site', 'http://localhost:8125/' ) ), $urls );
        // The backend pages are the application's, the same on every engine.
        $this->assertContains( array( 'Info', '/admin/setup/info' ), $php->adminPaths() );
        $this->assertSame( $php->adminPaths(), expVelocity::create( 'velocity.ini', 'frankenphp' )->adminPaths() );

        // Only the Qbix server has a dashboard.
        $labels = array_column( expVelocity::create( 'velocity.ini', 'qbix' )->urls(), 0 );
        $this->assertContains( 'Dashboard', $labels );
        $this->assertNotContains( 'Dashboard', array_column( $urls, 0 ) );

        // Every site by its path, the default at /; the admin one apart.
        ezpINIHelper::setINISetting( 'site.ini', 'SiteSettings', 'DefaultAccess', 'site' );
        ezpINIHelper::setINISetting( 'site.ini', 'SiteAccessSettings', 'AvailableSiteAccessList', array( 'site', 'eng', 'admin' ) );
        $this->assertSame( array( array( '/', 'default (site)' ), array( '/eng', '' ) ),
                           expVelocity::create( 'velocity.ini', 'php' )->sitePaths() );

        // Siteaccesses matched by host only: no admin path to show.
        ezpINIHelper::setINISetting( 'site.ini', 'SiteAccessSettings', 'MatchOrder', 'host' );
        $this->assertSame( array(), expVelocity::create( 'velocity.ini', 'php' )->adminPaths() );
        $this->assertSame( array(), expVelocity::create( 'velocity.ini', 'php' )->sitePaths() );

        ezpINIHelper::setINISetting( 'velocity.ini', 'PHPServerSettings', 'Host', '192.0.2.7' );
        $this->assertSame( 'http://192.0.2.7:8125/', expVelocity::create( 'velocity.ini', 'php' )->urls()[0][1] );
    }

    public function testHttpsIsShownBesidePlainHttp()
    {
        $dir = sys_get_temp_dir() . '/velocity-tls-' . getmypid();
        @mkdir( $dir );
        file_put_contents( $dir . '/cert.pem', 'x' );
        file_put_contents( $dir . '/key.pem', 'x' );
        ezpINIHelper::setINISetting( 'velocity.ini', 'FrankenPHPSettings', 'Port', '8126' );
        // Not the installation's own server, which may be running.
        ezpINIHelper::setINISetting( 'velocity.ini', 'FrankenPHPSettings', 'PidFile', 'var/tmp/velocity-test-none.pid' );
        ezpINIHelper::setINISetting( 'velocity.ini', 'FrankenPHPSettings', 'ConfigFile', 'var/tmp/velocity-test-none.Caddyfile' );
        ezpINIHelper::setINISetting( 'velocity.ini', 'FrankenPHPSettings', 'HTTPSPort', '8446' );
        ezpINIHelper::setINISetting( 'velocity.ini', 'FrankenPHPSettings', 'HTTPS', 'disabled' );
        ezpINIHelper::setINISetting( 'velocity.ini', 'HTTPSSettings', 'Enabled', 'false' );
        $franken = expVelocity::create( 'velocity.ini', 'frankenphp' );
        $this->assertSame( 8446, $franken->configuredHttpsPort() );
        $this->assertNotContains( 'HTTPS', array_column( $franken->urls(), 0 ) );

        ezpINIHelper::setINISetting( 'velocity.ini', 'HTTPSSettings', 'Enabled', 'true' );
        ezpINIHelper::setINISetting( 'velocity.ini', 'HTTPSSettings', 'Certificate', $dir . '/cert.pem' );
        ezpINIHelper::setINISetting( 'velocity.ini', 'HTTPSSettings', 'Key', $dir . '/key.pem' );
        $urls = expVelocity::create( 'velocity.ini', 'frankenphp' )->urls();
        $this->assertSame( array( 'Site', 'http://localhost:8126/' ), $urls[0] );
        $this->assertSame( array( 'HTTPS', 'https://localhost:8446/' ), $urls[1] );

        @unlink( $dir . '/cert.pem' );
        @unlink( $dir . '/key.pem' );
        @rmdir( $dir );
    }

    public function testFrankenphpHttpsWithASelfSignedCertificate()
    {
        ezpINIHelper::setINISetting( 'velocity.ini', 'FrankenPHPSettings', 'Port', '8127' );
        // Not the installation's own server, which may be running.
        ezpINIHelper::setINISetting( 'velocity.ini', 'FrankenPHPSettings', 'PidFile', 'var/tmp/velocity-test-none.pid' );
        ezpINIHelper::setINISetting( 'velocity.ini', 'FrankenPHPSettings', 'ConfigFile', 'var/tmp/velocity-test-none.Caddyfile' );
        ezpINIHelper::setINISetting( 'velocity.ini', 'FrankenPHPSettings', 'HTTPSPort', '8447' );
        ezpINIHelper::setINISetting( 'velocity.ini', 'FrankenPHPSettings', 'HTTPS', 'disabled' );
        ezpINIHelper::setINISetting( 'velocity.ini', 'HTTPSSettings', 'Enabled', 'false' );
        ezpINIHelper::setINISetting( 'velocity.ini', 'HTTPSSettings', 'Certificate', '' );
        ezpINIHelper::setINISetting( 'velocity.ini', 'HTTPSSettings', 'Key', '' );

        $franken = expVelocity::create( 'velocity.ini', 'frankenphp' );
        $this->assertFalse( $franken->httpsEnabled() );
        $this->assertStringNotContainsString( 'https://', $franken->caddyfileText() );

        // --https: this start only, a self-signed pair when none is named.
        $franken->forceHttps();
        $this->assertTrue( $franken->httpsEnabled() );
        $tls = $franken->tlsFiles();
        $this->assertTrue( $tls[2] );
        $this->assertStringEndsWith( 'var/vc/frankenphp/tls/selfsigned.crt', $tls[0] );
        $caddy = $franken->caddyfileText();
        $this->assertStringContainsString( 'http://:8127 {', $caddy );
        $this->assertStringContainsString( 'https://:8447 {', $caddy );
        $this->assertStringContainsString( 'selfsigned.key', $caddy );

        // The setting does the same for every start -- the shipped default.
        ezpINIHelper::setINISetting( 'velocity.ini', 'FrankenPHPSettings', 'HTTPS', 'enabled' );
        $this->assertTrue( expVelocity::create( 'velocity.ini', 'frankenphp' )->httpsEnabled() );
        $this->assertSame( 'enabled', eZINI::fetchFromFile( 'settings/velocity.ini' )->variable( 'FrankenPHPSettings', 'HTTPS' ) );

        // --no-https: plain HTTP for this start, whatever the settings say.
        $plain = expVelocity::create( 'velocity.ini', 'frankenphp' );
        $plain->withoutHttps();
        $this->assertFalse( $plain->httpsEnabled() );
        $this->assertStringNotContainsString( 'https://', $plain->caddyfileText() );

        // A named certificate is used as it is, and must exist.
        ezpINIHelper::setINISetting( 'velocity.ini', 'HTTPSSettings', 'Certificate', '/nonexistent/cert.pem' );
        $this->assertIsString( expVelocity::create( 'velocity.ini', 'frankenphp' )->tlsFiles() );
    }

    public function testMakingASelfSignedCertificate()
    {
        if ( !function_exists( 'openssl_pkey_new' ) )
            $this->markTestSkipped( 'no openssl extension' );
        $dir = sys_get_temp_dir() . '/velocity-selfsigned-' . getmypid();
        $franken = expVelocity::create( 'velocity.ini', 'frankenphp' );
        $make = new ReflectionMethod( $franken, 'makeSelfSigned' );
        $make->setAccessible( true );
        $this->assertTrue( $make->invoke( $franken, $dir . '/c.crt', $dir . '/c.key' ) );

        $cert = openssl_x509_parse( file_get_contents( $dir . '/c.crt' ) );
        $this->assertStringContainsString( 'DNS:localhost', $cert['extensions']['subjectAltName'] );
        $this->assertStringContainsString( 'IP Address:127.0.0.1', $cert['extensions']['subjectAltName'] );
        $this->assertGreaterThan( time() + 300 * 86400, $cert['validTo_time_t'] );
        $this->assertSame( 'sha256WithRSAEncryption', $cert['signatureTypeLN'] ?? 'sha256WithRSAEncryption' );
        $this->assertSame( '600', substr( sprintf( '%o', fileperms( $dir . '/c.key' ) ), -3 ) );
        $this->assertTrue( openssl_x509_check_private_key( file_get_contents( $dir . '/c.crt' ), file_get_contents( $dir . '/c.key' ) ) );

        @unlink( $dir . '/c.crt' );
        @unlink( $dir . '/c.key' );
        @rmdir( $dir );
    }

    public function testEverythingAServerWritesIsBelowVarVelocity()
    {
        // The shipped settings, not an override on the test machine.
        $ini = eZINI::fetchFromFile( 'settings/velocity.ini' );
        $this->assertSame( 'var/vc/qbix/log', $ini->variable( 'LogSettings', 'Dir' ) );
        $this->assertSame( 'var/vc/qbix/run/server.pid', $ini->variable( 'ServerSettings', 'PidFile' ) );
        $this->assertSame( 'var/vc/qbix/run/console.log', $ini->variable( 'ServerSettings', 'LogFile' ) );
        $this->assertSame( 'var/vc/frankenphp/run/Caddyfile', $ini->variable( 'FrankenPHPSettings', 'ConfigFile' ) );
        $this->assertSame( 'var/vc/frankenphp/run/server.pid', $ini->variable( 'FrankenPHPSettings', 'PidFile' ) );
        $this->assertSame( 'var/vc/php/run/server.pid', $ini->variable( 'PHPServerSettings', 'PidFile' ) );
        $this->assertSame( 'var/vc/php/log/server.log', $ini->variable( 'PHPServerSettings', 'LogFile' ) );

        // FrankenPHP's request logs: a directory of its own, not the Qbix server's.
        ezpINIHelper::setINISetting( 'velocity.ini', 'LogSettings', 'Enabled', 'enabled' );
        ezpINIHelper::setINISetting( 'velocity.ini', 'LogSettings', 'Dir', 'var/log/elsewhere' );
        ezpINIHelper::setINISetting( 'velocity.ini', 'FrankenPHPSettings', 'LogDir', '' );
        ezpINIHelper::setINISetting( 'velocity.ini', 'FrankenPHPSettings', 'AccessLog', '' );
        $logs = expVelocity::create( 'velocity.ini', 'frankenphp' )->requestLogs();
        $this->assertStringEndsWith( 'var/vc/frankenphp/log/access.log', $logs['access'] );
        $this->assertStringEndsWith( 'var/log/elsewhere/', dirname( expVelocity::create( 'velocity.ini', 'qbix' )->requestLogs()['access'] ) . '/' );
        $this->assertNull( expVelocity::create( 'velocity.ini', 'php' )->requestLogs()['access'] );
    }

    public function testRelativePath()
    {
        $velocity = expVelocity::create( 'velocity.ini', 'php' );
        $root = rtrim( eZSys::rootDir(), '/' );
        $this->assertSame( 'var/tmp/x.log', $velocity->relativePath( $root . '/var/tmp/x.log' ) );
        $this->assertSame( '/usr/bin/php', $velocity->relativePath( '/usr/bin/php' ) );
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
