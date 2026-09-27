<?php
/**
 * File containing the eZIntegerType class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZIntegerType ezintegertype.php
  \ingroup eZDatatype
  \brief A content datatype which handles integers

  It provides the functionality to work as an integer and handles
  class definition input, object definition input and object viewing.

  It uses the spare field data_int in a content object attribute for storing
  the attribute data.
*/

class eZIntegerType extends eZDataType
{
    const DATA_TYPE_STRING = "ezinteger";
    const MIN_VALUE_FIELD = "data_int1";
    const MIN_VALUE_VARIABLE = "_ezinteger_min_integer_value_";
    const MAX_VALUE_FIELD = "data_int2";
    const MAX_VALUE_VARIABLE = "_ezinteger_max_integer_value_";
    const DEFAULT_VALUE_FIELD = "data_int3";
    const DEFAULT_VALUE_VARIABLE = "_ezinteger_default_value_";
    const INPUT_STATE_FIELD = "data_int4";
    const NO_MIN_MAX_VALUE = 0;
    const HAS_MIN_VALUE = 1;
    const HAS_MAX_VALUE = 2;
    const HAS_MIN_MAX_VALUE = 3;
    /// The range of the signed 32 bit int(11) columns the values are stored in
    const COLUMN_MIN = -2147483648;
    const COLUMN_MAX = 2147483647;

    public function __construct()
    {
        parent::__construct( self::DATA_TYPE_STRING, ezpI18n::tr( 'kernel/classes/datatypes', "Integer", 'Datatype name' ),
                           array( 'serialize_supported' => true,
                                  'object_serialize_map' => array( 'data_int' => 'value' ) ) );
        $this->IntegerValidator = new eZIntegerValidator();
    }

    /*!
     \private
     \return the posted value of \a $name with the spaces taken out, or null when it
     is not a string. A request can post an array under any name (name[]=x), which
     the validator's preg_match() and trim() refuse with a TypeError.
    */
    static function postedNumber( $http, $name )
    {
        $value = $http->postVariable( $name );
        if ( is_int( $value ) )
            return (string)$value;
        if ( !is_string( $value ) )
            return null;
        return str_replace( " ", "", $value );
    }

    /*!
     \private
     \return true if \a $value, already accepted as an integer, fits the int(11)
     columns (data_int, data_int1-4, sort_key_int) it is stored in. A larger number
     makes a strict database refuse the whole store, and a lenient one clip it
     silently to a different value than the editor typed.
    */
    static function fitsColumn( $value )
    {
        return filter_var( $value, FILTER_VALIDATE_INT,
                           array( 'options' => array( 'min_range' => self::COLUMN_MIN, 'max_range' => self::COLUMN_MAX ) ) ) !== false;
    }

