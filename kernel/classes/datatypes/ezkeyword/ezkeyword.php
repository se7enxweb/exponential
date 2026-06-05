<?php
/**
 * File containing the eZKeyword class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZKeyword ezkeyword.php
  \ingroup eZDatatype
  \brief A content datatype which handles keyword index instances

*/

class eZKeyword
{
    function attributes()
    {
        return array( 'keywords',
                      'keyword_string',
                      'related_objects',
                      'related_nodes' );
    }

    function hasAttribute( $name )
    {
        return in_array( $name, $this->attributes() );
    }

    function attribute( $name )
    {
        switch ( $name )
        {
            case 'keywords' :
            {
                return $this->KeywordArray;
            }break;

            case 'keyword_string' :
            {
                return $this->keywordString();
            }break;

            case 'related_objects' :
            case 'related_nodes' :
            {
                return $this->relatedObjects();
            }break;
            default:
            {
                eZDebug::writeError( "Attribute '$name' does not exist", __METHOD__ );
                return null;
            }break;
        }
    }

    /*!
     Initialze the keyword index
    */
    function initializeKeyword( $keywordString )
    {
        if ( !is_array( $keywordString ) )
        {
            $keywordArray = explode( ',', $keywordString );
            $keywordArray = array_unique ( $keywordArray );
        }
        foreach ( array_keys( $keywordArray ) as $key )
        {
            if ( trim( $keywordArray[$key] ) != '' )
            {
                $this->KeywordArray[$key] = trim( $keywordArray[$key] );
            }
        }
    }

