<?php
/**
 * File containing the expSpreadsheetWriter class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Writes one sheet of text cells as a minimal valid Office Open XML workbook (.xlsx) or OpenDocument spreadsheet
 * (.ods). The first row can be shown as a header (bold, frozen). Every cell is text (an inline string in .xlsx), so
 * nothing is read as a number, a date or a formula. Needs the PHP extension zip (ZipArchive); no other library.
 */
class expSpreadsheetWriter
{
    /**
     * @var string|null where temporary files go; null is the installation's cache directory
     *                  (var/<site>/cache/spreadsheet, see temporaryDirectory())
     */
    public static $temporaryDirectory = null;

    /**
     * @param string $path
     * @param array $table list of rows, each a list of strings
     * @param array $options "sheet" (the sheet's name, default "Sheet1"), "header" (default true: the first row is
     *                       bold and frozen), "widths" (list of column widths in characters)
     * @throws expSpreadsheetException
     */
    static function writeXlsxFile( $path, array $table, array $options = array() )
    {
        $sheetName = self::sheetName( isset( $options['sheet'] ) ? $options['sheet'] : 'Sheet1' );
        $header = !isset( $options['header'] ) || $options['header'];
        $widths = isset( $options['widths'] ) ? (array)$options['widths'] : array();

        $main = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';
        $rel = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
        $parts = array();
        $parts['[Content_Types].xml'] = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>';
        $parts['_rels/.rels'] = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="' . $rel . '/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
        $parts['xl/workbook.xml'] = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<workbook xmlns="' . $main . '" xmlns:r="' . $rel . '">'
            . '<sheets><sheet name="' . self::xml( $sheetName ) . '" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';
        $parts['xl/_rels/workbook.xml.rels'] = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="' . $rel . '/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="' . $rel . '/styles" Target="styles.xml"/>'
            . '</Relationships>';
        $parts['xl/styles.xml'] = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<styleSheet xmlns="' . $main . '">'
            . '<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><name val="Calibri"/></font></fonts>'
            . '<fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill></fills>'
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="2"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            . '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/></cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '</styleSheet>';

        $sheet = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" . '<worksheet xmlns="' . $main . '">';
        if ( $header && count( $table ) > 1 )
            $sheet .= '<sheetViews><sheetView workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>';
        if ( $widths )
        {
            $sheet .= '<cols>';
            foreach ( array_values( $widths ) as $i => $width )
                $sheet .= '<col min="' . ( $i + 1 ) . '" max="' . ( $i + 1 ) . '" width="' . (float)$width . '" customWidth="1"/>';
            $sheet .= '</cols>';
        }
        $sheet .= '<sheetData>';
        $r = 0;
        foreach ( $table as $cells )
        {
            $r++;
            $sheet .= '<row r="' . $r . '">';
            foreach ( array_values( $cells ) as $c => $value )
            {
                $value = (string)$value;
                if ( $value === '' )
                    continue;
                $style = $header && $r === 1 ? ' s="1"' : '';
                $space = trim( $value ) !== $value || strpos( $value, "\n" ) !== false ? ' xml:space="preserve"' : '';
                $sheet .= '<c r="' . self::columnName( $c ) . $r . '" t="inlineStr"' . $style . '><is><t' . $space . '>'
                        . self::xml( $value ) . '</t></is></c>';
            }
            $sheet .= '</row>';
        }
        $sheet .= '</sheetData></worksheet>';
        $parts['xl/worksheets/sheet1.xml'] = $sheet;

        self::zip( $path, $parts );
    }

