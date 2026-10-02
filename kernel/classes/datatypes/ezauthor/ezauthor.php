<?php
/**
 * File containing the eZAuthor class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZAuthor ezauthor.php
  \ingroup eZDatatype
  \brief eZAuthor handles author lists

  \code

  $author = new eZAuthor( "Colour" );
  $author->addValue( "Red" );
  $author->addValue( "Green" );

  // Serialize the class to an XML document
  $xmlString = $author->xmlString();

  \endcode
*/

class eZAuthor
{
    public function __construct( )
    {
        $this->Authors = array();
        $this->AuthorCount = 0;
    }

    /*!
     Sets the name of the author set.
    */
    function setName( $name )
    {
        $this->Name = $name;
    }

    /*!
     Returns the name of the author set.
    */
    function name()
    {
        return $this->Name;
    }

    /**
     * Add an author
     *
     * @param int $id
     * @param string $name
     * @param string $email
     */
    function addAuthor( $id, $name, $email )
    {
        // Form input and imported strings can be anything: an author is plain
        // text, and a value the XML could not hold would lose the whole list
        $name = self::cleanText( $name );
        $email = self::cleanText( $email );
        if ( !is_scalar( $id ) )
            $id = -1;
        if ( $id == -1 )
        {
            if ( isset( $this->Authors[$this->AuthorCount - 1] ) )
                $id = $this->Authors[$this->AuthorCount - 1]['id'] + 1;
            else
                $id = 1;
        }

        $this->Authors[] = array( "id" => $id,
                                  "name" => $name,
                                  "email" => $email,
                             "is_default" => false );

        $this->AuthorCount ++;
    }

    /**
     * Remove authors
     *
     * @param array $removeList List of id's of authors to remove
     */
    function removeAuthors( $removeList )
    {
        if ( !is_array( $removeList ) )
            $removeList = is_scalar( $removeList ) ? array( $removeList ) : array();
        $removeIDs = array();
        foreach ( $removeList as $id )
        {
            if ( is_scalar( $id ) )
                $removeIDs[] = (string)$id;
        }
        if ( count( $removeIDs ) == 0 )
            return;

        // Filtered in one pass: splicing inside a foreach over the same array
        // shifted the keys under the loop, so the author after a removed one
        // could be removed instead of (or as well as) the selected one
        $authors = array();
        foreach ( $this->Authors as $author )
        {
            if ( !in_array( (string)$author['id'], $removeIDs, true ) )
                $authors[] = $author;
        }
        $this->Authors = $authors;
        $this->AuthorCount = count( $authors );
    }

    /**
     * An author name or email as a string the XML storage can hold: arrays and
     * objects become '', characters XML 1.0 does not allow are dropped (they were
     * written escaped, and the stored list could then not be read back).
     *
     * @param mixed $value
     * @return string
     */
    static function cleanText( $value )
    {
        if ( !is_scalar( $value ) )
            return '';
        return preg_replace( '/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', (string)$value );
    }

    function attributes()
    {
        static $def = array( 'author_list',
                             'name',
                             'is_empty' );
        return $def;
    }

    function hasAttribute( $name )
    {
        return in_array( $name, $this->attributes() );
    }

    function attribute( $name )
    {
        switch ( $name )
        {
            case "name" :
            {
                return $this->Name;
            }break;
            case "is_empty" :
            {
                return $this->AuthorCount === 0;
            }break;
            case "author_list" :
            {
                return $this->Authors;
            }break;
            default:
            {
                eZDebug::writeError( "Attribute '$name' does not exist", __METHOD__ );
                return null;
            }
            break;
        }
    }

    /*!
     \return a string which contains all the interesting meta data.

     The result of this method can passed to the search engine or other
     parts which work on meta data.

     The string will contain all the authors with their name and email.

     Example:
     \code
     'John Doe john@doe.com'
     \endcode
    */
    function metaData()
    {
        $data = '';
        foreach ( $this->Authors as $author )
        {
            $data .= $author['name'] . ' ' . $author['email'] . "\n";
        }
        return $data;
    }

    /*!
     Will decode an xml string and initialize the eZ author object
    */
    function decodeXML( $xmlString )
    {
        if ( !is_string( $xmlString ) || trim( $xmlString ) === '' )
            return;
        // Broken stored XML gives an empty list, not warnings
        $dom = new DOMDocument( '1.0', 'utf-8' );
        $useErrors = libxml_use_internal_errors( true );
        $success = $dom->loadXML( $xmlString );
        libxml_clear_errors();
        libxml_use_internal_errors( $useErrors );

        if ( $success )
        {
            $authors = $dom->getElementsByTagName( 'author' );
            foreach ( $authors as $author )
            {
                $this->addAuthor( $author->getAttribute( "id" ), $author->getAttribute( "name" ), $author->getAttribute( "email" ) );
            }
        }
    }

    /*!
     Will return the XML string for this author set.
    */
    function xmlString( )
    {
        $doc = new DOMDocument( '1.0', 'utf-8' );

        $root = $doc->createElement( "ezauthor" );
        $doc->appendChild( $root );

        $authors = $doc->createElement( "authors" );
        $root->appendChild( $authors );

        $id = 0;
        foreach ( $this->Authors as $author )
        {
            unset( $authorNode );
            $authorNode = $doc->createElement( "author" );
            $authorNode->setAttribute( "id", $id++ );
            $authorNode->setAttribute( "name", $author["name"] );
            $authorNode->setAttribute( "email", $author["email"] );

            $authors->appendChild( $authorNode );
        }

        $xml = $doc->saveXML();

        return $xml;
    }

    /// Contains the Authors.
    protected $Authors;

    /// Contains the author counter value.
    protected $AuthorCount;

    // Contains the name of the author set.
    protected $Name;
}

?>
