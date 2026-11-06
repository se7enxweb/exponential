<?php
/**
 * File containing the expSpreadsheetReader class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Reads the table of the first sheet of an Office Open XML workbook (.xlsx): the cells' text and numbers, from shared
 * strings, inline strings and values; and of the first table of an OpenDocument spreadsheet (.ods): each cell's text
 * paragraphs (spaces, tabs and line breaks as written), repeated rows and columns. Styles, formulas (their last
 * result is read), merged cells, other sheets and everything else are ignored. Needs the PHP extension zip
 * (ZipArchive); no other library.
 *
 * Limits against hostile files: no DOCTYPE in any part, each part at most MAX_PART_BYTES uncompressed, a repeated
 * row or column with content repeated at most MAX_REPEAT times (an empty one only moves the position on).
 */
class expSpreadsheetReader
{
    /** @var int the largest uncompressed part read */
    const MAX_PART_BYTES = 104857600;

    /** @var int the most times a row or column with content is repeated (.ods table:number-*-repeated) */
    const MAX_REPEAT = 1024;

    const NS_ODF_OFFICE = 'urn:oasis:names:tc:opendocument:xmlns:office:1.0';
    const NS_ODF_TABLE = 'urn:oasis:names:tc:opendocument:xmlns:table:1.0';
    const NS_ODF_TEXT = 'urn:oasis:names:tc:opendocument:xmlns:text:1.0';

    const NS_RELATIONSHIPS = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
    const NS_PACKAGE_RELATIONSHIPS = 'http://schemas.openxmlformats.org/package/2006/relationships';

    /**
     * @return bool whether ZipArchive is there
     */
    static function isAvailable()
    {
        return class_exists( 'ZipArchive' );
    }

    /**
     * @throws expSpreadsheetException when ZipArchive is missing
     */
    static function requireZip()
    {
        if ( !self::isAvailable() )
            throw new expSpreadsheetException( 'Reading and writing .xlsx and .ods files needs the PHP extension zip (ZipArchive), which this PHP does not have. Install it (e.g. the package php-zip), or use CSV or JSON.' );
    }

    /**
     * @param string $path an .xlsx file
     * @return array spreadsheet row number (from 1) => array of column index (from 0) => string; empty rows are
     *               left out, cells without a value too
     * @throws expSpreadsheetException
     */
    static function readXlsxFile( $path )
    {
        self::requireZip();
        $zip = new ZipArchive();
        if ( !is_file( $path ) || $zip->open( $path ) !== true )
            throw new expSpreadsheetException( 'The file is not an .xlsx workbook (not a ZIP archive)' );
        try
        {
            $sheetPath = self::firstSheetPath( $zip );
            $shared = self::sharedStrings( $zip );
            $sheet = self::part( $zip, $sheetPath );
            if ( $sheet === null )
                throw new expSpreadsheetException( 'The workbook has no worksheet ' . $sheetPath );
            return self::sheetTable( $sheet, $shared );
        }
        finally
        {
            $zip->close();
        }
    }

    /**
     * @param string $data the bytes of an .xlsx file
     * @return array as readXlsxFile()
     * @throws expSpreadsheetException
     */
    static function readXlsxString( $data )
    {
        self::requireZip();
        $file = expSpreadsheetWriter::temporaryFile();
        try
        {
            file_put_contents( $file, $data );
            return self::readXlsxFile( $file );
        }
        finally
        {
            @unlink( $file );
        }
    }

