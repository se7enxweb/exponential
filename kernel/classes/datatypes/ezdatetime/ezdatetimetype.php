<?php
/**
 * File containing the eZDateTimeType class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZDateTimeType ezdatetimetype.php
  \ingroup eZDatatype
  \brief Stores a date and time value

*/

class eZDateTimeType extends eZDataType
{
    const DATA_TYPE_STRING = 'ezdatetime';

    const DEFAULT_FIELD = 'data_int1';

    const USE_SECONDS_FIELD = 'data_int2';

    const ADJUSTMENT_FIELD = 'data_text5';

    const DEFAULT_EMTPY = 0;

    const DEFAULT_CURRENT_DATE = 1;

    const DEFAULT_ADJUSTMENT = 2;

    public function __construct()
    {
        parent::__construct( self::DATA_TYPE_STRING, ezpI18n::tr( 'kernel/classes/datatypes', "Date and time", 'Datatype name' ),
                           array( 'serialize_supported' => true ) );
    }

    /*!
     Private method only for use inside this class
    */
    function validateDateTimeHTTPInput( $day, $month, $year, $hour, $minute, $second, $contentObjectAttribute )
    {
        // checkdate()/mktime() take ints and throw a TypeError on PHP 8 for an
        // array or a string such as "abc" or "12abc", so anything that is not a
        // plain number is a date the editor has to correct, not a fatal error
        $day = self::dateTimeInputPart( $day );
        $month = self::dateTimeInputPart( $month );
        $year = self::dateTimeInputPart( $year );
        if ( $day === false or $month === false or $year === false )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                 'Date is not valid.' ) );
            return eZInputValidator::STATE_INVALID;
        }
        $hour = self::dateTimeInputPart( $hour );
        $minute = self::dateTimeInputPart( $minute );
        $second = self::dateTimeInputPart( $second );
        if ( $hour === false or $minute === false or $second === false )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                 'Time is not valid.' ) );
            return eZInputValidator::STATE_INVALID;
        }

        $state = eZDateTimeValidator::validateDate( $day, $month, $year );
        if ( $state == eZInputValidator::STATE_INVALID )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                 'Date is not valid.' ) );
            return eZInputValidator::STATE_INVALID;
        }

        $state = eZDateTimeValidator::validateTime( $hour, $minute, $second );

        if ( $state == eZInputValidator::STATE_INVALID )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                 'Time is not valid.' ) );
            return eZInputValidator::STATE_INVALID;
        }
        return $state;
    }

    /*!
     \static
     \return the int value of one posted date or time field, or false when it
             is not a plain non-negative number (an array, "abc", "12abc", "-1",
             or more digits than an int can hold). An empty field is 0, the
             callers decide about empty fields before they get here.
    */
    static function dateTimeInputPart( $value )
    {
        if ( is_int( $value ) )
            return $value >= 0 ? $value : false;
        if ( !is_string( $value ) )
            return false;
        $value = trim( $value );
        if ( $value === '' )
            return 0;
        if ( strlen( $value ) > 9 or !ctype_digit( $value ) )
            return false;
        return (int)$value;
    }

    /*!
     \return the timestamp for the posted fields, or null when they do not
             make a valid date (the fetch functions run after validation, but
             must not fatal when they are called without it)
    */
    function dateTimeInputStamp( $year, $month, $day, $hour, $minute, $second, $useSeconds )
    {
        if ( !is_scalar( $year ) or !is_scalar( $month ) or !is_scalar( $day ) or
             !is_scalar( $hour ) or !is_scalar( $minute ) or !is_scalar( $second ) )
            return null;
        if ( $year == '' and $month == '' and $day == '' and
             $hour == '' and $minute == '' and ( !$useSeconds or $second == '' ) )
            return null;
        $parts = array();
        foreach ( array( $year, $month, $day, $hour, $minute, $second ) as $part )
        {
            $part = self::dateTimeInputPart( $part );
            if ( $part === false )
                return null;
            $parts[] = $part;
        }
        list( $year, $month, $day, $hour, $minute, $second ) = $parts;
        if ( !checkdate( $month, $day, $year ) )
            return null;
        $dateTime = new eZDateTime();
        $dateTime->setMDYHMS( $month, $day, $year, $hour, $minute, $second );
        return $dateTime->timeStamp();
    }

    /*!
     \return the current time moved by the adjustment stored in the class
             attribute. The stored values are strings, and one left empty in the
             class editor is "", which PHP 8 refuses in arithmetic with a
             TypeError: that made every new object of such a class fail.
    */
    function adjustedDefaultTimeStamp( $classAttribute )
    {
        $adjustments = $this->classAttributeContent( $classAttribute );
        $int = function ( $key ) use ( $adjustments )
        {
            $v = $adjustments[$key] ?? 0;
            return is_numeric( $v ) ? (int)$v : 0;
        };
        $value = new eZDateTime();
        $secondAdjustment = $classAttribute->attribute( self::USE_SECONDS_FIELD ) == 1 ? $int( 'second' ) : 0;
        $value->adjustDateTime( $int( 'hour' ), $int( 'minute' ), $secondAdjustment, $int( 'month' ), $int( 'day' ), $int( 'year' ) );
        return $value->timeStamp();
    }

    /*!
     Validates the input and returns true if the input was
     valid for this datatype.
    */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $classAttribute = $contentObjectAttribute->contentClassAttribute();
        $useSeconds = ( $classAttribute->attribute( self::USE_SECONDS_FIELD ) == 1 );

        if ( $http->hasPostVariable( $base . '_datetime_year_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_datetime_month_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_datetime_day_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_datetime_hour_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_datetime_minute_' . $contentObjectAttribute->attribute( 'id' ) ) and
             ( !$useSeconds or $http->hasPostVariable( $base . '_datetime_second_' . $contentObjectAttribute->attribute( 'id' ) ) ) )
        {
            $year   = $http->postVariable( $base . '_datetime_year_' . $contentObjectAttribute->attribute( 'id' ) );
            $month  = $http->postVariable( $base . '_datetime_month_' . $contentObjectAttribute->attribute( 'id' ) );
            $day    = $http->postVariable( $base . '_datetime_day_' . $contentObjectAttribute->attribute( 'id' ) );
            $hour   = $http->postVariable( $base . '_datetime_hour_' . $contentObjectAttribute->attribute( 'id' ) );
            $minute = $http->postVariable( $base . '_datetime_minute_' . $contentObjectAttribute->attribute( 'id' ) );
            $second = $useSeconds ? $http->postVariable( $base . '_datetime_second_' . $contentObjectAttribute->attribute( 'id' ) ) : 0;

            if ( $year == '' or
                 $month == '' or
                 $day == '' or
                 $hour == '' or
                 $minute == '' or
                 ( $useSeconds and $second == '' ) )
            {
                if ( !( $year == '' and
                        $month == '' and
                        $day == '' and
                        $hour == '' and
                        $minute == '' and
                        ( !$useSeconds or $second == '' ) ) or
                     ( !$classAttribute->attribute( 'is_information_collector' ) and
                       $contentObjectAttribute->validateIsRequired() ) )
                {
                    $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                         'Missing datetime input.' ) );
                    return eZInputValidator::STATE_INVALID;
                }
                else
                    return eZInputValidator::STATE_ACCEPTED;
            }
            else
            {
                return $this->validateDateTimeHTTPInput( $day, $month, $year, $hour, $minute, $second, $contentObjectAttribute );
            }
        }
        else if ( !$classAttribute->attribute( 'is_information_collector' ) and $contentObjectAttribute->validateIsRequired() )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes', 'Missing datetime input.' ) );
            return eZInputValidator::STATE_INVALID;
        }
        else
            return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     Fetches the http post var integer input and stores it in the data instance.
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $contentClassAttribute = $contentObjectAttribute->contentClassAttribute();
        $useSeconds = ( $contentClassAttribute->attribute( self::USE_SECONDS_FIELD ) == 1 );

        if ( $http->hasPostVariable( $base . '_datetime_year_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_datetime_month_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_datetime_day_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_datetime_hour_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_datetime_minute_' . $contentObjectAttribute->attribute( 'id' ) ) and
             ( !$useSeconds or $http->hasPostVariable( $base . '_datetime_second_' . $contentObjectAttribute->attribute( 'id' ) ) ) )
        {
            $year   = $http->postVariable( $base . '_datetime_year_' . $contentObjectAttribute->attribute( 'id' ) );
            $month  = $http->postVariable( $base . '_datetime_month_' . $contentObjectAttribute->attribute( 'id' ) );
            $day    = $http->postVariable( $base . '_datetime_day_' . $contentObjectAttribute->attribute( 'id' ) );
            $hour   = $http->postVariable( $base . '_datetime_hour_' . $contentObjectAttribute->attribute( 'id' ) );
            $minute = $http->postVariable( $base . '_datetime_minute_' . $contentObjectAttribute->attribute( 'id' ) );
            $second = $useSeconds ? $http->postVariable( $base . '_datetime_second_' . $contentObjectAttribute->attribute( 'id' ) ) : 0;

            $stamp = $this->dateTimeInputStamp( $year, $month, $day, $hour, $minute, $second, $useSeconds );

            $contentObjectAttribute->setAttribute( 'data_int', $stamp );
            return true;
        }
        return false;
    }

    function validateCollectionAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $contentClassAttribute = $contentObjectAttribute->contentClassAttribute();
        $useSeconds = ( $contentClassAttribute->attribute( self::USE_SECONDS_FIELD ) == 1 );

        if ( $http->hasPostVariable( $base . '_datetime_year_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_datetime_month_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_datetime_day_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_datetime_hour_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_datetime_minute_' . $contentObjectAttribute->attribute( 'id' ) ) and
             ( !$useSeconds or $http->hasPostVariable( $base . '_datetime_second_' . $contentObjectAttribute->attribute( 'id' ) ) ) )

        {
            $year   = $http->postVariable( $base . '_datetime_year_' . $contentObjectAttribute->attribute( 'id' ) );
            $month  = $http->postVariable( $base . '_datetime_month_' . $contentObjectAttribute->attribute( 'id' ) );
            $day    = $http->postVariable( $base . '_datetime_day_' . $contentObjectAttribute->attribute( 'id' ) );
            $hour   = $http->postVariable( $base . '_datetime_hour_' . $contentObjectAttribute->attribute( 'id' ) );
            $minute = $http->postVariable( $base . '_datetime_minute_' . $contentObjectAttribute->attribute( 'id' ) );
            $second = $useSeconds ? $http->postVariable( $base . '_datetime_second_' . $contentObjectAttribute->attribute( 'id' ) ) : 0;

            if ( $year == '' or
                 $month == '' or
                 $day == '' or
                 $hour == '' or
                 $minute == '' or
                 ( $useSeconds and $second == '' ) )
            {
                if ( !( $year == '' and
                        $month == '' and
                        $day == '' and
                        $hour == '' and
                        $minute == '' and
                        ( !$useSeconds or $second == '' ) ) or
                     $contentObjectAttribute->validateIsRequired() )
                {
                    $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                         'Missing datetime input.' ) );
                    return eZInputValidator::STATE_INVALID;
                }
                else
                    return eZInputValidator::STATE_ACCEPTED;
            }
            else
            {
                return $this->validateDateTimeHTTPInput( $day, $month, $year, $hour, $minute, $second, $contentObjectAttribute );
            }
        }
        else
            return eZInputValidator::STATE_INVALID;
    }

   /*!
    Fetches the http post variables for collected information
   */
    function fetchCollectionAttributeHTTPInput( $collection, $collectionAttribute, $http, $base, $contentObjectAttribute )
    {
        $contentClassAttribute = $contentObjectAttribute->contentClassAttribute();
        $useSeconds = ( $contentClassAttribute->attribute( self::USE_SECONDS_FIELD ) == 1 );

        if ( $http->hasPostVariable( $base . '_datetime_year_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_datetime_month_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_datetime_day_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_datetime_hour_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_datetime_minute_' . $contentObjectAttribute->attribute( 'id' ) ) and
             ( !$useSeconds or $http->hasPostVariable( $base . '_datetime_second_' . $contentObjectAttribute->attribute( 'id' ) ) ) )
        {
            $year   = $http->postVariable( $base . '_datetime_year_' . $contentObjectAttribute->attribute( 'id' ) );
            $month  = $http->postVariable( $base . '_datetime_month_' . $contentObjectAttribute->attribute( 'id' ) );
            $day    = $http->postVariable( $base . '_datetime_day_' . $contentObjectAttribute->attribute( 'id' ) );
            $hour   = $http->postVariable( $base . '_datetime_hour_' . $contentObjectAttribute->attribute( 'id' ) );
            $minute = $http->postVariable( $base . '_datetime_minute_' . $contentObjectAttribute->attribute( 'id' ) );
            $second = $useSeconds ? $http->postVariable( $base . '_datetime_second_' . $contentObjectAttribute->attribute( 'id' ) ) : 0;

            $stamp = $this->dateTimeInputStamp( $year, $month, $day, $hour, $minute, $second, $useSeconds );

            $collectionAttribute->setAttribute( 'data_int', $stamp );
            return true;
        }
        return false;
    }

    /*!
     Returns the content.
    */
    function objectAttributeContent( $contentObjectAttribute )
    {
        $dateTime = new eZDateTime();
        $stamp = $contentObjectAttribute->attribute( 'data_int' );
        $dateTime->setTimeStamp( $stamp );
        return $dateTime;
    }

    function isIndexable()
    {
        return true;
    }

    function isInformationCollector()
    {
        return true;
    }

    /*!
     Returns the meta data used for storing search indeces.
    */
    function metaData( $contentObjectAttribute )
    {
        return (int)$contentObjectAttribute->attribute( 'data_int' );
    }
    /*!
     \return string representation of an contentobjectattribute data for simplified export

    */
    function toString( $contentObjectAttribute )
    {
        $stamp = $contentObjectAttribute->attribute( 'data_int' );
        return $stamp === null ? '' : $stamp;
    }

    function fromString( $contentObjectAttribute, $string )
    {
        // toString() gives the timestamp, so a number is read back as one; any
        // other text used to go into the int column as it was
        if ( empty( $string ) or !is_numeric( $string ) )
        {
            $string = null;
        }
        else
        {
            $string = (int)$string;
        }

        return $contentObjectAttribute->setAttribute( 'data_int', $string );
    }

    /*!
     Set class attribute value for template version
    */
    function initializeClassAttribute( $classAttribute )
    {
        if ( $classAttribute->attribute( self::DEFAULT_FIELD ) == null )
            $classAttribute->setAttribute( self::DEFAULT_FIELD, 0 );
        $classAttribute->store();
    }

    function parseXML( $xmlText )
    {
        $dom = new DOMDocument;
        // Broken stored XML must not print libxml warnings into the page
        $useErrors = libxml_use_internal_errors( true );
        $success = $dom->loadXML( $xmlText );
        libxml_clear_errors();
        libxml_use_internal_errors( $useErrors );
        return $dom;
    }

    function classAttributeContent( $classAttribute )
    {
        $xmlText = (string)$classAttribute->attribute( 'data_text5' );
        if ( trim( $xmlText ) == '' )
        {
            $classAttrContent = eZDateTimeType::defaultClassAttributeContent();
            return $classAttrContent;
        }
        $doc = eZDateTimeType::parseXML( $xmlText );
        $root = $doc->documentElement;
        // Unparsable XML has no root element, and XML without some of the
        // elements used to leave those keys out (and $content undefined when
        // none was there): start from the defaults so every key exists
        $content = eZDateTimeType::defaultClassAttributeContent();
        if ( !$root )
        {
            return $content;
        }
        $type = $root->getElementsByTagName( 'year' )->item( 0 );
        if ( $type )
        {
            $content['year'] = $type->getAttribute( 'value' );
        }
        $type = $root->getElementsByTagName( 'month' )->item( 0 );
        if ( $type )
        {
            $content['month'] = $type->getAttribute( 'value' );
        }
        $type = $root->getElementsByTagName( 'day' )->item( 0 );
        if ( $type )
        {
            $content['day'] = $type->getAttribute( 'value' );
        }
        $type = $root->getElementsByTagName( 'hour' )->item( 0 );
        if ( $type )
        {
            $content['hour'] = $type->getAttribute( 'value' );
        }
        $type = $root->getElementsByTagName( 'minute' )->item( 0 );
        if ( $type )
        {
            $content['minute'] = $type->getAttribute( 'value' );
        }
        $type = $root->getElementsByTagName( 'second' )->item( 0 );
        if ( $type )
        {
            $content['second'] = $type->getAttribute( 'value' );
        }
        return $content;
    }

    function defaultClassAttributeContent()
    {
        return array( 'year' => '',
                      'month' => '',
                      'day' => '',
                      'hour' => '',
                      'minute' => '',
                      'second' => '' );
    }

    /*!
     Sets the default value.
    */
    function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion != false )
        {
            $dataInt = $originalContentObjectAttribute->attribute( "data_int" );
            $contentObjectAttribute->setAttribute( "data_int", $dataInt );
        }
        else
        {
            $contentClassAttribute = $contentObjectAttribute->contentClassAttribute();
            $defaultType = $contentClassAttribute->attribute( self::DEFAULT_FIELD );
            if ( $defaultType == self::DEFAULT_CURRENT_DATE )
            {
                $contentObjectAttribute->setAttribute( "data_int", time() );
            }
            else if ( $defaultType == self::DEFAULT_ADJUSTMENT )
            {
                $contentObjectAttribute->setAttribute( "data_int", $this->adjustedDefaultTimeStamp( $contentClassAttribute ) );
            }
            else
                $contentObjectAttribute->setAttribute( "data_int", null );
        }
    }

    function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $default = $base . "_ezdatetime_default_" . $classAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $default ) )
        {
            // The select only offers 0, 1 and 2; anything else (an array, text)
            // used to be stored as it came
            $defaultValue = $http->postVariable( $default );
            $defaultValue = is_scalar( $defaultValue ) ? (int)$defaultValue : self::DEFAULT_EMTPY;
            if ( !in_array( $defaultValue, array( self::DEFAULT_EMTPY, self::DEFAULT_CURRENT_DATE, self::DEFAULT_ADJUSTMENT ), true ) )
                $defaultValue = self::DEFAULT_EMTPY;
            $classAttribute->setAttribute( self::DEFAULT_FIELD,  $defaultValue );
            if ( $defaultValue == self::DEFAULT_ADJUSTMENT )
            {
                $doc = new DOMDocument( '1.0', 'utf-8' );
                $root = $doc->createElement( 'adjustment' );
                $contentList = eZDateTimeType::contentObjectArrayXMLMap();
                foreach ( $contentList as $key => $value )
                {
                    $postValue = $http->postVariable( $base . '_ezdatetime_' . $value . '_' . $classAttribute->attribute( 'id' ) );
                    // An adjustment is a whole, possibly negative number, or
                    // empty. A missing field is null and an array a TypeError in
                    // setAttribute(), and text was stored and later printed into
                    // the class edit form as it came
                    $postValue = is_scalar( $postValue ) ? trim( (string)$postValue ) : '';
                    if ( !preg_match( '/^[-+]?\d{1,9}$/', $postValue ) )
                        $postValue = '';
                    unset( $elementType );
                    $elementType = $doc->createElement( $key );
                    $elementType->setAttribute( 'value', $postValue );
                    $root->appendChild( $elementType );
                }
                $doc->appendChild( $root );
                $docText = $doc->saveXML();
                $classAttribute->setAttribute( self::ADJUSTMENT_FIELD , $docText );
            }

            $useSeconds = $base . "_ezdatetime_use_seconds_" . $classAttribute->attribute( 'id' );
            $classAttribute->setAttribute( self::USE_SECONDS_FIELD, $http->hasPostVariable( $useSeconds ) ? 1 : 0 );
        }

        return true;
    }

    function contentObjectArrayXMLMap()
    {
        return array( 'year' => 'year',
                      'month' => 'month',
                      'day' => 'day',
                      'hour' => 'hour',
                      'minute' => 'minute',
                      'second' => 'second' );
    }


    /*!
     Returns the date.
    */
    function title( $contentObjectAttribute, $name = null )
    {
        $locale = eZLocale::instance();
        $retVal = $contentObjectAttribute->attribute( "data_int" ) === null ? '' : $locale->formatDateTime( $contentObjectAttribute->attribute( "data_int" ) );
        return $retVal;
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( "data_int" ) !== null;
    }

    function sortKey( $contentObjectAttribute )
    {
        return (int)$contentObjectAttribute->attribute( 'data_int' );
    }

    function sortKeyType()
    {
        return 'int';
    }

    function serializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $dom = $attributeParametersNode->ownerDocument;
        $defaultValue = $classAttribute->attribute( self::DEFAULT_FIELD );
        $defaultValueNode = $dom->createElement( 'default-value' );

        switch ( $defaultValue )
        {
            case self::DEFAULT_CURRENT_DATE:
            {
                $defaultValueNode->setAttribute( 'type', 'current-date' );
            } break;
            case self::DEFAULT_ADJUSTMENT:
            {
                $defaultValueNode->setAttribute( 'type', 'adjustment' );

                $adjustDOMValue = new DOMDocument( '1.0', 'utf-8' );
                $adjustValue = (string)$classAttribute->attribute( self::ADJUSTMENT_FIELD );
                // loadXML() throws a ValueError for an empty string on PHP 8, and
                // broken XML must not print libxml warnings during an export
                $success = false;
                if ( trim( $adjustValue ) !== '' )
                {
                    $useErrors = libxml_use_internal_errors( true );
                    $success = $adjustDOMValue->loadXML( $adjustValue );
                    libxml_clear_errors();
                    libxml_use_internal_errors( $useErrors );
                }

                if ( $success )
                {
                    $adjustmentNode = $adjustDOMValue->getElementsByTagName( 'adjustment' )->item( 0 );

                    if ( $adjustmentNode )
                    {
                        $importedAdjustmentNode = $dom->importNode( $adjustmentNode, true );
                        $defaultValueNode->appendChild( $importedAdjustmentNode );
                    }
                }
            } break;
            case self::DEFAULT_EMTPY:
            {
                $defaultValueNode->setAttribute( 'type', 'empty' );
            } break;
            default:
            {
                eZDebug::writeError( 'Unknown type of DateTime default value. Empty type used instead.', __METHOD__ );
                $defaultValueNode->setAttribute( 'type', 'empty' );
            } break;
        }
        $attributeParametersNode->appendChild( $defaultValueNode );

        $useSeconds = $classAttribute->attribute( self::USE_SECONDS_FIELD );
        $useSecondsNode = $dom->createElement( 'use-seconds' );
        $useSecondsNode->appendChild( $dom->createTextNode( (string)$useSeconds ) );
        $attributeParametersNode->appendChild( $useSecondsNode );
    }

    function unserializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $defaultValue = '';
        $defaultNode = $attributeParametersNode->getElementsByTagName( 'default-value' )->item( 0 );
        if ( $defaultNode )
        {
            $defaultValue = strtolower( $defaultNode->getAttribute( 'type' ) );
        }
        switch ( $defaultValue )
        {
            case 'current-date':
            {
                $classAttribute->setAttribute( self::DEFAULT_FIELD, self::DEFAULT_CURRENT_DATE );
            } break;
            case 'adjustment':
            {
                $adjustmentValue = '';
                $adjustmentNode = $defaultNode->getElementsByTagName( 'adjustment' )->item( 0 );
                if ( $adjustmentNode )
                {
                    $adjustmentDOMValue = new DOMDocument( '1.0', 'utf-8' );
                    $importedAdjustmentNode = $adjustmentDOMValue->importNode( $adjustmentNode, true );
                    $adjustmentDOMValue->appendChild( $importedAdjustmentNode );
                    $adjustmentValue = $adjustmentDOMValue->saveXML();
                }

                $classAttribute->setAttribute( self::DEFAULT_FIELD, self::DEFAULT_ADJUSTMENT );
                $classAttribute->setAttribute( self::ADJUSTMENT_FIELD, $adjustmentValue );
            } break;
            case 'empty':
            {
                $classAttribute->setAttribute( self::DEFAULT_FIELD, self::DEFAULT_EMTPY );
            } break;
            default:
            {
                eZDebug::writeError( 'Type of DateTime default value is not set. Empty type used as default.', __METHOD__ );
                $classAttribute->setAttribute( self::DEFAULT_FIELD, self::DEFAULT_EMTPY );
            } break;
        }

        $useSecondsNode = $attributeParametersNode->getElementsByTagName( 'use-seconds' )->item( 0 );
        if ( $useSecondsNode && $useSecondsNode->textContent === '1' )
        {
            $classAttribute->setAttribute( self::USE_SECONDS_FIELD, 1 );
        }
    }

    /*!
     \return a DOM representation of the content object attribute
    */
    function serializeContentObjectAttribute( $package, $objectAttribute )
    {
        $node = $this->createContentObjectAttributeDOMNode( $objectAttribute );

        $stamp = $objectAttribute->attribute( 'data_int' );

        if ( $stamp !== null )
        {
            $dom = $node->ownerDocument;
            $dateTimeNode = $dom->createElement( 'date_time' );
            $dateTimeNode->appendChild( $dom->createTextNode( eZDateUtils::rfc1123Date( $stamp ) ) );
            $node->appendChild( $dateTimeNode );
        }
        return $node;
    }

    function unserializeContentObjectAttribute( $package, $objectAttribute, $attributeNode )
    {
        $dateTimeNode = $attributeNode->getElementsByTagName( 'date_time' )->item( 0 );
        if ( is_object( $dateTimeNode ) )
        {
            // strtotime() gives false for a date it cannot read; that went into
            // the int column as 0 (1 January 1970) instead of "no date"
            $timestamp = trim( $dateTimeNode->textContent ) === '' ? false : eZDateUtils::textToDate( $dateTimeNode->textContent );
            $objectAttribute->setAttribute( 'data_int', $timestamp === false ? null : $timestamp );
        }
    }

    function supportsBatchInitializeObjectAttribute()
    {
        return true;
    }

    function batchInitializeObjectAttributeData( $classAttribute )
    {
        $defaultType = $classAttribute->attribute( self::DEFAULT_FIELD );

        switch( $defaultType )
        {
            case self::DEFAULT_CURRENT_DATE:
            {
                $default = time();
            } break;

            case self::DEFAULT_ADJUSTMENT:
            {
                $default = $this->adjustedDefaultTimeStamp( $classAttribute );
            } break;

            default:
            {
                return array();
            }
        }

        return array( 'data_int' => $default, 'sort_key_int' => $default );
    }
}

eZDataType::register( eZDateTimeType::DATA_TYPE_STRING, "eZDateTimeType" );

?>
