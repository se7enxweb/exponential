<?php
/**
 * File containing the eZMatrixType class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZMatrixType ezmatrixtype.php
  \ingroup eZDatatype
  \brief The class eZMatrixType does

*/

class eZMatrixType extends eZDataType
{
    const DEFAULT_NAME_VARIABLE = '_ezmatrix_default_name_';

    const NUM_COLUMNS_VARIABLE = '_ezmatrix_default_num_columns_';
    const NUM_ROWS_VARIABLE = '_ezmatrix_default_num_rows_';
    const CELL_VARIABLE = '_ezmatrix_cell_';
    const DATA_TYPE_STRING = 'ezmatrix';
    /// The most rows a new matrix starts with; the class form accepted any number
    const MAX_DEFAULT_ROWS = 1000;

    public function __construct()
    {
        parent::__construct( self::DATA_TYPE_STRING, ezpI18n::tr( 'kernel/classes/datatypes', 'Matrix', 'Datatype name' ),
                           array( 'serialize_supported' => true ) );
    }

    /*!
     Validates the input and returns true if the input was
     valid for this datatype.
    */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $data = false;
        if ( $http->hasPostVariable( $base . self::CELL_VARIABLE . $contentObjectAttribute->attribute( 'id' ) ) )
            $data = $http->postVariable( $base . self::CELL_VARIABLE . $contentObjectAttribute->attribute( 'id' ) );