    /*!
     Stores the keyword index to database
    */
    function store( $attribute )
    {
        $db = eZDB::instance();

        $object = $attribute->attribute( 'object' );
        $classID = (int)$object->attribute( 'contentclass_id' );
        $attributeID = $attribute->attribute( 'id' );

        if ( $db->databaseName() === 'mongo' )
        {
            // --- 1. Resolve existing keyword records for the current keyword texts ---
            $existingWords = array();
            if ( count( $this->KeywordArray ) > 0 )
            {
                $rows = $db->aggregate( 'ezkeyword', [
                    [ '$match'   => [ 'keyword' => [ '$in' => array_values( $this->KeywordArray ) ],
                                      'class_id' => $classID ] ],
                    [ '$project' => [ '_id' => 0, 'id' => 1, 'keyword' => 1 ] ],
                ] );
                foreach ( $rows as $r )
                    $existingWords[] = $r;
            }

            // Split into truly-new keywords vs already-existing ones
            $newWordArray      = array();
            $existingWordArray = array();
            foreach ( $this->KeywordArray as $keyword )
            {
                $found = false;
                foreach ( $existingWords as $ew )
                {
                    if ( $ew['keyword'] == $keyword )
                    {
                        $existingWordArray[] = [ 'keyword' => $keyword, 'id' => (int)$ew['id'] ];
                        $found = true;
                        break;
                    }
                }
                if ( !$found )
                    $newWordArray[] = $keyword;
            }

            // --- 2. Insert new keywords, collect their IDs ---
            $addRelationWordArray = array();
            foreach ( $newWordArray as $keyword )
            {
                $keyword   = trim( $keyword );
                $keywordID = $db->nextAtomicID( 'ezkeyword' );
                $db->insert( 'ezkeyword', [ 'id' => $keywordID, 'keyword' => $keyword, 'class_id' => $classID ] );
                $addRelationWordArray[] = [ 'keyword' => $keyword, 'id' => $keywordID ];
            }

            // --- 3. Get current keyword links for this attribute ---
            $currentWordArray = array();
            if ( $attributeID !== null )
            {
                $links = $db->aggregate( 'ezkeyword_attribute_link', [
                    [ '$match'   => [ 'objectattribute_id' => (int)$attributeID ] ],
                    [ '$project' => [ '_id' => 0, 'keyword_id' => 1 ] ],
                ] );
                if ( !empty( $links ) )
                {
                    $kwIDs = array_map( function( $l ) { return (int)$l['keyword_id']; }, $links );
                    $kwRows = $db->aggregate( 'ezkeyword', [
                        [ '$match'   => [ 'id' => [ '$in' => $kwIDs ] ] ],
                        [ '$project' => [ '_id' => 0, 'id' => 1, 'keyword' => 1 ] ],
                    ] );
                    foreach ( $kwRows as $r )
                        $currentWordArray[] = $r;
                }
            }

            // Determine which existing words are new for this attribute
            foreach ( $existingWordArray as $existingWord )
            {
                $alreadyLinked = false;
                foreach ( $currentWordArray as $cw )
                {
                    if ( $existingWord['keyword'] == $cw['keyword'] )
                    {
                        $alreadyLinked = true;
                        break;
                    }
                }
                if ( !$alreadyLinked )
                    $addRelationWordArray[] = $existingWord;
            }

            // --- 4. Remove links for keywords no longer used by this attribute ---
            $removeIDs = array();
            foreach ( $currentWordArray as $cw )
            {
                $stillUsed = false;
                foreach ( $this->KeywordArray as $keyword )
                {
                    if ( $keyword == $cw['keyword'] )
                    {
                        $stillUsed = true;
                        break;
                    }
                }
                if ( !$stillUsed )
                    $removeIDs[] = (int)$cw['id'];
            }
            if ( !empty( $removeIDs ) )
            {
                $db->deleteWhere( 'ezkeyword_attribute_link', [
                    'keyword_id'          => [ '$in' => $removeIDs ],
                    'objectattribute_id'  => (int)$attributeID,
                ] );
            }

            // --- 5. Insert new attribute-keyword links ---
            foreach ( $addRelationWordArray as $kw )
            {
                $db->insert( 'ezkeyword_attribute_link', [
                    'keyword_id'         => (int)$kw['id'],
                    'objectattribute_id' => (int)$attribute->attribute( 'id' ),
                ] );
            }

            // --- 6. Remove orphan keywords (keywords with no links) ---
            $allKwRows = $db->aggregate( 'ezkeyword', [
                [ '$project' => [ '_id' => 0, 'id' => 1 ] ],
            ] );
            foreach ( $allKwRows as $kw )
            {
                $kwID = (int)$kw['id'];
                $linkCount = $db->aggregate( 'ezkeyword_attribute_link', [
                    [ '$match' => [ 'keyword_id' => $kwID ] ],
                    [ '$count' => 'n' ],
                ] );
                if ( empty( $linkCount ) || $linkCount[0]['n'] == 0 )
                    $db->deleteWhere( 'ezkeyword', [ 'id' => $kwID ] );
            }
            return;
        }

        // --- SQL path ---

        // Get already existing keywords
        if ( count( $this->KeywordArray ) > 0 )
        {
            $escapedKeywordArray = array();
            foreach( $this->KeywordArray as $keyword )
            {
                $keyword = $db->escapeString( $keyword );
                $escapedKeywordArray[] = $keyword;
            }
            $wordsString = implode( '\',\'', $escapedKeywordArray );
            $existingWords = $db->arrayQuery( "SELECT * FROM ezkeyword WHERE keyword IN ( '$wordsString' ) AND class_id='$classID' " );
        }
        else
        {
            $existingWords = array();
        }

        $newWordArray = array();
        $existingWordArray = array();
        // Find out which words to store
        foreach ( $this->KeywordArray as $keyword )
        {
            $wordExists = false;
            $wordID = false;
            foreach ( $existingWords as $existingKeyword )
            {
                if ( $keyword == $existingKeyword['keyword'] )
                {
                     $wordExists = true;
                     $wordID = $existingKeyword['id'];
                     break;
                }
            }

            if ( $wordExists == false )
            {
                $newWordArray[] = $keyword;
            }
            else
            {
                $existingWordArray[] = array( 'keyword' => $keyword, 'id' => $wordID );
            }
        }

        // Store every new keyword
        $addRelationWordArray = array();
        foreach ( $newWordArray as $keyword )
        {
            $keyword = trim( $keyword );
            $keyword = $db->escapeString( $keyword );
            $db->query( "INSERT INTO ezkeyword ( keyword, class_id ) VALUES ( '$keyword', '$classID' )" );

            $keywordID = $db->lastSerialID( 'ezkeyword', 'id' );
            $addRelationWordArray[] = array( 'keyword' => $keywordID, 'id' => $keywordID );
        }

        // Find the words which is new for this attribute
        if ( $attributeID !== null )
        {
            $currentWordArray = $db->arrayQuery( "SELECT ezkeyword.id, ezkeyword.keyword FROM ezkeyword, ezkeyword_attribute_link
                                                   WHERE ezkeyword.id=ezkeyword_attribute_link.keyword_id
                                                   AND ezkeyword_attribute_link.objectattribute_id='$attributeID'" );
        }
        else
            $currentWordArray = array();

        foreach ( $existingWordArray as $existingWord )
        {
            $newWord = true;
            foreach ( $currentWordArray as $currentWord )
            {
                if ( $existingWord['keyword']  == $currentWord['keyword'] )
                {
                    $newWord = false;
                }
            }

            if ( $newWord == true )
            {
                $addRelationWordArray[] = $existingWord;
            }
        }

        // Find the current words no longer used
        $removeWordRelationIDArray = array();
        foreach ( $currentWordArray as $currentWord )
        {
            $stillUsed = false;
            foreach ( $this->KeywordArray as $keyword )
            {
                if ( $keyword == $currentWord['keyword'] )
                    $stillUsed = true;
            }
            if ( !$stillUsed )
            {
                $removeWordRelationIDArray[] = $currentWord['id'];
            }
        }

        if ( count( $removeWordRelationIDArray ) > 0 )
        {
            $removeIDString = implode( ', ', $removeWordRelationIDArray );
            $db->query( "DELETE FROM ezkeyword_attribute_link WHERE keyword_id IN ( $removeIDString ) AND  ezkeyword_attribute_link.objectattribute_id='$attributeID'" );
        }

        // Only store relation to new keywords
        // Store relations to keyword for this content object
        foreach ( $addRelationWordArray as $keywordArray )
        {
            $db->query( "INSERT INTO ezkeyword_attribute_link ( keyword_id, objectattribute_id ) VALUES ( '" . $keywordArray['id'] ."', '" . $attribute->attribute( 'id' ) . "' )" );
        }

        /* Clean up no longer used words:
         * 1. Select words having no links.
         * 2. Delete them.
         * We cannot do this in one cross-table DELETE since older MySQL versions do not support this.
         */
        if ( $db->databaseName() == 'oracle' )
        {
            $query =
                'SELECT ezkeyword.id FROM ezkeyword, ezkeyword_attribute_link ' .
                'WHERE ezkeyword.id=ezkeyword_attribute_link.keyword_id(+) AND ' .
                'ezkeyword_attribute_link.keyword_id IS NULL';
        }
        else
        {
            $query =
                'SELECT ezkeyword.id FROM ezkeyword LEFT JOIN ezkeyword_attribute_link ' .
                ' ON ezkeyword.id=ezkeyword_attribute_link.keyword_id' .
                ' WHERE ezkeyword_attribute_link.keyword_id IS NULL';
        }
        $unusedWordsIDs = $db->arrayQuery( $query );
        foreach ( $unusedWordsIDs as $wordID )
            $db->query( 'DELETE FROM ezkeyword WHERE id=' . $wordID['id'] );
    }

    /*!
     Fetches the keywords for the given attribute.
    */
    function fetch( &$attribute )
    {
        if ( $attribute->attribute( 'id' ) === null )
            return;

        $db = eZDB::instance();
        $attrID = (int)$attribute->attribute( 'id' );

        if ( $db->databaseName() === 'mongo' )
        {
            $links = $db->aggregate( 'ezkeyword_attribute_link', [
                [ '$match'   => [ 'objectattribute_id' => $attrID ] ],
                [ '$project' => [ '_id' => 0, 'keyword_id' => 1 ] ],
            ] );
            $this->ObjectAttributeID = $attrID;
            if ( !empty( $links ) )
            {
                $kwIDs = array_map( function( $l ) { return (int)$l['keyword_id']; }, $links );
                $kwRows = $db->aggregate( 'ezkeyword', [
                    [ '$match'   => [ 'id' => [ '$in' => $kwIDs ] ] ],
                    [ '$project' => [ '_id' => 0, 'keyword' => 1 ] ],
                ] );
                foreach ( $kwRows as $r )
                    $this->KeywordArray[] = $r['keyword'];
            }
            $this->KeywordArray = array_unique( $this->KeywordArray );
            return;
        }

        $wordArray = $db->arrayQuery( "SELECT ezkeyword.keyword FROM ezkeyword_attribute_link, ezkeyword
                                    WHERE ezkeyword_attribute_link.keyword_id=ezkeyword.id AND
                                    ezkeyword_attribute_link.objectattribute_id='" . $attribute->attribute( 'id' ) ."' " );

        $this->ObjectAttributeID = $attribute->attribute( 'id' );
        foreach ( array_keys( $wordArray ) as $wordKey )
        {
            $this->KeywordArray[] = $wordArray[$wordKey]['keyword'];
        }
        $this->KeywordArray = array_unique ( $this->KeywordArray );
    }

    /*!
     Sets the keyword index
    */
    function setKeywordArray( $keywords )
    {
        $this->KeywordArray = $keywords;
    }

    /*!
     Returns the keyword index
    */
    function keywordArray( )
    {
        return $this->KeywordArray;
    }

    /*!
     Returns the keywords as a string
    */
    function keywordString()
    {
        return implode( ', ', $this->KeywordArray );
    }

    /*!
     Returns the objects which have at least one keyword in common

     \return an array of eZContentObjectTreeNode instances, or null if the attribute is not stored yet
    */
    function relatedObjects()
    {
        $return = false;
        if ( $this->ObjectAttributeID )
        {
            $return = array();

            $db = eZDB::instance();

            if ( $db->databaseName() === 'mongo' )
            {
                // Get keyword IDs linked to this attribute
                $links = $db->aggregate( 'ezkeyword_attribute_link', [
                    [ '$match'   => [ 'objectattribute_id' => (int)$this->ObjectAttributeID ] ],
                    [ '$project' => [ '_id' => 0, 'keyword_id' => 1 ] ],
                ] );
                $keywordIDArray = array_map( function( $l ) { return (int)$l['keyword_id']; }, $links );

                if ( !empty( $keywordIDArray ) )
                {
                    // Get other attributes sharing any of those keywords
                    $otherLinks = $db->aggregate( 'ezkeyword_attribute_link', [
                        [ '$match'   => [
                            'keyword_id'         => [ '$in' => $keywordIDArray ],
                            'objectattribute_id' => [ '$ne' => (int)$this->ObjectAttributeID ],
                        ] ],
                        [ '$project' => [ '_id' => 0, 'objectattribute_id' => 1 ] ],
                    ] );
                    $otherAttrIDs = array_values( array_unique(
                        array_map( function( $l ) { return (int)$l['objectattribute_id']; }, $otherLinks )
                    ) );

                    if ( !empty( $otherAttrIDs ) )
                    {
                        // Get object IDs for those attributes
                        $objRows = $db->aggregate( 'ezcontentobject_attribute', [
                            [ '$match'   => [ 'id' => [ '$in' => $otherAttrIDs ] ] ],
                            [ '$group'   => [ '_id' => '$contentobject_id' ] ],
                            [ '$project' => [ '_id' => 0, 'contentobject_id' => '$_id' ] ],
                        ] );
                        $objectIDArray = array_map( function( $r ) { return (int)$r['contentobject_id']; }, $objRows );

                        if ( !empty( $objectIDArray ) )
                        {
                            $aNodes = eZContentObjectTreeNode::findMainNodeArray( $objectIDArray );
                            foreach ( $aNodes as $node )
                            {
                                $theObject = $node->object();
                                if ( $theObject->canRead() )
                                    $return[] = $node;
                            }
                        }
                    }
                }
                return $return;
            }

            // --- SQL path ---
            $wordArray = $db->arrayQuery( "SELECT * FROM ezkeyword_attribute_link
                                           WHERE objectattribute_id='" . $this->ObjectAttributeID ."' " );

            $keywordIDArray = array();
            // Fetch the objects which have one of these words
            foreach ( $wordArray as $word )
            {
                $keywordIDArray[] = $word['keyword_id'];
            }

            $keywordCondition = $db->generateSQLINStatement( $keywordIDArray, 'keyword_id' );

            if ( count( $keywordIDArray ) > 0 )
            {
                $objectArray = $db->arrayQuery( "SELECT DISTINCT ezcontentobject_attribute.contentobject_id FROM ezkeyword_attribute_link, ezcontentobject_attribute
                                                  WHERE $keywordCondition AND
                                                        ezcontentobject_attribute.id = ezkeyword_attribute_link.objectattribute_id
                                                        AND  objectattribute_id <> '" . $this->ObjectAttributeID ."' " );

                $objectIDArray = array();
                foreach ( $objectArray as $object )
                {
                    $objectIDArray[] = $object['contentobject_id'];
                }

                if ( count( $objectIDArray ) > 0 )
                {
                    $aNodes = eZContentObjectTreeNode::findMainNodeArray( $objectIDArray );

                    foreach ( $aNodes as $key => $node )
                    {
                        $theObject = $node->object();
                        if ( $theObject->canRead() )
                        {
                            $return[] = $node;
                        }
                    }
                }
            }
        }
        return $return;
    }

    /// Contains the keywords
    public $KeywordArray = array();

    /// Contains the ID attribute if fetched
    public $ObjectAttributeID = false;
}

?>
