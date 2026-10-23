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
        $this->assertSame( array( 'path' => $png, 'name' => 'Strand.png', 'mime' => 'image/png' ), $checked );
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
        $this->assertSame( array( 'path' => $svg, 'name' => 'drawing.svg', 'mime' => 'image/svg+xml' ), $checked );

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