        // One filled cell is enough. Cells are strings; anything else posted
        // (a nested array) counts as empty rather than reaching trim().
        $count = 0;
        if ( is_array( $data ) )
        {
            foreach ( $data as $cell )
            {
                if ( is_scalar( $cell ) && trim( (string)$cell ) !== '' )
                {
                    ++$count;
                    break;
                }
            }
        }
        if ( $contentObjectAttribute->validateIsRequired() and ( $count == 0 or $data === false ) )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                 'Missing matrix input.' ) );
            return eZInputValidator::STATE_INVALID;
        }
        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     Store content
    */
    function storeObjectAttribute( $contentObjectAttribute )
    {
        $matrix = $contentObjectAttribute->content();
        $contentObjectAttribute->setAttribute( 'data_text', $matrix->xmlString() );
        $matrix->decodeXML( $contentObjectAttribute->attribute( 'data_text' ) );
        $contentObjectAttribute->setContent( $matrix );
    }

    function storeClassAttribute( $contentClassAttribute, $version )
    {
        $matrixDefinition = $contentClassAttribute->content();
        $contentClassAttribute->setAttribute( 'data_text5', $matrixDefinition->xmlString() );
        $matrixDefinition->decodeClassAttribute( $contentClassAttribute->attribute( 'data_text5' ) );
        $contentClassAttribute->setContent(  $matrixDefinition );
    }

    /*!
     Returns the content.
    */
    function objectAttributeContent( $contentObjectAttribute )
    {
        $matrix = new eZMatrix( '' );

        $matrix->decodeXML( $contentObjectAttribute->attribute( 'data_text' ) );

        return $matrix;
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        $matrix = $contentObjectAttribute->content();
        $columnsArray = $matrix->attribute( 'columns' );
        $count = 0;
        foreach ( isset( $columnsArray['sequential'] ) ? $columnsArray['sequential'] : array() as $column )
        {
            if ( isset( $column['rows'] ) && is_array( $column['rows'] ) )
                $count += count( $column['rows'] );
        }
        return $count > 0;
    }

    /*!
     Returns the meta data used for storing search indeces.
    */
    function metaData( $contentObjectAttribute )
    {
        $matrix = $contentObjectAttribute->content();
        $columnsArray = $matrix->attribute( 'columns' );
        $metaDataArray = array();
        foreach ( isset( $columnsArray['sequential'] ) ? $columnsArray['sequential'] : array() as $column )
        {
            $rows = isset( $column['rows'] ) && is_array( $column['rows'] ) ? $column['rows'] : array();
            foreach ( $rows as $row )
            {
                $metaDataArray[] = array( 'id' => $column['identifier'],
                                          'text' => $row );
            }
        }
        return $metaDataArray;
    }

    /*!
     Fetches the http post var matrix cells input and stores it in the data instance.
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $cellsVarName = $base . self::CELL_VARIABLE . $contentObjectAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $cellsVarName ) && is_array( $http->postVariable( $cellsVarName ) ) )
        {
            // Every cell is stored as text: a nested array posted in place of a
            // cell becomes an empty cell instead of the word "Array".
            $cells = array();
            foreach ( $http->postVariable( $cellsVarName ) as $cell )
            {
                $cells[] = is_scalar( $cell ) ? (string)$cell : '';
            }
            $matrix = $contentObjectAttribute->attribute( 'content' );
            $matrix->Cells = $cells;

            $contentObjectAttribute->setAttribute( 'data_text', $matrix->xmlString() );
            $matrix->decodeXML( $contentObjectAttribute->attribute( 'data_text' ) );
            $contentObjectAttribute->setContent( $matrix );
        }
        return true;
    }

    function customObjectAttributeHTTPAction( $http, $action, $contentObjectAttribute, $parameters )
    {
        switch ( $action )
        {
            case 'new_row' :
            {
                $matrix = $contentObjectAttribute->content( );

                $postvarname = 'ContentObjectAttribute' . '_data_matrix_remove_' . $contentObjectAttribute->attribute( 'id' );
                $addCountName = 'ContentObjectAttribute' . '_data_matrix_add_count_' . $contentObjectAttribute->attribute( 'id' );

                $addCount = 1;
                if ( $http->hasPostVariable( $addCountName ) )
                {
                    $addCount = $http->postVariable( $addCountName );
                }

                $selected = $http->hasPostVariable( $postvarname ) ? $http->postVariable( $postvarname ) : false;
                if ( is_array( $selected ) && isset( $selected[0] ) && is_numeric( $selected[0] ) )
                {
                    $matrix->addRow( (int)$selected[0], $addCount );
                }
                else
                {
                    $matrix->addRow( false, $addCount );
                }

                $contentObjectAttribute->setAttribute( 'data_text', $matrix->xmlString() );
                $matrix->decodeXML( $contentObjectAttribute->attribute( 'data_text' ) );
                $contentObjectAttribute->setContent( $matrix );
                $contentObjectAttribute->store();
            }break;
            case 'remove_selected' :
            {
                $matrix = $contentObjectAttribute->content( );
                $postvarname = 'ContentObjectAttribute' . '_data_matrix_remove_' . $contentObjectAttribute->attribute( 'id' );
                // Nothing ticked posts nothing: rsort( null ) was a TypeError.
                // Highest row first, each once, so removing one row does not
                // move the next one to remove.
                $arrayRemove = $http->hasPostVariable( $postvarname ) ? $http->postVariable( $postvarname ) : array();
                $arrayRemove = array_unique( array_map( 'intval', array_filter( (array)$arrayRemove, 'is_numeric' ) ) );
                rsort( $arrayRemove, SORT_NUMERIC );
                foreach ( $arrayRemove as $rowNum )
                {
                    $matrix->removeRow( $rowNum );
                }

                $contentObjectAttribute->setAttribute( 'data_text', $matrix->xmlString() );
                $matrix->decodeXML( $contentObjectAttribute->attribute( 'data_text' ) );
                $contentObjectAttribute->setContent( $matrix );
                $contentObjectAttribute->store();
            }break;
            default :
            {
                eZDebug::writeError( 'Unknown custom HTTP action: ' . $action, 'eZMatrixType' );
            }break;
        }
    }

    /*!
     Returns the integer value.
    */
    function title( $contentObjectAttribute, $name = 'name' )
    {
        $matrix = $contentObjectAttribute->content( );

        $value = $matrix->attribute( $name );

        return $value;
    }

    /*!
     Sets the default value.
    */
    function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {

        if ( $currentVersion != false )
        {
            $matrix = $originalContentObjectAttribute->content();
            $contentClassAttribute = $contentObjectAttribute->contentClassAttribute();
            // make sure that $matrix contains right columns
            $matrix->adjustColumnsToDefinition( $contentClassAttribute->attribute( 'content' ) );

            $contentObjectAttribute->setAttribute( 'data_text', $matrix->xmlString() );
            $contentObjectAttribute->setContent( $matrix );
        }
        else
        {
            $contentClassAttribute = $contentObjectAttribute->contentClassAttribute();
            $numRows = self::defaultRowCount( $contentClassAttribute->attribute( 'data_int1' ) );
            $matrix = new eZMatrix( '', $numRows, $contentClassAttribute->attribute( 'content' ) );
            // 'default name' is never used => just a stub
            // $matrix->setName( $contentClassAttribute->attribute( 'data_text1' ) );
            $contentObjectAttribute->setAttribute( 'data_text', $matrix->xmlString() );
            $contentObjectAttribute->setContent( $matrix );
        }

    }

    /**
     * The number of rows a new matrix starts with, from the class attribute's
     * data_int1: a whole number from 0 to MAX_DEFAULT_ROWS. A negative or huge
     * value would otherwise build that many rows for every new object.
     *
     * @param mixed $value
     * @return int
     */
    static function defaultRowCount( $value )
    {
        return max( 0, min( (int)$value, self::MAX_DEFAULT_ROWS ) );
    }

    /**
     * $identifier, or $identifier with _2, _3 ... appended when an earlier
     * column already has it. Cells are matched to columns by identifier, so two
     * columns sharing one lose one column's data when the class changes.
     *
     * @param string $identifier
     * @param array $taken identifiers used so far, as keys
     * @return string
     */
    static function uniqueColumnIdentifier( $identifier, array $taken )
    {
        if ( $identifier === '' || !isset( $taken[$identifier] ) )
            return $identifier;
        for ( $n = 2; isset( $taken[$identifier . '_' . $n] ); ++$n );
        return $identifier . '_' . $n;
    }

    function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        // 'default name' is never used => just a stub
        // $defaultValueName = $base . self::DEFAULT_NAME_VARIABLE . $classAttribute->attribute( 'id' );
        $defaultValueName = '';
        $defaultNumColumnsName = $base . self::NUM_COLUMNS_VARIABLE . $classAttribute->attribute( 'id' );
        $defaultNumRowsName = $base . self::NUM_ROWS_VARIABLE . $classAttribute->attribute( 'id' );
        $dataFetched = false;
        // 'default name' is never used => just a stub
        /*
        if ( $http->hasPostVariable( $defaultValueName ) )
        {
            $defaultValueValue = $http->postVariable( $defaultValueName );

            if ( $defaultValueValue == '' )
            {
                $defaultValueValue = '';
            }
            $classAttribute->setAttribute( 'data_text1', $defaultValueValue );
            $dataFetched = true;
        }
        */

        if ( $http->hasPostVariable( $defaultNumRowsName ) )
        {
            $defaultNumRowsValue = $http->postVariable( $defaultNumRowsName );

            if ( !is_scalar( $defaultNumRowsValue ) || trim( (string)$defaultNumRowsValue ) === '' )
            {
                $defaultNumRowsValue = 1;
            }
            $classAttribute->setAttribute( 'data_int1', self::defaultRowCount( $defaultNumRowsValue ) );
            $dataFetched = true;
        }

        $columnNameVariable = $base . '_data_ezmatrix_column_name_' . $classAttribute->attribute( 'id' );
        $columnIDVariable = $base . '_data_ezmatrix_column_id_' . $classAttribute->attribute( 'id' );


        if ( $http->hasPostVariable( $columnNameVariable ) && $http->hasPostVariable( $columnIDVariable ) &&
             is_array( $http->postVariable( $columnNameVariable ) ) && is_array( $http->postVariable( $columnIDVariable ) ) )
        {
            $columns = array();
            $taken = array();
            $i = 0;
            $columnNameList = $http->postVariable( $columnNameVariable );
            $columnIDList = $http->postVariable( $columnIDVariable );

            $matrixDefinition = $classAttribute->attribute( 'content' );
            $columnNames = $matrixDefinition->attribute( 'columns' );
            foreach ( $columnNames as $columnName )
            {
                $columnID = '';
                $name = '';
                $index = $columnName['index'];

                // after adding a new column $columnIDList and $columnNameList doesn't contain values for new column.
                // if so just add column with empty 'name' and 'columnID'.
                if ( isset( $columnIDList[$index] ) && isset( $columnNameList[$index] ) &&
                     is_scalar( $columnIDList[$index] ) && is_scalar( $columnNameList[$index] ) )
                {
                    $columnID = trim( (string)$columnIDList[$index] );
                    $name = (string)$columnNameList[$index];
                    if ( strlen( $columnID ) == 0 )
                    {
                        $columnID = $name;
                        // Initialize transformation system
                        $trans = eZCharTransform::instance();
                        $columnID = $trans->transformByGroup( $columnID, 'identifier' );
                        // Only an identifier made here from the name is made
                        // unique. One the class already has is kept as it is:
                        // stored objects match their cells to it.
                        $columnID = self::uniqueColumnIdentifier( $columnID, $taken );
                    }
                }

                $taken[$columnID] = true;

                $columns[] = array( 'name' => $name,
                                    'identifier' => $columnID,
                                    'index' => $i );

                $i++;
            }

            $matrixDefinition->ColumnNames = $columns;
            $classAttribute->setContent( $matrixDefinition );
            $classAttribute->setAttribute( 'data_text5', $matrixDefinition->xmlString() );

            $dataFetched = true;
        }
        if ( $dataFetched )
        {
            return true;
        }
        return false;

    }

    function preStoreClassAttribute( $classAttribute, $version )
    {
        $matrixDefinition = $classAttribute->attribute( 'content' );
        $classAttribute->setAttribute( 'data_text5', $matrixDefinition->xmlString() );
    }

    /*!
     Returns the content.
    */
    function classAttributeContent( $contentClassAttribute )
    {
        $matrixDefinition = new eZMatrixDefinition();
        $matrixDefinition->decodeClassAttribute( $contentClassAttribute->attribute( 'data_text5' ) );
        return $matrixDefinition;
    }

    function customClassAttributeHTTPAction( $http, $action, $contentClassAttribute )
    {
        $id = $contentClassAttribute->attribute( 'id' );
        switch ( $action )
        {
            case 'new_ezmatrix_column' :
            {
                $matrixDefinition = $contentClassAttribute->content( );
                $matrixDefinition->addColumn( '' );
                $contentClassAttribute->setContent( $matrixDefinition );
                $contentClassAttribute->store();
            }break;
            case 'remove_selected' :
            {
                $matrixDefinition = $contentClassAttribute->content( );

                $postvarname = 'ContentClass' . '_data_ezmatrix_column_remove_' . $contentClassAttribute->attribute( 'id' );
                // Nothing ticked posts nothing (foreach over null). Highest
                // index first, as removeColumn() renumbers the ones after it.
                $array_remove = $http->hasPostVariable( $postvarname ) ? $http->postVariable( $postvarname ) : array();
                $array_remove = array_unique( array_map( 'intval', array_filter( (array)$array_remove, 'is_numeric' ) ) );
                rsort( $array_remove, SORT_NUMERIC );
                foreach( $array_remove as $columnIndex )
                {
                    $matrixDefinition->removeColumn( $columnIndex );
                }
                $contentClassAttribute->setContent( $matrixDefinition );
            }break;
            default :
            {
                eZDebug::writeError( 'Unknown custom HTTP action: ' . $action, 'eZMatrixType' );
            }break;
        }
    }

    function isIndexable()
    {
        return true;
    }

    /*!
     \return string representation of an contentobjectattribute data for simplified export

    */
    function toString( $contentObjectAttribute )
    {
        $matrix = $contentObjectAttribute->attribute( 'content' );
        $matrixArray = array();
        $rows = $matrix->attribute( 'rows' );

        foreach( $rows['sequential'] as $row )
        {
            $matrixArray[] = eZStringUtils::implodeStr( $row['columns'], '|' );
        }

        return eZStringUtils::implodeStr( $matrixArray, '&' );

    }

    function fromString( $contentObjectAttribute, $string )
    {
        $matrix = $contentObjectAttribute->attribute( 'content' );
        $matrix->Cells = array();
        $matrix->Matrix['rows']['sequential'] = array();
        $matrix->NumRows = 0;

        // Cells are stored row after row, one per column: a row with more or
        // fewer cells than the matrix has columns shifted every later cell
        // into the wrong column. Each row is cut or padded to the column count.
        $numColumns = $matrix->attribute( 'columnCount' );

        if ( $string != '' )
        {
            $matrixRowsList = eZStringUtils::explodeStr( $string, "&" );

            foreach( $matrixRowsList as $key => $value )
            {
                $newCells = eZStringUtils::explodeStr( $value, '|' );
                if ( $numColumns > 0 )
                    $newCells = array_pad( array_slice( $newCells, 0, $numColumns ), $numColumns, '' );
                $matrix->Cells = array_merge( $matrix->Cells, $newCells );
                $newRow = array();

                $newRow['columns'] = $newCells;
                $newRow['identifier'] =  'row_' . ( $matrix->NumRows + 1 );
                $newRow['name'] = 'Row_' . ( $matrix->NumRows + 1 );
                $matrix->NumRows++;

                $matrix->Matrix['rows']['sequential'][] = $newRow;
            }
        }

        return true;
    }

    function serializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $content = $classAttribute->content();
        if ( $content )
        {
            $defaultName = $classAttribute->attribute( 'data_text1' );
            $defaultRowCount = $classAttribute->attribute( 'data_int1' );
            $columns = $content->attribute( 'columns' );

            $dom = $attributeParametersNode->ownerDocument;
            $defaultNameNode = $dom->createElement( 'default-name' );
            $defaultNameNode->appendChild( $dom->createTextNode( $defaultName ) );
            $attributeParametersNode->appendChild( $defaultNameNode );
            $defaultRowCountNode = $dom->createElement( 'default-row-count' );
            $defaultRowCountNode->appendChild( $dom->createTextNode( $defaultRowCount ) );
            $attributeParametersNode->appendChild( $defaultRowCountNode );
            $columnsNode = $dom->createElement( 'columns' );
            $attributeParametersNode->appendChild( $columnsNode );
            foreach ( $columns as $column )
            {
                unset( $columnNode );
                $columnNode = $dom->createElement( 'column' );
                $columnNode->setAttribute( 'name', $column['name'] );
                $columnNode->setAttribute( 'identifier', $column['identifier'] );
                $columnNode->setAttribute( 'index', $column['index'] );
                $columnsNode->appendChild( $columnNode );
            }
        }
    }

    function unserializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        // A package may leave out any of these elements; reading a property of
        // the missing node stopped the class import.
        $defaultNameNode = $attributeParametersNode->getElementsByTagName( 'default-name' )->item( 0 );
        $defaultRowCountNode = $attributeParametersNode->getElementsByTagName( 'default-row-count' )->item( 0 );
        $classAttribute->setAttribute( 'data_text1', $defaultNameNode ? $defaultNameNode->textContent : '' );
        $classAttribute->setAttribute( 'data_int1', self::defaultRowCount( $defaultRowCountNode ? $defaultRowCountNode->textContent : 1 ) );

        $matrixDefinition = new eZMatrixDefinition();
        $columnsNode = $attributeParametersNode->getElementsByTagName( 'columns' )->item( 0 );
        $columnsList = $columnsNode ? $columnsNode->getElementsByTagName( 'column' ) : array();
        foreach ( $columnsList  as $columnNode )
        {
            $columnName = $columnNode->getAttribute( 'name' );
            $columnIdentifier = $columnNode->getAttribute( 'identifier' );
            $matrixDefinition->addColumn( $columnName, $columnIdentifier );
        }
        $classAttribute->setContent( $matrixDefinition );
    }

    function serializeContentObjectAttribute( $package, $objectAttribute )
    {
        $node = $this->createContentObjectAttributeDOMNode( $objectAttribute );

        // An attribute never stored has no XML: loadXML( '' ) warned and
        // importNode( null ) was a TypeError that stopped the package export.
        // Such an attribute is exported as an empty matrix.
        $dom = new DOMDocument( '1.0', 'utf-8' );
        $xmlString = (string)$objectAttribute->attribute( 'data_text' );
        $previous = libxml_use_internal_errors( true );
        $loaded = trim( $xmlString ) !== '' && $dom->loadXML( $xmlString );
        libxml_clear_errors();
        libxml_use_internal_errors( $previous );
        if ( !$loaded || !$dom->documentElement )
        {
            $empty = new eZMatrix( '' );
            $dom->loadXML( $empty->xmlString() );
        }

        $importedRoot = $node->ownerDocument->importNode( $dom->documentElement, true );
        $node->appendChild( $importedRoot );

        return $node;
    }

    function unserializeContentObjectAttribute( $package, $objectAttribute, $attributeNode )
    {
        $rootNode = $attributeNode->getElementsByTagName( 'ezmatrix' )->item( 0 );
        $xmlString = $rootNode ? $rootNode->ownerDocument->saveXML( $rootNode ) : '';
        $objectAttribute->setAttribute( 'data_text', $xmlString );
    }

    function supportsBatchInitializeObjectAttribute()
    {
        return true;
    }

    function batchInitializeObjectAttributeData( $classAttribute )
    {
        $numRows = self::defaultRowCount( $classAttribute->attribute( 'data_int1' ) );
        $matrix = new eZMatrix( '', $numRows, $classAttribute->attribute( 'content' ) );
        $db = eZDB::instance();
        return array( 'data_text' => "'" . $db->escapeString( $matrix->xmlString() ) . "'" );
    }
}

eZDataType::register( eZMatrixType::DATA_TYPE_STRING, 'ezmatrixtype' );

?>
