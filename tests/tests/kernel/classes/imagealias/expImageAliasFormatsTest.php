<?php
/**
 * The format variants of image aliases (WebP, AVIF), the negotiation by the Accept header and the converter choice,
 * without a database:
 *
 *  IAF-01 - variant names: "<alias file>.<format>" next to the alias, split back; no variant of an alias in that
 *           format already, of a GIF or of an SVG
 *  IAF-02 - what the ImageMagick command can write, read from "-list format" (ImageMagick 6 and 7, read-only formats)
 *  IAF-03 - the converter order (PreferredConverter: auto = imagick, imagemagick, gd; a named one first) and the
 *           converters for a format, only those that can write it
 *  IAF-04 - the negotiation: only a type the Accept header names counts (image/* and *\/* do not), q=0 refuses it,
 *           the highest q wins, the order of Formats[] between equals
 *  IAF-05 - a signed URL of an alias with Formats[]=webp: the browser that accepts WebP is sent to the variant (made
 *           now), another one to the alias; both answers carry Vary: Accept; a streamed answer is private (a response
 *           cache keyed by URL alone must not keep it); with Negotiate disabled nothing varies
 *  IAF-06 - a variant older than its alias is made again; an unsigned or wrongly signed URL serves a current variant
 *           but never makes one
 *  IAF-07 - the storage path of a variant (index_image.php): made when the alias lists the format, else 404
 *  IAF-08 - no converter can write the format: the alias in its own format (still Vary: Accept), the variant's
 *           storage path 404, nothing written
 *  IAF-09 - an alias that exists, with formats to negotiate: its entry gets the signed content/image URL (the file
 *           stays in full_path); not for the original, the unsigned forms or an alias with nothing to negotiate
 *  IAF-10 - the variants go with their alias (eZImageAliasHandler::removeAliasVariants())
 *  IAF-11 - the quality of a variant: the alias block, then [MIMETypeSettings], then 80 / 60
 *  IAF-12 - a real conversion with every converter this machine has, each file with its format's signature
 *  IAF-13 - .htaccess_root and .htaccess_root_static give .webp and .avif their types
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/imagealias/expImageAliasFormatsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

/** The server with the delivery setting fixed */
class expImageAliasFormatsTestServer extends expImageAliasServer
{
    /** @var string|null */
    public $method = null;

    public function deliveryMethod( $path )
    {
        return $this->method !== null ? $this->method : parent::deliveryMethod( $path );
    }
}

class expImageAliasFormatsTest extends PHPUnit\Framework\TestCase
{
    const CHROME = 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8';
    const OLD_SAFARI = 'image/png,image/svg+xml,image/*;q=0.8,video/*;q=0.8,*/*;q=0.5';

    /** @var string */
    private $base;

    /** @var string */
    private $dir;

    /** @var string|false */
    private $cwd = false;

