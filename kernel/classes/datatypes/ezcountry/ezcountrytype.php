<?php
/**
 * File containing the eZCountryType class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZCountryType ezcountrytype.php
  \ingroup eZDatatype
  \brief A content datatype that contains country.

  The list of countries is fetched from contenet.ini.
  Country is stored as text string.
*/



class eZCountryType extends eZDataType
{
    const DATA_TYPE_STRING = 'ezcountry';

    const DEFAULT_LIST_FIELD = 'data_text5';

    const MULTIPLE_CHOICE_FIELD = 'data_int1';

    public function __construct()
    {
        parent::__construct( self::DATA_TYPE_STRING, ezpI18n::tr( 'kernel/classes/datatypes', 'Country', 'Datatype name' ),
                           array( 'serialize_supported' => true,
                                  'object_serialize_map' => array( 'data_text' => 'country' ) ) );
    }

    /*!
     Fetches country list from ini.
    */
    static function fetchCountryList()
    {
        if ( isset( $GLOBALS['CountryList'] ) )
            return $GLOBALS['CountryList'];

        $ini = eZINI::instance( 'country.ini' );
        $countries = $ini->getNamedArray();
        eZCountryType::fetchTranslatedNames( $countries );
        $GLOBALS['CountryList'] = $countries;
        return $countries;
    }

    /*!
      Fetches translated country names from locale
      \a $countries will be updated.
    */
    static function fetchTranslatedNames( &$countries )
    {
        $locale = eZLocale::instance();
        $translatedCountryNames = $locale->translatedCountryNames();
        foreach ( array_keys( $countries ) as $countryKey )
        {
            $translatedName = isset( $translatedCountryNames[$countryKey] ) ? $translatedCountryNames[$countryKey] : false;
            if ( $translatedName )
                $countries[$countryKey]['Name'] = $translatedName;
        }
        usort( $countries, array( 'eZCountryType', 'compareCountryNames' ) );
    }

    /**
     * Sort callback used by fetchTranslatedNames to compare two country arrays
     *
     * @param array $a Country 1
     * @param array $b Country 2
     * @return bool
     */
    protected static function compareCountryNames( $a, $b )
    {
        return strcoll( $a["Name"], $b["Name"] );
    }

    /*!
      Fetches country by \a $fetchBy.
      if \a $fetchBy is false country name will be used.
    */
    static function fetchCountry( $value, $fetchBy = false )
    {
        $fetchBy = !$fetchBy ? 'Name' : $fetchBy;

        // A code or name is a string: a nested form value is no country. The
        // list is sorted by name (usort renumbers it), so it is searched by the
        // field; looking the code up as a key only ever matched a numeric
        // "code", and "0" or "12" came back as whichever country sorted there
        if ( !is_scalar( $value ) || (string)$value === '' )
            return false;
        $value = (string)$value;

        $allCountries = eZCountryType::fetchCountryList();
        $result = false;
        foreach ( $allCountries as $country )
        {
            if ( isset( $country[$fetchBy] ) and (string)$country[$fetchBy] === $value )
            {
                $result = $country;
                break;
            }
        }

        return $result;
    }

    /**
     * The known alpha-2 codes in the posted value $data (a list, or a single
     * code), in order and without repeats. Anything else is dropped.
     *
     * @param mixed $data
     * @return array alpha-2 code => country
     */
    static function postedCountries( $data )
    {
        if ( !is_array( $data ) )
            $data = array( $data );
        $countries = array();
        foreach ( $data as $alpha2 )
        {
            if ( !is_string( $alpha2 ) || trim( $alpha2 ) == '' )
                continue;
            $eZCountry = eZCountryType::fetchCountry( $alpha2, 'Alpha2' );
            if ( $eZCountry )
                $countries[$alpha2] = $eZCountry;
        }
        return $countries;
    }

    /**
     * True if the posted value $data names at least one existing country: a
     * known alpha-2 code, or (the single string the form posted before 4.x) a
     * known country name.
     */
    static function hasPostedCountry( $data )
    {
        if ( count( eZCountryType::postedCountries( $data ) ) > 0 )
            return true;
        return is_string( $data ) && eZCountryType::fetchCountry( $data, 'Name' ) !== false;
    }

