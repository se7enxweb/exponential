<?php
/**
 * File containing the eZOption class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZOption ezoption.php
  \ingroup eZDatatype
  \brief eZOption handles option set datatypes

  \code

  $option = new eZOption( "Colour" );
  $option->addValue( "Red" );
  $option->addValue( "Green" );

  // Serialize the class to an XML document
  $xmlString = $option->xmlString();

  \endcode
*/

class eZOption
{
    /**
     * Constructor
     * 
     * @param string $name
     */
    public function __construct( $name )
    {
        $this->Name = $name;
        $this->Options = array();
        $this->OptionCount = 0;
    }

    /*!
     Sets the name of the option
    */
    function setName( $name )
    {
        $this->Name = $name;
    }


    /*!
     Returns the name of the option set.
    */
    function name()
    {
        return $this->Name;
    }

    /*!
     Adds an option
    */
    function addOption( $valueArray )
    {
        $value = isset( $valueArray['value'] ) ? $valueArray['value'] : '';
        $additional_price = isset( $valueArray['additional_price'] ) ? $valueArray['additional_price'] : '';
        $this->Options[] = array( "id" => $this->OptionCount,
                                  "value" => $value,
                                  'additional_price' => $additional_price,
                                  "is_default" => false );

        $this->OptionCount += 1;
    }

    function insertOption( $valueArray, $beforeID )
    {
        // The position comes from the form: a value that is not a number would
        // be a TypeError in array_splice(), one past the end appends
        $beforeID = is_numeric( $beforeID ) ? max( 0, min( (int)$beforeID, count( $this->Options ) ) ) : count( $this->Options );
        array_splice( $this->Options, $beforeID, 0 ,  array( array( "id" => $this->OptionCount,
                                                                    "value" => isset( $valueArray['value'] ) ? $valueArray['value'] : '',
                                                                    'additional_price' => isset( $valueArray['additional_price'] ) ? $valueArray['additional_price'] : '',
                                                                    "is_default" => false ) ) );
        $this->OptionCount += 1;
    }

    function removeOptions( $array_remove )
    {
        // The ids are positions posted by the edit form. Only the ones that
        // name an option are removed, each once and from the highest down so
        // that removing one does not move the next; the old loop took them in
        // posted order, removed the wrong rows for an unsorted list and threw
        // a TypeError for a value that is not a number
        if ( !is_array( $array_remove ) )
            $array_remove = array( $array_remove );
        $positions = array();
        foreach ( $array_remove as $id )
        {
            if ( is_scalar( $id ) && is_numeric( $id ) && (int)$id == $id &&
                 $id >= 0 && $id < count( $this->Options ) )
                $positions[(int)$id] = (int)$id;
        }
        krsort( $positions );
        foreach ( $positions as $position )
        {
            array_splice( $this->Options, $position, 1 );
        }
        $this->OptionCount -= count( $positions );
    }

    function attributes()
    {
        return array( 'name',
                      'option_list' );
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
            case "option_list" :
            {
                return $this->Options;
            }break;
            default:
            {
                eZDebug::writeError( "Attribute '$name' does not exist", __METHOD__ );
                return null;
            }break;
        }
    }

    /*!
     Will decode an xml string and initialize the eZ option object
    */
    function decodeXML( $xmlString )
    {
        if ( $xmlString != "" )
        {
            // Broken stored XML reads as a set without name and options: the
            // parser's complaints are collected, not raised as warnings, and a
            // missing <name> no longer is a fatal error on null
            $dom = new DOMDocument( '1.0', 'utf-8' );
            $previous = libxml_use_internal_errors( true );
            $success = is_string( $xmlString ) ? $dom->loadXML( $xmlString ) : false;
            libxml_clear_errors();
            libxml_use_internal_errors( $previous );
            if ( !$success )
            {
                $this->Name = '';
                $this->Options = array();
                $this->OptionCount = 0;
                return;
            }

            // set the name of the node
            $nameNode = $dom->getElementsByTagName( "name" )->item( 0 );
            $this->setName( $nameNode ? $nameNode->textContent : '' );

            $optionNodes = $dom->getElementsByTagName( "option" );
            $this->OptionCount = 0;

            foreach ( $optionNodes as $optionNode )
            {
                $this->addOption( array( 'value' => $optionNode->textContent,
                                         'additional_price' => $optionNode->getAttribute( 'additional_price' ) ) );
            }
        }
        else
        {
            $this->addOption( "" );
            $this->addOption( "" );
        }
    }

    /*!
     Will return the XML string for this option set.
    */
    function xmlString( )
    {
        $doc = new DOMDocument( '1.0', 'utf-8' );

        $root = $doc->createElement( "ezoption" );
        $doc->appendChild( $root );

        $name = $doc->createElement( "name" );
        $name->appendChild( $doc->createCDATASection( self::scalarString( $this->Name ) ) );
        $root->appendChild( $name );

        $options = $doc->createElement( "options" );
        $root->appendChild( $options );

        foreach ( $this->Options as $option )
        {
            $optionNode = $doc->createElement( "option" );
            $optionNode->appendChild( $doc->createCDATASection( self::scalarString( isset( $option["value"] ) ? $option["value"] : '' ) ) );
            $optionNode->setAttribute( "id", self::scalarString( isset( $option['id'] ) ? $option['id'] : '' ) );
            $optionNode->setAttribute( 'additional_price', self::scalarString( isset( $option['additional_price'] ) ? $option['additional_price'] : '' ) );
            $options->appendChild( $optionNode );
        }

        $xml = $doc->saveXML();

        return $xml;
    }

    /*!
     \static
     \return \a $value as a string for the DOM: '' for null (a form field that
     was not posted) and for an array (a field posted as name[]).
    */
    static function scalarString( $value )
    {
        return is_scalar( $value ) ? (string)$value : '';
    }

    /// Contains the Option name
    public $Name;

    /// Contains the Options
    public $Options;

    /// Contains the option counter value
    public $OptionCount;
}

?>
