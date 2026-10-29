<?php
/**
 * File containing the expRestCors class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Cross-origin resource sharing (CORS) for the REST interface (index_rest.php: the content API, the OAuth token
 * end point and every other route of a REST provider), configured in rest.ini [CorsSettings].
 *
 * Off by default. While it is off nothing changes: no header is added and an OPTIONS request goes to the router as
 * before. While it is on:
 *
 * - A response to a request whose Origin is in AllowedOrigins[] carries Access-Control-Allow-Origin with that
 *   origin (never "*" when credentials are allowed), Access-Control-Allow-Credentials when AllowCredentials is
 *   enabled and Access-Control-Expose-Headers when ExposedHeaders[] lists any.
 * - A request from any other origin, or without an Origin, gets no Access-Control-* header at all.
 * - Every response carries "Vary: Origin", so a shared cache never serves the answer for one origin to another.
 * - A preflight (OPTIONS with Origin and Access-Control-Request-Method) is answered here and never runs the
 *   request: 204 with the allowed methods, headers and Access-Control-Max-Age when the origin, the method and every
 *   requested header are allowed, 403 without any Access-Control-* header otherwise.
 *
 * The class has no state but its settings and reads nothing but the arguments it is given, so it is tested without
 * a kernel or a database (tests/tests/kernel/private/restapi/expRestCorsTest.php).
 */
class expRestCors
{
    const SECTION = 'CorsSettings';

    /** @var bool */
    protected $enabled;
    /** @var array lower case origins, "scheme://host[:port]" */
    protected $origins;
    /** @var bool AllowedOrigins[]=* given and credentials not allowed */
    protected $anyOrigin;
    /** @var array upper case method names */
    protected $methods;
    /** @var array header names as configured */
    protected $headers;
    /** @var array */
    protected $exposedHeaders;
    /** @var int seconds, 0 for no Access-Control-Max-Age */
    protected $maxAge;
    /** @var bool */
    protected $credentials;

    /**
     * @param array $settings enabled (bool), origins, methods, headers, exposed_headers (arrays of strings),
     *                        max_age (int) and credentials (bool); missing keys take the defaults of rest.ini
     */
    public function __construct( array $settings = array() )
    {
        $settings += array(
            'enabled' => false,
            'origins' => array(),
            'methods' => array( 'GET', 'POST', 'PUT', 'PATCH', 'DELETE' ),
            'headers' => array( 'Authorization', 'Content-Type', 'Accept', 'Accept-Language', 'X-Siteaccess' ),
            'exposed_headers' => array(),
            'max_age' => 600,
            'credentials' => false,
        );

        $this->enabled = (bool)$settings['enabled'];
        $this->credentials = (bool)$settings['credentials'];
        $this->maxAge = max( 0, (int)$settings['max_age'] );

        $this->origins = array();
        $this->anyOrigin = false;
        foreach ( self::stringList( $settings['origins'] ) as $origin )
        {
            if ( $origin === '*' )
            {
                // A wildcard together with credentials would let every site act as the signed in user: ignored.
                if ( !$this->credentials )
                    $this->anyOrigin = true;
                continue;
            }
            $normalized = self::normalizeOrigin( $origin );
            if ( $normalized !== null )
                $this->origins[$normalized] = true;
        }

        $this->methods = array();
        foreach ( self::stringList( $settings['methods'] ) as $method )
            $this->methods[strtoupper( $method )] = true;

        $this->headers = self::stringList( $settings['headers'] );
        $this->exposedHeaders = self::stringList( $settings['exposed_headers'] );
    }

    /**
     * Builds the settings from rest.ini [CorsSettings].
     *
     * @param eZINI $ini rest.ini
     * @return expRestCors
     */
    public static function fromIni( $ini )
    {
        $settings = array();
        $read = function ( $name ) use ( $ini )
        {
            return $ini->hasVariable( self::SECTION, $name ) ? $ini->variable( self::SECTION, $name ) : null;
        };

        $settings['enabled'] = strtolower( trim( (string)$read( 'Cors' ) ) ) === 'enabled';
        $settings['credentials'] = strtolower( trim( (string)$read( 'AllowCredentials' ) ) ) === 'enabled';
        foreach ( array( 'origins' => 'AllowedOrigins', 'methods' => 'AllowedMethods',
                         'headers' => 'AllowedHeaders', 'exposed_headers' => 'ExposedHeaders' ) as $key => $name )
        {
            $value = $read( $name );
            if ( is_array( $value ) )
                $settings[$key] = $value;
        }
        $maxAge = $read( 'MaxAge' );
        if ( $maxAge !== null && $maxAge !== '' )
            $settings['max_age'] = (int)$maxAge;

        return new self( $settings );
    }

    /** @return bool */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Whether a request origin is allowed. An origin is compared exactly (scheme, host and port), after lower casing
     * the scheme and the host; "null" and anything that is not an origin are never allowed.
     *
     * @param string|null $origin the Origin header
     * @return bool
     */
    public function isOriginAllowed( $origin )
    {
        if ( !$this->enabled )
            return false;
        $normalized = self::normalizeOrigin( $origin );
        if ( $normalized === null )
            return false;
        return $this->anyOrigin || isset( $this->origins[$normalized] );
    }

