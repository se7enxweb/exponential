<?php
/**
 * Setup > Extensions hands an extension out as a package archive only through a button of its form (POST, with
 * the form token), and no package takes an extension that holds secret values or key material
 * (\Exponential\View\Kernel\Setup\Extensions::download(), eZExtensionPackageHandler). No database.
 *
 *  ED-01 - eZExtensionPackageHandler::secretFiles(), the rule of every package: a secret name with a value, or a
 *          value with a password in it, in any file below settings/; copies of settings files; key material anywhere.
 *          Empty values, numbers, switches and comments are not secrets. addExtension() and the package wizard refuse.
 *  ED-02 - download() refuses an extension with a secret setting before anything is packed
 *  ED-03 - download() refuses an unknown extension or format
 *  ED-05 - the check reads what the package takes, as text: commented lines, Key[]=, blanks, quotes, BOM and CRLF,
 *          names in any case, hidden files, links outside the extension, files too big to read; the package holds
 *          exactly the entries the check marked as packed
 *  ED-04 - the view no longer packs from the address (/setup/extensions/<name>/<format>), and both designs post a
 *          button instead of linking
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use Exponential\View\Kernel\Setup\Extensions;

/** A package held in a throwaway directory: what addExtension() asks of it */
class ExtensionDownloadTestPackage
{
    private $path;

    public function __construct( $path )
    {
        $this->path = $path;
    }

    public function path()
    {
        return $this->path;
    }

    public function md5sum( $file )
    {
        return is_file( $file ) ? md5_file( $file ) : '';
    }

    public function appendInstall()
    {
    }
}

class ExtensionDownloadTest extends PHPUnit\Framework\TestCase
{
    /** @var string the working directory before the test */
    private $cwd;

    /** @var string an extension made for the test, below extension/ */
    private $name;

    protected function setUp(): void
    {
        $this->cwd = getcwd();
        chdir( dirname( __DIR__, 5 ) );
        $this->name = 'phpunitextdownload' . getmypid();
        $settings = eZExtension::baseDirectory() . '/' . $this->name . '/settings';
        mkdir( $settings . '/siteaccess/site', 0777, true );
        file_put_contents( $settings . '/plain.ini', "<?php /* #?ini charset=\"utf-8\"?\n[Limits]\nMaxTokens=4096\nRequireToken=enabled\nPassword=\n# a comment\n*/ ?>\n" );
    }

    protected function tearDown(): void
    {
        eZDir::recursiveDelete( eZExtension::baseDirectory() . '/' . $this->name );
        chdir( $this->cwd );
    }

    private function dir()
    {
        return eZExtension::baseDirectory() . '/' . $this->name;
    }

    private function download( $choice, array $available )
    {
        $view = ( new ReflectionClass( Extensions::class ) )->newInstanceWithoutConstructor();
        $method = new ReflectionMethod( Extensions::class, 'download' );
        return $method->invoke( $view, $choice, $available );
    }

    private function put( $relative, $contents = "x\n" )
    {
        $path = $this->dir() . '/' . $relative;
        if ( !is_dir( dirname( $path ) ) )
            mkdir( dirname( $path ), 0777, true );
        file_put_contents( $path, $contents );
    }

    private function secrets()
    {
        return eZExtensionPackageHandler::secretFiles( $this->dir() );
    }

    /** ED-01 */
    public function testSecretSettingsAreFound()
    {
        $this->assertSame( array(), $this->secrets() );
        $this->put( 'settings/siteaccess/site/site.ini.append.php',
                    "<?php /* #?ini charset=\"utf-8\"?\n[DatabaseSettings]\nUser=db\nPassword=hunter2\n[Feed]\nSource=https://reader:pw1@feeds.example.invalid/x\nApiKey[default]=abc\n*/ ?>\n" );
        $this->assertSame( array( 'settings/siteaccess/site/site.ini.append.php: ApiKey', 'settings/siteaccess/site/site.ini.append.php: Password',
                                  'settings/siteaccess/site/site.ini.append.php: Source' ), $this->secrets() );
        $this->assertSame( array(), eZExtensionPackageHandler::secretFiles( $this->dir() . '/missing' ) );
    }

    /** ED-01, every file below settings/, and the names of passwords and authorizations */
    public function testEveryFileBelowSettingsAndMoreNamesAreRead()
    {
        $this->put( 'settings/credentials.txt', "db_pass=hunter2\nProxyAuth=user:pw\nBearer=abc\nmysqli.default_pw=pw\nBypass=cache\nSmtpAuth=none\nAuth=login\nMaxTokens=10\n" );
        $this->assertSame( array( 'settings/credentials.txt: Auth', 'settings/credentials.txt: Bearer', 'settings/credentials.txt: ProxyAuth',
                                  'settings/credentials.txt: db_pass', 'settings/credentials.txt: mysqli.default_pw' ), $this->secrets() );
    }

