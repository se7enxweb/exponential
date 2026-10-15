<?php
/**
 * SVG images in the image datatype, without a database.
 *
 *  IS-01 - an SVG is accepted (edit form, upload, AJAX uploader checks) and a harmful one is accepted to be stored sanitized
 *  IS-02 - SanitizeSVG=disabled, or SVG off the list: refused with "Images of the type image/svg+xml are not accepted."
 *  IS-03 - an SVG in which nothing safe remains, or that is not well-formed, is refused
 *  IS-04 - the package thumbnails stay raster images
 *  IS-05 - sanitizeSVGFile() writes the sanitized file, also from .svgz, and writes nothing when SVG is not accepted
 *  IS-06 - every alias of an SVG is the original file, with its width and height; nothing is converted
 *  IS-07 - the image manager never converts an SVG
 *  IS-08 - SVG names pass the name check only while SVG is accepted
 *  IS-09 - a file that would be stored as SVG (by its name or type) without being recognised as one is not stored
 *
 * The store itself (a content object, its version and the ezimagefile rows) needs the database; it is
 * checked on a running installation through the kernel API.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/datatypes/ezimage/expImageSvgTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

/** An alias handler with a fixed alias list and no content object attribute behind it */
class expImageSvgTestHandler extends eZImageAliasHandler
{
    /** @var array */
    public $fixedAliasList = array();

    public function __construct( $aliasList )
    {
        $this->ContentObjectAttributeData = array( 'id' => 0, 'contentobject_id' => 0, 'version' => 0 );
        $this->fixedAliasList = $aliasList;
    }

    function aliasList( $checkValidity = true )
    {
        return $this->fixedAliasList;
    }
}

class expImageSvgTest extends PHPUnit\Framework\TestCase
{
    const HARMFUL = '<?xml version="1.0"?><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 20" onload="alert(1)"><script>alert(2)</script><rect width="40" height="20" fill="#08f"/></svg>';

    /** @var string */
    private $dir;

