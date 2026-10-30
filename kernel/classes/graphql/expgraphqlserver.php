<?php
/**
 * Runs a GraphQL request (query, variables, operationName) against the schema of the current siteaccess, as the
 * user of the context, and gives the answer as an array ({ data, errors, extensions }). The same code serves the
 * end point /api/graphql, the session end point graphql/query and the console (exp:graphql:query).
 *
 * Before it runs, a query is checked against the limits of graphql.ini (MaxDepth, MaxComplexity) and, for a
 * request that may not read the schema (Introspection), refused when it asks for it. Every error gets
 * extensions.code (expGraphQLError's constants) and extensions.requestId; an error that is not meant for the
 * client is "Internal server error" unless Debug=enabled.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLServer
{
    /** The longest query accepted, in bytes, when graphql.ini MaxQueryLength does not say. */
    const MAX_QUERY_BYTES = 100000;

    /** @var expGraphQLSchemaBuilder */
    protected $builder;

    /** @var expGraphQLResponseCache|null the response cache (null: the installation's) */
    protected $responseCache = null;

    /** @var expGraphQLPersistedQueries|null the persisted queries (null: the installation's) */
    protected $persistedStore = null;

    /**
     * Uses another response cache and store of persisted queries than the installation's (tests).
     *
     * @param expGraphQLResponseCache|null $cache
     * @param expGraphQLPersistedQueries|null $store
     */
    public function useStorage( $cache, $store )
    {
        $this->responseCache = $cache;
        $this->persistedStore = $store;
    }

    /**
     * @param expGraphQLSchemaBuilder $builder
     */
    public function __construct( expGraphQLSchemaBuilder $builder )
    {
        $this->builder = $builder;
    }

    /** @return expGraphQLSchemaBuilder */
    public function builder()
    {
        return $this->builder;
    }

    /**
     * The request of an HTTP call: GET (query, variables, operationName in the query string), POST with a JSON body
     * (application/json), a POST of the query alone (application/graphql), or a form POST.
     *
     * @param string $method GET or POST
     * @param string $contentType
     * @param string $body
     * @param array $get
     * @param array $post
     * @return array query, variables, operationName; or error (a message) when the request cannot be read
     */
    public static function parseHttpRequest( $method, $contentType, $body, array $get, array $post )
    {
        $method = strtoupper( (string)$method );
        $contentType = strtolower( trim( explode( ';', (string)$contentType )[0] ) );
        if ( $method === 'GET' )
            $input = $get;
        else if ( $method !== 'POST' )
            return array( 'error' => expGraphQLMessage::tr( 'Use GET or POST.' ) );
        else if ( $contentType === 'application/graphql' )
            $input = array( 'query' => (string)$body );
        else if ( $contentType === 'application/json' || ( $contentType === '' && $body !== '' && $body[0] === '{' ) )
        {
            $input = json_decode( (string)$body, true );
            if ( !is_array( $input ) )
                return array( 'error' => expGraphQLMessage::tr( 'The body is not a JSON object.' ) );
        }
        else
            $input = $post;
        $variables = isset( $input['variables'] ) ? $input['variables'] : null;
        if ( is_string( $variables ) && $variables !== '' )
        {
            $variables = json_decode( $variables, true );
            if ( !is_array( $variables ) )
                return array( 'error' => expGraphQLMessage::tr( 'variables is not a JSON object.' ) );
        }
        // A persisted query: the id of the request (?id=articles), or Apollo's extensions.persistedQuery.sha256Hash
        $extensions = isset( $input['extensions'] ) ? $input['extensions'] : null;
        if ( is_string( $extensions ) && $extensions !== '' )
        {
            $extensions = json_decode( $extensions, true );
            if ( !is_array( $extensions ) )
                return array( 'error' => expGraphQLMessage::tr( 'extensions is not a JSON object.' ) );
        }
        $hash = is_array( $extensions ) && isset( $extensions['persistedQuery']['sha256Hash'] ) && is_string( $extensions['persistedQuery']['sha256Hash'] )
            ? strtolower( $extensions['persistedQuery']['sha256Hash'] ) : null;
        return array( 'query' => isset( $input['query'] ) && is_string( $input['query'] ) ? $input['query'] : '',
                      'variables' => is_array( $variables ) ? $variables : null,
                      'operationName' => isset( $input['operationName'] ) && is_string( $input['operationName'] ) && $input['operationName'] !== '' ? $input['operationName'] : null,
                      'id' => isset( $input['id'] ) && is_string( $input['id'] ) && $input['id'] !== '' ? $input['id'] : null,
                      'persistedHash' => $hash,
                      'method' => $method );
    }

    /**
     * Runs a request of parseHttpRequest(): a persisted query is looked up (or stored, for an automatic persisted
     * query a client may store), PersistedQueriesOnly is applied, then the query runs.
     *
     * @param array $request query, variables, operationName, id, persistedHash
     * @param expGraphQLContext $context
     * @return array answer (the GraphQL answer), persisted (the hash of a persisted query, or null)
     */
    public function run( array $request, expGraphQLContext $context )
    {
        $request += array( 'query' => '', 'variables' => null, 'operationName' => null, 'id' => null, 'persistedHash' => null );
        $resolved = self::resolvePersisted( $request, $context, $this->persistedStore );
        if ( isset( $resolved['error'] ) )
        {
            return array( 'answer' => array( 'errors' => array( self::error( $resolved['error'][0], $resolved['error'][1], $context ) ),
                                              'extensions' => array( 'requestId' => $context->requestId, 'schemaHash' => $this->builder->schemaHash() ) ),
                          'persisted' => null );
        }
        return array( 'answer' => $this->execute( $resolved['query'], $request['variables'], $request['operationName'], $context ),
                      'persisted' => $resolved['hash'] );
    }

    /**
     * The HTTP answer of a request (the end point and the session end point): run() with the response cache for
     * persisted queries (graphql.ini ResponseCache), an ETag on every answer and 304 for an If-None-Match that
     * matches it.
     *
     * @param array $request of parseHttpRequest()
     * @param expGraphQLContext $context
     * @param array $server $_SERVER (the raw request: the key of an anonymous GET, If-None-Match)
     * @return array status (200 or 304), body (JSON), headers
     */
    public function respond( array $request, expGraphQLContext $context, array $server )
    {
        $request += array( 'query' => '', 'variables' => null, 'operationName' => null, 'id' => null, 'persistedHash' => null );
        $headers = array( 'X-GraphQL-Schema' => $this->builder->schemaHash(), 'X-Request-Id' => $context->requestId );
        $resolved = self::resolvePersisted( $request, $context, $this->persistedStore );
        $cache = null;
        $key = null;
        $anonymous = !$context->isSignedIn();
        if ( !isset( $resolved['error'] ) && $resolved['hash'] !== null && expGraphQL::setting( 'ResponseCache', 'enabled' ) === 'enabled'
             && expGraphQL::setting( 'Debug', 'disabled' ) !== 'enabled' )
        {
            $cache = $this->responseCache !== null ? $this->responseCache : expGraphQLResponseCache::forInstallation();
            if ( $anonymous )
                $key = expGraphQLResponseCache::anonymousKey( $server );
            else
            {
                // a GET with a token or a key: kept under the raw request and the rights, and the early answer of
                // index_rest.php learns the rights of these credentials for CredentialCacheTtl seconds
                $key = expGraphQLResponseCache::signedInKey( $server, $context->roleHash() );
                $credential = expGraphQLResponseCache::credential( $server );
                if ( $key !== null && $credential !== null && $cache->readCredential( $credential ) === null
                     && ( $context->authMode === expGraphQLContext::AUTH_TOKEN || $context->authMode === expGraphQLContext::AUTH_API_KEY ) )
                {
                    $cache->writeCredential( $credential, array( 'roleHash' => $context->roleHash(),
                        'identity' => $context->apiKeyId ? 'key:' . (int)$context->apiKeyId : 'user:' . ( $context->user instanceof eZUser ? (int)$context->user->id() : 0 ),
                        'rateLimit' => (int)expGraphQL::setting( 'RateLimit', 600 ), 'rateWindow' => expGraphQL::intSetting( 'RateLimitWindow', 60 ),
                        'ttl' => (int)expGraphQL::setting( 'CredentialCacheTtl', 60 ) ) );
                }
            }
            if ( $key === null )
            {
                $key = expGraphQLResponseCache::userKey( array( expGraphQL::currentSiteAccess(), $this->builder->schemaHash(),
                    $context->roleHash(), $resolved['hash'],
                    $request['variables'], $request['operationName'], $context->baseUrl ) );
            }
            $entry = $cache->read( $key );
            if ( $entry !== null )
                return self::httpAnswer( $entry['body'], (string)$entry['etag'], $headers + expGraphQLResponseCache::headers( $entry, 'HIT', $anonymous ), $server );
        }
        if ( isset( $resolved['error'] ) )
            $answer = array( 'errors' => array( self::error( $resolved['error'][0], $resolved['error'][1], $context ) ),
                             'extensions' => array( 'requestId' => $context->requestId, 'schemaHash' => $this->builder->schemaHash() ) );
        else
            $answer = $this->execute( $resolved['query'], $request['variables'], $request['operationName'], $context );
        if ( $cache !== null && expGraphQLResponseCache::storable( $answer, $resolved['query'], !$anonymous ) )
        {
            // the same body for every request it answers: the request id is in the header X-Request-Id only
            unset( $answer['extensions']['requestId'] );
            $body = expGraphQLRestResponse::encode( $answer );
            $etag = expGraphQLResponseCache::etag( $body );
            $cache->write( $key, $body, array( 'etag' => $etag, 'schemaHash' => $this->builder->schemaHash(),
                                               'ttl' => (int)expGraphQL::setting( 'ResponseCacheTtl', 86400 ),
                                               'rateLimit' => $anonymous ? (int)expGraphQL::setting( 'RateLimitAnonymous', 120 ) : 0,
                                               'rateWindow' => expGraphQL::intSetting( 'RateLimitWindow', 60 ),
                                               'cors' => self::corsHeaders( $server ) ) );
            return self::httpAnswer( $body, $etag, $headers + expGraphQLResponseCache::headers( array( 'etag' => $etag, 'schemaHash' => $this->builder->schemaHash() ),
                                                                                                   'MISS', $anonymous ), $server );
        }
        $body = expGraphQLRestResponse::encode( $answer );
        // a weak ETag: the same answer but for its request id
        $withoutId = $answer;
        unset( $withoutId['extensions']['requestId'] );
        $etag = 'W/' . expGraphQLResponseCache::etag( expGraphQLRestResponse::encode( $withoutId ) );
        $headers += array( 'Content-Type' => 'application/json; charset=UTF-8', 'ETag' => $etag, 'Cache-Control' => 'private, no-store' );
        if ( $cache !== null )
            $headers['X-GraphQL-Cache'] = 'BYPASS';
        return self::httpAnswer( $body, $etag, $headers, $server );
    }

    /**
     * @param array $server
     * @return string[] the Access-Control headers of a CORS request (rest.ini [CorsSettings]), kept with its answer for
     *                  the early answer of index_rest.php
     */
    protected static function corsHeaders( array $server )
    {
        if ( empty( $server['HTTP_ORIGIN'] ) || !class_exists( 'expRestCors' ) )
            return array();
        return array_values( (array)expRestCors::fromIni( eZINI::instance( 'rest.ini' ) )->responseHeaders( $server ) );
    }

    /**
     * @param string $body
     * @param string $etag
     * @param array $headers
     * @param array $server
     * @return array status, body, headers
     */
    protected static function httpAnswer( $body, $etag, array $headers, array $server )
    {
        if ( isset( $server['HTTP_IF_NONE_MATCH'] ) && expGraphQLResponseCache::etagMatches( (string)$server['HTTP_IF_NONE_MATCH'], $etag ) )
            return array( 'status' => 304, 'body' => '', 'headers' => $headers );
        return array( 'status' => 200, 'body' => $body, 'headers' => $headers );
    }

    /**
     * The query text of a request and the hash it is persisted under.
     *
     * @param array $request
     * @param expGraphQLContext $context
     * @param expGraphQLPersistedQueries|null $store null: the store of the installation
     * @return array query and hash (null: not persisted), or error (array( message, code ))
     */
    public static function resolvePersisted( array $request, expGraphQLContext $context, $store = null )
    {
        $query = isset( $request['query'] ) ? (string)$request['query'] : '';
        $id = isset( $request['id'] ) ? $request['id'] : null;
        $hash = isset( $request['persistedHash'] ) ? $request['persistedHash'] : null;
        if ( $id === null && $hash === null )
        {
            if ( self::persistedOnly( $context ) )
                return array( 'error' => array( expGraphQLMessage::tr( 'Only persisted queries are accepted here: send the id of a stored query.' ), expGraphQLError::PERSISTED_QUERY_REQUIRED ) );
            return array( 'query' => $query, 'hash' => null );
        }
        if ( expGraphQL::setting( 'PersistedQueries', 'enabled' ) !== 'enabled' )
            return array( 'error' => array( 'PersistedQueryNotSupported', expGraphQLError::PERSISTED_QUERY_NOT_SUPPORTED ) );
        $store = $store instanceof expGraphQLPersistedQueries ? $store : expGraphQLPersistedQueries::fromSettings();
        if ( $id !== null )
        {
            $found = $store->find( $id );
            if ( $found === null )
                return array( 'error' => array( 'PersistedQueryNotFound', expGraphQLError::PERSISTED_QUERY_NOT_FOUND ) );
            return array( 'query' => $found, 'hash' => expGraphQLPersistedQueries::hash( $found ) );
        }
        if ( !expGraphQLPersistedQueries::isHash( $hash ) )
            return array( 'error' => array( expGraphQLMessage::tr( 'The persisted query hash is not a SHA-256.' ), expGraphQLError::BAD_USER_INPUT ) );
        if ( trim( $query ) === '' )
        {
            $found = $store->find( $hash );
            if ( $found === null )
                return array( 'error' => array( 'PersistedQueryNotFound', expGraphQLError::PERSISTED_QUERY_NOT_FOUND ) );
            return array( 'query' => $found, 'hash' => $hash );
        }
        // An automatic persisted query: the text with its hash, stored when the client may store queries
        if ( expGraphQLPersistedQueries::hash( $query ) !== $hash )
            return array( 'error' => array( expGraphQLMessage::tr( 'The persisted query hash does not match the query.' ), expGraphQLError::BAD_USER_INPUT ) );
        if ( $store->find( $hash ) === null )
        {
            if ( !self::mayRegister( $context ) || $store->count() >= expGraphQL::intSetting( 'MaxPersistedQueries', 1000 ) )
            {
                // not stored: the query still runs, but stays ad hoc
                if ( self::persistedOnly( $context ) )
                    return array( 'error' => array( expGraphQLMessage::tr( 'Only persisted queries are accepted here: send the id of a stored query.' ), expGraphQLError::PERSISTED_QUERY_REQUIRED ) );
                return array( 'query' => $query, 'hash' => null );
            }
            $store->store( $query );
        }
        return array( 'query' => $query, 'hash' => $hash );
    }

    /**
     * @param expGraphQLContext $context
     * @return bool the request may only run persisted queries (PersistedQueriesOnly); the console and the session of
     *              the administration (the playground) always may run any query
     */
    public static function persistedOnly( expGraphQLContext $context )
    {
        if ( $context->authMode === expGraphQLContext::AUTH_CONSOLE || $context->authMode === expGraphQLContext::AUTH_SESSION )
            return false;
        $setting = (string)expGraphQL::setting( 'PersistedQueriesOnly', 'disabled' );
        if ( $setting === 'enabled' )
            return true;
        return $setting === 'anonymous' && !$context->isSignedIn();
    }

    /**
     * @param expGraphQLContext $context
     * @return bool the request may store an automatic persisted query (AutomaticPersistedQueries)
     */
    public static function mayRegister( expGraphQLContext $context )
    {
        $setting = (string)expGraphQL::setting( 'AutomaticPersistedQueries', 'signed-in' );
        if ( $setting === 'enabled' )
            return true;
        if ( $setting === 'disabled' )
            return $context->authMode === expGraphQLContext::AUTH_CONSOLE;
        return $context->isSignedIn();
    }

    /**
     * @param string $query
     * @param array|null $variables
     * @param string|null $operationName
     * @param expGraphQLContext $context
     * @return array the answer: data and/or errors, and extensions (requestId, schemaHash)
     */
    public function execute( $query, $variables, $operationName, expGraphQLContext $context )
    {
        $extensions = array( 'requestId' => $context->requestId, 'schemaHash' => $this->builder->schemaHash() );
        $query = (string)$query;
        if ( trim( $query ) === '' )
            return array( 'errors' => array( self::error( expGraphQLMessage::tr( 'The request has no query.' ), expGraphQLError::BAD_USER_INPUT, $context ) ), 'extensions' => $extensions );
        if ( strlen( $query ) > expGraphQL::intSetting( 'MaxQueryLength', self::MAX_QUERY_BYTES ) )
            return array( 'errors' => array( self::error( expGraphQLMessage::tr( 'The query is longer than %max bytes.', array( '%max' => expGraphQL::intSetting( 'MaxQueryLength', self::MAX_QUERY_BYTES ) ) ), expGraphQLError::BAD_USER_INPUT, $context ) ),
                          'extensions' => $extensions );

        $debug = expGraphQL::setting( 'Debug', 'disabled' ) === 'enabled';
        // The rules of the specification, with the limits of graphql.ini in place of the library's (switched off) ones
        $rules = \GraphQL\Validator\DocumentValidator::allRules();
        $rules[\GraphQL\Validator\Rules\QueryDepth::class] = new \GraphQL\Validator\Rules\QueryDepth( expGraphQL::intSetting( 'MaxDepth', 12 ) );
        $rules[\GraphQL\Validator\Rules\QueryComplexity::class] = new \GraphQL\Validator\Rules\QueryComplexity( expGraphQL::intSetting( 'MaxComplexity', 1000 ) );
        $rules[\GraphQL\Validator\Rules\DisableIntrospection::class] = new \GraphQL\Validator\Rules\DisableIntrospection(
            $context->allowsIntrospection() ? \GraphQL\Validator\Rules\DisableIntrospection::DISABLED : \GraphQL\Validator\Rules\DisableIntrospection::ENABLED );

        try
        {
            $result = \GraphQL\GraphQL::executeQuery( $this->builder->schema(), $query, null, $context, $variables, $operationName, null, $rules );
        }
        catch ( Exception $e )
        {
            eZDebug::writeError( get_class( $e ) . ': ' . $e->getMessage(), __METHOD__ );
            return array( 'errors' => array( self::error( $debug ? $e->getMessage() : expGraphQLMessage::tr( 'Internal server error' ), expGraphQLError::INTERNAL, $context ) ),
                          'extensions' => $extensions );
        }
        $result->setErrorFormatter( function ( \GraphQL\Error\Error $error ) use ( $context, $debug ) {
            return expGraphQLServer::formatError( $error, $context, $debug );
        } );
        $answer = $result->toArray();
        $answer['extensions'] = $extensions;
        return $answer;
    }

    /**
     * @param \GraphQL\Error\Error $error
     * @param expGraphQLContext $context
     * @param bool $debug
     * @return array message, locations, path, extensions (code, requestId; debug with Debug=enabled)
     */
    public static function formatError( \GraphQL\Error\Error $error, expGraphQLContext $context, $debug )
    {
        $code = self::codeOf( $error );
        $previous = $error->getPrevious();
        if ( $code === expGraphQLError::INTERNAL && $previous !== null )
            eZDebug::writeError( get_class( $previous ) . ': ' . $previous->getMessage() . ' in ' . $previous->getFile() . ':' . $previous->getLine(), __METHOD__ );
        $formatted = \GraphQL\Error\FormattedError::createFromException( $error,
            $debug ? \GraphQL\Error\DebugFlag::INCLUDE_DEBUG_MESSAGE | \GraphQL\Error\DebugFlag::INCLUDE_TRACE : \GraphQL\Error\DebugFlag::NONE );
        $extensions = isset( $formatted['extensions'] ) && is_array( $formatted['extensions'] ) ? $formatted['extensions'] : array();
        unset( $formatted['extensions'] );
        $debugInfo = array();
        foreach ( array( 'debugMessage', 'trace', 'file', 'line' ) as $key )
        {
            if ( isset( $extensions[$key] ) )
                $debugInfo[$key] = $extensions[$key];
            if ( isset( $formatted[$key] ) )
            {
                $debugInfo[$key] = $formatted[$key];
                unset( $formatted[$key] );
            }
            unset( $extensions[$key] );
        }
        // A resolver's own error is meant for the client: its message, not "Internal server error"
        if ( $previous instanceof expGraphQLError )
            $formatted['message'] = $previous->getMessage();
        else
            $formatted['message'] = self::translatedMessage( $code, (string)$formatted['message'] );
        $formatted['extensions'] = array( 'code' => $code, 'requestId' => $context->requestId ) + $extensions;
        if ( $debug && $debugInfo )
            $formatted['extensions']['debug'] = $debugInfo;
        return $formatted;
    }

    /**
     * The message of an error of the library in the language of the request, where the kernel has its own words for
     * it (the limits, introspection, an internal error); the messages of the specification's validation stay as the
     * library writes them.
     *
     * @param string $code
     * @param string $message
     * @return string
     */
    public static function translatedMessage( $code, $message )
    {
        if ( $code === expGraphQLError::DEPTH_LIMIT && preg_match( '/should be (\d+) but got (\d+)/', $message, $m ) )
            return expGraphQLMessage::tr( 'The query is %depth levels deep; at most %max are allowed.', array( '%depth' => $m[2], '%max' => $m[1] ) );
        if ( $code === expGraphQLError::COMPLEXITY_LIMIT && preg_match( '/should be (\d+) but got (\d+)/', $message, $m ) )
            return expGraphQLMessage::tr( 'The query has a complexity of %complexity; at most %max is allowed.', array( '%complexity' => $m[2], '%max' => $m[1] ) );
        if ( $code === expGraphQLError::INTROSPECTION_DISABLED )
            return expGraphQLMessage::tr( 'This request may not read the schema (introspection).' );
        if ( $code === expGraphQLError::INTERNAL && $message === 'Internal server error' )
            return expGraphQLMessage::tr( 'Internal server error' );
        return $message;
    }

    /**
     * @param \GraphQL\Error\Error $error
     * @return string the extensions.code of an error
     */
    public static function codeOf( \GraphQL\Error\Error $error )
    {
        $previous = $error->getPrevious();
        if ( $previous instanceof expGraphQLError )
            return $previous->errorCode();
        if ( $error instanceof \GraphQL\Error\SyntaxError )
            return expGraphQLError::PARSE;
        if ( $previous !== null && !( $previous instanceof \GraphQL\Error\ClientAware && $previous->isClientSafe() ) )
            return expGraphQLError::INTERNAL;
        $message = $error->getMessage();
        if ( strpos( $message, 'Max query depth should be' ) === 0 )
            return expGraphQLError::DEPTH_LIMIT;
        if ( strpos( $message, 'Max query complexity should be' ) === 0 )
            return expGraphQLError::COMPLEXITY_LIMIT;
        if ( strpos( $message, 'GraphQL introspection is not allowed' ) === 0 )
            return expGraphQLError::INTROSPECTION_DISABLED;
        if ( $error->getPath() === null )
            return expGraphQLError::VALIDATION;
        return expGraphQLError::BAD_USER_INPUT;
    }

    /**
     * An error of the request itself, before a query runs.
     *
     * @param string $message
     * @param string $code
     * @param expGraphQLContext|null $context
     * @return array
     */
    public static function error( $message, $code, $context = null )
    {
        $extensions = array( 'code' => (string)$code );
        if ( $context instanceof expGraphQLContext )
            $extensions['requestId'] = $context->requestId;
        return array( 'message' => (string)$message, 'extensions' => $extensions );
    }

    /**
     * The answer of a disabled end point, or of a missing library.
     *
     * @param string $message
     * @param string $code
     * @return array
     */
    public static function refusal( $message, $code )
    {
        return array( 'errors' => array( self::error( $message, $code ) ) );
    }
}

?>
