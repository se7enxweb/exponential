<?php
/**
 * The aliases of on-demand mode are computed exactly as they will be made, without a database:
 *
 *  IAC-01 - for every alias of image.ini (and test aliases: exact, width, percent, crop, scale then crop, PNG output,
 *           an alias made from another one), the computed file name, MIME type, width and height equal those of a
 *           real conversion by eZImageManager::createImageAlias()
 *  IAC-02 - the same with a focal point and a crop rectangle on the image
 *  IAC-03 - an alias made from another alias that exists already is computed from that one
 *  IAC-04 - eZImageManager::aliasTargetPlan() reads no file and converts nothing; an SVG and an unknown alias have no plan
 *  IAC-05 - the sizes follow the handler that converts: GD truncates, ImageMagick rounds; an unknown filter gives
 *           no size
 *  IAC-06 - the computed entry a template gets: URL in the configured form, full path, is_valid, on_demand, no file
 *           size; a draft gets an expiring token, an outdated alias a remake time
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/imagealias/expImageAliasComputedTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expImageAliasComputedTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $dir;

    /** @var string */
    private $original;

    /** @var int[] */
    private $size;

    /** Test aliases, added to the image manager once */
    private static $testAliases = array(
        'exp_iac_exact' => array( 'filters' => array( 'geometry/scaleexact=120;90' ) ),
        'exp_iac_width' => array( 'filters' => array( 'geometry/scalewidth=250' ) ),
        'exp_iac_percent' => array( 'filters' => array( 'geometry/scalepercent=30;30' ) ),
        'exp_iac_crop' => array( 'filters' => array( 'geometry/crop=100;80;10;20' ) ),
        'exp_iac_scalecrop' => array( 'filters' => array( 'geometry/scaledownonly=300;300', 'geometry/crop=200;100;0;0' ) ),
        'exp_iac_png' => array( 'filters' => array( 'geometry/scaledownonly=150;150' ), 'mime_type' => 'image/png' ),
        'exp_iac_chained' => array( 'filters' => array( 'geometry/scaledownonly=100;100' ), 'reference' => 'exp_iac_width' ),
        'exp_iac_gray' => array( 'filters' => array( 'colorspace/gray', 'geometry/scaleheight=70' ) ),
    );

    /** @var string|false */
    private $cwd = false;

    /**
     * The kernel registers its shutdown and exception handlers (eZExecution) on the way through the cluster file
     * handler; done once here so no test is reported for adding them.
     */
    public static function setUpBeforeClass(): void
    {
        eZExecution::registerShutdownHandler();
    }

    protected function setUp(): void
    {
        parent::setUp();
        if ( !function_exists( 'imagecreatefromjpeg' ) )
            $this->markTestSkipped( 'GD is needed to convert the sample image' );
        // Storage paths are relative to the installation, as the kernel uses them
        $this->cwd = getcwd();
        chdir( dirname( __DIR__, 5 ) );
        $manager = eZImageManager::factory();
        foreach ( self::$testAliases as $name => $definition )
        {
            if ( $manager->hasAlias( $name ) )
                continue;
            $filters = array();
            foreach ( $definition['filters'] as $text )
                $filters[] = $manager->createFilterDataFromINI( $text );
            $manager->appendImageAlias( array( 'name' => $name, 'reference' => isset( $definition['reference'] ) ? $definition['reference'] : false,
                                               'mime_type' => isset( $definition['mime_type'] ) ? $definition['mime_type'] : false,
                                               'quality' => false, 'filters' => $filters ) );
        }
        $base = 'var/tmp/storage/images/exp_iac_' . getmypid() . '_' . mt_rand();
        $this->dir = $base . '/12-3-eng-GB';
        mkdir( $this->dir, 0700, true );
        $this->original = $this->dir . '/andernach.jpg';
        copy( dirname( __DIR__, 4 ) . '/tests/lib/ezimage/data/andernach.jpg', $this->original );
        $size = getimagesize( $this->original );
        $this->size = array( $size[0], $size[1] );
        expImageAliasLock::setDirectory( $base . '/locks' );
    }

    protected function tearDown(): void
    {
        expImageAliasLock::setDirectory( null );
        expImageAliasSettings::setMode( null );
        expImageAliasUrl::resetKeys();
        if ( $this->dir )
        {
            $base = dirname( $this->dir );
            foreach ( array( $this->dir, $base . '/locks' ) as $dir )
            {
                foreach ( (array)glob( $dir . '/*' ) as $file )
                    @unlink( $file );
                @rmdir( $dir );
            }
            @rmdir( $base );
        }
        if ( $this->cwd !== false )
            chdir( $this->cwd );
        parent::tearDown();
    }

    private function originalEntry()
    {
        return array( 'name' => 'original', 'url' => $this->original, 'basename' => 'andernach', 'width' => $this->size[0],
                      'height' => $this->size[1], 'mime_type' => 'image/jpeg', 'alternative_text' => '', 'text' => '',
                      'original_filename' => 'andernach.jpg', 'info' => false, 'timestamp' => '1' );
    }

    private static function aliasNames()
    {
        return array_values( array_diff( array_keys( eZImageManager::factory()->aliasList() ), array( 'original' ) ) );
    }

    /**
     * Converts every alias (each with the aliases made before it, as on a page) and compares with the computation.
     *
     * @param array $focalData
     */
    private function compareAll( array $focalData )
    {
        $manager = eZImageManager::factory();
        $aliasList = array( 'original' => $this->originalEntry() );
        $compared = 0;
        foreach ( self::aliasNames() as $name )
        {
            // computed with the aliases made so far, as imageAlias() does
            $computed = expImageAliasUrl::computeTarget( $name, $this->originalEntry(), $focalData, $aliasList );
            $parameters = array( 'basename' => 'andernach' );
            if ( $focalData['focal'] !== null || $focalData['crop'] !== null )
                $parameters['focal'] = $focalData + array( 'width' => $this->size[0], 'height' => $this->size[1] );
            $made = $manager->createImageAlias( $name, $aliasList, $parameters );
            if ( !$made )
            {
                $this->assertNull( $computed, "$name: no conversion, no computation" );
                continue;
            }
            $this->assertNotNull( $computed, "$name: computed" );
            $real = $aliasList[$name];
            $this->assertSame( $real['url'], $computed['mime']['url'], "$name: file" );
            $this->assertSame( $real['mime_type'], $computed['mime']['name'], "$name: MIME type" );
            $this->assertFileExists( $real['url'] );
            $size = getimagesize( $real['url'] );
            $this->assertNotFalse( $computed['width'], "$name: the size of an image.ini alias is known" );
            $this->assertSame( array( $size[0], $size[1] ), array( $computed['width'], $computed['height'] ), "$name: size" );
            $this->assertSame( array( (int)$real['width'], (int)$real['height'] ), array( $computed['width'], $computed['height'] ), "$name: size stored" );
            ++$compared;
        }
        $this->assertGreaterThanOrEqual( count( self::$testAliases ), $compared );
    }

    /** IAC-01 */
    public function testComputedAliasesEqualConversions()
    {
        $this->compareAll( array( 'focal' => null, 'crop' => null ) );
    }

    /** IAC-02 */
    public function testComputedAliasesEqualConversionsWithFocalPointAndCrop()
    {
        $this->compareAll( array( 'focal' => array( 'x' => 0.7, 'y' => 0.3 ), 'crop' => array( 'x' => 0.1, 'y' => 0.05, 'width' => 0.8, 'height' => 0.7 ) ) );
    }

    /** IAC-02 */
    public function testComputedAliasesEqualConversionsWithFocalPointOnly()
    {
        $this->compareAll( array( 'focal' => array( 'x' => 0.15, 'y' => 0.9 ), 'crop' => null ) );
    }

    /** IAC-03 */
    public function testAnExistingReferenceIsUsed()
    {
        $original = $this->originalEntry();
        // The reference alias was made with other settings (a smaller size): the chained alias is made from it
        $existing = array( 'original' => $original,
                           'exp_iac_width' => array( 'url' => $this->dir . '/andernach_exp_iac_width.jpg', 'width' => 80, 'height' => 53 ) );
        $computed = expImageAliasUrl::computeTarget( 'exp_iac_chained', $original, array( 'focal' => null, 'crop' => null ), $existing );
        $this->assertSame( $this->dir . '/andernach_exp_iac_chained.jpg', $computed['mime']['url'] );
        $this->assertSame( array( 80, 53 ), array( $computed['width'], $computed['height'] ), 'scaled down only: the reference is smaller' );
    }

    /** IAC-04 */
    public function testThePlanConvertsNothing()
    {
        $manager = eZImageManager::factory();
        $missing = $this->dir . '/not-there.jpg';
        $plan = $manager->aliasTargetPlan( 'exp_iac_png', $missing, array( 'basename' => 'not-there', 'source_size' => array( 400, 300 ) ) );
        $this->assertSame( $this->dir . '/not-there_exp_iac_png.png', $plan['mime']['url'] );
        $this->assertSame( 'image/png', $plan['mime']['name'] );
        $this->assertFileDoesNotExist( $plan['mime']['url'] );
        $this->assertSame( array( 'andernach.jpg' ), array_map( 'basename', glob( $this->dir . '/*' ) ), 'nothing written' );
        $this->assertFalse( $manager->aliasTargetPlan( 'small', $this->dir . '/logo.svg', array( 'basename' => 'logo' ) ) );
        $this->assertFalse( $manager->aliasTargetPlan( 'no_such_alias', $this->original, array( 'basename' => 'andernach' ) ) );
        $this->assertNull( expImageAliasUrl::computeTarget( 'original', $this->originalEntry(), array() ) );
    }

    /** IAC-05 */
    public function testSizesFollowTheHandler()
    {
        $gd = new eZImageGDHandler( 'GD', true );
        $shell = new eZImageShellHandler( 'ImageMagick' );
        $down = array( 'name' => 'geometry/scaledownonly', 'data' => array( '100', '100' ) );
        // 320 x 200 -> 100 x 62.5: GD cuts off, ImageMagick rounds
        $this->assertSame( array( 100, 62 ), $gd->filterResultSize( $down, 320, 200 ) );
        $this->assertSame( array( 100, 63 ), $shell->filterResultSize( $down, 320, 200 ) );
        $this->assertSame( array( 50, 40 ), $gd->filterResultSize( $down, 50, 40 ), 'down only' );
        $this->assertSame( array( 49, 31 ), $gd->filterResultSize( array( 'name' => 'geometry/scale', 'data' => array( '88', '31' ) ), 320, 200 ) );
        $this->assertSame( array( 90, 80 ), $gd->filterResultSize( array( 'name' => 'geometry/crop', 'data' => array( 100, 80, 230, 0 ) ), 320, 200 ) );
        $this->assertSame( array( 320, 200 ), $gd->filterResultSize( array( 'name' => 'colorspace/gray', 'data' => false ), 320, 200 ) );
        $this->assertNull( $gd->filterResultSize( array( 'name' => 'resize', 'data' => array( '200x' ) ), 320, 200 ) );
        $this->assertNull( $shell->filterResultSize( array( 'name' => 'resize', 'data' => array( '200x' ) ), 320, 200 ) );
        $this->assertFalse( expImageAliasUrl::stepsSize( array( array( 'handler' => $gd, 'filters' => array( $down, array( 'name' => 'resize', 'data' => array( '2' ) ) ) ) ),
                                                         array( 320, 200 ) ) );
        $this->assertSame( array( 100, 62 ), expImageAliasUrl::stepsSize( array( array( 'handler' => $gd, 'filters' => array( $down ) ) ), array( 320, 200 ) ) );
        // A handler without filterResultSize() (an extension's): followed as ImageMagick computes
        $this->assertSame( array( 100, 63 ), expImageAliasUrl::stepsSize( array( array( 'handler' => new stdClass(), 'filters' => array( $down ) ) ), array( 320, 200 ) ) );
    }

    /** IAC-06 */
    public function testTheComputedEntryForTemplates()
    {
        expImageAliasUrl::setKeys( str_repeat( 'k', 64 ) );
        $list = array( 'original' => $this->originalEntry() );
        $attribute = array( 'id' => 12, 'version' => 3, 'language_code' => 'eng-GB', 'contentobject_id' => 7 );
        $focal = array( 'focal' => null, 'crop' => null );

        $entry = expImageAliasUrl::computedEntry( 'exp_iac_exact', $list, $focal, $attribute, array( 'form' => 'token', 'prefix' => '' ) );
        $this->assertStringStartsWith( 'content/image/', $entry['url'] );
        $this->assertStringEndsWith( '/andernach_exp_iac_exact.jpg', $entry['url'] );
        $this->assertSame( $this->dir . '/andernach_exp_iac_exact.jpg', $entry['full_path'] );
        $this->assertSame( array( 120, 90, 'image/jpeg', 'andernach_exp_iac_exact.jpg', 'jpg', true, false, false, true ),
                           array( $entry['width'], $entry['height'], $entry['mime_type'], $entry['filename'], $entry['suffix'],
                                  $entry['is_valid'], $entry['is_new'], $entry['filesize'], $entry['on_demand'] ) );
        $parts = explode( '/', substr( $entry['url'], strlen( 'content/image/' ) ) );
        $check = expImageAliasUrl::verify( $parts[0] );
        $this->assertTrue( $check['valid'] );
        $this->assertSame( array( 12, 3, 'eng-GB', 'exp_iac_exact', $this->original, 'andernach_exp_iac_exact.jpg', 120, 90 ),
                           array( $check['payload']['a'], $check['payload']['v'], $check['payload']['l'], $check['payload']['n'],
                                  $check['payload']['o'], $check['payload']['u'], $check['payload']['w'], $check['payload']['h'] ) );
        $this->assertArrayNotHasKey( 'x', $check['payload'], 'a published image: no expiry' );
        $this->assertArrayNotHasKey( 'r', $check['payload'] );

        $outdated = expImageAliasUrl::computedEntry( 'exp_iac_exact', $list, $focal, $attribute, array( 'form' => 'token', 'prefix' => '', 'outdated' => true, 'now' => 1700000000 ) );
        $parts = explode( '/', substr( $outdated['url'], strlen( 'content/image/' ) ) );
        $this->assertSame( 1700000000, expImageAliasUrl::verify( $parts[0] )['payload']['r'] );

        $storage = expImageAliasUrl::computedEntry( 'exp_iac_exact', $list, $focal, $attribute, array( 'form' => 'storage' ) );
        $this->assertSame( $this->dir . '/andernach_exp_iac_exact.jpg', $storage['url'] );

        // Without a signing key the token form falls back to the storage path
        expImageAliasUrl::setKeys( '' );
        $this->assertSame( $this->dir . '/andernach_exp_iac_exact.jpg',
                           expImageAliasUrl::computedEntry( 'exp_iac_exact', $list, $focal, $attribute, array( 'form' => 'token', 'prefix' => '' ) )['url'] );

        // A draft: the token expires
        expImageAliasUrl::setKeys( str_repeat( 'k', 64 ) );
        $draftDir = 'var/tmp/storage/images-versioned/12/3-eng-GB';
        $draftList = array( 'original' => array( 'url' => $draftDir . '/andernach.jpg' ) + $this->originalEntry() );
        $draft = expImageAliasUrl::computedEntry( 'exp_iac_exact', $draftList, $focal, $attribute, array( 'form' => 'token', 'prefix' => '', 'now' => 1700000000 ) );
        $parts = explode( '/', substr( $draft['url'], strlen( 'content/image/' ) ) );
        $this->assertSame( 1700000000 + expImageAliasSettings::draftTokenLifetime(), expImageAliasUrl::decodePayload( explode( '.', $parts[0] )[0] )['x'] );

        // No original: nothing to compute
        $this->assertNull( expImageAliasUrl::computedEntry( 'exp_iac_exact', array(), $focal, $attribute ) );
    }
}
