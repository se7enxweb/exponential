<?php
/**
 * File containing the graphql/playground view: Setup > GraphQL. Whether the API answers in this siteaccess, its
 * end points, the library, the schema (its classes and hash) and a playground that runs a query through the
 * session end point graphql/query, with the rights of the signed-in user.
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

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'enabled', $enabled );
        $tpl->setVariable( 'library', $library );
        $tpl->setVariable( 'library_version', $library ? (string)\expGraphQL::libraryVersion() : '' );
        $tpl->setVariable( 'endpoint', $endpoint );
        $tpl->setVariable( 'session_endpoint', $sessionEndpoint );
        $tpl->setVariable( 'schema', $schema );
        $tpl->setVariable( 'example', self::EXAMPLE );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:graphql/playground.tpl' );
        $Result['path'] = array( array( 'url' => 'setup/menu', 'text' => \ezpI18n::tr( 'design/admin/graphql', 'Setup' ) ),
                                 array( 'url' => false, 'text' => \ezpI18n::tr( 'design/admin/graphql', 'GraphQL' ) ) );
        return $Result;
    }
}

}
?>