    /**
     * Validates $data with the constraints defined on the class attribute
     *
     * @param $data
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @param eZContentClassAttribute $classAttribute
     *
     * @return int
     */
    function validateIntegerHTTPInput( $data, $contentObjectAttribute, $classAttribute )
    {
        $min = $classAttribute->attribute( self::MIN_VALUE_FIELD );
        $max = $classAttribute->attribute( self::MAX_VALUE_FIELD );
        $input_state = $classAttribute->attribute( self::INPUT_STATE_FIELD );

        // Leading zeros ("007") are an integer the regular expression accepts;
        // FILTER_VALIDATE_INT does not, so compare the number without them
        $number = preg_replace( '/^(-?)0+(?=\d)/', '$1', (string)$data );
        if ( preg_match( '/^-?[0-9]+$/', $number ) && !self::fitsColumn( $number ) )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                 'The number is not within the required range %1 - %2' ),
                                                         self::COLUMN_MIN, self::COLUMN_MAX );
            return eZInputValidator::STATE_INVALID;
        }

        switch( $input_state )
        {
            case self::NO_MIN_MAX_VALUE:
            {
                $this->IntegerValidator->setRange( false, false );
                $state = $this->IntegerValidator->validate( $data );
                if( $state === eZInputValidator::STATE_INVALID || $state === eZInputValidator::STATE_INTERMEDIATE )
                    $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                         'The input is not a valid integer.' ) );
                else
                    return $state;
            } break;
            case self::HAS_MIN_VALUE:
            {
                $this->IntegerValidator->setRange( $min, false );
                $state = $this->IntegerValidator->validate( $data );
                if( $state === eZInputValidator::STATE_ACCEPTED )
                    return eZInputValidator::STATE_ACCEPTED;
                else
                    $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                         'The number must be greater than %1' ),
                                                                 $min );
            } break;
            case self::HAS_MAX_VALUE:
            {
                $this->IntegerValidator->setRange( false, $max );
                $state = $this->IntegerValidator->validate( $data );
                if( $state===1 )
                    return eZInputValidator::STATE_ACCEPTED;
                else
                    $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                         'The number must be less than %1' ),
                                                                 $max );
            } break;
            case self::HAS_MIN_MAX_VALUE:
            {
                $this->IntegerValidator->setRange( $min, $max );
                $state = $this->IntegerValidator->validate( $data );
                if( $state===1 )
                    return eZInputValidator::STATE_ACCEPTED;
                else
                    $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                         'The number is not within the required range %1 - %2' ),
                                                                 $min, $max );
            } break;
        }

        return eZInputValidator::STATE_INVALID;

    }

    /*!
     Validates the input and returns true if the input was
     valid for this datatype.
    */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $classAttribute = $contentObjectAttribute->contentClassAttribute();

        if ( $http->hasPostVariable( $base . "_data_integer_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $data = self::postedNumber( $http, $base . "_data_integer_" . $contentObjectAttribute->attribute( "id" ) );
            if ( $data === null )
            {
                $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                     'The input is not a valid integer.' ) );
                return eZInputValidator::STATE_INVALID;
            }

            if ( $data == "" )
            {
                if ( !$classAttribute->attribute( 'is_information_collector' ) and
                     $contentObjectAttribute->validateIsRequired() )
                {
                    $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                         'Input required.' ) );
                    return eZInputValidator::STATE_INVALID;
                }
                else
                    return eZInputValidator::STATE_ACCEPTED;
            }
            else
            {
                return $this->validateIntegerHTTPInput( $data, $contentObjectAttribute, $classAttribute );
            }
        }
        else if ( !$classAttribute->attribute( 'is_information_collector' ) and $contentObjectAttribute->validateIsRequired() )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes', 'Input required.' ) );
            return eZInputValidator::STATE_INVALID;
        }
        else
            return eZInputValidator::STATE_ACCEPTED;
    }

    function fixupObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
    }

    /*!
     Sets the default value.
    */
    function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion != false )
        {
//             $contentObjectAttributeID = $contentObjectAttribute->attribute( "id" );
//             $currentObjectAttribute = eZContentObjectAttribute::fetch( $contentObjectAttributeID,
//                                                                         $currentVersion );
            $dataInt = $originalContentObjectAttribute->attribute( "data_int" );
            $contentObjectAttribute->setAttribute( "data_int", $dataInt );
        }
        else
        {
            $contentClassAttribute = $contentObjectAttribute->contentClassAttribute();
            $default = $contentClassAttribute->attribute( "data_int3" );
            if ( $default !== 0 )
            {
                $contentObjectAttribute->setAttribute( "data_int", $default );
            }
        }
    }

    /*!
     Fetches the http post var integer input and stores it in the data instance.
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . "_data_integer_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $data = self::postedNumber( $http, $base . "_data_integer_" . $contentObjectAttribute->attribute( "id" ) );
            // Validation refused anything but a string; an empty field is stored as
            // no value (NULL) as intended, not as the 0 str_replace() made of null
            if ( $data === null )
                return false;
            $data = trim( $data ) != '' ? $data : null;
            $contentObjectAttribute->setAttribute( "data_int", $data );
            return true;
        }
        return false;
    }

    function validateCollectionAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . "_data_integer_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $data = self::postedNumber( $http, $base . "_data_integer_" . $contentObjectAttribute->attribute( "id" ) );
            if ( $data === null )
            {
                $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                     'The input is not a valid integer.' ) );
                return eZInputValidator::STATE_INVALID;
            }
            $classAttribute = $contentObjectAttribute->contentClassAttribute();

            if ( $data == "" )
            {
                if ( $contentObjectAttribute->validateIsRequired() )
                {
                    $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                         'Input required.' ) );
                    return eZInputValidator::STATE_INVALID;
                }
                else
                    return eZInputValidator::STATE_ACCEPTED;
            }
            else
            {
                return $this->validateIntegerHTTPInput( $data, $contentObjectAttribute, $classAttribute );
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
        if ( $http->hasPostVariable( $base . "_data_integer_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $data = self::postedNumber( $http, $base . "_data_integer_" . $contentObjectAttribute->attribute( "id" ) );
            // Validation refused anything but a string; an empty field is stored as
            // no value (NULL) as intended, not as the 0 str_replace() made of null
            if ( $data === null )
                return false;
            $data = trim( $data ) != '' ? $data : null;
            $collectionAttribute->setAttribute( "data_int", $data );
            return true;
        }
        return false;
    }

    /*!
     Does nothing, the data is already present in the attribute.
    */
    function storeObjectAttribute( $object_attribute )
    {
    }

    function storeClassAttribute( $attribute, $version )
    {
    }

    function validateClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $minValueName = $base . self::MIN_VALUE_VARIABLE . $classAttribute->attribute( "id" );
        $maxValueName = $base . self::MAX_VALUE_VARIABLE . $classAttribute->attribute( "id" );
        $defaultValueName = $base . self::DEFAULT_VALUE_VARIABLE . $classAttribute->attribute( "id" );

        if ( $http->hasPostVariable( $minValueName ) and
             $http->hasPostVariable( $maxValueName ) and
             $http->hasPostVariable( $defaultValueName ) )
        {
            $minValueValue = self::postedNumber( $http, $minValueName );
            $maxValueValue = self::postedNumber( $http, $maxValueName );
            $defaultValueValue = self::postedNumber( $http, $defaultValueName );
            if ( $minValueValue === null or $maxValueValue === null or $defaultValueValue === null )
                return eZInputValidator::STATE_INVALID;

            // Each value is stored in an int(11) column, and the default was never
            // checked at all: "abc" or 99999999999 went to the database as it was
            // The datatype object is shared by every attribute of the request, so
            // the validator still has the range an object attribute or a fixup set
            $this->IntegerValidator->setRange( false, false );

            // (a min/max like "5x" is still left to the fixup below, as before)
            foreach ( array( 'min' => $minValueValue, 'max' => $maxValueValue, 'default' => $defaultValueValue ) as $which => $value )
            {
                if ( $value === '' )
                    continue;
                $number = preg_replace( '/^(-?)0+(?=\d)/', '$1', $value );
                if ( !preg_match( '/^-?[0-9]+$/', $number ) )
                {
                    if ( $which === 'default' )
                        return eZInputValidator::STATE_INVALID;
                    continue;
                }
                if ( !self::fitsColumn( $number ) )
                    return eZInputValidator::STATE_INVALID;
            }

            if ( ( $minValueValue == "" ) && ( $maxValueValue == "") ){
                return  eZInputValidator::STATE_ACCEPTED;
            }
            else if ( ( $minValueValue == "" ) && ( $maxValueValue !== "") )
            {
                $max_state = $this->IntegerValidator->validate( $maxValueValue );
                return  $max_state;
            }
            else if ( ( $minValueValue !== "" ) && ( $maxValueValue == "") )
            {
                $min_state = $this->IntegerValidator->validate( $minValueValue );
                return  $min_state;
            }
            else
            {
                $min_state = $this->IntegerValidator->validate( $minValueValue );
                $max_state = $this->IntegerValidator->validate( $maxValueValue );
                if ( ( $min_state == eZInputValidator::STATE_ACCEPTED ) and
                     ( $max_state == eZInputValidator::STATE_ACCEPTED ) )
                {
                    if ($minValueValue <= $maxValueValue)
                        return eZInputValidator::STATE_ACCEPTED;
                    else
                    {
                        $state = eZInputValidator::STATE_INTERMEDIATE;
                        eZDebug::writeNotice( "Integer minimum value great than maximum value." );
                        return $state;
                    }
                }
            }

            if ($defaultValueValue == ""){
                $default_state =  eZInputValidator::STATE_ACCEPTED;
            }
            else
                $default_state = $this->IntegerValidator->validate( $defaultValueValue );
        }

        return eZInputValidator::STATE_INVALID;
    }

    function fixupClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $minValueName = $base . self::MIN_VALUE_VARIABLE . $classAttribute->attribute( "id" );
        $maxValueName = $base . self::MAX_VALUE_VARIABLE . $classAttribute->attribute( "id" );
        if ( $http->hasPostVariable( $minValueName ) and $http->hasPostVariable( $maxValueName ) )
        {
            $minValueValue = self::postedNumber( $http, $minValueName );
            $maxValueValue = self::postedNumber( $http, $maxValueName );
            // Not a string at all: nothing a fixup can make an integer of
            if ( $minValueValue === null or $maxValueValue === null )
                return;
            $minValueValue = $this->IntegerValidator->fixup( $minValueValue );
            $http->setPostVariable( $minValueName, $minValueValue );

            $maxValueValue = $this->IntegerValidator->fixup( $maxValueValue );
            $http->setPostVariable( $maxValueName, $maxValueValue );

            if ($minValueValue > $maxValueValue)
            {
                $this->IntegerValidator->setRange( $minValueValue, false );
                $maxValueValue = $this->IntegerValidator->fixup( $maxValueValue );
                $http->setPostVariable( $maxValueName, $maxValueValue );
            }
        }
    }

    function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $minValueName = $base . self::MIN_VALUE_VARIABLE . $classAttribute->attribute( "id" );
        $maxValueName = $base . self::MAX_VALUE_VARIABLE . $classAttribute->attribute( "id" );
        $defaultValueName = $base . self::DEFAULT_VALUE_VARIABLE . $classAttribute->attribute( "id" );

        if ( $http->hasPostVariable( $minValueName ) and
             $http->hasPostVariable( $maxValueName ) and
             $http->hasPostVariable( $defaultValueName ) )
        {
            $minValueValue = self::postedNumber( $http, $minValueName );
            $maxValueValue = self::postedNumber( $http, $maxValueName );
            $defaultValueValue = self::postedNumber( $http, $defaultValueName );
            if ( $minValueValue === null or $maxValueValue === null or $defaultValueValue === null )
                return false;

            $classAttribute->setAttribute( self::MIN_VALUE_FIELD, $minValueValue );
            $classAttribute->setAttribute( self::MAX_VALUE_FIELD, $maxValueValue );
            $classAttribute->setAttribute( self::DEFAULT_VALUE_FIELD, $defaultValueValue );

            if ( ( $minValueValue == "" ) && ( $maxValueValue == "") ){
                $input_state = self::NO_MIN_MAX_VALUE;
                $classAttribute->setAttribute( self::INPUT_STATE_FIELD, $input_state );
            }
            else if ( ( $minValueValue == "" ) && ( $maxValueValue !== "") )
            {
                $input_state = self::HAS_MAX_VALUE;
                $classAttribute->setAttribute( self::INPUT_STATE_FIELD, $input_state );
            }
            else if ( ( $minValueValue !== "" ) && ( $maxValueValue == "") )
            {
                $input_state = self::HAS_MIN_VALUE;
                $classAttribute->setAttribute( self::INPUT_STATE_FIELD, $input_state );
            }
            else
            {
                $input_state = self::HAS_MIN_MAX_VALUE;
                $classAttribute->setAttribute( self::INPUT_STATE_FIELD, $input_state );
            }
            return true;
        }
        return false;
    }

    /*!
     Returns the content.
    */
    function objectAttributeContent( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( "data_int" );
    }


    /*!
     Returns the meta data used for storing search indeces.
    */
    function metaData( $contentObjectAttribute )
    {
        return (int)$contentObjectAttribute->attribute( "data_int" );
    }
    /*!
     \return string representation of an contentobjectattribute data for simplified export

    */
    function toString( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_int' );
    }

    function fromString( $contentObjectAttribute, $string )
    {
        // toString() gives '' for no value; read it back as no value, and refuse
        // what is not an integer instead of storing it as 0 (or a clipped number)
        $string = trim( (string)$string );
        if ( $string === '' )
            return $contentObjectAttribute->setAttribute( 'data_int', null );
        $number = preg_replace( '/^(-?)0+(?=\d)/', '$1', $string );
        if ( !preg_match( '/^-?[0-9]+$/', $number ) or !self::fitsColumn( $number ) )
            return false;
        return $contentObjectAttribute->setAttribute( 'data_int', $string );
    }

    /*!
     Returns the integer value.
    */
    function title( $contentObjectAttribute, $name = null )
    {
        return $contentObjectAttribute->attribute( "data_int" );
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_int' ) !== null;
    }

    function isInformationCollector()
    {
        return true;
    }

    /*!
     \return true if the datatype can be indexed
    */
    function isIndexable()
    {
        return true;
    }

    function sortKey( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_int' );
    }

    function sortKeyType()
    {
        return 'int';
    }

    function serializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $defaultValue = $classAttribute->attribute( self::DEFAULT_VALUE_FIELD );
        $minValue = $classAttribute->attribute( self::MIN_VALUE_FIELD );
        $maxValue = $classAttribute->attribute( self::MAX_VALUE_FIELD );
        $minMaxState = $classAttribute->attribute( self::INPUT_STATE_FIELD );

        $dom = $attributeParametersNode->ownerDocument;
        $defaultValueNode = $dom->createElement( 'default-value' );
        $defaultValueNode->appendChild( $dom->createTextNode( $defaultValue ) );
        $attributeParametersNode->appendChild( $defaultValueNode );
        if ( $minMaxState == self::HAS_MIN_VALUE or $minMaxState == self::HAS_MIN_MAX_VALUE )
        {
            $minValueNode = $dom->createElement( 'min-value' );
            $minValueNode->appendChild( $dom->createTextNode( $minValue ) );
            $attributeParametersNode->appendChild( $minValueNode );
        }
        if ( $minMaxState == self::HAS_MAX_VALUE or $minMaxState == self::HAS_MIN_MAX_VALUE )
        {
            $maxValueNode = $dom->createElement( 'max-value' );
            $maxValueNode->appendChild( $dom->createTextNode( $maxValue ) );
            $attributeParametersNode->appendChild( $maxValueNode );
        }
    }

    function unserializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $defaultValueNode = $attributeParametersNode->getElementsByTagName( 'default-value' )->item( 0 );
        $minValueNode = $attributeParametersNode->getElementsByTagName( 'min-value' )->item( 0 );
        $maxValueNode = $attributeParametersNode->getElementsByTagName( 'max-value' )->item( 0 );

        $defaultValue = $defaultValueNode instanceof DOMNode ? $defaultValueNode->textContent : '';
        $minValue = $minValueNode instanceof DOMNode ? $minValueNode->textContent : '';
        $maxValue = $maxValueNode instanceof DOMNode ? $maxValueNode->textContent : '';

        if ( strlen( $minValue ) > 0 and strlen( $maxValue ) > 0 )
            $minMaxState = self::HAS_MIN_MAX_VALUE;
        else if ( strlen( $minValue ) > 0 )
            $minMaxState = self::HAS_MIN_VALUE;
        else if ( strlen( $maxValue ) > 0 )
            $minMaxState = self::HAS_MAX_VALUE;
        else
            $minMaxState = self::NO_MIN_MAX_VALUE;

        $classAttribute->setAttribute( self::DEFAULT_VALUE_FIELD, $defaultValue );
        $classAttribute->setAttribute( self::MIN_VALUE_FIELD, $minValue );
        $classAttribute->setAttribute( self::MAX_VALUE_FIELD, $maxValue );
        $classAttribute->setAttribute( self::INPUT_STATE_FIELD, $minMaxState );
    }

    function batchInitializeObjectAttributeData( $classAttribute )
    {
        $default = $classAttribute->attribute( "data_int3" );
        if ( $default === 0 )
        {
            return array();
        }
        else
        {
            return array( 'data_int'     => $default,
                          'sort_key_int' => $default );
        }
    }

    function supportsBatchInitializeObjectAttribute()
    {
        return true;
    }

    /// \privatesection
    /// The integer value validator
    public $IntegerValidator;
}

eZDataType::register( eZIntegerType::DATA_TYPE_STRING, "eZIntegerType" );

?>