    /** @var array settings changed by the test, to restore */
    private $saved = array();

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = dirname( __DIR__, 5 ) . '/var/tmp/exp_image_svg_' . getmypid() . '_' . mt_rand();
        mkdir( $this->dir, 0700, true );
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance( 'image.ini' );
        foreach ( $this->saved as $name => $value )
        {
            if ( $value === null )
                $ini->removeSetting( 'ImageUploadSettings', $name );
            else
                $ini->setVariable( 'ImageUploadSettings', $name, $value );
        }
        foreach ( (array)glob( $this->dir . '/*' ) as $file )
            unlink( $file );
        if ( is_dir( $this->dir ) )
            rmdir( $this->dir );
        parent::tearDown();
    }

    private function setting( $name, $value )
    {
        $ini = eZINI::instance( 'image.ini' );
        if ( !array_key_exists( $name, $this->saved ) )
            $this->saved[$name] = $ini->hasVariable( 'ImageUploadSettings', $name ) ? $ini->variable( 'ImageUploadSettings', $name ) : null;
        $ini->setVariable( 'ImageUploadSettings', $name, $value );
    }

    private function file( $name, $content )
    {
        $path = $this->dir . '/' . $name;
        file_put_contents( $path, $content );
        return $path;
    }

    /** IS-01 */
    public function testSvgIsAccepted()
    {
        $this->assertTrue( eZImageType::svgAccepted() );
        $this->assertFalse( eZImageType::imageFileError( $this->file( 'logo.svg', self::HARMFUL ), 'logo.svg' ) );
        $this->assertFalse( eZImageType::imageFileError( $this->file( 'logo.svgz', gzencode( self::HARMFUL ) ), 'logo.svgz' ) );
        // an upload's temporary file has no name to go by
        $this->assertFalse( eZImageType::imageFileError( $this->file( 'phpA1b2C3', self::HARMFUL ) ) );
    }

    /** IS-02 */
    public function testRefusedWhenNotSanitizedOrNotListed()
    {
        $svg = $this->file( 'logo.svg', self::HARMFUL );
        $this->setting( 'SanitizeSVG', 'disabled' );
        $this->assertFalse( eZImageType::svgAccepted() );
        $this->assertSame( 'Images of the type image/svg+xml are not accepted.', eZImageType::imageFileError( $svg, 'logo.svg' ) );
        $this->assertFalse( eZImageType::sanitizeSVGFile( $svg ) );
        $this->assertSame( self::HARMFUL, file_get_contents( $svg ), 'nothing is written' );

        $this->setting( 'SanitizeSVG', 'enabled' );
        $this->setting( 'AllowedMIMETypes', array( 'image/png', 'image/jpeg' ) );
        $this->assertFalse( eZImageType::svgAccepted() );
        $this->assertSame( 'Images of the type image/svg+xml are not accepted.', eZImageType::imageFileError( $svg, 'logo.svg' ) );
    }

    /** IS-03 */
    public function testNothingSafeOrBrokenIsRefused()
    {
        $onlyScript = $this->file( 'x.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>' );
        $this->assertSame( 'The SVG image could not be read, or nothing in it can be shown safely.', eZImageType::imageFileError( $onlyScript, 'x.svg' ) );
        $broken = $this->file( 'y.svg', '<svg xmlns="http://www.w3.org/2000/svg"><rect></svg>' );
        $this->assertIsString( eZImageType::imageFileError( $broken, 'y.svg' ) );
        $xxe = $this->file( 'z.svg', '<?xml version="1.0"?><!DOCTYPE svg [<!ENTITY x SYSTEM "file:///etc/passwd">]><svg xmlns="http://www.w3.org/2000/svg"><text>&x;</text></svg>' );
        $this->assertIsString( eZImageType::imageFileError( $xxe, 'z.svg' ) );
    }

    /** IS-04 */
    public function testPackageThumbnailsStayRaster()
    {
        $svg = $this->file( 'thumb.svg', self::HARMFUL );
        $this->assertSame( 'Images of the type image/svg+xml are not accepted.', eZImageType::imageFileError( $svg, 'thumb.svg', false ) );
    }

    /** IS-05 */
    public function testSanitizeSVGFile()
    {
        $svg = $this->file( 'logo.svgz', gzencode( self::HARMFUL ) );
        $target = $this->dir . '/stored.svg';
        $this->assertTrue( eZImageType::sanitizeSVGFile( $svg, $target ) );
        $stored = file_get_contents( $target );
        $this->assertStringNotContainsString( 'script', $stored );
        $this->assertStringNotContainsString( 'onload', $stored );
        $this->assertStringContainsString( '<rect width="40" height="20" fill="#08f"/>', $stored );
        $this->assertSame( array( 40, 20 ), expSvgSanitizer::dimensions( $target ) );

        $inPlace = $this->file( 'tmp_upload', self::HARMFUL );
        $this->assertTrue( eZImageType::sanitizeSVGFile( $inPlace ) );
        $this->assertSame( $stored, file_get_contents( $inPlace ) );
    }

    /** IS-06 */
    public function testEveryAliasOfAnSvgIsTheOriginal()
    {
        $original = array( 'name' => 'original', 'url' => 'var/storage/images/logo/123-1-eng-GB/logo.svg',
                           'filename' => 'logo.svg', 'suffix' => 'svg', 'dirpath' => 'var/storage/images/logo/123-1-eng-GB',
                           'basename' => 'logo', 'mime_type' => 'image/svg+xml', 'width' => '40', 'height' => '20',
                           'alternative_text' => 'Logo', 'text' => 'Logo', 'original_filename' => 'logo.svg',
                           'is_valid' => '1', 'is_new' => false, 'filesize' => 123, 'full_path' => 'var/storage/images/logo/123-1-eng-GB/logo.svg' );
        $handler = new expImageSvgTestHandler( array( 'original' => $original ) );
        $manager = eZImageManager::factory();
        $tested = 0;
        foreach ( array_keys( $manager->aliasList() ) as $aliasName )
        {
            if ( $aliasName === 'original' )
                continue;
            $alias = $handler->imageAlias( $aliasName );
            $this->assertIsArray( $alias, $aliasName );
            $this->assertSame( $aliasName, $alias['name'] );
            $this->assertSame( $original['url'], $alias['url'] );
            $this->assertSame( $original['full_path'], $alias['full_path'] );
            $this->assertSame( '40', $alias['width'] );
            $this->assertSame( '20', $alias['height'] );
            $this->assertSame( 'image/svg+xml', $alias['mime_type'] );
            $this->assertSame( 'Logo', $alias['text'] );
            $this->assertFalse( $alias['is_new'] );
            $tested++;
        }
        $this->assertGreaterThan( 0, $tested );
        $this->assertSame( $original, $handler->imageAlias( 'original' ) );
        $this->assertNull( $handler->imageAlias( 'no_such_alias_' . uniqid() ) );
        // no alias was added to the attribute
        $this->assertSame( array( 'original' ), array_keys( $handler->fixedAliasList ) );
    }

    /** IS-07 */
    public function testTheImageManagerNeverConvertsAnSvg()
    {
        $svg = $this->file( 'logo.svg', self::HARMFUL );
        $manager = eZImageManager::factory();
        $destination = $this->dir . '/logo_small.png';
        $this->assertFalse( $manager->convert( $svg, $destination, 'small' ) );
        $this->assertFileDoesNotExist( $destination );

        $list = array( 'original' => array( 'url' => $svg, 'mime_type' => 'image/svg+xml', 'alternative_text' => '' ) );
        $before = self::currentExceptionHandler();
        $this->assertFalse( $manager->createImageAlias( 'small', $list, array( 'basename' => 'logo' ) ) );
        $this->assertSame( array( 'original' ), array_keys( $list ) );
        // the cluster file handler registers the kernel's shutdown and exception handlers once
        if ( self::currentExceptionHandler() !== $before )
            restore_exception_handler();
    }

    private static function currentExceptionHandler()
    {
        $current = set_exception_handler( static function ( $e ) {} );
        restore_exception_handler();
        return $current;
    }

    /** IS-08 */
    public function testSvgNamesFollowTheSetting()
    {
        $check = new ReflectionMethod( 'eZImageType', 'validateImageFileExtension' );
        $this->assertTrue( $check->invoke( null, 'logo.svg' ) );
        $this->assertTrue( $check->invoke( null, 'logo.svgz' ) );
        $this->assertTrue( $check->invoke( null, 'photo.jpg' ) );
        $this->setting( 'SanitizeSVG', 'disabled' );
        $this->assertFalse( $check->invoke( null, 'logo.svg' ) );
        $this->assertTrue( $check->invoke( null, 'photo.jpg' ) );
    }

    /** IS-09 */
    public function testAFileStoredAsSvgIsAlwaysTheSanitizersOutput()
    {
        $svgMime = eZMimeType::findByURL( 'logo.svg' );
        $this->assertTrue( eZImageAliasHandler::isUnsanitizedSVG( $svgMime, false ) );
        $this->assertTrue( eZImageAliasHandler::isUnsanitizedSVG( array( 'name' => 'image/svg+xml', 'suffix' => '' ), false ) );
        $this->assertTrue( eZImageAliasHandler::isUnsanitizedSVG( array( 'name' => 'application/octet-stream', 'suffix' => 'SVGZ' ), false ) );
        $this->assertFalse( eZImageAliasHandler::isUnsanitizedSVG( $svgMime, true ) );
        $this->assertFalse( eZImageAliasHandler::isUnsanitizedSVG( eZMimeType::findByURL( 'photo.png' ), false ) );

        // an SVG the content check does not recognise (UTF-16 here) is neither sanitized nor stored by
        // the package import's path
        $utf16 = $this->file( 'imported.svg', "\xFF\xFE" . mb_convert_encoding( self::HARMFUL, 'UTF-16LE', 'UTF-8' ) );
        $this->assertFalse( expSvgSanitizer::isSVGFile( $utf16 ) );
        $handler = new expImageSvgTestHandler( array() );
        $this->assertFalse( $handler->initializeFromFile( $utf16, '', 'imported.svg' ) );
    }
}
