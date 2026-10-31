<?php
/**
 * File containing the graphql/playground view: Setup > GraphQL. Whether the API answers in this siteaccess, its
 * end points, the library, the schema (its classes and hash), whether it and its mutations answer in each siteaccess,
 * its limits, its response cache, its persisted queries, the personal API keys with a GraphQL scope, and a
 * playground that runs a query through the session end point graphql/query, with the rights of the signed-in user.
 *
 * With the policy graphql/administrate the page also changes things (POST, the form token of the session):
 * switching the API or its mutations on or off in a siteaccess (expGraphQLSetup, the settings of exp:graphql:enable
 * and exp:graphql:disable), clearing the response cache, removing a stored persisted query; and it lists the keys of
 * every user, not only the user's own.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\GraphQL
{

class Playground extends \Exponential\Runnable\ModuleView
{
    /** The query the playground starts with. */
    const EXAMPLE = "{\n  siteaccess { name languages schemaHash }\n  currentUser { name signedInBy }\n  node(urlAlias: \"/\") {\n    name\n    classIdentifier\n    children(limit: 5) {\n      totalCount\n      nodes { name classIdentifier url }\n    }\n  }\n}\n";

    public function run( array $scope )
    {
        $http = \eZHTTPTool::instance();
        $user = \eZUser::currentUser();
        $access = $user->hasAccessTo( 'graphql', 'administrate' );
        $canAdministrate = isset( $access['accessWord'] ) && $access['accessWord'] === 'yes';
        $messages = array();
        $isPost = strtoupper( (string)\eZSys::serverVariable( 'REQUEST_METHOD', true ) ) === 'POST';
        if ( $isPost && $canAdministrate )
            $messages = self::act( $http );

        $enabled = \expGraphQL::isEnabled();
        $library = \expGraphQL::loadLibrary();
        $schema = array( 'hash' => '', 'classes' => 0 );
        if ( $enabled && $library )
        {
            $builder = \expGraphQLSchemaBuilder::fromSettings();
            $schema = array( 'hash' => $builder->schemaHash(), 'classes' => count( $builder->catalogue()->identifiers() ) );
        }
        $endpoint = rtrim( \eZSys::serverURL(), '/' ) . \eZSys::wwwDir() . '/api/graphql';
        $sessionEndpoint = '/graphql/query';
        \eZURI::transformURI( $sessionEndpoint );

        $persisted = array();
        foreach ( \expGraphQLPersistedQueries::fromSettings()->all() as $query )
            $persisted[] = array( 'hash' => (string)$query['hash'], 'names' => implode( ', ', (array)$query['names'] ), 'bytes' => (int)$query['bytes'],
                                  'shipped' => $query['source'] !== 'store' );
        $keys = array();
        $userLogin = (string)$user->attribute( 'login' );
        foreach ( \expGraphQLSetup::keys() as $key )
        {
            if ( $canAdministrate || $key['owner'] === $userLogin )
                $keys[] = $key;
        }

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'enabled', $enabled );
        $tpl->setVariable( 'library', $library );
        $tpl->setVariable( 'library_version', $library ? (string)\expGraphQL::libraryVersion() : '' );
        $tpl->setVariable( 'endpoint', $endpoint );
        $tpl->setVariable( 'session_endpoint', $sessionEndpoint );
        $tpl->setVariable( 'schema', $schema );
        $tpl->setVariable( 'example', self::EXAMPLE );
        $tpl->setVariable( 'can_administrate', $canAdministrate );
        $tpl->setVariable( 'messages', $messages );
        // this page shows the settings of its own siteaccess (admin); the table shows every siteaccess, so the notice
        // that the API is off here says so, and names the siteaccess, when it answers elsewhere
        $states = \expGraphQLSetup::siteAccessStates();
        $tpl->setVariable( 'siteaccesses', $states );
        $tpl->setVariable( 'siteaccess', (string)\expGraphQL::currentSiteAccess() );
        $tpl->setVariable( 'answers_elsewhere', in_array( true, array_column( $states, 'enabled' ), true ) );
        $tpl->setVariable( 'mutations', \expGraphQLMutations::enabled() );
        $tpl->setVariable( 'limits', self::limits() );
        $tpl->setVariable( 'cache', \expGraphQLResponseCache::forInstallation()->stats() + array( 'enabled' => \expGraphQL::setting( 'ResponseCache', 'enabled' ) === 'enabled',
                                                                                                  'ttl' => (int)\expGraphQL::setting( 'ResponseCacheTtl', 86400 ) ) );
        $tpl->setVariable( 'persisted', $persisted );
        $tpl->setVariable( 'keys', $keys );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:graphql/playground.tpl' );
        $Result['path'] = array( array( 'url' => 'setup/menu', 'text' => \ezpI18n::tr( 'design/admin/graphql', 'Setup' ) ),
                                 array( 'url' => false, 'text' => \ezpI18n::tr( 'design/admin/graphql', 'GraphQL' ) ) );
        return $Result;
    }

    /**
     * The limits of the current siteaccess, as label => value rows.
     *
     * @return array[] label, value
     */
    protected static function limits()
    {
        $t = function ( $text, array $params = array() ) { return \ezpI18n::tr( 'design/admin/graphql', $text, null, $params ); };
        $window = \expGraphQL::intSetting( 'RateLimitWindow', 60 );
        $rate = function ( $n ) use ( $t, $window ) {
            return (int)$n > 0 ? $t( '%count in %seconds s', array( '%count' => (int)$n, '%seconds' => $window ) ) : $t( 'no limit' );
        };
        return array(
            array( 'label' => $t( 'Depth' ), 'value' => (string)\expGraphQL::intSetting( 'MaxDepth', 12 ) ),
            array( 'label' => $t( 'Complexity' ), 'value' => (string)\expGraphQL::intSetting( 'MaxComplexity', 1000 ) ),
            array( 'label' => $t( 'Items per list' ), 'value' => $t( '%max (default %default)', array( '%max' => \expGraphQL::intSetting( 'MaxListLimit', 100 ),
                                                                                                      '%default' => \expGraphQL::intSetting( 'DefaultListLimit', 25 ) ) ) ),
            array( 'label' => $t( 'Requests per user or key' ), 'value' => $rate( \expGraphQL::setting( 'RateLimit', 600 ) ) ),
            array( 'label' => $t( 'Requests per anonymous address' ), 'value' => $rate( \expGraphQL::setting( 'RateLimitAnonymous', 120 ) ) ),
            array( 'label' => $t( 'Introspection' ), 'value' => (string)\expGraphQL::setting( 'Introspection', 'signed-in' ) ),
            array( 'label' => $t( 'Persisted queries only' ), 'value' => (string)\expGraphQL::setting( 'PersistedQueriesOnly', 'disabled' ) ),
            array( 'label' => $t( 'Largest query' ), 'value' => $t( '%bytes bytes', array( '%bytes' => \expGraphQL::intSetting( 'MaxQueryLength', \expGraphQLServer::MAX_QUERY_BYTES ) ) ) ),
            array( 'label' => $t( 'Largest upload' ), 'value' => $t( '%bytes bytes', array( '%bytes' => \expGraphQLUpload::maxBytes() ) ) ),
        );
    }

    /**
     * The actions of the page (graphql/administrate).
     *
     * @param \eZHTTPTool $http
     * @return array[] the messages: kind (ok, warn, bad), text
     */
    protected static function act( \eZHTTPTool $http )
    {
        $t = function ( $text, array $params = array() ) { return \ezpI18n::tr( 'design/admin/graphql', $text, null, $params ); };
        $known = \expIniEditor::knownSiteAccesses();
        $first = function ( $name ) use ( $http ) {
            $keys = array_keys( (array)$http->postVariable( $name ) );
            return isset( $keys[0] ) ? (string)$keys[0] : '';
        };
        $changes = null;
        $done = '';
        foreach ( array( 'EnableButton' => array( false, true ), 'DisableButton' => array( false, false ),
                         'MutationsOnButton' => array( true, true ), 'MutationsOffButton' => array( true, false ) ) as $button => $what )
        {
            if ( !$http->hasPostVariable( $button ) )
                continue;
            $siteAccess = $first( $button );
            if ( !in_array( $siteAccess, $known, true ) )
                return array( array( 'kind' => 'bad', 'text' => $t( 'There is no siteaccess %siteaccess.', array( '%siteaccess' => $siteAccess ) ) ) );
            list( $mutationsOnly, $on ) = $what;
            if ( $mutationsOnly )
                $changes = \expGraphQLSetup::mutationChanges( array( $siteAccess ), $on );
            else if ( $on )
                $changes = \expGraphQLSetup::enableChanges( array( $siteAccess ), array(), false );
            else
                $changes = \expGraphQLSetup::disableChanges( array( $siteAccess ), false );
            $done = $mutationsOnly ? ( $on ? $t( 'The mutations are switched on in %siteaccess.', array( '%siteaccess' => $siteAccess ) )
                                           : $t( 'The mutations are switched off in %siteaccess.', array( '%siteaccess' => $siteAccess ) ) )
                                   : ( $on ? $t( 'The GraphQL API is switched on in %siteaccess.', array( '%siteaccess' => $siteAccess ) )
                                           : $t( 'The GraphQL API is switched off in %siteaccess.', array( '%siteaccess' => $siteAccess ) ) );
        }
        if ( $changes !== null )
        {
            $result = \expGraphQLSetup::apply( $changes, false );
            if ( !$result['ok'] )
            {
                $errors = array();
                foreach ( $result['files'] as $file )
                    if ( $file['error'] !== null )
                        $errors[] = $file['path'] . ': ' . $file['error'];
                return array( array( 'kind' => 'bad', 'text' => $t( 'The settings could not be written: %error', array( '%error' => implode( '; ', $errors ) ) ) ) );
            }
            return array( array( 'kind' => 'ok', 'text' => $done ) );
        }
        if ( $http->hasPostVariable( 'ClearCacheButton' ) )
        {
            $removed = \expGraphQLResponseCache::forInstallation()->clear();
            return array( array( 'kind' => 'ok', 'text' => $t( 'The response cache was cleared: %count answers removed.', array( '%count' => $removed ) ) ) );
        }
        if ( $http->hasPostVariable( 'RemovePersistedButton' ) )
        {
            $hash = $first( 'RemovePersistedButton' );
            $confirmed = $http->hasPostVariable( 'ConfirmRemovePersisted' ) ? (array)$http->postVariable( 'ConfirmRemovePersisted' ) : array();
            if ( !isset( $confirmed[$hash] ) )
                return array( array( 'kind' => 'warn', 'text' => $t( 'Tick the box beside the query to remove it.' ) ) );
            if ( !\expGraphQLPersistedQueries::isHash( $hash ) || !\expGraphQLPersistedQueries::fromSettings()->remove( $hash ) )
                return array( array( 'kind' => 'bad', 'text' => $t( 'The query could not be removed: a query shipped in a directory of PersistedQueryDirectoryList[] is removed with its file.' ) ) );
            \expGraphQLResponseCache::expireIfAvailable();
            return array( array( 'kind' => 'ok', 'text' => $t( 'The persisted query was removed.' ) ) );
        }
        return array();
    }
}

}
?>
