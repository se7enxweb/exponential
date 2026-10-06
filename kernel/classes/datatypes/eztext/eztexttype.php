<?php
/**
 * File containing the eZTextType class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZTextType eztexttype.php
  \ingroup eZDatatype
  \brief Stores a text area value

*/

class eZTextType extends eZDataType
{
    const DATA_TYPE_STRING = "eztext";
    const COLS_FIELD = 'data_int1';
    const COLS_VARIABLE = '_eztext_cols_';
    // The preferred number of rows the class edit form offers is 2..25; a
    // value outside what a textarea can sensibly show is not stored
    const COLS_DEFAULT = 10;
    const COLS_MAX = 1000;

    public function __construct()
    {
        parent::__construct( self::DATA_TYPE_STRING, ezpI18n::tr( 'kernel/classes/datatypes', "Text block", 'Datatype name' ),
                           array( 'serialize_supported' => true,
                                  'object_serialize_map' => array( 'data_text' => 'text' ) ) );
    }

    /*!
     Set class attribute value for template version
    */
    function initializeClassAttribute( $classAttribute )
    {
        if ( $classAttribute->attribute( self::COLS_FIELD ) == null )
            $classAttribute->setAttribute( self::COLS_FIELD, 10 );
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
        $contentClassAttribute = $contentObjectAttribute->contentClassAttribute();
        if ( $contentClassAttribute->attribute( "data_int1" ) == 0 )
        {
            $contentClassAttribute->setAttribute( "data_int1", 10 );
            $contentClassAttribute->store();
        }
    }

    /*!
     Validates the input and returns true if the input was
     valid for this datatype.
    */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $classAttribute = $contentObjectAttribute->contentClassAttribute();
        if ( $http->hasPostVariable( $base . '_data_text_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $data = self::postedText( $http->postVariable( $base . '_data_text_' . $contentObjectAttribute->attribute( 'id' ) ) );

            if ( trim( $data ) == "" )
            {
                if ( !$classAttribute->attribute( 'is_information_collector' ) and
                     $contentObjectAttribute->validateIsRequired() )
                {
                    $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                         'Input required.' ) );
                    return eZInputValidator::STATE_INVALID;
                }
            }
        }
        else if ( !$classAttribute->attribute( 'is_information_collector' ) and $contentObjectAttribute->validateIsRequired() )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes', 'Input required.' ) );
            return eZInputValidator::STATE_INVALID;
        }

        return eZInputValidator::STATE_ACCEPTED;
    }

    function validateCollectionAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . '_data_text_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $data = self::postedText( $http->postVariable( $base . '_data_text_' . $contentObjectAttribute->attribute( 'id' ) ) );
            $classAttribute = $contentObjectAttribute->contentClassAttribute();

            if ( trim( $data ) == "" )
            {
                if ( $contentObjectAttribute->validateIsRequired() )
                {
                    $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                         'Input required.' ) );
                    return eZInputValidator::STATE_INVALID;
                }
            }
            return eZInputValidator::STATE_ACCEPTED;
        }
        else
            return eZInputValidator::STATE_INVALID;
    }

    /*!
     Fetches the http post var string input and stores it in the data instance.
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . "_data_text_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $data = self::postedText( $http->postVariable( $base . "_data_text_" . $contentObjectAttribute->attribute( "id" ) ) );
            $contentObjectAttribute->setAttribute( "data_text", $data );
            return true;
        }
        return false;
    }

    /*!
     Fetches the http post variables for collected information
    */
    function fetchCollectionAttributeHTTPInput( $collection, $collectionAttribute, $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . "_data_text_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $dataText = self::postedText( $http->postVariable( $base . "_data_text_" . $contentObjectAttribute->attribute( "id" ) ) );
            $collectionAttribute->setAttribute( 'data_text', $dataText );
            return true;
        }
        return false;
    }

    /*!
     Store the content.
    */
    function storeObjectAttribute( $attribute )
    {
    }

    /*!
     Simple string insertion is supported.
    */
    function isSimpleStringInsertionSupported()
    {
        return true;
    }

    /*!
     Inserts the string \a $string in the \c 'data_text' database field.
    */
    function insertSimpleString( $object, $objectVersion, $objectLanguage,
                                 $objectAttribute, $string,
                                 &$result )
    {
        $result = array( 'errors' => array(),
                         'require_storage' => true );
        $objectAttribute->setContent( $string );
        $objectAttribute->setAttribute( 'data_text', $string );
        return true;
    }

    /*!
     Returns the content.
    */
    function objectAttributeContent( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( "data_text" );
    }

    function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $column = $base . self::COLS_VARIABLE . $classAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $column ) )
        {
            $classAttribute->setAttribute( self::COLS_FIELD, self::columnCount( $http->postVariable( $column ) ) );
            return true;
        }
        return false;
    }

    /*!
     Returns the meta data used for storing search indeces.
    */
    function metaData( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( "data_text" );
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

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        // data_text is null on an attribute that was never stored
        return trim( (string)$contentObjectAttribute->attribute( 'data_text' ) ) != '';
    }

    /*!
     Returns the text.
    */
    function title( $data_instance, $name = null )
    {
        return $data_instance->attribute( "data_text" );
    }

    function isIndexable()
    {
        return true;
    }

    function isInformationCollector()
    {
        return true;
    }

    function serializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $dom = $attributeParametersNode->ownerDocument;
        $textColumns = $classAttribute->attribute( self::COLS_FIELD );

        $textColumnCountNode = $dom->createElement( 'text-column-count' );
        $textColumnCountNode->appendChild( $dom->createTextNode( $textColumns ) );
        $attributeParametersNode->appendChild( $textColumnCountNode );
    }

    function unserializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        // A package that leaves the element out gets the default instead of a
        // fatal error on ->textContent of nothing
        $textColumnCountNode = $attributeParametersNode->getElementsByTagName( 'text-column-count' )->item( 0 );
        $textColumns = $textColumnCountNode ? self::columnCount( $textColumnCountNode->textContent ) : self::COLS_DEFAULT;
        $classAttribute->setAttribute( self::COLS_FIELD, $textColumns );
    }

    function diff( $old, $new, $options = false )
    {
        $diff = new eZDiff();
        $diff->setDiffEngineType( $diff->engineType( 'text' ) );
        $diff->initDiffEngine();
        $diffObject = $diff->diff( $old->content(), $new->content() );
        return $diffObject;
    }

    function supportsBatchInitializeObjectAttribute()
    {
        return true;
    }

    /*!
     \private
     \return the posted text as a string. A field posted as an array (name[]=)
     is not text; it is taken as empty instead of reaching the database or a
     string function as an array.
    */
    static function postedText( $value )
    {
        if ( is_array( $value ) || is_object( $value ) )
            return '';
        return (string)$value;
    }

    /*!
     \private
     \return the preferred number of rows as an integer in 1..COLS_MAX, or the
     default when \a $value is not a positive whole number. The value is
     printed into the rows attribute of the textarea, so it must be a number.
    */
    static function columnCount( $value )
    {
        if ( is_array( $value ) || is_object( $value ) )
            return self::COLS_DEFAULT;
        $value = trim( (string)$value );
        if ( !preg_match( '/^[0-9]+$/', $value ) || (int)$value < 1 )
            return self::COLS_DEFAULT;
        return min( (int)$value, self::COLS_MAX );
    }

    public $Attributes;
}

eZDataType::register( eZTextType::DATA_TYPE_STRING, "eZTextType" );

?>