    /**
     * @param string $path
     * @param array $table list of rows, each a list of strings
     * @param array $options "sheet" (default "Sheet1"), "header" (default true: the first row is bold)
     * @throws expSpreadsheetException
     */
    static function writeOdsFile( $path, array $table, array $options = array() )
    {
        $sheetName = self::sheetName( isset( $options['sheet'] ) ? $options['sheet'] : 'Sheet1' );
        $header = !isset( $options['header'] ) || $options['header'];
        $columns = 1;
        foreach ( $table as $cells )
            $columns = max( $columns, count( $cells ) );

        $content = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<office:document-content'
            . ' xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0"'
            . ' xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0"'
            . ' xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0"'
            . ' xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0"'
            . ' xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0"'
            . ' office:version="1.2">'
            . '<office:automatic-styles>'
            . '<style:style style:name="ceHeader" style:family="table-cell"><style:text-properties fo:font-weight="bold"/></style:style>'
            . '</office:automatic-styles>'
            . '<office:body><office:spreadsheet>'
            . '<table:table table:name="' . self::xml( $sheetName ) . '">'
            . '<table:table-column table:number-columns-repeated="' . $columns . '"/>';
        $r = 0;
        foreach ( $table as $cells )
        {
            $r++;
            $content .= '<table:table-row>';
            $cells = array_values( $cells );
            for ( $c = 0; $c < $columns; $c++ )
            {
                $value = isset( $cells[$c] ) ? (string)$cells[$c] : '';
                if ( $value === '' )
                {
                    $content .= '<table:table-cell/>';
                    continue;
                }
                $style = $header && $r === 1 ? ' table:style-name="ceHeader"' : '';
                $content .= '<table:table-cell office:value-type="string"' . $style . '>' . self::odfParagraphs( $value ) . '</table:table-cell>';
            }
            $content .= '</table:table-row>';
        }
        $content .= '</table:table></office:spreadsheet></office:body></office:document-content>';

        $manifest = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<manifest:manifest xmlns:manifest="urn:oasis:names:tc:opendocument:xmlns:manifest:1.0" manifest:version="1.2">'
            . '<manifest:file-entry manifest:full-path="/" manifest:version="1.2" manifest:media-type="application/vnd.oasis.opendocument.spreadsheet"/>'
            . '<manifest:file-entry manifest:full-path="content.xml" manifest:media-type="text/xml"/>'
            . '</manifest:manifest>';

        // the mimetype first and stored, as OpenDocument requires
        self::zip( $path, array(
            'mimetype' => 'application/vnd.oasis.opendocument.spreadsheet',
            'content.xml' => $content,
            'META-INF/manifest.xml' => $manifest,
        ), array( 'mimetype' ) );
    }

    /**
     * @param array $table
     * @param array $options
     * @return string the bytes of the .xlsx file
     */
    static function xlsxString( array $table, array $options = array() )
    {
        return self::viaFile( 'writeXlsxFile', $table, $options );
    }

    /**
     * @param array $table
     * @param array $options
     * @return string the bytes of the .ods file
     */
    static function odsString( array $table, array $options = array() )
    {
        return self::viaFile( 'writeOdsFile', $table, $options );
    }

    /**
     * A new empty temporary file.
     *
     * @return string its path
     * @throws expSpreadsheetException
     */
    static function temporaryFile()
    {
        $directory = self::temporaryDirectory();
        $file = @tempnam( $directory, 'expsheet' );
        // tempnam() falls back to the system's directory when it cannot use the one given: not wanted under
        // open_basedir, where only the installation's own folders can be written
        if ( $file === false || realpath( dirname( $file ) ) !== realpath( $directory ) )
        {
            if ( $file !== false )
                @unlink( $file );
            throw new expSpreadsheetException( "No temporary file can be made in $directory" );
        }
        return $file;
    }

    /**
     * Where temporary files go: $temporaryDirectory when it is set; else the cache directory of the installation
     * (var/<site>/cache/spreadsheet, made when missing), which the web server can write also under open_basedir;
     * else var/tmp of the installation; the system's temporary directory only outside an installation.
     *
     * @return string
     */
    static function temporaryDirectory()
    {
        if ( self::$temporaryDirectory !== null )
            return self::$temporaryDirectory;
        $candidates = array();
        if ( class_exists( 'eZSys' ) && class_exists( 'eZINI' ) )
        {
            try
            {
                $candidates[] = eZSys::cacheDirectory() . '/spreadsheet';
            }
            catch ( Throwable $e )
            {
                // no settings: not in an installation
            }
        }
        if ( defined( 'EXP_ROOT_DIR' ) )
            $candidates[] = EXP_ROOT_DIR . '/var/tmp';
        foreach ( $candidates as $directory )
        {
            if ( !is_dir( $directory ) )
                @mkdir( $directory, 0775, true );
            if ( is_dir( $directory ) && is_writable( $directory ) )
                return $directory;
        }
        return sys_get_temp_dir();
    }

