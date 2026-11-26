<?php
/**
 * File containing the expFirebirdTransport class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The PHP layer under expFirebirdDB. The driver speaks only to this class, so a test can put a recording transport
 * in its place (tests/tests/lib/ezdb/firebird). The layer of PHP is pdo_firebird (expFirebirdPDOTransport).
 *
 * run() returns array( 'rows' => array( row, ... ), 'affected' => int ), each row a lower-case column => value
 * array with every value a string or null (as MySQL gives them), or false on an error (then error() tells which).
 */
abstract class expFirebirdTransport
{
    /** @var array|null the last error: sqlcode, gds, state, message */
    protected $error = null;

    /**
     * The layer's name (pdo_firebird).
     *
     * @return string
     */
    abstract public function name();

    /**
     * Opens the connection.
     *
     * @param array $config host, port, database, user, password, charset, role
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
     * The client library, e.g. "pdo_firebird 8.4.14, Firebird client 5.0.1".
     *
     * @return string
     */
    abstract public function clientVersion();

    /**
     * The server's version string ("LI-V5.0.4.1812 Firebird 5.0"), or ''.
     *
     * @return string
     */
    abstract public function serverVersion();

    /**
     * A new, unopened transport of the same kind (the driver opens a second connection to create a global
     * temporary table outside the transaction of the first).
     *
     * @return expFirebirdTransport
     */
    abstract public function sibling();

    /**
     * The last error: array( 'sqlcode' => int|null, 'gds' => int|null, 'state' => string|null, 'message' => string ),
     * or null.
     *
     * @return array|null
     */
    public function error()
    {
        return $this->error;
    }

    protected function setError( $sqlCode, $gds, $state, $message )
    {
        $this->error = array( 'sqlcode' => $sqlCode === null ? null : (int)$sqlCode, 'gds' => $gds === null ? null : (int)$gds,
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
     * Whether PHP can talk to Firebird: pdo_firebird of PHP 8.4 or later (earlier versions keep an implicit
     * transaction open in autocommit mode, so a transaction cannot be started and other connections' writes stay
     * invisible).
     *
     * @return string|null the layer, or null
     */
    public static function availableLayer()
    {
        if ( PHP_VERSION_ID >= 80400 && class_exists( 'PDO', false ) && in_array( 'firebird', PDO::getAvailableDrivers(), true ) )
            return 'pdo_firebird';
        return null;
    }
}
