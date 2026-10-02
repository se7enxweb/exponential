<?php
/**
 * File containing the eZMatrixDefinition class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZMatrixDefinition ezmatrixdefinition.php
  \ingroup eZDatatype
  \brief The class eZMatrixDefinition does

*/

class eZMatrixDefinition
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ColumnNames = array();
    }


    function decodeClassAttribute( $xmlString )
    {
        $dom = new DOMDocument( '1.0', 'utf-8' );
        if ( strlen ( (string)$xmlString ) != 0 )
        {
            // Broken XML leaves the class with no columns (and a debug error)
            // instead of a PHP warning on every page that loads the class.
            $columnList = array();
            $previous = libxml_use_internal_errors( true );
            $success = $dom->loadXML( $xmlString );
            libxml_clear_errors();
            libxml_use_internal_errors( $previous );
            if ( $success )
            {
                foreach ( $dom->getElementsByTagName( "column-name" ) as $columnElement )
                {
                    $columnList[] = array( 'name' => $columnElement->textContent,
                                           'identifier' => $columnElement->getAttribute( 'id' ),
                                           'index' => (int)$columnElement->getAttribute( 'idx' ) );
                }
            }
            else
            {
                eZDebug::writeError( 'The matrix column definition is not valid XML', __METHOD__ );
            }
            $this->ColumnNames = $columnList;
        }
        else
        {
            $this->addColumn( );
            $this->addColumn( );
        }

    }

    function attributes()
    {
        return array( 'columns' );
    }

    function hasAttribute( $attr )
    {
        return in_array( $attr, $this->attributes() );
    }

    function attribute( $attr )
    {
        if ( $attr == 'columns' )
        {
            return $this->ColumnNames;
        }

        eZDebug::writeError( "Attribute '$attr' does not exist", __METHOD__ );
        return null;
    }

    function xmlString( )
    {
        $doc = new DOMDocument( '1.0', 'utf-8' );
        $root = $doc->createElement( "ezmatrix" );
        $doc->appendChild( $root );

        foreach ( $this->ColumnNames as $columnName )
        {
            $columnNameNode = $doc->createElement( 'column-name' );
            $columnNameNode->appendChild( $doc->createTextNode( $columnName['name'] ) );
            $columnNameNode->setAttribute( 'id', $columnName['identifier'] );
            $columnNameNode->setAttribute( 'idx', $columnName['index'] );
            $root->appendChild( $columnNameNode );
            unset( $columnNameNode );
            unset( $textNode );
        }

        $xml = $doc->saveXML();

        return $xml;
    }

    function addColumn( $name = false , $id = false )
    {
        // Only a missing name gets the default: "0" == false, so a column
        // called 0 was renamed Col_n
        if ( $name === false || $name === null || $name === '' )
        {
            $name = 'Col_' . ( count( $this->ColumnNames ) );
        }

        if ( $id === false || $id === null || $id === '' )
        {
            // Initialize transformation system
            $trans = eZCharTransform::instance();
            $id = $trans->transformByGroup( $name, 'identifier' );

            // A generated identifier is made unique; one given (from a package
            // or stored class) is kept, as stored objects match cells to it
            $taken = array();
            foreach ( $this->ColumnNames as $column )
                $taken[$column['identifier']] = true;
            $id = eZMatrixType::uniqueColumnIdentifier( (string)$id, $taken );
        }

        $this->ColumnNames[] = array( 'name' => (string)$name,
                                      'identifier' => (string)$id,
                                      'index' => count( $this->ColumnNames ) );
    }

    /**
     * Removes the column at position $index and renumbers the ones after it,
     * so positions and 'index' stay 0..n-1. unset() alone left a gap that the
     * edit form and the stored XML carried until the next full save. Removing
     * several columns must go from the highest position down.
     */
    function removeColumn( $index )
    {
        $index = (int)$index;
        if ( !isset( $this->ColumnNames[$index] ) )
            return false;

        unset( $this->ColumnNames[$index] );
        $this->ColumnNames = array_values( $this->ColumnNames );
        foreach ( $this->ColumnNames as $i => $column )
            $this->ColumnNames[$i]['index'] = $i;
        return true;
    }

    public $ColumnNames;

}

?>
