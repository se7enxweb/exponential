<?php
/**
 * File containing the eZPriceType class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZPriceType ezpricetype.php
  \ingroup eZDatatype
  \brief Stores a price (float)

*/

class eZPriceType extends eZDataType
{
    const DATA_TYPE_STRING = "ezprice";
    const INCLUDE_VAT_FIELD = 'data_int1';
    const INCLUDE_VAT_VARIABLE = '_ezprice_include_vat_';
    const VAT_ID_FIELD = 'data_float1';
    const VAT_ID_VARIABLE = '_ezprice_vat_id_';
    const INCLUDED_VAT = 1;
    const EXCLUDED_VAT = 2;

    public function __construct()
    {
        parent::__construct( self::DATA_TYPE_STRING, ezpI18n::tr( 'kernel/classes/datatypes', "Price", 'Datatype name' ),
                           array( 'serialize_supported' => true,
                                  'object_serialize_map' => array( 'data_float' => 'price' ) ) );
    }

    /*!
     Validates the input and returns true if the input was
     valid for this datatype.
    */
    /*!
     \private
     \return the posted value of \a $name as a string, or null when it is missing or
     not a scalar: a request can post an array under any name (name[]=x), which
     trim() in eZLocale::internalCurrency() refuses with a TypeError.
    */
    static function postedScalar( $http, $name )
    {
        if ( !$http->hasPostVariable( $name ) )
            return null;
        $value = $http->postVariable( $name );
        return is_scalar( $value ) ? (string)$value : null;
    }

