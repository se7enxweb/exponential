<?php
/**
 * Upload from a URL in the online editor: the address checks, the limits, the file checks and one real fetch of a
 * small public image served by this installation itself, which creates an image object under Media (node 43) that
 * is removed in tearDown. Live installation, no test database. The upload goes into a draft of the admin's own, as
 * from content/edit; a published version and someone else's draft are refused and nothing is created.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezoe/expOEUrlFetcherTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expOETestCase.php';

class expOEUrlFetcherTest extends expOETestCase
{
    const IMAGE_URL = 'https://alpha.se7enx.com/design/standard/images/32x32.gif';

    protected $objectIDs = array();
    protected $savedFromUrl = null;
    protected $savedMax = null;
    protected $savedTimeout = null;
    protected $temporary = array();

    public function setUp(): void
    {
        parent::setUp();
        $ini = eZINI::instance( 'ezoe.ini' );
        $this->savedFromUrl = $ini->hasVariable( 'EditorSettings', 'UploadFromUrl' ) ? $ini->variable( 'EditorSettings', 'UploadFromUrl' ) : 'enabled';
        $this->savedTimeout = $ini->hasVariable( 'EditorSettings', 'UploadFromUrlTimeout' ) ? $ini->variable( 'EditorSettings', 'UploadFromUrlTimeout' ) : '300';
        $this->savedMax = $ini->hasVariable( 'EditorSettings', 'UploadFromUrlMaxSize' ) ? $ini->variable( 'EditorSettings', 'UploadFromUrlMaxSize' ) : '20M';
        expOEUrlFetcher::$resolver = null;
    }

    public function tearDown(): void
    {
        expOEUrlFetcher::$resolver = null;
        foreach ( $this->objectIDs as $id )
        {
            $object = eZContentObject::fetch( $id );
            if ( $object instanceof eZContentObject )
                $object->purge();
        }
        $this->objectIDs = array();
        foreach ( $this->temporary as $file )
        {
            if ( is_file( $file ) )
                unlink( $file );
        }
        $ini = eZINI::instance( 'ezoe.ini' );
        $ini->setVariable( 'EditorSettings', 'UploadFromUrl', $this->savedFromUrl );
        $ini->setVariable( 'EditorSettings', 'UploadFromUrlMaxSize', $this->savedMax );
        $ini->setVariable( 'EditorSettings', 'UploadFromUrlTimeout', $this->savedTimeout );
        parent::tearDown();
    }

    public static function acceptedUrls()
    {
        return array( array( 'http://93.184.216.34/a.png' ), array( 'https://93.184.216.34:8443/a/b.png?x=1' ), array( 'https://[2606:4700:4700::1111]/a.png' ) );
    }

    public static function refusedUrls()
    {
        return array(
            'empty' => array( '' ), 'no scheme' => array( 'example.com/a.png' ), 'ftp' => array( 'ftp://93.184.216.34/a.png' ),
            'file' => array( 'file:///etc/passwd' ), 'gopher' => array( 'gopher://93.184.216.34/' ), 'javascript' => array( 'javascript:alert(1)' ),
            'data' => array( 'data:image/png;base64,AAAA' ), 'credentials' => array( 'http://user:pass@93.184.216.34/a.png' ),
            'user only' => array( 'http://user@93.184.216.34/a.png' ), 'space' => array( 'http://93.184.216.34/a b.png' ),
            'loopback' => array( 'http://127.0.0.1/a.png' ), 'loopback 127.1.2.3' => array( 'http://127.1.2.3/' ), 'localhost' => array( 'http://localhost/a.png' ),
            'private 10' => array( 'http://10.1.2.3/a.png' ), 'private 172' => array( 'http://172.16.0.1/' ), 'private 192' => array( 'https://192.168.1.1/' ),
            'link-local' => array( 'http://169.254.169.254/latest/meta-data/' ), 'unspecified' => array( 'http://0.0.0.0/' ),
            'cgnat' => array( 'http://100.64.0.1/' ), 'multicast' => array( 'http://224.0.0.1/' ), 'reserved' => array( 'http://240.0.0.1/' ),
            'ipv6 loopback' => array( 'http://[::1]/a.png' ), 'ipv6 link-local' => array( 'http://[fe80::1]/' ), 'ipv6 unique local' => array( 'http://[fd00::1]/' ),
            'ipv6 mapped loopback' => array( 'http://[::ffff:127.0.0.1]/' ), 'ipv6 mapped private' => array( 'http://[::ffff:10.0.0.1]/' ),
            'ipv6 multicast' => array( 'http://[ff02::1]/' ), 'decimal ip' => array( 'http://2130706433/' ), 'hex ip' => array( 'http://0x7f000001/' ),
            'short ip' => array( 'http://127.1/' ), 'too long' => array( 'http://93.184.216.34/' . str_repeat( 'a', 2100 ) ),
        );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'acceptedUrls' )]
    public function testPublicAddressesAreAccepted( $url )
    {
        $target = expOEUrlFetcher::validateUrl( $url );
        $this->assertContains( $target['scheme'], array( 'http', 'https' ) );
        $this->assertTrue( expOEUrlFetcher::isPublicIp( $target['ip'] ) );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'refusedUrls' )]
    public function testUnsafeAddressesAreRefused( $url )
    {
        $this->expectException( expOEUrlException::class );
        expOEUrlFetcher::validateUrl( $url );
    }

    public function testEveryResolvedAddressMustBePublic()
    {
        expOEUrlFetcher::$resolver = function ( $host ) { return array( '93.184.216.34', '10.0.0.5' ); };
        $this->expectException( expOEUrlException::class );
        expOEUrlFetcher::validateUrl( 'http://mixed.example/a.png' );
    }

    public function testAHostThatResolvesToAPrivateAddressIsRefused()
    {
        expOEUrlFetcher::$resolver = function ( $host ) { return array( '127.0.0.1' ); };
        $this->expectException( expOEUrlException::class );
        expOEUrlFetcher::validateUrl( 'http://rebinding.example/a.png' );
    }

    public function testAHostThatDoesNotResolveIsRefused()
    {
        expOEUrlFetcher::$resolver = function ( $host ) { return array(); };
        $this->expectException( expOEUrlException::class );
        expOEUrlFetcher::validateUrl( 'http://nowhere.example/a.png' );
    }

    public function testTheConnectionGoesToTheCheckedAddress()
    {
        expOEUrlFetcher::$resolver = function ( $host ) { return array( '93.184.216.34' ); };
        $target = expOEUrlFetcher::validateUrl( 'http://cdn.example/a.png' );
        $this->assertSame( '93.184.216.34', $target['ip'] );
        $this->assertSame( 'cdn.example', $target['host'] );
        $this->assertSame( 80, $target['port'] );
    }

    public static function redirects()
    {
        return array(
            'to loopback' => array( 'http://127.0.0.1/secret' ), 'to metadata' => array( 'http://169.254.169.254/latest/' ),
            'to private' => array( 'http://192.168.0.10/admin' ), 'protocol relative to private' => array( '//10.0.0.1/x' ),
            'to ftp' => array( 'ftp://93.184.216.34/x' ), 'to file' => array( 'file:///etc/passwd' ), 'to localhost' => array( 'https://localhost/x' ),
        );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'redirects' )]
    public function testARedirectToAnUnsafeTargetIsRefused( $location )
    {
        // fetch() resolves every Location against the URL it came from and validates it like a first URL
        $next = expOEUrlFetcher::resolveLocation( 'http://93.184.216.34/start.png', $location );
        $this->expectException( expOEUrlException::class );
        expOEUrlFetcher::validateUrl( $next );
    }

    public function testRelativeRedirectsStayOnTheHost()
    {
        $this->assertSame( 'http://93.184.216.34/a/new.png', expOEUrlFetcher::resolveLocation( 'http://93.184.216.34/a/old.png', 'new.png' ) );
        $this->assertSame( 'https://93.184.216.34:8443/new.png', expOEUrlFetcher::resolveLocation( 'https://93.184.216.34:8443/a/old.png', '/new.png' ) );
    }

    public function testIpClassification()
    {
        foreach ( array( '8.8.8.8', '93.184.216.34', '2606:4700:4700::1111', '::ffff:8.8.8.8' ) as $ip )
            $this->assertTrue( expOEUrlFetcher::isPublicIp( $ip ), $ip );
        foreach ( array( '127.0.0.1', '10.0.0.1', '172.31.255.255', '192.168.0.1', '169.254.1.1', '0.0.0.1', '100.127.0.1', '224.0.0.1', '255.255.255.255',
                         '::1', '::', 'fe80::1', 'fc00::1', 'fdff::1', 'ff02::1', '::ffff:127.0.0.1', '64:ff9b::7f00:1', '2001:db8::1', 'not an ip' ) as $ip )
            $this->assertFalse( expOEUrlFetcher::isPublicIp( $ip ), $ip );
        // the edges of 172.16.0.0/12
        $this->assertTrue( expOEUrlFetcher::isPublicIp( '172.15.255.255' ) );
        $this->assertTrue( expOEUrlFetcher::isPublicIp( '172.32.0.1' ) );
    }

    public function testSizeLimitIsTheSettingOnlyAndNotCappedByPhp()
    {
        $this->assertSame( 145 * 1048576, expOEUrlFetcher::maxSize(), 'the default is 145M' );
        $this->ini( 'UploadFromUrlMaxSize', '1K' );
        $this->assertSame( 1024, expOEUrlFetcher::maxSize() );
        $this->ini( 'UploadFromUrlMaxSize', '500M' );
        $this->assertSame( 500 * 1048576, expOEUrlFetcher::maxSize() );
        $this->ini( 'UploadFromUrlMaxSize', 'nonsense' );
        $this->assertSame( 145 * 1048576, expOEUrlFetcher::maxSize() );
    }

    public function testTheTotalTimeoutIsASetting()
    {
        $this->assertSame( 300, expOEUrlFetcher::timeout() );
        $this->ini( 'UploadFromUrlTimeout', '45' );
        $this->assertSame( 45, expOEUrlFetcher::timeout() );
        $this->ini( 'UploadFromUrlTimeout', '0' );
        $this->assertSame( 300, expOEUrlFetcher::timeout() );
    }

    public function testTheSizeLimitIsEnforcedWhileDownloading()
    {
        $this->ini( 'UploadFromUrlMaxSize', '1K' );
        try
        {
            // the image is a few hundred bytes: use a larger public file of this installation
            expOEUrlFetcher::fetch( 'https://alpha.se7enx.com/design/standard/images/exponential.png' );
            $this->markTestSkipped( 'the public file is not larger than 1K' );
        }
        catch ( expOEUrlException $e )
        {
            $this->assertStringContainsString( 'larger', $e->getMessage() );
        }
        $this->assertSame( array(), $this->leftovers() );
    }

    public function testExtensionAndContentMustAgree()
    {
        $dir = eZSys::varDirectory() . '/tmp';
        $gif = $dir . '/ezoe_url_test_' . getmypid() . '.gif';
        $this->temporary[] = $gif;
        file_put_contents( $gif, base64_decode( 'R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7' ) );
        $this->assertSame( 'ok.gif', expOEUrlFetcher::checkFile( $gif, 'ok.gif' ) );
        $this->assertSame( 'noext.gif', expOEUrlFetcher::checkFile( $gif, 'noext' ), 'the extension comes from the content when the name has none' );
        foreach ( array( 'fake.pdf', 'fake.png', 'fake.jpg', 'fake.zip', 'fake.mp3', 'fake.docx' ) as $name )
        {
            try
            {
                expOEUrlFetcher::checkFile( $gif, $name );
                $this->fail( $name . ' passed with GIF content' );
            }
            catch ( expOEUrlException $e )
            {
                $this->assertStringContainsString( 'not of the type', $e->getMessage(), $name );
            }
        }
        $this->ini( 'UploadExtensionCheck', 'disabled' );
        foreach ( array( 'shell.php', 'shell.php.gif', 'x.exe', 'x.html', 'x.js', 'tool.xyz' ) as $name )
        {
            try
            {
                expOEUrlFetcher::checkFile( $gif, $name );
                $this->fail( $name . ' passed' );
            }
            catch ( expOEUrlException $e )
            {
                $this->assertStringContainsString( 'not accepted', $e->getMessage(), $name );
            }
        }
    }

    public function testExecutableAndWebPageContentIsRefusedWhateverTheName()
    {
        $dir = eZSys::varDirectory() . '/tmp';
        $cases = array( 'php' => "<?php echo 1; ?>\n", 'html' => "<!DOCTYPE html><html><body><script>alert(1)</script></body></html>", 'elf' => "\x7fELF\x02\x01\x01\x00" . str_repeat( "\0", 64 ), 'sh' => "#!/bin/sh\necho hi\n" );
        foreach ( $cases as $key => $content )
        {
            foreach ( array( 'a.png', 'a.txt', 'a.jpg' ) as $name )
            {
                $file = $dir . '/ezoe_url_test_' . getmypid() . '_' . $key;
                $this->temporary[] = $file;
                file_put_contents( $file, $content );
                try
                {
                    expOEUrlFetcher::checkFile( $file, $name );
                    $this->fail( $key . ' content passed as ' . $name );
                }
                catch ( expOEUrlException $e )
                {
                    $this->assertStringContainsString( 'not of the type', $e->getMessage() );
                }
            }
        }
    }

    public function testFileNames()
    {
        $this->assertSame( 'photo.jpg', expOEUrlFetcher::fileNameFrom( 'https://x.example/a/b/photo.jpg?size=1', '' ) );
        $this->assertSame( 'report.pdf', expOEUrlFetcher::fileNameFrom( 'https://x.example/download.php?id=3', 'attachment; filename="report.pdf"' ) );
        $this->assertSame( 'Grüße.pdf', expOEUrlFetcher::fileNameFrom( 'https://x.example/d', "attachment; filename*=UTF-8''Gr%C3%BC%C3%9Fe.pdf" ) );
        $this->assertSame( 'passwd', expOEUrlFetcher::fileNameFrom( 'https://x.example/', 'attachment; filename="../../etc/passwd"' ) );
        $this->assertSame( 'evil.php.png', expOEUrlFetcher::sanitizeFileName( 'evil.php.png' ) );
        $this->assertSame( 'x_.png', expOEUrlFetcher::sanitizeFileName( "x\"<.png" ) );
        $this->assertLessThanOrEqual( 100, strlen( expOEUrlFetcher::sanitizeFileName( str_repeat( 'a', 300 ) . '.png' ) ) );
        $this->assertStringEndsWith( '.png', expOEUrlFetcher::sanitizeFileName( str_repeat( 'a', 300 ) . '.png' ) );
    }

    public function testTheFeatureCanBeSwitchedOff()
    {
        $this->assertTrue( expOEUrlFetcher::enabled() );
        $this->ini( 'UploadFromUrl', 'disabled' );
        $this->assertFalse( expOEUrlFetcher::enabled() );
        $this->expectException( expOEUrlException::class );
        expOEUrlFetcher::fetch( self::IMAGE_URL );
    }

    public function testARealFetchOfAPublicImageOfThisInstallation()
    {
        $fetched = expOEUrlFetcher::fetch( self::IMAGE_URL );
        try
        {
            $this->assertSame( '32x32.gif', $fetched['name'] );
            $this->assertSame( 'image/gif', $fetched['type'] );
            $this->assertFileExists( $fetched['path'] );
            $this->assertStringStartsWith( eZSys::varDirectory(), $fetched['path'] );
            $this->assertGreaterThan( 0, $fetched['size'] );
        }
        finally
        {
            expOEUrlFetcher::cleanup( $fetched );
        }
        $this->assertFileDoesNotExist( $fetched['path'] );
        $this->assertDirectoryDoesNotExist( $fetched['dir'] );
        $this->assertSame( array(), $this->leftovers() );
    }

    /**
     * A published folder under Media (43), removed in tearDown, and a draft of it made by the admin, as content/edit
     * makes one: an upload goes into the version being edited, which is a draft of the editor's own.
     *
     * @return array( eZContentObject, int the number of the draft )
     */
    protected function containerWithDraft( $name, $someoneElse = false )
    {
        $this->loginAdmin();
        $container = eZContentFunctions::createAndPublishObject( array(
            'parent_node_id' => 43, 'class_identifier' => 'folder', 'creator_id' => 14,
            'attributes' => array( 'name' => $name . ' ' . getmypid() ) ) );
        $this->assertNotFalse( $container, 'the test folder under Media (43) was not created' );
        $this->objectIDs[] = (int) $container->attribute( 'id' );
        if ( $someoneElse )
        {
            // a draft of someone else's (the anonymous user's); the admin edits nothing of it
            $this->loginAnonymous();
        }
        $draft = $container->createNewVersion();
        $creator = (int) eZUser::currentUserID();
        $this->loginAdmin();
        $this->assertInstanceOf( 'eZContentObjectVersion', $draft );
        $this->assertSame( $creator, (int) $draft->attribute( 'creator_id' ) );
        $this->assertNotSame( (int) $container->attribute( 'current_version' ), (int) $draft->attribute( 'version' ) );
        return array( $container, (int) $draft->attribute( 'version' ) );
    }

    /** @return int[] the version numbers of $fromID that relate to $toID, straight from the database */
    protected function relationVersions( $fromID, $toID )
    {
        $rows = eZDB::instance()->arrayQuery( 'SELECT from_contentobject_version AS v, ' . mt_rand() . ' AS nonce FROM ezcontentobject_link'
            . ' WHERE from_contentobject_id = ' . (int) $fromID . ' AND to_contentobject_id = ' . (int) $toID );
        return array_map( 'intval', array_column( $rows, 'v' ) );
    }

    public function testTheUploadViewCreatesAnImageObjectFromAUrl()
    {
        list( $container, $draft ) = $this->containerWithDraft( 'ezoe url upload test' );

        $out = $this->runView( 'upload', array( (string) $container->attribute( 'id' ), (string) $draft, 'objects', '0' ),
            array( 'uploadButton' => '1', 'uploadUrl' => self::IMAGE_URL, 'location' => '43', 'objectName' => 'ezoe url upload image ' . getmypid(), 'ContentObjectAttribute_image' => 'alt from url' ) );
        $this->assertSame( 1, preg_match( '/selectByEmbedId\(\s*(\d+)\s*,\s*(\d+)\s*,\s*("(?:[^"\\\\]|\\\\.)*")\s*\)/', $out, $m ), $out );
        $this->objectIDs[] = (int) $m[1];
        $created = eZContentObject::fetch( (int) $m[1] );
        $this->assertInstanceOf( 'eZContentObject', $created );
        $this->assertSame( 'image', $created->attribute( 'class_identifier' ) );
        $this->assertSame( 'ezoe url upload image ' . getmypid(), $created->attribute( 'name' ) );
        $this->assertSame( 43, (int) $created->attribute( 'main_node' )->attribute( 'parent_node_id' ) );
        $this->assertTrue( (bool) $created->attribute( 'can_read' ) );
        // the relation belongs to the draft being edited, not to version 1 (the version the new image has)
        $this->assertSame( array( $draft ), $this->relationVersions( $container->attribute( 'id' ), $m[1] ) );
        $this->assertSame( array(), $this->leftovers() );
    }

    public function testTheUploadViewDoesNotWriteIntoAPublishedVersion()
    {
        list( $container, $draft ) = $this->containerWithDraft( 'ezoe url upload published' );
        $before = (int) eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS c, ' . mt_rand() . ' AS nonce FROM ezcontentobject' )[0]['c'];
        $links = (int) eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS c, ' . mt_rand() . ' AS nonce FROM ezcontentobject_link WHERE from_contentobject_id = ' . (int) $container->attribute( 'id' ) )[0]['c'];

        foreach ( array( (int) $container->attribute( 'current_version' ), $draft + 50 ) as $version )
        {
            $out = $this->runView( 'upload', array( (string) $container->attribute( 'id' ), (string) $version, 'objects', '0' ),
                array( 'uploadButton' => '1', 'uploadUrl' => self::IMAGE_URL, 'location' => '43', 'objectName' => 'ezoe url upload refused ' . getmypid() ) );
            $this->assertStringNotContainsString( 'selectByEmbedId', $out, "version $version" );
            $this->assertStringContainsString( 'ObjectVersion', $out, "version $version" );
        }
        $this->assertSame( $before, (int) eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS c, ' . mt_rand() . ' AS nonce FROM ezcontentobject' )[0]['c'] );
        $this->assertSame( $links, (int) eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS c, ' . mt_rand() . ' AS nonce FROM ezcontentobject_link WHERE from_contentobject_id = ' . (int) $container->attribute( 'id' ) )[0]['c'] );
        $this->assertSame( array(), $this->leftovers() );
    }

    public function testTheUploadViewDoesNotWriteIntoSomeoneElsesDraft()
    {
        // the admin may read and edit the folder, but the draft is the anonymous user's
        list( $container, $draft ) = $this->containerWithDraft( 'ezoe url upload foreign draft', true );
        $before = (int) eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS c, ' . mt_rand() . ' AS nonce FROM ezcontentobject' )[0]['c'];
        $out = $this->runView( 'upload', array( (string) $container->attribute( 'id' ), (string) $draft, 'objects', '0' ),
            array( 'uploadButton' => '1', 'uploadUrl' => self::IMAGE_URL, 'location' => '43' ) );
        $this->assertStringNotContainsString( 'selectByEmbedId', $out );
        $this->assertStringContainsString( 'ObjectVersion', $out );
        $this->assertSame( $before, (int) eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS c, ' . mt_rand() . ' AS nonce FROM ezcontentobject' )[0]['c'] );
        $this->assertSame( array(), $this->leftovers() );
    }

    public static function viewRefusals()
    {
        return array( 'private address' => array( 'http://127.0.0.1/x.png' ), 'credentials' => array( 'http://u:p@alpha.se7enx.com/x.png' ),
                      'ftp' => array( 'ftp://alpha.se7enx.com/x.png' ), 'not a url' => array( 'hello' ),
                      'not an image' => array( 'https://alpha.se7enx.com/robots.txt.png' ), 'missing file' => array( 'https://alpha.se7enx.com/design/standard/images/no-such-file.gif' ) );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'viewRefusals' )]
    public function testTheUploadViewShowsTheServerErrorAndCreatesNothing( $url )
    {
        list( $container, $draft ) = $this->containerWithDraft( 'ezoe url upload refusal' );
        $before = (int) eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS c FROM ezcontentobject' )[0]['c'];
        $out = $this->runView( 'upload', array( (string) $container->attribute( 'id' ), (string) $draft, 'objects', '0' ),
            array( 'uploadButton' => '1', 'uploadUrl' => $url, 'location' => '43' ) );
        $this->assertStringNotContainsString( 'selectByEmbedId', $out );
        $this->assertStringContainsString( 'color: red', $out, $out );
        $this->assertSame( $before, (int) eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS c FROM ezcontentobject' )[0]['c'] );
        $this->assertSame( array(), $this->leftovers() );
    }

    /** @return array files left in the temporary directory of the fetcher */
    protected function leftovers()
    {
        $base = eZSys::varDirectory() . '/tmp/ezoe_url';
        return is_dir( $base ) ? array_values( array_diff( (array) scandir( $base ), array( '.', '..' ) ) ) : array();
    }
}
