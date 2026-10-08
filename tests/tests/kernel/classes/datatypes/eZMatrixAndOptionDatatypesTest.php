<?php
/**
 * The matrix (ezmatrix) and option (ezoption) datatypes as the content and class edit views, the shop and the
 * package system use them: required input, cells and options posted by the edit form, the custom actions (new
 * row, new option, remove selected), class settings (columns, default rows, default option name), text export
 * and import, product option information and the package serialization of class and object.
 *
 * No database: the class attribute is held in memory and store() only counts (see eZDatatypeTestFixtures.php).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group datatypes
 */

require_once __DIR__ . '/eZDatatypeTestFixtures.php';

class eZMatrixAndOptionDatatypesTest extends eZDatatypeTestCase
{
    // ---------------------------------------------------------------- ezmatrix

    private function matrixClass( array $columns = array( 'Name', 'Value' ), $defaultRows = 2, array $extra = array() )
    {
        $definition = new eZMatrixDefinition();
        foreach ( $columns as $column )
            $definition->addColumn( $column );
        return $this->classAttribute( 'ezmatrix', array_merge( array( 'data_int1' => $defaultRows, 'data_text5' => $definition->xmlString() ), $extra ) );
    }

    private function newMatrix( $classAttribute = null )
    {
        $classAttribute = $classAttribute ?: $this->matrixClass();
        $attribute = $this->objectAttribute( 'ezmatrix', $classAttribute );
        $this->dataType( 'ezmatrix' )->initializeObjectAttribute( $attribute, false, null );
        return $attribute;
    }

    public function testNewMatrixHasTheClassColumnsAndDefaultRows()
    {
        $attribute = $this->newMatrix( $this->matrixClass( array( 'First name', 'Age', 'First name' ), 3 ) );
        $matrix = $attribute->content();
        $this->assertSame( 3, $matrix->attribute( 'rowCount' ) );
        $this->assertSame( 3, $matrix->attribute( 'columnCount' ) );
        $this->assertSame( array( 'first_name', 'age', 'first_name_2' ), array_column( $matrix->attribute( 'columns' )['sequential'], 'identifier' ) );
        $this->assertSame( array_fill( 0, 9, '' ), $matrix->attribute( 'cells' ) );
        $this->assertFalse( $this->dataType( 'ezmatrix' )->hasObjectAttributeContent( $this->objectAttribute( 'ezmatrix', $this->matrixClass() ) ) );
        $this->assertTrue( $this->dataType( 'ezmatrix' )->hasObjectAttributeContent( $attribute ) );
    }

    public static function defaultRowCountProvider()
    {
        return array( array( '5', 5 ), array( -3, 0 ), array( '99999', eZMatrixType::MAX_DEFAULT_ROWS ), array( 'x', 0 ), array( null, 0 ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('defaultRowCountProvider')]
    public function testDefaultRowCountIsCapped( $value, $expected )
    {
        $this->assertSame( $expected, eZMatrixType::defaultRowCount( $value ) );
    }

    public function testUniqueColumnIdentifier()
    {
        $this->assertSame( 'a', eZMatrixType::uniqueColumnIdentifier( 'a', array() ) );
        $this->assertSame( 'a_2', eZMatrixType::uniqueColumnIdentifier( 'a', array( 'a' => true ) ) );
        $this->assertSame( 'a_3', eZMatrixType::uniqueColumnIdentifier( 'a', array( 'a' => true, 'a_2' => true ) ) );
        $this->assertSame( '', eZMatrixType::uniqueColumnIdentifier( '', array( '' => true ) ) );
    }

    public function testMatrixRequiredInput()
    {
        $type = $this->dataType( 'ezmatrix' );
        $required = $this->objectAttribute( 'ezmatrix', $this->matrixClass( array( 'A' ), 1, array( 'is_required' => 1 ) ) );
        $cases = array(
            array( array(), eZInputValidator::STATE_INVALID ),
            array( array( 'ContentObjectAttribute_ezmatrix_cell_4711' => array( '', ' ' ) ), eZInputValidator::STATE_INVALID ),
            array( array( 'ContentObjectAttribute_ezmatrix_cell_4711' => array( '', 'x' ) ), eZInputValidator::STATE_ACCEPTED ),
            array( array( 'ContentObjectAttribute_ezmatrix_cell_4711' => array( array( 'x' ) ) ), eZInputValidator::STATE_INVALID ),
            array( array( 'ContentObjectAttribute_ezmatrix_cell_4711' => 'x' ), eZInputValidator::STATE_INVALID ),
            array( array( 'ContentObjectAttribute_ezmatrix_cell_4711' => array( '0' ) ), eZInputValidator::STATE_ACCEPTED ),
        );
        foreach ( $cases as $i => list( $post, $expected ) )
            $this->assertSame( $expected, $type->validateObjectAttributeHTTPInput( $this->post( $post ), 'ContentObjectAttribute', $required ), "case $i" );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateObjectAttributeHTTPInput( $this->post( array() ), 'ContentObjectAttribute', $this->objectAttribute( 'ezmatrix', $this->matrixClass() ) ) );
    }

    public function testMatrixFetchStoresTheCellsAsText()
    {
        $type = $this->dataType( 'ezmatrix' );
        $attribute = $this->newMatrix();
        $http = $this->post( array( 'ContentObjectAttribute_ezmatrix_cell_4711' => array( 'Ada', '36', array( 'nested' ), 1.5 ) ) );
        $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute ) );
        $matrix = $attribute->content();
        $this->assertSame( array( 'Ada', '36', '', '1.5' ), $matrix->attribute( 'cells' ) );
        $this->assertSame( array( 'Ada', '36' ), $matrix->attribute( 'rows' )['sequential'][0]['columns'] );
        $this->assertStringContainsString( '<c>Ada</c>', $attribute->attribute( 'data_text' ) );
        $this->assertSame( array( array( 'id' => 'name', 'text' => 'Ada' ), array( 'id' => 'name', 'text' => '' ),
                                  array( 'id' => 'value', 'text' => '36' ), array( 'id' => 'value', 'text' => '1.5' ) ),
                           $type->metaData( $attribute ) );
        $this->assertSame( 'Ada|36&|1.5', $type->toString( $attribute ) );

