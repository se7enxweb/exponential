<?php
/**
 * The focal point and crop rectangle of images (expImageFocalPoint, eZImageManager, eZImageAliasHandler),
 * without a database.
 *
 *  FP-01 - focal points and crop rectangles are fractions of the original; anything else is refused
 *  FP-02 - they are read from and written to the <ezimage> XML, and other XML is left alone
 *  FP-03 - a crop filter is centered on the focal point, as far as the image allows
 *  FP-04 - the size is followed through scale filters, so a scale-then-crop alias centers too
 *  FP-05 - without a focal point a crop filter keeps its own offsets; an unknown filter stops the centering
 *  FP-06 - the crop rectangle is cut out before the alias's filters
 *  FP-07 - an alias made from another alias: the reference chain and the focal point followed through it
 *  FP-08 - pixels: the image manager makes the alias around the focal point, and from the crop rectangle;
 *          without either the alias is made exactly as before
 *  FP-09 - the alias handler reads the focal point from the attribute's XML for templates, refuses
 *          values that are not valid and images that cannot have one (SVG, no image)
 *
 * Storing a new focal point (setFocalPoint() stores the original again for the version) needs the
 * database and the cluster file handler; it is checked on a running installation.
 *
 * Run: php vendor/bin/phpunit tests/tests/lib/ezimage/expImageFocalPointTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

/** An alias handler with a given XML and alias list and no content object attribute behind it */
class expImageFocalPointTestHandler extends eZImageAliasHandler
{
    /** @var array */
    public $fixedAliasList = array();

    public function __construct( $xml, $aliasList = array() )
    {
        $this->ContentObjectAttributeData = array( 'id' => 0, 'contentobject_id' => 0, 'version' => 0,
                                                   'data_text' => $xml, 'DataTypeCustom' => array() );
        $this->fixedAliasList = $aliasList;
    }

    function aliasList( $checkValidity = true )
    {
        return $this->fixedAliasList;
    }
}

class expImageFocalPointTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $dir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = dirname( __DIR__, 4 ) . '/var/tmp/exp_image_focal_' . getmypid() . '_' . mt_rand();
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

    private static function filter( $name, array $data = array() )
    {
        return array( 'name' => $name, 'data' => $data ?: false );
    }

    /** FP-01 */
    public function testValuesAreFractionsOfTheOriginal()
    {
        $this->assertSame( array( 'x' => 0.25, 'y' => 1.0 ), expImageFocalPoint::normalizePoint( '0.25', 1 ) );
        $this->assertSame( array( 'x' => 0.0, 'y' => 0.333333 ), expImageFocalPoint::normalizePoint( 0, 1 / 3 ) );
        foreach ( array( array( -0.1, 0.5 ), array( 0.5, 1.2 ), array( 'a', 0.5 ), array( null, 0.5 ), array( array(), 0.5 ), array( NAN, 0.5 ), array( '', '' ) ) as $pair )
            $this->assertNull( expImageFocalPoint::normalizePoint( $pair[0], $pair[1] ), var_export( $pair, true ) );

        $crop = expImageFocalPoint::normalizeCrop( array( 'x' => '0.1', 'y' => 0, 'width' => 0.5, 'height' => '0.75' ) );
        $this->assertSame( array( 'x' => 0.1, 'y' => 0.0, 'width' => 0.5, 'height' => 0.75 ), $crop );
        $this->assertNull( expImageFocalPoint::normalizeCrop( array( 'x' => 0, 'y' => 0, 'width' => 1, 'height' => 1 ) ), 'the whole image is no crop' );
        $this->assertNull( expImageFocalPoint::normalizeCrop( array( 'x' => 0.6, 'y' => 0, 'width' => 0.5, 'height' => 1 ) ), 'outside the image' );
        $this->assertNull( expImageFocalPoint::normalizeCrop( array( 'x' => 0, 'y' => 0, 'width' => 0.001, 'height' => 1 ) ), 'too small' );
        $this->assertNull( expImageFocalPoint::normalizeCrop( array( 'x' => 0, 'y' => 0, 'width' => 0.5 ) ), 'incomplete' );
        $this->assertNull( expImageFocalPoint::normalizeCrop( 'x=0' ) );
    }

    /** FP-02 */
    public function testXMLRoundTrip()
    {
        $doc = new DOMDocument( '1.0', 'utf-8' );
        $doc->loadXML( '<ezimage serial_number="1" url="var/storage/images/a.jpg"><original attribute_id="1"/><alias name="small" url="x"/></ezimage>' );
        $focal = array( 'x' => 0.25, 'y' => 0.4 );
        $crop = array( 'x' => 0.1, 'y' => 0.0, 'width' => 0.8, 'height' => 1.0 );
        expImageFocalPoint::writeToXML( $doc->documentElement, $focal, $crop );
        $xml = $doc->saveXML();
        $this->assertStringContainsString( '<focal x="0.25" y="0.4"/>', $xml );
        $this->assertStringContainsString( '<crop x="0.1" y="0" width="0.8" height="1"/>', $xml );
        $this->assertSame( array( 'focal' => $focal, 'crop' => $crop ), expImageFocalPoint::readFromXMLString( $xml ) );

        // written again: replaced, not added; null removes; the rest stays
        expImageFocalPoint::writeToXML( $doc->documentElement, array( 'x' => 0.5, 'y' => 0.5 ), null );
        $xml = $doc->saveXML();
        $this->assertSame( 1, substr_count( $xml, '<focal' ) );
        $this->assertStringNotContainsString( '<crop', $xml );
        $this->assertStringContainsString( '<alias name="small" url="x"/>', $xml );

        $none = array( 'focal' => null, 'crop' => null );
        $this->assertSame( $none, expImageFocalPoint::readFromXMLString( '<ezimage serial_number="1"/>' ) );
        $this->assertSame( $none, expImageFocalPoint::readFromXMLString( '<ezimage><focal x="2" y="0"/></ezimage>' ) );
        $this->assertSame( $none, expImageFocalPoint::readFromXMLString( '<other><focal x="0.2" y="0"/></other>' ) );
        $this->assertSame( $none, expImageFocalPoint::readFromXMLString( '<ezimage><focal' ) );
        $this->assertSame( $none, expImageFocalPoint::readFromXMLString( null ) );
        $this->assertSame( '25% 40%', expImageFocalPoint::cssPosition( $focal ) );
        $this->assertSame( '50% 50%', expImageFocalPoint::cssPosition( null ) );
    }

    /** FP-03 */
    public function testCropIsCenteredOnTheFocalPoint()
    {
        $filters = array( self::filter( 'geometry/crop', array( 100, 100, 0, 0 ) ) );
        $plan = expImageFocalPoint::plan( $filters, 400, 200, array( 'x' => 0.5, 'y' => 0.5 ) );
        $this->assertSame( array( 100, 100, 150, 50 ), $plan['filters'][0]['data'] );
        $this->assertSame( array( 'x' => 150.0, 'y' => 50.0, 'width' => 100.0, 'height' => 100.0 ), $plan['source'] );
        $this->assertSame( array( 'x' => 0.5, 'y' => 0.5 ), $plan['focal'] );

        // at the edge the window stays inside the image, the focal point moves within it
        $plan = expImageFocalPoint::plan( $filters, 400, 200, array( 'x' => 0.95, 'y' => 0.0 ) );
        $this->assertSame( array( 100, 100, 300, 0 ), $plan['filters'][0]['data'] );
        $this->assertSame( array( 'x' => 0.8, 'y' => 0.0 ), $plan['focal'] );
        $this->assertSame( 100, $plan['width'] );

        // a window larger than the image starts at 0
        $plan = expImageFocalPoint::plan( array( self::filter( 'geometry/crop', array( 500, 300, 7, 7 ) ) ), 400, 200, array( 'x' => 0.9, 'y' => 0.9 ) );
        $this->assertSame( array( 500, 300, 0, 0 ), $plan['filters'][0]['data'] );
        $this->assertSame( array( 400, 200 ), array( $plan['width'], $plan['height'] ) );
    }

    /** FP-04 */
    public function testScaleThenCropCenters()
    {
        // ezflow's pattern: scale to a width, then crop to a height
        $filters = array( self::filter( 'geometry/scalewidth', array( 200 ) ), self::filter( 'geometry/crop', array( 200, 50, 0, 0 ) ) );
        $plan = expImageFocalPoint::plan( $filters, 800, 800, array( 'x' => 0.5, 'y' => 0.75 ) );
        // 800x800 -> 200x200, focal y 150, window 125..175
        $this->assertSame( array( 200, 50, 0, 125 ), $plan['filters'][1]['data'] );
        $this->assertSame( array( 'x' => 0.0, 'y' => 500.0, 'width' => 800.0, 'height' => 200.0 ), $plan['source'] );

        $checks = array(
            array( 'geometry/scale', array( 100, 100 ), 400, 200, array( 100, 50 ) ),
            array( 'geometry/scaledownonly', array( 1000, 1000 ), 400, 200, array( 400, 200 ) ),
            array( 'geometry/scaledownonly', array( 100, 100 ), 400, 200, array( 100, 50 ) ),
            array( 'geometry/scalewidthdownonly', array( 800 ), 400, 200, array( 400, 200 ) ),
            array( 'geometry/scaleheight', array( 100 ), 400, 200, array( 200, 100 ) ),
            array( 'geometry/scaleheightdownonly', array( 100 ), 400, 200, array( 200, 100 ) ),
            array( 'geometry/scaleexact', array( 30, 70 ), 400, 200, array( 30, 70 ) ),
            array( 'geometry/scalepercent', array( 50, 25 ), 400, 200, array( 200, 50 ) ),
            array( 'border', array( 4, 2 ), 400, 200, array( 408, 204 ) ),
            array( 'colorspace/gray', array(), 400, 200, array( 400, 200 ) ),
        );
        foreach ( $checks as $check )
        {
            $plan = expImageFocalPoint::plan( array( self::filter( $check[0], $check[1] ) ), $check[2], $check[3], array( 'x' => 0.5, 'y' => 0.5 ) );
            $this->assertSame( $check[4], array( $plan['width'], $plan['height'] ), $check[0] );
        }
    }

    /** FP-05 */
    public function testWithoutFocalPointOrWithAnUnknownFilterOffsetsStay()
    {
        $filters = array( self::filter( 'geometry/crop', array( 100, 100, 20, 30 ) ) );
        $plan = expImageFocalPoint::plan( $filters, 400, 200, null );
        $this->assertSame( $filters, $plan['filters'] );
        $this->assertSame( array( 'x' => 20.0, 'y' => 30.0, 'width' => 100.0, 'height' => 100.0 ), $plan['source'] );

        $filters = array( self::filter( 'resize', array( '50%' ) ), self::filter( 'geometry/crop', array( 100, 100, 0, 0 ) ) );
        $plan = expImageFocalPoint::plan( $filters, 400, 200, array( 'x' => 0.9, 'y' => 0.9 ) );
        $this->assertSame( $filters, $plan['filters'] );
        $this->assertFalse( $plan['width'] );
        $this->assertFalse( $plan['source'] );

        // a size that is not known yet stops the plan too
        $plan = expImageFocalPoint::plan( $filters, 0, 0, array( 'x' => 0.9, 'y' => 0.9 ) );
        $this->assertSame( $filters, $plan['filters'] );
    }

    /** FP-06 */
    public function testTheCropRectangleIsCutFirst()
    {
        $crop = array( 'x' => 0.5, 'y' => 0.25, 'width' => 0.5, 'height' => 0.5 );
        $plan = expImageFocalPoint::plan( array( self::filter( 'geometry/scaledownonly', array( 100, 100 ) ) ), 400, 200, null, $crop );
        $this->assertSame( array( 'name' => 'geometry/crop', 'data' => array( 200, 100, 200, 50 ) ), $plan['filters'][0] );
        $this->assertSame( 'geometry/scaledownonly', $plan['filters'][1]['name'] );
        $this->assertSame( array( 100, 50 ), array( $plan['width'], $plan['height'] ) );
        $this->assertSame( array( 'x' => 200.0, 'y' => 50.0, 'width' => 200.0, 'height' => 100.0 ), $plan['source'] );

        // the focal point is in the original; inside the cut its position is relative to the cut
        $plan = expImageFocalPoint::plan( array( self::filter( 'geometry/crop', array( 50, 50, 0, 0 ) ) ), 400, 200, array( 'x' => 0.75, 'y' => 0.5 ), $crop );
        $this->assertSame( array( 50, 50, 75, 25 ), $plan['filters'][1]['data'] );
        $this->assertSame( array( 'x' => 275.0, 'y' => 75.0, 'width' => 50.0, 'height' => 50.0 ), $plan['source'] );
    }

    /** FP-07 */
    public function testReferenceChains()
    {
        $aliasList = array(
            'reference' => array( 'reference' => false, 'filters' => array( self::filter( 'geometry/scaledownonly', array( 200, 200 ) ) ) ),
            'square' => array( 'reference' => 'reference', 'filters' => array( self::filter( 'geometry/crop', array( 50, 50, 0, 0 ) ) ) ),
            'loop1' => array( 'reference' => 'loop2', 'filters' => array() ),
            'loop2' => array( 'reference' => 'loop1', 'filters' => array() ),
            'broken' => array( 'reference' => 'missing', 'filters' => array() ),
        );
        $this->assertSame( array(), expImageFocalPoint::referenceChain( $aliasList, 'reference' ) );
        $this->assertSame( array( 'reference' ), expImageFocalPoint::referenceChain( $aliasList, 'square' ) );
        $this->assertSame( array( 'loop2' ), expImageFocalPoint::referenceChain( $aliasList, 'loop1' ) );
        $this->assertSame( array(), expImageFocalPoint::referenceChain( $aliasList, 'broken' ) );
        $this->assertSame( array( 'geometry/scaledownonly', 'geometry/crop' ),
                           array_column( expImageFocalPoint::filterChain( $aliasList, 'square' ), 'name' ) );

        // 800x400 cut to its right half (400x400), scaled to 200x200: the focal point at 3/4, 1/4 of the
        // original is at 1/2, 1/4 of the reference
        $crop = array( 'x' => 0.5, 'y' => 0.0, 'width' => 0.5, 'height' => 1.0 );
        $focal = expImageFocalPoint::focalInSource( $aliasList, 'square', array( 'x' => 0.75, 'y' => 0.25 ), $crop, 800, 400 );
        $this->assertSame( array( 'x' => 0.5, 'y' => 0.25 ), $focal );
        $this->assertNull( expImageFocalPoint::focalInSource( $aliasList, 'square', null, $crop, 800, 400 ) );

        // the whole chain from the original gives the same window as the alias made from the reference
        $plan = expImageFocalPoint::plan( expImageFocalPoint::filterChain( $aliasList, 'square' ), 800, 400, array( 'x' => 0.75, 'y' => 0.25 ), $crop );
        $this->assertSame( array( 'x' => 550.0, 'y' => 50.0, 'width' => 100.0, 'height' => 100.0 ), $plan['source'] );
    }

    /**
     * A 400x200 PNG: the left half red, the right half blue, a green square at 300..340, 20..60.
     */
    private function sampleImage()
    {
        $image = imagecreatetruecolor( 400, 200 );
        imagefilledrectangle( $image, 0, 0, 199, 199, imagecolorallocate( $image, 255, 0, 0 ) );
        imagefilledrectangle( $image, 200, 0, 399, 199, imagecolorallocate( $image, 0, 0, 255 ) );
        imagefilledrectangle( $image, 300, 20, 339, 59, imagecolorallocate( $image, 0, 255, 0 ) );
        $path = $this->dir . '/sample.png';
        imagepng( $image, $path );
        return $path;
    }

    private static function color( $path, $x, $y )
    {
        $image = imagecreatefrompng( $path );
        $rgb = imagecolorat( $image, $x, $y );
        $color = array( ( $rgb >> 16 ) & 0xff, ( $rgb >> 8 ) & 0xff, $rgb & 0xff );
        if ( $color[0] > 200 && $color[1] < 60 && $color[2] < 60 )
            return 'red';
        if ( $color[2] > 200 && $color[0] < 60 && $color[1] < 60 )
            return 'blue';
        if ( $color[1] > 200 && $color[0] < 60 && $color[2] < 60 )
            return 'green';
        return implode( ',', $color );
    }

    /** FP-08 */
    public function testTheImageManagerMakesAliasesAroundTheFocalPoint()
    {
        if ( !function_exists( 'imagecreatetruecolor' ) )
            $this->markTestSkipped( 'GD is needed to draw the sample image' );
        eZImageManager::factory()->appendImageAlias( array( 'name' => 'exp_focal_test_crop', 'reference' => false, 'mime_type' => false, 'quality' => false,
                                                            'filters' => array( self::filter( 'geometry/crop', array( 40, 40, 0, 0 ) ) ) ) );
        $source = $this->sampleImage();

        // no focal data: exactly as before (the top left corner)
        $plain = $this->convert( $source, 'plain.png', 'exp_focal_test_crop' );
        $this->assertNotFalse( $plain );
        $this->assertSame( array( 40, 40 ), array_slice( getimagesize( $plain ), 0, 2 ) );
        $this->assertSame( 'red', self::color( $plain, 20, 20 ) );

        // focal point on the green square: the 40x40 window is the square
        $focal = $this->convert( $source, 'focal.png', 'exp_focal_test_crop',
                                 array( 'focal' => array( 'focal' => array( 'x' => 0.8, 'y' => 0.2 ), 'crop' => null, 'width' => 400, 'height' => 200 ) ) );
        $this->assertNotFalse( $focal );
        $this->assertSame( array( 40, 40 ), array_slice( getimagesize( $focal ), 0, 2 ) );
        foreach ( array( array( 1, 1 ), array( 20, 20 ), array( 38, 38 ) ) as $point )
            $this->assertSame( 'green', self::color( $focal, $point[0], $point[1] ) );

        // crop rectangle: the lower right quarter of the image; the alias crop keeps its 0,0 offsets in it
        $cropped = $this->convert( $source, 'cropped.png', 'exp_focal_test_crop',
                                   array( 'focal' => array( 'focal' => null, 'crop' => array( 'x' => 0.5, 'y' => 0.5, 'width' => 0.5, 'height' => 0.5 ),
                                                            'width' => 400, 'height' => 200 ) ) );
        $this->assertNotFalse( $cropped );
        $this->assertSame( 'blue', self::color( $cropped, 20, 20 ) );

        // the original alias never gets either
        $original = $this->convert( $source, 'original.png', 'original',
                                    array( 'focal' => array( 'focal' => array( 'x' => 0.8, 'y' => 0.2 ), 'crop' => array( 'x' => 0.5, 'y' => 0.5, 'width' => 0.5, 'height' => 0.5 ) ) ) );
        $this->assertNotFalse( $original );
        $this->assertSame( array( 400, 200 ), array_slice( getimagesize( $original ), 0, 2 ) );
    }

    /**
     * Converts $source with the alias $alias and moves the result to $name in the test directory (the
     * image manager names the file after the source and the alias), returns the path or false.
     */
    private function convert( $source, $name, $alias, $parameters = array() )
    {
        $path = $this->dir . '/' . $name;
        $destination = $path;
        $before = self::currentExceptionHandler();
        $done = eZImageManager::factory()->convert( $source, $destination, $alias, $parameters );
        // the cluster file handler registers the kernel's shutdown and exception handlers once
        if ( self::currentExceptionHandler() !== $before )
            restore_exception_handler();
        $written = is_array( $destination ) && isset( $destination['url'] ) ? $destination['url'] : $destination;
        if ( !$done || !is_string( $written ) || !is_file( $written ) )
            return false;
        if ( $written !== $path )
            rename( $written, $path );
        return $path;
    }

    private static function currentExceptionHandler()
    {
        $current = set_exception_handler( static function ( $e ) {} );
        restore_exception_handler();
        return $current;
    }

    /** FP-09 */
    public function testTheAliasHandlerReadsAndRefuses()
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?><ezimage serial_number="1" url="var/storage/images/a.png"><original attribute_id="1"/>' .
               '<focal x="0.25" y="0.75"/><crop x="0" y="0.1" width="0.5" height="0.5"/></ezimage>';
        $handler = new expImageFocalPointTestHandler( $xml );
        $this->assertSame( array( 'x' => 0.25, 'y' => 0.75 ), $handler->focalPoint() );
        $this->assertSame( array( 'x' => 0.0, 'y' => 0.1, 'width' => 0.5, 'height' => 0.5 ), $handler->cropRectangle() );
        $this->assertTrue( $handler->hasAttribute( 'focal_point' ) );
        $this->assertTrue( $handler->hasAttribute( 'crop_rectangle' ) );
        $this->assertSame( array( 'x' => 0.25, 'y' => 0.75, 'position' => '25% 75%' ), $handler->attribute( 'focal_point' ) );
        $this->assertSame( $handler->cropRectangle(), $handler->attribute( 'crop_rectangle' ) );

        $plain = new expImageFocalPointTestHandler( '<?xml version="1.0" encoding="utf-8"?><ezimage serial_number="1"/>' );
        $this->assertNull( $plain->attribute( 'focal_point' ) );
        $this->assertNull( $plain->attribute( 'crop_rectangle' ) );

        // values that are not valid: refused before anything is read or written
        $this->assertFalse( $plain->setFocalPoint( array( 'x' => 2, 'y' => 0 ) ) );
        $this->assertFalse( $plain->setFocalPoint( 'middle' ) );
        $this->assertFalse( $plain->setFocalPoint( null, array( 'x' => 0.9, 'y' => 0, 'width' => 0.5, 'height' => 1 ) ) );
        // no image, or an SVG: no focal point
        $this->assertFalse( $plain->supportsFocalPoint() );
        $this->assertFalse( $plain->setFocalPoint( array( 'x' => 0.5, 'y' => 0.5 ) ) );
        $svg = new expImageFocalPointTestHandler( $xml, array( 'original' => array( 'url' => 'var/storage/images/a.svg', 'mime_type' => 'image/svg+xml' ) ) );
        $this->assertFalse( $svg->supportsFocalPoint() );
        $this->assertFalse( $svg->setFocalPoint( array( 'x' => 0.5, 'y' => 0.5 ) ) );
        $png = new expImageFocalPointTestHandler( $xml, array( 'original' => array( 'url' => 'var/storage/images/a.png', 'mime_type' => 'image/png' ) ) );
        $this->assertTrue( $png->supportsFocalPoint() );
        // the same values again: nothing to do
        $this->assertTrue( $png->setFocalPoint( array( 'x' => 0.25, 'y' => 0.75 ), array( 'x' => 0, 'y' => 0.1, 'width' => 0.5, 'height' => 0.5 ) ) );
    }
}
