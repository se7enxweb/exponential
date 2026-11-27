<?php
/**
 * File containing the expCassandraTransport class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The PHP layer under expCassandraDB: CQL's native protocol to Apache Cassandra or ScyllaDB. The driver speaks only to
 * this class, so a test can put a recording transport in its place (tests/tests/lib/ezdb/cassandra). The layer of PHP
 * is the library mroosz/php-cassandra (expCassandraPHPTransport): a client of the protocol (v3 to v5) written in PHP,
 * maintained, MIT licensed, without an extension of PHP. DataStax's extension (datastax/php-driver) is unmaintained
 * since 2017 and builds on no PHP 8; its successor of ScyllaDB's users (he4rt/scylladb-php-driver) is a C++ extension
 * to build per PHP version.
 *
 * run() returns array( 'rows' => array( row, ... ), 'columns' => array( name => CQL type ), 'paging_state' => string|null )
 * with each row a column => value array as the library gives them (int, float, bool, string, array for a
 * collection, null), or false on an error (then error() tells which).
 *
 * @since 6.0.15
 * @package lib
 */
abstract class expCassandraTransport
{
    /** @var array|null the last error: code, message, category */
    protected $error = null;

    /**
     * The layer's name (php-cassandra).
     *
     * @return string
     */
    abstract public function name();

    /**
     * Opens the connection.
     *
     * @param array $config hosts (array of host or host:port; tls://host for TLS), port, user, password, keyspace,
     *                      connect_timeout, request_timeout, consistency, serial_consistency, compression, ssl (options)
     * @return bool
     */
    abstract public function open( array $config );

    /**
     * Runs one CQL statement, with "?" values bound when $values is not empty.
     *
     * @param string $cql
     * @param array $values PHP values in the order of the markers
     * @param array $options page_size, paging_state, consistency (ONE, LOCAL_QUORUM ...), serial (SERIAL, LOCAL_SERIAL)
     * @return array|false
     */
    abstract public function run( $cql, array $values = array(), array $options = array() );

    /**
     * Closes the connection; a closed transport may be opened again.
     */
    abstract public function close();

    /** @return bool */
    abstract public function isOpen();

    /**
     * The version of the protocol in use (3, 4, 5), or 0.
     *
     * @return int
     */
    abstract public function protocolVersion();

    /**
     * The version of the layer (the library's).
     *
     * @return string
     */
    abstract public function clientVersion();

    /**
     * The last error.
     *
     * @return array|null code, message, category
     */
    public function error()
    {
        return $this->error;
    }

    /**
     * Notes an error.
     *
     * @param int|null $code
     * @param string $message
     */
    protected function setError( $code, $message )
    {
        $this->error = array( 'code' => $code === null ? null : (int)$code, 'message' => (string)$message,
                              'category' => expCassandraDialect::errorCategory( $code, $message ) );
    }

    /**
     * The layer this server can use: php-cassandra, or null.
     *
     * @return string|null
     */
    public static function availableLayer()
    {
        return expCassandraPHPTransport::available() ? 'php-cassandra' : null;
    }

    /**
     * Why there is no layer, for a message.
     *
     * @return string
     */
    public static function missingLayerText()
    {
        if ( PHP_VERSION_ID < 80100 )
            return 'Apache Cassandra and ScyllaDB need PHP 8.1 or later and the library mroosz/php-cassandra (composer require mroosz/php-cassandra)';
        return 'Apache Cassandra and ScyllaDB need the library mroosz/php-cassandra: run "composer require mroosz/php-cassandra"';
    }
}
?>
