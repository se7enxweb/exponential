<?php
/**
 * File containing the eZAuthorType class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZAuthorType ezauthortype.php
  \ingroup eZDatatype
  \brief eZAuthorType handles multiple authors

*/

class eZAuthorType extends eZDataType
{
    const DATA_TYPE_STRING = "ezauthor";

    /// The most authors one attribute takes from a form
    const MAX_AUTHORS = 1000;

    public function __construct()
    {
        parent::__construct( self::DATA_TYPE_STRING, ezpI18n::tr( 'kernel/classes/datatypes', "Authors", 'Datatype name' ),
                           array( 'serialize_supported' => true ) );
    }

    /*!
     Validates the input and returns true if the input was
     valid for this datatype.
    */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $actionRemoveSelected = false;
        if ( $http->hasPostVariable( 'CustomActionButton' ) )
        {
            $customActionArray = $http->postVariable( 'CustomActionButton' );

            if ( isset( $customActionArray[$contentObjectAttribute->attribute( "id" ) . '_remove_selected'] ) )
                if ( $customActionArray[$contentObjectAttribute->attribute( "id" ) . '_remove_selected'] == 'Remove selected' )
                    $actionRemoveSelected = true;
        }

        $rows = $this->authorHTTPInput( $http, $base, $contentObjectAttribute );
        if ( $rows !== false )
        {
            if ( $http->hasPostVariable( $base . "_data_author_remove_" . $contentObjectAttribute->attribute( "id" ) ) )
                $removeList = $http->postVariable( $base . "_data_author_remove_" . $contentObjectAttribute->attribute( "id" ) );
            else
                $removeList = array();
            $removeList = is_array( $removeList ) ? array_filter( $removeList, 'is_scalar' ) : array();

            if ( count( $rows ) > self::MAX_AUTHORS )
            {
                $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                     'The author list can have at most %1 authors.' ), self::MAX_AUTHORS );
                return eZInputValidator::STATE_INVALID;
            }

            $firstName = isset( $rows[0] ) ? $rows[0]['name'] : '';
            if ( $contentObjectAttribute->validateIsRequired() )
            {
                if ( trim( $firstName ) == "" )
                {
                    $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                         'At least one author is required.' ) );
                    return eZInputValidator::STATE_INVALID;
                }
            }
            if ( trim( $firstName ) != "" )
            {
                foreach ( $rows as $row )
                {
                    if ( $actionRemoveSelected )
                        if ( in_array( $row['id'], $removeList ) )
                            continue;

                    $name =  $row['name'];
                    $email =  $row['email'];
                    if ( trim( $name )== "" )
                    {
                        $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                             'The author name must be provided.' ) );
                        return eZInputValidator::STATE_INVALID;

                    }
                    $isValidate =  eZMail::validate( $email );
                    if ( ! $isValidate )
                    {
                        $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                             'The email address is not valid.' ) );
                        return eZInputValidator::STATE_INVALID;
                    }
                }
            }
        }
        else
        {
            if ( $contentObjectAttribute->validateIsRequired() )
            {
                $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                     'At least one author is required.' ) );
                return eZInputValidator::STATE_INVALID;
            }
        }
        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     Store content
    */
    function storeObjectAttribute( $contentObjectAttribute )
    {
        $author = $contentObjectAttribute->content();
        $contentObjectAttribute->setAttribute( "data_text", $author->xmlString() );
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
    }

    /*!
     Returns the content.
    */
    function objectAttributeContent( $contentObjectAttribute )
    {
        $author = new eZAuthor( );

        if ( trim( $contentObjectAttribute->attribute( "data_text" ) ) != "" )
        {
            $author->decodeXML( $contentObjectAttribute->attribute( "data_text" ) );
            $temp = $contentObjectAttribute->attribute( "data_text");
        }
        else
        {
            $user = eZUser::currentUser();
            $userobject = $user->attribute( 'contentobject' );
            if ( $userobject )
            {
                $author->addAuthor( $userobject->attribute( 'id' ), $userobject->attribute( 'name' ), $user->attribute( 'email' ) );
            }
         }

        if ( count( $author->attribute( 'author_list' ) ) == 0 )
        {
//             $author->addAuthor( "Default", "" );
        }

        return $author;
    }


    /*!
     Returns the meta data used for storing search indeces.
    */
    function metaData( $contentObjectAttribute )
    {
        $author = $contentObjectAttribute->content();
        if ( !$author )
            return false;

        return $author->metaData();
    }

    function toString( $contentObjectAttribute )
    {
        $authorList = array();
        $content = $contentObjectAttribute->attribute( 'content' );
        foreach ( $content->attribute( 'author_list') as $author )
        {
            $authorList[] = eZStringUtils::implodeStr( array( $author['name'], $author['email'],$author['id'] ), '|' );
        }
        return eZStringUtils::implodeStr( $authorList, "&" );
    }

    function fromString( $contentObjectAttribute, $string )
    {
        // Anything but a string is an empty list (explodeStr() of an array is a TypeError)
        $authorList = is_scalar( $string ) ? eZStringUtils::explodeStr( (string)$string, '&' ) : array();

        $author = new eZAuthor( );


        foreach ( $authorList as $authorStr )
        {
            // An empty string is no author; a short entry (name only, or name
            // and email) gets the missing parts as '' and a new id
            if ( $authorStr === '' )
                continue;
            $authorData = eZStringUtils::explodeStr( $authorStr, '|' );
            $author->addAuthor( isset( $authorData[2] ) && $authorData[2] !== '' ? $authorData[2] : -1,
                                $authorData[0],
                                isset( $authorData[1] ) ? $authorData[1] : '' );

        }
        $contentObjectAttribute->setContent( $author );
        return $author;
    }

    /*!
     Fetches the http post var integer input and stores it in the data instance.
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $rows = $this->authorHTTPInput( $http, $base, $contentObjectAttribute );
        if ( $rows !== false )
        {
            $author = new eZAuthor( );

            // Beyond the limit validation refuses the list; what is kept for
            // the redisplayed form stays bounded as well
            foreach ( array_slice( $rows, 0, self::MAX_AUTHORS ) as $row )
            {
                $author->addAuthor( $row['id'], $row['name'], $row['email'] );
            }
            $contentObjectAttribute->setContent( $author );
        }
        return true;
    }

    /**
     * The author rows posted for $contentObjectAttribute, or false when the form
     * did not post the list. The id, name and email lists are separate post
     * variables: one that is missing, not a list, or shorter than the id list
     * gives '' instead of undefined offsets, and a nested array gives ''.
     *
     * @return array|false array( array( 'id' => .., 'name' => .., 'email' => .. ), ... )
     */
    protected function authorHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $attributeID = $contentObjectAttribute->attribute( "id" );
        if ( !$http->hasPostVariable( $base . "_data_author_id_" . $attributeID ) )
            return false;

        $lists = array();
        foreach ( array( 'id', 'name', 'email' ) as $field )
        {
            $name = $base . "_data_author_" . $field . "_" . $attributeID;
            $list = $http->hasPostVariable( $name ) ? $http->postVariable( $name ) : array();
            if ( !is_array( $list ) )
                $list = array( $list );
            $lists[$field] = array_values( $list );
        }

        $rows = array();
        foreach ( $lists['id'] as $i => $id )
        {
            $rows[] = array( 'id' => is_scalar( $id ) ? (string)$id : '',
                             'name' => isset( $lists['name'][$i] ) ? eZAuthor::cleanText( $lists['name'][$i] ) : '',
                             'email' => isset( $lists['email'][$i] ) ? eZAuthor::cleanText( $lists['email'][$i] ) : '' );
        }
        return $rows;
    }

    function customObjectAttributeHTTPAction( $http, $action, $contentObjectAttribute, $parameters )
    {
        switch ( $action )
        {
            case "new_author" :
            {
                $author = $contentObjectAttribute->content( );

                $author->addAuthor( -1, "", "" );
                $contentObjectAttribute->setContent( $author );
            }break;
            case "remove_selected" :
            {
                $author = $contentObjectAttribute->content( );
                $postvarname = $parameters['base_name'] . "_data_author_remove_" . $contentObjectAttribute->attribute( "id" );
                if ( !$http->hasPostVariable( $postvarname ) )
                    break;
                $array_remove = $http->postVariable( $postvarname );

                $author->removeAuthors( $array_remove );
                $contentObjectAttribute->setContent( $author );
            }break;
            default :
            {
                eZDebug::writeError( "Unknown custom HTTP action: " . $action, "eZAuthorType" );
            }break;
        }
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        $author = $contentObjectAttribute->content( );
        $authorList = $author->attribute( 'author_list' );
        return count( $authorList ) > 0;
    }

    /*!
     Returns the string value.
    */
    function title( $contentObjectAttribute, $name = null )
    {
        $author = $contentObjectAttribute->content( );
        $name = $author->attribute( 'name' );
        if ( trim( (string)$name ) == '' )
        {
            $authorList = $author->attribute( 'author_list' );
            if ( is_array( $authorList ) and isset( $authorList[0]['name'] ) )
            {
                $name = $authorList[0]['name']; // Get the first name of Auhtors
                $author->setName( $name );
            }
        }
        return $name;
    }

    function isIndexable()
    {
        return true;
    }

    function serializeContentObjectAttribute( $package, $objectAttribute )
    {
        $node = $this->createContentObjectAttributeDOMNode( $objectAttribute );

        // An attribute never stored (empty data_text) or with broken XML is
        // exported as an empty author list: loadXML( '' ) throws in PHP 8 and a
        // failed parse has no document element to import
        $dataText = $objectAttribute->attribute( 'data_text' );
        $dom = new DOMDocument( '1.0', 'utf-8' );
        $success = false;
        if ( is_string( $dataText ) && trim( $dataText ) !== '' )
        {
            $useErrors = libxml_use_internal_errors( true );
            $success = $dom->loadXML( $dataText );
            libxml_clear_errors();
            libxml_use_internal_errors( $useErrors );
        }
        if ( !$success || !$dom->documentElement )
        {
            $author = new eZAuthor();
            $dom->loadXML( $author->xmlString() );
        }

        $nodeDOM = $node->ownerDocument;
        $importedElement = $nodeDOM->importNode( $dom->documentElement, true );
        $node->appendChild( $importedElement );

        return $node;
    }

    function unserializeContentObjectAttribute( $package, $objectAttribute, $attributeNode )
    {
        // A package without the <ezauthor> element gives an empty author list
        $rootNode = $attributeNode->getElementsByTagName( 'ezauthor' )->item( 0 );
        if ( $rootNode )
            $xmlString = $rootNode->ownerDocument->saveXML( $rootNode );
        else
        {
            $author = new eZAuthor();
            $xmlString = $author->xmlString();
        }
        $objectAttribute->setAttribute( 'data_text', $xmlString );
    }

    function supportsBatchInitializeObjectAttribute()
    {
        return true;
    }
}

eZDataType::register( eZAuthorType::DATA_TYPE_STRING, "eZAuthorType" );

?>