    /**
     * @param string $path an .ods file
     * @return array as readXlsxFile(): spreadsheet row number (from 1) => column index (from 0) => string
     * @throws expSpreadsheetException
     */
    static function readOdsFile( $path )
    {
        self::requireZip();
        $zip = new ZipArchive();
        if ( !is_file( $path ) || $zip->open( $path ) !== true )
            throw new expSpreadsheetException( 'The file is not an .ods spreadsheet (not a ZIP archive)' );
        try
        {
            $mimetype = $zip->getFromName( 'mimetype' );
            if ( $mimetype !== false && trim( $mimetype ) !== 'application/vnd.oasis.opendocument.spreadsheet' )
                throw new expSpreadsheetException( 'The file is an OpenDocument file but not a spreadsheet (' . trim( $mimetype ) . ')' );
            $stat = $zip->statName( 'content.xml' );
            if ( $stat === false )
                throw new expSpreadsheetException( 'The file is not an .ods spreadsheet (no content.xml)' );
            if ( $stat['size'] > self::MAX_PART_BYTES )
                throw new expSpreadsheetException( 'The part content.xml is larger than ' . self::MAX_PART_BYTES . ' bytes' );
            $xml = $zip->getFromName( 'content.xml' );
            if ( $xml === false )
                throw new expSpreadsheetException( 'The part content.xml cannot be read' );
        }
        finally
        {
            $zip->close();
        }
        return self::odsTable( $xml );
    }

    /**
     * @param string $data the bytes of an .ods file
     * @return array as readOdsFile()
     * @throws expSpreadsheetException
     */
    static function readOdsString( $data )
    {
        self::requireZip();
        $file = expSpreadsheetWriter::temporaryFile();
        try
        {
            file_put_contents( $file, $data );
            return self::readOdsFile( $file );
        }
        finally
        {
            @unlink( $file );
        }
    }

    /**
     * The table of the first sheet of an OpenDocument content.xml. Pure.
     *
     * @param string $xml
     * @return array row number (from 1) => column index (from 0) => string
     * @throws expSpreadsheetException
     */
    static function odsTable( $xml )
    {
        if ( stripos( $xml, '<!DOCTYPE' ) !== false )
            throw new expSpreadsheetException( 'The part content.xml has a DOCTYPE, which a spreadsheet never needs' );
        $document = new DOMDocument();
        $previous = libxml_use_internal_errors( true );
        $loaded = $document->loadXML( $xml, LIBXML_NONET | LIBXML_COMPACT | ( defined( 'LIBXML_PARSEHUGE' ) ? LIBXML_PARSEHUGE : 0 ) );
        libxml_clear_errors();
        libxml_use_internal_errors( $previous );
        if ( !$loaded )
            throw new expSpreadsheetException( 'The part content.xml is not well-formed XML' );
        $tables = $document->getElementsByTagNameNS( self::NS_ODF_TABLE, 'table' );
        if ( $tables->length === 0 )
            return array();
        $table = array();
        $rowNumber = 0;
        foreach ( self::odsRows( $tables->item( 0 ) ) as $row )
        {
            $repeat = max( 1, (int)$row->getAttributeNS( self::NS_ODF_TABLE, 'number-rows-repeated' ) );
            $cells = self::odsCells( $row );
            if ( !$cells )
            {
                $rowNumber += $repeat;
                continue;
            }
            for ( $i = 0; $i < min( $repeat, self::MAX_REPEAT ); $i++ )
                $table[++$rowNumber] = $cells;
            if ( $repeat > self::MAX_REPEAT )
                $rowNumber += $repeat - self::MAX_REPEAT;
        }
        return $table;
    }

    /**
     * The rows of a table, also those inside table:table-header-rows and table:table-rows groups.
     *
     * @param DOMElement $table
     * @return DOMElement[]
     */
    private static function odsRows( DOMElement $table )
    {
        $rows = array();
        foreach ( $table->childNodes as $child )
        {
            if ( !$child instanceof DOMElement || $child->namespaceURI !== self::NS_ODF_TABLE )
                continue;
            if ( $child->localName === 'table-row' )
                $rows[] = $child;
            elseif ( in_array( $child->localName, array( 'table-header-rows', 'table-rows', 'table-row-group' ), true ) )
                $rows = array_merge( $rows, self::odsRows( $child ) );
        }
        return $rows;
    }