    /**
     * The content 'value' as the comma separated country names metaData(),
     * title() and sortKey() use. An entry is a country array or, from the
     * single-select form input and old data, a plain name or '' (an unknown
     * code); reading 'Name' from those strings was a TypeError.
     */
    static function valueNames( $content )
    {
        $value = is_array( $content ) && array_key_exists( 'value', $content ) ? $content['value'] : null;
        // null (never stored) stays null, as the callers returned it before
        if ( !is_array( $value ) )
            return is_scalar( $value ) || $value === null ? $value : '';
        $imploded = '';
        foreach ( $value as $country )
        {
            if ( is_array( $country ) )
                $countryName = isset( $country['Name'] ) ? (string)$country['Name'] : '';
            else
                $countryName = is_scalar( $country ) ? (string)$country : '';
            if ( $imploded == '' )
                $imploded = $countryName;
            else
                $imploded .= ',' . $countryName;
        }
        return $imploded;
    }

    function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $classAttributeID = $classAttribute->attribute( 'id' );
        $content = $classAttribute->content();

        if ( $http->hasPostVariable( $base . '_ezcountry_multiple_choice_value_' . $classAttribute->attribute( 'id' ) . '_exists' ) )
        {
             $content['multiple_choice'] = $http->hasPostVariable( $base . "_ezcountry_ismultiple_value_" . $classAttributeID ) ? 1 : 0;
        }

