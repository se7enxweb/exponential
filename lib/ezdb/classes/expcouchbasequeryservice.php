<?php
/**
 * File containing the expCouchbaseQueryService class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The Query service of a Couchbase cluster over its REST API (POST /query/service): what expCouchbaseDB sends its
 * SQL++ through. PHP's curl extension is all it needs; one handle is kept per connection, so the TCP (and TLS)
 * connection is reused from statement to statement.
 *
 * Why REST and not the SDK: Couchbase's PHP SDK (the extension couchbase, 4.x) is a C++ extension over the C++ SDK that
 * has to be built for each PHP version; no host offers it as a package, and the driver needs nothing of it but the
 * Query service (the key-value API would save a few milliseconds a write, at the cost of a second code path). The
 * Query service answers the same SQL++ the SDK sends, with transactions (BEGIN WORK, txid), in JSON.
 *
 * Tests give the driver a stand-in: any callable( array $request ) returning the decoded response.
 *
 * @since 6.0.15
 * @package lib
 */
class expCouchbaseQueryService
{
    /** @var string http(s)://host:port */
    protected $base;

    /** @var string */
    protected $user;

    /** @var string */
    protected $password;

    /** @var array curl options of TLS and timeouts */
    protected $options;

    /** @var resource|CurlHandle|null */
    protected $handle = null;

    /** @var string[] the nodes, tried in turn when one does not answer */
    protected $nodes;

    /**
     * @param string $servers host, host:port, a comma list; a scheme (https://) turns TLS on
     * @param int $port the Query service's port (8093, or 18093 with TLS) when a node names none
     * @param string $user
     * @param string $password
     * @param array $settings TLS (enabled/disabled), TLSCAFile, TLSVerifyPeer, ConnectTimeout, RequestTimeout
     */
    public function __construct( $servers, $port, $user, $password, array $settings = array() )
    {
        $tls = isset( $settings['TLS'] ) && $settings['TLS'] === 'enabled';
        $this->nodes = array();
        foreach ( self::hosts( $servers ) as $host )
        {
            $scheme = $tls ? 'https' : 'http';
            if ( preg_match( '#^(https?)://(.+)$#i', $host, $m ) )
            {
                $scheme = strtolower( $m[1] );
                $host = $m[2];
            }
            $host = rtrim( $host, '/' );
            if ( !preg_match( '/:\d+$/', $host ) || substr_count( $host, ':' ) > 1 && substr( $host, -1 ) === ']' )
                $host .= ':' . ( $port ? (int)$port : ( $scheme === 'https' ? 18093 : 8093 ) );
            $this->nodes[] = $scheme . '://' . $host;
        }
        if ( !$this->nodes )
            $this->nodes[] = 'http://127.0.0.1:' . ( $port ? (int)$port : 8093 );
        $this->base = $this->nodes[0];
        $this->user = (string)$user;
        $this->password = (string)$password;
        $this->options = array(
            CURLOPT_CONNECTTIMEOUT => isset( $settings['ConnectTimeout'] ) ? max( 1, (int)$settings['ConnectTimeout'] ) : 5,
            CURLOPT_TIMEOUT => isset( $settings['RequestTimeout'] ) ? max( 1, (int)$settings['RequestTimeout'] ) : 75,
        );
        if ( !empty( $settings['TLSCAFile'] ) )
            $this->options[CURLOPT_CAINFO] = (string)$settings['TLSCAFile'];
        if ( isset( $settings['TLSVerifyPeer'] ) && $settings['TLSVerifyPeer'] === 'disabled' )
        {
            $this->options[CURLOPT_SSL_VERIFYPEER] = false;
            $this->options[CURLOPT_SSL_VERIFYHOST] = 0;
        }
    }

    /**
     * The nodes of a Server setting (pure).
     *
     * @param string|array $servers
     * @return string[]
     */
    public static function hosts( $servers )
    {
        $list = is_array( $servers ) ? $servers : explode( ',', (string)$servers );
        return array_values( array_filter( array_map( 'trim', $list ), 'strlen' ) );
    }

    /** @return string the node statements go to */
    public function base()
    {
        return $this->base;
    }

    /**
     * Whether PHP can reach the service (curl).
     *
     * @return bool
     */
    public static function available()
    {
        return function_exists( 'curl_init' );
    }

    /**
     * Sends one request.
     *
     * @param array $request statement, query_context, scan_consistency, txid ...
     * @return array the decoded response: status, results, errors, metrics; a transport failure is status 'fatal'
     *               with errors [ { code: 0, msg } ]
     */
    public function __invoke( array $request )
    {
        $body = json_encode( $request, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE );
        $tried = 0;
        $error = '';
        foreach ( array_unique( array_merge( array( $this->base ), $this->nodes ) ) as $node )
        {
            $tried++;
            if ( $this->handle === null || $node !== $this->base )
            {
                $this->handle = curl_init();
                $this->base = $node;
            }
            curl_setopt_array( $this->handle, $this->options + array(
                CURLOPT_URL => $node . '/query/service',
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERPWD => $this->user . ':' . $this->password,
                CURLOPT_HTTPHEADER => array( 'Content-Type: application/json', 'Accept: application/json' ),
            ) );
            $raw = curl_exec( $this->handle );
            if ( $raw === false )
            {
                $error = curl_error( $this->handle );
                $this->handle = null;
                // a statement inside a transaction belongs to the node that began it
                if ( isset( $request['txid'] ) )
                    break;
                continue;
            }
            $status = (int)curl_getinfo( $this->handle, CURLINFO_HTTP_CODE );
            $response = json_decode( (string)$raw, true );
            if ( !is_array( $response ) )
                return array( 'status' => 'fatal', 'results' => array(), 'errors' => array( array( 'code' => $status, 'msg' => 'HTTP ' . $status . ': ' . mb_substr( (string)$raw, 0, 300 ) ) ) );
            return $response;
        }
        return array( 'status' => 'fatal', 'results' => array(), 'errors' => array( array( 'code' => 0, 'msg' => 'The Query service does not answer (' . $tried . ' node(s)): ' . $error ) ) );
    }

    public function close()
    {
        $this->handle = null;
    }
}
?>
