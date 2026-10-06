<?php
/**
 * The integer and float datatypes as the content and class edit views use them: validating and fetching the posted
 * value of an object attribute (with and without a minimum and maximum), the class attribute's own settings
 * (validate, fixup, fetch), defaults for new objects, content, meta data, sort keys and the package serialization
 * of the class settings.
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

class eZNumericDatatypesTest extends eZDatatypeTestCase
{
    // ---------------------------------------------------------------- integer, object attribute

    private function integerAttribute( $state = eZIntegerType::NO_MIN_MAX_VALUE, $min = 0, $max = 0, array $extra = array() )
    {
        return $this->objectAttribute( 'ezinteger', array_merge( array( 'data_int1' => $min, 'data_int2' => $max, 'data_int4' => $state ), $extra ) );
    }

    public static function integerInputProvider()
    {
        $none = eZIntegerType::NO_MIN_MAX_VALUE;
        $min = eZIntegerType::HAS_MIN_VALUE;
        $max = eZIntegerType::HAS_MAX_VALUE;
        $both = eZIntegerType::HAS_MIN_MAX_VALUE;
        return array(
            'plain' => array( $none, 0, 0, '42', eZInputValidator::STATE_ACCEPTED ),
            'negative' => array( $none, 0, 0, '-42', eZInputValidator::STATE_ACCEPTED ),
            'spaces are taken out' => array( $none, 0, 0, '1 000', eZInputValidator::STATE_ACCEPTED ),
            'leading zeros' => array( $none, 0, 0, '007', eZInputValidator::STATE_ACCEPTED ),
            'letters' => array( $none, 0, 0, '4a', eZInputValidator::STATE_INVALID ),
            'decimal' => array( $none, 0, 0, '4.5', eZInputValidator::STATE_INVALID ),
            'column maximum' => array( $none, 0, 0, '2147483647', eZInputValidator::STATE_ACCEPTED ),
            'above the column' => array( $none, 0, 0, '2147483648', eZInputValidator::STATE_INVALID ),
            'below the column' => array( $none, 0, 0, '-2147483649', eZInputValidator::STATE_INVALID ),
            'min ok' => array( $min, 10, 0, '10', eZInputValidator::STATE_ACCEPTED ),
            'below min' => array( $min, 10, 0, '9', eZInputValidator::STATE_INVALID ),
            'max ok' => array( $max, 0, 10, '10', eZInputValidator::STATE_ACCEPTED ),
            'above max' => array( $max, 0, 10, '11', eZInputValidator::STATE_INVALID ),
            'in range' => array( $both, 5, 10, '7', eZInputValidator::STATE_ACCEPTED ),
            'below range' => array( $both, 5, 10, '4', eZInputValidator::STATE_INVALID ),
            'above range' => array( $both, 5, 10, '11', eZInputValidator::STATE_INVALID ),
            'range with letters' => array( $both, 5, 10, 'x', eZInputValidator::STATE_INVALID ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('integerInputProvider')]
    public function testIntegerObjectInputIsValidatedAgainstTheClassRange( $state, $min, $max, $input, $expected )
    {
        $attribute = $this->integerAttribute( $state, $min, $max );
        $http = $this->post( array( 'ContentObjectAttribute_data_integer_4711' => $input ) );
        $result = $this->dataType( 'ezinteger' )->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute );
        $this->assertSame( $expected, $result );
        if ( $expected === eZInputValidator::STATE_INVALID )
            $this->assertNotEmpty( $attribute->validationError() );
        else
            $this->assertEmpty( $attribute->validationError() );
    }

    public function testIntegerRangeErrorNamesTheRange()
    {
        $attribute = $this->integerAttribute( eZIntegerType::HAS_MIN_MAX_VALUE, 5, 10 );
        $http = $this->post( array( 'ContentObjectAttribute_data_integer_4711' => '12' ) );
        $this->dataType( 'ezinteger' )->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute );
        $this->assertStringContainsString( '5 - 10', $attribute->validationError() );
    }

    public function testIntegerRangeOfOneAttributeDoesNotLeakIntoTheNext()
    {
        $type = $this->dataType( 'ezinteger' );
        $limited = $this->integerAttribute( eZIntegerType::HAS_MAX_VALUE, 0, 5 );
        $http = $this->post( array( 'ContentObjectAttribute_data_integer_4711' => '100' ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $limited ) );
        $free = $this->integerAttribute();
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $free ) );
    }

    public function testIntegerArrayInputIsRefusedNotAnError()
    {
        $attribute = $this->integerAttribute();
        $http = $this->post( array( 'ContentObjectAttribute_data_integer_4711' => array( '1' ) ) );
        $type = $this->dataType( 'ezinteger' );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute ) );
        $this->assertFalse( $type->fetchObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute ) );
        $this->assertNull( $attribute->attribute( 'data_int' ) );
    }

    public function testIntegerEmptyInputDependsOnRequired()
    {
        $type = $this->dataType( 'ezinteger' );
        $http = $this->post( array( 'ContentObjectAttribute_data_integer_4711' => '' ) );
        $optional = $this->integerAttribute();
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $optional ) );
        $required = $this->integerAttribute( eZIntegerType::NO_MIN_MAX_VALUE, 0, 0, array( 'is_required' => 1 ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $required ) );
        $this->assertSame( 'Input required.', $required->validationError() );

        $required->setValidationParameters( array( 'skip-isRequired' => true ) );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $required ) );
    }

    public function testIntegerMissingInputDependsOnRequiredAndCollector()
    {
        $type = $this->dataType( 'ezinteger' );
        $http = $this->post( array() );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $this->integerAttribute() ) );
        $required = $this->integerAttribute( eZIntegerType::NO_MIN_MAX_VALUE, 0, 0, array( 'is_required' => 1 ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $required ) );
        $collector = $this->integerAttribute( eZIntegerType::NO_MIN_MAX_VALUE, 0, 0, array( 'is_required' => 1, 'is_information_collector' => 1 ) );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $collector ) );
    }

    public function testIntegerFetchStoresTheNumberOrNoValue()
    {
        $type = $this->dataType( 'ezinteger' );
        $attribute = $this->integerAttribute();
        $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $this->post( array( 'ContentObjectAttribute_data_integer_4711' => ' 1 2 ' ) ), 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( '12', $attribute->attribute( 'data_int' ) );
        $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $this->post( array( 'ContentObjectAttribute_data_integer_4711' => '' ) ), 'ContentObjectAttribute', $attribute ) );
        $this->assertNull( $attribute->attribute( 'data_int' ) );
        $this->assertFalse( $type->hasObjectAttributeContent( $attribute ) );
        $this->assertFalse( $type->fetchObjectAttributeHTTPInput( $this->post( array() ), 'ContentObjectAttribute', $attribute ) );
    }

    public function testIntegerCollectionInput()
    {
        $type = $this->dataType( 'ezinteger' );
        $attribute = $this->integerAttribute( eZIntegerType::HAS_MIN_VALUE, 3 );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateCollectionAttributeHTTPInput( $this->post( array() ), 'ContentObjectAttribute', $attribute ) );
        $http = $this->post( array( 'ContentObjectAttribute_data_integer_4711' => '2' ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateCollectionAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute ) );
        $http = $this->post( array( 'ContentObjectAttribute_data_integer_4711' => '4' ) );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateCollectionAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute ) );
        $http = $this->post( array( 'ContentObjectAttribute_data_integer_4711' => array() ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateCollectionAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute ) );

        $required = $this->integerAttribute( eZIntegerType::NO_MIN_MAX_VALUE, 0, 0, array( 'is_required' => 1 ) );
        $http = $this->post( array( 'ContentObjectAttribute_data_integer_4711' => '' ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateCollectionAttributeHTTPInput( $http, 'ContentObjectAttribute', $required ) );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateCollectionAttributeHTTPInput( $http, 'ContentObjectAttribute', $this->integerAttribute() ) );

        $collected = new eZInformationCollectionAttribute( array( 'data_int' => null ) );
        $http = $this->post( array( 'ContentObjectAttribute_data_integer_4711' => ' 5 ' ) );
        $this->assertTrue( $type->fetchCollectionAttributeHTTPInput( null, $collected, $http, 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( '5', $collected->attribute( 'data_int' ) );
        $http = $this->post( array( 'ContentObjectAttribute_data_integer_4711' => array( 5 ) ) );
        $this->assertFalse( $type->fetchCollectionAttributeHTTPInput( null, $collected, $http, 'ContentObjectAttribute', $attribute ) );
        $this->assertFalse( $type->fetchCollectionAttributeHTTPInput( null, $collected, $this->post( array() ), 'ContentObjectAttribute', $attribute ) );
    }

    public function testIntegerContentMetaDataTitleAndSortKey()
    {
        $type = $this->dataType( 'ezinteger' );
        $attribute = $this->integerAttribute( eZIntegerType::NO_MIN_MAX_VALUE, 0, 0, array() );
        $attribute->setAttribute( 'data_int', '17' );
        $this->assertSame( '17', $type->objectAttributeContent( $attribute ) );
        $this->assertSame( 17, $type->metaData( $attribute ) );
        $this->assertSame( '17', $type->title( $attribute ) );
        $this->assertSame( '17', $type->sortKey( $attribute ) );
        $this->assertSame( 'int', $type->sortKeyType() );
        $this->assertTrue( $type->hasObjectAttributeContent( $attribute ) );
        $this->assertTrue( $type->isIndexable() );
        $this->assertTrue( $type->isInformationCollector() );
        $this->assertTrue( $type->supportsBatchInitializeObjectAttribute() );
    }

    public function testIntegerDefaultForNewAndCopiedObjects()
    {
        $type = $this->dataType( 'ezinteger' );
        $new = $this->integerAttribute( eZIntegerType::NO_MIN_MAX_VALUE, 0, 0, array( 'data_int3' => 9 ) );
        $type->initializeObjectAttribute( $new, false, null );
        $this->assertSame( 9, $new->attribute( 'data_int' ) );

        $original = $this->integerAttribute();
        $original->setAttribute( 'data_int', 33 );
        $copy = $this->integerAttribute( eZIntegerType::NO_MIN_MAX_VALUE, 0, 0, array( 'data_int3' => 9 ) );
        $type->initializeObjectAttribute( $copy, 2, $original );
        $this->assertSame( 33, $copy->attribute( 'data_int' ) );

        $this->assertSame( array(), $type->batchInitializeObjectAttributeData( $this->classAttribute( 'ezinteger', array( 'data_int3' => 0 ) ) ) );
        $this->assertSame( array( 'data_int' => 4, 'sort_key_int' => 4 ),
                           $type->batchInitializeObjectAttributeData( $this->classAttribute( 'ezinteger', array( 'data_int3' => 4 ) ) ) );
    }

    // ---------------------------------------------------------------- integer, class attribute

    private function postIntegerClassSettings( $min, $max, $default )
    {
        return $this->post( array( 'ContentClass_ezinteger_min_integer_value_901' => $min,
                                   'ContentClass_ezinteger_max_integer_value_901' => $max,
                                   'ContentClass_ezinteger_default_value_901' => $default ) );
    }

    public static function integerClassSettingsProvider()
    {
        return array(
            'nothing' => array( '', '', '', eZInputValidator::STATE_ACCEPTED, eZIntegerType::NO_MIN_MAX_VALUE ),
            'min only' => array( '3', '', '', eZInputValidator::STATE_ACCEPTED, eZIntegerType::HAS_MIN_VALUE ),
            'max only' => array( '', '8', '', eZInputValidator::STATE_ACCEPTED, eZIntegerType::HAS_MAX_VALUE ),
            'both' => array( '3', '8', '5', eZInputValidator::STATE_ACCEPTED, eZIntegerType::HAS_MIN_MAX_VALUE ),
            'min above max' => array( '9', '8', '', eZInputValidator::STATE_INTERMEDIATE, eZIntegerType::HAS_MIN_MAX_VALUE ),
            'default not a number' => array( '', '', 'abc', eZInputValidator::STATE_INVALID, eZIntegerType::NO_MIN_MAX_VALUE ),
            'default above the column' => array( '', '', '99999999999', eZInputValidator::STATE_INVALID, eZIntegerType::NO_MIN_MAX_VALUE ),
            'min above the column' => array( '99999999999', '', '', eZInputValidator::STATE_INVALID, eZIntegerType::HAS_MIN_VALUE ),
            'max with letters' => array( '', '8x', '', eZInputValidator::STATE_INTERMEDIATE, eZIntegerType::HAS_MAX_VALUE ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('integerClassSettingsProvider')]
    public function testIntegerClassSettingsAreValidatedAndFetched( $min, $max, $default, $expectedState, $expectedInputState )
    {
        $type = $this->dataType( 'ezinteger' );
        $classAttribute = $this->classAttribute( 'ezinteger' );
        $http = $this->postIntegerClassSettings( $min, $max, $default );
        $this->assertSame( $expectedState, $type->validateClassAttributeHTTPInput( $http, 'ContentClass', $classAttribute ) );
        $this->assertTrue( $type->fetchClassAttributeHTTPInput( $http, 'ContentClass', $classAttribute ) );
        $this->assertSame( $expectedInputState, $classAttribute->attribute( eZIntegerType::INPUT_STATE_FIELD ) );
        $this->assertSame( $min, $classAttribute->attribute( eZIntegerType::MIN_VALUE_FIELD ) );
        $this->assertSame( $max, $classAttribute->attribute( eZIntegerType::MAX_VALUE_FIELD ) );
    }

    public function testIntegerClassSettingsNeedAllThreeAndStrings()
    {
        $type = $this->dataType( 'ezinteger' );
        $classAttribute = $this->classAttribute( 'ezinteger' );
        $http = $this->post( array( 'ContentClass_ezinteger_min_integer_value_901' => '1' ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateClassAttributeHTTPInput( $http, 'ContentClass', $classAttribute ) );
        $this->assertFalse( $type->fetchClassAttributeHTTPInput( $http, 'ContentClass', $classAttribute ) );
        $http = $this->postIntegerClassSettings( array( '1' ), '', '' );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateClassAttributeHTTPInput( $http, 'ContentClass', $classAttribute ) );
        $this->assertFalse( $type->fetchClassAttributeHTTPInput( $http, 'ContentClass', $classAttribute ) );
    }

    public function testIntegerClassFixupRaisesTheMaximumToTheMinimum()
    {
        $type = $this->dataType( 'ezinteger' );
        $http = $this->postIntegerClassSettings( '9', '4', '' );
        $type->fixupClassAttributeHTTPInput( $http, 'ContentClass', $this->classAttribute( 'ezinteger' ) );
        $this->assertSame( 9, (int)$_POST['ContentClass_ezinteger_max_integer_value_901'] );
        $this->assertSame( 9, (int)$_POST['ContentClass_ezinteger_min_integer_value_901'] );

        $http = $this->postIntegerClassSettings( array(), '4', '' );
        $type->fixupClassAttributeHTTPInput( $http, 'ContentClass', $this->classAttribute( 'ezinteger' ) );
        $this->assertSame( '4', $_POST['ContentClass_ezinteger_max_integer_value_901'] );
    }

    public static function integerSerializationProvider()
    {
        return array(
            'none' => array( eZIntegerType::NO_MIN_MAX_VALUE, '', '', '<datatype-parameters><default-value>5</default-value></datatype-parameters>' ),
            'min' => array( eZIntegerType::HAS_MIN_VALUE, '1', '', '<datatype-parameters><default-value>5</default-value><min-value>1</min-value></datatype-parameters>' ),
            'max' => array( eZIntegerType::HAS_MAX_VALUE, '', '9', '<datatype-parameters><default-value>5</default-value><max-value>9</max-value></datatype-parameters>' ),
            'both' => array( eZIntegerType::HAS_MIN_MAX_VALUE, '1', '9', '<datatype-parameters><default-value>5</default-value><min-value>1</min-value><max-value>9</max-value></datatype-parameters>' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('integerSerializationProvider')]
    public function testIntegerClassSettingsSurviveThePackageSerialization( $state, $min, $max, $expectedXml )
    {
        $classAttribute = $this->classAttribute( 'ezinteger', array( 'data_int1' => $min, 'data_int2' => $max, 'data_int3' => '5', 'data_int4' => $state ) );
        list( $copy, $xml ) = $this->roundTripClassParameters( $classAttribute );
        $this->assertSame( $expectedXml, $xml );
        $this->assertSame( $state, $copy->attribute( eZIntegerType::INPUT_STATE_FIELD ) );
        $this->assertSame( '5', $copy->attribute( eZIntegerType::DEFAULT_VALUE_FIELD ) );
        $this->assertSame( $min, $copy->attribute( eZIntegerType::MIN_VALUE_FIELD ) );
        $this->assertSame( $max, $copy->attribute( eZIntegerType::MAX_VALUE_FIELD ) );
    }

    // ---------------------------------------------------------------- float

    private function floatAttribute( $state = eZFloatType::NO_MIN_MAX_VALUE, $min = 0, $max = 0, array $extra = array() )
    {
        return $this->objectAttribute( 'ezfloat', array_merge( array( 'data_float1' => $min, 'data_float2' => $max, 'data_float4' => $state ), $extra ) );
    }

    public static function floatInputProvider()
    {
        $none = eZFloatType::NO_MIN_MAX_VALUE;
        $min = eZFloatType::HAS_MIN_VALUE;
        $max = eZFloatType::HAS_MAX_VALUE;
        $both = eZFloatType::HAS_MIN_MAX_VALUE;
        return array(
            'integer' => array( $none, 0, 0, '3', eZInputValidator::STATE_ACCEPTED ),
            'decimal' => array( $none, 0, 0, '3.25', eZInputValidator::STATE_ACCEPTED ),
            'negative' => array( $none, 0, 0, '-0.5', eZInputValidator::STATE_ACCEPTED ),
            'thousands separator of the locale' => array( $none, 0, 0, '1,000.5', eZInputValidator::STATE_ACCEPTED ),
            'letters' => array( $none, 0, 0, 'abc', eZInputValidator::STATE_INVALID ),
            'too big for a double' => array( $none, 0, 0, '1' . str_repeat( '0', 400 ), eZInputValidator::STATE_INVALID ),
            'min ok' => array( $min, 1.5, 0, '1.5', eZInputValidator::STATE_ACCEPTED ),
            'below min' => array( $min, 1.5, 0, '1.4', eZInputValidator::STATE_INVALID ),
            'max ok' => array( $max, 0, 2.5, '2.5', eZInputValidator::STATE_ACCEPTED ),
            'above max' => array( $max, 0, 2.5, '2.6', eZInputValidator::STATE_INVALID ),
            'in range' => array( $both, 1, 2, '1.5', eZInputValidator::STATE_ACCEPTED ),
            'out of range' => array( $both, 1, 2, '2.5', eZInputValidator::STATE_INVALID ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('floatInputProvider')]
    public function testFloatObjectInputIsValidatedAgainstTheClassRange( $state, $min, $max, $input, $expected )
    {
        $attribute = $this->floatAttribute( $state, $min, $max );
        $http = $this->post( array( 'ContentObjectAttribute_data_float_4711' => $input ) );
        $this->assertSame( $expected, $this->dataType( 'ezfloat' )->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute ) );
        if ( $expected === eZInputValidator::STATE_INVALID )
            $this->assertNotEmpty( $attribute->validationError() );
    }

    public function testFloatRangeOfOneAttributeDoesNotLeakIntoTheNext()
    {
        $type = $this->dataType( 'ezfloat' );
        $http = $this->post( array( 'ContentObjectAttribute_data_float_4711' => '50' ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $this->floatAttribute( eZFloatType::HAS_MAX_VALUE, 0, 1 ) ) );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $this->floatAttribute() ) );
    }

    public function testFloatMissingEmptyAndArrayInput()
    {
        $type = $this->dataType( 'ezfloat' );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateObjectAttributeHTTPInput( $this->post( array() ), 'ContentObjectAttribute', $this->floatAttribute() ) );
        $http = $this->post( array( 'ContentObjectAttribute_data_float_4711' => '' ) );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $this->floatAttribute() ) );
        $required = $this->floatAttribute( eZFloatType::NO_MIN_MAX_VALUE, 0, 0, array( 'is_required' => 1 ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $required ) );
        $http = $this->post( array( 'ContentObjectAttribute_data_float_4711' => array( '1' ) ) );
        $attribute = $this->floatAttribute();
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute ) );
        $this->assertFalse( $type->fetchObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute ) );
    }

    public function testFloatFetchStoresTheInternalNumberOrNoValue()
    {
        $type = $this->dataType( 'ezfloat' );
        $attribute = $this->floatAttribute();
        $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $this->post( array( 'ContentObjectAttribute_data_float_4711' => '1,234.5' ) ), 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( '1234.5', $attribute->attribute( 'data_float' ) );
        $this->assertSame( '1,234.5', $attribute->HTTPValue );
        $this->assertSame( 1234.5, $type->metaData( $attribute ) );
        $this->assertTrue( $type->hasObjectAttributeContent( $attribute ) );
        $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $this->post( array( 'ContentObjectAttribute_data_float_4711' => '' ) ), 'ContentObjectAttribute', $attribute ) );
        $this->assertNull( $attribute->attribute( 'data_float' ) );
        $this->assertFalse( $type->hasObjectAttributeContent( $attribute ) );
        $this->assertFalse( $type->fetchObjectAttributeHTTPInput( $this->post( array() ), 'ContentObjectAttribute', $attribute ) );
    }

    private function postFloatClassSettings( $min, $max, $default )
    {
        return $this->post( array( 'ContentClass_ezfloat_min_float_value_901' => $min,
                                   'ContentClass_ezfloat_max_float_value_901' => $max,
                                   'ContentClass_ezfloat_default_value_901' => $default ) );
    }

    public static function floatClassSettingsProvider()
    {
        return array(
            'nothing' => array( '', '', '', eZInputValidator::STATE_ACCEPTED, eZFloatType::NO_MIN_MAX_VALUE ),
            'min only' => array( '0.5', '', '', eZInputValidator::STATE_ACCEPTED, eZFloatType::HAS_MIN_VALUE ),
            'max only' => array( '', '2.5', '1', eZInputValidator::STATE_ACCEPTED, eZFloatType::HAS_MAX_VALUE ),
            'both' => array( '0.5', '2.5', '1', eZInputValidator::STATE_ACCEPTED, eZFloatType::HAS_MIN_MAX_VALUE ),
            'min above max' => array( '3', '2.5', '', eZInputValidator::STATE_INTERMEDIATE, eZFloatType::HAS_MIN_MAX_VALUE ),
            'default not a number' => array( '', '', 'x', eZInputValidator::STATE_INVALID, eZFloatType::NO_MIN_MAX_VALUE ),
            'infinite max' => array( '', '1' . str_repeat( '0', 400 ), '', eZInputValidator::STATE_INVALID, eZFloatType::HAS_MAX_VALUE ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('floatClassSettingsProvider')]
    public function testFloatClassSettingsAreValidatedAndFetched( $min, $max, $default, $expectedState, $expectedInputState )
    {
        $type = $this->dataType( 'ezfloat' );
        $classAttribute = $this->classAttribute( 'ezfloat' );
        $http = $this->postFloatClassSettings( $min, $max, $default );
        $this->assertSame( $expectedState, $type->validateClassAttributeHTTPInput( $http, 'ContentClass', $classAttribute ) );
        $this->assertTrue( $type->fetchClassAttributeHTTPInput( $http, 'ContentClass', $classAttribute ) );
        $this->assertSame( $expectedInputState, $classAttribute->attribute( eZFloatType::INPUT_STATE_FIELD ) );
    }

    public function testFloatClassSettingsNeedAllThreeAndStrings()
    {
        $type = $this->dataType( 'ezfloat' );
        $classAttribute = $this->classAttribute( 'ezfloat' );
        $http = $this->post( array( 'ContentClass_ezfloat_min_float_value_901' => '1' ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateClassAttributeHTTPInput( $http, 'ContentClass', $classAttribute ) );
        $this->assertFalse( $type->fetchClassAttributeHTTPInput( $http, 'ContentClass', $classAttribute ) );
        $http = $this->postFloatClassSettings( '', array( 'x' ), '' );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateClassAttributeHTTPInput( $http, 'ContentClass', $classAttribute ) );
        $this->assertFalse( $type->fetchClassAttributeHTTPInput( $http, 'ContentClass', $classAttribute ) );
    }

    public function testFloatClassFixupRaisesTheMaximumToTheMinimum()
    {
        $type = $this->dataType( 'ezfloat' );
        $http = $this->postFloatClassSettings( '2.5', '1.5', '' );
        $type->fixupClassAttributeHTTPInput( $http, 'ContentClass', $this->classAttribute( 'ezfloat' ) );
        $this->assertEquals( 2.5, (float)$_POST['ContentClass_ezfloat_max_float_value_901'] );
        $http = $this->postFloatClassSettings( '1', '3', '' );
        $type->fixupClassAttributeHTTPInput( $http, 'ContentClass', $this->classAttribute( 'ezfloat' ) );
        $this->assertSame( '3', $_POST['ContentClass_ezfloat_max_float_value_901'] );
    }

    public function testFloatClassSettingsSurviveThePackageSerialization()
    {
        foreach ( array( eZFloatType::NO_MIN_MAX_VALUE => array( '', '' ), eZFloatType::HAS_MIN_VALUE => array( '0.5', '' ),
                         eZFloatType::HAS_MAX_VALUE => array( '', '7.5' ), eZFloatType::HAS_MIN_MAX_VALUE => array( '0.5', '7.5' ) ) as $state => $range )
        {
            $classAttribute = $this->classAttribute( 'ezfloat', array( 'data_float1' => $range[0], 'data_float2' => $range[1], 'data_float3' => '1.25', 'data_float4' => $state ) );
            list( $copy, $xml ) = $this->roundTripClassParameters( $classAttribute );
            $this->assertStringContainsString( '<default-value>1.25</default-value>', $xml );
            $this->assertSame( $state, $copy->attribute( eZFloatType::INPUT_STATE_FIELD ), "state $state" );
            $this->assertSame( $range[0], $copy->attribute( eZFloatType::MIN_FIELD ) );
            $this->assertSame( $range[1], $copy->attribute( eZFloatType::MAX_FIELD ) );
        }
    }

    public function testFloatDefaultsForNewAndCopiedObjects()
    {
        $type = $this->dataType( 'ezfloat' );
        $new = $this->floatAttribute( eZFloatType::NO_MIN_MAX_VALUE, 0, 0, array( 'data_float3' => 2.5 ) );
        $type->initializeObjectAttribute( $new, false, null );
        $this->assertSame( 2.5, $new->attribute( 'data_float' ) );
        $original = $this->floatAttribute();
        $original->setAttribute( 'data_float', 0.75 );
        $copy = $this->floatAttribute();
        $type->initializeObjectAttribute( $copy, 3, $original );
        $this->assertSame( 0.75, $copy->attribute( 'data_float' ) );
        $this->assertSame( array( 'data_float' => 2.5 ), $type->batchInitializeObjectAttributeData( $this->classAttribute( 'ezfloat', array( 'data_float3' => 2.5 ) ) ) );
        $this->assertSame( array(), $type->batchInitializeObjectAttributeData( $this->classAttribute( 'ezfloat', array( 'data_float3' => 0 ) ) ) );
        $this->assertSame( 2.5, $type->title( $new ) );
        $this->assertSame( 2.5, $type->objectAttributeContent( $new ) );
        $this->assertSame( 2.5, $type->toString( $new ) );
    }
}
