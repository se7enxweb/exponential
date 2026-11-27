<?php
/**
 * File containing the expCassandraPHPTransport class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * expCassandraTransport over the library mroosz/php-cassandra (PHP 8.1 or later, MIT): CQL's native protocol v3 to v5
 * in PHP, with authentication, TLS (a host written tls://host), prepared statements (a statement with values is
 * prepared once per connection and kept), paging and LZ4 compression. composer.json suggests the library; an
 * installation whose Composer autoloader does not know it (copied into vendor/ by hand) has it loaded from
 * vendor/mroosz/php-cassandra/src here.
 *
 * @since 6.0.15
 * @package lib
 */
class expCassandraPHPTransport extends expCassandraTransport
{
    /** @var object|null Cassandra\Connection */
    protected $connection = null;

    /** @var array */
    protected $config = array();

    public function name()
    {
        return 'php-cassandra';
    }

    /**
     * Whether the library can be used here (PHP 8.1 or later, the classes found).
     *
     * @return bool
     */
    public static function available()
    {
        if ( PHP_VERSION_ID < 80100 )
            return false;
        self::requireLibrary();
        return class_exists( 'Cassandra\\Connection' ) && class_exists( 'Cassandra\\Connection\\StreamNodeConfig' );
    }

    /**
     * Loads the library from vendor/ when Composer's autoloader does not know it.
     */
    public static function requireLibrary()
    {
        if ( class_exists( 'Cassandra\\Connection', true ) )
            return;
        $root = class_exists( 'eZSys' ) ? (string)eZSys::rootDir() : (string)getcwd();
        $src = rtrim( $root, '/' ) . '/vendor/mroosz/php-cassandra/src';
        if ( !is_file( $src . '/Connection.php' ) )
            return;
        spl_autoload_register( function ( $class ) use ( $src )
        {
            if ( strncmp( $class, 'Cassandra\\', 10 ) !== 0 )
                return;
            $file = $src . '/' . str_replace( '\\', '/', substr( $class, 10 ) ) . '.php';
            if ( is_file( $file ) )
                require $file;
        } );
    }

    public function open( array $config )
    {
        $this->config = $config;
        $this->error = null;
        if ( !self::available() )
        {
            $this->setError( null, expCassandraTransport::missingLayerText() );
            return false;
        }
        try
        {
            $nodes = array();
            $port = isset( $config['port'] ) && (int)$config['port'] > 0 ? (int)$config['port'] : 9042;
            $ssl = isset( $config['ssl'] ) && is_array( $config['ssl'] ) ? $config['ssl'] : array();
            foreach ( (array)$config['hosts'] as $host )
            {
                list( $name, $nodePort ) = self::splitHost( (string)$host, $port );
                if ( !empty( $config['tls'] ) && strpos( $name, '://' ) === false )
                    $name = 'tls://' . $name;
                $sslOptions = $ssl ? $ssl : array( 'verify_peer' => true, 'verify_peer_name' => true, 'allow_self_signed' => false );
                $nodes[] = new \Cassandra\Connection\StreamNodeConfig( $name, $nodePort, (string)$config['user'], (string)$config['password'],
                                                                     (float)( isset( $config['connect_timeout'] ) ? $config['connect_timeout'] : 5 ),
                                                                     (float)( isset( $config['request_timeout'] ) ? $config['request_timeout'] : 30 ),
                                                                     false, $sslOptions );
            }
            // protocol v4 unless v5 is asked for: php-cassandra 1.2.0 fails to send a v5 frame of more than 128 KiB (a
            // statement with a long text: "Stream write failed"), v4 has no such limit
            $versions = isset( $config['protocol'] ) && (int)$config['protocol'] >= 5
                ? array( \Cassandra\Protocol\ProtocolVersion::V5, \Cassandra\Protocol\ProtocolVersion::V4, \Cassandra\Protocol\ProtocolVersion::V3 )
                : array( \Cassandra\Protocol\ProtocolVersion::V4, \Cassandra\Protocol\ProtocolVersion::V3 );
            $options = new \Cassandra\Connection\ConnectionOptions( !empty( $config['compression'] ) && function_exists( 'lz4_compress' ),
                                                                    false, \Cassandra\Connection\NodeSelectionStrategy::RoundRobin, 100, $versions,
                                                                    \Cassandra\Protocol\ProtocolVersion::V4 );
            // a node that resets the socket while the protocol version is agreed (it does under a burst of new
            // connections) is asked once more
            for ( $attempt = 1; ; $attempt++ )
            {
                $this->connection = new \Cassandra\Connection( $nodes, (string)( isset( $config['keyspace'] ) ? $config['keyspace'] : '' ), $options );
                try
                {
                    $this->connection->connect();
                    break;
                }
                catch ( \Cassandra\Exception\ServerException $e )
                {
                    throw $e;
                }
                catch ( Throwable $e )
                {
                    if ( $attempt >= 3 )
                        throw $e;
                    usleep( 200000 * $attempt );
                }
            }
            $this->connection->setConsistency( self::consistency( isset( $config['consistency'] ) ? $config['consistency'] : 'LOCAL_QUORUM' ) );
            return true;
        }
        catch ( Throwable $e )
        {
            $this->connection = null;
            $this->takeException( $e );
            return false;
        }
    }