    /**
     * Whether the request is a CORS preflight that this class answers (only while CORS is enabled).
     *
     * @param string $method the request method
     * @param array $server $_SERVER
     * @return bool
     */
    public function isPreflight( $method, array $server )
    {
        return $this->enabled
            && strtoupper( (string)$method ) === 'OPTIONS'
            && isset( $server['HTTP_ORIGIN'] )
            && isset( $server['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] );
    }

    /**
     * The answer to a preflight request.
     *
     * @param array $server $_SERVER
     * @return array( 'status' => int, 'headers' => array of "Name: value" lines )
     */
    public function preflight( array $server )
    {
        $refused = array( 'status' => 403, 'headers' => array( 'Vary: Origin' ) );

        $origin = isset( $server['HTTP_ORIGIN'] ) ? $server['HTTP_ORIGIN'] : null;
        if ( !$this->isOriginAllowed( $origin ) )
            return $refused;

        $method = strtoupper( trim( (string)( isset( $server['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] ) ? $server['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] : '' ) ) );
        if ( $method === '' || !isset( $this->methods[$method] ) )
            return $refused;

        $allowedHeaders = array();
        foreach ( $this->headers as $header )
            $allowedHeaders[strtolower( $header )] = true;
        $requested = isset( $server['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'] ) ? (string)$server['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'] : '';
        foreach ( explode( ',', $requested ) as $header )
        {
            $header = strtolower( trim( $header ) );
            if ( $header !== '' && !isset( $allowedHeaders[$header] ) )
                return $refused;
        }

        $headers = $this->originHeaders( $origin );
        $headers[] = 'Access-Control-Allow-Methods: ' . implode( ', ', array_keys( $this->methods ) );
        if ( !empty( $this->headers ) )
            $headers[] = 'Access-Control-Allow-Headers: ' . implode( ', ', $this->headers );
        if ( $this->maxAge > 0 )
            $headers[] = 'Access-Control-Max-Age: ' . $this->maxAge;
        $headers[] = 'Vary: Origin';

        return array( 'status' => 204, 'headers' => $headers );
    }

    /**
     * The headers for the response to an actual (not preflight) request: none while CORS is disabled, "Vary: Origin"
     * while it is enabled, and the Access-Control-* headers when the origin is allowed.
     *
     * @param array $server $_SERVER
     * @return array of "Name: value" lines
     */
    public function responseHeaders( array $server )
    {
        if ( !$this->enabled )
            return array();

        $origin = isset( $server['HTTP_ORIGIN'] ) ? $server['HTTP_ORIGIN'] : null;
        $headers = array();
        if ( $this->isOriginAllowed( $origin ) )
        {
            $headers = $this->originHeaders( $origin );
            if ( !empty( $this->exposedHeaders ) )
                $headers[] = 'Access-Control-Expose-Headers: ' . implode( ', ', $this->exposedHeaders );
        }
        $headers[] = 'Vary: Origin';
        return $headers;
    }

    /**
     * Sends the headers of an actual request, or answers a preflight, for the current request.
     *
     * @param array $server $_SERVER
     * @return bool true when a preflight was answered and the request must not run
     */
    public function handle( array $server )
    {
        if ( !$this->enabled )
            return false;

        $method = isset( $server['REQUEST_METHOD'] ) ? $server['REQUEST_METHOD'] : 'GET';
        if ( $this->isPreflight( $method, $server ) )
        {
            $answer = $this->preflight( $server );
            if ( !headers_sent() )
            {
                http_response_code( $answer['status'] );
                foreach ( $answer['headers'] as $header )
                    header( $header, false );
                header( 'Content-Length: 0' );
            }
            return true;
        }

        if ( !headers_sent() )
        {
            foreach ( $this->responseHeaders( $server ) as $header )
                header( $header, false );
        }
        return false;
    }

    /** @return array Access-Control-Allow-Origin and, when allowed, Access-Control-Allow-Credentials */
    protected function originHeaders( $origin )
    {
        // the origin as the browser sent it: it compares the header with its own origin byte for byte
        $headers = array( 'Access-Control-Allow-Origin: ' . ( ( $this->anyOrigin && !$this->credentials ) ? '*' : trim( (string)$origin ) ) );
        if ( $this->credentials )
            $headers[] = 'Access-Control-Allow-Credentials: true';
        return $headers;
    }

    /**
     * "scheme://host[:port]" in lower case, or null for anything else ("null", a path, a user name, control
     * characters, a scheme other than http or https).
     *
     * @param mixed $origin
     * @return string|null
     */
    public static function normalizeOrigin( $origin )
    {
        if ( !is_string( $origin ) )
            return null;
        $origin = trim( $origin );
        if ( !preg_match( '#^(https?)://([a-z0-9.\-]+|\[[0-9a-f:.]+\])(:[0-9]{1,5})?\z#i', $origin, $m ) )
            return null;
        return strtolower( $m[1] ) . '://' . strtolower( $m[2] ) . ( isset( $m[3] ) ? $m[3] : '' );
    }

    /** @return array the non-empty trimmed strings of a setting */
    protected static function stringList( $value )
    {
        $list = array();
        foreach ( (array)$value as $item )
        {
            if ( !is_string( $item ) && !is_int( $item ) )
                continue;
            // a header value must stay one line
            $item = trim( str_replace( array( "\r", "\n" ), '', (string)$item ) );
            if ( $item !== '' )
                $list[] = $item;
        }
        return array_values( array_unique( $list ) );
    }
}
