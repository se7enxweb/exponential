<?php
/**
 * The image step of the style package wizard (eZStylePackageCreator::validateImageFiles()) checks the uploaded image
 * by its content (L06), under the name the form posts it as.
 *
 *  SP-01 - without an upload the step goes on, nothing is reported
 *  SP-02 - a PHP file named .png: refused, with the error on the form field PackageImageFile; the step reads the
 *          upload under that name (it used to look it up under the name translated into the site's language,
 *          which is another name than the posted one wherever the translation differs)
 *  SP-03 - an SVG file is refused the same way
 *  SP-04 - a real PNG passes the check of the step
 *
 * $_FILES is set by the test and put back; the files are in a throwaway directory under var/tmp. A file that passes
 * is not stored by the test (storing writes into the site's storage). No database.
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 04b3b6985a), Copyright (C) Mugo Web, GPL v2
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/packages/eZStylePackageCreatorTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class eZStylePackageCreatorTest extends PHPUnit\Framework\TestCase
{
    private $dir;

    private $files;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->dir = 'var/tmp/phpunit-stylepackage-' . getmypid() . '-' . mt_rand();
        mkdir( $this->dir, 0775, true );
        $this->files = $_FILES;
        unset( $GLOBALS['eZHTTPFile-PackageImageFile'] );
    }

    protected function tearDown(): void
    {
        $_FILES = $this->files;
        unset( $GLOBALS['eZHTTPFile-PackageImageFile'] );
        if ( $this->dir && is_dir( $this->dir ) )
            eZDir::recursiveDelete( $this->dir );
    }

    private function upload( $name, $contents )
    {
        $path = $this->dir . '/upload-' . mt_rand();
        file_put_contents( $path, $contents );
        $_FILES = array( 'PackageImageFile' => array( 'name' => $name, 'type' => 'image/png', 'tmp_name' => $path,
                                                      'error' => 0, 'size' => strlen( $contents ) ) );
    }

    private static function validate( &$errors )
    {
        $creator = new eZStylePackageCreator( 'ezstyle' );
        $stepMap = array();
        $data = array();
        $errors = array();
        return $creator->validateImageFiles( null, eZHTTPTool::instance(), 'imagefiles', $stepMap, $data, $errors );
    }

    /** SP-01 */
    public function testWithoutAnUploadTheStepGoesOn()
    {
        $_FILES = array();
        $this->assertTrue( self::validate( $errors ) );
        $this->assertSame( array(), $errors );
    }

    /** SP-02 */
    public function testADisguisedFileIsRefusedOnItsField()
    {
        $this->upload( 'logo.png', '<?php phpinfo(); ?>' );
        $this->assertFalse( self::validate( $errors ) );
        $this->assertCount( 1, $errors );
        $this->assertSame( 'PackageImageFile', $errors[0]['field'] );
        $this->assertNotSame( '', (string)$errors[0]['description'] );
    }

    /** SP-03 */
    public function testAnSvgIsRefused()
    {
        $this->upload( 'logo.svg', '<svg xmlns="http://www.w3.org/2000/svg" width="1" height="1"/>' );
        $this->assertFalse( self::validate( $errors ) );
        $this->assertSame( 'PackageImageFile', $errors[0]['field'] );
    }

    /** SP-04 */
    public function testARealPngPassesTheCheck()
    {
        if ( !function_exists( 'imagecreatetruecolor' ) )
            $this->markTestSkipped( 'GD is needed to make the test image.' );
        $image = imagecreatetruecolor( 4, 3 );
        ob_start();
        imagepng( $image );
        $this->upload( 'logo.png', ob_get_clean() );
        $errors = array();
        $this->assertTrue( eZStylePackageCreator::httpFileIsImage( 'PackageImageFile', $errors ) );
        $this->assertSame( array(), $errors );
    }
}