        if ( $http->hasPostVariable( $base . '_ezcountry_default_selection_value_' . $classAttribute->attribute( 'id' ) . '_exists' ) )
        {
            if ( $http->hasPostVariable( $base . "_ezcountry_default_country_list_". $classAttributeID ) )
            {
                // Fetch ezcountry by aplha2 code (as reserved in iso-3166 code list)
                $defaultValues = $http->postVariable( $base . "_ezcountry_default_country_list_". $classAttributeID );
                $content['default_countries'] = eZCountryType::postedCountries( $defaultValues );
            }
            else
            {
                $content['default_countries'] = array();
            }
        }
        $classAttribute->setContent( $content );
        $classAttribute->store();
        return true;
    }

    function preStoreClassAttribute( $classAttribute, $version )
    {
        $content = $classAttribute->content();
        return eZCountryType::storeClassAttributeContent( $classAttribute, $content );
    }

    function storeClassAttributeContent( $classAttribute, $content )
    {
        if ( is_array( $content ) )
        {
            $multipleChoice = isset( $content['multiple_choice'] ) ? $content['multiple_choice'] : 0;
            $defaultCountryList = isset( $content['default_countries'] ) && is_array( $content['default_countries'] ) ? $content['default_countries'] : array();
            $defaultCountry = implode( ',', array_keys( $defaultCountryList ) );

            $classAttribute->setAttribute( self::DEFAULT_LIST_FIELD, $defaultCountry );
            $classAttribute->setAttribute( self::MULTIPLE_CHOICE_FIELD, $multipleChoice );
        }
        return false;
    }

    /*!
     Sets the default value.
    */
    function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion != false )
        {
            $dataText = $originalContentObjectAttribute->content();
            $contentObjectAttribute->setContent( $dataText );
        }
        else
        {
            $default = array( 'value' => array() );
            $contentObjectAttribute->setContent( $default );
        }
    }

    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( !$contentObjectAttribute->validateIsRequired() )
            return eZInputValidator::STATE_ACCEPTED;

        if ( $http->hasPostVariable( $base . '_country_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $data = $http->postVariable( $base . '_country_' . $contentObjectAttribute->attribute( 'id' ) );

            // Required means a country that exists: an unknown code was accepted
            // here and then dropped by the fetch, storing nothing
            if ( eZCountryType::hasPostedCountry( $data ) )
                return eZInputValidator::STATE_ACCEPTED;
        }

        $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                             'Input required.' ) );
        return eZInputValidator::STATE_INVALID;
    }

    function validateCollectionAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( !$contentObjectAttribute->validateIsRequired() )
            return eZInputValidator::STATE_ACCEPTED;

        if ( $http->hasPostVariable( $base . '_country_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $data = $http->postVariable( $base . '_country_' . $contentObjectAttribute->attribute( 'id' ) );

            // Required means a country that exists: an unknown code was accepted
            // here and then dropped by the fetch, storing nothing
            if ( eZCountryType::hasPostedCountry( $data ) )
                return eZInputValidator::STATE_ACCEPTED;
        }

        $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                             'Input required.' ) );
        return eZInputValidator::STATE_INVALID;
    }

    /*!
     Fetches the http post var and stores it in the data instance.
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . '_country_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $data = $http->postVariable( $base . '_country_' . $contentObjectAttribute->attribute( 'id' ) );
            $defaultList = array();
            if ( is_array( $data ) )
            {
                // Known codes only; a nested array is no code
                $defaultList = eZCountryType::postedCountries( $data );
            }
            else
            {
                $countries = eZCountryType::fetchCountryList();
                foreach ( $countries as $country )
                {
                    if ( $country['Name'] == $data )
                    {
                        $defaultList[$country['Alpha2']] = $country['Name'];
                    }
                }
            }
            $content = array( 'value' => $defaultList );

            $contentObjectAttribute->setContent( $content );
        }
        else
        {
            $content = array( 'value' => array() );
            $contentObjectAttribute->setContent( $content );
        }
        return true;
    }

    /*!
     Fetches the http post variables for collected information
    */
    function fetchCollectionAttributeHTTPInput( $collection, $collectionAttribute, $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . "_country_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $dataText = $http->postVariable( $base . "_country_" . $contentObjectAttribute->attribute( "id" ) );

            // Only known codes are collected: a single value was a TypeError
            // in implode(), a nested array became "Array"
            $value = implode( ',', array_keys( eZCountryType::postedCountries( $dataText ) ) );
            $collectionAttribute->setAttribute( 'data_text', $value );
            return true;
        }
        return false;
    }

    function storeObjectAttribute( $contentObjectAttribute )
    {
        $content = $contentObjectAttribute->content();

        $valueArray = is_array( $content ) && array_key_exists( 'value', $content ) ? $content['value'] : null;
        $value = is_array( $valueArray ) ? implode( ',', array_keys( $valueArray ) ) : $valueArray;

        $contentObjectAttribute->setAttribute( "data_text", $value );
    }

    /*!
     Simple string insertion is supported.
    */
    function isSimpleStringInsertionSupported()
    {
        return true;
    }

    function insertSimpleString( $object, $objectVersion, $objectLanguage,
                                 $objectAttribute, $string,
                                 &$result )
    {
        $result = array( 'errors' => array(),
                         'require_storage' => true );
        $content = array( 'value' => $string );
        $objectAttribute->setContent( $content );
        return true;
    }

    /*!
     Returns the content.
    */
    function objectAttributeContent( $contentObjectAttribute )
    {
        $value = $contentObjectAttribute->attribute( 'data_text' );

        // data_text is null for an attribute never stored
        $countryList = explode( ',', (string)$value );
        $resultList = array();
        foreach ( $countryList as $alpha2 )
        {
            $eZCountry = eZCountryType::fetchCountry( $alpha2, 'Alpha2' );
            $resultList[$alpha2] = $eZCountry ? $eZCountry : '';
        }
        // Supporting of previous version format.
        // For backwards compatibility.
        if ( count( $resultList ) == 1 and $resultList[(string)$value] == '' )
            $resultList = $value;

        $content = array( 'value' => $resultList );
        return $content;
    }

    function classAttributeContent( $classAttribute )
    {
        $defaultCountry = $classAttribute->attribute( self::DEFAULT_LIST_FIELD );
        $multipleChoice = $classAttribute->attribute( self::MULTIPLE_CHOICE_FIELD );
        $defaultCountryList = explode( ',', (string)$defaultCountry );
        $resultList = array();
        foreach ( $defaultCountryList as $alpha2 )
        {
            $eZCountry = eZCountryType::fetchCountry( $alpha2, 'Alpha2' );
            if ( $eZCountry )
                $resultList[$alpha2] = $eZCountry;
        }
        $content = array( 'default_countries' => $resultList,
                          'multiple_choice' => $multipleChoice );

        return $content;
    }

    /*!
     Returns the meta data used for storing search indeces.
    */
    function metaData( $contentObjectAttribute )
    {
        $content = $contentObjectAttribute->content();
        $content = array( 'value' => eZCountryType::valueNames( $content ) );
        return $content['value'];
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
     Returns the country for use as a title
    */
    function title( $contentObjectAttribute, $name = null )
    {
        $content = $contentObjectAttribute->content();
        $content = array( 'value' => eZCountryType::valueNames( $content ) );
        return $content['value'];
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        $content = $contentObjectAttribute->content();
        $value = is_array( $content ) && isset( $content['value'] ) ? $content['value'] : '';
        $result = ( ( !is_array( $value ) and trim( (string)$value ) != '' ) or ( is_array( $value ) and count( $value ) > 0 ) );
        return $result;
    }

    function isIndexable()
    {
        return true;
    }

    function isInformationCollector()
    {
        return true;
    }

    function sortKey( $contentObjectAttribute )
    {
        $trans = eZCharTransform::instance();
        $content = $contentObjectAttribute->content();
        $content = array( 'value' => eZCountryType::valueNames( $content ) );
        return $trans->transformByGroup( $content['value'], 'lowercase' );
    }

    function sortKeyType()
    {
        return 'string';
    }

    function diff( $old, $new, $options = false )
    {
        return null;
    }

    function supportsBatchInitializeObjectAttribute()
    {
        return true;
    }
}

eZDataType::register( eZCountryType::DATA_TYPE_STRING, 'ezcountrytype' );

?>