    /** ED-01, copies of settings files */
    public function testCopiesOfSettingsFilesAreRefused()
    {
        foreach ( array( 'site.ini.bak', 'site.ini.orig', 'site.ini.append.php.dist', 'site.ini~', 'site.ini.old' ) as $name )
            $this->put( 'settings/' . $name, "Title=nothing secret\n" );
        $this->put( 'doc/notes.bak', "Title=outside settings, not a settings file\n" );
        $this->assertSame( array( 'settings/site.ini.append.php.dist: copy of a settings file', 'settings/site.ini.bak: copy of a settings file',
                                  'settings/site.ini.old: copy of a settings file', 'settings/site.ini.orig: copy of a settings file',
                                  'settings/site.ini~: copy of a settings file' ), $this->secrets() );
    }

    /** ED-01, key material anywhere in the extension */
    public function testKeyMaterialIsFoundAnywhere()
    {
        foreach ( array( 'certs/server.pem', 'certs/server.key', 'store/client.p12', 'store/client.pfx', '.env', 'docker/.env.local',
                         'ssh/id_rsa', 'ssh/id_ed25519.pub', 'var/jwt/public.txt', 'lib/.ssh/config' ) as $file )
            $this->put( $file );
        foreach ( array( '.env.example', 'docker/.env.sample', 'doc/keyboard.md', 'classes/keys.php', 'design/monkey.png' ) as $file )
            $this->put( $file );
        $this->assertSame( array( '.env: key material', 'certs/server.key: key material', 'certs/server.pem: key material',
                                  'docker/.env.local: key material', 'lib/.ssh/config: key material', 'ssh/id_ed25519.pub: key material',
                                  'ssh/id_rsa: key material', 'store/client.p12: key material', 'store/client.pfx: key material',
                                  'var/jwt/public.txt: key material' ), $this->secrets() );
    }

    /** ED-05, what eZINI reads differently is read as text: comments, arrays, blanks, quotes, BOM and CRLF */
    public function testTheRawTextIsRead()
    {
        $this->put( 'settings/a.ini.append.php', "<?php /* #?ini charset=\"utf-8\"?\n[Db]\n#Password=hunter2\n*/ ?>\n" );
        $this->put( 'settings/b.ini', "[Db]\nPassword[]=hunter2\nToken[main]=abc\n" );
        $this->put( 'settings/c.ini', "[Db]\n  Password   =   \"hunter2\"  \n" );
        $this->put( 'settings/d.ini', "\xEF\xBB\xBFSecret=one\r\nUser=x\r\nDbPass=two\rTitle=three\r\n" );
        $this->put( 'settings/siteaccess/site/e', "Server=mysql://u:hunter2@db/x\n; Url=https://u:pw@host/\n" );
        $this->put( 'settings/f.ini', "# ApiKey=sk-...\n# Password=<your password>\n# SearchServerUserPass=username:password\n# a token: in prose\nPassword=\nTimeout=30\n" );
        $this->put( 'doc/example.ini.append.php', "Password=hunter2\n" );
        $this->assertSame( array( 'doc/example.ini.append.php: Password', 'settings/a.ini.append.php: Password', 'settings/b.ini: Password',
                                  'settings/b.ini: Token', 'settings/c.ini: Password', 'settings/d.ini: DbPass', 'settings/d.ini: Secret',
                                  'settings/siteaccess/site/e: Server', 'settings/siteaccess/site/e: Url' ), $this->secrets() );
    }

    /** ED-05, names in any case, hidden files, links and files the check cannot read */
    public function testCaseHiddenFilesLinksAndUnreadableFiles()
    {
        $this->put( 'certs/SERVER.PEM' );
        $this->put( 'docker/.Env' );
        $this->put( 'config/site.INI.BAK', "Title=x\n" );
        $this->put( '.hidden/settings.ini', "Password=hunter2\n" );
        $outside = dirname( __DIR__, 5 ) . '/var/tmp/phpunit-extdownload-outside-' . getmypid();
        file_put_contents( $outside, "Password=hunter2\n" );
        symlink( $outside, $this->dir() . '/settings/linked.ini' );
        symlink( 'plain.ini', $this->dir() . '/settings/inside.ini' );
        $this->put( 'settings/huge.ini', str_repeat( 'x', eZExtensionPackageHandler::MAX_SCAN_BYTES + 1 ) );
        try
        {
            $this->assertSame( array( '.hidden/settings.ini: Password', 'certs/SERVER.PEM: key material',
                                      'config/site.INI.BAK: copy of a settings file', 'docker/.Env: key material',
                                      'settings/huge.ini: too large or unreadable to check', 'settings/linked.ini: link outside the extension' ),
                               $this->secrets() );
        }
        finally
        {
            unlink( $outside );
        }

        mkdir( $this->dir() . '/locked' );
        chmod( $this->dir() . '/locked', 0 );
        try
        {
            if ( is_readable( $this->dir() . '/locked' ) )
                $this->markTestSkipped( 'runs as a user who reads every directory' );
            $this->assertSame( array( '.: unreadable directory: locked' ), $this->secrets() );
        }
        finally
        {
            chmod( $this->dir() . '/locked', 0755 );
        }
    }

