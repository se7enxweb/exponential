<?php
/**
 * The checks of the image editor's save view, without a database (expImageEditorStore).
 *  IE-01 - the stored name keeps the image's name and takes the extension of the new type
 *  IE-02 - no file, a failed or too large upload, a file that is not an HTTP upload: refused, with the status
 *  IE-03 - an SVG stays an SVG and a raster image stays a raster image
 *  IE-04 - only the types of OutputMIMETypes[] are stored; a raster image of another type is refused
 *  IE-05 - a hostile SVG passes the check (it is stored as the sanitizer's output); one with nothing safe in it does not
 *  IE-06 - the class attribute's maximum file size applies
 *  IE-07 - only the user's own draft, and only with edit access in the attribute's language
 *  IE-08 - an attribute without an image takes a new drawing: an SVG, stored as drawing.svg; a raster image is refused
 *  IE-09 - a raster image is stored as GD decoded and wrote it anew (nothing appended survives), as a copy with a random
 *          name in the cache directory that discard() removes
 *  IE-10 - expimageeditor.ini MaxPixels and MaxFileSize; ReEncodeRaster=disabled stores the upload itself
 *  IE-11 - the sanitizer the store goes through leaves no script, event attribute, foreignObject or external reference
 *  IE-12 - the form token must be the session's; stale copies are removed, fresh ones and foreign files are not
 * The store itself (initializeFromFile() and the ezimagefile rows) needs the database; it is checked in the browser on a
 * running installation (README.md, "Checks").
 *
 * Run: php vendor/bin/phpunit --bootstrap tests/bootstrap.php extension/expimageeditor/tests/phpunit/unit/
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expimageeditor
 */

require_once dirname( __DIR__, 3 ) . '/classes/expimageeditorexception.php';
require_once dirname( __DIR__, 3 ) . '/classes/expimageeditorstore.php';

class expImageEditorStoreTest extends PHPUnit\Framework\TestCase
{
    const HOSTILE_SVG = '<?xml version="1.0"?><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 20" onload="alert(1)"><script>alert(2)</script><rect width="40" height="20" fill="#08f"/></svg>';

    /** @var string */
    private $dir;

