<?php
/**
 * The identifier (ezidentifier) and range option (ezrangeoption) datatypes without a database: the identifier's
 * class settings (validation, defaults, package serialization), the identifier text built from pre-text, number
 * and post-text, and its content and text export; the range option's posted values (required, numbers, step, the
 * size of the range), fetching, shop option information, text export and import, the class default name and the
 * package serialization. Assigning an identifier number needs the database and is not covered here.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group datatypes
 */

require_once __DIR__ . '/eZDatatypeTestFixtures.php';

class eZIdentifierAndRangeOptionDatatypesTest extends eZDatatypeTestCase
{
    // ---------------------------------------------------------------- ezidentifier

    private function identifierClass( array $fields = array() )
    {
        return $this->classAttribute( 'ezidentifier', array_merge( array( 'data_text1' => 'INV-', 'data_text2' => '/26', 'data_int1' => 1, 'data_int2' => 5, 'data_int3' => 1 ), $fields ) );
    }

    public static function identifierStringProvider()
    {
        return array(
            'padded' => array( array(), 42, 'INV-00042/26' ),
            'longer than the digits' => array( array( 'data_int2' => 2 ), 12345, 'INV-12345/26' ),
            'placeholder' => array( array(), false, 'INV-xxxxx/26' ),
            'no digits' => array( array( 'data_int2' => 0 ), 7, 'INV-7/26' ),
            'negative digits' => array( array( 'data_int2' => -3 ), false, 'INV-/26' ),
            'too many digits' => array( array( 'data_int2' => 500 ), 1, 'INV-' . str_repeat( '0', eZIdentifierType::DIGITS_MAX - 1 ) . '1/26' ),
            'no texts' => array( array( 'data_text1' => '', 'data_text2' => '' ), 3, '00003' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('identifierStringProvider')]
    public function testIdentifierString( $fields, $value, $expected )
    {
        $this->assertSame( $expected, $this->dataType( 'ezidentifier' )->generateIdentifierString( $this->identifierClass( $fields ), $value ) );
    }

    public function testIdentifierContentAndText()
    {
        $type = $this->dataType( 'ezidentifier' );
        $none = $this->objectAttribute( 'ezidentifier', $this->identifierClass(), array( 'data_text' => null ) );
        $this->assertSame( 'INV-xxxxx/26', $type->objectAttributeContent( $none ), 'before one is assigned, the form shows the shape' );
        $this->assertFalse( $type->hasObjectAttributeContent( $none ) );

        $assigned = $this->objectAttribute( 'ezidentifier', $this->identifierClass(), array( 'data_text' => 'INV-00007/26', 'data_int' => 7 ) );
        $this->assertSame( 'INV-00007/26', $type->objectAttributeContent( $assigned ) );
        $this->assertTrue( $type->hasObjectAttributeContent( $assigned ) );
        $this->assertSame( 'INV-00007/26', $type->title( $assigned ) );
        $this->assertSame( 'INV-00007/26', $type->metaData( $assigned ) );
        $this->assertSame( 'INV-00007/26', $type->toString( $assigned ) );
        $this->assertTrue( $type->fromString( $assigned, '' ) );
        $this->assertSame( 'INV-00007/26', $assigned->attribute( 'data_text' ) );
        $this->assertTrue( $type->fromString( $assigned, 'X-1' ) );
        $this->assertSame( 'X-1', $assigned->attribute( 'data_text' ) );
        $this->assertTrue( $type->isIndexable() );
    }

    public static function identifierClassSettingsProvider()
    {
        return array(
            'valid' => array( '1', '5', 'A', 'B', eZInputValidator::STATE_ACCEPTED ),
            'spaces in numbers' => array( '1 000', '5', 'A', 'B', eZInputValidator::STATE_ACCEPTED ),
            'start 0' => array( '0', '5', 'A', 'B', eZInputValidator::STATE_INTERMEDIATE ),
            'start above the column' => array( '2147483648', '5', 'A', 'B', eZInputValidator::STATE_INTERMEDIATE ),
            'digits above the maximum' => array( '1', '51', 'A', 'B', eZInputValidator::STATE_INTERMEDIATE ),
            'letters' => array( 'x', '5', 'A', 'B', eZInputValidator::STATE_INTERMEDIATE ),
            'pre-text too long' => array( '1', '5', str_repeat( 'a', 51 ), 'B', eZInputValidator::STATE_INVALID ),
            'post-text an array' => array( '1', '5', 'A', array( 'B' ), eZInputValidator::STATE_INVALID ),
            'start an array' => array( array( '1' ), '5', 'A', 'B', eZInputValidator::STATE_INVALID ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('identifierClassSettingsProvider')]
    public function testIdentifierClassSettingsValidation( $start, $digits, $pre, $post, $expected )
    {
        $http = $this->post( array( 'ContentClass_ezidentifier_start_integer_value_901' => $start, 'ContentClass_ezidentifier_digits_integer_value_901' => $digits,
                                    'ContentClass_ezidentifier_pretext_value_901' => $pre, 'ContentClass_ezidentifier_posttext_value_901' => $post ) );
        $this->assertSame( $expected, $this->dataType( 'ezidentifier' )->validateClassAttributeHTTPInput( $http, 'ContentClass', $this->identifierClass() ) );
    }

    public function testIdentifierClassSettingsNeedStartAndDigits()
    {
        $http = $this->post( array( 'ContentClass_ezidentifier_start_integer_value_901' => '1' ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $this->dataType( 'ezidentifier' )->validateClassAttributeHTTPInput( $http, 'ContentClass', $this->identifierClass() ) );
    }

    public function testIdentifierClassInitializationAndSerialization()
    {
        $type = $this->dataType( 'ezidentifier' );
        $classAttribute = $this->classAttribute( 'ezidentifier', array( 'data_int1' => null, 'data_int2' => null, 'data_int3' => null ) );
        $type->initializeClassAttribute( $classAttribute );
        $this->assertSame( array( 1, 1, 1 ), array( $classAttribute->attribute( 'data_int1' ), $classAttribute->attribute( 'data_int2' ), $classAttribute->attribute( 'data_int3' ) ) );
        $set = $this->identifierClass( array( 'data_int3' => 9 ) );
        $type->initializeClassAttribute( $set );
        $this->assertSame( 9, $set->attribute( 'data_int3' ) );

        list( $copy, $xml ) = $this->roundTripClassParameters( $this->identifierClass( array( 'data_int3' => 17, 'data_text1' => 'A&B' ) ) );
        $this->assertSame( '<datatype-parameters><digits>5</digits><pre-text>A&amp;B</pre-text><post-text>/26</post-text><start-value>1</start-value><identifier>17</identifier></datatype-parameters>', $xml );
        foreach ( array( 'data_int2' => '5', 'data_text1' => 'A&B', 'data_text2' => '/26', 'data_int1' => '1', 'data_int3' => '17' ) as $field => $value )
            $this->assertSame( $value, $copy->attribute( $field ), $field );

        $dom = new DOMDocument();
        $dom->loadXML( '<attribute><datatype-parameters/></attribute>' );
        $empty = $this->identifierClass();
        $type->unserializeContentClassAttribute( $empty, $dom->documentElement, $dom->documentElement->firstChild );
        $this->assertSame( '', $empty->attribute( 'data_text1' ) );
    }

    // ---------------------------------------------------------------- ezrangeoption

    private function postRange( $name, $start, $stop, $step )
    {
        return $this->post( array( 'ContentObjectAttribute_data_rangeoption_name_4711' => $name, 'ContentObjectAttribute_data_rangeoption_start_value_4711' => $start,
                                   'ContentObjectAttribute_data_rangeoption_stop_value_4711' => $stop, 'ContentObjectAttribute_data_rangeoption_step_value_4711' => $step ) );
    }

    public static function rangeInputProvider()
    {
        $required = array( 'is_required' => 1 );
        return array(
            'range' => array( array(), 'Size', '1', '10', '1', eZInputValidator::STATE_ACCEPTED ),
            'decimal step' => array( array(), 'Weight', '0.5', '2', '0.5', eZInputValidator::STATE_ACCEPTED ),
            'step zero' => array( array(), 'Size', '1', '10', '0', eZInputValidator::STATE_INVALID ),
            'negative step' => array( array(), 'Size', '1', '10', '-1', eZInputValidator::STATE_INVALID ),
            'letters' => array( array(), 'Size', 'a', '10', '1', eZInputValidator::STATE_INVALID ),
            'too many values' => array( array(), 'Size', '1', '100000000', '1', eZInputValidator::STATE_INVALID ),
            'array posted' => array( array(), array( 'Size' ), '1', '10', '1', eZInputValidator::STATE_INVALID ),
            'empty, optional' => array( array(), '', '', '', '', eZInputValidator::STATE_ACCEPTED ),
            'empty, required' => array( $required, 'Size', '', '10', '1', eZInputValidator::STATE_INVALID ),
            'empty, required collector' => array( array( 'is_required' => 1, 'is_information_collector' => 1 ), '', '', '', '', eZInputValidator::STATE_ACCEPTED ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('rangeInputProvider')]
    public function testRangeOptionInput( $classFields, $name, $start, $stop, $step, $expected )
    {
        $attribute = $this->objectAttribute( 'ezrangeoption', $classFields );
        $this->assertSame( $expected, $this->dataType( 'ezrangeoption' )->validateObjectAttributeHTTPInput( $this->postRange( $name, $start, $stop, $step ), 'ContentObjectAttribute', $attribute ) );
    }

    public function testRangeOptionNotPosted()
    {
        $type = $this->dataType( 'ezrangeoption' );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateObjectAttributeHTTPInput( $this->post( array() ), 'ContentObjectAttribute', $this->objectAttribute( 'ezrangeoption' ) ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateObjectAttributeHTTPInput( $this->post( array() ), 'ContentObjectAttribute', $this->objectAttribute( 'ezrangeoption', array( 'is_required' => 1 ) ) ) );
    }

    public function testRangeOptionFetchStoreAndShopInformation()
    {
        $type = $this->dataType( 'ezrangeoption' );
        $attribute = $this->objectAttribute( 'ezrangeoption' );
        $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $this->postRange( 'Size', '1', '3', '1' ), 'ContentObjectAttribute', $attribute ) );
        $type->storeObjectAttribute( $attribute );
        $reread = $this->objectAttribute( 'ezrangeoption', array(), array( 'data_text' => $attribute->attribute( 'data_text' ) ) );
        $option = $reread->content();
        $this->assertSame( 'Size', $option->attribute( 'name' ) );
        $this->assertSame( array( '1', '2', '3' ), array_map( 'strval', array_column( $option->attribute( 'option_list' ), 'value' ) ) );
        $this->assertSame( 'Size', $type->title( $reread ) );
        $this->assertTrue( $type->hasObjectAttributeContent( $reread ) );
        $this->assertSame( $reread->attribute( 'data_text' ), $type->metaData( $reread ) );

        $first = $option->attribute( 'option_list' )[1];
        $info = $type->productOptionInformation( $reread, $first['id'], null );
        $this->assertSame( 'Size', $info['name'] );
        $this->assertEquals( 2, $info['value'] );
        $this->assertFalse( $type->productOptionInformation( $reread, 999, null ) );

        $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $this->postRange( array( 'x' ), '1', '2', '1' ), 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( '', $attribute->content()->attribute( 'name' ) );
    }

    public function testRangeOptionTextRoundTrip()
    {
        $type = $this->dataType( 'ezrangeoption' );
        $attribute = $this->objectAttribute( 'ezrangeoption' );
        $this->assertInstanceOf( 'eZRangeOption', $type->fromString( $attribute, 'Size | big|1|5|2' ) );
        $reread = $this->objectAttribute( 'ezrangeoption', array(), array( 'data_text' => $attribute->attribute( 'data_text' ) ) );
        $this->assertSame( 'Size | big', $reread->content()->attribute( 'name' ) );
        $this->assertSame( 'Size | big|1|5|2', $type->toString( $reread ) );
        $this->assertTrue( $type->fromString( $attribute, '' ) );
    }

    public function testRangeOptionClassDefaultInitializationAndSerialization()
    {
        $type = $this->dataType( 'ezrangeoption' );
        $classAttribute = $this->classAttribute( 'ezrangeoption' );
        $this->assertFalse( $type->fetchClassAttributeHTTPInput( $this->post( array() ), 'ContentClass', $classAttribute ) );
        $this->assertTrue( $type->fetchClassAttributeHTTPInput( $this->post( array( 'ContentClass_ezrangeoption_default_name_901' => 'Pick' ) ), 'ContentClass', $classAttribute ) );
        $this->assertSame( 'Pick', $classAttribute->attribute( 'data_text1' ) );
        $this->assertTrue( $type->fetchClassAttributeHTTPInput( $this->post( array( 'ContentClass_ezrangeoption_default_name_901' => array() ) ), 'ContentClass', $classAttribute ) );
        $this->assertSame( '', $classAttribute->attribute( 'data_text1' ) );

        $new = $this->objectAttribute( 'ezrangeoption', array( 'data_text1' => 'Pick' ) );
        $type->initializeObjectAttribute( $new, false, null );
        $this->assertSame( 'Pick', $new->content()->attribute( 'name' ) );
        $this->assertStringContainsString( 'Pick', $new->attribute( 'data_text' ) );

        list( $copy, $xml ) = $this->roundTripClassParameters( $this->classAttribute( 'ezrangeoption', array( 'data_text1' => 'A<B' ) ) );
        $this->assertSame( '<datatype-parameters><default-name>A&lt;B</default-name></datatype-parameters>', $xml );
        $this->assertSame( 'A<B', $copy->attribute( 'data_text1' ) );

        $node = $type->serializeContentObjectAttribute( null, $new );
        $reread = $this->objectAttribute( 'ezrangeoption' );
        $type->unserializeContentObjectAttribute( null, $reread, $node );
        $this->assertSame( 'Pick', $reread->content()->attribute( 'name' ) );
        $broken = $type->serializeContentObjectAttribute( null, $this->objectAttribute( 'ezrangeoption', array(), array( 'data_text' => '' ) ) );
        $this->assertSame( 0, $broken->getElementsByTagName( 'ezrangeoption' )->length );
    }
}
