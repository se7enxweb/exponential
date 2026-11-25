<?php
/**
 * File containing the expSQLServerTransport class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The PHP layer under expSQLServerDB: Microsoft's sqlsrv functions, pdo_sqlsrv, PDO_ODBC with an ODBC driver for SQL
 * Server, or pdo_dblib over FreeTDS. The driver speaks only to this class, so the SQL it writes is the same on every
 * layer, and a test can put a recording transport in its place.
 *
 * run() returns every result set of a batch: array( 'sets' => array( array( row, ... ), ... ), 'affected' => int ),
 * each row a column => value array with every value a string or null (as MySQL gives them), or false on an error
 * (then error() tells which).
 */
abstract class expSQLServerTransport
{
    /** @var array|null the last error: number, state, message */
    protected $error = null;

    /**
     * The layer's name: sqlsrv, pdo_sqlsrv, pdo_odbc, pdo_dblib.
     *
     * @return string
     */
    abstract public function name();

    /**
     * Opens the connection.
     *
     * @param array $config host, port, database, user, password, encrypt, trust_server_certificate,
     *                      multi_subnet_failover, application_intent, login_timeout, app_name, odbc_driver
     * @return bool
     */
    abstract public function open( array $config );

    /**
     * Runs a batch, with "?" parameters bound as NVARCHAR when $params is not empty.
     *
     * @param string $sql
     * @param string[] $params
     * @return array|false
     */
    abstract public function run( $sql, array $params = array() );

    /**
     * Closes the connection; a closed transport may be opened again.
     */
    abstract public function close();

    /**
     * @return bool
     */
    abstract public function isOpen();

    /**
     * The client library and its version, e.g. "sqlsrv 5.13.3, ODBC Driver 18 for SQL Server 18.07.0001".
     *
     * @return string
     */
    abstract public function clientVersion();

    /**
     * The last error: array( 'number' => int|null, 'state' => string|null, 'message' => string ), or null.
     *
     * @return array|null
     */
    public function error()
    {
        return $this->error;
    }

    /**
     * @param int|null $number
     * @param string|null $state
     * @param string $message
     */
    protected function setError( $number, $state, $message )
    {
        $this->error = array( 'number' => $number === null ? null : (int)$number, 'state' => $state, 'message' => (string)$message );
    }

    /**
     * A row with every value as MySQL would give it: a string, or null.
     *
     * @param array $row
     * @return array
     */
    public static function normalizeRow( array $row )
    {
        foreach ( $row as $key => $value )
        {
            if ( $value === null || is_string( $value ) )
                continue;
            if ( is_bool( $value ) )
                $row[$key] = $value ? '1' : '0';
            else if ( is_int( $value ) )
                $row[$key] = (string)$value;
            else if ( is_float( $value ) )
                $row[$key] = self::floatString( $value );
            else if ( is_resource( $value ) )
                $row[$key] = (string)stream_get_contents( $value );
            else if ( $value instanceof DateTimeInterface )
                $row[$key] = $value->format( 'Y-m-d H:i:s' );
            else
                $row[$key] = (string)$value;
        }
        return $row;
    }

    /**
     * A float as plain digits, without an exponent where PHP's string conversion would write one.
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
     * The layer to use: the configured one, or for "auto" the first one PHP has, in the order sqlsrv, pdo_sqlsrv,
     * pdo_odbc, pdo_dblib. Null when PHP has none.
     *
     * @param string $layer auto, sqlsrv, pdo_sqlsrv, pdo_odbc, pdo_dblib
     * @param array|null $available the layers PHP has (default: availableLayers())
     * @return string|null
     */
    public static function chooseLayer( $layer, $available = null )
    {
        if ( $available === null )
            $available = self::availableLayers();
        $layer = strtolower( trim( (string)$layer ) );
        if ( $layer === '' || $layer === 'auto' )
        {
            foreach ( array( 'sqlsrv', 'pdo_sqlsrv', 'pdo_odbc', 'pdo_dblib' ) as $candidate )
                if ( in_array( $candidate, $available, true ) )
                    return $candidate;
            return null;
        }
        $aliases = array( 'odbc' => 'pdo_odbc', 'dblib' => 'pdo_dblib', 'freetds' => 'pdo_dblib', 'pdo' => 'pdo_sqlsrv' );
        if ( isset( $aliases[$layer] ) )
            $layer = $aliases[$layer];
        return in_array( $layer, $available, true ) ? $layer : null;
    }

    /**
     * The layers this PHP has.
     *
     * @return string[]
     */
    public static function availableLayers()
    {
        $layers = array();
        if ( function_exists( 'sqlsrv_connect' ) )
            $layers[] = 'sqlsrv';
        $pdo = class_exists( 'PDO' ) ? PDO::getAvailableDrivers() : array();
        if ( in_array( 'sqlsrv', $pdo, true ) )
            $layers[] = 'pdo_sqlsrv';
        if ( in_array( 'odbc', $pdo, true ) )
            $layers[] = 'pdo_odbc';
        if ( in_array( 'dblib', $pdo, true ) )
            $layers[] = 'pdo_dblib';
        return $layers;
    }

    /**
     * A transport of the layer.
     *
     * @param string $layer
     * @return expSQLServerTransport
     */
    public static function create( $layer )
    {
        if ( $layer === 'sqlsrv' )
            return new expSQLServerSqlsrvTransport();
        return new expSQLServerPDOTransport( $layer );
    }
}