        // a post without the cells changes nothing
        $before = $attribute->attribute( 'data_text' );
        $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $this->post( array( 'ContentObjectAttribute_ezmatrix_cell_4711' => 'x' ) ), 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( $before, $attribute->attribute( 'data_text' ) );
    }

    public function testMatrixCellsWithCharactersXmlCannotHoldAreReadBack()
    {
        $type = $this->dataType( 'ezmatrix' );
        $attribute = $this->newMatrix( $this->matrixClass( array( 'A' ), 1 ) );
        $type->fetchObjectAttributeHTTPInput( $this->post( array( 'ContentObjectAttribute_ezmatrix_cell_4711' => array( "bell\x07 & <tag>" ) ) ), 'ContentObjectAttribute', $attribute );
        $copy = $this->objectAttribute( 'ezmatrix', $this->matrixClass( array( 'A' ), 1 ), array( 'data_text' => $attribute->attribute( 'data_text' ) ) );
        $this->assertSame( array( 'bell & <tag>' ), $copy->content()->attribute( 'cells' ) );
    }

    public function testMatrixNewRowAndRemoveSelectedActions()
    {
        $type = $this->dataType( 'ezmatrix' );
        $attribute = $this->newMatrix( $this->matrixClass( array( 'A' ), 1 ) );
        $type->fetchObjectAttributeHTTPInput( $this->post( array( 'ContentObjectAttribute_ezmatrix_cell_4711' => array( 'one' ) ) ), 'ContentObjectAttribute', $attribute );

        $type->customObjectAttributeHTTPAction( $this->post( array( 'ContentObjectAttribute_data_matrix_add_count_4711' => '2' ) ), 'new_row', $attribute, array() );
        $this->assertSame( array( 'one', '', '' ), $attribute->content()->attribute( 'cells' ) );
        $this->assertSame( 1, $attribute->storeCount );

        // insert before the ticked row
        $type->customObjectAttributeHTTPAction( $this->post( array( 'ContentObjectAttribute_data_matrix_remove_4711' => array( '0' ) ) ), 'new_row', $attribute, array() );
        $this->assertSame( array( '', 'one', '', '' ), $attribute->content()->attribute( 'cells' ) );

        // the count is limited
        $type->customObjectAttributeHTTPAction( $this->post( array( 'ContentObjectAttribute_data_matrix_add_count_4711' => '100000' ) ), 'new_row', $attribute, array() );
        $this->assertSame( 44, $attribute->content()->attribute( 'rowCount' ) );

        $type->customObjectAttributeHTTPAction( $this->post( array( 'ContentObjectAttribute_data_matrix_remove_4711' => array( '0', '0', 'x', '2', '999' ) ) ), 'remove_selected', $attribute, array() );
        $cells = $attribute->content()->attribute( 'cells' );
        $this->assertSame( 42, count( $cells ) );
        $this->assertSame( 'one', $cells[0] );

        $type->customObjectAttributeHTTPAction( $this->post( array() ), 'remove_selected', $attribute, array() );
        $this->assertSame( 42, $attribute->content()->attribute( 'rowCount' ) );
        $type->customObjectAttributeHTTPAction( $this->post( array() ), 'no_such_action', $attribute, array() );
    }

    public function testMatrixCopyFollowsChangedClassColumns()
    {
        $type = $this->dataType( 'ezmatrix' );
        $original = $this->newMatrix( $this->matrixClass( array( 'A', 'B' ), 1 ) );
        $type->fetchObjectAttributeHTTPInput( $this->post( array( 'ContentObjectAttribute_ezmatrix_cell_4711' => array( 'a1', 'b1' ) ) ), 'ContentObjectAttribute', $original );

        // the class lost column A and got C
        $copy = $this->objectAttribute( 'ezmatrix', $this->matrixClass( array( 'B', 'C' ), 1 ) );
        $type->initializeObjectAttribute( $copy, 2, $original );
        $matrix = $copy->content();
        $this->assertSame( array( 'b', 'c' ), array_column( $matrix->attribute( 'columns' )['sequential'], 'identifier' ) );
        $this->assertSame( array( 'b1', '' ), $matrix->attribute( 'cells' ) );
    }

    public function testMatrixCopyKeepsTheCellsOfColumnsRenamedInPlace()
    {
        $type = $this->dataType( 'ezmatrix' );
        // a published version as a package installed it; every case copies a
        // fresh one, as the copy adjusts the original's matrix in place
        $published = function () use ( $type ) {
            $original = $this->newMatrix( $this->matrixClass( array( 'Specification', 'Value' ), 2 ) );
            $type->fetchObjectAttributeHTTPInput( $this->post( array( 'ContentObjectAttribute_ezmatrix_cell_4711' => array( 'Material', 'Recycled polyester', 'Weight', '480 g' ) ) ), 'ContentObjectAttribute', $original );
            return $original;
        };

        // the class has other identifiers at the same positions (the placeholder
        // columns a class installed without its column definition was left with)
        $copy = $this->objectAttribute( 'ezmatrix', $this->matrixClass( array( 'Col_0', 'Col_1' ), 0 ) );
        $type->initializeObjectAttribute( $copy, 2, $published() );
        $matrix = $copy->content();
        $this->assertSame( array( 'col_0', 'col_1' ), array_column( $matrix->attribute( 'columns' )['sequential'], 'identifier' ) );
        $this->assertSame( array( 'Col_0', 'Col_1' ), array_column( $matrix->attribute( 'columns' )['sequential'], 'name' ) );
        $this->assertSame( array( 'Material', 'Recycled polyester', 'Weight', '480 g' ), $matrix->attribute( 'cells' ) );
        $this->assertStringContainsString( '<c>480 g</c>', $copy->attribute( 'data_text' ) );

        // one column renamed in place, one kept, one added at the end
        $copy = $this->objectAttribute( 'ezmatrix', $this->matrixClass( array( 'Property', 'Value', 'Unit' ), 0 ) );
        $type->initializeObjectAttribute( $copy, 2, $published() );
        $matrix = $copy->content();
        $this->assertSame( array( 'property', 'value', 'unit' ), array_column( $matrix->attribute( 'columns' )['sequential'], 'identifier' ) );
        $this->assertSame( array( 'Material', 'Recycled polyester', '', 'Weight', '480 g', '' ), $matrix->attribute( 'cells' ) );

        // the same columns in another order still follow their identifiers
        $copy = $this->objectAttribute( 'ezmatrix', $this->matrixClass( array( 'Value', 'Specification' ), 0 ) );
        $type->initializeObjectAttribute( $copy, 2, $published() );
        $this->assertSame( array( 'Recycled polyester', 'Material', '480 g', 'Weight' ), $copy->content()->attribute( 'cells' ) );
    }

    public function testMatrixClassFromAPackageWritesItsColumnsToTheStoredDefinition()
    {
        $type = $this->dataType( 'ezmatrix' );
        $dom = new DOMDocument();
        $dom->loadXML( '<attribute><datatype-parameters><default-name/><default-row-count>0</default-row-count><columns>'
                     . '<column name="Specification" identifier="specification" index="0"/><column name="Value" identifier="value" index="1"/>'
                     . '</columns></datatype-parameters></attribute>' );
        // as the package installer has it: a new attribute stored once (with the
        // placeholder columns of an empty definition), then only sync()ed
        $classAttribute = $this->classAttribute( 'ezmatrix', array( 'data_int1' => 0, 'data_text1' => '', 'data_text5' => '' ) );
        $classAttribute->setAttribute( 'data_text5', $classAttribute->content()->xmlString() );
        $this->assertStringContainsString( 'id="col_0"', $classAttribute->attribute( 'data_text5' ) );
        $classAttribute->setHasDirtyData( false );

        $type->unserializeContentClassAttribute( $classAttribute, $dom->documentElement, $dom->documentElement->firstChild );
        $this->assertTrue( $classAttribute->hasDirtyData(), 'sync() after the import must store the columns' );
        $stored = new eZMatrixDefinition();
        $stored->decodeClassAttribute( $classAttribute->attribute( 'data_text5' ) );
        $this->assertSame( array( 'specification', 'value' ), array_column( $stored->attribute( 'columns' ), 'identifier' ) );
        $this->assertSame( array( 'Specification', 'Value' ), array_column( $stored->attribute( 'columns' ), 'name' ) );
    }

    public function testMatrixFromStringCutsAndPadsRowsToTheColumns()
    {
        $type = $this->dataType( 'ezmatrix' );
        $attribute = $this->newMatrix( $this->matrixClass( array( 'A', 'B' ), 0 ) );
        $this->assertTrue( $type->fromString( $attribute, 'a1|b1|extra&a2&a\|3|b\&3' ) );
        $matrix = $attribute->content();
        $this->assertSame( array( 'a1', 'b1', 'a2', '', 'a|3', 'b&3' ), $matrix->attribute( 'cells' ) );
        $this->assertSame( 3, $matrix->attribute( 'rowCount' ) );
        // rows are joined with & after their cells with |, so the | escaped in a cell is escaped once more
        $text = $type->toString( $attribute );
        $this->assertSame( 'a1|b1&a2|&a\\\\|3|b\\&3', $text );
        $copy = $this->newMatrix( $this->matrixClass( array( 'A', 'B' ), 0 ) );
        $type->fromString( $copy, $text );
        $this->assertSame( $matrix->attribute( 'cells' ), $copy->content()->attribute( 'cells' ) );

        $this->assertTrue( $type->fromString( $attribute, '' ) );
        $this->assertSame( 0, $attribute->content()->attribute( 'rowCount' ) );
    }

    public function testMatrixClassColumnsFromTheClassForm()
    {
        $type = $this->dataType( 'ezmatrix' );
        $classAttribute = $this->matrixClass( array( 'Old', 'Other', 'Third' ), 2 );
        $http = $this->post( array( 'ContentClass_ezmatrix_default_num_rows_901' => '7',
                                    'ContentClass_data_ezmatrix_column_name_901' => array( 0 => 'Kept', 1 => 'New name', 2 => array( 'x' ) ),
                                    'ContentClass_data_ezmatrix_column_id_901' => array( 0 => 'old', 1 => '', 2 => 'third' ) ) );
        $this->assertTrue( $type->fetchClassAttributeHTTPInput( $http, 'ContentClass', $classAttribute ) );
        $this->assertSame( 7, $classAttribute->attribute( 'data_int1' ) );
        $columns = $classAttribute->content()->attribute( 'columns' );
        $this->assertSame( array( 'Kept', 'New name', '' ), array_column( $columns, 'name' ) );
        $this->assertSame( array( 'old', 'new_name', '' ), array_column( $columns, 'identifier' ) );
        $this->assertSame( array( 0, 1, 2 ), array_column( $columns, 'index' ) );

        $this->assertTrue( $type->fetchClassAttributeHTTPInput( $this->post( array( 'ContentClass_ezmatrix_default_num_rows_901' => array() ) ), 'ContentClass', $classAttribute ) );
        $this->assertSame( 1, $classAttribute->attribute( 'data_int1' ) );
        $this->assertFalse( $type->fetchClassAttributeHTTPInput( $this->post( array() ), 'ContentClass', $classAttribute ) );
    }

    public function testMatrixClassColumnActions()
    {
        $type = $this->dataType( 'ezmatrix' );
        $classAttribute = $this->matrixClass( array( 'A', 'B', 'C' ) );
        $type->customClassAttributeHTTPAction( $this->post( array() ), 'new_ezmatrix_column', $classAttribute );
        $this->assertSame( 4, count( $classAttribute->content()->attribute( 'columns' ) ) );
        $this->assertSame( 1, $classAttribute->storeCount );

        $type->customClassAttributeHTTPAction( $this->post( array( 'ContentClass_data_ezmatrix_column_remove_901' => array( '0', '2', '2', 'x' ) ) ), 'remove_selected', $classAttribute );
        $columns = $classAttribute->content()->attribute( 'columns' );
        $this->assertSame( array( 'b', 'col_3' ), array_column( $columns, 'identifier' ) );
        $this->assertSame( array( 0, 1 ), array_column( $columns, 'index' ) );
        $type->customClassAttributeHTTPAction( $this->post( array() ), 'remove_selected', $classAttribute );
        $this->assertSame( 2, count( $classAttribute->content()->attribute( 'columns' ) ) );

        $type->preStoreClassAttribute( $classAttribute, 0 );
        $this->assertStringContainsString( 'id="col_3"', $classAttribute->attribute( 'data_text5' ) );
    }

    public function testMatrixClassSerialization()
    {
        $type = $this->dataType( 'ezmatrix' );
        list( $copy, $xml ) = $this->roundTripClassParameters( $this->matrixClass( array( 'Name', 'Value & more' ), 4 ) );
        $this->assertStringContainsString( '<default-row-count>4</default-row-count>', $xml );
        $this->assertStringContainsString( '<column name="Value &amp; more" identifier="value_more" index="1"/>', $xml );
        $this->assertSame( 4, $copy->attribute( 'data_int1' ) );
        $this->assertSame( array( 'name', 'value_more' ), array_column( $copy->content()->attribute( 'columns' ), 'identifier' ) );

        $dom = new DOMDocument();
        $dom->loadXML( '<attribute><datatype-parameters/></attribute>' );
        $classAttribute = $this->matrixClass();
        $type->unserializeContentClassAttribute( $classAttribute, $dom->documentElement, $dom->documentElement->firstChild );
        $this->assertSame( 1, $classAttribute->attribute( 'data_int1' ) );
        $this->assertSame( array(), $classAttribute->content()->attribute( 'columns' ) );
    }

    public function testMatrixObjectSerialization()
    {
        $type = $this->dataType( 'ezmatrix' );
        $attribute = $this->newMatrix( $this->matrixClass( array( 'A' ), 1 ) );
        $type->fetchObjectAttributeHTTPInput( $this->post( array( 'ContentObjectAttribute_ezmatrix_cell_4711' => array( 'cell' ) ) ), 'ContentObjectAttribute', $attribute );
        $node = $type->serializeContentObjectAttribute( null, $attribute );
        $copy = $this->objectAttribute( 'ezmatrix', $this->matrixClass( array( 'A' ), 1 ) );
        $type->unserializeContentObjectAttribute( null, $copy, $node );
        $this->assertSame( array( 'cell' ), $copy->content()->attribute( 'cells' ) );

        // an attribute never stored is exported as an empty matrix
        $node = $type->serializeContentObjectAttribute( null, $this->objectAttribute( 'ezmatrix', $this->matrixClass() ) );
        $this->assertSame( 1, $node->getElementsByTagName( 'ezmatrix' )->length );
        $dom = new DOMDocument();
        $dom->loadXML( '<attribute/>' );
        $copy = $this->objectAttribute( 'ezmatrix', $this->matrixClass(), array( 'data_text' => 'x' ) );
        $type->unserializeContentObjectAttribute( null, $copy, $dom->documentElement );
        $this->assertSame( '', $copy->attribute( 'data_text' ) );
    }

    public function testMatrixBatchInitializationAndStore()
    {
        $type = $this->dataType( 'ezmatrix' );
        $data = $type->batchInitializeObjectAttributeData( $this->matrixClass( array( 'A' ), 2 ) );
        $this->assertStringStartsWith( "'", $data['data_text'] );
        $this->assertStringContainsString( 'ezmatrix', $data['data_text'] );

        $attribute = $this->newMatrix();
        $attribute->setAttribute( 'data_text', '' );
        $type->storeObjectAttribute( $attribute );
        $this->assertStringContainsString( '<ezmatrix', $attribute->attribute( 'data_text' ) );
        $classAttribute = $this->matrixClass( array( 'Z' ) );
        $type->storeClassAttribute( $classAttribute, 0 );
        $this->assertStringContainsString( 'id="z"', $classAttribute->attribute( 'data_text5' ) );
        $this->assertSame( '', $type->title( $attribute ) );
        $this->assertTrue( $type->isIndexable() );
    }

    // ---------------------------------------------------------------- ezoption

    private function postOptions( $name, array $values, array $prices = array(), $ids = null )
    {
        $post = array( 'ContentObjectAttribute_data_option_name_4711' => $name,
                       'ContentObjectAttribute_data_option_id_4711' => $ids === null ? array_keys( $values ) : $ids,
                       'ContentObjectAttribute_data_option_value_4711' => $values );
        if ( $prices )
            $post['ContentObjectAttribute_data_option_additional_price_4711'] = $prices;
        return $this->post( $post );
    }

    public static function optionInputProvider()
    {
        return array(
            'options' => array( array(), 'Size', array( 'S', 'M' ), array( '0', '1.50' ), eZInputValidator::STATE_ACCEPTED ),
            'no options, optional' => array( array(), '', array( '', '' ), array(), eZInputValidator::STATE_ACCEPTED ),
            'no options, required' => array( array( 'is_required' => 1 ), 'Size', array( '' ), array(), eZInputValidator::STATE_INVALID ),
            'no name, required' => array( array( 'is_required' => 1 ), ' ', array( 'S' ), array(), eZInputValidator::STATE_INVALID ),
            'one value missing' => array( array(), 'Size', array( 'S', ' ' ), array(), eZInputValidator::STATE_INVALID ),
            'negative price' => array( array(), 'Size', array( 'S' ), array( '-2.5' ), eZInputValidator::STATE_ACCEPTED ),
            'price with three decimals' => array( array(), 'Size', array( 'S' ), array( '1.505' ), eZInputValidator::STATE_INVALID ),
            'price with letters' => array( array(), 'Size', array( 'S' ), array( '1e3' ), eZInputValidator::STATE_INVALID ),
            'price with a bar' => array( array(), 'Size', array( 'S' ), array( '|5' ), eZInputValidator::STATE_INVALID ),
            'array as value' => array( array(), 'Size', array( array( 'S' ) ), array(), eZInputValidator::STATE_ACCEPTED ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('optionInputProvider')]
    public function testOptionObjectInput( $classFields, $name, $values, $prices, $expected )
    {
        $attribute = $this->objectAttribute( 'ezoption', $classFields );
        $this->assertSame( $expected, $this->dataType( 'ezoption' )->validateObjectAttributeHTTPInput( $this->postOptions( $name, $values, $prices ), 'ContentObjectAttribute', $attribute ) );
    }

    public function testOptionNotPostedDependsOnRequired()
    {
        $type = $this->dataType( 'ezoption' );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateObjectAttributeHTTPInput( $this->post( array() ), 'ContentObjectAttribute', $this->objectAttribute( 'ezoption' ) ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateObjectAttributeHTTPInput( $this->post( array() ), 'ContentObjectAttribute', $this->objectAttribute( 'ezoption', array( 'is_required' => 1 ) ) ) );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateObjectAttributeHTTPInput( $this->post( array() ), 'ContentObjectAttribute', $this->objectAttribute( 'ezoption', array( 'is_required' => 1, 'is_information_collector' => 1 ) ) ) );
    }

    public function testOptionFetchStoreAndText()
    {
        $type = $this->dataType( 'ezoption' );
        $attribute = $this->objectAttribute( 'ezoption' );
        $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $this->postOptions( 'Size', array( 'S', 'M|L' ), array( '0', '2.50' ) ), 'ContentObjectAttribute', $attribute ) );
        $option = $attribute->content();
        $this->assertSame( 'Size', $option->attribute( 'name' ) );
        $this->assertSame( array( 'S', 'M|L' ), array_column( $option->attribute( 'option_list' ), 'value' ) );
        $this->assertTrue( $type->hasObjectAttributeContent( $attribute ) );
        $this->assertSame( 'Size', $type->title( $attribute ) );
        $type->storeObjectAttribute( $attribute );
        $this->assertStringContainsString( '<ezoption>', $attribute->attribute( 'data_text' ) );
        $this->assertSame( $attribute->attribute( 'data_text' ), $type->metaData( $attribute ) );
        $text = $type->toString( $attribute );
        $this->assertSame( 'Size|S|0|M\|L|2.50', $text );

        $copy = $this->objectAttribute( 'ezoption' );
        $this->assertInstanceOf( 'eZOption', $type->fromString( $copy, $text ) );
        $this->assertSame( $text, $type->toString( $this->objectAttribute( 'ezoption', array(), array( 'data_text' => $copy->attribute( 'data_text' ) ) ) ) );
        $this->assertTrue( $type->fromString( $copy, '' ) );

        $info = $type->productOptionInformation( $attribute, 1, null );
        $this->assertSame( array( 'id' => 1, 'name' => 'Size', 'value' => 'M|L', 'additional_price' => '2.50' ), $info );
        $this->assertFalse( $type->productOptionInformation( $attribute, 9, null ) );
    }

    public function testOptionCustomActions()
    {
        $type = $this->dataType( 'ezoption' );
        $attribute = $this->objectAttribute( 'ezoption' );
        $type->fetchObjectAttributeHTTPInput( $this->postOptions( 'Size', array( 'S', 'M' ) ), 'ContentObjectAttribute', $attribute );
        $type->storeObjectAttribute( $attribute );

        $type->customObjectAttributeHTTPAction( $this->post( array() ), 'new_option', $attribute, array() );
        $this->assertSame( 3, count( $attribute->content()->attribute( 'option_list' ) ) );

        $type->customObjectAttributeHTTPAction( $this->post( array( 'ContentObjectAttribute_data_option_remove_4711' => '1' ) ), 'new_option', $attribute, array() );
        $this->assertSame( 4, count( $attribute->content()->attribute( 'option_list' ) ) );

        $ids = array_column( $attribute->content()->attribute( 'option_list' ), 'id' );
        $type->customObjectAttributeHTTPAction( $this->post( array( 'ContentObjectAttribute_data_option_remove_4711' => array( $ids[0], $ids[1] ) ) ), 'remove_selected', $attribute, array() );
        $this->assertSame( 2, count( $attribute->content()->attribute( 'option_list' ) ) );
        $type->customObjectAttributeHTTPAction( $this->post( array() ), 'remove_selected', $attribute, array() );
        $this->assertSame( 2, count( $attribute->content()->attribute( 'option_list' ) ) );
        $this->assertGreaterThanOrEqual( 3, $attribute->storeCount );
    }

    public function testOptionCollection()
    {
        $type = $this->dataType( 'ezoption' );
        $attribute = $this->objectAttribute( 'ezoption' );
        $required = $this->objectAttribute( 'ezoption', array( 'is_required' => 1 ) );
        $cases = array(
            array( array(), $attribute, eZInputValidator::STATE_INVALID ),
            array( array( 'ContentObjectAttribute_data_option_value_4711' => '2' ), $attribute, eZInputValidator::STATE_ACCEPTED ),
            array( array( 'ContentObjectAttribute_data_option_value_4711' => '' ), $attribute, eZInputValidator::STATE_ACCEPTED ),
            array( array( 'ContentObjectAttribute_data_option_value_4711' => '' ), $required, eZInputValidator::STATE_INVALID ),
            array( array( 'ContentObjectAttribute_data_option_value_4711' => 'x' ), $attribute, eZInputValidator::STATE_INVALID ),
            array( array( 'ContentObjectAttribute_data_option_value_4711' => array( 1 ) ), $attribute, eZInputValidator::STATE_INVALID ),
        );
        foreach ( $cases as $i => list( $post, $target, $expected ) )
            $this->assertSame( $expected, $type->validateCollectionAttributeHTTPInput( $this->post( $post ), 'ContentObjectAttribute', $target ), "case $i" );

        $collected = new eZInformationCollectionAttribute( array( 'data_int' => null ) );
        $this->assertTrue( $type->fetchCollectionAttributeHTTPInput( null, $collected, $this->post( array( 'ContentObjectAttribute_data_option_value_4711' => '3' ) ), 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( 3, $collected->attribute( 'data_int' ) );
        $this->assertFalse( $type->fetchCollectionAttributeHTTPInput( null, $collected, $this->post( array( 'ContentObjectAttribute_data_option_value_4711' => 'x' ) ), 'ContentObjectAttribute', $attribute ) );
        $this->assertFalse( $type->fetchCollectionAttributeHTTPInput( null, $collected, $this->post( array() ), 'ContentObjectAttribute', $attribute ) );
    }

    public function testOptionClassDefaultNameAndInitialization()
    {
        $type = $this->dataType( 'ezoption' );
        $classAttribute = $this->classAttribute( 'ezoption' );
        $this->assertFalse( $type->fetchClassAttributeHTTPInput( $this->post( array() ), 'ContentClass', $classAttribute ) );
        $this->assertTrue( $type->fetchClassAttributeHTTPInput( $this->post( array( 'ContentClass_ezoption_default_name_901' => 'Colour' ) ), 'ContentClass', $classAttribute ) );
        $this->assertSame( 'Colour', $classAttribute->attribute( 'data_text1' ) );
        $this->assertTrue( $type->fetchClassAttributeHTTPInput( $this->post( array( 'ContentClass_ezoption_default_name_901' => array( 'x' ) ) ), 'ContentClass', $classAttribute ) );
        $this->assertSame( '', $classAttribute->attribute( 'data_text1' ) );

        $new = $this->objectAttribute( 'ezoption', array( 'data_text1' => 'Colour' ) );
        $type->initializeObjectAttribute( $new, false, null );
        $this->assertSame( 'Colour', $new->content()->attribute( 'name' ) );
        $this->assertStringContainsString( 'Colour', $new->attribute( 'data_text' ) );

        $copy = $this->objectAttribute( 'ezoption' );
        $type->initializeObjectAttribute( $copy, 2, $new );
        $this->assertSame( $new->attribute( 'data_text' ), $copy->attribute( 'data_text' ) );

        list( $copiedClass, $xml ) = $this->roundTripClassParameters( $this->classAttribute( 'ezoption', array( 'data_text1' => 'Colour & size' ) ) );
        $this->assertSame( '<datatype-parameters><default-value>Colour &amp; size</default-value></datatype-parameters>', $xml );
        $this->assertSame( 'Colour & size', $copiedClass->attribute( 'data_text1' ) );

        $data = $type->batchInitializeObjectAttributeData( $this->classAttribute( 'ezoption', array( 'data_text1' => 'Colour' ) ) );
        $this->assertStringContainsString( 'Colour', $data['data_text'] );
    }

    public function testOptionObjectSerialization()
    {
        $type = $this->dataType( 'ezoption' );
        $attribute = $this->objectAttribute( 'ezoption' );
        $type->fetchObjectAttributeHTTPInput( $this->postOptions( 'Size', array( 'S' ) ), 'ContentObjectAttribute', $attribute );
        $type->storeObjectAttribute( $attribute );
        $node = $type->serializeContentObjectAttribute( null, $attribute );
        $copy = $this->objectAttribute( 'ezoption' );
        $type->unserializeContentObjectAttribute( null, $copy, $node );
        $this->assertSame( 'Size', $copy->content()->attribute( 'name' ) );

        $node = $type->serializeContentObjectAttribute( null, $this->objectAttribute( 'ezoption', array(), array( 'data_text' => 'broken <' ) ) );
        $this->assertSame( 0, $node->getElementsByTagName( 'ezoption' )->length );

        $dom = new DOMDocument();
        $dom->loadXML( '<attribute><data-text>&lt;ezoption&gt;&lt;name&gt;Old&lt;/name&gt;&lt;options/&gt;&lt;/ezoption&gt;</data-text></attribute>' );
        $old = $this->objectAttribute( 'ezoption' );
        $type->unserializeContentObjectAttribute( null, $old, $dom->documentElement );
        $this->assertSame( 'Old', $old->content()->attribute( 'name' ) );

        foreach ( array( '<attribute/>', '<attribute> </attribute>' ) as $xml )
        {
            $dom->loadXML( $xml );
            $empty = $this->objectAttribute( 'ezoption', array(), array( 'data_text' => 'x' ) );
            $type->unserializeContentObjectAttribute( null, $empty, $dom->documentElement );
            $this->assertSame( trim( $dom->documentElement->textContent ), trim( $empty->attribute( 'data_text' ) ) );
        }
    }

    public function testOptionPostedListAndString()
    {
        $http = $this->post( array( 'a' => 'one', 'b' => array( 'x', array( 'y' ), 3 ), 'c' => array( 'z' ) ) );
        $this->assertSame( array( 'one' ), eZOptionType::postedList( $http, 'a' ) );
        $this->assertSame( array( 'x', '', '3' ), eZOptionType::postedList( $http, 'b' ) );
        $this->assertSame( array(), eZOptionType::postedList( $http, 'missing' ) );
        $this->assertSame( 'one', eZOptionType::postedString( $http, 'a' ) );
        $this->assertSame( '', eZOptionType::postedString( $http, 'c' ) );
        $this->assertSame( '', eZOptionType::postedString( $http, 'missing' ) );
    }
}
