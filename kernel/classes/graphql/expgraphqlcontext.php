<?php
/**
 * One GraphQL request: who asks (the user, and how the request was signed in), where (the siteaccess and the
 * address the URLs of the answer start with), the content it reads (the repository) and an id for the request.
 * Made for each request and never kept: a persistent worker (Velocity) starts every request with a new one.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLContext
{
    /** How a request was signed in: none (the anonymous user), an OAuth token, a personal API key, a session
     * cookie of the web interface, or the console (exp:graphql:query). */
    const AUTH_ANONYMOUS = 'anonymous';
    const AUTH_TOKEN = 'token';
    const AUTH_API_KEY = 'apikey';
    const AUTH_SESSION = 'session';
    const AUTH_CONSOLE = 'console';

    /** @var eZUser|null */
    public $user;

    /** @var string one of the AUTH_* constants */
    public $authMode;

    /** @var expGraphQLRepository */
    public $repository;

    /** @var string scheme and host the URLs of the answer start with ('' for addresses from the root) */
    public $baseUrl;

    /** @var string */
    public $requestId;

    /**
     * @param eZUser|null $user
     * @param string $authMode
     * @param expGraphQLRepository $repository
     * @param string $baseUrl
     */
    public function __construct( $user, $authMode, expGraphQLRepository $repository, $baseUrl = '' )
    {
        $this->user = $user;
        $this->authMode = $user instanceof eZUser && $user->isAnonymous() && $authMode !== self::AUTH_CONSOLE ? self::AUTH_ANONYMOUS : (string)$authMode;
        $this->repository = $repository;
        $this->baseUrl = rtrim( (string)$baseUrl, '/' );
        $this->requestId = self::newRequestId();
    }

    /** @var int|null the id of the personal API key the request was signed in with */
    public $apiKeyId = null;

    /** @var string|null the rights of the user as a hash (expGraphQLResponseCache::roleHash()), made when first asked for */
    public $roleHash = null;

    /**
     * @return string the rights of the user as a hash: what the answers of persisted queries are kept by
     */
    public function roleHash()
    {
        if ( $this->roleHash === null )
            $this->roleHash = $this->user instanceof eZUser ? expGraphQLResponseCache::roleHash( $this->user ) : '';
        return $this->roleHash;
    }

    /** @var expGraphQLBatchLoader[] kind => loader, made when first asked for */
    protected $loaders = array();

    /**
     * The batch loader of this request for a kind of value (expGraphQLBatchLoader): the keys a level of the query
     * asks for are fetched together, and a key once fetched is answered from memory.
     *
     *   names            object id => the object's name (owners)
     *   content          object id => expGraphQLContent at its main location (relations)
     *   node             node id => expGraphQLContent at that location (parents)
     *
     * @param string $kind
     * @param string|false $language the translation content is read in (content, node)
     * @return expGraphQLBatchLoader
     */
    public function loader( $kind, $language = false )
    {
        $key = $kind . '|' . (string)$language;
        if ( !isset( $this->loaders[$key] ) )
        {
            $repository = $this->repository;
            switch ( $kind )
            {
                case 'names':
                    $batch = function ( array $ids ) use ( $repository ) { return $repository->objectNames( $ids ); };
                    break;
                case 'content':
                    $batch = function ( array $ids ) use ( $repository, $language ) { return $repository->relatedList( $ids, $language ); };
                    break;
                case 'node':
                    $batch = function ( array $ids ) use ( $repository, $language ) { return $repository->contentByNodeIdList( $ids, $language ); };
                    break;
                default:
                    throw new InvalidArgumentException( "No GraphQL loader $kind" );
            }
            $this->loaders[$key] = new expGraphQLBatchLoader( $batch );
        }
        return $this->loaders[$key];
    }

    /**
     * @return array kind => array( batches, keys ): what the loaders of this request fetched
     */
    public function loaderStats()
    {
        $stats = array();
        foreach ( $this->loaders as $key => $loader )
            $stats[$key] = array( 'batches' => $loader->batches, 'keys' => $loader->keys );
        return $stats;
    }

    /**
     * @return bool the request was signed in (a token, a key, a session or the console), not anonymous
     */
    public function isSignedIn()
    {
        return $this->authMode !== self::AUTH_ANONYMOUS;
    }

    /**
     * @return bool introspection is allowed for this request (graphql.ini [GraphQLSettings] Introspection)
     */
    public function allowsIntrospection()
    {
        $setting = (string)expGraphQL::setting( 'Introspection', 'signed-in' );
        if ( $setting === 'enabled' )
            return true;
        if ( $setting === 'disabled' )
            return $this->authMode === self::AUTH_CONSOLE;
        return $this->isSignedIn();
    }

    /**
     * @param string $path an address from the root of the site, or a path of the installation (var/...)
     * @return string the address with the base URL in front
     */
    public function absoluteUrl( $path )
    {
        $path = (string)$path;
        if ( $path === '' || preg_match( '#^[a-z][a-z0-9+.-]*://#i', $path ) )
            return $path;
        return $this->baseUrl . '/' . ltrim( $path, '/' );
    }

    /**
     * The address a site URL (a URL alias, a module view) gets in the current siteaccess (eZURI::transformURI()).
     *
     * @param string $url about/company, content/download/1/2
     * @return string
     */
    public function siteUrl( $url )
    {
        $url = '/' . ltrim( (string)$url, '/' );
        eZURI::transformURI( $url, false, 'relative' );
        return $this->absoluteUrl( $url );
    }

    /**
     * @return string the scheme and host of the current HTTP request, '' on the console
     */
    public static function requestBaseUrl()
    {
        if ( PHP_SAPI === 'cli' && !isset( $_SERVER['HTTP_HOST'] ) )
            return '';
        return rtrim( (string)eZSys::serverURL(), '/' );
    }

    /**
     * @return string 16 hexadecimal digits
     */
    public static function newRequestId()
    {
        try
        {
            return bin2hex( random_bytes( 8 ) );
        }
        catch ( Exception $e )
        {
            return substr( sha1( uniqid( '', true ) ), 0, 16 );
        }
    }
}

?>
