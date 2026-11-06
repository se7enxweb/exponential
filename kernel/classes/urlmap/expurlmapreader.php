<?php
/**
 * File containing the expUrlMapReader class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Reads a URL map file into rows (expUrlMapRow). Every row of the file gives a row, the ones that cannot be imported
 * too (with their problems and line numbers); only a file that cannot be read at all throws
 * expUrlMapFormatException.
 */
abstract class expUrlMapReader
{
    /** @var array */
    protected $options;

    /**
     * @param array $options format options (see each reader)
     */
    function __construct( array $options = array() )
    {
        $this->options = $options;
    }

    /**
     * @param string $data the file's content
     * @return expUrlMapRow[]
     * @throws expUrlMapFormatException
     */
    abstract function readString( $data );

    /**
     * @param string $path
     * @return expUrlMapRow[]
     * @throws expUrlMapFormatException
     */
    function readFile( $path )
    {
        if ( !is_file( $path ) || !is_readable( $path ) )
            throw new expUrlMapFormatException( "The file $path cannot be read" );
        return $this->readString( (string)file_get_contents( $path ) );
    }

    /**
     * Text input as UTF-8 with "\n" line ends: a UTF-8 byte order mark is dropped, UTF-16 (with its mark) is
     * converted, and text that is not UTF-8 is read as Windows-1252.
     *
     * @param string $data
     * @return string
     */
    static function utf8Text( $data )
    {
        $data = (string)$data;
        if ( strncmp( $data, "\xEF\xBB\xBF", 3 ) === 0 )
            $data = substr( $data, 3 );
        elseif ( strncmp( $data, "\xFF\xFE", 2 ) === 0 || strncmp( $data, "\xFE\xFF", 2 ) === 0 )
        {
            $encoding = $data[0] === "\xFF" ? 'UTF-16LE' : 'UTF-16BE';
            if ( !function_exists( 'mb_convert_encoding' ) )
                throw new expUrlMapFormatException( 'The file is UTF-16; reading it needs the PHP extension mbstring. Save it as UTF-8.' );
            $data = mb_convert_encoding( substr( $data, 2 ), 'UTF-8', $encoding );
        }
        elseif ( !preg_match( '//u', $data ) )
        {
            $data = function_exists( 'mb_convert_encoding' ) ? mb_convert_encoding( $data, 'UTF-8', 'Windows-1252' ) : (string)iconv( 'Windows-1252', 'UTF-8//IGNORE', $data );
        }
        return str_replace( array( "\r\n", "\r" ), "\n", $data );
    }
}
