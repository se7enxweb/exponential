<?php
/**
 * File containing the expAccessReader class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Reads the tables of a Microsoft Access file (.accdb, .mdb) for expAccessImport: on Linux with MDBTools
 * (expAccessMdbToolsReader, read only, no Access needed), on Windows through Microsoft's Access ODBC driver
 * (expAccessODBCReader). Both describe a table the same way:
 *
 *   fields   name => array( type, length, scale, not_null, auto )
 *            type: counter, byte, smallint, integer, bigint, boolean, single, double, currency, decimal, datetime,
 *                  text, memo, binary, guid
 *   primary  the columns of the primary key
 *   indexes  name => array( unique, fields )
 *
 * and give rows as column => value: null, text, a number as text or number, a date as 'Y-m-d H:i:s', binary data as
 * expAccessBinary.
 *
 * @since 6.0.15
 * @package kernel
 */
abstract class expAccessReader
{
    /** @var string the file */
    protected $file = '';

    /**
     * @param string $file
     * @throws RuntimeException when the file cannot be read
     */
    public function __construct( $file )
    {
        $this->file = (string)$file;
        if ( !is_file( $this->file ) || !is_readable( $this->file ) )
            throw new RuntimeException( "The Access file {$this->file} cannot be read" );
    }

    /**
     * The reader this system can use: odbc (Windows, ACE) or mdbtools (any system with the mdbtools programs).
     *
     * @param string $file
     * @param string $wanted auto, mdbtools or odbc
     * @param array $settings the reader's settings (mdbtools: Bin; odbc: Driver, Layer, Password)
     * @return expAccessReader
     * @throws RuntimeException when none can
     */
    public static function create( $file, $wanted = 'auto', array $settings = array() )
    {
        if ( !is_file( (string)$file ) || !is_readable( (string)$file ) )
            throw new RuntimeException( "The Access file $file cannot be read" );
        $wanted = strtolower( trim( (string)$wanted ) );
        if ( $wanted === 'mdbtools' )
            return new expAccessMdbToolsReader( $file, $settings );
        if ( $wanted === 'odbc' )
            return new expAccessODBCReader( $file, $settings );
        $why = array();
        if ( DIRECTORY_SEPARATOR === '\\' )
        {
            try
            {
                return new expAccessODBCReader( $file, $settings );
            }
            catch ( Throwable $e )
            {
                $why[] = 'odbc: ' . $e->getMessage();
            }
        }
        if ( expAccessMdbToolsReader::available( isset( $settings['Bin'] ) ? $settings['Bin'] : '' ) )
            return new expAccessMdbToolsReader( $file, $settings );
        $why[] = 'mdbtools: the programs mdb-tables, mdb-schema and mdb-json were not found';
        throw new RuntimeException( 'No reader for Access files on this system (' . implode( '; ', $why ) . ')' );
    }

    /** @return string the file */
    public function file()
    {
        return $this->file;
    }

    /** @return string mdbtools or odbc */
    abstract public function name();

    /** @return string the file's format (JET3, JET4, ACE12 ...) when the reader can tell */
    public function format()
    {
        return '';
    }

    /**
     * The user tables (no MSys* system tables).
     *
     * @return string[]
     */
    abstract public function tables();

    /**
     * A table's description (see the class).
     *
     * @param string $table
     * @return array fields, primary, indexes
     */
    abstract public function table( $table );

    /**
     * Calls $each( array $row ) for every row of a table.
     *
     * @param string $table
     * @param callable $each
     * @return int the rows read
     */
    abstract public function rows( $table, callable $each );

    /**
     * A date as 'Y-m-d H:i:s', from the reader's text ('Y-m-d H:i:s', 'Y-m-d', or a date strtotime() reads); null
     * for an empty value.
     *
     * @param mixed $value
     * @return string|null
     */
    public static function date( $value )
    {
        if ( $value === null || $value === '' )
            return null;
        $value = trim( (string)$value );
        if ( preg_match( '/^(\d{4})-(\d{2})-(\d{2})(?:[ T](\d{2}):(\d{2})(?::(\d{2}))?)?/', $value, $m ) )
            return sprintf( '%s-%s-%s %s:%s:%s', $m[1], $m[2], $m[3], isset( $m[4] ) ? $m[4] : '00', isset( $m[5] ) ? $m[5] : '00', isset( $m[6] ) && $m[6] !== '' ? $m[6] : '00' );
        $time = strtotime( $value );
        return $time === false ? $value : date( 'Y-m-d H:i:s', $time );
    }
}

/**
 * Binary data of an Access column (OLE object, attachment, binary), kept apart from text.
 *
 * @since 6.0.15
 * @package kernel
 */
class expAccessBinary
{
    /** @var string */
    public $bytes;

    public function __construct( $bytes )
    {
        $this->bytes = (string)$bytes;
    }

    public function __toString()
    {
        return base64_encode( $this->bytes );
    }
}
?>
