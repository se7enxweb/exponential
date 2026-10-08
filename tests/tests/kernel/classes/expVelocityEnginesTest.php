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

    /**
     * [ServerSettings] Instances: each instance's own pid file and log beside
     * the first one's, which keeps the configured names.
     */
    public function testInstancePaths()
    {
        $this->assertSame( '/run/vc/server.pid', expVelocity::instancePath( '/run/vc/server.pid', 0 ) );
        $this->assertSame( '/run/vc/server.1.pid', expVelocity::instancePath( '/run/vc/server.pid', 1 ) );
        $this->assertSame( '/run/vc/server.3.pid', expVelocity::instancePath( '/run/vc/server.pid', 3 ) );
        $this->assertSame( '/var/log/console.2.log', expVelocity::instancePath( '/var/log/console.log', 2 ) );
        $this->assertSame( '/run/vc/serverpid.1', expVelocity::instancePath( '/run/vc/serverpid', 1 ) );
        $this->assertSame( '/run/v.c/server.1.pid', expVelocity::instancePath( '/run/v.c/server.pid', 1 ) );
    }

    /** Instances applies to the qbix engine only; the others always run one. */
    public function testOnlyTheQbixEngineRunsSeveralInstances()
    {
        $this->assertGreaterThanOrEqual( 1, expVelocity::create( 'velocity.ini', 'qbix' )->instances() );
        $this->assertSame( 1, expVelocity::create( 'velocity.ini', 'frankenphp' )->instances() );
        $this->assertSame( 1, expVelocity::create( 'velocity.ini', 'php' )->instances() );
    }

    /**
     * FrankenPHP parses php_ini lines as ini text: a value with a semicolon
     * (session.save_path "0;0660;/dir") must be quoted or it is cut at the ';'.
     */
    public function testIniValueQuotesWhatTheIniParserWouldCut()
    {
        $this->assertSame( '256M', expVelocityFrankenPHP::iniValue( '256M' ) );
        $this->assertSame( '/var/lib/mysql/mysql.sock', expVelocityFrankenPHP::iniValue( '/var/lib/mysql/mysql.sock' ) );
        $this->assertSame( '"0;0660;/srv/sessions"', expVelocityFrankenPHP::iniValue( '0;0660;/srv/sessions' ) );
        $this->assertSame( '"a # b"', expVelocityFrankenPHP::iniValue( 'a # b' ) );
        $this->assertSame( '"already;quoted"', expVelocityFrankenPHP::iniValue( '"already;quoted"' ) );
        $this->assertSame( '', expVelocityFrankenPHP::iniValue( '' ) );
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

    /** With the caches of the sites in a tree of their own (multi-site hosting) their public caches are served too */
    public function testTheStaticListServesThePublicCachesBelowCacheVarDir()
    {
        $this->assertSame( expVelocity::STATIC_PATHS, expVelocity::staticPaths() );

        ezpINIHelper::setINISetting( 'site.ini', 'FileSettings', 'CacheVarDir', 'var_cache' );
        $static = '~' . expVelocity::staticPaths() . '~';
        foreach ( array( '/var_cache/example/cache/public/javascript/x.js', '/var_cache/example/cache/texttoimage/x.png',
                         '/var/site/cache/public/javascript/x.js', '/design/standard/stylesheets/core.css' ) as $path )
            $this->assertSame( 1, preg_match( $static, $path ), $path );
        foreach ( array( '/var_cache/example/cache/template/compiled/x.php', '/var_cache/example/cache/ini/site.php',
                         '/var_cache/example/log/error.log', '/var_cacheX/example/cache/public/x.js', '/var/site/cache/ini/x.php' ) as $path )
            $this->assertSame( 0, preg_match( $static, $path ), $path );
    }

    /** "./var_cache/" is the directory eZSys::cacheDirectory() writes as var_cache, and is served as that */
    public function testACacheVarDirIsServedAsTheCacheDirectoryIsWritten()
    {
        ezpINIHelper::setINISetting( 'site.ini', 'FileSettings', 'CacheVarDir', 'var_cache' );
        $plain = expVelocity::staticPaths();
        foreach ( array( './var_cache/', ' var_cache ', 'var_cache//' ) as $value )
        {
            ezpINIHelper::setINISetting( 'site.ini', 'FileSettings', 'CacheVarDir', $value );
            $this->assertSame( $plain, expVelocity::staticPaths(), $value );
        }
    }

    /**
     * The list is written without a siteaccess: a CacheVarDir set for some siteaccesses only is read from their own
     * settings and served too, next to the global one
     */
    public function testACacheVarDirOfASiteaccessIsServedToo()
    {
        $name = 'x1velocity' . substr( uniqid(), -6 );
        $dir = 'settings/siteaccess/' . $name;
        mkdir( $dir, 0777, true );
        try
        {
            file_put_contents( $dir . '/site.ini.append.php',
                "<?php /* #?ini charset=\"utf-8\"?\n\n[FileSettings]\nCacheVarDir=x1_sa_cache\n*/ ?>\n" );
            $list = (array)eZINI::instance()->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' );
            ezpINIHelper::setINISetting( 'site.ini', 'SiteAccessSettings', 'AvailableSiteAccessList', array_merge( $list, array( $name ) ) );
            ezpINIHelper::setINISetting( 'site.ini', 'FileSettings', 'CacheVarDir', 'var_cache' );

            $static = '~' . expVelocity::staticPaths() . '~';
            foreach ( array( '/x1_sa_cache/example/cache/public/javascript/x.js', '/var_cache/example/cache/public/x.css' ) as $path )
                $this->assertSame( 1, preg_match( $static, $path ), $path );
            $this->assertSame( 0, preg_match( $static, '/x1_sa_cache/example/cache/ini/site.php' ) );
        }
        finally
        {
            @unlink( $dir . '/site.ini.append.php' );
            @rmdir( $dir );
        }
    }

    /**
     * A siteaccess's ActiveAccessExtensions are read as eZSiteAccess::getIni() reads them: the settings of such an
     * extension, and those it keeps for that siteaccess, set its CacheVarDir too
     */
    public function testACacheVarDirOfAnAccessExtensionIsServed()
    {
        $id = substr( uniqid(), -6 );
        $siteAccess = 'x1velocitysa' . $id;
        $extension = 'x1velocityext' . $id;
        $made = $this->makeSiteaccessWithAccessExtension( $siteAccess, $extension, 'x1_ext_cache', 'x1_extsa_cache' );
        try
        {
            $static = '~' . expVelocity::staticPaths() . '~';
            // The siteaccess's own directory in the extension wins over the extension's settings, as it does on a request
            $this->assertSame( 1, preg_match( $static, '/x1_extsa_cache/example/cache/public/x.js' ) );
            $this->assertSame( 0, preg_match( $static, '/x1_ext_cache/example/cache/public/x.js' ) );
        }
        finally
        {
            $this->removeMadePaths( $made );
        }
    }

    /**
     * Reading the settings of every siteaccess leaves the process as it found it: the server starts with the same
     * state whatever its siteaccesses hold. No current siteaccess set or left set to null, no exception handler, no
     * INI instance added or changed, no extension remembered, no cache file written.
     */
    public function testReadingTheSettingsOfTheSiteaccessesChangesNoState()
    {
        $id = substr( uniqid(), -6 );
        $siteAccess = 'x1velocitysa' . $id;
        $extension = 'x1velocityext' . $id;
        $made = $this->makeSiteaccessWithAccessExtension( $siteAccess, $extension, 'x1_ext_cache', 'x1_extsa_cache' );
        $hadAccess = array_key_exists( 'eZCurrentAccess', $GLOBALS );
        $access = $hadAccess ? $GLOBALS['eZCurrentAccess'] : null;
        $handler = function_exists( 'get_exception_handler' ) ? get_exception_handler() : null;
        $ini = eZINI::instance();
        $overrideDirs = $ini->overrideDirs( false );
        $globalOverrideDirs = eZINI::globalOverrideDirs( false );
        $instances = $this->staticValue( 'eZINI', 'instances' );
        $extensionState = array();
        foreach ( array( 'activeExtensionsCache', 'extensionNameCache', 'extensionPathCache', 'extensionSettingsSiteAccessCache' ) as $property )
            $extensionState[$property] = $this->staticValue( 'eZExtension', $property );
        $activeExtensions = $GLOBALS['eZActiveExtensions'] ?? null;
        $cacheFiles = $this->cacheFiles();
        try
        {
            $this->assertSame( 1, preg_match( '~' . expVelocity::staticPaths() . '~', '/x1_extsa_cache/example/cache/public/x.js' ) );

            $this->assertSame( $hadAccess, array_key_exists( 'eZCurrentAccess', $GLOBALS ) );
            if ( $hadAccess )
                $this->assertSame( $access, $GLOBALS['eZCurrentAccess'] );
            if ( function_exists( 'get_exception_handler' ) )
                $this->assertSame( $handler, get_exception_handler() );
            $this->assertSame( $ini, eZINI::instance() );
            $this->assertSame( $overrideDirs, $ini->overrideDirs( false ) );
            $this->assertSame( $globalOverrideDirs, eZINI::globalOverrideDirs( false ) );
            $this->assertSame( array_keys( $instances ), array_keys( $this->staticValue( 'eZINI', 'instances' ) ) );
            foreach ( $instances as $key => $instance )
                $this->assertSame( $instance, $this->staticValue( 'eZINI', 'instances' )[$key], $key );
            foreach ( $extensionState as $property => $value )
                $this->assertSame( $value, $this->staticValue( 'eZExtension', $property ), $property );
            $this->assertSame( $activeExtensions, $GLOBALS['eZActiveExtensions'] ?? null );
            $this->assertSame( $cacheFiles, $this->cacheFiles() );
        }
        finally
        {
            $this->removeMadePaths( $made );
        }
    }

    /**
     * A siteaccess in AvailableSiteAccessList whose site.ini names an access extension, with a CacheVarDir in the
     * extension's settings and another in what the extension keeps for that siteaccess.
     *
     * @return string[] what was made, files first
     */
    protected function makeSiteaccessWithAccessExtension( $siteAccess, $extension, $extensionValue, $extensionSiteAccessValue )
    {
        $settings = function ( $body ) { return "<?php /* #?ini charset=\"utf-8\"?\n\n$body\n*/ ?>\n"; };
        $files = array(
            "settings/siteaccess/$siteAccess/site.ini.append.php" =>
                $settings( "[ExtensionSettings]\nActiveAccessExtensions[]=$extension" ),
            "extension/$extension/settings/site.ini.append.php" =>
                $settings( "[FileSettings]\nCacheVarDir=$extensionValue" ),
            "extension/$extension/settings/siteaccess/$siteAccess/site.ini.append.php" =>
                $settings( "[FileSettings]\nCacheVarDir=$extensionSiteAccessValue" ),
        );
        $made = array();
        foreach ( $files as $file => $text )
        {
            for ( $dir = dirname( $file ); !is_dir( $dir ); $dir = dirname( $dir ) )
                $made[] = $dir;
            if ( !is_dir( dirname( $file ) ) )
                mkdir( dirname( $file ), 0777, true );
            file_put_contents( $file, $text );
            array_unshift( $made, $file );
        }
        $list = (array)eZINI::instance()->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' );
        ezpINIHelper::setINISetting( 'site.ini', 'SiteAccessSettings', 'AvailableSiteAccessList', array_merge( $list, array( $siteAccess ) ) );
        return $made;
    }

    protected function removeMadePaths( array $made )
    {
        $dirs = array();
        foreach ( $made as $path )
        {
            if ( is_file( $path ) )
                @unlink( $path );
            else
                $dirs[] = $path;
        }
        // Deepest first
        usort( $dirs, function ( $a, $b ) { return strlen( $b ) - strlen( $a ); } );
        foreach ( $dirs as $dir )
            @rmdir( $dir );
    }

    protected function staticValue( $class, $property )
    {
        $reflection = new ReflectionProperty( $class, $property );
        if ( PHP_VERSION_ID < 80100 )
            $reflection->setAccessible( true );
        return $reflection->getValue();
    }

    /** Every file below the INI cache directory and the extension cache directory */
    protected function cacheFiles()
    {
        $files = array();
        $dirs = array( $GLOBALS['eZINI_CONFIG_CACHE_DIR'] ?? 'var/cache/ini/', eZExtension::CACHE_DIR );
        foreach ( $dirs as $dir )
            foreach ( glob( rtrim( $dir, '/' ) . '/*' ) ?: array() as $file )
                $files[] = $file;
        sort( $files );
        return $files;
    }

    /**
     * The warm-up renders a site in the parent process; the log and INI cache directories that site set
     * (eZSiteAccess::change()) are put back before the globals are swept, so no request starts with them
     */
    public function testTheWarmUpPutsBackThePathsOfTheSiteItRendered()
    {
        $source = file_get_contents( dirname( __DIR__, 4 ) . '/kernel/private/classes/commands/velocity-warmup.php' );
        $reset = strpos( $source, 'eZSiteAccess::resetSitePaths()' );
        $this->assertNotFalse( $reset );
        $this->assertLessThan( strpos( $source, 'foreach ($clearPrefixes as $__p)' ), $reset );
        $this->assertTrue( method_exists( 'eZSiteAccess', 'resetSitePaths' ) );
    }

    /**
     * The pool requires the warm-up inside a function (Q_WebServer_Pool), so the variables of
     * bin/php/velocity-warmup.php are not globals. Run that way, the warm-up reads the site's own maintenance
     * marker -- a site in maintenance renders nothing -- and its final sweep removes what the warm-up added but
     * keeps the globals the server had before it: those of the snapshot the entry script hands over.
     */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function testTheWarmUpRunAsThePoolRunsItKeepsTheServersGlobalsAndReadsItsRoot()
    {
        $root = sys_get_temp_dir() . '/x1velocitywarmup' . substr( uniqid(), -6 );
        mkdir( $root . '/var', 0777, true );
        $installation = dirname( __DIR__, 4 );
        file_put_contents( $root . '/autoload.php', "<?php\nrequire_once " . var_export( $installation . '/autoload.php', true ) . ";\n" );
        // In maintenance (as index.php reads it): the warm-up renders nothing. Not found, it would render the front page.
        file_put_contents( $root . '/var/maintenance.json', json_encode( array( 'reason' => 'test' ) ) );
        $cwd = getcwd();
        putenv( 'VELOCITY_WARMUP_URLS' );
        $GLOBALS['x1VelocityServerOwn'] = 'kept';
        try
        {
            chdir( $root );
            $run = static function ( $file )
            {
                require $file;
            };
            $run( $installation . '/bin/php/velocity-warmup.php' );

            $this->assertSame( 'kept', $GLOBALS['x1VelocityServerOwn'] ?? null );
            $this->assertArrayHasKey( '_SERVER', $GLOBALS );
            foreach ( array( '__warmupGlobalsBefore', 'root', 'urls', 'kernel', 'clearPrefixes', 'expVelocityWarmup' ) as $name )
                $this->assertArrayNotHasKey( $name, $GLOBALS, $name );
        }
        finally
        {
            chdir( $cwd );
            @unlink( $root . '/var/maintenance.json' );
            @unlink( $root . '/autoload.php' );
            @rmdir( $root . '/var' );
            @rmdir( $root );
        }
    }

    /** What the entry script hands over: the snapshot is taken out of $GLOBALS; without a root, the working directory */
    public function testTheWarmUpReadsWhatItsEntryScriptHandsOver()
    {
        $had = array_key_exists( 'root', $GLOBALS );
        $old = $had ? $GLOBALS['root'] : null;
        try
        {
            $GLOBALS['__warmupGlobalsBefore'] = array( '_SERVER' => 0 );
            $this->assertSame( array( '_SERVER' => 0 ), \Exponential\Command\Kernel\VelocityWarmup::globalsBefore() );
            $this->assertArrayNotHasKey( '__warmupGlobalsBefore', $GLOBALS );
            $this->assertNull( \Exponential\Command\Kernel\VelocityWarmup::globalsBefore() );

            $GLOBALS['root'] = '/srv/site';
            $this->assertSame( '/srv/site', \Exponential\Command\Kernel\VelocityWarmup::warmupRoot() );
            foreach ( array( null, '', 5 ) as $value )
            {
                $GLOBALS['root'] = $value;
                $this->assertSame( getcwd(), \Exponential\Command\Kernel\VelocityWarmup::warmupRoot() );
            }
        }
        finally
        {
            if ( $had )
                $GLOBALS['root'] = $old;
            else
                unset( $GLOBALS['root'] );
        }
    }

    /** Only a plain directory inside the installation may widen the list */
    public function testACacheVarDirThatIsNoPlainDirectoryIsNotServed()
    {
        foreach ( array( '/srv/cache', '../cache', 'var_cache/..', 'a b', 'x|y', 'var', '(.*)' ) as $value )
        {
            ezpINIHelper::setINISetting( 'site.ini', 'FileSettings', 'CacheVarDir', $value );
            $this->assertSame( expVelocity::STATIC_PATHS, expVelocity::staticPaths(), $value );
        }
    }

    public function testTheStaticListLeavesInternalFilesOut()
    {
        $static = '~' . expVelocity::STATIC_PATHS . '~';
        foreach ( array( '/design/standard/stylesheets/core.css', '/var/site/storage/images/a/b.jpg',
                         '/extension/ezwebin/design/ezwebin/javascript/x.js', '/share/icons/crystal/a.png',
                         '/extension/sevenx_themes_media/design/media/fonts/inter.woff2',
                         '/favicon.ico', '/robots.txt', '/index.js', '/sw.js', '/var/site/storage/original/image/logo.svg',
                         '/var/site/cache/public/javascript/x.js', '/var/storage/packages/7x/a/thumbnail.png', '/var/site/storage/pdf/handbook-2026_1.pdf',
                         '/extension/explayouts_ui_api/design/standard/vendor/ace-editor/ace.js' ) as $path )
            $this->assertSame( 1, preg_match( $static, $path ), $path );

        // Below var/ only what .htaccess_root serves: never scratch files,
        // logs, caches, the database, or the package store beyond its previews.
        foreach ( array( '/settings/site.ini', '/settings/override/site.ini.append.php', '/var/storage/sqlite3/sqlite3.db',
                         '/autoload.php', '/kernel/classes/expvelocity.php', '/.git/config', '/composer.json',
                         '/var/site/cache/ini/x.php', '/bin/php/velocity-router.php', '/index.php',
                         '/var/site/storage/original/application/contract.pdf', '/sw.js.bak', '/index.js.bak', '/design/x/index.js',
                         '/var/tmp/notes.txt', '/var/tmp/x.css', '/var/tmp/x.png', '/var/log/error.log',
                         '/var/site/log/storage.log', '/var/cache/x.css', '/var/site/cache/template/compiled/x.php',
                         '/var/storage/packages/7x/a/package.xml', '/var/storage/packages/7x/a/preview.svg',
                         '/var/storage/packages/7x/a/design/standard/stylesheets/x.css',
                         '/var/site/storage/original/image/x.php', '/var/site/storage/original/image/x.html',
                         '/var/site/storage/pdf/x.php', '/var/site/storage/pdf/.x.pdf', '/var/site/storage/pdf/a/b.pdf', '/var/site/storage/pdf/x.pdf.txt', '/var/site/storage/pdf/x y.pdf',
                         '/share/filelist.md5', '/extension/x/settings/x.css' ) as $path )
            $this->assertSame( 0, preg_match( $static, $path ), $path );

        // Listed directories, but never served: a script's source, dot paths.
        $never = '~' . expVelocity::NEVER_STATIC . '~';
        foreach ( array( '/var/site/storage/images/a/x.php', '/design/standard/stylesheets/.hidden.css',
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
        $this->assertSame( 'recommended', expVelocity::create( 'velocity.ini', 'qbix' )->role() );
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

        // Threads are not the qbix engine's worker processes: an empty own value
        // gives FrankenPHP's default, twice the CPU cores (expVelocityFrankenPHP::threadCounts()).
        $this->assertStringContainsString( 'num_threads ' . ( 2 * expVelocityFrankenPHP::cpuCount() ) . "\n",
                                           expVelocity::create( 'velocity.ini', 'frankenphp' )->caddyfileText() );
        ezpINIHelper::setINISetting( 'velocity.ini', 'FrankenPHPSettings', 'Workers', '7' );
        $this->assertStringContainsString( "num_threads 7\n", expVelocity::create( 'velocity.ini', 'frankenphp' )->caddyfileText() );
        ezpINIHelper::setINISetting( 'velocity.ini', 'FrankenPHPSettings', 'Workers', '' );

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
        $config = json_decode( file_get_contents( $write->invoke( $velocity ) ), true );
        $this->assertSame( array( expVelocity::STATIC_PATHS ), $config['Q']['web']['static']['paths'] ?? null );

        // The same routes the frankenphp engine's Caddyfile has.
        $caddy = expVelocity::create( 'velocity.ini', 'frankenphp' )->caddyfileText();
        $this->assertStringContainsString( '^/(api/|index_rest\\.php)', $caddy );
        $this->assertStringContainsString( '^/([^/]+/)?content/treemenu', $caddy );
    }

    /**
     * [ServerSettings] ResponseHeaders[] and ResponseHeadersOnScripts, and
     * [HTTPSSettings] HSTSMaxAge: by default the four headers the front ends
     * send and HSTS max-age=300 reach Q.webserver of the site configuration.
     */
    public function testTheDefaultResponseHeadersAndHstsReachTheServer()
    {
        $webserver = $this->qbixWebserverConfig( expVelocity::create( 'velocity.ini', 'qbix' ) );
        $this->assertSame( array(
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), payment=(), usb=()',
        ), $webserver['headers'] ?? null );
        $this->assertTrue( $webserver['headersOnScripts'] ?? null );
        $this->assertSame( array( 'maxAge' => 300 ), $webserver['hsts'] ?? null );
    }

    public function testResponseHeadersLeaveOutWhatCannotBeAHeader()
    {
        ezpINIHelper::setINISetting( 'velocity.ini', 'ServerSettings', 'ResponseHeaders', array(
            'X-A: one', 'no colon', ': no name', 'X-Empty:', 'Bad Name: x',
            'x-a: a repeat', 'X-B:  spaced value  ', 'X-C: a: b',
        ) );
        $velocity = expVelocity::create( 'velocity.ini', 'qbix' );
        $this->assertSame( array( 'X-A' => 'one', 'X-B' => 'spaced value', 'X-C' => 'a: b' ), $velocity->responseHeaders() );

        ezpINIHelper::setINISetting( 'velocity.ini', 'ServerSettings', 'ResponseHeadersOnScripts', 'disabled' );
        $webserver = $this->qbixWebserverConfig( $velocity );
        $this->assertSame( 'one', $webserver['headers']['X-A'] ?? null );
        $this->assertArrayNotHasKey( 'headersOnScripts', $webserver );
    }

    /**
     * apc.enable_cli=1 for each cache that uses APCu, not only for the
     * response cache: the HTTP cache and the SQL query cache need it as much.
     */
    public function testApcuIsSwitchedOnForEachCacheThatUsesIt()
    {
        $wants = function () {
            $command = expVelocity::create( 'velocity.ini', 'qbix' )->command();
            return in_array( 'apc.enable_cli=1', $command, true );
        };
        ezpINIHelper::setINISetting( 'velocity.ini', 'PHPSettings', 'IniOptions', array() );
        ezpINIHelper::setINISetting( 'velocity.ini', 'CacheSettings', 'APCu', 'enabled' );
        ezpINIHelper::setINISetting( 'httpcache.ini', 'HttpCacheSettings', 'Enabled', 'disabled' );
        ezpINIHelper::setINISetting( 'httpcache.ini', 'HttpCacheSettings', 'APCu', 'enabled' );
        ezpINIHelper::setINISetting( 'querycache.ini', 'QueryCacheSettings', 'Mode', 'off' );

        ezpINIHelper::setINISetting( 'velocity.ini', 'CacheSettings', 'Enabled', 'enabled' );
        $this->assertTrue( $wants(), 'response cache' );

        ezpINIHelper::setINISetting( 'velocity.ini', 'CacheSettings', 'Enabled', 'disabled' );
        $this->assertFalse( $wants(), 'no cache wants APCu' );

        ezpINIHelper::setINISetting( 'httpcache.ini', 'HttpCacheSettings', 'Enabled', 'enabled' );
        $this->assertTrue( $wants(), 'HTTP cache' );
        ezpINIHelper::setINISetting( 'httpcache.ini', 'HttpCacheSettings', 'APCu', 'disabled' );
        $this->assertFalse( $wants(), 'HTTP cache on files only' );
        ezpINIHelper::setINISetting( 'httpcache.ini', 'HttpCacheSettings', 'Enabled', 'disabled' );

        ezpINIHelper::setINISetting( 'querycache.ini', 'QueryCacheSettings', 'Mode', 'request' );
        $this->assertFalse( $wants(), 'query cache per request' );
        ezpINIHelper::setINISetting( 'querycache.ini', 'QueryCacheSettings', 'Mode', 'shared' );
        $this->assertTrue( $wants(), 'query cache shared' );

        // The status output names the caches that want it.
        $velocity = expVelocity::create( 'velocity.ini', 'qbix' );
        $this->assertSame( array( 'query cache' ), $velocity->apcuReasons() );
        $this->assertSame( 'on, for the query cache', $velocity->apcuSummary() );
        ezpINIHelper::setINISetting( 'querycache.ini', 'QueryCacheSettings', 'Mode', 'off' );
        $this->assertSame( 'off, no cache uses it', expVelocity::create( 'velocity.ini', 'qbix' )->apcuSummary() );
        ezpINIHelper::setINISetting( 'querycache.ini', 'QueryCacheSettings', 'Mode', 'shared' );

        // IniOptions decides it when it names it.
        ezpINIHelper::setINISetting( 'velocity.ini', 'PHPSettings', 'IniOptions', array( 'apc.enable_cli=0' ) );
        $this->assertFalse( $wants(), 'IniOptions wins' );
        $this->assertStringContainsString( 'IniOptions', expVelocity::create( 'velocity.ini', 'qbix' )->apcuSummary() );
    }

    public function testAnEmptyHeaderListAndNoMaxAgeWriteNothing()
    {
        ezpINIHelper::setINISetting( 'velocity.ini', 'ServerSettings', 'ResponseHeaders', array() );
        ezpINIHelper::setINISetting( 'velocity.ini', 'HTTPSSettings', 'HSTSMaxAge', '0' );
        $velocity = expVelocity::create( 'velocity.ini', 'qbix' );
        $this->assertNull( $velocity->hstsSetting() );
        $webserver = $this->qbixWebserverConfig( $velocity );
        $this->assertArrayNotHasKey( 'headers', $webserver );
        $this->assertArrayNotHasKey( 'headersOnScripts', $webserver );
        $this->assertArrayNotHasKey( 'hsts', $webserver );
    }

    public function testHstsSubdomainsAndPreload()
    {
        ezpINIHelper::setINISetting( 'velocity.ini', 'HTTPSSettings', 'HSTSMaxAge', '31536000' );
        ezpINIHelper::setINISetting( 'velocity.ini', 'HTTPSSettings', 'HSTSIncludeSubDomains', 'enabled' );
        ezpINIHelper::setINISetting( 'velocity.ini', 'HTTPSSettings', 'HSTSPreload', 'enabled' );
        $this->assertSame(
            array( 'maxAge' => 31536000, 'includeSubDomains' => true, 'preload' => true ),
            expVelocity::create( 'velocity.ini', 'qbix' )->hstsSetting()
        );
    }

    /**
     * Q.webserver as the qbix engine writes it to var/tmp/velocity-server.json.
     */
    protected function qbixWebserverConfig( expVelocity $velocity )
    {
        $write = new ReflectionMethod( $velocity, 'writeServerConfig' );
        $path = $write->invoke( $velocity );
        $this->assertIsString( $path );
        $config = json_decode( file_get_contents( $path ), true );
        return $config['Q']['webserver'] ?? array();
    }

    /**
     * Q.web.cache as the qbix engine writes it.
     */
    protected function qbixWebCacheConfig( expVelocity $velocity )
    {
        $write = new ReflectionMethod( $velocity, 'writeServerConfig' );
        $config = json_decode( file_get_contents( $write->invoke( $velocity ) ), true );
        return $config['Q']['web']['cache'] ?? null;
    }

    /**
     * The server's built-in default has the response cache on, so a
     * [CacheSettings] Enabled=disabled that is left out of the configuration
     * kept it caching. It must be written as false.
     */
    public function testADisabledResponseCacheIsWrittenAsFalse()
    {
        ezpINIHelper::setINISetting( 'velocity.ini', 'CacheSettings', 'Enabled', 'disabled' );
        $cache = $this->qbixWebCacheConfig( expVelocity::create( 'velocity.ini', 'qbix' ) );
        $this->assertIsArray( $cache );
        $this->assertSame( false, $cache['enabled'] ?? null );

        ezpINIHelper::setINISetting( 'velocity.ini', 'CacheSettings', 'Enabled', 'enabled' );
        $cache = $this->qbixWebCacheConfig( expVelocity::create( 'velocity.ini', 'qbix' ) );
        $this->assertSame( true, $cache['enabled'] ?? null );
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
        // Bound to every address, whatever the installation's override says, so
        // the addresses read localhost.
        ezpINIHelper::setINISetting( 'velocity.ini', 'ServerSettings', 'Host', '' );
        ezpINIHelper::setINISetting( 'velocity.ini', 'FrankenPHPSettings', 'Host', '' );
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
