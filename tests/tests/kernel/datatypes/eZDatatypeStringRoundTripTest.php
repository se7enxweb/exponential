<?php
/**
 * Tests of toString()/fromString() of the datatypes that need no database for it: integer, float, boolean, string,
 * text, email, ISBN, date, date and time, time, author and option. These two functions are what the CSV import and
 * export, the package system and the REST layer use, so what toString() writes must come back unchanged through
 * fromString(), and text that is not a value of the type must be refused rather than stored as something else.
 *
 * The content object attribute is a stand-in that holds the data_* fields and the content in memory.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group datatypes
 */

class eZDatatypeStringRoundTripAttributeStub
{
    public $fields = array( 'data_int' => null, 'data_float' => null, 'data_text' => null );

    public $content = null;

    public function attribute( $name )
    {
        if ( $name === 'content' )
            return $this->content;
        return isset( $this->fields[$name] ) ? $this->fields[$name] : null;
    }

    public function hasAttribute( $name )
    {
        return $name === 'content' || array_key_exists( $name, $this->fields );
    }

    public function setAttribute( $name, $value )
    {
        $this->fields[$name] = $value;
        return true;
    }

    public function setContent( $content )
    {
        $this->content = $content;
    }
}

class eZDatatypeStringRoundTripTest extends PHPUnit\Framework\TestCase
{
    private $timezone;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->timezone = date_default_timezone_get();
        date_default_timezone_set( 'UTC' );
    }

    protected function tearDown(): void
    {
        date_default_timezone_set( $this->timezone );
    }

    private static function fromString( $type, $string )
    {
        $attribute = new eZDatatypeStringRoundTripAttributeStub();
        $result = $type->fromString( $attribute, $string );
        return array( $attribute, $result );
    }

    public static function roundTripProvider()
    {
        return array(
            'integer'           => array( 'eZIntegerType', '42', 'data_int' ),
            'negative integer'  => array( 'eZIntegerType', '-7', 'data_int' ),
            'float'             => array( 'eZFloatType', '3.25', 'data_float' ),
            'negative float'    => array( 'eZFloatType', '-0.5', 'data_float' ),
            'boolean true'      => array( 'eZBooleanType', '1', 'data_int' ),
            'boolean false'     => array( 'eZBooleanType', '0', 'data_int' ),
            'string'            => array( 'eZStringType', 'Plain | text & "quotes"', 'data_text' ),
            'text'              => array( 'eZTextType', "Two\nlines", 'data_text' ),
            'email'             => array( 'eZEmailType', 'someone@t1.example.invalid', 'data_text' ),
            'isbn'              => array( 'eZISBNType', '978-0-306-40615-7', 'data_text' ),
            'date'              => array( 'eZDateType', '1700000000', 'data_int' ),
            'date and time'     => array( 'eZDateTimeType', '1700000000', 'data_int' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('roundTripProvider')]
    public function testFromStringThenToStringGivesTheTextBack( $class, $string, $field )
    {
        $type = new $class();
        list( $attribute, $result ) = self::fromString( $type, $string );
        $this->assertNotFalse( $result );
        $this->assertNotNull( $attribute->fields[$field] );
        $this->assertSame( $string, (string)$type->toString( $attribute ) );
    }

    public static function refusedProvider()
    {
        return array(
            'integer text'        => array( 'eZIntegerType', 'twelve' ),
            'integer decimal'     => array( 'eZIntegerType', '1.5' ),
            'integer too big'     => array( 'eZIntegerType', '99999999999999999999999' ),
            'float text'          => array( 'eZFloatType', 'pi' ),
            'float infinite'      => array( 'eZFloatType', 'INF' ),
            'date text'           => array( 'eZDateType', 'yesterday' ),
            'time letters'        => array( 'eZTimeType', 'aa:bb' ),
            'time out of range'   => array( 'eZTimeType', '25:00' ),
            'time without minute' => array( 'eZTimeType', '10' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('refusedProvider')]
    public function testFromStringRefusesTextThatIsNoValueOfTheType( $class, $string )
    {
        list( , $result ) = self::fromString( new $class(), $string );
        $this->assertFalse( $result );
    }

    public static function emptyProvider()
    {
        return array(
            array( 'eZIntegerType', 'data_int' ),
            array( 'eZFloatType', 'data_float' ),
            array( 'eZDateType', 'data_int' ),
            array( 'eZDateTimeType', 'data_int' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('emptyProvider')]
    public function testEmptyStringIsNoValue( $class, $field )
    {
        $type = new $class();
        list( $attribute, $result ) = self::fromString( $type, '' );
        $this->assertNotFalse( $result );
        $this->assertNull( $attribute->fields[$field] );
        $this->assertSame( '', (string)$type->toString( $attribute ) );
    }

    public function testIntegerKeepsLeadingZerosOutOfTheCheckOnly()
    {
        list( $attribute ) = self::fromString( new eZIntegerType(), ' 007 ' );
        $this->assertSame( '007', $attribute->fields['data_int'] );
    }

    public static function booleanProvider()
    {
        return array(
            array( 'true', 1 ), array( 'yes', 1 ), array( '1', 1 ), array( 'on', 1 ),
            array( 'false', 0 ), array( 'no', 0 ), array( '0', 0 ), array( '', 0 ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('booleanProvider')]
    public function testBooleanFromStringMapsWordsToZeroOrOne( $string, $expected )
    {
        list( $attribute ) = self::fromString( new eZBooleanType(), $string );
        $this->assertSame( $expected, (int)$attribute->fields['data_int'] );
        $this->assertContains( $attribute->fields['data_int'], array( 0, 1, '0', '1' ), 'stored as 0 or 1' );
    }

    public function testDateTimeFromNonNumericTextIsNoValue()
    {
        list( $attribute ) = self::fromString( new eZDateTimeType(), 'next week' );
        $this->assertNull( $attribute->fields['data_int'] );
    }

    public static function timeProvider()
    {
        return array(
            'hours minutes seconds' => array( '10:30:15', 10 * 3600 + 30 * 60 + 15 ),
            'without seconds'       => array( '08:05', 8 * 3600 + 5 * 60 ),
            'midnight'              => array( '0:0:0', 0 ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('timeProvider')]
    public function testTimeFromString( $string, $seconds )
    {
        list( $attribute, $result ) = self::fromString( new eZTimeType(), $string );
        $this->assertTrue( $result );
        $this->assertSame( $seconds, (int)$attribute->fields['data_int'] );
    }

    public function testTimeToStringOfAStoredTime()
    {
        $attribute = new eZDatatypeStringRoundTripAttributeStub();
        $attribute->content = eZTime::create( 9, 5, 7 );
        $this->assertSame( '9:5:7', ( new eZTimeType() )->toString( $attribute ) );
        $attribute->content = null;
        $this->assertSame( '', ( new eZTimeType() )->toString( $attribute ) );
    }

    public function testAuthorListRoundTrip()
    {
        $string = eZStringUtils::implodeStr( array(
            eZStringUtils::implodeStr( array( 'Ada | Example', 'ada@t1.example.invalid', '1' ), '|' ),
            eZStringUtils::implodeStr( array( 'Bob & Co', 'bob@t1.example.invalid', '2' ), '|' ) ), '&' );
        $type = new eZAuthorType();
        list( $attribute ) = self::fromString( $type, $string );
        $this->assertInstanceOf( 'eZAuthor', $attribute->content );
        $names = array_column( $attribute->content->attribute( 'author_list' ), 'name' );
        $this->assertSame( array( 'Ada | Example', 'Bob & Co' ), $names );
        $this->assertSame( $string, $type->toString( $attribute ) );
    }

    public function testAuthorWithNameOnlyGetsAnId()
    {
        list( $attribute ) = self::fromString( new eZAuthorType(), 'Only Name' );
        $list = $attribute->content->attribute( 'author_list' );
        $this->assertCount( 1, $list );
        $this->assertSame( '', $list[0]['email'] );
        $this->assertSame( 1, $list[0]['id'] );
    }

    public function testOptionRoundTrip()
    {
        $string = eZStringUtils::implodeStr( array( 'Size', 'Small', '0', 'Large | XL', '2.50' ), '|' );
        $type = new eZOptionType();
        list( $attribute ) = self::fromString( $type, $string );
        $option = new eZOption( '' );
        $option->decodeXML( $attribute->fields['data_text'] );
        $attribute->content = $option;
        $this->assertSame( $string, $type->toString( $attribute ) );
    }
}
