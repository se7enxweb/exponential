<?php
/**
 * The controller of the GraphQL end point (expGraphQLRestProvider): runs the query as the user the REST layer
 * authenticated (an OAuth token, a personal API key, or nobody: the anonymous user) in the siteaccess the request
 * matched, and answers its JSON. Every answer has a status object, so the REST application cache never keeps one
 * (rest.ini [expGraphQLRestController_CacheSettings] switches it off as well).
 *
 *   200  the answer of the query, errors of the query included (GraphQL over HTTP, application/json)
 *   400  a request that cannot be read (not JSON, no query)
 *   404  the API is switched off in this siteaccess (graphql.ini [GraphQLSettings] Enabled, SiteAccessList[])
 *   503  the library is missing
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLRestController extends ezpRestMvcController
{
    public function doQuery()
    {
        // the messages in the language the client asks for (every request anew: a persistent worker keeps statics)
        expGraphQLMessage::useLanguage( isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ? (string)$_SERVER['HTTP_ACCEPT_LANGUAGE'] : '' );
        if ( !expGraphQL::isEnabled() )
            return self::answer( 404, expGraphQLServer::refusal( expGraphQLMessage::tr( 'The GraphQL API is switched off in this siteaccess.' ), expGraphQLError::DISABLED ) );
        if ( !expGraphQL::loadLibrary() )
            return self::answer( 503, expGraphQLServer::refusal( expGraphQLMessage::tr( 'The GraphQL library is not installed.' ), expGraphQLError::INTERNAL ) );

        $request = expGraphQLServer::parseHttpRequest( $this->request->originalProtocol === 'http-get' ? 'GET' : 'POST',
                                                       isset( $_SERVER['CONTENT_TYPE'] ) ? (string)$_SERVER['CONTENT_TYPE'] : '',
                                                       (string)$this->request->body, (array)$this->request->get, (array)$this->request->post );
        if ( isset( $request['error'] ) )
            return self::answer( 400, expGraphQLServer::refusal( $request['error'], expGraphQLError::BAD_USER_INPUT ) );

        $user = eZUser::currentUser();
        $key = class_exists( 'expApiKeyRest' ) ? expApiKeyRest::current() : null;
        if ( $key !== null )
            $mode = expGraphQLContext::AUTH_API_KEY;
        else
            $mode = $user->isAnonymous() ? expGraphQLContext::AUTH_ANONYMOUS : expGraphQLContext::AUTH_TOKEN;
        // The rate limit first: a request over it costs no schema and no query
        $hit = expGraphQLRateLimiter::hitRequest( $mode, $user, $key !== null ? (int)$key->attribute( 'id' ) : null );
        $rateHeaders = $hit === null ? array() : expGraphQLRateLimiter::headers( $hit );
        if ( $hit !== null && !$hit['allowed'] )
            return self::answer( 429, expGraphQLRateLimiter::refusal( $hit ), $rateHeaders );
        $builder = expGraphQLSchemaBuilder::fromSettings();
        $context = new expGraphQLContext( $user, $mode, new expGraphQLRepository( $builder->catalogue() ), expGraphQLContext::requestBaseUrl() );
        $context->apiKeyId = $key !== null ? (int)$key->attribute( 'id' ) : null;
        $http = ( new expGraphQLServer( $builder ) )->respond( $request, $context, $_SERVER );
        $result = new ezpRestMvcResult();
        $result->status = new expGraphQLRestResponse( $http['status'], $http['body'], $http['headers'] + $rateHeaders );
        return $result;
    }

    /**
     * /api/graphql/playground: the playground of the administration, signed in with the session of the browser.
     */
    public function doPlayground()
    {
        if ( !expGraphQL::isEnabled() )
            return self::answer( 404, expGraphQLServer::refusal( expGraphQLMessage::tr( 'The GraphQL API is switched off in this siteaccess.' ), expGraphQLError::DISABLED ) );
        $url = '/graphql/playground';
        eZURI::transformURI( $url, false, 'full' );
        $result = new ezpRestMvcResult();
        $result->status = new ezpRestStatusResponse( 302, null, array( 'Location' => $url ) );
        return $result;
    }

    /**
     * @param int $status
     * @param array $answer
     * @param array $headers
     * @return ezpRestMvcResult
     */
    protected static function answer( $status, array $answer, array $headers = array() )
    {
        $result = new ezpRestMvcResult();
        $result->status = new expGraphQLRestResponse( (int)$status, $answer, $headers + array( 'Cache-Control' => 'private, no-store' ) );
        return $result;
    }
}

?>
