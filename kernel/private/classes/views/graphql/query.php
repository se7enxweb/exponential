<?php
/**
 * File containing the graphql/query view: the session end point of the GraphQL API. A POST (JSON body
 * { query, variables, operationName }, or a form) runs the query as the signed-in user of the session and answers
 * the JSON of the query, as /api/graphql does for a token. The playground of Setup > GraphQL posts to it; a page of
 * the site may too (the policy graphql/query).
 *
 *   200  the answer of the query     404  the API is switched off here (graphql.ini)
 *   400  a request that cannot be read, or a GET (only POST: a query of a session is never in an address)
 *   503  the library is missing
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\GraphQL
{

class Query extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the messages in the language the browser asks for (every request anew: a persistent worker keeps statics)
        \expGraphQLMessage::useLanguage( (string)\eZSys::serverVariable( 'HTTP_ACCEPT_LANGUAGE', true ) );
        if ( !\expGraphQL::isEnabled() )
            self::send( 404, \expGraphQLServer::refusal( \expGraphQLMessage::tr( 'The GraphQL API is switched off in this siteaccess.' ), \expGraphQLError::DISABLED ) );
        if ( !\expGraphQL::loadLibrary() )
            self::send( 503, \expGraphQLServer::refusal( \expGraphQLMessage::tr( 'The GraphQL library is not installed.' ), \expGraphQLError::INTERNAL ) );
        $method = strtoupper( (string)\eZSys::serverVariable( 'REQUEST_METHOD', true ) );
        if ( $method !== 'POST' )
            self::send( 400, \expGraphQLServer::refusal( \expGraphQLMessage::tr( 'Send the query with POST.' ), \expGraphQLError::BAD_USER_INPUT ) );

        $request = \expGraphQLServer::parseHttpRequest( 'POST', (string)\eZSys::serverVariable( 'CONTENT_TYPE', true ),
                                                        (string)file_get_contents( 'php://input' ), array(), $_POST );
        if ( isset( $request['error'] ) )
            self::send( 400, \expGraphQLServer::refusal( $request['error'], \expGraphQLError::BAD_USER_INPUT ) );

        $user = \eZUser::currentUser();
        $hit = \expGraphQLRateLimiter::hitRequest( \expGraphQLContext::AUTH_SESSION, $user, null );
        if ( $hit !== null )
        {
            foreach ( \expGraphQLRateLimiter::headers( $hit ) as $name => $value )
                header( "$name: $value" );
            if ( !$hit['allowed'] )
                self::send( 429, \expGraphQLRateLimiter::refusal( $hit ) );
        }
        $builder = \expGraphQLSchemaBuilder::fromSettings();
        $context = new \expGraphQLContext( $user, \expGraphQLContext::AUTH_SESSION, new \expGraphQLRepository( $builder->catalogue() ),
                                           \expGraphQLContext::requestBaseUrl() );
        $answer = ( new \expGraphQLServer( $builder ) )->run( $request, $context )['answer'];
        header( 'X-GraphQL-Schema: ' . $builder->schemaHash() );
        self::send( 200, $answer );
        return null;
    }

    /**
     * Writes the JSON answer and ends the request.
     *
     * @param int $status
     * @param array $answer
     */
    protected static function send( $status, array $answer )
    {
        if ( $status !== 200 )
            http_response_code( $status );
        header( 'Content-Type: application/json; charset=utf-8' );
        header( 'Cache-Control: private, no-store' );
        echo \expGraphQLRestResponse::encode( $answer );
        \eZExecution::cleanExit();
    }
}

}
?>
