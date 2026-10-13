<?php
/**
 * The AJAX uploader of the administration interface (ezjscServerFunctionsAjaxUploader::upload()) checks an image by
 * its content before the file is stored, as content edit and the upload view do (eZContentUpload::imageUploadError()
 * against image.ini [ImageUploadSettings] AllowedMIMETypes[]).
 *
 *  AU-01 - a PHP file named .jpg for a class whose file attribute is an image: refused with the reason
 *  AU-02 - a real JPEG for the same class: allowed
 *  AU-03 - a class whose file attribute is not an image (a file class): the file datatype's own checks apply, the
 *          image check does not
 *  AU-04 - a class without an attribute that takes an uploaded file: nothing to check
 *  AU-05 - the file attribute of upload.ini [<class>_ClassSettings] FileAttribute is the one checked
 *
 * The check (imageUploadError(), private) is called by reflection with a class that is held in memory and an
 * eZHTTPFile of a file in a throwaway directory; nothing is stored. No database.
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit c8e45fced7), Copyright (C) Mugo Web, GPL v2
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezjscore/ezjscServerFunctionsAjaxUploaderImageTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

$ezjscoreRoot = dirname( __DIR__, 4 ) . '/extension/ezjscore';
require_once $ezjscoreRoot . '/classes/ezjscserverfunctions.php';
require_once $ezjscoreRoot . '/classes/ezjscserverfunctionsajaxuploader.php';
unset( $ezjscoreRoot );

/** A class attribute held in memory: its identifier, data type string and data type */
class ezjscAjaxUploaderImageTestClassAttribute
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

    public function dataType()
    {
        switch ( $this->type )
        {
            case 'ezimage': return new eZImageType();
            case 'ezbinaryfile': return new eZBinaryFileType();
            default: return new eZStringType();
        }
    }
}

/** A content class held in memory */
class ezjscAjaxUploaderImageTestClass extends eZContentClass
{
    private $testIdentifier;

    private $testDataMap;

    public function __construct( $identifier, array $dataMap )
    {
        $this->testIdentifier = $identifier;
        $this->testDataMap = $dataMap;
    }

    function attribute( $attr, $noFunction = false )
    {
        return $attr === 'identifier' ? $this->testIdentifier : null;
    }

    function dataMap()
    {
        return $this->testDataMap;
    }
}

class ezjscServerFunctionsAjaxUploaderImageTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $dir;

    /** @var array|null upload.ini [k1image_ClassSettings] FileAttribute before the test */
    private $savedFileAttribute = null;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        if ( !function_exists( 'imagecreatetruecolor' ) )
            $this->markTestSkipped( 'GD is needed to make the test image.' );
        $this->dir = dirname( __DIR__, 4 ) . '/var/tmp/phpunit-ajaxuploader-image-' . getmypid() . '-' . mt_rand();
        mkdir( $this->dir, 0700, true );
        $ini = eZINI::instance( 'upload.ini' );
        if ( $ini->hasVariable( 'k1image_ClassSettings', 'FileAttribute' ) )
            $this->savedFileAttribute = array( $ini->variable( 'k1image_ClassSettings', 'FileAttribute' ) );
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance( 'upload.ini' );
        if ( $this->savedFileAttribute !== null )
            $ini->setVariable( 'k1image_ClassSettings', 'FileAttribute', $this->savedFileAttribute[0] );
        else if ( $ini->hasVariable( 'k1image_ClassSettings', 'FileAttribute' ) )
            $ini->removeSetting( 'k1image_ClassSettings', 'FileAttribute' );
        if ( $this->dir && is_dir( $this->dir ) )
        {
            foreach ( (array)glob( $this->dir . '/*' ) as $file )
                unlink( $file );
            rmdir( $this->dir );
        }
    }

    private function httpFile( $name, $contents = null )
    {
        $path = $this->dir . '/' . $name;
        if ( $contents === null )
        {
            $image = imagecreatetruecolor( 4, 3 );
            imagejpeg( $image, $path );
        }
        else
            file_put_contents( $path, $contents );
        return new eZHTTPFile( 'UploadFile', array( 'name' => $name, 'type' => 'image/jpeg', 'tmp_name' => $path, 'size' => filesize( $path ) ) );
    }

    private static function check( eZContentClass $class, eZHTTPFile $file )
    {
        $method = new ReflectionMethod( 'ezjscServerFunctionsAjaxUploader', 'imageUploadError' );
        return $method->invoke( null, $class, $file );
    }

    private static function imageClass()
    {
        return new ezjscAjaxUploaderImageTestClass( 'k1image', array(
            'name' => new ezjscAjaxUploaderImageTestClassAttribute( 'ezstring' ),
            'image' => new ezjscAjaxUploaderImageTestClassAttribute( 'ezimage' ) ) );
    }

    /** AU-01 */
    public function testADisguisedFileIsRefusedForAnImageClass()
    {
        $error = self::check( self::imageClass(), $this->httpFile( 'shell.jpg', '<?php phpinfo(); ?>' ) );
        $this->assertIsString( $error );
        $this->assertNotSame( '', $error );
    }

    /** AU-02 */
    public function testARealImageIsAllowed()
    {
        $this->assertFalse( self::check( self::imageClass(), $this->httpFile( 'photo.jpg' ) ) );
    }

    /** AU-03 */
    public function testAFileClassKeepsItsOwnChecks()
    {
        $class = new ezjscAjaxUploaderImageTestClass( 'k1file', array(
            'name' => new ezjscAjaxUploaderImageTestClassAttribute( 'ezstring' ),
            'file' => new ezjscAjaxUploaderImageTestClassAttribute( 'ezbinaryfile' ) ) );
        $this->assertFalse( self::check( $class, $this->httpFile( 'shell.jpg', '<?php phpinfo(); ?>' ) ) );
    }

    /** AU-04 */
    public function testAClassWithoutAFileAttributeHasNothingToCheck()
    {
        $class = new ezjscAjaxUploaderImageTestClass( 'k1text', array( 'name' => new ezjscAjaxUploaderImageTestClassAttribute( 'ezstring' ) ) );
        $this->assertFalse( self::check( $class, $this->httpFile( 'shell.jpg', '<?php phpinfo(); ?>' ) ) );
    }

    /** AU-05 */
    public function testTheConfiguredFileAttributeIsChecked()
    {
        $class = new ezjscAjaxUploaderImageTestClass( 'k1image', array(
            'file' => new ezjscAjaxUploaderImageTestClassAttribute( 'ezbinaryfile' ),
            'image' => new ezjscAjaxUploaderImageTestClassAttribute( 'ezimage' ) ) );
        $php = '<?php phpinfo(); ?>';
        $this->assertFalse( self::check( $class, $this->httpFile( 'a.jpg', $php ) ), 'without the setting: the first file attribute, a file' );
        eZINI::instance( 'upload.ini' )->setVariable( 'k1image_ClassSettings', 'FileAttribute', 'image' );
        $this->assertIsString( self::check( $class, $this->httpFile( 'b.jpg', $php ) ), 'FileAttribute=image: the image is checked' );
    }
}
