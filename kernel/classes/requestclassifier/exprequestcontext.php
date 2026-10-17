<?php
/**
 * File containing the expRequestContext class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What the request classifier looks at: one request, as it came in. Built once per request by fromGlobals(), or
 * from an array by create() (tests, a CLI replay of a log).
 *
 * The namespace path is the path below the siteaccess: with URI siteaccess matching, /admin/content/view/full/2
 * is /content/view/full/2 in the namespace of the siteaccess "admin", and /site/wp-login.php is /wp-login.php.
 * The rule sets evaluate the namespace path, so a probe looks the same under every siteaccess.
 *
 * @package kernel
 */
class expRequestContext
{
    /** @var string GET, POST ... (upper case) */
    public $method = 'GET';
    /** @var string The request URI as sent (path and query, still encoded) */
    public $uri = '/';
    /** @var string The path of the URI, still encoded */
    public $path = '/';
    /** @var string The query string, without the "?", still encoded */
    public $query = '';
    /** @var string The siteaccess name ('' when unknown) */
    public $siteaccess = '';
    /** @var string The path below the siteaccess (still encoded), see the class description */
    public $namespacePath = '/';
    /** @var string */
    public $userAgent = '';
    /** @var string The client address, raw: never written anywhere as it is */
    public $ip = '';
    /** @var int|null The HTTP status of the answer, null while unknown */
    public $status = null;
    /** @var string */
    public $referrer = '';

    /** @var array Decoded values, computed once */
    protected $decoded = array();

    /**
     * A context from an array: method, uri (or path and query), siteaccess, namespacePath, userAgent, ip, status,
     * referrer. Without namespacePath the first path element is taken off when it is the siteaccess name (URI
     * siteaccess matching), unless siteaccessInURI is false.
     *
     * @param array $values
     * @return expRequestContext
     */
    public static function create( array $values )
    {
        $c = new self();
        if ( isset( $values['method'] ) )
            $c->method = self::cleanMethod( $values['method'] );
        if ( isset( $values['uri'] ) )
        {
            $c->setURI( $values['uri'] );
        }
        else
        {
            $path = isset( $values['path'] ) ? (string)$values['path'] : '/';
            $query = isset( $values['query'] ) ? ltrim( (string)$values['query'], '?' ) : '';
            $c->setURI( $path . ( $query !== '' ? '?' . $query : '' ) );
        }
        foreach ( array( 'siteaccess', 'userAgent', 'ip', 'referrer' ) as $name )
        {
            if ( isset( $values[$name] ) )
                $c->$name = (string)$values[$name];
        }
        if ( isset( $values['status'] ) && $values['status'] !== '' )
            $c->status = (int)$values['status'];
        if ( isset( $values['namespacePath'] ) )
        {
            $c->namespacePath = (string)$values['namespacePath'];
        }
        else
        {
            $parts = array();
            if ( $c->siteaccess !== '' && ( !isset( $values['siteaccessInURI'] ) || $values['siteaccessInURI'] ) )
                $parts = isset( $values['uriPart'] ) ? (array)$values['uriPart'] : array( $c->siteaccess );
            $c->namespacePath = self::namespaceOf( $c->path, $parts );
        }
        return $c;
    }

    /**
     * The context of the current request.
     *
     * @param int|null $status the final HTTP status, when the caller knows it
     * @return expRequestContext
     */
    public static function fromGlobals( $status = null )
    {
        $c = new self();
        $server = $_SERVER;
        $c->method = self::cleanMethod( isset( $server['REQUEST_METHOD'] ) ? $server['REQUEST_METHOD'] : 'GET' );
        $c->setURI( isset( $server['REQUEST_URI'] ) ? (string)$server['REQUEST_URI'] : '/' );
        $c->userAgent = isset( $server['HTTP_USER_AGENT'] ) ? (string)$server['HTTP_USER_AGENT'] : '';
        $c->referrer = isset( $server['HTTP_REFERER'] ) ? (string)$server['HTTP_REFERER'] : '';
        $c->ip = class_exists( 'eZSys' ) ? (string)eZSys::clientIP()
               : ( isset( $server['REMOTE_ADDR'] ) ? (string)$server['REMOTE_ADDR'] : '' );
        if ( $status !== null )
        {
            $c->status = (int)$status;
        }
        else if ( PHP_SAPI !== 'cli' )
        {
            $code = http_response_code();
            if ( is_int( $code ) && $code > 0 )
                $c->status = $code;
        }

        // The siteaccess and, with URI matching (also host + URI), the path elements that chose it
        $parts = array();
        $access = isset( $GLOBALS['eZCurrentAccess'] ) && is_array( $GLOBALS['eZCurrentAccess'] ) ? $GLOBALS['eZCurrentAccess'] : array();
        if ( isset( $access['name'] ) )
            $c->siteaccess = (string)$access['name'];
        if ( isset( $access['uri_part'] ) && is_array( $access['uri_part'] ) && $access['uri_part'] )
        {
            $parts = $access['uri_part'];
        }
        else if ( isset( $access['type'] ) && class_exists( 'eZSiteAccess' ) && $c->siteaccess !== ''
                  && ( $access['type'] == eZSiteAccess::TYPE_URI || $access['type'] == eZSiteAccess::TYPE_HTTP_HOST_URI ) )
        {
            $parts = array( $c->siteaccess );
        }
        $c->namespacePath = self::namespaceOf( $c->path, $parts );
        return $c;
    }

