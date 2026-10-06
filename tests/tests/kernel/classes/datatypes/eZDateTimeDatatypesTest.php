<?php
/**
 * The date (ezdate), date and time (ezdatetime) and time (eztime) datatypes as the content and class edit views
 * and the information collector use them: which posted fields make a date or time, required input, what is stored
 * (the date datatype keeps the local date as a UTC midnight), titles in a time zone west of UTC, defaults of new
 * objects (current time, adjustments), text export and import, and the package serialization of class settings
 * and of object values.
 *
 * No database: the class attribute is held in memory (see eZDatatypeTestFixtures.php). The time zone is UTC unless
 * a test sets another one; the base class puts it back.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group datatypes
 */

require_once __DIR__ . '/eZDatatypeTestFixtures.php';

class eZDateTimeDatatypesTest extends eZDatatypeTestCase
{
    // ---------------------------------------------------------------- ezdate

    private function postDate( $year, $month, $day )
    {
        return $this->post( array( 'ContentObjectAttribute_date_year_4711' => $year, 'ContentObjectAttribute_date_month_4711' => $month,
                                   'ContentObjectAttribute_date_day_4711' => $day ) );
    }

    public static function dateInputProvider()
    {
        return array(
            'date' => array( '2026', '10', '5', array(), eZInputValidator::STATE_ACCEPTED ),
            'spaces around' => array( ' 2026 ', '10 ', ' 05', array(), eZInputValidator::STATE_ACCEPTED ),
            '29 February of a leap year' => array( '2024', '2', '29', array(), eZInputValidator::STATE_ACCEPTED ),
            '29 February otherwise' => array( '2026', '2', '29', array(), eZInputValidator::STATE_INVALID ),
            'month 13' => array( '2026', '13', '1', array(), eZInputValidator::STATE_INVALID ),
            'letters' => array( '20x6', '1', '1', array(), eZInputValidator::STATE_INVALID ),
            'decimal' => array( '2026', '1.5', '1', array(), eZInputValidator::STATE_INVALID ),
            'negative' => array( '2026', '-1', '1', array(), eZInputValidator::STATE_INVALID ),
            'array' => array( array( '2026' ), '1', '1', array(), eZInputValidator::STATE_INVALID ),
            'all empty, optional' => array( '', '', '', array(), eZInputValidator::STATE_ACCEPTED ),
            'all empty, required' => array( '', '', '', array( 'is_required' => 1 ), eZInputValidator::STATE_INVALID ),
            'all empty, required collector' => array( '', '', '', array( 'is_required' => 1, 'is_information_collector' => 1 ), eZInputValidator::STATE_ACCEPTED ),
            'partly empty' => array( '2026', '', '1', array(), eZInputValidator::STATE_INVALID ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('dateInputProvider')]
    public function testDateObjectInput( $year, $month, $day, $classFields, $expected )
    {
        $attribute = $this->objectAttribute( 'ezdate', $classFields );
        $this->assertSame( $expected, $this->dataType( 'ezdate' )->validateObjectAttributeHTTPInput( $this->postDate( $year, $month, $day ), 'ContentObjectAttribute', $attribute ) );
    }

    public function testDateNotPostedAndCollection()
    {
        $type = $this->dataType( 'ezdate' );
        $http = $this->post( array( 'ContentObjectAttribute_date_year_4711' => '2026' ) );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $this->objectAttribute( 'ezdate' ) ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $this->objectAttribute( 'ezdate', array( 'is_required' => 1 ) ) ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateCollectionAttributeHTTPInput( $http, 'ContentObjectAttribute', $this->objectAttribute( 'ezdate' ) ) );
        $this->assertFalse( $type->fetchObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $this->objectAttribute( 'ezdate' ) ) );

        $required = $this->objectAttribute( 'ezdate', array( 'is_required' => 1 ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateCollectionAttributeHTTPInput( $this->postDate( '', '', '' ), 'ContentObjectAttribute', $required ) );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateCollectionAttributeHTTPInput( $this->postDate( '', '', '' ), 'ContentObjectAttribute', $this->objectAttribute( 'ezdate' ) ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateCollectionAttributeHTTPInput( $this->postDate( '2026', '', '' ), 'ContentObjectAttribute', $this->objectAttribute( 'ezdate' ) ) );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateCollectionAttributeHTTPInput( $this->postDate( '2026', '1', '31' ), 'ContentObjectAttribute', $required ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateCollectionAttributeHTTPInput( $this->postDate( '2026', '2', '31' ), 'ContentObjectAttribute', $required ) );
    }

    public static function timeZoneProvider()
    {
        return array( array( 'UTC' ), array( 'Europe/Berlin' ), array( 'America/New_York' ), array( 'Pacific/Auckland' ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('timeZoneProvider')]
    public function testDateIsStoredAsUtcMidnightAndShownAsTheSameDate( $timeZone )
    {
        date_default_timezone_set( $timeZone );
        $type = $this->dataType( 'ezdate' );
        $attribute = $this->objectAttribute( 'ezdate' );
        $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $this->postDate( '2026', '10', '5' ), 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( gmmktime( 0, 0, 0, 10, 5, 2026 ), $attribute->attribute( 'data_int' ) );

        $content = $type->objectAttributeContent( $attribute );
        $this->assertInstanceOf( 'eZDate', $content );
        $this->assertSame( array( 2026, 10, 5 ), array( (int)$content->year(), (int)$content->month(), (int)$content->day() ) );
        $this->assertSame( eZLocale::instance()->formatDate( mktime( 0, 0, 0, 10, 5, 2026 ) ), $type->title( $attribute ), "title in $timeZone" );

        $collected = new eZInformationCollectionAttribute( array( 'data_int' => null ) );
        $this->assertTrue( $type->fetchCollectionAttributeHTTPInput( null, $collected, $this->postDate( '2026', '10', '5' ), 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( gmmktime( 0, 0, 0, 10, 5, 2026 ), $collected->attribute( 'data_int' ) );
    }

    public function testDateFetchOfNoOrABrokenDateStoresNoDate()
    {
        $type = $this->dataType( 'ezdate' );
        foreach ( array( array( '', '', '' ), array( '2026', '2', '30' ), array( 'x', '1', '1' ), array( array(), '1', '1' ) ) as $parts )
        {
            $attribute = $this->objectAttribute( 'ezdate', array(), array( 'data_int' => 123 ) );
            $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $this->postDate( $parts[0], $parts[1], $parts[2] ), 'ContentObjectAttribute', $attribute ) );
            $this->assertNull( $attribute->attribute( 'data_int' ) );
            $this->assertFalse( $type->hasObjectAttributeContent( $attribute ) );
            $this->assertSame( '', $type->title( $attribute ) );
            $this->assertSame( '', $type->toString( $attribute ) );
        }
    }

    public function testDateDefaultsAndClassSettings()
    {
        $type = $this->dataType( 'ezdate' );
        $classAttribute = $this->classAttribute( 'ezdate', array( 'data_int1' => null ) );
        $type->initializeClassAttribute( $classAttribute );
        $this->assertSame( 0, $classAttribute->attribute( 'data_int1' ) );

        foreach ( array( '1' => 1, '0' => 0, 'yes' => 0, '2' => 0 ) as $posted => $expected )
        {
            $this->assertTrue( $type->fetchClassAttributeHTTPInput( $this->post( array( 'ContentClass_ezdate_default_901' => (string)$posted ) ), 'ContentClass', $classAttribute ) );
            $this->assertSame( $expected, $classAttribute->attribute( 'data_int1' ), "posted $posted" );
        }
        $this->assertTrue( $type->fetchClassAttributeHTTPInput( $this->post( array( 'ContentClass_ezdate_default_901' => array( 1 ) ) ), 'ContentClass', $classAttribute ) );

        $now = time();
        $new = $this->objectAttribute( 'ezdate', array( 'data_int1' => 1 ) );
        $type->initializeObjectAttribute( $new, false, null );
        $this->assertGreaterThanOrEqual( $now, $new->attribute( 'data_int' ) );
        $empty = $this->objectAttribute( 'ezdate', array( 'data_int1' => 0 ) );
        $type->initializeObjectAttribute( $empty, false, null );
        $this->assertNull( $empty->attribute( 'data_int' ) );
        $copy = $this->objectAttribute( 'ezdate', array( 'data_int1' => 1 ) );
        $type->initializeObjectAttribute( $copy, 2, $this->objectAttribute( 'ezdate', array(), array( 'data_int' => 86400 ) ) );
        $this->assertSame( 86400, $copy->attribute( 'data_int' ) );

        $this->assertSame( array(), $type->batchInitializeObjectAttributeData( $this->classAttribute( 'ezdate', array( 'data_int1' => 0 ) ) ) );
        $batch = $type->batchInitializeObjectAttributeData( $this->classAttribute( 'ezdate', array( 'data_int1' => 1 ) ) );
        $this->assertSame( $batch['data_int'], $batch['sort_key_int'] );
        $this->assertGreaterThanOrEqual( $now, $batch['data_int'] );
    }

    public function testDateClassSerialization()
    {
        $type = $this->dataType( 'ezdate' );
        list( $copy, $xml ) = $this->roundTripClassParameters( $this->classAttribute( 'ezdate', array( 'data_int1' => 1 ) ) );
        $this->assertSame( '<datatype-parameters><default-value type="current-date"/></datatype-parameters>', $xml );
        $this->assertSame( 1, $copy->attribute( 'data_int1' ) );
        list( $copy, $xml ) = $this->roundTripClassParameters( $this->classAttribute( 'ezdate', array( 'data_int1' => 0 ) ) );
        $this->assertSame( '<datatype-parameters><default-value type="empty"/></datatype-parameters>', $xml );
        $this->assertSame( 0, $copy->attribute( 'data_int1' ) );

        $dom = new DOMDocument();
        $dom->loadXML( '<attribute><datatype-parameters/></attribute>' );
        $classAttribute = $this->classAttribute( 'ezdate', array( 'data_int1' => 1 ) );
        $type->unserializeContentClassAttribute( $classAttribute, $dom->documentElement, $dom->documentElement->firstChild );
        $this->assertSame( 1, $classAttribute->attribute( 'data_int1' ) );
    }

    public function testDateObjectSerializationKeepsTheDate()
    {
        $type = $this->dataType( 'ezdate' );
        $attribute = $this->objectAttribute( 'ezdate', array(), array( 'data_int' => gmmktime( 0, 0, 0, 3, 14, 2025 ) ) );
        $node = $type->serializeContentObjectAttribute( null, $attribute );
        $this->assertSame( 'Fri, 14 Mar 2025 00:00:00 GMT', $node->getElementsByTagName( 'date' )->item( 0 )->textContent );
        $copy = $this->objectAttribute( 'ezdate' );
        $type->unserializeContentObjectAttribute( null, $copy, $node );
        $this->assertSame( gmmktime( 0, 0, 0, 3, 14, 2025 ), $copy->attribute( 'data_int' ) );

        $none = $type->serializeContentObjectAttribute( null, $this->objectAttribute( 'ezdate' ) );
        $this->assertSame( 0, $none->getElementsByTagName( 'date' )->length );

        $dom = new DOMDocument();
        $dom->loadXML( '<attribute><date>not a date</date></attribute>' );
        $broken = $this->objectAttribute( 'ezdate', array(), array( 'data_int' => 5 ) );
        $type->unserializeContentObjectAttribute( null, $broken, $dom->documentElement );
        $this->assertNull( $broken->attribute( 'data_int' ) );
    }

    public static function stampFromStringProvider()
    {
        return array(
            'timestamp' => array( '1700000000', 1700000000 ),
            'epoch' => array( '0', 0 ),
            'empty' => array( '', null ),
            'spaces only' => array( '  ', null ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('stampFromStringProvider')]
    public function testDateAndDateTimeFromStringKeepEveryTimestamp( $string, $expected )
    {
        foreach ( array( 'ezdate', 'ezdatetime' ) as $dataTypeString )
        {
            $type = $this->dataType( $dataTypeString );
            $attribute = $this->objectAttribute( $dataTypeString, array(), array( 'data_int' => 42 ) );
            $this->assertNotFalse( $type->fromString( $attribute, $string ) );
            $this->assertSame( $expected, $attribute->attribute( 'data_int' ) === null ? null : (int)$attribute->attribute( 'data_int' ), "$dataTypeString from '$string'" );
            $this->assertSame( $expected === null ? '' : (string)$expected, (string)$type->toString( $attribute ) );
        }
    }

    // ---------------------------------------------------------------- ezdatetime

    private function postDateTime( array $parts, $withSeconds = false )
    {
        $names = array( 'year', 'month', 'day', 'hour', 'minute' );
        if ( $withSeconds )
            $names[] = 'second';
        $post = array();
        foreach ( $names as $i => $name )
            $post['ContentObjectAttribute_datetime_' . $name . '_4711'] = $parts[$i];
        return $this->post( $post );
    }

    public static function dateTimeInputProvider()
    {
        return array(
            'date and time' => array( array( '2026', '10', '5', '14', '30' ), false, array(), eZInputValidator::STATE_ACCEPTED ),
            'midnight' => array( array( '2026', '10', '5', '0', '0' ), false, array(), eZInputValidator::STATE_ACCEPTED ),
            'hour 24' => array( array( '2026', '10', '5', '24', '0' ), false, array(), eZInputValidator::STATE_INVALID ),
            'minute 60' => array( array( '2026', '10', '5', '1', '60' ), false, array(), eZInputValidator::STATE_INVALID ),
            'second 60' => array( array( '2026', '10', '5', '1', '1', '60' ), true, array( 'data_int2' => 1 ), eZInputValidator::STATE_INVALID ),
            'with seconds' => array( array( '2026', '10', '5', '1', '1', '59' ), true, array( 'data_int2' => 1 ), eZInputValidator::STATE_ACCEPTED ),
            'seconds missing' => array( array( '2026', '10', '5', '1', '1', '' ), true, array( 'data_int2' => 1 ), eZInputValidator::STATE_INVALID ),
            'invalid day' => array( array( '2026', '4', '31', '1', '1' ), false, array(), eZInputValidator::STATE_INVALID ),
            'letters in the hour' => array( array( '2026', '4', '1', '1a', '1' ), false, array(), eZInputValidator::STATE_INVALID ),
            'negative minute' => array( array( '2026', '4', '1', '1', '-1' ), false, array(), eZInputValidator::STATE_INVALID ),
            'array year' => array( array( array( 1 ), '4', '1', '1', '1' ), false, array(), eZInputValidator::STATE_INVALID ),
            'all empty optional' => array( array( '', '', '', '', '' ), false, array(), eZInputValidator::STATE_ACCEPTED ),
            'all empty required' => array( array( '', '', '', '', '' ), false, array( 'is_required' => 1 ), eZInputValidator::STATE_INVALID ),
            'time missing' => array( array( '2026', '1', '1', '', '' ), false, array(), eZInputValidator::STATE_INVALID ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('dateTimeInputProvider')]
    public function testDateTimeObjectInput( $parts, $withSeconds, $classFields, $expected )
    {
        $attribute = $this->objectAttribute( 'ezdatetime', $classFields );
        $type = $this->dataType( 'ezdatetime' );
        $http = $this->postDateTime( $parts, $withSeconds );
        $this->assertSame( $expected, $type->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( $expected, $type->validateCollectionAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute ) );
    }

    public function testDateTimeFetchStoresTheLocalTime()
    {
        date_default_timezone_set( 'Europe/Berlin' );
        $type = $this->dataType( 'ezdatetime' );
        $attribute = $this->objectAttribute( 'ezdatetime' );
        $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $this->postDateTime( array( '2026', '10', '5', '14', '30' ) ), 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( mktime( 14, 30, 0, 10, 5, 2026 ), $attribute->attribute( 'data_int' ) );
        $this->assertSame( mktime( 14, 30, 0, 10, 5, 2026 ), $type->objectAttributeContent( $attribute )->timeStamp() );
        $this->assertSame( eZLocale::instance()->formatDateTime( mktime( 14, 30, 0, 10, 5, 2026 ) ), $type->title( $attribute ) );
        $this->assertSame( mktime( 14, 30, 0, 10, 5, 2026 ), $type->sortKey( $attribute ) );
        $this->assertSame( mktime( 14, 30, 0, 10, 5, 2026 ), $type->metaData( $attribute ) );

        $withSeconds = $this->objectAttribute( 'ezdatetime', array( 'data_int2' => 1 ) );
        $type->fetchObjectAttributeHTTPInput( $this->postDateTime( array( '2026', '10', '5', '14', '30', '15' ), true ), 'ContentObjectAttribute', $withSeconds );
        $this->assertSame( mktime( 14, 30, 15, 10, 5, 2026 ), $withSeconds->attribute( 'data_int' ) );

        // the seconds field is ignored when the class does not use seconds
        $noSeconds = $this->objectAttribute( 'ezdatetime' );
        $type->fetchObjectAttributeHTTPInput( $this->postDateTime( array( '2026', '10', '5', '14', '30', '15' ), true ), 'ContentObjectAttribute', $noSeconds );
        $this->assertSame( mktime( 14, 30, 0, 10, 5, 2026 ), $noSeconds->attribute( 'data_int' ) );

        $collected = new eZInformationCollectionAttribute( array( 'data_int' => null ) );
        $this->assertTrue( $type->fetchCollectionAttributeHTTPInput( null, $collected, $this->postDateTime( array( '2026', '1', '2', '3', '4' ) ), 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( mktime( 3, 4, 0, 1, 2, 2026 ), $collected->attribute( 'data_int' ) );
    }

    public function testDateTimeFetchOfNothingOrBrokenInputStoresNoTime()
    {
        $type = $this->dataType( 'ezdatetime' );
        foreach ( array( array( '', '', '', '', '' ), array( '2026', '2', '30', '1', '1' ), array( '2026', '2', '3', 'x', '1' ), array( '2026', array(), '3', '1', '1' ) ) as $parts )
        {
            $attribute = $this->objectAttribute( 'ezdatetime', array(), array( 'data_int' => 99 ) );
            $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $this->postDateTime( $parts ), 'ContentObjectAttribute', $attribute ) );
            $this->assertNull( $attribute->attribute( 'data_int' ) );
            $this->assertSame( '', $type->title( $attribute ) );
        }
        $this->assertFalse( $type->fetchObjectAttributeHTTPInput( $this->post( array() ), 'ContentObjectAttribute', $this->objectAttribute( 'ezdatetime' ) ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateCollectionAttributeHTTPInput( $this->post( array() ), 'ContentObjectAttribute', $this->objectAttribute( 'ezdatetime' ) ) );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateObjectAttributeHTTPInput( $this->post( array() ), 'ContentObjectAttribute', $this->objectAttribute( 'ezdatetime', array( 'is_required' => 1 ) ) ) );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateObjectAttributeHTTPInput( $this->post( array() ), 'ContentObjectAttribute', $this->objectAttribute( 'ezdatetime' ) ) );
    }

    public static function inputPartProvider()
    {
        return array(
            array( '12', 12 ), array( ' 7 ', 7 ), array( '', 0 ), array( 5, 5 ), array( -1, false ), array( '-1', false ),
            array( '1a', false ), array( '1.5', false ), array( '1234567890', false ), array( array(), false ), array( null, false ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('inputPartProvider')]
    public function testDateTimeInputPart( $value, $expected )
    {
        $this->assertSame( $expected, eZDateTimeType::dateTimeInputPart( $value ) );
    }

    private function adjustmentXml( array $values )
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n<adjustment>";
        foreach ( array( 'year', 'month', 'day', 'hour', 'minute', 'second' ) as $key )
            $xml .= '<' . $key . ' value="' . ( $values[$key] ?? '' ) . '"/>';
        return $xml . "</adjustment>\n";
    }

    public function testDateTimeClassSettingsWithAnAdjustment()
    {
        $type = $this->dataType( 'ezdatetime' );
        $classAttribute = $this->classAttribute( 'ezdatetime' );
        $http = $this->post( array( 'ContentClass_ezdatetime_default_901' => '2', 'ContentClass_ezdatetime_use_seconds_901' => '1',
                                    'ContentClass_ezdatetime_year_901' => '1', 'ContentClass_ezdatetime_month_901' => ' -2 ',
                                    'ContentClass_ezdatetime_day_901' => 'abc', 'ContentClass_ezdatetime_hour_901' => array( 1 ),
                                    'ContentClass_ezdatetime_minute_901' => '+30' ) );
        $this->assertTrue( $type->fetchClassAttributeHTTPInput( $http, 'ContentClass', $classAttribute ) );
        $this->assertSame( eZDateTimeType::DEFAULT_ADJUSTMENT, $classAttribute->attribute( 'data_int1' ) );
        $this->assertSame( 1, $classAttribute->attribute( 'data_int2' ) );
        $this->assertSame( array( 'year' => '1', 'month' => '-2', 'day' => '', 'hour' => '', 'minute' => '+30', 'second' => '' ),
                           $type->classAttributeContent( $classAttribute ) );

        foreach ( array( '7', 'x', array( 1 ) ) as $posted )
        {
            $type->fetchClassAttributeHTTPInput( $this->post( array( 'ContentClass_ezdatetime_default_901' => $posted ) ), 'ContentClass', $classAttribute );
            $this->assertSame( eZDateTimeType::DEFAULT_EMTPY, $classAttribute->attribute( 'data_int1' ) );
            $this->assertSame( 0, $classAttribute->attribute( 'data_int2' ) );
        }
        $this->assertTrue( $type->fetchClassAttributeHTTPInput( $this->post( array() ), 'ContentClass', $classAttribute ) );
    }

    public function testDateTimeClassContentOfMissingOrBrokenXml()
    {
        $type = $this->dataType( 'ezdatetime' );
        $empty = array( 'year' => '', 'month' => '', 'day' => '', 'hour' => '', 'minute' => '', 'second' => '' );
        foreach ( array( '', '   ', 'not xml <', '<adjustment/>' ) as $xml )
            $this->assertSame( $empty, $type->classAttributeContent( $this->classAttribute( 'ezdatetime', array( 'data_text5' => $xml ) ) ) );
        $partial = $type->classAttributeContent( $this->classAttribute( 'ezdatetime', array( 'data_text5' => '<adjustment><day value="3"/></adjustment>' ) ) );
        $this->assertSame( '3', $partial['day'] );
        $this->assertSame( '', $partial['hour'] );
    }

    public function testDateTimeDefaultsOfNewObjects()
    {
        $type = $this->dataType( 'ezdatetime' );
        $now = time();
        $current = $this->objectAttribute( 'ezdatetime', array( 'data_int1' => 1 ) );
        $type->initializeObjectAttribute( $current, false, null );
        $this->assertGreaterThanOrEqual( $now, $current->attribute( 'data_int' ) );
        $this->assertLessThan( $now + 60, $current->attribute( 'data_int' ) );

        $adjusted = $this->objectAttribute( 'ezdatetime', array( 'data_int1' => 2, 'data_text5' => $this->adjustmentXml( array( 'day' => '2', 'hour' => '' ) ) ) );
        $type->initializeObjectAttribute( $adjusted, false, null );
        $this->assertEqualsWithDelta( $now + 2 * 86400, $adjusted->attribute( 'data_int' ), 3700, 'two days ahead (one DST change at most)' );

        $seconds = $this->classAttribute( 'ezdatetime', array( 'data_int1' => 2, 'data_int2' => 0, 'data_text5' => $this->adjustmentXml( array( 'second' => '30' ) ) ) );
        $stamp = $type->adjustedDefaultTimeStamp( $seconds );
        $this->assertLessThan( $now + 30, $stamp, 'seconds are only added when the class uses seconds' );

        $none = $this->objectAttribute( 'ezdatetime', array( 'data_int1' => 0 ), array( 'data_int' => 77 ) );
        $type->initializeObjectAttribute( $none, false, null );
        $this->assertNull( $none->attribute( 'data_int' ) );

        $copy = $this->objectAttribute( 'ezdatetime', array( 'data_int1' => 1 ) );
        $type->initializeObjectAttribute( $copy, 3, $this->objectAttribute( 'ezdatetime', array(), array( 'data_int' => 1234 ) ) );
        $this->assertSame( 1234, $copy->attribute( 'data_int' ) );

        $this->assertSame( array(), $type->batchInitializeObjectAttributeData( $this->classAttribute( 'ezdatetime', array( 'data_int1' => 0 ) ) ) );
        $batch = $type->batchInitializeObjectAttributeData( $this->classAttribute( 'ezdatetime', array( 'data_int1' => 2, 'data_text5' => $this->adjustmentXml( array( 'year' => '-1' ) ) ) ) );
        $this->assertSame( $batch['data_int'], $batch['sort_key_int'] );
        $this->assertLessThan( $now - 360 * 86400, $batch['data_int'] );

        $classAttribute = $this->classAttribute( 'ezdatetime', array( 'data_int1' => null ) );
        $type->initializeClassAttribute( $classAttribute );
        $this->assertSame( 0, $classAttribute->attribute( 'data_int1' ) );
        $this->assertSame( 1, $classAttribute->storeCount );
    }

    public function testDateTimeClassSerialization()
    {
        $type = $this->dataType( 'ezdatetime' );
        $xml = $this->adjustmentXml( array( 'day' => '2', 'hour' => '-3' ) );
        list( $copy, $serialized ) = $this->roundTripClassParameters( $this->classAttribute( 'ezdatetime', array( 'data_int1' => 2, 'data_int2' => 1, 'data_text5' => $xml ) ) );
        $this->assertStringContainsString( '<default-value type="adjustment"><adjustment>', $serialized );
        $this->assertStringContainsString( '<use-seconds>1</use-seconds>', $serialized );
        $this->assertSame( 2, $copy->attribute( 'data_int1' ) );
        $this->assertSame( 1, $copy->attribute( 'data_int2' ) );
        $this->assertSame( $type->classAttributeContent( $this->classAttribute( 'ezdatetime', array( 'data_text5' => $xml ) ) ), $type->classAttributeContent( $copy ) );

        list( $copy, $serialized ) = $this->roundTripClassParameters( $this->classAttribute( 'ezdatetime', array( 'data_int1' => 2, 'data_text5' => '' ) ) );
        $this->assertStringContainsString( '<default-value type="adjustment"/>', $serialized );
        $this->assertSame( 2, $copy->attribute( 'data_int1' ) );

        foreach ( array( 0 => 'empty', 1 => 'current-date' ) as $default => $name )
        {
            list( $copy, $serialized ) = $this->roundTripClassParameters( $this->classAttribute( 'ezdatetime', array( 'data_int1' => $default ) ) );
            $this->assertStringContainsString( '<default-value type="' . $name . '"/>', $serialized );
            $this->assertSame( $default, $copy->attribute( 'data_int1' ) );
            $this->assertSame( 0, $copy->attribute( 'data_int2' ) );
        }
    }

    public function testDateTimeObjectSerialization()
    {
        $type = $this->dataType( 'ezdatetime' );
        $stamp = gmmktime( 13, 45, 10, 6, 1, 2025 );
        $node = $type->serializeContentObjectAttribute( null, $this->objectAttribute( 'ezdatetime', array(), array( 'data_int' => $stamp ) ) );
        $this->assertSame( 'Sun, 01 Jun 2025 13:45:10 GMT', $node->getElementsByTagName( 'date_time' )->item( 0 )->textContent );
        $copy = $this->objectAttribute( 'ezdatetime' );
        $type->unserializeContentObjectAttribute( null, $copy, $node );
        $this->assertSame( $stamp, $copy->attribute( 'data_int' ) );

        foreach ( array( '', 'garbage' ) as $text )
        {
            $dom = new DOMDocument();
            $dom->loadXML( '<attribute><date_time>' . $text . '</date_time></attribute>' );
            $broken = $this->objectAttribute( 'ezdatetime', array(), array( 'data_int' => 5 ) );
            $type->unserializeContentObjectAttribute( null, $broken, $dom->documentElement );
            $this->assertNull( $broken->attribute( 'data_int' ) );
        }
    }

    // ---------------------------------------------------------------- eztime

    private function postTime( $hour, $minute, $second = null )
    {
        $post = array( 'ContentObjectAttribute_time_hour_4711' => $hour, 'ContentObjectAttribute_time_minute_4711' => $minute );
        if ( $second !== null )
            $post['ContentObjectAttribute_time_second_4711'] = $second;
        return $this->post( $post );
    }

    public static function timeInputProvider()
    {
        return array(
            'time' => array( '14', '30', null, array(), eZInputValidator::STATE_ACCEPTED ),
            'midnight' => array( '0', '0', null, array(), eZInputValidator::STATE_ACCEPTED ),
            'last minute' => array( '23', '59', null, array(), eZInputValidator::STATE_ACCEPTED ),
            'hour 24' => array( '24', '0', null, array(), eZInputValidator::STATE_INVALID ),
            'minute 60' => array( '1', '60', null, array(), eZInputValidator::STATE_INVALID ),
            'letters' => array( '1x', '0', null, array(), eZInputValidator::STATE_INVALID ),
            'seconds' => array( '1', '2', '3', array( 'data_int2' => 1 ), eZInputValidator::STATE_ACCEPTED ),
            'second 60' => array( '1', '2', '60', array( 'data_int2' => 1 ), eZInputValidator::STATE_INVALID ),
            'second missing' => array( '1', '2', '', array( 'data_int2' => 1 ), eZInputValidator::STATE_INVALID ),
            'empty optional' => array( '', '', null, array(), eZInputValidator::STATE_ACCEPTED ),
            'empty required' => array( '', '', null, array( 'is_required' => 1 ), eZInputValidator::STATE_INVALID ),
            'half empty' => array( '1', '', null, array(), eZInputValidator::STATE_INVALID ),
            'array' => array( array( 1 ), '1', null, array(), eZInputValidator::STATE_INVALID ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('timeInputProvider')]
    public function testTimeObjectInput( $hour, $minute, $second, $classFields, $expected )
    {
        $type = $this->dataType( 'eztime' );
        $attribute = $this->objectAttribute( 'eztime', $classFields );
        $http = $this->postTime( $hour, $minute, $second );
        $this->assertSame( $expected, $type->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( $expected, $type->validateCollectionAttributeHTTPInput( $http, 'ContentObjectAttribute', $attribute ) );
    }

    public function testTimeFetchStoresTheSecondsOfTheDay()
    {
        $type = $this->dataType( 'eztime' );
        $attribute = $this->objectAttribute( 'eztime' );
        $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $this->postTime( '14', '30' ), 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( 14 * 3600 + 30 * 60, $attribute->attribute( 'data_int' ) );
        $this->assertSame( 14 * 3600 + 30 * 60, $type->sortKey( $attribute ) );
        $this->assertSame( 14 * 3600 + 30 * 60, $type->metaData( $attribute ) );
        $this->assertTrue( $type->hasObjectAttributeContent( $attribute ) );
        $this->assertSame( '14:30:0', $type->toString( $attribute ) );
        $this->assertSame( eZLocale::instance()->formatTime( mktime( 14, 30, 0 ) ), $type->title( $attribute ) );

        $withSeconds = $this->objectAttribute( 'eztime', array( 'data_int2' => 1 ) );
        $type->fetchObjectAttributeHTTPInput( $this->postTime( '0', '0', '9' ), 'ContentObjectAttribute', $withSeconds );
        $this->assertSame( 9, $withSeconds->attribute( 'data_int' ) );

        foreach ( array( array( '', '' ), array( '25', '1' ), array( '1', 'x' ) ) as $parts )
        {
            $broken = $this->objectAttribute( 'eztime', array(), array( 'data_int' => 50 ) );
            $this->assertTrue( $type->fetchObjectAttributeHTTPInput( $this->postTime( $parts[0], $parts[1] ), 'ContentObjectAttribute', $broken ) );
            $this->assertNull( $broken->attribute( 'data_int' ) );
            $this->assertFalse( $type->hasObjectAttributeContent( $broken ) );
            $this->assertSame( 0, $type->sortKey( $broken ) );
            $this->assertSame( '', $type->toString( $broken ) );
            $this->assertSame( '', $type->title( $broken ) );
            $this->assertFalse( $type->objectAttributeContent( $broken )['is_valid'] );
        }
        $this->assertFalse( $type->fetchObjectAttributeHTTPInput( $this->post( array() ), 'ContentObjectAttribute', $attribute ) );

        $collected = new eZInformationCollectionAttribute( array( 'data_int' => null ) );
        $this->assertTrue( $type->fetchCollectionAttributeHTTPInput( null, $collected, $this->postTime( '1', '1' ), 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( 3660, $collected->attribute( 'data_int' ) );
        $this->assertTrue( $type->fetchCollectionAttributeHTTPInput( null, $collected, $this->postTime( 'a', '1' ), 'ContentObjectAttribute', $attribute ) );
        $this->assertNull( $collected->attribute( 'data_int' ) );
    }

    public function testTimeClassSettingsDefaultsAndSerialization()
    {
        $type = $this->dataType( 'eztime' );
        $classAttribute = $this->classAttribute( 'eztime' );
        $this->assertFalse( $type->fetchClassAttributeHTTPInput( $this->post( array() ), 'ContentClass', $classAttribute ) );
        $this->assertTrue( $type->fetchClassAttributeHTTPInput( $this->post( array( 'ContentClass_eztime_default_901' => '1', 'ContentClass_eztime_use_seconds_901' => 'on' ) ), 'ContentClass', $classAttribute ) );
        $this->assertSame( 1, $classAttribute->attribute( 'data_int1' ) );
        $this->assertSame( 1, $classAttribute->attribute( 'data_int2' ) );
        $this->assertTrue( $type->fetchClassAttributeHTTPInput( $this->post( array( 'ContentClass_eztime_default_901' => 'x' ) ), 'ContentClass', $classAttribute ) );
        $this->assertSame( 0, $classAttribute->attribute( 'data_int1' ) );
        $this->assertSame( 0, $classAttribute->attribute( 'data_int2' ) );

        $current = $this->objectAttribute( 'eztime', array( 'data_int1' => 1 ) );
        $type->initializeObjectAttribute( $current, false, null );
        $this->assertGreaterThanOrEqual( 0, $current->attribute( 'data_int' ) );
        $this->assertLessThan( 86400, $current->attribute( 'data_int' ) );
        $copy = $this->objectAttribute( 'eztime' );
        $type->initializeObjectAttribute( $copy, 2, $this->objectAttribute( 'eztime', array(), array( 'data_int' => 600 ) ) );
        $this->assertSame( 600, $copy->attribute( 'data_int' ) );

        list( $copy, $xml ) = $this->roundTripClassParameters( $this->classAttribute( 'eztime', array( 'data_int1' => 1, 'data_int2' => 1 ) ) );
        $this->assertSame( '<datatype-parameters><default-value type="current-date"/><use-seconds>1</use-seconds></datatype-parameters>', $xml );
        $this->assertSame( 1, $copy->attribute( 'data_int1' ) );
        $this->assertEquals( 1, $copy->attribute( 'data_int2' ) );
    }

    public static function timeFromStringProvider()
    {
        return array(
            'hours and minutes' => array( '7:05', 7 * 3600 + 5 * 60 ),
            'with seconds' => array( '23:59:59', 86399 ),
            'spaces' => array( ' 1 : 2 ', 3720 ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('timeFromStringProvider')]
    public function testTimeFromString( $string, $expected )
    {
        $type = $this->dataType( 'eztime' );
        $attribute = $this->objectAttribute( 'eztime' );
        $this->assertTrue( $type->fromString( $attribute, $string ) );
        $this->assertSame( $expected, $attribute->attribute( 'data_int' ) );
        $copy = $this->objectAttribute( 'eztime' );
        $type->fromString( $copy, $type->toString( $attribute ) );
        $this->assertSame( $expected, $copy->attribute( 'data_int' ) );
    }
}
