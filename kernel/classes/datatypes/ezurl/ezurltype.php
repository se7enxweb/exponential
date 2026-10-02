<?php
/**
 * File containing the eZURLType class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZURLType ezurltype.php
  \ingroup eZDatatype
  \brief A content datatype which handles urls

*/


class eZURLType extends eZDataType
{
    /**
     * @var \eZIntegerValidator
     */
    public $MaxLenValidator;
    const DATA_TYPE_STRING = 'ezurl';

    public function __construct()
    {
        parent::__construct( self::DATA_TYPE_STRING, ezpI18n::tr( 'kernel/classes/datatypes', 'URL', 'Datatype name' ),
                           array( 'serialize_supported' => true ) );
        $this->MaxLenValidator = new eZIntegerValidator();
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
            $dataText = $originalContentObjectAttribute->attribute( "data_text" );
            $url = $originalContentObjectAttribute->attribute( "content" );
            $contentObjectAttribute->setContent( $url );
            $contentObjectAttribute->setAttribute( "data_text", $dataText );
        }
        else
        {
            $contentClassAttribute = $contentObjectAttribute->contentClassAttribute();
            $default = $contentClassAttribute->attribute( 'data_text1' );
            if ( $default !== '' && $default !== NULL )
            {
                $contentObjectAttribute->setAttribute( 'data_text', $default );
            }
        }
    }

    /*!
     Validates the input and returns true if the input was
     valid for this datatype.
    */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . "_ezurl_url_" . $contentObjectAttribute->attribute( "id" ) )  and
             $http->hasPostVariable( $base . "_ezurl_text_" . $contentObjectAttribute->attribute( "id" ) )
           )
        {
            $url = $http->PostVariable( $base . "_ezurl_url_" . $contentObjectAttribute->attribute( "id" ) );
            $text = $http->PostVariable( $base . "_ezurl_text_" . $contentObjectAttribute->attribute( "id" ) );
            // Both are text fields: an array can only come from a forged form
            // and would fail later where the URL is trimmed and stored
            if ( !is_string( $url ) || !is_string( $text ) )
            {
                $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                     'Input required.' ) );
                return eZInputValidator::STATE_INVALID;
            }
            if ( $contentObjectAttribute->validateIsRequired() )
                if ( trim( $url ) == "" )
                {
                    $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                         'Input required.' ) );
                    return eZInputValidator::STATE_INVALID;
                }
            // The URL becomes the href of a link: a script or data URL would
            // run in the browser of whoever clicks it
            $scheme = eZURLType::unsafeURLScheme( $url );
            if ( $scheme !== false )
            {
                $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                     'Links with the %1 scheme are not allowed.', null, array( $scheme . ':' ) ) );
                return eZInputValidator::STATE_INVALID;
            }
            // Remove all url-object links to this attribute.
            eZURLObjectLink::removeURLlinkList( $contentObjectAttribute->attribute( "id" ), $contentObjectAttribute->attribute('version') );
        }
        else if ( $contentObjectAttribute->validateIsRequired() )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes', 'Input required.' ) );
            return eZInputValidator::STATE_INVALID;
        }
        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     \static
     \return the scheme of \a $url (lower case, without the colon) if it is one
     that runs code or carries inline content when the URL is followed, that is
     javascript:, vbscript:, data: and their old aliases, and false otherwise.

     The scheme is looked for the way a browser finds it: character references
     are decoded (the URL may be output unwashed somewhere) and control
     characters and spaces are dropped, since browsers ignore them at the start
     and tabs and line breaks anywhere in a URL. "Java&#x09;Script:" and
     " \x01javascript:" are therefore caught as well as the plain form.
    */
    static function unsafeURLScheme( $url )
    {
        if ( !is_string( $url ) )
            return false;
        $probe = $url;
        for ( $i = 0; $i < 3; ++$i )
        {
            // Numeric references are decoded with or without the ";", as
            // browsers do in attribute values
            $decoded = preg_replace_callback( '/&#(x[0-9a-f]+|[0-9]+);?/i', function ( $m ) {
                $code = ( $m[1][0] === 'x' || $m[1][0] === 'X' ) ? hexdec( substr( $m[1], 1 ) ) : (int)$m[1];
                return ( $code > 0 && $code < 0x110000 ) ? mb_chr( $code, 'UTF-8' ) : '';
            }, $probe );
            $decoded = html_entity_decode( (string)$decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
            if ( $decoded === $probe )
                break;
            $probe = $decoded;
        }
        $probe = preg_replace( '/[\x00-\x20\x7f]+/', '', (string)$probe );
        if ( preg_match( '/^([a-z][a-z0-9+.\-]*):/i', (string)$probe, $matches ) )
        {
            $scheme = strtolower( $matches[1] );
            if ( in_array( $scheme, array( 'javascript', 'vbscript', 'data', 'livescript', 'mocha' ), true ) )
                return $scheme;
        }
        return false;
    }

    /*!
     \static
     \return true if \a $url may be output as the href of a link.
    */
    static function isLinkableURL( $url )
    {
        return is_string( $url ) && trim( $url ) !== '' && eZURLType::unsafeURLScheme( $url ) === false;
    }

    /*!
     Adds view.url_is_linkable: whether the stored URL may be the href of a
     link. The view templates check it, because values stored before the URL
     was validated (or imported from a package) can still hold a script URL.
    */
    function objectDisplayInformation( $objectAttribute, $mergeInfo = false )
    {
        $info = is_array( $mergeInfo ) ? $mergeInfo : array();
        $info['view']['url_is_linkable'] = eZURLType::isLinkableURL( $objectAttribute->content() );
        return eZDataType::objectDisplayInformation( $objectAttribute, $info );
    }

    function deleteStoredObjectAttribute( $contentObjectAttribute, $version = null )
    {
        $contentObjectAttributeID = $contentObjectAttribute->attribute( 'id' );
        $urls = array();
        if ( $version == null )
        {
            $urls = eZURLObjectLink::fetchLinkList( $contentObjectAttributeID, false, false );
            eZURLObjectLink::removeURLlinkList( $contentObjectAttributeID, false );
        }
        else
        {
            $urls = eZURLObjectLink::fetchLinkList( $contentObjectAttributeID, $version, false );
            eZURLObjectLink::removeURLlinkList( $contentObjectAttributeID, $version );
        }
        $urls = array_unique( $urls );

        $db = eZDB::instance();
        $db->begin();

        foreach ( $urls as $urlID )
        {
            if ( !eZURLObjectLink::hasObjectLinkList( $urlID ) )
            {
                eZURL::removeByID( $urlID );
            }
        }

        $db->commit();
    }

    /*!
     Fetches the http post var url input and stores it in the data instance.
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . '_ezurl_url_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_ezurl_text_' . $contentObjectAttribute->attribute( 'id' ) )
             )
        {
            $url = $http->postVariable( $base . '_ezurl_url_' . $contentObjectAttribute->attribute( 'id' ) );
            $text = $http->postVariable( $base . '_ezurl_text_' . $contentObjectAttribute->attribute( 'id' ) );
            // Validation rejects anything but two strings
            if ( !is_string( $url ) || !is_string( $text ) )
                return false;

            $contentObjectAttribute->setAttribute( 'data_text', $text );

            $contentObjectAttribute->setContent( $url );
            return true;
        }
        return false;
    }

    /*!
      Makes some post-store operations. Called by framework after store of eZContentObjectAttribute object.
    */
    function postStore( $objectAttribute )
    {
        // Update url-object link
        $urlValue = $objectAttribute->content();
        // content() is false when there is no URL, and null on an attribute
        // nothing was set on yet (trim( null ) is deprecated)
        if ( is_string( $urlValue ) && trim( $urlValue ) != '' )
        {
            $urlID = eZURL::registerURL( $urlValue );
            $objectAttributeID = $objectAttribute->attribute( 'id' );
            $objectAttributeVersion = $objectAttribute->attribute( 'version' );

            $db = eZDB::instance();
            $db->begin();

            $objectLinkList = eZURLObjectLink::fetchLinkObjectList( $objectAttributeID, $objectAttributeVersion );

            // In order not to have duplicated links, delete existing ones that have been created during the version creation process
            // and create a clean one (we can't update url_id since there's no primary key). This fixes EZP-20988
            if ( !empty( $objectLinkList ) )
            {
                eZURLObjectLink::removeURLlinkList( $objectAttributeID, $objectAttributeVersion );
            }

            $linkObjectLink = eZURLObjectLink::create( $urlID, $objectAttributeID, $objectAttributeVersion );
            $linkObjectLink->store();

            $db->commit();
        }
    }

    /*!
      Store the URL in the URL database and store the reference to it.
    */
    function storeObjectAttribute( $attribute )
    {
        $urlValue = $attribute->content();
        // content() is false when there is no URL, and null on an attribute
        // nothing was set on yet (trim( null ) is deprecated)
        if ( is_string( $urlValue ) && trim( $urlValue ) != '' )
        {
            $oldURLID = $attribute->attribute( 'data_int' );
            $urlID = eZURL::registerURL( $urlValue );
            $attribute->setAttribute( 'data_int', $urlID );

            if ( $oldURLID && $oldURLID != $urlID &&
                 !eZURLObjectLink::hasObjectLinkList( $oldURLID ) )
                    eZURL::removeByID( $oldURLID );
        }
        else
        {
            $attribute->setAttribute( 'data_int', 0 );
        }

    }

    function storeClassAttribute( $attribute, $version )
    {
    }

    function storeDefinedClassAttribute( $attribute )
    {
    }

    function validateClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     Returns the content.
    */
    function objectAttributeContent( $contentObjectAttribute )
    {
        if ( !$contentObjectAttribute->attribute( 'data_int' ) )
        {
            $attrValue = false;
            return $attrValue;
        }

        $url = eZURL::url( $contentObjectAttribute->attribute( 'data_int' ) );
        return $url;
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        if ( $contentObjectAttribute->attribute( 'data_int' ) == 0 )
            return false;

        $url = eZURL::fetch( $contentObjectAttribute->attribute( 'data_int' ) );
        if ( is_object( $url ) and
             trim( $url->attribute( 'url' ) ) != '' and
             $url->attribute( 'is_valid' ) )
            return true;
        return false;
    }

    /*!
     Returns the meta data used for storing search indeces.
    */
    function metaData( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_text' );
    }

    /*!
     Returns the content of the url for use as a title
    */
    function title( $contentObjectAttribute, $name = null )
    {
        return  $contentObjectAttribute->attribute( 'data_text' );
    }

    function toString( $contentObjectAttribute )
    {
        if ( !$contentObjectAttribute->attribute( 'data_int' ) )
        {
            $attrValue = false;
            return $attrValue;
        }

        $url = eZURL::url( $contentObjectAttribute->attribute( 'data_int' ) );
        $text = $contentObjectAttribute->attribute( 'data_text');
        if ( $text != '' )
        {
            $exportData = $url . '|' . $text;
        }
        else
        {
            $exportData = $url;
        }
        return $exportData;
    }


    function fromString( $contentObjectAttribute, $string )
    {

        if ( $string == '' )
            return true;

        $separatorPos = strpos( $string, '|' );
        // Check if supplied data has a separator which separates url from url text
        if( $separatorPos === false )
        {
            // An import does not pass HTTP validation, so a script URL is
            // refused here (and not registered in the shared URL table)
            if ( eZURLType::unsafeURLScheme( $string ) !== false )
                return false;
            $urlID = eZURL::registerURL( $string );
            $contentObjectAttribute->setAttribute( 'data_int', $urlID );
            return $urlID;
        }
        else
        {
            $url = substr( $string, 0, $separatorPos );
            $text = substr( $string, $separatorPos + 1 );
            if ( eZURLType::unsafeURLScheme( $url ) !== false )
                return false;
            if( $url !== '' )
            {
                $urlID = eZURL::registerURL( $url );
                $contentObjectAttribute->setAttribute( 'data_int', $urlID );
            }

            // A text of "0" is a text too (toString() wrote it)
            if( $text !== '' )
            {
                $contentObjectAttribute->setAttribute( 'data_text', $text );
            }

            return true;
        }
    }

    /*!
     \param package
     \param content attribute

     \return a DOM representation of the content object attribute
    */
    function serializeContentObjectAttribute( $package, $objectAttribute )
    {
        $node = $this->createContentObjectAttributeDOMNode( $objectAttribute );
        $dom = $node->ownerDocument;

        $url = eZURL::fetch( $objectAttribute->attribute( 'data_int' ) );
        if ( is_object( $url ) and
             trim( $url->attribute( 'url' ) ) != '' )
        {
            $urlNode = $dom->createElement( 'url' );
            $urlNode->appendChild( $dom->createTextNode( urlencode( $url->attribute( 'url' ) ) ) );
            $urlNode->setAttribute( 'original-url-md5', $url->attribute( 'original_url_md5' ) );
            $urlNode->setAttribute( 'is-valid', $url->attribute( 'is_valid' ) );
            $urlNode->setAttribute( 'last-checked', $url->attribute( 'last_checked' ) );
            $urlNode->setAttribute( 'created', $url->attribute( 'created' ) );
            $urlNode->setAttribute( 'modified', $url->attribute( 'modified' ) );
            $node->appendChild( $urlNode );
        }

        // A text of "0" is a text as well and must survive the package
        if ( (string)$objectAttribute->attribute( 'data_text' ) !== '' )
        {
            $textNode = $dom->createElement( 'text' );
            $textNode->appendChild( $dom->createTextNode( $objectAttribute->attribute( 'data_text' ) ) );
            $node->appendChild( $textNode );
        }

        return $node;
    }

    /*!
     \param package
     \param contentobject attribute object
     \param domnode object
    */
    function unserializeContentObjectAttribute( $package, $objectAttribute, $attributeNode )
    {
        $urlNode = $attributeNode->getElementsByTagName( 'url' )->item( 0 );

        if ( is_object( $urlNode ) )
        {
            unset( $url );
            $url = urldecode( $urlNode->textContent );

            // A package is not validated like a form: a script URL in it is
            // left out rather than registered and linked
            $urlID = ( trim( $url ) !== '' && eZURLType::unsafeURLScheme( $url ) === false ) ? eZURL::registerURL( $url ) : false;
            $urlObject = $urlID ? eZURL::fetch( $urlID ) : null;
            if ( $urlObject instanceof eZURL )
            {
                // A package that leaves an attribute out keeps what the URL
                // has, instead of an empty string in an integer field
                if ( $urlNode->hasAttribute( 'original-url-md5' ) )
                    $urlObject->setAttribute( 'original_url_md5', $urlNode->getAttribute( 'original-url-md5' ) );
                if ( $urlNode->hasAttribute( 'is-valid' ) )
                    $urlObject->setAttribute( 'is_valid', (int)$urlNode->getAttribute( 'is-valid' ) );
                if ( $urlNode->hasAttribute( 'last-checked' ) )
                    $urlObject->setAttribute( 'last_checked', (int)$urlNode->getAttribute( 'last-checked' ) );
                $urlObject->setAttribute( 'created', time() );
                $urlObject->setAttribute( 'modified', time() );
                $urlObject->store();

                $objectAttribute->setAttribute( 'data_int', $urlID );
            }
        }

        $textNode = $attributeNode->getElementsByTagName( 'text' )->item( 0 );
        if ( $textNode )
            $objectAttribute->setAttribute( 'data_text', $textNode->textContent );
    }

    function supportsBatchInitializeObjectAttribute()
    {
        return true;
    }
}

eZDataType::register( eZURLType::DATA_TYPE_STRING, 'eZURLType' );

?>