    /** ED-05, the package holds exactly the entries the check read and marked as packed */
    public function testThePackageHoldsWhatTheCheckRead()
    {
        $this->put( 'classes/a.php', "<?php\n" );
        $this->put( '.git/config', "x\n" );
        $this->put( 'design/.cache', "x\n" );
        symlink( 'classes', $this->dir() . '/alias' );
        $package = new ExtensionDownloadTestPackage( dirname( __DIR__, 5 ) . '/var/tmp/phpunit-extdownload-pkg-' . getmypid() );
        try
        {
            eZExtensionPackageHandler::addExtension( $package, $this->name );
            $copied = array();
            eZDir::recursiveList( $package->path() . '/ezextension/' . $this->name, '', $copied );
            $copied = array_map( function ( $f ) { return ltrim( $f['path'] . '/' . $f['name'], '/' ); }, $copied );
            sort( $copied );
            $packed = array_keys( array_filter( eZExtensionPackageHandler::packEntries( $this->dir() ), function ( $e ) { return $e['packed']; } ) );
            sort( $packed );
            $this->assertSame( $packed, $copied );
            $this->assertSame( array( 'classes', 'classes/a.php', 'design', 'settings', 'settings/plain.ini', 'settings/siteaccess', 'settings/siteaccess/site' ), $copied );
        }
        finally
        {
            eZDir::recursiveDelete( $package->path() );
            unlink( $this->dir() . '/alias' );
        }
    }

    /** ED-01, every way to a package: the handler itself and the package wizard refuse */
    public function testThePackageHandlerAndTheWizardRefuse()
    {
        $this->put( 'certs/server.key' );
        try
        {
            eZExtensionPackageHandler::addExtension( null, $this->name );
            $this->fail( 'addExtension() packs an extension with key material' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( 'certs/server.key: key material', $e->getMessage() );
        }
        try
        {
            eZExtensionPackageHandler::addExtension( null, '../' . basename( dirname( __DIR__, 5 ) ) );
            $this->fail( 'addExtension() takes a path' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( 'is not known', $e->getMessage() );
        }

        $_POST = array( 'PackageExtensionNames' => array( $this->name, '../settings' ) );
        $errors = array();
        $stepMap = array();
        $persistent = array();
        $creator = new eZExtensionPackageCreator( 'ezextension' );
        try
        {
            $this->assertFalse( $creator->validateExtensionName( null, eZHTTPTool::instance(), 'extensionname', $stepMap, $persistent, $errors ) );
        }
        finally
        {
            $_POST = array();
        }
        $this->assertCount( 2, $errors );
        $this->assertStringContainsString( 'certs/server.key', $errors[0]['description'] );
        $this->assertStringContainsString( 'is not known', $errors[1]['description'] );
    }

    /** ED-02 */
    public function testAnExtensionWithASecretIsNotPacked()
    {
        file_put_contents( $this->dir() . '/settings/site.ini.append.php', "<?php /* #?ini charset=\"utf-8\"?\n[DatabaseSettings]\nPassword=hunter2\n*/ ?>\n" );
        $error = $this->download( $this->name . '/zip', array( $this->name ) );
        $this->assertStringContainsString( 'site.ini.append.php: Password', $error );
        $this->assertStringNotContainsString( 'hunter2', $error );
    }

    /** ED-03 */
    public function testUnknownExtensionsAndFormatsAreRefused()
    {
        $this->assertSame( 'The extension or the archive format is not known.', $this->download( $this->name . '/exe', array( $this->name ) ) );
        $this->assertSame( 'The extension or the archive format is not known.', $this->download( $this->name . '/zip', array() ) );
        $this->assertSame( 'The extension or the archive format is not known.', $this->download( '../' . $this->name . '/zip', array( '../' . $this->name ) ) );
    }

    /** ED-04 */
    public function testOnlyThePostedButtonDownloads()
    {
        $root = dirname( __DIR__, 5 );
        $source = file_get_contents( $root . '/kernel/private/classes/views/setup/extensions.php' );
        $this->assertStringNotContainsString( "\$Params['ExtensionFormat']", $source );
        $this->assertStringContainsString( "\$http->hasPostVariable( 'ExtensionDownload' )", $source );
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            $tpl = file_get_contents( $root . "/design/$design/templates/setup/extensions.tpl" );
            $this->assertStringNotContainsString( "'/setup/extensions/', \$row.name", $tpl, $design );
            $this->assertStringContainsString( 'name="ExtensionDownload"', $tpl, $design );
        }
    }
}
