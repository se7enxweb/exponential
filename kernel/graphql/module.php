<?php
/**
 * The module graphql: the GraphQL API with the session of the web interface (doc/eng-us/features/6.0/graphql.md).
 *
 *   graphql/playground   Setup > GraphQL: the end points, the schema and a playground to run queries (policy
 *                        graphql/playground)
 *   graphql/query        the session end point: a query as the signed-in user, JSON (policy graphql/query); the
 *                        playground posts to it
 *
 * Both answer 404 while the API is switched off in the siteaccess (graphql.ini [GraphQLSettings] Enabled).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

$Module = array( 'name' => 'GraphQL',
                 'variable_params' => false );

$ViewList = array();

$ViewList['playground'] = array(
    'functions' => array( 'playground' ),
    'script' => 'playground.php',
    'ui_context' => 'administration',
    'default_navigation_part' => 'ezsetupnavigationpart',
    'params' => array() );

$ViewList['query'] = array(
    'functions' => array( 'query' ),
    'script' => 'query.php',
    'ui_context' => 'ajax',
    'default_navigation_part' => 'ezsetupnavigationpart',
    'params' => array() );

$FunctionList = array();
// Open the playground of Setup > GraphQL
$FunctionList['playground'] = array();
// Run GraphQL queries with the session of the web interface (graphql/query)
$FunctionList['query'] = array();

?>
