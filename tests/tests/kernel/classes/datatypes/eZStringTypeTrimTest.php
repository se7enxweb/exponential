<?php
/**
 * The text line (ezstring) class attribute option "Trim the stored value" (eZStringType::TRIM_FIELD, data_int2):
 * off by default, so the text is stored as posted; when on, white space at the start and the end is removed on
 * every way the text is stored (edit form, information collection, simple string insertion, fromString()); the
 * class edit form sets and clears it only when it shows the option; packages carry it.
 *
 * No database: the class attribute is held in memory (see eZDatatypeTestFixtures.php).
 *
 * The option was first written for LoveStack by Mugo Web, with thanks:
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 6a5c7dd23e), Copyright (C) Mugo Web, GPL v2
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group datatypes
 */

require_once __DIR__ . '/eZDatatypeTestFixtures.php';

class eZStringTypeTrimTest extends eZDatatypeTestCase
{
    private function fetchPosted( array $classFields, $posted )
    {
        $attribute = $this->objectAttribute( 'ezstring', $classFields );
        $this->assertTrue( $this->dataType( 'ezstring' )->fetchObjectAttributeHTTPInput(
            $this->post( array( 'ContentObjectAttribute_ezstring_data_text_4711' => $posted ) ), 'ContentObjectAttribute', $attribute ) );
        return $attribute->attribute( 'data_text' );
    }

    public function testOffByDefaultKeepsTheTextAsPosted()
    {
        $this->assertFalse( eZStringType::isTrimEnabled( $this->classAttribute( 'ezstring' ) ) );
        $this->assertSame( "  Hello world \t", $this->fetchPosted( array(), "  Hello world \t" ) );
    }

    public function testOnTrimsTheStoredValue()
    {
        $this->assertSame( 'Hello world', $this->fetchPosted( array( 'data_int2' => 1 ), "  Hello world \t\n" ) );
        $this->assertSame( 'a  b', $this->fetchPosted( array( 'data_int2' => 1 ), ' a  b ' ), 'space inside is kept' );
        $this->assertSame( '', $this->fetchPosted( array( 'data_int2' => 1 ), '   ' ) );
    }

    public function testOtherWaysOfStoringTrimToo()
    {
        $type = $this->dataType( 'ezstring' );

        $attribute = $this->objectAttribute( 'ezstring', array( 'data_int2' => 1 ) );
        $type->fromString( $attribute, '  imported  ' );
        $this->assertSame( 'imported', $attribute->attribute( 'data_text' ) );

        $attribute = $this->objectAttribute( 'ezstring', array( 'data_int2' => 1 ) );
        $result = array();
        $type->insertSimpleString( null, 1, 'eng-GB', $attribute, ' simple ', $result );
        $this->assertSame( 'simple', $attribute->attribute( 'data_text' ) );

        $attribute = $this->objectAttribute( 'ezstring', array( 'data_int2' => 1 ) );
        $collected = new eZInformationCollectionAttribute( array() );
        $type->fetchCollectionAttributeHTTPInput( null, $collected, $this->post( array( 'ContentObjectAttribute_ezstring_data_text_4711' => ' mail me ' ) ), 'ContentObjectAttribute', $attribute );
        $this->assertSame( 'mail me', $collected->attribute( 'data_text' ) );

        // and off: as given
        $attribute = $this->objectAttribute( 'ezstring' );
        $type->fromString( $attribute, '  imported  ' );
        $this->assertSame( '  imported  ', $attribute->attribute( 'data_text' ) );
    }

    public function testClassEditFormSetsAndClearsTheOption()
    {
        $type = $this->dataType( 'ezstring' );
        $classAttribute = $this->classAttribute( 'ezstring' );
        $type->fetchClassAttributeHTTPInput( $this->post( array(
            'ContentClass_ezstring_max_string_length_901' => '20',
            'ContentClass_ezstring_trim_901_posted' => '1',
            'ContentClass_ezstring_trim_901' => '1' ) ), 'ContentClass', $classAttribute );
        $this->assertSame( 1, (int)$classAttribute->attribute( eZStringType::TRIM_FIELD ) );
        $this->assertSame( 20, (int)$classAttribute->attribute( 'data_int1' ) );

        $type->fetchClassAttributeHTTPInput( $this->post( array(
            'ContentClass_ezstring_max_string_length_901' => '20',
            'ContentClass_ezstring_trim_901_posted' => '1' ) ), 'ContentClass', $classAttribute );
        $this->assertSame( 0, (int)$classAttribute->attribute( eZStringType::TRIM_FIELD ) );
    }

    public function testClassEditFormWithoutTheOptionLeavesIt()
    {
        $classAttribute = $this->classAttribute( 'ezstring', array( 'data_int2' => 1 ) );
        $this->dataType( 'ezstring' )->fetchClassAttributeHTTPInput( $this->post( array( 'ContentClass_ezstring_max_string_length_901' => '9' ) ), 'ContentClass', $classAttribute );
        $this->assertSame( 1, (int)$classAttribute->attribute( eZStringType::TRIM_FIELD ) );
    }

    public function testPackageCarriesTheOption()
    {
        list( $copy, $xml ) = $this->roundTripClassParameters( $this->classAttribute( 'ezstring', array( 'data_int1' => 30, 'data_int2' => 1 ) ) );
        $this->assertSame( '<datatype-parameters><max-length>30</max-length><default-string/><trim>1</trim></datatype-parameters>', $xml );
        $this->assertSame( 1, (int)$copy->attribute( eZStringType::TRIM_FIELD ) );

        list( $copy, $xml ) = $this->roundTripClassParameters( $this->classAttribute( 'ezstring', array( 'data_int1' => 30 ) ) );
        $this->assertSame( '<datatype-parameters><max-length>30</max-length><default-string/></datatype-parameters>', $xml, 'off: as before' );
        $this->assertSame( 0, (int)$copy->attribute( eZStringType::TRIM_FIELD ) );
    }
}