    /**
     * A host as name and port (host, host:port, [v6]:port, tls://host:port).
     *
     * @param string $host
     * @param int $port
     * @return array( string, int )
     */
    public static function splitHost( $host, $port )
    {
        $host = trim( $host );
        $scheme = '';
        if ( preg_match( '#^([a-z]+://)(.*)$#i', $host, $m ) )
        {
            $scheme = $m[1];
            $host = $m[2];
        }
        if ( preg_match( '/^\[(.+)\](?::(\d+))?$/', $host, $m ) )
            return array( $scheme . '[' . $m[1] . ']', isset( $m[2] ) && $m[2] !== '' ? (int)$m[2] : $port );
        if ( substr_count( $host, ':' ) === 1 )
        {
            list( $name, $p ) = explode( ':', $host );
            return array( $scheme . $name, (int)$p ?: $port );
        }
        return array( $scheme . $host, $port );
    }

    /**
     * The library's consistency of a name.
     *
     * @param string $name
     * @return object
     */
    public static function consistency( $name )
    {
        $name = strtoupper( trim( (string)$name ) );
        foreach ( \Cassandra\Consistency::cases() as $case )
            if ( $case->name === $name )
                return $case;
        return \Cassandra\Consistency::LOCAL_QUORUM;
    }

    public function run( $cql, array $values = array(), array $options = array() )
    {
        $this->error = null;
        if ( !$this->connection )
        {
            $this->setError( null, 'The connection to Cassandra is not open' );
            return false;
        }
        try
        {
            // no serial consistency: php-cassandra 1.2.0 writes the enum instead of its value into the frame, which
            // the server refuses for every conditional statement; the server's default (SERIAL) applies
            $queryOptions = new \Cassandra\Request\Options\QueryOptions(
                true,
                isset( $options['page_size'] ) ? (int)$options['page_size'] : null,
                isset( $options['paging_state'] ) && $options['paging_state'] !== '' ? (string)$options['paging_state'] : null
            );
            $consistency = isset( $options['consistency'] ) ? self::consistency( $options['consistency'] ) : null;
            $result = $this->connection->query( (string)$cql, array_values( $values ), $consistency, $queryOptions );
            $answer = array( 'rows' => array(), 'columns' => array(), 'paging_state' => null );
            if ( $result instanceof \Cassandra\Response\Result\RowsResult )
            {
                $meta = $result->getRowsMetadata();
                $answer['paging_state'] = $meta->pagingState;
                if ( is_array( $meta->columns ) )
                    foreach ( $meta->columns as $column )
                        $answer['columns'][$column->name] = true;
                foreach ( $result->fetchAll() as $row )
                    $answer['rows'][] = $row;
            }
            return $answer;
        }
        catch ( Throwable $e )
        {
            $this->takeException( $e );
            return false;
        }
    }

    /**
     * Takes a library exception as the error (code: the protocol's error code of a server error).
     *
     * @param Throwable $e
     */
    protected function takeException( Throwable $e )
    {
        $code = null;
        if ( $e instanceof \Cassandra\Exception\ServerException )
            $code = (int)$e->getCode();
        $message = $e->getMessage();
        if ( !empty( $this->config['password'] ) )
            $message = str_replace( (string)$this->config['password'], '***', $message );
        $this->setError( $code, $message );
        if ( $code === null && ( $e instanceof \Cassandra\Exception\ConnectionException || $e instanceof \Cassandra\Exception\NodeException
                                 || $e instanceof \Cassandra\Exception\StreamException || $e instanceof \Cassandra\Exception\SocketException ) )
            $this->error['category'] = 'connection';
    }

    public function close()
    {
        if ( $this->connection )
        {
            try
            {
                $this->connection->disconnect();
            }
            catch ( Throwable $e )
            {
            }
        }
        $this->connection = null;
    }

    public function isOpen()
    {
        return $this->connection !== null && $this->connection->isConnected();
    }

    public function protocolVersion()
    {
        return $this->connection ? (int)$this->connection->getVersion() : 0;
    }

    public function clientVersion()
    {
        return class_exists( 'Cassandra\\ReleaseConstants' ) ? 'php-cassandra ' . \Cassandra\ReleaseConstants::PHP_CASSANDRA_DRIVER_VERSION : 'php-cassandra';
    }
}
?>
