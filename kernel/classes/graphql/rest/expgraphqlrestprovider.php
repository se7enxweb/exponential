<?php
/**
 * The REST routes of the GraphQL API (doc/eng-us/features/6.0/graphql.md):
 *
 *   POST|GET /api/graphql              a query (JSON body, application/graphql, a form, or the query string)
 *   GET      /api/graphql/playground   the playground of the administration (a redirect to graphql/playground)
 *   POST|GET /api/graphql/subscribe    a subscription: an event stream (text/event-stream, graphql-sse), and the
 *                                      stream Velocity's WebSocket bridge (graphql-transport-ws) opens per subscription
 *
 * The REST layer reads the credentials: an OAuth token, or a personal API key with the scope graphql:read; a
 * request without any runs as the anonymous user (rest.ini [RouteSettings] OptionalAuthentication[]).
 *
 * Registered in rest.ini [ApiProvider] ProviderClass[graphql]=expGraphQLRestProvider.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLRestProvider implements ezpRestProviderInterface
{
    public function getRoutes()
    {
        $query = array( 'http-get' => 'query', 'http-post' => 'query' );
        return array(
            'expGraphQLQuery' => new ezpMvcRailsRoute( '', 'expGraphQLRestController', $query ),
            'expGraphQLQuerySlash' => new ezpMvcRailsRoute( '/', 'expGraphQLRestController', $query ),
            'expGraphQLPlayground' => new ezpMvcRailsRoute( '/playground', 'expGraphQLRestController', array( 'http-get' => 'playground' ) ),
            'expGraphQLSubscribe' => new ezpMvcRailsRoute( '/subscribe', 'expGraphQLRestController', array( 'http-get' => 'subscribe', 'http-post' => 'subscribe' ) ),
        );
    }

    public function getViewController()
    {
        return new expGraphQLRestViewController();
    }
}

?>