    /** @var array|null OutputMIMETypes[] before the test; false: not set */
    private $savedTypes = false;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = sys_get_temp_dir() . '/expimageeditor-test-' . bin2hex( random_bytes( 4 ) );
        mkdir( $this->dir );
        $ini = eZINI::instance( 'expimageeditor.ini' );
        $this->savedTypes = $ini->hasVariable( 'EditorSettings', 'OutputMIMETypes' ) ? $ini->variable( 'EditorSettings', 'OutputMIMETypes' ) : false;
        $ini->setVariable( 'EditorSettings', 'OutputMIMETypes', array( 'image/jpeg', 'image/png', 'image/webp', 'image/svg+xml' ) );
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance( 'expimageeditor.ini' );
        if ( $this->savedTypes === false )
            $ini->removeSetting( 'EditorSettings', 'OutputMIMETypes' );
        else
            $ini->setVariable( 'EditorSettings', 'OutputMIMETypes', $this->savedTypes );
        foreach ( (array)glob( $this->dir . '/*' ) as $file )
            unlink( $file );
        rmdir( $this->dir );
        parent::tearDown();
    }

    /** A PNG (or GIF) of the given size, as a file */
    private function raster( $name, $type = 'png', $width = 4, $height = 3 )
    {
        $image = imagecreatetruecolor( $width, $height );
        $path = $this->dir . '/' . $name;
        $type === 'gif' ? imagegif( $image, $path ) : imagepng( $image, $path );
        return $path;
    }

    private function file( $name, $content )
    {
        $path = $this->dir . '/' . $name;
        file_put_contents( $path, $content );
        return $path;
    }

    private static function upload( $path, $error = UPLOAD_ERR_OK )
    {
        return array( 'name' => 'blob', 'tmp_name' => $path, 'error' => $error, 'size' => is_file( $path ) ? filesize( $path ) : 0 );
    }

    /**
     * An image attribute whose stored image has this type and name, in a class attribute with this maximum size (MB)
     */
    private function attribute( $mime, $name, $maxMegabytes = 0, $valid = true )
    {
        $handler = $this->createStub( eZImageAliasHandler::class );
        $handler->method( 'attribute' )->willReturnMap( array(
            array( 'original', $valid ? array( 'is_valid' => true, 'mime_type' => $mime, 'original_filename' => $name ) : array( 'is_valid' => false ) ),
        ) );
        $classAttribute = $this->createStub( eZContentClassAttribute::class );
        $classAttribute->method( 'attribute' )->willReturnMap( array( array( 'data_int1', $maxMegabytes ) ) );
        $attribute = $this->createStub( eZContentObjectAttribute::class );
        $attribute->method( 'attribute' )->willReturnMap( array(
            array( 'content', $handler ),
            array( 'contentclass_attribute', $classAttribute ),
        ) );
        return $attribute;
    }

    private function assertRefused( $status, callable $call )
    {
        try
        {
            $call();
        }
        catch ( expImageEditorException $e )
        {
            $this->assertSame( $status, $e->status(), $e->getMessage() );
            return $e;
        }
        $this->fail( "Expected a refusal with status $status" );
    }

    /** IE-01 */
    public function testTargetNameKeepsTheNameAndTakesTheNewExtension()
    {
        $this->assertSame( 'beach.png', expImageEditorStore::targetName( 'beach.jpg', 'image/png' ) );
        $this->assertSame( 'beach.photo.webp', expImageEditorStore::targetName( 'beach.photo.JPEG', 'image/webp' ) );
        $this->assertSame( 'logo.svg', expImageEditorStore::targetName( 'logo.svgz', 'image/svg+xml' ) );
        $this->assertSame( 'image.jpg', expImageEditorStore::targetName( '', 'image/jpeg' ) );
        $this->assertSame( 'passwd.png', expImageEditorStore::targetName( '../../etc/passwd', 'image/png' ) );
        $this->assertSame( 'ab.png', expImageEditorStore::targetName( "a\0<b>", 'image/png' ) );
    }

    /** IE-02 */
    public function testBrokenUploadsAreRefused()
    {
        $attribute = $this->attribute( 'image/png', 'a.png' );
        $png = $this->raster( 'a.png' );
        $this->assertRefused( 400, function () use ( $attribute ) { expImageEditorStore::checkUpload( null, $attribute, false ); } );
        $this->assertRefused( 400, function () use ( $attribute ) { expImageEditorStore::checkUpload( array( 'tmp_name' => array( 'x' ), 'error' => 0, 'size' => 1 ), $attribute, false ); } );
        $this->assertRefused( 413, function () use ( $attribute, $png ) { expImageEditorStore::checkUpload( self::upload( $png, UPLOAD_ERR_INI_SIZE ), $attribute, false ); } );
        $this->assertRefused( 400, function () use ( $attribute, $png ) { expImageEditorStore::checkUpload( self::upload( $png, UPLOAD_ERR_PARTIAL ), $attribute, false ); } );
        $this->assertRefused( 400, function () use ( $attribute ) { expImageEditorStore::checkUpload( self::upload( '/nonexistent/x.png' ), $attribute, false ); } );
        // in the view: a file on the disk that was not uploaded with this request is not taken
        $this->assertRefused( 400, function () use ( $attribute, $png ) { expImageEditorStore::checkUpload( self::upload( $png ), $attribute ); } );
    }

    /** IE-03 */
    public function testTheKindOfImageStaysTheSame()
    {
        $svg = $this->file( 'a.svg', self::HOSTILE_SVG );
        $png = $this->raster( 'a.png' );
        $this->assertRefused( 415, function () use ( $svg ) { expImageEditorStore::checkUpload( self::upload( $svg ), $this->attribute( 'image/jpeg', 'a.jpg' ), false ); } );
        $this->assertRefused( 415, function () use ( $png ) { expImageEditorStore::checkUpload( self::upload( $png ), $this->attribute( 'image/svg+xml', 'a.svg' ), false ); } );

        $checked = expImageEditorStore::checkUpload( self::upload( $png ), $this->attribute( 'image/jpeg', 'Strand.jpg' ), false );
        expImageEditorStore::discard( $checked );
        $this->assertSame( array( 'name' => 'Strand.png', 'mime' => 'image/png', 'temporary' => true ),
                           array_intersect_key( $checked, array_flip( array( 'name', 'mime', 'temporary' ) ) ) );
    }

    /** IE-09 */
    public function testARasterImageIsStoredAsGDWroteItAnew()
    {
        // a PNG with data appended after its end (the kernel's upload check already refuses PHP or HTML there)
        $png = $this->raster( 'p.png', 'png', 6, 5 );
        file_put_contents( $png, 'TRAILER-PAYLOAD-0123456789', FILE_APPEND );
        $checked = expImageEditorStore::checkUpload( self::upload( $png ), $this->attribute( 'image/png', 'p.png' ), false );
        try
        {
            $this->assertTrue( $checked['temporary'] );
            $this->assertNotSame( $png, $checked['path'] );
            $this->assertStringStartsWith( expImageEditorStore::temporaryDirectory() . '/', $checked['path'] );
            $this->assertMatchesRegularExpression( '#/[0-9a-f]{32}\.png$#', $checked['path'], 'a random name, no part of the upload' );
            $stored = file_get_contents( $checked['path'] );
            $this->assertStringNotContainsString( 'TRAILER-PAYLOAD', $stored );
            $this->assertStringEndsWith( "IEND\xae\x42\x60\x82", $stored, 'nothing after the end of the image' );
            $this->assertSame( array( 6, 5, 'image/png' ), array_slice( array_values( array_intersect_key( getimagesize( $checked['path'] ), array_flip( array( 0, 1, 'mime' ) ) ) ), 0, 3 ) );
        }
        finally
        {
            expImageEditorStore::discard( $checked );
        }
        clearstatcache();
        $this->assertFileDoesNotExist( $checked['path'], 'discard() removes the copy' );

        // discard() removes only a copy it made: never the upload, never a file elsewhere
        expImageEditorStore::discard( array( 'path' => $png, 'temporary' => true ) );
        $this->assertFileExists( $png );
    }

    /** IE-10 */
    public function testTheEditorsOwnLimits()
    {
        $ini = eZINI::instance( 'expimageeditor.ini' );
        $png = $this->raster( 'big.png', 'png', 40, 30 );
        try
        {
            $ini->setVariable( 'EditorSettings', 'MaxPixels', '1000' );
            $this->assertRefused( 413, function () use ( $png ) { expImageEditorStore::checkUpload( self::upload( $png ), $this->attribute( 'image/png', 'a.png' ), false ); } );

            $ini->setVariable( 'EditorSettings', 'MaxPixels', '0' );
            $ini->setVariable( 'EditorSettings', 'MaxFileSize', '0.00001' );
            $this->assertRefused( 413, function () use ( $png ) { expImageEditorStore::checkUpload( self::upload( $png ), $this->attribute( 'image/png', 'a.png' ), false ); } );

            // without re-encoding the upload itself is stored, after the same checks
            $ini->setVariable( 'EditorSettings', 'MaxFileSize', '0' );
            $ini->setVariable( 'EditorSettings', 'ReEncodeRaster', 'disabled' );
            $checked = expImageEditorStore::checkUpload( self::upload( $png ), $this->attribute( 'image/png', 'a.png' ), false );
            $this->assertSame( array( $png, false ), array( $checked['path'], $checked['temporary'] ) );
        }
        finally
        {
            $ini->removeSetting( 'EditorSettings', 'MaxPixels' );
            $ini->removeSetting( 'EditorSettings', 'MaxFileSize' );
            $ini->removeSetting( 'EditorSettings', 'ReEncodeRaster' );
        }
        $this->assertSame( array( 25000000, 20 * 1024 * 1024, true ), array( expImageEditorStore::maxPixels(), expImageEditorStore::editorMaxBytes(), expImageEditorStore::reencodeEnabled() ),
                           'the defaults without the settings' );
    }

    /** IE-11 */
    public function testTheSanitizerLeavesNothingThatRuns()
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 40 20" onload="alert(1)">'
             . '<script>alert(2)</script><foreignObject width="10" height="10"><iframe xmlns="http://www.w3.org/1999/xhtml" src="https://example.invalid/"/></foreignObject>'
             . '<image href="https://example.invalid/track.png" width="5" height="5"/><use xlink:href="https://example.invalid/x.svg#a"/>'
             . '<a href="javascript:alert(3)"><rect width="40" height="20" fill="#08f" onclick="alert(4)" onmouseover="alert(5)"/></a>'
             . '<set attributeName="href" to="javascript:alert(6)"/></svg>';
        $clean = expSvgSanitizer::sanitize( $svg );
        $this->assertIsString( $clean );
        $this->assertStringContainsString( '<rect', $clean, 'the drawing stays' );
        foreach ( array( '<script', 'onload', 'onclick', 'onmouseover', 'foreignObject', 'iframe', 'example.invalid', 'javascript:', '<set' ) as $needle )
            $this->assertStringNotContainsStringIgnoringCase( $needle, $clean, $needle );
    }

    /** IE-12 */
    public function testTheFormTokenAndTheTemporaryFiles()
    {
        require_once dirname( __DIR__, 3 ) . '/classes/runnable/views/expimageeditor/save.php';
        $this->assertFalse( \Exponential\View\Extension\Expimageeditor\Expimageeditor\Save::tokenMatches( '' ) );
        $this->assertFalse( \Exponential\View\Extension\Expimageeditor\Expimageeditor\Save::tokenMatches( null ) );
        if ( class_exists( 'ezxFormToken' ) && in_array( 'ezformtoken', eZExtension::activeExtensions(), true ) && ezxFormToken::isEnabled() )
        {
            $this->assertTrue( \Exponential\View\Extension\Expimageeditor\Expimageeditor\Save::tokenMatches( ezxFormToken::getToken() ) );
            $this->assertFalse( \Exponential\View\Extension\Expimageeditor\Expimageeditor\Save::tokenMatches( 'x' . ezxFormToken::getToken() ) );
        }

        $dir = $this->dir;
        $old = $dir . '/' . str_repeat( 'a', 32 ) . '.png';
        $fresh = $dir . '/' . str_repeat( 'b', 32 ) . '.png';
        $other = $dir . '/keep-me.png';
        foreach ( array( $old, $fresh, $other ) as $file )
            file_put_contents( $file, 'x' );
        touch( $old, time() - 7200 );
        touch( $other, time() - 7200 );
        expImageEditorStore::removeStaleTemporaryFiles( $dir );
        clearstatcache();
        $this->assertFileDoesNotExist( $old, 'a copy an interrupted request left' );
        $this->assertFileExists( $fresh, 'a copy of a request that may still run' );
        $this->assertFileExists( $other, 'a file the editor did not name' );
    }

    /** IE-04 */
    public function testOnlyTheOutputTypesAreStored()
    {
        $gif = $this->raster( 'a.gif', 'gif' );
        $e = $this->assertRefused( 415, function () use ( $gif ) { expImageEditorStore::checkUpload( self::upload( $gif ), $this->attribute( 'image/gif', 'a.gif' ), false ); } );
        $this->assertSame( 'The editor may not store images of this type.', $e->getMessage() );

        eZINI::instance( 'expimageeditor.ini' )->setVariable( 'EditorSettings', 'OutputMIMETypes', array( 'image/jpeg' ) );
        $png = $this->raster( 'a.png' );
        $this->assertRefused( 415, function () use ( $png ) { expImageEditorStore::checkUpload( self::upload( $png ), $this->attribute( 'image/png', 'a.png' ), false ); } );

        // a text file is no image whatever it is called
        $text = $this->file( 'a.png', '<html><script>alert(1)</script></html>' );
        $this->assertRefused( 415, function () use ( $text ) { expImageEditorStore::checkUpload( self::upload( $text ), $this->attribute( 'image/png', 'a.png' ), false ); } );
    }

    /** IE-05 */
    public function testSvgIsCheckedBySanitizer()
    {
        $svg = $this->file( 'a.svg', self::HOSTILE_SVG );
        $checked = expImageEditorStore::checkUpload( self::upload( $svg ), $this->attribute( 'image/svg+xml', 'Logo.svg' ), false );
        $this->assertSame( 'Logo.svg', $checked['name'] );
        $this->assertSame( 'image/svg+xml', $checked['mime'] );

        $onlyScript = $this->file( 'b.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>' );
        $this->assertRefused( 415, function () use ( $onlyScript ) { expImageEditorStore::checkUpload( self::upload( $onlyScript ), $this->attribute( 'image/svg+xml', 'b.svg' ), false ); } );
    }

    /** IE-06 */
    public function testTheMaximumFileSizeApplies()
    {
        // a random image does not compress below one megabyte
        $big = $this->dir . '/big.png';
        $pixels = imagecreatetruecolor( 1200, 1200 );
        for ( $y = 0; $y < 1200; $y++ )
            for ( $x = 0; $x < 1200; $x += 4 )
                imagesetpixel( $pixels, $x, $y, random_int( 0, 0xffffff ) );
        imagepng( $pixels, $big, 0 );
        $this->assertGreaterThan( 1024 * 1024, filesize( $big ) );

        $this->assertRefused( 413, function () use ( $big ) { expImageEditorStore::checkUpload( self::upload( $big ), $this->attribute( 'image/png', 'big.png', 1 ), false ); } );
        $this->assertSame( 'big.png', expImageEditorStore::checkUpload( self::upload( $big ), $this->attribute( 'image/png', 'big.png', 0 ), false )['name'] );
    }

    /** IE-08 */
    public function testAnAttributeWithoutAnImageTakesANewDrawing()
    {
        $empty = $this->attribute( '', '', 0, false );
        $svg = $this->file( 'new.svg', '<svg xmlns="http://www.w3.org/2000/svg" width="800" height="600"><rect width="10" height="10"/></svg>' );
        $checked = expImageEditorStore::checkUpload( self::upload( $svg ), $empty, false );
        $this->assertSame( array( 'path' => $svg, 'name' => 'drawing.svg', 'mime' => 'image/svg+xml', 'temporary' => false ), $checked,
                           'an SVG is not re-encoded: the store writes it only as the sanitizer\'s output' );

        $png = $this->raster( 'new.png' );
        $e = $this->assertRefused( 415, function () use ( $png, $empty ) { expImageEditorStore::checkUpload( self::upload( $png ), $empty, false ); } );
        $this->assertSame( 'A new drawing must be an SVG image.', $e->getMessage() );
    }

    /** IE-07 */
    public function testOnlyTheUsersOwnDraftIsEditable()
    {
        $object = function ( $canEdit ) {
            $object = $this->createMock( eZContentObject::class );
            $object->expects( $this->once() )->method( 'canEdit' )->with( false, false, false, 'ger-DE' )->willReturn( $canEdit );
            return $object;
        };
        $version = function ( $status, $creator ) {
            $version = $this->createStub( eZContentObjectVersion::class );
            $version->method( 'attribute' )->willReturnMap( array( array( 'status', $status ), array( 'creator_id', $creator ) ) );
            return $version;
        };

        expImageEditorStore::checkEditable( $object( true ), $version( eZContentObjectVersion::STATUS_DRAFT, 14 ), 'ger-DE', 14 );
        expImageEditorStore::checkEditable( $object( true ), $version( eZContentObjectVersion::STATUS_INTERNAL_DRAFT, 14 ), 'ger-DE', 14 );
        $this->assertRefused( 403, function () use ( $object, $version ) { expImageEditorStore::checkEditable( $object( false ), $version( eZContentObjectVersion::STATUS_DRAFT, 14 ), 'ger-DE', 14 ); } );
        $this->assertRefused( 403, function () use ( $object, $version ) { expImageEditorStore::checkEditable( $object( true ), $version( eZContentObjectVersion::STATUS_DRAFT, 15 ), 'ger-DE', 14 ); } );
        $this->assertRefused( 403, function () use ( $object, $version ) { expImageEditorStore::checkEditable( $object( true ), $version( eZContentObjectVersion::STATUS_PUBLISHED, 14 ), 'ger-DE', 14 ); } );
        $this->assertRefused( 403, function () use ( $object, $version ) { expImageEditorStore::checkEditable( $object( true ), $version( eZContentObjectVersion::STATUS_ARCHIVED, 14 ), 'ger-DE', 14 ); } );
    }
}
