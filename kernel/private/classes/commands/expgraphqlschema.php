<?php
/**
 * @description Print or write the GraphQL schema of a siteaccess: the schema language (sdl), the introspection result (json) or the classes and their fields (summary)
 * @alias exp:graphql:schema
 *
 * File containing the exp:graphql:schema command (bin/php/expgraphqlschema.php); guide: doc/eng-us/features/6.0/graphql.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expgraphqlschema extends \Exponential\Runnable\Command
{
    const EXIT_OK = 0;
    const EXIT_ERRORS = 1;
    const EXIT_USAGE = 2;
    const EXIT_UNAVAILABLE = 3;

    /** The formats of --format. */
    const FORMATS = array( 'sdl', 'json', 'summary' );

    public function run()
    {
        $this->script( array( 'description' => "Print the GraphQL schema of a siteaccess\n" .
                                               "sdl (the default): the schema language, for code generators and editors;\n" .
                                               "json: the introspection result ({ \"data\": { \"__schema\": ... } }), as a client reads it;\n" .
                                               "summary: the classes, their types and fields, the datatype of each field and whether it has a mapping.\n" .
                                               "The schema is printed whether or not the API is switched on (a schema for development).\n" .
                                               "\n" .
                                               "./console exp:graphql:schema --siteaccess=site\n" .
                                               "./console exp:graphql:schema --format=json --output=schema.json\n" .
                                               "\n" .
                                               "Exit code: 0 printed; 1 the file could not be written; 3 the library is missing; 2 wrong options.",
                              'use-session' => false,
                              'use-modules' => true,
                              'use-extensions' => true ) );
        $options = $this->startup( '[format:][output:]', '', array( 'format' => 'sdl (the default), json or summary',
                                                                    'output' => 'write to this file instead of the screen' ) );
        $format = isset( $options['format'] ) && $options['format'] ? (string)$options['format'] : 'sdl';
        if ( !in_array( $format, self::FORMATS, true ) )
        {
            $this->cli()->error( "FAIL --format must be sdl, json or summary: $format" );
            $this->shutdown( self::EXIT_USAGE );
        }
        if ( !\expGraphQL::loadLibrary() )
        {
            $this->cli()->error( 'FAIL webonyx/graphql-php is not installed (composer.json, or graphql.ini [GraphQLSettings] LibraryPath)' );
            $this->shutdown( self::EXIT_UNAVAILABLE );
        }
        $builder = \expGraphQLSchemaBuilder::fromSettings();
        if ( $format === 'sdl' )
            $text = $builder->sdl();
        else if ( $format === 'summary' )
            $text = json_encode( array( 'schema' => 'exp-graphql-schema-summary/1' ) + $builder->summary(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n";
        else
        {
            $result = \GraphQL\GraphQL::executeQuery( $builder->schema(), \GraphQL\Type\Introspection::getIntrospectionQuery() )->toArray();
            $text = json_encode( $result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n";
        }
        $output = isset( $options['output'] ) ? (string)$options['output'] : '';
        if ( $output === '' )
        {
            fwrite( STDOUT, $text );
            $this->shutdown( self::EXIT_OK );
        }
        if ( @file_put_contents( $output, $text ) === false )
        {
            $this->cli()->error( "FAIL $output could not be written" );
            $this->shutdown( self::EXIT_ERRORS );
        }
        $this->cli()->output( "PASS schema " . $builder->schemaHash() . " written to $output ($format, " . strlen( $text ) . ' bytes)' );
        $this->shutdown( self::EXIT_OK );
    }
}

}