    /**
     * The cells of a row with text: column index => text.
     *
     * @param DOMElement $row
     * @return string[]
     */
    private static function odsCells( DOMElement $row )
    {
        $cells = array();
        $column = 0;
        foreach ( $row->childNodes as $cell )
        {
            if ( !$cell instanceof DOMElement || $cell->namespaceURI !== self::NS_ODF_TABLE
                 || ( $cell->localName !== 'table-cell' && $cell->localName !== 'covered-table-cell' ) )
                continue;
            $repeat = max( 1, (int)$cell->getAttributeNS( self::NS_ODF_TABLE, 'number-columns-repeated' ) );
            $text = $cell->localName === 'table-cell' ? self::odsCellText( $cell ) : '';
            if ( $text !== '' )
            {
                for ( $i = 0; $i < min( $repeat, self::MAX_REPEAT ); $i++ )
                    $cells[$column + $i] = $text;
            }
            $column += $repeat;
        }
        return $cells;
    }

    /**
     * The text of a cell: its paragraphs joined by line ends; a cell without paragraphs gives its value.
     *
     * @param DOMElement $cell
     * @return string
     */
    private static function odsCellText( DOMElement $cell )
    {
        $paragraphs = array();
        foreach ( $cell->childNodes as $child )
        {
            if ( $child instanceof DOMElement && $child->namespaceURI === self::NS_ODF_TEXT && ( $child->localName === 'p' || $child->localName === 'h' ) )
                $paragraphs[] = self::odfText( $child );
        }
        if ( $paragraphs )
            return implode( "\n", $paragraphs );
        foreach ( array( 'value', 'string-value', 'date-value', 'time-value', 'boolean-value' ) as $attribute )
        {
            if ( $cell->hasAttributeNS( self::NS_ODF_OFFICE, $attribute ) )
                return self::number( $cell->getAttributeNS( self::NS_ODF_OFFICE, $attribute ) );
        }
        return '';
    }

    /**
     * The text of an OpenDocument paragraph: text:s as its count of spaces, text:tab as a tab, text:line-break as a
     * line end, spans and links by their text; notes and annotations are left out.
     *
     * @param DOMNode $node
     * @return string
     */
    private static function odfText( DOMNode $node )
    {
        $text = '';
        foreach ( $node->childNodes as $child )
        {
            if ( $child instanceof DOMText )
            {
                $text .= $child->data;
                continue;
            }
            if ( !$child instanceof DOMElement )
                continue;
            if ( $child->namespaceURI === self::NS_ODF_TEXT )
            {
                switch ( $child->localName )
                {
                    case 's':
                        $text .= str_repeat( ' ', max( 1, min( 1000, (int)$child->getAttributeNS( self::NS_ODF_TEXT, 'c' ) ) ) );
                        continue 2;
                    case 'tab':
                        $text .= "\t";
                        continue 2;
                    case 'line-break':
                        $text .= "\n";
                        continue 2;
                    case 'note':
                    case 'bookmark':
                    case 'bookmark-start':
                    case 'bookmark-end':
                        continue 2;
                }
            }
            if ( $child->namespaceURI === self::NS_ODF_OFFICE && $child->localName === 'annotation' )
                continue;
            $text .= self::odfText( $child );
        }
        return $text;
    }

    /**
     * Column index (from 0) of a cell reference: "A1" => 0, "AB12" => 27.
     *
     * @param string $reference
     * @return int
     */
    static function columnIndex( $reference )
    {
        $index = 0;
        $letters = preg_replace( '/[^A-Z]/', '', strtoupper( (string)$reference ) );
        for ( $i = 0, $n = strlen( $letters ); $i < $n; $i++ )
            $index = $index * 26 + ( ord( $letters[$i] ) - 64 );
        return $index - 1;
    }