    /**
     * The path below the siteaccess: /index.php in front is taken off, then the elements of $uriParts when the path
     * starts with them (in any case). A path that does not start with them is returned as it is.
     *
     * @param string $path still encoded
     * @param array $uriParts
     * @return string
     */
    public static function namespaceOf( $path, array $uriParts )
    {
        $path = '/' . ltrim( (string)$path, '/' );
        if ( strncasecmp( $path, '/index.php', 10 ) === 0 && ( strlen( $path ) === 10 || $path[10] === '/' ) )
            $path = '/' . ltrim( substr( $path, 10 ), '/' );
        if ( !$uriParts )
            return $path;
        $prefix = '';
        foreach ( $uriParts as $part )
        {
            $part = trim( (string)$part, '/' );
            if ( $part !== '' )
                $prefix .= '/' . $part;
        }
        if ( $prefix === '' )
            return $path;
        $length = strlen( $prefix );
        if ( strncasecmp( $path, $prefix, $length ) === 0 && ( strlen( $path ) === $length || $path[$length] === '/' ) )
            return '/' . ltrim( substr( $path, $length ), '/' );
        return $path;
    }

    /**
     * Sets uri, path and query from a request URI (an absolute URL is reduced to its path and query).
     *
     * @param string $uri
     * @return void
     */
    public function setURI( $uri )
    {
        $uri = (string)$uri;
        if ( preg_match( '#^[a-z][a-z0-9+.-]*://[^/?\#]*#i', $uri, $m ) )
            $uri = (string)substr( $uri, strlen( $m[0] ) );
        $hash = strpos( $uri, '#' );
        if ( $hash !== false )
            $uri = substr( $uri, 0, $hash );
        if ( $uri === '' || $uri[0] !== '/' )
            $uri = '/' . $uri;
        $this->uri = $uri;
        $q = strpos( $uri, '?' );
        $this->path = $q === false ? $uri : substr( $uri, 0, $q );
        $this->query = $q === false ? '' : (string)substr( $uri, $q + 1 );
        $this->namespacePath = $this->path;
        $this->decoded = array();
    }

    /**
     * The path, URL-decoded once (%2F stays a slash, + stays a plus).
     *
     * @return string
     */
    public function decodedPath()
    {
        if ( !isset( $this->decoded['path'] ) )
            $this->decoded['path'] = rawurldecode( $this->path );
        return $this->decoded['path'];
    }

    /**
     * The namespace path, URL-decoded once.
     *
     * @return string
     */
    public function decodedNamespacePath()
    {
        if ( !isset( $this->decoded['namespace'] ) )
            $this->decoded['namespace'] = rawurldecode( $this->namespacePath );
        return $this->decoded['namespace'];
    }

    /**
     * The query string, URL-decoded once as a form would be (+ is a space).
     *
     * @return string
     */
    public function decodedQuery()
    {
        if ( !isset( $this->decoded['query'] ) )
            $this->decoded['query'] = $this->query === '' ? '' : urldecode( $this->query );
        return $this->decoded['query'];
    }

    /**
     * @param mixed $method
     * @return string
     */
    protected static function cleanMethod( $method )
    {
        $method = strtoupper( (string)$method );
        return preg_match( '/^[A-Z]{1,12}$/', $method ) ? $method : 'GET';
    }
}
