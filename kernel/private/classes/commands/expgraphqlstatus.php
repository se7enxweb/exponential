<?php
/**
 * @description The GraphQL API of a siteaccess: switched on or off, end point, library, limits, introspection, naming, schema hash, classes and datatypes without a mapping
 * @alias exp:graphql:status
 *
 * File containing the exp:graphql:status command (bin/php/expgraphqlstatus.php); guide: doc/eng-us/features/6.0/graphql.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expgraphqlstatus extends \Exponential\Runnable\Command
{
    const EXIT_OK = 0;
    const EXIT_USAGE = 2;
    const EXIT_UNAVAILABLE = 3;

    /** The machine output (--format=json). */
    const SCHEMA = 'exp-graphql-status/1';

    public function run()
    {
        $this->script( array( 'description' => "Show the GraphQL API of a siteaccess\n" .
                                               "Whether it is switched on, its end point, the library and its version, the limits, introspection,\n" .
                                               "the naming, the schema hash, the classes in the schema and the datatypes that have no mapping\n" .
                                               "of their own (they are strings).\n" .
                                               "\n" .
                                               "./console exp:graphql:status --siteaccess=site\n" .
                                               "./console exp:graphql:status --format=json\n" .
                                               "\n" .
                                               "Exit code: 0 the library is available; 3 it is missing; 2 wrong options.",
                              'use-session' => false,
                              'use-modules' => true,
                              'use-extensions' => true ) );
        $options = $this->startup( '[format:]', '', array( 'format' => 'text (the default) or json' ) );
        $format = isset( $options['format'] ) && $options['format'] ? (string)$options['format'] : 'text';
        if ( $format !== 'text' && $format !== 'json' )
        {
            $this->cli()->error( "FAIL --format must be text or json: $format" );
            $this->shutdown( self::EXIT_USAGE );
        }
        $status = self::status();
        if ( $format === 'json' )
            $this->cli()->output( json_encode( array( 'schema' => self::SCHEMA ) + $status, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );
        else
        {
            $out = $this->cli();
            $out->output( sprintf( '%-14s %s', 'siteaccess', $status['siteaccess'] ) );
            $out->output( sprintf( '%-14s %s', 'enabled', $status['enabled'] ? 'yes' : 'no (graphql.ini [GraphQLSettings] Enabled, SiteAccessList[])' ) );
            $out->output( sprintf( '%-14s %s', 'end point', $status['endpoint'] ) );
            $out->output( sprintf( '%-14s %s', 'library', $status['library'] ? 'webonyx/graphql-php ' . $status['libraryVersion'] : 'not installed' ) );
            $out->output( sprintf( '%-14s depth %d, complexity %d, list %d (default %d)', 'limits', $status['limits']['maxDepth'], $status['limits']['maxComplexity'],
                                   $status['limits']['maxListLimit'], $status['limits']['defaultListLimit'] ) );
            $out->output( sprintf( '%-14s %d per user or key, %d per anonymous address, in %d s', 'rate limits', $status['rateLimits']['user'],
                                   $status['rateLimits']['anonymous'], $status['rateLimits']['window'] ) );
            $out->output( sprintf( '%-14s %s, %d stored, automatic: %s, persisted only: %s', 'persisted', $status['persisted']['enabled'] ? 'enabled' : 'disabled',
                                   $status['persisted']['stored'], $status['persisted']['automatic'], $status['persisted']['only'] ) );
            $out->output( sprintf( '%-14s %s', 'introspection', $status['introspection'] ) );
            $out->output( sprintf( '%-14s %s', 'naming', $status['naming'] ) );
            if ( $status['schemaHash'] !== null )
            {
                $out->output( sprintf( '%-14s %s, %d classes, %d fields', 'schema', $status['schemaHash'], $status['classes'], $status['fields'] ) );
                $out->output( sprintf( '%-14s %s', 'string only', $status['unmappedDatatypes'] ? implode( ' ', $status['unmappedDatatypes'] ) : '-' ) );
            }
            $out->output( $status['library'] ? 'PASS the GraphQL API can run' . ( $status['enabled'] ? '' : ' (switched off here)' ) : 'FAIL the library is missing' );
        }
        $this->shutdown( $status['library'] ? self::EXIT_OK : self::EXIT_UNAVAILABLE );
    }

    /**
     * @return array the status of the current siteaccess
     */
    public static function status()
    {
        $library = \expGraphQL::loadLibrary();
        $status = array( 'siteaccess' => \expGraphQL::currentSiteAccess(),
                         'enabled' => \expGraphQL::isEnabled(),
                         'siteaccessList' => \expGraphQL::listSetting( 'SiteAccessList' ),
                         'endpoint' => '/api/graphql',
                         'library' => $library,
                         'libraryVersion' => $library ? \expGraphQL::libraryVersion() : null,
                         'limits' => array( 'maxDepth' => \expGraphQL::intSetting( 'MaxDepth', 12 ), 'maxComplexity' => \expGraphQL::intSetting( 'MaxComplexity', 1000 ),
                                            'maxListLimit' => \expGraphQL::intSetting( 'MaxListLimit', 100 ), 'defaultListLimit' => \expGraphQL::intSetting( 'DefaultListLimit', 25 ) ),
                         'rateLimits' => array( 'user' => (int)\expGraphQL::setting( 'RateLimit', 600 ), 'anonymous' => (int)\expGraphQL::setting( 'RateLimitAnonymous', 120 ),
                                                'window' => \expGraphQL::intSetting( 'RateLimitWindow', 60 ) ),
                         'persisted' => array( 'enabled' => \expGraphQL::setting( 'PersistedQueries', 'enabled' ) === 'enabled',
                                               'automatic' => (string)\expGraphQL::setting( 'AutomaticPersistedQueries', 'signed-in' ),
                                               'only' => (string)\expGraphQL::setting( 'PersistedQueriesOnly', 'disabled' ),
                                               'stored' => \expGraphQLPersistedQueries::fromSettings()->count() ),
                         'introspection' => (string)\expGraphQL::setting( 'Introspection', 'signed-in' ),
                         'naming' => \expGraphQLNaming::fromSettings()->style(),
                         'schemaHash' => null, 'classes' => 0, 'fields' => 0, 'unmappedDatatypes' => array() );
        if ( $library )
        {
            $summary = \expGraphQLSchemaBuilder::fromSettings()->summary();
            $status['schemaHash'] = $summary['schemaHash'];
            $status['classes'] = count( $summary['classes'] );
            $unmapped = array();
            foreach ( $summary['classes'] as $class )
            {
                $status['fields'] += count( $class['fields'] );
                foreach ( $class['fields'] as $field )
                    if ( !$field['mapped'] )
                        $unmapped[$field['datatype']] = true;
            }
            ksort( $unmapped );
            $status['unmappedDatatypes'] = array_keys( $unmapped );
        }
        return $status;
    }
}

}