    public static function setUpBeforeClass(): void
    {
        eZExecution::registerShutdownHandler();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->cwd = getcwd();
        chdir( dirname( __DIR__, 5 ) );
        $this->base = 'var/tmp/storage/images/exp_iaf_' . getmypid() . '_' . mt_rand();
        $this->dir = $this->base . '/5-1-eng-GB';
        mkdir( $this->dir, 0700, true );
        copy( dirname( __DIR__, 4 ) . '/tests/lib/ezimage/data/andernach.jpg', $this->dir . '/andernach.jpg' );
        copy( dirname( __DIR__, 4 ) . '/tests/lib/ezimage/data/andernach.jpg', $this->dir . '/andernach_small.jpg' );
        expImageAliasUrl::setKeys( str_repeat( 's', 64 ) );
        expImageAliasLock::setDirectory( $this->base . '/locks' );
        expImageAliasLog::setDirectory( $this->base . '/log' );
        ezpINIHelper::setINISetting( 'image.ini', 'small', 'Formats', array( 'webp' ) );
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'Negotiate', 'enabled' );
    }

    protected function tearDown(): void
    {
        ezpINIHelper::restoreINISettings();
        expImageFormatConverter::setCapabilities( null );
        expImageAliasUrl::resetKeys();
        expImageAliasLock::setDirectory( null );
        expImageAliasLog::setDirectory( null );
        expImageAliasLock::setWaitLimit( null );
        foreach ( array( $this->dir, $this->base . '/locks', $this->base . '/log' ) as $dir )
        {
            foreach ( array_merge( (array)glob( $dir . '/*' ), (array)glob( $dir . '/.*.tmp' ) ) as $file )
                @unlink( $file );
            @rmdir( $dir );
        }
        @rmdir( $this->base );
        if ( $this->cwd !== false )
            chdir( $this->cwd );
        parent::tearDown();
    }

    private function payload( $file, array $extra = array() )
    {
        return $extra + array( 'a' => 5, 'v' => 1, 'l' => 'eng-GB', 'n' => 'small', 'o' => $this->dir . '/andernach.jpg', 'u' => $file,
                               'w' => 160, 'h' => 120, 'm' => 'image/jpeg' );
    }

    private function token()
    {
        return array( expImageAliasUrl::token( $this->payload( 'andernach_small.jpg' ) ), 'andernach_small.jpg' );
    }

    /** A converter that writes WebP here, or the test is skipped */
    private function needWebP()
    {
        $converters = expImageFormatConverter::convertersFor( 'webp', 'auto' );
        if ( !$converters )
            $this->markTestSkipped( 'No converter on this machine writes WebP' );
        expImageFormatConverter::setCapabilities( array( $converters[0] => array( 'webp' ) ) );
    }

    private function server( $method, $accept )
    {
        $server = new expImageAliasFormatsTestServer();
        $server->method = $method;
        $server->setAccept( $accept );
        return $server;
    }

    /** IAF-01 */
    public function testVariantNames()
    {
        $this->assertSame( 'var/site/storage/images/a/5-1-eng-GB/x_large.jpg.webp',
                           expImageFormatConverter::variantPath( 'var/site/storage/images/a/5-1-eng-GB/x_large.jpg', 'WEBP' ) );
        $this->assertSame( array( 'var/s/storage/images/a/1-2-eng-GB/x_large.png', 'avif' ),
                           expImageFormatConverter::splitVariant( 'var/s/storage/images/a/1-2-eng-GB/x_large.png.avif' ) );
        $this->assertNull( expImageFormatConverter::splitVariant( 'var/s/storage/images/a/1-2-eng-GB/x_large.webp' ), 'an alias, no variant' );
        $this->assertNull( expImageFormatConverter::splitVariant( 'var/s/storage/images/a/1-2-eng-GB/x_large.webp.webp' ) );
        $this->assertNull( expImageFormatConverter::splitVariant( 'var/s/storage/images/a/1-2-eng-GB/x_large.jpg.gif' ) );
        $this->assertSame( 'image/webp', expImageFormatConverter::mimeType( 'webp' ) );
        $this->assertSame( 'image/avif', expImageFormatConverter::mimeType( 'avif' ) );
        $this->assertNull( expImageFormatConverter::mimeType( 'png' ) );
        $this->assertTrue( expImageFormatConverter::variantApplies( 'image/jpeg', 'webp' ) );
        $this->assertTrue( expImageFormatConverter::variantApplies( 'image/png', 'avif' ) );
        $this->assertTrue( expImageFormatConverter::variantApplies( 'image/webp', 'avif' ) );
        $this->assertFalse( expImageFormatConverter::variantApplies( 'image/webp', 'webp' ) );
        $this->assertFalse( expImageFormatConverter::variantApplies( 'image/gif', 'webp' ), 'animated GIFs keep their frames' );
        $this->assertFalse( expImageFormatConverter::variantApplies( 'image/svg+xml', 'webp' ) );
        $this->assertFalse( expImageFormatConverter::variantApplies( 'image/jpeg', 'jxl' ) );
    }

    /** IAF-02 */
    public function testImageMagickFormatList()
    {
        $im6 = array( '   Format  Mode  Description', '-------------------------------------------------------------------------------',
                      '     JPEG* rw-   Joint Photographic Experts Group JFIF format (90)',
                      '     WEBP* rw+   WebP Image Format (libwebp 1.2.0 [020F])' );
        $this->assertSame( array( 'webp' ), expImageFormatConverter::parseFormatList( $im6 ) );
        // ImageMagick 7 has a module column; AVIF through HEIC; a read-only format is no format to write
        $im7 = array( '     AVIF  HEIC      rw+   AV1 Image File Format (1.17.6)', '     WEBP* WEBP      rw+   WebP Image Format (libwebp 1.3.2 [020F])' );
        $this->assertSame( array( 'avif', 'webp' ), expImageFormatConverter::parseFormatList( $im7 ) );
        $this->assertSame( array(), expImageFormatConverter::parseFormatList( array( '     AVIF  HEIC      r--   AV1 Image File Format', 'WEBP is not here' ) ) );
    }

    /** IAF-03 */
    public function testConverterOrder()
    {
        $this->assertSame( array( 'imagick', 'imagemagick', 'gd' ), expImageFormatConverter::order( 'auto' ) );
        $this->assertSame( array( 'gd', 'imagick', 'imagemagick' ), expImageFormatConverter::order( 'gd' ) );
        $this->assertSame( array( 'imagemagick', 'imagick', 'gd' ), expImageFormatConverter::order( 'imagemagick' ) );
        $this->assertSame( array( 'imagick', 'imagemagick', 'gd' ), expImageFormatConverter::order( 'vips' ) );
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'PreferredConverter', 'gd' );
        $this->assertSame( 'gd', expImageAliasSettings::preferredConverter() );
        $this->assertSame( array( 'gd', 'imagick', 'imagemagick' ), expImageFormatConverter::order() );

        expImageFormatConverter::setCapabilities( array( 'imagick' => array( 'webp' ), 'imagemagick' => array( 'webp' ), 'gd' => array( 'webp', 'avif' ) ) );
        $this->assertSame( array( 'imagick', 'imagemagick', 'gd' ), expImageFormatConverter::convertersFor( 'webp', 'auto' ) );
        $this->assertSame( array( 'gd' ), expImageFormatConverter::convertersFor( 'avif', 'auto' ), 'only the converter that writes it' );
        $this->assertSame( array( 'gd', 'imagick', 'imagemagick' ), expImageFormatConverter::convertersFor( 'webp' ) );
        $this->assertTrue( expImageFormatConverter::canWrite( 'avif' ) );
        expImageFormatConverter::setCapabilities( array( 'imagick' => array(), 'imagemagick' => array( 'webp' ), 'gd' => array() ) );
        $this->assertFalse( expImageFormatConverter::canWrite( 'avif' ) );
        $this->assertFalse( expImageFormatConverter::canWrite( 'png' ) );
    }

    /** IAF-04 */
    public function testNegotiation()
    {
        $this->assertSame( 'webp', expImageAliasServer::negotiate( array( 'webp', 'avif' ), self::CHROME ), 'equal: the order of Formats[]' );
        $this->assertSame( 'avif', expImageAliasServer::negotiate( array( 'avif', 'webp' ), self::CHROME ) );
        $this->assertSame( 'webp', expImageAliasServer::negotiate( array( 'webp' ), 'image/webp,*/*' ) );
        $this->assertNull( expImageAliasServer::negotiate( array( 'webp', 'avif' ), self::OLD_SAFARI ), 'image/* and */* do not count' );
        $this->assertNull( expImageAliasServer::negotiate( array( 'webp' ), '*/*' ) );
        $this->assertNull( expImageAliasServer::negotiate( array( 'webp' ), '' ) );
        $this->assertNull( expImageAliasServer::negotiate( array( 'webp' ), 'image/webp;q=0, image/*' ), 'q=0 refuses' );
        $this->assertSame( 'avif', expImageAliasServer::negotiate( array( 'webp', 'avif' ), 'image/webp;q=0.5, image/avif' ), 'the higher q' );
        $this->assertSame( 'webp', expImageAliasServer::negotiate( array( 'webp' ), 'IMAGE/WEBP ; q=0.9' ) );
        $this->assertNull( expImageAliasServer::negotiate( array(), self::CHROME ) );
    }

    /** IAF-05 */
    public function testASignedURLIsAnsweredInTheAcceptedFormat()
    {
        $this->needWebP();
        $variant = $this->dir . '/andernach_small.jpg.webp';

        $response = $this->server( 'redirect', self::CHROME )->handleParams( $this->token() );
        $this->assertSame( 302, $response['status'] );
        $this->assertStringEndsWith( '/' . $variant, $response['headers']['Location'] );
        $this->assertSame( 'Accept', $response['headers']['Vary'] );
        $this->assertStringStartsWith( 'public, max-age=', $response['headers']['Cache-Control'] );
        $this->assertTrue( expImageFormatConverter::hasSignature( $variant, 'webp' ), 'the variant was made' );
        $this->assertStringContainsString( 'event=generate result=ok', (string)file_get_contents( $this->base . '/log/imagealias.log' ) );

        $response = $this->server( 'redirect', self::OLD_SAFARI )->handleParams( $this->token() );
        $this->assertSame( 302, $response['status'] );
        $this->assertStringEndsWith( '/' . $this->dir . '/andernach_small.jpg', $response['headers']['Location'] );
        $this->assertSame( 'Accept', $response['headers']['Vary'], 'the browser without WebP gets the alias, also with Vary' );

        $response = $this->server( 'stream', self::CHROME )->handleParams( $this->token() );
        $this->assertSame( 200, $response['status'] );
        $this->assertSame( $variant, $response['file'] );
        $this->assertSame( 'image/webp', $response['headers']['Content-Type'] );
        $this->assertSame( 'Accept', $response['headers']['Vary'] );
        $this->assertStringStartsWith( 'private, max-age=', $response['headers']['Cache-Control'], 'no response cache keyed by URL alone keeps it' );
        $response = $this->server( 'stream', '*/*' )->handleParams( $this->token() );
        $this->assertSame( 'image/jpeg', $response['headers']['Content-Type'] );
        $this->assertStringStartsWith( 'private, max-age=', $response['headers']['Cache-Control'] );

        // Negotiate disabled, or an alias without Formats[]: one answer for everybody, cached as before
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'Negotiate', 'disabled' );
        $response = $this->server( 'stream', self::CHROME )->handleParams( $this->token() );
        $this->assertSame( 'image/jpeg', $response['headers']['Content-Type'] );
        $this->assertArrayNotHasKey( 'Vary', $response['headers'] );
        $this->assertStringStartsWith( 'public, max-age=', $response['headers']['Cache-Control'] );

        // A placeholder of such an alias varies too (another browser may get a file)
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'Negotiate', 'enabled' );
        expImageAliasUrl::setKeys( str_repeat( 'x', 64 ) );
        $missing = expImageAliasUrl::token( $this->payload( 'andernach_small2.jpg' ) );
        $response = $this->server( 'redirect', self::CHROME )->handleParams( array( $missing, 'andernach_small2.jpg' ) );
        $this->assertSame( 404, $response['status'] );
        $this->assertSame( 'Accept', $response['headers']['Vary'] );
        $this->assertStringContainsString( 'no-store', $response['headers']['Cache-Control'] );
    }

    /** IAF-06 */
    public function testStaleVariantsAndUnsignedURLs()
    {
        $this->needWebP();
        $variant = $this->dir . '/andernach_small.jpg.webp';
        // Signed with the key "s"; checked against "x" it is a wrong signature
        $url = $this->token();

        // A wrong signature: the alias, nothing made
        expImageAliasUrl::setKeys( str_repeat( 'x', 64 ) );
        $response = $this->server( 'redirect', self::CHROME )->handleParams( $url );
        $this->assertStringEndsWith( '/andernach_small.jpg', $response['headers']['Location'] );
        $this->assertFileDoesNotExist( $variant );

        // Signed: made; the wrong signature now gets it, since it is there and current
        expImageAliasUrl::setKeys( str_repeat( 's', 64 ) );
        $this->server( 'redirect', self::CHROME )->handleParams( $url );
        $this->assertFileExists( $variant );
        expImageAliasUrl::setKeys( str_repeat( 'x', 64 ) );
        $response = $this->server( 'redirect', self::CHROME )->handleParams( $url );
        $this->assertStringEndsWith( '/andernach_small.jpg.webp', $response['headers']['Location'] );

        // The alias was made again after the variant: the old variant is no answer, unsigned; signed it is made again
        file_put_contents( $variant, 'RIFF0000WEBPold' );
        touch( $variant, time() - 100 );
        clearstatcache();
        $response = $this->server( 'redirect', self::CHROME )->handleParams( $url );
        $this->assertStringEndsWith( '/andernach_small.jpg', $response['headers']['Location'] );
        expImageAliasUrl::setKeys( str_repeat( 's', 64 ) );
        $response = $this->server( 'redirect', self::CHROME )->handleParams( $url );
        $this->assertStringEndsWith( '/andernach_small.jpg.webp', $response['headers']['Location'] );
        clearstatcache();
        $this->assertGreaterThan( 100, filesize( $variant ), 'made again' );
        $this->assertGreaterThanOrEqual( filemtime( $this->dir . '/andernach_small.jpg' ), filemtime( $variant ) );
    }

    /** IAF-07 */
    public function testTheStoragePathOfAVariant()
    {
        $this->needWebP();
        $response = ( new expImageAliasFormatsTestServer() )->handleStoragePath( '/' . $this->dir . '/andernach_small.jpg.webp' );
        $this->assertSame( 200, $response['status'] );
        $this->assertSame( 'image/webp', $response['headers']['Content-Type'] );
        $this->assertSame( $this->dir . '/andernach_small.jpg.webp', $response['file'] );
        $this->assertArrayNotHasKey( 'Vary', $response['headers'], 'the path names the format' );
        $this->assertTrue( expImageFormatConverter::hasSignature( $this->dir . '/andernach_small.jpg.webp', 'webp' ) );

        // A format the alias does not list, and a file that is no alias
        foreach ( array( 'andernach_small.jpg.avif', 'andernach.jpg.webp', 'andernach_large.jpg.webp' ) as $file )
        {
            $response = ( new expImageAliasFormatsTestServer() )->handleStoragePath( $this->dir . '/' . $file );
            $this->assertSame( 404, $response['status'], $file );
            $this->assertFileDoesNotExist( $this->dir . '/' . $file );
        }
    }

    /** IAF-08 */
    public function testNoConverterForTheFormat()
    {
        expImageFormatConverter::setCapabilities( array( 'imagick' => array(), 'imagemagick' => array(), 'gd' => array() ) );
        $response = $this->server( 'redirect', self::CHROME )->handleParams( $this->token() );
        $this->assertSame( 302, $response['status'] );
        $this->assertStringEndsWith( '/andernach_small.jpg', $response['headers']['Location'] );
        $this->assertSame( 'Accept', $response['headers']['Vary'] );
        $response = ( new expImageAliasFormatsTestServer() )->handleStoragePath( $this->dir . '/andernach_small.jpg.webp' );
        $this->assertSame( 404, $response['status'] );
        $this->assertFileDoesNotExist( $this->dir . '/andernach_small.jpg.webp' );
        $this->assertFalse( expImageFormatConverter::convert( $this->dir . '/andernach_small.jpg', $this->dir . '/x.webp', 'webp', 80 ) );
        $this->assertSame( array(), (array)glob( $this->dir . '/.*.tmp' ), 'no temporary file left' );
    }

    /** IAF-09 */
    public function testTheEntryOfAnAliasThatExists()
    {
        $original = array( 'url' => $this->dir . '/andernach.jpg', 'name' => 'original' );
        $entry = array( 'name' => 'small', 'url' => $this->dir . '/andernach_small.jpg', 'is_valid' => true, 'width' => 160, 'height' => 120,
                        'mime_type' => 'image/jpeg', 'full_path' => $this->dir . '/andernach_small.jpg' );
        $attribute = array( 'id' => 5, 'version' => 1, 'language_code' => 'eng-GB' );
        $negotiated = expImageAliasUrl::negotiatedEntry( $entry, $original, $attribute, array( 'form' => 'token', 'prefix' => '' ) );
        $this->assertStringStartsWith( 'content/image/', $negotiated['url'] );
        $this->assertStringEndsWith( '/andernach_small.jpg', $negotiated['url'] );
        $this->assertSame( $this->dir . '/andernach_small.jpg', $negotiated['full_path'] );
        $this->assertSame( 160, $negotiated['width'] );
        $parts = explode( '/', substr( $negotiated['url'], strlen( 'content/image/' ) ) );
        $check = expImageAliasUrl::verify( $parts[0] );
        $this->assertTrue( $check['valid'] );
        $this->assertSame( 'small', $check['payload']['n'] );
        $this->assertSame( 'andernach_small.jpg', $check['payload']['u'] );

        $this->assertStringContainsString( 'content/image/5/1/eng-GB/small/',
                                           expImageAliasUrl::negotiatedEntry( $entry, $original, $attribute, array( 'form' => 'ids-token', 'prefix' => '' ) )['url'] );
        foreach ( array( 'ids', 'node', 'storage' ) as $form )
            $this->assertNull( expImageAliasUrl::negotiatedEntry( $entry, $original, $attribute, array( 'form' => $form ) ), $form );
        $this->assertNull( expImageAliasUrl::negotiatedEntry( array( 'name' => 'original' ) + $entry, $original, $attribute, array( 'form' => 'token' ) ) );
        $this->assertNull( expImageAliasUrl::negotiatedEntry( array( 'url' => 'var/other/storage/images/x/a_small.jpg' ) + $entry, $original, $attribute,
                                                              array( 'form' => 'token' ) ), 'not next to the original' );

        // Only aliases with formats to negotiate
        $this->assertSame( array( 'webp' ), expImageAliasSettings::negotiatedFormats( 'small' ) );
        $this->assertSame( array(), expImageAliasSettings::negotiatedFormats( 'original' ) );
        $this->assertSame( array(), expImageAliasSettings::negotiatedFormats( 'large' ) );
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'Negotiate', 'disabled' );
        $this->assertSame( array(), expImageAliasSettings::negotiatedFormats( 'small' ) );
        ezpINIHelper::setINISetting( 'image.ini', 'small', 'Formats', array( 'AVIF', 'webp', 'jxl', 'avif' ) );
        $this->assertSame( array( 'avif', 'webp' ), expImageAliasSettings::aliasFormats( 'small' ) );
    }

    /** IAF-10 */
    public function testVariantsGoWithTheirAlias()
    {
        foreach ( array( 'webp', 'avif' ) as $format )
            file_put_contents( $this->dir . '/andernach_small.jpg.' . $format, 'x' );
        eZImageAliasHandler::removeAliasVariants( $this->dir . '/andernach_small.jpg' );
        $this->assertFileDoesNotExist( $this->dir . '/andernach_small.jpg.webp' );
        $this->assertFileDoesNotExist( $this->dir . '/andernach_small.jpg.avif' );
        $this->assertFileExists( $this->dir . '/andernach_small.jpg', 'the alias itself is left to its caller' );
        // Never outside a storage directory
        $outside = 'var/tmp/exp_iaf_' . getmypid() . '.jpg';
        file_put_contents( $outside . '.webp', 'x' );
        eZImageAliasHandler::removeAliasVariants( $outside );
        $this->assertFileExists( $outside . '.webp' );
        unlink( $outside . '.webp' );
    }

    /** IAF-11 */
    public function testTheQualityOfAVariant()
    {
        $this->assertSame( 80, expImageAliasSettings::formatQuality( 'small', 'webp' ) );
        $this->assertSame( 60, expImageAliasSettings::formatQuality( 'small', 'avif' ) );
        ezpINIHelper::setINISetting( 'image.ini', 'MIMETypeSettings', 'Quality', array( 'image/jpeg;75', 'image/webp;70' ) );
        $this->assertSame( 70, expImageAliasSettings::formatQuality( 'small', 'webp' ) );
        ezpINIHelper::setINISetting( 'image.ini', 'small', 'Quality', array( 'image/webp;90', 'image/avif;500' ) );
        $this->assertSame( 90, expImageAliasSettings::formatQuality( 'small', 'webp' ) );
        $this->assertSame( 100, expImageAliasSettings::formatQuality( 'small', 'avif' ), 'limited to 100' );
    }

    /** IAF-12 */
    public function testRealConversions()
    {
        $capabilities = expImageFormatConverter::capabilities( true );
        $made = 0;
        foreach ( $capabilities as $converter => $formats )
        {
            foreach ( $formats as $format )
            {
                expImageFormatConverter::setCapabilities( array( $converter => array( $format ) ) );
                $destination = $this->dir . "/andernach_small.jpg.$converter.$format";
                $this->assertTrue( expImageFormatConverter::convert( $this->dir . '/andernach_small.jpg', $destination, $format, 80 ), "$converter $format" );
                $this->assertSame( $converter, expImageFormatConverter::lastConverter() );
                $this->assertTrue( expImageFormatConverter::hasSignature( $destination, $format ), "$converter $format" );
                $size = getimagesize( $destination );
                if ( $size )
                    $this->assertSame( array( 640, 480 ), array( $size[0], $size[1] ), "$converter $format keeps the size" );
                ++$made;
            }
        }
        expImageFormatConverter::setCapabilities( null );
        if ( !$made )
            $this->markTestSkipped( 'No converter on this machine writes WebP or AVIF' );
        $this->assertFalse( expImageFormatConverter::hasSignature( $this->dir . '/andernach_small.jpg', 'webp' ) );
    }

    /** IAF-13 */
    public function testTheShippedApacheRulesNameTheVariantTypes()
    {
        foreach ( array( '.htaccess_root', '.htaccess_root_static' ) as $file )
        {
            $text = (string)file_get_contents( $file );
            // Apache names foo_i160.jpg.avif after its inner .jpg when it does not know .avif
            $this->assertMatchesRegularExpression( '/^\s*AddType image\/avif \.avif\s*$/m', $text, $file );
            $this->assertMatchesRegularExpression( '/^\s*AddType image\/webp \.webp\s*$/m', $text, $file );
        }
    }
}
