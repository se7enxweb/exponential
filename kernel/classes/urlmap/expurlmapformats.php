<?php
/**
 * File containing the expUrlMapFormats class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The URL map file formats: which reader and writer each has, and which format a file is (by an explicit format
 * name, its extension, or a look at its content).
 *
 *   csv     read, write   .csv .tsv .txt (a .tsv is written with tabs)
 *   json    read, write   .json
 *   xlsx    read, write   .xlsx
 *   ods     write         .ods
 *   apache  read, write   .htaccess .apache .apacheconf (and .conf with Apache directives)
 *   nginx   read, write   .nginx (and .conf with nginx directives)
 */
class expUrlMapFormats
{
    const CSV = 'csv';
    const JSON = 'json';
    const XLSX = 'xlsx';
    const ODS = 'ods';
    const APACHE = 'apache';
    const NGINX = 'nginx';

    /**
     * @return array name => array( label, extensions, reader class or null, writer class or null, MIME type,
     *               writer options )
     */
    static function definitions()
    {
        return array(
            self::CSV => array( 'label' => 'CSV', 'extensions' => array( 'csv', 'tsv', 'txt' ), 'reader' => 'expUrlMapCsvReader',
                                'writer' => 'expUrlMapCsvWriter', 'mime' => 'text/csv', 'options' => array() ),
            self::JSON => array( 'label' => 'JSON', 'extensions' => array( 'json' ), 'reader' => 'expUrlMapJsonReader',
                                 'writer' => 'expUrlMapJsonWriter', 'mime' => 'application/json', 'options' => array() ),
            self::XLSX => array( 'label' => 'Excel (.xlsx)', 'extensions' => array( 'xlsx' ), 'reader' => 'expUrlMapXlsxReader',
                                 'writer' => 'expUrlMapSpreadsheetWriter',
                                 'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'options' => array( 'type' => 'xlsx' ) ),
            self::ODS => array( 'label' => 'OpenDocument (.ods)', 'extensions' => array( 'ods' ), 'reader' => null,
                                'writer' => 'expUrlMapSpreadsheetWriter', 'mime' => 'application/vnd.oasis.opendocument.spreadsheet',
                                'options' => array( 'type' => 'ods' ) ),
            self::APACHE => array( 'label' => 'Apache rewrite rules', 'extensions' => array( 'htaccess', 'apache', 'apacheconf' ),
                                   'reader' => 'expUrlMapApacheReader', 'writer' => 'expUrlMapApacheWriter', 'mime' => 'text/plain', 'options' => array() ),
            self::NGINX => array( 'label' => 'nginx rewrite rules', 'extensions' => array( 'nginx' ), 'reader' => 'expUrlMapNginxReader',
                                  'writer' => 'expUrlMapNginxWriter', 'mime' => 'text/plain', 'options' => array() ),
        );
    }

    /** @return string[] */
    static function names()
    {
        return array_keys( self::definitions() );
    }

    /** @return string[] the formats that can be read */
    static function readable()
    {
        return array_keys( array_filter( self::definitions(), function( $d ) { return $d['reader'] !== null; } ) );
    }

    /** @return string[] the formats that can be written */
    static function writable()
    {
        return array_keys( array_filter( self::definitions(), function( $d ) { return $d['writer'] !== null; } ) );
    }

    /**
     * The format of a file: by its extension (the name ".htaccess" is Apache); ".conf" and unknown extensions by its
     * content when it is given; null when that does not say.
     *
     * @param string $fileName
     * @param string|null $content
     * @return string|null
     */
    static function detect( $fileName, $content = null )
    {
        $base = strtolower( basename( (string)$fileName ) );
        if ( $base === '.htaccess' || $base === 'htaccess' )
            return self::APACHE;
        $extension = strpos( $base, '.' ) !== false ? substr( $base, strrpos( $base, '.' ) + 1 ) : '';
        foreach ( self::definitions() as $name => $definition )
        {
            if ( in_array( $extension, $definition['extensions'], true ) )
                return $name;
        }
        return $content === null ? null : self::sniff( $content );
    }

    /**
     * The format by content: a ZIP archive is .xlsx, "[" or "{" first is JSON, Apache or nginx directives, else CSV.
     *
     * @param string $content
     * @return string
     */
    static function sniff( $content )
    {
        $content = (string)$content;
        if ( strncmp( $content, "PK\x03\x04", 4 ) === 0 )
            return self::XLSX;
        $text = ltrim( preg_replace( '/^\xEF\xBB\xBF/', '', $content ) );
        if ( $text !== '' && ( $text[0] === '[' || $text[0] === '{' ) )
            return self::JSON;
        if ( preg_match( '/^\s*(RewriteRule|RewriteCond|RewriteEngine|Redirect|RedirectMatch|RedirectPermanent|RedirectTemp)\s/mi', $text ) )
            return self::APACHE;
        if ( preg_match( '/^\s*(rewrite\s+\S+\s+\S+|location\s+[=~^\/]|return\s+30\d|map\s+\$|server\s*\{)/m', $text ) )
            return self::NGINX;
        return self::CSV;
    }

    /**
     * @param string $format
     * @param array $options passed to the reader
     * @return expUrlMapReader
     * @throws expUrlMapFormatException
     */
    static function reader( $format, array $options = array() )
    {
        $definition = self::definition( $format );
        if ( $definition['reader'] === null )
            throw new expUrlMapFormatException( 'The format ' . $format . ' can only be written; read one of: ' . implode( ', ', self::readable() ) );
        $class = $definition['reader'];
        return new $class( $options );
    }

    /**
     * @param string $format
     * @param array $options passed to the writer (on top of the format's own)
     * @return expUrlMapWriter
     * @throws expUrlMapFormatException
     */
    static function writer( $format, array $options = array() )
    {
        $definition = self::definition( $format );
        $class = $definition['writer'];
        return new $class( $options + $definition['options'] );
    }

    /**
     * Reads a file in the given format, or in the one detect() finds.
     *
     * @param string $path
     * @param string|null $format
     * @return expUrlMapRow[]
     * @throws expUrlMapFormatException
     */
    static function readFile( $path, $format = null )
    {
        if ( $format === null || $format === '' )
        {
            $format = self::detect( $path );
            if ( $format === null )
            {
                if ( !is_readable( $path ) )
                    throw new expUrlMapFormatException( "The file $path cannot be read" );
                $format = self::sniff( (string)file_get_contents( $path, false, null, 0, 65536 ) );
            }
        }
        return self::reader( $format )->readFile( $path );
    }

    /**
     * Writes a file in the given format, or in the one of its extension; a .tsv file with tabs.
     *
     * @param string $path
     * @param expUrlMapRow[] $rows
     * @param string|null $format
     * @throws expUrlMapFormatException
     */
    static function writeFile( $path, array $rows, $format = null )
    {
        if ( $format === null || $format === '' )
        {
            $format = self::detect( $path );
            if ( $format === null )
                throw new expUrlMapFormatException( "The format of $path is not known from its name; give it explicitly (" . implode( ', ', self::writable() ) . ')' );
        }
        $options = array();
        if ( $format === self::CSV && strtolower( substr( (string)$path, -4 ) ) === '.tsv' )
            $options['delimiter'] = "\t";
        self::writer( $format, $options )->writeFile( $path, $rows );
    }

    /**
     * @param string $format
     * @return array
     * @throws expUrlMapFormatException
     */
    private static function definition( $format )
    {
        $definitions = self::definitions();
        $format = strtolower( trim( (string)$format ) );
        if ( !isset( $definitions[$format] ) )
            throw new expUrlMapFormatException( 'Unknown URL map format "' . $format . '"; use one of: ' . implode( ', ', array_keys( $definitions ) ) );
        return $definitions[$format];
    }
}
