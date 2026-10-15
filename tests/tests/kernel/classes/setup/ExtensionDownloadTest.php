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
 *  ED-06 - under a secret name only an empty value or a whole placeholder passes (no look-alike, number or switch,
 *          unless the name announces a measure or a switch)
 *  ED-07 - every text file is read for the shapes of secrets, configuration files as pairs, UTF-16 and binary
 *          settings files are refused, more names of key material
 *  ED-08 - the package wizard removes a package refused while it is made and shows the reason
 *  ED-09 - the regression table: every input a check or a review found must be refused
 *  ED-10 - fail closed: a failing pattern (PCRE limit), too many entries, long names, "0", missing directories,
 *          names that are not UTF-8
 *  ED-11 - addExtension() decides on the copy it makes, not only on the source
 *  ED-12 - package.ini [ExtensionExportSettings] AllowedExtensions[] and the policy package/export_extension
 *  ED-04 - the view no longer packs from the address (/setup/extensions/<name>/<format>), and both designs post a
 *          button instead of linking
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use Exponential\View\Kernel\Setup\Extensions;

/** A package held in a throwaway directory: what addExtension() asks of it */
class ExtensionDownloadTestPackage extends eZPackage
{
    private $testPath;

    public function __construct( $path )
    {
        $this->testPath = $path;
    }

    function path()
    {
        return $this->testPath;
    }

    static function md5sum( $file )
    {
        return is_file( $file ) ? md5_file( $file ) : '';
    }

    function appendInstall( $type, $name, $os = false, $isInstall = true, $filename = false, $subdirectory = false, $parameters = false )
    {
    }
}

/** The package handler with a source check that finds nothing: what is refused then is refused on the copy */
class ExtensionDownloadTestBlindHandler extends eZExtensionPackageHandler
{
    static function packRefusal( $extensionName )
    {
        return '';
    }
}

/** The extension wizard with its package made in a throwaway directory */
class ExtensionDownloadTestCreator extends eZExtensionPackageCreator
{
    public static $path;

    function createPackage( &$package, $http, &$persistentData, &$cleanupFiles, $storePackage = true )
    {
        $package = new ExtensionDownloadTestPackage( self::$path );
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
        $this->assertSame( array( 'settings/siteaccess/site/site.ini.append.php: (address with a password)', 'settings/siteaccess/site/site.ini.append.php: ApiKey', 'settings/siteaccess/site/site.ini.append.php: Password',
                                  'settings/siteaccess/site/site.ini.append.php: Source' ), $this->secrets() );
        $this->assertSame( array( '.: not a directory' ), eZExtensionPackageHandler::secretFiles( $this->dir() . '/missing' ) );
    }