    /**
     * @param ZipArchive $zip
     * @param string $name
     * @return SimpleXMLElement|null
     * @throws expSpreadsheetException
     */
    private static function part( ZipArchive $zip, $name )
    {
        $stat = $zip->statName( $name );
        if ( $stat === false )
            return null;
        if ( $stat['size'] > self::MAX_PART_BYTES )
            throw new expSpreadsheetException( "The part $name is larger than " . self::MAX_PART_BYTES . ' bytes' );
        $xml = $zip->getFromName( $name );
        if ( $xml === false )
            throw new expSpreadsheetException( "The part $name cannot be read" );
        if ( stripos( $xml, '<!DOCTYPE' ) !== false )
            throw new expSpreadsheetException( "The part $name has a DOCTYPE, which a workbook never needs" );
        $previous = libxml_use_internal_errors( true );
        $element = simplexml_load_string( $xml, 'SimpleXMLElement', LIBXML_NONET | LIBXML_COMPACT );
        libxml_clear_errors();
        libxml_use_internal_errors( $previous );
        if ( $element === false )
            throw new expSpreadsheetException( "The part $name is not well-formed XML" );
        return $element;
    }

    /**
     * The path of the first sheet, from xl/workbook.xml and its relationships; xl/worksheets/sheet1.xml when they
     * do not say.
     *
     * @param ZipArchive $zip
     * @return string
     */
    private static function firstSheetPath( ZipArchive $zip )
    {
        $workbookPath = 'xl/workbook.xml';
        $rels = self::part( $zip, '_rels/.rels' );
        if ( $rels !== null )
        {
            foreach ( $rels->children( self::NS_PACKAGE_RELATIONSHIPS )->Relationship as $relationship )
            {
                if ( substr( (string)self::attribute( $relationship, 'Type' ), -15 ) === '/officeDocument' )
                    $workbookPath = ltrim( (string)self::attribute( $relationship, 'Target' ), '/' );
            }
        }
        $workbook = self::part( $zip, $workbookPath );
        if ( $workbook === null )
            throw new expSpreadsheetException( 'The file is not an .xlsx workbook (no ' . $workbookPath . ')' );
        $ns = self::rootNamespace( $workbook );
        $id = null;
        $sheets = $workbook->children( $ns )->sheets;
        if ( $sheets && $sheets->children( $ns )->sheet )
        {
            $sheet = $sheets->children( $ns )->sheet[0];
            foreach ( $sheet->attributes( self::NS_RELATIONSHIPS ) as $name => $value )
            {
                if ( $name === 'id' )
                    $id = (string)$value;
            }
            if ( $id === null )
            {
                // Strict OOXML uses another relationships namespace
                foreach ( $sheet->attributes( 'http://purl.oclc.org/ooxml/officeDocument/relationships' ) as $name => $value )
                {
                    if ( $name === 'id' )
                        $id = (string)$value;
                }
            }
        }
        $directory = dirname( $workbookPath );
        $relsPath = ( $directory === '.' ? '' : $directory . '/' ) . '_rels/' . basename( $workbookPath ) . '.rels';
        $workbookRels = self::part( $zip, $relsPath );
        if ( $id !== null && $workbookRels !== null )
        {
            foreach ( $workbookRels->children( self::NS_PACKAGE_RELATIONSHIPS )->Relationship as $relationship )
            {
                if ( (string)self::attribute( $relationship, 'Id' ) === $id )
                    return self::resolve( $directory, (string)self::attribute( $relationship, 'Target' ) );
            }
        }
        return 'xl/worksheets/sheet1.xml';
    }

    /**
     * @param string $directory
     * @param string $target
     * @return string
     */
    private static function resolve( $directory, $target )
    {
        if ( $target !== '' && $target[0] === '/' )
            return ltrim( $target, '/' );
        $parts = array();
        foreach ( explode( '/', ( $directory === '.' ? '' : $directory . '/' ) . $target ) as $segment )
        {
            if ( $segment === '' || $segment === '.' )
                continue;
            if ( $segment === '..' )
                array_pop( $parts );
            else
                $parts[] = $segment;
        }
        return implode( '/', $parts );
    }

