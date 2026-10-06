<?php
/**
 * The checkbox (ezboolean), e-mail (ezemail) and selection (ezselection) datatypes as the content and class edit
 * views and the information collector use them: required input, what is stored for posted values, class settings
 * (default of a checkbox, options of a selection), text export and import, titles, sort keys and the package
 * serialization of the class settings.
 *
 * No database: the class attribute is held in memory (see eZDatatypeTestFixtures.php).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group datatypes
 */

require_once __DIR__ . '/eZDatatypeTestFixtures.php';

class eZChoiceDatatypesTest extends eZDatatypeTestCase
{
    // ---------------------------------------------------------------- ezboolean

    public static function booleanRequiredProvider()
    {
        return array(
            'checked' => array( array( 'ContentObjectAttribute_data_boolean_4711' => '1' ), eZInputValidator::STATE_ACCEPTED ),
            'checked with "on"' => array( array( 'ContentObjectAttribute_data_boolean_4711' => 'on' ), eZInputValidator::STATE_ACCEPTED ),
            'not posted' => array( array(), eZInputValidator::STATE_INVALID ),
            'posted "0"' => array( array( 'ContentObjectAttribute_data_boolean_4711' => '0' ), eZInputValidator::STATE_INVALID ),
            'posted "false"' => array( array( 'ContentObjectAttribute_data_boolean_4711' => 'false' ), eZInputValidator::STATE_INVALID ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('booleanRequiredProvider')]
    public function testRequiredCheckboxMustBeChecked( $post, $expected )
    {
        $type = $this->dataType( 'ezboolean' );
        $attribute = $this->objectAttribute( 'ezboolean', array( 'is_required' => 1 ) );
        $http = $this->post( $post );
        $this->assertSame( $expected, $type->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( $expected, $type->validateCollectionAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute ) );
        if ( $expected === eZInputValidator::STATE_ACCEPTED )
        {
            // what was accepted is stored as checked
            $type->fetchObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute );
            $this->assertSame( 1, $attribute->attribute( 'data_int' ) );
        }
    }

    public function testOptionalCheckboxAcceptsAnything()
    {
        $type = $this->dataType( 'ezboolean' );
        $attribute = $this->objectAttribute( 'ezboolean' );
        foreach ( array( array(), array( 'ContentObjectAttribute_data_boolean_4711' => '0' ), array( 'ContentObjectAttribute_data_boolean_4711' => '1' ) ) as $post )
        {
            $http = $this->post( $post );
            $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute ) );
            $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateCollectionAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute ) );
        }
        $collector = $this->objectAttribute( 'ezboolean', array( 'is_required' => 1, 'is_information_collector' => 1 ) );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateObjectAttributeHTTPInput( $this->post( array() ), 'ContentObjectAttribute', $collector ) );
    }

    public static function booleanFetchProvider()
    {
        return array(
            'not posted' => array( array(), 0 ),
            '1' => array( array( 'ContentObjectAttribute_data_boolean_4711' => '1' ), 1 ),
            'on' => array( array( 'ContentObjectAttribute_data_boolean_4711' => 'on' ), 1 ),
            '0' => array( array( 'ContentObjectAttribute_data_boolean_4711' => '0' ), 0 ),
            'false' => array( array( 'ContentObjectAttribute_data_boolean_4711' => 'false' ), 0 ),
            'empty' => array( array( 'ContentObjectAttribute_data_boolean_4711' => '' ), 1 ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('booleanFetchProvider')]
    public function testCheckboxFetchStoresZeroOrOne( $post, $expected )
    {
        $type = $this->dataType( 'ezboolean' );
        $attribute = $this->objectAttribute( 'ezboolean' );
        $http = $this->post( $post );
        $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( $expected, $attribute->attribute( 'data_int' ) );
        $collected = new eZInformationCollectionAttribute( array( 'data_int' => null ) );
        $this->assertTrue( $type->fetchCollectionAttributeHTTPInput( null, $collected, $http, 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( $expected, $collected->attribute( 'data_int' ) );
    }

    public function testCheckboxClassDefault()
    {
        $type = $this->dataType( 'ezboolean' );
        $classAttribute = $this->classAttribute( 'ezboolean', array( 'data_int3' => 1 ) );
        // the form did not show the setting: nothing changes
        $this->assertTrue( $type->fetchClassAttributeHTTPInput( $this->post( array() ), 'ContentClass', $classAttribute ) );
        $this->assertSame( 1, $classAttribute->attribute( 'data_int3' ) );
        // shown, unchecked
        $this->assertTrue( $type->fetchClassAttributeHTTPInput( $this->post( array( 'ContentClass_ezboolean_default_value_901_exists' => 1 ) ), 'ContentClass', $classAttribute ) );
        $this->assertSame( 0, $classAttribute->attribute( 'data_int3' ) );
        // shown, checked
        $this->assertTrue( $type->fetchClassAttributeHTTPInput( $this->post( array( 'ContentClass_ezboolean_default_value_901_exists' => 1, 'ContentClass_ezboolean_default_value_901' => 'on' ) ), 'ContentClass', $classAttribute ) );
        $this->assertSame( 1, $classAttribute->attribute( 'data_int3' ) );
    }

    public function testCheckboxDefaultsAndCopies()
    {
        $type = $this->dataType( 'ezboolean' );
        $new = $this->objectAttribute( 'ezboolean', array( 'data_int3' => 1 ) );
        $type->initializeObjectAttribute( $new, false, null );
        $this->assertSame( 1, $new->attribute( 'data_int' ) );
        $original = $this->objectAttribute( 'ezboolean', array(), array( 'data_int' => 0 ) );
        $copy = $this->objectAttribute( 'ezboolean', array( 'data_int3' => 1 ) );
        $type->initializeObjectAttribute( $copy, 2, $original );
        $this->assertSame( 0, $copy->attribute( 'data_int' ) );
        $this->assertSame( array( 'data_int' => 1, 'sort_key_int' => 1 ), $type->batchInitializeObjectAttributeData( $this->classAttribute( 'ezboolean', array( 'data_int3' => 1 ) ) ) );
    }

    public function testCheckboxContentAndSerialization()
    {
        $type = $this->dataType( 'ezboolean' );
        $attribute = $this->objectAttribute( 'ezboolean', array(), array( 'data_int' => 1 ) );
        $this->assertSame( 1, $type->objectAttributeContent( $attribute ) );
        $this->assertSame( 1, $type->title( $attribute ) );
        $this->assertSame( 1, $type->metaData( $attribute ) );
        $this->assertSame( 1, $type->sortKey( $attribute ) );
        $this->assertSame( 'int', $type->sortKeyType() );
        $this->assertTrue( $type->hasObjectAttributeContent( $this->objectAttribute( 'ezboolean', array(), array( 'data_int' => 0 ) ) ) );

        list( $copy, $xml ) = $this->roundTripClassParameters( $this->classAttribute( 'ezboolean', array( 'data_int3' => 1 ) ) );
        $this->assertSame( '<datatype-parameters><default-value is-set="true"/></datatype-parameters>', $xml );
        $this->assertEquals( 1, $copy->attribute( 'data_int3' ) );
        list( $copy, $xml ) = $this->roundTripClassParameters( $this->classAttribute( 'ezboolean', array( 'data_int3' => 0 ) ) );
        $this->assertSame( '<datatype-parameters><default-value is-set="false"/></datatype-parameters>', $xml );
        $this->assertEquals( 0, $copy->attribute( 'data_int3' ) );

        $dom = new DOMDocument();
        $dom->loadXML( '<attribute><datatype-parameters/></attribute>' );
        $classAttribute = $this->classAttribute( 'ezboolean', array( 'data_int3' => 1 ) );
        $type->unserializeContentClassAttribute( $classAttribute, $dom->documentElement, $dom->documentElement->firstChild );
        $this->assertEquals( 0, $classAttribute->attribute( 'data_int3' ) );
    }

    public static function booleanFromStringProvider()
    {
        return array(
            array( '1', 1 ), array( '0', 0 ), array( '2', 1 ), array( '-1', 1 ), array( '0.0', 0 ), array( ' YES ', 1 ),
            array( 'on', 1 ), array( 'true', 1 ), array( 'no', 0 ), array( 'off', 0 ), array( '', 0 ), array( true, 1 ),
            array( false, 0 ), array( array( 1 ), 0 ), array( null, 0 ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('booleanFromStringProvider')]
    public function testCheckboxFromString( $string, $expected )
    {
        $this->assertSame( $expected, eZBooleanType::booleanFromString( $string ) );
        $attribute = $this->objectAttribute( 'ezboolean' );
        $this->assertTrue( $this->dataType( 'ezboolean' )->fromString( $attribute, $string ) );
        $this->assertSame( $expected, $attribute->attribute( 'data_int' ) );
    }

    // ---------------------------------------------------------------- ezemail

    public static function emailInputProvider()
    {
        return array(
            'address' => array( array(), 'someone@k1.example.invalid', eZInputValidator::STATE_ACCEPTED ),
            'spaces around' => array( array(), "  someone@k1.example.invalid \n", eZInputValidator::STATE_ACCEPTED ),
            'not an address' => array( array(), 'someone at example', eZInputValidator::STATE_INVALID ),
            'line break inside' => array( array(), "a@k1.example.invalid\nBcc: b@k1.example.invalid", eZInputValidator::STATE_INVALID ),
            'optional empty' => array( array(), '', eZInputValidator::STATE_ACCEPTED ),
            'required empty' => array( array( 'is_required' => 1 ), '  ', eZInputValidator::STATE_INVALID ),
            'required collector empty' => array( array( 'is_required' => 1, 'is_information_collector' => 1 ), '', eZInputValidator::STATE_ACCEPTED ),
            'collector with a bad address' => array( array( 'is_information_collector' => 1 ), 'bad', eZInputValidator::STATE_INVALID ),
            'array' => array( array(), array( 'a@k1.example.invalid' ), eZInputValidator::STATE_ACCEPTED ),
            'required array' => array( array( 'is_required' => 1 ), array( 'a@k1.example.invalid' ), eZInputValidator::STATE_INVALID ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('emailInputProvider')]
    public function testEmailObjectInput( $classFields, $input, $expected )
    {
        $attribute = $this->objectAttribute( 'ezemail', $classFields );
        $http = $this->post( array( 'ContentObjectAttribute_data_text_4711' => $input ) );
        $this->assertSame( $expected, $this->dataType( 'ezemail' )->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute ) );
    }

    public function testEmailNotPostedAndCollection()
    {
        $type = $this->dataType( 'ezemail' );
        $http = $this->post( array() );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $this->objectAttribute( 'ezemail' ) ) );
        $required = $this->objectAttribute( 'ezemail', array( 'is_required' => 1 ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $required ) );
        $this->assertSame( 'Missing email input.', $required->validationError() );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateCollectionAttributeHTTPInput( $http, 'ContentObjectAttribute', $this->objectAttribute( 'ezemail' ) ) );

        $http = $this->post( array( 'ContentObjectAttribute_data_text_4711' => '' ) );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateCollectionAttributeHTTPInput( $http, 'ContentObjectAttribute', $this->objectAttribute( 'ezemail' ) ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateCollectionAttributeHTTPInput( $http, 'ContentObjectAttribute', $required ) );
        $this->assertSame( 'The email address is empty.', $required->validationError() );
        $http = $this->post( array( 'ContentObjectAttribute_data_text_4711' => 'x@k1.example.invalid' ) );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateCollectionAttributeHTTPInput( $http, 'ContentObjectAttribute', $required ) );
        $http = $this->post( array( 'ContentObjectAttribute_data_text_4711' => "x@k1.example.invalid\r\n" . 'To: y@k1.example.invalid' ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateCollectionAttributeHTTPInput( $http, 'ContentObjectAttribute', $required ) );
    }

    public function testEmailFetchStoresTheTrimmedAddress()
    {
        $type = $this->dataType( 'ezemail' );
        $attribute = $this->objectAttribute( 'ezemail' );
        $http = $this->post( array( 'ContentObjectAttribute_data_text_4711' => " Someone@K1.example.invalid \n" ) );
        $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( 'Someone@K1.example.invalid', $attribute->attribute( 'data_text' ) );
        $this->assertSame( 'someone@k1.example.invalid', $type->sortKey( $attribute ) );
        $this->assertSame( 'Someone@K1.example.invalid', $type->title( $attribute ) );
        $this->assertSame( 'Someone@K1.example.invalid', $type->metaData( $attribute ) );
        $this->assertSame( 'Someone@K1.example.invalid', $type->objectAttributeContent( $attribute ) );
        $this->assertTrue( $type->hasObjectAttributeContent( $attribute ) );
        $collected = new eZInformationCollectionAttribute( array( 'data_text' => '' ) );
        $this->assertTrue( $type->fetchCollectionAttributeHTTPInput( null, $collected, $http, 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( 'Someone@K1.example.invalid', $collected->attribute( 'data_text' ) );
        $this->assertFalse( $type->fetchObjectAttributeHTTPInput( $this->post( array() ), 'ContentObjectAttribute', $attribute ) );
        $this->assertFalse( $type->fetchCollectionAttributeHTTPInput( null, $collected, $this->post( array() ), 'ContentObjectAttribute', $attribute ) );
        $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $this->post( array( 'ContentObjectAttribute_data_text_4711' => array( 'x' ) ) ), 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( '', $attribute->attribute( 'data_text' ) );
        $this->assertFalse( $type->hasObjectAttributeContent( $attribute ) );
    }

    public function testEmailCopyKeepsTheAddress()
    {
        $type = $this->dataType( 'ezemail' );
        $original = $this->objectAttribute( 'ezemail', array(), array( 'data_text' => 'a@k1.example.invalid' ) );
        $copy = $this->objectAttribute( 'ezemail' );
        $type->initializeObjectAttribute( $copy, 2, $original );
        $this->assertSame( 'a@k1.example.invalid', $copy->attribute( 'data_text' ) );
        $fresh = $this->objectAttribute( 'ezemail' );
        $type->initializeObjectAttribute( $fresh, false, null );
        $this->assertSame( '', $fresh->attribute( 'data_text' ) );
    }

    public static function emailFromStringProvider()
    {
        return array(
            'address' => array( 'a@k1.example.invalid', true, 'a@k1.example.invalid' ),
            'spaces around' => array( "  a@k1.example.invalid\n", true, 'a@k1.example.invalid' ),
            'empty' => array( '', true, '' ),
            'header injection' => array( "a@k1.example.invalid\r\nBcc: b@k1.example.invalid", false, 'kept@k1.example.invalid' ),
            'control character' => array( "a\x00@k1.example.invalid", false, 'kept@k1.example.invalid' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('emailFromStringProvider')]
    public function testEmailFromStringNeverStoresALineBreak( $string, $accepted, $stored )
    {
        $type = $this->dataType( 'ezemail' );
        $attribute = $this->objectAttribute( 'ezemail', array(), array( 'data_text' => 'kept@k1.example.invalid' ) );
        $result = $type->fromString( $attribute, $string );
        if ( $accepted )
            $this->assertNotFalse( $result );
        else
            $this->assertFalse( $result );
        $this->assertSame( $stored, $attribute->attribute( 'data_text' ) );
        $this->assertSame( $stored, $type->toString( $attribute ) );
    }

    // ---------------------------------------------------------------- ezselection

    private static function optionsXml( array $options )
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n" . '<ezselection><options>';
        foreach ( $options as $id => $name )
            $xml .= '<option id="' . $id . '" name="' . htmlspecialchars( $name, ENT_QUOTES ) . '"/>';
        return $xml . '</options></ezselection>' . "\n";
    }

    private function selectionClass( $multiple = 0, array $options = array( 1 => 'Red', 2 => 'Green', 3 => 'Blue' ), array $extra = array() )
    {
        return $this->classAttribute( 'ezselection', array_merge( array( 'data_int1' => $multiple, 'data_text5' => self::optionsXml( $options ) ), $extra ) );
    }

    public function testSelectionClassContentReadsTheOptions()
    {
        $content = $this->dataType( 'ezselection' )->classAttributeContent( $this->selectionClass( 1 ) );
        $this->assertSame( array( array( 'id' => '1', 'name' => 'Red' ), array( 'id' => '2', 'name' => 'Green' ), array( 'id' => '3', 'name' => 'Blue' ) ), $content['options'] );
        $this->assertSame( 1, $content['is_multiselect'] );
    }

    public function testSelectionWithBrokenOrNoXmlHasOneEmptyOption()
    {
        $type = $this->dataType( 'ezselection' );
        foreach ( array( '', 'not <xml', '<ezselection/>' ) as $xml )
        {
            $content = $type->classAttributeContent( $this->classAttribute( 'ezselection', array( 'data_text5' => $xml ) ) );
            $this->assertSame( array( array( 'id' => 0, 'name' => '' ) ), $content['options'] );
        }
    }

    public static function selectedIdsProvider()
    {
        return array(
            'single' => array( 0, array( '2' ), array( '2' ) ),
            'single keeps the first' => array( 0, array( '3', '1' ), array( '3' ) ),
            'multiple in posted order' => array( 1, array( '3', '1' ), array( '3', '1' ) ),
            'duplicates once' => array( 1, array( '2', '2', 2 ), array( '2' ) ),
            'unknown ids dropped' => array( 1, array( '9', '1-2', '2' ), array( '2' ) ),
            'nested arrays dropped' => array( 1, array( array( '1' ), '3' ), array( '3' ) ),
            'not an array' => array( 1, '1', array() ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('selectedIdsProvider')]
    public function testSelectionStoresOnlyKnownOptionIds( $multiple, $posted, $expected )
    {
        $type = $this->dataType( 'ezselection' );
        $classAttribute = $this->selectionClass( $multiple );
        $this->assertSame( $expected, $type->selectedOptionIDs( $posted, $classAttribute ) );
        $attribute = $this->objectAttribute( 'ezselection', $classAttribute );
        $http = $this->post( array( 'ContentObjectAttribute_ezselect_selected_array_4711' => $posted ) );
        $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( implode( '-', $expected ), $attribute->attribute( 'data_text' ) );
        $collected = new eZInformationCollectionAttribute( array( 'data_text' => '' ) );
        $this->assertTrue( $type->fetchCollectionAttributeHTTPInput( null, $collected, $http, 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( implode( '-', $expected ), $collected->attribute( 'data_text' ) );
    }

    public function testSelectionRequired()
    {
        $type = $this->dataType( 'ezselection' );
        $required = $this->objectAttribute( 'ezselection', $this->selectionClass( 0, array( 1 => 'A' ), array( 'is_required' => 1 ) ) );
        $optional = $this->objectAttribute( 'ezselection', $this->selectionClass( 0, array( 1 => 'A' ) ) );
        $cases = array(
            array( array(), eZInputValidator::STATE_INVALID, eZInputValidator::STATE_ACCEPTED, eZInputValidator::STATE_INVALID ),
            array( array( 'ContentObjectAttribute_ezselect_selected_array_4711' => array( '1' ) ), eZInputValidator::STATE_ACCEPTED, eZInputValidator::STATE_ACCEPTED, eZInputValidator::STATE_ACCEPTED ),
            array( array( 'ContentObjectAttribute_ezselect_selected_array_4711' => array( '7' ) ), eZInputValidator::STATE_INVALID, eZInputValidator::STATE_ACCEPTED, eZInputValidator::STATE_INVALID ),
            array( array( 'ContentObjectAttribute_ezselect_selected_array_4711' => '' ), eZInputValidator::STATE_INVALID, eZInputValidator::STATE_ACCEPTED, eZInputValidator::STATE_INVALID ),
        );
        foreach ( $cases as $i => list( $post, $requiredState, $optionalState, $collectionState ) )
        {
            $http = $this->post( $post );
            $this->assertSame( $requiredState, $type->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $required ), "case $i required" );
            $this->assertSame( $optionalState, $type->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $optional ), "case $i optional" );
            $this->assertSame( $collectionState, $type->validateCollectionAttributeHTTPInput( $http, 'ContentObjectAttribute', $required ), "case $i collection" );
        }
        $collector = $this->objectAttribute( 'ezselection', $this->selectionClass( 0, array( 1 => 'A' ), array( 'is_required' => 1, 'is_information_collector' => 1 ) ) );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateObjectAttributeHTTPInput( $this->post( array() ), 'ContentObjectAttribute', $collector ) );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateClassAttributeHTTPInput( $this->post( array() ), 'ContentClass', $this->selectionClass() ) );
    }

    public function testSelectionNamesTitleMetaDataAndText()
    {
        $type = $this->dataType( 'ezselection' );
        $classAttribute = $this->selectionClass( 1, array( 1 => 'Red', 2 => 'Green|Yellow', 3 => 'Blue' ) );
        $attribute = $this->objectAttribute( 'ezselection', $classAttribute, array( 'data_text' => '3-1' ) );
        $this->assertSame( array( '3', '1' ), $type->objectAttributeContent( $attribute ) );
        $this->assertTrue( $type->hasObjectAttributeContent( $attribute ) );
        $this->assertSame( 'Red, Blue', $type->title( $attribute ) );
        $this->assertSame( 'Blue Red', $type->metaData( $attribute ) );
        $this->assertSame( 'Blue|Red', $type->toString( $attribute ) );
        $this->assertSame( '3-1', $type->sortKey( $attribute ) );

        $attribute->setAttribute( 'data_text', '2' );
        $this->assertSame( 'Green\|Yellow', $type->toString( $attribute ) );
        $copy = $this->objectAttribute( 'ezselection', $classAttribute );
        $this->assertTrue( $type->fromString( $copy, $type->toString( $attribute ) ) );
        $this->assertSame( '2', $copy->attribute( 'data_text' ) );

        $this->assertTrue( $type->fromString( $copy, 'Blue|Red|Purple' ) );
        $this->assertSame( '3-1', $copy->attribute( 'data_text' ) );

        $none = $this->objectAttribute( 'ezselection', $classAttribute, array( 'data_text' => null ) );
        $this->assertSame( array( '' ), $type->objectAttributeContent( $none ) );
        $this->assertFalse( $type->hasObjectAttributeContent( $none ) );
        $this->assertSame( '', $type->title( $none ) );
        $this->assertSame( '', $type->toString( $none ) );
        $this->assertSame( '', $type->metaData( $none ) );
    }

    public function testSelectionCopyIsStored()
    {
        $type = $this->dataType( 'ezselection' );
        $original = $this->objectAttribute( 'ezselection', $this->selectionClass(), array( 'data_text' => '2' ) );
        $copy = $this->objectAttribute( 'ezselection', $this->selectionClass() );
        $type->initializeObjectAttribute( $copy, 5, $original );
        $this->assertSame( '2', $copy->attribute( 'data_text' ) );
        $this->assertSame( 1, $copy->storeCount );
        $fresh = $this->objectAttribute( 'ezselection', $this->selectionClass() );
        $type->initializeObjectAttribute( $fresh, false, null );
        $this->assertSame( 0, $fresh->storeCount );
    }

    public function testSelectionClassEditRenamesAddsAndRemovesOptions()
    {
        $type = $this->dataType( 'ezselection' );
        $classAttribute = $this->selectionClass( 0 );

        // rename option 2 only; option 1's name posted as an array is ignored
        $http = $this->post( array( 'ContentClass_ezselection_option_name_array_901' => array( 1 => array( 'x' ), 2 => 'Lime' ),
                                    'ContentClass_ezselection_ismultiple_value_901' => '1' ) );
        $this->assertTrue( $type->fetchClassAttributeHTTPInput( $http, 'ContentClass', $classAttribute ) );
        $content = $type->classAttributeContent( $classAttribute );
        $this->assertSame( array( 'Red', 'Lime', 'Blue' ), array_column( $content['options'], 'name' ) );
        $this->assertSame( 1, $classAttribute->attribute( 'data_int1' ) );

        // a new option gets the next id after the highest
        $http = $this->post( array( 'ContentClass_ezselection_newoption_button_901' => 1 ) );
        $type->fetchClassAttributeHTTPInput( $http, 'ContentClass', $classAttribute );
        $content = $type->classAttributeContent( $classAttribute );
        $this->assertSame( array( '1', '2', '3', '4' ), array_column( $content['options'], 'id' ) );
        $this->assertSame( 0, $classAttribute->attribute( 'data_int1' ), 'the multiple flag is set from every post that changes options' );

        // remove 1 and 4; a string instead of the array removes nothing
        $http = $this->post( array( 'ContentClass_ezselection_removeoption_button_901' => 1, 'ContentClass_ezselection_option_remove_array_901' => array( 1 => 1, 4 => 1, 2 => 0 ) ) );
        $type->fetchClassAttributeHTTPInput( $http, 'ContentClass', $classAttribute );
        $this->assertSame( array( '2', '3' ), array_column( $type->classAttributeContent( $classAttribute )['options'], 'id' ) );
        $http = $this->post( array( 'ContentClass_ezselection_removeoption_button_901' => 1, 'ContentClass_ezselection_option_remove_array_901' => '23' ) );
        $type->fetchClassAttributeHTTPInput( $http, 'ContentClass', $classAttribute );
        $this->assertSame( array( '2', '3' ), array_column( $type->classAttributeContent( $classAttribute )['options'], 'id' ) );

        // nothing posted: the XML is left as it was
        $before = $classAttribute->attribute( 'data_text5' );
        $this->assertTrue( $type->fetchClassAttributeHTTPInput( $this->post( array() ), 'ContentClass', $classAttribute ) );
        $this->assertSame( $before, $classAttribute->attribute( 'data_text5' ) );
    }

    public function testSelectionNamesWithMarkupSurviveTheClassXml()
    {
        $type = $this->dataType( 'ezselection' );
        $classAttribute = $this->selectionClass( 0, array( 1 => 'A' ) );
        $http = $this->post( array( 'ContentClass_ezselection_option_name_array_901' => array( 1 => 'Tom & "Jerry" <b>' ) ) );
        $type->fetchClassAttributeHTTPInput( $http, 'ContentClass', $classAttribute );
        $this->assertSame( 'Tom & "Jerry" <b>', $type->classAttributeContent( $classAttribute )['options'][0]['name'] );
    }

    public function testSelectionPackageSerialization()
    {
        $type = $this->dataType( 'ezselection' );
        list( $copy, $xml ) = $this->roundTripClassParameters( $this->selectionClass( 1 ) );
        $this->assertSame( '<datatype-parameters><options><option id="1" name="Red"/><option id="2" name="Green"/><option id="3" name="Blue"/></options><is-multiselect>1</is-multiselect></datatype-parameters>', $xml );
        $this->assertSame( 1, $copy->attribute( 'data_int1' ) );
        $this->assertSame( $type->classAttributeContent( $this->selectionClass( 1 ) ), $type->classAttributeContent( $copy ) );

        list( $copy, $xml ) = $this->roundTripClassParameters( $this->classAttribute( 'ezselection', array( 'data_text5' => 'broken <', 'data_int1' => 0 ) ) );
        $this->assertSame( '<datatype-parameters><options/><is-multiselect>0</is-multiselect></datatype-parameters>', $xml );
        $this->assertSame( 0, $copy->attribute( 'data_int1' ) );

        $dom = new DOMDocument();
        $dom->loadXML( '<attribute><datatype-parameters/></attribute>' );
        $classAttribute = $this->selectionClass( 1 );
        $type->unserializeContentClassAttribute( $classAttribute, $dom->documentElement, $dom->documentElement->firstChild );
        $this->assertSame( 0, $classAttribute->attribute( 'data_int1' ) );
        $this->assertSame( array( array( 'id' => 0, 'name' => '' ) ), $type->classAttributeContent( $classAttribute )['options'] );
    }
}
