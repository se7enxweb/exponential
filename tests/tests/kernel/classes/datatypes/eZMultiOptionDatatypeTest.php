<?php
/**
 * The multi-option datatype (ezmultioption) as the content edit view, the shop and the package system use it:
 * validation of the option sets the edit form posts (values, additional prices, required set name), fetching them
 * in priority order, the custom actions (new multioption, new option, remove selected), product option information,
 * text export and import, the class default name and the package serialization.
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

class eZMultiOptionDatatypeTest extends eZDatatypeTestCase
{
    /**
     * The post of the edit form: $sets is a list of array( name, priority, default, values, prices ) keyed by the
     * multioption id.
     */
    private function postSets( $setName, array $sets )
    {
        $post = array( 'ContentObjectAttribute_data_optionset_name_4711' => $setName,
                       'ContentObjectAttribute_data_multioption_id_4711' => array_keys( $sets ) );
        foreach ( $sets as $id => $set )
        {
            list( $name, $priority, $default, $values, $prices ) = $set + array( '', 0, '', array(), array() );
            $post['ContentObjectAttribute_data_multioption_name_4711_' . $id] = $name;
            $post['ContentObjectAttribute_data_multioption_priority_4711_' . $id] = $priority;
            if ( $default !== '' )
                $post['ContentObjectAttribute_data_radio_checked_4711_' . $id] = $default;
            $post['ContentObjectAttribute_data_option_id_4711_' . $id] = array_keys( $values );
            // new options in the form have no option id yet; the set's counter gives them one
            $post['ContentObjectAttribute_data_option_option_id_4711_' . $id] = array_fill( 0, count( $values ), '' );
            $post['ContentObjectAttribute_data_option_value_4711_' . $id] = $values;
            $post['ContentObjectAttribute_data_option_additional_price_4711_' . $id] = $prices;
        }
        return $this->post( $post );
    }

    public static function validationProvider()
    {
        $required = array( 'is_required' => 1 );
        return array(
            'one set' => array( array(), 'Shirt', array( 1 => array( 'Size', 1, '', array( 'S', 'M' ), array( '', '2.50' ) ) ), eZInputValidator::STATE_ACCEPTED ),
            'negative price' => array( array(), 'Shirt', array( 1 => array( 'Size', 1, '', array( 'S' ), array( '-1' ) ) ), eZInputValidator::STATE_ACCEPTED ),
            'price with a sign and two decimals' => array( array(), 'Shirt', array( 1 => array( 'Size', 1, '', array( 'S' ), array( '+1.05' ) ) ), eZInputValidator::STATE_ACCEPTED ),
            'price with three decimals' => array( array(), 'Shirt', array( 1 => array( 'Size', 1, '', array( 'S' ), array( '1.005' ) ) ), eZInputValidator::STATE_INVALID ),
            'price with a bar' => array( array(), 'Shirt', array( 1 => array( 'Size', 1, '', array( 'S' ), array( '|5' ) ) ), eZInputValidator::STATE_INVALID ),
            'price of an empty option is not checked' => array( array(), 'Shirt', array( 1 => array( 'Size', 1, '', array( '' ), array( 'x' ) ) ), eZInputValidator::STATE_ACCEPTED ),
            'required, empty value' => array( $required, 'Shirt', array( 1 => array( 'Size', 1, '', array( 'S', ' ' ) ) ), eZInputValidator::STATE_INVALID ),
            'required, no options' => array( $required, 'Shirt', array(), eZInputValidator::STATE_INVALID ),
            'required, no set name' => array( $required, ' ', array( 1 => array( 'Size', 1, '', array( 'S' ) ) ), eZInputValidator::STATE_INVALID ),
            'required, complete' => array( $required, 'Shirt', array( 1 => array( 'Size', 1, '', array( 'S' ) ) ), eZInputValidator::STATE_ACCEPTED ),
            'required collector without anything' => array( array( 'is_required' => 1, 'is_information_collector' => 1 ), '', array(), eZInputValidator::STATE_ACCEPTED ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('validationProvider')]
    public function testValidation( $classFields, $setName, $sets, $expected )
    {
        $attribute = $this->objectAttribute( 'ezmultioption', $classFields );
        $this->assertSame( $expected, $this->dataType( 'ezmultioption' )->validateObjectAttributeHTTPInput( $this->postSets( $setName, $sets ), 'ContentObjectAttribute', $attribute ) );
    }

    public function testNothingPosted()
    {
        $type = $this->dataType( 'ezmultioption' );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateObjectAttributeHTTPInput( $this->post( array() ), 'ContentObjectAttribute', $this->objectAttribute( 'ezmultioption' ) ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateObjectAttributeHTTPInput( $this->post( array() ), 'ContentObjectAttribute', $this->objectAttribute( 'ezmultioption', array( 'is_required' => 1 ) ) ) );
        $attribute = $this->objectAttribute( 'ezmultioption' );
        $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $this->post( array() ), 'ContentObjectAttribute', $attribute ) );
        $this->assertFalse( $type->hasObjectAttributeContent( $attribute ) );
    }

    private function fetched( $setName, array $sets )
    {
        $type = $this->dataType( 'ezmultioption' );
        $attribute = $this->objectAttribute( 'ezmultioption' );
        $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $this->postSets( $setName, $sets ), 'ContentObjectAttribute', $attribute ) );
        $type->storeObjectAttribute( $attribute );
        return $attribute;
    }

    public function testFetchSortsBySetPriorityAndKeepsOptions()
    {
        $attribute = $this->fetched( 'Shirt', array( 1 => array( 'Size', 2, '', array( 'S', 'M' ), array( '', '1.50' ) ),
                                                     2 => array( 'Colour', 1, '', array( 'Red' ), array( '0' ) ),
                                                     3 => array( 'Fit', 'high', '', array( 'Slim' ) ) ) );
        $content = $attribute->content();
        $this->assertSame( 'Shirt', $content->attribute( 'name' ) );
        $list = $content->attribute( 'multioption_list' );
        $this->assertSame( array( 'Fit', 'Colour', 'Size' ), array_column( $list, 'name' ), 'priority order, text sorts as 0' );
        $this->assertSame( array( 'S', 'M' ), array_column( $list[2]['optionlist'], 'value' ) );
        $this->assertSame( array( '', '1.50' ), array_column( $list[2]['optionlist'], 'additional_price' ) );
        $this->assertTrue( $this->dataType( 'ezmultioption' )->hasObjectAttributeContent( $attribute ) );
        $this->assertSame( 'Shirt', $this->dataType( 'ezmultioption' )->title( $attribute ) );
        $this->assertStringContainsString( '<ezmultioption option_counter="4">', $attribute->attribute( 'data_text' ) );
        $this->assertSame( $attribute->attribute( 'data_text' ), $this->dataType( 'ezmultioption' )->metaData( $attribute ) );

        // option ids are unique over the whole set
        $ids = array();
        foreach ( $list as $multioption )
            foreach ( $multioption['optionlist'] as $option )
                $ids[] = $option['option_id'];
        $this->assertSame( count( $ids ), count( array_unique( $ids ) ) );
    }

    public function testProductOptionInformationFindsTheOptionOfItsMultioption()
    {
        $type = $this->dataType( 'ezmultioption' );
        $attribute = $this->fetched( 'Shirt', array( 1 => array( 'Size', 1, '', array( 'S', 'M' ), array( '', '2' ) ),
                                                     2 => array( 'Colour', 2, '', array( 'Red', 'Blue' ), array( '1', '' ) ) ) );
        $copy = $this->objectAttribute( 'ezmultioption', array(), array( 'data_text' => $attribute->attribute( 'data_text' ) ) );
        foreach ( $copy->content()->attribute( 'multioption_list' ) as $multioption )
        {
            foreach ( $multioption['optionlist'] as $option )
            {
                $info = $type->productOptionInformation( $copy, $option['option_id'], null );
                $this->assertSame( $multioption['name'], $info['name'] );
                $this->assertSame( $option['value'], $info['value'] );
                $this->assertSame( $option['additional_price'], $info['additional_price'] );
            }
        }
        $this->assertEmpty( $type->productOptionInformation( $copy, 999, null ) );
    }

    public function testTextRoundTrip()
    {
        $type = $this->dataType( 'ezmultioption' );
        $attribute = $this->fetched( 'Shirt & co', array( 1 => array( 'Size|fit', 1, '', array( 'S', 'M' ), array( '', '2.50' ) ),
                                                          2 => array( 'Colour', 2, '', array( 'Red' ), array( '1' ) ) ) );
        $text = $type->toString( $attribute );
        $copy = $this->objectAttribute( 'ezmultioption' );
        $this->assertInstanceOf( 'eZMultiOption', $type->fromString( $copy, $text ) );
        $reread = $this->objectAttribute( 'ezmultioption', array(), array( 'data_text' => $copy->attribute( 'data_text' ) ) );
        $this->assertSame( $text, $type->toString( $reread ) );
        $this->assertSame( 'Shirt & co', $reread->content()->attribute( 'name' ) );
        $this->assertSame( array( 'Size|fit', 'Colour' ), array_column( $reread->content()->attribute( 'multioption_list' ), 'name' ) );

        $ids = array();
        foreach ( $reread->content()->attribute( 'multioption_list' ) as $multioption )
            foreach ( $multioption['optionlist'] as $option )
                $ids[] = $option['option_id'];
        $this->assertSame( count( $ids ), count( array_unique( $ids ) ), 'option ids read from text are unique over the set' );
        $this->assertTrue( $type->fromString( $copy, '' ) );
    }

    public function testCustomActions()
    {
        $type = $this->dataType( 'ezmultioption' );
        $attribute = $this->fetched( 'Shirt', array( 1 => array( 'Size', 1, '', array( 'S' ) ) ) );

        $type->customObjectAttributeHTTPAction( $this->post( array() ), 'new_multioption', $attribute, array() );
        $list = array_values( $attribute->content()->attribute( 'multioption_list' ) );
        $this->assertSame( 2, count( $list ) );
        $this->assertSame( 2, count( $list[1]['optionlist'] ) );

        $firstId = $list[0]['id'];
        $type->customObjectAttributeHTTPAction( $this->post( array() ), 'new-option_' . $firstId, $attribute, array() );
        $this->assertSame( 2, count( $attribute->content()->attribute( 'multioption_list' )[0]['optionlist'] ) );

        // an id no multioption has, or none at all: nothing happens
        $type->customObjectAttributeHTTPAction( $this->post( array() ), 'new-option_99', $attribute, array() );
        $type->customObjectAttributeHTTPAction( $this->post( array() ), 'new-option', $attribute, array() );
        $type->customObjectAttributeHTTPAction( $this->post( array() ), 'remove-selected-option_x', $attribute, array() );
        $this->assertSame( 2, count( $attribute->content()->attribute( 'multioption_list' )[0]['optionlist'] ) );

        $optionIds = array_column( $attribute->content()->attribute( 'multioption_list' )[0]['optionlist'], 'id' );
        $type->customObjectAttributeHTTPAction( $this->post( array( 'ContentObjectAttribute_data_option_remove_4711_' . $firstId => array( $optionIds[0] ) ) ),
                                                'remove-selected-option_' . $firstId, $attribute, array() );
        $this->assertSame( 1, count( $attribute->content()->attribute( 'multioption_list' )[0]['optionlist'] ) );

        $type->customObjectAttributeHTTPAction( $this->post( array( 'ContentObjectAttribute_data_multioption_remove_4711' => array( $firstId ) ) ), 'remove_selected_multioption', $attribute, array() );
        $this->assertSame( 1, count( $attribute->content()->attribute( 'multioption_list' ) ) );
        $type->customObjectAttributeHTTPAction( $this->post( array() ), 'remove_selected_multioption', $attribute, array() );
        $this->assertSame( 1, count( $attribute->content()->attribute( 'multioption_list' ) ) );
        $type->customObjectAttributeHTTPAction( $this->post( array() ), 'unknown', $attribute, array() );
        $this->assertGreaterThanOrEqual( 4, $attribute->storeCount );
    }

    public function testCollection()
    {
        $type = $this->dataType( 'ezmultioption' );
        $attribute = $this->objectAttribute( 'ezmultioption' );
        $collected = new eZInformationCollectionAttribute( array( 'data_int' => null ) );
        $this->assertTrue( $type->fetchCollectionAttributeHTTPInput( null, $collected, $this->post( array( 'ContentObjectAttribute_data_multioption_value_4711' => '4' ) ), 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( 4, $collected->attribute( 'data_int' ) );
        foreach ( array( array(), array( 'ContentObjectAttribute_data_multioption_value_4711' => 'x' ), array( 'ContentObjectAttribute_data_multioption_value_4711' => array( 1 ) ) ) as $post )
            $this->assertFalse( $type->fetchCollectionAttributeHTTPInput( null, $collected, $this->post( $post ), 'ContentObjectAttribute', $attribute ) );
    }

    public function testClassDefaultNameInitializationAndSerialization()
    {
        $type = $this->dataType( 'ezmultioption' );
        $classAttribute = $this->classAttribute( 'ezmultioption' );
        $this->assertFalse( $type->fetchClassAttributeHTTPInput( $this->post( array() ), 'ContentClass', $classAttribute ) );
        $this->assertTrue( $type->fetchClassAttributeHTTPInput( $this->post( array( 'ContentClass_ezmultioption_default_name_901' => 'Options' ) ), 'ContentClass', $classAttribute ) );
        $this->assertSame( 'Options', $classAttribute->attribute( 'data_text1' ) );
        $this->assertTrue( $type->fetchClassAttributeHTTPInput( $this->post( array( 'ContentClass_ezmultioption_default_name_901' => array() ) ), 'ContentClass', $classAttribute ) );
        $this->assertSame( '', $classAttribute->attribute( 'data_text1' ) );

        $new = $this->objectAttribute( 'ezmultioption', array( 'data_text1' => 'Options' ) );
        $type->initializeObjectAttribute( $new, false, null );
        $this->assertSame( 'Options', $new->content()->attribute( 'name' ) );
        $copy = $this->objectAttribute( 'ezmultioption' );
        $type->initializeObjectAttribute( $copy, 2, $new );
        $this->assertSame( $new->attribute( 'data_text' ), $copy->attribute( 'data_text' ) );

        list( $copied, $xml ) = $this->roundTripClassParameters( $this->classAttribute( 'ezmultioption', array( 'data_text1' => 'A & B' ) ) );
        $this->assertSame( '<datatype-parameters><default-value>A &amp; B</default-value></datatype-parameters>', $xml );
        $this->assertSame( 'A & B', $copied->attribute( 'data_text1' ) );

        $attribute = $this->fetched( 'Shirt', array( 1 => array( 'Size', 1, '', array( 'S' ) ) ) );
        $node = $type->serializeContentObjectAttribute( null, $attribute );
        $reread = $this->objectAttribute( 'ezmultioption' );
        $type->unserializeContentObjectAttribute( null, $reread, $node );
        $this->assertSame( 'Shirt', $reread->content()->attribute( 'name' ) );
        $broken = $type->serializeContentObjectAttribute( null, $this->objectAttribute( 'ezmultioption', array(), array( 'data_text' => '<x' ) ) );
        $this->assertSame( 0, $broken->getElementsByTagName( 'ezmultioption' )->length );
        $dom = new DOMDocument();
        $dom->loadXML( '<attribute/>' );
        $type->unserializeContentObjectAttribute( null, $reread, $dom->documentElement );
        $this->assertSame( '', $reread->attribute( 'data_text' ) );
        $this->assertTrue( $type->isIndexable() );
    }

    public function testNewObjectsOfBothMultiOptionDatatypesGetTheDefaultName()
    {
        foreach ( array( 'ezmultioption', 'ezmultioption2' ) as $dataTypeString )
        {
            $type = $this->dataType( $dataTypeString );
            $new = $this->objectAttribute( $dataTypeString, array( 'data_text1' => 'Choose' ) );
            $type->initializeObjectAttribute( $new, false, null );
            $this->assertSame( 'Choose', $new->content()->attribute( 'name' ), $dataTypeString );
            $this->assertStringContainsString( 'Choose', $new->attribute( 'data_text' ), $dataTypeString );
        }
    }
}
