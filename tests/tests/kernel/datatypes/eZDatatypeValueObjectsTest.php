<?php
/**
 * Tests of the value objects behind several datatypes, which store their content as XML: eZAuthor, eZOption,
 * eZRangeOption, eZMultiOption and eZMatrixDefinition (stored XML written and read back, adding and removing
 * entries, broken input), and the ISBN checks of eZISBNType and eZISBN13 (checksums and the ISBN-10 to ISBN-13
 * conversion; the registration ranges, which need the database, are not used).
 *
 * No database and no siteaccess.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group datatypes
 */

class eZDatatypeValueObjectsTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
    }

    // ---------------------------------------------------------------- eZAuthor

    public function testAuthorListIsWrittenAndReadBack()
    {
        $authors = new eZAuthor();
        $authors->addAuthor( -1, 'Ada Example', 'ada@t1.example.invalid' );
        $authors->addAuthor( -1, 'Bob <"&\'> Example', 'bob@t1.example.invalid' );
        $copy = new eZAuthor();
        $copy->decodeXML( $authors->xmlString() );
        $list = $copy->attribute( 'author_list' );
        $this->assertCount( 2, $list );
        $this->assertSame( 'Ada Example', $list[0]['name'] );
        $this->assertSame( 'Bob <"&\'> Example', $list[1]['name'] );
        $this->assertSame( 'bob@t1.example.invalid', $list[1]['email'] );
        $this->assertFalse( $copy->attribute( 'is_empty' ) );
        $this->assertSame( "Ada Example ada@t1.example.invalid\nBob <\"&'> Example bob@t1.example.invalid\n", $copy->metaData() );
    }

    public function testAuthorIdsCountOnFromTheLastOne()
    {
        $authors = new eZAuthor();
        $authors->addAuthor( 5, 'A', 'a@t1.example.invalid' );
        $authors->addAuthor( -1, 'B', 'b@t1.example.invalid' );
        $list = $authors->attribute( 'author_list' );
        $this->assertSame( 6, $list[1]['id'] );
    }

    public function testRemoveAuthorsRemovesExactlyTheGivenIds()
    {
        $authors = new eZAuthor();
        foreach ( array( 'A', 'B', 'C', 'D' ) as $i => $name )
            $authors->addAuthor( $i + 1, $name, strtolower( $name ) . '@t1.example.invalid' );
        $authors->removeAuthors( array( '2', 3 ) );
        $names = array_column( $authors->attribute( 'author_list' ), 'name' );
        $this->assertSame( array( 'A', 'D' ), $names );
        $authors->removeAuthors( 'no such id' );
        $this->assertCount( 2, $authors->attribute( 'author_list' ) );
    }

    public function testAuthorTextLosesCharactersXmlCannotHold()
    {
        $authors = new eZAuthor();
        $authors->addAuthor( -1, "bell\x07 name", array( 'not text' ) );
        $copy = new eZAuthor();
        $copy->decodeXML( $authors->xmlString() );
        $list = $copy->attribute( 'author_list' );
        $this->assertSame( 'bell name', $list[0]['name'] );
        $this->assertSame( '', $list[0]['email'] );
    }

    public static function brokenXmlProvider()
    {
        return array( 'empty' => array( '' ), 'not xml' => array( 'no xml <' ), 'null' => array( null ), 'other root' => array( '<x/>' ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('brokenXmlProvider')]
    public function testAuthorFromBrokenXmlIsEmpty( $xml )
    {
        $authors = new eZAuthor();
        $authors->decodeXML( $xml );
        $this->assertTrue( $authors->attribute( 'is_empty' ) );
    }

    // ---------------------------------------------------------------- eZOption

    public function testOptionSetIsWrittenAndReadBack()
    {
        $option = new eZOption( 'Size & colour' );
        $option->addOption( array( 'value' => 'Small', 'additional_price' => '0' ) );
        $option->addOption( array( 'value' => 'Large <XL>', 'additional_price' => '12.50' ) );
        $copy = new eZOption( '' );
        $copy->decodeXML( $option->xmlString() );
        $this->assertSame( 'Size & colour', $copy->attribute( 'name' ) );
        $list = $copy->attribute( 'option_list' );
        $this->assertSame( array( 'Small', 'Large <XL>' ), array_column( $list, 'value' ) );
        $this->assertSame( array( '0', '12.50' ), array_column( $list, 'additional_price' ) );
    }

    /**
     * Option names and values are stored in CDATA sections; a value containing the end marker "]]>" must not break
     * the stored XML, or the whole option set reads back empty.
     */
    public function testOptionValueWithTheCdataEndMarker()
    {
        $option = new eZOption( 'a]]>b' );
        $option->addOption( array( 'value' => 'x]]>y' ) );
        $copy = new eZOption( '' );
        $copy->decodeXML( $option->xmlString() );
        $this->assertSame( 'a]]>b', $copy->attribute( 'name' ) );
        $this->assertSame( array( 'x]]>y' ), array_column( $copy->attribute( 'option_list' ), 'value' ) );
    }

    public function testInsertAndRemoveOptions()
    {
        $option = new eZOption( 'o' );
        $option->addOption( array( 'value' => 'a' ) );
        $option->addOption( array( 'value' => 'c' ) );
        $option->insertOption( array( 'value' => 'b' ), 1 );
        $option->insertOption( array( 'value' => 'z' ), 'not a number' );
        $this->assertSame( array( 'a', 'b', 'c', 'z' ), array_column( $option->attribute( 'option_list' ), 'value' ) );
        $option->removeOptions( array( 3, '0', 99, 'x' ) );
        $this->assertSame( array( 'b', 'c' ), array_column( $option->attribute( 'option_list' ), 'value' ) );
    }

    public function testEmptyOptionXmlGivesTwoEmptyOptions()
    {
        $option = new eZOption( '' );
        $option->decodeXML( '' );
        $this->assertCount( 2, $option->attribute( 'option_list' ) );
        $broken = new eZOption( 'x' );
        $broken->decodeXML( '<ezoption><name>' );
        $this->assertSame( array(), $broken->attribute( 'option_list' ) );
        $this->assertSame( '', $broken->attribute( 'name' ) );
    }

    // ---------------------------------------------------------------- eZRangeOption

    public static function rangeProvider()
    {
        return array(
            'integers'        => array( 1, 5, 1, array( 1, 2, 3, 4, 5 ) ),
            'step two'        => array( 0, 9, 2, array( 0, 2, 4, 6, 8 ) ),
            'single'          => array( 3, 3, 1, array( 3 ) ),
            'stop below'      => array( 5, 1, 1, array() ),
            'decimal step'    => array( 0, 0.3, 0.1, array( 0, 0.1, 0.2, 0.3 ) ),
            'decimal to one'  => array( 0, 1, 0.25, array( 0, 0.25, 0.5, 0.75, 1 ) ),
            'negative step'   => array( 1, 5, -1, array() ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('rangeProvider')]
    public function testRangeOptionValues( $start, $stop, $step, $values )
    {
        $range = new eZRangeOption( 'r' );
        $range->setStartValue( $start );
        $range->setStopValue( $stop );
        $range->setStepValue( $step );
        $copy = new eZRangeOption( '' );
        $copy->decodeXML( $range->xmlString() );
        $actual = array_column( $copy->attribute( 'option_list' ), 'value' );
        $this->assertCount( count( $values ), $actual, implode( ',', $actual ) );
        foreach ( $values as $i => $value )
            $this->assertEqualsWithDelta( $value, $actual[$i], 1e-9 );
    }

    public function testRangeOptionNameAndLimits()
    {
        $range = new eZRangeOption( 'Rock & roll' );
        $range->setStartValue( 1 );
        $range->setStopValue( 2 );
        $range->setStepValue( 1 );
        $copy = new eZRangeOption( '' );
        $copy->decodeXML( $range->xmlString() );
        $this->assertSame( 'Rock & roll', $copy->attribute( 'name' ) );
        $this->assertFalse( eZRangeOption::rangeCount( 0, 1e9, 1 ) );
        $this->assertFalse( eZRangeOption::rangeCount( 'a', 1, 1 ) );
        $this->assertSame( 0, eZRangeOption::rangeCount( 2, 1, 1 ) );
        $this->assertSame( 11, eZRangeOption::rangeCount( 0, 1, 0.1 ) );
    }

    // ---------------------------------------------------------------- eZMultiOption

    public function testMultiOptionIsWrittenAndReadBack()
    {
        $multi = new eZMultiOption( 'Pizza' );
        $first = $multi->addMultiOption( 'Size', 1, false );
        $multi->addOption( $first, 0, 'Small', 0 );
        $multi->addOption( $first, 0, 'Large', 3 );
        $second = $multi->addMultiOption( 'Crust', 2, false );
        $multi->addOption( $second, 0, 'Thin & crispy', 0 );
        $copy = new eZMultiOption( '' );
        $copy->decodeXML( $multi->xmlString() );
        $this->assertSame( 'Pizza', $copy->attribute( 'name' ) );
        $list = array_values( $copy->attribute( 'multioption_list' ) );
        $this->assertSame( array( 'Size', 'Crust' ), array_column( $list, 'name' ) );
        $this->assertSame( array( 'Small', 'Large' ), array_column( $list[0]['optionlist'], 'value' ) );
        $this->assertSame( array( 'Thin & crispy' ), array_column( $list[1]['optionlist'], 'value' ) );
    }

    // ---------------------------------------------------------------- eZMatrixDefinition

    public function testMatrixDefinitionIsWrittenAndReadBack()
    {
        $definition = new eZMatrixDefinition();
        $definition->addColumn( 'First name' );
        $definition->addColumn( 'Price & tax' );
        $definition->addColumn( '0' );
        $copy = new eZMatrixDefinition();
        $copy->decodeClassAttribute( $definition->xmlString() );
        $columns = $copy->attribute( 'columns' );
        $this->assertSame( array( 'First name', 'Price & tax', '0' ), array_column( $columns, 'name' ) );
        $this->assertSame( array( 0, 1, 2 ), array_column( $columns, 'index' ) );
        $this->assertSame( 'first_name', $columns[0]['identifier'] );
        $this->assertSame( count( $columns ), count( array_unique( array_column( $columns, 'identifier' ) ) ) );
    }

    public function testMatrixColumnsWithTheSameNameGetDistinctIdentifiers()
    {
        $definition = new eZMatrixDefinition();
        $definition->addColumn( 'Name' );
        $definition->addColumn( 'Name' );
        $ids = array_column( $definition->attribute( 'columns' ), 'identifier' );
        $this->assertNotSame( $ids[0], $ids[1] );
    }

    public function testMatrixRemoveColumnRenumbers()
    {
        $definition = new eZMatrixDefinition();
        foreach ( array( 'a', 'b', 'c' ) as $name )
            $definition->addColumn( $name );
        $this->assertTrue( $definition->removeColumn( 1 ) );
        $this->assertFalse( $definition->removeColumn( 7 ) );
        $columns = $definition->attribute( 'columns' );
        $this->assertSame( array( 'a', 'c' ), array_column( $columns, 'name' ) );
        $this->assertSame( array( 0, 1 ), array_column( $columns, 'index' ) );
    }

    public function testMatrixDefinitionDefaultsAndBrokenXml()
    {
        $definition = new eZMatrixDefinition();
        $definition->decodeClassAttribute( '' );
        $this->assertCount( 2, $definition->attribute( 'columns' ) );
        $broken = new eZMatrixDefinition();
        $broken->decodeClassAttribute( '<ezmatrix><column-name>' );
        $this->assertSame( array(), $broken->attribute( 'columns' ) );
    }

    // ---------------------------------------------------------------- ISBN

    public static function isbn10Provider()
    {
        return array(
            'valid'          => array( '0306406152', true ),
            'valid x'        => array( '080442957X', true ),
            'lower x'        => array( '080442957x', true ),
            'bad checksum'   => array( '0306406153', false ),
            'x not last'     => array( '03064X6152', false ),
            'too short'      => array( '030640615', false ),
            'letters'        => array( 'abcdefghij', false ),
            'not a string'   => array( 306406152, false ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('isbn10Provider')]
    public function testIsbn10Checksum( $isbn, $valid )
    {
        $type = new eZISBNType();
        $this->assertSame( $valid, $type->validateISBNChecksum( $isbn ) );
    }

    public static function isbn13Provider()
    {
        return array(
            'valid'           => array( '9780306406157', true ),
            'with dashes'     => array( '978-0-306-40615-7', true ),
            'with spaces'     => array( '978 0 306 40615 7', true ),
            '979 prefix'      => array( '9791090636071', true ),
            'bad checksum'    => array( '9780306406158', false ),
            'wrong prefix'    => array( '9770306406157', false ),
            'too long'        => array( '97803064061570', false ),
            'letter'          => array( '978030640615a', false ),
            'empty'           => array( '', false ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('isbn13Provider')]
    public function testIsbn13Checksum( $isbn, $valid )
    {
        $isbn13 = new eZISBN13();
        $error = '';
        $this->assertSame( $valid, $isbn13->validateISBN13Checksum( $isbn, $error ) );
        if ( !$valid && $isbn !== '' )
            $this->assertNotSame( '', (string)$error );
    }

    public function testIsbn13ChecksumErrorNamesTheRightDigit()
    {
        $isbn13 = new eZISBN13();
        $error = '';
        $isbn13->validateISBN13Checksum( '9780306406150', $error );
        $this->assertStringContainsString( '7', (string)$error );
    }

    public static function conversionProvider()
    {
        return array(
            array( '0306406152', '9780306406157' ),
            array( '080442957X', '9780804429573' ),
            array( '0131103628', '9780131103627' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('conversionProvider')]
    public function testIsbn10ToIsbn13( $isbn10, $isbn13 )
    {
        $this->assertSame( $isbn13, eZISBNType::convertISBN10toISBN13( $isbn10 ) );
        $checker = new eZISBN13();
        $error = '';
        $this->assertTrue( $checker->validateISBN13Checksum( $isbn13, $error ) );
    }
}
