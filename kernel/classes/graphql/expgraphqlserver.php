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
    /** The longest query accepted, in bytes. */
    const MAX_QUERY_BYTES = 100000;

    /** @var expGraphQLSchemaBuilder */
    protected $builder;

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
            return array( 'error' => 'Use GET or POST.' );
        else if ( $contentType === 'application/graphql' )
            $input = array( 'query' => (string)$body );
        else if ( $contentType === 'application/json' || ( $contentType === '' && $body !== '' && $body[0] === '{' ) )
        {
            $input = json_decode( (string)$body, true );
            if ( !is_array( $input ) )
                return array( 'error' => 'The body is not a JSON object.' );
        }
        else
            $input = $post;
        $variables = isset( $input['variables'] ) ? $input['variables'] : null;
        if ( is_string( $variables ) && $variables !== '' )
        {
            $variables = json_decode( $variables, true );
            if ( !is_array( $variables ) )
                return array( 'error' => 'variables is not a JSON object.' );
        }
        return array( 'query' => isset( $input['query'] ) && is_string( $input['query'] ) ? $input['query'] : '',
                      'variables' => is_array( $variables ) ? $variables : null,
                      'operationName' => isset( $input['operationName'] ) && is_string( $input['operationName'] ) && $input['operationName'] !== '' ? $input['operationName'] : null );
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
            return array( 'errors' => array( self::error( 'The request has no query.', expGraphQLError::BAD_USER_INPUT, $context ) ), 'extensions' => $extensions );
        if ( strlen( $query ) > self::MAX_QUERY_BYTES )
            return array( 'errors' => array( self::error( 'The query is longer than ' . self::MAX_QUERY_BYTES . ' bytes.', expGraphQLError::BAD_USER_INPUT, $context ) ),
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
            return array( 'errors' => array( self::error( $debug ? $e->getMessage() : 'Internal server error', expGraphQLError::INTERNAL, $context ) ),
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
        $formatted['extensions'] = array( 'code' => $code, 'requestId' => $context->requestId ) + $extensions;
        if ( $debug && $debugInfo )
            $formatted['extensions']['debug'] = $debugInfo;
        return $formatted;
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
