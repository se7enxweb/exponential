<?php
/**
 * File containing the expHANATransport class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The PHP layer under expHANADB. The driver speaks only to this class, so a test can put a recording transport in
 * its place (tests/tests/lib/ezdb/hana). The layer of PHP is SAP's HANA ODBC driver through PDO_ODBC or PHP's odbc
 * extension (expHANAODBCTransport).
 *
 * run() returns array( 'rows' => array( row, ... ), 'affected' => int ), each row a lower-case column => value array
 * with every value a string or null (as MySQL gives them), or false on an error (then error() tells which).
 */
abstract class expHANATransport
{
    /** @var array|null the last error: code, state, message */
    protected $error = null;

    /**
     * The layer's name (pdo_odbc or odbc).
     *
     * @return string
     */
    abstract public function name();

    /**
     * Opens the connection.
     *
     * @param array $config host, port, schema, user, password, driver, tenant, encrypt, validate_certificate, extra,
     *                      persistent, layer
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
     * The client library, e.g. "pdo_odbc 8.5.0, HDBODBC 2.25.22".
     *
     * @return string
     */
    abstract public function clientVersion();

    /**
     * A new, unopened transport of the same kind (the driver opens a second connection to create a global temporary
     * table outside the transaction of the first).
     *
     * @return expHANATransport
     */
    abstract public function sibling();

    /**
     * The last error: array( 'code' => int|null, 'state' => string|null, 'message' => string ), or null.
     *
     * @return array|null
     */
    public function error()
    {
        return $this->error;
    }

    protected function setError( $code, $state, $message )
    {
        $this->error = array( 'code' => $code === null ? null : (int)$code, 'state' => $state === null ? null : (string)$state,
                              'message' => trim( (string)$message ) );
    }

    /**
     * The error code of HANA in a message of its ODBC driver ("General error;301 unique constraint violated"), or
     * null.
     *
     * @param string $message
     * @return int|null
     */
    public static function codeInMessage( $message )
    {
        if ( preg_match( '/\](?:[^\]\[]*?);(-?\d+)\s/', (string)$message, $m ) || preg_match( '/;(-?\d+)\s/', (string)$message, $m ) )
            return (int)$m[1];
        return null;
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
     * The ODBC layers PHP has: pdo_odbc and odbc.
     *
     * @return string[]
     */
    public static function availableLayers()
    {
        $have = array();
        if ( class_exists( 'PDO', false ) && in_array( 'odbc', PDO::getAvailableDrivers(), true ) )
            $have[] = 'pdo_odbc';
        if ( function_exists( 'odbc_connect' ) )
            $have[] = 'odbc';
        return $have;
    }

    /**
     * The layer to use: pdo_odbc or odbc, or null when PHP has neither (or not the one asked for).
     *
     * @param string $wanted auto, pdo_odbc or odbc
     * @param string[]|null $have the layers PHP has (tests); null: asked of PHP
     * @return string|null
     */
    public static function availableLayer( $wanted = 'auto', $have = null )
    {
        $have = $have === null ? self::availableLayers() : $have;
        $wanted = strtolower( trim( (string)$wanted ) );
        if ( $wanted === 'pdo_odbc' || $wanted === 'odbc' )
            return in_array( $wanted, $have, true ) ? $wanted : null;
        // the odbc extension first: PDO_ODBC needs it anyway for the statements with ":" (triggers)
        foreach ( array( 'odbc', 'pdo_odbc' ) as $layer )
            if ( in_array( $layer, $have, true ) )
                return $layer;
        return null;
    }
}
