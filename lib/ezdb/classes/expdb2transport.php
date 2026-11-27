<?php
/**
 * File containing the expDb2Transport class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The PHP layer under expDb2DB and expInformixDB. The drivers speak only to this class, so a test can put a
 * recording transport in its place (tests/tests/lib/ezdb/db2). The layer of PHP is pdo_ibm, or PDO_ODBC with an ODBC
 * driver of 64-bit lengths (expDb2PDOTransport).
 *
 * run() returns array( 'rows' => array( row, ... ), 'affected' => int ), each row a lower-case column => value
 * array with every value a string or null (as MySQL gives them), or false on an error (then error() tells which).
 */
abstract class expDb2Transport
{
    /** @var array|null the last error: sqlcode, state, message */
    protected $error = null;

    /**
     * The layer's name (pdo_ibm, pdo_odbc).
     *
     * @return string
     */
    abstract public function name();

    /**
     * Opens the connection.
     *
     * @param array $config layer, host, port, database, user, password, driver, security, extra, persistent
     * @return bool
     */
    abstract public function open( array $config );

    /**
     * Runs one statement, with "?" parameters bound as text when $params is not empty.
     *
     * @param string $sql
     * @param string[] $params
     * @return array|false
     */
    abstract public function run( $sql, array $params = array() );

    /** @return bool */
    abstract public function begin();

    /** @return bool */
    abstract public function commit();

    /** @return bool */
    abstract public function rollback();

    /**
     * Closes the connection; a closed transport may be opened again.
     */
    abstract public function close();

    /** @return bool */
    abstract public function isOpen();

    /**
     * The client library, e.g. "pdo_ibm, IBM Data Server Driver 12.1.4".
     *
     * @return string
     */
    abstract public function clientVersion();

    /**
     * The server's version string ("12.01.0600"), or ''.
     *
     * @return string
     */
    abstract public function serverVersion();

    /**
     * A new, unopened transport of the same kind (the driver opens a second connection to create a global
     * temporary table outside the transaction of the first).
     *
     * @return expDb2Transport
     */
    abstract public function sibling();

    /**
     * The last error: array( 'sqlcode' => int|null, 'state' => string|null, 'message' => string ), or null.
     *
     * @return array|null
     */
    public function error()
    {
        return $this->error;
    }

    protected function setError( $sqlCode, $state, $message )
    {
        $this->error = array( 'sqlcode' => $sqlCode === null ? null : (int)$sqlCode,
                              'state' => $state === null ? null : (string)$state, 'message' => trim( (string)$message ) );
    }

    /**
     * A row with lower-case keys and every value as MySQL would give it: a string, or null.
     *
     * @param array $row
     * @return array
     */
    public static function normalizeRow( array $row )
    {
        $out = array();
        foreach ( $row as $key => $value )
        {
            $key = is_string( $key ) ? strtolower( $key ) : $key;
            if ( $value === null || is_string( $value ) )
                $out[$key] = $value;
            else if ( is_bool( $value ) )
                $out[$key] = $value ? '1' : '0';
            else if ( is_int( $value ) )
                $out[$key] = (string)$value;
            else if ( is_float( $value ) )
                $out[$key] = self::floatString( $value );
            else if ( is_resource( $value ) )
                $out[$key] = (string)stream_get_contents( $value );
            else if ( $value instanceof DateTimeInterface )
                $out[$key] = $value->format( 'Y-m-d H:i:s' );
            else
                $out[$key] = (string)$value;
        }
        return $out;
    }

    /**
     * A float as plain digits, without an exponent.
     *
     * @param float $value
     * @return string
     */
    public static function floatString( $value )
    {
        $text = (string)$value;
        if ( stripos( $text, 'e' ) === false )
            return $text;
        $text = rtrim( rtrim( sprintf( '%.17F', $value ), '0' ), '.' );
        return $text === '' || $text === '-' ? '0' : $text;
    }

    /**
     * The layer PHP has for Db2 and Informix: pdo_ibm or ibm_db2 (IBM's PECL extensions over the Data Server Driver
     * for ODBC and CLI), or PDO_ODBC when it is asked for (an ODBC driver with 64-bit lengths: the Windows driver, or
     * libdb2o.so of a full Db2 client; the libdb2.so of the ODBC and CLI package gives unixODBC 32-bit lengths, which
     * reads NULL as garbage).
     *
     * @param string $wanted auto, pdo_ibm, ibm_db2 or pdo_odbc
     * @param string[]|null $drivers PDO's drivers (null: PDO::getAvailableDrivers())
     * @param string[] $auto the order of auto
     * @param bool|null $ibmDb2 whether ibm_db2 is loaded (null: function_exists('db2_connect'))
     * @return string|null the layer, or null
     */
    public static function availableLayer( $wanted = 'auto', $drivers = null, array $auto = array( 'pdo_ibm', 'ibm_db2' ), $ibmDb2 = null )
    {
        if ( $drivers === null )
            $drivers = class_exists( 'PDO', false ) ? PDO::getAvailableDrivers() : array();
        if ( $ibmDb2 === null )
            $ibmDb2 = function_exists( 'db2_connect' );
        $wanted = strtolower( trim( (string)$wanted ) );
        $order = in_array( $wanted, array( 'pdo_ibm', 'ibm_db2', 'pdo_odbc' ), true ) ? array( $wanted ) : $auto;
        foreach ( $order as $layer )
        {
            if ( $layer === 'ibm_db2' ? $ibmDb2 : in_array( $layer === 'pdo_ibm' ? 'ibm' : 'odbc', $drivers, true ) )
                return $layer;
        }
        return null;
    }
}
