<?php
/**
 * The alternative text of an image uploaded with the AJAX uploader (L16): the location form asks for it, and the
 * upload is refused when the class requires it and none is given.
 *
 *  UA-01 - a class whose image attribute requires the text: the upload without a text (or only spaces) is refused
 *          with the reason, before the file is used
 *  UA-02 - with a text: allowed, the text taken trimmed
 *  UA-03 - an image attribute without the option, and a class without an image: no text needed
 *  UA-04 - location.tpl of design/admin and design/admin4: the field when the class has an image ("(required)",
 *          input-required and aria-required only when it is required), nothing without an image
 *
 * The check (checkAlternativeText(), private) is called by reflection with an upload handler and a class held in
 * memory and $_POST set by the test; the template block is rendered as written in each design by
 * eZTemplate::factory(). No database.
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit a28863d9c0), Copyright (C) Mugo Web, GPL v2
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezjscore/ezjscServerFunctionsAjaxUploaderAltTextTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

$ezjscoreRoot = dirname( __DIR__, 4 ) . '/extension/ezjscore';
require_once $ezjscoreRoot . '/classes/ezjscserverfunctions.php';
require_once $ezjscoreRoot . '/classes/ezjscserverfunctionsajaxuploader.php';
unset( $ezjscoreRoot );

/** A class attribute held in memory: its data type string and the alternative text option (data_int2) */
class ezjscAjaxUploaderAltTextTestClassAttribute extends eZContentClassAttribute
{
    private $altTextType;

    private $altTextRequired;

    public function __construct( $type, $required = false )
    {
        $this->altTextType = $type;
        $this->altTextRequired = $required;
    }

    function attribute( $attr, $noFunction = false )
    {
        if ( $attr === 'data_type_string' )
            return $this->altTextType;
        if ( $attr === eZImageType::ALT_TEXT_REQUIRED_FIELD )
            return $this->altTextRequired ? 1 : 0;
        return null;
    }
}

/** A content class held in memory */
class ezjscAjaxUploaderAltTextTestClass extends eZContentClass
{
    private $altTextDataMap;

    public function __construct( array $dataMap )
    {
        $this->altTextDataMap = $dataMap;
    }

    function dataMap()
    {
        return $this->altTextDataMap;
    }
}

/** An upload handler that gives the class of the test */
class ezjscAjaxUploaderAltTextTestHandler
{
    public $class;

    public function __construct( $class )
    {
        $this->class = $class;
    }

    public function getContentClass( $mimeData )
    {
        return $this->class;
    }
}

class ezjscServerFunctionsAjaxUploaderAltTextTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $dir;

    private $savedPost;

    private $savedGlobals = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->dir = 'var/tmp/phpunit-ajaxuploader-alttext-' . getmypid() . '-' . mt_rand();
        mkdir( $this->dir, 0700, true );
        $this->savedPost = $_POST;
        foreach ( array( 'eZTemplateCompilerSettings', 'eZSiteBasics' ) as $name )
            $this->savedGlobals[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
        $GLOBALS['eZTemplateCompilerSettings']['compile'] = false;
        $GLOBALS['eZSiteBasics']['no-cache-adviced'] = true;
    }

    protected function tearDown(): void
    {
        $_POST = $this->savedPost;
        foreach ( $this->savedGlobals as $name => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$name] );
            else
                $GLOBALS[$name] = $saved[0];
        }
        foreach ( (array)glob( $this->dir . '/*' ) as $file )
            unlink( $file );
        rmdir( $this->dir );
    }

    private static function imageClass( $required )
    {
        return new ezjscAjaxUploaderAltTextTestClass( array(
            'name' => new ezjscAjaxUploaderAltTextTestClassAttribute( 'ezstring' ),
            'image' => new ezjscAjaxUploaderAltTextTestClassAttribute( 'ezimage', $required ) ) );
    }

    /** checkAlternativeText() for $class with the posted $text (null: not posted); array( thrown message, text ) */
    private function check( $class, $text )
    {
        $_POST = $text === null ? array() : array( 'UploadAlternativeText' => $text );
        $file = $this->dir . '/upload.png';
        file_put_contents( $file, "\x89PNG\r\n\x1a\n" );
        $method = new ReflectionMethod( 'ezjscServerFunctionsAjaxUploader', 'checkAlternativeText' );
        $altText = 'untouched';
        $args = array( new ezjscAjaxUploaderAltTextTestHandler( $class ), $file, &$altText );
        try
        {
            $method->invokeArgs( null, $args );
            return array( null, $altText );
        }
        catch ( RuntimeException $e )
        {
            return array( $e->getMessage(), $altText );
        }
    }

    /** UA-01 */
    public function testRefusedWithoutTheRequiredText()
    {
        foreach ( array( null, '', '   ' ) as $text )
        {
            list( $refused, ) = $this->check( self::imageClass( true ), $text );
            $this->assertSame( 'An alternative text is required for this image.', $refused, var_export( $text, true ) );
        }
    }

    /** UA-02 */
    public function testAllowedWithTheText()
    {
        $this->assertSame( array( null, 'A red square' ), $this->check( self::imageClass( true ), '  A red square ' ) );
    }

    /** UA-03 */
    public function testNoTextNeededOtherwise()
    {
        $this->assertSame( array( null, '' ), $this->check( self::imageClass( false ), null ) );
        $fileClass = new ezjscAjaxUploaderAltTextTestClass( array( 'file' => new ezjscAjaxUploaderAltTextTestClassAttribute( 'ezbinaryfile' ) ) );
        $this->assertSame( array( null, '' ), $this->check( $fileClass, '' ) );
        $this->assertSame( array( null, '' ), $this->check( null, '' ), 'no class for the file' );
    }

    public static function designProvider()
    {
        return array( 'admin' => array( 'admin' ), 'admin4' => array( 'admin4' ) );
    }

    private function renderField( $design, $field, $required )
    {
        $source = file_get_contents( "design/$design/templates/ajaxuploader/location.tpl" );
        $start = strpos( $source, '{if $alt_text_field}' );
        $this->assertNotFalse( $start, "$design: the field is in location.tpl" );
        $end = strpos( $source, '</p>', $start );
        $end = strpos( $source, '{/if}', $end );
        $file = $this->dir . "/$design.tpl";
        file_put_contents( $file, substr( $source, $start, $end + strlen( '{/if}' ) - $start ) );
        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'alt_text_field', $field );
        $tpl->setVariable( 'alt_text_required', $required );
        try
        {
            return trim( $tpl->fetch( $file ) );
        }
        finally
        {
            $tpl->unsetVariable( 'alt_text_field' );
            $tpl->unsetVariable( 'alt_text_required' );
        }
    }

    /** UA-04 */
    #[\PHPUnit\Framework\Attributes\DataProvider('designProvider')]
    public function testTheLocationFormAsksForTheText( $design )
    {
        $required = $this->renderField( $design, true, true );
        $this->assertStringContainsString( 'name="UploadAlternativeText"', $required );
        $this->assertStringContainsString( '(required)', $required );
        $this->assertStringContainsString( 'input-required', $required );
        $this->assertStringContainsString( 'aria-required="true"', $required );

        $optional = $this->renderField( $design, true, false );
        $this->assertStringContainsString( 'name="UploadAlternativeText"', $optional );
        $this->assertStringNotContainsString( 'required', $optional );

        $this->assertSame( '', $this->renderField( $design, false, false ), 'no image, no field' );
    }
}