    /*!
     \private
     \return the "VAT type id,inc/ex VAT" pair stored in data_text for the posted
     values. Both are integers in the form (a VAT type id or -1 for dynamic VAT,
     and 1 or 2); anything else posted was stored as it came, commas and all.
    */
    static function vatDataText( $vatType, $vatExInc )
    {
        $vatType = is_numeric( $vatType ) ? (int)$vatType : '';
        $vatExInc = is_numeric( $vatExInc ) ? (int)$vatExInc : '';
        return $vatType . ',' . $vatExInc;
    }

    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        // Check "price inc/ex VAT" and "VAT type" fields.
        $vatTypeID = self::postedScalar( $http, $base . '_ezprice_vat_id_' . $contentObjectAttribute->attribute( 'id' ) );
        $vatExInc = self::postedScalar( $http, $base . '_ezprice_inc_ex_vat_' . $contentObjectAttribute->attribute( 'id' ) );
        if ( $vatExInc == 1 && $vatTypeID == -1 )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                 'Dynamic VAT cannot be included.' ) );
            return eZInputValidator::STATE_INVALID;
        }

        // Check price.
        if ( $http->hasPostVariable( $base . "_data_price_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $data = self::postedScalar( $http, $base . "_data_price_" . $contentObjectAttribute->attribute( "id" ) );
            if ( $data === null )
            {
                $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                     'Invalid price.' ) );
                return eZInputValidator::STATE_INVALID;
            }

            $locale = eZLocale::instance();
            $data = $locale->internalCurrency( $data );
            $classAttribute = $contentObjectAttribute->contentClassAttribute();
            if( !$contentObjectAttribute->validateIsRequired() && ( $data == "" ) )
            {
                return eZInputValidator::STATE_ACCEPTED;
            }
            // The decimal point is escaped: an unescaped "." took any character, so
            // "12x50" or a "12,50" the locale did not convert passed as a price
            // and reached the float column as something else than was typed
            if ( preg_match( "#^[0-9]+(\.[0-9]{0,2})?$#", $data ) and is_finite( (float)$data ) )
                return eZInputValidator::STATE_ACCEPTED;

            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                 'Invalid price.' ) );
            return eZInputValidator::STATE_INVALID;
        }
        else if ( $contentObjectAttribute->validateIsRequired() )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes', 'Input required.' ) );
            return eZInputValidator::STATE_INVALID;
        }
        else
        {
            return eZInputValidator::STATE_ACCEPTED;
        }
    }

    function storeObjectAttribute( $attribute )
    {
    }

    function metaData( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( "data_float" );
    }

    /*!
     reimp
    */
    function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion != false )
        {
            $dataText = $originalContentObjectAttribute->attribute( "data_text" );
            $dataFloat = $originalContentObjectAttribute->attribute( "data_float" );
            $contentObjectAttribute->setAttribute( "data_float", $dataFloat );
            $contentObjectAttribute->setAttribute( "data_text", $dataText );
        }
    }

    /*!
     Set default class attribute value
    */
    function initializeClassAttribute( $classAttribute )
    {
        if ( $classAttribute->attribute( self::INCLUDE_VAT_FIELD ) == 0 )
            $classAttribute->setAttribute( self::INCLUDE_VAT_FIELD, self::INCLUDED_VAT );
        $classAttribute->store();
    }
    function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $isVatIncludedVariable = $base . self::INCLUDE_VAT_VARIABLE . $classAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $isVatIncludedVariable ) )
        {
            // 1 (inc. VAT) or 2 (ex. VAT) are the only values the form offers
            $isVatIncluded = self::postedScalar( $http, $isVatIncludedVariable ) == self::EXCLUDED_VAT ? self::EXCLUDED_VAT : self::INCLUDED_VAT;
            $classAttribute->setAttribute( self::INCLUDE_VAT_FIELD, $isVatIncluded );
        }
        $vatIDVariable = $base . self::VAT_ID_VARIABLE . $classAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $vatIDVariable  ) )
        {
            // A VAT type id, or -1 for dynamic VAT: an array or a word is neither
            $vatID = self::postedScalar( $http, $vatIDVariable  );
            if ( is_numeric( $vatID ) )
                $classAttribute->setAttribute( self::VAT_ID_FIELD, (int)$vatID );
        }
        return true;
    }

    /*!
     Fetches the http post var integer input and stores it in the data instance.
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $data = self::postedScalar( $http, $base . "_data_price_" . $contentObjectAttribute->attribute( "id" ) );
        // No price field in the request (another form posting to the same
        // object): nothing to fetch, instead of storing a null price and ","
        if ( $data === null )
            return false;
        $vatType = self::postedScalar( $http, $base . '_ezprice_vat_id_' . $contentObjectAttribute->attribute( 'id' ) );
        $vatExInc = self::postedScalar( $http, $base . '_ezprice_inc_ex_vat_' . $contentObjectAttribute->attribute( 'id' ) );

        $locale = eZLocale::instance();
        $data = $locale->internalCurrency( $data );

        $data_text = self::vatDataText( $vatType, $vatExInc );

        $contentObjectAttribute->setAttribute( "data_float", $data );
        $contentObjectAttribute->setAttribute( 'data_text', $data_text );

        return true;
    }

    /*!
     Returns the content.
    */
    function objectAttributeContent( $contentObjectAttribute )
    {
        $classAttribute = $contentObjectAttribute->contentClassAttribute();
        $storedPrice = $contentObjectAttribute->attribute( "data_float" );
        $price = new eZPrice( $classAttribute, $contentObjectAttribute, $storedPrice );

        if ( $contentObjectAttribute->attribute( 'data_text' ) != '' )
        {
            // A value without the comma (written by hand or an import) has no
            // inc/ex part; list() warned "Undefined array key 1" on it
            list( $vatType, $vatExInc ) = explode( ',', $contentObjectAttribute->attribute( "data_text" ), 2 ) + array( '', '' );

            $price->setAttribute( 'selected_vat_type', $vatType );
            $price->setAttribute( 'is_vat_included', $vatExInc );
        }

        return $price;
    }

    /*!
     Returns class content.
    */
    function classAttributeContent( $classAttribute )
    {
        $contentObjectAttribute = false;
        $price = new eZPrice( $classAttribute, $contentObjectAttribute );
        return $price;
    }

    /**
     * Return content action(s) which can be performed on object containing
     * the current datatype. Return format is array of arrays with key 'name'
     * and 'action'. 'action' can be mapped to url in datatype.ini
     *
     * @param eZContentClassAttribute $classAttribute
     * @return array
    */
    function contentActionList( $classAttribute )
    {
        $actionList = parent::contentActionList( $classAttribute );
        $actionList[] = array( 'name' => ezpI18n::tr( 'kernel/classes/datatypes', 'Add to basket' ),
                               'action' => 'ActionAddToBasket'
        );
        $actionList[] = array( 'name' => ezpI18n::tr( 'kernel/classes/datatypes', 'Add to wish list' ),
                               'action' => 'ActionAddToWishList'
        );
        return $actionList;
    }

    function title( $contentObjectAttribute, $name = null )
    {
        return $contentObjectAttribute->attribute( "data_float" );
    }

    function sortKey( $contentObjectAttribute )
    {
        $intPrice = (int)($contentObjectAttribute->attribute( 'data_float' ) * 100.00);
        return $intPrice;
    }

    function sortKeyType()
    {
        return 'int';
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        return true;
    }

    function toString( $contentObjectAttribute )
    {

        $price = $contentObjectAttribute->attribute( 'content' );
        $vatType =$price->attribute( 'selected_vat_type' );
        // No VAT type selected, or one since deleted: its id is empty rather
        // than a fatal "attribute() on null" that stopped the whole export
        $vatTypeID = is_object( $vatType ) ? $vatType->attribute( 'id' ) : '';

        $priceStr = implode( '|', array( $price->attribute( 'price' ), $vatTypeID , ($price->attribute( 'is_vat_included' ) )? 1:0 ) );
        return $priceStr;
    }


    function fromString( $contentObjectAttribute, $string )
    {
        if ( $string == '' )
            return true;

        $priceData = explode( '|', $string );
        if ( count( $priceData ) != 3 )
            return false;
        // A price that is not a number was stored as 0; refuse it instead
        if ( !is_numeric( trim( $priceData[0] ) ) or !is_finite( (float)$priceData[0] ) )
            return false;

        // toString() writes "is VAT included" as 1 or 0, the form stores 1 (inc.)
        // or 2 (ex. VAT); both read the same, store the form's 2 for an exported 0
        $vatExInc = trim( $priceData[2] );
        if ( $vatExInc === '0' )
            $vatExInc = (string)self::EXCLUDED_VAT;
        $dataText = self::vatDataText( trim( $priceData[1] ), $vatExInc );
        $price = trim( $priceData[0] );

        $contentObjectAttribute->setAttribute( "data_float", $price );
        $contentObjectAttribute->setAttribute( 'data_text', $dataText );

        return true;
    }

    function serializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $price = $classAttribute->content();
        if ( $price )
        {
            $vatIncluded = $price->attribute( 'is_vat_included' );
            $vatTypes = $price->attribute( 'vat_type' );

            $dom = $attributeParametersNode->ownerDocument;
            $vatIncludedNode = $dom->createElement( 'vat-included' );
            $vatIncludedNode->setAttribute( 'is-set', $vatIncluded ? 'true' : 'false' );
            $attributeParametersNode->appendChild( $vatIncludedNode );
            $vatTypeNode = $dom->createElement( 'vat-type' );
            $chosenVatType = $classAttribute->attribute( 'data_float1' );
            $gotVat = false;
            foreach ( $vatTypes as $vatType )
            {
                $id = $vatType->attribute( 'id' );
                if ( $id == $chosenVatType )
                {
                    $vatTypeNode->setAttribute( 'name', $vatType->attribute( 'name' ) );
                    $vatTypeNode->setAttribute( 'percentage', $vatType->attribute( 'percentage' ) );
                    $gotVat = true;
                    break;
                }
            }
            if ( $gotVat )
                $attributeParametersNode->appendChild( $vatTypeNode );
        }
    }

    function unserializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $vatNode = $attributeParametersNode->getElementsByTagName( 'vat-included' )->item( 0 );
        if ( $vatNode instanceof DOMElement )
        {
            $vatIncluded = strtolower( $vatNode->getAttribute( 'is-set' ) ) == 'true';
            if ( $vatIncluded )
                $vatIncluded = self::INCLUDED_VAT;
            else
                $vatIncluded = self::EXCLUDED_VAT;

            $classAttribute->setAttribute( self::INCLUDE_VAT_FIELD, $vatIncluded );
        }
        $vatTypeNode = $attributeParametersNode->getElementsByTagName( 'vat-type' )->item( 0 );
        // serializeContentClassAttribute() leaves <vat-type> out when the class
        // attribute has no VAT type, so a package made from such a class stopped
        // the import with "getAttribute() on null"; it keeps no VAT type instead
        if ( !$vatTypeNode instanceof DOMElement )
            return;
        $vatName = $vatTypeNode->getAttribute( 'name' );
        $vatPercentage = $vatTypeNode->getAttribute( 'percentage' );
        $vatID = false;
        $vatTypes = eZVatType::fetchList();
        foreach ( $vatTypes as $vatType )
        {
            if ( $vatType->attribute( 'name' ) == $vatName and
                 $vatType->attribute( 'percentage' ) == $vatPercentage )
            {
                $vatID = $vatType->attribute( 'id' );
                break;
            }
        }
        if ( !$vatID )
        {
            $vatType = eZVatType::create();
            $vatType->setAttribute( 'name', $vatName );
            $vatType->setAttribute( 'percentage', $vatPercentage );
            $vatType->store();
            $vatID = $vatType->attribute( 'id' );
        }
        $classAttribute->setAttribute( self::VAT_ID_FIELD, $vatID );
    }

    function supportsBatchInitializeObjectAttribute()
    {
        return true;
    }
}

eZDataType::register( eZPriceType::DATA_TYPE_STRING, "eZPriceType" );

?>
