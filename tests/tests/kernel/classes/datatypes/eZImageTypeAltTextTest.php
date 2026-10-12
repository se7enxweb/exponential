<?php
/**
 * The image (ezimage) class attribute option "Alternative text is required" (eZImageType::ALT_TEXT_REQUIRED_FIELD,
 * data_int2): off by default; when on, the content edit form refuses an image without an alternative text and
 * accepts one with it; the class edit form sets and clears it only when it shows the option; packages carry it;
 * the upload dialogs ask eZImageType::classAltTextRequired().
 *
 * No database: the class attribute is held in memory (see eZDatatypeTestFixtures.php); the stored image is image
 * XML in data_text that points at no file.
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit a28863d9c0), Copyright (C) Mugo Web, GPL v2
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group datatypes
 */

require_once __DIR__ . '/eZDatatypeTestFixtures.php';

/**
 * A class whose data map is given, for eZImageType::classAltTextRequired().
 */
class eZImageTypeAltTextTestClass extends eZContentClass
{
    public $testDataMap = array();

    public function __construct( array $dataMap )
    {
        $this->testDataMap = $dataMap;
    }

    function dataMap()
    {
        return $this->testDataMap;
    }
}

class eZImageTypeAltTextTest extends eZDatatypeTestCase
{
    private $savedFiles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->savedFiles = $_FILES;
        $_FILES = array();
    }

    protected function tearDown(): void
    {
        $_FILES = $this->savedFiles;
        parent::tearDown();
    }

    /**
     * Image XML of a stored image (the file itself is not read by the validation).
     */
    private static function storedImageXML( $altText = '', $isValid = '1' )
    {
        return '<?xml version="1.0" encoding="utf-8"?>' . "\n" .
               '<ezimage serial_number="1" is_valid="' . $isValid . '" filename="k1.jpg" suffix="jpg" basename="k1" ' .
               'dirpath="var/storage/images/k1/4711-1-eng-GB" url="var/storage/images/k1/4711-1-eng-GB/k1.jpg" ' .
               'original_filename="k1.jpg" mime_type="image/jpeg" width="10" height="10" ' .
               'alternative_text="' . htmlspecialchars( $altText, ENT_QUOTES ) . '" alias_key="1293033771" timestamp="1700000000">' .
               '<original attribute_id="4711" attribute_version="1" attribute_language="eng-GB"/>' .
               '<information Height="10" Width="10" IsColor="1"/>' .
               '</ezimage>';
    }

    private function imageAttribute( array $classFields = array(), $altText = '', $withImage = true )
    {
        $attribute = $this->objectAttribute( 'ezimage', $classFields, array( 'data_text' => self::storedImageXML( $altText, $withImage ? '1' : '' ) ) );
        // the image handler asks whether the attribute is translatable, which reads the class attribute row
        $attribute->ContentClassAttributeCanTranslate = 0;
        return $attribute;
    }

    private function validate( $attribute, array $post )
    {
        return $this->dataType( 'ezimage' )->validateObjectAttributeHTTPInput( $this->post( $post ), 'ContentObjectAttribute', $attribute );
    }

    public function testOffByDefault()
    {
        $classAttribute = $this->classAttribute( 'ezimage' );
        $this->assertFalse( eZImageType::isAltTextRequired( $classAttribute ) );
        $this->assertSame( 'data_int2', eZImageType::ALT_TEXT_REQUIRED_FIELD );
        $attribute = $this->imageAttribute();
        $this->assertFalse( eZImageType::isAltTextRequired( $attribute ), 'asked through the object attribute' );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED,
                           $this->validate( $attribute, array( 'ContentObjectAttribute_data_imagealttext_4711' => '' ) ),
                           'an image without alternative text is accepted while the option is off' );
    }

    public function testRequiredAltTextRefusedWhenEmpty()
    {
        $attribute = $this->imageAttribute( array( 'data_int2' => 1 ) );
        $this->assertTrue( eZImageType::isAltTextRequired( $attribute ) );
        $this->assertSame( eZInputValidator::STATE_INVALID,
                           $this->validate( $attribute, array( 'ContentObjectAttribute_data_imagealttext_4711' => '' ) ) );
        $this->assertNotSame( '', (string)$attribute->validationError() );
    }

    public function testRequiredAltTextRefusedWhenOnlySpaces()
    {
        $attribute = $this->imageAttribute( array( 'data_int2' => 1 ) );
        $this->assertSame( eZInputValidator::STATE_INVALID,
                           $this->validate( $attribute, array( 'ContentObjectAttribute_data_imagealttext_4711' => " \t " ) ) );
    }

    public function testRequiredAltTextAcceptedWhenGiven()
    {
        $attribute = $this->imageAttribute( array( 'data_int2' => 1 ) );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED,
                           $this->validate( $attribute, array( 'ContentObjectAttribute_data_imagealttext_4711' => 'A red bicycle' ) ) );
    }

    public function testFormWithoutTheFieldKeepsTheStoredText()
    {
        $this->assertSame( eZInputValidator::STATE_ACCEPTED,
                           $this->validate( $this->imageAttribute( array( 'data_int2' => 1 ), 'Stored text' ), array() ) );
        $this->assertSame( eZInputValidator::STATE_INVALID,
                           $this->validate( $this->imageAttribute( array( 'data_int2' => 1 ), '' ), array() ) );
    }

    public function testNoImageNeedsNoAltText()
    {
        // an optional image field left empty: nothing to describe
        $attribute = $this->imageAttribute( array( 'data_int2' => 1 ), '', false );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED,
                           $this->validate( $attribute, array( 'ContentObjectAttribute_data_imagealttext_4711' => '' ) ) );
    }

    public function testValidateImageAltText()
    {
        $this->assertTrue( eZImageType::validateImageAltText( 'x' ) );
        $this->assertTrue( eZImageType::validateImageAltText( 0 ), 'a number is text' );
        $this->assertFalse( eZImageType::validateImageAltText( '' ) );
        $this->assertFalse( eZImageType::validateImageAltText( "  \n" ) );
        $this->assertFalse( eZImageType::validateImageAltText( "\x01\x02" ), 'control characters are removed before storing' );
        $this->assertFalse( eZImageType::validateImageAltText( null ) );
        $this->assertFalse( eZImageType::validateImageAltText( array( 'x' ) ) );
    }

    public function testClassEditFormSetsAndClearsTheOption()
    {
        $type = $this->dataType( 'ezimage' );
        $classAttribute = $this->classAttribute( 'ezimage' );

        $this->assertTrue( $type->fetchClassAttributeHTTPInput( $this->post( array(
            'ContentClass_ezimage_alt_text_required_901_posted' => '1',
            'ContentClass_ezimage_alt_text_required_901' => '1',
            'ContentClass_ezimage_max_filesize_901' => '2' ) ), 'ContentClass', $classAttribute ) );
        $this->assertSame( 1, (int)$classAttribute->attribute( 'data_int2' ) );
        $this->assertSame( 2, (int)$classAttribute->attribute( 'data_int1' ), 'the file size is still read' );

        // the checkbox not posted, the form shown: cleared
        $type->fetchClassAttributeHTTPInput( $this->post( array(
            'ContentClass_ezimage_alt_text_required_901_posted' => '1',
            'ContentClass_ezimage_max_filesize_901' => '2' ) ), 'ContentClass', $classAttribute );
        $this->assertSame( 0, (int)$classAttribute->attribute( 'data_int2' ) );
    }

    public function testClassEditFormWithoutTheOptionLeavesIt()
    {
        $classAttribute = $this->classAttribute( 'ezimage', array( 'data_int2' => 1 ) );
        $this->dataType( 'ezimage' )->fetchClassAttributeHTTPInput( $this->post( array( 'ContentClass_ezimage_max_filesize_901' => '3' ) ), 'ContentClass', $classAttribute );
        $this->assertSame( 1, (int)$classAttribute->attribute( 'data_int2' ), 'a form of another design keeps the setting' );
        $this->assertSame( 3, (int)$classAttribute->attribute( 'data_int1' ) );
    }

    public function testPackageCarriesTheOption()
    {
        list( $copy, $xml ) = $this->roundTripClassParameters( $this->classAttribute( 'ezimage', array( 'data_int1' => 5, 'data_int2' => 1 ) ) );
        $this->assertStringContainsString( '<alternative-text-required>1</alternative-text-required>', $xml );
        $this->assertSame( 1, (int)$copy->attribute( 'data_int2' ) );
        $this->assertSame( 5, (int)$copy->attribute( 'data_int1' ) );

        list( $copy, $xml ) = $this->roundTripClassParameters( $this->classAttribute( 'ezimage', array( 'data_int1' => 5 ) ) );
        $this->assertSame( '<datatype-parameters><max-size unit-size="mega">5</max-size></datatype-parameters>', $xml, 'off: the parameters as before' );
        $this->assertSame( 0, (int)$copy->attribute( 'data_int2' ) );
    }

    public function testClassAltTextRequiredForTheUploadDialogs()
    {
        $image = $this->classAttribute( 'ezimage', array( 'identifier' => 'image' ) );
        $requiredImage = $this->classAttribute( 'ezimage', array( 'identifier' => 'image', 'data_int2' => 1 ) );
        $title = $this->classAttribute( 'ezstring', array( 'identifier' => 'name', 'data_int2' => 1 ) );

        $this->assertNull( eZImageType::classAltTextRequired( new eZImageTypeAltTextTestClass( array( 'name' => $title ) ) ), 'no image attribute' );
        $this->assertFalse( eZImageType::classAltTextRequired( new eZImageTypeAltTextTestClass( array( 'name' => $title, 'image' => $image ) ) ) );
        $this->assertTrue( eZImageType::classAltTextRequired( new eZImageTypeAltTextTestClass( array( 'name' => $title, 'image' => $requiredImage ) ) ) );
        $this->assertNull( eZImageType::classAltTextRequired( new eZImageTypeAltTextTestClass( array( 'image' => $requiredImage ) ), 'other' ), 'only the named attribute' );
        $this->assertNull( eZImageType::classAltTextRequired( null ) );
    }
}