    /** ED-01, every file below settings/, and the names of passwords and authorizations */
    public function testEveryFileBelowSettingsAndMoreNamesAreRead()
    {
        $this->put( 'settings/logins.txt', "db_pass=hunter2\nProxyAuth=user:pw\nBearer=abc\nmysqli.default_pw=pw\nBypass=cache\nSmtpAuth=none\nSmtpPassword=login\nAuth=login\nMaxTokens=10\n" );
        $this->assertSame( array( 'settings/logins.txt: Auth', 'settings/logins.txt: Bearer', 'settings/logins.txt: ProxyAuth', 'settings/logins.txt: SmtpPassword',
                                  'settings/logins.txt: db_pass', 'settings/logins.txt: mysqli.default_pw' ), $this->secrets() );
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
        // a certificate is judged by what it holds: a public one (certs/server.pem, a CA bundle) passes, a private
        // key in a .pem, .crt or .asc file is refused
        $this->put( 'certs/tls.crt', "-----BEGIN CERTIFICATE-----\nMIIB\n-----END CERTIFICATE-----\n-----BEGIN RSA PRIVATE KEY-----\nMIIE\n-----END RSA PRIVATE KEY-----\n" );
        $this->assertSame( array( '.env: key material', 'certs/server.key: key material', 'certs/tls.crt: (private key)',
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
        $this->put( 'settings/siteaccess/site/e', "Server=mysql://u:hunter2@db/x\n; Url=https://u:hunter2@host/\n" );
        $this->put( 'settings/f.ini', "# ApiKey=...\n# Password=<...>\n# Password=xxxx\n# a token: in prose\nPassword=\nTimeout=30\nMaxTokens=4096\nRequireToken=enabled\nContextTokens=1000000\n" );
        $this->put( 'doc/example.ini.append.php', "Password=hunter2\n" );
        $this->assertSame( array( 'doc/example.ini.append.php: Password', 'settings/a.ini.append.php: Password', 'settings/b.ini: Password',
                                  'settings/b.ini: Token', 'settings/c.ini: Password', 'settings/d.ini: DbPass', 'settings/d.ini: Secret',
                                  'settings/siteaccess/site/e: (address with a password)', 'settings/siteaccess/site/e: Server',
                                  'settings/siteaccess/site/e: Url' ), $this->secrets() );
    }

    /** ED-06, under a secret name only an empty value or a whole placeholder passes: no number, switch or look-alike */
    public function testOnlyEmptyValuesAndWholePlaceholdersPass()
    {
        // an active setting is taken at its word: "secret" and "changeme" may be the password in use
        $refused = array( 'realsecret...', 'realsecret\u{2026}', 'secret', 'changeme', 'your_real_pw', '<abc123>', '12345678', '1234567890123456',
                          'yes', 'login', '%real' );
        foreach ( $refused as $value )
        {
            $this->assertTrue( eZExtensionPackageHandler::isSecretPair( 'Password', $value ), "Password=$value" );
            $this->assertTrue( eZExtensionPackageHandler::isSecretPair( 'ApiKey', $value ), "ApiKey=$value" );
        }
        // nothing, a placeholder, a switch or a template placeholder: no password is one of these
        foreach ( array( '', '""', '...', "\u{2026}", 'xxx', 'XXXXXXXX', '***', '<...>', "<\u{2026}>", '<your key>',
                         'true', 'false', 'none', 'null', '"none"', 'enabled', 'disabled', '0',
                         '%password%', '{password}', '${DB_PASSWORD}', '{{ secret }}', '%env(DB_PASS)%', 'Enter your private key here' ) as $value )
        {
            $this->assertFalse( eZExtensionPackageHandler::isSecretPair( 'Password', $value ), "Password=$value" );
            $this->assertFalse( eZExtensionPackageHandler::isSecretPair( 'ApiKey', $value ), "ApiKey=$value" );
        }
        // in a comment, a documentation word stands for the value: "# Password=changeme"
        foreach ( array( 'secret', 'changeme', 'dbpassword', 'your_password', 'tiger' ) as $value )
            $this->assertFalse( eZExtensionPackageHandler::isSecretPair( 'Password', $value, true ), "#Password=$value" );
        $this->assertTrue( eZExtensionPackageHandler::isSecretPair( 'Password', 'hunter2', true ), '#Password=hunter2' );
        foreach ( array( array( 'MaxTokens', '4096' ), array( 'ContextTokens', '1000000' ), array( 'CharsPerToken', '3.5' ),
                         array( 'PasswordLifeTime', '90' ), array( 'RequireToken', 'enabled' ), array( 'AllowSessionAuth', 'disabled' ),
                         array( 'Timeout', '30' ), array( 'Title', 'none' ) ) as $pair )
            $this->assertFalse( eZExtensionPackageHandler::isSecretPair( $pair[0], $pair[1] ), implode( '=', $pair ) );
        $this->put( 'settings/g.ini', "Password=realsecret...\nApiKey=1234567890123456\nToken=yes\n" );
        $this->assertSame( array( 'settings/g.ini: ApiKey', 'settings/g.ini: Password', 'settings/g.ini: Token' ), $this->secrets() );
    }

    /** ED-07, secrets by their shape in every text file, also outside settings/ */
    public function testShapesAreFoundInEveryTextFile()
    {
        $this->put( 'classes/a.php', "<?php\n\$k = \"-----BEGIN RSA PRIVATE KEY-----\\nMIIE\";\n" );
        $this->put( 'classes/b.php', "<?php\ndefine( 'MY_API_KEY', 'abc123' );\ndefine( 'TITLE', 'x' );\n" );
        $this->put( 'doc/c.md', "curl -H 'Authorization: Bearer abcdefgh12345678' https://api.example.invalid/\n" );
        $this->put( 'design/d.tpl', "{def \$key='sk-proj-abcdefghijklmnopqrstuvwxyz012345'}\n" );
        $this->put( 'lib/e.json', "{\"aws\": \"AKIAABCDEFGHIJKLMNOP\", \"gh\": \"ghp_abcdefghijklmnopqrstuvwxyz0123456789\"}\n" );
        $this->put( 'lib/f.xml', "<slack>xoxb-1234567890-abcdefghij</slack>\n" );
        $this->put( 'lib/g.txt', "mirror: https://deploy:hunter2@git.example.invalid/x.git\n" );
        $this->put( 'lib/clean.php', "<?php\n\$password = \$http->postVariable( 'Password' );\n// sk-... and AKIA... in prose\n" );
        $this->assertSame( array( 'classes/a.php: (private key)', 'classes/b.php: (constant)', 'design/d.tpl: (API key)',
                                  'doc/c.md: (bearer token)', 'lib/e.json: (API key)', 'lib/f.xml: (API key)',
                                  'lib/g.txt: (address with a password)' ), $this->secrets() );
    }

    /** ED-07, configuration files: "name: value", quoted names, YAML values on the next line */
    public function testConfigurationFilesAreReadAsPairs()
    {
        $this->put( 'config/a.yml', "db:\n  host: localhost\n  password: hunter2\n" );
        $this->put( 'config/b.yaml', "api_token:\n  abc123\nclient_secret: |\n  multi\n" );
        $this->put( 'config/c.conf', "auth_pass = s3cret\nport = 5432\n" );
        $this->put( 'docker/.env.local.example', "DB_PASSWORD=hunter2\n" );
        $this->put( 'config/d.yml', "password:\n  ...\ntimeout: 30\n" );
        $this->assertSame( array( 'config/a.yml: password', 'config/b.yaml: api_token', 'config/b.yaml: client_secret',
                                  'config/c.conf: auth_pass', 'docker/.env.local.example: key material' ), $this->secrets() );
    }

    /** ED-07, text the check cannot read is refused, binary files are not read */
    public function testUnreadableEncodingsAndBinaryFiles()
    {
        $this->put( 'settings/u.ini', "\xFF\xFEP\0a\0s\0s\0=\0x\0" );
        $this->put( 'doc/u16.txt', "\xFE\xFF\0P\0a" );
        $this->put( 'settings/bin.ini', "Title=x\0Password=hunter2" );
        $this->put( 'design/image.png', "\x89PNG\r\n\0\0Password=hunter2 sk-proj-abcdefghijklmnopqrstuvwxyz012345" );
        $this->assertSame( array( 'design/image.png: (API key)', 'doc/u16.txt: (text in UTF-16 or UTF-32)', 'settings/bin.ini: binary settings file',
                                  'settings/u.ini: (text in UTF-16 or UTF-32)' ), $this->secrets() );
    }

    /** ED-07, more names of key material, files and directories */
    public function testMoreKeyFileNames()
    {
        foreach ( array( 'credentials.json', 'gcp/my-service-account.json', 'old/private.p12.old', 'old/server.key.txt', 'certs.pem/cert', 'tls.KEY/a' ) as $file )
            $this->put( $file );
        $this->put( 'doc/keyboard.txt' );
        $this->assertSame( array( 'certs.pem/cert: key material', 'credentials.json: key material', 'gcp/my-service-account.json: key material',
                                  'old/private.p12.old: key material', 'old/server.key.txt: key material', 'tls.KEY/a: key material' ), $this->secrets() );
    }

    /** ED-08, a refusal while the wizard makes the package leaves nothing of it behind and is shown */
    public function testTheWizardRemovesARefusedPackage()
    {
        $this->put( 'certs/server.key' );
        $path = dirname( __DIR__, 5 ) . '/var/tmp/phpunit-extdownload-wizard-' . getmypid();
        ExtensionDownloadTestCreator::$path = $path;
        $creator = new ExtensionDownloadTestCreator( 'ezextension' );
        $package = false;
        $persistent = array( 'extensionlist' => array( 'classes_only_' . getmypid(), $this->name ) );
        mkdir( eZExtension::baseDirectory() . '/classes_only_' . getmypid() . '/classes', 0777, true );
        file_put_contents( eZExtension::baseDirectory() . '/classes_only_' . getmypid() . '/classes/a.php', "<?php\n" );
        try
        {
            $creator->finalize( $package, eZHTTPTool::instance(), $persistent );
            $this->fail( 'the wizard packs an extension with key material' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( 'certs/server.key: key material', $e->getMessage() );
        }
        finally
        {
            eZDir::recursiveDelete( eZExtension::baseDirectory() . '/classes_only_' . getmypid() );
        }
        $this->assertDirectoryDoesNotExist( $path, 'the extension packed before the refusal is removed' );

        $source = file_get_contents( dirname( __DIR__, 5 ) . '/kernel/private/classes/views/package/create.php' );
        $this->assertStringContainsString( "try\n                {\n                    \$creator->finalize( \$package, \$http, \$persistentData );\n                }\n                catch ( \\RuntimeException \$e )", $source );
        $this->assertStringContainsString( "'description' => \$finalizeError", $source );
    }

    /**
     * ED-09, the regression table: each input alone in an extension must be refused. It holds every way in that a
     * check or a review found, so a later change to the check cannot lose one of them unseen.
     */
    public static function refusedInputs()
    {
        $sk = 'sk-proj-abcdefghijklmnopqrstuvwxyz012345';
        $utf16 = '';
        foreach ( str_split( "token $sk" ) as $c )
            $utf16 .= $c . "\0";
        $big = str_repeat( "\0\x01", 2621440 ) . $sk;
        $edge = str_repeat( 'a', 1048576 - 10 ) . ' ' . $sk;
        return array(
            'commented #Password='        => array( 'settings/a.ini.append.php', "<?php /* #?ini charset=\"utf-8\"?\n#Password=hunter2\n*/ ?>\n" ),
            '; commented'                  => array( 'settings/a.ini', "; Password=hunter2\n" ),
            'Password[]='                  => array( 'settings/a.ini', "Password[]=hunter2\n" ),
            'Password[name]='              => array( 'settings/a.ini', "Password[db]=hunter2\n" ),
            'blanks around ='              => array( 'settings/a.ini', "  Password   =   hunter2  \n" ),
            'quoted value'                 => array( 'settings/a.ini', "Password=\"hunter2\"\n" ),
            'upper case name'              => array( 'settings/a.ini', "PASSWORD=hunter2\n" ),
            'lower case name'              => array( 'settings/a.ini', "password=hunter2\n" ),
            'UTF-8 BOM'                    => array( 'settings/a.ini', "\xEF\xBB\xBFPassword=hunter2\n" ),
            'CRLF'                         => array( 'settings/a.ini', "Title=x\r\nPassword=hunter2\r\n" ),
            'CR only'                      => array( 'settings/a.ini', "Title=x\rPassword=hunter2\r" ),
            'form feed'                    => array( 'settings/a.ini', "Title=x\fPassword=hunter2\n" ),
            'pair inside a value'          => array( 'settings/a.ini', "Title=x Password=hunter2\n" ),
            'duplicate key'                => array( 'settings/a.ini', "Password=\nPassword=hunter2\n" ),
            'placeholder suffix ...'       => array( 'settings/a.ini', "Password=realsecret...\n" ),
            "placeholder suffix \u{2026}"  => array( 'settings/a.ini', "Password=realsecret\u{2026}\n" ),
            'secret as placeholder'        => array( 'settings/a.ini', "Password=secret\n" ),
            'changeme'                     => array( 'settings/a.ini', "Password=changeme\n" ),
            'your_*'                       => array( 'settings/a.ini', "Password=your_real_pw\n" ),
            '<value>'                      => array( 'settings/a.ini', "Password=<abc123>\n" ),
            'numeric secret'               => array( 'settings/a.ini', "Password=12345678\n" ),
            'numeric key'                  => array( 'settings/a.ini', "ApiKey=1234567890123456\n" ),
            'settings without extension'   => array( 'settings/siteaccess/site/creds', "Password=hunter2\n" ),
            'ini outside settings'         => array( 'doc/example.ini', "Password=hunter2\n" ),
            'NUL in settings'              => array( 'settings/a.ini', "Title=x\0\nPassword=hunter2\n" ),
            'UTF-16 BOM settings'          => array( 'settings/a.ini', "\xFF\xFEP\0=\0x\0" ),
            'UTF-16 BOM text'              => array( 'doc/a.txt', "\xFE\xFF\0x" ),
            'UTF-16 without BOM'           => array( 'doc/a.txt', $utf16 ),
            'PEM in PHP'                   => array( 'classes/a.php', "<?php \$k = '-----BEGIN PRIVATE KEY-----';\n" ),
            'PEM in binary'                => array( 'design/a.png', "\x89PNG\0\0-----BEGIN EC PRIVATE KEY-----\n" ),
            'secret deep in a big binary'  => array( 'design/big.bin', $big ),
            'secret across a piece edge'   => array( 'doc/edge.txt', $edge ),
            'define() one line'            => array( 'classes/a.php', "<?php define( 'API_KEY', 'abc123' );\n" ),
            'define() lines'               => array( 'classes/a.php', "<?php define(\n    \"DB_PASSWORD\",\n    \"hunter2\"\n);\n" ),
            'Bearer'                       => array( 'doc/a.md', "Authorization: Bearer abcdefgh12345678\n" ),
            'Bearer next line'             => array( 'doc/a.md', "Authorization:\n  Bearer abcdefgh12345678\n" ),
            'sk- in JSON'                  => array( 'lib/a.json', "{\"key\": \"$sk\"}" ),
            'AKIA in template'             => array( 'design/a.tpl', "AKIAABCDEFGHIJKLMNOP" ),
            'address with a password'      => array( 'doc/a.txt', "https://deploy:hunter2@git.example.invalid/x\n" ),
            'YAML next line'               => array( 'config/a.yml', "password:\n  hunter2\n" ),
            'YAML after a blank line'      => array( 'config/a.yml', "password:\n\n  hunter2\n" ),
            'YAML block scalar'            => array( 'config/a.yml', "client_secret: |\n  hunter2\n" ),
            'YAML nested mapping'          => array( 'config/a.yml', "password:\n  value: hunter2\n" ),
            'YAML inline'                  => array( 'config/a.yaml', "db:\n  password: hunter2\n" ),
            '.conf pair'                   => array( 'config/a.conf', "auth_pass = s3cret\n" ),
            '.env file'                    => array( '.env', "X=1\n" ),
            'credentials file'             => array( 'credentials.json', "{}" ),
            'key file upper case'          => array( 'certs/A.KEY', "x" ),
            'key file with suffix'         => array( 'old/server.key.txt', "x" ),
        );
    }

    /**
     * @dataProvider refusedInputs
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'refusedInputs' )]
    public function testTheRegressionTableIsRefused( $file, $contents )
    {
        unlink( $this->dir() . '/settings/plain.ini' );
        $this->put( $file, $contents );
        $this->assertNotSame( array(), $this->secrets(), $file );
        $this->assertNotSame( '', eZExtensionPackageHandler::packRefusal( $this->name ) );
    }

    /** ED-09, and what must still pass */
    public function testCleanInputsPass()
    {
        $this->put( 'settings/b.ini', "Password=\nPassword=...\nApiKey=xxx\nToken=<...>\nMaxTokens=4096\nRequireToken=enabled\nTimeout=30\nTitle=none\n" );
        $this->put( 'config/c.yml', "password:\ntimeout: 30\ndb:\n  host: localhost\n" );
        $this->put( 'classes/d.php', "<?php\n\$password = \$http->postVariable( 'Password' );\ndefine( 'TITLE', 'x' );\n" );
        $this->put( 'design/e.png', "\x89PNG\0\0plain image bytes" );
        $this->assertSame( array(), $this->secrets() );
    }

    /** ED-10, any failure of a pattern is a refusal: the backtrack limit reached, and a value too long to read */
    public function testAPatternThatFailsRefuses()
    {
        $this->put( 'settings/long.ini', "Title=abc\n" );
        $limit = ini_get( 'pcre.backtrack_limit' );
        $jit = ini_get( 'pcre.jit' );
        ini_set( 'pcre.jit', '0' );
        ini_set( 'pcre.backtrack_limit', '1' );
        try
        {
            $secrets = $this->secrets();
            $refusal = eZExtensionPackageHandler::packRefusal( $this->name );
        }
        finally
        {
            ini_set( 'pcre.backtrack_limit', $limit );
            ini_set( 'pcre.jit', $jit );
        }
        $this->assertContains( 'settings/long.ini: could not be checked', $secrets );
        $this->assertNotSame( '', $refusal );
        try
        {
            ini_set( 'pcre.jit', '0' );
            ini_set( 'pcre.backtrack_limit', '100' );
            eZExtensionPackageHandler::pregMatch( '/(?:a|aa)+$/', str_repeat( 'a', 30 ) . '!' );
            $this->fail( 'a failed pattern reads as no match' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( 'pattern check failed', $e->getMessage() );
        }
        finally
        {
            ini_set( 'pcre.backtrack_limit', $limit );
            ini_set( 'pcre.jit', $jit );
        }
    }

    /** ED-10, limits and odd states fail closed: too many entries, a long name, a missing directory, "0" */
    public function testLimitsAndOddStatesFailClosed()
    {
        $this->put( 'settings/b.ini', str_repeat( 'N', eZExtensionPackageHandler::MAX_NAME_BYTES + 1 ) . "=1\n" );
        $this->assertSame( array( 'settings/b.ini: ' . str_repeat( 'N', eZExtensionPackageHandler::MAX_NAME_BYTES + 1 ) ), $this->secrets() );
        unlink( $this->dir() . '/settings/b.ini' );

        $this->put( '0', "sk-proj-abcdefghijklmnopqrstuvwxyz012345\n" );
        $this->put( 'settings/0', "Password=hunter2\n" );
        $this->put( 'settings/long.ini', 'Title=' . str_repeat( 'a', eZExtensionPackageHandler::MAX_VALUE_BYTES + 1 ) . "\n" );
        $this->assertSame( array( '0: (API key)', 'settings/0: Password', 'settings/long.ini: Title' ), $this->secrets() );
        unlink( $this->dir() . '/settings/long.ini' );
        unlink( $this->dir() . '/0' );
        unlink( $this->dir() . '/settings/0' );

        eZExtensionPackageHandler::$maxEntries = 3;
        try
        {
            $this->assertSame( array( '.: more than 3 entries' ), $this->secrets() );
        }
        finally
        {
            eZExtensionPackageHandler::$maxEntries = eZExtensionPackageHandler::MAX_ENTRIES;
        }

        $this->assertSame( array( '.: not a directory' ), eZExtensionPackageHandler::secretFiles( $this->dir() . '/missing' ) );
        $this->assertSame( array( '.: not a directory' ), eZExtensionPackageHandler::secretFiles( '' ) );
        mkdir( $this->dir() . '/empty' );
        $this->assertSame( array(), eZExtensionPackageHandler::secretFiles( $this->dir() . '/empty' ), 'an empty extension holds nothing to refuse' );
    }

    /** ED-10, a file name that is not UTF-8 is read like any other */
    public function testNamesThatAreNotUtf8()
    {
        // bytes that are not UTF-8: names by the byte patterns, values refused (no pattern reads them as nothing)
        $this->assertTrue( eZExtensionPackageHandler::pregMatch( eZExtensionPackageHandler::KEY_FILE_PATTERN, "\xFF.key" ) );
        $this->put( 'settings/bytes.ini', "Password=\xFF\xFE\xFD\nTitle=\xC3\x28\n" );
        $this->assertSame( array( 'settings/bytes.ini: Password' ), $this->secrets() );
        unlink( $this->dir() . '/settings/bytes.ini' );
        $this->put( 'settings/bytes.ini', "Password=x\xFF\n" );
        $this->assertSame( array( 'settings/bytes.ini: Password' ), $this->secrets() );
        unlink( $this->dir() . '/settings/bytes.ini' );

        if ( @file_put_contents( $this->dir() . "/\xFF.key", 'x' ) === false || @file_put_contents( $this->dir() . "/settings/\xFE", "Password=hunter2\n" ) === false )
            $this->markTestSkipped( 'the file system takes UTF-8 names only' );
        // sorted byte by byte: "s" (0x73) before 0xFF
        $this->assertSame( array( "settings/\xFE: Password", "\xFF.key: key material" ), $this->secrets() );
    }

    /** ED-11, the decision is made on the copy in the package: a source check that missed something does not pack it */
    public function testTheCopyIsCheckedNotOnlyTheSource()
    {
        $this->put( 'settings/site.ini', "Password=hunter2\n" );
        $package = new ExtensionDownloadTestPackage( dirname( __DIR__, 5 ) . '/var/tmp/phpunit-extdownload-copy-' . getmypid() );
        try
        {
            ExtensionDownloadTestBlindHandler::addExtension( $package, $this->name );
            $this->fail( 'packed although the copy holds a secret' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( 'settings/site.ini: Password', $e->getMessage() );
        }
        $this->assertDirectoryDoesNotExist( $package->path() . '/ezextension/' . $this->name, 'the refused copy is removed' );
        eZDir::recursiveDelete( $package->path() );
    }

    /** ED-12, package.ini [ExtensionExportSettings]: AllowedExtensions[] (an extension named "0" too) and the policy */
    public function testTheExportSettings()
    {
        $ini = eZINI::instance( 'package.ini' );
        $this->assertSame( array(), array_values( array_filter( (array)$ini->variable( 'ExtensionExportSettings', 'AllowedExtensions' ), 'strlen' ) ), 'shipped empty' );
        $this->assertSame( 'disabled', $ini->variable( 'ExtensionExportSettings', 'RequirePolicy' ) );
        $this->assertSame( '', eZExtensionPackageHandler::exportRefusal( $this->name, false ) );
        $saved = $ini->variable( 'ExtensionExportSettings', 'AllowedExtensions' );
        try
        {
            $ini->setVariable( 'ExtensionExportSettings', 'AllowedExtensions', array( '0' ) );
            $this->assertSame( '', eZExtensionPackageHandler::exportRefusal( '0', false ), 'an extension named "0" stays in the list' );
            $this->assertStringContainsString( 'AllowedExtensions', eZExtensionPackageHandler::exportRefusal( $this->name, false ) );
            try
            {
                eZExtensionPackageHandler::addExtension( null, $this->name );
                $this->fail( 'addExtension() ignores AllowedExtensions[]' );
            }
            catch ( RuntimeException $e )
            {
                $this->assertStringContainsString( 'AllowedExtensions', $e->getMessage() );
            }
        }
        finally
        {
            $ini->setVariable( 'ExtensionExportSettings', 'AllowedExtensions', $saved );
        }

        $root = dirname( __DIR__, 5 );
        include $root . '/kernel/package/module.php';
        $this->assertArrayHasKey( 'export_extension', $FunctionList );
        $handler = file_get_contents( $root . '/kernel/classes/packagehandlers/ezextension/ezextensionpackagehandler.php' );
        $this->assertStringContainsString( "hasAccessTo( 'package', 'export_extension' )", $handler );
        foreach ( array( 'kernel/private/classes/views/setup/extensions.php', 'kernel/classes/packagecreators/ezextension/ezextensionpackagecreator.php' ) as $caller )
            $this->assertStringContainsString( 'exportRefusal( ', file_get_contents( $root . '/' . $caller ), $caller );
    }

    /** ED-05, names in any case, hidden files, links and files the check cannot read */
    public function testCaseHiddenFilesLinksAndUnreadableFiles()
    {
        $this->put( 'certs/SERVER.KEY' );
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
            $this->assertSame( array( '.hidden/settings.ini: Password', 'certs/SERVER.KEY: key material',
                                      'config/site.INI.BAK: copy of a settings file', 'docker/.Env: key material',
                                      'settings/huge.ini: text too large to check', 'settings/linked.ini: link outside the extension' ),
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

    /**
     * ED-12, the other half of the regression table: each input alone in an extension must pack. These are the
     * switches, measures, addresses, placeholders and examples that ordinary extensions hold (each row from an
     * extension the check refused although it held no secret).
     */
    public static function packedInputs()
    {
        return array(
            'a switch named after a token'  => array( 'settings/debug.ini.append.php', "<?php /* #?ini charset=\"utf-8\"?\n[DebugSettings]\nezformtoken=disabled\n*/ ?>\n" ),
            'a browser version'             => array( 'settings/autosave.ini.append.php', "<?php /*\n[AutosaveSettings]\nIEDisableWithPassword[]\nIEDisableWithPassword[]=11\n*/ ?>\n" ),
            'Auth=none'                     => array( 'settings/a.ini', "Auth=none\n" ),
            'a header name'                 => array( 'settings/a.ini', "ApiKeyHeader=none\nApiKeyHeader[openai]=Authorization\n" ),
            'a switch under a secret name'  => array( 'settings/a.ini', "TransactionKey=false\nPassword=disabled\nToken=null\n" ),
            'a token address'               => array( 'settings/a.ini', "TokenURL=https://login.example.com/oauth2/token\n" ),
            'a public key'                  => array( 'settings/a.ini', "PublicKey=6LeAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA\n" ),
            'a fill-in text'                => array( 'settings/a.ini', "PrivateKey=Enter your private key here\n" ),
            'template placeholders'         => array( 'settings/a.ini', "MySQLDumpCommand=mysqldump --user={user} --password={password} --host={host} {database}\nPassword=%db_password%\nApiKey=\${API_KEY}\n" ),
            'a computed YAML value'         => array( 'vendor/x/.github/workflows/tests.yml', "steps:\n  - uses: actions/cache@v4\n    with:\n      key: composer-\${{ matrix.php }}-\${{ hashFiles('composer.lock') }}\n" ),
            'a comment that explains'       => array( 'settings/a.ini', "#   ClientSecret = the secret of the client\nClientSecret=\n" ),
            'a commented example'           => array( 'settings/a.ini', "#DBPassword=tiger\n# Password=changeme\n" ),
            'a public certificate bundle'   => array( 'cacert.pem', "##  Bundle of CA Root Certificates\n-----BEGIN CERTIFICATE-----\nMIIB\n-----END CERTIFICATE-----\n" ),
            'a documented constant'         => array( 'INSTALL.md', "define( 'CLUSTER_STORAGE_PASS', 'dbpassword' );\n" ),
            'example addresses'             => array( 'doc/a.md', "new URL('http://abc:xyz@example.com');\nhttps://user:pass@host/ https://u:p@host/ https://a:b@host/\n" ),
            'test tokens'                   => array( 'tests/a_test.php', "<?php \$h = 'Authorization: Bearer test-key-123456';\n\$k = 'sk-test-SECRET-abcdefghijklmnopqrstuvwxyz';\n" ),
        );
    }

    /**
     * @dataProvider packedInputs
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'packedInputs' )]
    public function testTheseArePacked( $file, $contents )
    {
        $this->put( $file, $contents );
        $this->assertSame( array(), $this->secrets(), $file );
    }

    /** ED-13, the extension's package_check.ini narrows the findings it names, and nothing else */
    /** A build's dependencies (node_modules) are not packed and not read: a test key of a library in them refuses nothing */
    public function testNodeModulesAreNeitherPackedNorRead()
    {
        $this->put( 'node_modules/lib/cert.js', "const key = '-----BEGIN PRIVATE KEY-----\\nMIIx\\n-----END PRIVATE KEY-----';\n" );
        $this->put( 'design/standard/javascript/editor.js', "x\n" );
        $this->assertSame( array(), $this->secrets() );
        $entries = eZExtensionPackageHandler::packEntries( $this->dir() );
        $this->assertSame( array( 'type' => 'dir', 'packed' => false ), $entries['node_modules'] );
        $this->assertArrayNotHasKey( 'node_modules/lib', $entries );
        // the same key outside node_modules still refuses the extension
        $this->put( 'design/standard/javascript/cert.js', "const key = '-----BEGIN PRIVATE KEY-----\\nMIIx\\n-----END PRIVATE KEY-----';\n" );
        $this->assertNotSame( array(), $this->secrets() );
    }

    public function testTheAllowFileNarrowsOnlyWhatItNames()
    {
        $this->put( 'settings/recaptcha.ini', "#   PrivateKey=6LcAbCdEfGhIjKlMnOpQrStUvWxYz0123456789\nApiKey=s3cretvalue\n" );
        $this->put( 'certs/tls.crt', "-----BEGIN PRIVATE KEY-----\nMIIE\n-----END PRIVATE KEY-----\n" );
        $this->put( 'settings/site.ini~', "Title=x\n" );
        $this->assertSame( array( 'certs/tls.crt: (private key)', 'settings/recaptcha.ini: ApiKey', 'settings/recaptcha.ini: PrivateKey',
                                  'settings/site.ini~: copy of a settings file' ), $this->secrets() );

        $this->put( eZExtensionPackageHandler::ALLOW_FILE,
                    "[PackageCheck]\n# the example key of the documentation comment\nAllowedFindings[]=settings/recaptcha.ini:PrivateKey\n"
                    . "AllowedFindings[]=certs/tls.crt:(private key)\nAllowedFindings[]=settings/site.ini~:copy of a settings file\n"
                    . "AllowedFindings[]=../other/settings/recaptcha.ini:ApiKey\nAllowedFindings[]=settings/*:ApiKey\n" );
        // the named example passes; a private key, a copy, a path outside and a wildcard never do
        $this->assertSame( array( 'certs/tls.crt: (private key)', 'settings/recaptcha.ini: ApiKey',
                                  'settings/site.ini~: copy of a settings file' ), $this->secrets() );

        // a directory entry ends in "/"
        $this->put( eZExtensionPackageHandler::ALLOW_FILE, "AllowedFindings[]=settings/:ApiKey\nAllowedFindings[]=settings/:PrivateKey\n" );
        unlink( $this->dir() . '/certs/tls.crt' );
        unlink( $this->dir() . '/settings/site.ini~' );
        $this->assertSame( array(), $this->secrets() );

        // a link as the allow file allows nothing
        unlink( $this->dir() . '/' . eZExtensionPackageHandler::ALLOW_FILE );
        file_put_contents( $this->dir() . '/settings/allow.txt', "AllowedFindings[]=settings/:ApiKey\nAllowedFindings[]=settings/:PrivateKey\n" );
        symlink( $this->dir() . '/settings/allow.txt', $this->dir() . '/' . eZExtensionPackageHandler::ALLOW_FILE );
        $this->assertContains( 'settings/recaptcha.ini: ApiKey', $this->secrets() );
    }

    /** ED-14, the extensions that ship with the kernel (share/filelist.md5) can be packed */
    public function testTheBundledExtensionsPass()
    {
        $root = dirname( __DIR__, 5 );
        preg_match_all( '#^[0-9a-f]{32}\s+extension/([^/\s]+)/#m', (string)file_get_contents( $root . '/share/filelist.md5' ), $m );
        $names = array_unique( $m[1] );
        $this->assertNotEmpty( $names );
        foreach ( $names as $name )
        {
            if ( !is_dir( $root . '/extension/' . $name ) )
                continue;
            $this->assertSame( array(), eZExtensionPackageHandler::secretFiles( $root . '/extension/' . $name ), $name );
        }
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
