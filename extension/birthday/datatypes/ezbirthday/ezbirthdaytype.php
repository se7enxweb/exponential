<?php

class eZBirthdayType extends eZDataType
{
    const DATA_TYPE_STRING = 'ezbirthday';
    const BIRTHDAY_DEFAULT = 'data_text1';
    const BIRTHDAY_DEFAULT_EMTPY = 0;
    const BIRTHDAY_DEFAULT_CURRENT_DATE = 1;

    function __construct()
    {
        parent::__construct( self::DATA_TYPE_STRING, ezpI18n::tr( 'kernel/classes/datatypes', "Birthday", 'Datatype name' ),
                           array( 'serialize_supported' => true ) );

    }

    static function addZero( $value )
    {
        return sprintf( "%02d", $value );
    }

    /*!
     Validates the input and returns true if the input was
     valid for this datatype.
    */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $year = $day = $month = '';

        if ( $http->hasPostVariable( $base . "_birthday_year_" . $contentObjectAttribute->attribute( "id" ) ) and
             $http->hasPostVariable( $base . "_birthday_month_" . $contentObjectAttribute->attribute( "id" ) ) and
             $http->hasPostVariable( $base . "_birthday_day_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $year = $http->postVariable( $base . "_birthday_year_" . $contentObjectAttribute->attribute( "id" ) );
            $month = $http->postVariable( $base . "_birthday_month_" . $contentObjectAttribute->attribute( "id" ) );
            $day = $http->postVariable( $base . "_birthday_day_" . $contentObjectAttribute->attribute( "id" ) );
        }

        $classAttribute = $contentObjectAttribute->contentClassAttribute();

        if ( ( ( $classAttribute->attribute( "is_required" ) == false ) and
             $year == '' and $month == '' and $day == '' ) or
             $classAttribute->attribute( 'is_information_collector' ) )
        {
            return eZInputValidator::STATE_ACCEPTED;
        }

        if ( $classAttribute->attribute( "is_required" ) and
             $year == '' or $month == '' or $day == '' )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                 'Missing date input.' ) );
            return eZInputValidator::STATE_INVALID;
        }

        $datetime = checkdate( $month, $day, $year );
        if ( $datetime !== false )
        {
            return eZInputValidator::STATE_ACCEPTED;
        }
        else
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                 'Please enter a correct date.' ) );
            return eZInputValidator::STATE_INVALID;
        }


    }

    /*!
     Validates the InformationCollection input and returns true if the input was
     valid for this datatype.
    */
    function validateCollectionAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $year = $day = $month = '';
        if ( $http->hasPostVariable( $base . "_birthday_year_" . $contentObjectAttribute->attribute( "id" ) ) and
             $http->hasPostVariable( $base . "_birthday_month_" . $contentObjectAttribute->attribute( "id" ) ) and
             $http->hasPostVariable( $base . "_birthday_day_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $year = $http->postVariable( $base . "_birthday_year_" . $contentObjectAttribute->attribute( "id" ) );
            $month = $http->postVariable( $base . "_birthday_month_" . $contentObjectAttribute->attribute( "id" ) );
            $day = $http->postVariable( $base . "_birthday_day_" . $contentObjectAttribute->attribute( "id" ) );
        }

        $classAttribute = $contentObjectAttribute->contentClassAttribute();
        if ( ( $classAttribute->attribute( "is_required" ) == false ) and
             $year == '' and $month == '' and $day == '' )
        {
            return eZInputValidator::STATE_ACCEPTED;
        }
        if ( $classAttribute->attribute( "is_required" ) and
             $year == '' or $month == '' or $day == '' )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                 'Missing date input.' ) );
            return eZInputValidator::STATE_INVALID;
        }

        $datetime = checkdate( $month, $day, $year );

        if ( $datetime !== false )
        {
            return eZInputValidator::STATE_ACCEPTED;
        }
        else
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                 'Please enter a correct date.' ) );
            return eZInputValidator::STATE_INVALID;
        }
    }

    /*!
     Fetches the http post var integer input and stores it in the data instance.
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $year = $day = $month = '';
        if ( $http->hasPostVariable( $base . "_birthday_year_" . $contentObjectAttribute->attribute( "id" ) ) and
             $http->hasPostVariable( $base . "_birthday_month_" . $contentObjectAttribute->attribute( "id" ) ) and
             $http->hasPostVariable( $base . "_birthday_day_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $year = $http->postVariable( $base . "_birthday_year_" . $contentObjectAttribute->attribute( "id" ) );
            $month = $http->postVariable( $base . "_birthday_month_" . $contentObjectAttribute->attribute( "id" ) );
            $day = $http->postVariable( $base . "_birthday_day_" . $contentObjectAttribute->attribute( "id" ) );
        }

        if ( $year == '' or $month == '' or $day == '' )
        {
            $date = null;
        }
        else
            $date = $year . '-'.eZBirthdayType::addZero( $month ) . '-'. eZBirthdayType::addZero( $day );

        $contentObjectAttribute->setAttribute( "data_text", $date );
        return true;
    }

    /*!
     Fetches the http post variables for collected information
    */
    function fetchCollectionAttributeHTTPInput( $collection, $collectionAttribute, $http, $base, $contentObjectAttribute )
    {
        $year = $day = $month = '';
        if ( $http->hasPostVariable( $base . '_birthday_year_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_birthday_month_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_birthday_day_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {

            $year  = $http->postVariable( $base . '_birthday_year_' . $contentObjectAttribute->attribute( 'id' ) );
            $month = $http->postVariable( $base . '_birthday_month_' . $contentObjectAttribute->attribute( 'id' ) );
            $day   = $http->postVariable( $base . '_birthday_day_' . $contentObjectAttribute->attribute( 'id' ) );
            //$date = new eZDate();
            $contentClassAttribute = $contentObjectAttribute->contentClassAttribute();

            if ( $year == '' or $month == '' or $day == '' )
                $date = NULL;
            else
                $date = $year . '-'.eZBirthdayType::addZero( $month ) . '-'. eZBirthdayType::addZero( $day );

            $collectionAttribute->setAttribute( 'data_text', $date );
            return true;
        }
        return false;
    }

    /*!
     Returns the content.
    */
    function objectAttributeContent( $contentObjectAttribute )
    {
        $dateStr = $contentObjectAttribute->attribute( 'data_text' );
        if ( preg_match( "/([0-9]{4})-([0-9]{2})-([0-9]{2})/", $dateStr, $valueArray ) )
        {
            $year = $valueArray[1];
            $month =  $valueArray[2];
            $day = $valueArray[3] ;
            $birthday = new eZBirthday( array ("year" => $year, "month" => $month, "day" => $day ) );
        }
        else
            $birthday = new eZBirthday();
        return $birthday;
    }

    /*!
     Set class attribute value for template version
    */
    function initializeClassAttribute( $classAttribute )
    {
        if ( $classAttribute->attribute( self::BIRTHDAY_DEFAULT ) == null )
            $classAttribute->setAttribute( self::BIRTHDAY_DEFAULT, 0 );
        $classAttribute->store();
    }

    /*!
     Sets the default value.
    */
    function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion != false )
        {
            $dataText = $originalContentObjectAttribute->attribute( "data_text" );
            $contentObjectAttribute->setAttribute( "data_text", $dataText );
        }
        else
        {
            $contentClassAttribute = $contentObjectAttribute->contentClassAttribute();
            $defaultType = $contentClassAttribute->attribute( self::BIRTHDAY_DEFAULT );
            if ( $defaultType == 1 )
            {
                $day = eZBirthdayType::addZero( date( 'd' ) );
                $month = eZBirthdayType::addZero( date( 'm' ) );
                $year = date('Y');
                $contentObjectAttribute->setAttribute( "data_text", $year . "-" . $month . "-" . $day );
            }
        }
    }

    function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $default = $base . "_ezbirthday_default_" . $classAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $default ) )
        {
            $defaultValue = $http->postVariable( $default );
            $classAttribute->setAttribute( self::BIRTHDAY_DEFAULT,  $defaultValue );
        }
        return true;
    }

    /*!
     Returns the meta data used for storing search indeces.
    */
    function metaData( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_text' );
    }

    /*!
     \return string representation of an contentobjectattribute data for simplified export
     */
    function toString( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_text' );
    }

    function fromString( $contentObjectAttribute, $string )
    {
        return $contentObjectAttribute->setAttribute( 'data_text', $string );
    }

    /*!
     Returns the date.
    */
    function title( $objectAttribute, $name = null )
    {
        return $objectAttribute->attribute( "data_text" );
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        return ( strlen( $contentObjectAttribute->attribute( "data_text" ) ) > 0 );
    }

    /*!
     \reimp
    */
    function sortKey( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_text' );
    }

    function sortKeyType()
    {
        return 'string';
    }

    /*!
     \reimp
    */
    function serializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $dom = $attributeParametersNode->ownerDocument;
        $defaultValue = $classAttribute->attribute( self::BIRTHDAY_DEFAULT );
        $element = $dom->createElement( 'default-value' );
        switch ( $defaultValue )
        {
            case self::BIRTHDAY_DEFAULT_EMTPY:
            {
                $element->setAttribute( 'type', 'empty' );
                $attributeParametersNode->appendChild( $element );
            } break;
            case self::BIRTHDAY_DEFAULT_CURRENT_DATE:
            {
                $element->setAttribute( 'type', 'current-date' );
                $attributeParametersNode->appendChild( $element );
                }
                break;
        }
    }

    function unserializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        
        foreach ( $attributeParametersNode->getElementsByTagName( 'default-value' ) as $attribute )
        {
            $defaultValue = $attribute->getAttribute( 'type' );
        }
        
        switch ( $defaultValue )
        {
            case 'empty':
                {
                    $classAttribute->setAttribute( self::BIRTHDAY_DEFAULT, self::BIRTHDAY_DEFAULT_EMTPY );
                }
                break;
            case 'current-date':
                {
                    $classAttribute->setAttribute( self::BIRTHDAY_DEFAULT, self::BIRTHDAY_DEFAULT_CURRENT_DATE );
                }
                break;
        }
    }

    /*!
     \return the collect information action if enabled
    */
    function contentActionList( $classAttribute )
    {
        if ( $classAttribute->attribute( 'is_information_collector' ) == true )
        {
            return array( array( 'name' => 'Send',
                                 'action' => 'ActionCollectInformation'
                                 ) );
        }
        else
            return array();
    }

    function isIndexable()
    {
        return true;
    }

    /*!
     \reimp
    */
    function isInformationCollector()
    {
        return true;
    }

}

eZDataType::register( eZBirthdayType::DATA_TYPE_STRING, "ezbirthdaytype" );

?>
