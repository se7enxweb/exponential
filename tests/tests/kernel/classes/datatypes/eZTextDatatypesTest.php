<?php
/**
 * The text line (ezstring) and text block (eztext) datatypes as the content and class edit views use them:
 * required input, maximum length, posted arrays, information collection, class settings (maximum length, default
 * text, rows), defaults for new objects, simple string insertion, sort keys and the package serialization.
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

class eZTextDatatypesTest extends eZDatatypeTestCase
{
    // ---------------------------------------------------------------- ezstring

    private function stringAttribute( array $classFields = array() )
    {
        return $this->objectAttribute( 'ezstring', $classFields );
    }

    public static function stringInputProvider()
    {
        return array(
            'text' => array( array(), 'Hello', eZInputValidator::STATE_ACCEPTED ),
            'optional empty' => array( array(), '', eZInputValidator::STATE_ACCEPTED ),
            'required empty' => array( array( 'is_required' => 1 ), '', eZInputValidator::STATE_INVALID ),
            'required spaces only' => array( array( 'is_required' => 1 ), "  \t ", eZInputValidator::STATE_INVALID ),
            'required zero' => array( array( 'is_required' => 1 ), '0', eZInputValidator::STATE_ACCEPTED ),
            'required collector empty' => array( array( 'is_required' => 1, 'is_information_collector' => 1 ), '', eZInputValidator::STATE_ACCEPTED ),
            'at the maximum length' => array( array( 'data_int1' => 5 ), 'abcde', eZInputValidator::STATE_ACCEPTED ),
            'multibyte at the maximum length' => array( array( 'data_int1' => 5 ), 'äöüßé', eZInputValidator::STATE_ACCEPTED ),
            'over the maximum length' => array( array( 'data_int1' => 5 ), 'abcdef', eZInputValidator::STATE_INVALID ),
            'spaces around do not count' => array( array( 'data_int1' => 5 ), '  abcde  ', eZInputValidator::STATE_ACCEPTED ),
            'no maximum' => array( array( 'data_int1' => 0 ), str_repeat( 'x', 500 ), eZInputValidator::STATE_ACCEPTED ),
            'number posted' => array( array(), 12, eZInputValidator::STATE_ACCEPTED ),
            'array posted' => array( array(), array( 'x' ), eZInputValidator::STATE_INVALID ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('stringInputProvider')]
    public function testStringObjectInput( $classFields, $input, $expected )
    {
        $attribute = $this->stringAttribute( $classFields );
        $http = $this->post( array( 'ContentObjectAttribute_ezstring_data_text_4711' => $input ) );
        $this->assertSame( $expected, $this->dataType( 'ezstring' )->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute ) );
    }

    public function testStringTooLongErrorNamesTheMaximum()
    {
        $attribute = $this->stringAttribute( array( 'data_int1' => 3 ) );
        $http = $this->post( array( 'ContentObjectAttribute_ezstring_data_text_4711' => 'abcd' ) );
        $this->dataType( 'ezstring' )->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute );
        $this->assertStringContainsString( '3', $attribute->validationError() );
    }

    public function testStringNotPostedDependsOnRequired()
    {
        $type = $this->dataType( 'ezstring' );
        $http = $this->post( array() );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $this->stringAttribute() ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $this->stringAttribute( array( 'is_required' => 1 ) ) ) );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $this->stringAttribute( array( 'is_required' => 1, 'is_information_collector' => 1 ) ) ) );
    }

    public function testStringFetchKeepsTheTextAsPosted()
    {
        $type = $this->dataType( 'ezstring' );
        $attribute = $this->stringAttribute();
        $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $this->post( array( 'ContentObjectAttribute_ezstring_data_text_4711' => ' Hi <b> ' ) ), 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( ' Hi <b> ', $attribute->attribute( 'data_text' ) );
        $this->assertTrue( $type->hasObjectAttributeContent( $attribute ) );
        $this->assertFalse( $type->fetchObjectAttributeHTTPInput( $this->post( array( 'ContentObjectAttribute_ezstring_data_text_4711' => array( 'x' ) ) ), 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( ' Hi <b> ', $attribute->attribute( 'data_text' ) );
        $this->assertFalse( $type->fetchObjectAttributeHTTPInput( $this->post( array() ), 'ContentObjectAttribute', $attribute ) );
    }

    public function testStringHasContentIgnoresSpacesAndNull()
    {
        $type = $this->dataType( 'ezstring' );
        $attribute = $this->stringAttribute();
        foreach ( array( null, '', '   ' ) as $empty )
        {
            $attribute->setAttribute( 'data_text', $empty );
            $this->assertFalse( $type->hasObjectAttributeContent( $attribute ) );
        }
        $attribute->setAttribute( 'data_text', '0' );
        $this->assertTrue( $type->hasObjectAttributeContent( $attribute ) );
    }

    public static function stringCollectionProvider()
    {
        return array(
            'text' => array( array(), 'Hi', eZInputValidator::STATE_ACCEPTED ),
            'optional empty' => array( array(), '', eZInputValidator::STATE_ACCEPTED ),
            'required empty' => array( array( 'is_required' => 1 ), '', eZInputValidator::STATE_INVALID ),
            'required spaces only' => array( array( 'is_required' => 1 ), '   ', eZInputValidator::STATE_INVALID ),
            'too long' => array( array( 'data_int1' => 2 ), 'abc', eZInputValidator::STATE_INVALID ),
            'array' => array( array(), array( 'a' ), eZInputValidator::STATE_INVALID ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('stringCollectionProvider')]
    public function testStringCollectionInput( $classFields, $input, $expected )
    {
        $attribute = $this->stringAttribute( $classFields );
        $http = $this->post( array( 'ContentObjectAttribute_ezstring_data_text_4711' => $input ) );
        $this->assertSame( $expected, $this->dataType( 'ezstring' )->validateCollectionAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute ) );
    }

    public function testStringCollectionNotPostedIsInvalidAndFetchStoresText()
    {
        $type = $this->dataType( 'ezstring' );
        $attribute = $this->stringAttribute();
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateCollectionAttributeHTTPInput( $this->post( array() ), 'ContentObjectAttribute', $attribute ) );
        $collected = new eZInformationCollectionAttribute( array( 'data_text' => '' ) );
        $this->assertTrue( $type->fetchCollectionAttributeHTTPInput( null, $collected, $this->post( array( 'ContentObjectAttribute_ezstring_data_text_4711' => 'Mail me' ) ), 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( 'Mail me', $collected->attribute( 'data_text' ) );
        $this->assertFalse( $type->fetchCollectionAttributeHTTPInput( null, $collected, $this->post( array( 'ContentObjectAttribute_ezstring_data_text_4711' => array() ) ), 'ContentObjectAttribute', $attribute ) );
        $this->assertFalse( $type->fetchCollectionAttributeHTTPInput( null, $collected, $this->post( array() ), 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( 'Mail me', $collected->attribute( 'data_text' ) );
    }

    public static function stringClassSettingsProvider()
    {
        return array(
            'empty maximum means none' => array( '', 'x', eZInputValidator::STATE_ACCEPTED, 0 ),
            'zero maximum' => array( '0', 'x', eZInputValidator::STATE_ACCEPTED, 0 ),
            'positive maximum' => array( '25', 'x', eZInputValidator::STATE_ACCEPTED, '25' ),
            'spaces in the maximum' => array( '2 5', 'x', eZInputValidator::STATE_ACCEPTED, '2 5' ),
            'negative maximum' => array( '-3', 'x', eZInputValidator::STATE_INTERMEDIATE, '-3' ),
            'letters' => array( 'ab', 'x', eZInputValidator::STATE_INVALID, 'ab' ),
            'default of 50 characters' => array( '', str_repeat( 'd', 50 ), eZInputValidator::STATE_ACCEPTED, 0 ),
            'default over 50 characters' => array( '', str_repeat( 'd', 51 ), eZInputValidator::STATE_INVALID, '' ),
            'default posted as array' => array( '', array( 'd' ), eZInputValidator::STATE_INVALID, '' ),
            'maximum posted as array' => array( array( '5' ), 'x', eZInputValidator::STATE_INVALID, array( '5' ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('stringClassSettingsProvider')]
    public function testStringClassSettingsValidation( $maxLength, $default, $expected, $postedMaxAfter )
    {
        $http = $this->post( array( 'ContentClass_ezstring_max_string_length_901' => $maxLength, 'ContentClass_ezstring_default_value_901' => $default ) );
        $this->assertSame( $expected, $this->dataType( 'ezstring' )->validateClassAttributeHTTPInput( $http, 'ContentClass', $this->classAttribute( 'ezstring' ) ) );
        $this->assertSame( $postedMaxAfter, $_POST['ContentClass_ezstring_max_string_length_901'] );
    }

    public function testStringClassSettingsWithoutMaximumAreInvalid()
    {
        $http = $this->post( array( 'ContentClass_ezstring_default_value_901' => 'x' ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $this->dataType( 'ezstring' )->validateClassAttributeHTTPInput( $http, 'ContentClass', $this->classAttribute( 'ezstring' ) ) );
    }

    public function testStringClassFixupAndFetch()
    {
        $type = $this->dataType( 'ezstring' );
        $classAttribute = $this->classAttribute( 'ezstring' );
        $http = $this->post( array( 'ContentClass_ezstring_max_string_length_901' => '-4', 'ContentClass_ezstring_default_value_901' => 'Default' ) );
        $type->fixupClassAttributeHTTPInput( $http, 'ContentClass', $classAttribute );
        $this->assertSame( 1, (int)$_POST['ContentClass_ezstring_max_string_length_901'] );
        $this->assertTrue( $type->fetchClassAttributeHTTPInput( $http, 'ContentClass', $classAttribute ) );
        $this->assertSame( 1, (int)$classAttribute->attribute( eZStringType::MAX_LEN_FIELD ) );
        $this->assertSame( 'Default', $classAttribute->attribute( eZStringType::DEFAULT_STRING_FIELD ) );

        $classAttribute = $this->classAttribute( 'ezstring', array( 'data_int1' => 7, 'data_text1' => 'keep' ) );
        $http = $this->post( array( 'ContentClass_ezstring_max_string_length_901' => array( 9 ), 'ContentClass_ezstring_default_value_901' => array( 'x' ) ) );
        $type->fixupClassAttributeHTTPInput( $http, 'ContentClass', $classAttribute );
        $this->assertTrue( $type->fetchClassAttributeHTTPInput( $http, 'ContentClass', $classAttribute ) );
        $this->assertSame( 'keep', $classAttribute->attribute( eZStringType::DEFAULT_STRING_FIELD ) );
        $this->assertSame( 1, (int)$_POST['ContentClass_ezstring_max_string_length_901'] );
    }

    public function testStringDefaultForNewAndCopiedObjects()
    {
        $type = $this->dataType( 'ezstring' );
        $new = $this->stringAttribute( array( 'data_text1' => 'Untitled' ) );
        $type->initializeObjectAttribute( $new, false, null );
        $this->assertSame( 'Untitled', $new->attribute( 'data_text' ) );

        $noDefault = $this->stringAttribute( array( 'data_text1' => '' ) );
        $type->initializeObjectAttribute( $noDefault, false, null );
        $this->assertSame( '', $noDefault->attribute( 'data_text' ) );

        $original = $this->stringAttribute();
        $original->setAttribute( 'data_text', 'Was there' );
        $copy = $this->stringAttribute( array( 'data_text1' => 'Untitled' ) );
        $type->initializeObjectAttribute( $copy, 4, $original );
        $this->assertSame( 'Was there', $copy->attribute( 'data_text' ) );
    }

    public function testStringContentTitleSortKeyAndSimpleInsertion()
    {
        $type = $this->dataType( 'ezstring' );
        $attribute = $this->stringAttribute();
        $result = null;
        $this->assertTrue( $type->isSimpleStringInsertionSupported() );
        $this->assertTrue( $type->insertSimpleString( null, 1, 'eng-GB', $attribute, 'Hello World', $result ) );
        $this->assertSame( array( 'errors' => array(), 'require_storage' => true ), $result );
        $this->assertSame( 'Hello World', $attribute->attribute( 'data_text' ) );
        $this->assertSame( 'Hello World', $attribute->content() );
        $this->assertSame( 'Hello World', $type->objectAttributeContent( $attribute ) );
        $this->assertSame( 'Hello World', $type->title( $attribute ) );
        $this->assertSame( 'Hello World', $type->metaData( $attribute ) );
        $this->assertSame( 'hello world', $type->sortKey( $attribute ) );
        $this->assertSame( 'string', $type->sortKeyType() );
        $this->assertTrue( $type->isIndexable() );
        $this->assertTrue( $type->isInformationCollector() );
    }

    public static function stringSerializationProvider()
    {
        return array(
            'both' => array( 30, 'Untitled', '<datatype-parameters><max-length>30</max-length><default-string>Untitled</default-string></datatype-parameters>' ),
            'no default' => array( 0, '', '<datatype-parameters><max-length>0</max-length><default-string/></datatype-parameters>' ),
            'markup in the default' => array( 0, 'a<b>&c', '<datatype-parameters><max-length>0</max-length><default-string>a&lt;b&gt;&amp;c</default-string></datatype-parameters>' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('stringSerializationProvider')]
    public function testStringClassSettingsSurviveThePackageSerialization( $maxLength, $default, $expectedXml )
    {
        list( $copy, $xml ) = $this->roundTripClassParameters( $this->classAttribute( 'ezstring', array( 'data_int1' => $maxLength, 'data_text1' => $default ) ) );
        $this->assertSame( $expectedXml, $xml );
        $this->assertSame( $maxLength, $copy->attribute( eZStringType::MAX_LEN_FIELD ) );
        $this->assertSame( $default, $copy->attribute( eZStringType::DEFAULT_STRING_FIELD ) );
    }

    public function testStringUnserializeWithoutElementsGivesNoLimitAndNoDefault()
    {
        $dom = new DOMDocument();
        $dom->loadXML( '<attribute><datatype-parameters/></attribute>' );
        $classAttribute = $this->classAttribute( 'ezstring', array( 'data_int1' => 9, 'data_text1' => 'x' ) );
        $this->dataType( 'ezstring' )->unserializeContentClassAttribute( $classAttribute, $dom->documentElement, $dom->documentElement->firstChild );
        $this->assertSame( 0, $classAttribute->attribute( eZStringType::MAX_LEN_FIELD ) );
        $this->assertSame( '', $classAttribute->attribute( eZStringType::DEFAULT_STRING_FIELD ) );
    }

    public function testStringBatchInitializationQuotesTheDefaultForSql()
    {
        $type = $this->dataType( 'ezstring' );
        $this->assertSame( array(), $type->batchInitializeObjectAttributeData( $this->classAttribute( 'ezstring', array( 'data_text1' => '' ) ) ) );
        $data = $type->batchInitializeObjectAttributeData( $this->classAttribute( 'ezstring', array( 'data_text1' => "It's New" ) ) );
        $this->assertSame( "'" . eZDB::instance()->escapeString( "It's New" ) . "'", $data['data_text'] );
        $this->assertSame( strtolower( $data['data_text'] ), $data['sort_key_string'] );
    }

    // ---------------------------------------------------------------- eztext

    private function textAttribute( array $classFields = array() )
    {
        return $this->objectAttribute( 'eztext', array_merge( array( 'data_int1' => 10 ), $classFields ) );
    }

    public static function textInputProvider()
    {
        return array(
            'text' => array( array(), "Line 1\nLine 2", eZInputValidator::STATE_ACCEPTED ),
            'optional empty' => array( array(), '', eZInputValidator::STATE_ACCEPTED ),
            'required empty' => array( array( 'is_required' => 1 ), '', eZInputValidator::STATE_INVALID ),
            'required blank lines only' => array( array( 'is_required' => 1 ), "  \n\t\n ", eZInputValidator::STATE_INVALID ),
            'required zero' => array( array( 'is_required' => 1 ), '0', eZInputValidator::STATE_ACCEPTED ),
            'required collector empty' => array( array( 'is_required' => 1, 'is_information_collector' => 1 ), '', eZInputValidator::STATE_ACCEPTED ),
            'required array posted' => array( array( 'is_required' => 1 ), array( 'x' ), eZInputValidator::STATE_INVALID ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('textInputProvider')]
    public function testTextObjectInput( $classFields, $input, $expected )
    {
        $http = $this->post( array( 'ContentObjectAttribute_data_text_4711' => $input ) );
        $this->assertSame( $expected, $this->dataType( 'eztext' )->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $this->textAttribute( $classFields ) ) );
    }

    public static function textCollectionProvider()
    {
        return array(
            'text' => array( array(), 'Hello', eZInputValidator::STATE_ACCEPTED ),
            'required empty' => array( array( 'is_required' => 1 ), '', eZInputValidator::STATE_INVALID ),
            'required blank' => array( array( 'is_required' => 1 ), " \n ", eZInputValidator::STATE_INVALID ),
            'optional empty' => array( array(), '', eZInputValidator::STATE_ACCEPTED ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('textCollectionProvider')]
    public function testTextCollectionInput( $classFields, $input, $expected )
    {
        $http = $this->post( array( 'ContentObjectAttribute_data_text_4711' => $input ) );
        $this->assertSame( $expected, $this->dataType( 'eztext' )->validateCollectionAttributeHTTPInput( $http, 'ContentObjectAttribute', $this->textAttribute( $classFields ) ) );
    }

    public function testTextNotPosted()
    {
        $type = $this->dataType( 'eztext' );
        $http = $this->post( array() );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $this->textAttribute() ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $this->textAttribute( array( 'is_required' => 1 ) ) ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateCollectionAttributeHTTPInput( $http, 'ContentObjectAttribute', $this->textAttribute() ) );
        $this->assertFalse( $type->fetchObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $this->textAttribute() ) );
        $collected = new eZInformationCollectionAttribute( array( 'data_text' => 'old' ) );
        $this->assertFalse( $type->fetchCollectionAttributeHTTPInput( null, $collected, $http, 'ContentObjectAttribute', $this->textAttribute() ) );
    }

    public function testTextFetchStoresTextAndArraysAsEmpty()
    {
        $type = $this->dataType( 'eztext' );
        $attribute = $this->textAttribute();
        $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $this->post( array( 'ContentObjectAttribute_data_text_4711' => "a\nb" ) ), 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( "a\nb", $attribute->attribute( 'data_text' ) );
        $this->assertTrue( $type->hasObjectAttributeContent( $attribute ) );
        $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $this->post( array( 'ContentObjectAttribute_data_text_4711' => array( 'x' ) ) ), 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( '', $attribute->attribute( 'data_text' ) );
        $this->assertFalse( $type->hasObjectAttributeContent( $attribute ) );

        $collected = new eZInformationCollectionAttribute( array( 'data_text' => '' ) );
        $this->assertTrue( $type->fetchCollectionAttributeHTTPInput( null, $collected, $this->post( array( 'ContentObjectAttribute_data_text_4711' => 'Note' ) ), 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( 'Note', $collected->attribute( 'data_text' ) );
    }

    public static function columnCountProvider()
    {
        return array(
            'number' => array( '20', 20 ),
            'spaces around' => array( ' 7 ', 7 ),
            'zero' => array( '0', eZTextType::COLS_DEFAULT ),
            'negative' => array( '-5', eZTextType::COLS_DEFAULT ),
            'letters' => array( '5a', eZTextType::COLS_DEFAULT ),
            'markup' => array( '5" onfocus="x', eZTextType::COLS_DEFAULT ),
            'empty' => array( '', eZTextType::COLS_DEFAULT ),
            'array' => array( array( 5 ), eZTextType::COLS_DEFAULT ),
            'over the maximum' => array( '5000', eZTextType::COLS_MAX ),
            'integer' => array( 12, 12 ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('columnCountProvider')]
    public function testTextRowsSettingIsAlwaysANumberInRange( $posted, $expected )
    {
        $this->assertSame( $expected, eZTextType::columnCount( $posted ) );
        $classAttribute = $this->classAttribute( 'eztext' );
        $this->assertTrue( $this->dataType( 'eztext' )->fetchClassAttributeHTTPInput( $this->post( array( 'ContentClass_eztext_cols_901' => $posted ) ), 'ContentClass', $classAttribute ) );
        $this->assertSame( $expected, $classAttribute->attribute( eZTextType::COLS_FIELD ) );
    }

    public function testTextClassSettingsNotPosted()
    {
        $this->assertFalse( $this->dataType( 'eztext' )->fetchClassAttributeHTTPInput( $this->post( array() ), 'ContentClass', $this->classAttribute( 'eztext' ) ) );
    }

    public function testTextInitializationSetsDefaultRowsAndCopiesText()
    {
        $type = $this->dataType( 'eztext' );
        $classAttribute = $this->classAttribute( 'eztext', array( 'data_int1' => null ) );
        $type->initializeClassAttribute( $classAttribute );
        $this->assertSame( 10, $classAttribute->attribute( 'data_int1' ) );
        $this->assertSame( 1, $classAttribute->storeCount );

        $attribute = $this->objectAttribute( 'eztext', array( 'data_int1' => 0 ) );
        $original = $this->textAttribute();
        $original->setAttribute( 'data_text', 'copied' );
        $type->initializeObjectAttribute( $attribute, 2, $original );
        $this->assertSame( 'copied', $attribute->attribute( 'data_text' ) );
        $this->assertSame( 10, $attribute->contentClassAttribute()->attribute( 'data_int1' ) );
        $this->assertSame( 1, $attribute->contentClassAttribute()->storeCount );

        $fresh = $this->textAttribute();
        $type->initializeObjectAttribute( $fresh, false, null );
        $this->assertSame( '', $fresh->attribute( 'data_text' ) );
        $this->assertSame( 0, $fresh->contentClassAttribute()->storeCount );
    }

    public function testTextSerializationKeepsTheRows()
    {
        list( $copy, $xml ) = $this->roundTripClassParameters( $this->classAttribute( 'eztext', array( 'data_int1' => 25 ) ) );
        $this->assertSame( '<datatype-parameters><text-column-count>25</text-column-count></datatype-parameters>', $xml );
        $this->assertSame( 25, $copy->attribute( eZTextType::COLS_FIELD ) );

        $dom = new DOMDocument();
        $dom->loadXML( '<attribute><datatype-parameters><text-column-count>abc</text-column-count></datatype-parameters></attribute>' );
        $classAttribute = $this->classAttribute( 'eztext' );
        $this->dataType( 'eztext' )->unserializeContentClassAttribute( $classAttribute, $dom->documentElement, $dom->documentElement->firstChild );
        $this->assertSame( eZTextType::COLS_DEFAULT, $classAttribute->attribute( eZTextType::COLS_FIELD ) );

        $dom->loadXML( '<attribute><datatype-parameters/></attribute>' );
        $this->dataType( 'eztext' )->unserializeContentClassAttribute( $classAttribute, $dom->documentElement, $dom->documentElement->firstChild );
        $this->assertSame( eZTextType::COLS_DEFAULT, $classAttribute->attribute( eZTextType::COLS_FIELD ) );
    }

    public function testTextContentAndSimpleInsertion()
    {
        $type = $this->dataType( 'eztext' );
        $attribute = $this->textAttribute();
        $result = null;
        $this->assertTrue( $type->insertSimpleString( null, 1, 'eng-GB', $attribute, "Para 1\n\nPara 2", $result ) );
        $this->assertTrue( $result['require_storage'] );
        $this->assertSame( "Para 1\n\nPara 2", $type->objectAttributeContent( $attribute ) );
        $this->assertSame( "Para 1\n\nPara 2", $type->metaData( $attribute ) );
        $this->assertSame( "Para 1\n\nPara 2", $type->title( $attribute ) );
        $this->assertSame( "Para 1\n\nPara 2", $type->toString( $attribute ) );
        $this->assertNotFalse( $type->fromString( $attribute, 'replaced' ) );
        $this->assertSame( 'replaced', $attribute->attribute( 'data_text' ) );
        $this->assertTrue( $type->isIndexable() );
        $this->assertTrue( $type->isInformationCollector() );
        $this->assertTrue( $type->isSimpleStringInsertionSupported() );
        $this->assertTrue( $type->supportsBatchInitializeObjectAttribute() );
    }
}