    /**
     * Column name of an index from 0: 0 => A, 27 => AB.
     *
     * @param int $index
     * @return string
     */
    static function columnName( $index )
    {
        $name = '';
        for ( $n = (int)$index + 1; $n > 0; $n = intdiv( $n - 1, 26 ) )
            $name = chr( 65 + ( $n - 1 ) % 26 ) . $name;
        return $name;
    }

    /**
     * @param string $method
     * @param array $table
     * @param array $options
     * @return string
     */
    private static function viaFile( $method, array $table, array $options )
    {
        expSpreadsheetReader::requireZip();
        $file = self::temporaryFile();
        try
        {
            self::$method( $file, $table, $options );
            return (string)file_get_contents( $file );
        }
        finally
        {
            @unlink( $file );
        }
    }

    /**
     * @param string $path
     * @param array $parts name => content, in order
     * @param string[] $stored the parts written without compression
     * @throws expSpreadsheetException
     */
    private static function zip( $path, array $parts, array $stored = array() )
    {
        expSpreadsheetReader::requireZip();
        $zip = new ZipArchive();
        if ( $zip->open( $path, ZipArchive::CREATE | ZipArchive::OVERWRITE ) !== true )
            throw new expSpreadsheetException( "The file $path cannot be written" );
        foreach ( $parts as $name => $content )
        {
            $zip->addFromString( $name, $content );
            if ( in_array( $name, $stored, true ) )
                $zip->setCompressionName( $name, ZipArchive::CM_STORE );
        }
        if ( !$zip->close() )
            throw new expSpreadsheetException( "The file $path cannot be written" );
    }

    /**
     * Text for XML: escaped, and the characters XML 1.0 does not allow are left out.
     *
     * @param string $text
     * @return string
     */
    private static function xml( $text )
    {
        $text = preg_replace( '/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', (string)$text );
        return htmlspecialchars( $text, ENT_QUOTES | ENT_XML1 | ENT_SUBSTITUTE, 'UTF-8' );
    }

    /**
     * Cell text as OpenDocument paragraphs: one <text:p> per line, runs of spaces as <text:s/>, tabs as <text:tab/>.
     *
     * @param string $value
     * @return string
     */
    private static function odfParagraphs( $value )
    {
        $out = '';
        foreach ( explode( "\n", str_replace( array( "\r\n", "\r" ), "\n", $value ) ) as $line )
        {
            $escaped = self::xml( $line );
            $escaped = str_replace( "\t", '<text:tab/>', $escaped );
            $escaped = preg_replace_callback( '/(^ +| {2,}| +$)/', function( $m )
            {
                $count = strlen( $m[1] );
                // a single space between words stays a space; leading, trailing and repeated ones are counted
                return $count === 1 ? '<text:s/>' : '<text:s text:c="' . $count . '"/>';
            }, $escaped );
            $out .= '<text:p>' . $escaped . '</text:p>';
        }
        return $out;
    }

    /**
     * A valid sheet name: at most 31 characters, none of []:*?/\.
     *
     * @param string $name
     * @return string
     */
    private static function sheetName( $name )
    {
        $name = trim( str_replace( array( '[', ']', ':', '*', '?', '/', '\\' ), ' ', (string)$name ) );
        if ( $name === '' )
            $name = 'Sheet1';
        return function_exists( 'mb_substr' ) ? mb_substr( $name, 0, 31, 'UTF-8' ) : substr( $name, 0, 31 );
    }
}
