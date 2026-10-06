<?php
/**
 * The ISBN (ezisbn) and author list (ezauthor) datatypes as the content edit view and the package system use them:
 * the four-field ISBN-10 form and the ISBN-13 field (as far as it needs no registration ranges, which live in the
 * database), checksums, the ISBN-10 to ISBN-13 conversion, content arrays, class settings; the author rows the
 * edit form posts (required, names, addresses, removed rows, the limit), the custom actions, titles, text export
 * and import and the package serialization.
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

class eZISBNAndAuthorDatatypesTest extends eZDatatypeTestCase
{
    // ---------------------------------------------------------------- ezisbn, ISBN-10 form

    private function postIsbn10( $f1, $f2, $f3, $f4 )
    {
        return $this->post( array( 'ContentObjectAttribute_isbn_field1_4711' => $f1, 'ContentObjectAttribute_isbn_field2_4711' => $f2,
                                   'ContentObjectAttribute_isbn_field3_4711' => $f3, 'ContentObjectAttribute_isbn_field4_4711' => $f4 ) );
    }

    public static function isbn10Provider()
    {
        return array(
            'valid' => array( '0', '306', '40615', '2', eZInputValidator::STATE_ACCEPTED ),
            'check digit X' => array( '0', '8044', '2957', 'x', eZInputValidator::STATE_ACCEPTED ),
            'two digit group' => array( '91', '7054', '940', '0', eZInputValidator::STATE_ACCEPTED ),
            'six digit group' => array( '999360', '00', '0', '7', eZInputValidator::STATE_INVALID ),
            'bad checksum' => array( '0', '306', '40615', '3', eZInputValidator::STATE_INVALID ),
            'too short' => array( '0', '306', '4061', '2', eZInputValidator::STATE_INVALID ),
            'letters' => array( '0', '3a6', '40615', '2', eZInputValidator::STATE_INVALID ),
            'X inside' => array( '0', '306', '4061X', '2', eZInputValidator::STATE_INVALID ),
            'empty field' => array( '0', '', '40615', '2', eZInputValidator::STATE_INVALID ),
            'array posted' => array( array( '0' ), '306', '40615', '2', eZInputValidator::STATE_INVALID ),
            'all empty, optional' => array( '', '', '', '', eZInputValidator::STATE_ACCEPTED ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('isbn10Provider')]
    public function testIsbn10Form( $f1, $f2, $f3, $f4, $expected )
    {
        $attribute = $this->objectAttribute( 'ezisbn', array( 'data_int1' => 0 ) );
        $this->assertSame( $expected, $this->dataType( 'ezisbn' )->validateObjectAttributeHTTPInput( $this->postIsbn10( $f1, $f2, $f3, $f4 ), 'ContentObjectAttribute', $attribute ) );
    }

    public function testIsbn10RequiredAndFetch()
    {
        $type = $this->dataType( 'ezisbn' );
        $required = $this->objectAttribute( 'ezisbn', array( 'data_int1' => 0, 'is_required' => 1 ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateObjectAttributeHTTPInput( $this->postIsbn10( '', '', '', '' ), 'ContentObjectAttribute', $required ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateObjectAttributeHTTPInput( $this->post( array() ), 'ContentObjectAttribute', $required ) );

        $attribute = $this->objectAttribute( 'ezisbn', array( 'data_int1' => 0 ), array( 'data_text' => 'kept' ) );
        $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $this->postIsbn10( '', '', '', '' ), 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( 'kept', $attribute->attribute( 'data_text' ) );
        $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $this->postIsbn10( '0', '8044', '2957', 'x' ), 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( '0-8044-2957-X', $attribute->attribute( 'data_text' ) );

        $content = $type->objectAttributeContent( $attribute );
        $this->assertSame( array( 'field1' => '0', 'field2' => '8044', 'field3' => '2957', 'field4' => 'X', 'value' => '0-8044-2957-X',
                                  'value_without_hyphens' => '080442957X', 'value_with_spaces' => '0 8044 2957 X' ), $content );
        $this->assertTrue( $type->hasObjectAttributeContent( $attribute ) );
        $this->assertSame( '0-8044-2957-X', $type->title( $attribute ) );
        $this->assertSame( '0-8044-2957-X', $type->metaData( $attribute ) );
        $this->assertSame( '0-8044-2957-X', $type->toString( $attribute ) );
    }

    public static function checksumProvider()
    {
        return array(
            array( '0306406152', true ), array( '080442957X', true ), array( '080442957x', true ), array( '0306406153', false ),
            array( 'X306406152', false ), array( 'ABCDE12345', false ), array( '03064X6152', false ), array( '030640615', false ), array( '03064061522', false ), array( '', false ), array( null, false ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('checksumProvider')]
    public function testIsbn10Checksum( $number, $expected )
    {
        $this->assertSame( $expected, $this->dataType( 'ezisbn' )->validateISBNChecksum( $number ) );
    }

    public function testIsbn10IsConvertedToIsbn13()
    {
        $this->assertSame( '9780306406157', eZISBNType::convertISBN10toISBN13( '0306406152' ) );
        $this->assertSame( '9780804429573', eZISBNType::convertISBN10toISBN13( '080442957X' ) );
        $error = null;
        $this->assertTrue( ( new eZISBN13() )->validateISBN13Checksum( eZISBNType::convertISBN10toISBN13( '9531571058' ), $error ) );
    }

    // ---------------------------------------------------------------- ezisbn, ISBN-13 field

    public static function isbn13WithoutRangesProvider()
    {
        return array(
            'ISBN-10 entered, valid' => array( '0-306-40615-2', eZInputValidator::STATE_ACCEPTED ),
            'ISBN-10 entered, bad checksum' => array( '0-306-40615-3', eZInputValidator::STATE_INVALID ),
            'wrong prefix' => array( '977-0-306-40615-7', eZInputValidator::STATE_INVALID ),
            'bad checksum' => array( '978-0-306-40615-8', eZInputValidator::STATE_INVALID ),
            'too long' => array( '978-0-306-40615-77', eZInputValidator::STATE_INVALID ),
            'letters' => array( '978-0-306-4061A-7', eZInputValidator::STATE_INVALID ),
            'ten characters with letters' => array( 'ABCDE-12345', eZInputValidator::STATE_INVALID ),
            'empty, optional' => array( '', eZInputValidator::STATE_ACCEPTED ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('isbn13WithoutRangesProvider')]
    public function testIsbn13Field( $number, $expected )
    {
        $attribute = $this->objectAttribute( 'ezisbn', array( 'data_int1' => 1 ) );
        $this->assertSame( $expected, $this->dataType( 'ezisbn' )->validateObjectAttributeHTTPInput( $this->post( array( 'ContentObjectAttribute_isbn_13_4711' => $number ) ), 'ContentObjectAttribute', $attribute ) );
        if ( $expected === eZInputValidator::STATE_INVALID )
            $this->assertNotEmpty( $attribute->validationError() );
    }

    public function testIsbn13RequiredEmptyFetchAndContent()
    {
        $type = $this->dataType( 'ezisbn' );
        $required = $this->objectAttribute( 'ezisbn', array( 'data_int1' => 1, 'is_required' => 1 ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateObjectAttributeHTTPInput( $this->post( array() ), 'ContentObjectAttribute', $required ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateObjectAttributeHTTPInput( $this->post( array( 'ContentObjectAttribute_isbn_13_4711' => array( '978' ) ) ), 'ContentObjectAttribute', $required ) );

        $attribute = $this->objectAttribute( 'ezisbn', array( 'data_int1' => 1 ), array( 'data_text' => '978-0-306-40615-7' ) );
        $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $this->post( array() ), 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( '978-0-306-40615-7', $attribute->attribute( 'data_text' ) );
        $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $this->post( array( 'ContentObjectAttribute_isbn_13_4711' => '' ) ), 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( '', $attribute->attribute( 'data_text' ) );
        $this->assertFalse( $type->hasObjectAttributeContent( $attribute ) );

        // an ISBN-10 the validation refused is kept as entered, for the form to show it again
        $attribute->IsValid = eZInputValidator::STATE_INVALID;
        $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $this->post( array( 'ContentObjectAttribute_isbn_13_4711' => '0-306-40615-3' ) ), 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( '0-306-40615-3', $attribute->attribute( 'data_text' ) );

        $attribute->setAttribute( 'data_text', '978-0-306-40615-7' );
        $content = $type->objectAttributeContent( $attribute );
        $this->assertSame( '978', $content['prefix'] );
        $this->assertSame( array( '0', '306', '40615', '7' ), array( $content['field1'], $content['field2'], $content['field3'], $content['field4'] ) );
        $this->assertSame( '9780306406157', $content['value_without_hyphens'] );
        $this->assertSame( '978 0 306 40615 7', $content['value_with_spaces'] );

        $empty = $type->objectAttributeContent( $this->objectAttribute( 'ezisbn', array( 'data_int1' => 1 ), array( 'data_text' => null ) ) );
        $this->assertSame( '', $empty['value'] );
        $this->assertSame( '', $empty['field4'] );
    }

    public function testIsbnClassSettings()
    {
        $type = $this->dataType( 'ezisbn' );
        $classAttribute = $this->classAttribute( 'ezisbn', array( 'data_int1' => null ) );
        $type->initializeClassAttribute( $classAttribute );
        $this->assertSame( 1, $classAttribute->attribute( 'data_int1' ) );

        $content = $type->classAttributeContent( $this->classAttribute( 'ezisbn', array( 'data_int1' => 0 ) ) );
        $this->assertSame( 0, $content['ISBN13'] );
        $this->assertInstanceOf( 'eZISBN13', $content['ranges'] );

        $classAttribute = $this->classAttribute( 'ezisbn', array( 'data_int1' => 0 ) );
        $this->assertTrue( $type->fetchClassAttributeHTTPInput( $this->post( array( 'ContentClass_ezisbn_13_value_901_exists' => 1, 'ContentClass_ezisbn_13_value_901' => 'on' ) ), 'ContentClass', $classAttribute ) );
        $this->assertSame( 1, $classAttribute->content()['ISBN13'] );
        $this->assertSame( 1, $classAttribute->storeCount );
        $type->preStoreClassAttribute( $classAttribute, 0 );
        $this->assertSame( 1, $classAttribute->attribute( 'data_int1' ) );
        $this->assertTrue( $type->fetchClassAttributeHTTPInput( $this->post( array( 'ContentClass_ezisbn_13_value_901_exists' => 1 ) ), 'ContentClass', $classAttribute ) );
        $type->preStoreClassAttribute( $classAttribute, 0 );
        $this->assertSame( 0, $classAttribute->attribute( 'data_int1' ) );
        $this->assertFalse( $type->storeClassAttributeContent( $classAttribute, 'not an array' ) );
    }

    public function testIsbnTextAndCopies()
    {
        $type = $this->dataType( 'ezisbn' );
        $attribute = $this->objectAttribute( 'ezisbn' );
        $type->fromString( $attribute, '978-0-306-40615-7' );
        $this->assertSame( '978-0-306-40615-7', $type->toString( $attribute ) );
        $this->assertTrue( $type->isIndexable() );
        $this->assertTrue( $type->supportsBatchInitializeObjectAttribute() );
    }

    // ---------------------------------------------------------------- ezauthor

    private function postAuthors( array $rows, $remove = null, $removeButton = false )
    {
        $post = array( 'ContentObjectAttribute_data_author_id_4711' => array_column( $rows, 0 ),
                       'ContentObjectAttribute_data_author_name_4711' => array_column( $rows, 1 ),
                       'ContentObjectAttribute_data_author_email_4711' => array_column( $rows, 2 ) );
        if ( $remove !== null )
            $post['ContentObjectAttribute_data_author_remove_4711'] = $remove;
        if ( $removeButton )
            $post['CustomActionButton'] = array( '4711_remove_selected' => 'Remove selected' );
        return $this->post( $post );
    }

    private function authorAttribute( array $classFields = array(), $xml = null )
    {
        if ( $xml === null )
        {
            $author = new eZAuthor();
            $author->addAuthor( 1, 'Ada', 'ada@k1.example.invalid' );
            $xml = $author->xmlString();
        }
        return $this->objectAttribute( 'ezauthor', $classFields, array( 'data_text' => $xml ) );
    }

    public static function authorValidationProvider()
    {
        $required = array( 'is_required' => 1 );
        return array(
            'one author' => array( array(), array( array( '1', 'Ada', 'ada@k1.example.invalid' ) ), null, false, eZInputValidator::STATE_ACCEPTED ),
            'name missing on the second' => array( array(), array( array( '1', 'Ada', 'ada@k1.example.invalid' ), array( '2', ' ', 'b@k1.example.invalid' ) ), null, false, eZInputValidator::STATE_INVALID ),
            'bad address' => array( array(), array( array( '1', 'Ada', 'not an address' ) ), null, false, eZInputValidator::STATE_INVALID ),
            'empty list, optional' => array( array(), array( array( '1', '', '' ) ), null, false, eZInputValidator::STATE_ACCEPTED ),
            'empty list, required' => array( $required, array( array( '1', '', '' ) ), null, false, eZInputValidator::STATE_INVALID ),
            'bad row removed with the button' => array( array(), array( array( '1', 'Ada', 'ada@k1.example.invalid' ), array( '2', '', 'x' ) ), array( '2' ), true, eZInputValidator::STATE_ACCEPTED ),
            'bad row ticked without the button' => array( array(), array( array( '1', 'Ada', 'ada@k1.example.invalid' ), array( '2', '', 'x' ) ), array( '2' ), false, eZInputValidator::STATE_INVALID ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('authorValidationProvider')]
    public function testAuthorValidation( $classFields, $rows, $remove, $removeButton, $expected )
    {
        $attribute = $this->authorAttribute( $classFields );
        $this->assertSame( $expected, $this->dataType( 'ezauthor' )->validateObjectAttributeHTTPInput( $this->postAuthors( $rows, $remove, $removeButton ), 'ContentObjectAttribute', $attribute ) );
    }

    public function testAuthorLimitAndNothingPosted()
    {
        $type = $this->dataType( 'ezauthor' );
        $rows = array();
        for ( $i = 1; $i <= eZAuthorType::MAX_AUTHORS + 1; $i++ )
            $rows[] = array( (string)$i, "Author $i", "a$i@k1.example.invalid" );
        $attribute = $this->authorAttribute();
        $http = $this->postAuthors( $rows );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute ) );
        $this->assertStringContainsString( (string)eZAuthorType::MAX_AUTHORS, $attribute->validationError() );
        $type->fetchObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute );
        $this->assertSame( eZAuthorType::MAX_AUTHORS, count( $attribute->content()->attribute( 'author_list' ) ) );

        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateObjectAttributeHTTPInput( $this->post( array() ), 'ContentObjectAttribute', $this->authorAttribute() ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateObjectAttributeHTTPInput( $this->post( array() ), 'ContentObjectAttribute', $this->authorAttribute( array( 'is_required' => 1 ) ) ) );
    }

    public function testAuthorFetchStoreTitleAndText()
    {
        $type = $this->dataType( 'ezauthor' );
        $attribute = $this->authorAttribute();
        $http = $this->postAuthors( array( array( '1', 'Ada | Lovelace', 'ada@k1.example.invalid' ), array( '2', "Bob\x07", 'bob@k1.example.invalid' ) ) );
        $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute ) );
        $list = $attribute->content()->attribute( 'author_list' );
        $this->assertSame( array( 'Ada | Lovelace', 'Bob' ), array_column( $list, 'name' ) );
        $type->storeObjectAttribute( $attribute );
        $this->assertStringContainsString( 'bob@k1.example.invalid', $attribute->attribute( 'data_text' ) );
        $this->assertTrue( $type->hasObjectAttributeContent( $attribute ) );
        $this->assertSame( 'Ada | Lovelace', $type->title( $attribute ) );
        $this->assertStringContainsString( 'Ada | Lovelace ada@k1.example.invalid', $type->metaData( $attribute ) );

        $text = $type->toString( $attribute );
        // authors are joined with & after their fields with |, so the | escaped in a name is escaped once more
        $this->assertSame( 'Ada \\\\| Lovelace|ada@k1.example.invalid|1&Bob|bob@k1.example.invalid|2', $text );
        $copy = $this->authorAttribute();
        $type->fromString( $copy, $text );
        $this->assertSame( $list, $copy->content()->attribute( 'author_list' ) );
        $type->fromString( $copy, array( 'x' ) );
        $this->assertSame( array(), $copy->content()->attribute( 'author_list' ) );
    }

    public function testAuthorCustomActionsAndCopies()
    {
        $type = $this->dataType( 'ezauthor' );
        $attribute = $this->authorAttribute();
        $type->customObjectAttributeHTTPAction( $this->post( array() ), 'new_author', $attribute, array( 'base_name' => 'ContentObjectAttribute' ) );
        $this->assertSame( 2, count( $attribute->content()->attribute( 'author_list' ) ) );
        $type->customObjectAttributeHTTPAction( $this->post( array() ), 'remove_selected', $attribute, array( 'base_name' => 'ContentObjectAttribute' ) );
        $this->assertSame( 2, count( $attribute->content()->attribute( 'author_list' ) ) );
        // stored authors are numbered from 0; the new one follows the last
        $this->assertSame( array( '0', 1 ), array_column( $attribute->content()->attribute( 'author_list' ), 'id' ) );
        $type->customObjectAttributeHTTPAction( $this->post( array( 'ContentObjectAttribute_data_author_remove_4711' => array( '0' ) ) ), 'remove_selected', $attribute, array( 'base_name' => 'ContentObjectAttribute' ) );
        $this->assertSame( array( 1 ), array_column( $attribute->content()->attribute( 'author_list' ), 'id' ) );
        $type->customObjectAttributeHTTPAction( $this->post( array() ), 'unknown', $attribute, array() );

        $copy = $this->objectAttribute( 'ezauthor' );
        $type->initializeObjectAttribute( $copy, 3, $this->authorAttribute() );
        $this->assertStringContainsString( 'Ada', $copy->attribute( 'data_text' ) );
    }

    public function testAuthorPackageSerialization()
    {
        $type = $this->dataType( 'ezauthor' );
        $attribute = $this->authorAttribute();
        $node = $type->serializeContentObjectAttribute( null, $attribute );
        $copy = $this->objectAttribute( 'ezauthor' );
        $type->unserializeContentObjectAttribute( null, $copy, $node );
        $this->assertSame( array( 'Ada' ), array_column( $copy->content()->attribute( 'author_list' ), 'name' ) );
    }
}