    /**
     * An attribute without a namespace (after children( $ns ), $element['name'] looks in that namespace instead).
     *
     * @param SimpleXMLElement $element
     * @param string $name
     * @return string|null
     */
    private static function attribute( SimpleXMLElement $element, $name )
    {
        $attributes = $element->attributes();
        return isset( $attributes[$name] ) ? (string)$attributes[$name] : null;
    }

    /**
     * @param SimpleXMLElement $element
     * @return string
     */
    private static function rootNamespace( SimpleXMLElement $element )
    {
        $node = dom_import_simplexml( $element );
        return (string)$node->namespaceURI;
    }

    /**
     * @param ZipArchive $zip
     * @return string[]
     */
    private static function sharedStrings( ZipArchive $zip )
    {
        $part = self::part( $zip, 'xl/sharedStrings.xml' );
        if ( $part === null )
            return array();
        $ns = self::rootNamespace( $part );
        $strings = array();
        foreach ( $part->children( $ns )->si as $item )
            $strings[] = self::text( $item, $ns );
        return $strings;
    }

    /**
     * The text of a string item (<si> or <is>): its <t>, or the <t> of each run <r>; phonetic runs are left out.
     *
     * @param SimpleXMLElement $item
     * @param string $ns
     * @return string
     */
    private static function text( SimpleXMLElement $item, $ns )
    {
        $children = $item->children( $ns );
        if ( isset( $children->t ) )
            return (string)$children->t;
        $text = '';
        foreach ( $children->r as $run )
            $text .= (string)$run->children( $ns )->t;
        return $text;
    }

    /**
     * @param SimpleXMLElement $sheet
     * @param string[] $shared
     * @return array
     */
    private static function sheetTable( SimpleXMLElement $sheet, array $shared )
    {
        $ns = self::rootNamespace( $sheet );
        $table = array();
        $data = $sheet->children( $ns )->sheetData;
        if ( !$data )
            return $table;
        $rowNumber = 0;
        foreach ( $data->children( $ns )->row as $row )
        {
            $r = self::attribute( $row, 'r' );
            $rowNumber = $r !== null ? (int)$r : $rowNumber + 1;
            $cells = array();
            $column = -1;
            foreach ( $row->children( $ns )->c as $cell )
            {
                $r = self::attribute( $cell, 'r' );
                $column = $r !== null ? self::columnIndex( $r ) : $column + 1;
                $type = self::attribute( $cell, 't' );
                $type = $type !== null ? $type : 'n';
                $children = $cell->children( $ns );
                $value = isset( $children->v ) ? (string)$children->v : null;
                switch ( $type )
                {
                    case 's':
                        $text = $value !== null && isset( $shared[(int)$value] ) ? $shared[(int)$value] : '';
                        break;
                    case 'inlineStr':
                        $text = isset( $children->is ) ? self::text( $children->is, $ns ) : '';
                        break;
                    case 'b':
                        $text = $value === '1' ? 'TRUE' : ( $value === null ? '' : 'FALSE' );
                        break;
                    case 'e':
                        $text = '';
                        break;
                    case 'n':
                        $text = $value === null ? '' : self::number( $value );
                        break;
                    default:
                        // str (a formula's text), d (an ISO date)
                        $text = $value === null ? '' : $value;
                }
                if ( $text !== '' )
                    $cells[$column] = $text;
            }
            if ( $cells )
                $table[$rowNumber] = $cells;
        }
        return $table;
    }

    /**
     * A number as it is shown: whole numbers without ".0" or an exponent.
     *
     * @param string $value
     * @return string
     */
    private static function number( $value )
    {
        if ( is_numeric( $value ) && preg_match( '/^-?\d+(\.0+)?$|[eE]/', $value ) )
        {
            $float = (float)$value;
            if ( floor( $float ) === $float && abs( $float ) < 1e15 )
                return sprintf( '%.0f', $float );
        }
        return $value;
    }
}
