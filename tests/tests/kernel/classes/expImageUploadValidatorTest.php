<?php
/**
 * Uploaded images are checked by their content against image.ini [ImageUploadSettings] AllowedMIMETypes[].
 * No database: the images are made with GD in a throwaway directory.
 *
 *  IV-01 - JPEG, PNG, GIF, WebP and AVIF are accepted, with the type read from the content
 *  IV-02 - A PHP file named .jpg is refused, by the validator and by the image datatype
 *  IV-03 - SVG is refused by default; the validator accepts it only when it is listed, the datatype never
 *  IV-04 - Without fileinfo the older check gives the type, still against the list, and one warning is logged
 *  IV-05 - ValidateMIMEType=disabled: any image by the older check; an unlisted type is refused when enabled
 *  IV-06 - Type names are normalized (case, parameters, aliases); the shipped settings are the defaults
 *  IV-07 - eZContentUpload refuses a disguised file for an image attribute before any object exists
 *  IV-08 - Missing, empty and unreadable paths are refused
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commits c8e45fced7 and 04b3b6985a), Copyright (C) Mugo Web, GPL v2
 */

/** The validator as on a PHP build without fileinfo */
class expImageUploadValidatorWithoutFileInfo extends expImageUploadValidator
{
    public static function resetWarnings()
    {
        static::$finfoMissingWarnings = 0;
    }

    protected static function hasFileInfo()
    {
        return false;
    }
}

/** A class attribute: only its data type string */
class expImageUploadValidatorClassAttributeStandIn
{
    private $type;

    public function __construct( $type )
    {
        $this->type = $type;
    }

    public function attribute( $name )
    {
        return $name === 'data_type_string' ? $this->type : null;
    }
}

class expImageUploadValidatorTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $dir;

    protected function setUp(): void
    {
        parent::setUp();
        if ( !function_exists( 'imagecreatetruecolor' ) )
            $this->markTestSkipped( 'GD is needed to make the test images.' );
        $this->dir = dirname( __DIR__, 4 ) . '/var/tmp/exp_image_upload_validator_' . getmypid() . '_' . mt_rand();
        mkdir( $this->dir, 0700, true );
    }

    protected function tearDown(): void
    {
        foreach ( (array)glob( $this->dir . '/*' ) as $file )
            unlink( $file );
        if ( is_dir( $this->dir ) )
            rmdir( $this->dir );
        parent::tearDown();
    }

    /** Writes a 4 x 3 image in $format and returns its path */
    private function image( $format, $name = null )
    {
        $image = imagecreatetruecolor( 4, 3 );
        imagefilledrectangle( $image, 0, 0, 3, 2, imagecolorallocate( $image, 200, 30, 30 ) );
        $path = $this->dir . '/' . ( $name ?: 'image.' . $format );
        switch ( $format )
        {
            case 'jpeg': imagejpeg( $image, $path ); break;
            case 'png':  imagepng( $image, $path ); break;
            case 'gif':  imagegif( $image, $path ); break;
            case 'webp': imagewebp( $image, $path ); break;
            case 'avif': imageavif( $image, $path ); break;
            case 'bmp':  imagebmp( $image, $path ); break;
        }
        return $path;
    }

    private function file( $name, $contents )
    {
        $path = $this->dir . '/' . $name;
        file_put_contents( $path, $contents );
        return $path;
    }

    public static function acceptedFormats()
    {
        return array(
            'JPEG' => array( 'jpeg', 'image/jpeg', 'imagejpeg' ),
            'PNG'  => array( 'png', 'image/png', 'imagepng' ),
            'GIF'  => array( 'gif', 'image/gif', 'imagegif' ),
            'WebP' => array( 'webp', 'image/webp', 'imagewebp' ),
            'AVIF' => array( 'avif', 'image/avif', 'imageavif' ),
        );
    }

    /** IV-01 */
    #[\PHPUnit\Framework\Attributes\DataProvider('acceptedFormats')]
    public function testFormatAccepted( $format, $mime, $writer )
    {
        if ( !function_exists( $writer ) )
            $this->markTestSkipped( "GD cannot write $format here." );
        $path = $this->image( $format, 'upload_without_extension' );
        if ( !is_file( $path ) || !filesize( $path ) )
            $this->markTestSkipped( "GD wrote no $format file here." );

        $this->assertSame( array( 'valid' => true, 'mime' => $mime ),
                           expImageUploadValidator::validateAgainst( $path, expImageUploadValidator::DEFAULT_ALLOWED_MIME_TYPES ) );
        $this->assertSame( array( 'valid' => true, 'mime' => $mime ), expImageUploadValidator::validate( $path ) );
    }

    /** IV-01: the datatype stores what the validator and getimagesize() accept */
    public function testImageDatatypeAcceptsPng()
    {
        $this->assertFalse( eZImageType::imageFileError( $this->image( 'png' ), 'photo.png' ) );
    }

    /** IV-02 */
    public function testPhpFileNamedJpgIsRefused()
    {
        $path = $this->file( 'shell.jpg', "<?php echo 'x'; system( \$_GET['c'] ); ?>\n" );

        $result = expImageUploadValidator::validate( $path, 'shell.jpg' );
        $this->assertFalse( $result['valid'] );
        $this->assertStringStartsNotWith( 'image/', $result['mime'] );
        $this->assertIsString( eZImageType::imageFileError( $path, 'shell.jpg' ) );
    }

    /** IV-02: a JPEG header in front of a script is still not a listed image that PHP can read */
    public function testHtmlNamedPngIsRefused()
    {
        $path = $this->file( 'page.png', "<!doctype html><html><body><script>alert(1)</script></body></html>" );
        $this->assertFalse( expImageUploadValidator::validate( $path, 'page.png' )['valid'] );
        $this->assertIsString( eZImageType::imageFileError( $path, 'page.png' ) );
    }

    /** IV-03 */
    public function testSvgRefusedUnlessListed()
    {
        $svg = $this->file( 'drawing.svg', '<?xml version="1.0"?><svg xmlns="http://www.w3.org/2000/svg" width="4" height="3"><script>alert(1)</script><rect width="4" height="3"/></svg>' );

        $default = expImageUploadValidator::validateAgainst( $svg, expImageUploadValidator::DEFAULT_ALLOWED_MIME_TYPES );
        $this->assertFalse( $default['valid'] );
        $this->assertSame( 'image/svg+xml', $default['mime'] );
        $this->assertFalse( expImageUploadValidator::validate( $svg )['valid'] );
        $this->assertNotContains( 'image/svg+xml', expImageUploadValidator::allowedMIMETypes() );

        $listed = expImageUploadValidator::validateAgainst( $svg, array( 'image/png', 'image/svg+xml' ) );
        $this->assertTrue( $listed['valid'] );

        // the image datatype stores raster images only, whatever the list says
        $this->assertIsString( eZImageType::imageFileError( $svg, 'drawing.svg' ) );
    }

    /** IV-04 */
    public function testWithoutFileInfoFallsBackAndWarnsOnce()
    {
        expImageUploadValidatorWithoutFileInfo::resetWarnings();
        $this->assertSame( 0, expImageUploadValidatorWithoutFileInfo::fileInfoMissingWarnings() );

        $png = $this->image( 'png', 'upload_png' );
        $this->assertSame( array( 'valid' => true, 'mime' => 'image/png' ),
                           expImageUploadValidatorWithoutFileInfo::validateAgainst( $png, array( 'image/png' ) ) );
        $this->assertSame( 1, expImageUploadValidatorWithoutFileInfo::fileInfoMissingWarnings() );

        // still against the list
        $this->assertFalse( expImageUploadValidatorWithoutFileInfo::validateAgainst( $png, array( 'image/jpeg' ) )['valid'] );
        $php = $this->file( 'shell.jpg', "<?php phpinfo(); ?>" );
        $this->assertSame( array( 'valid' => false, 'mime' => '' ),
                           expImageUploadValidatorWithoutFileInfo::validateAgainst( $php, array( 'image/jpeg' ), true, 'shell.jpg' ) );

        // logged once, not once per call
        $this->assertSame( 1, expImageUploadValidatorWithoutFileInfo::fileInfoMissingWarnings() );
        expImageUploadValidatorWithoutFileInfo::resetWarnings();
    }

    /** IV-05 */
    public function testAllowListAndDisabledCheck()
    {
        $gif = $this->image( 'gif' );
        $this->assertSame( array( 'valid' => false, 'mime' => 'image/gif' ),
                           expImageUploadValidator::validateAgainst( $gif, array( 'image/png' ) ) );
        $this->assertSame( array( 'valid' => true, 'mime' => 'image/gif' ),
                           expImageUploadValidator::validateAgainst( $gif, array( 'image/png' ), false ) );
        $this->assertFalse( expImageUploadValidator::validateAgainst( $this->file( 'x.gif', 'plain text, no image' ), array(), false )['valid'] );
    }

    /** IV-06 */
    public function testNormalizeAndDefaults()
    {
        $this->assertSame( 'image/jpeg', expImageUploadValidator::normalize( ' IMAGE/PJPEG ' ) );
        $this->assertSame( 'image/bmp', expImageUploadValidator::normalize( 'image/x-ms-bmp' ) );
        $this->assertSame( 'image/png', expImageUploadValidator::normalize( 'image/png; charset=binary' ) );
        $this->assertSame( '', expImageUploadValidator::normalize( array( 'image/png' ) ) );
        $this->assertTrue( expImageUploadValidator::isEnabled() );
        $this->assertSame( expImageUploadValidator::DEFAULT_ALLOWED_MIME_TYPES, expImageUploadValidator::allowedMIMETypes() );

        if ( function_exists( 'imagebmp' ) )
        {
            $bmp = expImageUploadValidator::validate( $this->image( 'bmp' ) );
            $this->assertSame( array( 'valid' => true, 'mime' => 'image/bmp' ), $bmp );
        }
    }

    /** IV-07 */
    public function testContentUploadRefusesDisguisedImage()
    {
        $dataMap = array( 'image' => new expImageUploadValidatorClassAttributeStandIn( 'ezimage' ),
                          'file' => new expImageUploadValidatorClassAttributeStandIn( 'ezbinaryfile' ) );
        $php = $this->file( 'shell.jpg', "<?php phpinfo(); ?>" );

        $this->assertIsString( eZContentUpload::imageUploadError( $dataMap, 'image', $php, 'shell.jpg' ) );
        $this->assertFalse( eZContentUpload::imageUploadError( $dataMap, 'image', $this->image( 'jpeg' ), 'photo.jpg' ) );
        // not an image attribute: the file datatypes keep their own checks
        $this->assertFalse( eZContentUpload::imageUploadError( $dataMap, 'file', $php, 'shell.jpg' ) );
        $this->assertFalse( eZContentUpload::imageUploadError( $dataMap, 'missing', $php, 'shell.jpg' ) );
    }

    /** IV-08 */
    public function testUnusablePathsAreRefused()
    {
        foreach ( array( '', $this->dir . '/none.png', $this->dir, "a\0b", null, array() ) as $path )
            $this->assertSame( array( 'valid' => false, 'mime' => '' ), expImageUploadValidator::validate( $path ) );
        $this->assertSame( array( 'valid' => false, 'mime' => '' ), expImageUploadValidator::validate( $this->file( 'empty.png', '' ) ) );
    }
}
